<?php
include 'config/db.php';

// Zeitraum: letzte 7 Tage
$heute = date('Y-m-d');
$startDatum = date('Y-m-d', strtotime('-6 days'));

// Kinder laden
$kinderRes = $mysqli->query("SELECT name FROM kinder ORDER BY name");
$kinder = [];
while ($row = $kinderRes->fetch_assoc()) {
  $kinder[] = $row['name'];
}

// Daten vorbereiten
$daten = [];
$aufgabenMap = [];

foreach ($kinder as $kind) {
  // Leere Tagesliste für letzten 7 Tage
  $daten[$kind] = [];
  for ($i = 6; $i >= 0; $i--) {
    $tag = date('Y-m-d', strtotime("-$i days"));
    $daten[$kind][$tag] = 0;
  }

  // 1. Sterne pro Tag
  $res = $mysqli->query("
    SELECT DATE(timestamp) AS datum, SUM(sterne) AS sterne
    FROM sterne_log
    WHERE kind = '$kind' AND status = 'valid'
      AND DATE(timestamp) BETWEEN '$startDatum' AND '$heute'
    GROUP BY DATE(timestamp)
  ");
  while ($row = $res->fetch_assoc()) {
    $daten[$kind][$row['datum']] = (int)$row['sterne'];
  }

  // 2. Aufgaben je Tag + Zeitbereich
  $aufgabenRes = $mysqli->query("
    SELECT DATE(l.timestamp) AS datum, t.name, t.zeit
    FROM task_logs l
    JOIN tasks t ON l.task_id = t.id
    WHERE l.kind = '$kind'
      AND l.status = 'done'
      AND DATE(l.timestamp) BETWEEN '$startDatum' AND '$heute'
    ORDER BY datum ASC
  ");

  $aufgabenMap[$kind] = [];
  while ($row = $aufgabenRes->fetch_assoc()) {
    $d = $row['datum'];
    $z = $row['zeit'];
    $n = $row['name'];
    $eintrag = "$z: $n";

    if (!isset($aufgabenMap[$kind][$d])) {
      $aufgabenMap[$kind][$d] = [];
    }
    $aufgabenMap[$kind][$d][] = $eintrag;
  }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Sternestatistik</title>
  <link rel="stylesheet" href="styles/style.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

  <h1>📊 Sterne-Statistik der letzten 7 Tage</h1>
  <a href="index.php" class="back-button">🔙 Zurück</a>

<?php foreach ($daten as $kind => $sterneProTag): 
  // X-Achse mit Wochentag + Datum
  $labels = json_encode(array_map(function($datum) {
    $tage = ['So','Mo','Di','Mi','Do','Fr','Sa'];
    $tagName = $tage[date('w', strtotime($datum))];
    return "$tagName ($datum)";
  }, array_keys($sterneProTag)));

  $werte = json_encode(array_values($sterneProTag));
  $tooltipMap = json_encode($aufgabenMap[$kind] ?? []);
  $chartId = "chart_" . md5($kind);
?>

  <h2><?php echo htmlspecialchars($kind); ?></h2>
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
                const label = context[0].label;
                const datum = label.match(/\((\d{4}-\d{2}-\d{2})\)/)?.[1];
                const aufgabenMap = <?php echo $tooltipMap; ?>;
                const eintraege = aufgabenMap[datum] || [];
                return eintraege.map(e => '✅ ' + e);
              }
            }
          }
        }
      }
    });
  </script>
<?php endforeach; ?>

</body>
</html>
