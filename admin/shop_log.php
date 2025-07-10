<?php
include '../config/db.php';

// Markiere als erledigt
if (isset($_GET['done'])) {
  $id = intval($_GET['done']);
  $mysqli->query("UPDATE shop_log SET status = 'done' WHERE id = $id");
  header("Location: shop_log.php");
  exit;
}

// Alle Einlösungen laden + Reward-Namen joinen
$res = $mysqli->query("
  SELECT s.*, r.name AS reward_name 
  FROM shop_log s 
  JOIN shop_rewards r ON s.reward_id = r.id 
  ORDER BY s.timestamp DESC
");
?>

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Shop Logfile</title>
  <link rel="stylesheet" href="../styles/style.css">
</head>
<body>

  <h1>Log – Shop-Einlösungen</h1>

  <?php if ($res->num_rows > 0): ?>
    <table>
      <thead>
        <tr>
          <th>Kind</th>
          <th>Belohnung</th>
          <th>Sterne</th>
          <th>Zeitpunkt</th>
          <th>Status</th>
          <th>Aktion</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $res->fetch_assoc()): ?>
          <tr>
            <td><?php echo htmlspecialchars($row['kind']); ?></td>
            <td><?php echo htmlspecialchars($row['reward_name']); ?></td>
            <td>⭐ <?php echo intval($row['sterne']); ?></td>
            <td><?php echo $row['timestamp']; ?></td>
            <td><?php echo $row['status']; ?></td>
            <td>
              <?php if ($row['status'] == 'open'): ?>
                <a href="shop_log.php?done=<?php echo $row['id']; ?>" class="done-btn">✅ Erledigt</a>
              <?php else: ?>
                ✔️
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p>Keine Einlösungen bisher.</p>
  <?php endif; ?>

  <a href="shop_admin.php" class="back-button">🔙 Zurück zum Shop-Admin</a>

</body>
</html>
