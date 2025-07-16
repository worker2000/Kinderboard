<?php
include 'config/db.php';

// Alle Kinder aus der Datenbank holen
$res = $mysqli->query("SELECT * FROM kinder ORDER BY name");
?>

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Wer bist du?</title>
  <link rel="stylesheet" href="styles/style.css">
  <style>
    .live-clock {
      font-size: 2rem;
      font-weight: bold;
      margin-top: 3rem;
      text-align: center;
    }
    .day-name {
      font-weight: bold;
      display: block;
      margin-bottom: 0.5rem;
    }

    .who-are-you {
      display: flex;
      gap: 2rem;
      justify-content: center;
      flex-wrap: wrap;
      margin-top: 2rem;
    }

    .who-button {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      width: 150px;
      height: 150px;
      background: #1976d2;
      color: #fff;
      border-radius: 50%;
      text-decoration: none;
      font-size: 1.1rem;
      font-weight: bold;
      box-shadow: 0 4px 10px #aaa;
      overflow: hidden;
      position: relative;
    }

    .who-button img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 50%;
    }

    .who-name {
      text-align: center;
      margin-top: 0.5rem;
      font-weight: bold;
      color: #333;
    }
  </style>
</head>
<body>

  <h1>Wer bist du?</h1>

  <div class="who-are-you">
    <?php while ($row = $res->fetch_assoc()): ?>
      <div style="text-align: center;">
        <a href="board.php?kind=<?php echo urlencode($row['name']); ?>" class="who-button">
          <?php if ($row['bild_url']): ?>
            <img src="<?php echo htmlspecialchars($row['bild_url']); ?>" alt="Bild von <?php echo htmlspecialchars($row['name']); ?>">
          <?php endif; ?>
        </a>
        <div class="who-name"><?php echo htmlspecialchars($row['name']); ?></div>
      </div>
    <?php endwhile; ?>
  </div>
  <div style="position: absolute; top: 20px; right: 30px;">
  <a href="statistik.php" class="log-button">📊 Zur Statistik</a>
</div>

  <!-- Live Clock + Bunter Wochentag -->
  <div class="live-clock">
    <span id="day-name" class="day-name">--</span>
    Uhrzeit: <span id="clock-time">--:--:--</span>
  </div>

  <script>
    const dayColors = {
      0: '#e53935', // Sonntag - Rot
      1: '#1976d2', // Montag - Blau
      2: '#43a047', // Dienstag - Grün
      3: '#fbc02d', // Mittwoch - Gelb
      4: '#8e24aa', // Donnerstag - Lila
      5: '#fb8c00', // Freitag - Orange
      6: '#3949ab'  // Samstag - Dunkelblau
    };

    const dayNames = [
      'Sonntag',
      'Montag',
      'Dienstag',
      'Mittwoch',
      'Donnerstag',
      'Freitag',
      'Samstag'
    ];

    function updateClock() {
      const now = new Date();
      const h = String(now.getHours()).padStart(2, '0');
      const m = String(now.getMinutes()).padStart(2, '0');
      const s = String(now.getSeconds()).padStart(2, '0');
      const day = now.getDay();

      document.getElementById('clock-time').textContent = `${h}:${m}:${s}`;
      const dayElem = document.getElementById('day-name');
      dayElem.textContent = `Heute ist ${dayNames[day]}`;
      dayElem.style.color = dayColors[day];
    }

    updateClock();
    setInterval(updateClock, 1000);
  </script>

</body>
</html>
