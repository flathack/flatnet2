<?php
/**
 * @history Steven 18.08.2014 angelegt.
 * @history Steven 01.09.2014 selektor geschrieben
 * @author Steven
 * F�hrt die Aktionen zwischen der Site und dem SQL Server durch.
 */
class sql {
	
	function connectToDB() {
		$this->connectToDBNewWay();
	}

	/**
	 * Stellt die Verbindung zum SQL Server her, daf�r werden die Daten direkt in die Class
	 * geschrieben.
	 */
	function connectToDBOldWay() {
		/*
		
		echo "<p class='meldung'>connectToDBOldWay. Diese Funktion ist veraltet!</p>";

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
			$host = "localhost";
			$sqlusername = "62_flathacksql1";
			$sqluserpassword = "12141214";
			$sqldatabase = "62_flathacksql1";
			$errorsqlconnect .= "<p class=''>Quaggan kann Seite nicht finden. Nur 404. Quaggan traurig.</p>";
			$errorsqlconnect .= "<img src='/flatnet2/images/fehler/404.png' name='' alt='404 Fehler'>";
			$errordbconnect .= "<p class=''>Quaggan kann Seite nicht finden. Nur 404. Quaggan traurig.</p>";
			$errordbconnect .= "<img src='/flatnet2/images/fehler/404.png' name='' alt='404 Fehler'>";
		}

		$errordbconnect .= "</div></div></html>";
		$errorsqlconnect .= "</div></div></html>";
		# Stellt die Verbindung her:
		
		mysql_connect($host, $sqlusername, $sqluserpassword);
		
		mysql_select_db($sqldatabase) or die ("<body id='wrapper'>" . $errordbconnect . "<div class='mainbody'><img src='/flatnet2/images/fehler/dbError.png' name='' alt='Fehler'></div></body>");
		
		*/
	}

	/**
	 * New Connect
	 * @todo
	 */
	function connectToDBNewWay() {
		try {
		$dbname = $this->getDBName();
		$db = new PDO("mysql:host=localhost;dbname=$dbname", "62_flathacksql1", "12141214");
		} catch (Exception $e) {
			$css = '<link href="/flatnet2/css/error.css" type="text/css" rel="stylesheet" />';
			$errorText = "<p class='info'>Datenbank Error</p>";
			$bild = "<img src='/flatnet2/images/fehler/grund.PNG' name='' alt='DatenbankError'>";
			$errorBeschreibung = "<p>Quggan traurig, Quaggan kann die Datenbank nicht finden, nur Errors.</p>";
			die ("<body><div><div class='wrapper'>" 
					.$css. $errorText . $bild . $errorBeschreibung 
					."</div></div></body></html>");
		}
		return $db;
	}
	
	/**
	 * Setzt den DB Namen fest.
	 * @return string
	 */
	function getDBName() {
		$name = "62_flathacksql1";
		return $name;
	}
	
