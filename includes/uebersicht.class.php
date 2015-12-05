<?php
include 'objekt/functions.class.php';

/**
 * @history: 02.12.2015 erstellt.
 * @author BSSCHOE
 *
 */
class uebersicht extends functions {
	
	function mainUebersichtFunction() {
		# Uebersicht anzeigen:
		$this->showUebersicht();
		
		# Administrative-Funktionen
		if($this->userHasRight("54", 0) == true) {
			$this->showCreateEntity();
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
		
		echo "<div class='bereiche'><div id='newEntity'><form method=post>";
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
			echo "<input id='right' type=number name=rightID value='" .$rightID. "' placeholder=Recht />";
			echo "<input id='checkbox' type=checkbox checked value='" .$active. "' name=active /><label for=active>Aktiv</label>";
			echo "<input id='submit' type=submit value=speichern />";
		echo "</form></div></div>";
	}
	
	/**
	 * 
	 */
	function showUebersicht() {
		$kacheln = $this->getObjectsToArray("SELECT * FROM uebersicht_kacheln WHERE active=1 ORDER BY sortierung, name, id");
		
		for($i = 0; $i < sizeof($kacheln); $i++) {
			if($this->userHasRight($kacheln[$i]->rightID, 0) == true) {
			
				echo "<div class='bereiche'>";
				
					echo "<div id=".$kacheln[$i]->cssID.">";
					echo "<a href='".$kacheln[$i]->link."'><h2>".$kacheln[$i]->name."</h2></a>";
					
					echo "<p>";
					echo $kacheln[$i]->beschreibung;
					echo "</p>";
					echo "</div>";
				
				echo "</div>";
			
			}
		}
	}
	
}