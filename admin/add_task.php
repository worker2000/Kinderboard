<?php
include '../config/db.php';

// Kinder (können mehrere sein)
$kinder = $_POST['kinder'];
$zeit = $_POST['zeit'];
$name = $mysqli->real_escape_string($_POST['name']);
$sterne = isset($_POST['sterne']) ? intval($_POST['sterne']) : 0;

// Wochentage
$mo = isset($_POST['mo']) ? 1 : 0;
$di = isset($_POST['di']) ? 1 : 0;
$mi = isset($_POST['mi']) ? 1 : 0;
$do = isset($_POST['do']) ? 1 : 0;
$fr = isset($_POST['fr']) ? 1 : 0;
$sa = isset($_POST['sa']) ? 1 : 0;
$so = isset($_POST['so']) ? 1 : 0;

// Einmaliges Datum
$einmalig_date = !empty($_POST['einmalig_date']) ? $_POST['einmalig_date'] : null;

foreach ($kinder as $kind) {
  $kind = $mysqli->real_escape_string($kind);
  $query = "
  INSERT INTO tasks (kind, zeit, name, mo, di, mi, do, fr, sa, so, einmalig_date, sterne)
  VALUES ('$kind', '$zeit', '$name', $mo, $di, $mi, $do, $fr, $sa, $so, " . ($einmalig_date ? "'$einmalig_date'" : "NULL") . ", $sterne)
";
  $mysqli->query($query);
}

header("Location: admin.php");
exit();
