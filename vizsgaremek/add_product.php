<?php
session_start();

// Check if the user is logged in and has the correct permissions
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || 
   !isset($_SESSION['jogosultsag']) || ($_SESSION['jogosultsag'] !== 'admin' && $_SESSION['jogosultsag'] !== 'superadmin')) {
    header("Location: index.php");
    exit();
}

// Connect to the database
require_once 'php/connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Form data
    $productName = $_POST['name'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    // Image upload handling
    $imageNames = []; // Store image names only (without the "kepek/" path)
    if (isset($_FILES['images'])) {
        $imageCount = count($_FILES['images']['name']);
        for ($i = 0; $i < $imageCount; $i++) {
            $imageTmpName = $_FILES['images']['tmp_name'][$i];
            $imageName = basename($_FILES['images']['name'][$i]); // Get only the name of the image

            // Define the path where the image will be saved
            $imagePath = "kepek/" . $imageName;

            // Move uploaded file to the 'kepek' folder
            if (move_uploaded_file($imageTmpName, $imagePath)) {
                $imageNames[] = $imageName; // Save only the image name, not the full path
            }
        }
    }

    // Insert product into the database
    try {
        $pdo->beginTransaction();

        // Insert product
        $stmt = $pdo->prepare("INSERT INTO termekek (nev, leiras, kategoria, ar, keszlet) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$productName, $description, $category, $price, $stock]);
        $productID = $pdo->lastInsertId(); // Get the last inserted product ID

        // Insert images into 'termek_kepek' table (storing only the image name without "kepek/" path)
        $stmt = $pdo->prepare("INSERT INTO termek_kepek (termek_azon, kep_url) VALUES (?, ?)");
        foreach ($imageNames as $imageName) {
            $stmt->execute([$productID, $imageName]); // Save only the image name (not full path)
        }

        $pdo->commit();

        // Redirect to manage products page after successful insertion
        header("Location: manage_products.php");
        exit();  // Always call exit() after header redirection to prevent further code execution
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Add Product</title>
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

    <h2>Add New Product</h2>
    <form action="add_product.php" method="POST" enctype="multipart/form-data">
        <label for="name">Product Name</label>
        <input type="text" id="name" name="name" required><br>

        <label for="description">Description</label>
        <textarea id="description" name="description" required></textarea><br>

        <label for="category">Category</label>
        <input type="text" id="category" name="category"><br>

        <label for="price">Price</label>
        <input type="number" id="price" name="price" required><br>

        <label for="stock">Stock</label>
        <input type="number" id="stock" name="stock" required><br>

        <label for="images">Product Images</label>
        <input type="file" name="images[]" accept="image/*" multiple><br>

        <button type="submit">Add Product</button>
        <button type="button" onclick="window.location.href='manage_products.php'">Back to Manage Products</button>
    </form>

<footer>
    <p>&copy; 2025 Heet Clothing | The style that never burns out!</p>
</footer>

<script src="java_script/script.js"></script>
</body>
</html>
