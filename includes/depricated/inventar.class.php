<?php
/**
 * @history Steven 25.08.2014 angelegt.
 *
 * @author Steven
 * Ermöglicht das Verwalten des Inventars.
 *
 * vollständig überarbeitet.
 */
include 'objekt/functions.class.php';

class inventar extends functions  {

	/**
	 * Zeigt die Einträge aus der Datenbank an.
	 */
	function showInventar($status) {
		
		$ersteller = $this->getUserID($_SESSION['username']);
		
		if($status != "") {
			$select = "SELECT * FROM inventar WHERE status = '$status' AND ersteller = '$ersteller' ORDER BY status";
		} else {
			$status = 0;
			$select = "SELECT * FROM inventar WHERE ersteller = '$ersteller' ORDER BY status";
		}
		# Ausgabe initialisieren
		$ausgabe = "";

		$ergebnis = mysql_query($select);
		while($row = mysql_fetch_object($ergebnis)) {

			$ausgabe .= "<tbody>";
			# Status Select: 
			$select2 = "SELECT statusID, wert FROM inventar_hilfe WHERE statusID = '$row->status'";
			$ergebnis2 = mysql_query($select2);
			while($row2 = mysql_fetch_object($ergebnis2)) {
				$ausgabe .= "<td>$row2->wert</td>";
			}
			$ausgabe .= "<td><a href='Inventar_neu.php?action=update&id=$row->id'>" . substr($row->name, 0, 30) . "</a></td>";
			$ausgabe .= "<td><a href='?suche=$row->standort'>" . substr($row->standort, 0, 10) . "</a></td>";
			$ausgabe .= "<td><a href='?suche=$row->kategorie'>" . substr($row->kategorie, 0, 15) . "</a></td>";
			$ausgabe .= "<td><a href='?suche=$row->preis'>" . $row->preis . "</a> €</td>";

			$ausgabe .= "<td>" . "<a href='?del=$row->id' class='buttonlink'>X</a>" . "</td>";
			$ausgabe .= "</tbody>";
		}
		return $ausgabe;
	}

