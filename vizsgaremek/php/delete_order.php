<?php
session_start();
require_once 'connect.php';

// Check permissions
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || 
   !isset($_SESSION['jogosultsag']) || ($_SESSION['jogosultsag'] !== 'admin' && $_SESSION['jogosultsag'] !== 'superadmin')) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];

    // First, delete related order items
    $sql_delete_items = "DELETE FROM tetelek WHERE rendeles_azon = ?";
    $stmt_items = $conn->prepare($sql_delete_items);
    $stmt_items->bind_param("i", $order_id);
    $stmt_items->execute();

    // Then, delete the order itself
    $sql_delete_order = "DELETE FROM rendelesek WHERE azon = ?";
    $stmt_order = $conn->prepare($sql_delete_order);
    $stmt_order->bind_param("i", $order_id);
    
    $stmt_order->execute();

    header("Location: ../manage_orders.php");
    exit();
}
