<?php require("php/connect.php"); ?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css">
    <title>Heet Clothing</title>
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
            <input type="text" id="search-input" placeholder="Search..." value="<?= htmlspecialchars($searchTerm ?? '') ?>">
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
                    <a class="nav-link current-page" href="index.php">Home</a>
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

<main class="main-content mt-2">
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-image-wrapper">
            <img src="kepek/deskopt_reptile.png" alt="Reptile Background" class="hero-image">
        </div>
        <div class="hero-content container text-center py-5">
            <h1>HEET UP YOUR STYLE</h1>
            <p>Fuel your ambition. Stand out. Never slow down.</p>
            <br>
            <a href="clothes.php" class="shop-now-btn btn btn-primary">Shop Now</a>
        </div>
    </section>

    <!-- Slider Section -->
    <section class="slider-section container my-5">
        <div class="swiper">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <img src="kepek/person_hoodie_ front.png" alt="Model picture 1" class="img-fluid">
                </div>
                <div class="swiper-slide">
                    <img src="kepek/person_hoodie_ back.png" alt="Model picture 2" class="img-fluid">
                </div>
            </div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
            <div class="swiper-pagination"></div>
        </div>
    </section>

    <!-- Trending Now Section -->
    <section id="products" class="products-section container my-5">
        <h2 class="text-center mb-4">Trending Now</h2>
        <div class="row products-row justify-content-center">
            <?php
            $sql = "SELECT azon, nev, ar, discounted_price FROM termekek WHERE keszlet > 0 ORDER BY azon ASC LIMIT 2";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($products as $product):
                $sql = "SELECT kep_url FROM termek_kepek WHERE termek_azon = :id LIMIT 1";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $product['azon'], PDO::PARAM_INT);
                $stmt->execute();
                $kep = $stmt->fetchColumn() ?: 'no-image.jpg';
            ?>
            <div class="col-10 col-sm-8 col-md-5 mb-4 trending-product-card">
                <div class="card custom-card h-100" onclick="redirectToProduct(<?= $product['azon']; ?>)">
                    <img src="kepek/<?= htmlspecialchars($kep); ?>" class="card-img-top" alt="<?= htmlspecialchars($product['nev']); ?>">
                    <div class="card-body text-center">
                        <h3 class="card-title"><?= htmlspecialchars($product['nev']); ?></h3>
                        <p class="card-text">
                            <?php if (isset($product['discounted_price']) && $product['discounted_price'] !== null && $product['discounted_price'] < $product['ar']): ?>
                                <span class="original-price"><?= number_format($product['ar'], 0, ',', ' ') ?> Ft</span>
                                <span class="discount-price"><?= number_format($product['discounted_price'], 0, ',', ' ') ?> Ft</span>
                            <?php else: ?>
                                <?= number_format($product['ar'], 0, ',', ' ') ?> Ft
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Sale Items Section -->
    <?php
    $sql = "SELECT azon, nev, ar, discounted_price FROM termekek WHERE keszlet > 0 AND discounted_price IS NOT NULL AND discounted_price < ar ORDER BY azon ASC LIMIT 4";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $sale_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($sale_products)):
    ?>
    <section class="sale-section container my-5">
        <h2>Sale Items</h2>
        <div class="row justify-content-center">
            <?php
            foreach ($sale_products as $product):
                $sql = "SELECT kep_url FROM termek_kepek WHERE termek_azon = :id LIMIT 1";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $product['azon'], PDO::PARAM_INT);
                $stmt->execute();
                $kep = $stmt->fetchColumn() ?: 'no-image.jpg';
            ?>
            <div class="col-10 col-sm-6 col-md-4 col-lg-3 mb-4">
                <div class="card sale-card h-100" onclick="redirectToProduct(<?= $product['azon']; ?>)">
                    <div class="position-relative">
                        <img src="kepek/<?= htmlspecialchars($kep); ?>" class="card-img-top" alt="<?= htmlspecialchars($product['nev']); ?>">
                        <span class="sale-badge">Sale</span>
                    </div>
                    <div class="card-body">
                        <h3 class="card-title"><?= htmlspecialchars($product['nev']); ?></h3>
                        <p class="card-text">
                            <span class="original-price"><?= number_format($product['ar'], 0, ',', ' ') ?> Ft</span>
                            <span class="discount-price"><?= number_format($product['discounted_price'], 0, ',', ' ') ?> Ft</span>
                        </p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
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
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="java_script/script.js"></script>
</body>
</html>
