<?php
/**
 * @history Steven 18.08.2014 angelegt.
 * @history Steven 01.09.2014 selektor geschrieben
 * @author Steven
 * Führt die Aktionen zwischen der Site und dem SQL Server durch.
 */
class sql {

	/**
	 * Stellt die Verbindung zum SQL Server her, dafür werden die Daten direkt in die Class
	 * geschrieben.
	 */
	function connectToDB() {

	#	error_reporting(0);

		$mode = "phpfriends";

		# Bauen der ErrorMessages:
		$errordbconnect = "<html>";
		$errordbconnect .= '<link href="/flatnet2/css/style.css" type="text/css" rel="stylesheet" />';
		$errordbconnect .= "<div class='mainbody'>";

		$errorsqlconnect = "<html>";
		$errorsqlconnect .= '<link href="/flatnet2/css/style.css" type="text/css" rel="stylesheet" />';
		$errorsqlconnect .= "<div id='wrapper'><div class='mainbody'>";

		if($mode == "phpfriends") {
			# PHP Friends
			$host = "127.0.0.1";
			$sqlusername = "devUser";
			$sqluserpassword = "";
			$sqldatabase = "flathacksql1";
			$errorsqlconnect .= "<p class=''>Quaggan kann Seite nicht finden. Nur 404. Quaggan traurig. Fuuuu</p>";
			$errorsqlconnect .= "<img src='/flatnet2/images/fehler/404.png' name='' alt='404 Fehler'>";
			$errordbconnect .= "<p class=''>Quaggan kann Seite nicht finden. Nur 404. Quaggan traurig. Fuuuu</p>";
			$errordbconnect .= "<img src='/flatnet2/images/fehler/404.png' name='' alt='404 Fehler'>";
		}

		$errordbconnect .= "</div></div></html>";
		$errorsqlconnect .= "</div></div></html>";
		# Stellt die Verbindung her:
		
		mysql_connect($host, $sqlusername, $sqluserpassword);
		
		mysql_select_db($sqldatabase) or die ($errordbconnect);
	}

	/**
	 * Stellt die Verbindung zu einer anderen Datenbank her.
	 * @param unknown $db
	 * @param unknown $username
	 * @param unknown $password
	 */
	function connectToSpecialDB($db, $username, $password) {
		$mode = "phpfriends";

		# Bauen der ErrorMessages:
		$errordbconnect = "<html>";
		$errordbconnect .= '<link href="/flatnet2/css/style.css" type="text/css" rel="stylesheet" />';
		$errordbconnect .=  '<link rel="icon" href="/flatnet2/favicon.jpg" type="image/x-icon" />';
		$errordbconnect .= "<div class='mainbody'>";
		$errorsqlconnect = "<html>";
		$errorsqlconnect .= '<link href="/flatnet2/css/style.css" type="text/css" rel="stylesheet" />';
		$errorsqlconnect .= '<link rel="icon" href="/flatnet2/favicon.jpg" type="image/x-icon" />';
		$errorsqlconnect .= "<div id='wrapper'><div class='mainbody'>";

		if($mode == "phpfriends") {
			# PHP Friends
			$host = "127.0.0.1";
			$sqlusername = $username;
			$sqluserpassword = $password;
			$sqldatabase = $db;
			$errorsqlconnect .= "Verbindung zum SQL Server fehlgeschlagen.";
			$errordbconnect .= "Datenbank konnte nicht ausgewählt werden";
		}

		$errordbconnect .= "</div></div></html>";
		$errorsqlconnect .= "</div></div></html>";
		# Stellt die Verbindung her:

		mysql_connect($host, $sqlusername, $sqluserpassword) or die ($errorsqlconnect);
		mysql_select_db($sqldatabase) or die ($errordbconnect);
	}

