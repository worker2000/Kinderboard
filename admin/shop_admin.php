<?php
include '../config/db.php';

// ✅ Neue Belohnung speichern
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_reward'])) {
  $name = $mysqli->real_escape_string($_POST['name']);
  $kosten = intval($_POST['kosten']);
  $beschreibung = $mysqli->real_escape_string($_POST['beschreibung']);
  $bild_url = $mysqli->real_escape_string($_POST['bild_url']);
  $link_url = $mysqli->real_escape_string($_POST['link_url']);
  $kinder = implode(',', $_POST['kinder']);

  $mysqli->query("
    INSERT INTO shop_rewards 
    (name, kosten, beschreibung, bild_url, link_url, kinder)
    VALUES ('$name', $kosten, '$beschreibung', '$bild_url', '$link_url', '$kinder')
  ");

  header("Location: shop_admin.php");
  exit;
}

// ❌ Belohnung löschen
if (isset($_GET['delete'])) {
  $id = intval($_GET['delete']);
  $mysqli->query("DELETE FROM shop_rewards WHERE id = $id");
  header("Location: shop_admin.php");
  exit;
}

// ✅ Bestellung als erledigt markieren
if (isset($_GET['done'])) {
  $done_id = intval($_GET['done']);
  $mysqli->query("UPDATE shop_log SET status='done' WHERE id = $done_id");
  header("Location: shop_admin.php");
  exit;
}

// 📦 Alle Belohnungen laden
$rewards = $mysqli->query("SELECT * FROM shop_rewards ORDER BY kosten ASC");

// 👧 Alle Kinder laden
$kinderRes1 = $mysqli->query("SELECT name FROM kinder ORDER BY name");

// 🕓 Offene Bestellungen
$offeneBestellungen = $mysqli->query("
  SELECT sl.id, sl.kind, sl.timestamp, sr.name AS reward_name
  FROM shop_log sl
  JOIN shop_rewards sr ON sr.id = sl.reward_id
  WHERE sl.status = 'open'
  ORDER BY sl.timestamp DESC
");

// ❗ Fehlgeschlagene Discord-Nachrichten
$discordFails = $mysqli->query("
  SELECT * FROM discord_log WHERE status = 'fail' ORDER BY timestamp DESC
");
?>

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Admin – Belohnungs-Shop</title>
  <link rel="stylesheet" href="../styles/style.css">
</head>
<body>

  <h1>🛒 Admin – Belohnungs-Shop</h1>

  <h2>➕ Neue Belohnung hinzufügen</h2>
  <form method="POST" action="shop_admin.php">
    <label>Name:</label><br>
    <input type="text" name="name" required><br>

    <label>Kosten (Sterne):</label><br>
    <input type="number" name="kosten" min="1" required><br>

    <label>Kind(er):</label><br>
    <select name="kinder[]" multiple required>
      <?php while ($row = $kinderRes1->fetch_assoc()): ?>
        <option value="<?php echo htmlspecialchars($row['name']); ?>">
          <?php echo htmlspecialchars($row['name']); ?>
        </option>
      <?php endwhile; ?>
    </select><br>

    <label>Beschreibung:</label><br>
    <textarea name="beschreibung" rows="3"></textarea><br>

    <label>Bild-URL:</label><br>
    <input type="text" name="bild_url"><br>

    <label>Externer Link:</label><br>
    <input type="text" name="link_url"><br><br>

    <button type="submit" name="add_reward">💾 Speichern</button>
  </form>

  <h2>🎁 Alle Belohnungen</h2>
  <div class="shop-list">
    <?php while ($row = $rewards->fetch_assoc()): ?>
      <div class="reward-item">
        <strong><?php echo htmlspecialchars($row['name']); ?></strong><br>
        Kosten: ⭐ <?php echo $row['kosten']; ?><br>
        Für: <?php echo htmlspecialchars($row['kinder']); ?><br>
        <a href="shop_admin.php?delete=<?php echo $row['id']; ?>" class="delete-btn">🗑️ Löschen</a>
      </div>
    <?php endwhile; ?>
  </div>

  <h2>🕓 Offene Bestellungen</h2>
  <?php if ($offeneBestellungen->num_rows === 0): ?>
    <p>Keine offenen Bestellungen.</p>
  <?php else: ?>
    <table>
      <tr>
        <th>#</th>
        <th>Kind</th>
        <th>Belohnung</th>
        <th>Datum</th>
        <th>Aktion</th>
      </tr>
      <?php while ($row = $offeneBestellungen->fetch_assoc()): ?>
        <tr>
          <td><?php echo $row['id']; ?></td>
          <td><?php echo htmlspecialchars($row['kind']); ?></td>
          <td><?php echo htmlspecialchars($row['reward_name']); ?></td>
          <td><?php echo $row['timestamp']; ?></td>
          <td><a href="shop_admin.php?done=<?php echo $row['id']; ?>" class="done-btn">✅ Erledigt</a></td>
        </tr>
      <?php endwhile; ?>
    </table>
  <?php endif; ?>

  <h2>❌ Fehlgeschlagene Discord-Benachrichtigungen</h2>
  <?php if ($discordFails->num_rows === 0): ?>
    <p>Keine Fehler 🙂</p>
  <?php else: ?>
    <table>
      <tr>
        <th>Kind</th>
        <th>Belohnung</th>
        <th>Zeitpunkt</th>
        <th>Status</th>
      </tr>
      <?php while ($row = $discordFails->fetch_assoc()): ?>
        <tr>
          <td><?php echo htmlspecialchars($row['kind']); ?></td>
          <td><?php echo htmlspecialchars($row['reward_name']); ?></td>
          <td><?php echo $row['timestamp']; ?></td>
          <td style="color:red;"><?php echo $row['status']; ?></td>
        </tr>
      <?php endwhile; ?>
    </table>
  <?php endif; ?>

</body>
</html>
