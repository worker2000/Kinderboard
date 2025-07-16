<?php
include 'config/db.php';

$kind = isset($_GET['kind']) ? $_GET['kind'] : '';
if (!$kind) {
  header("Location: index.php");
  exit;
}

// ⭐ Aktuelle Gesamtsterne nur gültige
$res = $mysqli->query("
  SELECT SUM(sterne) AS summe 
  FROM sterne_log 
  WHERE kind='$kind' AND status='valid'
");
$row = $res->fetch_assoc();
$gesamt_sterne = $row['summe'] ? intval($row['summe']) : 0;

// Tageszeit feststellen
$jetzt = new DateTime();
$uhrzeit = $jetzt->format('H:i');
$stunde = (int)$jetzt->format('H');

if ($stunde >= 5 && $stunde < 12) {
  $zeit_bereich = 'Vormittag';
} elseif ($stunde >= 12 && $stunde < 17) {
  $zeit_bereich = 'Nachmittag';
} elseif ($stunde >= 17 && $stunde < 22) {
  $zeit_bereich = 'Abend';
} else {
  $zeit_bereich = null;
}

// Wochentag-Kürzel
$heute = strtolower(substr($jetzt->format('D'), 0, 2)); // mo, tu, we, ...
$tage = [
  'Monday'    => 'Montag',
  'Tuesday'   => 'Dienstag',
  'Wednesday' => 'Mittwoch',
  'Thursday'  => 'Donnerstag',
  'Friday'    => 'Freitag',
  'Saturday'  => 'Samstag',
  'Sunday'    => 'Sonntag'
];
$tag = $tage[$jetzt->format('l')];

// Motivation für Ian
$show_video = false;
$video_file = '';

if ($kind === 'Ian') {
  $res = $mysqli->query(
    "SELECT COUNT(*) AS total, SUM(status) AS done 
     FROM tasks WHERE kind='Ian' AND zeit='Vormittag'
     AND (
      einmalig_date = CURDATE()
      OR (
        einmalig_date IS NULL
        AND (
          mo=1 AND '$heute'='mo' OR
          di=1 AND '$heute'='tu' OR
          mi=1 AND '$heute'='we' OR
          do=1 AND '$heute'='th' OR
          fr=1 AND '$heute'='fr' OR
          sa=1 AND '$heute'='sa' OR
          so=1 AND '$heute'='su'
        )
      )
    )"
  );
  $row = $res->fetch_assoc();

  if ($row['total'] > 0 && $row['total'] == $row['done']) {
    $show_video = true;
    $video_dir = __DIR__ . "/motivation/ian/";
    $files = glob($video_dir . "*.mp4");
    if ($files) {
      $rand_key = array_rand($files);
      $video_file = "motivation/ian/" . basename($files[$rand_key]);
    }
  }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Aufgabenboard – <?php echo htmlspecialchars($kind); ?></title>
  <link rel="stylesheet" href="styles/style.css">
</head>
<body>

<h1>Hallo <?php echo htmlspecialchars($kind); ?>!</h1>
<p class="today-info">
  Heute ist <?php echo $tag; ?>, es ist <span id="live-time"><?php echo $uhrzeit; ?></span> Uhr
</p>

<a href="index.php" class="back-button">🔙 Zurück</a>
<span class="sterne-anzeige">⭐ Du hast aktuell <?php echo $gesamt_sterne; ?> Sterne</span>
<a href="shop.php?kind=<?php echo urlencode($kind); ?>" class="shop-link">🎁 Zum Belohnungs-Shop</a>

<?php
// === Einmalige Aufgaben ===
$query = "
  SELECT * FROM tasks 
  WHERE kind='$kind' 
  AND einmalig_date = CURDATE()
  ORDER BY zeit
";
$res = $mysqli->query($query);
if ($res && $res->num_rows > 0):
  echo "<h2>🎯 Einmalige Aufgaben</h2>";
  while ($row = $res->fetch_assoc()):
    $done = $row['status'] ? '✅' : '❌';
    $sterne = intval($row['sterne']);
    $sterne_txt = $sterne > 0 ? " – ⭐ {$sterne}" : "";

    echo "
      <div class='task-item'>
        <form method='POST' action='update_task.php'>
          <input type='hidden' name='id' value='{$row['id']}'>
          <input type='hidden' name='kind' value='{$kind}'>
          <button type='submit'>{$done} {$row['name']}{$sterne_txt}</button>
        </form>
      </div>
    ";
  endwhile;
endif;
?>

<?php
// === Tageszeit-Bereich ===
if ($zeit_bereich) {
  echo "<h2>🕒 $zeit_bereich</h2>";

  $query = "
    SELECT * FROM tasks 
    WHERE kind='$kind' AND zeit='$zeit_bereich'
    AND (
      einmalig_date IS NULL
      AND (
        mo=1 AND '$heute'='mo' OR
        di=1 AND '$heute'='tu' OR
        mi=1 AND '$heute'='we' OR
        do=1 AND '$heute'='th' OR
        fr=1 AND '$heute'='fr' OR
        sa=1 AND '$heute'='sa' OR
        so=1 AND '$heute'='su'
      )
    )
  ";
  $res = $mysqli->query($query);

  if ($res && $res->num_rows > 0):
    while ($row = $res->fetch_assoc()):
      $done = $row['status'] ? '✅' : '❌';
      $sterne = intval($row['sterne']);
      $sterne_txt = $sterne > 0 ? " – ⭐ {$sterne}" : "";

      echo "
        <div class='task-item'>
          <form method='POST' action='update_task.php'>
            <input type='hidden' name='id' value='{$row['id']}'>
            <input type='hidden' name='kind' value='{$kind}'>
            <button type='submit'>{$done} {$row['name']}{$sterne_txt}</button>
          </form>
        </div>
      ";
    endwhile;
  else:
    echo "<p>Keine Aufgaben für den $zeit_bereich.</p>";
  endif;
}
?>

<?php if ($show_video && $video_file): ?>
  <div id="video-overlay">
    <video src="<?php echo $video_file; ?>" autoplay controls></video>
  </div>
  <script>
    const video = document.querySelector('#video-overlay video');
    const overlay = document.getElementById('video-overlay');
    video.onended = () => { overlay.style.display = 'none'; };
  </script>
<?php endif; ?>

<script>
  function updateClock() {
    const now = new Date();
    const h = String(now.getHours()).padStart(2, '0');
    const m = String(now.getMinutes()).padStart(2, '0');
    document.getElementById('live-time').textContent = `${h}:${m}`;
  }
  setInterval(updateClock, 1000);
</script>

<script>
  let idleTime = 30 * 1000;
  let idleTimer;
  function resetTimer() {
    clearTimeout(idleTimer);
    idleTimer = setTimeout(() => {
      window.location.href = 'index.php';
    }, idleTime);
  }
  window.onload = resetTimer;
  document.onmousemove = resetTimer;
  document.onkeypress = resetTimer;
  document.onclick = resetTimer;
  document.ontouchstart = resetTimer;
</script>

</body>
</html>
