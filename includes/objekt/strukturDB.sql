-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 07. Jan 2019 um 07:34
-- Server-Version: 10.1.37-MariaDB
-- PHP-Version: 7.3.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `62_flathacksql1`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `account_infos`
--

CREATE TABLE `account_infos` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `besitzer` int(11) NOT NULL,
  `attribut` varchar(250) COLLATE utf8_german2_ci NOT NULL,
  `wert` varchar(250) COLLATE utf8_german2_ci NOT NULL,
  `account` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `adressbuch`
--

CREATE TABLE `adressbuch` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `vorname` varchar(30) COLLATE utf8_german2_ci NOT NULL,
  `nachname` varchar(30) COLLATE utf8_german2_ci NOT NULL,
  `strasse` varchar(40) COLLATE utf8_german2_ci NOT NULL,
  `hausnummer` varchar(15) COLLATE utf8_german2_ci NOT NULL,
  `postleitzahl` int(5) NOT NULL,
  `bundesland` varchar(30) COLLATE utf8_german2_ci NOT NULL,
  `land` varchar(50) COLLATE utf8_german2_ci NOT NULL,
  `telefon1` varchar(50) COLLATE utf8_german2_ci NOT NULL,
  `telefon2` varchar(50) COLLATE utf8_german2_ci NOT NULL,
  `telefon3` varchar(50) COLLATE utf8_german2_ci NOT NULL,
  `telefon4` varchar(50) COLLATE utf8_german2_ci NOT NULL,
  `telefon1art` varchar(50) COLLATE utf8_german2_ci NOT NULL,
  `telefon2art` varchar(50) COLLATE utf8_german2_ci NOT NULL,
  `telefon3art` varchar(50) COLLATE utf8_german2_ci NOT NULL,
  `telefon4art` varchar(50) COLLATE utf8_german2_ci NOT NULL,
  `skype` varchar(50) COLLATE utf8_german2_ci NOT NULL,
  `facebook` varchar(100) COLLATE utf8_german2_ci NOT NULL,
  `notizen` text COLLATE utf8_german2_ci NOT NULL,
  `fax` varchar(50) COLLATE utf8_german2_ci NOT NULL,
  `gruppe` varchar(50) COLLATE utf8_german2_ci NOT NULL,
  `email` varchar(50) COLLATE utf8_german2_ci NOT NULL,
  `geburtstag` date NOT NULL,
  `stadt` varchar(50) COLLATE utf8_german2_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `benutzer`
--

