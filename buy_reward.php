<?php
include 'config/db.php';

$kind = $_POST['kind'];
$reward_id = intval($_POST['reward_id']);
$kosten = intval($_POST['kosten']);

// Aktuelle Sterne prüfen
$res = $mysqli->query("SELECT SUM(sterne) AS summe FROM sterne_log WHERE kind='$kind' AND status='valid'");
$row = $res->fetch_assoc();
$gesamt_sterne = $row['summe'] ? intval($row['summe']) : 0;

if ($gesamt_sterne >= $kosten) {
  // Sterne als Abzug loggen
  $timestamp = date('Y-m-d H:i:s');
  $mysqli->query(
    "INSERT INTO sterne_log (kind, task_id, sterne, status, timestamp)
     VALUES ('$kind', 0, -$kosten, 'valid', '$timestamp')"
  );

  // Einlösung loggen
  $mysqli->query(
    "INSERT INTO shop_log (kind, reward_id, sterne, timestamp)
     VALUES ('$kind', $reward_id, $kosten, '$timestamp')"
  );
}

header("Location: shop.php?kind=" . urlencode($kind));
exit;
