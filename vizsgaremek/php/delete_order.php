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

    // Start a transaction to ensure data consistency
    $conn->begin_transaction();

    try {
        // Step 1: Get the customer ID (vasarlo_azon) from the order
        $sql_get_customer = "SELECT vasarlo_azon FROM rendelesek WHERE azon = ?";
        $stmt_get_customer = $conn->prepare($sql_get_customer);
        $stmt_get_customer->bind_param("i", $order_id);
        $stmt_get_customer->execute();
        $customer_result = $stmt_get_customer->get_result();
        $customer_row = $customer_result->fetch_assoc();
        $customer_id = $customer_row['vasarlo_azon'] ?? null;
        $stmt_get_customer->close();

        if (!$customer_id) {
            throw new Exception("Nem található vásárló a rendeléshez!");
        }

        // Step 2: Get the products, quantities, and sizes in this order
        $sql_get_items = "SELECT termek_azon, mennyiseg, meret FROM tetelek WHERE rendeles_azon = ?";
        $stmt_get_items = $conn->prepare($sql_get_items);
        $stmt_get_items->bind_param("i", $order_id);
        $stmt_get_items->execute();
        $result = $stmt_get_items->get_result();

        // Step 3: Loop through the items and update the size-specific stock for each product
        while ($row = $result->fetch_assoc()) {
            $product_id = $row['termek_azon'];
            $quantity = $row['mennyiseg'];
            $size = $row['meret'];

            // Update the stock in termek_meretek for the specific size
            $sql_update_stock = "UPDATE termek_meretek SET keszlet = keszlet + ? WHERE termek_azon = ? AND meret = ?";
            $stmt_update_stock = $conn->prepare($sql_update_stock);
            $stmt_update_stock->bind_param("iis", $quantity, $product_id, $size);
            $stmt_update_stock->execute();
            $stmt_update_stock->close();
        }
        $stmt_get_items->close();

        // Step 4: Delete the related order items
        $sql_delete_items = "DELETE FROM tetelek WHERE rendeles_azon = ?";
        $stmt_items = $conn->prepare($sql_delete_items);
        $stmt_items->bind_param("i", $order_id);
        $stmt_items->execute();
        $stmt_items->close();

        // Step 5: Delete the order itself
        $sql_delete_order = "DELETE FROM rendelesek WHERE azon = ?";
        $stmt_order = $conn->prepare($sql_delete_order);
        $stmt_order->bind_param("i", $order_id);
        $stmt_order->execute();
        $stmt_order->close();

        // Step 6: Delete the customer from vasarlok table
        $sql_delete_customer = "DELETE FROM vasarlok WHERE azon = ?";
        $stmt_customer = $conn->prepare($sql_delete_customer);
        $stmt_customer->bind_param("i", $customer_id);
        $stmt_customer->execute();
        $stmt_customer->close();

        // Commit the transaction
        $conn->commit();

        // Redirect back to the order management page
        header("Location: ../manage_orders.php");
        exit();
    } catch (Exception $e) {
        // Roll back the transaction on error
        $conn->rollback();
        // Optional: Log the error or display a message
        die("Hiba történt: " . $e->getMessage());
    }
}

$conn->close();
?>
