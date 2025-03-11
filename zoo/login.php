<?php
session_start();
require_once 'logger.php';
require_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $user = $res->fetch_assoc();
        // Prüfe Passwort
        if (password_verify($password, $user['password'])) {
            // Session-Werte setzen
            $_SESSION['user_id']    = $user['id'];
            $_SESSION['username']   = $user['username'];

            // Neu: is_admin & is_pfleger in Session sichern
            $_SESSION['is_admin']   = ($user['is_admin'] == 1);
            $_SESSION['is_pfleger'] = ($user['is_pfleger'] == 1);

            writeLog("User '$username' hat sich eingeloggt.");

            header("Location: index.php");
            exit;
        } else {
            $error = "Falsches Passwort.";
            writeLog("Fehlgeschlagener Login (falsches Passwort) für User '$username'.");
        }
    } else {
        $error = "Benutzer nicht gefunden.";
        writeLog("Fehlgeschlagener Login (unbekannter User) für '$username'.");
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Login – Naturzoo Rheine</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header class="main-header">
    <div class="header-content">
      <h1>Anmeldung</h1>
      <nav class="main-nav">
        <ul>
          <li><a href="index.php">Startseite</a></li>
          <li><a href="gehege.php">Gehege</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <main>
    <section class="login-form" style="max-width:400px; margin:30px auto; background:#fff; padding:20px; border:1px solid #ddd; border-radius:8px;">
      <h2>Login</h2>
      <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
      <form action="login.php" method="POST">
        <div class="input-group">
          <label for="username">Benutzername</label>
          <input type="text" id="username" name="username" required>
        </div>
        <div class="input-group">
          <label for="password">Passwort</label>
          <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn">Anmelden</button>
      </form>
      <p>Noch kein Konto? <a href="register.php">Jetzt registrieren</a></p>
    </section>
  </main>
</body>
</html>