	/**
	 * Erstellt einen neuen Eintrag in der Inventarliste oder updatet diesen.
	 */
	function newInventar($action, $id) {
		# Ausgabe initialisieren:
		$ausgabe = "";
		$meldung = "";
		
		if(isset($_POST['update'])) {
		
			$name = isset($_POST['name']) ? $_POST['name'] : '';
			$kategorieSchreib = isset($_POST['kategorieSchreib']) ? $_POST['kategorieSchreib'] : '';
			$kategorieListe = isset($_POST['kategorieListe']) ? $_POST['kategorieListe'] : '';
			$besitzer = isset($_POST['besitzer']) ? $_POST['besitzer'] : $_SESSION['username'];
			$bestellnummer = isset($_POST['bestellnummer']) ? $_POST['bestellnummer'] : '';
			$garantie = isset($_POST['garantie']) ? $_POST['garantie'] : '';
			$kaufdat = isset($_POST['kaufdat']) ? $_POST['kaufdat'] : '';
			$kaufort = isset($_POST['kaufort']) ? $_POST['kaufort'] : '';
			$standort = isset($_POST['standort']) ? $_POST['standort'] : '';
			$menge = isset($_POST['menge']) ? $_POST['menge'] : '1';
			$notizen = isset($_POST['notizen']) ? $_POST['notizen'] : '';
			$preis = isset($_POST['preis']) ? $_POST['preis'] : '';
			$verkaeufer = isset($_POST['verkaeufer']) ? $_POST['verkaeufer'] : '';
			$status = isset($_POST['status']) ? $_POST['status'] : '';
			$timestamp = isset($_POST['timestamp']) ? $_POST['timestamp'] : '';
			$ersteller = $this->getUserID($_SESSION['username']);
				
			if($kategorieSchreib == "") {
				$kategorieFinal = $kategorieListe;
			} else {
				$kategorieFinal = $kategorieSchreib;
			}
			
			$sqlupdate = "
			UPDATE inventar SET ersteller='$ersteller', name='$name',kaufdat='$kaufdat',preis='$preis',kaufort='$kaufort',standort='$standort',
			bestellnummer='$bestellnummer',kategorie='$kategorieFinal',status='$status',
			garantie='$garantie',verkäufer='$verkaeufer',menge='$menge',
			notizen='$notizen',besitzer='$besitzer'
			WHERE id = '$id'";
		
			$update = mysql_query($sqlupdate);
		
			if($update == true) {
				$ausgabe .= "<p class='erfolg'>Objekt aktualisiert</p>";
			} else {
				$ausgabe .= "<p class='meldung'>Es gab einen Fehler: ";
				$ausgabe .= mysql_error() . "<p>";
			}
		}

		if($action == "Speichern" AND $id == "") {
			$inventar = "NEW";
			# Werte zuweisen
			$id = isset($_POST['id']) ? $_POST['id'] : '';
			$name = isset($_POST['name']) ? $_POST['name'] : '';
			$kategorieSchreib = isset($_POST['kategorieSchreib']) ? $_POST['kategorieSchreib'] : '';
			$kategorieListe = isset($_POST['kategorieListe']) ? $_POST['kategorieListe'] : '';
			$kategorie = isset($_POST['kategorieSchreib']) ? $_POST['kategorieSchreib'] : '';
			$besitzer = isset($_POST['besitzer']) ? $_POST['besitzer'] : $_SESSION['username'];
			$bestellnummer = isset($_POST['bestellnummer']) ? $_POST['bestellnummer'] : '';
			$garantie = isset($_POST['garantie']) ? $_POST['garantie'] : '';
			$kaufdat = isset($_POST['kaufdat']) ? $_POST['kaufdat'] : '';
			$kaufort = isset($_POST['kaufort']) ? $_POST['kaufort'] : '';
			$standort = isset($_POST['standort']) ? $_POST['standort'] : '';
			$menge = isset($_POST['menge']) ? $_POST['menge'] : '1';
			$notizen = isset($_POST['notizen']) ? $_POST['notizen'] : '';
			$preis = isset($_POST['preis']) ? $_POST['preis'] : '';
			$verkaeufer = isset($_POST['verkaeufer']) ? $_POST['verkaeufer'] : '';
			$status = isset($_POST['status']) ? $_POST['status'] : '';
			$timestamp = isset($_POST['timestamp']) ? $_POST['timestamp'] : '';
			$ersteller = $this->getUserID($_SESSION['username']);
			
			if($kategorieSchreib == "") {
				$kategorieFinal = $kategorieListe;
			} else {
				$kategorieFinal = $kategorieSchreib;
			}
			
		} else if ($action == "update" AND  $id > 0) {

			$inventar = "UPDATE";

			$select = "SELECT * FROM inventar WHERE id = '$id'";
			$ergebnis2 = mysql_query($select);
			while($row = mysql_fetch_object($ergebnis2)) {
				$id = $row->id;
				$name = $row->name;
				$kategorie = $row->kategorie;
				$besitzer = $row->besitzer;
				$bestellnummer = $row->bestellnummer;
				$garantie = $row->garantie;
				$kaufdat = $row->kaufdat;
				$kaufort = $row->kaufort;
				$standort = $row->standort;
				$menge = $row->menge;
				$notizen = $row->notizen;
				$preis = $row->preis;
				$verkaeufer = $row->verkäufer;
				$status = $row->status;
				$timestamp = $row->timestamp;
				$ersteller = $row->ersteller;
			}
		}
		
		if($this->getUserName($ersteller) != $_SESSION['username']) {
			echo "<p class='info'>Anscheinend hast du Spaß in der Adresszeile herumzuspielen. Doch leider habe ich an diese
					Eventualitäten gedacht und es wird dir nicht möglich sein, irgendwelche Daten auszuspionieren.</p>";
			$user = $_SESSION['username'];
			$insert = "INSERT INTO vorschlaege (text, autor, status) VALUES ('Benutzer $user hat versucht, illegale Inventareinträge abzufragen.', '$ersteller', 'illegal')";
				$ergebnis = mysql_query($insert);
				if($ergebnis == true) {
					echo "<p class='meldung'>Steven wurde über dieses Vergehen informiert.</p>";
				}
			exit;
		}
		
		# Input der Daten
		$ausgabe .= "<div id='draggable' class='newInventar'>";
		$ausgabe .= "<a href=\"?\"  class='highlightedLink'>X</a>";
		$ausgabe .= "<h2>Inventareintrag</h2>";
		if($timestamp != "") {
			$ausgabe .= "Zuletzt geändert: $timestamp";
		} else {
			$ausgabe .= "Neuer Eintrag";
		}
		$ausgabe .= '<form method=post>';
		if($_GET['action'] == "Speichern") {
		
			$ausgabe .= "<input type='submit' name='new' value='NEW' />";
			$ausgabe .= "<input type='hidden' name='newEintrag' value='' />";
		} else if($_GET['action'] == "update") {
			$ausgabe .= "<input type='submit' name='update' value='UPDATE' />";
			$ausgabe .= "<a href='inventar.php?del=$id' class='highlightedLink'>Löschen</a>";
		}
		$user = $_SESSION['username'];
		$ausgabe .= "<p><input type='text' name='name' id='titel' value='";
		if(isset($name)) {
			$ausgabe .= $name;
		} $ausgabe .= "' placeholder='Name des Gegenstandes*' required autofocus /></p>";
		$ausgabe .= "<p><input type='text' name='kategorieSchreib' id='kategorieSchreib'  value='";
		if(isset($kategorie)) {
			$ausgabe .= $kategorie;
		}  $ausgabe .= "' placeholder='neue Kategorie' />";
		
		# Kategorie Übersicht:
		$getCategories = "SELECT kategorie FROM inventar GROUP BY kategorie";
		$queryCategories = mysql_query($getCategories);
		$ausgabe .= "<select name='kategorieListe' id='kategorieListe' value=''>";
		$ausgabe .= "<option></option>";
		while($rowKategorie = mysql_fetch_object($queryCategories)) {
			$ausgabe .= "<option>$rowKategorie->kategorie</option>";
		}
		$ausgabe .= "</select> (bestehende Kategorien)</p>";
		# Ende
		
		$ausgabe .= "<p><input type=date name='kaufdat' value='";
		if(isset($kaufdat)) {
			$ausgabe .= $kaufdat;
		}  $ausgabe .= "' placeholder='Kaufdatum' />";
		$ausgabe .= "<input type='text' name='kaufort' value='";
		if(isset($kaufort)) {
			$ausgabe .= $kaufort;
		}  $ausgabe .= "' placeholder='Kaufort' />";
		$ausgabe .= "<input type=text name='preis' value='";
		if(isset($preis)) {
			$ausgabe .= $preis;
		}  $ausgabe .= "' placeholder='Preis' /></p>";
		
		$ausgabe .= "<p><input type='text' name='bestellnummer' value='";
		if(isset($bestellnummer)) {
			$ausgabe .= $bestellnummer;
		}  $ausgabe .= "' placeholder='Bestellnummer' />";
		$ausgabe .= "<input type='text' name='verkaeufer' value='";
		if(isset($verkaeufer)) {
			$ausgabe .= $verkaeufer;
		}  $ausgabe .= "' placeholder='Verkäufer' />";
		$ausgabe .= "<input type=date name='garantie' value='";
		if(isset($garantie)) {
			$ausgabe .= $garantie;
		}  $ausgabe .= "' placeholder='Garantie Datum' /></p>";
		$ausgabe .= "<p class='beschriftung'><input type='text' name='standort' id='titel' value='";
		if(isset($standort)) {
			$ausgabe .= $standort;
		}  $ausgabe .= "' placeholder='Standort, z. B. Kinderzimmer, Kiste unter dem Bett.' /></p>";
		$ausgabe .= "<p id='beschriftung'>Status</p>";
		
		# SELECT DER STATI
		$ausgabe .= "<select name='status' value='' size='1'>";
		
		$sql = "SELECT * FROM inventar_hilfe";
		$query = mysql_query($sql);
		while($rowStatus = mysql_fetch_object($query)) {
			$ausgabe .= "<option value='$rowStatus->statusID'"; 	if($status == $rowStatus->statusID) {
				$ausgabe .= " selected ";
			}	$ausgabe .= ">$rowStatus->wert</option>";
		}
		$ausgabe .= "<option value='0'"; 	if($status == NULL) {
			$ausgabe .= " selected ";
		}	$ausgabe .= "></option>";
		$ausgabe .= "</select>";
		
		$ausgabe .= "<p></p>";
		$ausgabe .= "<p id='beschriftung'>Menge</p>";
		$ausgabe .= "<input type='text' name='menge' value='";
		if(isset($menge)) {
			$ausgabe .= $menge;
		}  $ausgabe .= "' placeholder='Menge' />";
		$ausgabe .= "<p id='beschriftung'>Besitzer</p>";
		$ausgabe .= "<input type='text' name='besitzer' value='";
		if(isset($besitzer)) {
			$ausgabe .= $besitzer;
		} else {
			$ausgabe .= $_SESSION['username'];
		}
		$ausgabe .= "' placeholder='Besitzer' />";
		
		
		$ausgabe .= "<textarea class='ckeditor' name='notizen'>";
		if(isset($notizen)) {
			$ausgabe .= $notizen;
		}
		$ausgabe .= "</textarea>";
		
		# Check, ob new, update or delete #
		if($_GET['action'] == "Speichern") {
		
			$ausgabe .= "<input type='submit' name='new' value='NEW' />";
		} else if($_GET['action'] == "update") {
			$ausgabe .= "<input type='submit' name='update' value='UPDATE' />";
		}
		
		$ausgabe .= "</form>";
		$ausgabe .= "</div>";

		# Ausgabe der Daten
		# $zurück = '<a href="inventar.php" class="highlightedLink">Zurück</a>';
		return $ausgabe;
	}
	
