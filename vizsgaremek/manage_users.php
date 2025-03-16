<?php
session_start();
require_once 'php/connect.php'; // Ensure this file contains the database connection setup

// Check if the user is logged in and has admin or superadmin privileges
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || 
   !isset($_SESSION['jogosultsag']) || ($_SESSION['jogosultsag'] !== 'admin' && $_SESSION['jogosultsag'] !== 'superadmin')) {
    header("Location: index.php");
    exit();
}

// Fetch all users from the database
$sql = "SELECT azon, felhasznalonev, email, jogosultsag FROM felhasznalok";
$result = $conn->query($sql);
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
    <title>Felhasználók kezelése</title>
</head>
<body class="reptile-bg">

<!-- Bootstrap Navbar -->
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container-fluid nav-container d-flex flex-column align-items-center">
        <!-- Toggle Button -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- Search Container -->
        <div class="search-container d-flex align-items-center">
            <i class="fas fa-search" id="search-toggle"></i>
            <input type="text" id="search-input" placeholder="Search..." value="<?= htmlspecialchars($searchTerm ?? '') ?>">
        </div>
        <!-- Logo -->
        <a class="navbar-brand logo mb-2" href="index.php">
            <img src="kepek/heet-logo-white.png" alt="Webshop Logo">
        </a>
        <!-- Navigation Links -->
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
                    <a class="nav-link" href="signup.php">Sign Up</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="about.php">About Us</a>
                </li>
            </ul>
        </div>
        <!-- Icons (Cart and User) -->
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
    <section class="profile-section">
        <div class="profile-container">
            <h2>Felhasználók Kezelése</h2>
            <p>Üdv, <?= htmlspecialchars($_SESSION['username']); ?>!</p>
            <hr>
            
            <div class="user-table-wrapper">
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Felhasználónév</th>
                            <th>Email</th>
                            <th>Jogosultság</th>
                            <th>Műveletek</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['azon']); ?></td>
                                <td><?= htmlspecialchars($row['felhasznalonev']); ?></td>
                                <td><?= htmlspecialchars($row['email']); ?></td>
                                <td><?= htmlspecialchars($row['jogosultsag']); ?></td>
                                <td>
                                    <?php if ($_SESSION['jogosultsag'] === 'superadmin'): ?>
                                        <form action="php/update_role.php" method="POST" class="inline-form">
                                            <input type="hidden" name="user_id" value="<?= $row['azon']; ?>">
                                            <select name="new_role">
                                                <option value="user" <?= $row['jogosultsag'] === 'user' ? 'selected' : ''; ?>>User</option>
                                                <option value="admin" <?= $row['jogosultsag'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                                <option value="superadmin" <?= $row['jogosultsag'] === 'superadmin' ? 'selected' : ''; ?>>Superadmin</option>
                                            </select>
                                            <button type="submit">Frissítés</button>
                                        </form>
                                    <?php endif; ?>
                                    
                                    <?php if ($_SESSION['jogosultsag'] === 'superadmin' || ($_SESSION['jogosultsag'] === 'admin' && $row['jogosultsag'] === 'user')): ?>
                                        <form action="php/delete_user.php" method="POST" class="inline-form">
                                            <input type="hidden" name="user_id" value="<?= $row['azon']; ?>">
                                            <button type="submit" class="delete-button">Törlés</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <a href="admin.php" class="back-button">Vissza az Admin Panelre</a>
        </div>
    </section>
</main>

<!-- Bootstrap Footer -->
<footer class="footer text-white text-center py-3 mt-auto">
    <p>© 2025 Heet Clothing | The style that never burns out!</p>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="java_script/script.js"></script>
</body>
</html>
