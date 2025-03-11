<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Dashboard – Naturzoo Rheine</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header class="main-header">
    <div class="header-content">
      <h1>Mitarbeiter-Dashboard</h1>
      <nav class="main-nav">
        <ul>
          <li><a href="index.php">Startseite</a></li>
          <li><a href="gehege.php">Gehege</a></li>
          <li><a href="logout.php">Abmelden</a></li>
        </ul>
      </nav>
    </div>
    <div class="login-container">
      <span class="user-info">
        Angemeldet als <?php echo htmlspecialchars($_SESSION['username']); ?>        
        <a href="logout.php" class="btn">Logout</a>
      </span>
    </div>
  </header>

  

  <footer>
    <p>&copy; 2025 Naturzoo Rheine</p>
  </footer>
</body>
</html>
