<?php
session_start();
require_once 'connect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['jogosultsag'] !== 'superadmin') {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['user_id'], $_POST['new_role'])) {
    $user_id = intval($_POST['user_id']);
    $new_role = $_POST['new_role'];

    if (!in_array($new_role, ['user', 'admin', 'superadmin'])) {
        exit("Érvénytelen jogosultság.");
    }

    $stmt = $conn->prepare("UPDATE felhasznalok SET jogosultsag = ? WHERE azon = ?");
    $stmt->bind_param("si", $new_role, $user_id);

    if ($stmt->execute()) {
        header("Location: ../manage_users.php");
        exit();
    } else {
        echo "Hiba a frissítés során.";
    }
}
?>
