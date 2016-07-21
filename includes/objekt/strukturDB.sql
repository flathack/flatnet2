<<<<<<< HEAD
-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Erstellungszeit: 04. Feb 2016 um 09:02
-- Server-Version: 10.1.9-MariaDB
-- PHP-Version: 5.6.15

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
  `attribut` varchar(250) COLLATE latin1_general_ci NOT NULL,
  `wert` varchar(250) COLLATE latin1_general_ci NOT NULL,
  `account` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `adressbuch`
--

CREATE TABLE `adressbuch` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `vorname` varchar(30) COLLATE latin1_general_ci NOT NULL,
  `nachname` varchar(30) COLLATE latin1_general_ci NOT NULL,
  `strasse` varchar(40) COLLATE latin1_general_ci NOT NULL,
  `hausnummer` varchar(15) COLLATE latin1_general_ci NOT NULL,
  `postleitzahl` int(5) NOT NULL,
  `bundesland` varchar(30) COLLATE latin1_general_ci NOT NULL,
  `land` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `telefon1` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `telefon2` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `telefon3` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `telefon4` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `telefon1art` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `telefon2art` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `telefon3art` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `telefon4art` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `skype` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `facebook` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `notizen` text COLLATE latin1_general_ci NOT NULL,
  `fax` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `gruppe` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `email` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `geburtstag` date NOT NULL,
  `stadt` varchar(50) COLLATE latin1_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `benutzer`
--

