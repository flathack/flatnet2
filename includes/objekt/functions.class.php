<?php
/**
 * @author: Steven Sch�del
 * Projekt Flatnet
 *
 * Diese Klasse enth�lt Funktionen die allgemein genutzt werden k�nnen, d. h. unabh�ngig sind.
 */

include 'mysql.class.php';

class functions extends sql {

	/**
	 * Allgemeine Suche
	 * @param string $suchWort // Das Wort wonach gesucht werden soll
	 * @param string $tabelle // In welcher Tabelle gesucht werden soll
	 * @param string $name // Welcher Spaltenname ausgegeben werden soll nach erfolgreicher Suche
	 * @param string $pfad // Welcher Pfad hinterlegt werden soll.
	 * @return string // Gibt die komplette Suche, samt umgebenen DIV wieder.
	 */
	function suche($suchWort = "test", $tabelle = "docu", $name = "text", $pfad = "?id") {
		if($this->userHasRight("23", 0) == true) {
			if($suchWort) {
	
				$urspr�nglicheSuche = $suchWort;
				$ergebnis_der_suche = "";
				# Suche mit Wildcards best�cken
				$suchWort = "%" . $suchWort . "%";
	
				# Spalten der Tabelle selektieren:
				$colums  = "SHOW COLUMNS FROM `$tabelle`";
				$getColums = mysql_query($colums);
	
				# SuchQuery bauen:
				# Start String:
				$querySuche = "SELECT * FROM $tabelle WHERE (id LIKE '$suchWort' ";
	
				# OR + Spaltenname LIKE Suchwort
				while($rowSpalten = mysql_fetch_object($getColums)) {
					$querySuche .= " OR $rowSpalten->Field LIKE '$suchWort'";
				}
				# Klammer am Ende schlie�en-
				$querySuche .= ")";
	
				# Query f�r die Suche
				$suchfeld = mysql_query($querySuche);
	
				# Ausgabe der Suche
				$ergebnis_der_suche .= "<h2>Suchergebnis (= $urspr�nglicheSuche)</h2>";
				while($rowsuch = mysql_fetch_object($suchfeld))
				{
					$ergebnis_der_suche .= "<div class='invSuchErgeb'>";
					$ergebnis_der_suche .= "<a href='$pfad=$rowsuch->id'>" . substr($rowsuch->$name, 0, 20) . "..</a>";
					$ergebnis_der_suche .= "</div>";
				}
				$close = "<a href='?' class='closeSumme'>X</a>";
	
				$return = "<div id='draggable' class='summe'>" . $close . $ergebnis_der_suche . "</div>";
	
				return $return;
			}
		} 
	}
	
	/**
	 * Zeigt die Links an, um die Seiten zu wechseln.
	 * @param unknown $query
	 * @param unknown $proSeite
	 */
	function SeitenZahlen($query, $proSeite) {
		$anzahl = $this->getAmount($query);
		$anzahlSeiten = $anzahl / $proSeite;
	
		for ($i = 1 ; $i <= $anzahlSeiten ; $i++) {
			echo "<a";
			if(isset($_GET['von'])) {
				if($_GET['von'] == $i) {
					echo " class='seitenLinkActive' ";
				} else {
					echo " class='seitenLink' ";
				}
			} else {
				echo " class='seitenLink' ";
			}
			echo " href='?von=$i'>$i</a>";
		}
	}
	
	/**
	 * Zeigt alle Verf�gbaren Seiten an.
	 */
	function seitenAnzeigen($proSeite) {
		# VON BIS HERAUSFINDEN:
		if(!isset($_GET['von'])) {
			# von
			$count1 = 0;
		} else {
			$count1 = $_GET['von'];
			if($count1 == "") {
				$count1 = 1;
			}
			$count1 = $count1 * $proSeite - 1 * $proSeite;
		}
	
		$ergebnis = "LIMIT $proSeite OFFSET $count1";
	
		return $ergebnis;
	}

