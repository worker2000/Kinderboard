<?php
include '../config/db.php';

// Zeitraum & Kind aus Formular oder Default
$start = $_GET['start'] ?? date('Y-m-d', strtotime('-6 days'));
$end   = $_GET['end'] ?? date('Y-m-d');
$kind  = $_GET['kind'] ?? '';

// Alle Kinder für Dropdown
$kinderRes = $mysqli->query("SELECT name FROM kinder ORDER BY name");
$alle_kinder = [];
while ($row = $kinderRes->fetch_assoc()) {
  $alle_kinder[] = $row['name'];
}

// Nur ausführen, wenn Kind ausgewählt wurde
$daten = [];
$aufgabenMap = [];

if ($kind) {
  // Leere Tagesliste für Zeitraum
  $range = new DatePeriod(
    new DateTime($start),
    new DateInterval('P1D'),
    (new DateTime($end))->modify('+1 day')
  );

  foreach ($range as $tag) {
    $daten[$tag->format('Y-m-d')] = 0;
  }

  // 1. Sterne
  $res = $mysqli->query("
    SELECT DATE(timestamp) AS datum, SUM(sterne) AS sterne
    FROM sterne_log
    WHERE kind = '$kind' AND status = 'valid'
      AND DATE(timestamp) BETWEEN '$start' AND '$end'
    GROUP BY DATE(timestamp)
  ");
  while ($row = $res->fetch_assoc()) {
    $daten[$row['datum']] = (int)$row['sterne'];
  }

  // 2. Aufgaben
  $aufgabenRes = $mysqli->query("
    SELECT DATE(l.timestamp) AS datum, t.name, t.zeit
    FROM task_logs l
    JOIN tasks t ON l.task_id = t.id
    WHERE l.kind = '$kind'
      AND l.status = 'done'
      AND DATE(l.timestamp) BETWEEN '$start' AND '$end'
    ORDER BY datum ASC
  ");
  while ($row = $aufgabenRes->fetch_assoc()) {
    $d = $row['datum'];
    $z = $row['zeit'];
    $n = $row['name'];
    $eintrag = "$z: $n";

    if (!isset($aufgabenMap[$d])) {
      $aufgabenMap[$d] = [];
    }
    $aufgabenMap[$d][] = $eintrag;
  }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Admin – Statistik</title>
  <link rel="stylesheet" href="../styles/style.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

  <h1>📊 Admin – Sterne-Statistik</h1>
  <a href="kinder_admin.php" class="back-button">🔙 Zurück</a>

  <form method="GET" style="margin-bottom: 2rem;">
    <label>Kind:</label>
    <select name="kind" required>
      <option value="">-- auswählen --</option>
      <?php foreach ($alle_kinder as $k): ?>
        <option value="<?php echo $k; ?>" <?php if ($k == $kind) echo 'selected'; ?>><?php echo $k; ?></option>
      <?php endforeach; ?>
    </select>

    <label>Von:</label>
    <input type="date" name="start" value="<?php echo $start; ?>" required>

    <label>Bis:</label>
    <input type="date" name="end" value="<?php echo $end; ?>" required>

    <button type="submit">📈 Anzeigen</button>
  </form>

  <?php if ($kind): 
$labels = json_encode(array_map(function($datum) {
  $tage = ['So','Mo','Di','Mi','Do','Fr','Sa'];
  $tagName = $tage[date('w', strtotime($datum))];
  return "$tagName ($datum)";
}, array_keys($daten)));
    $werte = json_encode(array_values($daten));
    $tooltipMap = json_encode($aufgabenMap);
    $chartId = "chart_" . md5($kind . $start . $end);
  ?>
    <h2><?php echo htmlspecialchars($kind); ?> – Sterne von <?php echo $start; ?> bis <?php echo $end; ?></h2>
    <canvas id="<?php echo $chartId; ?>" width="600" height="300"></canvas>
    <script>
      const ctx_<?php echo $chartId; ?> = document.getElementById('<?php echo $chartId; ?>').getContext('2d');
      new Chart(ctx_<?php echo $chartId; ?>, {
        type: 'bar',
        data: {
          labels: <?php echo $labels; ?>,
          datasets: [{
            label: '⭐ Sterne',
            data: <?php echo $werte; ?>,
            backgroundColor: 'rgba(33, 150, 243, 0.5)',
            borderColor: 'rgba(33, 150, 243, 1)',
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          scales: {
            y: {
              beginAtZero: true,
              ticks: { stepSize: 1 }
            }
          },
          plugins: {
            tooltip: {
              callbacks: {
                afterBody: function(context) {
                  const datum = context[0].label;
                  const map = <?php echo $tooltipMap; ?>;
                  const eintraege = map[datum] || [];
                  return eintraege.map(e => '✅ ' + e);
                }
              }
            }
          }
        }
      });
    </script>
  <?php endif; ?>

</body>
</html>