	function saveNewEintrag() {
		if(isset($_POST['new'])) {
			if($name == "") {
				$meldung .= "<p class='info'>Bitte weitere Daten eingeben</p>";
			} else {
		
				if($kategorieSchreib == "") {
					$kategorieFinal = $kategorieListe;
				} else {
					$kategorieFinal = $kategorieSchreib;
				}
				# SQL
				$insert="
				INSERT INTO inventar
				(name, kaufdat, preis, kaufort, standort, bestellnummer, kategorie, status, garantie, verkäufer, menge, notizen, besitzer, ersteller)
				VALUES
				('$name','$kaufdat','$preis','$kaufort','$standort','$bestellnummer','$kategorieFinal','$status','$garantie','$verkaeufer',
				'$menge','$notizen','$besitzer','$ersteller')";
				$ergebnis = mysql_query($insert);
				if($ergebnis == true) {
					$meldung .= "<p class='erfolg'>Das Objekt wurde gespeichert.</p>";
				} else {
					$meldung .= "<p class='meldung'>Fehler</p>";
				}
			}
		
			return $meldung . $ausgabe;
		}
	}

	/**
	 * Ermöglicht das löschen eines Eintrages im Inventar
	 * @param unknown $id
	 */
	function delInventar($id) {

		/*
		 * Abfrage ob gelöscht werden soll.
		*/
		if($id > 0 AND !isset($_POST['jaloeschen'])) { # check ob eine Zahl
			
			# Variable Initialisiern:
			$loeschabfrage = "";
			
			$inventarErsteller = $this->getInventarErsteller($id);
			$currentUser = $this->getUserID($_SESSION['username']);
				
			if($this->check("256", 0) == true AND $inventarErsteller == $currentUser) {
				# Abfrage, ob der User den Artikel wirklich löschen will.
				$loeschabfrage .= "<form method=post>";
				$loeschabfrage .= "<p class='meldung'>Sicher, dass der Inventareintrag Nr $id gelöscht werden soll?</p>";
				$loeschabfrage .= "<input type=hidden value='$id' name ='id' readonly />";
				$loeschabfrage .= "<input type=submit name='jaloeschen' value='Ja'/>
					<a href='/flatnet2/datenbank/inventar/inventar.php' class='buttonlink'>Nein</a><br><br>";
				$loeschabfrage .= "</form>";
			} else {
				$loeschabfrage .= "<p class='meldung'>Kein Zugriff</p>";
			}

			
				
			
				
			# Abfrage ausgeben:
			return $loeschabfrage;
		}

		/*
		 * Führt die Löschung durch.
		*/
		$jaloeschen = isset($_POST['jaloeschen']) ? $_POST['jaloeschen'] : '';
		$loeschid = isset($_GET['id']) ? $_GET['id'] : '';
		if($loeschid) {
			
			$inventarErsteller = $this->getInventarErsteller($loeschid);
			$currentUser = $this->getUserID($_SESSION['username']);
			
			if($this->check("256", 0) == true AND $inventarErsteller == $currentUser) {
				# Variable Initialisieren:
				$status_loeschung = "";
				
				# Durchführung der Löschung.
				$loeschQuery = "DELETE FROM `inventar` WHERE `id` = $loeschid";
				$ergebnis = mysql_query($loeschQuery);
				
				if($ergebnis == true) {
				$status_loeschung .=  "<p class='erfolg'>Eintrag gelöscht.</p>";
			} else {
						$status_loeschung .= "<p class='meldung'>Es gab einen Fehler.</p>";
				$status_loeschung .= mysql_error();
				}
				
				# Ergebnis der Löschung ausgeben:
				return $status_loeschung;
			} else {
				 $status_loeschung .= "<p class='meldung'>Keine Berechtigung</p>";
				return $status_loeschung;
			}
				
			
		}
	}

