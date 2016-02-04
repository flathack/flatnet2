<?php
include 'objekt/functions.class.php';

/**
 * @history: 02.12.2015 erstellt.
 * @author BSSCHOE
 *
 */
class uebersicht extends functions {
	
	function mainUebersichtFunction() {
		# Übersicht Manipulation
		$this->changeStatusFunction();
		
		# Uebersicht anzeigen:
		$this->showUebersicht();
		
		# Administrative-Funktionen
		if($this->userHasRight("54", 0) == true) {
			echo "<div class='mainbody'><a class='buttonlink' href='?newEntry'>Neue Kachel</a>";
			echo "<a class='buttonlink' href='?editEntry'>Kacheln bearbeiten</a></div>";
		}
		if($this->userHasRight("54", 0) == true AND isset($_GET['newEntry'])) {
			$this->showCreateEntity();
		}
		if($this->userHasRight("54", 0) == true AND isset($_GET['editEntry'])) {
			$this->showEditEntry();
		}
		
	}
	
	/**
	 * Erstellt einen Eintrag in der Tabelle.
	 * @param unknown $id
	 * @param unknown $timestamp
	 * @param unknown $name
	 * @param unknown $link
	 * @param unknown $beschreibung
	 * @param unknown $sortierung
	 * @param unknown $active
	 */
	function createEntity($id, $timestamp, $name, $link, $beschreibung, $sortierung, $active, $css, $rightID) {
		
		$query = "INSERT INTO uebersicht_kacheln (id, timestamp, name, link, beschreibung, sortierung, active, cssID, rightID) 
				VALUES
				('$id', CURRENT_TIMESTAMP,'$name','$link','$beschreibung','$sortierung','$active','$css','$rightID')";
		if($this->sql_insert_update_delete($query) == true) {
			return true;
		} else {
			return false;
		}
		
	}
	
	function showEditEntry() {
		$select = "SELECT * FROM uebersicht_kacheln";
		$kacheln = $this->getObjektInfo($select);
		
		echo "<table class='flatnetTable'>";
		
		echo "<thead><td>ID</td><td>Name</td><td>Link</td><td>Beschreibung</td><td>Sortierung</td><td>Aktiv</td><td>Css ID</td><td>RightID</td></thead>";
		for ($i = 0 ; $i < sizeof($kacheln) ; $i++) {
			
			echo "<tbody>";
				echo "<td>" .$kacheln[$i]->id. "</td>";
				echo "<td>" .$kacheln[$i]->name. "</td>";
				echo "<td>" .$kacheln[$i]->link. "</td>";
				echo "<td>" .$kacheln[$i]->beschreibung. "</td>";
				echo "<td>" .$kacheln[$i]->sortierung. "</td>";
				echo "<td>" .$kacheln[$i]->active. "</td>";
				echo "<td>" .$kacheln[$i]->cssID. "</td>";
				echo "<td>" .$kacheln[$i]->rightID. "</td>";
			echo "</tbody>";
			
		}
		echo "</table>";
	}
	
	/**
	 * Zeigt die Kachel zum erstelen einer Kachel
	 */
	function showCreateEntity() {
		
		if(isset($_POST['name']) AND isset($_POST['link'])) {
			if($_POST['name'] != "" AND $_POST['link'] != "" AND $_POST['rightID'] != "") {
				if(!isset($_POST['active'])) {
					$active = 0;
				} else {
					$active = 1;
				}
				
				if($this->createEntity("", "CURRENT_TIMESTAMP", $_POST['name'], $_POST['link'], $_POST['beschreibung'], $_POST['sortierung'], $active, $_POST['css'], $_POST['rightID']) == true) {
					echo "<p class='erfolg'>Die Übersicht wurde hinzugefügt</p>";
				} else {
					echo "<p class='meldung'>Es gab einen Fehler</p>";
				}
				
			}
			
		}
		
		echo "<div class='bereichNEW'><form method=post>";
		if(isset($_POST['name'])) {
			$name = $_POST['name'];
		} else {
			$name = "";
		}
		if(isset($_POST['link'])) {
			$link = $_POST['link'];
		} else {
			$link = "";
		}
		if(isset($_POST['beschreibung'])) {
			$beschreibung = $_POST['beschreibung'];
		} else {
			$beschreibung = "";
		}
		if(isset($_POST['sortierung'])) {
			$sortierung = $_POST['sortierung'];
		} else {
			$sortierung = "";
		}
		if(isset($_POST['css'])) {
			$css = $_POST['css'];
		} else {
			$css = "";
		}
		if(isset($_POST['rightID'])) {
			$rightID = $_POST['rightID'];
		} else {
			$rightID = "";
		}
		if(isset($_POST['active'])) {
			$active = $_POST['active'];
		} else {
			$active = "";
		}
			echo "<input id='name' type=text name=name value='" .$name. "' placeholder=Name />";
			echo "<input id='link' type=text name=link value='" .$link. "' placeholder=Link />";
			echo "<input id='desc' type=text name=beschreibung value='" .$beschreibung. "' placeholder=Beschreibung />";
			echo "<input id='sort' type=number name=sortierung value='" .$sortierung. "' placeholder=sort />";
			echo "<input id='css' type=text name=css value='" .$css. "' placeholder=css />";
			
			echo "<select name=rightID>";
				$getAllRights = $this->getObjektInfo("SELECT * FROM userrights ORDER BY kategorie, id");
				for($i = 0 ; $i < sizeof($getAllRights) ; $i++) {
					echo "<option value='"; 
						echo $getAllRights[$i]->id;
					echo "'>";
						echo $getAllRights[$i]->kategorie ." - ".$getAllRights[$i]->id ." - ". $getAllRights[$i]->recht;
					echo "</option>";
				}
			echo "</select>";
			
		#	echo "<input id='right' type=number name=rightID value='" .$rightID. "' placeholder=Recht />";
			echo "<input id='checkbox' type=checkbox checked value='" .$active. "' name=active /><label for=active>Aktiv</label>";
			echo "<input id='submit' type=submit value=speichern />";
		echo "</form></div>";
	}
	