	/**
	 * gibt die Benutzer ID aus.
	 * @param string $username
	 * @return int
	 */
	function getUserID($username) {
		# namen des Benutzers ausw�hlen:
		$this->connectToDB();
		$selectUsername = "SELECT id, Name FROM benutzer WHERE Name = '$username' LIMIT 1";
		$ergebnisUsername = mysql_query($selectUsername);
		while ($rowUsername = mysql_fetch_object($ergebnisUsername)) {
			$userID = $rowUsername->id;
		}
		# DB CLOSE
		return $userID;

	}

	/**
	 * Gibt den Namen des Benutzers aus.
	 * @param string $username
	 * @return int
	 */
	function getUserName($userid) {
		# namen des Benutzers ausw�hlen:
		$this->connectToDB();
		$selectUsername = "SELECT id, Name FROM benutzer WHERE id = '$userid' LIMIT 1";
		$ergebnisUsername = mysql_query($selectUsername);
		while ($rowUsername = mysql_fetch_object($ergebnisUsername)) {
			$username = $rowUsername->Name;
		}
		
		if(!isset($username) OR $username == "") {
			$username = "Gel�schter Nutzer";
		}
		# 
		return $username;

	}

	/**
	 * gibt die ID der kategorie aus.
	 * @param unknown $kategorieName
	 * @return unknown
	 */
	function getCatID($kategorieName) {

		$selectKat = "SELECT id, kategorie FROM blogkategorien WHERE kategorie = '$kategorieName' LIMIT 1";
		$katergeb = mysql_query($selectKat);
		while($row = mysql_fetch_object($katergeb)) {
			$catID = $row->id;
		}

		return $catID;
	}

	/**
	 * gibt den Namen der Kategorie aus.
	 * @param unknown $kategorieID
	 * @return unknown
	 */
	function getCatName($kategorieID) {

		$selectKat = "SELECT id, kategorie FROM blogkategorien WHERE id = '$kategorieID' LIMIT 1";
		$katergeb = mysql_query($selectKat);
		while($row = mysql_fetch_object($katergeb)) {
			$catName = $row->kategorie;
		}

		return $catName;
	}

	/** Quelle: http://www.webmasterpro.de/coding/article/php-ein-einfaches-flexibles-rechtesystem.html
	 * Einstieg
	 */
	function pruefung($benoetigt) {

		$user = $_SESSION['username'];
		$rightFromCurrentUser="SELECT rights, titel FROM benutzer WHERE Name = '$user'";
		$ergebnis = mysql_query($rightFromCurrentUser);
		$row = mysql_fetch_object($ergebnis);
		$benutzerrechte = $row->rights;
		$rechte = array();
		
		# Menge der Rechte pr�fen:
		$selectAnzahl = "SELECT id FROM userrights";
		$ergebnisAnzahl = mysql_query($selectAnzahl);
		$menge = mysql_num_rows($ergebnisAnzahl);
		# Anzahl pr�fen Ende

		for($i = $menge; $i >= 0; $i--) {
			$wert = pow(2, $i);
			if($benutzerrechte >= $wert) {
				$rechte[] = $wert;
				$benutzerrechte -= $wert;
			}
		}
		if(in_array($benoetigt, $rechte)) {
			return true;
		} else {
			if(isset($row->titel) AND $row->titel == "Super User") {
				return true;
			} else {
				echo "<p class='meldung'>Du bist nicht berechtigt, diese Seite zu sehen.</p>";
				exit;
			}
			
		}
	}
	
