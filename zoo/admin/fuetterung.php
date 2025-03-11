<?php
require_once '../db_config.php';
require_once '../logger.php';
include 'admin_header.php';

// --- Löschen ---
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM fuetterung WHERE fuetterung_id = $delete_id");
    header("Location: fuetterung.php");
    exit;
}

// --- Initialisierung der Formularvariablen für Bearbeitung ---
$fuetterung_id = 0;
$tier_id       = 0;
$zeit          = "";
$futter_id     = 0;

// --- Bearbeiten: Falls edit_id gesetzt ist, lade den entsprechenden Datensatz ---
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    $sqlEdit = "SELECT * FROM fuetterung WHERE fuetterung_id = $edit_id";
    $resultEdit = $conn->query($sqlEdit);
    if ($resultEdit && $resultEdit->num_rows == 1) {
        $rowEdit = $resultEdit->fetch_assoc();
        $fuetterung_id = $rowEdit['fuetterung_id'];
        $tier_id       = $rowEdit['tier_id'];
        $zeit          = $rowEdit['zeit'];
        $futter_id     = $rowEdit['futter'];
    }
}

// --- Hinzufügen / Aktualisieren ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fuetterung_id = intval($_POST['fuetterung_id']);
    $tier_id       = intval($_POST['tier_id']);
    $zeit          = $conn->real_escape_string(trim($_POST['zeit']));
    $futter_id     = intval($_POST['futter_id']);

    if ($fuetterung_id === 0) {
        // Neuer Datensatz
        $sql = "INSERT INTO fuetterung (tier_id, zeit, futter)
                VALUES ($tier_id, '$zeit', $futter_id)";
        $conn->query($sql);
    } else {
        // Bestehenden Datensatz bearbeiten
        $sql = "UPDATE fuetterung
                SET tier_id = $tier_id, zeit = '$zeit', futter = $futter_id
                WHERE fuetterung_id = $fuetterung_id";
        $conn->query($sql);
    }
    header("Location: fuetterung.php");
    exit;
}
?>

<h2>Fütterungsdaten</h2>
<table class="admin-table">
  <thead>
    <tr>
      <th>Tier</th>
      <th>Art</th>
      <th>Zeit</th>
      <th>Futter</th>
      <th>Aktion</th>
    </tr>
  </thead>
  <tbody>
    <?php
    // JOIN mit "tier" und "futter" liefert Tiername, Tierart und Futternamen
    $sql = "SELECT fu.fuetterung_id, t.Name AS tier_name, t.Art AS tier_art, fu.zeit, fut.Name AS futter_name
            FROM fuetterung fu
            LEFT JOIN tier t ON fu.tier_id = t.Tier_ID
            LEFT JOIN futter fut ON fu.futter = fut.Futter_ID";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            ?>
            <tr>
              <td><?php echo htmlspecialchars($row['tier_name']); ?></td>
              <td><?php echo htmlspecialchars($row['tier_art']); ?></td>
              <td><?php echo htmlspecialchars($row['zeit']); ?></td>
              <td><?php echo htmlspecialchars($row['futter_name']); ?></td>
              <td>
                <a href="?delete_id=<?php echo $row['fuetterung_id']; ?>" style="color:red;" onclick="return confirm('Wirklich löschen?');">Löschen</a>
                &nbsp;|&nbsp;
                <a href="?edit_id=<?php echo $row['fuetterung_id']; ?>">Ändern</a>
              </td>
            </tr>
            <?php
        }
    } else {
        echo "<tr><td colspan='5'>Keine Einträge gefunden.</td></tr>";
    }
    ?>
  </tbody>
</table>
<p>&nbsp;</p>

<h3><?php echo ($fuetterung_id == 0 ? "Neuen Datensatz hinzufügen" : "Datensatz bearbeiten"); ?></h3>
<form action="fuetterung.php" method="POST">
  <!-- Hidden-Feld für fuetterung_id -->
  <input type="hidden" name="fuetterung_id" value="<?php echo $fuetterung_id; ?>">

  <label for="tier_id">Tier:</label>
  <select name="tier_id" id="tier_id" required>
    <option value="">Bitte wählen</option>
    <?php
    $sqlTier = "SELECT Tier_ID, Name FROM tier";
    $resultTier = $conn->query($sqlTier);
    while ($t = $resultTier->fetch_assoc()) {
      $selected = ($t['Tier_ID'] == $tier_id) ? "selected" : "";
      echo "<option value='{$t['Tier_ID']}' $selected>{$t['Name']}</option>";
    }
    ?>
  </select>

  <label for="zeit">Zeit (z.B. 09:00):</label>
  <input type="text" name="zeit" id="zeit" value="<?php echo htmlspecialchars($zeit); ?>" required>

  <label for="futter_id">Futter:</label>
  <select name="futter_id" id="futter_id" required>
    <option value="">Bitte wählen</option>
    <?php
    $sqlFutter = "SELECT Futter_ID, Name FROM futter";
    $resultFutter = $conn->query($sqlFutter);
    while ($f = $resultFutter->fetch_assoc()) {
      $selected = ($f['Futter_ID'] == $futter_id) ? "selected" : "";
      echo "<option value='{$f['Futter_ID']}' $selected>{$f['Name']}</option>";
    }
    ?>
  </select>

  <button type="submit" class="btn">Speichern</button>
</form>

<?php include 'admin_footer.php'; ?>
