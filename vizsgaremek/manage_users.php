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
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Felhasználók Kezelése</title>
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
    <section class="profile-section">
        <div class="profile-container">
            <h2>Felhasználók Kezelése</h2>
            <p>Üdv, <?= htmlspecialchars($_SESSION['username']); ?>!</p>
            <hr>
            
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

            <a href="admin.php" class="back-button">Vissza az Admin Panelre</a>
        </div>
    </section>
</main>

<footer>
    <p>&copy; 2025 Heet Clothing | The style that never burns out!</p>
</footer>

<script src="java_script/script.js"></script>
</body>
</html>
