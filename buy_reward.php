<?php
include 'config/db.php';
require_once 'lib/discord.php'; // <== Discord-Funktion einbinden

// Eingabedaten validieren
$kind = $mysqli->real_escape_string($_POST['kind']);
$reward_id = intval($_POST['reward_id']);
$kosten = intval($_POST['kosten']);
$timestamp = date('Y-m-d H:i:s');

// Aktuelle Sterne prüfen
$res = $mysqli->query("SELECT SUM(sterne) AS summe FROM sterne_log WHERE kind='$kind' AND status='valid'");
$row = $res->fetch_assoc();
$gesamt_sterne = $row['summe'] ? intval($row['summe']) : 0;

if ($gesamt_sterne < $kosten) {
  // Nicht genug Sterne – zurückleiten
  header("Location: shop.php?kind=" . urlencode($kind));
  exit;
}

// Belohnungsdaten holen (Name für Log und Discord)
$res = $mysqli->query("SELECT name FROM shop_rewards WHERE id = $reward_id");
if (!$res || $res->num_rows === 0) {
  die("Belohnung nicht gefunden.");
}
$row = $res->fetch_assoc();
$reward_name = $mysqli->real_escape_string($row['name']);

// 1) Belohnung buchen
$mysqli->query("
  INSERT INTO shop_log (kind, reward_id, status, timestamp)
  VALUES ('$kind', $reward_id, 'open', '$timestamp')
");

// 2) Sterne abbuchen
$mysqli->query("
  INSERT INTO sterne_log (kind, sterne, status, task_id, timestamp)
  VALUES ('$kind', -$kosten, 'valid', NULL, '$timestamp')
");

// 3) Discord-Nachricht senden
sendeDiscordNachricht($kind, $reward_name);

// 4) Zurück zum Shop
header("Location: shop.php?kind=" . urlencode($kind));
exit;
