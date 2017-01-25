<?php

include 'objekt/functions.class.php';

class toyota extends functions {
	
	function mainMuepFunction() {
		
		$this->navigation();
		$this->vergleichen();
		
		$this->createFahrzeug();
		$this->alterFahrzeug();
		$this->deleteFahrzeug();
		$this->showMuepTable();
	}
	
	function navigation() {
		echo "<ul class='finanzNAV'>";
			echo "<li>" ."<a href='?neu'>Neues Fahrzeug</a>". "</li>";
			echo "<li>" ."<a href='alle.php'>Alle Fahrzeuge</a>". "</li>";
			echo "<li>" ."<a href='index.php'>Vergleichen</a>". "</li>";
		echo "</ul>";
	}
	
	function vergleichen() {

		echo "<h2>Mitarbeiter&uuml;berlassungsprogramm</h2>";
		
	}
	
	function showMuepTable() {
		
	}
	
	function createFahrzeug() {
		if(isset($_GET['neu'])) {
		#	$info = array(
		#		"text" => "pkwname" => ""
		#			
		#	);
		#	$this->createNewObject("Neues Fahrzeug erstellen", $info, $subitname);
		}
	}
	
	function deleteFahrzeug() {
		
	}
	
	function alterFahrzeug() {
		
	}
	
	/**
	 * Allgemeine Funktion zur der GUI für ein neues Objekt
	 * @param unknown $title
	 * @param unknown $types
	 * @param unknown $names
	 * @param unknown $values
	 * @param unknown $placeholders
	 * @param unknown $subitname
	 */
	function createNewObject($title, $info, $subitname) {
		
		echo "<div class='newFahrt'>";
		echo "<h3>$title</h3>";
		
		for($i = 0 ; $i < sizeof($info) ; $i++) {
			
		}
		
		echo "<input type=submit>";
		echo "</div>";
		
	}
	
	
}



?>