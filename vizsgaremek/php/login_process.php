<?php
session_start();

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: ../profile.php");
    exit();
}

require("connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    try {
        $sql = "SELECT * FROM felhasznalok WHERE felhasznalonev = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['jelszo'])) {
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $user['azon'];
            $_SESSION['username'] = $user['felhasznalonev'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['jogosultsag'] = $user['jogosultsag'];

            header("Location: ../profile.php");
            exit();
        } else {
            header("Location: ../login.php?error=1");
            exit();
        }
    } catch (PDOException $e) {
        header("Location: ../login.php?error=db");
        exit();
    }
}
?>
