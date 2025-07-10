<?php
include 'config/db.php';

$id = intval($_POST['id']);
$kind = $_POST['kind'];

// 1) Aktuellen Status + Sterne holen
$result = $mysqli->query("SELECT status, sterne FROM tasks WHERE id = $id LIMIT 1");
$row = $result->fetch_assoc();

$alter_status = $row['status'];
$sterne = intval($row['sterne']);

// 2) Neuen Status bestimmen
$neuer_status = $alter_status ? 0 : 1;

// 3) Status umdrehen
$mysqli->query("UPDATE tasks SET status = $neuer_status WHERE id = $id");

// 4) Task-Log speichern
$datum = date('Y-m-d');
$timestamp = date('Y-m-d H:i:s');
$status_txt = $neuer_status ? 'done' : 'undone';

$mysqli->query(
  "INSERT INTO task_logs (task_id, kind, datum, status, timestamp)
   VALUES ($id, '$kind', '$datum', '$status_txt', '$timestamp')"
);

// 5) Sternelogik: hin oder zurück
if ($sterne > 0) {

  if ($neuer_status) {
    // Von offen → erledigt: prüfen, ob schon gutgeschrieben
    $check = $mysqli->query(
      "SELECT id FROM sterne_log 
       WHERE kind='$kind' AND task_id=$id AND DATE(timestamp)=CURDATE() AND status='valid'"
    );

    if ($check->num_rows == 0) {
      $mysqli->query(
        "INSERT INTO sterne_log (kind, task_id, sterne, status, timestamp)
         VALUES ('$kind', $id, $sterne, 'valid', '$timestamp')"
      );
    }

  } else {
    // Von erledigt → zurück: heute gültigen Eintrag auf rejected setzen
    $mysqli->query(
      "UPDATE sterne_log 
       SET status='rejected' 
       WHERE kind='$kind' AND task_id=$id AND DATE(timestamp)=CURDATE() AND status='valid'"
    );
  }
}

header("Location: board.php?kind=" . urlencode($kind));
exit();
