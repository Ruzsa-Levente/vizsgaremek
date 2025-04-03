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
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch associated images
    $stmt = $pdo->prepare("SELECT * FROM termek_kepek WHERE termek_azon = ?");
    $stmt->execute([$productId]);
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch associated sizes
    $stmt = $pdo->prepare("SELECT * FROM termek_meretek WHERE termek_azon = ?");
    $stmt->execute([$productId]);
    $sizes = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_product'])) {
    // Form data
    $productName = $_POST['name'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $sizes_data = isset($_POST['sizes']) ? $_POST['sizes'] : []; // Méretadatok

    // Image upload handling
    $imageURLs = [];
    if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
        $imageCount = count($_FILES['images']['name']);
        for ($i = 0; $i < $imageCount; $i++) {
            $imageTmpName = $_FILES['images']['tmp_name'][$i];
            $imageName = basename($_FILES['images']['name'][$i]);
            $imagePath = "kepek/" . $imageName;

            if (move_uploaded_file($imageTmpName, $imagePath)) {
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

        // Insert new images into 'termek_kepek' table
        if (!empty($imageURLs)) {
            $stmt = $pdo->prepare("INSERT INTO termek_kepek (termek_azon, kep_url) VALUES (?, ?)");
            foreach ($imageURLs as $imageURL) {
                $stmt->execute([$productId, $imageURL]);
            }
        }

        // Update or insert sizes into 'termek_meretek' table
        $stmt_delete = $pdo->prepare("DELETE FROM termek_meretek WHERE termek_azon = ?");
        $stmt_delete->execute([$productId]); // Először töröljük a régi méreteket

        if (!empty($sizes_data)) {
            $stmt_insert = $pdo->prepare("INSERT INTO termek_meretek (termek_azon, meret, keszlet) VALUES (?, ?, ?)");
            foreach ($sizes_data as $size) {
                if (!empty($size['meret']) && isset($size['keszlet']) && $size['keszlet'] >= 0) {
                    $stmt_insert->execute([$productId, $size['meret'], $size['keszlet']]);
                }
            }
        }

        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }

    // Refresh the page
    header("Location: edit_product.php?id=" . $productId);
    exit();
}

// Handle image deletion
if (isset($_GET['delete_image_id'])) {
    $imageId = $_GET['delete_image_id'];

    $stmt = $pdo->prepare("SELECT * FROM termek_kepek WHERE azon = ?");
    $stmt->execute([$imageId]);
    $image = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($image) {
        $imagePath = "kepek/" . $image['kep_url'];
        $stmt = $pdo->prepare("DELETE FROM termek_kepek WHERE azon = ?");
        $stmt->execute([$imageId]);
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
        header("Location: edit_product.php?id=" . $productId);
        exit();
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
    <title>Edit Product</title>
</head>



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

<body class="reptile-bg">
    <main class="container-distance">
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

            <label for="stock">Total Stock</label>
            <input type="number" id="stock" name="stock" value="<?= $product['keszlet'] ?>" required><br>

            <!-- Sizes Section -->
            <h3>Sizes</h3>
            <div id="sizes-container">
                <?php foreach ($sizes as $index => $size): ?>
                    <div class="size-row">
                        <input type="text" name="sizes[<?= $index ?>][meret]" value="<?= htmlspecialchars($size['meret']) ?>" placeholder="Size (e.g., S, M, L)" required>
                        <input type="number" name="sizes[<?= $index ?>][keszlet]" value="<?= $size['keszlet'] ?>" placeholder="Stock" min="0" required>
                        <button type="button" class="remove-size-btn" onclick="removeSizeRow(this)">Remove</button>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" id="add-size-btn">Add Size</button><br>

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
