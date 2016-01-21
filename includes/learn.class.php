<?php
/**
 * Ermöglicht das lernen von Inhalten des Studiums
 * @author BSSCHOE
 * Erstellt am 25.11.2015
 */

include 'objekt/functions.class.php';

class learn extends functions {
	
	/**
	 * Erstellt den benÃ¶tigten DB Eintrag
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
	 * Zeigt die EintrÃ¤ge der Kategorie
	 */
	function showKategorieEintraege() {
		
		echo "<div class='showEintragInLearn'>";
		
		if(isset($_GET['kategorie'])) {
			if(is_numeric($_GET['kategorie']) == true) {
				$kategorie = $_GET['kategorie'];
				$this->showOptionsForKategorie($kategorie);
				
				$userid = $this->getUserID($_SESSION['username']);
				
				$getKategorieInfos = $this->getObjektInfo("SELECT * FROM learnkategorie WHERE id = '$kategorie' ");
				
				if($getKategorieInfos[0]->public == 1) {
					$getEintraege = $this->getObjektInfo("SELECT * FROM learnlernkarte WHERE kategorie = $kategorie");
				} else {
					$getEintraege = $this->getObjektInfo("SELECT * FROM learnlernkarte WHERE besitzer = $userid AND kategorie = $kategorie");
				}
				
				$this->editEintrag();
				
				for ($i = 0 ; $i < sizeof($getEintraege); $i++) {
					
					$status = $this->getStatusFromLernID($userid, $getEintraege[$i]->id);
						
					echo "<div class='learnEintrag'>";
					$idfornav = $getEintraege[$i]->id;
					echo "<a href=\"#eintrag$idfornav\" class='rightGreenLink' onclick=\"document.getElementById('learnEintrag$i').style.display = 'block'\"> Lösung anzeigen </a>";
					echo "<a href=\"#eintrag$idfornav\"  class='rightRedLink' onclick=\"document.getElementById('learnEintrag$i').style.display = 'none'\">Verstecken</a>";
					
					echo "<h3><a name='#eintrag". $getEintraege[$i]->id ."'>" . $getEintraege[$i]->frage . "</a></h3>";
						echo "<p>Ranking: $status</p>";
						echo "<div class='loesungEintrag' style=\"display: none;\" id=\"learnEintrag$i\">";
							
							echo "<form method=post>";
							echo "<textarea name='editEintrag' class='ckeditor' id='ckeditor'>" . $getEintraege[$i]->loesung . "</textarea>";
							echo "<input type=hidden name=editID value='".$getEintraege[$i]->id."' />";
							echo "<input type=submit name=saveEintrag$i value='Änderungen speichern' />";
							echo "</form>";
							echo "<p id='bottom'>Gewusst? 
								<a class='greenLink' href='?kategorie=".$getEintraege[$i]->kategorie."&plusLernstatus&lern_id=".$getEintraege[$i]->id."'>Ja</a>
								<a class='redLink' href='?kategorie=".$getEintraege[$i]->kategorie."&minusLernstatus&lern_id=".$getEintraege[$i]->id."'>Nein</a></p>";
						echo "</div>";
					echo "</div>";
				}
				
			}
		}
		
		echo "</div>";
		
	}
	
	/**
	 * Zeigt die Optionen fÃ¼r die gegenwÃ¤rtige Kategorie an.
	 * @param unknown $kategorie
	 */
	function showOptionsForKategorie($kategorie) {
		echo "<a class='buttonlink' href='?kategorie=$kategorie&createNew'>Neuen Eintrag erstellen</a>";
	}
	
	/**
	 * Zeigt Optionen fÃ¼r die Seite an.
	 */
	function showOptionsForAll() {
		echo "<a href='?createNewCat' class='buttonlink'>Neue Kategorie erstellen</a>";
		echo "<a href='lernen.php' class='greenLink'>Lernmodus</a>";
	}
	
	/**
	 * Bietet eine Druckübersicht an:
	 */
	function export() {
		
		if(isset($_GET['kategorie'])) {
			$kategorie = $_GET['kategorie'];
			$besitzer = $this->getUserID($_SESSION['username']);
			$kategorieEintraege = $this->getEinraegeFromKategorie($besitzer, $kategorie);
			
			for($i = 0 ; $i < sizeof($kategorieEintraege); $i++) {
				echo "<div class='eintragLoesungDruck'>";
					echo "<h2>" . $kategorieEintraege[$i]->frage . "</h2>";
					echo "<p>" . $kategorieEintraege[$i]->loesung . "</p>";
				echo "</div>";
			}
			
		}
		
	}
	
	/**
	 * KATEGORIE
	 */
	public $lernkarte = "ENTITY";
	
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
				
