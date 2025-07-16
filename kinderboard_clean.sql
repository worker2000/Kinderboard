-- phpMyAdmin SQL Dump
-- version 5.2.1deb1+deb12u1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Erstellungszeit: 16. Jul 2025 um 09:19
-- Server-Version: 10.11.11-MariaDB-0+deb12u1
-- PHP-Version: 8.2.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `kinderboard`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `discord_config`
--

CREATE TABLE `discord_config` (
  `id` int(11) NOT NULL,
  `bot_token` varchar(255) DEFAULT NULL,
  `channel_id` varchar(255) DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `discord_config`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `discord_log`
--

CREATE TABLE `discord_log` (
  `id` int(11) NOT NULL,
  `kind` varchar(50) DEFAULT NULL,
  `reward_name` varchar(100) DEFAULT NULL,
  `timestamp` datetime DEFAULT NULL,
  `status` enum('success','fail') DEFAULT 'success'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `discord_log`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kinder`
--

CREATE TABLE `kinder` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `alter` int(11) DEFAULT NULL,
  `geburtstag` date DEFAULT NULL,
  `bild_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `kinder`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `remarks`
--

CREATE TABLE `remarks` (
  `id` int(11) NOT NULL,
  `datum` date NOT NULL,
  `kind` varchar(100) DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `timestamp` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `shop_log`
--

CREATE TABLE `shop_log` (
  `id` int(11) NOT NULL,
  `kind` varchar(50) DEFAULT NULL,
  `reward_id` int(11) DEFAULT NULL,
  `sterne` int(11) DEFAULT NULL,
  `timestamp` datetime DEFAULT NULL,
  `status` varchar(20) DEFAULT 'open'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `shop_rewards`
--

CREATE TABLE `shop_rewards` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `kosten` int(11) DEFAULT NULL,
  `beschreibung` text DEFAULT NULL,
  `bild_url` varchar(255) DEFAULT NULL,
  `link_url` varchar(1024) DEFAULT NULL,
  `kinder` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `shop_rewards`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sterne_log`
--

CREATE TABLE `sterne_log` (
  `id` int(11) NOT NULL,
  `kind` varchar(50) DEFAULT NULL,
  `task_id` int(11) DEFAULT NULL,
  `sterne` int(11) DEFAULT NULL,
  `status` enum('valid','rejected') DEFAULT 'valid',
  `timestamp` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `sterne_log`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `kind` varchar(50) NOT NULL,
  `zeit` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `icon` varchar(255) DEFAULT '',
  `status` tinyint(1) DEFAULT 0,
  `mo` tinyint(1) DEFAULT 0,
  `di` tinyint(1) DEFAULT 0,
  `mi` tinyint(1) DEFAULT 0,
  `do` tinyint(1) DEFAULT 0,
  `fr` tinyint(1) DEFAULT 0,
  `sa` tinyint(1) DEFAULT 0,
  `so` tinyint(1) DEFAULT 0,
  `einmalig_date` date DEFAULT NULL,
  `sterne` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `tasks`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `task_logs`
--

CREATE TABLE `task_logs` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `kind` varchar(50) NOT NULL,
  `datum` date NOT NULL,
  `status` enum('done','undone') NOT NULL,
  `timestamp` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `task_logs`
--


--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `discord_config`
--
ALTER TABLE `discord_config`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `discord_log`
--
ALTER TABLE `discord_log`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `kinder`
--
ALTER TABLE `kinder`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `remarks`
--
ALTER TABLE `remarks`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `shop_log`
--
ALTER TABLE `shop_log`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `shop_rewards`
--
ALTER TABLE `shop_rewards`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `sterne_log`
--
ALTER TABLE `sterne_log`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `task_logs`
--
ALTER TABLE `task_logs`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `discord_config`
--
ALTER TABLE `discord_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `discord_log`
--
ALTER TABLE `discord_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `kinder`
--
ALTER TABLE `kinder`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT für Tabelle `remarks`
--
ALTER TABLE `remarks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `shop_log`
--
ALTER TABLE `shop_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT für Tabelle `shop_rewards`
--
ALTER TABLE `shop_rewards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT für Tabelle `sterne_log`
--
ALTER TABLE `sterne_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=132;

--
-- AUTO_INCREMENT für Tabelle `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT für Tabelle `task_logs`
--
ALTER TABLE `task_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=173;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
