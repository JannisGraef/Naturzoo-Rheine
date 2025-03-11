<?php
$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname     = "naturzoo_rheine";

$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);
if ($conn->connect_error) {
    die("Fehler bei der DB-Verbindung: " . $conn->connect_error);
}
?>
