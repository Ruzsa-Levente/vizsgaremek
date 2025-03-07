<?php
session_start();
require_once 'php/connect.php';

// Check if user has admin/superadmin permissions
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || 
   !isset($_SESSION['jogosultsag']) || ($_SESSION['jogosultsag'] !== 'admin' && $_SESSION['jogosultsag'] !== 'superadmin')) {
    header("Location: index.php");
    exit();
}

// Fetch all orders
$sql = "SELECT rendelesek.azon, vasarlok.nev, vasarlok.email, rendelesek.datum, rendelesek.osszesen, rendelesek.status 
        FROM rendelesek 
        JOIN vasarlok ON rendelesek.vasarlo_azon = vasarlok.azon 
        ORDER BY rendelesek.datum DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Rendelések Kezelése</title>
</head>
<body class="reptile-bg">

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
<main>
    <section class="profile-section">
        <div class="profile-container">
            <h2>Rendelések Kezelése</h2>
            
            <table class="order-table">
                <thead>
                    <tr>
                        <th>Rendelés ID</th>
                        <th>Vásárló</th>
                        <th>Email</th>
                        <th>Dátum</th>
                        <th>Összeg</th>
                        <th>Állapot</th>
                        <th>Akciók</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>#<?= htmlspecialchars($row['azon']); ?></td>
                            <td><?= htmlspecialchars($row['nev']); ?></td>
                            <td><?= htmlspecialchars($row['email']); ?></td>
                            <td><?= htmlspecialchars($row['datum']); ?></td>
                            <td><?= htmlspecialchars($row['osszesen']); ?> Ft</td>
                            <td><?= htmlspecialchars($row['status']); ?></td>
                            <td>
                                <!-- "Megnéz" Button -->
                                <form action="view_order.php" method="GET">
                                    <input type="hidden" name="order_id" value="<?= $row['azon']; ?>">
                                    <button type="submit">Megnéz</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <a href="admin.php" class="back-button">Vissza az admin panelre</a>
        </div>
    </section>
</main>



<footer>
    <p>&copy; 2025 Heet Clothing | The style that never burns out!</p>
</footer>

<script src="java_script/script.js"></script>
</body>
</html>
