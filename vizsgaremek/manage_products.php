<?php

session_start();

// Check if the user is logged in and has the correct permissions
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || 
   !isset($_SESSION['jogosultsag']) || ($_SESSION['jogosultsag'] !== 'admin' && $_SESSION['jogosultsag'] !== 'superadmin')) {
    header("Location: index.php");
    exit();
}

// Include database connection
include 'php/connect.php';

// Fetch products
$sql = "SELECT * FROM termekek";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Manage Products</title>
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
            <div class="cart-icon">
                <a href="billing.php">
                    <i class="fas fa-shopping-cart"></i>
                    <span id="cart-count">0</span>
                </a>
            </div>
            <div class="user-icon">
                <a href="profile.php">
                    <i class="fas fa-user"></i>
                </a>
            </div>
        </div>
    </div>
</header>

<main>
    <h2>Manage Products</h2>

    <a href="add_product.php"><button>Új termék hozzáadása</button></a>

    <table class="white">
        <thead>
            <tr>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?= htmlspecialchars($product['nev']); ?></td>
                    <td><?= htmlspecialchars($product['kategoria']); ?></td>
                    <td><?= htmlspecialchars($product['ar']); ?> HUF</td>
                    <td><?= htmlspecialchars($product['keszlet']); ?></td>
                    <td>
                        <a href="edit_product.php?id=<?= $product['azon']; ?>">Edit</a>
                        <!-- Delete button link -->
                        <a href="php/delete_product.php?id=<?= $product['azon']; ?>" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>



<footer>
    <!-- Your footer content -->
</footer>

<script src="java_script/script.js"></script>
</body>
</html>
