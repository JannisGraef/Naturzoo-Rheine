<?php
require_once '../db_config.php';
include 'admin_header.php';

// Löschen: Zuerst aus "menge" löschen, dann aus "futter"
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM menge WHERE Futter_ID = $delete_id");
    $conn->query("DELETE FROM futter WHERE Futter_ID = $delete_id");
    header("Location: futter.php");
    exit;
}

// Initialisierung der Formularfelder
$futter_id    = 0;
$name         = "";
$lieferant_id = "";
$menge        = "";

// Bearbeiten: Falls edit_id gesetzt ist, lade den Datensatz
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    $sql = "SELECT f.*, m.Menge, m.Lieferant_ID FROM futter f 
            LEFT JOIN menge m ON f.Futter_ID = m.Futter_ID
            WHERE f.Futter_ID = $edit_id";
    $result = $conn->query($sql);
    if ($result && $result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $futter_id    = $row['Futter_ID'];
        $name         = $row['Name'];
        $lieferant_id = $row['Lieferant_ID'];
        $menge        = $row['Menge'];
    }
}

// Hinzufügen / Bearbeiten
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $futter_id    = intval($_POST['futter_id']);
    $name         = $conn->real_escape_string(trim($_POST['name']));
    $lieferant_id = intval($_POST['lieferant_id']);
    $menge        = floatval($_POST['menge']);

    if ($futter_id === 0) {
        // Neuer Datensatz
        $sql = "INSERT INTO futter (Name) VALUES ('$name')";
        $conn->query($sql);
        $futter_id = $conn->insert_id;
        $sql2 = "INSERT INTO menge (Menge, Futter_ID, Lieferant_ID) VALUES ($menge, $futter_id, $lieferant_id)";
        $conn->query($sql2);
    } else {
        // Bestehenden Datensatz bearbeiten
        $sql = "UPDATE futter SET Name='$name' WHERE Futter_ID=$futter_id";
        $conn->query($sql);
        $sql2 = "UPDATE menge SET Menge=$menge, Lieferant_ID=$lieferant_id WHERE Futter_ID=$futter_id";
        $conn->query($sql2);
    }
    header("Location: futter.php");
    exit;
}
?>

<h2>Futter verwalten</h2>
<table class="admin-table">
  <thead>
    <tr>
      <th>Futter_ID</th>
      <th>Name</th>
      <th>Lieferant</th>
      <th>Menge</th>
      <th>Aktion</th>
    </tr>
  </thead>
  <tbody>
    <?php
    // JOIN, um den Lieferantennamen zu erhalten
    $sql = "SELECT f.Futter_ID, f.Name, l.Name as Lieferant, m.Menge
            FROM futter f 
            LEFT JOIN menge m ON f.Futter_ID = m.Futter_ID
            LEFT JOIN lieferant l ON m.Lieferant_ID = l.Lieferant_ID";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        ?>
        <tr>
          <td><?php echo $row['Futter_ID']; ?></td>
          <td><?php echo htmlspecialchars($row['Name']); ?></td>
          <td><?php echo htmlspecialchars($row['Lieferant']); ?></td>
          <td><?php echo htmlspecialchars($row['Menge']) . ' kg'; ?></td>

          <td>
            <a href="?delete_id=<?php echo $row['Futter_ID']; ?>" style="color:red;" onclick="return confirm('Wirklich löschen?');">Löschen</a>
            &nbsp;|&nbsp;
            <a href="?edit_id=<?php echo $row['Futter_ID']; ?>">Ändern</a>
          </td>
        </tr>
        <?php
      }
    } else {
      echo "<tr><td colspan='5'>Keine Futterdaten gefunden.</td></tr>";
    }
    ?>
  </tbody>
</table>
<p>&nbsp;</p>
<h3>Neuen Futter-Datensatz hinzufügen / bearbeiten</h3>
<p>Zum Bearbeiten trage die vorhandene Futter_ID ein, zum Neuanlegen „0“.</p>
<form action="futter.php" method="POST">
  <label for="futter_id">Futter_ID:</label>
  <input type="number" name="futter_id" id="futter_id" value="<?php echo $futter_id; ?>" required>

  <label for="name">Name:</label>
  <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($name); ?>" required>

  <!-- Dropdown für Lieferanten -->
  <label for="lieferant_id">Lieferant:</label>
  <select name="lieferant_id" id="lieferant_id" required>
    <option value="">Bitte wählen</option>
    <?php
    $sqlLieferant = "SELECT * FROM lieferant";
    $resultLieferant = $conn->query($sqlLieferant);
    while ($l = $resultLieferant->fetch_assoc()) {
      $selected = ($lieferant_id == $l['Lieferant_ID']) ? "selected" : "";
      echo "<option value='{$l['Lieferant_ID']}' $selected>{$l['Name']}</option>";
    }
    ?>
  </select>

  <label for="menge">Menge (in kg):</label>
  <input type="text" name="menge" id="menge" value="<?php echo htmlspecialchars($menge); ?>" required>

  <button type="submit" class="btn">Speichern</button>
</form>


