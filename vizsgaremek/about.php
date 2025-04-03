<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>About Us - Heet Clothing</title>
</head>
<body class="reptile-bg">

<?php
require("php/connect.php");
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
?>

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
                    <a class="nav-link current-page" href="about.php">About Us</a>
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

<main class="main-content ">
    <section id="about" class="about-section container mt-5">
        <h2 class="about-title animate__fadeIn">About Heet</h2>
        <p class="about-subtitle animate__fadeInUp animate__delay-1s">Welcome to Heet – where fashion meets passion. We are dedicated to crafting high-quality, stylish apparel that empowers confidence and individuality.</p>
        
        <div class="about-content">
            <div class="about-item animate__fadeInLeft animate__delay-2s">
                <h3>Who Are We?</h3>
                <p>Heet is more than just a clothing brand; it's a movement. Designed for trendsetters and go-getters, we create fashion-forward pieces that blend comfort, quality, and bold aesthetics.</p>
                <img src="kepek/index_lebron.png" alt="lebron" style="width: 100%;border-radius: 4%;">
            </div>
            
            <div class="about-item animate__fadeInRight animate__delay-2s">
                <h3>Who Is Our Target Audience?</h3>
                <p>Our brand is built for those who live life on their own terms. Whether you're into streetwear, casual fits, or standout statement pieces, Heet has something for you. We cater to both men and women who value self-expression through fashion.</p>
            </div>
            
            <div class="about-item animate__fadeInUp animate__delay-3s">
                <h3>Where Can You Find Us?</h3>
                <p>Shop Heet online or visit us in person:</p>
                <p class="about-location"><strong>Heet Store</strong><br>
                   123 Fashion Street,<br>
                   Style City, SC 98765</p>
                <p>Got questions? Reach out to us or drop by – let’s turn up the heat in fashion together!</p>
            </div>
        </div>
    </section>
</main>

<!-- Bootstrap Footer -->
<footer class="footer text-white text-center py-4 mt-auto">
    <div class="container-fluid px-0"> <!-- Changed to container-fluid and removed padding -->
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