	/**
	 * Stellt die Verbindung zu einer anderen Datenbank her.
	 * @param unknown $db
	 * @param unknown $username
	 * @param unknown $password
	 */
	function connectToSpecialDB($db, $username, $password) {
		echo "<p class='meldung'>connectToSpecialDB. Diese Funktion ist veraltet!</p>";
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
			$errordbconnect .= "Datenbank konnte nicht ausgew�hlt werden";
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

				# Abfrage, ob der User den Artikel wirklich l�schen will.
				echo "<form method=post>";
				echo "<p class='meldung'>Soll der Eintrag gel�scht werden?<br>";
				echo "<input type = hidden value = '$id' name ='id' readonly />";
				echo "<input type=submit name='jaloeschen' value='Ja'/>
						<a href='?' class='buttonlink'>Nein</a></p>";
				echo "</form>";
			}
		}

		/*
		 * F�hrt die L�schung durch.
		*/
		if(isset($_POST['jaloeschen'])) {
			$jaloeschen = isset($_POST['jaloeschen']) ? $_POST['jaloeschen'] : '';
			$loeschid = isset($_POST['id']) ? $_POST['id'] : '';
			if($loeschid) {

				# Durchf�hrung der L�schung.
				$loeschQuery = "DELETE FROM `$tabelle` WHERE `id` = $loeschid";
					
				if($this->sql_insert_update_delete($loeschQuery) == true) {
					echo "<p class='erfolg'>Eintrag gel�scht.</p>";
				} else {
					echo "<p class='meldung'>Fehler beim l�schen!</p>";
				}

			}
		}

	}

	function sqlDeleteCustom($query) {

		if(isset($_GET['loeschen']) AND isset($_GET['loeschid'])) {

			$id = $_GET['loeschid'];

			if($id > 0 AND !isset($_POST['jaloeschen'])) {

				# Abfrage, ob der User den Artikel wirklich l�schen will.
				echo "<form method=post>";
				echo "<p class='meldung'>Soll die L�schung durchgef�hrt werden?<br>";
				echo "<input type = hidden value = '$id' name ='id' readonly />";
				echo "<input type=submit name='jaloeschen' value='Ja'/>
						<a href='?' class='buttonlink'>Nein</a></p>";
				echo "</form>";
			}
		}

		/*
		 * F�hrt die L�schung durch.
		*/
		if(isset($_POST['jaloeschen'])) {
			$jaloeschen = isset($_POST['jaloeschen']) ? $_POST['jaloeschen'] : '';
			$loeschid = isset($_POST['id']) ? $_POST['id'] : '';
			if($loeschid) {

					
				if($this->sql_insert_update_delete($query) == true) {
					echo "<p class='erfolg'>Erfolgreich gel�scht</p>";
				} else {
					echo "<p class='meldung'>Fehler beim l�schen!</p>";
				}

			}
		}

	}

	/** Type = BOOLEAN
	 * F�hrt die Funktionen Insert, Update und Delete durch.
	 * Gibt true oder false zur�ck.
	 * Loggt nur, wenn �nderungen tats�chlich durchgef�hrt werden.
	 */
	function sql_insert_update_delete($query) {
		
		$db = $this->connectToDBNewWay();
		
		$affected_rows = $db->exec($query);
		
		if($affected_rows > 0) {
			# Log durchf�hren:
			$this->logme($query);
			return true;
		} else {
			return false;
		}
		
	}
	
	/**
	 * Extra sql Methode f�r gw.class.php in Zeile 1446
	 * Hier wird nicht geloggt.
	 * @param unknown $query
	 * @return boolean
	 */
	function sql_insert_update_delete_hw($query) {
	
		$db = $this->connectToDBNewWay();
	
		# Log durchf�hren:
		# $this->logme($query);
	
		$affected_rows = $db->exec($query);
	
		if($affected_rows > 0) {
			return true;
		} else {
			return false;
		}
	
	
	}
	
	/**
	 * Loggt transaktionen auf der Datenbank
	 * @param unknown $received_query
	 * @return boolean
	 */
	function logme($received_query) {
		if(isset($_SESSION['username'])) {
			$username = $_SESSION['username'];
		} else {
			$username = "Unknown-User";
		}
		
		# UserID bekommen
		$getuserid = $this->getObjektInfo("SELECT id, Name FROM benutzer WHERE Name='$username' LIMIT 1");
		if(!isset($getuserid[0]->id)) {
			$id=0;
		} else {
			$id=$getuserid[0]->id;
		}
		
		# IP herausfinden:
		if (!isset($_SERVER['HTTP_X_FORWARDED_FOR']) ) { $ip = $_SERVER['REMOTE_ADDR']; } else { $ip = $_SERVER['HTTP_X_FORWARDED_FOR']; }
		
		# Querytext bauen:
		$text_for_log = $username ." : ". $received_query;
		$query = "INSERT INTO log (benutzer, log_text, ip_adress) VALUES (\"$id\",\"$text_for_log\",\"$ip\")";
		# Logeintrag hinzuf�gen
		$db = $this->connectToDBNewWay();
		$affected_rows = $db->exec($query);
		
		if($affected_rows > 0) {
			return true;
		} else {
			echo "<p class='dezentInfo'>Es konnte nicht geloggt werden: $query</p>";
		}
		
	}

	/**
	 * Gibt die Menge der Zeilen einer Anfrage wieder.
	 * @param unknown $query
	 * @return unknown
	 */
	function getAmount($query) {
		
		$db = $this->connectToDBNewWay();
		
		$stmt = $db->query($query);
		$count = $stmt->rowCount();
		
		return $count;
	}

	/**
	 * Speichert die Informationen !eines! Objekts in der Variable $row
	 * @param unknown $query
	 * @return unknown
	 */
	function getObjektInfo($query) {
		$db = $this->connectToDBNewWay();
		$stmt = $db->query($query);
		$results = $stmt->fetchAll(PDO::FETCH_OBJ);
						
		return $results;
	}
	
	/**
	 * L�d Informationen in ein Objekt.
	 * @param unknown $query
	 * @return unknown
	 */
	function getObjectsToArray($query) {
		 $this->getObjektInfo($query);
	}
	
	/**
	 * Erm�glicht das Abfragen, ob ein Objekt in einer Datenbank bereits existiert.
	 * und gibt True oder False zur�ck.
	 */
	function objectExists($query) {
		$row = $this->getObjektInfo($query);	
		if(isset($row[0])) {
			return true;
		} else {
			return false;
		}
	}

}

?>