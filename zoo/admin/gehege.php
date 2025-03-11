<?php
require_once '../db_config.php';
include 'admin_header.php';

// Löschen
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM gebäude WHERE Gebäude_ID = $delete_id");
    header("Location: gehege.php");
    exit;
}

// Initialisiere Formularfelder
$gebaeude_id = 0;
$name = "";

// Bearbeiten: Falls edit_id gesetzt ist, lade den entsprechenden Datensatz
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    $sql = "SELECT * FROM gebäude WHERE Gebäude_ID = $edit_id";
    $result = $conn->query($sql);
    if ($result && $result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $gebaeude_id = $row['Gebäude_ID'];
        $name = $row['Name'];
        // Beachte: Das Bild-Feld wird nicht vorbefüllt, da file inputs nicht per HTML befüllt werden können.
    }
}

// Hinzufügen / Bearbeiten
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gebaeude_id = intval($_POST['gebaeude_id']);
    $name = $conn->real_escape_string(trim($_POST['name']));
    $imageData = null;
    if (isset($_FILES['bild']) && $_FILES['bild']['error'] == 0) {
        $imageData = file_get_contents($_FILES['bild']['tmp_name']);
        $imageData = $conn->real_escape_string($imageData);
    }

    if ($gebaeude_id === 0) {
        // Neuer Datensatz
        if ($imageData) {
            $sql = "INSERT INTO gebäude (Name, bild) VALUES ('$name', '$imageData')";
        } else {
            $sql = "INSERT INTO gebäude (Name) VALUES ('$name')";
        }
        $conn->query($sql);
    } else {
        // Bestehenden Datensatz bearbeiten
        $sql = "UPDATE gebäude SET Name='$name'";
        if ($imageData) {
            $sql .= ", bild='$imageData'";
        }
        $sql .= " WHERE Gebäude_ID = $gebaeude_id";
        $conn->query($sql);
    }
    header("Location: gehege.php");
    exit;
}
?>

<h2>Gehege verwalten</h2>
<table class="admin-table">
  <thead>
    <tr>
      <th>ID</th>
      <th>Name</th>
      <th>Bild</th>
      <th>Aktion</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $sql = "SELECT * FROM gebäude";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            ?>
            <tr>
              <td><?php echo $row['Gebäude_ID']; ?></td>
              <td><?php echo htmlspecialchars($row['Name']); ?></td>
              <td>
                <?php if (!empty($row['bild'])): ?>
                  <img src="data:image/jpeg;base64,<?php echo base64_encode($row['bild']); ?>" alt="Bild" style="width:50px;height:50px;">
                <?php else: ?>
                  Kein Bild
                <?php endif; ?>
              </td>
              <td>
                <a href="?delete_id=<?php echo $row['Gebäude_ID']; ?>" style="color:red;" onclick="return confirm('Wirklich löschen?');">Löschen</a>
                &nbsp;|&nbsp;
                <a href="?edit_id=<?php echo $row['Gebäude_ID']; ?>">Ändern</a>
              </td>
            </tr>
            <?php
        }
    } else {
        echo "<tr><td colspan='4'>Keine Gehege gefunden.</td></tr>";
    }
    ?>
  </tbody>
</table>
<p>&nbsp;</p>
<h3>Neues Gehege hinzufügen / bearbeiten</h3>
<p>Zum Bearbeiten trage die bestehende Gebäude_ID ein (über "Ändern" in der Tabelle werden die Felder automatisch vorbelegt), zum Neuanlegen „0“.</p>
<form action="gehege.php" method="POST" enctype="multipart/form-data">
  <label for="gebaeude_id">Gebäude_ID:</label>
  <input type="number" name="gebaeude_id" id="gebaeude_id" value="<?php echo $gebaeude_id; ?>" required>

  <label for="name">Name:</label>
  <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($name); ?>" required>

  <label for="bild">Bild (optional):</label>
  <input type="file" name="bild" id="bild" accept="image/*">

  <button type="submit" class="btn">Speichern</button>
</form>

<?php include 'admin_footer.php'; ?>
