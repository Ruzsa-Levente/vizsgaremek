<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>About Us</title>
</head>
<body class="reptile-bg">

<?php
require("php/connect.php");
?>

<header>
    <div class="nav-container">
        <div class="logo" onclick="window.location.href='index.php'">
            <img src="kepek/heet-logo-white.png" alt="Webshop Logo">
        </div>
        <nav>
            <a href="index.php " >Home</a>
            <a href="clothes.php" >Clothes</a>
            <a href="signup.php">Sign Up</a>
            <a href="about.php" class="current-page">About Us</a>
        </nav>
    </div>
    <!-- Kereső ikon és kereső mező -->
<div class="search-container">
    <i class="fas fa-search" onclick="toggleSearch()"></i>
    <input type="text" id="search-input" placeholder="Search..." onblur="hideSearch()" value="<?= htmlspecialchars($searchTerm ?? '') ?>">
</div>
    
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
</header>

<main>
    <section id="about">
        <h2>About Heet</h2>
        <p>Welcome to Heet – where fashion meets passion. We are dedicated to crafting high-quality, stylish apparel that empowers confidence and individuality.</p>
        
        <h3>Who Are We?</h3>
        <p>Heet is more than just a clothing brand; it's a movement. Designed for trendsetters and go-getters, we create fashion-forward pieces that blend comfort, quality, and bold aesthetics.</p>
        
        <h3>Who Is Our Target Audience?</h3>
        <p>Our brand is built for those who live life on their own terms. Whether you're into streetwear, casual fits, or standout statement pieces, Heet has something for you. We cater to both men and women who value self-expression through fashion.</p>
        
        <h3>Where Can You Find Us?</h3>
        <p>Shop Heet online or visit us in person:</p>
        <p><strong>Heet Store</strong><br>
           123 Fashion Street,<br>
           Style City, SC 98765</p>
        <p>Got questions? Reach out to us or drop by – let’s turn up the heat in fashion together!</p>
    </section>
</main>

<script src="java_script/script.js"></script>
<footer>
<p>&copy; 2025 Heet Clothing | The style that never burns out!</p>
</footer>

</body>
</html>
