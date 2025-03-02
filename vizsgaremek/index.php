<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/style2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css">
    <script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
    <title>Heet Clothing</title>
    
</head>
<body>

<?php 
require("php/connect.php"); // Adatbázis kapcsolat

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
            <a href="index.php" class="current-page">Home</a>
            <a href="clothes.php">Clothes</a>
            <a href="signup.php">Sign Up</a>
            <a href="about.php">About Us</a>
        </nav>
        <div class="nav-icons">
            <div class="search-container">
                <i class="fas fa-search" onclick="toggleSearch()"></i>
                <input type="text" id="search-input" placeholder="Search..." onblur="hideSearch()" value="<?= htmlspecialchars($searchTerm ?? '') ?>">
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

<section class="hero">
    <div class="hero-content">
        <h1>HEET UP YOUR STYLE</h1>
        <p>Fuel your ambition. Stand out. Never slow down.</p>
        <a href="#products" class="shop-now-btn">Shop Now</a>
    </div>
</section>

<section class="slider-section">
    <div class="swiper">
        <div class="swiper-wrapper">
            <div class="swiper-slide">
                <img src="kepek/person_hoodie_ front.png" alt="Piros Póló">
            </div>
            <div class="swiper-slide">
                <img src="kepek/person_hoodie_ back.png" alt="Kék Farmer">
            </div>

            <div class="swiper-slide">
                <img src="kepek/zoldfront2.png" alt="Kék Farmer">
            </div>

            <div class="swiper-slide">
                <img src="kepek/zoldback2.png" alt="Kék Farmer">
            </div>
        </div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
        <div class="swiper-pagination"></div>
    </div>
</section>

<main>
    <section id="products">
        <h2>Trending Now</h2>
        <div class="product-grid">
        <?php
require("php/connect.php"); // Adatbázis kapcsolat

// Termékek lekérdezése az adatbázisból (csak az első 2)
$sql = "SELECT azon, nev, ar FROM termekek ORDER BY azon ASC LIMIT 2";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Termékek lekérdezése az adatbázisból (csak az első 2)
$sql = "SELECT azon, nev, ar FROM termekek ORDER BY azon ASC LIMIT 2";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($products as $index => $product): 
    // Alap kép lekérdezése
    $sql = "SELECT kep_url FROM termek_kepek WHERE termek_azon = :id LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $product['azon'], PDO::PARAM_INT);
    $stmt->execute();
    $kep = $stmt->fetchColumn() ?: 'no-image.jpg'; // Ha nincs kép, alapértelmezettet használ
    
    // Különböző hover képek a két termékre
    if ($index == 0) {
        // Első termék hover kép
        $hover_image = 'kepek/Person 3 Front.png'; // Statikus fájl neve
    } else {
        // Második termék hover kép
        $hover_image = 'kepek/person_hoodie_ front.png'; // Statikus fájl neve
    }
?>
    <div class="product" onclick="redirectToProduct(<?= $product['azon']; ?>)">
        <div class="product-image">
            <!-- Alap kép az adatbázisból -->
            <img src="kepek/<?= htmlspecialchars($kep); ?>" alt="<?= htmlspecialchars($product['nev']); ?>" class="default-image">
            <!-- Hover kép a statikus fájlokból -->
            <img src="<?= htmlspecialchars($hover_image); ?>" alt="Hover Image" class="hover-image">
        </div>
        <h3><?= htmlspecialchars($product['nev']); ?></h3>
        <p><?= number_format($product['ar'], 0, ',', ' ') ?> Ft</p>
    </div>
<?php endforeach; ?>


        </div>
    </section>
<!-- Ads Section -->
<section class="ads-section">
    <div class="ads-header">
        <h2>Legújabb kollekcióink</h2>
        <button class="show-ads-btn">Mutasd</button>
    </div>
    <div class="ad-container">
        <div class="ad">
            <img src="kepek/nayfront1.png" alt="Hirdetés 1">
            <div class="ad-text">
                <h2>Új kollekció érkezett!</h2>
                <p>Ne maradj le a legújabb darabokról, kattints és nézd meg!</p>
                <a href="clothes.php" class="shop-now-btn">Fedezd fel</a>
            </div>
        </div>
        <div class="ad">
            <img src="kepek/navyfrontnoi.png" alt="Hirdetés 2">
            <div class="ad-text">
                <h2>Exkluzív akciók!</h2>
                <p>Csak korlátozott ideig! Vásárolj most kedvezménnyel.</p>
                <a href="clothes.php" class="shop-now-btn">Vásárolj most</a>
            </div>
        </div>
    </div>
</section>

<!-- Marketing Section -->
<section class="marketing-section">
    <div class="marketing-content">
        <div class="marketing-text">
            <h2>Ne hagyd ki a legújabb ajánlatainkat!</h2>
            <p>Fedezd fel a legújabb kollekcióinkat, és találd meg a stílusodhoz illő darabokat! Csak nálunk kaphatók azok a trendi termékek, amikkel kiemelkedhetsz a tömegből! Az új szezon legjobb ajánlatai most elérhetőek – ne maradj le róluk!</p>
            <a href="clothes.php" class="shop-now-m-btn">Vásárolj most</a>
        </div>
        <div class="marketing-image">
            <img src="kepek/noifront3.png" alt="Marketing Image">
        </div>
    </div>
</section>

<!-- Extended Marketing Section -->
<section class="extended-marketing-section">
    <div class="extended-marketing-content">
        <!-- Kép balra -->
        <div class="extended-marketing-image">
            <img src="kepek/lifestyle.png" alt="Lifestyle Image">
        </div>
        <!-- Szöveg jobbra -->
        <div class="extended-marketing-text">
            <h2>Új Termékek és akciok!</h2>
            <p>Az új kollekcióinkkal most igazán megéri nálunk vásárolni. Friss stílusok, minőségi anyagok és különleges akciók várnak rád! Fedezd fel a legújabb trendeket, és találd meg a kedvencedet. Készen állsz arra, hogy felvidítsd a ruhatáradat?</p>
            <a href="clothes.php" class="shop-now-em-btn">Vásárolj most</a>
        </div>
    </div>
</section>





</main>
</main>

</main>

<!-- Alján lévő linkek -->
<div class="bottom-links">
    <!-- Regisztrációs link -->
    <div class="bottom-link">
        <a href="signup.php" class="signup-btn">
            <i class="fas fa-user-plus"></i> Ha még nincs fiókod, regisztrálj itt!
        </a>
    </div>

    <!-- Shop felfedezése link -->
    <div class="bottom-link">
        <a href="clothes.php" class="shop-btn">
            <i class="fas fa-tshirt"></i> Fedezd fel a shopot
        </a>
    </div>

</div>
</div>


<footer>
    <div class="footer-content">
        <p>&copy; 2025 Heet Clothing | The style that never burns out!</p>
        <a href="terms.php" class="terms-link">
            <i class="fas fa-file-contract"></i> Szerződési feltételek
        </a>
    </div>
</footer>



<script src="java_script/script.js"></script>
<script src="java_script/script2.js"></script>
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
