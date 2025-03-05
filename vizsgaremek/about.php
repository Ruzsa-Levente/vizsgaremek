<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/style2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>About Us</title>
</head>
<body class="reptile-bg">

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
            <a href="index.php">Home</a>
            <a href="clothes.php">Clothes</a>
            <a href="signup.php">Sign Up</a>
            <a href="about.php" class="current-page">About Us</a>
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
