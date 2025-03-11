<?php
session_start();
require_once '../logger.php';
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
  die("Kein Zugriff! Nur für Administratoren.");
}


writeLog("User '" . $_SESSION['username'] . "' hat den Adminbereich betreten.");
?>
<!DOCTYPE html>
<html lang="de">

<head>
  <meta charset="UTF-8">
  <title>Admin Panel – Naturzoo Rheine</title>
  <link rel="stylesheet" href="../style.css">
</head>

<body>
  <header class="admin-header">
    <div class="header-content">
      <!-- Home-Button links hinzufügen -->
      <div class="admin-home">
        <a href="../index.php" class="home-btn">Home</a>
      </div>
      <h1 style="color:#fff;">Admin Panel</h1>
    </div>
    <div class="login-container">
      <span class="user-info">
        Angemeldet als <?php echo htmlspecialchars($_SESSION['username']); ?>
        <a href="../logout.php" class="btn">Logout</a>
      </span>
    </div>
  </header>
  <div class="admin-container">
    <?php include 'sidebar.php'; ?>
    <div class="admin-content">