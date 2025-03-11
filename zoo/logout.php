<?php
session_start();
require_once 'logger.php';
// Falls der Benutzer eingeloggt war, können wir den Namen loggen.
if (isset($_SESSION['username'])) {
    writeLog("User '" . $_SESSION['username'] . "' hat sich ausgeloggt.");
}

session_destroy();
header('Location: index.php');
exit;
?>
