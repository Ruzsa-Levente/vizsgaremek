<?php
require("php/connect.php");
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/style2.css">
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
            <a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'current-page' : '' ?>">Home</a>
            <a href="clothes.php" class="<?= basename($_SERVER['PHP_SELF']) == 'clothes.php' ? 'current-page' : '' ?>">Clothes</a>
            <a href="signup.php" class="<?= basename($_SERVER['PHP_SELF']) == 'signup.php' ? 'current-page' : '' ?>">Sign Up</a>
            <a href="about.php" class="<?= basename($_SERVER['PHP_SELF']) == 'about.php' ? 'current-page' : '' ?>">About Us</a>
        </nav>
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
    <div class="cont">
        <?php
        try {
            // Termékek lekérdezése az adatbázisból
            $sql = "SELECT azon, nev, ar FROM termekek"; // Módosított SQL, hogy csak az ár és név jöjjön
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($products as $product):
                // A termék első képének lekérdezése
                $sql = "SELECT kep_url FROM termek_kepek WHERE termek_azon = :id LIMIT 1";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $product['azon'], PDO::PARAM_INT);
                $stmt->execute();
                $kep = $stmt->fetchColumn() ?: 'no-image.jpg'; // Ha nincs kép, alapértelmezett kép
                
                ?>
                <div class="product-card">
                    <div class="product-card__image">
                        <img src="kepek/<?= htmlspecialchars($kep); ?>" alt="<?= htmlspecialchars($product['nev']); ?>">
                    </div>
                    <div class="product-card__info">
                        <h2 class="product-card__title"><?= htmlspecialchars($product['nev']); ?></h2>
                        <div class="product-card__price-row">
                            <span class="product-card__price"><?= number_format($product['ar'], 0, ',', ' ') ?> Ft</span>
                            <a href="product.php?id=<?= $product['azon']; ?>" class="product-card__btn">Részletek</a>
                        </div>
                    </div>
                </div>
                <?php
            endforeach;
        } catch (PDOException $e) {
            echo "Hiba történt: " . $e->getMessage();
        }
        ?>
    </div>

</div>

</main>

<footer>
    <p>&copy; 2025 Heet Clothing | The style that never burns out!</p>
</footer>

<script src="java_script/script.js"></script>
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
