<?php
/**
 * Ermöglicht das speichern von Einträgen der Doku innerhalb der Datenbank.
 * @author Steven Schödel
 * @history 03.02.2015 angelegt.
 * @history 10.02.2015 Überarbeitung auf Objektorientierung.
 *
 */

include 'objekt\functions.class.php';

class docu extends functions {

	/**
	 * Zeigt die Dokumentation, samt Eingabefeld an.
	 */
	function showDocu() {
		
		# Eingabefeld anzeigen.
		$this->showEingabeFeld();
		
		# Eintrag in DB speichern.
		if(isset($_POST['docuText'])) {
			$this->setDocuEintrag($_POST['docuText']);
		}
		
		if(isset($_GET['loeschid'])) {
			$this->deleteDocuEintrag();
		}
		
		# Ausgabe der Doku aus der Datenbank
		$query = "SELECT *, month(timestamp) AS monat, day(timestamp) AS tag, year(timestamp) AS jahr FROM docu ORDER BY timestamp DESC";
		$ergebnis = mysql_query($query);
		echo  "<table class='flatnetTable'>";
		echo  "<thead><td id='datum'>Datum</td><td id='text'>Text</td><td></td></thead>";
		while ($row = mysql_fetch_object($ergebnis)) {
			echo  "<tbody><td>" . $row->tag . "." . $row->monat . "." . $row->jahr . "</td><td>" . $row->text . "</td><td><a href='?loeschen&loeschid=$row->id' class='highlightedLink'>X</a></td></tbody>";
		}

		echo  "</table>";
	}
	
	/**
	 * Zeigt das Eingabefeld für neuen Dokueintrag
	 */
	function showEingabeFeld() {
		# Neuer Eintrag in die Doku:
		if($this->userHasRight("19", 0) == true) {
			echo  "<form action='hilfe.php' method=post>";
			echo  "<input type='text' name='docuText' id='titel' value='' placeholder='Beschreibung' />";
			echo  "<input type='submit' name='sendDocu' value='Absenden' />";
			echo  "</form>";
		}
	}
	
	/**
	 * Speichert eintrag in die Dokumentation.
	 * @param unknown $text
	 * @param unknown $autor
	 */
	function setDocuEintrag($text) {
		# Speichert den< Eintrag in die Datenbank.
			if($_POST['docuText'] == "") {
				echo "<p class='meldung'>Feld ist leer.</p>";
			} else {
				$autor = $this->getUserID($_SESSION['username']);
				$insert = "INSERT INTO docu (text, autor) VALUES ('$text','$autor')";
				$ergebnis2 = mysql_query($insert);
				if($ergebnis2 == true) {
					echo "<p class='erfolg'>Eintrag gespeichert.</p>";
				} else {
					echo "<p class='meldung'>Fehler beim speichern in der Datenbank.</p>";
				}
			}
	}
	
	/**
	 * Löscht einen Docueintrag.
	 */
	function deleteDocuEintrag() {
		if($this->userHasRight("66", 0) == true) {
			$this->sqlDelete("docu");
		} 
	}
	
}

?>