<?php

/**
 * @history Steven 20.08.2014 angelegt.
 * @author Steven
 * Ermöglicht die Funktionen des Adressbuches.
 */
include 'objekt/functions.class.php';
class datenbanken extends functions {
	
	/**
	 * ermögtlicht das exportieren von Dateien, wenn über GET die Zahl 1 angegeben wird und der User Steven ist.
	 * @status: getestet, funktioniert, Ausgabe muss noch formatiert werden.
	 */
	function export($tabelle) {
		if (isset ( $_GET ['export'] )) {
			if ($_GET ['export'] == 1 and $_SESSION ['username'] == "steven") {
				$select = "SELECT * FROM $tabelle";
				$export = mysql_query ( $select );
				$fields = mysql_num_fields ( $export );
				
				for($i = 0; $i < $fields; $i ++) {
					$header .= mysql_field_name ( $export, $i ) . ";"; // #\t
				}
				
				while ( $row = mysql_fetch_row ( $export ) ) {
					$line = '';
					foreach ( $row as $value ) {
						if ((! isset ( $value )) or ($value == "")) {
							$value = ";"; // #\t
						} else {
							$value = str_replace ( '"', '""', $value );
							$value = '"' . $value . '"' . ";"; // #\t
						}
						$line .= $value;
					}
					$data .= trim ( $line ) . "#";
				}
				$data = str_replace ( "\r", "", $data );
				
				if ($data == "") {
					$data = "\n(0) Records Found!\n";
				}
				
				header ( "Content-type: application/octet-stream" );
				header ( "Content-Disposition: attachment; filename=ausgabe.txt" );
				header ( "Pragma: no-cache" );
				header ( "Expires: 0" );
				print "$header\n$data";
			}
		}
	}
	
	/**
	 * Verbindet Function und Eingabe von: UserBearbeiten
	 */
	function UserBearbeiten() {
		if($this->userHasRight("14", 0) == true) {
			echo $this->UserBearbeitenEingabe ();
			echo $this->UserBearbeitenFunction ();
		}
	}
	
