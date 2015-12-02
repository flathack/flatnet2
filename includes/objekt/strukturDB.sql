-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Erstellungszeit: 02. Dez 2015 um 08:12
-- Server-Version: 10.1.8-MariaDB
-- PHP-Version: 5.6.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `flathacksql1`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `account_infos`
--

CREATE TABLE `account_infos` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `besitzer` int(11) NOT NULL,
  `attribut` varchar(250) NOT NULL,
  `wert` varchar(250) NOT NULL,
  `account` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Tabellenstruktur für Tabelle `adressbuch`
--

CREATE TABLE `adressbuch` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `vorname` varchar(30) COLLATE latin1_german1_ci NOT NULL,
  `nachname` varchar(30) COLLATE latin1_german1_ci NOT NULL,
  `strasse` varchar(40) COLLATE latin1_german1_ci NOT NULL,
  `hausnummer` varchar(15) COLLATE latin1_german1_ci NOT NULL,
  `postleitzahl` int(5) NOT NULL,
  `bundesland` varchar(30) COLLATE latin1_german1_ci NOT NULL,
  `land` varchar(50) COLLATE latin1_german1_ci NOT NULL,
  `telefon1` varchar(50) COLLATE latin1_german1_ci NOT NULL,
  `telefon2` varchar(50) COLLATE latin1_german1_ci NOT NULL,
  `telefon3` varchar(50) COLLATE latin1_german1_ci NOT NULL,
  `telefon4` varchar(50) COLLATE latin1_german1_ci NOT NULL,
  `telefon1art` varchar(50) COLLATE latin1_german1_ci NOT NULL,
  `telefon2art` varchar(50) COLLATE latin1_german1_ci NOT NULL,
  `telefon3art` varchar(50) COLLATE latin1_german1_ci NOT NULL,
  `telefon4art` varchar(50) COLLATE latin1_german1_ci NOT NULL,
  `skype` varchar(50) COLLATE latin1_german1_ci NOT NULL,
  `facebook` varchar(100) COLLATE latin1_german1_ci NOT NULL,
  `notizen` text COLLATE latin1_german1_ci NOT NULL,
  `fax` varchar(50) COLLATE latin1_german1_ci NOT NULL,
  `gruppe` varchar(50) COLLATE latin1_german1_ci NOT NULL,
  `email` varchar(50) COLLATE latin1_german1_ci NOT NULL,
  `geburtstag` date NOT NULL,
  `stadt` varchar(50) COLLATE latin1_german1_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `benutzer` (
  `id` int(10) NOT NULL,
  `Name` varchar(20) NOT NULL,
  `Passwort` char(128) NOT NULL COMMENT 'als MD5 Hash',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `rights` bigint(20) NOT NULL COMMENT 'Rechte des Benutzers',
  `forumRights` bigint(20) NOT NULL COMMENT 'Rechte zum Anzeigen von Inhalten im Forum',
  `versuche` int(3) DEFAULT NULL COMMENT 'Versuche für den Login',
  `realName` varchar(50) NOT NULL COMMENT 'Echter Name des Benutzers',
  `titel` varchar(250) NOT NULL COMMENT 'Titel des Benutzers'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Daten für Tabelle `benutzer`
--

INSERT INTO `benutzer` (`id`, `Name`, `Passwort`, `timestamp`, `rights`, `forumRights`, `versuche`, `realName`, `titel`) VALUES
(1, 'admin', 'fa818419cd8c85cecba515a276ae6977', '2014-03-08 08:29:51', 1, 127, 0, 'SuperAdmin', 'Administrator');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `blogkategorien`
--

CREATE TABLE `blogkategorien` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `kategorie` varchar(254) COLLATE utf8_unicode_ci NOT NULL,
  `beschreibung` varchar(300) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Beschreibung der Kategorien',
  `rightPotenz` int(11) NOT NULL COMMENT 'Potenz zur Berechnung des Wertes, Potenz von 2',
  `rightWert` bigint(20) NOT NULL COMMENT 'Wert der Potenz, wenn sie ausgerechnet ist.',
  `sortierung` int(11) DEFAULT NULL COMMENT 'Sortierung zur Anzeige im Forum'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Tabellenstruktur für Tabelle `blog_kommentare`
--

CREATE TABLE `blog_kommentare` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `autor` int(11) NOT NULL,
  `text` text NOT NULL,
  `blogid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Tabellenstruktur für Tabelle `docu`
--

CREATE TABLE `docu` (
  `id` int(11) NOT NULL COMMENT 'Primary Key',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Erstellungsdatum des Eintrags',
  `text` text NOT NULL COMMENT 'Text des Eintrags',
  `autor` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='hilfe.php';

--
-- Tabellenstruktur für Tabelle `fahrkosten`
--

CREATE TABLE `fahrkosten` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `besitzer` int(11) NOT NULL,
  `datum` date NOT NULL,
  `fahrart` varchar(250) NOT NULL,
  `ziel` varchar(250) NOT NULL,
  `notizen` mediumtext NOT NULL,
  `spritpreis` decimal(10,3) NOT NULL COMMENT 'Spritpreis am Tag der Eintragung',
  `fahrrichtung` int(11) NOT NULL COMMENT 'Hin und Rückfahrt?'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Tabellenstruktur für Tabelle `fahrkostenziele`
--

CREATE TABLE `fahrkostenziele` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `besitzer` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `entfernung` decimal(10,0) NOT NULL COMMENT 'in Kilometer'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Tabellenstruktur für Tabelle `fahrzeuge`
--

CREATE TABLE `fahrzeuge` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `besitzer` int(11) NOT NULL,
  `name` varchar(250) NOT NULL COMMENT 'Vollständiger Name des Fahrzeugs',
  `verbrauch` decimal(10,2) NOT NULL COMMENT 'auf 100 km',
  `name_tag` varchar(250) NOT NULL COMMENT 'Name in der Tabelle Fahrkosten'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Tabellenstruktur für Tabelle `finanzen_jahresabschluss`
--

CREATE TABLE `finanzen_jahresabschluss` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `besitzer` int(11) NOT NULL,
  `jahr` int(11) NOT NULL,
  `wert` decimal(10,2) NOT NULL,
  `konto` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Tabellenstruktur für Tabelle `finanzen_konten`
--

CREATE TABLE `finanzen_konten` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `konto` varchar(250) NOT NULL,
  `besitzer` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
  `umsatzName` varchar(250) NOT NULL,
  `umsatzWert` decimal(10,2) NOT NULL,
  `datum` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Tabellenstruktur für Tabelle `gwcosts`
--

CREATE TABLE `gwcosts` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `besitzer` int(11) NOT NULL,
  `text` varchar(250) NOT NULL,
  `wert` decimal(10,2) NOT NULL,
  `kaufdat` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Tabellenstruktur für Tabelle `gwmatlist`
--

CREATE TABLE `gwmatlist` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `matID` int(11) NOT NULL,
  `matName` varchar(254) NOT NULL,
  `matPrice` decimal(10,0) DEFAULT NULL,
  `kategorie` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Tabellenstruktur für Tabelle `gw_accounts`
--

CREATE TABLE `gw_accounts` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `besitzer` int(11) NOT NULL,
  `account` int(11) NOT NULL,
  `mail` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Tabellenstruktur für Tabelle `gw_animals`
--

CREATE TABLE `gw_animals` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `tierName` varchar(254) NOT NULL,
  `tierStandort` varchar(254) NOT NULL,
  `nameWegmarke` varchar(254) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Tabellenstruktur für Tabelle `gw_chars`
--

CREATE TABLE `gw_chars` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `besitzer` int(11) NOT NULL,
  `name` varchar(254) COLLATE utf8_unicode_ci NOT NULL,
  `geboren` date NOT NULL,
  `rasse` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `klasse` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `stufe` tinyint(3) NOT NULL,
  `handwerk1` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `handwerk2` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `handwerk1stufe` smallint(3) NOT NULL,
  `handwerk2stufe` smallint(3) NOT NULL,
  `erkundung` smallint(3) NOT NULL,
  `spielstunden` smallint(6) NOT NULL,
  `notizen` text COLLATE utf8_unicode_ci NOT NULL,
  `account` int(11) DEFAULT NULL COMMENT 'Welchem Account dieser Charakter zugeordnet ist'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Tabellenstruktur für Tabelle `inventar`
--

CREATE TABLE `inventar` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `name` varchar(254) NOT NULL,
  `kaufdat` date NOT NULL,
  `preis` float DEFAULT NULL,
  `kaufort` varchar(254) NOT NULL,
  `standort` varchar(254) NOT NULL,
  `bestellnummer` varchar(254) NOT NULL,
  `kategorie` varchar(254) NOT NULL,
  `status` int(2) DEFAULT NULL,
  `garantie` date NOT NULL,
  `verkäufer` varchar(254) NOT NULL,
  `menge` int(11) NOT NULL,
  `notizen` text NOT NULL,
  `besitzer` varchar(254) NOT NULL,
  `ersteller` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tabellenstruktur für Tabelle `inventar_hilfe`
--

CREATE TABLE `inventar_hilfe` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `statusID` int(11) NOT NULL COMMENT 'ID des Status',
  `wert` varchar(254) NOT NULL COMMENT 'der Dazugehörige Wert'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Regelt alle Hilfsvariablen für das Inventar.';

--
-- Daten für Tabelle `inventar_hilfe`
--

INSERT INTO `inventar_hilfe` (`id`, `timestamp`, `statusID`, `wert`) VALUES
(1, '2014-09-18 08:09:57', 0, 'Alle'),
(2, '2014-09-18 08:09:57', 1, 'In Betrieb'),
(3, '2014-09-18 08:10:56', 2, 'Kaputt'),
(4, '2014-09-18 08:10:56', 3, 'Verkauft'),
(6, '2014-09-18 08:10:56', 5, 'Bestellt'),
(7, '2014-09-18 08:10:56', 6, 'Rücksendung'),
(8, '2014-09-18 08:11:47', 7, 'Verschenkt'),
(10, '2014-09-18 08:11:53', 9, 'Im Auftrag bestellt'),
(11, '2014-09-18 18:34:20', 10, 'Wunschliste');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `learnkategorie`
--

CREATE TABLE `learnkategorie` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `kategorie` varchar(250) NOT NULL,
  `besitzer` int(11) NOT NULL,
  `public` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Tabellenstruktur für Tabelle `learnlernkarte`
--

CREATE TABLE `learnlernkarte` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `besitzer` int(11) NOT NULL,
  `kategorie` int(11) NOT NULL,
  `frage` varchar(250) NOT NULL,
  `loesung` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Tabellenstruktur für Tabelle `registercode`
--

CREATE TABLE `registercode` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `code` varchar(250) NOT NULL,
  `used` int(11) NOT NULL COMMENT 'Wurde er benutzt',
  `usageTimes` int(11) NOT NULL,
  `usedBy` varchar(250) NOT NULL COMMENT 'Welcher Benutzer hat ihn eingelöst.',
  `ipadress` varchar(15) NOT NULL COMMENT 'Ipadress des Benutzers',
  `rights` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Tabellenstruktur für Tabelle `rightkategorien`
--

CREATE TABLE `rightkategorien` (
  `id` int(11) NOT NULL,
  `name` varchar(254) NOT NULL COMMENT 'Name der Kategorie'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `rightkategorien`
--

INSERT INTO `rightkategorien` (`id`, `name`) VALUES
(1, 'Öffentlich'),
(2, 'Administrator'),
(3, 'Forum'),
(4, 'Ankündigungen'),
(5, 'Guildwars'),
(6, 'Finanzen'),
(7, 'Adressbuch'),
(8, 'Fahrkosten'),
(9, 'Starcitizen'),
(10, 'Lernbereich');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `rights`
--

CREATE TABLE `rights` (
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `besitzer` int(11) NOT NULL COMMENT 'Dieser Benutzer hat das Recht',
  `right_id` int(11) NOT NULL COMMENT 'Rechte ID aus der Tabelle userrights'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Daten für Tabelle `rights`
--

INSERT INTO `rights` (`timestamp`, `besitzer`, `right_id`) VALUES
('2015-09-22 12:58:12', 1, 2),
('2015-09-22 12:58:15', 1, 3),
('2015-09-22 12:29:38', 1, 7),
('2015-09-22 12:58:14', 1, 8),
('2015-09-22 13:12:28', 1, 9),
('2015-09-22 12:56:49', 1, 10),
('2015-09-22 12:58:23', 1, 11),
('2015-09-22 12:58:24', 1, 12),
('2015-09-22 12:58:20', 1, 13),
('2015-09-22 12:58:21', 1, 14),
('2015-09-22 12:58:10', 1, 15),
('2015-09-23 09:39:10', 1, 16),
('2015-09-22 12:58:18', 1, 17),
('2015-09-22 12:58:18', 1, 18),
('2015-09-22 12:58:15', 1, 19),
('2015-09-22 12:58:13', 1, 20),
('2015-09-23 09:39:35', 1, 21),
('2015-09-22 12:58:23', 1, 22),
('2015-09-23 05:43:47', 1, 23),
('2015-09-23 09:38:50', 1, 24),
('2015-09-23 05:43:51', 1, 25),
('2015-09-23 05:43:57', 1, 26),
('2015-09-23 05:43:58', 1, 27),
('2015-09-23 05:44:01', 1, 28),
('2015-09-23 05:44:03', 1, 29),
('2015-09-23 05:44:04', 1, 30),
('2015-09-23 05:44:06', 1, 31),
('2015-09-23 05:44:07', 1, 32),
('2015-09-23 05:44:08', 1, 33),
('2015-09-23 05:44:10', 1, 34),
('2015-09-23 05:53:11', 1, 35),
('2015-09-23 05:53:02', 1, 36),
('2015-09-24 05:50:58', 1, 37),
('2015-09-23 05:59:18', 1, 38),
('2015-09-23 05:59:19', 1, 39),
('2015-09-23 05:59:20', 1, 40),
('2015-09-23 05:59:21', 1, 41),
('2015-09-23 06:02:30', 1, 42),
('2015-09-23 06:08:36', 1, 43),
('2015-09-23 06:08:38', 1, 44),
('2015-09-23 06:14:00', 1, 45),
('2015-09-23 06:13:56', 1, 46),
('2015-09-23 06:13:58', 1, 47),
('2015-09-23 06:16:59', 1, 48),
('2015-09-23 06:17:00', 1, 49),
('2015-09-23 06:17:02', 1, 50),
('2015-09-23 06:36:09', 1, 51),
('2015-09-24 06:47:02', 1, 52),
('2015-09-24 06:47:00', 1, 53),
('2015-09-24 06:44:33', 1, 54),
('2015-09-23 06:36:40', 1, 55),
('2015-09-24 06:46:58', 1, 56),
('2015-09-23 06:36:44', 1, 57),
('2015-09-23 06:36:46', 1, 58),
('2015-09-23 06:44:40', 1, 59),
('2015-09-23 06:54:19', 1, 60),
('2015-09-23 07:15:09', 1, 61),
('2015-09-23 07:15:11', 1, 62),
('2015-09-23 07:49:01', 1, 63),
('2015-09-23 07:49:03', 1, 64),
('2015-09-23 07:48:58', 1, 66),
('2015-09-24 05:56:35', 1, 67),
('2015-09-24 06:21:18', 1, 68),
('2015-11-26 11:13:14', 1, 70);


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `userrights`
--

CREATE TABLE `userrights` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Erstellungsdatum',
  `kategorie` int(11) NOT NULL COMMENT 'Ermöglicht eine Kategorisierung',
  `recht` text COLLATE utf8_unicode_ci NOT NULL,
  `potenz` int(11) NOT NULL,
  `dezimalerWert` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Daten für Tabelle `userrights`
--

INSERT INTO `userrights` (`id`, `timestamp`, `kategorie`, `recht`, `potenz`, `dezimalerWert`) VALUES
(2, '2015-08-20 19:29:03', 3, 'SEHEN: Forum', 1, 2),
(3, '2015-08-20 19:29:10', 5, 'Guildwarsbereich sehen', 2, 4),
(7, '2015-08-20 19:29:25', 1, 'Übersicht, Profil sehen', 0, 1),
(8, '2015-09-23 07:47:26', 4, 'SEHEN: Ankündigungshauptseite', 6, 64),
(9, '2015-08-20 19:29:39', 5, 'DO: Allgemeines Schreibrecht innerhalb des GW Bereichs', 5, 32),
(10, '2015-08-20 19:29:50', 2, 'SEHEN: Betrachten FREMDER nicht veröffentlichter Foreneinträge', 4, 16),
(11, '2015-09-22 11:42:43', 8, 'WATCH: Betrachten des Fahrkosten-Bereichs', 7, 128),
(12, '2015-09-22 11:43:06', 8, 'DO: Fahrkostenbereich bearbeiten', 8, 256),
(13, '2015-08-20 19:30:08', 7, 'WATCH: Adressbuch betrachten', 9, 512),
(14, '2015-08-20 19:30:13', 7, 'DO: Einträge im Adressbuch bearbeiten', 10, 1024),
(15, '2015-09-23 06:10:11', 2, 'Rechteverwaltung: Darf Berechtigungen erstellen', 11, 2048),
(16, '2015-08-20 19:30:28', 5, 'DO: Handwerksmaterialien GW Bereich', 12, 4096),
(17, '2015-08-20 19:30:34', 6, 'SEHEN: Finanzbereich', 13, 8192),
(18, '2015-08-20 19:30:39', 6, 'DO: Finanzbereich Schreibrechte', 14, 16384),
(19, '2015-09-23 07:47:31', 4, 'DO: Ankündigungen erstellen', 15, 32768),
(20, '2015-08-20 19:30:52', 3, 'DO: Forum Schreibrechte', 16, 65536),
(21, '2015-08-20 19:30:58', 5, 'ADMIN: Guildwarsbereich', 17, 131072),
(22, '2015-08-20 19:31:07', 7, 'SEHEN: Kalender im Adressbuch', 18, 262144),
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
(34, '2015-09-23 05:48:21', 3, 'DO: Benutzer darf seine eigenen Antworten in Themen löschen', 0, 0),
(35, '0000-00-00 00:00:00', 3, 'Forum-Admin: Benutzer darf Antworten in Themen löschen (alle Benutzer)', 0, 0),
(36, '0000-00-00 00:00:00', 2, 'SEHEN: Administrationsbereich', 0, 0),
(37, '2015-09-23 06:00:19', 2, 'Benutzerverwaltung: Benutzer darf die Benutzerverwaltung anzeigen', 0, 0),
(38, '2015-09-23 06:00:14', 2, 'Benutzerverwaltung: Benutzer darf neue Benutzer anlegen', 0, 0),
(39, '2015-09-23 06:00:09', 2, 'Benutzerverwaltung: Benutzer darf bestehende Benutzer bearbeiten', 0, 0),
(40, '2015-09-23 06:00:05', 2, 'Benutzerverwaltung: Benutzer darf bestehende Benutzeraccounts löschen', 0, 0),
(41, '2015-09-23 05:59:56', 2, 'Benutzerverwaltung: Benutzer darf alle Informationen eines Benutzers anzeigen', 0, 0),
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
(60, '0000-00-00 00:00:00', 8, 'SEHEN: Statisik im Fahrtkosten-Bereich', 0, 0),
(61, '0000-00-00 00:00:00', 5, 'SEHEN: Liste der GW Charakter', 0, 0),
(62, '0000-00-00 00:00:00', 5, 'SEHEN: Kalender im GW Bereich', 0, 0),
(63, '0000-00-00 00:00:00', 5, 'SEHEN & DO: Kostenbereich Guildwars', 0, 0),
(64, '0000-00-00 00:00:00', 5, 'SEHEN: Statistik zu allen Charaktern', 0, 0),
(65, '0000-00-00 00:00:00', 9, 'SEHEN: Starcitizen Hauptseite', 0, 0),
(66, '0000-00-00 00:00:00', 4, 'LÖSCHEN: Ankündigungen', 0, 0),
(67, '2015-09-24 05:55:15', 2, 'SEHEN: Links zu externen Ressourcen', 0, 0),
(68, '2015-09-24 06:20:49', 2, 'SEHEN: Rechtekategorie-Verwaltung', 0, 0),
(69, '2015-11-05 05:39:56', 5, 'DO: Darf neue Materialien im Handwerksbereich anlegen und verwalten', 0, 0),
(70, '', 10, 'Darf den Lernbereich sehen', 0, 0),
(71, '', 10, 'Darf im Lernbereich Inhalte erstellen', 0, 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `vorschlaege`
--

CREATE TABLE `vorschlaege` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `autor` int(11) NOT NULL,
  `text` text NOT NULL,
  `status` varchar(20) NOT NULL,
  `ipadress` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
-- Indizes für die Tabelle `fahrkosten`
--
ALTER TABLE `fahrkosten`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `fahrkostenziele`
--
ALTER TABLE `fahrkostenziele`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `fahrzeuge`
--
ALTER TABLE `fahrzeuge`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `finanzen_jahresabschluss`
--
ALTER TABLE `finanzen_jahresabschluss`
  ADD PRIMARY KEY (`id`);

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
-- Indizes für die Tabelle `gw_animals`
--
ALTER TABLE `gw_animals`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `gw_chars`
--
ALTER TABLE `gw_chars`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `inventar`
--
ALTER TABLE `inventar`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `inventar_hilfe`
--
ALTER TABLE `inventar_hilfe`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `learnkategorie`
--
ALTER TABLE `learnkategorie`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `learnlernkarte`
--
ALTER TABLE `learnlernkarte`
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
-- Indizes für die Tabelle `userrights`
--
ALTER TABLE `userrights`
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
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key';
--
-- AUTO_INCREMENT für Tabelle `fahrkosten`
--
ALTER TABLE `fahrkosten`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `fahrkostenziele`
--
ALTER TABLE `fahrkostenziele`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `fahrzeuge`
--
ALTER TABLE `fahrzeuge`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `finanzen_jahresabschluss`
--
ALTER TABLE `finanzen_jahresabschluss`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
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
-- AUTO_INCREMENT für Tabelle `gw_animals`
--
ALTER TABLE `gw_animals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `gw_chars`
--
ALTER TABLE `gw_chars`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `inventar`
--
ALTER TABLE `inventar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `inventar_hilfe`
--
ALTER TABLE `inventar_hilfe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `learnkategorie`
--
ALTER TABLE `learnkategorie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `learnlernkarte`
--
ALTER TABLE `learnlernkarte`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `userrights`
--
ALTER TABLE `userrights`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `vorschlaege`
--
ALTER TABLE `vorschlaege`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
