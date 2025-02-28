<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Profil</title>
</head>
<body class="reptile-bg">

<header>
<div class="nav-container">
    <div class="logo" onclick="window.location.href='index.php'">
        <img src="kepek/heet-logo-white.png" alt="Webshop Logo">
    </div>
    <nav>
        <a href="index.php">Home</a>
        <a href="index.php#products">Clothes</a>
        <a href="signup.php">Sign Up</a>
        <a href="about.php">About Us</a>
    </nav>
    <div class="nav-icons">
        <!-- Kosár ikon -->
        <div class="cart-icon">
            <a href="billing.php">
                <i class="fas fa-shopping-cart"></i>
                <span id="cart-count">0</span>
            </a>
        </div>

        <!-- Felhasználó ikon -->
        <div class="user-icon">
            <a href="login.php">
                <i class="fas fa-user"></i>
            </a>
        </div>
    </div>
</div>
</header>

<main>
    <section class="profile-section">
        <div class="profile-container">
            <h2>Üdv, <?= htmlspecialchars($_SESSION['username']); ?>!</h2>
            <p>Felhasználónév: <?= htmlspecialchars($_SESSION['username']); ?></p>
            <p>Email: <?= htmlspecialchars($_SESSION['email']); ?></p>
            <p>Jogosultság: <?= htmlspecialchars($_SESSION['jogosultsag']); ?></p>

            <?php if (isset($_SESSION['jogosultsag']) && ($_SESSION['jogosultsag'] === 'admin' || $_SESSION['jogosultsag'] === 'superadmin')): ?>
                <form action="admin.php" method="GET">
                    <button type="submit" class="admin-button">Admin Page</button>
                </form>
            <?php endif; ?>

            <form action="php/logout.php" method="POST">
                <button type="submit">Kijelentkezés</button>
            </form>
        </div>
    </section>
</main>

<footer>
    <p>&copy; 2025 Heet Clothing | The style that never burns out!</p>
</footer>

<script src="java_script/script.js"></script>
</body>
</html>
