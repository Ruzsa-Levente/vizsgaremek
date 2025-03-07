<?php
session_start();
// Ha már be vagy jelentkezve, ne jelenjen meg a login oldal, hanem irányíts a profil oldalra
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: profile.php"); // Itt irányítjuk a profil oldalra
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
    <title>Login</title>
</head>
<body class="reptile-bg">

<header>
    <div class="nav-container">
        <div class="logo" onclick="window.location.href='index.php'">
            <img src="kepek/heet-logo-white.png" alt="Webshop Logo">
        </div>
        <nav>
            <a href="index.php " >Home</a>
            <a href="clothes.php" >Clothes</a>
            <a href="signup.php">Sign Up</a>
            <a href="about.php">About Us</a>
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
    <section class="login-section">
        <div class="login-container">
            <h2>Login</h2>

            <?php
            if (isset($_GET['error'])) {
                if ($_GET['error'] == "1") {
                    echo "<p class='error-message'>Hibás felhasználónév vagy jelszó!</p>";
                } elseif ($_GET['error'] == "db") {
                    echo "<p class='error-message'>Adatbázishiba! Próbáld újra később.</p>";
                }
            }
            ?>

            <form action="php/login_process.php" method="POST">
                <div class="form-group">
                    <label for="username">Felhasználónév:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Jelszó:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <input type="submit" value="Bejelentkezés">
                </div>
            </form>

            <div class="redirect">
                <p>Még nincs fiókod? <a href="signup.php">Regisztrálj!</a></p>
            </div>
        </div>
    </section>
</main>

<footer>
    <p>&copy; 2025 Heet Clothing | The style that never burns out!</p>
</footer>

<script src="java_script/script.js"></script>
</body>
</html>