	/**
	 * Ermöglicht das bearbeiten von Daten im Adressbuch.
	 * Benötigt folgende GET variablen zum funktionieren:
	 * bearbeiten != "", idanzeigen=(userid, z. b. 1)
	 */
	function UserBearbeitenEingabe() {
		if($this->userHasRight("14", 0) == false) {
			exit ();
		}
		// Ausgabe initialisieren:
		$ausgabe = "";
		
		// Var zuweisen:
		$bearbeiten = isset ( $_GET ['bearbeiten'] ) ? $_GET ['bearbeiten'] : '';
		
		// Erst los legen, wenn in Adresszeile Bearbeiten steht.
		if (isset ( $_GET ["bearbeiten"] )) {
			$userid = $_GET ['bearbeiten'];
			$ausgabe .= "<h2>Eintrag anzeigen und bearbeiten</h2>";
			$ausgabe .= "<div>";
			$ausgabe .= "<table class='AdressTable'>";
			$ausgabe .= "<form action='?' METHOD=GET>";
			$bearbeiten = $_GET ["bearbeiten"];
			$modsql = "SELECT * FROM adressbuch WHERE id=$bearbeiten";
			$readfurbearbeiten = mysql_query ( $modsql );
			while ( $rowbearb = mysql_fetch_object ( $readfurbearbeiten ) ) {
				$_GET ['userid'] = $userid;
				$ausgabe .= "

				<tr>
				<td><input type='hidden' name='id' value='$rowbearb->id' readonly></td>

				<tr>
				<td ><input type='text'   name='vorname' value='$rowbearb->vorname' required autofocus /></td>
				<td><input placeholder='Nachname' type='text'  name='nachname' value='$rowbearb->nachname' required></td>
				<td rowspan='10'> <textarea name='notizen' class='ckeditor' cols='52' rows='5' wrap='physical'>$rowbearb->notizen</textarea> </td>
				</tr>

				<tr>
				<td ><input type='text'   name='strasse' value='$rowbearb->strasse'  placeholder='Straße'>
				<input  type='text'   name='hausnummer' value='$rowbearb->hausnummer' placeholder='Nr.'></td><td></td>
				</tr>

				<tr>
				<td ><input placeholder='PLZ' type='text'   name='postleitzahl' value='$rowbearb->postleitzahl'></td>
				<td><input placeholder='Stadt' type='text'   name='stadt' value='$rowbearb->stadt'></td>
				</tr>

				<tr>
				<td ><input placeholder='Bundesland' type='text'   name='bundesland' value='$rowbearb->bundesland'></td>
				<td><input placeholder='Land' type='text'   name='land' value='$rowbearb->land'></td>
				</tr>

				<tr>
				<td><input placeholder='Telefon 1' type='text'   name='telefon1' value='$rowbearb->telefon1' >
				<input type='text'  size='10'  name='telefon1art' value='$rowbearb->telefon1art' placeholder='Handy'></td>
				<td><input placeholder='Fax' type='text'   name='fax' value='$rowbearb->fax'></td>
				</tr>

				<tr>
				<td><input placeholder='Telefon 2' type='text'   name='telefon2' value='$rowbearb->telefon2' >
				<input type='text'  size='10'  name='telefon2art' value='$rowbearb->telefon2art' placeholder='Home'></td>
				<td><input placeholder='Gruppe' type='text'   name='gruppe' value='$rowbearb->gruppe'></td>
				</tr>

				<tr>
				<td><input placeholder='Telefon 3' type='text'   name='telefon3' value='$rowbearb->telefon3' >
				<input type='text'  size='10'  name='telefon3art' value='$rowbearb->telefon3art' placeholder='Arbeit'></td>
				<td><input placeholder='E-Mail' type='text'   name='email' value='$rowbearb->email'></td>
				</tr>

				<tr>
				<td><input placeholder='Telefon 4' type='text'   name='telefon4' value='$rowbearb->telefon4' >
				<input  type='text' size='10'  name='telefon4art' value='$rowbearb->telefon4art' placeholder='Privat'></td>
				<td><input placeholder='Geburtstag' type='date'  maxlength='10' name='geburtstag' value='$rowbearb->geburtstag'></td></tr>

				<tr>
				<td><input placeholder='Skype' type='text'   name='skype' value='$rowbearb->skype'></td>
				<td><input placeholder='Facebook' type='text'   name='facebook' value=''></tr>";
				$ausgabe .= "<tr>
				<td>
				<input type='submit' name='update' value='Speichern'>
				</td>
				<td>
				<a href='?loeschen=ja&loeschid=$rowbearb->id' class='buttonlink'>&#10008; löschen</a>
				</td>
				</tr>";
			}
			$ausgabe .= "</form>";
			$ausgabe .= "</table>";
			$ausgabe .= "</div>";
		}
		
		return $ausgabe;
	}
	
