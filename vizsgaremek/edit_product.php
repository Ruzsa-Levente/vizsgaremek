<?php

session_start();

// Check if the user is logged in and has the correct permissions
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || 
   !isset($_SESSION['jogosultsag']) || ($_SESSION['jogosultsag'] !== 'admin' && $_SESSION['jogosultsag'] !== 'superadmin')) {
    header("Location: index.php");
    exit();
}

require_once 'php/connect.php';

if (isset($_GET['id'])) {
    $productId = $_GET['id'];

    // Fetch product details
    $stmt = $pdo->prepare("SELECT * FROM termekek WHERE azon = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();

    // Fetch associated images
    $stmt = $pdo->prepare("SELECT * FROM termek_kepek WHERE termek_azon = ?");
    $stmt->execute([$productId]);
    $images = $stmt->fetchAll();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_product'])) {
    // Form data
    $productName = $_POST['name'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    // Image upload handling
    $imageURLs = [];
    if (isset($_FILES['images'])) {
        $imageCount = count($_FILES['images']['name']);
        for ($i = 0; $i < $imageCount; $i++) {
            $imageTmpName = $_FILES['images']['tmp_name'][$i];
            $imageName = basename($_FILES['images']['name'][$i]);
            $imagePath = "kepek/" . $imageName;

            // Move uploaded file to the 'kepek' folder
            if (move_uploaded_file($imageTmpName, $imagePath)) {
                // Save only the image name (without the 'kepek/' prefix) to the database
                $imageURLs[] = $imageName;
            }
        }
    }

    // Update product details
    try {
        $pdo->beginTransaction();

        // Update product
        $stmt = $pdo->prepare("UPDATE termekek SET nev = ?, leiras = ?, kategoria = ?, ar = ?, keszlet = ? WHERE azon = ?");
        $stmt->execute([$productName, $description, $category, $price, $stock, $productId]);

        // Insert new images into 'termek_kepek' table (only store the image name without the 'kepek/' prefix)
        $stmt = $pdo->prepare("INSERT INTO termek_kepek (termek_azon, kep_url) VALUES (?, ?)");
        foreach ($imageURLs as $imageURL) {
            $stmt->execute([$productId, $imageURL]);
        }

        $pdo->commit();

    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }

    // Refresh the page to show newly uploaded images
    header("Location: edit_product.php?id=" . $productId);
    exit();
}

// Handle image deletion
if (isset($_GET['delete_image_id'])) {
    $imageId = $_GET['delete_image_id'];

    // Fetch the image to get the file path
    $stmt = $pdo->prepare("SELECT * FROM termek_kepek WHERE azon = ?");
    $stmt->execute([$imageId]);
    $image = $stmt->fetch();

    if ($image) {
        $imagePath = "kepek/" . $image['kep_url'];  // Add 'kepek/' to the stored image name

        // Delete the image from the database
        $stmt = $pdo->prepare("DELETE FROM termek_kepek WHERE azon = ?");
        $stmt->execute([$imageId]);

        // Delete the image file from the server
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        echo "Image deleted successfully!";
        header("Location: edit_product.php?id=" . $productId); // Redirect back to the edit page
        exit();
    } else {
        echo "Image not found!";
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
    <title>Edit Product</title>
</head>

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

<body class="reptile-bg">
    <h2>Edit Product</h2>
    <form action="edit_product.php?id=<?= $product['azon'] ?>" method="POST" enctype="multipart/form-data">
        <label for="name">Product Name</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($product['nev']) ?>" required><br>

        <label for="description">Description</label>
        <textarea id="description" name="description" required><?= htmlspecialchars($product['leiras']) ?></textarea><br>

        <label for="category">Category</label>
        <input type="text" id="category" name="category" value="<?= htmlspecialchars($product['kategoria']) ?>"><br>

        <label for="price">Price</label>
        <input type="number" id="price" name="price" value="<?= $product['ar'] ?>" required><br>

        <label for="stock">Stock</label>
        <input type="number" id="stock" name="stock" value="<?= $product['keszlet'] ?>" required><br>

        <label for="images">Product Images</label>
        <input type="file" name="images[]" accept="image/*" multiple><br>

        <button type="submit" name="update_product">Update Product</button>
        <button type="button" onclick="window.location.href='manage_products.php'">Back to Manage Products</button>
    </form>

 
    <h3>Existing Images</h3>
    <ul>
        <?php foreach ($images as $image): ?>
            <li>
                <img src="kepek/<?= $image['kep_url'] ?>" alt="Product Image" width="100">
                <a href="edit_product.php?id=<?= $product['azon'] ?>&delete_image_id=<?= $image['azon'] ?>" onclick="return confirm('Are you sure you want to delete this image?')">Delete</a>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
