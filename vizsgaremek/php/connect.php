<?php
// Adatbázis kapcsolat beállításai
$dsn = "mysql:host=localhost;dbname=webshop;charset=utf8mb4";
$username = "root";
$password = "";

try {
    // PDO kapcsolat létrehozása
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Kapcsolati hiba kezelése
    echo "Connection failed: " . $e->getMessage();
    exit();
}
?>
