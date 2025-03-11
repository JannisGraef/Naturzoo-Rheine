<?php
require_once '../db_config.php';
require_once '../logger.php';
include 'admin_header.php';

// --- Löschen ---
// Erwartet werden nun: delete_pfleger, delete_revier, delete_zeit, delete_tag, delete_futter, delete_menge
if (isset($_GET['delete_pfleger']) && isset($_GET['delete_revier']) && isset($_GET['delete_zeit']) && isset($_GET['delete_tag']) && isset($_GET['delete_futter']) && isset($_GET['delete_menge'])) {
    $pfleger_id = intval($_GET['delete_pfleger']);
    $revier_id  = intval($_GET['delete_revier']);
    $zeit_id    = intval($_GET['delete_zeit']);
    $tag_id     = intval($_GET['delete_tag']);
    $futter     = intval($_GET['delete_futter']);
    $menge      = intval($_GET['delete_menge']);
    $conn->query("DELETE FROM pfleger_tier 
                   WHERE Pfleger_ID = $pfleger_id 
                     AND Revier_ID = $revier_id 
                     AND Zeit_ID = $zeit_id 
                     AND Tag_ID = $tag_id
                     AND futter = $futter
                     AND menge = $menge");
    header("Location: admin_pflegerpanel.php");
    exit;
}

// --- Initialisierung der Formularfelder ---
$sel_pfleger = 0;
$sel_revier  = 0;
$sel_zeit    = 0;
$sel_tag     = 0;
$sel_futter  = 0;
$sel_menge   = 0;

// --- Bearbeiten: Vorbefüllung aus GET-Parametern ---
// Die Bearbeitungs-Links übergeben jetzt edit_pfleger, edit_revier, edit_zeit, edit_tag, edit_futter und edit_menge
if (isset($_GET['edit_pfleger']) && isset($_GET['edit_revier']) && isset($_GET['edit_zeit']) && isset($_GET['edit_tag']) && isset($_GET['edit_futter']) && isset($_GET['edit_menge'])) {
    $sel_pfleger = intval($_GET['edit_pfleger']);
    $sel_revier  = intval($_GET['edit_revier']);
    $sel_zeit    = intval($_GET['edit_zeit']);
    $sel_tag     = intval($_GET['edit_tag']);
    $sel_futter  = intval($_GET['edit_futter']);
    $sel_menge   = intval($_GET['edit_menge']);
}

// --- Hinzufügen / Aktualisieren ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pfleger = intval($_POST['pfleger']);
    $revier  = intval($_POST['revier']);
    $zeit    = intval($_POST['zeit']);
    $tag     = intval($_POST['tag']);
    $futter  = intval($_POST['futter']);
    $menge   = intval($_POST['menge']);

    // Bei Bearbeitung werden die Originalwerte (mit orig_*) übermittelt
    if (isset($_POST['orig_pfleger']) && isset($_POST['orig_revier']) && isset($_POST['orig_zeit']) && isset($_POST['orig_tag']) && isset($_POST['orig_futter']) && isset($_POST['orig_menge'])) {
        $orig_pfleger = intval($_POST['orig_pfleger']);
        $orig_revier  = intval($_POST['orig_revier']);
        $orig_zeit    = intval($_POST['orig_zeit']);
        $orig_tag     = intval($_POST['orig_tag']);
        $orig_futter  = intval($_POST['orig_futter']);
        $orig_menge   = intval($_POST['orig_menge']);
        $sql = "UPDATE pfleger_tier 
                SET Pfleger_ID = $pfleger, 
                    Revier_ID = $revier, 
                    Zeit_ID = $zeit, 
                    Tag_ID = $tag,
                    futter = $futter,
                    menge = $menge
                WHERE Pfleger_ID = $orig_pfleger 
                  AND Revier_ID = $orig_revier 
                  AND Zeit_ID = $orig_zeit 
                  AND Tag_ID = $orig_tag
                  AND futter = $orig_futter
                  AND menge = $orig_menge";
        $conn->query($sql);
    } else {
        $sqlCheck = "SELECT * FROM pfleger_tier 
                     WHERE Pfleger_ID = $pfleger 
                       AND Revier_ID = $revier 
                       AND Zeit_ID = $zeit 
                       AND Tag_ID = $tag
                       AND futter = $futter
                       AND menge = $menge";
        $resultCheck = $conn->query($sqlCheck);
        if ($resultCheck && $resultCheck->num_rows == 0) {
            $sqlInsert = "INSERT INTO pfleger_tier (Pfleger_ID, Revier_ID, Zeit_ID, Tag_ID, futter, menge)
                          VALUES ($pfleger, $revier, $zeit, $tag, $futter, $menge)";
            $conn->query($sqlInsert);
        }
    }
    header("Location: admin_pflegerpanel.php");
    exit;
}
?>

