<?php
include '../config/db.php';
$id = intval($_POST['id']);
$mysqli->query("DELETE FROM tasks WHERE id = $id");
header("Location: admin.php");
exit();