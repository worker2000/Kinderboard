# 🎨 Kinderboard – Das digitale Belohnungssystem für Kinder

**Kinderboard** ist ein webbasierter Aufgaben- und Belohnungsplan für Familien und pädagogische Einrichtungen. Kinder können Aufgaben erledigen, Sterne sammeln und diese später gegen Belohnungen eintauschen. Optional ist eine Discord-Integration möglich, bei der Bestellungen automatisch im Discord gemeldet werden.

---

## ✨ Funktionen

- ✅ Aufgaben für Kinder definieren (mit Zeitbereichen: Vormittag, Nachmittag, Abend)
- ⭐ Sterne für erledigte Aufgaben automatisch vergeben
- 🛒 Shop mit Belohnungen – Kinder können selbst „einkaufen“
- 📊 Übersichtliche Statistiken mit Balkendiagrammen & Tooltips
- 🎮 **Discord-Integration** bei Belohnungskäufen (inkl. Test-Button & Logging)
- 👶 Optimiert für Tablets & kleine Kinderhände
- 👮 Adminbereich zur Verwaltung von Aufgaben, Kindern, Shop & Statistiken

---

## 🖼️ Screenshots

*(optional – hier könntest du später Bilder einfügen)*

---

## 🚀 Installation

### 1. Voraussetzungen

- PHP 8.1+
- MySQL/MariaDB
- Apache oder Nginx
- Git (für Updates via GitHub)

### 2. Repository klonen

```bash
git clone https://github.com/worker2000/Kinderboard.git
cd Kinderboard

3. Datenbank vorbereiten
Importiere das mitgelieferte SQL-Setup (falls vorhanden) oder erstelle folgende Tabellen:

kinder

tasks

task_logs

sterne_log

shop_rewards

shop_log

discord_log

settings (für Discord-Config)

4. Konfiguration
Erstelle die Datei config/db.php mit folgendem Inhalt:

<?php
$mysqli = new mysqli("localhost", "benutzer", "passwort", "datenbank");
if ($mysqli->connect_error) {
  die("Verbindung fehlgeschlagen: " . $mysqli->connect_error);
}
?>
5. Discord optional einrichten
Erstelle einen Bot unter: https://discord.com/developers/applications

Gib Token + Channel-ID unter admin/discord_admin.php ein

Testfunktion vorhanden

🛠️ Projektstruktur
/kinderboard/
├── board.php              → Hauptseite fürs Kind
├── statistik.php          → Kindgerechte Sterneauswertung
├── admin/                 → Adminbereich (Tasks, Shop, Discord)
├── includes/              → Hilfsfunktionen (z. B. Discord-API)
├── config/                → Datenbankverbindung etc.
├── styles/                → CSS-Dateien
└── pics/                  → Kinderbilder

🔒 Sicherheit
Admin-Ansichten über admin/ erreichbar (noch ohne Authentifizierung – bei Bedarf ergänzen)

📜 Lizenz
MIT © 2025 worker2000
