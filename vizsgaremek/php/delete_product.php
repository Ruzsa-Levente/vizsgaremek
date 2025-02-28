<?php
// Include database connection
include 'connect.php';

// Check if the 'id' parameter is passed in the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $productId = $_GET['id'];

    try {
        // Start a transaction to ensure data consistency
        $pdo->beginTransaction();

        // Get the image names for the product from the termek_kepek table
        $sql = "SELECT kep_url FROM termek_kepek WHERE termek_azon = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$productId]);
        $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Delete the product images from the kepek folder
        foreach ($images as $image) {
            $imagePath = "../kepek/" . $image['kep_url']; // Full path to the image file
            if (file_exists($imagePath)) {
                unlink($imagePath); // Delete the file from the server
            }
        }

        // Delete the image records from the termek_kepek table
        $sql = "DELETE FROM termek_kepek WHERE termek_azon = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$productId]);

        // Delete the product from the termekek table
        $sql = "DELETE FROM termekek WHERE azon = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$productId]);

        // Commit the transaction
        $pdo->commit();

        // Redirect back to manage products page
        header("Location: ../manage_products.php");
        exit();
    } catch (Exception $e) {
        // Rollback the transaction if an error occurs
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
} else {
    // If no valid 'id' is provided
    echo "Invalid product ID.";
}
?>