CREATE TABLE `benutzer` (
  `id` int(10) NOT NULL,
  `Name` varchar(20) COLLATE utf8_german2_ci NOT NULL,
  `Passwort` char(128) COLLATE utf8_german2_ci NOT NULL COMMENT 'als MD5 Hash',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `rights` bigint(20) NOT NULL COMMENT 'Rechte des Benutzers',
  `forumRights` bigint(20) NOT NULL COMMENT 'Rechte zum Anzeigen von Inhalten im Forum',
  `versuche` int(3) DEFAULT NULL COMMENT 'Versuche für den Login',
  `realName` varchar(50) COLLATE utf8_german2_ci NOT NULL COMMENT 'Echter Name des Benutzers',
  `titel` varchar(250) COLLATE utf8_german2_ci NOT NULL COMMENT 'Titel des Benutzers'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

--
-- Daten für Tabelle `benutzer`
--

INSERT INTO `benutzer` (`id`, `Name`, `Passwort`, `timestamp`, `rights`, `forumRights`, `versuche`, `realName`, `titel`) VALUES
(1, 'administrator', 'fa818419cd8c85cecba515a276ae6977', '2014-03-08 07:29:51', 1, 1, 0, '', 'Administrator');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `blogkategorien`
--

CREATE TABLE `blogkategorien` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `kategorie` varchar(254) COLLATE utf8_german2_ci NOT NULL,
  `beschreibung` varchar(300) COLLATE utf8_german2_ci NOT NULL COMMENT 'Beschreibung der Kategorien',
  `rightPotenz` int(11) NOT NULL COMMENT 'Potenz zur Berechnung des Wertes, Potenz von 2',
  `rightWert` bigint(20) NOT NULL COMMENT 'Wert der Potenz, wenn sie ausgerechnet ist.',
  `sortierung` int(11) DEFAULT NULL COMMENT 'Sortierung zur Anzeige im Forum'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `blogtexte`
--

CREATE TABLE `blogtexte` (
  `id` int(11) NOT NULL COMMENT 'Primary Key',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Erstelldatum Blog',
  `kategorie` int(11) NOT NULL,
  `autor` int(11) NOT NULL COMMENT 'Autor Blog',
  `titel` varchar(255) CHARACTER SET utf8 NOT NULL,
  `text` text CHARACTER SET utf8 NOT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `locked` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `blog_kommentare`
--

CREATE TABLE `blog_kommentare` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `autor` int(11) NOT NULL,
  `text` text COLLATE utf8_german2_ci NOT NULL,
  `blogid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `docu`
--

CREATE TABLE `docu` (
  `id` int(11) NOT NULL COMMENT 'Primary Key',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Erstellungsdatum des Eintrags',
  `text` text COLLATE utf8_german2_ci NOT NULL COMMENT 'Text des Eintrags',
  `autor` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci COMMENT='hilfe.php';

--
-- Daten für Tabelle `docu`
--

INSERT INTO `docu` (`id`, `timestamp`, `text`, `autor`) VALUES
(1, '2019-01-04 08:38:50', '<p>Willkommen Das ist eine TestAnk&uuml;ndigung</p>\r\n', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `eventadministrators`
--

CREATE TABLE `eventadministrators` (
  `eventid` int(11) NOT NULL,
  `userid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `eventcodeusage`
--

CREATE TABLE `eventcodeusage` (
  `codeid` int(11) NOT NULL,
  `codeusage` int(11) NOT NULL COMMENT 'Anzahl der Nutzungen',
  `userid` int(11) NOT NULL COMMENT 'Primary key aus eventguests'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `eventguests`
--

CREATE TABLE `eventguests` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `guestname` varchar(250) COLLATE utf8_german2_ci NOT NULL,
  `guestmailaddress` varchar(100) COLLATE utf8_german2_ci NOT NULL,
  `eventid` int(11) NOT NULL,
  `loggedin` int(11) DEFAULT NULL,
  `zusage` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `eventinvitecodes`
--

CREATE TABLE `eventinvitecodes` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `eventid` int(11) NOT NULL,
  `eventinvitecode` varchar(100) COLLATE utf8_german2_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `eventlist`
--

CREATE TABLE `eventlist` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `eventname` varchar(250) COLLATE utf8_german2_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `eventtexts`
--

CREATE TABLE `eventtexts` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `eventid` int(11) NOT NULL,
  `text` text COLLATE utf8_german2_ci NOT NULL,
  `textid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `finanzen_jahresabschluss`
--

CREATE TABLE `finanzen_jahresabschluss` (
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `besitzer` int(11) NOT NULL,
  `jahr` int(11) NOT NULL,
  `wert` decimal(10,2) NOT NULL,
  `konto` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `finanzen_konten`
--

CREATE TABLE `finanzen_konten` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `konto` varchar(250) COLLATE utf8_german2_ci NOT NULL,
  `besitzer` int(11) NOT NULL,
  `aktiv` tinyint(4) NOT NULL,
  `notizen` text COLLATE utf8_german2_ci NOT NULL,
  `art` int(11) NOT NULL,
  `mail` varchar(200) COLLATE utf8_german2_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `finanzen_monatsabschluss`
--

CREATE TABLE `finanzen_monatsabschluss` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `besitzer` int(11) NOT NULL,
  `monat` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `wert` decimal(10,2) NOT NULL,
  `konto` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `finanzen_shares`
--

CREATE TABLE `finanzen_shares` (
  `besitzer` int(11) NOT NULL COMMENT 'Share_Holder',
  `konto_id` int(11) NOT NULL COMMENT 'Fremdschlüssel KontoID',
  `target_user` int(11) NOT NULL COMMENT 'User, der dass Konto sehen darf.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `finanzen_umsaetze`
--

CREATE TABLE `finanzen_umsaetze` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `buchungsnr` int(11) NOT NULL,
  `besitzer` int(11) NOT NULL,
  `konto` int(11) NOT NULL,
  `gegenkonto` int(11) NOT NULL,
  `umsatzName` varchar(250) COLLATE utf8_german2_ci NOT NULL,
  `umsatzWert` decimal(10,2) NOT NULL,
  `datum` date NOT NULL,
  `link` varchar(500) COLLATE utf8_german2_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `gwcosts`
--

CREATE TABLE `gwcosts` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `besitzer` int(11) NOT NULL,
  `text` varchar(250) COLLATE utf8_german2_ci NOT NULL,
  `wert` decimal(10,2) NOT NULL,
  `kaufdat` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `gwmatlist`
--

CREATE TABLE `gwmatlist` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `matID` int(11) NOT NULL,
  `matName` varchar(254) COLLATE utf8_german2_ci NOT NULL,
  `matPrice` decimal(10,0) DEFAULT NULL,
  `kategorie` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

--
-- Daten für Tabelle `gwmatlist`
--

INSERT INTO `gwmatlist` (`id`, `timestamp`, `matID`, `matName`, `matPrice`, `kategorie`) VALUES
(1, '2015-03-03 05:27:25', 1, 'Zinnbrocken', '0', 1),
(2, '2015-03-03 05:27:25', 2, 'Bronzebarren', '0', 1),
(3, '2015-03-03 05:27:25', 3, 'Kupfererz', '0', 1),
(4, '2015-03-03 05:27:25', 6, 'Spule Jutefaden', '0', 1),
(5, '2015-03-03 05:27:25', 11, 'Eisenerz', '0', 1),
(6, '2015-03-03 05:27:25', 16, 'Spule Wollfaden', '0', 1),
(7, '2015-03-03 05:27:25', 21, 'Holzkohlebrocken', '0', 1),
(8, '2015-03-03 05:27:25', 23, 'Silbererz', '0', 1),
(9, '2015-03-03 05:27:25', 26, 'Spule Baumwollfaden', '0', 1),
(10, '2015-03-03 05:27:25', 31, 'Platinerz', '0', 1),
(11, '2015-03-03 05:27:25', 36, 'Spule Leinenfaden', '0', 1),
(12, '2015-03-03 05:27:25', 43, 'Golderz', '0', 1),
(13, '2015-03-03 05:27:25', 46, 'Spule Seidenfaden', '0', 1),
(14, '2015-03-03 05:27:25', 51, 'Mithrilerz', '0', 1),
(15, '2015-03-03 05:27:25', 52, 'Mithrilbarren', '0', 1),
(16, '2015-03-03 05:27:25', 53, 'Goldbarren', '0', 1),
(17, '2015-03-03 05:27:25', 55, 'Gazeballen', '0', 1),
(18, '2015-03-03 05:27:25', 56, 'Spule Gazefaden', '0', 1),
(19, '2015-03-03 05:27:25', 61, 'Orichalcumerz', '0', 1),
(20, '2015-03-03 05:27:25', 62, 'Orichalcumbarren', '0', 1),
(21, '2015-03-03 05:30:22', 4, 'Juterest', '0', 1),
(22, '2015-03-03 05:30:22', 5, 'Juteballen', '0', 1),
(23, '2015-03-03 05:30:22', 9, 'Grüner Holzblock', '0', 1),
(24, '2015-03-03 05:30:22', 10, 'Grüne Holzplanke', '0', 1),
(25, '2015-03-03 05:30:22', 14, 'Wollrest', '0', 1),
(26, '2015-03-03 05:30:22', 15, 'Wollballen', '0', 1),
(27, '2015-03-03 05:30:22', 19, 'Geschmeidiger Holzblock', '0', 1),
(28, '2015-03-03 05:30:22', 20, 'Geschmeidige Holzplanken', '0', 1),
(29, '2015-03-03 05:30:22', 24, 'Baumwollrest', '0', 1),
(30, '2015-03-03 05:30:22', 25, 'Baumwollballen', '0', 1),
(31, '2015-03-03 05:30:22', 29, 'Abgelagerter Holzblock', '0', 1),
(32, '2015-03-03 05:30:22', 30, 'Abgelagerte Holzplanke', '0', 1),
(33, '2015-03-03 05:30:22', 32, 'Platinbarren', '0', 1),
(34, '2015-03-03 05:30:22', 33, 'Silberbarren', '0', 1),
(35, '2015-03-03 05:30:22', 34, 'Leinenrest', '0', 1),
(36, '2015-03-03 05:30:22', 35, 'Leinenballen', '0', 1),
(37, '2015-03-03 05:30:22', 39, 'Harter Holzblock', '0', 1),
(38, '2015-03-03 05:30:22', 40, 'Harte Holzplanke', '0', 1),
(39, '2015-03-03 05:30:22', 42, 'Dunkelstahlbarren', '0', 1),
(40, '2015-03-03 05:30:22', 44, 'Seidenrest', '0', 1),
(41, '2015-03-03 05:30:22', 45, 'Seidenballen', '0', 1),
(42, '2015-03-03 05:30:22', 49, 'Alter Holzblock', '0', 1),
(43, '2015-03-03 05:30:22', 50, 'Alte Holzplanke', '0', 1),
(44, '2015-03-03 05:30:22', 59, 'Antikes Holz', '0', 1),
(45, '2015-03-03 05:30:22', 60, 'Antike Holzplanke', '0', 1),
(46, '2015-03-03 12:16:02', 7, 'Rohlederstücke', '0', 1),
(47, '2015-03-03 12:16:02', 17, 'Dünnes Lederstück', '0', 1),
(48, '2015-03-03 12:16:02', 27, 'Raues Lederstück', '0', 1),
(49, '2015-03-03 12:16:02', 37, 'Robustes Lederstück', '0', 1),
(50, '2015-03-03 12:16:02', 47, 'Dickes Lederstück', '0', 1),
(51, '2015-03-03 12:16:46', 8, 'Gespannter Rohlederflicken', '0', 1),
(52, '2015-03-03 12:17:39', 12, 'Eisenbarren', '0', 1),
(53, '2015-03-03 12:17:39', 54, 'Gazerest', '0', 1),
(54, '2015-03-03 12:21:28', 65, 'Knochensplitter', '0', 2),
(55, '2015-03-03 12:21:28', 66, 'Winzige Klauen', '0', 2),
(56, '2015-03-03 12:21:28', 69, 'Winzige Schuppe', '0', 2),
(57, '2015-03-03 12:21:28', 70, 'Winzige Totem', '0', 2),
(58, '2015-03-03 12:21:28', 71, 'Winziger Giftbeutel', '0', 2),
(59, '2015-03-03 12:21:28', 75, 'Knochenscherbe', '0', 2),
(60, '2015-03-03 12:21:28', 78, 'Kleiner Fangzahn', '0', 2),
(61, '2015-03-03 12:21:28', 79, 'Kleine Schuppen', '0', 2),
(62, '2015-03-03 12:21:28', 80, 'Kleines Totem', '0', 2),
(63, '2015-03-03 12:21:28', 81, 'Kleiner Giftbeutel', '0', 2),
(64, '2015-03-03 12:21:28', 85, 'Knochen', '0', 2),
(65, '2015-03-03 12:21:28', 88, 'Fangzahn', '0', 2),
(66, '2015-03-03 12:21:28', 89, 'Schuppen', '0', 2),
(67, '2015-03-03 12:21:28', 90, 'Totem', '0', 2),
(68, '2015-03-03 12:21:28', 91, 'Giftbeutel', '0', 2),
(69, '2015-03-03 12:21:28', 95, 'Dicker Knochen', '0', 2),
(70, '2015-03-03 12:21:28', 96, 'Scharfe Klaue', '0', 2),
(71, '2015-03-03 12:21:28', 99, 'Glatte Schuppen', '0', 2),
(72, '2015-03-03 12:21:28', 100, 'Graviertes Totem', '0', 2),
(73, '2015-03-03 12:21:28', 101, 'Voller Giftbeutel', '0', 2),
(74, '2015-03-03 12:21:28', 105, 'Großer Knochen', '0', 2),
(75, '2015-03-03 12:21:28', 106, 'Große Klaue', '0', 2),
(76, '2015-03-03 12:21:28', 110, 'Verziertes Totem', '0', 2),
(77, '2015-03-03 12:21:28', 111, 'Wirkungsvolle Giftbeutel', '0', 2),
(78, '2015-03-03 12:22:20', 41, 'Primordiumbrocken', '0', 1),
(79, '2015-03-05 16:05:29', 13, 'Kupferbarren', '0', 1),
(80, '2015-03-05 16:05:29', 18, 'Getrockneter Dünner Lederflicken', '0', 1),
(81, '2015-03-05 16:05:29', 22, 'Stahlbarren', '0', 1),
(82, '2015-03-05 16:05:29', 28, 'Getrockneter rauer Lederflicken', '0', 1),
(83, '2015-03-05 16:05:29', 38, 'Getrockneter Robuster Lederflicken', '0', 1),
(84, '2015-03-05 16:05:29', 48, 'Getrockneter Dicker Lederflicken', '0', 1),
(85, '2015-03-05 16:05:29', 57, 'Gehärtetes Lederstück', '0', 1),
(86, '2015-03-05 16:05:29', 58, 'Getrockneter Gehärteter Lederflicken', '0', 1),
(87, '2015-03-05 16:05:29', 63, 'Rucksack-Rahmengestell des Handwerkers', '0', 1),
(88, '2015-03-05 16:05:29', 64, 'Phiole mit schwachem Blut', '0', 2),
(89, '2015-03-05 16:05:29', 67, 'Haufen glitzernden Staubs', '0', 2),
(90, '2015-03-05 16:05:29', 74, 'Phiole mit dünnem Blut', '0', 2),
(91, '2015-03-05 16:05:29', 76, 'Kleine Klaue', '0', 2),
(92, '2015-03-05 16:05:29', 77, 'Haufen funkelnder Staub', '0', 2),
(93, '2015-03-05 16:05:29', 84, 'Phiole mit Blut', '0', 2),
(94, '2015-03-05 16:05:29', 86, 'Klaue', '0', 2),
(95, '2015-03-05 16:05:29', 87, 'Haufen strahlenden Staub', '0', 2),
(96, '2015-03-05 16:05:29', 94, 'Phiole mit dickem Blut', '0', 2),
(97, '2015-03-05 16:05:29', 97, 'Haufen leuchtenden Staub', '0', 2),
(98, '2015-03-05 16:05:29', 104, 'Phiole mit wirkungsvollem Blut', '0', 2),
(99, '2015-03-05 16:05:29', 107, 'Haufen weißglühenden Staub', '0', 2),
(100, '2015-03-05 19:02:30', 68, 'Winziger Fangzahn', '0', 2),
(101, '2015-03-05 19:02:30', 72, 'Phiole mit kraftvollem Blut', '0', 2),
(102, '2015-03-05 19:02:30', 73, 'Antiker Knochen', '0', 2),
(103, '2015-03-05 19:02:30', 82, 'Scheußliche Klaue', '0', 2),
(104, '2015-03-05 19:02:30', 83, 'Haufen kristallinen Staubs', '0', 2),
(105, '2015-03-05 19:02:30', 92, 'Scheußlicher Fangzahn', '0', 2),
(106, '2015-03-05 19:02:30', 93, 'Gepanzerte Schuppe', '0', 2),
(107, '2015-03-05 19:02:30', 98, 'Scharfer Fangzahn', '0', 2),
(108, '2015-03-05 19:02:30', 102, 'Kunstvolles Totem', '0', 2),
(109, '2015-03-05 19:02:30', 103, 'Kraftvoller Giftbeutel', '0', 2),
(110, '2015-03-05 19:02:30', 108, 'Großer Fangzahn', '0', 2),
(111, '2015-03-05 19:02:30', 109, 'Große Schuppe', '0', 2),
(112, '2015-03-05 19:02:30', 112, 'Karka-Panzer', '0', 2),
(113, '2015-03-05 19:02:30', 113, 'Uhrwerk-Zwischenzahnrad', '0', 2),
(114, '2015-03-05 19:03:46', 169, 'Haufen Blutsteinstaub', '0', 4),
(115, '2015-03-05 19:03:46', 171, 'Dragoniterz', '0', 4),
(116, '2015-03-05 19:03:46', 172, 'Aufsteigendes Fragment', '0', 4),
(117, '2015-03-05 19:03:46', 178, 'Klumpen aus dunkler Materie', '0', 4),
(118, '2015-03-05 19:03:46', 187, 'Thermokatalytisches Reagens', '0', 4),
(119, '2015-03-05 19:08:18', 114, 'Kristall-Span', '0', 3),
(120, '2015-03-05 19:08:18', 115, 'Zerstörer-Span', '0', 3),
(121, '2015-03-05 19:08:18', 116, 'Verdorbener-Span', '0', 3),
(122, '2015-03-05 19:08:18', 117, 'Haufen beschmutzter Essenz', '0', 3),
(123, '2015-03-05 19:08:18', 118, 'Gelandener Span', '0', 3),
(124, '2015-03-05 19:08:18', 119, 'Onyx-Span', '0', 3),
(125, '2015-03-05 19:08:18', 120, 'Geschmolzener Span', '0', 3),
(126, '2015-03-05 19:08:18', 121, 'Gletscher-Span', '0', 3),
(127, '2015-03-05 19:08:18', 122, 'Großer Schädel', '0', 3),
(128, '2015-03-05 19:08:18', 170, 'Kristallinerz', '0', 4),
(129, '2015-03-05 19:08:18', 233, 'Karotte', '0', 6),
(130, '2015-03-05 19:08:18', 234, 'Salatkopf', '0', 6),
(131, '2015-03-05 19:08:18', 235, 'Pilz', '0', 6),
(132, '2015-03-05 19:08:18', 236, 'Zwiebel', '0', 6),
(133, '2015-03-05 19:08:18', 237, 'Kartoffel', '0', 6),
(134, '2015-03-05 19:08:18', 238, 'Petersilienblatt', '0', 6),
(135, '2015-03-05 19:08:18', 239, 'Blaubeere', '0', 6),
(136, '2015-03-05 19:08:18', 240, 'Knoblauchknolle', '0', 6),
(137, '2015-03-05 19:08:18', 241, 'Apfel', '0', 6),
(138, '2015-03-05 19:08:18', 243, 'Selleriestange', '0', 6),
(139, '2015-03-05 19:08:18', 244, 'Chilischote', '0', 6),
(140, '2015-03-05 19:08:19', 245, 'Zimtstange', '0', 6),
(141, '2015-03-05 19:08:19', 246, 'Kreuzkümmel', '0', 6),
(142, '2015-03-05 19:08:19', 247, 'Grüne Bohne', '0', 6),
(143, '2015-03-05 19:08:19', 248, 'Zitrone', '0', 6),
(144, '2015-03-05 19:08:19', 249, 'Muskatnuss', '0', 6),
(145, '2015-03-05 19:08:19', 250, 'Thymianblatt', '0', 6),
(146, '2015-03-05 19:08:19', 251, 'Tomate', '0', 6),
(147, '2015-03-05 19:08:19', 252, 'Vanilleschote', '0', 6),
(148, '2015-03-05 19:11:25', 253, 'Stück Butter', '0', 6),
(149, '2015-03-05 19:11:25', 256, 'Ei', '0', 6),
(150, '2015-03-05 19:11:25', 258, 'Stück Wildfleisch', '0', 6),
(151, '2015-03-05 19:11:25', 259, 'Stück Geflügelfleisch', '0', 6),
(152, '2015-03-05 19:11:25', 260, 'Beutel Mehl', '0', 6),
(153, '2015-03-05 19:11:25', 261, 'Krug Pflanzenöl', '0', 6),
(154, '2015-03-05 19:11:25', 262, 'Paket Backpulver', '0', 6),
(155, '2015-03-05 19:11:25', 263, 'Paket Salz', '0', 6),
(156, '2015-03-05 19:11:25', 264, 'Flasche Sojasoße', '0', 6),
(157, '2015-03-05 19:11:25', 265, 'Beutel mit Stärke', '0', 6),
(158, '2015-03-05 19:11:25', 266, 'Sack Zucker', '0', 6),
(159, '2015-03-05 19:11:25', 267, 'Flasche Essig', '0', 6),
(160, '2015-03-05 19:11:25', 268, 'Krug Wasser', '0', 6),
(161, '2015-03-05 19:11:25', 269, 'Erdbeere', '0', 6),
(162, '2015-03-05 19:11:25', 270, 'Rote Beete', '0', 6),
(163, '2015-03-05 19:11:25', 271, 'Muschel', '0', 6),
(164, '2015-03-05 19:11:25', 272, 'Oreganoblatt', '0', 6),
(165, '2015-03-05 19:11:25', 273, 'Salbeiblatt', '0', 6),
(166, '2015-03-05 19:11:25', 274, 'Spinatblatt', '0', 6),
(167, '2015-03-05 19:11:25', 275, 'Steckrübe', '0', 6),
(168, '2015-03-05 19:11:25', 276, 'Reisbällchen', '0', 6),
(169, '2015-03-05 19:11:25', 277, 'Banane', '0', 6),
(170, '2015-03-05 19:11:25', 278, 'Basilikumblatt', '0', 6),
(171, '2015-03-05 19:11:25', 279, 'Lorbeerblatt', '0', 6),
(172, '2015-03-05 19:11:25', 280, 'Paprika', '0', 6),
(173, '2015-03-05 19:11:25', 281, 'Schwarze Bohne', '0', 6),
(174, '2015-03-05 19:11:25', 282, 'Kidneybohne', '0', 6),
(175, '2015-03-05 19:16:56', 283, 'Walnuss', '0', 6),
(176, '2015-03-05 19:16:56', 284, 'Schoko-Riegel', '0', 6),
(177, '2015-03-05 19:16:56', 285, 'Kohlkopf', '0', 6),
(178, '2015-03-05 19:16:56', 286, 'Dillzweig', '0', 6),
(179, '2015-03-05 19:16:56', 287, 'Traube', '0', 6),
(180, '2015-03-05 19:16:56', 288, 'Grünkohlblatt', '0', 6),
(181, '2015-03-05 19:16:56', 289, 'Portobellopilz', '0', 6),
(182, '2015-03-05 19:16:56', 290, 'Rosmarinzweig', '0', 6),
(183, '2015-03-05 19:16:56', 291, 'Sesamsamen', '0', 6),
(184, '2015-03-05 19:16:56', 292, 'Süßkartoffel', '0', 6),
(185, '2015-03-05 19:16:56', 293, 'Zucchini', '0', 6),
(186, '2015-03-05 19:16:56', 294, 'Mandel', '0', 6),
(187, '2015-03-05 19:16:56', 295, 'Avocado', '0', 6),
(188, '2015-03-05 19:16:56', 296, 'Kirsche', '0', 6),
(189, '2015-03-05 19:16:56', 297, 'Ingwerwurzel', '0', 6),
(190, '2015-03-05 19:16:56', 298, 'Limette', '0', 6),
(191, '2015-03-05 19:16:56', 299, 'Schüssel mit Sauerrahm', '0', 6),
(192, '2015-03-05 19:16:56', 300, 'Brombeere', '0', 6),
(193, '2015-03-05 19:16:56', 301, 'Blumenkohlkopf', '0', 6),
(194, '2015-03-05 19:16:56', 302, 'Nelke', '0', 6),
(195, '2015-03-05 19:16:56', 303, 'Koriandersamen', '0', 6),
(196, '2015-03-05 19:16:56', 304, 'Grüne Zwiebel', '0', 6),
(197, '2015-03-05 19:16:56', 305, 'Minzblatt', '0', 6),
(198, '2015-03-05 19:16:56', 306, 'Kohlrübe', '0', 6),
(199, '2015-03-05 19:16:56', 307, 'Zuckerkürbis', '0', 6),
(200, '2015-03-05 19:16:56', 308, 'Kichererbse', '0', 6),
(201, '2015-03-05 19:16:56', 309, 'Kokosnuss', '0', 6),
(202, '2015-03-05 19:16:56', 310, 'Meerrettchwurzel', '0', 6),
(203, '2015-03-05 19:16:56', 311, 'Orange', '0', 6),
(204, '2015-03-05 19:16:56', 312, 'Birne', '0', 6),
(205, '2015-03-05 19:16:56', 313, 'Pinienkern', '0', 6),
(206, '2015-03-05 19:16:56', 314, 'Schalotte', '0', 6),
(207, '2015-03-05 19:16:56', 315, 'Artischocke', '0', 6),
(208, '2015-03-05 19:16:56', 316, 'Spargelstange', '0', 6),
(209, '2015-03-05 19:16:56', 317, 'Butternusskürbis', '0', 6),
(210, '2015-03-05 19:16:56', 318, 'Cayennepfeffer', '0', 6),
(211, '2015-03-05 19:16:56', 319, 'Lauch', '0', 6),
(212, '2015-03-05 19:16:56', 320, 'Pastinakenwurzel', '0', 6),
(213, '2015-03-05 19:16:56', 321, 'Himbeere', '0', 6),
(214, '2015-03-05 19:16:56', 322, 'Estragonblätter', '0', 6),
(215, '2015-03-05 19:16:56', 323, 'Lotuswurzel', '0', 6),
(216, '2015-03-05 19:16:56', 324, 'Seetang', '0', 6),
(217, '2015-03-05 19:16:56', 325, 'Schneetrüffel', '0', 6),
(218, '2015-03-05 19:16:56', 326, 'Aubergine', '0', 6),
(219, '2015-03-05 19:16:56', 327, 'Pfirsich', '0', 6),
(220, '2015-03-05 19:16:56', 328, 'Orrianischer Trüffel', '0', 6),
(221, '2015-03-05 19:16:56', 329, 'Geister-Paprika', '0', 6),
(222, '2015-03-05 19:16:56', 330, 'Zitronengras', '0', 6),
(223, '2015-03-05 19:16:56', 331, 'Omnombeere', '0', 6),
(224, '2015-03-05 19:16:56', 332, 'Safranfaden', '0', 6),
(225, '2015-03-05 19:16:56', 333, 'Mango', '0', 6),
(226, '2015-03-05 19:16:56', 334, 'Maracuja', '0', 6),
(227, '2015-03-05 19:16:56', 335, 'Nopal', '0', 6),
(228, '2015-03-05 19:16:56', 336, 'Stachelbirne', '0', 6),
(229, '2015-03-06 03:22:19', 337, 'Stück Candy-Corn', '0', 7),
(230, '2015-03-06 03:22:19', 338, 'Geschwätziger Schädel', '0', 7),
(231, '2015-03-06 03:22:19', 339, 'Nougatfüllung', '0', 7),
(232, '2015-03-06 03:22:19', 340, 'Plastikzähne', '0', 7),
(233, '2015-03-06 03:22:19', 341, 'Winzige Schneeflocke', '0', 7),
(234, '2015-03-06 03:22:19', 342, 'Filigrane Schneeflocke', '0', 7),
(235, '2015-03-06 03:22:19', 343, 'Glitzernde Schneeflocke', '0', 7),
(236, '2015-03-06 03:22:19', 344, 'Einzigartige Schneeflocke', '0', 7),
(237, '2015-03-06 03:22:19', 345, 'Unberührte Schneeflocke', '0', 7),
(238, '2015-03-06 03:22:19', 346, 'Makellose Schneeflocke', '0', 7),
(239, '2015-03-06 03:22:19', 347, 'Stück Zhaikritze', '0', 7),
(240, '2015-03-06 03:22:19', 348, 'Candy-Corn-Kolben', '0', 7),
(241, '2015-03-06 03:22:19', 349, 'Plappernder Schädel', '0', 7),
(242, '2015-03-06 03:22:19', 350, 'Hochwertige Plastikzähne', '0', 7),
(243, '2015-03-06 03:22:19', 351, 'Tyrias beste Nougatfüllung', '0', 7),
(244, '2015-03-08 07:05:59', 242, 'Schwarzes Pfefferkorn', '0', 6),
(245, '2015-08-05 14:49:48', 188, 'Bernsteinkiesel', NULL, 5),
(246, '2015-08-05 14:51:48', 123, 'Phiole kondensierter Nebelessenz', NULL, 3),
(247, '2015-08-05 14:52:40', 132, 'Sonnenperlen', NULL, 3),
(248, '2015-08-05 14:53:30', 153, 'Obsidian Scherben', NULL, 3),
(249, '2015-08-05 14:54:12', 152, 'Ektoplasmakugeln', NULL, 3),
(250, '2015-08-05 14:55:05', 162, 'Mystische Münzen', NULL, 3),
(251, '2015-08-05 14:56:19', 160, 'Geschmolzener Magnetstein', NULL, 3),
(252, '2015-08-05 14:57:06', 149, 'Onyx Kern', NULL, 3),
(253, '2015-08-05 14:57:31', 148, 'Geladener Kern', NULL, 3),
(254, '2015-08-05 14:58:46', 164, 'Makellose Toxische Sporenproben', NULL, 3),
(255, '2015-08-05 14:59:21', 166, 'Ambrit-Stücke', NULL, 3),
(256, '2015-08-05 15:01:39', 168, 'Aufgeladene Ambrit-Platten', NULL, 3),
(257, '2015-08-05 15:04:10', 189, 'Granatkiesel', NULL, 5),
(258, '2015-08-05 15:04:10', 190, 'Malachitkiesel', NULL, 5),
(259, '2015-08-05 15:04:10', 191, 'Perlen', NULL, 5),
(260, '2015-08-05 15:04:10', 192, 'Tigeraugenkiesel', NULL, 5),
(261, '2015-08-05 15:04:10', 193, 'Türkiskiesel', NULL, 5),
(262, '2015-08-05 15:06:23', 194, 'Amethystnugget', NULL, 5),
(263, '2015-08-05 15:06:23', 195, 'Karneolnugget', NULL, 5),
(264, '2015-08-05 15:06:23', 196, 'Lapisnugget', NULL, 5),
(265, '2015-08-05 15:06:23', 197, 'Peridotnugget', NULL, 5),
(266, '2015-08-05 15:10:30', 198, 'Spinellnugget', NULL, 5),
(267, '2015-08-05 15:10:30', 199, 'Sonnensteinnugget', NULL, 5),
(268, '2015-08-05 15:10:30', 200, 'Topasnugget', NULL, 5),
(269, '2015-08-05 15:10:30', 201, 'Amethystbrocken', NULL, 5),
(270, '2015-08-05 15:10:30', 202, 'Karneolbrocken', NULL, 5),
(271, '2015-08-05 15:10:30', 203, 'Lapisbrocken', NULL, 5),
(272, '2015-08-05 15:10:30', 204, 'Peridotbrocken', NULL, 5),
(273, '2015-08-05 15:10:30', 205, 'Spinellbrocken', NULL, 5),
(274, '2015-08-05 15:10:30', 207, 'Topasbrocken', NULL, 5),
(275, '2015-08-05 15:17:22', 208, 'Beryll-Scherben', NULL, 5),
(276, '2015-08-05 15:17:22', 209, 'Chrysokoll-Scherben', NULL, 5),
(277, '2015-08-05 15:17:22', 212, 'Opal-Scherben', NULL, 5),
(278, '2015-08-05 15:17:22', 213, 'Rubin-Scherbe', NULL, 5),
(279, '2015-08-05 15:17:22', 214, 'Saphir-Scherben', NULL, 5),
(280, '2015-08-05 15:17:22', 215, 'Beryllkristalle', NULL, 5),
(281, '2015-08-05 15:17:22', 216, 'Chrysokollkristalle', NULL, 5),
(282, '2015-08-05 15:17:22', 217, 'Korallententakeln', NULL, 5),
(283, '2015-08-05 15:20:18', 218, 'Smaragdkristalle', NULL, 5),
(284, '2015-08-05 15:20:18', 219, 'Opalkristalle', NULL, 5),
(285, '2015-08-05 15:20:18', 220, 'Rubinkristalle', NULL, 5),
(286, '2015-08-05 15:20:18', 221, 'Saphirkristalle', NULL, 5),
(287, '2015-08-05 15:20:18', 224, 'Beryllkugeln', NULL, 5),
(288, '2015-08-05 15:20:18', 225, 'Chrysokollkugeln', NULL, 5),
(289, '2015-08-05 15:21:24', 230, 'Saphirkugeln', NULL, 5),
(290, '2015-08-05 15:21:24', 232, 'Quarzkristalle', NULL, 5),
(291, '2015-08-05 15:24:09', 254, 'Gläser Buttermilch', NULL, 6),
(292, '2015-08-05 15:24:09', 255, 'Käseecken', NULL, 6),
(293, '2015-08-05 15:24:09', 257, 'Pakete Hefe', NULL, 6),
(294, '2015-08-05 15:25:45', 206, 'Sonnensteinbrocken', NULL, 5),
(295, '2015-08-05 15:26:44', 210, 'Korallenstück', NULL, 5),
(296, '2015-08-05 15:26:44', 211, 'Smaragd-Scherbe', NULL, 5),
(297, '2015-08-05 15:29:35', 222, 'Maracujablüte', NULL, 5),
(298, '2015-08-05 15:29:35', 223, 'Azuritkristall', NULL, 5),
(299, '2015-08-05 15:29:35', 226, 'Korallenkugel', NULL, 5),
(300, '2015-08-05 15:29:35', 227, 'Smaragdkugel', NULL, 5),
(301, '2015-08-05 15:29:35', 228, 'Opalkugel', NULL, 5),
(302, '2015-08-05 15:29:35', 229, 'Rubinkugel', NULL, 5),
(303, '2015-08-05 15:29:35', 231, 'Azuritkugel', NULL, 5),
(304, '2015-08-05 15:33:20', 179, 'Blutsteinziegel', NULL, 4),
(305, '2015-08-05 15:33:20', 180, 'Kristallinbarren', NULL, 4),
(306, '2015-08-05 15:33:20', 181, 'Dragonitbarren', NULL, 4),
(307, '2015-08-05 15:33:20', 182, 'Aufsteigender Stern', NULL, 4),
(308, '2015-08-05 15:34:51', 167, 'Ambrit-Platte', NULL, 3),
(309, '2015-08-07 09:33:14', 124, 'Kristall-Fragment', NULL, 3),
(310, '2015-08-19 08:56:51', 125, 'Zerstörer-Fragment', NULL, 3),
(311, '2015-08-19 08:56:51', 126, 'Verdorbenes-Fragment', NULL, 3),
(312, '2015-08-19 08:56:51', 127, 'Haufen übler Essenz', NULL, 3),
(313, '2015-08-19 08:56:51', 128, 'Geladenes Fragment', NULL, 3),
(314, '2015-08-19 08:56:51', 129, 'Onyx-Fragment', NULL, 3),
(315, '2015-08-19 08:56:51', 130, 'Geschmolzenes Fragment', NULL, 3),
(316, '2015-08-19 08:56:51', 131, 'Gletscher-Fragment', NULL, 3),
(317, '2015-08-19 08:56:51', 133, 'Batzen geronnener Nebelessenz', NULL, 3),
(318, '2015-08-19 08:56:51', 134, 'Kristall-Scherbe', NULL, 3),
(319, '2015-08-19 08:56:51', 144, 'Kristall-Kern', NULL, 3),
(320, '2015-08-19 08:56:51', 154, 'Kristall-Magnetstein', NULL, 3),
(321, '2015-08-19 08:56:51', 165, 'Uhrwerkmechanismus', NULL, 3),
(322, '2015-08-19 08:59:10', 143, 'Scherbe kristallisierter Nebelessenz', NULL, 3),
(323, '2015-08-19 08:59:10', 163, 'Aufgeladener Quarzkristall', NULL, 3),
(324, '2015-08-19 09:00:05', 142, 'Riesenauge', NULL, 3),
(325, '2015-08-24 01:20:13', 146, 'Verdorbener Kern', NULL, 3),
(326, '2015-08-25 09:06:39', 147, 'Haufen widerwärtiger Essenz', NULL, 3),
(327, '2015-08-25 09:09:56', 150, 'Geschmolzener Kern', NULL, 3),
(328, '2015-08-25 09:09:56', 151, 'Gletscher-Kern', NULL, 3),
(329, '2015-08-25 09:09:56', 155, 'Zerstörer-Magnetstein', NULL, 3),
(330, '2015-08-25 09:09:56', 156, 'Verdorbener Magnetstein', NULL, 3),
(331, '2015-08-25 09:09:56', 157, 'Haufen fauler Essenz', NULL, 3),
(332, '2015-08-25 09:09:56', 158, 'Geladener Magnetstein', NULL, 3),
(333, '2015-08-25 09:09:56', 159, 'Onyx-Magnetstein', NULL, 3),
(334, '2015-08-25 09:09:56', 161, 'Gletscher-Magnetstein', NULL, 3),
(335, '2015-08-25 09:14:05', 135, 'Zerstörer-Scherbe', NULL, 3),
(336, '2015-08-25 09:14:05', 136, 'Verdorbene Scherbe', NULL, 3),
(337, '2015-08-25 09:14:05', 137, 'Haufen schmutziger Essenz', NULL, 3),
(338, '2015-08-25 09:14:05', 138, 'Geladene Scherbe', NULL, 3),
(339, '2015-08-25 09:14:05', 139, 'Onyx-Scherbe', NULL, 3),
(340, '2015-08-25 09:14:05', 140, 'Geschmolzene Scherbe', NULL, 3),
(341, '2015-08-25 09:14:05', 141, 'Gletscher-Scherbe', NULL, 3),
(342, '2015-08-25 09:14:05', 145, 'Zerstörer-Kern', NULL, 3),
(343, '2015-08-25 09:19:21', 173, 'Batzen von Rückständen des ßltesten-Geists', NULL, 4),
(344, '2015-08-25 09:19:21', 174, 'Spule dicker elonischer Schnur', NULL, 4),
(345, '2015-08-25 09:19:21', 175, 'Spule Seidenwebfaden', NULL, 4),
(346, '2015-08-25 09:19:21', 176, 'Mithrilliumbrocken', NULL, 4),
(347, '2015-08-25 09:19:21', 177, 'Xunlai-Elektrumbarren', NULL, 4),
(348, '2015-08-25 09:19:21', 183, 'Geisterholzplanke', NULL, 4),
(349, '2015-08-25 09:19:21', 184, 'Elonischer Lederflicken', NULL, 4),
(350, '2015-08-25 09:19:21', 185, 'Damastballen', NULL, 4),
(351, '2015-08-25 09:19:21', 186, 'Deldrimor-Stahlbarren', NULL, 4),
(352, '2015-10-28 19:26:49', 352, 'Haufen aus Leinsamen', NULL, 1),
(353, '2015-10-28 19:27:12', 353, 'Mühlstein', NULL, 1),
(354, '2015-10-28 19:31:55', 354, 'Phiole mit Leinsamen-ßl', NULL, 2),
(355, '2015-10-28 19:31:55', 355, 'Blatt-Fossil', NULL, 2),
(356, '2015-10-28 19:31:55', 356, 'Stachel-Dorn', NULL, 2),
(357, '2015-10-28 19:31:55', 357, 'Haufen rauer Sand', NULL, 2),
(358, '2015-10-28 19:34:49', 375, 'Flaschen Luftschiff-ßl', NULL, 4),
(359, '2015-10-28 19:36:31', 384, 'Süßwasserperle', NULL, 5),
(360, '2015-10-30 20:18:21', 385, 'Cassavawurzel', NULL, 6),
(361, '2015-10-30 20:18:21', 386, 'Beutel mit Cassavamehl', NULL, 6),
(362, '2015-10-30 20:18:21', 387, 'Miesmuscheln', NULL, 6),
(363, '2015-10-31 00:01:30', 358, 'Mordrem-Span', NULL, 3),
(364, '2015-10-31 00:01:30', 359, 'Mordrem-Fragment', NULL, 3),
(365, '2015-10-31 00:01:30', 360, 'Mordrem-Scherbe', NULL, 3),
(366, '2015-10-31 00:01:30', 361, 'Mordrem-Kern', NULL, 3),
(367, '2015-10-31 00:01:30', 362, 'Mordrem-Magnetstein', NULL, 3),
(368, '2015-10-31 00:01:30', 363, 'Immergrün-Späne', NULL, 3),
(369, '2015-10-31 00:01:30', 364, 'Immergrün-Fragment', NULL, 3),
(370, '2015-10-31 00:01:30', 365, 'Immergrün-Scherbe', NULL, 3),
(371, '2015-10-31 00:01:30', 366, 'Immergrün-Kern', NULL, 3),
(372, '2015-10-31 00:01:30', 367, 'Immergrün-Magnetstein', NULL, 3),
(373, '2015-10-31 00:01:30', 368, 'Aufgeladenes Fossil', NULL, 3),
(374, '2015-10-31 00:01:30', 369, 'Aufgeladener Dorn', NULL, 3),
(375, '2015-10-31 00:01:30', 370, 'Leuchtpilzgruppe', NULL, 3),
(376, '2015-10-31 00:01:30', 371, 'Scherbe des Ruhms', NULL, 3),
(377, '2015-10-31 00:01:30', 372, 'Erinnerungen des Kampfes', NULL, 3),
(378, '2015-10-31 00:01:30', 373, 'Mystischer Glücksklee', NULL, 3),
(379, '2015-10-31 00:02:51', 374, 'Fulgurit', NULL, 4),
(380, '2015-10-31 00:02:51', 376, 'Haufen Güldener Staub', NULL, 4),
(381, '2015-10-31 00:02:51', 377, 'Ley-Linien-Funke', NULL, 4),
(382, '2015-10-31 00:04:22', 378, 'Schwarzer Diamant', NULL, 5),
(383, '2015-10-31 00:04:22', 379, 'Stück Maguuma-Wurzelholz', NULL, 5),
(384, '2015-10-31 00:04:22', 380, 'Ebenholzkugel', NULL, 5),
(385, '2015-10-31 00:04:22', 381, 'Leinblüte', NULL, 5),
(386, '2015-10-31 00:04:22', 382, 'Maguuma-Lilie', NULL, 5),
(387, '2015-10-31 00:04:22', 383, 'Mondsteinkugel', NULL, 5),
(388, '2015-11-01 13:11:43', 388, 'Haufen Pimentbeeren', NULL, 6),
(389, '2015-11-01 13:11:43', 389, 'Sägeblattpilz', NULL, 6),
(390, '2015-11-04 20:13:59', 390, 'Flachsfaser', '0', 1),
(391, '2015-11-04 20:13:59', 391, 'Beutel mit rotem Pigment', '0', 1),
(392, '2015-11-04 20:13:59', 392, 'Beutel mit orangenem Pigment', '0', 1),
(393, '2015-11-04 20:13:59', 393, 'Beutel mit gelben Pigment', '0', 1),
(394, '2015-11-04 20:13:59', 394, 'Beutel mit grünem Pigment', '0', 1),
(395, '2015-11-04 20:13:59', 395, 'Beutel mit blauen Pigment', '0', 1),
(396, '2015-11-04 20:13:59', 396, 'Beutel mit lilanen Pigment', '0', 1),
(397, '2015-11-04 20:13:59', 397, 'Beutel mit weißem Pigment', '0', 1),
(398, '2015-11-04 20:13:59', 398, 'Beutel mit schwarzem Pigment', '0', 1),
(399, '2015-11-04 20:13:59', 399, 'Beutel mit braunem Pigment', '0', 1),
(400, '2015-11-04 20:16:42', 400, 'Perlmutt-Stück', '0', 2),
(401, '2015-11-04 20:16:42', 401, 'Güldene Späne', '0', 3),
(402, '2015-11-04 20:16:42', 402, 'Güldene Barren', '0', 3),
(403, '2015-11-04 20:16:42', 403, 'Resornierender Span', '0', 3),
(404, '2015-11-04 20:16:42', 404, 'Resornierendes Fragment', '0', 3),
(405, '2015-11-04 20:16:42', 405, 'Resornierender Kern', '0', 3),
(406, '2015-11-04 20:16:42', 406, 'Resornierender Magnetstein', '0', 3),
(407, '2015-11-04 20:17:53', 407, 'Stabilisierende Matrix', '0', 4),
(408, '2015-11-04 20:17:53', 408, 'Kugel aus dunkler Energie', '0', 4),
(409, '2015-11-04 20:17:53', 409, 'Würfel aus stabilisierender dunkler Energie', '0', 4),
(410, '2015-11-05 04:24:09', 410, 'Achatkugel', '198', 5),
(411, '2016-02-02 03:33:43', 411, 'Zuckerstangen', '160', 7),
(412, '2016-11-05 07:55:10', 412, 'Mystische Kuriosität', '0', 4),
(413, '2016-11-05 07:55:10', 413, 'Scherbe von Anstrengung', '0', 4),
(414, '2017-05-20 15:57:20', 414, 'Blutrubin', '0', 3),
(415, '2017-05-20 15:57:20', 415, 'Versteinertes Holz', '0', 3),
(416, '2017-05-20 15:57:20', 416, 'Frische Winterbeere', '0', 3),
(417, '2017-05-20 15:57:20', 417, 'Jade-Scherbe', '0', 3),
(418, '2017-05-20 15:57:20', 418, 'Feuerorchidenblüte', '0', 3),
(419, '2017-05-20 15:58:35', 419, 'Scherbe der Freundschaft', '0', 4),
(420, '2017-05-20 15:58:35', 420, 'Scherbe des Krieges', '0', 4),
(421, '2017-05-20 15:58:35', 421, 'Scherbe der Liturgie', '0', 4);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `gwusersmats`
--

CREATE TABLE `gwusersmats` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `besitzer` int(11) NOT NULL,
  `matID` int(11) NOT NULL,
  `matAnzahl` int(11) NOT NULL,
  `account` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `gw_accounts`
--

CREATE TABLE `gw_accounts` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `besitzer` int(11) NOT NULL,
  `account` int(11) NOT NULL,
  `mail` varchar(250) COLLATE utf8_german2_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `gw_chars`
--

CREATE TABLE `gw_chars` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `besitzer` int(11) NOT NULL,
  `name` varchar(254) COLLATE utf8_german2_ci NOT NULL,
  `geboren` date NOT NULL,
  `rasse` varchar(50) COLLATE utf8_german2_ci NOT NULL,
  `klasse` varchar(50) COLLATE utf8_german2_ci NOT NULL,
  `stufe` tinyint(3) NOT NULL,
  `handwerk1` varchar(50) COLLATE utf8_german2_ci NOT NULL,
  `handwerk2` varchar(50) COLLATE utf8_german2_ci NOT NULL,
  `handwerk1stufe` smallint(3) NOT NULL,
  `handwerk2stufe` smallint(3) NOT NULL,
  `erkundung` smallint(3) NOT NULL,
  `spielstunden` smallint(6) NOT NULL,
  `notizen` text COLLATE utf8_german2_ci NOT NULL,
  `account` int(11) DEFAULT NULL COMMENT 'Welchem Account dieser Charakter zugeordnet ist'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `hardwaredefinition`
--

CREATE TABLE `hardwaredefinition` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `hwTypeName` varchar(250) COLLATE utf8_german2_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `hardwaredeliverers`
--

CREATE TABLE `hardwaredeliverers` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `hwDelName` varchar(250) COLLATE utf8_german2_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `hardwareentries`
--

CREATE TABLE `hardwareentries` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `besitzer` int(11) NOT NULL,
  `hwManu` int(11) NOT NULL COMMENT 'FK',
  `hwName` varchar(250) COLLATE utf8_german2_ci NOT NULL,
  `hwValue` decimal(9,2) DEFAULT NULL,
  `hwDescription` text COLLATE utf8_german2_ci NOT NULL,
  `hwBuydate` date NOT NULL,
  `hwGarantieLengthMonth` int(11) NOT NULL,
  `hwType` int(11) NOT NULL COMMENT 'FK',
  `hwSerial` varchar(30) COLLATE utf8_german2_ci NOT NULL,
  `hwDeliverer` int(11) NOT NULL COMMENT 'FK',
  `hwDelNumber` varchar(250) COLLATE utf8_german2_ci NOT NULL,
  `hwDelDate` date NOT NULL,
  `hwReNumber` varchar(250) COLLATE utf8_german2_ci NOT NULL,
  `hwReDate` date NOT NULL,
  `hwGarantieType` int(11) NOT NULL COMMENT 'FK',
  `hwStandort` varchar(250) COLLATE utf8_german2_ci NOT NULL,
  `hwSold` date NOT NULL,
  `hwKostenstelle` varchar(30) COLLATE utf8_german2_ci NOT NULL,
  `hwHardwareType` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `hardwaregarantietypes`
--

CREATE TABLE `hardwaregarantietypes` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `hwGarantieName` varchar(250) COLLATE utf8_german2_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `hardwaremanufacturers`
--

CREATE TABLE `hardwaremanufacturers` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `manuName` varchar(250) COLLATE utf8_german2_ci NOT NULL,
  `manuDescription` text COLLATE utf8_german2_ci NOT NULL,
  `ersteller` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `hardwaretypes`
--

CREATE TABLE `hardwaretypes` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `hwTypeName` varchar(250) COLLATE utf8_german2_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

--
-- Daten für Tabelle `hardwaretypes`
--

INSERT INTO `hardwaretypes` (`id`, `timestamp`, `hwTypeName`) VALUES
(1, '2018-09-12 09:11:32', 'Desktop'),
(2, '2018-09-12 09:11:32', 'Laptop');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `log`
--

CREATE TABLE `log` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `benutzer` int(11) NOT NULL,
  `log_text` text COLLATE utf8_german2_ci NOT NULL,
  `ip_adress` varchar(15) COLLATE utf8_german2_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `registercode`
--

CREATE TABLE `registercode` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `code` varchar(250) COLLATE utf8_german2_ci NOT NULL,
  `used` int(11) NOT NULL COMMENT 'Wurde er benutzt',
  `usageTimes` int(11) NOT NULL,
  `usedBy` varchar(250) COLLATE utf8_german2_ci NOT NULL COMMENT 'Welcher Benutzer hat ihn eingelöst.',
  `ipadress` varchar(15) COLLATE utf8_german2_ci NOT NULL COMMENT 'Ipadress des Benutzers',
  `rights` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `rightkategorien`
--

CREATE TABLE `rightkategorien` (
  `id` int(11) NOT NULL,
  `name` varchar(254) COLLATE utf8_german2_ci NOT NULL COMMENT 'Name der Kategorie'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

--
-- Daten für Tabelle `rightkategorien`
--

INSERT INTO `rightkategorien` (`id`, `name`) VALUES
(1, 'ßffentlich'),
(2, 'Administrator'),
(3, 'Forum'),
(4, 'Ankündigungen'),
(5, 'Guildwars'),
(6, 'Finanzen'),
(7, 'Adressbuch'),
(8, 'Fahrkosten (dep)'),
(9, 'Starcitizen (dep)'),
(10, 'Lernbereich (dep)'),
(11, 'Gesundheit (dep)'),
(12, 'EventPlanner'),
(13, 'Toyota');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `rights`
--

CREATE TABLE `rights` (
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `besitzer` int(11) NOT NULL COMMENT 'Dieser Benutzer hat das Recht',
  `right_id` int(11) NOT NULL COMMENT 'Rechte ID aus der Tabelle userrights'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

--
-- Daten für Tabelle `rights`
--

INSERT INTO `rights` (`timestamp`, `besitzer`, `right_id`) VALUES
('2015-09-22 10:58:12', 1, 2),
('2015-09-22 10:58:15', 1, 3),
('2015-09-22 10:29:38', 1, 7),
('2015-09-22 10:58:14', 1, 8),
('2015-09-22 11:12:28', 1, 9),
('2015-09-22 10:56:49', 1, 10),
('2015-09-22 10:58:20', 1, 13),
('2015-09-22 10:58:21', 1, 14),
('2015-09-22 10:58:10', 1, 15),
('2015-09-23 07:39:10', 1, 16),
('2015-09-22 10:58:18', 1, 17),
('2015-09-22 10:58:18', 1, 18),
('2015-09-22 10:58:15', 1, 19),
('2015-09-22 10:58:13', 1, 20),
('2015-09-23 07:39:35', 1, 21),
('2015-09-22 10:58:23', 1, 22),
('2015-09-23 03:43:47', 1, 23),
('2015-09-23 07:38:50', 1, 24),
('2015-09-23 03:43:51', 1, 25),
('2015-09-23 03:43:57', 1, 26),
('2015-09-23 03:43:58', 1, 27),
('2015-09-23 03:44:01', 1, 28),
('2015-09-23 03:44:03', 1, 29),
('2015-09-23 03:44:04', 1, 30),
('2015-09-23 03:44:06', 1, 31),
('2015-09-23 03:44:07', 1, 32),
('2015-09-23 03:44:08', 1, 33),
('2015-09-23 03:44:10', 1, 34),
('2015-09-23 03:53:11', 1, 35),
('2015-09-23 03:53:02', 1, 36),
('2015-09-24 03:50:58', 1, 37),
('2015-09-23 03:59:18', 1, 38),
('2015-09-23 03:59:19', 1, 39),
('2015-09-23 03:59:20', 1, 40),
('2015-09-23 03:59:21', 1, 41),
('2015-09-23 04:02:30', 1, 42),
('2015-09-23 04:08:36', 1, 43),
('2015-09-23 04:08:38', 1, 44),
('2015-09-23 04:14:00', 1, 45),
('2015-09-23 04:13:56', 1, 46),
('2015-09-23 04:13:58', 1, 47),
('2015-09-23 04:16:59', 1, 48),
('2015-09-23 04:17:00', 1, 49),
('2015-09-23 04:17:02', 1, 50),
('2015-09-23 04:36:09', 1, 51),
('2015-09-24 04:47:02', 1, 52),
('2015-09-24 04:47:00', 1, 53),
('2015-09-24 04:44:33', 1, 54),
('2015-09-23 04:36:40', 1, 55),
('2015-09-24 04:46:58', 1, 56),
('2015-09-23 04:36:44', 1, 57),
('2015-09-23 04:36:46', 1, 58),
('2015-09-23 04:44:40', 1, 59),
('2015-09-23 05:15:09', 1, 61),
('2015-09-23 05:15:11', 1, 62),
('2015-09-23 05:49:01', 1, 63),
('2015-09-23 05:49:03', 1, 64),
('2015-09-23 05:48:58', 1, 66),
('2015-09-24 03:56:35', 1, 67),
('2015-09-24 04:21:18', 1, 68),
('2015-12-04 22:48:32', 1, 69),
('2019-01-04 08:13:57', 1, 70),
('2019-01-04 08:14:00', 1, 71),
('2019-01-04 12:48:13', 1, 72),
('2016-02-22 06:18:25', 1, 73),
('2016-02-22 06:18:28', 1, 77),
('2018-10-08 06:36:39', 1, 78),
('2018-10-08 06:36:43', 1, 79),
('2018-10-08 06:36:45', 1, 80),
('2018-09-11 07:46:12', 1, 81),
('2018-09-11 07:46:15', 1, 82);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `uebersicht_kacheln`
--

CREATE TABLE `uebersicht_kacheln` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `name` varchar(250) COLLATE utf8_german2_ci NOT NULL COMMENT 'Name der Kachel',
  `link` varchar(250) COLLATE utf8_german2_ci NOT NULL COMMENT 'Link zur Startseite der Kachel',
  `beschreibung` varchar(250) COLLATE utf8_german2_ci NOT NULL COMMENT 'Beschreibung der Kachel',
  `sortierung` int(11) NOT NULL COMMENT 'Sortierung',
  `active` tinyint(4) DEFAULT NULL COMMENT 'Kachel aktiviert',
  `cssID` varchar(250) COLLATE utf8_german2_ci NOT NULL,
  `rightID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

--
-- Daten für Tabelle `uebersicht_kacheln`
--

INSERT INTO `uebersicht_kacheln` (`id`, `timestamp`, `name`, `link`, `beschreibung`, `sortierung`, `active`, `cssID`, `rightID`) VALUES
(1, '2016-02-04 08:08:04', 'Adressbuch', '/flatnet2/datenbank/datenbanken.php', 'Zeigt ein einfaches Adressbuch an.', 1, 1, 'adressbuch', 13),
(3, '2016-02-04 08:08:08', 'Forum', '/flatnet2/forum/index.php', 'Zeigt ein Forum an', 2, 1, 'forum', 13),
(8, '2016-02-04 08:08:11', 'Guildwars', '/flatnet2/guildwars/start.php', 'Verwaltung für Guildwars Charakter und Handwerk', 3, 1, 'guildwars', 3),
(9, '2016-02-04 08:08:14', 'Finanzbereich', '/flatnet2/finanzen/index.php', 'Eine Finanzverwaltung für Budget-Planung', 4, 1, 'finanzen', 17),
(12, '2015-12-06 20:59:50', 'Geburtstagskalender', '/flatnet2/datenbank/kalender.php', 'Basis Geburtstagskalender', 100, 1, 'gebkalender', 22),
(14, '2018-10-08 06:36:23', 'Planner', '/flatnet2/planner/index.php', 'Event Planner', 5000, 1, 'planner', 78),
(15, '2019-01-04 08:13:43', 'Administration', '/flatnet2/admin/control.php', 'Administration', 100, 1, 'administration', 36),
(16, '2019-01-04 08:14:36', 'Lernbereich', '/flatnet2/learner/index.php', 'Lernbereich', 1, 1, 'learner', 70);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `userrights`
--

CREATE TABLE `userrights` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Erstellungsdatum',
  `kategorie` int(11) NOT NULL COMMENT 'Ermöglicht eine Kategorisierung',
  `recht` text COLLATE utf8_german2_ci NOT NULL,
  `potenz` int(11) NOT NULL,
  `dezimalerWert` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

--
-- Daten für Tabelle `userrights`
--

INSERT INTO `userrights` (`id`, `timestamp`, `kategorie`, `recht`, `potenz`, `dezimalerWert`) VALUES
(2, '2015-08-20 17:29:03', 3, 'SEHEN: Forum', 1, 2),
(3, '2015-08-20 17:29:10', 5, 'Guildwarsbereich sehen', 2, 4),
(7, '2015-08-20 17:29:25', 1, 'Übersicht, Profil sehen', 0, 1),
(8, '2015-09-23 05:47:26', 4, 'SEHEN: Ankündigungshauptseite', 6, 64),
(9, '2015-08-20 17:29:39', 5, 'DO: Allgemeines Schreibrecht innerhalb des GW Bereichs', 5, 32),
(10, '2015-08-20 17:29:50', 2, 'SEHEN: Betrachten FREMDER nicht veröffentlichter Foreneinträge', 4, 16),
(11, '2015-09-22 09:42:43', 8, 'Leeres Recht', 0, 0),
(12, '2015-09-22 09:43:06', 8, 'Leeres Recht', 0, 0),
(13, '2015-08-20 17:30:08', 7, 'WATCH: Adressbuch betrachten', 9, 512),
(14, '2015-08-20 17:30:13', 7, 'DO: Einträge im Adressbuch bearbeiten', 10, 1024),
(15, '2015-09-23 04:10:11', 2, 'Rechteverwaltung: Darf Berechtigungen erstellen', 11, 2048),
(16, '2015-08-20 17:30:28', 5, 'DO: Handwerksmaterialien GW Bereich', 12, 4096),
(17, '2015-08-20 17:30:34', 6, 'SEHEN: Finanzbereich', 13, 8192),
(18, '2015-08-20 17:30:39', 6, 'DO: Finanzbereich Schreibrechte', 14, 16384),
(19, '2015-09-23 05:47:31', 4, 'DO: Ankündigungen erstellen', 15, 32768),
(20, '2015-08-20 17:30:52', 3, 'DO: Forum Schreibrechte', 16, 65536),
(21, '2015-08-20 17:30:58', 5, 'ADMIN: Guildwarsbereich', 17, 131072),
(22, '2015-08-20 17:31:07', 7, 'SEHEN: Kalender im Adressbuch', 18, 262144),
(23, '0000-00-00 00:00:00', 1, 'DO: Darf die Suche nutzen', 0, 0),
(24, '0000-00-00 00:00:00', 4, 'SEHEN: Ankündigung oberhalb der Seite', 0, 0),
(25, '0000-00-00 00:00:00', 1, 'SEHEN: Benachrichtigungscenter', 0, 0),
(26, '0000-00-00 00:00:00', 3, 'Forum-Admin: Hat Zugriff auf die Admin-Tools', 0, 0),
(27, '0000-00-00 00:00:00', 3, 'Forum-Admin: Darf Themen sperren und entsperren', 0, 0),
(28, '0000-00-00 00:00:00', 3, 'DO: Darf im Forum seine eigenen Einträge löschen', 0, 0),
(29, '0000-00-00 00:00:00', 3, 'Forum-Admin: Darf Einträge im Forum löschen (alle Benutzer)', 0, 0),
(30, '0000-00-00 00:00:00', 3, 'DO: Antworten in Themen erstellen', 0, 0),
(31, '0000-00-00 00:00:00', 3, 'SEHEN: Antworten eines Themas', 0, 0),
(32, '0000-00-00 00:00:00', 3, 'Forum-Admin: Benutzer darf Einträge editieren (alle Benutzer)', 0, 0),
(33, '0000-00-00 00:00:00', 3, 'DO: Benutzer darf seine eigenen Antworten in Themen editieren', 0, 0),
(34, '2015-09-23 03:48:21', 3, 'DO: Benutzer darf seine eigenen Antworten in Themen löschen', 0, 0),
(35, '0000-00-00 00:00:00', 3, 'Forum-Admin: Benutzer darf Antworten in Themen löschen (alle Benutzer)', 0, 0),
(36, '0000-00-00 00:00:00', 2, 'SEHEN: Administrationsbereich', 0, 0),
(37, '2015-09-23 04:00:19', 2, 'Benutzerverwaltung: Benutzer darf die Benutzerverwaltung anzeigen', 0, 0),
(38, '2015-09-23 04:00:14', 2, 'Benutzerverwaltung: Benutzer darf neue Benutzer anlegen', 0, 0),
(39, '2015-09-23 04:00:09', 2, 'Benutzerverwaltung: Benutzer darf bestehende Benutzer bearbeiten', 0, 0),
(40, '2015-09-23 04:00:05', 2, 'Benutzerverwaltung: Benutzer darf bestehende Benutzeraccounts löschen', 0, 0),
(41, '2015-09-23 03:59:56', 2, 'Benutzerverwaltung: Benutzer darf alle Informationen eines Benutzers anzeigen', 0, 0),
(42, '0000-00-00 00:00:00', 2, 'LOG: Darf das Log anzeigen', 0, 0),
(43, '0000-00-00 00:00:00', 2, 'LOG: Darf Einträge aus dem Log löschen', 0, 0),
(44, '0000-00-00 00:00:00', 2, 'Rechteverwaltung: SEHEN: Rechteverwaltung', 0, 0),
(45, '0000-00-00 00:00:00', 2, 'Benutzerverwaltung: Darf Benutzer entsperren und sperren', 0, 0),
(46, '0000-00-00 00:00:00', 2, 'Rechteverwaltung: Darf Berechtigungen an die Benutzer verteilen', 0, 0),
(47, '0000-00-00 00:00:00', 2, 'Rechtverwaltung FORUM: Darf Rechte an Benutzer im Forum verteilen', 0, 0),
(48, '0000-00-00 00:00:00', 2, 'Forumverwaltung: SEHEN: Forenverwaltung', 0, 0),
(49, '0000-00-00 00:00:00', 2, 'Forumverwaltung: Darf neue Kategorien im Forum erstellen', 0, 0),
(50, '0000-00-00 00:00:00', 2, 'Forumverwaltung: Darf bestehende Kategorien im Forum bearbeiten', 0, 0),
(51, '0000-00-00 00:00:00', 2, 'Objektverwaltung: SEHEN: Darf die Objektverwaltung sehen und bedienen', 0, 0),
(52, '0000-00-00 00:00:00', 2, 'Objektverwaltung: Darf Updates durchführen', 0, 0),
(53, '0000-00-00 00:00:00', 2, 'Objektverwaltung: Darf in der ObjVerw. Einträge löschen', 0, 0),
(54, '0000-00-00 00:00:00', 2, 'Objektverwaltung: Darf Einträge erstellen', 0, 0),
(55, '0000-00-00 00:00:00', 2, 'Objektverwaltung: Darf Objekte anzeigen', 0, 0),
(56, '0000-00-00 00:00:00', 2, 'Objektverwaltung: Darf die Master SQL-Query-BOX verwenden', 0, 0),
(57, '0000-00-00 00:00:00', 2, 'Registercodes: Darf die Codeverwaltung verwenden', 0, 0),
(58, '0000-00-00 00:00:00', 2, 'Benutzerverwaltung: Darf die Informationen eines Benutzers löschen', 0, 0),
(59, '0000-00-00 00:00:00', 7, 'DO: Einträge im Adressbuch erstellen', 0, 0),
(60, '2018-10-09 22:00:00', 8, 'Leeres Recht', 0, 0),
(61, '0000-00-00 00:00:00', 5, 'SEHEN: Liste der GW Charakter', 0, 0),
(62, '0000-00-00 00:00:00', 5, 'SEHEN: Kalender im GW Bereich', 0, 0),
(63, '0000-00-00 00:00:00', 5, 'SEHEN & DO: Kostenbereich Guildwars', 0, 0),
(64, '0000-00-00 00:00:00', 5, 'SEHEN: Statistik zu allen Charaktern', 0, 0),
(65, '0000-00-00 00:00:00', 9, 'Leeres Recht', 0, 0),
(66, '0000-00-00 00:00:00', 4, 'LÖSCHEN: Ankündigungen', 0, 0),
(67, '2015-09-24 03:55:15', 2, 'SEHEN: Links zu externen Ressourcen', 0, 0),
(68, '2015-09-24 04:20:49', 2, 'SEHEN: Rechtekategorie-Verwaltung', 0, 0),
(69, '2015-11-05 04:39:56', 5, 'DO: Darf neue Materialien im Handwerksbereich anlegen und verwalten', 0, 0),
(70, '2015-11-25 19:36:24', 10, 'Leeres Recht', 0, 0),
(71, '2015-11-25 19:36:36', 10, 'Leeres Recht', 0, 0),
(72, '2015-11-25 19:37:21', 10, 'Leeres Recht', 0, 0),
(73, '2016-02-07 20:49:17', 2, 'Darf die Übersichtkacheln administrieren', 0, 0),
(74, '2016-02-07 20:49:30', 11, 'Leeres Recht', 0, 0),
(76, '2016-02-07 20:50:13', 11, 'Leeres Recht', 0, 0),
(77, '2016-02-07 20:51:44', 2, 'Darf in der Übersicht den Status verändern', 0, 0),
(78, '2016-10-31 14:11:57', 12, 'Uebersicht Kacheln ansehen', 0, 0),
(79, '2016-10-31 14:12:15', 12, 'Super Administrator', 0, 0),
(80, '2016-10-31 14:12:25', 12, 'Leeres Recht', 0, 0),
(81, '2017-01-19 06:54:18', 13, 'Darf den Toyota-Bereich sehen', 0, 0),
(82, '2017-01-19 06:54:48', 13, 'DO: Schreibrecht im Bereich Toyota', 0, 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `vokabelkategorien`
--

CREATE TABLE `vokabelkategorien` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `kat_name` varchar(100) COLLATE utf8_german2_ci NOT NULL,
  `sprach_id` int(11) NOT NULL,
  `lektion_nr` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

--
-- Daten für Tabelle `vokabelkategorien`
--

INSERT INTO `vokabelkategorien` (`id`, `timestamp`, `kat_name`, `sprach_id`, `lektion_nr`) VALUES
(1, '2019-01-04 12:12:23', '1', 1, 1),
(2, '2019-01-04 12:12:27', '2', 1, 2),
(3, '2019-01-04 12:12:29', '3', 1, 3),
(4, '2019-01-04 12:12:32', '4', 1, 4),
(5, '2019-01-04 12:12:34', '5', 1, 5),
(6, '2019-01-04 12:12:50', '6', 1, 6),
(7, '2019-01-04 12:13:36', '7', 1, 7),
(8, '2019-01-04 12:13:46', '8', 1, 8),
(9, '2019-01-04 12:15:04', '9', 1, 9),
(10, '2019-01-04 12:15:04', '10', 1, 10),
(11, '2019-01-04 12:15:04', '11', 1, 11),
(12, '2019-01-04 12:15:04', '12', 1, 12),
(13, '2019-01-04 12:15:04', '13', 1, 13),
(14, '2019-01-04 12:15:04', '14', 1, 14),
(15, '2019-01-04 12:15:04', '15', 1, 15),
(16, '2019-01-04 12:15:04', '16', 1, 16),
(17, '2019-01-04 12:15:04', '17', 1, 17),
(18, '2019-01-04 12:15:04', '18', 1, 18),
(19, '2019-01-04 12:15:04', '19', 1, 19),
(20, '2019-01-04 12:15:04', '20', 1, 20);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `vokabelliste`
--

CREATE TABLE `vokabelliste` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `vok_name_ori` varchar(100) COLLATE utf8_german2_ci NOT NULL COMMENT 'Originalsprache',
  `vok_name_ueb` varchar(100) COLLATE utf8_german2_ci NOT NULL COMMENT 'Uebersetzung',
  `vok_kat` int(11) NOT NULL COMMENT 'FK_Kategorie',
  `vok_desc` text COLLATE utf8_german2_ci NOT NULL COMMENT 'Zusatzinfos'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

--
-- Daten für Tabelle `vokabelliste`
--

INSERT INTO `vokabelliste` (`id`, `timestamp`, `vok_name_ori`, `vok_name_ueb`, `vok_kat`, `vok_desc`) VALUES
(1, '2019-01-04 07:50:15', 'わたち', 'ich', 1, ''),
(2, '2019-01-04 10:24:15', 'わたち2', 'ich2', 1, ''),
(3, '2019-01-04 10:24:15', 'わたち3', 'ich3', 1, ''),
(4, '2019-01-04 10:24:15', 'わたち4', 'ich4', 1, ''),
(10, '2019-01-07 06:14:46', 'むずかしい', 'schwierig schwierig schwierig', 8, ''),
(11, '2019-01-04 14:10:43', 'やさしい', 'einfach', 8, ''),
(12, '2019-01-04 14:10:43', 'たかい', 'teuer', 8, ''),
(13, '2019-01-04 14:10:43', 'やすい', 'billig', 8, ''),
(14, '2019-01-04 14:10:43', 'あつい', 'heiß', 8, '');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `vokabelnfortschritt`
--

CREATE TABLE `vokabelnfortschritt` (
  `user_id` int(11) NOT NULL,
  `vokabel_id` int(11) NOT NULL,
  `positiv` int(11) NOT NULL,
  `negativ` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `vokabeln_sprachauswahl`
--

CREATE TABLE `vokabeln_sprachauswahl` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sprach_name` varchar(100) COLLATE utf8_german2_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

--
-- Daten für Tabelle `vokabeln_sprachauswahl`
--

INSERT INTO `vokabeln_sprachauswahl` (`id`, `timestamp`, `sprach_name`) VALUES
(1, '2019-01-04 07:55:25', 'Japanisch');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `vorschlaege`
--

CREATE TABLE `vorschlaege` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `autor` int(11) NOT NULL,
  `text` text COLLATE utf8_german2_ci NOT NULL,
  `status` varchar(20) COLLATE utf8_german2_ci NOT NULL,
  `ipadress` varchar(15) COLLATE utf8_german2_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `account_infos`
--
ALTER TABLE `account_infos`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `adressbuch`
--
ALTER TABLE `adressbuch`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `id_2` (`id`);

--
-- Indizes für die Tabelle `benutzer`
--
ALTER TABLE `benutzer`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `blogkategorien`
--
ALTER TABLE `blogkategorien`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `blogtexte`
--
ALTER TABLE `blogtexte`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `blog_kommentare`
--
ALTER TABLE `blog_kommentare`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `docu`
--
ALTER TABLE `docu`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `eventadministrators`
--
ALTER TABLE `eventadministrators`
  ADD PRIMARY KEY (`eventid`,`userid`);

--
-- Indizes für die Tabelle `eventcodeusage`
--
ALTER TABLE `eventcodeusage`
  ADD PRIMARY KEY (`codeid`,`userid`) USING BTREE;

--
-- Indizes für die Tabelle `eventguests`
--
ALTER TABLE `eventguests`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `eventinvitecodes`
--
ALTER TABLE `eventinvitecodes`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `eventlist`
--
ALTER TABLE `eventlist`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `eventtexts`
--
ALTER TABLE `eventtexts`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `finanzen_jahresabschluss`
--
ALTER TABLE `finanzen_jahresabschluss`
  ADD PRIMARY KEY (`besitzer`,`jahr`,`konto`);

--
-- Indizes für die Tabelle `finanzen_konten`
--
ALTER TABLE `finanzen_konten`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `finanzen_monatsabschluss`
--
ALTER TABLE `finanzen_monatsabschluss`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `finanzen_shares`
--
ALTER TABLE `finanzen_shares`
  ADD PRIMARY KEY (`besitzer`,`konto_id`,`target_user`);

--
-- Indizes für die Tabelle `finanzen_umsaetze`
--
ALTER TABLE `finanzen_umsaetze`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `gwcosts`
--
ALTER TABLE `gwcosts`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `gwmatlist`
--
ALTER TABLE `gwmatlist`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `gwusersmats`
--
ALTER TABLE `gwusersmats`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `gw_accounts`
--
ALTER TABLE `gw_accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `gw_chars`
--
ALTER TABLE `gw_chars`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `hardwaredefinition`
--
ALTER TABLE `hardwaredefinition`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `hardwaredeliverers`
--
ALTER TABLE `hardwaredeliverers`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `hardwareentries`
--
ALTER TABLE `hardwareentries`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `hardwaregarantietypes`
--
ALTER TABLE `hardwaregarantietypes`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `hardwaremanufacturers`
--
ALTER TABLE `hardwaremanufacturers`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `hardwaretypes`
--
ALTER TABLE `hardwaretypes`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `log`
--
ALTER TABLE `log`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `registercode`
--
ALTER TABLE `registercode`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `rightkategorien`
--
ALTER TABLE `rightkategorien`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `rights`
--
ALTER TABLE `rights`
  ADD PRIMARY KEY (`besitzer`,`right_id`);

--
-- Indizes für die Tabelle `uebersicht_kacheln`
--
ALTER TABLE `uebersicht_kacheln`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `userrights`
--
ALTER TABLE `userrights`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `vokabelkategorien`
--
ALTER TABLE `vokabelkategorien`
  ADD PRIMARY KEY (`id`,`sprach_id`) USING BTREE;

--
-- Indizes für die Tabelle `vokabelliste`
--
ALTER TABLE `vokabelliste`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `vokabelnfortschritt`
--
ALTER TABLE `vokabelnfortschritt`
  ADD PRIMARY KEY (`user_id`,`vokabel_id`);

--
-- Indizes für die Tabelle `vokabeln_sprachauswahl`
--
ALTER TABLE `vokabeln_sprachauswahl`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `vorschlaege`
--
ALTER TABLE `vorschlaege`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `account_infos`
--
ALTER TABLE `account_infos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `adressbuch`
--
ALTER TABLE `adressbuch`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `benutzer`
--
ALTER TABLE `benutzer`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `blogkategorien`
--
ALTER TABLE `blogkategorien`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `blogtexte`
--
ALTER TABLE `blogtexte`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key';

--
-- AUTO_INCREMENT für Tabelle `blog_kommentare`
--
ALTER TABLE `blog_kommentare`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `docu`
--
ALTER TABLE `docu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key', AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `eventguests`
--
ALTER TABLE `eventguests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `eventinvitecodes`
--
ALTER TABLE `eventinvitecodes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `eventlist`
--
ALTER TABLE `eventlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `eventtexts`
--
ALTER TABLE `eventtexts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `finanzen_konten`
--
ALTER TABLE `finanzen_konten`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `finanzen_monatsabschluss`
--
ALTER TABLE `finanzen_monatsabschluss`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `finanzen_umsaetze`
--
ALTER TABLE `finanzen_umsaetze`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `gwcosts`
--
ALTER TABLE `gwcosts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `gwmatlist`
--
ALTER TABLE `gwmatlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=422;

--
-- AUTO_INCREMENT für Tabelle `gwusersmats`
--
ALTER TABLE `gwusersmats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `gw_accounts`
--
ALTER TABLE `gw_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `gw_chars`
--
ALTER TABLE `gw_chars`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `hardwaredefinition`
--
ALTER TABLE `hardwaredefinition`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `hardwaredeliverers`
--
ALTER TABLE `hardwaredeliverers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `hardwareentries`
--
ALTER TABLE `hardwareentries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `hardwaregarantietypes`
--
ALTER TABLE `hardwaregarantietypes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `hardwaremanufacturers`
--
ALTER TABLE `hardwaremanufacturers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `hardwaretypes`
--
ALTER TABLE `hardwaretypes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT für Tabelle `log`
--
ALTER TABLE `log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `registercode`
--
ALTER TABLE `registercode`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `rightkategorien`
--
ALTER TABLE `rightkategorien`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT für Tabelle `uebersicht_kacheln`
--
ALTER TABLE `uebersicht_kacheln`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT für Tabelle `userrights`
--
ALTER TABLE `userrights`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT für Tabelle `vokabelkategorien`
--
ALTER TABLE `vokabelkategorien`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT für Tabelle `vokabelliste`
--
ALTER TABLE `vokabelliste`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT für Tabelle `vokabeln_sprachauswahl`
--
ALTER TABLE `vokabeln_sprachauswahl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `vorschlaege`
--
ALTER TABLE `vorschlaege`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
