<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Billing</title>
</head>
<body class="reptile-bg">

<?php
require("php/connect.php");
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
?>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container-fluid nav-container d-flex flex-column align-items-center">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
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
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="clothes.php">Clothes</a></li>
                <li class="nav-item">
                    <a class="nav-link" href="sale.php">Sale</a>
                </li>
                <li class="nav-item"><a class="nav-link" href="signup.php">Sign Up</a></li>
                <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
            </ul>
        </div>
        <div class="icon-container position-absolute top-50 translate-middle-y d-flex align-items-center">
            <div class="cart-icon">
                <a href="billing.php">
                    <i class="fas fa-shopping-cart"></i>
                    <span id="cart-count">0</span>
                </a>
            </div>
            <div class="user-icon">
                <a href="login.php">
                    <i class="fas fa-user"></i>
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- Billing Section -->
<main class="main-content mt-5">
    <div class="container">
        <section id="billing" class="billing-container mx-auto mt-6">
            <h2 id="osszegzes_text" class="text-center mb-4">Összegzés</h2>
            <div class="cart-wrapper d-flex flex-wrap">
                <!-- Kosár (Bal oldal) -->
                <div class="cart-left">
                    <h4>Kosár tartalma</h4>
                    <ul id="cart-items" class="list-unstyled">
                        <!-- Kosár tételek betöltése -->
                        <ul id="cart-items" class="list-unstyled">
    <!-- Kosár tételek betöltése dinamikusan -->
</ul>
                    </ul>
                    <p id="total">Totál: 0.00Ft</p>
                </div>
                

                <!-- Összegzés (Jobb oldal) -->
                <div class="cart-right">
                    <h4 class="text-center">Adatok</h4>
                    <form id="checkout-form">
                        <label for="name">Teljes név:</label>
                        <input type="text" id="name" class="form-control" required>

                        <label for="email">E-mail:</label>
                        <input type="email" id="email" class="form-control" required>

                        <label for="phone">Telefonszám:</label>
                        <input type="tel" id="phone" class="form-control" required>

                        <label for="delivery">Szállítási mód:</label>
                        <select id="delivery" class="form-select">
                            <option value="home">Házhozszállítás</option>
                            <option value="pickup">Áruházi átvétel</option>
                        </select>

                        <button type="submit" id="place-order-btn" class="btn btn-success w-100 mt-3">Megrendelés</button>
                    </form>
                </div>
            </div>
        </section>
    </div>
</main>

<!-- Footer -->
<footer class="footer text-white text-center py-4 mt-auto">
    <div class="container">
        <p>© 2025 Heet Clothing | All rights reserved.</p>
        <a href="aszf.php" class="text-white text-decoration-none">ÁSZF</a>
        <div class="social-icons mt-3">
            <a href="https://www.facebook.com" class="text-white mx-3"><i class="fab fa-facebook-f"></i></a>
            <a href="https://www.instagram.com" class="text-white mx-3"><i class="fab fa-instagram"></i></a>
            <a href="https://www.tiktok.com" class="text-white mx-3"><i class="fab fa-tiktok"></i></a>
        </div>
    </div>
</footer>

<script src="java_script/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
