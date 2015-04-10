-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Erstellungszeit: 10. Apr 2015 um 18:17
-- Server Version: 5.6.20
-- PHP-Version: 5.5.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Datenbank: `newlogd`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `accounts`
--

CREATE TABLE IF NOT EXISTS `accounts` (
`id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `email` varchar(255) CHARACTER SET utf8 NOT NULL,
  `password` varchar(255) COLLATE utf8_bin NOT NULL,
  `created-on` datetime NOT NULL,
  `locked` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '0',
  `adminflags` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3 ;

--
-- Daten für Tabelle `accounts`
--

INSERT INTO `accounts` (`id`, `name`, `email`, `password`, `created-on`, `locked`, `adminflags`) VALUES
(1, 'Admin', 'admin@localhost', '$2y$10$Mt8gSrrLc2AhQOcEK8TV/eqAjzGaHhSk25bXUzVO1Azau61JSXm0y', '2015-03-13 13:10:07', '0', 2147483647);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `characters`
--

CREATE TABLE IF NOT EXISTS `characters` (
`id` bigint(20) unsigned NOT NULL,
  `account_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(50) COLLATE utf8_bin NOT NULL,
  `prefix` varchar(50) COLLATE utf8_bin NOT NULL,
  `suffix` varchar(50) COLLATE utf8_bin NOT NULL,
  `displayname` varchar(550) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `localmodules`
--

CREATE TABLE IF NOT EXISTS `localmodules` (
`id` bigint(20) unsigned NOT NULL,
  `class` varchar(63) CHARACTER SET utf8 NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `description` text COLLATE utf8_bin NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3 ;

--
-- Daten für Tabelle `localmodules`
--

INSERT INTO `localmodules` (`id`, `class`, `name`, `description`, `active`) VALUES
(1, 'registration', 'Registration', 'This module provides a registration form to create an account.', 1),
(2, 'tableedit', 'Tabellen-Editor', 'Fügt für eine angegebene Tabelle einen Editor ein.', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `navigations`
--

CREATE TABLE IF NOT EXISTS `navigations` (
`id` bigint(20) unsigned NOT NULL,
  `parentid` bigint(20) unsigned DEFAULT NULL,
  `page_id` bigint(20) unsigned NOT NULL,
  `action` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8 NOT NULL,
  `sort` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=15 ;

--
-- Daten für Tabelle `navigations`
--

INSERT INTO `navigations` (`id`, `parentid`, `page_id`, `action`, `title`, `sort`) VALUES
(1, 2, 1, 'about', 'Über NewLoGD', 0),
(2, NULL, 1, NULL, 'Neu hier?', 0),
(3, 2, 1, 'about_license', 'GNU AGPL 3', 0),
(4, NULL, 2, 'main', 'Zurück zur Hauptseite', 0),
(5, NULL, 2, NULL, 'Neu hier?', 0),
(6, 5, 2, 'about_license', 'GNU AGPL 3', 0),
(7, NULL, 3, 'main', 'Zurück zur Hauptseite', 0),
(8, NULL, 3, NULL, 'Neu hier?', 0),
(9, 8, 3, 'about', 'Über NewLoGD', 0),
(10, 2, 1, 'register', 'Registrieren', -10),
(11, NULL, 4, 'main', 'Zurück zur Hauptseite', 0),
(12, NULL, 6, NULL, 'Administration', 20),
(13, 12, 6, 'edit_pages', 'Seiten-Editor', 0),
(14, NULL, 9, 'ucp', 'Zurück zur Benutzerzentrale', -10);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `pages`
--

CREATE TABLE IF NOT EXISTS `pages` (
`id` bigint(20) unsigned NOT NULL,
  `type` varchar(255) CHARACTER SET utf8 NOT NULL,
  `action` varchar(255) CHARACTER SET utf8 NOT NULL,
  `title` varchar(255) COLLATE utf8_bin NOT NULL,
  `subtitle` varchar(255) COLLATE utf8_bin NOT NULL,
  `content` text COLLATE utf8_bin NOT NULL,
  `flags` bigint(1) unsigned NOT NULL DEFAULT '3',
  `access` tinyint(4) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=10 ;

--
-- Daten für Tabelle `pages`
--

INSERT INTO `pages` (`id`, `type`, `action`, `title`, `subtitle`, `content`, `flags`, `access`) VALUES
(1, 'node', 'main', 'NewLoGD', 'Willkommen', 'NewLoGD ist eine Neuauflage von The Legend of the Green Dragon (LoGD oder auch LotGD). Es basiert lose auf Seth Ables Text-RPG Legend of the Red Dragon.', 1, 1),
(2, 'node', 'about', 'Über NewLoGD', 'Allgemeines', 'The Legend of the Green Dragon ist MightyEs Remake vom klassischen, 1989 veröffentlichten BBS-Door-Spiel Legend of the Red Dragon (oder LoRD) von Seth Able Robinson. Die exklusiven Rechte an LoRD gehört inzwischen Gameport - weshalb der Inhalt des Remakes The Legend of the Green Dragon praktisch vollständig neu ist, mit Ausnahme ein paar weniger Referenzen wie zum Beispiel die vollbusige Bardame Violet oder der attraktive Barde Seth. Zusätzlich wurden verschiedene Anpassungen vorgenommen, damit LoGD besser spielbar ist als Browserspiel.\r\n\r\nNach dem Release der LoGD-Version 0.9.7+jt und der daraufhin steigenden Beliebtheit des Spiels wurden bereits zahlreiche Mods veröffentlicht und vertrieben, was dank der Open Source Lizenz möglich war. Insbesondere die deutsche Übersetzung des Spiels und die dazugehörenden Modifikationen verbreiteten sich stark, weshalb sich die LoGD-Version 1.0 nie im deutschsprachigen Raum durchsetzen konnte - was mitunter auch and der fehlenden Übersetzung lag, an der deutlich komplizierteren Code-Basis und der inkompatiblen Lizenz.\r\n\r\nAuch wenn inzwischen der kleine Hype im das Browserspiel abgeklungen ist, finden sich nach wie vor zahlreiche Installationen im Netz. Die alte Code-Basis der deutschen Version 0.9.7+jt ext GER hat aber inzwischen aber zahlreiche Probleme, da sie noch auf PHP4 basiert. Auch aktuelle MySQL-Versionen sind nicht mehr vollständig kompatibel, was neuere Installationen unnötig erschwert.\r\n\r\nDas Ziel dieses neuen LoGD-Forks ist es deshalb, basierend auf PHP 5.5 eine neue, primär deutschsprachige Basis zu bilden, die ähnlich viele Features hat wie die 0.9.7+jt ext GER. Anders als das Original versucht dieser Fork ein stark konfigurierbares Framework zu sein, dessen Seiten primär aus einer Datenbank gebildet werden.\r\n\r\nEin weiterer Unterschied ist, dass dieser Fork nicht unter der GNU GPL v2 veröffentlich wird, sondern unter der Affero GPL v3. Diese Version schliesst eine Lücke der GNU GPL v2: Es ist nur erforderlich, bei Code-Weitergabe die vollständige Source weiterzugeben, sondern auch beim zugänglichmachen einer Installation über ein Netzwerk - also dem Internet. Das verbietet Serverbetreibern, ihre Source zu schliessen und vermöglicht einen Rückfluss von Code in den Entwicklungszweig dieses Forks.\r\n\r\nUnd nun - viel Spass mit dem Spiel!', 1, 1),
(3, 'node', 'about_license', 'Über NewLoGD', 'Lizenz', '<h3 style="text-align: center;">GNU AFFERO GENERAL PUBLIC LICENSE</h3>\r\n<p style="text-align: center;">Version 3, 19 November 2007</p>\r\n\r\n<p>Copyright &copy; 2007 Free Software Foundation,\r\nInc. &lt;<a href="http://fsf.org/">http://fsf.org/</a>&gt;\r\n <br />\r\n Everyone is permitted to copy and distribute verbatim copies\r\n of this license document, but changing it is not allowed.</p>\r\n\r\n<h3><a name="preamble"></a>Preamble</h3>\r\n\r\n<p>The GNU Affero General Public License is a free, copyleft license\r\nfor software and other kinds of works, specifically designed to ensure\r\ncooperation with the community in the case of network server software.</p>\r\n\r\n<p>The licenses for most software and other practical works are\r\ndesigned to take away your freedom to share and change the works.  By\r\ncontrast, our General Public Licenses are intended to guarantee your\r\nfreedom to share and change all versions of a program--to make sure it\r\nremains free software for all its users.</p>\r\n\r\n<p>When we speak of free software, we are referring to freedom, not\r\nprice.  Our General Public Licenses are designed to make sure that you\r\nhave the freedom to distribute copies of free software (and charge for\r\nthem if you wish), that you receive source code or can get it if you\r\nwant it, that you can change the software or use pieces of it in new\r\nfree programs, and that you know you can do these things.</p>\r\n\r\n<p>Developers that use our General Public Licenses protect your rights\r\nwith two steps: (1) assert copyright on the software, and (2) offer\r\nyou this License which gives you legal permission to copy, distribute\r\nand/or modify the software.</p>\r\n\r\n<p>A secondary benefit of defending all users'' freedom is that\r\nimprovements made in alternate versions of the program, if they\r\nreceive widespread use, become available for other developers to\r\nincorporate.  Many developers of free software are heartened and\r\nencouraged by the resulting cooperation.  However, in the case of\r\nsoftware used on network servers, this result may fail to come about.\r\nThe GNU General Public License permits making a modified version and\r\nletting the public access it on a server without ever releasing its\r\nsource code to the public.</p>\r\n\r\n<p>The GNU Affero General Public License is designed specifically to\r\nensure that, in such cases, the modified source code becomes available\r\nto the community.  It requires the operator of a network server to\r\nprovide the source code of the modified version running there to the\r\nusers of that server.  Therefore, public use of a modified version, on\r\na publicly accessible server, gives the public access to the source\r\ncode of the modified version.</p>\r\n\r\n<p>An older license, called the Affero General Public License and\r\npublished by Affero, was designed to accomplish similar goals.  This is\r\na different license, not a version of the Affero GPL, but Affero has\r\nreleased a new version of the Affero GPL which permits relicensing under\r\nthis license.</p>\r\n\r\n<p>The precise terms and conditions for copying, distribution and\r\nmodification follow.</p>\r\n\r\n<h3><a name="terms"></a>TERMS AND CONDITIONS</h3>\r\n\r\n<h4><a name="section0"></a>0. Definitions.</h4>\r\n\r\n<p>&quot;This License&quot; refers to version 3 of the GNU Affero General Public\r\nLicense.</p>\r\n\r\n<p>&quot;Copyright&quot; also means copyright-like laws that apply to other kinds\r\nof works, such as semiconductor masks.</p>\r\n\r\n<p>&quot;The Program&quot; refers to any copyrightable work licensed under this\r\nLicense.  Each licensee is addressed as &quot;you&quot;.  &quot;Licensees&quot; and\r\n&quot;recipients&quot; may be individuals or organizations.</p>\r\n\r\n<p>To &quot;modify&quot; a work means to copy from or adapt all or part of the work\r\nin a fashion requiring copyright permission, other than the making of an\r\nexact copy.  The resulting work is called a &quot;modified version&quot; of the\r\nearlier work or a work &quot;based on&quot; the earlier work.</p>\r\n\r\n<p>A &quot;covered work&quot; means either the unmodified Program or a work based\r\non the Program.</p>\r\n\r\n<p>To &quot;propagate&quot; a work means to do anything with it that, without\r\npermission, would make you directly or secondarily liable for\r\ninfringement under applicable copyright law, except executing it on a\r\ncomputer or modifying a private copy.  Propagation includes copying,\r\ndistribution (with or without modification), making available to the\r\npublic, and in some countries other activities as well.</p>\r\n\r\n<p>To &quot;convey&quot; a work means any kind of propagation that enables other\r\nparties to make or receive copies.  Mere interaction with a user through\r\na computer network, with no transfer of a copy, is not conveying.</p>\r\n\r\n<p>An interactive user interface displays &quot;Appropriate Legal Notices&quot;\r\nto the extent that it includes a convenient and prominently visible\r\nfeature that (1) displays an appropriate copyright notice, and (2)\r\ntells the user that there is no warranty for the work (except to the\r\nextent that warranties are provided), that licensees may convey the\r\nwork under this License, and how to view a copy of this License.  If\r\nthe interface presents a list of user commands or options, such as a\r\nmenu, a prominent item in the list meets this criterion.</p>\r\n\r\n<h4><a name="section1"></a>1. Source Code.</h4>\r\n\r\n<p>The &quot;source code&quot; for a work means the preferred form of the work\r\nfor making modifications to it.  &quot;Object code&quot; means any non-source\r\nform of a work.</p>\r\n\r\n<p>A &quot;Standard Interface&quot; means an interface that either is an official\r\nstandard defined by a recognized standards body, or, in the case of\r\ninterfaces specified for a particular programming language, one that\r\nis widely used among developers working in that language.</p>\r\n\r\n<p>The &quot;System Libraries&quot; of an executable work include anything, other\r\nthan the work as a whole, that (a) is included in the normal form of\r\npackaging a Major Component, but which is not part of that Major\r\nComponent, and (b) serves only to enable use of the work with that\r\nMajor Component, or to implement a Standard Interface for which an\r\nimplementation is available to the public in source code form.  A\r\n&quot;Major Component&quot;, in this context, means a major essential component\r\n(kernel, window system, and so on) of the specific operating system\r\n(if any) on which the executable work runs, or a compiler used to\r\nproduce the work, or an object code interpreter used to run it.</p>\r\n\r\n<p>The &quot;Corresponding Source&quot; for a work in object code form means all\r\nthe source code needed to generate, install, and (for an executable\r\nwork) run the object code and to modify the work, including scripts to\r\ncontrol those activities.  However, it does not include the work''s\r\nSystem Libraries, or general-purpose tools or generally available free\r\nprograms which are used unmodified in performing those activities but\r\nwhich are not part of the work.  For example, Corresponding Source\r\nincludes interface definition files associated with source files for\r\nthe work, and the source code for shared libraries and dynamically\r\nlinked subprograms that the work is specifically designed to require,\r\nsuch as by intimate data communication or control flow between those\r\nsubprograms and other parts of the work.</p>\r\n\r\n<p>The Corresponding Source need not include anything that users\r\ncan regenerate automatically from other parts of the Corresponding\r\nSource.</p>\r\n\r\n<p>The Corresponding Source for a work in source code form is that\r\nsame work.</p>\r\n\r\n<h4><a name="section2"></a>2. Basic Permissions.</h4>\r\n\r\n<p>All rights granted under this License are granted for the term of\r\ncopyright on the Program, and are irrevocable provided the stated\r\nconditions are met.  This License explicitly affirms your unlimited\r\npermission to run the unmodified Program.  The output from running a\r\ncovered work is covered by this License only if the output, given its\r\ncontent, constitutes a covered work.  This License acknowledges your\r\nrights of fair use or other equivalent, as provided by copyright law.</p>\r\n\r\n<p>You may make, run and propagate covered works that you do not\r\nconvey, without conditions so long as your license otherwise remains\r\nin force.  You may convey covered works to others for the sole purpose\r\nof having them make modifications exclusively for you, or provide you\r\nwith facilities for running those works, provided that you comply with\r\nthe terms of this License in conveying all material for which you do\r\nnot control copyright.  Those thus making or running the covered works\r\nfor you must do so exclusively on your behalf, under your direction\r\nand control, on terms that prohibit them from making any copies of\r\nyour copyrighted material outside their relationship with you.</p>\r\n\r\n<p>Conveying under any other circumstances is permitted solely under\r\nthe conditions stated below.  Sublicensing is not allowed; section 10\r\nmakes it unnecessary.</p>\r\n\r\n<h4><a name="section3"></a>3. Protecting Users'' Legal Rights From Anti-Circumvention Law.</h4>\r\n\r\n<p>No covered work shall be deemed part of an effective technological\r\nmeasure under any applicable law fulfilling obligations under article\r\n11 of the WIPO copyright treaty adopted on 20 December 1996, or\r\nsimilar laws prohibiting or restricting circumvention of such\r\nmeasures.</p>\r\n\r\n<p>When you convey a covered work, you waive any legal power to forbid\r\ncircumvention of technological measures to the extent such circumvention\r\nis effected by exercising rights under this License with respect to\r\nthe covered work, and you disclaim any intention to limit operation or\r\nmodification of the work as a means of enforcing, against the work''s\r\nusers, your or third parties'' legal rights to forbid circumvention of\r\ntechnological measures.</p>\r\n\r\n<h4><a name="section4"></a>4. Conveying Verbatim Copies.</h4>\r\n\r\n<p>You may convey verbatim copies of the Program''s source code as you\r\nreceive it, in any medium, provided that you conspicuously and\r\nappropriately publish on each copy an appropriate copyright notice;\r\nkeep intact all notices stating that this License and any\r\nnon-permissive terms added in accord with section 7 apply to the code;\r\nkeep intact all notices of the absence of any warranty; and give all\r\nrecipients a copy of this License along with the Program.</p>\r\n\r\n<p>You may charge any price or no price for each copy that you convey,\r\nand you may offer support or warranty protection for a fee.</p>\r\n\r\n<h4><a name="section5"></a>5. Conveying Modified Source Versions.</h4>\r\n\r\n<p>You may convey a work based on the Program, or the modifications to\r\nproduce it from the Program, in the form of source code under the\r\nterms of section 4, provided that you also meet all of these conditions:</p>\r\n\r\n<ul>\r\n\r\n<li>a) The work must carry prominent notices stating that you modified\r\n    it, and giving a relevant date.</li>\r\n\r\n<li>b) The work must carry prominent notices stating that it is\r\n    released under this License and any conditions added under section\r\n    7.  This requirement modifies the requirement in section 4 to\r\n    &quot;keep intact all notices&quot;.</li>\r\n\r\n<li>c) You must license the entire work, as a whole, under this\r\n    License to anyone who comes into possession of a copy.  This\r\n    License will therefore apply, along with any applicable section 7\r\n    additional terms, to the whole of the work, and all its parts,\r\n    regardless of how they are packaged.  This License gives no\r\n    permission to license the work in any other way, but it does not\r\n    invalidate such permission if you have separately received it.</li>\r\n\r\n<li>d) If the work has interactive user interfaces, each must display\r\n    Appropriate Legal Notices; however, if the Program has interactive\r\n    interfaces that do not display Appropriate Legal Notices, your\r\n    work need not make them do so.</li>\r\n\r\n</ul>\r\n\r\n<p>A compilation of a covered work with other separate and independent\r\nworks, which are not by their nature extensions of the covered work,\r\nand which are not combined with it such as to form a larger program,\r\nin or on a volume of a storage or distribution medium, is called an\r\n&quot;aggregate&quot; if the compilation and its resulting copyright are not\r\nused to limit the access or legal rights of the compilation''s users\r\nbeyond what the individual works permit.  Inclusion of a covered work\r\nin an aggregate does not cause this License to apply to the other\r\nparts of the aggregate.</p>\r\n\r\n<h4><a name="section6"></a>6. Conveying Non-Source Forms.</h4>\r\n\r\n<p>You may convey a covered work in object code form under the terms\r\nof sections 4 and 5, provided that you also convey the\r\nmachine-readable Corresponding Source under the terms of this License,\r\nin one of these ways:</p>\r\n\r\n<ul>\r\n\r\n<li>a) Convey the object code in, or embodied in, a physical product\r\n    (including a physical distribution medium), accompanied by the\r\n    Corresponding Source fixed on a durable physical medium\r\n    customarily used for software interchange.</li>\r\n\r\n<li>b) Convey the object code in, or embodied in, a physical product\r\n    (including a physical distribution medium), accompanied by a\r\n    written offer, valid for at least three years and valid for as\r\n    long as you offer spare parts or customer support for that product\r\n    model, to give anyone who possesses the object code either (1) a\r\n    copy of the Corresponding Source for all the software in the\r\n    product that is covered by this License, on a durable physical\r\n    medium customarily used for software interchange, for a price no\r\n    more than your reasonable cost of physically performing this\r\n    conveying of source, or (2) access to copy the\r\n    Corresponding Source from a network server at no charge.</li>\r\n\r\n<li>c) Convey individual copies of the object code with a copy of the\r\n    written offer to provide the Corresponding Source.  This\r\n    alternative is allowed only occasionally and noncommercially, and\r\n    only if you received the object code with such an offer, in accord\r\n    with subsection 6b.</li>\r\n\r\n<li>d) Convey the object code by offering access from a designated\r\n    place (gratis or for a charge), and offer equivalent access to the\r\n    Corresponding Source in the same way through the same place at no\r\n    further charge.  You need not require recipients to copy the\r\n    Corresponding Source along with the object code.  If the place to\r\n    copy the object code is a network server, the Corresponding Source\r\n    may be on a different server (operated by you or a third party)\r\n    that supports equivalent copying facilities, provided you maintain\r\n    clear directions next to the object code saying where to find the\r\n    Corresponding Source.  Regardless of what server hosts the\r\n    Corresponding Source, you remain obligated to ensure that it is\r\n    available for as long as needed to satisfy these requirements.</li>\r\n\r\n<li>e) Convey the object code using peer-to-peer transmission, provided\r\n    you inform other peers where the object code and Corresponding\r\n    Source of the work are being offered to the general public at no\r\n    charge under subsection 6d.</li>\r\n\r\n</ul>\r\n\r\n<p>A separable portion of the object code, whose source code is excluded\r\nfrom the Corresponding Source as a System Library, need not be\r\nincluded in conveying the object code work.</p>\r\n\r\n<p>A &quot;User Product&quot; is either (1) a &quot;consumer product&quot;, which means any\r\ntangible personal property which is normally used for personal, family,\r\nor household purposes, or (2) anything designed or sold for incorporation\r\ninto a dwelling.  In determining whether a product is a consumer product,\r\ndoubtful cases shall be resolved in favor of coverage.  For a particular\r\nproduct received by a particular user, &quot;normally used&quot; refers to a\r\ntypical or common use of that class of product, regardless of the status\r\nof the particular user or of the way in which the particular user\r\nactually uses, or expects or is expected to use, the product.  A product\r\nis a consumer product regardless of whether the product has substantial\r\ncommercial, industrial or non-consumer uses, unless such uses represent\r\nthe only significant mode of use of the product.</p>\r\n\r\n<p>&quot;Installation Information&quot; for a User Product means any methods,\r\nprocedures, authorization keys, or other information required to install\r\nand execute modified versions of a covered work in that User Product from\r\na modified version of its Corresponding Source.  The information must\r\nsuffice to ensure that the continued functioning of the modified object\r\ncode is in no case prevented or interfered with solely because\r\nmodification has been made.</p>\r\n\r\n<p>If you convey an object code work under this section in, or with, or\r\nspecifically for use in, a User Product, and the conveying occurs as\r\npart of a transaction in which the right of possession and use of the\r\nUser Product is transferred to the recipient in perpetuity or for a\r\nfixed term (regardless of how the transaction is characterized), the\r\nCorresponding Source conveyed under this section must be accompanied\r\nby the Installation Information.  But this requirement does not apply\r\nif neither you nor any third party retains the ability to install\r\nmodified object code on the User Product (for example, the work has\r\nbeen installed in ROM).</p>\r\n\r\n<p>The requirement to provide Installation Information does not include a\r\nrequirement to continue to provide support service, warranty, or updates\r\nfor a work that has been modified or installed by the recipient, or for\r\nthe User Product in which it has been modified or installed.  Access to a\r\nnetwork may be denied when the modification itself materially and\r\nadversely affects the operation of the network or violates the rules and\r\nprotocols for communication across the network.</p>\r\n\r\n<p>Corresponding Source conveyed, and Installation Information provided,\r\nin accord with this section must be in a format that is publicly\r\ndocumented (and with an implementation available to the public in\r\nsource code form), and must require no special password or key for\r\nunpacking, reading or copying.</p>\r\n\r\n<h4><a name="section7"></a>7. Additional Terms.</h4>\r\n\r\n<p>&quot;Additional permissions&quot; are terms that supplement the terms of this\r\nLicense by making exceptions from one or more of its conditions.\r\nAdditional permissions that are applicable to the entire Program shall\r\nbe treated as though they were included in this License, to the extent\r\nthat they are valid under applicable law.  If additional permissions\r\napply only to part of the Program, that part may be used separately\r\nunder those permissions, but the entire Program remains governed by\r\nthis License without regard to the additional permissions.</p>\r\n\r\n<p>When you convey a copy of a covered work, you may at your option\r\nremove any additional permissions from that copy, or from any part of\r\nit.  (Additional permissions may be written to require their own\r\nremoval in certain cases when you modify the work.)  You may place\r\nadditional permissions on material, added by you to a covered work,\r\nfor which you have or can give appropriate copyright permission.</p>\r\n\r\n<p>Notwithstanding any other provision of this License, for material you\r\nadd to a covered work, you may (if authorized by the copyright holders of\r\nthat material) supplement the terms of this License with terms:</p>\r\n\r\n<ul>\r\n\r\n<li>a) Disclaiming warranty or limiting liability differently from the\r\n    terms of sections 15 and 16 of this License; or</li>\r\n\r\n<li>b) Requiring preservation of specified reasonable legal notices or\r\n    author attributions in that material or in the Appropriate Legal\r\n    Notices displayed by works containing it; or</li>\r\n\r\n<li>c) Prohibiting misrepresentation of the origin of that material, or\r\n    requiring that modified versions of such material be marked in\r\n    reasonable ways as different from the original version; or</li>\r\n\r\n<li>d) Limiting the use for publicity purposes of names of licensors or\r\n    authors of the material; or</li>\r\n\r\n<li>e) Declining to grant rights under trademark law for use of some\r\n    trade names, trademarks, or service marks; or</li>\r\n\r\n<li>f) Requiring indemnification of licensors and authors of that\r\n    material by anyone who conveys the material (or modified versions of\r\n    it) with contractual assumptions of liability to the recipient, for\r\n    any liability that these contractual assumptions directly impose on\r\n    those licensors and authors.</li>\r\n\r\n</ul>\r\n\r\n<p>All other non-permissive additional terms are considered &quot;further\r\nrestrictions&quot; within the meaning of section 10.  If the Program as you\r\nreceived it, or any part of it, contains a notice stating that it is\r\ngoverned by this License along with a term that is a further restriction,\r\nyou may remove that term.  If a license document contains a further\r\nrestriction but permits relicensing or conveying under this License, you\r\nmay add to a covered work material governed by the terms of that license\r\ndocument, provided that the further restriction does not survive such\r\nrelicensing or conveying.</p>\r\n\r\n<p>If you add terms to a covered work in accord with this section, you\r\nmust place, in the relevant source files, a statement of the\r\nadditional terms that apply to those files, or a notice indicating\r\nwhere to find the applicable terms.</p>\r\n\r\n<p>Additional terms, permissive or non-permissive, may be stated in the\r\nform of a separately written license, or stated as exceptions;\r\nthe above requirements apply either way.</p>\r\n\r\n<h4><a name="section8"></a>8. Termination.</h4>\r\n\r\n<p>You may not propagate or modify a covered work except as expressly\r\nprovided under this License.  Any attempt otherwise to propagate or\r\nmodify it is void, and will automatically terminate your rights under\r\nthis License (including any patent licenses granted under the third\r\nparagraph of section 11).</p>\r\n\r\n<p>However, if you cease all violation of this License, then your\r\nlicense from a particular copyright holder is reinstated (a)\r\nprovisionally, unless and until the copyright holder explicitly and\r\nfinally terminates your license, and (b) permanently, if the copyright\r\nholder fails to notify you of the violation by some reasonable means\r\nprior to 60 days after the cessation.</p>\r\n\r\n<p>Moreover, your license from a particular copyright holder is\r\nreinstated permanently if the copyright holder notifies you of the\r\nviolation by some reasonable means, this is the first time you have\r\nreceived notice of violation of this License (for any work) from that\r\ncopyright holder, and you cure the violation prior to 30 days after\r\nyour receipt of the notice.</p>\r\n\r\n<p>Termination of your rights under this section does not terminate the\r\nlicenses of parties who have received copies or rights from you under\r\nthis License.  If your rights have been terminated and not permanently\r\nreinstated, you do not qualify to receive new licenses for the same\r\nmaterial under section 10.</p>\r\n\r\n<h4><a name="section9"></a>9. Acceptance Not Required for Having Copies.</h4>\r\n\r\n<p>You are not required to accept this License in order to receive or\r\nrun a copy of the Program.  Ancillary propagation of a covered work\r\noccurring solely as a consequence of using peer-to-peer transmission\r\nto receive a copy likewise does not require acceptance.  However,\r\nnothing other than this License grants you permission to propagate or\r\nmodify any covered work.  These actions infringe copyright if you do\r\nnot accept this License.  Therefore, by modifying or propagating a\r\ncovered work, you indicate your acceptance of this License to do so.</p>\r\n\r\n<h4><a name="section10"></a>10. Automatic Licensing of Downstream Recipients.</h4>\r\n\r\n<p>Each time you convey a covered work, the recipient automatically\r\nreceives a license from the original licensors, to run, modify and\r\npropagate that work, subject to this License.  You are not responsible\r\nfor enforcing compliance by third parties with this License.</p>\r\n\r\n<p>An &quot;entity transaction&quot; is a transaction transferring control of an\r\norganization, or substantially all assets of one, or subdividing an\r\norganization, or merging organizations.  If propagation of a covered\r\nwork results from an entity transaction, each party to that\r\ntransaction who receives a copy of the work also receives whatever\r\nlicenses to the work the party''s predecessor in interest had or could\r\ngive under the previous paragraph, plus a right to possession of the\r\nCorresponding Source of the work from the predecessor in interest, if\r\nthe predecessor has it or can get it with reasonable efforts.</p>\r\n\r\n<p>You may not impose any further restrictions on the exercise of the\r\nrights granted or affirmed under this License.  For example, you may\r\nnot impose a license fee, royalty, or other charge for exercise of\r\nrights granted under this License, and you may not initiate litigation\r\n(including a cross-claim or counterclaim in a lawsuit) alleging that\r\nany patent claim is infringed by making, using, selling, offering for\r\nsale, or importing the Program or any portion of it.</p>\r\n\r\n<h4><a name="section11"></a>11. Patents.</h4>\r\n\r\n<p>A &quot;contributor&quot; is a copyright holder who authorizes use under this\r\nLicense of the Program or a work on which the Program is based.  The\r\nwork thus licensed is called the contributor''s &quot;contributor version&quot;.</p>\r\n\r\n<p>A contributor''s &quot;essential patent claims&quot; are all patent claims\r\nowned or controlled by the contributor, whether already acquired or\r\nhereafter acquired, that would be infringed by some manner, permitted\r\nby this License, of making, using, or selling its contributor version,\r\nbut do not include claims that would be infringed only as a\r\nconsequence of further modification of the contributor version.  For\r\npurposes of this definition, &quot;control&quot; includes the right to grant\r\npatent sublicenses in a manner consistent with the requirements of\r\nthis License.</p>\r\n\r\n<p>Each contributor grants you a non-exclusive, worldwide, royalty-free\r\npatent license under the contributor''s essential patent claims, to\r\nmake, use, sell, offer for sale, import and otherwise run, modify and\r\npropagate the contents of its contributor version.</p>\r\n\r\n<p>In the following three paragraphs, a &quot;patent license&quot; is any express\r\nagreement or commitment, however denominated, not to enforce a patent\r\n(such as an express permission to practice a patent or covenant not to\r\nsue for patent infringement).  To &quot;grant&quot; such a patent license to a\r\nparty means to make such an agreement or commitment not to enforce a\r\npatent against the party.</p>\r\n\r\n<p>If you convey a covered work, knowingly relying on a patent license,\r\nand the Corresponding Source of the work is not available for anyone\r\nto copy, free of charge and under the terms of this License, through a\r\npublicly available network server or other readily accessible means,\r\nthen you must either (1) cause the Corresponding Source to be so\r\navailable, or (2) arrange to deprive yourself of the benefit of the\r\npatent license for this particular work, or (3) arrange, in a manner\r\nconsistent with the requirements of this License, to extend the patent\r\nlicense to downstream recipients.  &quot;Knowingly relying&quot; means you have\r\nactual knowledge that, but for the patent license, your conveying the\r\ncovered work in a country, or your recipient''s use of the covered work\r\nin a country, would infringe one or more identifiable patents in that\r\ncountry that you have reason to believe are valid.</p>\r\n\r\n<p>If, pursuant to or in connection with a single transaction or\r\narrangement, you convey, or propagate by procuring conveyance of, a\r\ncovered work, and grant a patent license to some of the parties\r\nreceiving the covered work authorizing them to use, propagate, modify\r\nor convey a specific copy of the covered work, then the patent license\r\nyou grant is automatically extended to all recipients of the covered\r\nwork and works based on it.</p>\r\n\r\n<p>A patent license is &quot;discriminatory&quot; if it does not include within\r\nthe scope of its coverage, prohibits the exercise of, or is\r\nconditioned on the non-exercise of one or more of the rights that are\r\nspecifically granted under this License.  You may not convey a covered\r\nwork if you are a party to an arrangement with a third party that is\r\nin the business of distributing software, under which you make payment\r\nto the third party based on the extent of your activity of conveying\r\nthe work, and under which the third party grants, to any of the\r\nparties who would receive the covered work from you, a discriminatory\r\npatent license (a) in connection with copies of the covered work\r\nconveyed by you (or copies made from those copies), or (b) primarily\r\nfor and in connection with specific products or compilations that\r\ncontain the covered work, unless you entered into that arrangement,\r\nor that patent license was granted, prior to 28 March 2007.</p>\r\n\r\n<p>Nothing in this License shall be construed as excluding or limiting\r\nany implied license or other defenses to infringement that may\r\notherwise be available to you under applicable patent law.</p>\r\n\r\n<h4><a name="section12"></a>12. No Surrender of Others'' Freedom.</h4>\r\n\r\n<p>If conditions are imposed on you (whether by court order, agreement or\r\notherwise) that contradict the conditions of this License, they do not\r\nexcuse you from the conditions of this License.  If you cannot convey a\r\ncovered work so as to satisfy simultaneously your obligations under this\r\nLicense and any other pertinent obligations, then as a consequence you may\r\nnot convey it at all.  For example, if you agree to terms that obligate you\r\nto collect a royalty for further conveying from those to whom you convey\r\nthe Program, the only way you could satisfy both those terms and this\r\nLicense would be to refrain entirely from conveying the Program.</p>\r\n\r\n<h4><a name="section13"></a>13. Remote Network Interaction; Use with the GNU General Public License.</h4>\r\n\r\n<p>Notwithstanding any other provision of this License, if you modify the\r\nProgram, your modified version must prominently offer all users\r\ninteracting with it remotely through a computer network (if your version\r\nsupports such interaction) an opportunity to receive the Corresponding\r\nSource of your version by providing access to the Corresponding Source\r\nfrom a network server at no charge, through some standard or customary\r\nmeans of facilitating copying of software.  This Corresponding Source\r\nshall include the Corresponding Source for any work covered by version 3\r\nof the GNU General Public License that is incorporated pursuant to the\r\nfollowing paragraph.</p>\r\n\r\n<p>Notwithstanding any other provision of this License, you have permission\r\nto link or combine any covered work with a work licensed under version 3\r\nof the GNU General Public License into a single combined work, and to\r\nconvey the resulting work.  The terms of this License will continue to\r\napply to the part which is the covered work, but the work with which it is\r\ncombined will remain governed by version 3 of the GNU General Public\r\nLicense.</p>\r\n\r\n<h4><a name="section14"></a>14. Revised Versions of this License.</h4>\r\n\r\n<p>The Free Software Foundation may publish revised and/or new versions of\r\nthe GNU Affero General Public License from time to time.  Such new\r\nversions will be similar in spirit to the present version, but may differ\r\nin detail to address new problems or concerns.</p>\r\n\r\n<p>Each version is given a distinguishing version number.  If the\r\nProgram specifies that a certain numbered version of the GNU Affero\r\nGeneral Public License &quot;or any later version&quot; applies to it, you have\r\nthe option of following the terms and conditions either of that\r\nnumbered version or of any later version published by the Free\r\nSoftware Foundation.  If the Program does not specify a version number\r\nof the GNU Affero General Public License, you may choose any version\r\never published by the Free Software Foundation.</p>\r\n\r\n<p>If the Program specifies that a proxy can decide which future\r\nversions of the GNU Affero General Public License can be used, that\r\nproxy''s public statement of acceptance of a version permanently\r\nauthorizes you to choose that version for the Program.</p>\r\n\r\n<p>Later license versions may give you additional or different\r\npermissions.  However, no additional obligations are imposed on any\r\nauthor or copyright holder as a result of your choosing to follow a\r\nlater version.</p>\r\n\r\n<h4><a name="section15"></a>15. Disclaimer of Warranty.</h4>\r\n\r\n<p>THERE IS NO WARRANTY FOR THE PROGRAM, TO THE EXTENT PERMITTED BY\r\nAPPLICABLE LAW.  EXCEPT WHEN OTHERWISE STATED IN WRITING THE COPYRIGHT\r\nHOLDERS AND/OR OTHER PARTIES PROVIDE THE PROGRAM &quot;AS IS&quot; WITHOUT WARRANTY\r\nOF ANY KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING, BUT NOT LIMITED TO,\r\nTHE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR\r\nPURPOSE.  THE ENTIRE RISK AS TO THE QUALITY AND PERFORMANCE OF THE PROGRAM\r\nIS WITH YOU.  SHOULD THE PROGRAM PROVE DEFECTIVE, YOU ASSUME THE COST OF\r\nALL NECESSARY SERVICING, REPAIR OR CORRECTION.</p>\r\n\r\n<h4><a name="section16"></a>16. Limitation of Liability.</h4>\r\n\r\n<p>IN NO EVENT UNLESS REQUIRED BY APPLICABLE LAW OR AGREED TO IN WRITING\r\nWILL ANY COPYRIGHT HOLDER, OR ANY OTHER PARTY WHO MODIFIES AND/OR CONVEYS\r\nTHE PROGRAM AS PERMITTED ABOVE, BE LIABLE TO YOU FOR DAMAGES, INCLUDING ANY\r\nGENERAL, SPECIAL, INCIDENTAL OR CONSEQUENTIAL DAMAGES ARISING OUT OF THE\r\nUSE OR INABILITY TO USE THE PROGRAM (INCLUDING BUT NOT LIMITED TO LOSS OF\r\nDATA OR DATA BEING RENDERED INACCURATE OR LOSSES SUSTAINED BY YOU OR THIRD\r\nPARTIES OR A FAILURE OF THE PROGRAM TO OPERATE WITH ANY OTHER PROGRAMS),\r\nEVEN IF SUCH HOLDER OR OTHER PARTY HAS BEEN ADVISED OF THE POSSIBILITY OF\r\nSUCH DAMAGES.</p>\r\n\r\n<h4><a name="section17"></a>17. Interpretation of Sections 15 and 16.</h4>\r\n\r\n<p>If the disclaimer of warranty and limitation of liability provided\r\nabove cannot be given local legal effect according to their terms,\r\nreviewing courts shall apply local law that most closely approximates\r\nan absolute waiver of all civil liability in connection with the\r\nProgram, unless a warranty or assumption of liability accompanies a\r\ncopy of the Program in return for a fee.</p>\r\n\r\n<p>END OF TERMS AND CONDITIONS</p>\r\n\r\n<h3><a name="howto"></a>How to Apply These Terms to Your New Programs</h3>\r\n\r\n<p>If you develop a new program, and you want it to be of the greatest\r\npossible use to the public, the best way to achieve this is to make it\r\nfree software which everyone can redistribute and change under these terms.</p>\r\n\r\n<p>To do so, attach the following notices to the program.  It is safest\r\nto attach them to the start of each source file to most effectively\r\nstate the exclusion of warranty; and each file should have at least\r\nthe &quot;copyright&quot; line and a pointer to where the full notice is found.</p>\r\n\r\n<pre>    &lt;one line to give the program''s name and a brief idea of what it does.&gt;\r\n    Copyright (C) &lt;year&gt;  &lt;name of author&gt;\r\n\r\n    This program is free software: you can redistribute it and/or modify\r\n    it under the terms of the GNU Affero General Public License as\r\n    published by the Free Software Foundation, either version 3 of the\r\n    License, or (at your option) any later version.\r\n\r\n    This program is distributed in the hope that it will be useful,\r\n    but WITHOUT ANY WARRANTY; without even the implied warranty of\r\n    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the\r\n    GNU Affero General Public License for more details.\r\n\r\n    You should have received a copy of the GNU Affero General Public License\r\n    along with this program.  If not, see &lt;http://www.gnu.org/licenses/&gt;.\r\n</pre>\r\n\r\n<p>Also add information on how to contact you by electronic and paper mail.</p>\r\n\r\n<p>If your software can interact with users remotely through a computer\r\nnetwork, you should also make sure that it provides a way for users to\r\nget its source.  For example, if your program is a web application, its\r\ninterface could display a &quot;Source&quot; link that leads users to an archive\r\nof the code.  There are many ways you could offer source, and different\r\nsolutions will be better for different programs; see section 13 for the\r\nspecific requirements.</p>\r\n\r\n<p>You should also get your employer (if you work as a programmer) or school,\r\nif any, to sign a &quot;copyright disclaimer&quot; for the program, if necessary.\r\nFor more information on this, and how to apply and follow the GNU AGPL, see\r\n&lt;<a href="http://www.gnu.org/licenses/">http://www.gnu.org/licenses/</a>&gt;.</p>', 48, 1),
(4, 'node', 'register', 'Registrierung', '', 'Du kommst in einen kaum beleuchteten Raum. Nur das Podest in der Mitte wird von der Decke her beleuchtet. Auf dem Podest befindet sich ein kleiner Handspiegel. Interessierst nimmst du ihn in die Hand. Das Gesicht eines kauzigen Zauberers mit grauem, langem Bart erscheint. Er fragt dich verschiedene Fragen...\r\n\r\nDu erstellst mit der Registrierung einen Charakter in dieser Welt. Das bedeutet, dass du dem Charakter einen Namen geben musst. Der Name sollte idealerweise ein realistischer Name sein (Niemand heisst [Cola] zum Vornamen, oder [XxX Coolguy XxX]...), sollte aber auch nicht dein eigener sein (schliesslich spielst du dich hier nicht selbst). Wir bitten dich ebenfalls, keine anzüglichen Namen zu wählen oder Namen berühmter/berüchtigter Personen.\r\n\r\nDie E-Mailadresse muss existieren. Wir werden an diese eine E-Mail schicken, der ein Bestätigungslink beiliegt. Diese URL musst du aufrufen zum zu bestätigen, dass die Adresse tatsächlich existiert - erst dann wird der Account freigeschalten. Dein Passwort ist frei wählbar, sollte aber in Kombination mit dieser E-Mailadresse einzigartig sein. Wir speichern in der Datenbank ausschliesslich einen sogenannten versalzenen Hash deines Passworts. Falls jemand drittes Zugriff auf die Datenbank bekommen sollte, kann er daraus dein Passwort nicht rekonstruieren.', 1, 1),
(5, 'login', 'login', '', '', '', 256, 1),
(6, 'node', 'ucp', 'Benutzer-Zentrale', '', 'Hier ist die Benutzerzentrale.', 1, 2),
(7, 'logout', 'logout', '', '', '', 256, 2),
(9, 'node', 'edit_pages', 'Seiten-Editor', '', 'Das hier ist der Editor für Seiten.', 1, 2);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `pages_localmodules_xref`
--

CREATE TABLE IF NOT EXISTS `pages_localmodules_xref` (
  `localmodule_id` bigint(20) unsigned NOT NULL,
  `page_id` bigint(20) unsigned NOT NULL,
  `config` text COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Daten für Tabelle `pages_localmodules_xref`
--

INSERT INTO `pages_localmodules_xref` (`localmodule_id`, `page_id`, `config`) VALUES
(1, 4, '{"name_fieldname":"Name des Benutzerkontos","password1_fieldname":"Dein Passwort","password2_fieldname":"Dein Passwort (bestätigen)","email1_fieldname":"Deine E-Mailadresse","email2_fieldname":"Deine E-Mailadresse (bestätigen)","submitbutton_name":"Registrierung bestätigen"}'),
(2, 9, '{"table-to-edit":"pages"}');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tables`
--

CREATE TABLE IF NOT EXISTS `tables` (
`id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=2 ;

--
-- Daten für Tabelle `tables`
--

INSERT INTO `tables` (`id`, `name`) VALUES
(1, 'pages');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `table_fields`
--

CREATE TABLE IF NOT EXISTS `table_fields` (
`id` bigint(20) unsigned NOT NULL,
  `tables_id` bigint(20) unsigned NOT NULL,
  `fieldname` varchar(255) CHARACTER SET utf8 NOT NULL,
  `fieldtype` int(11) NOT NULL DEFAULT '1',
  `default_value` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=8 ;

--
-- Daten für Tabelle `table_fields`
--

INSERT INTO `table_fields` (`id`, `tables_id`, `fieldname`, `fieldtype`, `default_value`, `description`) VALUES
(2, 1, 'type', 1, 'node', 'Page-Typus'),
(4, 1, 'action', 1, NULL, 'Aktions-Name'),
(5, 1, 'title', 1, NULL, 'Seitentitel'),
(6, 1, 'subtitle', 1, NULL, 'Untertitel der Seite'),
(7, 1, 'content', 128, NULL, 'Seiten-Inhalt');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `email` (`email`), ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `characters`
--
ALTER TABLE `characters`
 ADD PRIMARY KEY (`id`), ADD KEY `account_id` (`account_id`,`name`);

--
-- Indexes for table `localmodules`
--
ALTER TABLE `localmodules`
 ADD UNIQUE KEY `moduleid` (`id`), ADD UNIQUE KEY `class` (`class`), ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `navigations`
--
ALTER TABLE `navigations`
 ADD PRIMARY KEY (`id`), ADD KEY `page_id` (`page_id`), ADD KEY `parentid` (`parentid`), ADD KEY `action` (`action`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `action` (`action`);

--
-- Indexes for table `pages_localmodules_xref`
--
ALTER TABLE `pages_localmodules_xref`
 ADD PRIMARY KEY (`localmodule_id`,`page_id`), ADD KEY `pages_localmodules_xref_pages_fk` (`page_id`);

--
-- Indexes for table `tables`
--
ALTER TABLE `tables`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `table_fields`
--
ALTER TABLE `table_fields`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `key-table_id-fieldname` (`tables_id`,`fieldname`), ADD KEY `table_id` (`tables_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `characters`
--
ALTER TABLE `characters`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `localmodules`
--
ALTER TABLE `localmodules`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `navigations`
--
ALTER TABLE `navigations`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `tables`
--
ALTER TABLE `tables`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `table_fields`
--
ALTER TABLE `table_fields`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `characters`
--
ALTER TABLE `characters`
ADD CONSTRAINT `characters_accounts_fk` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Constraints der Tabelle `navigations`
--
ALTER TABLE `navigations`
ADD CONSTRAINT `fk-action` FOREIGN KEY (`action`) REFERENCES `pages` (`action`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `navigations_navigations_fk` FOREIGN KEY (`parentid`) REFERENCES `navigations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `navigations_pages_fk` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `pages_localmodules_xref`
--
ALTER TABLE `pages_localmodules_xref`
ADD CONSTRAINT `pages_localmodules_xref_localmodules_fk` FOREIGN KEY (`localmodule_id`) REFERENCES `localmodules` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `pages_localmodules_xref_pages_fk` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `table_fields`
--
ALTER TABLE `table_fields`
ADD CONSTRAINT `fk-table_fields-tables` FOREIGN KEY (`tables_id`) REFERENCES `tables` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
