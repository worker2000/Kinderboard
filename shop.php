<?php
include 'config/db.php';

// Welches Kind?
$kind = isset($_GET['kind']) ? $_GET['kind'] : '';
if (!$kind) {
  header("Location: index.php");
  exit;
}

// Aktuelle Sterne zählen
$res = $mysqli->query("SELECT SUM(sterne) AS summe FROM sterne_log WHERE kind='$kind' AND status='valid'");
$row = $res->fetch_assoc();
$gesamt_sterne = $row['summe'] ? intval($row['summe']) : 0;

// Rewards laden – nur passende für dieses Kind
$rewards = $mysqli->query("
  SELECT * FROM shop_rewards 
  WHERE kinder LIKE '%$kind%' 
    AND id NOT IN (
      SELECT reward_id FROM shop_log WHERE kind = '$kind' AND status = 'open'
    )
  ORDER BY kosten ASC
");
?>

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Belohnungs-Shop für <?php echo htmlspecialchars($kind); ?></title>
  <link rel="stylesheet" href="styles/style.css">
</head>
<body>

  <h1>⭐ Belohnungs-Shop</h1>
  <p>Hallo <?php echo htmlspecialchars($kind); ?>! Du hast aktuell <strong><?php echo $gesamt_sterne; ?> Sterne</strong>.</p>

  <a href="board.php?kind=<?php echo urlencode($kind); ?>" class="back-button">🔙 Zurück zum Board</a>

  <div class="shop-list">
  <?php
  if ($rewards && $rewards->num_rows > 0) {
    while ($row = $rewards->fetch_assoc()) {
      echo '<div class="reward-item">';
      echo "<strong>{$row['name']}</strong><br>";
      echo "<p>{$row['beschreibung']}</p>";

      if ($row['bild_url']) {
        echo "<img src='{$row['bild_url']}' alt='{$row['name']}'><br>";
      }

      if ($row['link_url']) {
        echo "<a href='{$row['link_url']}' target='_blank'>🔗 Mehr Info</a><br>";
      }

      echo "Kosten: ⭐ {$row['kosten']}<br>";

      if ($gesamt_sterne >= $row['kosten']) {
        echo "
          <form method='POST' action='buy_reward.php'>
            <input type='hidden' name='kind' value='$kind'>
            <input type='hidden' name='reward_id' value='{$row['id']}'>
            <input type='hidden' name='kosten' value='{$row['kosten']}'>
            <button>Kaufen</button>
          </form>
        ";
      } else {
        $fehlt = $row['kosten'] - $gesamt_sterne;
        echo "<p class='not-enough'>Es fehlen dir aktuell noch ⭐ {$fehlt} – streng dich weiter an!</p>";
      }

      echo "</div>";
    }
  } else {
    echo "<p>Keine Belohnungen gefunden, die zu dir passen.</p>";
  }
  ?>
  </div>

</body>
</html>
