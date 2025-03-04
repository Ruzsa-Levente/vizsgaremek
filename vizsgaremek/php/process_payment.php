<?php
require("connect.php");

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || empty($data['cartItems'])) {
    echo json_encode(["success" => false, "message" => "No order data received."]);
    exit;
}

$conn->begin_transaction(); // Tranzakció indítása

try {
    // Vásárló rögzítése (példa, ha már regisztrált, itt lekérheted az adatait)
    $vasarlo_nev = "Vendég"; // Ha van bejelentkezett user, akkor itt módosítsd
    $vasarlo_email = "guest@example.com";
    
    $stmt = $conn->prepare("INSERT INTO vasarlok (nev, email) VALUES (?, ?)");
    $stmt->bind_param("ss", $vasarlo_nev, $vasarlo_email);
    $stmt->execute();
    $vasarlo_id = $stmt->insert_id;
    $stmt->close();

    // Új rendelés létrehozása
    $datum = date("Y-m-d");
    $osszesen = $data["total"];
    
    $stmt = $conn->prepare("INSERT INTO rendelesek (vasarlo_azon, datum, osszesen) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $vasarlo_id, $datum, $osszesen);
    $stmt->execute();
    $rendeles_id = $stmt->insert_id;
    $stmt->close();

    // Tétel feldolgozása
    foreach ($data["cartItems"] as $item) {
        $termek_nev = $item["name"];
        $mennyiseg = $item["quantity"];

        // Ellenőrizzük a termék létezését és a készletet
        $stmt = $conn->prepare("SELECT azon, keszlet FROM termekek WHERE nev = ?");
        $stmt->bind_param("s", $termek_nev);
        $stmt->execute();
        $result = $stmt->get_result();
        $termek = $result->fetch_assoc();
        $stmt->close();

        if (!$termek || $termek["keszlet"] < $mennyiseg) {
            throw new Exception("Not enough stock for {$termek_nev}.");
        }

        $termek_azon = $termek["azon"];
        $uj_keszlet = $termek["keszlet"] - $mennyiseg;

        // Rendelési tétel hozzáadása
        $stmt = $conn->prepare("INSERT INTO tetelek (rendeles_azon, termek_azon, mennyiseg) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $rendeles_id, $termek_azon, $mennyiseg);
        $stmt->execute();
        $stmt->close();

        // Készlet frissítése
        $stmt = $conn->prepare("UPDATE termekek SET keszlet = ? WHERE azon = ?");
        $stmt->bind_param("ii", $uj_keszlet, $termek_azon);
        $stmt->execute();
        $stmt->close();
    }

    $conn->commit(); // Tranzakció véglegesítése
    echo json_encode(["success" => true, "message" => "Order placed successfully!"]);

} catch (Exception $e) {
    $conn->rollback(); // Tranzakció visszavonása hiba esetén
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}

$conn->close();
?>