	function sqlDelete($tabelle) {

		if(isset($_GET['loeschen']) AND isset($_GET['loeschid'])) {
				
			$id = $_GET['loeschid'];
				
			if($id > 0 AND !isset($_POST['jaloeschen'])) {

				# Abfrage, ob der User den Artikel wirklich löschen will.
				echo "<form method=post>";
				echo "<p class='meldung'>Soll der Eintrag gelöscht werden?<br>";
				echo "<input type = hidden value = '$id' name ='id' readonly />";
				echo "<input type=submit name='jaloeschen' value='Ja'/>
						<a href='?' class='buttonlink'>Nein</a></p>";
				echo "</form>";
			}
		}

		/*
		 * Führt die Löschung durch.
		*/
		if(isset($_POST['jaloeschen'])) {
			$jaloeschen = isset($_POST['jaloeschen']) ? $_POST['jaloeschen'] : '';
			$loeschid = isset($_POST['id']) ? $_POST['id'] : '';
			if($loeschid) {

				# Durchführung der Löschung.
				$loeschQuery = "DELETE FROM `$tabelle` WHERE `id` = $loeschid";
				$ergebnis = mysql_query($loeschQuery);
					
				if($ergebnis == true) {
					echo "<p class='erfolg'>Eintrag gelöscht.</p>";
				} else {
					echo "<p class='meldung'>", mysql_error(), "</p>";
				}

			}
		}

	}

	function sqlDeleteCustom($query) {

		if(isset($_GET['loeschen']) AND isset($_GET['loeschid'])) {

			$id = $_GET['loeschid'];

			if($id > 0 AND !isset($_POST['jaloeschen'])) {

				# Abfrage, ob der User den Artikel wirklich löschen will.
				echo "<form method=post>";
				echo "<p class='meldung'>Soll die Löschung durchgeführt werden?<br>";
				echo "<input type = hidden value = '$id' name ='id' readonly />";
				echo "<input type=submit name='jaloeschen' value='Ja'/>
						<a href='?' class='buttonlink'>Nein</a></p>";
				echo "</form>";
			}
		}

		/*
		 * Führt die Löschung durch.
		*/
		if(isset($_POST['jaloeschen'])) {
			$jaloeschen = isset($_POST['jaloeschen']) ? $_POST['jaloeschen'] : '';
			$loeschid = isset($_POST['id']) ? $_POST['id'] : '';
			if($loeschid) {

				# Durchführung der Löschung.
				$ergebnis = mysql_query($query);
					
				if($ergebnis == true) {
					echo "<p class='erfolg'>Erfolgreich gelöscht!</p>";
				} else {
					echo "<p class='meldung'>", mysql_error(), "</p>";
				}

			}
		}

	}

	/** Type = BOOLEAN
	 * Führt die Funktionen Insert, Update und Delete durch.
	 * Gibt true oder false zurück.
	 */
	function sql_insert_update_delete($query) {

		$ergebnis = mysql_query($query);
		if($ergebnis == true) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Gibt die Menge der Zeilen einer Anfrage wieder.
	 * @param unknown $query
	 * @return unknown
	 */
	function getAmount($query) {
		$ergebnis = mysql_query($query);
		$menge = mysql_num_rows($ergebnis);
		return $menge;
	}

	/**
	 * Speichert die Informationen !eines! Objekts in der Variable $row
	 * @param unknown $query
	 * @return unknown
	 */
	function getObjektInfo($query) {
		$ergebnis = mysql_query($query);
		$row = mysql_fetch_object($ergebnis);
		
		return $row;
	}
	
	/**
	 * Läd Informationen in ein Objekt.
	 * @param unknown $query
	 * @return unknown
	 */
	function getObjectsToArray($query) {
		$ergebnis = mysql_query($query);
		$i = 0;
		while($row = mysql_fetch_object($ergebnis)) {
			$objects[$i] = $row;
			$i = $i + 1;
		}

		if(isset($objects)) {
			return $objects;
		}

	}
	
	/**
	 * Ermöglicht das Abfragen, ob ein Objekt in einer Datenbank bereits existiert.
	 * und gibt True oder False zurück.
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

}

?>