<?php include '../config/db.php'; ?>

<?php
// Alle Kinder holen für: Erstellen, Filter & Kopieren
$kinderRes1 = $mysqli->query("SELECT name FROM kinder ORDER BY name");
$kinderRes2 = $mysqli->query("SELECT name FROM kinder ORDER BY name");
?>

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Admin Panel</title>
  <link rel="stylesheet" href="../styles/style.css">
</head>
<body>

<a href="log_view.php" class="log-button">📅 Logfiles</a>
<a href="shop_admin.php" class="log-button">🎁 Shop Admin</a>
<a href="shop_log.php" class="log-button">📜 Shop-Logfile</a>
<a href="kinder_admin.php" class="log-button">🧒 Kinder verwalten</a>
<a href="discord_admin.php" class="log-button">🤖 Discord Bot</a>
<a href="statistik_admin.php" class="log-button">📊 Statistik</a>

<h1>Admin Panel</h1>

<!-- Aufgabe erstellen -->
<form method="POST" action="add_task.php">
  <label>Kind(er):</label>
  <select name="kinder[]" multiple required>
    <?php while ($row = $kinderRes1->fetch_assoc()): ?>
      <option value="<?php echo htmlspecialchars($row['name']); ?>">
        <?php echo htmlspecialchars($row['name']); ?>
      </option>
    <?php endwhile; ?>
  </select>

  <label>Zeit:</label>
  <select name="zeit" required>
    <option value="Vormittag">Vormittag</option>
    <option value="Nachmittag">Nachmittag</option>
    <option value="Abend">Abend</option>
  </select>

  <label>Name:</label>
  <input name="name" required>

  <fieldset>
    <legend>Wiederholt sich an:</legend>
    <label><input type="checkbox" name="mo" value="1"> Mo</label>
    <label><input type="checkbox" name="di" value="1"> Di</label>
    <label><input type="checkbox" name="mi" value="1"> Mi</label>
    <label><input type="checkbox" name="do" value="1"> Do</label>
    <label><input type="checkbox" name="fr" value="1"> Fr</label>
    <label><input type="checkbox" name="sa" value="1"> Sa</label>
    <label><input type="checkbox" name="so" value="1"> So</label>
    <label>Einmaliges Datum (optional):</label>
    <input type="date" name="einmalig_date">
    <label>Sterne:</label>
    <input type="number" name="sterne" min="0" value="0">
  </fieldset>

  <button type="submit">➕ Aufgabe hinzufügen</button>
</form>

<!-- Filter -->
<h2>Alle Aufgaben</h2>
<form method="GET" action="admin.php">
  <label>Zeige nur Aufgaben für:</label>
  <select name="filter_kind">
    <option value="">-- Alle --</option>
    <?php while ($row = $kinderRes2->fetch_assoc()): ?>
      <option value="<?php echo htmlspecialchars($row['name']); ?>"
        <?php if (isset($_GET['filter_kind']) && $_GET['filter_kind'] == $row['name']) echo 'selected'; ?>>
        <?php echo htmlspecialchars($row['name']); ?>
      </option>
    <?php endwhile; ?>
  </select>
  <button type="submit">Filtern</button>
</form>

<?php
$filter = '';
if (!empty($_GET['filter_kind'])) {
  $kind = $mysqli->real_escape_string($_GET['filter_kind']);
  $filter = "WHERE kind = '$kind'";
}

$result = $mysqli->query("SELECT * FROM tasks $filter ORDER BY kind, zeit");

while ($row = $result->fetch_assoc()) {
  $tage = [];
  if ($row['mo']) $tage[] = 'Mo';
  if ($row['di']) $tage[] = 'Di';
  if ($row['mi']) $tage[] = 'Mi';
  if ($row['do']) $tage[] = 'Do';
  if ($row['fr']) $tage[] = 'Fr';
  if ($row['sa']) $tage[] = 'Sa';
  if ($row['so']) $tage[] = 'So';
  $tage_txt = implode(', ', $tage);
  $einmalig = $row['einmalig_date'] ? "Einmalig: " . $row['einmalig_date'] : "";
  $info = trim("$tage_txt " . $einmalig);

  echo "
  <div class='task-item'>
    <strong>{$row['kind']} – {$row['zeit']} – {$row['name']}</strong><br>
    <small>{$info} | ⭐ {$row['sterne']} Sterne</small><br>

    <form method='POST' action='update_sterne.php' style='display:inline;'>
      <input type='hidden' name='id' value='{$row['id']}'>
      <input type='number' name='sterne' value='{$row['sterne']}' min='0' style='width:60px;'>
      <button>✅ Aktualisieren</button>
    </form>

    <form method='POST' action='delete_task.php' style='display:inline;'>
      <input type='hidden' name='id' value='{$row['id']}'>
      <button class='delete-btn'>🗑️ Löschen</button>
    </form>

    <form method='POST' action='copy_task.php' style='display:inline; margin-left:1rem;'>
      <input type='hidden' name='id' value='{$row['id']}'>
      <label>Für:</label>
      <select name='kind'>";
        $kinderRes3 = $mysqli->query("SELECT name FROM kinder ORDER BY name");
        while ($k = $kinderRes3->fetch_assoc()) {
          echo "<option value='" . htmlspecialchars($k['name']) . "'>" . htmlspecialchars($k['name']) . "</option>";
        }
  echo "</select>
      <button>📄 Kopieren</button>
    </form>
  </div>
  ";
}
?>

</body>
</html>
