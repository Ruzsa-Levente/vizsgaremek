<?php
session_start();
require_once 'connect.php';

// Check permissions
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || 
   !isset($_SESSION['jogosultsag']) || ($_SESSION['jogosultsag'] !== 'admin' && $_SESSION['jogosultsag'] !== 'superadmin')) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['order_id']) && isset($_POST['new_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['new_status'];

    $sql = "UPDATE rendelesek SET status = ? WHERE azon = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_status, $order_id);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Rendelés állapota frissítve!";
    } else {
        $_SESSION['error_message'] = "Hiba történt a frissítés során.";
    }
    
    header("Location: ../view_order.php?order_id=$order_id");
    exit();
}
