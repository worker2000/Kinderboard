<?php
include '../config/db.php';
require_once __DIR__ . '/../lib/discord.php';

$message = '';

// Testnachricht senden
if (isset($_POST['test_bot'])) {
  ob_start(); // damit keine Warning-Ausgabe erscheint
  sendeDiscordNachricht("Testkind", "🧪 Dies ist eine Testnachricht vom Kinderboard!");
  ob_end_clean();
  $message = "✅ Testnachricht wurde gesendet. Bitte prüfe deinen Discord-Channel.";
}
// Initiale Daten laden
$configRes = $mysqli->query("SELECT * FROM discord_config WHERE id = 1");
$config = $configRes->fetch_assoc();

// Speichern
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $bot_token = $mysqli->real_escape_string($_POST['bot_token']);
  $channel_id = $mysqli->real_escape_string($_POST['channel_id']);
  $active = isset($_POST['active']) ? 1 : 0;

  if ($config) {
    $mysqli->query("UPDATE discord_config SET bot_token='$bot_token', channel_id='$channel_id', active=$active WHERE id = 1");
  } else {
    $mysqli->query("INSERT INTO discord_config (id, bot_token, channel_id, active) VALUES (1, '$bot_token', '$channel_id', $active)");
  }

  $message = "✅ Einstellungen gespeichert!";
  $config = [
    'bot_token' => $bot_token,
    'channel_id' => $channel_id,
    'active' => $active
  ];
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Discord Bot Verwaltung</title>
  <link rel="stylesheet" href="../styles/style.css">
</head>
<body>

  <h1>🤖 Discord-Bot verwalten</h1>

  <a href="shop_admin.php" class="back-button">🔙 Zurück</a>

  <?php if ($message): ?>
    <p style="color: green; font-weight: bold;"><?php echo $message; ?></p>
  <?php endif; ?>

  <form method="POST">
    <label>Bot Token:</label><br>
    <input type="text" name="bot_token" value="<?php echo htmlspecialchars($config['bot_token'] ?? ''); ?>" style="width: 100%;"><br>

    <label>Channel-ID:</label><br>
    <input type="text" name="channel_id" value="<?php echo htmlspecialchars($config['channel_id'] ?? ''); ?>" style="width: 100%;"><br>

    <label><input type="checkbox" name="active" <?php if (($config['active'] ?? 0) == 1) echo 'checked'; ?>> Bot aktivieren</label><br><br>

    <button type="submit">💾 Speichern</button>
    <button type="submit" name="test_bot" style="background: #1976d2; color: white; margin-top: 1rem;">🧪 Test Bot</button>

  </form>

  <hr>

  <h2>📖 Anleitung: Discord-Bot erstellen und verbinden</h2>

  <ol>
    <li>
      Gehe zur Discord Developer-Seite:  
      👉 <a href="https://discord.com/developers/applications" target="_blank">https://discord.com/developers/applications</a>
    </li>
    <li>Klicke auf <strong>"New Application"</strong> und gib deinem Bot einen Namen.</li>
    <li>Wechsle zu <strong>"Bot"</strong > und klicke auf <strong>"Add Bot"</strong>.</li>
    <li>Aktiviere unter "Privileged Gateway Intents" → <strong>"Message Content Intent"</strong>.</li>
    <li>Kopiere den Token und füge ihn oben ins Feld <strong>„Bot Token“</strong> ein.</li>
    <li>
      Gehe zu <strong>"OAuth2 → URL Generator"</strong> und wähle:
      <ul>
        <li><code>bot</code></li>
        <li>Bot-Rechte: <code>Send Messages</code>, <code>Read Messages</code></li>
      </ul>
      Dann kopiere und öffne den erzeugten Link, um den Bot in deinen Server einzuladen.
    </li>
    <li>
      Gehe in Discord in den Ziel-Channel, öffne das Kontextmenü „#channel“ → <strong>"Link kopieren"</strong>  
      → Füge die Channel-ID oben ein.
    </li>
  </ol>

  <p>Wenn alles korrekt eingetragen ist, sendet dein Bot künftig automatisch eine Nachricht, sobald ein Kind eine Belohnung bestellt. 🎉</p>

</body>
</html>