	/**
	 * Ermöglicht das Updaten der Informationen zu einem Eintrag im Adressbuch.
	 * Benötigt GET Var "update" == "Speichern".
	 */
	function UserBearbeitenFunction() {
		if($this->userHasRight("14", 0) == true) {
			$fehler = "";
			$ausgabe = "";
			
			$update = isset ( $_GET ['update'] ) ? $_GET ['update'] : '';
			if ($update == 'Speichern') {
				$geburtstag = $_GET ["geburtstag"];
				$vorname = $_GET ["vorname"];
				$nachname = $_GET ["nachname"];
				$strasse = $_GET ["strasse"];
				$hausnummer = $_GET ["hausnummer"];
				$postleitzahl = $_GET ["postleitzahl"];
				$stadt = $_GET ["stadt"];
				$bundesland = $_GET ["bundesland"];
				$land = $_GET ["land"];
				$telefon1 = $_GET ["telefon1"];
				$telefon2 = $_GET ["telefon2"];
				$telefon3 = $_GET ["telefon3"];
				$telefon4 = $_GET ["telefon4"];
				$telefon1art = $_GET ["telefon1art"];
				$telefon2art = $_GET ["telefon2art"];
				$telefon3art = $_GET ["telefon3art"];
				$telefon4art = $_GET ["telefon4art"];
				$email = $_GET ["email"];
				$skype = $_GET ["skype"];
				$facebook = $_GET ["facebook"];
				$fax = $_GET ["fax"];
				$gruppe = $_GET ["gruppe"];
				$notizen = $_GET ["notizen"];
				$updid = $_GET ["id"];
				if ($nachname == "" or $vorname == "") {
					$fehler .= "<p class='meldung'>Eingabefehler. Es muss mindestens ein Vorname und ein Nachname eingegeben werden. <a href='?'>Zurück</a></p>";
				} else {
					$sqlupdate = "UPDATE adressbuch SET geburtstag='$geburtstag', vorname='$vorname', nachname='$nachname', strasse='$strasse', hausnummer='$hausnummer', postleitzahl='$postleitzahl',
					stadt='$stadt', bundesland='$bundesland', land='$land', telefon1='$telefon1', telefon2='$telefon2', telefon3='$telefon3', telefon4='$telefon4', telefon1art='$telefon1art', telefon2art='$telefon2art',
					telefon3art='$telefon3art', telefon4art='$telefon4art', email='$email', skype='$skype', facebook='$facebook', fax='$fax', gruppe='$gruppe', notizen='$notizen' WHERE id='$updid'";
					$update = mysql_query ( $sqlupdate );
					if ($update == true) {
						$fehler .= "<p class='erfolg'>Adressbucheintrag wurde erfolgreich geändert!</p>";
					} else {
						$fehler .= "<p class='meldung'>Fehler beim speichern</p>";
					}
				}
			}
			
			return $fehler . $ausgabe;
		}
	}
	
	/**
	 * Ermöglicht das löschen von Datensätzen aus dem Adressbuch.
	 * Nur wenn der User "steven" ist.
	 */
	function UserLoeschen() {
		try {
			$this->sqlDelete ( "adressbuch" );
		} catch ( Exception $e ) {
			echo $e->getMessage ();
		}
	}
	
	/**
	 * Verbindet Function und Eingabe von: UserErstellen
	 */
	function UserErstellen() {
		if($this->userHasRight("59", 0) == true) {
			echo $this->UserErstellenFunction ();
			echo $this->UserErstellenEingabe ();
		}
	}
	
