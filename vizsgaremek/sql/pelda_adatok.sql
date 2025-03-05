INSERT INTO termekek (nev, leiras, kategoria, ar, keszlet) 
VALUES 
('Fekete, logós póló', 'Egy egyszerű, fekete színű póló, pamut anyagból.', 'póló', 5000, 100),
('Fekete, logós pulóver', 'Klasszikus fekete pulóver, kényelmes viselet.', 'pulóver', 12000, 50),
('Crimson, logós pulóver', 'Meleg és puha crimson pulóver, tökéletes hűvös időre.', 'pulóver', 12000, 50),
('Piros, logós póló', 'Sportos és kényelmes piros póló, mindennapi viseletre.', 'póló', 5000, 100),
('Zöld, logós pulóver', 'Letisztult zöld pulóver, amely kényelmes és divatos viselet.', 'pulóver', 12000, 50),
('Szürke, logós top', 'Stílusos szürke top, könnyű és légáteresztő anyagból.', 'top', 7000, 80);
('Kék, logós pulóver', 'Stílusos kék pulóver, puha és kényelmes anyagból, ideális választás a hétköznapokra.', 'pulóver', 12000, 50),
('Bordó, kapucnis pulóver', 'Meleg és praktikus bordó kapucnis pulóver, tökéletes hideg napokra.', 'pulóver', 13000, 40),
('Fekete, kapucnis pulóver', 'Kényelmes fekete kapucnis pulóver, amely tökéletes választás mindennapi viseletre.', 'pulóver', 13000, 60),
('Kék, kapucnis pulóver', 'Friss kék kapucnis pulóver, puha anyaggal, minden alkalomra kényelmes viselet.', 'pulóver', 13000, 70),
('Rózsaszín, logós póló', 'Lágy rózsaszín póló, egyszerű dizájnnal és kényelmes pamut anyagból.', 'póló', 5500, 90),
('Barna, logós póló', 'Stílusos barna póló, amely minden nap kényelmes és praktikus viseletet biztosít.', 'póló', 5000, 100);



INSERT INTO vasarlok (nev, email, telefon, cim) 
VALUES 
('Kovács István', 'kovacs.istvan@example.com', '+36 20 123 4567', '1125 Budapest, Kossuth Lajos utca 10.');

INSERT INTO rendelesek (vasarlo_azon, datum, osszesen)
VALUES
(1, '2024-12-03', 22000);

INSERT INTO tetelek (rendeles_azon, termek_azon, mennyiseg)
VALUES
(1, 1, 2),  -- 1. rendeléshez: 2 db Fekete logos polo
(1, 2, 1);  -- 1. rendeléshez: 1 db Fekete logos pulover


INSERT INTO termek_kepek (termek_azon, kep_url) 
VALUES 
(1, 'black_logo_tshirt_front.png'),  -- Fekete logos póló (elöl)
(1, 'black_logo_tshirt_back.png'),   -- Fekete logos póló (hátul)
(2, 'black_logo_hoodie_front.png'),  -- Fekete logos pulóver (elöl)
(2, 'black_logo_hoodie_back.png'),   -- Fekete logos pulóver (hátul)
(3, 'crimsonfront.png'),
(3, 'crimsonback.png'),
(4, 'redfront.png'),
(4, 'redback.png'),
(5, 'zoldfront.png'),
(5, 'zoldback.png'),
(6, 'granitfront.png'),
(6, 'granitback.png'),
(7, 'bluefront.png'),
(7, 'blueback.png'),
(8, 'redhfront.png'),
(8, 'redhback.png'),
(9, 'blackhfront.png'),
(9, 'blackback.png'),
(10, 'navyfront.png'),
(10, 'navyback.png'),
(11, 'noifront.png'),
(11, 'noiback.png'),
(12, 'brownfront.png'),
(12, 'brownback.png');


