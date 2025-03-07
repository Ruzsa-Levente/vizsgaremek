<?php
// connect.php betöltése
require("php/connect.php");

// Lekérjük a terméket az adatbázisból, az id alapján
$product_id = isset($_GET['id']) ? $_GET['id'] : 1; // Ha nincs megadva id, akkor 1-es terméket tölt be
$sql = "SELECT * FROM termekek WHERE azon = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $product_id, PDO::PARAM_INT);
$stmt->execute();
$product = $stmt->fetch(PDO::FETCH_ASSOC);

// Ha nincs ilyen termék, akkor 404-es hiba oldal
if (!$product) {
    echo "Termék nem található!";
    exit();
}

// Lekérjük a termékhez tartozó képeket
$sql = "SELECT kep_url FROM termek_kepek WHERE termek_azon = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $product_id, PDO::PARAM_INT);
$stmt->execute();
$images = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Ha vannak képek, az elsőt használjuk fő képként
$main_image = !empty($images) ? $images[0] : "no-image.jpg";
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title><?= htmlspecialchars($product['nev']); ?></title>
</head>
<body>
<header>
    <div class="nav-container">
        <div class="logo" onclick="window.location.href='index.php'">
            <img src="kepek/heet-logo-white.png" alt="Webshop Logo">
        </div>
        <nav>
            <a href="index.php " >Home</a>
            <a href="clothes.php">Clothes</a>
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

    <!-- Product Section -->
    <section class="product-section">
        <div class="product-details">
<!-- Termék Képcsúszka -->
        <div class="product-slider">
    <button class="prev" onclick="prevImage()">&#10094;</button>
    
    <div class="slider-container">
        <?php foreach ($images as $index => $image): ?>
            <img src="kepek/<?= htmlspecialchars($image); ?>" 
                 alt="<?= htmlspecialchars($product['nev']); ?>" 
                 class="slide <?= $index === 0 ? 'active' : '' ?>" 
                 id="slide-<?= $index ?>">
        <?php endforeach; ?>
    </div>

    <button class="next" onclick="nextImage()">&#10095;</button>
</div>

            <!-- Termék információk -->
            <div class="product-info">
                <h1><?= htmlspecialchars($product['nev']); ?></h1>
                <p class="price"><?= number_format($product['ar'], 0, ',', ' ') ?> Ft</p>
                <p class="description"><?= htmlspecialchars($product['leiras']); ?></p>
                
                <!-- Változatok, méret és szín (ha van) -->
                <div class="product-options">
                    <label for="size">Size:</label>
                    <div class="size-selector">
                        <select id="size" name="size">
                            <option value="small">S</option>
                            <option value="medium">M</option>
                            <option value="large">L</option>
                        </select>
                    </div>
                </div>

                <!-- Kosárba rakás és Vásárlás gombok -->
                <div class="product-buttons">
                <button class="buy-btn" onclick="addToCart('<?= htmlspecialchars($product['nev']); ?>', <?= $product['ar']; ?>, 'kepek/<?= htmlspecialchars($main_image); ?>')">Add to cart</button>
                <button class="buy-btn" onclick="addToCart('<?= htmlspecialchars($product['nev']); ?>', <?= $product['ar']; ?>, 'kepek/<?= htmlspecialchars($main_image); ?>'); window.location.href='billing.php'">Buy now</button>

                </div>
            </div>
        </div>
    </section>

    <footer>
         <p>&copy; 2025 Heet Clothing | The style that never burns out!</p>
    </footer>

    <script src="java_script/script.js"></script>
    <script>
        function changeImage(imgElement) {
            document.querySelector(".main-image").src = imgElement.src;
        }
    </script>
</body>
</html>
