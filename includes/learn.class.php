<?php
/**
 * Ermöglicht das lernen von Inhalten des Studiums
 * @author BSSCHOE
 * Erstellt am 25.11.2015
 */

include 'objekt/functions.class.php';

class learn extends functions {
	
	/**
	 * Erstellt den benötigten DB Eintrag
	 */
	function dbInhalt() {
		
		# Tabelle 1: Lernkarte
		$createTable1 = "CREATE TABLE `flathacksql1`.`learnlernkarte` ( `id` INT NOT NULL AUTO_INCREMENT , `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `besitzer` INT NOT NULL , `kategorie` INT NOT NULL , `frage` VARCHAR(250) NOT NULL , `loesung` TEXT NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;";
		$id;
		
		$timestamp;
		
		$besitzer;
		
		$kategorie;
		
		$frage;
		
		$loesung;
		
		# Tabelle 2: Lernkategorien
		
		$createTable2 = "CREATE TABLE `flathacksql1`.`learnkategorie` ( `id` INT NOT NULL AUTO_INCREMENT , `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `kategorie` VARCHAR(250) NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;";
		$createTable2Alter = "ALTER TABLE `learnkategorie` ADD `besitzer` INT NOT NULL AFTER `kategorie`;";
		$createTable2Alter2 = "ALTER TABLE `learnkategorie` ADD `public` BOOLEAN NOT NULL AFTER `besitzer`;";
		
		$id2;
		
		$timestamp2;
		
		$kategorie2;
		
	}
	
	/**
	 * Zeigt die eigenen Einträge
	 */
	function showKategorien() {
		$userid = $this->getUserID($_SESSION['username']);
		$getkategories = $this->getObjectsToArray("SELECT * FROM learnkategorie WHERE besitzer = $userid");
		
		
		echo "<div class='showKategorien'>";
		echo "<p>Bitte eine Kategorie wählen</p>";
		echo "<ul>";
		for ($i = 0; $i < sizeof($getkategories); $i++) {
			
				echo "<li><a href='?kategorie=" . $getkategories[$i]->id . "'>" . $getkategories[$i]->kategorie . "</a></li>";
			
		}
		echo "</ul>";
		echo "</div>";
		
	}
	
	/**
	 * Zeigt die Einträge der Kategorie
	 */
	function showKategorieEintraege() {
		
		echo "<div class='showEintragInLearn'>";
		
		if(isset($_GET['kategorie'])) {
			if(is_numeric($_GET['kategorie']) == true) {
				$kategorie = $_GET['kategorie'];
				$this->showOptionsForKategorie($kategorie);
				
				$userid = $this->getUserID($_SESSION['username']);
				
				$getKategorieInfos = $this->getObjektInfo("SELECT * FROM learnkategorie");
				
				if($getKategorieInfos->public == 1) {
					$getEintraege = $this->getObjectsToArray("SELECT * FROM learnlernkarte WHERE kategorie = $kategorie");
				} else {
					$getEintraege = $this->getObjectsToArray("SELECT * FROM learnlernkarte WHERE besitzer = $userid AND kategorie = $kategorie");
				}
				
				$this->editEintrag();
				
				for ($i = 0 ; $i < sizeof($getEintraege); $i++) {
						
					echo "<div class='learnEintrag'>";
					echo "<a href=\"#\" class='rightGreenLink' onclick=\"document.getElementById('learnEintrag$i').style.display = 'block'\"> Lösung anzeigen </a>";
					echo "<a href=\"#\"  class='rightRedLink' onclick=\"document.getElementById('learnEintrag$i').style.display = 'none'\">Verstecken</a>";
					
					echo "<h3><a name='#eintrag". $Eintraege[$i]->id ."'>" . $getEintraege[$i]->frage . "</a></h3>";
						echo "<div class='loesungEintrag' style=\"display: none;\" id=\"learnEintrag$i\">";
							
							echo "<form method=post>";
							echo "<textarea name='editEintrag' class='ckeditor' id='ckeditor'>" . $getEintraege[$i]->loesung . "</textarea>";
							echo "<input type=hidden name=editID value='".$getEintraege[$i]->id."' />";
							echo "<input type=submit name=saveEintrag$i value='Änderungen speichern' />";
							echo "</form>";
							echo "<p id='bottom'>Gewusst? <a class='greenLink' href='?yes'>Ja</a> / <a class='highlightedLink' href='?no'>Nein</a></p>";
						
						echo "</div>";
					echo "</div>";
				}
				
			}
		}
		
		echo "</div>";
		
	}
	
