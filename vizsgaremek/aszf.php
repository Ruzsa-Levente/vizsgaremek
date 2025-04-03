<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Ászf</title>
</head>
<body class="hexa-bg">

<?php
require("php/connect.php");
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
?>

<!-- Bootstrap Navbar -->
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container-fluid nav-container d-flex flex-column align-items-center">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="search-container d-flex align-items-center">
            <i class="fas fa-search" id="search-toggle"></i>
            <input type="text" id="search-input" placeholder="Search..." value="<?= htmlspecialchars($searchTerm) ?>">
        </div>
        <a class="navbar-brand logo mb-2" href="index.php">
            <img src="kepek/heet-logo-white.png" alt="Webshop Logo">
        </a>
        <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
            <button class="navbar-close-btn" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-label="Close navigation">
                <i class="fas fa-times"></i>
            </button>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="clothes.php">Clothes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="sale.php">Sale</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="signup.php">Sign Up</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="about.php">About Us</a>
                </li>
            </ul>
        </div>
        <div class="icon-container position-absolute top-50  translate-middle-y d-flex align-items-center">
            <div class="cart-icon d-flex align-items-center">
                <a href="billing.php">
                    <i class="fas fa-shopping-cart"></i>
                    <span id="cart-count">0</span>
                </a>
            </div>
            <div class="user-icon d-flex align-items-center">
                <a href="login.php">
                    <i class="fas fa-user"></i>
                </a>
            </div>
        </div>
    </div>
</nav>

<main style="padding-top: 250px;padding-bottom: 150px" class="main-content">
    <div class="container">
        <section id="aszf" class="mx-auto">
        ÁLTALÁNOS SZERZŐDÉSI FELTÉTELEK (ÁSZF)
        <br>
