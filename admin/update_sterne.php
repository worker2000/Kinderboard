<?php
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = intval($_POST['id']);
  $sterne = intval($_POST['sterne']);

  $stmt = $mysqli->prepare("UPDATE tasks SET sterne = ? WHERE id = ?");
  $stmt->bind_param("ii", $sterne, $id);
  $stmt->execute();
  $stmt->close();
}

header("Location: admin.php");
exit;