	function showKategorieUeberschriften() {
		
		if(isset($_GET['kategorie'])) {
			if(is_numeric($_GET['kategorie']) == true) {
				
				echo "<h2>Diese Kategorie</h2>";
				
				$kategorie = $_GET['kategorie'];
				
				$userid = $this->getUserID($_SESSION['username']);
				
				$getKategorieInfos = $this->getObjektInfo("SELECT * FROM learnkategorie");
				
				if($getKategorieInfos->public == 1) {
					$getEintraege = $this->getObjectsToArray("SELECT * FROM learnlernkarte WHERE kategorie = $kategorie");
				} else {
					$getEintraege = $this->getObjectsToArray("SELECT * FROM learnlernkarte WHERE besitzer = $userid AND kategorie = $kategorie");
				}
				
				echo "<ul>";
					for ($i = 0; $i < sizeof($getEintraege); $i++) {
						echo "<li>" . $getEintraege[$i]->frage . "</li>";
					}
				echo "</ul>";
			}
			
		}
		
	}
	
	/**
	 * Zeigt die Optionen für die gegenwärtige Kategorie an.
	 * @param unknown $kategorie
	 */
	function showOptionsForKategorie($kategorie) {
		echo "<a class='buttonlink' href='?kategorie=$kategorie&createNew'>Neuen Eintrag erstellen</a>";
	}
	
	/**
	 * Zeigt Optionen für die Seite an.
	 */
	function showOptionsForAll() {
		echo "<a href='?createNewCat' class='buttonlink'>Neue Kategorie erstellen</a>";
	}
	

	/**
	 * Erstellt einen neuen Eintrag in der gewählten Kategorie
	 */
	function createNewEintragInKategorie() {
		if(isset($_GET['createNew']) AND isset($_GET['kategorie'])) {
			echo "<div class='createNewEintragInKategorie'>";
			echo "<a href='?' class='highlightedLink'>X</a>";
			
				echo "<form method=post>";
				
					if(isset($_POST['frage'])) {
						$frage = $_POST['frage'];
					} else {
						$frage ="";
					}
					
					if(isset($_POST['loesung'])) {
						$loesung = $_POST['loesung'];
					} else {
						$loesung = "";
					}
					
					echo "<input type=text name=frage placeholder='Frage eingeben' value='$frage' />";	
					
					echo "<textarea name='loesung' class='ckeditor'>$loesung</textarea>";
					
					echo "<input type=submit name=absenden value='Speichern' />";
				
 				echo "</form>";
			
			echo "</div>";
		}
		
		/**
		 * Speichert den Eintrag
		 */
		if(isset($_POST['frage']) AND isset($_POST['loesung']) AND isset($_GET['kategorie'])) {
			if($_POST['frage'] != "" AND $_POST['loesung'] != "") {
				$frage = $_POST['frage'];
				$kategorie = $_GET['kategorie'];
				$loesung = $_POST['loesung'];
				$userid = $this->getUserID($_SESSION['username']);
				
				$query = "INSERT INTO learnlernkarte (besitzer, kategorie, frage, loesung) VALUES ('$userid','$kategorie','$frage','$loesung')";
				
				if($this->sql_insert_update_delete($query) == true) {
					echo "<p class='erfolg'>Eintrag wurde gespeichert</p>";
				} else {
					echo "<p class='meldung'>Es gab einen Fehler! Der Eintrag konnte nicht gespeichert werden.</p>";
				}
			}
		}
	}
	
	/**
	 * Ermöglicht das editieren von Einträgen einer Kategorie.
	 */
	function editEintrag() {
		if(isset($_POST['editEintrag'])) {
			$text = $_POST['editEintrag'];
			$id = $_POST['editID'];
			
			if($id != "" AND $text != "") {
				if($this->sql_insert_update_delete("UPDATE learnlernkarte SET loesung = '$text' WHERE id = '$id' ") == true) {
					echo "<p class='erfolg'>Eintrag wurde geändert!</p>";
				} else {
					echo "<p class='meldung'>Änderung nicht möglich!</p>";;
				}
			}
		}
	}
	
	function deleteEintrag() {
		
	}
	
	function createNewKategorie() {
		
		if(isset($_POST['newcat'])) {
			
			$newcat = $_POST['newcat'];
			$userid = $this->getUserID($_SESSION['username']);
			if(isset($_POST['public'])) {
				$public = $_POST['public'];
			} else {
				$public = 0;
			}
			
			
			$query = "INSERT INTO learnkategorie (besitzer, kategorie, public) VALUES ('$userid','$newcat','$public')";
			
			if($this->sql_insert_update_delete($query) == true) {
				echo "<p class='erfolg'>Eintrag wurde gespeichert</p>";
			} else {
				echo "<p class='meldung'>Es gab einen Fehler! Der Eintrag konnte nicht gespeichert werden $public.</p>";
			}
			
		}
		
		if(isset($_GET['createNewCat'])) {
			echo "<div class='learnNewCat'>";
			echo "<form method=post>";
			
				echo "<input type=text name=newcat value='' placeholder='Kategorienamen' />";
				
				echo "<p id='oeffentlich'>Öffentlich? " . "<input type=checkbox name=public value=1 /></p>";
				
				echo "<input type=submit name=submit value=Absenden />";
			
			echo "</form></div>";
		}
		
	}
	
	function editKategorie() {
		
	}
	
	function deleteKategorie() {
		
	}
	
}