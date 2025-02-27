<?php
session_start();

// Kijelentkezés: session törlése
session_unset();
session_destroy();

// Átirányítás a login oldalra
header("Location: ../login.php");
exit();
