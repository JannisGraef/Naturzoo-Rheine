<?php
session_start();
require_once 'db_config.php';

// Prüfen, ob eine Gebäude-ID übergeben wurde
if (!isset($_GET['gid'])) {
  die("Kein Gehege ausgewählt.");
}
$gebaeude_id = intval($_GET['gid']);

// Gebäudeinfos laden
$sql_geb = "SELECT * FROM gebäude WHERE Gebäude_ID = $gebaeude_id";
$res_geb = $conn->query($sql_geb);
if (!$res_geb || $res_geb->num_rows === 0) {
  die("Gehege nicht gefunden.");
}
$gebaeude = $res_geb->fetch_assoc();

// Alle Tiere in diesem Gebäude laden
$sql_tiere = "SELECT * FROM tier WHERE Gebäude_ID = $gebaeude_id";
$res_tiere = $conn->query($sql_tiere);

?>
<!DOCTYPE html>
<html lang="de">

<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($gebaeude['Name']); ?> – Naturzoo Rheine</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>

  <header class="main-header">
    <div class="header-content">
      <nav class="main-nav">
        <ul>
          <li><a href="index.php">Startseite</a></li>
          <li><a href="gehege.php">Gehege</a></li>
          <li><a href="index.php#kontakt">Kontakt</a></li>
          <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
            <li><a href="admin/index.php">Admin Panel</a></li>
          <?php endif; ?>
          <?php if (isset($_SESSION['is_pfleger']) && $_SESSION['is_pfleger'] === true): ?>
            <li><a href="admin/index.php">Pfleger Panel</a></li>
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

  <main>
    <section class="enclosure-details">
      <h2>Im Gehege &bdquo;<?php echo htmlspecialchars($gebaeude['Name']); ?>&ldquo; leben folgende Tiere:</h2>

      <?php if ($res_tiere && $res_tiere->num_rows > 0): ?>
        <!-- Wrapper für die "Tier-Karten" -->
        <div class="animal-card-wrapper" style="display:flex; flex-wrap:wrap; gap:20px;">

          <?php while ($tier = $res_tiere->fetch_assoc()): ?>
            <?php
            $tier_id    = $tier['Tier_ID'];
            $tier_name  = $tier['Name'];
            $tier_art   = $tier['Art'];
            // Falls Du ein Feld 'Alter' in der Tabelle 'tier' hast
            $tier_alter = isset($tier['Alter']) ? $tier['Alter'] : '';

            // Tier-Bild prüfen
            if (!empty($tier['bild'])) {
              $imgSrc = "data:image/jpeg;base64," . base64_encode($tier['bild']);
            } else {
              $imgSrc = "images/animals/default.jpg";
            }
            ?>

            <div class="animal-card"
              style="width:220px; border:1px solid #ddd; border-radius:8px; overflow:hidden;">
              <img src="<?php echo $imgSrc; ?>"
                alt="<?php echo htmlspecialchars($tier_art); ?>"
                style="width:100%; height:150px; object-fit:cover;">

              <div style="padding:10px;">
                <h3><?php echo htmlspecialchars($tier_name); ?> (<?php echo htmlspecialchars($tier_art); ?>)</h3>
                <p>Alter: <?php echo htmlspecialchars($tier_alter); ?> Jahre</p>

                <?php
                // Fütterungsinfos laden
                // ----------------------------------------------------
                // Hier JOIN auf futter:
                //   fuetterung.futter_id -> futter.Futter_ID
                // ----------------------------------------------------
                // Fütterungsinfos laden
                $sql_futt = "
                  SELECT fuetterung.zeit,
                  futter.Name AS futterName
                  FROM fuetterung
                  JOIN futter
                  ON fuetterung.futter = futter.Futter_ID
                  WHERE fuetterung.tier_id = $tier_id
                ";
                $res_futt = $conn->query($sql_futt);

                if ($res_futt && $res_futt->num_rows > 0) {
                  echo "<ul>";
                  while ($fdata = $res_futt->fetch_assoc()) {
                    echo "<li>Fütterung um "
                      . htmlspecialchars($fdata['zeit'])
                      . " mit "
                      . htmlspecialchars($fdata['futterName']) // <-- Hier der Klartextname!
                      . "</li>";
                  }
                  echo "</ul>";
                } else {
                  echo "<p>Keine Fütterungsinfos.</p>";
                }

                ?>
              </div>
            </div>
          <?php endwhile; ?>

        </div>

      <?php else: ?>
        <p>Keine Tiere in diesem Gehege.</p>
      <?php endif; ?>

    </section>
  </main>

</body>

</html>