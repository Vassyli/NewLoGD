-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Erstellungszeit: 27. Mrz 2015 um 13:31
-- Server Version: 5.6.20
-- PHP-Version: 5.5.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Datenbank: `newlogd`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `navigation`
--

CREATE TABLE IF NOT EXISTS `navigation` (
`id` bigint(20) unsigned NOT NULL,
  `parentid` bigint(20) unsigned DEFAULT NULL,
  `page_id` bigint(20) unsigned NOT NULL,
  `action` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=5 ;

--
-- Daten für Tabelle `navigation`
--

INSERT INTO `navigation` (`id`, `parentid`, `page_id`, `action`, `title`) VALUES
(1, 2, 1, 'about', 'Über LoGD'),
(2, NULL, 1, NULL, 'Neu hier?'),
(3, NULL, 1, 'about_license', 'GNU AGPL 3'),
(4, NULL, 2, 'main', 'Zurück zur Hauptseite');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `page`
--

CREATE TABLE IF NOT EXISTS `page` (
`id` bigint(20) unsigned NOT NULL,
  `type` varchar(255) CHARACTER SET utf8 NOT NULL,
  `action` varchar(255) CHARACTER SET utf8 NOT NULL,
  `title` varchar(255) COLLATE utf8_bin NOT NULL,
  `subtitle` varchar(255) COLLATE utf8_bin NOT NULL,
  `content` text COLLATE utf8_bin NOT NULL,
  `flags` bigint(1) unsigned NOT NULL DEFAULT '3'
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3 ;

--
-- Daten für Tabelle `page`
--

INSERT INTO `page` (`id`, `type`, `action`, `title`, `subtitle`, `content`, `flags`) VALUES
(1, 'node', 'main', 'NewLoGD', 'Willkommen', 'NewLoGD ist eine Neuauflage von The Legend of the Green Dragon (LoGD oder auch LotGD). Es basiert lose auf Seth Ables Text-RPG Legend of the Red Dragon.', 2),
(2, 'node', 'about', 'Über NewLoGD', 'Allgemeines', 'The Legend of the Green Dragon ist MightyEs Remake vom klassischen, 1989 veröffentlichten BBS-Door-Spiel Legend of the Red Dragon (oder LoRD) von Seth Able Robinson. Die exklusiven Rechte an LoRD gehört inzwischen Gameport - weshalb der Inhalt des Remakes The Legend of the Green Dragon praktisch vollständig neu ist, mit Ausnahme ein paar weniger Referenzen wie zum Beispiel die vollbusige Bardame Violet oder der attraktive Barde Seth. Zusätzlich wurden verschiedene Anpassungen vorgenommen, damit LoGD besser spielbar ist als Browserspiel.\r\n\r\nNach dem Release der LoGD-Version 0.9.7+jt und der daraufhin steigenden Beliebtheit des Spiels wurden bereits zahlreiche Mods veröffentlicht und vertrieben, was dank der Open Source Lizenz möglich war. Insbesondere die deutsche Übersetzung des Spiels und die dazugehörenden Modifikationen verbreiteten sich stark, weshalb sich die LoGD-Version 1.0 nie im deutschsprachigen Raum durchsetzen konnte - was mitunter auch and der fehlenden Übersetzung lag, an der deutlich komplizierteren Code-Basis und der inkompatiblen Lizenz.\r\n\r\nAuch wenn inzwischen der kleine Hype im das Browserspiel abgeklungen ist, finden sich nach wie vor zahlreiche Installationen im Netz. Die alte Code-Basis der deutschen Version 0.9.7+jt ext GER hat aber inzwischen aber zahlreiche Probleme, da sie noch auf PHP4 basiert. Auch aktuelle MySQL-Versionen sind nicht mehr vollständig kompatibel, was neuere Installationen unnötig erschwert.\r\n\r\nDas Ziel dieses neuen LoGD-Forks ist es deshalb, basierend auf PHP 5.5 eine neue, primär deutschsprachige Basis zu bilden, die ähnlich viele Features hat wie die 0.9.7+jt ext GER. Anders als das Original versucht dieser Fork ein stark konfigurierbares Framework zu sein, dessen Seiten primär aus einer Datenbank gebildet werden.\r\n\r\nEin weiterer Unterschied ist, dass dieser Fork nicht unter der GNU GPL v2 veröffentlich wird, sondern unter der Affero GPL v3. Diese Version schliesst eine Lücke der GNU GPL v2: Es ist nur erforderlich, bei Code-Weitergabe die vollständige Source weiterzugeben, sondern auch beim zugänglichmachen einer Installation über ein Netzwerk - also dem Internet. Das verbietet Serverbetreibern, ihre Source zu schliessen und vermöglicht einen Rückfluss von Code in den Entwicklungszweig dieses Forks.\r\n\r\nUnd nun - viel Spass mit dem Spiel!', 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `navigation`
--
ALTER TABLE `navigation`
 ADD PRIMARY KEY (`id`), ADD KEY `page_id` (`page_id`), ADD KEY `parentid` (`parentid`);

--
-- Indexes for table `page`
--
ALTER TABLE `page`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `action` (`action`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `navigation`
--
ALTER TABLE `navigation`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `page`
--
ALTER TABLE `page`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;