<?php
require("connect.php");

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || empty($data['cartItems'])) {
    echo json_encode(["success" => false, "message" => "Nincs rendelési adat!"]);
    exit;
}

$conn->begin_transaction();

try {
    // Vásárló adatainak rögzítése
    $deliveryMethod = $data['delivery'];
    $customer = $data['customer'];

    if ($deliveryMethod === "home") {
        $vasarlo_nev = $data['shipping']['name'] ?? $customer['name'];
        $vasarlo_email = $data['shipping']['email'] ?? $customer['email'];
        $vasarlo_telefon = $data['shipping']['phone'] ?? $customer['phone'];
        $vasarlo_cim = $data['shipping']['address'] . ", " . $data['shipping']['city'] . " " . $data['shipping']['zip'];
    } else {
        $vasarlo_nev = $data['shipping']['name'] ?? $customer['name'];
        $vasarlo_email = $data['shipping']['email'] ?? $customer['email'];
        $vasarlo_telefon = $data['shipping']['phone'] ?? $customer['phone'];
        $vasarlo_cim = "In Store Pickup";
    }

    $stmt = $conn->prepare("INSERT INTO vasarlok (nev, email, telefon, cim) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $vasarlo_nev, $vasarlo_email, $vasarlo_telefon, $vasarlo_cim);
    $stmt->execute();
    $vasarlo_id = $stmt->insert_id;
    $stmt->close();

    // Rendelés létrehozása
    $datum = date("Y-m-d");
    $osszesen = $data["total"];
    $stmt = $conn->prepare("INSERT INTO rendelesek (vasarlo_azon, datum, osszesen) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $vasarlo_id, $datum, $osszesen);
    $stmt->execute();
    $rendeles_id = $stmt->insert_id;
    $stmt->close();

    // Tételek feldolgozása
    foreach ($data["cartItems"] as $item) {
        $termek_nev = $item["name"];
        $mennyiseg = $item["quantity"];
        $meret = $item["size"];

        $stmt = $conn->prepare("SELECT azon FROM termekek WHERE nev = ?");
        $stmt->bind_param("s", $termek_nev);
        $stmt->execute();
        $termek = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$termek) {
            throw new Exception("A(z) {$termek_nev} termék nem található!");
        }

        $termek_azon = $termek["azon"];

        $stmt = $conn->prepare("SELECT keszlet FROM termek_meretek WHERE termek_azon = ? AND meret = ?");
        $stmt->bind_param("is", $termek_azon, $meret);
        $stmt->execute();
        $meret_adat = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$meret_adat || $meret_adat["keszlet"] < $mennyiseg) {
            throw new Exception("Nincs elegendő készlet a(z) {$termek_nev} termékből ({$meret} méret)!");
        }

        $stmt = $conn->prepare("INSERT INTO tetelek (rendeles_azon, termek_azon, mennyiseg, meret) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $rendeles_id, $termek_azon, $mennyiseg, $meret);
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare("UPDATE termek_meretek SET keszlet = keszlet - ? WHERE termek_azon = ? AND meret = ?");
        $stmt->bind_param("iis", $mennyiseg, $termek_azon, $meret);
        $stmt->execute();
        $stmt->close();
    }

    $conn->commit();
    // Sikeres válasz JSON-ban, de az átirányítást a kliens oldalon kezeljük, így itt nem kell header
    echo json_encode(["success" => true, "message" => "A rendelés sikeresen feldolgozva!", "rendeles_id" => $rendeles_id]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}

$conn->close();
?>
