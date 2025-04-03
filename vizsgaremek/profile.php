<?php
session_start();
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Profil - Heet Clothing</title>
</head>
<body class="reptile-bg">

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
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link text-white" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="clothes.php">Clothes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="sale.php">Sale</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="signup.php">Sign Up</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="about.php">About Us</a>
                </li>
            </ul>
        </div>
        <div class="icon-container position-absolute top-50 translate-middle-y d-flex align-items-center">
            <div class="cart-icon d-flex align-items-center">
                <a href="billing.php" class="text-white">
                    <i class="fas fa-shopping-cart"></i>
                    <span id="cart-count">0</span>
                </a>
            </div>
            <div class="user-icon d-flex align-items-center">
                <a href="profile.php" class="text-white">
                    <i class="fas fa-user"></i>
                </a>
            </div>
        </div>
    </div>
</nav>

<main class="main-content d-flex align-items-center justify-content-center mt-5">
    <div class="profile-container container text-center animate__fadeIn">
        <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
            <h2 class="profile-title">Üdv, <?= htmlspecialchars($_SESSION['username']); ?>!</h2>
            <p class="profile-subtitle animate__fadeInUp animate__delay-1s">Itt találod a profil adataidat</p>
            <div class="profile-details mx-auto animate__fadeInUp animate__delay-2s">
                <div class="profile-item">
                    <span class="profile-label">Felhasználónév:</span>
                    <span class="profile-value"><?= htmlspecialchars($_SESSION['username']); ?></span>
                </div>
                <div class="profile-item">
                    <span class="profile-label">Email:</span>
                    <span class="profile-value"><?= htmlspecialchars($_SESSION['email']); ?></span>
                </div>
                <div class="profile-item">
                    <span class="profile-label">Jogosultság:</span>
                    <span class="profile-value"><?= htmlspecialchars($_SESSION['jogosultsag']); ?></span>
                </div>
                <?php if (isset($_SESSION['jogosultsag']) && ($_SESSION['jogosultsag'] === 'admin' || $_SESSION['jogosultsag'] === 'superadmin')): ?>
                    <form action="admin.php" method="GET" class="mt-3">
                        <button type="submit" class="profile-btn admin-btn">Admin Oldal</button>
                    </form>
                <?php endif; ?>
                <form action="php/logout.php" method="POST" class="mt-3">
                    <button type="submit" class="profile-btn logout-btn">Kijelentkezés</button>
                </form>
            </div>
        <?php else: ?>
            <h2 class="profile-title">Profil megtekintése</h2>
            <p class="profile-subtitle animate__fadeInUp animate__delay-1s">Jelentkezz be vagy regisztrálj a profilod eléréséhez!</p>
            <div class="profile-details mx-auto animate__fadeInUp animate__delay-2s">
                <a href="login.php" class="profile-btn login-btn">Bejelentkezés</a>
                <a href="signup.php" class="profile-btn signup-btn mt-3">Regisztráció</a>
            </div>
        <?php endif; ?>
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
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="java_script/script.js"></script>
</body>
</html>
