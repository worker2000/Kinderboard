<?php
include '../config/db.php';

// Gewähltes Datum oder heute
$datum = isset($_GET['datum']) ? $_GET['datum'] : date('Y-m-d');

// Bemerkung speichern
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remark']) && isset($_POST['kind'])) {
  $kind = $mysqli->real_escape_string($_POST['kind']);
  $text = $mysqli->real_escape_string($_POST['remark']);
  $timestamp = date('Y-m-d H:i:s');

  // Prüfen ob es schon einen Eintrag gibt
  $check = $mysqli->query("SELECT * FROM remarks WHERE datum='$datum' AND kind='$kind'");
  if ($check->num_rows > 0) {
    $mysqli->query("UPDATE remarks SET remark='$text', timestamp='$timestamp' WHERE datum='$datum' AND kind='$kind'");
  } else {
    $mysqli->query("INSERT INTO remarks (datum, kind, remark, timestamp) VALUES ('$datum', '$kind', '$text', '$timestamp')");
  }

  header("Location: log_view.php?datum=$datum");
  exit;
}

// Alle Kinder holen
$kinderRes = $mysqli->query("SELECT name FROM kinder ORDER BY name");
$kinder = [];
while ($k = $kinderRes->fetch_assoc()) {
  $kinder[] = $k['name'];
}

?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Log-Übersicht</title>
  <link rel="stylesheet" href="../styles/style.css">
  <style>
    .remark-form { display: none; margin: 1rem 0; }
    .show-btn { margin: 0.5rem 0; background: #1976d2; color: #fff; border: none; padding: 0.3rem 1rem; border-radius: 4px; cursor: pointer; }
    .show-btn:hover { background: #1565c0; }
  </style>
</head>
<body>

<h1>Log-Übersicht</h1>

<!-- Kalender -->
<form method="GET" action="log_view.php">
  <label>Wähle ein Datum:</label>
  <input type="date" name="datum" value="<?php echo $datum; ?>">
  <button type="submit">Anzeigen</button>
</form>

<hr>

<?php
foreach ($kinder as $kind) {
  echo "<h2>$kind</h2>";

  // Aufgaben-Logs
  $res = $mysqli->query("
    SELECT l.*, t.name 
    FROM task_logs l
    JOIN tasks t ON l.task_id = t.id
    WHERE l.kind='$kind' AND l.datum='$datum'
    ORDER BY l.timestamp
  ");
  if ($res && $res->num_rows > 0) {
    echo "<h3>Aufgaben</h3><ul>";
    while ($row = $res->fetch_assoc()) {
      echo "<li>{$row['name']} – {$row['status']} um {$row['timestamp']}</li>";
    }
    echo "</ul>";
  } else {
    echo "<p>Keine Aufgaben-Einträge.</p>";
  }

  // Sterne-Logs
  $sterne = $mysqli->query("
    SELECT * FROM sterne_log 
    WHERE kind='$kind' AND DATE(timestamp)='$datum'
    ORDER BY timestamp
  ");
  if ($sterne && $sterne->num_rows > 0) {
    echo "<h3>⭐ Sterne</h3><ul>";
    while ($row = $sterne->fetch_assoc()) {
      $status = $row['status'] == 'valid' ? '✅ gültig' : '❌ abgelehnt';
      $rejectBtn = $row['status'] == 'valid'
        ? "<a href='reject_sterne.php?id={$row['id']}&datum=$datum' class='delete-btn'>Sterne ablehnen</a>"
        : '';
      echo "<li>{$row['sterne']} ⭐ am {$row['timestamp']} – {$status} {$rejectBtn}</li>";
    }
    echo "</ul>";
  } else {
    echo "<p>Keine Sterne-Einträge.</p>";
  }

  // Aktuelle Bemerkung laden
  $remark_res = $mysqli->query("SELECT * FROM remarks WHERE datum='$datum' AND kind='$kind' LIMIT 1");
  $remark = $remark_res->num_rows > 0 ? $remark_res->fetch_assoc()['remark'] : '';

  // Button + verstecktes Formular
  echo "<button class='show-btn' onclick=\"toggleRemark('$kind')\">✏️ Bemerkung hinzufügen</button>";
  echo "
    <form method='POST' action='log_view.php?datum=$datum' class='remark-form' id='remark-$kind'>
      <input type='hidden' name='kind' value='$kind'>
      <textarea name='remark' rows='4' style='width:100%;'>" . htmlspecialchars($remark) . "</textarea><br>
      <button type='submit'>💾 Speichern</button>
    </form>
  ";

  echo "<hr>";
}
?>

<script>
  function toggleRemark(kind) {
    const form = document.getElementById('remark-' + kind);
    form.style.display = form.style.display === 'block' ? 'none' : 'block';
  }
</script>

</body>
</html>