				if($this->checkIfEintragInKategorieAlreadyExists($frage, $userid, $kategorie) == false) {
					if($this->sql_insert_update_delete($query) == true) {
						echo "<p class='erfolg'>Eintrag wurde gespeichert</p>";
					} else {
						echo "<p class='meldung'>Es gab einen Fehler! Der Eintrag konnte nicht gespeichert werden.</p>";
					}
				} else {
					echo "<p class='meldung'>Diese Frage gibt es bereits in dieser Kategorie</p>";
				}
				
			}
		}
	}
	
	/**
	 * Prüft, ob der Eintrag (frage) bereits für diesen Benutzer in der Kategorie existiert.
	 * @param unknown $titel
	 * @param unknown $besitzer
	 * @param unknown $kategorie
	 */
	function checkIfEintragInKategorieAlreadyExists($titel, $besitzer, $kategorie) {
	
		$query = "SELECT count(*) as anzahl FROM learnlernkarte WHERE besitzer = '$besitzer' AND frage='$titel' AND kategorie = '$kategorie' LIMIT 1";
	
		$gibtEsDenEintrag = $this->getObjektInfo($query);
	
		if(isset($gibtEsDenEintrag->frage)) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * ErmÃ¶glicht das editieren von Einträgen einer Kategorie.
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
	
	/**
	 * Zeigt alle Einträge als Liste an
	 */
	function showKategorieUeberschriften() {
	
		if(isset($_GET['kategorie'])) {
			if(is_numeric($_GET['kategorie']) == true) {
	
				echo "<h2>Diese Kategorie</h2>";
	
				$kategorie = $_GET['kategorie'];
	
				$userid = $this->getUserID($_SESSION['username']);
	
				$getKategorieInfos = $this->getObjektInfo("SELECT * FROM learnkategorie");
	
				if($getKategorieInfos[0]->public == 1) {
					$getEintraege = $this->getObjektInfo("SELECT * FROM learnlernkarte WHERE kategorie = $kategorie");
				} else {
					$getEintraege = $this->getObjektInfo("SELECT * FROM learnlernkarte WHERE besitzer = $userid AND kategorie = $kategorie");
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
	 * KATEGORIE
	 */
	public $kategorien = "ENTITY";
	
	/**
	 * Zeigt die eigenen EintrÃ¤ge
	 */
	function showKategorien() {
		$userid = $this->getUserID($_SESSION['username']);
		$getkategories = $this->getObjektInfo("SELECT * FROM learnkategorie WHERE besitzer = $userid");
	
		if(isset($_GET['kategorie'])) {
			$kategorieBereitsVorhanden = $_GET['kategorie'];
		} else {
			$kategorieBereitsVorhanden = "";
		}
	
		echo "<div class='showKategorien'>";
		echo "<p>Bitte eine Kategorie wählen</p>";
		echo "<ul>";
		for ($i = 0; $i < sizeof($getkategories); $i++) {
				
			echo "<li ";
			if($kategorieBereitsVorhanden == $getkategories[$i]->id) {
				echo " id = 'gewaehlteCat' ";
			}
			echo " >";
			echo "<a href='?kategorie=" . $getkategories[$i]->id . "'>" . $getkategories[$i]->kategorie . "</a> <a href='export.php?kategorie=" . $getkategories[$i]->id . "' class='rightBlueLink'>print</a></li>";
				
		}
		echo "</ul>";
		echo "</div>";
	
	}
	
	/**
	 * Ermöglicht die Funktionen, Kategorien zu verwalten.
	 */
	function mainKategorieFunction() {
		
		$this->createNewKategorie();
		$this->editKategorie();
		$this->deleteKategorie();
		
	}
	
	/**
	 * Ermöglicht das erstellen einer neuen Kategorie.
	 */
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
				echo "<label for='slideThree'>Soll die Kategorie für alle sichtbar sein? </label>";
				echo "<span class='slideThree'><input type=checkbox id='slideThree' name=public value=1 /></span>";
				echo "<br><br>";
				echo "<input type=submit name=submit value=Absenden />";
			
			echo "</form></div>";
		}
		
	}
	
	/**
	 * Ermöglicht das editieren einer Kategorie.
	 */
	function editKategorie() {
		
	}
	
	/**
	 * Ermöglicht das löschen einer Kategorie.
	 */
	function deleteKategorie() {
		
	}
	
	/**
	 * 
	 * @param unknown $kategorie
	 * @return unknown|boolean
	 */
	function getKategorieInfo($kategorie) {
		$query = "SELECT * FROM learnkategorie WHERE id = '$kategorie'";
		
		$check = $this->getObjektInfo($query);
		
		if(isset($check[0]->id)) {
			return $check;
		} else {
			return false;
		}
	}
	
	/**
	 * Lernstatus ENTITY
	 */
	
	public $lernstatus = "ENTITY";
	
	/**
	 * Stellt die Funktionen zur Verfügung.
	 */
	function mainStatusFunction() {
		$besitzer = $this->getUserID($_SESSION['username']);
		
		if(isset($_GET['plusLernstatus']) AND isset($_GET['lern_id'])) {
			$lern_id = $_GET['lern_id'];
			$this->RaiseLernstatus($besitzer, $lern_id);
		}
		
		if(isset($_GET['minusLernstatus']) AND isset($_GET['lern_id'])) {
			$lern_id = $_GET['lern_id'];
			$this->LowerLernstatus($besitzer, $lern_id);
		}
		
		
	}
	
	/**
	 * Erhöht den Lernstatus
	 * @param unknown $besitzer
	 * @param unknown $lern_id
	 * @return boolean
	 */
	function RaiseLernstatus($besitzer, $lern_id) {
		
		# check ob lern status existiert:
		
		if($this->checkIfLernstatusExists($besitzer, $lern_id) == true) {
			
			$status = $this->getStatusFromLernID($besitzer, $lern_id) + 1;
			
			if($this->setLernstatus($besitzer, $lern_id, $status) == true) {
				return true;
			} else {
				return false;
			}
			
		} else {
			
			$status = 1;
			if($this->createLernstatusIfNotExists($besitzer, $lern_id, $status) == true) {
				return true;
			} else {
				return false;
			}
		}
		
	}
	
	/**
	 * Setzt den Lernstatus herunter
	 * @param unknown $besitzer
	 * @param unknown $lern_id
	 */
	function LowerLernstatus($besitzer, $lern_id) {
		# check ob lern status existiert:
		
		if($this->checkIfLernstatusExists($besitzer, $lern_id) == true) {
				
			$status = $this->getStatusFromLernID($besitzer, $lern_id) - 1;
				
			if($this->setLernstatus($besitzer, $lern_id, $status) == true) {
				return true;
			} else {
				return false;
			}
				
		} else {
				
			$status = 0;
			if($this->createLernstatusIfNotExists($besitzer, $lern_id, $status) == true) {
				return true;
			} else {
				return false;
			}
		}
	}
	
	/**
	 * Setzt den Lernstatus auf einen bestimmten Wert.
	 * @param unknown $besitzer
	 * @param unknown $lern_id
	 * @param unknown $NEWstatus
	 * @return boolean
	 */
	function setLernstatus($besitzer, $lern_id, $NEWstatus) {
		if($this->checkIfLernstatusExists($besitzer, $lern_id) == true) {
			$query = "UPDATE lernstatus SET status = '$NEWstatus' WHERE besitzer = '$besitzer' AND lern_id = '$lern_id' LIMIT 1";
			
			if($this->sql_insert_update_delete($query) == true) {
				echo "<p class='erfolg'>Lernstatus aktualisiert.</p>";
				return true;
			} else {
				return false;
			}
		}
	}
	
	/**
	 * Gibt den Status zurück.
	 * @param unknown $besitzer
	 * @param unknown $lern_id
	 * 
	 * returns status
	 */
	function getStatusFromLernID($besitzer, $lern_id) {
		
		if($this->checkIfLernstatusExists($besitzer, $lern_id) == true) {
			
			$query = "SELECT * FROM lernstatus WHERE besitzer = '$besitzer' AND lern_id = '$lern_id' LIMIT 1";
			
			$check = $this->getObjektInfo($query);
			
			return $check->status;
			
		} else {
			$null = 0;
			return $null;
		}
		
	}
	
	/**
	 * Erstellt den Lernstatus, wenn er noch nicht vorhanden ist.
	 * @param unknown $lern_id
	 * @param unknown $besitzer
	 * 
	 * returns BOOLEAN
	 */
	function createLernstatusIfNotExists($besitzer, $lern_id, $status) {
		
		if($this->checkIfLernstatusExists($besitzer, $lern_id) == false) {
			$query = "INSERT INTO lernstatus (besitzer, lern_id, status, changedate) VALUES ('$besitzer','$lern_id','$status',CURRENT_TIMESTAMP)";
			
			if($this->sql_insert_update_delete($query)== true) {
				return true;
			} else {
				return false;
			}
		}
		
	}
	
	/**
	 * Checkt, ob der Lernstatus existiert.
	 * @param unknown $lern_id
	 * 
	 * RETURNS BOOLEAN
	 */
	function checkIfLernstatusExists($besitzer, $lern_id) {
		
		$query = "SELECT count(*) as anzahl FROM lernstatus WHERE lern_id = '$lern_id' AND besitzer = '$besitzer'";
		
		$check = $this->getObjektInfo($query);
		
		if($check->anzahl > 0) {
			return true;
		} else {
			return false;
		}
		
		
	}
	
	
	/**
	 * 
	 */
	public $lernmodus = "ENTITY";
	
	/**
	 * Hauptfunktion des Lernmodus.
	 */
	function mainLernmodusFunction() {
		$this->mainStatusFunction();
		
		
		if(isset($_GET['kategorie'])) {
			$besitzer = $this->getUserID($_SESSION['username']);
			$kategorie = $_GET['kategorie'];
			
			$this->showEintraegeFuerLernmodus($kategorie, $besitzer);
		}
		
		$this->showKategorien();
	}
	
	/**
	 * Zeigt die Eintraege für den Lernmodus an.
	 * @param unknown $kategorie
	 * @param unknown $besitzer
	 */
	function showEintraegeFuerLernmodus($kategorie, $besitzer) {
		
		$eintraege = $this->getEinraegeFromKategorie($besitzer, $kategorie);
		$anzahl = $this->getAnzahlFromEintraegeFuerKategorie($besitzer, $kategorie);
		if($anzahl > 0) {
			
			$random = rand(0, $anzahl-1);
			echo "<div class='learnEintrag'>";
			echo "<h2><a name=eintrag$id>" . $eintraege[$random]->frage . "</a></h2>";
			$status = $this->getStatusFromLernID($besitzer, $eintraege[$random]->id);
			echo "<p>Ranking: $status</p>";
			
			$id = $eintraege[$random]->id;
			echo "<a href=\"#eintrag$id\" class='rightGreenLink' onclick=\"document.getElementById('learnEintrag$random').style.display = 'block'\"> Lösung anzeigen </a>";
			echo "<a href=\"#eintrag$id\"  class='rightRedLink' onclick=\"document.getElementById('learnEintrag$random').style.display = 'none'\">Verstecken</a>";

			echo "<div class='loesungEintrag' style=\"display: none;\" id=\"learnEintrag$random\">";
			
			echo "<p>" . $eintraege[$random]->loesung . "</p>";
			echo "<p id='bottom'>Gewusst?
									<a class='greenLink' href='?kategorie=".$eintraege[$random]->kategorie."&plusLernstatus&lern_id=".$eintraege[$random]->id."'>Ja</a>
									<a class='redLink' href='?kategorie=".$eintraege[$random]->kategorie."&minusLernstatus&lern_id=".$eintraege[$random]->id."'>Nein</a></p>";
			echo "</div>";
			echo "</div>";
			
		}
		
	}
	
	/**
	 * Gibt die Anzahl der Einträge zurück.
	 * @param unknown $besitzer
	 * @param unknown $kategorie
	 */
	function getAnzahlFromEintraegeFuerKategorie($besitzer, $kategorie) {
		
		if($this->checkIfKategorieExists($kategorie) == true AND $this->checkIfUserIsAllowedToSeeKategorie($besitzer, $kategorie) == true) {
			# Checkanzahl:
			
			$query = "SELECT count(*) as anzahl FROM learnlernkarte WHERE kategorie = '$kategorie'";
			$check = $this->getObjektInfo($query);
			return $check->anzahl;
		}
		
		
	}
	
	/**
	 * Gibt die Einträge der Kategorie als Array zurück. Dabei wird geprüft, ob der Benutzer die Kategorie sehen darf.
	 * @param unknown $besitzer
	 * @param unknown $kategorie
	 */
	function getEinraegeFromKategorie($besitzer, $kategorie) {
		if($this->checkIfKategorieExists($kategorie) == true 
				AND $this->checkIfUserIsAllowedToSeeKategorie($besitzer, $kategorie) == true) {
			
			$query = "SELECT * FROM learnlernkarte WHERE kategorie = '$kategorie'";
			$eintraege = $this->getObjektInfo($query);
			
			return $eintraege;
		} else {
			return false;
		}
	}
	
	/**
	 * Checkt ob der Benutzer die Kategorie sehen darf.
	 * @param unknown $userID
	 * @param unknown $kategorie
	 * @return boolean
	 */
	function checkIfUserIsAllowedToSeeKategorie($userID, $kategorie) {
		
		$kategorieInfo = $this->getKategorieInfo($kategorie);
				
		if($userID == $kategorieInfo[0]->besitzer){
			return true;
		} else if($kategorieInfo[0]->public == 1) {
			return true;
		} else {
			return false;
		}
		
	}
	
	/**
	 * Prüft, ob die Kategorie bereits existiert.
	 * @param unknown $kategorie
	 * @return boolean
	 */
	function checkIfKategorieExists($kategorie) {
		$query = "SELECT count(*) as anzahl FROM learnkategorie WHERE id = '$kategorie'";
		
		$check = $this->getObjektInfo($query);
		
		if($check[0]->anzahl > 0) {
			return true;
		} else {
			return false;
		}
	}
	
	
}