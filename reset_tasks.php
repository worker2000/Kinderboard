<?php
include 'config/db.php';

$heute = strtolower(substr(date('D'),0,2)); // Mo, Tu, We, Th, Fr, Sa, Su

// Wiederholer zurücksetzen
$mysqli->query("
  UPDATE tasks SET status = 0
  WHERE
    (mo=1 AND '$heute'='mo') OR
    (di=1 AND '$heute'='tu') OR
    (mi=1 AND '$heute'='we') OR
    (do=1 AND '$heute'='th') OR
    (fr=1 AND '$heute'='fr') OR
    (sa=1 AND '$heute'='sa') OR
    (so=1 AND '$heute'='su')
");

// Einmalige nur heute zurücksetzen
$mysqli->query("
  UPDATE tasks SET status = 0 WHERE einmalig_date = CURDATE()
");

// Optional: Einmalige von gestern löschen (optional!)
$mysqli->query("DELETE FROM tasks WHERE einmalig_date < CURDATE()");

// Geburtstags-Sterne gutschreiben
$heute = date('m-d');
$res = $mysqli->query("SELECT * FROM kinder WHERE DATE_FORMAT(geburtstag, '%m-%d') = '$heute'");

while ($row = $res->fetch_assoc()) {
  $kind = $row['name'];
  $timestamp = date('Y-m-d H:i:s');

  $mysqli->query("INSERT INTO sterne_log (kind, task_id, sterne, status, timestamp)
                  VALUES ('$kind', 0, 10, 'valid', '$timestamp')");
}