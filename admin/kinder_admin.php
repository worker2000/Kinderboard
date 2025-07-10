<?php
include '../config/db.php';

// 🟢 Speichern
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
  $name = $mysqli->real_escape_string($_POST['name']);
  $alter = intval($_POST['alter']);
  $geburtstag = $mysqli->real_escape_string($_POST['geburtstag']);

  $bild_url = '';
  if (!empty($_FILES['bild']['name'])) {
    $ziel = '../pics/' . time() . '_' . basename($_FILES['bild']['name']);
    if (move_uploaded_file($_FILES['bild']['tmp_name'], $ziel)) {
      $bild_url = 'pics/' . basename($ziel);
    }
  }

  $sql = "INSERT INTO kinder (name, `alter`, geburtstag, bild_url)
          VALUES ('$name', $alter, '$geburtstag', '$bild_url')";
  $mysqli->query($sql);
  header("Location: kinder_admin.php");
  exit;
}

// 🗑️ Löschen
if (isset($_GET['delete'])) {
  $id = intval($_GET['delete']);
  $mysqli->query("DELETE FROM kinder WHERE id = $id");
  header("Location: kinder_admin.php");
  exit;
}

// Liste
$res = $mysqli->query("SELECT * FROM kinder ORDER BY name");
?>

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Kinder verwalten</title>
  <link rel="stylesheet" href="../styles/style.css">
</head>
<body>

  <h1>👨‍👩‍👧‍👦 Kinder verwalten</h1>

  <form method="POST" enctype="multipart/form-data">
    <label>Name:</label>
    <input name="name" required>

    <label>Alter:</label>
    <input name="alter" type="number" required>

    <label>Geburtsdatum:</label>
    <input name="geburtstag" type="date" required>

    <label>Bild hochladen:</label>
    <input type="file" name="bild">

    <button type="submit" name="save">➕ Speichern</button>
  </form>

  <h2>Alle Kinder</h2>
  <ul>
    <?php while ($row = $res->fetch_assoc()): ?>
      <li>
        <?php echo htmlspecialchars($row['name']); ?> (<?php echo $row['alter']; ?> Jahre)
        – Geburtstag: <?php echo $row['geburtstag']; ?>
        <?php if ($row['bild_url']): ?>
          <img src="../<?php echo htmlspecialchars($row['bild_url']); ?>" height="50">
        <?php endif; ?>
        <a href="?delete=<?php echo $row['id']; ?>" class="delete-btn">🗑️ Löschen</a>
      </li>
    <?php endwhile; ?>
  </ul>

</body>
</html>
