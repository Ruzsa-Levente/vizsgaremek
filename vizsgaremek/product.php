<?php
// connect.php betöltése
require("php/connect.php");

// Alapértelmezett rendezés
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'alphabetical';

// Rendezési feltételek
switch ($sort) {
    case 'best_selling':
        $sql = "SELECT azon, nev, ar FROM termekek ORDER BY sales DESC";
        break;
    case 'highest_price':
        $sql = "SELECT azon, nev, ar FROM termekek ORDER BY ar DESC";
        break;
    case 'lowest_price':
        $sql = "SELECT azon, nev, ar FROM termekek ORDER BY ar ASC";
        break;
    case 'alphabetical':
    default:
        $sql = "SELECT azon, nev, ar FROM termekek ORDER BY nev ASC";
        break;
}

// Termék lekérése ID alapján
$product_id = isset($_GET['id']) ? $_GET['id'] : 1;
$sql = "SELECT * FROM termekek WHERE azon = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $product_id, PDO::PARAM_INT);
$stmt->execute();
$product = $stmt->fetch(PDO::FETCH_ASSOC);

// Ha nincs ilyen termék
if (!$product) {
    echo "Termék nem található!";
    exit();
}

// Termék képek lekérése
$sql = "SELECT kep_url FROM termek_kepek WHERE termek_azon = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $product_id, PDO::PARAM_INT);
$stmt->execute();
$images = $stmt->fetchAll(PDO::FETCH_COLUMN);
$main_image = !empty($images) ? $images[0] : "no-image.jpg";

// Véletlenszerű ajánlott termékek (kivéve az aktuálisat)
$sql = "SELECT azon, nev, ar, (SELECT kep_url FROM termek_kepek WHERE termek_azon = t.azon LIMIT 1) AS kep_url 
        FROM termekek t 
        WHERE azon != :id 
        ORDER BY RAND() 
        LIMIT 7";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $product_id, PDO::PARAM_INT);
$stmt->execute();
$random_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                <a href="index.php">Home</a>
                <a href="clothes.php">Clothes</a>
                <a href="signup.php">Sign Up</a>
                <a href="about.php">About Us</a>
            </nav>
            <div class="nav-icons">
                <div class="search-container">
                    <i class="fas fa-search" onclick="toggleSearch()"></i>
                    <input type="text" id="search-input" placeholder="Search..." onblur="hideSearch()">
                </div>
            </div>
        </div>
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
    </header>

    <section class="product-section">
        <div class="product-details">
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

            <div class="product-info">
                <h1><?= htmlspecialchars($product['nev']); ?></h1>
                <p class="price"><?= number_format($product['ar'], 0, ',', ' ') ?> Ft</p>
                <p class="description"><?= htmlspecialchars($product['leiras']); ?></p>
                
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

                <div class="product-buttons">
                    <button class="buy-btn" onclick="addToCart('<?= htmlspecialchars($product['nev']); ?>', <?= $product['ar']; ?>, 'kepek/<?= htmlspecialchars($main_image); ?>')">Add to cart</button>
                    <button class="buy-btn" onclick="addToCart('<?= htmlspecialchars($product['nev']); ?>', <?= $product['ar']; ?>, 'kepek/<?= htmlspecialchars($main_image); ?>'); window.location.href='billing.php'">Buy now</button>
                </div>
            </div>
        </div>
    </section>

    <section class="suggested-products">
        <h2>Ajánlott termékek</h2>
        <div class="suggested-scroll-container">
            <div class="suggested-slider">
                <?php foreach ($random_products as $product): ?>
                    <div class="suggested-card">
                        <a href="product.php?id=<?= $product['azon'] ?>">
                            <img src="kepek/<?= htmlspecialchars($product['kep_url'] ?? 'no-image.jpg') ?>" alt="<?= htmlspecialchars($product['nev']) ?>">
                            <h3><?= htmlspecialchars($product['nev']) ?></h3>
                            <p class="price"><?= number_format($product['ar'], 0, ',', ' ') ?> Ft</p>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>
    </section>

    <footer>
        <p>&copy; 2025 Heet Clothing | The style that never burns out!</p>
    </footer>

    <script src="java_script/script.js"></script>
    <script>
        function scrollSuggestedLeft() {
            document.querySelector(".suggested-slider").scrollBy({ left: -220, behavior: "smooth" });
        }

        function scrollSuggestedRight() {
            document.querySelector(".suggested-slider").scrollBy({ left: 220, behavior: "smooth" });
        }
    </script>
</body>
</html>
