<?php
require_once 'db_config.php';

// Wir holen ALLE Pfleger-Aufgaben aus der Datenbank (ohne Session-Filter).
$sql = "
SELECT 
  p.Name AS PflegerName,
  rev.Name AS Gehege,
  tag.Wochentag,
  z.Uhrzeit AS Startzeit,
  z.Endzeit,
  GROUP_CONCAT(CONCAT(f.Name, ' ', m.Menge) SEPARATOR ', ') AS Fuetterungsdetails
FROM pfleger_tier pt
JOIN pfleger p ON pt.Pfleger_ID = p.Pfleger_ID
JOIN revier rev ON pt.Revier_ID = rev.Revier_ID
JOIN tag ON pt.Tag_ID = tag.Tag_ID
JOIN zeit z ON pt.Zeit_ID = z.Zeit_ID
LEFT JOIN futter f ON pt.futter = f.Futter_ID
LEFT JOIN menge m ON pt.menge = m.Menge_ID
GROUP BY p.Pfleger_ID, rev.Revier_ID, tag.Tag_ID, z.Zeit_ID
ORDER BY p.Name, tag.Wochentag, z.Uhrzeit
";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Pfleger Panel – Naturzoo Rheine</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .admin-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    .admin-table th {
      background-color: #2c5031; /* Dunkelgrün */
      color: #fff;
      padding: 12px;
      text-align: left;
    }
    .admin-table td {
      padding: 12px;
      border: 1px solid #ddd;
    }
  </style>
</head>
<body>
  <header class="main-header">
    <div class="header-content">
      <h1>Pfleger Panel</h1>
      <nav class="main-nav">
        <ul>
          <li><a href="index.php">Home</a></li>
          <li><a href="logout.php">Logout</a></li>
        </ul>
      </nav>
    </div>
  </header>
  <main>
    <section class="dashboard-content">
      <h2>Alle Pfleger und ihre Aufgaben</h2>
      <?php if ($result && $result->num_rows > 0): ?>
        <table class="admin-table">
          <thead>
            <tr>
              <th>Pfleger</th>
              <th>Revier</th>
              <th>Tag</th>
              <th>Arbeitszeit (von - bis)</th>
              <th>Fütterungsdetails</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?php echo htmlspecialchars($row['PflegerName']); ?></td>
              <td><?php echo htmlspecialchars($row['Gehege']); ?></td>
              <td><?php echo htmlspecialchars($row['Wochentag']); ?></td>
              <td>
                <?php echo htmlspecialchars($row['Startzeit']); ?>
                <?php if (!empty($row['Endzeit'])): ?>
                  - <?php echo htmlspecialchars($row['Endzeit']); ?>
                <?php endif; ?>
              </td>
              <td><?php echo htmlspecialchars($row['Fuetterungsdetails']) . ' kg'; ?></td>

            <?php endwhile; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p>Keine Aufgaben gefunden. Bitte füge Einträge in der Tabelle <em>pfleger_tier</em> hinzu.</p>
      <?php endif; ?>
    </section>
  </main>
</body>
</html>
