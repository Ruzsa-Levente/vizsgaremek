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
</head>
<body>

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
            <input type="text" id="search-input" placeholder="Search..." value="<?= htmlspecialchars($searchTerm ?? '') ?>">
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
                    <a class="nav-link current-page" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="clothes.php">Clothes</a>
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

<main class="main-content index-main-top-distance">
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content container text-center py-5">
            <h1>HEET UP YOUR STYLE</h1>
            <p>Fuel your ambition. Stand out. Never slow down.</p>
            <a href="#products" class="shop-now-btn btn btn-primary">Shop Now</a>
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

    <!-- Products Section -->
    <section id="products" class="container my-5">
        <h2 class="text-center mb-4">Trending Now</h2>
        <div class="row products-row">
            <?php
            $sql = "SELECT azon, nev, ar FROM termekek WHERE keszlet > 0 ORDER BY azon ASC LIMIT 2";
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
            <div class="col-md-5 col-lg-4 mb-4">
                <div class="card custom-card h-100" onclick="redirectToProduct(<?= $product['azon']; ?>)">
                    <img src="kepek/<?= htmlspecialchars($kep); ?>" class="card-img-top" alt="<?= htmlspecialchars($product['nev']); ?>">
                    <div class="card-body text-center">
                        <h3 class="card-title"><?= htmlspecialchars($product['nev']); ?></h3>
                        <p class="card-text"><?= number_format($product['ar'], 0, ',', ' ') ?> Ft</p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<!-- Trending Image Section with Button -->
<div class="trending-image-container my-4">
        <img src="kepek/pulcsik.png" alt="Trending Image" class="trending-image">
        <a href="#shop-now" class="trending-button">Pulcsik</a>
        <a href="clothes.php?category=pulóver" class="trending-button">Pulcsik</a>
    </div>

    
<div class="trending-image-container my-4">
        <img src="kepek/polok.png" alt="Trending Image" class="trending-image">
        <a href="#shop-now" class="trending-button">Pólók</a>
        <a href="clothes.php?category=póló" class="trending-button">Pólók</a>
    </div>
</section>

<!-- Bootstrap Footer -->
<footer class="footer text-white text-center py-3 mt-auto">
    <p>© 2025 Heet Clothing | The style that never burns out!</p>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="java_script/script.js"></script>
</body>
</html>
