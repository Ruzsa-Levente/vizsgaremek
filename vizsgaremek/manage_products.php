<?php
session_start();

// Ellenőrzi, hogy a felhasználó be van-e jelentkezve és megfelelő jogosultsága van-e
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || 
   !isset($_SESSION['jogosultsag']) || ($_SESSION['jogosultsag'] !== 'admin' && $_SESSION['jogosultsag'] !== 'superadmin')) {
    header("Location: index.php");
    exit();
}

// Adatbázis kapcsolat beillesztése
include 'php/connect.php';

// Termékek lekérdezése
$sql = "SELECT * FROM termekek";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Méretek lekérdezése minden termékhez
$product_sizes = [];
foreach ($products as $product) {
    $sql_sizes = "SELECT meret, keszlet FROM termek_meretek WHERE termek_azon = :product_id";
    $stmt_sizes = $pdo->prepare($sql_sizes);
    $stmt_sizes->bindParam(':product_id', $product['azon'], PDO::PARAM_INT);
    $stmt_sizes->execute();
    $product_sizes[$product['azon']] = $stmt_sizes->fetchAll(PDO::FETCH_ASSOC);
}

// Leárazás mentése, ha űrlapot küldtek
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['set_discount'])) {
    $product_id = $_POST['product_id'];
    $discount_price = $_POST['discount_price'] !== '' ? floatval($_POST['discount_price']) : null;

    $sql = "UPDATE termekek SET discounted_price = :discount_price WHERE azon = :product_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':discount_price', $discount_price, $discount_price === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->execute();
    
    // Frissíti az oldalt
    header("Location: manage_products.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css">
    <title>Termékek kezelése</title>
    <style>
        .original-price {
            text-decoration: line-through;
            color: #999;
            margin-right: 10px;
        }
        .discount-price {
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body class="reptile-bg">

<!-- Bootstrap Navbar -->
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container-fluid nav-container d-flex flex-column align-items-center">
        <!-- Toggle Button -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- Search Container -->
        <div class="search-container d-flex align-items-center">
            <i class="fas fa-search" id="search-toggle"></i>
            <input type="text" id="search-input" placeholder="Keresés..." value="<?= htmlspecialchars($searchTerm ?? '') ?>">
        </div>
        <!-- Logo -->
        <a class="navbar-brand logo mb-2" href="index.php">
            <img src="kepek/heet-logo-white.png" alt="Webshop Logo">
        </a>
        <!-- Navigation Links -->
        <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
            <button class="navbar-close-btn" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-label="Close navigation">
                <i class="fas fa-times"></i>
            </button>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="clothes.php">Clothes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="sale.php">Sale</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="signup.php">Sign Up</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="about.php">About Us</a>
                </li>
            </ul>
        </div>
        <!-- Icons (Cart and User) -->
        <div class="icon-container position-absolute top-50 translate-middle-y d-flex align-items-center">
            <div class="cart-icon d-flex align-items-center">
                <a href="billing.php">
                    <i class="fas fa-shopping-cart"></i>
                    <span id="cart-count">0</span>
                </a>
            </div>
            <div class="user-icon d-flex align-items-center">
                <a href="login.php">
                    <i class="fas fa-user"></i>
                </a>
            </div>
        </div>
    </div>
</nav>

<main style="margin-top: 200px;" class="container-distance">
    <section>
    <h2 id="manage-text">Termékek kezelése</h2>

    <a href="add_product.php"><button>Új termék hozzáadása</button></a>

    <table class="white">
        <thead>
            <tr>
                <th>Név</th>
                <th>Kategória</th>
                <th>Ár</th>
                <th>Készlet</th>
                <th>Méretek</th>
                <th>Leárazás</th>
                <th>Műveletek</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?= htmlspecialchars($product['nev']); ?></td>
                    <td><?= htmlspecialchars($product['kategoria']); ?></td>
                    <td>
                        <?php if ($product['discounted_price'] !== null && $product['discounted_price'] < $product['ar']): ?>
                            <span class="original-price"><?= htmlspecialchars($product['ar']); ?> HUF</span>
                            <span class="discount-price"><?= htmlspecialchars($product['discounted_price']); ?> HUF</span>
                        <?php else: ?>
                            <?= htmlspecialchars($product['ar']); ?> HUF
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($product['keszlet']); ?></td>
                    <td>
                        <?php
                        $sizes = $product_sizes[$product['azon']];
                        if (!empty($sizes)) {
                            $size_list = array_map(function($size) {
                                return htmlspecialchars($size['meret']) . ': ' . htmlspecialchars($size['keszlet']);
                            }, $sizes);
                            echo implode(', ', $size_list);
                        } else {
                            echo 'Nincs méret megadva';
                        }
                        ?>
                    </td>
                    <td>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="product_id" value="<?= $product['azon']; ?>">
                            <input type="number" name="discount_price" value="<?= $product['discounted_price'] ?? '' ?>" placeholder="Új ár" min="0" step="0.01" style="width: 80px;">
                            <button type="submit" name="set_discount">Beállítás</button>
                        </form>
                    </td>
                    <td>
                        <a href="edit_product.php?id=<?= $product['azon']; ?>">Szerkesztés</a>
                        <a href="php/delete_product.php?id=<?= $product['azon']; ?>" onclick="return confirm('Biztosan törölni szeretnéd ezt a terméket?')">Törlés</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <a href="admin.php" class="back-button">Vissza az admin panelre</a>
    </section>
</main>

<!-- Bootstrap Footer -->
<footer class="footer text-white text-center py-4 mt-auto">
    <div class="container">
        <div class="footer-content">
            <p class="mb-2">© 2025 Heet Clothing | Minden jog fenntartva.</p>
            <a href="aszf.php" class="text-white text-decoration-none mb-3 d-inline-block">ÁSZF</a>
            <br>
            <div class="social-icons mt-3 d-flex justify-content-center align-items-center">
            <span class="text-white me-3">Maradj kapcsolatban:</span>
                <a href="https://www.facebook.com/profile.php?id=61574451329401#" target="_blank" class="text-white mx-3">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="https://www.instagram.com/heetclothinghu/" target="_blank" class="text-white mx-3">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="https://www.tiktok.com/@heet.clothing" target="_blank" class="text-white mx-3">
                    <i class="fab fa-tiktok"></i>
                </a>
            </div>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="java_script/script.js"></script>
</body>
</html>
