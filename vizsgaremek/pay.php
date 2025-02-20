<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Payment</title>
</head>
<body class="reptile-bg">

<?php
require("php/connect.php");
?>

<header>
    <div class="nav-container">
    <div class="logo" onclick="window.location.href='index.php'">
    <img src="kepek/heet-logo-white.png" alt="Webshop Logo">
    </div>
        <nav>
            <a href="index.php" class="active">Home</a>
            <a href="billing.php">Back to Cart</a>
            <a href="signup.php">Sign Up</a>
        </nav>
    </div>
</header>

<section id="payment">
    <h2>Payment Details</h2>
    <div id="order-summary">
        <h3>Order Summary</h3>
        <ul id="order-items"></ul>
        <p id="order-total">Total: $0.00</p>
        <p id="delivery-method"></p>
    </div>

    <!-- Szállítási adatok (Csak Home Delivery esetén jelenik meg) -->
    <div id="delivery-form" style="display: none;">
        <h3>Shipping Information</h3>
        <form id="shipping-form">
            <label for="name">Full Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="address">Address:</label>
            <input type="text" id="address" name="address" required>

            <label for="city">City:</label>
            <input type="text" id="city" name="city" required>

            <label for="zip">ZIP Code:</label>
            <input type="text" id="zip" name="zip" required>

            <label for="phone">Phone Number:</label>
            <input type="text" id="phone" name="phone" required>
        </form>
    </div>

    <button id="pay-now-btn">Proceed to Payment</button>
</section>

<footer>
    <p>&copy; 2025 Heet Clothing | The style that never burns out!</p>
</footer>


<script src="java_script/script.js"></script>
</body>
</html>
