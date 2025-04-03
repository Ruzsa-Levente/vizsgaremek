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
    $sizes_data = isset($_POST['sizes']) ? $_POST['sizes'] : []; // Méretadatok

    // Image upload handling
    $imageNames = [];
    if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
        $imageCount = count($_FILES['images']['name']);
        for ($i = 0; $i < $imageCount; $i++) {
            $imageTmpName = $_FILES['images']['tmp_name'][$i];
            $imageName = basename($_FILES['images']['name'][$i]);
            $imagePath = "kepek/" . $imageName;

            if (move_uploaded_file($imageTmpName, $imagePath)) {
                $imageNames[] = $imageName;
            }
        }
    }

    // Insert product into the database
    try {
        $pdo->beginTransaction();

        // Insert product (keszlet kezdetben 0, a trigger majd frissíti)
        $stmt = $pdo->prepare("INSERT INTO termekek (nev, leiras, kategoria, ar, keszlet) VALUES (?, ?, ?, ?, 0)");
        $stmt->execute([$productName, $description, $category, $price]);
        $productID = $pdo->lastInsertId();

        // Insert images into 'termek_kepek' table
        if (!empty($imageNames)) {
            $stmt = $pdo->prepare("INSERT INTO termek_kepek (termek_azon, kep_url) VALUES (?, ?)");
            foreach ($imageNames as $imageName) {
                $stmt->execute([$productID, $imageName]);
            }
        }

        // Insert sizes into 'termek_meretek' table
        if (!empty($sizes_data)) {
            $stmt = $pdo->prepare("INSERT INTO termek_meretek (termek_azon, meret, keszlet) VALUES (?, ?, ?)");
            foreach ($sizes_data as $size) {
                if (!empty($size['meret']) && isset($size['keszlet']) && $size['keszlet'] >= 0) {
                    $stmt->execute([$productID, $size['meret'], $size['keszlet']]);
                }
            }
        }

        $pdo->commit();

        // Redirect to manage products page
        header("Location: manage_products.php");
        exit();
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css">
    <title>Add Product</title>
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

<main style="margin-top: 200px;" class="container-distance">
    <h2 id="manage2-text">Add New Product</h2>
    <form action="add_product.php" method="POST" enctype="multipart/form-data">
        <label for="name">Product Name</label>
        <input type="text" id="name" name="name" required><br>

        <label for="description">Description</label>
        <textarea id="description" name="description" required></textarea><br>

        <label for="category">Category</label>
        <input type="text" id="category" name="category"><br>

        <label for="price">Price</label>
        <input type="number" id="price" name="price" required><br>

        <!-- Sizes Section -->
        <h3>Sizes</h3>
        <div id="sizes-container">
            <div class="size-row">
                <input type="text" name="sizes[0][meret]" placeholder="Size (e.g., S, M, L)" required>
                <input type="number" name="sizes[0][keszlet]" placeholder="Stock" min="0" required>
                <button type="button" class="remove-size-btn" onclick="removeSizeRow(this)">Remove</button>
            </div>
        </div>
        <button type="button" id="add-size-btn">Add Size</button><br>

        <label for="images">Product Images</label>
        <input type="file" name="images[]" accept="image/*" multiple><br>

        <button type="submit">Add Product</button>
        <button type="button" onclick="window.location.href='manage_products.php'">Back to Manage Products</button>
    </form>
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

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="java_script/script.js"></script>
<script>
    // Add new size row
    document.getElementById('add-size-btn').addEventListener('click', function() {
        const container = document.getElementById('sizes-container');
        const index = container.children.length;
        const newRow = document.createElement('div');
        newRow.className = 'size-row';
        newRow.innerHTML = `
            <input type="text" name="sizes[${index}][meret]" placeholder="Size (e.g., S, M, L)" required>
            <input type="number" name="sizes[${index}][keszlet]" placeholder="Stock" min="0" required>
            <button type="button" class="remove-size-btn" onclick="removeSizeRow(this)">Remove</button>
        `;
        container.appendChild(newRow);
    });

    // Remove size row
    function removeSizeRow(button) {
        button.parentElement.remove();
    }
</script>
</body>
</html>