	/**
	 * Führt die Änderung aus.
	 */
	function changeStatusFunction() {
		if($this->userHasRight(52, 0) == true) {
			if(isset($_POST['changeStatus'])) {
			
				$id = $_POST['id'];
				$status = $_POST['changeStatus'];
			
				if($this->sql_insert_update_delete("UPDATE uebersicht_kacheln SET active=$status WHERE id=$id LIMIT 1") == true) {
					echo "<p class='erfolg'>Wurde geändert</p>";
				} else {
					echo "<p class='meldung'>Es gab einen Fehler.</p>";
				}
			}
		}
		
	}
	
	/**
	 * Ändert den Status einer Kachel.
	 * @param unknown $id
	 */
	function changeStatus($id) {
		
		if($this->userHasRight(52, 0) == true) {
			$getKachelInfo = $this->getObjektInfo("SELECT * FROM uebersicht_kacheln WHERE id = $id");
			
			
			
			echo "<div class='changeStatus'><form method=post>";
			echo "<input type=hidden name=id value=$id />";
			
			echo "<select name=changeStatus>";
			
			echo "<option"; 
			if($getKachelInfo[0]->active == 1) {
				echo " selected ";
			}
			echo " value=1>Aktiv</option>";
			
			echo "<option";
			if($getKachelInfo[0]->active == 0) {
				echo " selected ";
			}
			echo " value=0>Inaktiv</option>";
			
			echo "<option"; 
			if($getKachelInfo[0]->active == 2) {
				echo " selected ";
			}
			echo " value=2>gesperrt</option>";
			
			echo "</select>";
			
			echo "<input type=submit name=submit value=ok />";
			echo "</form></div>";
		}
	}
	
	/**
	 * Zeigt die Kacheln an.
	 */
	function showUebersicht() {
		$kacheln = $this->getObjektInfo("SELECT * FROM uebersicht_kacheln WHERE active=1 ORDER BY sortierung, name, id");
		$kachelnInactive = $this->getObjektInfo("SELECT * FROM uebersicht_kacheln WHERE active=0 ORDER BY sortierung, name, id");
		$kachelnGesperrt = $this->getObjektInfo("SELECT * FROM uebersicht_kacheln WHERE active=2 ORDER BY sortierung, name, id");
		
		# Normale Kacheln
		for($i = 0; $i < sizeof($kacheln); $i++) {
			if($this->userHasRight($kacheln[$i]->rightID, 0) == true) {
				echo "<div class=bereich".$kacheln[$i]->cssID.">";
				echo "<a href='".$kacheln[$i]->link."'><h2>".$kacheln[$i]->name."</h2></a>";
					
				echo "<p>";
				echo $kacheln[$i]->beschreibung;
				echo "</p>";
				$this->changeStatus($kacheln[$i]->id);
				echo "</div>";
			
			}
		}
		
		# Bereiche, welche für den Benutzer nicht sichtbar sind.
		for($i = 0; $i < sizeof($kacheln); $i++) {
			if($this->userHasRight($kacheln[$i]->rightID, 0) == false) {
				echo "<div class='bereichINACTIVE'>";
				echo "<h2></h2>";
				echo "<p></p>";
				$this->changeStatus($kacheln[$i]->id);
				echo "</div>";
			}
		}
		
		# Gesperrte Bereiche
		for($i = 0; $i < sizeof($kachelnGesperrt); $i++) {
			if($this->userHasRight($kachelnGesperrt[$i]->rightID, 0) == true OR $this->userHasRight("45", 0) == true) {
				echo "<div class='bereichGesperrt'>";
				echo "<h2>".$kachelnGesperrt[$i]->name."</h2>";
				echo "<p>Der Inhalt wurde kurzfristig aufgrund eines Fehlers gesperrt. Versuche es später erneut.</p>";
				$this->changeStatus($kachelnGesperrt[$i]->id);
				echo "</div>";
			}
		}
		
		# Inaktive Bereiche
		for($i = 0 ; $i < sizeof($kachelnInactive) ; $i++) {
			if($this->userHasRight("52", 0) == true) {
				echo "<div class='bereichINACTIVE'>";
				echo "<h2>".$kachelnInactive[$i]->name."</h2>";
				echo "<p>Dieser Inhalt ist deaktiviert, da er nicht fertig entwickelt ist oder die Entwicklung eingestellt wurde.</p>";
				$this->changeStatus($kachelnInactive[$i]->id);
				echo "</div>";
			}
		}
		
		if($this->userHasRight("45", 0) == true) {
			echo "<div class='bereichadministration'>";
			
				echo "<h2>Admin Kachel</h2>";
				$select = "SELECT * FROM benutzer WHERE versuche >= 3";
				$gesperrteUser = $this->getObjektInfo($select);
				for($i = 0 ; $i < sizeof($gesperrteUser) ; $i++) {
					echo "<p class='info'>" . $gesperrteUser[$i]->Name . " ist gesperrt <a href='/flatnet2/admin/control.php?statusID=".$gesperrteUser[$i]->id."&status=entsperren&userverw=1&action=1'>entsperren</a></p>";
				}
			
			echo "</div>";
		}
			
	}
	
}