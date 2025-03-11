<?php
session_start(); // Falls nicht schon gestartet
require_once 'db_config.php';
require_once 'logger.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username         = trim($_POST['username']);
    $password         = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($password !== $confirm_password) {
        die("Passwörter stimmen nicht überein.");
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Standardmäßig is_admin = 0
    $stmt = $conn->prepare("INSERT INTO users (username, password, is_admin) VALUES (?, ?, 0)");
    $stmt->bind_param("ss", $username, $hashedPassword);

    if ($stmt->execute()) {
        // Erfolgreiche Registrierung -> ins Log schreiben
        writeLog("Neuer User registriert: '$username'");
        $success = "Registrierung erfolgreich. </a>";
    } else {
        // Fehler -> ebenfalls ins Log schreiben
        writeLog("Fehler bei der Registrierung für Benutzer '$username'");
        $error = "Fehler bei der Registrierung.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Registrierung – Naturzoo Rheine</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header class="main-header">
    <div class="header-content">
      <h1>Registrierung</h1>
      <nav class="main-nav">
        <ul>
          <li><a href="index.php">Startseite</a></li>
          <li><a href="gehege.php">Gehege</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <main>
    <section class="register-form" style="max-width:400px; margin:30px auto; background:#fff; padding:20px; border:1px solid #ddd; border-radius:8px;">
      <h2>Jetzt registrieren</h2>
      <?php 
      if (isset($success)) {
          echo "<p style='color:green;'>$success</p>";
      } elseif (isset($error)) {
          echo "<p style='color:red;'>$error</p>";
      }
      ?>
      <form action="register.php" method="POST">
        <div class="input-group">
          <label for="username">Benutzername</label>
          <input type="text" id="username" name="username" required>
        </div>
        <div class="input-group">
          <label for="password">Passwort</label>
          <input type="password" id="password" name="password" required>
        </div>
        <div class="input-group">
          <label for="confirm_password">Passwort bestätigen</label>
          <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        <button type="submit" class="btn">Registrieren</button>
      </form>
      <p>Schon ein Konto? <a href="login.php">Hier anmelden</a></p>
    </section>
  </main>

</body>
</html>
