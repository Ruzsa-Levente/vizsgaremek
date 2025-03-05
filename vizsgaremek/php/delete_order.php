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

    // Step 1: Get the products and quantities in this order
    $sql_get_items = "SELECT termek_azon, mennyiseg FROM tetelek WHERE rendeles_azon = ?";
    $stmt_get_items = $conn->prepare($sql_get_items);
    $stmt_get_items->bind_param("i", $order_id);
    $stmt_get_items->execute();
    $result = $stmt_get_items->get_result();

    // Step 2: Loop through the items and update the stock for each product
    while ($row = $result->fetch_assoc()) {
        $product_id = $row['termek_azon'];
        $quantity = $row['mennyiseg'];

        // Update the product stock
        $sql_update_stock = "UPDATE termekek SET keszlet = keszlet + ? WHERE azon = ?";
        $stmt_update_stock = $conn->prepare($sql_update_stock);
        $stmt_update_stock->bind_param("ii", $quantity, $product_id);
        $stmt_update_stock->execute();
    }

    // Step 3: Delete the related order items
    $sql_delete_items = "DELETE FROM tetelek WHERE rendeles_azon = ?";
    $stmt_items = $conn->prepare($sql_delete_items);
    $stmt_items->bind_param("i", $order_id);
    $stmt_items->execute();

    // Step 4: Delete the order itself
    $sql_delete_order = "DELETE FROM rendelesek WHERE azon = ?";
    $stmt_order = $conn->prepare($sql_delete_order);
    $stmt_order->bind_param("i", $order_id);
    $stmt_order->execute();

    // Redirect back to the order management page
    header("Location: ../manage_orders.php");
    exit();
}
?>
