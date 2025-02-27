<?php
session_start();
require("connect.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Felhasználói adatok tisztítása
        $felhasznalonev = htmlspecialchars($_POST['felhasznalonev'], ENT_QUOTES, 'UTF-8');
        $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
        $jelszo = password_hash($_POST['jelszo'], PASSWORD_DEFAULT); // Jelszó titkosítása

        // Ellenőrizzük, hogy az email vagy a felhasználónév már létezik-e
        $check_sql = "SELECT * FROM felhasznalok WHERE felhasznalonev = :felhasznalonev OR email = :email";
        $check_stmt = $pdo->prepare($check_sql);
        $check_stmt->execute(['felhasznalonev' => $felhasznalonev, 'email' => $email]);

        if ($check_stmt->rowCount() > 0) {
            echo "Error: Username or email already exists!";
            exit();
        }

        // Új felhasználó beszúrása az adatbázisba
        $sql = "INSERT INTO felhasznalok (felhasznalonev, email, jelszo) VALUES (:felhasznalonev, :email, :jelszo)";
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':felhasznalonev', $felhasznalonev, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':jelszo', $jelszo, PDO::PARAM_STR);

        if ($stmt->execute()) {
            // Sikeres regisztráció után automatikus bejelentkezés
            // Lekérjük az új felhasználó adatait
            $sql = "SELECT * FROM felhasznalok WHERE felhasznalonev = :felhasznalonev";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['felhasznalonev' => $felhasznalonev]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Sikeres belépés, session beállítása
                $_SESSION['loggedin'] = true;
                $_SESSION['user_id'] = $user['azon'];
                $_SESSION['username'] = $user['felhasznalonev'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['jogosultsag'] = $user['jogosultsag'];

                // Redirect to index.php with the success query parameter
                header("Location: ../index.php?signup=success");
                exit();
            } else {
                echo "Error: User data could not be retrieved.";
            }
        } else {
            echo "Error: Unable to register.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
