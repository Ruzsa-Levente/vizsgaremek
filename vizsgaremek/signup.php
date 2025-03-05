<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/style2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Signup</title>
</head>
<body class="hexa-bg">

<?php
require("php/connect.php");


// Keresési kifejezés
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Alapértelmezett rendezési feltétel
$sort_order = "ORDER BY nev ASC"; // A-Z alapértelmezett rendezés

// Ha van rendezési paraméter
if (isset($_GET['sort'])) {
    switch ($_GET['sort']) {
        case 'price_asc':
            $sort_order = "ORDER BY ar ASC"; // Legolcsóbb
            break;
        case 'price_desc':
            $sort_order = "ORDER BY ar DESC"; // Legdrágább
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

// SQL lekérdezés a keresési kifejezés alapján
$sql = "SELECT azon, nev, ar FROM termekek WHERE nev LIKE :searchTerm OR leiras LIKE :searchTerm $sort_order";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':searchTerm', '%' . $searchTerm . '%', PDO::PARAM_STR);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<header>
<div class="nav-container">
<div class="logo" onclick="window.location.href='index.php'">
    <img src="kepek/heet-logo-white.png" alt="Webshop Logo">
    </div>
        <nav>
            <a href="index.php" >Home</a>
            <a href="clothes.php">Clothes</a>
            <a href="signup.php" class="current-page">Sign Up</a>
            <a href="about.php">About Us</a>
        </nav>
        <div class="nav-icons">
             <!-- Search bar icon and input -->
             <div class="search-container">
                <i class="fas fa-search" onclick="toggleSearch()"></i>
                <input type="text" id="search-input" placeholder="Search..." onblur="hideSearch()" value="<?= htmlspecialchars($searchTerm ?? '') ?>">
            </div>
        </div>
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
</div>
    </div>
    </header>

<h3 id="signup-text">Signup if you dont have an account yet!</h3>
<div class="signup">
    <form action="php/signup_process.php" method="post">
        <input type="text" name="felhasznalonev" placeholder="Username" required>
        <input type="email" name="email" placeholder="E-mail" required>
        <input type="password" name="jelszo" placeholder="Password" required>
        <button type="submit">Signup</button>
    </form>
</div>

<script src="java_script/script.js"></script>
<script src="java_script/script2.js"></script>
<footer>
    <p>&copy; 2025 Heet Clothing | The style that never burns out!</p>
</footer>
<script>
    function toggleSearch() {
        const searchContainer = document.querySelector('.search-container');
        searchContainer.classList.toggle('active');
        document.getElementById('search-input').focus();
    }

    function hideSearch() {
        const searchInput = document.getElementById('search-input');
        if (!searchInput.value) {
            const searchContainer = document.querySelector('.search-container');
            searchContainer.classList.remove('active');
        }
    }

    function redirectToProduct(productId) {
        window.location.href = `product.php?id=${productId}`;
    }
</script>
</body>
</html>



