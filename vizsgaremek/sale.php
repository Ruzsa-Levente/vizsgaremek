<?php
require("php/connect.php");

// Keresési kifejezés
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Alapértelmezett SQL lekérdezés - csak a leárazott termékek
$sql = "SELECT azon, nev, ar, discounted_price 
        FROM termekek 
        WHERE keszlet > 0 
        AND discounted_price IS NOT NULL 
        AND discounted_price < ar";

// Ha van keresési kifejezés
if ($searchTerm) {
    $sql .= " AND (nev LIKE :searchTerm OR leiras LIKE :searchTerm)";
}

// Alapértelmezett rendezési feltétel
$sort_order = "ORDER BY nev ASC"; // A-Z alapértelmezett rendezés

// Ha van rendezési paraméter
if (isset($_GET['sort'])) {
    switch ($_GET['sort']) {
        case 'price_asc':
            $sort_order = "ORDER BY discounted_price ASC"; // Legolcsóbb
            break;
        case 'price_desc':
            $sort_order = "ORDER BY discounted_price DESC"; // Legdrágább
            break;
        case 'popularity':
            $sort_order = "ORDER BY RAND()"; // Véletlenszerű rendezés
            break;
        case 'name_asc':
            $sort_order = "ORDER BY nev ASC"; // A-Z
            break;
        case 'name_desc':
            $sort_order = "ORDER BY nev DESC"; // Z-A
            break;
    }
}

// A rendezési feltételt hozzáadjuk a lekérdezéshez
$sql .= " $sort_order";

// Keresési paraméterek binding
$stmt = $pdo->prepare($sql);

// Ha van keresési kifejezés, binding a keresési feltételhez
if ($searchTerm) {
    $stmt->bindValue(':searchTerm', '%' . $searchTerm . '%', PDO::PARAM_STR);
}

$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Sale - Heet Clothing</title>
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
<body>

<!-- Bootstrap Navbar with Logo Above Links -->
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container-fluid nav-container d-flex flex-column align-items-center">
        <!-- Toggle Button (Moved to the left) -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- Search Container -->
        <div class="search-container d-flex align-items-center">
            <i class="fas fa-search" id="search-toggle"></i>
            <input type="text" id="search-input" name="search" placeholder="Search..." value="<?= htmlspecialchars($searchTerm ?? '') ?>" oninput="this.form.submit()">
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
                    <a class="nav-link current-page" href="sale.php">Sale</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="signup.php">Sign Up</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="about.php">About Us</a>
                </li>
            </ul>
        </div>
        <!-- Icons (Cart and User only) -->
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

<main class="main-content">
    <div class="sorting-container">
        <label for="sort-by">Rendezés:</label>
        <select id="sort-by" onchange="location = this.value;">
        <option value="clothes.php?search=<?= urlencode($searchTerm) ?>" <?= !isset($_GET['sort']) || empty($_GET['sort']) ? 'selected' : '' ?>>Válassz rendezést</option>
            <option value="sale.php?sort=price_asc&search=<?= urlencode($searchTerm) ?>" <?= isset($_GET['sort']) && $_GET['sort'] == 'price_asc' ? 'selected' : '' ?>>Legolcsóbb</option>
            <option value="sale.php?sort=price_desc&search=<?= urlencode($searchTerm) ?>" <?= isset($_GET['sort']) && $_GET['sort'] == 'price_desc' ? 'selected' : '' ?>>Legdrágább</option>
            <option value="sale.php?sort=popularity&search=<?= urlencode($searchTerm) ?>" <?= isset($_GET['sort']) && $_GET['sort'] == 'popularity' ? 'selected' : '' ?>>Legnépszerűbb</option>
            <option value="sale.php?sort=name_asc&search=<?= urlencode($searchTerm) ?>" <?= isset($_GET['sort']) && $_GET['sort'] == 'name_asc' ? 'selected' : '' ?>>A-Z</option>
            <option value="sale.php?sort=name_desc&search=<?= urlencode($searchTerm) ?>" <?= isset($_GET['sort']) && $_GET['sort'] == 'name_desc' ? 'selected' : '' ?>>Z-A</option>
        </select>
    </div>
    <h1 class="products-title">Leárazás</h1> <!-- Új "Termékeink" felirat -->


    <div class="cont container">
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">
        <?php
        if (count($products) > 0):
            foreach ($products as $product):
                $sql = "SELECT kep_url FROM termek_kepek WHERE termek_azon = :id LIMIT 1";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $product['azon'], PDO::PARAM_INT);
                $stmt->execute();
                $kep = $stmt->fetchColumn() ?: 'no-image.jpg';
        ?>
            <div class="col">
                <div class="product-card card h-100">
                    <a href="product.php?id=<?= $product['azon']; ?>" class="product-card__image-link">
                        <div class="product-card__image">
                            <img src="kepek/<?= htmlspecialchars($kep); ?>" class="card-img-top" alt="<?= htmlspecialchars($product['nev']); ?>">
                        </div>
                    </a>
                    <div class="product-card__info card-body">
                        <h2 class="product-card__title card-title"><?= htmlspecialchars($product['nev']); ?></h2>
                        <div class="product-card__price-row">
                            <span class="original-price"><?= number_format($product['ar'], 0, ',', ' ') ?> Ft</span>
                            <span class="discount-price"><?= number_format($product['discounted_price'], 0, ',', ' ') ?> Ft</span>
                            <a href="product.php?id=<?= $product['azon']; ?>" class="product-card__btn btn">Részletek</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php else: ?>
            <div class="col">
                <p class="text-center">Jelenleg nincs leárazott termék.</p>
            </div>
        <?php endif; ?>
        </div>
    </div>
</main>

<!-- Bootstrap Footer -->
<footer class="footer text-white text-center py-4 mt-auto">
    <div class="container">
        <div class="footer-content">
            <p class="mb-2">© 2025 Heet Clothing | All rights reserved.</p>
            <a href="aszf.php" class="text-white text-decoration-none mb-3 d-inline-block">ÁSZF</a>
            <br>
            <div class="social-icons mt-3 d-flex justify-content-center align-items-center">
            <span class="text-white me-3">Stay Connected:</span>
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

<!-- Bootstrap JS -->
<script src="java_script/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