Heet Clothing Webáruház
<br>
Hatálybalépés dátuma: 2025. március 18.
<br>
1. Bevezetés
Jelen Általános Szerződési Feltételek (továbbiakban: ÁSZF) a Heet Clothing webáruház (továbbiakban: Szolgáltató) által üzemeltetett online felületen (https://heetclothing.hu) keresztül nyújtott szolgáltatásokra és az ott kötött szerződésekre vonatkoznak. Az ÁSZF a Szolgáltató és a webáruházat használó természetes vagy jogi személyek (továbbiakban: Felhasználó vagy Vásárló) közötti jogviszonyt szabályozza.
<br>
2. Szolgáltató adatai
Cégnév: Heet Clothing Kft.
Székhely: 1111 Budapest, Minta utca 1., Magyarország
Adószám: HU12345678
Cégjegyzékszám: 01-09-123456
Kapcsolat: heet.clothing.hu@gmail.com
Telefonszám: +36 20 513 3309
Ügyfélszolgálat elérhetősége: Hétfőtől péntekig 9:00–17:00
A Szolgáltató a webáruház üzemeltetője, amely divatruházati termékek értékesítésével foglalkozik.
<br>
3. Az ÁSZF hatálya
Jelen ÁSZF minden olyan jogviszonyra kiterjed, amely a webáruházon keresztül létrejön a Szolgáltató és a Felhasználó között, ideértve a termékek megrendelését, vásárlását, szállítását és az ezzel kapcsolatos ügyintézést. Az ÁSZF elfogadása a webáruház használatának feltétele.
<br>
4. Fogalmak
Felhasználó: Bármely természetes vagy jogi személy, aki a webáruház szolgáltatásait igénybe veszi.
Fogyasztó: Olyan természetes személy, aki a szakmája, foglalkozása vagy üzleti tevékenysége körén kívül jár el.
Megrendelés: A Felhasználó által a webáruházon keresztül leadott vásárlási szándék.
Szerződés: A Szolgáltató és a Felhasználó között a megrendelés visszaigazolásával létrejövő jogviszony.
<br>
5. A szerződés létrejötte
5.1. A Felhasználó a webáruházban kiválasztott termékeket a kosárba helyezi, majd a megrendelési folyamat során megadja a szükséges adatokat (név, cím, fizetési mód stb.).

5.2. A megrendelés leadása a „Megrendelés elküldése” gombra kattintással történik, amely ajánlattételnek minősül.

5.3. A Szolgáltató a megrendelést e-mailben visszaigazolja, ezzel a szerződés létrejön. A szerződés nyelve magyar, és nem kerül iktatásra, kizárólag elektronikus formában érhető el a visszaigazoló e-mailben.
<br>
6. Árak és fizetési feltételek
6.1. A webáruházban feltüntetett árak magyar forintban (HUF) értendők, és tartalmazzák az ÁFÁ-t (27%).

6.2. A Szolgáltató fenntartja az árváltoztatás jogát, de a már leadott megrendelésekre a leadáskori árak érvényesek.

6.3. Fizetési módok:

Bankkártyás fizetés (SimplePay/OTP rendszeren keresztül)
6.4. A fizetési határidő előreutalás esetén a megrendelés visszaigazolásától számított 5 munkanap.
<br>
7. Szállítási feltételek
7.1. A Szolgáltató a termékeket Magyarország területén belül kézbesíti.

7.2. Szállítási módok és díjak:

Házhoz szállítás futárszolgálattal: 1.990 HUF (15.000 HUF feletti rendelés esetén ingyenes)
Csomagpont átvétel: 1.490 HUF
7.3. Szállítási idő: a megrendelés visszaigazolásától számított 2–5 munkanap.
7.4. A Szolgáltató nem vállal felelősséget a kézbesítés késedelméért, ha az a futárszolgálat hibájából következik be.
<br>
8. Elállási jog (Fogyasztók számára)
8.1. A Fogyasztót a 45/2014. (II. 26.) Korm. rendelet értelmében 14 naptári napos elállási jog illeti meg, amely a termék átvételétől kezdődik.

8.2. Az elállási szándékot írásban (e-mail: info@heetclothing.hu) kell jelezni, megadva a megrendelés számát.

8.3. A Fogyasztó köteles a terméket sértetlen állapotban, saját költségén visszaküldeni a Szolgáltató székhelyére (1111 Budapest, Minta utca 1.) az elállási nyilatkozat elküldésétől számított 14 napon belül.

8.4. A Szolgáltató a vételárat és a szállítási költséget (kivéve a visszaküldés költségét) 14 napon belül visszatéríti a Fogyasztó által megadott bankszámlára.
<br>
9. Garancia és jótállás
9.1. A Szolgáltató a tartós fogyasztási cikkekre (pl. cipőkre) a magyar jogszabályok szerinti 1 év jótállást biztosít.

9.2. A jótállási igényt a vásárlást igazoló számlával lehet érvényesíteni.

9.3. Hibás teljesítés esetén a Fogyasztó kérhet kijavítást, cserét, árleszállítást vagy a vételár visszatérítését a Ptk. alapján.
<br>
10. Adatvédelem
10.1. A Szolgáltató a Felhasználók adatait az Adatvédelmi Tájékoztatóban foglaltak szerint, a GDPR és az Info tv. előírásainak megfelelően kezeli.

10.2. Az adatkezelés célja a megrendelések teljesítése és a kapcsolattartás.

10.3. A részletes Adatvédelmi Tájékoztató a webáruházban (https://heetclothing.hu/adatvedelem) érhető el.
<br>
11. Panaszkezelés
11.1. Panaszokat a Szolgáltató az info@heetclothing.hu címen vagy postai úton (1111 Budapest, Minta utca 1.) fogad.

11.2. A Szolgáltató a panaszokat 30 napon belül kivizsgálja és írásban válaszol.

11.3. Ha a Fogyasztó elégedetlen a válasszal, a területileg illetékes járási hivatalhoz vagy a Budapesti Békéltető Testülethez fordulhat (cím: 1016 Budapest, Krisztina krt. 99.).
<br>
12. Záró rendelkezések
12.1. A Szolgáltató fenntartja a jogot az ÁSZF egyoldalú módosítására, amelyet a webáruházban közzétesz. A módosítások a közzétételt követő megrendelésekre érvényesek.

12.2. A jelen ÁSZF-re a magyar jog az irányadó. Vitás kérdésekben a magyar bíróságok illetékesek.

12.3. Jelen ÁSZF folyamatosan elérhető a <a href="https://heetclothing.hu/aszf.php">https://heetclothing.hu/aszf.php</a> címen. 
        </section>
    </div>
</main>

<!-- Bootstrap Footer -->
<footer class="footer text-white text-center py-4 mt-auto">
    <div class="container">
        <div class="footer-content">
            <p class="mb-2">© 2025 Heet Clothing | All rights reserved.</p>
            <a href="aszf.php" class="text-white text-decoration-none mb-3 d-inline-block">ÁSZF</a>
            <br>
            <div class="social-icons mt-3 d-flex justify-content-center align-items-center">
            <span class="text-white me-3">Stay Connected:</span>
                <a href="https://www.facebook.com/profile.php?id=61574451329401#" target="_blank" class="text-white mx-3">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="https://www.instagram.com/heetclothinghu/" target="_blank" class="text-white mx-3">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="https://www.tiktok.com/@heet.clothing" target="_blank" class="text-white mx-3">
                    <i class="fab fa-tiktok"></i>
                </a>
            </div>
        </div>
    </div>
</footer>

<script src="java_script/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
