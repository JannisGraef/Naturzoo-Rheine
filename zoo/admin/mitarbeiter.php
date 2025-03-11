<?php
require_once '../db_config.php';
include 'admin_header.php';

// Löschen eines Pfleger-Datensatzes
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM pfleger WHERE Pfleger_ID = $delete_id");
    header("Location: mitarbeiter.php");
    exit;
}

// Bearbeiten: Falls edit_id gesetzt ist, lade die Daten des Pflegers
$pfleger_id    = 0;
$name          = "";
$adresse       = "";
$telefonnummer = "";
$revier_id     = 0;
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    $sql = "SELECT * FROM pfleger WHERE Pfleger_ID = $edit_id";
    $result = $conn->query($sql);
    if ($result && $result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $pfleger_id    = $row['Pfleger_ID'];
        $name          = $row['Name'];
        $adresse       = $row['Adresse'];
        $telefonnummer = $row['Telefonnummer'];
        $revier_id     = $row['Revier_ID'];
    }
}

// Hinzufügen oder Bearbeiten
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pfleger_id     = intval($_POST['pfleger_id']);
    $name           = $conn->real_escape_string(trim($_POST['name']));
    $adresse        = $conn->real_escape_string(trim($_POST['adresse']));
    $telefonnummer  = $conn->real_escape_string(trim($_POST['telefonnummer']));
    $revier_id      = intval($_POST['revier_id']);

    if ($pfleger_id === 0) {
        // Neuer Datensatz
        $sql = "INSERT INTO pfleger (Name, Adresse, Telefonnummer, Revier_ID)
                VALUES ('$name', '$adresse', '$telefonnummer', $revier_id)";
        $conn->query($sql);
    } else {
        // Bestehenden Datensatz bearbeiten
        $sql = "UPDATE pfleger 
                SET Name='$name', Adresse='$adresse', Telefonnummer='$telefonnummer', Revier_ID=$revier_id
                WHERE Pfleger_ID = $pfleger_id";
        $conn->query($sql);
    }
    header("Location: mitarbeiter.php");
    exit;
}
?>

<h2>Mitarbeiter Übersicht</h2>
<table class="admin-table">
  <thead>
    <tr>
      <th>Pfleger_ID</th>
      <th>Name</th>
      <th>Adresse</th>
      <th>Telefonnummer</th>
      <th>Revier</th>
      <th>Aktion</th>
    </tr>
  </thead>
  <tbody>
    <?php
    // JOIN mit Tabelle revier, um den Reviernamen anzuzeigen
    $sql = "SELECT p.Pfleger_ID, p.Name, p.Adresse, p.Telefonnummer, r.Name AS RevierName
            FROM pfleger p
            LEFT JOIN revier r ON p.Revier_ID = r.Revier_ID";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        ?>
        <tr>
          <td><?php echo $row['Pfleger_ID']; ?></td>
          <td><?php echo htmlspecialchars($row['Name'] ?? ''); ?></td>
          <td><?php echo htmlspecialchars($row['Adresse'] ?? ''); ?></td>
          <td><?php echo htmlspecialchars($row['Telefonnummer'] ?? ''); ?></td>
          <td><?php echo htmlspecialchars($row['RevierName'] ?? ''); ?></td>
          <td>
            <a href="?delete_id=<?php echo $row['Pfleger_ID']; ?>" style="color:red;" onclick="return confirm('Wirklich löschen?');">Löschen</a>
            &nbsp;|&nbsp;
            <a href="?edit_id=<?php echo $row['Pfleger_ID']; ?>">Ändern</a>
          </td>
        </tr>
        <?php
      }
    } else {
      echo "<tr><td colspan='6'>Keine Einträge gefunden.</td></tr>";
    }
    ?>
  </tbody>
</table>
<p>&nbsp;</p>
<h3>Neuen Datensatz hinzufügen / bearbeiten</h3>
<p>Zum Bearbeiten klicke in der Tabelle auf "Ändern". Für einen neuen Eintrag gib als Pfleger_ID 0 ein.</p>
<form action="mitarbeiter.php" method="POST">
  <label for="pfleger_id">Pfleger_ID:</label>
  <input type="number" name="pfleger_id" id="pfleger_id" value="<?php echo $pfleger_id; ?>" required>

  <label for="name">Name:</label>
  <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($name); ?>" required>

  <label for="adresse">Adresse:</label>
  <input type="text" name="adresse" id="adresse" value="<?php echo htmlspecialchars($adresse); ?>" required>

  <label for="telefonnummer">Telefonnummer:</label>
  <input type="text" name="telefonnummer" id="telefonnummer" value="<?php echo htmlspecialchars($telefonnummer); ?>" required>

  <label for="revier_id">Revier:</label>
  <select name="revier_id" id="revier_id" required>
    <option value="">Bitte wählen</option>
    <?php
    $sqlRevier = "SELECT Revier_ID, Name FROM revier";
    $resultRevier = $conn->query($sqlRevier);
    while ($r = $resultRevier->fetch_assoc()) {
      $selected = ($revier_id == $r['Revier_ID']) ? "selected" : "";
      echo "<option value='{$r['Revier_ID']}' $selected>" . htmlspecialchars($r['Name']) . "</option>";
    }
    ?>
  </select>

  <button type="submit" class="btn">Speichern</button>
</form>