	/**
	 * Neue Pr�fungsFunktion
	 * Nimmt den aktuellen Benutzer und pr�ft, ob der Benutzer die Seite sehen darf oder nicht. 
	 * @param unknown $rightID
	 * @return boolean
	 */
	function userHasRightPruefung($rightID) {
		
		$user = $this->getUserID($_SESSION['username']);
		
		$query = "SELECT * FROM rights WHERE besitzer = $user AND right_id = $rightID LIMIT 1";
		
		$userHasRight = $this->getObjektInfo($query);
		
		if(isset($userHasRight->besitzer) AND isset($userHasRight->right_id)) {
			if($userHasRight->besitzer == $user AND $userHasRight->right_id == $rightID) {
				return true;
			} else {
				echo "<p class='meldung'>Du bist nicht berechtigt, diese Seite zu sehen.</p>";
				exit;
			}
		} else {
			echo "<p class='meldung'>Du bist nicht berechtigt, diese Seite zu sehen.</p>";
			exit;
		}
		
	}
	
	/**
	 * Pr�ft, ob der Benutzer das Recht besitzt oder nicht.
	 * Gibt TRUE oder FALSE zur�ck.
	 * @param unknown $id
	 * @param unknown $user
	 * @return boolean
	 */
	function userHasRight($id, $user) {
	
		# Wenn nur gechecked werden soll ob der aktuelle Benutzer zugriff hat:
		if($user == 0) {
			$user = $this->getUserID($_SESSION['username']);
		}
	
		$query = "SELECT * FROM rights WHERE besitzer = $user AND right_id = $id LIMIT 1";
	
		$userHasRight = $this->getObjektInfo($query);
	
		if(isset($userHasRight->besitzer) AND isset($userHasRight->right_id)) {
			if($userHasRight->besitzer == $user AND $userHasRight->right_id == $id) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	
	}

	/**
	 * Alte Rechtecheck Funktion.
	 * @param unknown $benoetigt
	 * @param unknown $benutzerrechte
	 * @return boolean
	 */
	function check($benoetigt, $benutzerrechte) {

		if($benutzerrechte == 0) {
			$user = $_SESSION['username'];
			$rightFromCurrentUser="SELECT id, Name, rights, titel FROM benutzer WHERE Name = '$user'";
			$ergebnis = mysql_query($rightFromCurrentUser);
			$row = mysql_fetch_object($ergebnis);
			$benutzerrechte = $row->rights;
		}

		$rechte = array();

		# Anzahl der Rechte pr�fen
		$selectAnzahl = "SELECT id FROM userrights";
		$ergebnisAnzahl = mysql_query($selectAnzahl);
		$menge = mysql_num_rows($ergebnisAnzahl);
		# Anzahl pr�fen Ende

		for($i = $menge; $i >= 0; $i--) {
			$wert = pow(2, $i);
			if($benutzerrechte >= $wert) {
				$rechte[] = $wert;
				$benutzerrechte -= $wert;
			}
		}
		if(in_array($benoetigt, $rechte)) {
			return true;
		} else {
			if(isset($row->titel) AND $row->titel == "Super User") {
				return true;
			} else {
				return false;
			}
		}
	}

	/**
	 * Pr�ft, ob der User eingeloggt ist. Dabei ist es m�glich nur abzufragen,
	 * ob der User eingeloggt ist oder ob er zu einem $pfad geschickt werden soll.
	 */
	public function logged_in($redirect = "redirect", $pfad = "/flatnet2/index.php" ) {
		$hostname = $_SERVER['HTTP_HOST'];
		$path = dirname($_SERVER['PHP_SELF']);

		if (!isset($_SESSION['angemeldet']) || !$_SESSION['angemeldet'] AND $pfad !="") {
			header("Location: $pfad");
		} else {
			return true;
		}
	}

	/**
	 * Erstellt den Logeintrag
	 * @return boolean
	 */
	function logEintrag($state, $art, $status) {

		if (!isset($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		else {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}

		if($state == true) {
			# Log Eintrag erstellen.
			$username = $_SESSION['username'];
			$userID = $this->getUserID($username);
			$insert="INSERT INTO vorschlaege (text, autor, status, ipadress) VALUES ('$username $art','$userID','$status','$ip')";
			$ergebnis = mysql_query($insert);
			if($ergebnis == true) {
				return true;
			} else {
				return false;
			}
		} else {
			# Log Eintrag erstellen.
			$insert="INSERT INTO vorschlaege (text, autor, status, ipadress) VALUES ('Ein Login ist fehlgeschlagen: $art ($ip).','0','Error','$ip')";
			$ergebnis = mysql_query($insert);
			if($ergebnis == true) {
				return true;
			} else {
				return false;
			}
		}
	}

	/**
	 * Gibt den richtigen Monat aus.
	 * @param unknown $number
	 * @return string
	 */
	function getMonthName($number) {

		if(!isset($number)) {
			$monat = "Fehler";
		}

		if($number == 1) {
			$monat = "Januar";
		}

		if($number == 2) {
			$monat = "Februar";
		}

		if($number == 3) {
			$monat = "M�rz";
		}

		if($number == 4) {
			$monat = "April";
		}

		if($number == 5) {
			$monat = "Mai";
		}

		if($number == 6) {
			$monat = "Juni";
		}

		if($number == 7) {
			$monat = "Juli";
		}

		if($number == 8) {
			$monat = "August";
		}

		if($number == 9) {
			$monat = "September";
		}

		if($number == 10) {
			$monat = "Oktober";
		}

		if($number == 11) {
			$monat = "November";
		}

		if($number == 12) {
			$monat = "Dezember";
		}

		return $monat;
	}

	function subNav($site) {
		if($site == "guildwars") {
			echo "
					<div class='rightOuterBody'>
					<ul>";

			echo "<li><a href='start.php' id='home'>Charakter</a> </li>
					<li><a href='http://gw2cartographers.com/' target='_blank' class='extern'>Maps</a></li>";
			if($this->userHasRight("62", 0) == true) {
				echo "	<li><a href='kalender.php' id='kalender'>Kalender</a></li>";
			}
			
			if($this->userHasRight("63", 0) == true) {
				echo "<li><a href='kosten.php' id='calc'>Kostenrechner</a></li>";
			}
			
			echo "<li><a href='handwerk.php' id='handwerk'>Handwerk</a></li>";
					
			echo "<li><a href='api.php' id='api'>API</a></li>

					</ul>
					</div>";
		}

		if($site == "profil") {
			echo "
					<div class='rightOuterBody'>
					<ul>";
				
			if(!isset($_GET['passChange']) AND !isset($_GET['userlist'])) {
				echo "<li><a href='?' id='marked'>Profil</a></li>	";
			} else {
				echo "<li><a href='?' id=''>Profil</a></li>	";
			}
			if(isset($_GET['passChange'])) {
				echo "<li><a href='?passChange' id='marked'>Benutzereinstellungen</a></li>	";
			} else {
				echo "<li><a href='?passChange' id=''>Benutzereinstellungen</a></li>	";
			}
			if(isset($_GET['userlist'])) {
				echo "<li><a href='?userlist' id='marked'>Mitgliederliste</a></li>";
			} else {
				echo "<li><a href='?userlist' id=''>Mitgliederliste</a></li>";
			}
			echo "</ul>
					</div>";
		}

		if($site == "admin") {
			echo "
					<div class='rightOuterBody'>
					<ul>";
			
			if($this->userHasRight("37", 0) == true) {
				echo "<li><a href='?action=1'";
				if(!isset($_GET['action']) OR $_GET['action'] == 1) { echo " id='marked' ";	} else { echo " id='' "; }
				echo ">Benutzer</a></li>";
			}
			
			if($this->userHasRight("42", 0) == true) {
				echo "<li><a href='?action=2'";
				if(isset($_GET['action']) AND $_GET['action'] == 2) { echo " id='marked' ";	} else { echo " id='' "; }
				echo ">Logs</a></li>";
			}
			
			if($this->userHasRight("51", 0) == true) {
				echo "<li><a href='?action=3'";
				if(isset($_GET['action']) AND $_GET['action'] == 3) { echo " id='marked' ";	} else { echo " id='' "; }
				echo ">Objekte</a></li>";
			}
			
			if($this->userHasRight("48", 0) == true) {
				echo "<li><a href='?action=5'";
				if(isset($_GET['action']) AND $_GET['action'] == 5) { echo " id='marked' ";	} else { echo " id='' "; }
				echo ">Forum</a></li>";
			}
			
			if($this->userHasRight("44", 0) == true) {
				echo "<li><a href='?action=6'";
				if(isset($_GET['action']) AND $_GET['action'] == 6) { echo " id='marked' ";	} else { echo " id='' "; }
				echo ">Rechteverwaltung</a></li>";
			}
			
			if($this->userHasRight("8", 0) == true) {
				echo "<li><a href='/flatnet2/informationen/hilfe.php'";
				echo ">Ank�ndigungen</a></li>";
			}
			
			if($this->userHasRight("67", 0) == true) {
				echo "<li><a href='http://localhost:9090/phpmyadmin/' target='_blank' class='extern'>PMA Local</a></li>
				<li><a href='https://pf-control.de/pma/' target='_blank' class='extern'>PMA Extern</a></li>
				<li><a href='https://pf-control.de/froxlor/customer_index.php?s=b7d502cd3d343f7bffbd493a1be553df' target='_blank' class='extern'>Froxlor</a></li>
				<li><a href='https://mail1.php-friends.de/' class='extern' target='_blank'>E-Mail</a></li>";
				
			}
			
			# Abschluss 
			echo "</ul></div>";
		}

		if($site == "informationen") {
			echo '
					<li><a href="kontakt.php" id="kontakt">Kontakt</a></li>
					<li><a href="hilfe.php" id="doku">Ank�ndigungen</a></li>
					<li><a href="quellen.php" id="quellen">Quellen</a></li>
					<li><a href="impressum.php" id="impressum">Impressum</a></li>';
		}

	}

	/**
	 * Gibt den Header wieder.
	 */
	function header() {
		
		echo '<link rel="apple-touch-icon" sizes="57x57" href="/apple-icon-57x57.png">
				<link rel="apple-touch-icon" sizes="60x60" href="/apple-icon-60x60.png">
				<link rel="apple-touch-icon" sizes="72x72" href="/apple-icon-72x72.png">
				<link rel="apple-touch-icon" sizes="76x76" href="/apple-icon-76x76.png">
				<link rel="apple-touch-icon" sizes="114x114" href="/apple-icon-114x114.png">
				<link rel="apple-touch-icon" sizes="120x120" href="/apple-icon-120x120.png">
				<link rel="apple-touch-icon" sizes="144x144" href="/apple-icon-144x144.png">
				<link rel="apple-touch-icon" sizes="152x152" href="/apple-icon-152x152.png">
				<link rel="apple-touch-icon" sizes="180x180" href="/apple-icon-180x180.png">
				<link rel="icon" type="image/png" sizes="192x192"  href="/android-icon-192x192.png">
				<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
				<link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
				<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
				<link rel="manifest" href="/manifest.json">
				<meta name="msapplication-TileColor" content="#ffffff">
				<meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
				<meta http-equiv="X-UA-Compatible" content="IE=edge" />
				<meta name="theme-color" content="#ffffff">';
		echo '	<script src="/flatnet2/tools/ckeditor/ckeditor.js"></script>';
		echo '<link href="/flatnet2/css/style.css" type="text/css" rel="stylesheet" />
			<link href="/flatnet2/css/style.css" type="text/css" rel="stylesheet" />';
		
		# Quellen f�r JQUERY Scripte
		echo "<script src='//code.jquery.com/jquery-1.10.2.js'></script>
				<script src='//code.jquery.com/ui/1.11.4/jquery-ui.js'></script>";
		
		# Verschiebbare Fenster
		echo '<script> $(function() { $( "#draggable" ).draggable(); }); </script>';
			
		header('Content-Type: text/html; charset=ISO-8859-1');
		session_start();
		echo "<header class='header'>";
			
		echo "<div class='userInfo'>";

		if(isset($_SESSION["username"])) {
			echo "Willkommen " . "<strong><a href='/flatnet2/usermanager/usermanager.php'>" . $_SESSION['username'] . "</a></strong> | ";
			echo "<a href='/flatnet2/includes/logout.php'> Abmelden </a>";
		} else {
			echo "<p class='info'>Du hast versucht auf eine Seite zuzugreifen, f�r die du nicht angemeldet bist. Bitte melde dich an und versuche es erneut<br>
					<a class='rightBlueLink' href='/flatnet2/index.php'>Hauptseite</a></p>
					</div></header>";
			exit;
		}
		echo "		<ul>
				<li><a href='/flatnet2/informationen/impressum.php'>Impressum</a></li>
				<li><a href='/flatnet2/informationen/quellen.php'>Quellen</a></li>";
		if($this->userHasRight("36", 0) == "true") {
			echo "	<li><a href='/flatnet2/admin/control.php'>Admin</a></li>";
		}
		echo "</ul>
				<div id='suche'>
				<form method='get'>
				<input type=text name='suche' value='' placeholder='Suche ...' id='suche' />
				<input type=submit value='OK' id='sucheSubmit' />
				</form>";
		
		# Benachrichtigungscenter:
		$this->benachrichtigungsCenter();
		
		echo "</div></div>";
			
		echo "<ul id='navigation'>";
		
		# �BERSICHT
		if($this->userHasRight("7", 0) == "true") {
			echo "<li>	<a href='/flatnet2/uebersicht.php' id='uebersicht'>�bersicht</a></li>";
		}
		
		# ADRESSBUCH
		if($this->userHasRight("13", 0) == true OR $this->userHasRight("22", 0) == true) {
			echo "<li>	<a href='/flatnet2/datenbank/datenbanken.php' id='adressbuch'>Adressbuch</a></li>";
		}
		
		# FORUM
		if($this->userHasRight("2", 0) == true) {
			echo "<li>	<a href='/flatnet2/forum/index.php' id='forum'>Forum</a></li>";
		}
		
		# GUILDWARS
		if($this->userHasRight("3", 0) == true) {
			echo "<li>	<a href='/flatnet2/guildwars/start.php' id='guildwars'>Guildwars</a></li>";
		}
		
		if($this->userHasRight("65", 0) == true) {
		#	echo "<li>	<a href='/flatnet2/admin/control.php' id='admin'>Administration</a></li>";
			echo "<li>	<a href='/flatnet2/starcitizen/index.php' id='starcitizen'>Starcitizen </a></li>";
		#	echo "<li>	<a href='/flatnetOld/index.php' id=''>	Zeitreise 2013</a></li>";
		#	echo "<li>	<a href='/flatnetOld2/index.php' id=''>	Zeitreise nach 2013</a></li>";
		}
		if($this->userHasRight("11", 0) == "true") {
			echo "<li>	<a href='/flatnet2/fahrten/index.php' id='fahrten'>	Fahrkosten </a></li>";
		}
		
		
		if($this->userHasRight("17", 0) == "true") {
			echo "<li>	<a href='/flatnet2/finanzen/index.php' id='finanzen'>	Finanzen </a>	</li>";
		}
		
		echo "</ul>
				<div id='ueberschrift'>
				<h1><a href='/flatnet2/uebersicht.php'>Steven.NET</a></h1>
				</div>";
		
		$this->ankuendigung();
		echo "</header>";
	}
	
	/**
	 * Zeigt eine Ank�ndigung an
	 */
	function ankuendigung() {
		if($this->userHasRight("24", 0) == true) {
			echo "<div class='InfoCenter'>";
			$docuInfo = $this->getObjektInfo("SELECT *, month(timestamp) AS monat, day(timestamp) AS tag, year(timestamp) AS jahr FROM docu ORDER BY timestamp DESC LIMIT 1");
			if(isset($docuInfo->text)) {
				echo $docuInfo->tag . "." . $docuInfo->monat . "." . $docuInfo->jahr . ": " . $docuInfo->text . "";
			}
			echo "</div>";
		}
	}
	
	/**
	 * Zeigt ein Benachrichtigungscenter an.
	 */
	function benachrichtigungsCenter() {
		
		if($this->userHasRight("25", 0) == true) {
		
			$text = "%<strong>" .$_SESSION['username'] . "</strong>%";
			
			$linkname = "Es gibt nichts neues";
			$class = "rightBlueLink";
			
			$anzahlBenachrichtigungen = $this->getObjektInfo("SELECT count(*) as anzahl 
					FROM blog_kommentare WHERE text LIKE '$text' 
					AND datediff(curdate(), timestamp) < 5
					ORDER BY timestamp DESC");
			
			if($anzahlBenachrichtigungen->anzahl > 0) {
				$linkname = "Du hast " . $anzahlBenachrichtigungen->anzahl . " Nachrichten";
				$class = "highlightedLink";
			}
			
			echo "<a href=\"#\" class='$class' onclick=\"document.getElementById('BenCenter').style.display = 'block'\">$linkname</a>";
			echo "<div class='benCenter' style=\"display: none;\" id=\"BenCenter\">";
			echo "<a href=\"#\"  class='highlightedLink' onclick=\"document.getElementById('BenCenter').style.display = 'none'\">Schlie�en</a>";
			echo "<p class='spacer'>Gr�ne Links sind NEU und graue sind ALT! (max 5 Tage alt)</p>";
			
			# Antworten auf eigene Beitr�ge suchen
			
			$query = "SELECT *
			, day(timestamp) as tag
			, month(timestamp) as monat
			, year(timestamp) as jahr 
			FROM blog_kommentare 
			WHERE text LIKE '$text' 
			AND datediff(curdate(), timestamp) < 5
			ORDER BY timestamp DESC";
			
			$gefundeneForeneintraege = $this->getObjectsToArray($query);
			
			
			if($gefundeneForeneintraege > 0) { # geht das?
				
				for($i = 0 ; $i < sizeof($gefundeneForeneintraege) ; $i++) {
					
					$blogidInfos = $this->getObjektInfo("SELECT * FROM blogtexte WHERE id = '". $gefundeneForeneintraege[$i]->blogid ."' LIMIT 1 ");
					
					echo "<a href='/flatnet2/blog/blogentry.php?showblogid=" 
							. $gefundeneForeneintraege[$i]->blogid 
							. "#KommID". $gefundeneForeneintraege[$i]->id 
							. "' id='benachrichtigung'>
							". $gefundeneForeneintraege[$i]->tag
					.".". $gefundeneForeneintraege[$i]->monat
					.".". $gefundeneForeneintraege[$i]->jahr
					." von " . $this->getUserName($gefundeneForeneintraege[$i]->autor)
					. " zum Thema <strong>".$blogidInfos->titel ."</strong></a><br>";
					
				}
				
			}
					
			echo "</div>";
		}
	}
	
	/**
	 * Zeigt den Titel des Benutzers an.
	 */
	function showTitel() {
		# Titel anzeigen:
		$userID = $this->getUserID($_SESSION['username']);
		$userTitel = $this->getObjektInfo("SELECT id, titel FROM benutzer WHERE id = '$userID' LIMIT 1");
		if(isset($userTitel) AND $userTitel->titel != "") {
			echo "<div class='spacer'><a class='rightGreenLink' href='/flatnet2/forum/index.php?blogcategory=5'>Titel: $userTitel->titel</a></div>";
		}
	}
	
	
	/**
	 * Erm�glicht das Abfragen, ob ein Objekt in einer Datenbank bereits existiert.
	 * und gibt True oder False zur�ck.
	 */
	function objectExists($table, $column, $object) {
		$check = "SELECT * FROM $table WHERE $column LIKE '$object' LIMIT 1";
		$checkergebnis = mysql_query($check);
		$row = mysql_fetch_object($checkergebnis);

		if($row == "") {
			return false;
		} else {
			return true;
		}
	}
	
} # CLASS ENDE
?>