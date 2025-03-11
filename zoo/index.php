<?php
session_start();
?>
<!DOCTYPE html>
<html lang="de">

<head>
  <meta charset="UTF-8">
  <title>Naturzoo Rheine – Startseite</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <header class="main-header">
    <div class="header-content">
      <nav class="main-nav">
        <ul>
          <li><a href="index.php">Startseite</a></li>
          <li><a href="gehege.php">Gehege</a></li>
          <li><a href="#kontakt">Kontakt</a></li>
          <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
            <li><a href="admin/index.php">Admin Panel</a></li>
          <?php endif; ?>
          <?php if (isset($_SESSION['is_pfleger']) && $_SESSION['is_pfleger'] === true): ?>
            <li><a href="pfleger_panel.php">Pfleger Panel</a></li>
          <?php endif; ?>
        </ul>
      </nav>
    </div>
    <div class="login-container">
      <?php if (!isset($_SESSION['user_id'])): ?>
        <a href="login.php" class="btn">Anmelden</a>
      <?php else: ?>
        <span class="user-info">
          Angemeldet als <?php echo htmlspecialchars($_SESSION['username']); ?>
          <a href="logout.php" class="btn">Logout</a>
        </span>
      <?php endif; ?>
    </div>
  </header>

  <!-- HERO-BEREICH -->
  <section class="hero">
    <!-- Stelle sicher, dass images/hero_zoo.jpg existiert -->
    <img src="images/hero_zoo.jpg" alt="Naturzoo Rheine">
    <div class="hero-text">
      <h1>Willkommen im Naturzoo Rheine</h1>
      <p>Erleben Sie die Natur hautnah!</p>
    </div>
  </section>

  <!-- INFOKARTEN: Drei Bilder nebeneinander -->
  <main>
    <section class="intro-cards">
      <div class="card">
        <img src="images/zoo1.jpg" alt="Einzigartige Tierwelt">
        <div class="card-text">
          <h2>Einzigartige Tierwelt</h2>
          <p>Erkunden Sie unsere faszinierende Tierwelt in naturnah gestalteten Gehegen.</p>
        </div>
      </div>
      <div class="card">
        <img src="images/zoo2.jpg" alt="Abenteuerliche Führungen">
        <div class="card-text">
          <h2>Abenteuerliche Führungen</h2>
          <p>Erleben Sie spannende Führungen und exklusive Einblicke hinter die Kulissen.</p>
        </div>
      </div>
      <div class="card">
        <img src="images/zoo3.jpg" alt="Familienfreundliche Erlebnisse">
        <div class="card-text">
          <h2>Familienfreundliche Erlebnisse</h2>
          <p>Ein unvergesslicher Tag für die ganze Familie im Naturzoo Rheine.</p>
        </div>
      </div>


    </section>
    <p>&nbsp;</p>
    <p>&nbsp;</p>


    <!-- NEUER ABSCHNITT FÜR DEN ZOO-PLAN -->
    <section class="zoo-plan-section">
      <h2>Naturzoo-Rheine Plan:</h2>
      <p>&nbsp;</p>
      <img src="images/zoo_plan.jpg" alt="Zoo-Plan" class="zoo-plan">
      <p>
        Werfen Sie einen Blick auf unseren Zoo-Plan und erhalten Sie eine Übersicht
        über alle unsere spannenden Gehege und Einrichtungen.
      </p>
    </section>
    <p>&nbsp;</p>

    <section id="kontakt" class="contact">
      <h2>Kontakt</h2>
      <p>Adresse: Tiergartenstraße 123, 48431 Rheine</p>
      <p>Telefon: 01234 / 567890</p>
      <p>E-Mail: info@naturzoo-rheine.de</p>
    </section>
  </main>

  <footer>
    <p>&copy; 2025 Naturzoo Rheine</p>
  </footer>
  <script src="script.js"></script>
</body>

</html>