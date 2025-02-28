<?php
session_start();
require_once 'php/connect.php';

// Check if user has admin/superadmin permissions
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || 
   !isset($_SESSION['jogosultsag']) || ($_SESSION['jogosultsag'] !== 'admin' && $_SESSION['jogosultsag'] !== 'superadmin')) {
    header("Location: index.php");
    exit();
}

// Check if order ID is provided
if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    header("Location: manage_orders.php");
    exit();
}

$order_id = $_GET['order_id'];

// Fetch order details
$sql_order = "SELECT rendelesek.azon, vasarlok.nev, vasarlok.email, rendelesek.datum, rendelesek.osszesen, rendelesek.status 
              FROM rendelesek 
              JOIN vasarlok ON rendelesek.vasarlo_azon = vasarlok.azon 
              WHERE rendelesek.azon = ?";
$stmt = $conn->prepare($sql_order);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

// Fetch ordered items
$sql_items = "SELECT termekek.nev, tetelek.mennyiseg, termekek.ar, (tetelek.mennyiseg * termekek.ar) AS subtotal 
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
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Rendelés Részletei</title>
</head>
<body class="reptile-bg">

<header>
<div class="nav-container">
    <div class="logo" onclick="window.location.href='index.php'">
        <img src="kepek/heet-logo-white.png" alt="Webshop Logo">
    </div>
    <nav>
        <a href="index.php">Home</a>
        <a href="index.php#products">Clothes</a>
        <a href="signup.php">Sign Up</a>
        <a href="about.php">About Us</a>
    </nav>
    <div class="nav-icons">
        <!-- Kosár ikon -->
        <div class="cart-icon">
            <a href="billing.php">
                <i class="fas fa-shopping-cart"></i>
                <span id="cart-count">0</span>
            </a>
        </div>

        <!-- Felhasználó ikon -->
        <div class="user-icon">
            <a href="profile.php">
                <i class="fas fa-user"></i>
            </a>
        </div>
    </div>
</div>
</header>

<main>
    <section class="profile-section">
        <div class="profile-container">
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
                        <th>Mennyiség</th>
                        <th>Egységár</th>
                        <th>Részösszeg</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $items->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['nev']); ?></td>
                            <td><?= htmlspecialchars($row['mennyiseg']); ?></td>
                            <td><?= htmlspecialchars($row['ar']); ?> Ft</td>
                            <td><?= htmlspecialchars($row['subtotal']); ?> Ft</td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <hr>

            <!-- Order Status Update -->
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

            <!-- Order Delete Button -->
            <form action="php/delete_order.php" method="POST" onsubmit="return confirm('Biztosan törölni szeretné ezt a rendelést?');">
                <input type="hidden" name="order_id" value="<?= $order_id ?>">
                <button type="submit" class="delete-button">Rendelés törlése</button>
            </form>

            <a href="manage_orders.php" class="back-button">Vissza a rendeléskezelőhöz</a>
        </div>
    </section>
</main>

<footer>
    <p>&copy; 2025 Heet Clothing | The style that never burns out!</p>
</footer>

<script src="java_script/script.js"></script>
</body>
</html>
