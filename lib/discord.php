<?php
function sendeDiscordNachricht($kind, $belohnung) {
  include __DIR__ . '/../config/db.php';
  $timestamp = date('Y-m-d H:i:s');

  // Konfiguration aus DB laden
  $configRes = $mysqli->query("SELECT * FROM discord_config WHERE id = 1 AND active = 1");
  if (!$configRes || $configRes->num_rows === 0) {
    return; // Kein aktiver Bot konfiguriert
  }

  $config = $configRes->fetch_assoc();
  $botToken = $config['bot_token'];
  $channelId = $config['channel_id'];

  // Nachricht aufbauen
  $message = [
    'content' => "🎉 **$kind** hat sich die Belohnung **$belohnung** gewünscht! 🛍️"
  ];

  // API-Aufruf
  $ch = curl_init("https://discord.com/api/v10/channels/{$channelId}/messages");
  curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bot {$botToken}",
    "Content-Type: application/json"
  ]);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

  $response = curl_exec($ch);
  $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);

  // Erfolg prüfen
  $status = ($http_code >= 200 && $http_code < 300) ? 'success' : 'fail';

  // Logging
  $stmt = $mysqli->prepare("
    INSERT INTO discord_log (kind, reward_name, timestamp, status)
    VALUES (?, ?, ?, ?)
  ");
  $stmt->bind_param("ssss", $kind, $belohnung, $timestamp, $status);
  $stmt->execute();
}