<h2>Pflegerpanel verwalten (Admin)</h2>
<table class="admin-table">
  <thead>
    <tr>
      <th>Pfleger</th>
      <th>Revier</th>
      <th>Tag</th>
      <th>Zeit (von - bis)</th>
      <th>Art des Futters</th>
      <th>Menge</th>
      <th>Aktion</th>
    </tr>
  </thead>
  <tbody>
    <?php
    // Wir verbinden pfleger_tier (alias pt) mit den anderen Tabellen:
    // - Pfleger: p
    // - Revier: rev (über pt.Revier_ID)
    // - Zeit: z
    // - Tag: tag
    // - Futter: fut (über pt.futter)
    // - Menge: m (über pt.menge)
    $sql = "SELECT pt.*, 
                   p.Name AS PflegerName, 
                   CONCAT(z.Uhrzeit, ' - ', z.Endzeit) AS Zeit,
                   tag.Wochentag,
                   rev.Name AS Revier,
                   fut.Name AS ArtDesFutters,
                   m.Menge AS Menge
            FROM pfleger_tier pt
            JOIN pfleger p ON pt.Pfleger_ID = p.Pfleger_ID
            JOIN revier rev ON pt.Revier_ID = rev.Revier_ID
            JOIN zeit z ON pt.Zeit_ID = z.Zeit_ID
            JOIN tag ON pt.Tag_ID = tag.Tag_ID
            LEFT JOIN futter fut ON pt.futter = fut.Futter_ID
            LEFT JOIN menge m ON pt.menge = m.Menge_ID
            GROUP BY pt.Pfleger_ID, pt.Revier_ID, pt.Zeit_ID, pt.Tag_ID, pt.futter, pt.menge
            ORDER BY p.Name, tag.Wochentag, z.Uhrzeit";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0):
      while ($row = $result->fetch_assoc()):
    ?>
        <tr>
          <td><?php echo htmlspecialchars($row['PflegerName']); ?></td>
          <td><?php echo htmlspecialchars($row['Revier']); ?></td>
          <td><?php echo htmlspecialchars($row['Wochentag']); ?></td>
          <td><?php echo htmlspecialchars($row['Zeit']); ?></td>
          <td><?php echo htmlspecialchars($row['ArtDesFutters']); ?></td>
          <td><?php echo htmlspecialchars($row['Menge']) . ' kg'; ?></td>

          <td>
            <a href="?delete_pfleger=<?php echo $row['Pfleger_ID']; ?>&delete_revier=<?php echo $row['Revier_ID']; ?>&delete_zeit=<?php echo $row['Zeit_ID']; ?>&delete_tag=<?php echo $row['Tag_ID']; ?>&delete_futter=<?php echo $row['futter']; ?>&delete_menge=<?php echo $row['menge']; ?>" style="color:red;" onclick="return confirm('Wirklich löschen?');">Löschen</a>
            &nbsp;|&nbsp;
            <a href="?edit_pfleger=<?php echo $row['Pfleger_ID']; ?>&edit_revier=<?php echo $row['Revier_ID']; ?>&edit_zeit=<?php echo $row['Zeit_ID']; ?>&edit_tag=<?php echo $row['Tag_ID']; ?>&edit_futter=<?php echo $row['futter']; ?>&edit_menge=<?php echo $row['menge']; ?>">Ändern</a>
          </td>
        </tr>
    <?php
      endwhile;
    else:
      echo "<tr><td colspan='7'>Keine Einträge gefunden.</td></tr>";
    endif;
    ?>
  </tbody>
</table>