	/**
	 * Ermöglicht das filtern der Inventareinträge in die verschiedenen Stati, die ein Objekt haben kann.
	 * Input wird per GET generiert.
	 */
	function filter($selectedFilter = "Alle") {
		# defaultFilter = "Alle" ( Standard )

		# Ausgabe Initialiseren:
		$ausgabe = "";

		# Query
		$sql = "SELECT * FROM inventar_hilfe";
		$query = mysql_query($sql);
		while($row = mysql_fetch_object($query)) {
			
			$ausgabe .= "<form method=get>";
			# ALLE
			$ausgabe .= "<input onChange='this.form.submit();' type='radio' value='$row->statusID' name='status' id='$row->wert'";

			if($selectedFilter == $row->statusID) {
				$ausgabe .= "checked";
			} else {
				$ausgabe .= "unchecked";
			}
			$ausgabe .= " />";
			$ausgabe .= "<label for='$row->wert'>$row->wert</label>";
		}

		return $ausgabe;
	}

	/**
	 * Addiert alle Objekte eines bestimmten Stati
	 */
	function summe($tabelle = "inventar") {
				
		if($this->check("128", 0) == true) {
			
			$user = $this->getUserID($_SESSION['username']);
			# Summe aller Einträge berechnen
			$summe= "SELECT status, kategorie, SUM(preis) as summe FROM $tabelle WHERE ersteller = '$user' GROUP BY status";
	
			$result = mysql_query($summe) or die(mysql_error());
			$Group = "<h3>Kategorien:</h3>";
			while($row = mysql_fetch_object($result)){
				$status = $this->getStatusName($row->status);
				$Group .= "<div class='invSuchErgeb'>";
				$Group .= "<a href='?suche=$status'>" . $status . "</a>" . " = ". round($row->summe, 2) . " €";
				$Group .= "</div>";
			}
			$close = "<a href='?' class='closeSumme'>X</a>";
	
			$return = "<div class='summe'>". $close . $Group . "</div>";
	
			return $return;
		} else {
			$close = "<a href='?' class='closeSumme'>X</a>";
			echo "<div class='summe'>". $close . "Kein Ergebnis" . "</div>";
		}
	}
	
	function getStatusName($id) {
		$select = "SELECT * FROM inventar_hilfe WHERE statusID = '$id' ";
		$result = mysql_query($select) or die(mysql_error());
		while($row = mysql_fetch_object($result)){
			$statusname = $row->wert;
		}
		
		return $statusname;
	}
	
	/**
	 * Zeigt die letzten Aktualisierungen an.
	 */
	function recentEntries() {
		echo "<div>";
		echo "<h2>Kürzlich aktualisiert</h2>";
		$user = $this->getUserID($_SESSION['username']);
		$select= "SELECT *, day(timestamp) as tag, month(timestamp) as monat FROM inventar WHERE ersteller = '$user' ORDER BY timestamp DESC LIMIT 10";
		$result = mysql_query($select) or die(mysql_error());
		echo "<ul>";
		while($row = mysql_fetch_object($result)){
			echo "<li><a href='Inventar_neu.php?action=update&id=$row->id'>$row->name ($row->tag.$row->monat)</a></li>";
		}
		echo "</ul>";
		echo "</div>";
	}
}
?>