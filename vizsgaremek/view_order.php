<?php
session_start();
require_once 'php/connect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || 
   !isset($_SESSION['jogosultsag']) || ($_SESSION['jogosultsag'] !== 'admin' && $_SESSION['jogosultsag'] !== 'superadmin')) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    header("Location: manage_orders.php");
    exit();
}

$order_id = $_GET['order_id'];

$sql_order = "SELECT rendelesek.azon, vasarlok.nev, vasarlok.email, rendelesek.datum, rendelesek.osszesen, rendelesek.status 
              FROM rendelesek 
              JOIN vasarlok ON rendelesek.vasarlo_azon = vasarlok.azon 
              WHERE rendelesek.azon = ?";
$stmt = $conn->prepare($sql_order);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

$sql_items = "SELECT termekek.nev, tetelek.mennyiseg, termekek.ar, (tetelek.mennyiseg * termekek.ar) AS subtotal, tetelek.meret 
              FROM tetelek 
              JOIN termekek ON tetelek.termek_azon = termekek.azon 
              WHERE tetelek.rendeles_azon = ?";
$stmt = $conn->prepare($sql_items);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css">
    <title>Rendelés Részletei</title>
</head>
<body class="reptile-bg">

<!-- Bootstrap Navbar -->
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container-fluid nav-container d-flex flex-column align-items-center">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="search-container d-flex align-items-center">
            <i class="fas fa-search" id="search-toggle"></i>
            <input type="text" id="search-input" placeholder="Search..." value="<?= htmlspecialchars($searchTerm ?? '') ?>">
        </div>
        <a class="navbar-brand logo mb-2" href="index.php">
            <img src="kepek/heet-logo-white.png" alt="Webshop Logo">
        </a>
        <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
            <button class="navbar-close-btn" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-label="Close navigation">
                <i class="fas fa-times"></i>
            </button>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="clothes.php">Clothes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="sale.php">Sale</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="signup.php">Sign Up</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="about.php">About Us</a>
                </li>
            </ul>
        </div>
        <div class="icon-container position-absolute top-50 translate-middle-y d-flex align-items-center">
            <div class="cart-icon d-flex align-items-center">
                <a href="billing.php">
                    <i class="fas fa-shopping-cart"></i>
                    <span id="cart-count">0</span>
                </a>
            </div>
            <div class="user-icon d-flex align-items-center">
                <a href="login.php">
                    <i class="fas fa-user"></i>
                </a>
            </div>
        </div>
    </div>
</nav>

<main>
    <section class="profile-section container-distance2">
        <div class="admin-profile-container">
            <h2>Rendelés Részletei</h2>
            <p><strong>Vásárló:</strong> <?= htmlspecialchars($order['nev']); ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($order['email']); ?></p>
            <p><strong>Dátum:</strong> <?= htmlspecialchars($order['datum']); ?></p>
            <p><strong>Összeg:</strong> <?= htmlspecialchars($order['osszesen']); ?> Ft</p>
            <p><strong>Állapot:</strong> <?= htmlspecialchars($order['status']); ?></p>
            
            <h3>Megrendelt Termékek</h3>
            <table class="order-table">
                <thead>
                    <tr>
                        <th>Termék</th>
                        <th>Méret</th>
                        <th>Mennyiség</th>
                        <th>Egységár</th>
                        <th>Részösszeg</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $items->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['nev']); ?></td>
                            <td><?= htmlspecialchars($row['meret']); ?></td>
                            <td><?= htmlspecialchars($row['mennyiseg']); ?></td>
                            <td><?= htmlspecialchars($row['ar']); ?> Ft</td>
                            <td><?= htmlspecialchars($row['subtotal']); ?> Ft</td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <hr>
            <form action="php/update_order_status.php" method="POST">
                <input type="hidden" name="order_id" value="<?= $order_id ?>">
                <label for="new_status">Állapot módosítása:</label>
                <select name="new_status" id="new_status">
                    <option value="Pending" <?= $order['status'] == 'Pending' ? 'selected' : '' ?>>Függőben</option>
                    <option value="Fulfilled" <?= $order['status'] == 'Fulfilled' ? 'selected' : '' ?>>Teljesítve</option>
                    <option value="Canceled" <?= $order['status'] == 'Canceled' ? 'selected' : '' ?>>Törölve</option>
                </select>
                <button type="submit">Frissítés</button>
            </form>

            <hr>
            <form action="php/delete_order.php" method="POST" onsubmit="return confirm('Biztosan törölni szeretné ezt a rendelést?');">
                <input type="hidden" name="order_id" value="<?= $order_id ?>">
                <button type="submit" class="delete-button">Rendelés törlése</button>
            </form>

            <a href="manage_orders.php" class="back-button">Vissza a rendeléskezelőhöz</a>
        </div>
    </section>
</main>

<!-- Bootstrap Footer -->
<footer class="footer text-white text-center py-4 mt-auto">
    <div class="container">
        <div class="footer-content">
            <p class="mb-2">© 2025 Heet Clothing | All rights reserved.</p>
            <a href="aszf.php" class="text-white text-decoration-none mb-3 d-inline-block">ÁSZF</a>
            <br>
            <div class="social-icons mt-3 d-flex justify-content-center align-items-center">
                <span class="text-white me-3">Stay Connected:</span>
                <a href="https://www.facebook.com/profile.php?id=61574451329401#" target="_blank" class="text-white mx-3">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="https://www.instagram.com/heetclothinghu/" target="_blank" class="text-white mx-3">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="https://www.tiktok.com/@heet.clothing" target="_blank" class="text-white mx-3">
                    <i class="fab fa-tiktok"></i>
                </a>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="java_script/script.js"></script>
</body>
</html>