	/**
	 * blendet die Eingabemaske zum erstellen eines Datensatzes in der Datenbank ein.
	 */
	function UserErstellenEingabe() {
		if($this->userHasRight("59", 0) == true) {
			
			$ausgabe = "";
			$vorname = isset ( $_GET ['vorname'] ) ? $_GET ['vorname'] : '';
			$nachname = isset ( $_GET ['nachname'] ) ? $_GET ['nachname'] : '';
			$strasse = isset ( $_GET ['strasse'] ) ? $_GET ['strasse'] : '';
			$hausnummer = isset ( $_GET ['hausnummer'] ) ? $_GET ['hausnummer'] : '';
			$postleitzahl = isset ( $_GET ['postleitzahl'] ) ? $_GET ['postleitzahl'] : '';
			$stadt = isset ( $_GET ['stadt'] ) ? $_GET ['stadt'] : '';
			$bundesland = isset ( $_GET ['bundesland'] ) ? $_GET ['bundesland'] : '';
			$land = isset ( $_GET ['land'] ) ? $_GET ['land'] : '';
			$telefon1 = isset ( $_GET ['telefon1'] ) ? $_GET ['telefon1'] : '';
			$telefon2 = isset ( $_GET ['telefon2'] ) ? $_GET ['telefon2'] : '';
			$telefon3 = isset ( $_GET ['telefon3'] ) ? $_GET ['telefon3'] : '';
			$telefon4 = isset ( $_GET ['telefon4'] ) ? $_GET ['telefon4'] : '';
			$telefon1art = isset ( $_GET ['telefon1art'] ) ? $_GET ['telefon1art'] : '';
			$telefon2art = isset ( $_GET ['telefon2art'] ) ? $_GET ['telefon2art'] : '';
			$telefon3art = isset ( $_GET ['telefon3art'] ) ? $_GET ['telefon3art'] : '';
			$telefon4art = isset ( $_GET ['telefon4art'] ) ? $_GET ['telefon4art'] : '';
			$email = isset ( $_GET ['email'] ) ? $_GET ['email'] : '';
			$fax = isset ( $_GET ['fax'] ) ? $_GET ['fax'] : '';
			$gruppe = isset ( $_GET ['gruppe'] ) ? $_GET ['gruppe'] : '';
			$skype = isset ( $_GET ['skype'] ) ? $_GET ['skype'] : '';
			$facebook = isset ( $_GET ['facebook'] ) ? $_GET ['facebook'] : '';
			$geburtstag = isset ( $_GET ['geburtstag'] ) ? $_GET ['geburtstag'] : '';
			$notizen = isset ( $_GET ['notizen'] ) ? $_GET ['notizen'] : '';
			
			
			if (isset ( $_GET ["eintragenja"] )) {
				$ausgabe .= "
				<form method=post>
				<div id='draggable' class='newChar'>
				<a href='?' class='highlightedLink'>Schließen</a></h2>
				<h2>Eingabebereich</h2>
				
				<table class='AdressTable'>
	
				<tr>
				<td><input type='text'   name='vorname' value='$vorname' placeholder='Vorname*' required autofocus></td>
				<td><input type='text'  name='nachname' value='$nachname' placeholder='Nachname*' required></td>
				</tr>
	
				<tr>
				<td><input type='text'   name='strasse' value='$strasse' placeholder='Straße' >
				<input type='text'   name='hausnummer' value='$hausnummer' placeholder='Nr.' ></td>
				<td><input type='text'  name='geburtstag' value='$geburtstag'  placeholder='Geburtstag'></td>
				</tr>
	
				<tr>
				<td><input type='text' name='postleitzahl' value='$postleitzahl' placeholder='PLZ'></td>
				<td><input type='text' name='bundesland' value='$bundesland' placeholder='Bundesland'></td>
				</tr>
	
				<tr>
				<td><input type='text'  name='stadt' value='$stadt' placeholder='Stadt'></td>
				<td><input type='text'  name='land' value='$land' placeholder='Land'></td>
				</tr>
	
				<tr>
				<td><input type='text'   name='telefon1' value='$telefon1' placeholder='Telefon1' >
				<input type='text' size='10'  name='telefon1art' value='$telefon1art' placeholder='Handy' ></td>
				<td><input type='text'   name='fax' value='$fax' placeholder='Fax'></td>
				</tr>
	
				<tr>
				<td><input type='text'   name='telefon2' value='$telefon2' placeholder='Telefon2' >
				<input type='text' size='10'  name='telefon2art' value='$telefon2art' placeholder='Home' ></td>
				<td><input type='text'   name='gruppe' value='$gruppe' placeholder='Gruppe'></td>
				</tr>
	
				<tr>
				<td><input type='text'   name='telefon3' value='$telefon3' placeholder='Telefon 3' >
				<input type='text' name='telefon3art' value='$telefon3art' placeholder='Arbeit' ></td>
				<td><input type='text'   name='email' value='$email' placeholder='E-Mail'></td>
				</tr>
	
				<tr>
				<td><input type='text'   name='telefon4' value='$telefon4' placeholder='Telefon 4' >
				<input type='text' name='telefon4art' value='$telefon4art' placeholder='Privat' ></td>
				</tr>
	
				<tr>
				<td><input type='text'   name='skype' value='$skype'  placeholder='Skypename'></td>
				<td><input type='text'   name='facebook' value='$facebook' placeholder='facebook'>
				</tr>
	
				<tr>
				<td colspan='4'>
				<textarea name='notizen' class='ckeditor'>$notizen</textarea>
				</td>
				</tr>
				</table>
				<input type='hidden' name='eintragenja' value='' />
				<input type='submit' name='eintragen' value='Speichern'>
				<a href='?' class='highlightedLink'>Schließen</a></h2>
				</div>
				</form>";
			}
			
			return $ausgabe;
		}
	}
	
