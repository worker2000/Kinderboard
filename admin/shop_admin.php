<?php
include '../config/db.php';

// Neues Reward speichern
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_reward'])) {
  $name = $mysqli->real_escape_string($_POST['name']);
  $kosten = intval($_POST['kosten']);
  $beschreibung = $mysqli->real_escape_string($_POST['beschreibung']);
  $bild_url = $mysqli->real_escape_string($_POST['bild_url']);
  $link_url = $mysqli->real_escape_string($_POST['link_url']);
  $kinder = implode(',', $_POST['kinder']); // Mehrfachauswahl

  $mysqli->query("
    INSERT INTO shop_rewards 
    (name, kosten, beschreibung, bild_url, link_url, kinder)
    VALUES ('$name', $kosten, '$beschreibung', '$bild_url', '$link_url', '$kinder')
  ");

  header("Location: shop_admin.php");
  exit;
}

// Reward löschen
if (isset($_GET['delete'])) {
  $id = intval($_GET['delete']);
  $mysqli->query("DELETE FROM shop_rewards WHERE id = $id");
  header("Location: shop_admin.php");
  exit;
}

// Rewards laden
$rewards = $mysqli->query("SELECT * FROM shop_rewards ORDER BY kosten ASC");

// 1️⃣ Alle Kinder holen f:
$kinderRes1 = $mysqli->query("SELECT name FROM kinder ORDER BY name");

?>

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Admin – Belohnungs-Shop</title>
  <link rel="stylesheet" href="../styles/style.css">
</head>
<body>

  <h1>Admin – Belohnungs-Shop</h1>

  <h2>Neue Belohnung hinzufügen</h2>
  <form method="POST" action="shop_admin.php">
    <label>Name:</label><br>
    <input type="text" name="name" required><br>

    <label>Kosten (Sterne):</label><br>
    <input type="number" name="kosten" min="1" required><br>

 <label>Kind(er):</label>
  <select name="kinder[]" multiple required>
    <?php while ($row = $kinderRes1->fetch_assoc()): ?>
      <option value="<?php echo htmlspecialchars($row['name']); ?>">
        <?php echo htmlspecialchars($row['name']); ?>
      </option>
    <?php endwhile; ?>
    </select><br>

    <label>Beschreibung:</label><br>
    <textarea name="beschreibung" rows="3"></textarea><br>

    <label>Bild URL:</label><br>
    <input type="text" name="bild_url"><br>

    <label>Externer Link:</label><br>
    <input type="text" name="link_url"><br><br>

    <button type="submit" name="add_reward">➕ Speichern</button>
  </form>

  <h2>Alle Belohnungen</h2>
  <div class="shop-list">
    <?php
    while ($row = $rewards->fetch_assoc()) {
      echo "
      <div class='reward-item'>
        <strong>{$row['name']}</strong><br>
        Kosten: ⭐ {$row['kosten']}<br>
        Für: {$row['kinder']}<br>
        <a href='shop_admin.php?delete={$row['id']}' class='delete-btn'>🗑️ Löschen</a>
      </div>
      ";
    }
    ?>
  </div>

</body>
</html>
