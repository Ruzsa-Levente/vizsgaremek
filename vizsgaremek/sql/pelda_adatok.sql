-- Termékek feltöltése (kezdeti készlet 0, mert a termek_meretek tábla adja meg)
-- Termékek feltöltése (kezdeti készlet 0, mert a termek_meretek tábla adja meg)
INSERT INTO termekek (nev, leiras, kategoria, ar, keszlet, discounted_price) 
VALUES 
('Fekete, logós póló', 'Egy egyszerű, fekete színű póló, pamut anyagból.', 'póló', 5000, 0, NULL),
('Fekete, logós pulóver', 'Klasszikus fekete pulóver, kényelmes viselet.', 'pulóver', 12000, 0, NULL),
('Crimson, logós pulóver', 'Meleg és puha crimson pulóver, tökéletes hűvös időre.', 'pulóver', 12000, 0, 10999),
('Piros, logós póló', 'Sportos és kényelmes piros póló, mindennapi viseletre.', 'póló', 5000, 0, NULL),
('Zöld, logós pulóver', 'Letisztult zöld pulóver, amely kényelmes és divatos viselet.', 'pulóver', 12000, 0, NULL),
('Szürke, logós top', 'Stílusos szürke top, könnyű és légáteresztő anyagból.', 'top', 7000, 0, NULL),
('Kék, logós pulóver', 'Stílusos kék pulóver, puha és kényelmes anyagból, ideális választás a hétköznapokra.', 'pulóver', 12000, 0, NULL),
('Bordó, kapucnis pulóver', 'Meleg és praktikus bordó kapucnis pulóver, tökéletes hideg napokra.', 'pulóver', 13000, 0, 10999),
('Fekete, kapucnis pulóver', 'Kényelmes fekete kapucnis pulóver, amely tökéletes választás mindennapi viseletre.', 'pulóver', 13000, 0, NULL),
('Kék, kapucnis pulóver', 'Friss kék kapucnis pulóver, puha anyaggal, minden alkalomra kényelmes viselet.', 'pulóver', 13000, 0, 10999),
('Rózsaszín, logós póló', 'Lágy rózsaszín póló, egyszerű dizájnnal és kényelmes pamut anyagból.', 'póló', 5500, 0, NULL),
('Barna, logós póló', 'Stílusos barna póló, amely minden nap kényelmes és praktikus viseletet biztosít.', 'póló', 5000, 0, NULL);

-- Termék méretek feltöltése (minden termékhez S, M, L méretek példaként)
INSERT INTO termek_meretek (termek_azon, meret, keszlet)
VALUES
(1, 'S', 30), (1, 'M', 50), (1, 'L', 20),  -- Fekete, logós póló
(2, 'S', 10), (2, 'M', 20), (2, 'L', 20),  -- Fekete, logós pulóver
(3, 'S', 15), (3, 'M', 25), (3, 'L', 10),  -- Crimson, logós pulóver
(4, 'S', 40), (4, 'M', 40), (4, 'L', 20),  -- Piros, logós póló
(5, 'S', 15), (5, 'M', 20), (5, 'L', 15),  -- Zöld, logós pulóver
(6, 'S', 25), (6, 'M', 30), (6, 'L', 25),  -- Szürke, logós top
(7, 'S', 10), (7, 'M', 20), (7, 'L', 20),  -- Kék, logós pulóver
(8, 'S', 10), (8, 'M', 15), (8, 'L', 15),  -- Bordó, kapucnis pulóver
(9, 'S', 20), (9, 'M', 20), (9, 'L', 20),  -- Fekete, kapucnis pulóver
(10, 'S', 20), (10, 'M', 30), (10, 'L', 20), -- Kék, kapucnis pulóver
(11, 'S', 30), (11, 'M', 30), (11, 'L', 30), -- Rózsaszín, logós póló
(12, 'S', 30), (12, 'M', 40), (12, 'L', 30); -- Barna, logós póló

-- Termék képek feltöltése
INSERT INTO termek_kepek (termek_azon, kep_url) 
VALUES 
(1, 'black_logo_tshirt_front.png'), (1, 'black_logo_tshirt_back.png'),
(2, 'black_logo_hoodie_front.png'), (2, 'black_logo_hoodie_back.png'),
(3, 'crimsonfront.png'), (3, 'crimsonback.png'),
(4, 'redfront.png'), (4, 'redback.png'),
(5, 'zoldfront.png'), (5, 'zoldback.png'),
(6, 'granitfront.png'), (6, 'granitback.png'),
(7, 'bluefront.png'), (7, 'blueback.png'),
(8, 'redhfront.png'), (8, 'redhback.png'),
(9, 'blackhfront.png'), (9, 'blackhback.png'),
(10, 'navyfront.png'), (10, 'navyback.png'),
(11, 'noifront.png'), (11, 'noiback.png'),
(12, 'brownfront.png'), (12, 'brownback.png');

-- Vásárlók feltöltése (példa)
INSERT INTO vasarlok (nev, email, telefon, cim) 
VALUES 
('Kovács István', 'kovacs.istvan@example.com', '+36 20 123 4567', '1125 Budapest, Kossuth Lajos utca 10.');

-- Rendelések feltöltése (példa)
INSERT INTO rendelesek (vasarlo_azon, datum, osszesen, status)
VALUES
(1, '2024-12-03', 22000, 'pending');

-- Példa tételek méretekkel
INSERT INTO tetelek (rendeles_azon, termek_azon, mennyiseg, meret)
VALUES 
(1, 1, 2, 'M'),  -- 2 db Fekete, logós póló, M méret
(1, 2, 1, 'L');  -- 1 db Fekete, logós pulóver, L méret

-- Felhasználók feltöltése (példa)
INSERT INTO felhasznalok (felhasznalonev, email, jelszo, jogosultsag)
VALUES
('1', 'user1@example.com', '$2y$12$4y/sI67PvY4jXLN46fB.vOARWSgORqkcmImv.EIr/3embLcAqRjzG', 'superadmin');