<p>&nbsp;</p>
<h3><?php echo ($sel_pfleger == 0 ? "Neuen Eintrag hinzufügen" : "Eintrag bearbeiten"); ?></h3>
<form action="admin_pflegerpanel.php" method="POST">
  <label for="pfleger">Pfleger:</label>
  <select name="pfleger" id="pfleger" required>
    <option value="">Bitte wählen</option>
    <?php
    $sqlPfleger = "SELECT Pfleger_ID, Name FROM pfleger";
    $resultPfleger = $conn->query($sqlPfleger);
    while ($p = $resultPfleger->fetch_assoc()) {
        $selected = ($sel_pfleger == $p['Pfleger_ID']) ? "selected" : "";
        echo "<option value='{$p['Pfleger_ID']}' $selected>" . htmlspecialchars($p['Name']) . "</option>";
    }
    ?>
  </select>

  <label for="revier">Revier:</label>
  <select name="revier" id="revier" required>
    <option value="">Bitte wählen</option>
    <?php
    $sqlRevier = "SELECT Revier_ID, Name FROM revier";
    $resultRevier = $conn->query($sqlRevier);
    while ($r = $resultRevier->fetch_assoc()) {
        $selected = ($sel_revier == $r['Revier_ID']) ? "selected" : "";
        echo "<option value='{$r['Revier_ID']}' $selected>" . htmlspecialchars($r['Name']) . "</option>";
    }
    ?>
  </select>

  <label for="zeit">Zeit:</label>
  <select name="zeit" id="zeit" required>
    <option value="">Bitte wählen</option>
    <?php
    $sqlZeit = "SELECT Zeit_ID, Uhrzeit, Endzeit FROM zeit";
    $resultZeit = $conn->query($sqlZeit);
    while ($z = $resultZeit->fetch_assoc()) {
        $selected = ($sel_zeit == $z['Zeit_ID']) ? "selected" : "";
        echo "<option value='{$z['Zeit_ID']}' $selected>" . htmlspecialchars($z['Uhrzeit'] . ' - ' . $z['Endzeit']) . "</option>";
    }
    ?>
  </select>

  <label for="tag">Tag:</label>
  <select name="tag" id="tag" required>
    <option value="">Bitte wählen</option>
    <?php
    $sqlTag = "SELECT Tag_ID, Wochentag FROM tag";
    $resultTag = $conn->query($sqlTag);
    while ($tag = $resultTag->fetch_assoc()) {
        $selected = ($sel_tag == $tag['Tag_ID']) ? "selected" : "";
        echo "<option value='{$tag['Tag_ID']}' $selected>" . htmlspecialchars($tag['Wochentag']) . "</option>";
    }
    ?>
  </select>

  <label for="futter">Futter:</label>
  <select name="futter" id="futter" required>
    <option value="">Bitte wählen</option>
    <?php
    $sqlFutter = "SELECT Futter_ID, Name FROM futter";
    $resultFutter = $conn->query($sqlFutter);
    while ($f = $resultFutter->fetch_assoc()) {
        $selected = ($sel_futter == $f['Futter_ID']) ? "selected" : "";
        echo "<option value='{$f['Futter_ID']}' $selected>" . htmlspecialchars($f['Name']) . "</option>";
    }
    ?>
  </select>

  <label for="menge">Menge:</label>
  <select name="menge" id="menge" required>
    <option value="">Bitte wählen</option>
    <?php
    $sqlMenge = "SELECT Menge_ID, Menge FROM menge";
    $resultMenge = $conn->query($sqlMenge);
    while ($m = $resultMenge->fetch_assoc()) {
        $selected = ($sel_menge == $m['Menge_ID']) ? "selected" : "";
        echo "<option value='{$m['Menge_ID']}' $selected>" . htmlspecialchars($m['Menge']) . "</option>";
    }
    ?>
  </select>

  <?php if ($sel_pfleger != 0): ?>
    <input type="hidden" name="orig_pfleger" value="<?php echo $sel_pfleger; ?>">
    <input type="hidden" name="orig_revier" value="<?php echo $sel_revier; ?>">
    <input type="hidden" name="orig_zeit" value="<?php echo $sel_zeit; ?>">
    <input type="hidden" name="orig_tag" value="<?php echo $sel_tag; ?>">
    <input type="hidden" name="orig_futter" value="<?php echo $sel_futter; ?>">
    <input type="hidden" name="orig_menge" value="<?php echo $sel_menge; ?>">
  <?php endif; ?>

  <button type="submit" class="btn">Speichern</button>
</form>

<?php include 'admin_footer.php'; ?>
