<?php
include '../config/db.php';

$id = intval($_GET['id']);
$datum = $_GET['datum'];

$mysqli->query("UPDATE sterne_log SET status = 'rejected' WHERE id = $id");

header("Location: log_view.php?datum=$datum");
exit;