CREATE TABLE `benutzer` (
  `id` int(10) NOT NULL,
  `Name` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `Passwort` char(128) COLLATE latin1_general_ci NOT NULL COMMENT 'als MD5 Hash',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `rights` bigint(20) NOT NULL COMMENT 'Rechte des Benutzers',
  `forumRights` bigint(20) NOT NULL COMMENT 'Rechte zum Anzeigen von Inhalten im Forum',
  `versuche` int(3) DEFAULT NULL COMMENT 'Versuche für den Login',
  `realName` varchar(50) COLLATE latin1_general_ci NOT NULL COMMENT 'Echter Name des Benutzers',
  `titel` varchar(250) COLLATE latin1_general_ci NOT NULL COMMENT 'Titel des Benutzers'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `blogkategorien`
--

CREATE TABLE `blogkategorien` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `kategorie` varchar(254) COLLATE latin1_general_ci NOT NULL,
  `beschreibung` varchar(300) COLLATE latin1_general_ci NOT NULL COMMENT 'Beschreibung der Kategorien',
  `rightPotenz` int(11) NOT NULL COMMENT 'Potenz zur Berechnung des Wertes, Potenz von 2',
  `rightWert` bigint(20) NOT NULL COMMENT 'Wert der Potenz, wenn sie ausgerechnet ist.',
  `sortierung` int(11) DEFAULT NULL COMMENT 'Sortierung zur Anzeige im Forum'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `blog_kommentare`
--

CREATE TABLE `blog_kommentare` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `autor` int(11) NOT NULL,
  `text` text COLLATE latin1_general_ci NOT NULL,
  `blogid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `docu`
--

CREATE TABLE `docu` (
  `id` int(11) NOT NULL COMMENT 'Primary Key',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Erstellungsdatum des Eintrags',
  `text` text COLLATE latin1_general_ci NOT NULL COMMENT 'Text des Eintrags',
  `autor` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci COMMENT='hilfe.php';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fahrkosten`
--

CREATE TABLE `fahrkosten` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `besitzer` int(11) NOT NULL,
  `datum` date NOT NULL,
  `fahrart` varchar(250) COLLATE latin1_general_ci NOT NULL,
  `ziel` varchar(250) COLLATE latin1_general_ci NOT NULL,
  `notizen` mediumtext COLLATE latin1_general_ci NOT NULL,
  `spritpreis` decimal(10,3) NOT NULL COMMENT 'Spritpreis am Tag der Eintragung',
  `fahrrichtung` int(11) NOT NULL COMMENT 'Hin und Rückfahrt'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fahrkostenziele`
--

CREATE TABLE `fahrkostenziele` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `besitzer` int(11) NOT NULL,
  `name` varchar(250) COLLATE latin1_general_ci NOT NULL,
  `entfernung` decimal(10,0) NOT NULL COMMENT 'in Kilometer'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fahrzeuge`
--

CREATE TABLE `fahrzeuge` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `besitzer` int(11) NOT NULL,
  `name` varchar(250) COLLATE latin1_general_ci NOT NULL COMMENT 'Vollständiger Name des Fahrzeugs',
  `verbrauch` decimal(10,2) NOT NULL COMMENT 'auf 100 km',
  `name_tag` varchar(250) COLLATE latin1_general_ci NOT NULL COMMENT 'Name in der Tabelle Fahrkosten'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `finanzen_konten`
--

CREATE TABLE `finanzen_konten` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `konto` varchar(250) COLLATE latin1_general_ci NOT NULL,
  `besitzer` int(11) NOT NULL,
  `aktiv` tinyint(4) NOT NULL,
  `notizen` text COLLATE latin1_general_ci NOT NULL,
  `art` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

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
  `umsatzName` varchar(250) COLLATE latin1_general_ci NOT NULL,
  `umsatzWert` decimal(10,2) NOT NULL,
  `datum` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `gwcosts`
--

CREATE TABLE `gwcosts` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `besitzer` int(11) NOT NULL,
  `text` varchar(250) COLLATE latin1_general_ci NOT NULL,
  `wert` decimal(10,2) NOT NULL,
  `kaufdat` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `gwmatlist`
--

CREATE TABLE `gwmatlist` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `matID` int(11) NOT NULL,
  `matName` varchar(254) COLLATE latin1_general_ci NOT NULL,
  `matPrice` decimal(10,0) DEFAULT NULL,
  `kategorie` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `gw_accounts`
--

CREATE TABLE `gw_accounts` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `besitzer` int(11) NOT NULL,
  `account` int(11) NOT NULL,
  `mail` varchar(250) COLLATE latin1_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `gw_chars`
--

CREATE TABLE `gw_chars` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `besitzer` int(11) NOT NULL,
  `name` varchar(254) COLLATE latin1_general_ci NOT NULL,
  `geboren` date NOT NULL,
  `rasse` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `klasse` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `stufe` tinyint(3) NOT NULL,
  `handwerk1` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `handwerk2` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `handwerk1stufe` smallint(3) NOT NULL,
  `handwerk2stufe` smallint(3) NOT NULL,
  `erkundung` smallint(3) NOT NULL,
  `spielstunden` smallint(6) NOT NULL,
  `notizen` text COLLATE latin1_general_ci NOT NULL,
  `account` int(11) DEFAULT NULL COMMENT 'Welchem Account dieser Charakter zugeordnet ist'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `learnkategorie`
--

CREATE TABLE `learnkategorie` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `kategorie` varchar(250) COLLATE latin1_general_ci NOT NULL,
  `besitzer` int(11) NOT NULL,
  `public` tinyint(1) NOT NULL,
  `verfallen` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `learnlernkarte`
--

CREATE TABLE `learnlernkarte` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `besitzer` int(11) NOT NULL,
  `kategorie` int(11) NOT NULL,
  `frage` varchar(250) COLLATE latin1_general_ci NOT NULL,
  `loesung` text COLLATE latin1_general_ci NOT NULL,
  `verfallen` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `lernstatus`
--

CREATE TABLE `lernstatus` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `besitzer` int(11) NOT NULL,
  `lern_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `changedate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `registercode`
--

CREATE TABLE `registercode` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `code` varchar(250) COLLATE latin1_general_ci NOT NULL,
  `used` int(11) NOT NULL COMMENT 'Wurde er benutzt',
  `usageTimes` int(11) NOT NULL,
  `usedBy` varchar(250) COLLATE latin1_general_ci NOT NULL COMMENT 'Welcher Benutzer hat ihn eingelöst.',
  `ipadress` varchar(15) COLLATE latin1_general_ci NOT NULL COMMENT 'Ipadress des Benutzers',
  `rights` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `rightkategorien`
--

CREATE TABLE `rightkategorien` (
  `id` int(11) NOT NULL,
  `name` varchar(254) COLLATE latin1_general_ci NOT NULL COMMENT 'Name der Kategorie'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `rights`
--

CREATE TABLE `rights` (
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `besitzer` int(11) NOT NULL COMMENT 'Dieser Benutzer hat das Recht',
  `right_id` int(11) NOT NULL COMMENT 'Rechte ID aus der Tabelle userrights'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `uebersicht_kacheln`
--

CREATE TABLE `uebersicht_kacheln` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `name` varchar(250) COLLATE latin1_general_ci NOT NULL COMMENT 'Name der Kachel',
  `link` varchar(250) COLLATE latin1_general_ci NOT NULL COMMENT 'Link zur Startseite der Kachel',
  `beschreibung` varchar(250) COLLATE latin1_general_ci NOT NULL COMMENT 'Beschreibung der Kachel',
  `sortierung` int(11) NOT NULL COMMENT 'Sortierung',
  `active` tinyint(4) DEFAULT NULL COMMENT 'Kachel aktiviert',
  `cssID` varchar(250) COLLATE latin1_general_ci NOT NULL,
  `rightID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `userrights`
--

CREATE TABLE `userrights` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Erstellungsdatum',
  `kategorie` int(11) NOT NULL COMMENT 'Ermöglicht eine Kategorisierung',
  `recht` text COLLATE latin1_general_ci NOT NULL,
  `potenz` int(11) NOT NULL,
  `dezimalerWert` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `vorschlaege`
--

CREATE TABLE `vorschlaege` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `autor` int(11) NOT NULL,
  `text` text COLLATE latin1_general_ci NOT NULL,
  `status` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `ipadress` varchar(15) COLLATE latin1_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

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
-- Indizes für die Tabelle `lernstatus`
--
ALTER TABLE `lernstatus`
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
-- AUTO_INCREMENT für Tabelle `gw_chars`
--
ALTER TABLE `gw_chars`
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
-- AUTO_INCREMENT für Tabelle `lernstatus`
--
ALTER TABLE `lernstatus`
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
-- AUTO_INCREMENT für Tabelle `uebersicht_kacheln`
--
ALTER TABLE `uebersicht_kacheln`
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
=======
-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Erstellungszeit: 04. Feb 2016 um 09:02
-- Server-Version: 10.1.9-MariaDB
-- PHP-Version: 5.6.15

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
  `attribut` varchar(250) COLLATE latin1_general_ci NOT NULL,
  `wert` varchar(250) COLLATE latin1_general_ci NOT NULL,
  `account` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `adressbuch`
--

CREATE TABLE `adressbuch` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `vorname` varchar(30) COLLATE latin1_general_ci NOT NULL,
  `nachname` varchar(30) COLLATE latin1_general_ci NOT NULL,
  `strasse` varchar(40) COLLATE latin1_general_ci NOT NULL,
  `hausnummer` varchar(15) COLLATE latin1_general_ci NOT NULL,
  `postleitzahl` int(5) NOT NULL,
  `bundesland` varchar(30) COLLATE latin1_general_ci NOT NULL,
  `land` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `telefon1` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `telefon2` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `telefon3` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `telefon4` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `telefon1art` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `telefon2art` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `telefon3art` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `telefon4art` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `skype` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `facebook` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `notizen` text COLLATE latin1_general_ci NOT NULL,
  `fax` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `gruppe` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `email` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `geburtstag` date NOT NULL,
  `stadt` varchar(50) COLLATE latin1_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `benutzer`
--

CREATE TABLE `benutzer` (
  `id` int(10) NOT NULL,
  `Name` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `Passwort` char(128) COLLATE latin1_general_ci NOT NULL COMMENT 'als MD5 Hash',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `rights` bigint(20) NOT NULL COMMENT 'Rechte des Benutzers',
  `forumRights` bigint(20) NOT NULL COMMENT 'Rechte zum Anzeigen von Inhalten im Forum',
  `versuche` int(3) DEFAULT NULL COMMENT 'Versuche für den Login',
  `realName` varchar(50) COLLATE latin1_general_ci NOT NULL COMMENT 'Echter Name des Benutzers',
  `titel` varchar(250) COLLATE latin1_general_ci NOT NULL COMMENT 'Titel des Benutzers'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `blogkategorien`
--

CREATE TABLE `blogkategorien` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `kategorie` varchar(254) COLLATE latin1_general_ci NOT NULL,
  `beschreibung` varchar(300) COLLATE latin1_general_ci NOT NULL COMMENT 'Beschreibung der Kategorien',
  `rightPotenz` int(11) NOT NULL COMMENT 'Potenz zur Berechnung des Wertes, Potenz von 2',
  `rightWert` bigint(20) NOT NULL COMMENT 'Wert der Potenz, wenn sie ausgerechnet ist.',
  `sortierung` int(11) DEFAULT NULL COMMENT 'Sortierung zur Anzeige im Forum'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `blog_kommentare`
--

CREATE TABLE `blog_kommentare` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `autor` int(11) NOT NULL,
  `text` text COLLATE latin1_general_ci NOT NULL,
  `blogid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `docu`
--

CREATE TABLE `docu` (
  `id` int(11) NOT NULL COMMENT 'Primary Key',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Erstellungsdatum des Eintrags',
  `text` text COLLATE latin1_general_ci NOT NULL COMMENT 'Text des Eintrags',
  `autor` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci COMMENT='hilfe.php';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fahrkosten`
--

CREATE TABLE `fahrkosten` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `besitzer` int(11) NOT NULL,
  `datum` date NOT NULL,
  `fahrart` varchar(250) COLLATE latin1_general_ci NOT NULL,
  `ziel` varchar(250) COLLATE latin1_general_ci NOT NULL,
  `notizen` mediumtext COLLATE latin1_general_ci NOT NULL,
  `spritpreis` decimal(10,3) NOT NULL COMMENT 'Spritpreis am Tag der Eintragung',
  `fahrrichtung` int(11) NOT NULL COMMENT 'Hin und Rückfahrt'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fahrkostenziele`
--

CREATE TABLE `fahrkostenziele` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `besitzer` int(11) NOT NULL,
  `name` varchar(250) COLLATE latin1_general_ci NOT NULL,
  `entfernung` decimal(10,0) NOT NULL COMMENT 'in Kilometer'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fahrzeuge`
--

CREATE TABLE `fahrzeuge` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `besitzer` int(11) NOT NULL,
  `name` varchar(250) COLLATE latin1_general_ci NOT NULL COMMENT 'Vollständiger Name des Fahrzeugs',
  `verbrauch` decimal(10,2) NOT NULL COMMENT 'auf 100 km',
  `name_tag` varchar(250) COLLATE latin1_general_ci NOT NULL COMMENT 'Name in der Tabelle Fahrkosten'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `finanzen_konten`
--

CREATE TABLE `finanzen_konten` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `konto` varchar(250) COLLATE latin1_general_ci NOT NULL,
  `besitzer` int(11) NOT NULL,
  `aktiv` tinyint(4) NOT NULL,
  `notizen` text COLLATE latin1_general_ci NOT NULL,
  `art` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

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
  `umsatzName` varchar(250) COLLATE latin1_general_ci NOT NULL,
  `umsatzWert` decimal(10,2) NOT NULL,
  `datum` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `gwcosts`
--

CREATE TABLE `gwcosts` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `besitzer` int(11) NOT NULL,
  `text` varchar(250) COLLATE latin1_general_ci NOT NULL,
  `wert` decimal(10,2) NOT NULL,
  `kaufdat` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `gwmatlist`
--

CREATE TABLE `gwmatlist` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `matID` int(11) NOT NULL,
  `matName` varchar(254) COLLATE latin1_general_ci NOT NULL,
  `matPrice` decimal(10,0) DEFAULT NULL,
  `kategorie` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `gw_accounts`
--

CREATE TABLE `gw_accounts` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `besitzer` int(11) NOT NULL,
  `account` int(11) NOT NULL,
  `mail` varchar(250) COLLATE latin1_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `gw_chars`
--

CREATE TABLE `gw_chars` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `besitzer` int(11) NOT NULL,
  `name` varchar(254) COLLATE latin1_general_ci NOT NULL,
  `geboren` date NOT NULL,
  `rasse` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `klasse` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `stufe` tinyint(3) NOT NULL,
  `handwerk1` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `handwerk2` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `handwerk1stufe` smallint(3) NOT NULL,
  `handwerk2stufe` smallint(3) NOT NULL,
  `erkundung` smallint(3) NOT NULL,
  `spielstunden` smallint(6) NOT NULL,
  `notizen` text COLLATE latin1_general_ci NOT NULL,
  `account` int(11) DEFAULT NULL COMMENT 'Welchem Account dieser Charakter zugeordnet ist'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `learnkategorie`
--

CREATE TABLE `learnkategorie` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `kategorie` varchar(250) COLLATE latin1_general_ci NOT NULL,
  `besitzer` int(11) NOT NULL,
  `public` tinyint(1) NOT NULL,
  `verfallen` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `learnlernkarte`
--

CREATE TABLE `learnlernkarte` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `besitzer` int(11) NOT NULL,
  `kategorie` int(11) NOT NULL,
  `frage` varchar(250) COLLATE latin1_general_ci NOT NULL,
  `loesung` text COLLATE latin1_general_ci NOT NULL,
  `verfallen` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `lernstatus`
--

CREATE TABLE `lernstatus` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `besitzer` int(11) NOT NULL,
  `lern_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `changedate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `registercode`
--

CREATE TABLE `registercode` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `code` varchar(250) COLLATE latin1_general_ci NOT NULL,
  `used` int(11) NOT NULL COMMENT 'Wurde er benutzt',
  `usageTimes` int(11) NOT NULL,
  `usedBy` varchar(250) COLLATE latin1_general_ci NOT NULL COMMENT 'Welcher Benutzer hat ihn eingelöst.',
  `ipadress` varchar(15) COLLATE latin1_general_ci NOT NULL COMMENT 'Ipadress des Benutzers',
  `rights` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `rightkategorien`
--

CREATE TABLE `rightkategorien` (
  `id` int(11) NOT NULL,
  `name` varchar(254) COLLATE latin1_general_ci NOT NULL COMMENT 'Name der Kategorie'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `rights`
--

CREATE TABLE `rights` (
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `besitzer` int(11) NOT NULL COMMENT 'Dieser Benutzer hat das Recht',
  `right_id` int(11) NOT NULL COMMENT 'Rechte ID aus der Tabelle userrights'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `uebersicht_kacheln`
--

CREATE TABLE `uebersicht_kacheln` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `name` varchar(250) COLLATE latin1_general_ci NOT NULL COMMENT 'Name der Kachel',
  `link` varchar(250) COLLATE latin1_general_ci NOT NULL COMMENT 'Link zur Startseite der Kachel',
  `beschreibung` varchar(250) COLLATE latin1_general_ci NOT NULL COMMENT 'Beschreibung der Kachel',
  `sortierung` int(11) NOT NULL COMMENT 'Sortierung',
  `active` tinyint(4) DEFAULT NULL COMMENT 'Kachel aktiviert',
  `cssID` varchar(250) COLLATE latin1_general_ci NOT NULL,
  `rightID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `userrights`
--

CREATE TABLE `userrights` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Erstellungsdatum',
  `kategorie` int(11) NOT NULL COMMENT 'Ermöglicht eine Kategorisierung',
  `recht` text COLLATE latin1_general_ci NOT NULL,
  `potenz` int(11) NOT NULL,
  `dezimalerWert` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `vorschlaege`
--

CREATE TABLE `vorschlaege` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `autor` int(11) NOT NULL,
  `text` text COLLATE latin1_general_ci NOT NULL,
  `status` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `ipadress` varchar(15) COLLATE latin1_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

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
-- Indizes für die Tabelle `lernstatus`
--
ALTER TABLE `lernstatus`
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
-- AUTO_INCREMENT für Tabelle `gw_chars`
--
ALTER TABLE `gw_chars`
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
-- AUTO_INCREMENT für Tabelle `lernstatus`
--
ALTER TABLE `lernstatus`
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
-- AUTO_INCREMENT für Tabelle `uebersicht_kacheln`
--
ALTER TABLE `uebersicht_kacheln`
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
>>>>>>> branch 'master' of https://github.com/flathack/flatnet2.git
