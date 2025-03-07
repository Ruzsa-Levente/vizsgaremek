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
$sql = "SELECT azon, nev, ar FROM termekek WHERE (nev LIKE :searchTerm OR leiras LIKE :searchTerm) AND keszlet > 0 $sort_order";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':searchTerm', '%' . $searchTerm . '%', PDO::PARAM_STR);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Clothes - Heet Clothing</title>
</head>
<body>

<header>
    <div class="nav-container">
        <div class="logo" onclick="window.location.href='index.php'">
            <img src="kepek/heet-logo-white.png" alt="Webshop Logo">
        </div>
        <nav>
            <a href="index.php " >Home</a>
            <a href="clothes.php" class="current-page">Clothes</a>
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

<!-- Main content of clothes page -->
<main>
    <!-- Rendezés fül -->
    <div class="sorting-container">
        <label for="sort-by">Rendezés:</label>
        <select id="sort-by" onchange="location = this.value;">
            <option value="clothes.php?sort=price_asc" <?= isset($_GET['sort']) && $_GET['sort'] == 'price_asc' ? 'selected' : '' ?>>Legolcsóbb</option>
            <option value="clothes.php?sort=price_desc" <?= isset($_GET['sort']) && $_GET['sort'] == 'price_desc' ? 'selected' : '' ?>>Legdrágább</option>
            <option value="clothes.php?sort=popularity" <?= isset($_GET['sort']) && $_GET['sort'] == 'popularity' ? 'selected' : '' ?>>Legnépszerűbb</option>
            <option value="clothes.php?sort=name_asc" <?= isset($_GET['sort']) && $_GET['sort'] == 'name_asc' ? 'selected' : '' ?>>A-Z</option>
            <option value="clothes.php?sort=name_desc" <?= isset($_GET['sort']) && $_GET['sort'] == 'name_desc' ? 'selected' : '' ?>>Z-A</option>
        </select>
    </div>

    <div class="cont">
    <?php
    if (count($products) > 0):
        foreach ($products as $product):
            // A termék első képének lekérdezése
            $sql = "SELECT kep_url FROM termek_kepek WHERE termek_azon = :id LIMIT 1 ";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $product['azon'], PDO::PARAM_INT);
            $stmt->execute();
            $kep = $stmt->fetchColumn() ?: 'no-image.jpg'; // Ha nincs kép, alapértelmezett kép
            
            ?>
            <div class="product-card">
                <!-- Kép linkelése a termék oldalára -->
                <a href="product.php?id=<?= $product['azon']; ?>" class="product-card__image-link">
                    <div class="product-card__image">
                        <img src="kepek/<?= htmlspecialchars($kep); ?>" alt="<?= htmlspecialchars($product['nev']); ?>">
                    </div>
                </a>
                <div class="product-card__info">
                    <h2 class="product-card__title"><?= htmlspecialchars($product['nev']); ?></h2>
                    <div class="product-card__price-row">
                        <span class="product-card__price"><?= number_format($product['ar'], 0, ',', ' ') ?> Ft</span>
                        <a href="product.php?id=<?= $product['azon']; ?>" class="product-card__btn">Részletek</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Nincs találat a keresésre.</p>
    <?php endif; ?>
</div>


</main>

<footer>
    <p>&copy; 2025 Heet Clothing | The style that never burns out!</p>
</footer>

<script src="java_script/script.js"></script>

</body>
</html>
