<?php
require_once '../db_config.php';
include 'admin_header.php';

// --- Löschen ---
// Beim Löschen eines Tieres sollen zuerst alle zugehörigen Fütterungsdatensätze gelöscht werden,
// damit keine Fremdschlüsselprobleme auftreten.
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    // Zuerst alle Fütterungsdatensätze, die diesem Tier zugeordnet sind, löschen
    $conn->query("DELETE FROM fuetterung WHERE tier_id = $delete_id");
    // Dann den Tier-Datensatz löschen
    $conn->query("DELETE FROM tier WHERE Tier_ID = $delete_id");
    header("Location: tiere.php");
    exit;
}

// --- Initialisierung der Formularfelder ---
$tier_id    = 0;
$name       = "";
$art        = "";
$alter      = "";
$gehege_id  = 0;

// --- Bearbeiten: Falls edit_id gesetzt ist, lade die Daten des Tieres ---
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    $sql = "SELECT * FROM tier WHERE Tier_ID = $edit_id";
    $result = $conn->query($sql);
    if ($result && $result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $tier_id   = $row['Tier_ID'];
        $name      = $row['Name'];
        $art       = $row['Art'];
        $alter     = $row['Alter'];
        $gehege_id = $row['Gebäude_ID'];
    }
}

// --- Hinzufügen / Bearbeiten ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tier_id   = intval($_POST['tier_id']);
    $name      = $conn->real_escape_string(trim($_POST['name']));
    $art       = $conn->real_escape_string(trim($_POST['art']));
    $alter     = $conn->real_escape_string(trim($_POST['alter']));
    $gehege_id = intval($_POST['gehege_id']);

    $imageData = "";
    if (isset($_FILES['bild']) && $_FILES['bild']['error'] == 0) {
        $imageData = file_get_contents($_FILES['bild']['tmp_name']);
        $imageData = $conn->real_escape_string($imageData);
    }

    if ($tier_id === 0) {
        // Neuer Datensatz in der Tabelle tier einfügen
        if ($imageData != "") {
            $sql = "INSERT INTO tier (Name, Art, `Alter`, Gebäude_ID, bild)
                    VALUES ('$name', '$art', '$alter', $gehege_id, '$imageData')";
        } else {
            $sql = "INSERT INTO tier (Name, Art, `Alter`, Gebäude_ID)
                    VALUES ('$name', '$art', '$alter', $gehege_id)";
        }
        if ($conn->query($sql)) {
            // Neue Tier-ID ermitteln
            $newTierId = $conn->insert_id;
            // Automatisch einen neuen Fütterungsdatensatz anlegen (Standardwerte: Zeit = "09:00", Futter_ID = 1)
            $defaultZeit = "09:00";
            $defaultFutter = 1;
            $sql2 = "INSERT INTO fuetterung (tier_id, zeit, futter) VALUES ($newTierId, '$defaultZeit', $defaultFutter)";
            $conn->query($sql2);
        }
    } else {
        // Bestehenden Datensatz aktualisieren
        $sql = "UPDATE tier SET Name='$name', Art='$art', `Alter`='$alter', Gebäude_ID=$gehege_id";
        if ($imageData != "") {
            $sql .= ", bild='$imageData'";
        }
        $sql .= " WHERE Tier_ID=$tier_id";
        $conn->query($sql);
    }
    header("Location: tiere.php");
    exit;
}
?>

<h2>Tiere verwalten</h2>
<table class="admin-table">
  <thead>
    <tr>
      <th>Tier_ID</th>
      <th>Name</th>
      <th>Art</th>
      <th>Alter</th>
      <th>Gehege</th>
      <th>Bild</th>
      <th>Aktion</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $sql = "SELECT t.*, g.Name as GehegeName FROM tier t
            LEFT JOIN gebäude g ON t.Gebäude_ID = g.Gebäude_ID";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        ?>
        <tr>
          <td><?php echo $row['Tier_ID']; ?></td>
          <td><?php echo htmlspecialchars($row['Name']); ?></td>
          <td><?php echo htmlspecialchars($row['Art']); ?></td>
          <td><?php echo htmlspecialchars($row['Alter']); ?></td>
          <td><?php echo htmlspecialchars($row['GehegeName']); ?></td>
          <td>
            <?php if (!empty($row['bild'])): ?>
              <img src="data:image/jpeg;base64,<?php echo base64_encode($row['bild']); ?>" style="width:50px;height:50px;">
            <?php else: ?>
              Kein Bild
            <?php endif; ?>
          </td>
          <td>
            <a href="?delete_id=<?php echo $row['Tier_ID']; ?>" style="color:red;" onclick="return confirm('Wirklich löschen?');">Löschen</a>
            &nbsp;|&nbsp;
            <a href="?edit_id=<?php echo $row['Tier_ID']; ?>">Ändern</a>
          </td>
        </tr>
        <?php
      }
    } else {
      echo "<tr><td colspan='7'>Keine Tiere gefunden.</td></tr>";
    }
    ?>
  </tbody>
</table>
<p>&nbsp;</p>
<h3><?php echo ($tier_id == 0 ? "Neuen Tier-Datensatz hinzufügen" : "Datensatz bearbeiten"); ?></h3>
<p>Zum Bearbeiten klicke in der Tabelle auf "Ändern". Für einen neuen Eintrag gib als Tier_ID 0 ein.</p>
<form action="tiere.php" method="POST" enctype="multipart/form-data">
  <label for="tier_id">Tier_ID:</label>
  <input type="number" name="tier_id" id="tier_id" value="<?php echo $tier_id; ?>" required>

  <label for="name">Name:</label>
  <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($name); ?>" required>

  <label for="art">Art:</label>
  <input type="text" name="art" id="art" value="<?php echo htmlspecialchars($art); ?>" required>

  <label for="alter">Alter:</label>
  <input type="number" name="alter" id="alter" value="<?php echo htmlspecialchars($alter); ?>" required>

  <label for="gehege_id">Gehege:</label>
  <select name="gehege_id" id="gehege_id" required>
    <option value="">Bitte wählen</option>
    <?php
    $sqlGehege = "SELECT * FROM gebäude";
    $resGehege = $conn->query($sqlGehege);
    while ($g = $resGehege->fetch_assoc()) {
      $selected = ($gehege_id == $g['Gebäude_ID']) ? "selected" : "";
      echo "<option value='{$g['Gebäude_ID']}' $selected>" . htmlspecialchars($g['Name']) . "</option>";
    }
    ?>
  </select>

  <label for="bild">Bild (optional):</label>
  <input type="file" name="bild" id="bild" accept="image/*">

  <button type="submit" class="btn">Speichern</button>
</form>

<?php include 'admin_footer.php'; ?>
