<?php
require("php/connect.php");

// Rendelés ID lekérése az URL-ből
$rendeles_id = isset($_GET['rendeles_id']) ? (int)$_GET['rendeles_id'] : 0;

$vasarlo = [];
$tetelek = [];
$osszesen = 0;

if ($rendeles_id > 0) {
    // Vásárló adatainak lekérdezése
    $stmt = $conn->prepare("
        SELECT v.nev, v.email, v.telefon, v.cim
        FROM vasarlok v
        JOIN rendelesek r ON v.azon = r.vasarlo_azon
        WHERE r.azon = ?
    ");
    $stmt->bind_param("i", $rendeles_id);
    $stmt->execute();
    $vasarlo = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // Rendelés tételeinek lekérdezése - discounted_price hozzáadása
    $stmt = $conn->prepare("
        SELECT t.mennyiseg, t.meret, p.nev, p.ar, p.discounted_price
        FROM tetelek t
        JOIN termekek p ON t.termek_azon = p.azon
        WHERE t.rendeles_azon = ?
    ");
    $stmt->bind_param("i", $rendeles_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $tetelek[] = $row;
    }
    $stmt->close();

    // Összesen lekérdezése
    $stmt = $conn->prepare("SELECT osszesen FROM rendelesek WHERE azon = ?");
    $stmt->bind_param("i", $rendeles_id);
    $stmt->execute();
    $osszesen = $stmt->get_result()->fetch_assoc()['osszesen'];
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <title>Heet Clothing - Sikeres vásárlás</title>
</head>
<body class="reptile-bg">
    <!-- Navbar (változatlan) -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container-fluid nav-container d-flex flex-column align-items-center">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="search-container d-flex align-items-center">
                <i class="fas fa-search" id="search-toggle"></i>
                <input type="text" id="search-input" placeholder="Search...">
            </div>
            <a class="navbar-brand logo mb-2" href="index.php">
                <img src="kepek/heet-logo-white.png" alt="Webshop Logo">
            </a>
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <button class="navbar-close-btn" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-label="Close navigation">
                    <i class="fas fa-times"></i>
                </button>
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="clothes.php">Clothes</a></li>
                    <li class="nav-item"><a class="nav-link" href="sale.php">Sale</a></li>
                    <li class="nav-item"><a class="nav-link" href="signup.php">Sign Up</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
                </ul>
            </div>
            <div class="icon-container position-absolute top-50 translate-middle-y d-flex align-items-center">
                <div class="cart-icon d-flex align-items-center">
                    <a href="billing.php"><i class="fas fa-shopping-cart"></i><span id="cart-count">0</span></a>
                </div>
                <div class="user-icon d-flex align-items-center">
                    <a href="login.php"><i class="fas fa-user"></i></a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <section class="success-container container my-5 text-center">
            <div class="success-header animate__fadeIn">
                <h1 class="success-title">Köszönjük a vásárlást!</h1>
                <p class="success-subtitle">A rendelésedet sikeresen feldolgoztuk.</p>
            </div>

            <?php if (!empty($vasarlo) && !empty($tetelek)): ?>
                <div class="order-summary animate__fadeInUp animate__delay-1s">
                    <h2 class="order-summary-title">Rendelés összefoglaló</h2>
                    <div class="order-details">
                        <div class="order-info">
                            <div class="order-item"><span class="order-label">Név:</span> <span class="order-value"><?php echo htmlspecialchars($vasarlo['nev']); ?></span></div>
                            <div class="order-item"><span class="order-label">Email:</span> <span class="order-value"><?php echo htmlspecialchars($vasarlo['email']); ?></span></div>
                            <div class="order-item"><span class="order-label">Telefon:</span> <span class="order-value"><?php echo htmlspecialchars($vasarlo['telefon']); ?></span></div>
                            <div class="order-item"><span class="order-label">Szállítási cím:</span> <span class="order-value"><?php echo htmlspecialchars($vasarlo['cim']); ?></span></div>
                        </div>
                        <div class="order-items">
                            <h3 class="order-items-title">Rendelt tételek</h3>
                            <ul class="items-list">
                                <?php foreach ($tetelek as $tetel): ?>
                                    <?php
                                    $effective_price = (isset($tetel['discounted_price']) && $tetel['discounted_price'] !== null && $tetel['discounted_price'] < $tetel['ar'])
                                        ? $tetel['discounted_price']
                                        : $tetel['ar'];
                                    ?>
                                    <li class="item">
                                        <?php echo htmlspecialchars($tetel['nev']) . " (" . htmlspecialchars($tetel['meret']) . ") - " . $tetel['mennyiseg'] . " db - " . number_format($effective_price * $tetel['mennyiseg'], 0, ',', ' ') . " Ft"; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="order-total"><span class="order-label">Összesen:</span> <span class="order-value"><?php echo number_format($osszesen, 0, ',', ' ') . " Ft"; ?></span></div>
                    </div>
                </div>
            <?php endif; ?>

            <a href="index.php" class="success-btn shop-now-btn animate__fadeInUp animate__delay-2s">Vissza a főoldalra</a>
        </section>
    </main>

    <!-- Footer (változatlan) -->
    <footer class="footer text-white text-center py-4 mt-auto">
        <div class="container">
            <div class="footer-content">
                <p class="mb-2">© 2025 Heet Clothing | All rights reserved.</p>
                <a href="aszf.php" class="text-white text-decoration-none mb-3 d-inline-block">ÁSZF</a>
                <br>
                <div class="social-icons mt-3 d-flex justify-content-center align-items-center">
                    <span class="text-white me-3">Stay Connected:</span>
                    <a href="https://www.facebook.com/profile.php?id=61574451329401#" target="_blank" class="text-white mx-3"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.instagram.com/heetclothinghu/" target="_blank" class="text-white mx-3"><i class="fab fa-instagram"></i></a>
                    <a href="https://www.tiktok.com/@heet.clothing" target="_blank" class="text-white mx-3"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="java_script/script.js"></script>
</body>
</html>
