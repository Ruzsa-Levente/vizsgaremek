<?php
// Start session
session_start();

// Include database connection
require_once 'connect.php'; 

// Check if the user is logged in and has appropriate permissions
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../index.php");
    exit();
}

// Get user ID to delete
if (isset($_POST['user_id']) && is_numeric($_POST['user_id'])) {
    $userId = $_POST['user_id'];
    $loggedInUserId = $_SESSION['user_id']; // Store logged-in user ID
    $loggedInRole = $_SESSION['jogosultsag']; // Store logged-in user role

    // Prevent the logged-in user from deleting themselves
    if ($userId == $loggedInUserId) {
        echo "You cannot delete your own account.";
        exit();
    }

    // If the logged-in user is an admin, check if the user is an admin or superadmin
    if ($loggedInRole === 'admin') {
        $sql = "SELECT jogosultsag FROM felhasznalok WHERE azon = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // If the user to be deleted is an admin or superadmin, prevent deletion
        if ($user && ($user['jogosultsag'] === 'admin' || $user['jogosultsag'] === 'superadmin')) {
            echo "You cannot delete other admins or superadmins.";
            exit();
        }
    }

    // Proceed with deletion if the checks pass
    try {
        // Start a transaction
        $pdo->beginTransaction();

        // Delete the user from the database
        $sql = "DELETE FROM felhasznalok WHERE azon = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);

        // Commit the transaction
        $pdo->commit();

        // Redirect back to the user management page
        header("Location: ../manage_users.php");
        exit();

    } catch (Exception $e) {
        // Rollback in case of an error
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid user ID.";
    exit();
}
?>
