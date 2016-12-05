<?php
/**
 * @history Steven 18.08.2014 angelegt.
 * @history Steven 01.09.2014 selektor geschrieben
 * @author Steven
 * Führt die Aktionen zwischen der Site und dem SQL Server durch.
 */
class sql {
	
	function connectToDB() {
		$this->connectToDBNewWay();
	}

	/**
	 * Stellt die Verbindung zur Datenbank her.
	 * 
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
	 * Function um Inhalte aus einer Tabelle zu löschen.
	 * @param unknown $tabelle
	 */
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
					
				if($this->sql_insert_update_delete($loeschQuery) == true) {
					echo "<p class='erfolg'>Eintrag gelöscht.</p>";
				} else {
					echo "<p class='meldung'>Fehler beim löschen!</p>";
				}

			}
		}

	}
	
	/**
	 * 
	 * @param unknown $query
	 */
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

					
				if($this->sql_insert_update_delete($query) == true) {
					echo "<p class='erfolg'>Erfolgreich gelöscht</p>";
				} else {
					echo "<p class='meldung'>Fehler beim löschen!</p>";
				}

			}
		}

	}

	/** Type = BOOLEAN
	 * Führt die Funktionen Insert, Update und Delete durch.
	 * Gibt true oder false zurück.
	 * Loggt nur, wenn Änderungen tatsächlich durchgeführt werden.
	 */
	function sql_insert_update_delete($query) {
		
		$db = $this->connectToDBNewWay();
		
		$affected_rows = $db->exec($query);
		
		if($affected_rows > 0) {
			# Log durchführen:
			$this->logme($query);
			return true;
		} else {
			return false;
		}
		
	}
	
	/**
	 * Extra sql Methode für gw.class.php in Zeile 1446
	 * Hier wird nicht geloggt.
	 * @param unknown $query
	 * @return boolean
	 */
	function sql_insert_update_delete_hw($query) {
		$db = $this->connectToDBNewWay();
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
		$text_for_log = $username ." : ". strip_tags(stripslashes($received_query));
		$query = "INSERT INTO log (benutzer, log_text, ip_adress) VALUES (\"$id\",\"$text_for_log\",\"$ip\")";
		# Logeintrag hinzufügen
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
	#	echo "<p class='dezentInfo'>" .$query . "</p>";
		
		$db = $this->connectToDBNewWay();
		$stmt = $db->query($query);
		$results = $stmt->fetchAll(PDO::FETCH_OBJ);
						
		return $results;
	}
	
	/**
	 * Läd Informationen in ein Objekt.
	 * @param unknown $query
	 * @return unknown
	 */
	function getObjectsToArray($query) {
		 $this->getObjektInfo($query);
	}
	
	/**
	 * Ermöglicht das Abfragen, ob ein Objekt in einer Datenbank bereits existiert.
	 * und gibt True oder False zurück.
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