	/**
	 * Ermöglicht das eintragen von Datensätzen zum Adressbuch in die Datenbank, die
	 * nötigen GET Vars liefert UserErstellenEingabe();
	 */
	function UserErstellenFunction() {
		if($this->userHasRight("59", 0) == true) {
			
			$ausgabe = "";
			
			if (isset ( $_POST ['eintragen'] )) {
				if ($_POST ['eintragen'] != "") {
					$vorname = $_POST ["vorname"];
					$nachname = $_POST ["nachname"];
					$strasse = $_POST ["strasse"];
					$hausnummer = $_POST ["hausnummer"];
					$postleitzahl = $_POST ["postleitzahl"];
					$stadt = $_POST ["stadt"];
					$bundesland = $_POST ["bundesland"];
					$land = $_POST ["land"];
					$telefon1 = $_POST ["telefon1"];
					$telefon2 = $_POST ["telefon2"];
					$telefon3 = $_POST ["telefon3"];
					$telefon4 = $_POST ["telefon4"];
					$telefon1art = $_POST ["telefon1art"];
					$telefon2art = $_POST ["telefon2art"];
					$telefon3art = $_POST ["telefon3art"];
					$telefon4art = $_POST ["telefon4art"];
					$email = $_POST ["email"];
					$fax = $_POST ["fax"];
					$gruppe = $_POST ["gruppe"];
					$skype = $_POST ["skype"];
					$facebook = $_POST ["facebook"];
					$geburtstag = $_POST ["geburtstag"];
					$notizen = $_POST ["notizen"];
					if ($nachname == "" or $vorname == "") {
						$ausgabe .= "<p class='meldung'>Eingabefehler. Es muss mindestens ein Vorname und ein Nachname eingegeben werden. <a href='?eintragenja=1' class='buttonlink'>Zurück</a></p>";
					} else {
						$eintrag = "INSERT INTO adressbuch (geburtstag, vorname, nachname, strasse, hausnummer, postleitzahl,
						stadt, bundesland, land, telefon1, telefon2, telefon3, telefon4, telefon1art, telefon2art,
						telefon3art, telefon4art, email, skype, facebook, fax, gruppe, notizen) VALUES ('$geburtstag',
						'$vorname','$nachname','$strasse','$hausnummer','$postleitzahl','$stadt','$bundesland','$land'
						,'$telefon1','$telefon2','$telefon3','$telefon4','$telefon1art','$telefon2art','$telefon3art',
						'$telefon4art','$email','$skype','$facebook','$fax','$gruppe','$notizen')";
						$eintragen = mysql_query ( $eintrag );
						if ($eintragen == true) {
							$ausgabe .= "<p class='erfolg'>Adressbucheintrag wurde erfolgreich hinzugefügt! <a href='?eintragenja=1'>Zurück</a></p>";
						} else {
							$ausgabe .= "<p class='meldung'>Fehler beim speichern der Daten, das tut uns leid.<a href='?eintragenja=1'>Zurück</a></p>";
						}
					}
				}
			}
			
			return $ausgabe;
		}
	}
	
	/**
	 * ließt alle eingetragenen Datensätze aus dem Adressbuch aus.
	 * $query z. B.: "SELECT id, vorname, nachname FROM adressbuch ORDER BY nachname"
	 * Gibt aus: $row->tabellenspalte
	 */
	function datenbankListAllerEintraege($query) {
		if($this->userHasRight("13", 0) == true) {
			$ausgabe = "";
			
			$ergebnis = mysql_query ( $query );
			$row = mysql_fetch_object ( $ergebnis ) or die ( "Error: $query <br>" . mysql_error () );
			$ausgabe .= "<div></div>";
			while ( $row = mysql_fetch_object ( $ergebnis ) ) {
				$ausgabe .= "<div class='adresseintrag'>";
				$ausgabe .= "<table>";
				$ausgabe .= "<tr>";
				$ausgabe .= "<td colspan='2'>	<a href='eintrag.php?bearbeiten=$row->id'>$row->vorname	";
				$ausgabe .= "	$row->nachname ";
				if ($row->gruppe != "") {
					$ausgabe .= "($row->gruppe)</a>";
				}
				$ausgabe .= "</td>";
				$ausgabe .= "<td>	<a href='eintrag.php?bearbeiten=$row->id' class='adressbuchlink'>&#10150;</a>	</td>";
				$ausgabe .= "</tr>";
				if ($row->telefon1 != "") {
					$ausgabe .= "<tr><td><strong> $row->telefon1art: </strong> </td><td><strong> $row->telefon1 </strong> </td></tr>";
				} else if ($row->email != "") {
					$ausgabe .= "<tr><td colspan='3'><strong> $row->email</td></tr>";
				} else {
					$ausgabe .= "<tr><td colspan = '2' class='grey'>Füge eine Nummer hinzu!</td><td></td></tr>";
				}
				
				$ausgabe .= "</table>";
				$ausgabe .= "</div>";
			}
			return $ausgabe;
		}
	}
	
	/**
	 * Zeigt alle Geburtstage aus dem gewählten Monat an.
	 */
	function showMonthGesamt() {
		
		if($this->userHasRight("22", 0) == true) {
			
			if (isset ( $_GET ['month'] )) {
				$monat = $_GET ['month'];
				if ($monat != "") {
					$select = "SELECT *, month(geburtstag) AS monat, day(geburtstag) as tag FROM adressbuch WHERE month(geburtstag) = '$monat' ORDER BY tag";
					
					$ergebnis = mysql_query ( $select );
					echo "<div id='draggable' class='summe'>";
					echo "<a href='?#gebs' class='closeSumme'>X</a>";
					echo "<h2>Detailansicht Monat</h2>";
					while ( $row = mysql_fetch_object ( $ergebnis ) ) {
						echo "<a href='eintrag.php?bearbeiten=$row->id'><strong>" . $row->tag . ".</strong> " . $row->vorname . " " . $row->nachname . "</a><br>";
					}
					
					echo "</div>";
				}
			}
		}
	}
	
	/**
	 * Gibt die Kontakte einer bestimmten Gruppe aus.
	 * @param unknown $group
	 * @return unknown
	 */
	function showContractsFromGroup($group) {
		if($this->userHasRight("22", 0) == true) {
			$contacts = $this->getObjectsToArray("SELECT * FROM adressbuch WHERE gruppe = '$group'");
		
			return $contacts;
		}
	}
	
	function arrayAusgeben($array) {
		for ($i = 0 ; $i < sizeof($allgroups) ; $i++) {
			echo "<p>" . $array[$i] . "</p>";
		}
	}
	
	/**
	 * Zeigt die Geburtstage aus der Adressbuchdatenbank an.
	 */
	function gebKalender() {
		if($this->userHasRight("22", 0) == true) {
			echo "<div class='mainbodyDark'>Gruppen:";
			# Alle Gruppen aus DB ziehen: 
			$allgroups = $this->getObjectsToArray ( "SELECT * FROM adressbuch GROUP BY gruppe" );
			
			# Checkboxen erstellen:
			echo "<form action='#gebs' method=post>";
			for($i = 0; $i < sizeof ( $allgroups ); $i ++) {
				
				# Kontakte ohne Gruppe ignorieren:
				if($allgroups [$i]->gruppe != "") {
					echo " <input onChange='this.form.submit();' type=checkbox name='checkedGroups[$i]' value='" . $allgroups [$i]->gruppe . "' ";
				}
				
				if (isset ( $_POST ['checkedGroups'] )) {
					if (isset ( $_POST ['checkedGroups'] [$i] )) {
						if ($_POST ['checkedGroups'] [$i] == $allgroups [$i]->gruppe) {
							echo " checked=checked ";
						}
					}
				}
				
				echo " ></input> ";
				if ($allgroups [$i]->gruppe == "") {
					# echo "<label for='" . $allgroups [$i]->gruppe . "' >Ohne Gruppe</label>";
				} else {
					echo "<label for='" . $allgroups [$i]->gruppe . "' >" . $allgroups [$i]->gruppe . "</label>";
				}
			}
			
			echo "</form>";
			echo "</div>";
			
			$order = " ORDER BY tag";
			
			if (isset ( $_POST ['checkedGroups'] )) {
				$selectedGroups = $_POST ['checkedGroups'];
			} else {
				$selectedGroups = "";
			}
			
			$counterFürOR = 0;
			
			for($i = 1; $i <= 12; $i ++) {
				echo "<div class='kalender'>";
				echo "<h2><a href='?month=$i#gebs'>" . $this->getMonthName ( $i ) . "</a></h2>";
				if ($this->userHasRight("22",0) == true) {
					
					if($selectedGroups == "")  {
						$query = "SELECT *, month(geburtstag) AS monat, day(geburtstag) as tag
						FROM adressbuch
						WHERE month(geburtstag) = '$i' ORDER BY tag, vorname";
					} else {
						# Query zusammenbauen:
						$query = "SELECT *, month(geburtstag) AS monat, day(geburtstag) as tag
						FROM adressbuch
						WHERE ";
						
						$counterFürOR = 0;
						
						# WHERE Klauseln bauen:
						for ($y = 0; $y < sizeof($allgroups) ; $y++) {
							if(isset($selectedGroups[$y]) AND $selectedGroups[$y] != "") {
								
								$query .= "month(geburtstag) = '$i' AND gruppe = '". $selectedGroups[$y] . "' ";
								$counterFürOR = $counterFürOR + 1;
								
								if($counterFürOR < sizeof($selectedGroups)) {
									$query .= "OR ";
								}
								
							}
						}
						# Query ordnen nach:
						
						$query .= " ORDER BY tag, vorname";
					}
					
					# $query zur Überprüfungszwecken anzeigen:
					# echo "<div class='newChar'>" . $query . " Größe von SelectedGroups:" . sizeof($selectedGroups) .  " | Counter: $counterFürOR</div>";
					
					# Aus Datenbank laden: 
					$getGeburtstage = $this->getObjectsToArray($query);
					
					$j = 1;
					if (sizeof($getGeburtstage) > 6) { // wenn nicht genug Platz ist.
						for($x = 0; $x < sizeof($getGeburtstage) AND $j <= 5 ; $x++ ) {
							
							$monat = $getGeburtstage[$x]->monat;
							
							echo "<a href='eintrag.php?bearbeiten=" . $getGeburtstage[$x]->id . "'><strong>" . $getGeburtstage[$x]->tag . ".</strong> " . $getGeburtstage[$x]->vorname . " " . $getGeburtstage[$x]->nachname . "</a> <br> ";
							$j ++;
						}
					echo "<br><a href='?month=$monat#gebs' class='kalenderLink'>alle</a>";
					} else { // wenn genug plazt ist.
						for($x = 0; $x < sizeof($getGeburtstage) ; $x++) {
							echo "<a href='eintrag.php?bearbeiten=" . $getGeburtstage[$x]->id . "'><strong>" . $getGeburtstage[$x]->tag . ".</strong> " . $getGeburtstage[$x]->vorname . " " . $getGeburtstage[$x]->nachname . "</a><br>";
						}
					} # ende else
				} # ende check
				echo "</div>";
			} # ende FOR
		}
	}
} /* Ende der Class */

?>