<?php
session_start();
require_once 'db_config.php';
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Gehege – Naturzoo Rheine</title>
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
        <span class="user-info">Angemeldet als <?php echo htmlspecialchars($_SESSION['username']); ?> 
          <a href="logout.php" class="btn">Logout</a>
        </span>
      <?php endif; ?>
    </div>
  </header>

  <main>
    <section class="search-section">
      
      <div class="search-bar">
      <h2>Unsere Gehege</h2>
        <form method="GET" action="gehege.php">
          <input type="text" name="search" placeholder="Gehege suchen..." 
                 value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
          <button type="submit" class="btn">Suchen</button>
        </form>
      </div>

      <div class="enclosure-list">
        <?php
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $searchTerm = $conn->real_escape_string($_GET['search']);
            $sql = "SELECT * FROM gebäude WHERE Name LIKE '%$searchTerm%'";
        } else {
            $sql = "SELECT * FROM gebäude";
        }
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $gebaeude_id = $row['Gebäude_ID'];
                $gebaeude_name = $row['Name'];
                $imgSrc = (!empty($row['bild'])) 
                          ? "data:image/jpeg;base64," . base64_encode($row['bild']) 
                          : "images/enclosure_default.jpg";
                ?>
                <div class="enclosure-item">
                  <img src="<?php echo $imgSrc; ?>" 
                       alt="<?php echo htmlspecialchars($gebaeude_name); ?>" 
                       class="enclosure-image">
                  <h3><?php echo htmlspecialchars($gebaeude_name); ?></h3>
                  <p>Erfahren Sie mehr über das Gehege &bdquo;<?php echo htmlspecialchars($gebaeude_name); ?>&ldquo;.</p>
                  <a href="enclosure_details.php?gid=<?php echo $gebaeude_id; ?>" class="btn">Mehr erfahren</a>
                </div>
                <?php
            }
        } else {
            echo "<p>Keine Gehege gefunden.</p>";
        }
        ?>
      </div>
    </section>

   
  </main>
</body>
</html>
