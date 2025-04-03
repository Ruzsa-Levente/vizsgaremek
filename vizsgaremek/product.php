<?php
require("php/connect.php");

$product_id = isset($_GET['id']) ? $_GET['id'] : 1;
$sql = "SELECT * FROM termekek WHERE azon = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $product_id, PDO::PARAM_INT);
$stmt->execute();
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "Termék nem található!";
    exit();
}

$sql = "SELECT kep_url FROM termek_kepek WHERE termek_azon = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $product_id, PDO::PARAM_INT);
$stmt->execute();
$images = $stmt->fetchAll(PDO::FETCH_COLUMN);

$main_image = !empty($images) ? $images[0] : "no-image.jpg";
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Méretek lekérdezése
$sql = "SELECT meret, keszlet FROM termek_meretek WHERE termek_azon = :id AND keszlet > 0";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $product_id, PDO::PARAM_INT);
$stmt->execute();
$sizes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Aktuális ár meghatározása
$effective_price = (isset($product['discounted_price']) && $product['discounted_price'] !== null && $product['discounted_price'] < $product['ar']) 
    ? $product['discounted_price'] 
    : $product['ar'];
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title><?= htmlspecialchars($product['nev']); ?></title>
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
<!-- Bootstrap Navbar -->
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container-fluid nav-container d-flex flex-column align-items-center">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="search-container d-flex align-items-center">
            <i class="fas fa-search" id="search-toggle"></i>
            <input type="text" id="search-input" placeholder="Search..." value="<?= htmlspecialchars($searchTerm) ?>">
        </div>
        <a class="navbar-brand logo mb-2" href="index.php">
            <img src="kepek/heet-logo-white.png" alt="Webshop Logo">
        </a>
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

<main style="min-height: 80vh;" class="main-content">
    <div class="container">
        <div class="row product-section">
            <!-- Product Slider -->
            <div class="col-lg-6">
                <div class="product-slider">
                    <button class="prev" onclick="prevImage()">❮</button>
                    <div class="slider-container">
                        <?php foreach ($images as $index => $image): ?>
                            <img src="kepek/<?= htmlspecialchars($image); ?>" 
                                 alt="<?= htmlspecialchars($product['nev']); ?>" 
                                 class="slide <?= $index === 0 ? 'active' : '' ?>" 
                                 id="slide-<?= $index ?>">
                        <?php endforeach; ?>
                    </div>
                    <button class="next" onclick="nextImage()">❯</button>
                </div>
            </div>
            
            <!-- Product Info -->
            <div class="col-lg-6 product-info">
                <h1 class="mb-3"><?= htmlspecialchars($product['nev']); ?></h1>
                <p class="price h3 mb-3">
                    <?php if (isset($product['discounted_price']) && $product['discounted_price'] !== null && $product['discounted_price'] < $product['ar']): ?>
                        <span class="original-price"><?= number_format($product['ar'], 0, ',', ' ') ?> Ft</span>
                        <span class="discount-price"><?= number_format($product['discounted_price'], 0, ',', ' ') ?> Ft</span>
                    <?php else: ?>
                        <?= number_format($product['ar'], 0, ',', ' ') ?> Ft
                    <?php endif; ?>
                </p>
                <p class="description mb-4"><?= htmlspecialchars($product['leiras']); ?></p>
                
                <div class="product-options mb-4">
                    <label for="size" class="form-label">Méret:</label>
                    <select id="size" name="size" class="form-select w-auto">
                        <?php if (empty($sizes)): ?>
                            <option value="">Nincs elérhető méret</option>
                        <?php else: ?>
                            <?php foreach ($sizes as $index => $size): ?>
                                <option value="<?= htmlspecialchars($size['meret']); ?>" <?= $index === 0 ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($size['meret']); ?>
                                    <?= $size['keszlet'] < 10 ? " (Csak: {$size['keszlet']} db)" : ''; ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="product-buttons">
                    <button class="buy-btn me-2" 
                            onclick="addToCart('<?= htmlspecialchars($product['nev']); ?>', <?= $effective_price; ?>, 'kepek/<?= htmlspecialchars($main_image); ?>', document.getElementById('size').value)">
                        Kosárba
                    </button>
                    <button class="buy-btn" 
                            onclick="addToCart('<?= htmlspecialchars($product['nev']); ?>', <?= $effective_price; ?>, 'kepek/<?= htmlspecialchars($main_image); ?>', document.getElementById('size').value); window.location.href='billing.php'">
                        Megveszem most
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
$recommendedProducts = [];

$sql = "SELECT t.azon, t.nev, t.ar, t.discounted_price, tk.kep_url 
        FROM termekek t 
        LEFT JOIN termek_kepek tk ON t.azon = tk.termek_azon 
        WHERE t.azon != :id 
        AND t.keszlet > 0 
        GROUP BY t.azon 
        ORDER BY RAND() 
        LIMIT 10";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $product_id, PDO::PARAM_INT);

if ($stmt->execute()) {
    $recommendedProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<?php if (!empty($recommendedProducts)): ?>
    <section class="recommended-products container ">
        <h2 class="text-center mb-4">Ajánlott termékek</h2>
        <div class="recommended-wrapper">
            <div class="recommended-container">
                <?php foreach ($recommendedProducts as $item): ?>
                    <div class="recommended-item">
                        <a href="product.php?id=<?= htmlspecialchars($item['azon']); ?>">
                            <img src="kepek/<?= !empty($item['kep_url']) ? htmlspecialchars($item['kep_url']) : 'no-image.jpg'; ?>" 
                                 alt="<?= htmlspecialchars($item['nev']); ?>">
                            <p class="product-name"><?= htmlspecialchars($item['nev']); ?></p>
                            <p class="product-price">
                                <?php if (isset($item['discounted_price']) && $item['discounted_price'] !== null && $item['discounted_price'] < $item['ar']): ?>
                                    <span class="original-price"><?= number_format($item['ar'], 0, ',', ' ') ?> Ft</span>
                                    <span class="discount-price"><?= number_format($item['discounted_price'], 0, ',', ' ') ?> Ft</span>
                                <?php else: ?>
                                    <?= number_format($item['ar'], 0, ',', ' ') ?> Ft
                                <?php endif; ?>
                            </p>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

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
</footer>

<script src="java_script/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
