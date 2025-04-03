-- Termékek tábla (készlet dinamikusan frissül a termek_meretek alapján)
CREATE TABLE termekek (
    azon INT AUTO_INCREMENT PRIMARY KEY,
    nev VARCHAR(100) NOT NULL,
    leiras TEXT,
    kategoria VARCHAR(20),
    ar INT NOT NULL,
    keszlet INT NOT NULL DEFAULT 0, -- Összes készlet, a termek_meretek alapján frissül
    discounted_price DECIMAL(10,2) DEFAULT NULL
);

-- Termék képek tábla
CREATE TABLE termek_kepek (
    azon INT AUTO_INCREMENT PRIMARY KEY,
    termek_azon INT NOT NULL,
    kep_url VARCHAR(255) NOT NULL,
    FOREIGN KEY (termek_azon) REFERENCES termekek(azon) ON DELETE CASCADE
);

-- Termék méretek tábla (méret-specifikus készletkezelés)
CREATE TABLE termek_meretek (
    azon INT AUTO_INCREMENT PRIMARY KEY,
    termek_azon INT NOT NULL,
    meret VARCHAR(10) NOT NULL, -- Pl. "S", "M", "L"
    keszlet INT NOT NULL DEFAULT 0,
    FOREIGN KEY (termek_azon) REFERENCES termekek(azon) ON DELETE CASCADE
);

-- Vásárlók tábla
CREATE TABLE vasarlok (
    azon INT AUTO_INCREMENT PRIMARY KEY,
    nev VARCHAR(30) NOT NULL,
    email VARCHAR(25) NOT NULL,
    telefon VARCHAR(15),
    cim VARCHAR(80)
);

-- Rendelések tábla
CREATE TABLE rendelesek (
    azon INT AUTO_INCREMENT PRIMARY KEY,
    vasarlo_azon INT NOT NULL,
    datum DATE NOT NULL,
    osszesen INT NOT NULL,
    status ENUM('pending', 'fulfilled', 'canceled') NOT NULL DEFAULT 'pending', -- 'canceled' hozzáadva az ENUM-hoz
    FOREIGN KEY (vasarlo_azon) REFERENCES vasarlok(azon)
);

-- Tételek tábla (méret oszloppal kiegészítve)
CREATE TABLE tetelek (
    azon INT AUTO_INCREMENT PRIMARY KEY,
    rendeles_azon INT NOT NULL,
    termek_azon INT NOT NULL,
    mennyiseg INT NOT NULL,
    meret VARCHAR(10) NOT NULL DEFAULT '',
    FOREIGN KEY (rendeles_azon) REFERENCES rendelesek(azon),
    FOREIGN KEY (termek_azon) REFERENCES termekek(azon)
);

-- Felhasználók tábla
CREATE TABLE felhasznalok (
    azon INT AUTO_INCREMENT PRIMARY KEY,
    felhasznalonev VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    jelszo VARCHAR(255) NOT NULL,
    jogosultsag ENUM('user', 'admin', 'superadmin') NOT NULL DEFAULT 'user'
);

-- Triggerek a termekek.keszlet dinamikus frissítéséhez a termek_meretek alapján
DELIMITER //

-- INSERT trigger: Új méret hozzáadásakor frissíti a készletet
CREATE TRIGGER update_product_stock_after_insert
AFTER INSERT ON termek_meretek
FOR EACH ROW
BEGIN
    UPDATE termekek
    SET keszlet = (
        SELECT SUM(keszlet)
        FROM termek_meretek
        WHERE termek_azon = NEW.termek_azon
    )
    WHERE azon = NEW.termek_azon;
END;//

-- UPDATE trigger: Méret készlet módosításakor frissíti a készletet
CREATE TRIGGER update_product_stock_after_update
AFTER UPDATE ON termek_meretek
FOR EACH ROW
BEGIN
    UPDATE termekek
    SET keszlet = (
        SELECT SUM(keszlet)
        FROM termek_meretek
        WHERE termek_azon = NEW.termek_azon
    )
    WHERE azon = NEW.termek_azon;
END;//

-- DELETE trigger: Méret törlésekor frissíti a készletet
CREATE TRIGGER update_product_stock_after_delete
AFTER DELETE ON termek_meretek
FOR EACH ROW
BEGIN
    UPDATE termekek
    SET keszlet = (
        SELECT IFNULL(SUM(keszlet), 0) -- Ha nincs méret, 0-ra állítja
        FROM termek_meretek
        WHERE termek_azon = OLD.termek_azon
    )
    WHERE azon = OLD.termek_azon;
END;//

DELIMITER ;

-- Kezdeti készletfrissítés a termekek táblában (INSERT után futtatva)
UPDATE termekek
SET keszlet = (
    SELECT SUM(keszlet)
    FROM termek_meretek
    WHERE termek_meretek.termek_azon = termekek.azon
);
