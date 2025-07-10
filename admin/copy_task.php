<?php
include '../config/db.php';

$id = intval($_POST['id']);
$kind = $mysqli->real_escape_string($_POST['kind']);

// Original holen:
$res = $mysqli->query("SELECT * FROM tasks WHERE id = $id LIMIT 1");
$row = $res->fetch_assoc();

// Duplikat anlegen:
$mysqli->query(
  "INSERT INTO tasks (kind, zeit, name, mo, di, mi, do, fr, sa, so)
   VALUES (
    '$kind',
    '{$row['zeit']}',
    '{$row['name']}',
    {$row['mo']}, {$row['di']}, {$row['mi']}, {$row['do']},
    {$row['fr']}, {$row['sa']}, {$row['so']}
  )"
);

header("Location: admin.php");
exit();
