<?php
include 'objekt/functions.class.php';
/**
 * Ermöglicht das Erstellen und Verwalten von Fahrtkosten.
 * 
 * @author BSSCHOE
 *        
 */
class fahrten extends functions {
	
	/**
	 * Zeigt eine Statistik zum Thema Verbrauch usw.
	 * an.
	 */
	function showStatistik() {
		if($this->userHasRight("60", 0) == true) {
			echo "<h2>Statistik</h2>";
			
			echo "<div class=''>";
			echo "<table class='flatnetTable'>";
			echo "<thead><td>Ziele</td><td></td><td>Gesamt</td></thead>";
			
			$userID = $this->getUserID ( $_SESSION ['username'] );
			$ziele = $this->getObjektInfo ( "SELECT *, count(ziel) as anzahl FROM fahrkosten WHERE besitzer = '$userID' AND spritpreis > 0 GROUP BY ziel" );
			
			$kmgesamt = 0;
			
			for($i = 0; $i < sizeof ( $ziele ); $i ++) {
				
				$entfernungInfo = $this->getObjektInfo ( "SELECT * FROM fahrkostenziele WHERE besitzer = '$userID' AND name = '" . $ziele [$i]->ziel . "' LIMIT 1" );
				
				$kmgesamt = $entfernungInfo[0]->entfernung * $ziele[$i]->fahrrichtung * $ziele[$i]->anzahl;
				echo "<tbody><td>" . $ziele [$i]->ziel . " (" . $entfernungInfo[0]->entfernung . " km)" . " </td><td>" . $ziele [$i]->anzahl . "</td><td>$kmgesamt km</td></tbody>";
			}
			
			
			
			echo "<thead><td colspan=4>Fahrzeuge</td></thead>";
			$infos = $this->getObjektInfo ( "SELECT *, count(fahrart) as anzahl FROM fahrkosten WHERE besitzer = '$userID' GROUP BY fahrart" );
			
			for($i = 0; $i < sizeof ( $infos ); $i ++) {
				
				$fahrzeugeInfo = $this->getObjektInfo ( "SELECT * FROM fahrzeuge WHERE besitzer = '$userID' AND name_tag = '" . $infos [$i]->fahrart . "' LIMIT 1" );
				
				// Berechnung der Kosten:
				if(!isset($fahrzeugeInfo[0]->verbrauch)) {
					$gesamtKosten = 0;
				} else {
					$gesamtkilometer = 0;
					
					$zielorteDiesesFahrzeugs = $this->getObjektInfo("SELECT * FROM fahrkosten WHERE besitzer=$userID AND fahrart='".$fahrzeugeInfo[0]->name_tag."' GROUP BY ziel");
					# Pro Zielort die KM berechnen
					for($j = 0 ; $j < sizeof($zielorteDiesesFahrzeugs) ;$j++) {
						# Anzahl der Fahrten bekommen:
						$getAnzahlDerFahrten = $this->getObjektInfo("SELECT sum(fahrrichtung) as summe FROM fahrkosten WHERE besitzer=$userID AND fahrart='".$fahrzeugeInfo[0]->name_tag."' AND ziel='".$zielorteDiesesFahrzeugs[$j]->ziel."' ");
						$zielortEntfernung = $this->getObjektInfo("SELECT * FROM fahrkostenziele WHERE besitzer=$userID AND name='".$zielorteDiesesFahrzeugs[$j]->ziel."' ");
						$kmProZielort = $zielortEntfernung[0]->entfernung * $getAnzahlDerFahrten[0]->summe;
						echo "<tbody><td>".$fahrzeugeInfo[0]->name_tag . "</td><td>".$zielorteDiesesFahrzeugs[$j]->ziel. "</td><td>" . $kmProZielort . " KM</td></tbody>";
					}
					
				}

			}
			
			echo "</table>";
			echo "</div>";
		}
	}
	
	/**
	 * Zeigt alle Fahrten an.
	 */
	function showFahrten() {
		
		if($this->userHasRight("11", 0) == true) {
			echo "<div class='newFahrt'>";
			$userID = $this->getUserID ( $_SESSION ['username'] );
			
			# ###################################
			# SEITEN ANZEIGEN					#
			# ###################################
			$proSeite = 15;
			$this->SeitenZahlen("SELECT *, day(datum) as tag, month(datum) as monat, year(datum) as jahr FROM fahrkosten WHERE besitzer = '$userID' ORDER BY datum DESC", $proSeite);
			$LimitUndOffset = $this->seitenAnzeigen($proSeite);
			# Neue Query wird gelesen und in die richtige Query eingefügt:
			$fahrten = $this->getObjektInfo ( "SELECT *, day(datum) as tag, month(datum) as monat, year(datum) as jahr FROM fahrkosten WHERE besitzer = '$userID' ORDER BY datum DESC $LimitUndOffset " );
			$this->getFahrtInfo($fahrten);
			# ################################# #
			# Tabelle anzeigen					#
			# ################################# #
	
			echo "<table class='flatnetTable'>";
			echo "<thead>";
			echo "<td>Datum</td>";
			echo "<td>Fahrart</td>";
			echo "<td>Ziel</td>";
			echo "<td>Notizen</td>";
			echo "<td>Spritpreis</td>";
			echo "<td>Kosten</td>";
			echo "<td></td><td></td>";
			for($i = 0; $i < sizeof ( $fahrten ); $i ++) {
				echo "<tbody><td>";
				echo "<a href='#' onclick=\"document.getElementById('fahrt$i').style.display = 'block'\">" . $fahrten [$i]->tag . "." . $fahrten [$i]->monat . "." . $fahrten [$i]->jahr . "</a></td><td>" . $fahrten [$i]->fahrart . "</td><td>" . $fahrten [$i]->ziel . "</td><td>" . substr ( $fahrten [$i]->notizen, 0, 10 ) . "..</td><td>" . $fahrten [$i]->spritpreis . " &#8364;</td>";
				
				// BERECHNUNG DER KOSTEN
				echo "<td>";
				// Fahrzeug Infos:
				$fahrzeugInfo = $this->getObjektInfo ( "SELECT * FROM fahrzeuge WHERE besitzer = '$userID' AND name_tag = '" . $fahrten [$i]->fahrart . "'" );
				// Streckeninfos:
				$zielInfo = $this->getObjektInfo ( "SELECT * FROM fahrkostenziele WHERE besitzer = '$userID' AND name = '" . $fahrten [$i]->ziel . "'" );
				
				if(!isset($fahrzeugInfo[0])) {
					$error = "ERROR";
					$liter = 0 / 100 * $zielInfo[0]->entfernung * $fahrten [$i]->fahrrichtung;
				} else {
					$error = "";
					$liter = $fahrzeugInfo[0]->verbrauch / 100 * $zielInfo[0]->entfernung * $fahrten [$i]->fahrrichtung;
						
				}
				
				$kosten = round ( $liter * $fahrten [$i]->spritpreis, 2 );
				echo $kosten . " &#8364;";
				echo "</td>";
				// OPTIONEN
				echo "<td>$error<a href='?edit=" . $fahrten [$i]->id . "' class='rightBlueLink'>edit</a></td>";
				echo "<td><a href='?loeschen&loeschid=" . $fahrten [$i]->id . "' class='rightRedLink'>X</a></td>";
				echo "</tbody>";
				echo "</div>";
			}
			
			echo "</table>";
		} else {
			echo "<p class='meldung'>Keine Rechte</p>";
		}
	}
	
	/**
	 * Gibt Detailsinformationen zu einer bestimmten Fahrt zurück. Hat den Charakter einer Statistik.
	 */
	function getFahrtInfo($fahrten) {
		
		for ($i = 0 ; $i < sizeof($fahrten) ; $i++) {
			echo "<div class='summe' style=\"display: none;\" id=\"fahrt$i\">";
			echo "<a href=\"#\"  class='highlightedLink' onclick=\"document.getElementById('fahrt$i').style.display = 'none'\">X</a>";
			echo "<h2>Fahrt vom " . $fahrten[$i]->datum . "</h2>";
			
			$userID = $this->getUserID($_SESSION['username']);
			$fahrzeugInfo = $this->getObjektInfo ( "SELECT * FROM fahrzeuge WHERE besitzer = '$userID' AND name_tag = '" . $fahrten [$i]->fahrart . "'" );
			
			$zielInfo = $this->getObjektInfo ( "SELECT * FROM fahrkostenziele WHERE besitzer = '$userID' AND name = '" . $fahrten [$i]->ziel . "'" );
			$liter = $fahrzeugInfo[0]->verbrauch / 100 * $zielInfo[0]->entfernung * $fahrten [$i]->fahrrichtung;
			$kosten = round ( $liter * $fahrten [$i]->spritpreis, 2 );
			
			echo "<p>Du bist mit dem <strong>" . $fahrten[$i]->fahrart . " (".$fahrzeugInfo[0]->name.")</strong> gefahren und hattest das Ziel <strong>" .$fahrten[$i]->ziel. "</strong></p>";
			echo "<p>Du hast <strong>$liter</strong> Liter verbraucht und <strong>$kosten</strong> € dafür bezahlt. </p>";
			echo "<p>Notizen: <strong>" .$fahrten[$i]->notizen . "</strong></p>";
			echo "</div>";
		}
		
	}
	
	/**
	 * Ermöglicht das erstellen einer neuen Fahrt
	 */
	function newFahrt() {
		// Right Check
		if($this->userHasRight("12", 0) == true) {
			
			// Feld zur Eingabe anzeigen.
			echo "<div class='newFahrt' id=\"neueFahrt\">";
		#	echo "<a href=\"#\"  class='highlightedLink' onclick=\"document.getElementById('neueFahrt').style.display = 'none'\">X</a>";
			echo "<h2>Fahrt hinzufügen</h2>";
			echo "<form method=post>";
			
			# bereits vorhandene Variablen erkennen:
			if (isset ( $_POST ['fahrart'] )) {
				$fahrtart = $_POST ['fahrart'];
			} else {
				$fahrtart = "";
			}
			if (isset ( $_POST ['ziel'] )) {
				$ziel = $_POST ['ziel'];
			} else {
				$ziel = "";
			}
			
			# Heutiges Datum:
			$timestamp = time();
			$heute = date("Y-m-d", $timestamp);
			
			#################################### Neue Darstellung ###################################
			### Datum
			
			echo "<div id=datum> <input type=date id='datepicker' placeholder='Datum' name='datum' value='$heute' required /></div>";
			
			### Fahrzeuge
			echo "<div id=fahrzeuge>";
			$userID = $this->getUserID ( $_SESSION ['username'] );
			$fahrzeuge = $this->getObjektInfo ( "SELECT * FROM fahrzeuge WHERE besitzer = '$userID'" );
			
			for($i = 0; $i < sizeof ( $fahrzeuge ); $i ++) {
				echo "<input type=radio name='fahrart' value='".$fahrzeuge[$i]->name_tag."' id='".$fahrzeuge[$i]->name_tag."'>";
				echo "<label for='".$fahrzeuge[$i]->name_tag."'><span><span></span></span>".$fahrzeuge[$i]->name_tag."</label>";
			}
			echo "</div>";
			
			### ZIELE
			echo "<div id=ziele>";
			$userID = $this->getUserID ( $_SESSION ['username'] );
			$ziele = $this->getObjektInfo ( "SELECT * FROM fahrkostenziele WHERE besitzer = '$userID'" );
				
			for($i = 0; $i < sizeof ( $ziele ); $i ++) {
				echo "<input type=radio name='ziel' value='".$ziele[$i]->name."' id='".$ziele[$i]->name."'>";
				echo "<label for='".$ziele[$i]->name."'><span><span></span></span>".$ziele[$i]->name."</label>";
			}
			echo "</div>";
			
			echo "<div id=bemerkungen><input type=text placeholder='Platz für Bemerkungen' name='notizen' value='' /></div>";
			
			echo "<div id=zusatz>";
			// Spritpreis: Letzten Spritpreis automatisch einfügen.
			$userID = $this->getUserID ( $_SESSION ['username'] );
			$lastPreis = $this->getObjektInfo ( "SELECT id, spritpreis as preis FROM fahrkosten WHERE besitzer = '$userID' order by id DESC" );
			if(isset($lastPreis[0]->preis)) {
				$preis = $lastPreis[0]->preis;
			} else {
				$preis = 0;
			}
				echo "<span id=anzahlFahrten><input type=text placeholder='Hin- und Rückfahrt?' name='fahrrichtung' value='2' /> Fahrten</span>";
				echo "<span id=spritpreis><input type=text placeholder='Preis' name='spritpreis' value='$preis' /> Spritpreis</span>";
			echo "</div>";
			
			echo "<div id='absenden'><input type=submit name=ok value='Absenden' /></div>";

			echo "</form>";
			
			echo "</div>";
			
			// Wenn abgeschickt wurde:
			if (isset ( $_POST ['ok'] )) {
				if ($_POST ['datum'] != "" and $_POST ['fahrart'] != "" and $_POST ['ziel'] != "") {
					
					// Benötigte Variablen erstellen.
					$userID = $this->getUserID ( $_SESSION ['username'] );
					$datum = $_POST ['datum'];
					$fahrtart = $_POST ['fahrart'];
					$ziel = $_POST ['ziel'];
					$notizen = $_POST ['notizen'];
					$spritpreis = $_POST ['spritpreis'];
					$fahrrichtung = $_POST ['fahrrichtung'];
					
					// Einfügen
					$query = "INSERT INTO fahrkosten (besitzer, datum, fahrart, ziel, notizen, spritpreis, fahrrichtung)
						values ('$userID','$datum','$fahrtart','$ziel','$notizen','$spritpreis','$fahrrichtung')";
					
					if ($this->sql_insert_update_delete ( $query ) == true) {
						
						echo "<p class='erfolg'>Erfolg</p>";
					} else {
						
						echo "<p class='meldung'>Fehler</p>";
					}
				} else {
					echo "<p class='meldung'>Einige Felder waren leer!</p>";
				} // If alles leer ENDE
			} // If Isset OK ende
				  // }
				  
			// ENDE
		} // Right Check Ende
	} // Function Ende
	
	/**
	 * Ermöglicht das bearbeiten einer bestehenden Fahrt.
	 */
	function alterFahrt() {
		if($this->userHasRight("12", 0) == true) {
			
			//
			// LOESCHEN
			//
			if (isset ( $_GET ['loeschid'] )) {
				
				// Prüfen ob der "löscher" das zu löschende Objekt besitzt.
				$userID = $this->getUserID ( $_SESSION ['username'] );
				$id = (isset ( $_GET ['loeschid'] )) ? $_GET ['loeschid'] : '';
				$objekt = $this->getObjektInfo ( "SELECT * FROM fahrkosten WHERE id = $id LIMIT 1" );
				if ($userID == $objekt[0]->besitzer) {
					$this->sqlDelete ( "fahrkosten" );
				} else {
					echo "<p class='meldung'>Fehler</p>";
				}
			}
			
			//
			// EDITIEREN
			//
			if (isset ( $_GET ['edit'] )) {
				$id = $_GET ['edit'];
				
				echo "<div id='draggable' class='newFahrt'>";
				echo "<a href='?' class='highlightedLink'>X</a>";
				
				// Wenn abgeschickt wurde:
				if (isset ( $_POST ['editOk'] )) {
					if ($_POST ['datum'] != "" and $_POST ['fahrart'] != "" and $_POST ['ziel'] != "") {
						
						// Benötigte Variablen erstellen.
						$userID = $this->getUserID ( $_SESSION ['username'] );
						$datum = $_POST ['datum'];
						$fahrart = $_POST ['fahrart'];
						$ziel = $_POST ['ziel'];
						$notizen = $_POST ['notizen'];
						$spritpreis = $_POST ['spritpreis'];
						if ($spritpreis == "") {
							$spritpreis = 0;
						}
						$fahrrichtung = $_POST ['fahrrichtung'];
						$id = $_POST ['id'];
						
						// Einfügen
						$query = "UPDATE fahrkosten SET 
						datum='$datum', 
						fahrart='$fahrart',
						ziel='$ziel', 
						notizen='$notizen',
						spritpreis='$spritpreis',
						fahrrichtung='$fahrrichtung'
						
						WHERE besitzer='$userID' 
						AND id='$id'";
						
						if ($this->sql_insert_update_delete ( $query ) == true) {
							echo "<p class='erfolg'>Erfolg</p>";
						} else {
							echo "<p class='meldung'>Fehler</p>";
						}
					} else {
						echo "<p class='meldung'>Einige Felder sind leer</p>";
					} // If alles leer ENDE
				} // If Isset OK ende
				
				$userID = $this->getUserID ( $_SESSION ['username'] );
				$objektInfo = $this->getObjektInfo ( "SELECT * FROM fahrkosten WHERE besitzer = '$userID' AND id = '$id'" );
				
				echo "<h2>Eintrag editieren</h2>";
				echo "<form method=post>";
				
				echo "<table><tr><td>Datum</td><td><input type=date placeholder='Datum' name='datum' value='" . $objektInfo[0]->datum . "' /></td></tr>";
				
				$userID = $this->getUserID ( $_SESSION ['username'] );
				$fahrzeuge = $this->getObjektInfo ( "SELECT * FROM fahrzeuge WHERE besitzer = '$userID'" );
				echo "<tr><td>Welches Fahrzeug</td><td><select name='fahrart'>";
				echo "<option></option>";
				for($i = 0; $i < sizeof ( $fahrzeuge ); $i ++) {
					echo "<option";
					if ($objektInfo[0]->fahrart == $fahrzeuge [$i]->name_tag) {
						echo " selected ";
					}
					echo ">" . $fahrzeuge [$i]->name_tag . "</option>";
				}
				echo "</select></td></tr>";
				
				$ziele = $this->getObjektInfo ( "SELECT * FROM fahrkostenziele WHERE besitzer = '$userID'" );
				echo "<tr><td>Welches Ziel: </td>";
				echo "<td><select name='ziel'>";
				echo "<option></option>";
				for($i = 0; $i < sizeof ( $ziele ); $i ++) {
					echo "<option";
					if ($objektInfo[0]->ziel == $ziele [$i]->name) {
						echo " selected ";
					}
					echo ">" . $ziele [$i]->name . "</option>";
				}
				echo "</select></td></tr>";
				
	# alt	#	echo "<tr><td>Bemerkungen</td><td><input type=text placeholder='Notizen' name='notizen' value='" . $objektInfo->notizen . "' /></td></tr>";
				echo "<tr><td>Bemerkungen</td><td><textarea placeholder='Notizen' name='notizen' rows=2 cols=70>" . $objektInfo[0]->notizen . "</textarea></td></tr>";
				echo "<tr><td>Spritpreis: </td><td><input type=text placeholder='Preis' name='spritpreis' value='" . $objektInfo[0]->spritpreis . "' /></td></tr>";
				echo "<tr><td>Fahrrichtung: </td><td><input type=text placeholder='Hin- und Rückfahrt?' name='fahrrichtung' value='" . $objektInfo[0]->fahrrichtung . "' /></td></tr>";
				echo "<tr><td><input type=submit name=editOk value='Absenden' /></td></tr>";
				echo "<tr><td></td><td><input type=hidden name='id' value='" . $objektInfo[0]->id . "' />";
				echo "</form>";
				echo "</table>";
				
				echo "</div>";
			}
		}
	}
	
	/**
	 * Zeigt eine Liste der Fahrzeuge des Benutzers an.
	 */
	function listFahrzeuge() {
		if($this->userHasRight("11", 0) == true) {
		#	if(isset($_GET['listFahrzeuge'])) {
				$userID = $this->getUserID($_SESSION['username']);
				$fahrzeugInfos = $this->getObjektInfo("SELECT id, name, name_tag FROM fahrzeuge WHERE besitzer = '$userID'");
				
				# Ausgabe
				echo "<div class='summe' style=\"display: none;\" id=\"listFahrzeuge\">";
				echo "<a href=\"#\"  class='highlightedLink' onclick=\"document.getElementById('listFahrzeuge').style.display = 'none'\">X</a>";
				#echo "<div id='draggable' class='summe'>";
				echo "<ul>";
		#		echo "<a href='?' class='closeSumme'>X</a>";
				for ($i = 0 ; $i < sizeof($fahrzeugInfos) ; $i++) {
					echo "<div class='invSuchErgeb'><a name='fahrzeug".$fahrzeugInfos[$i]->id."' href='?alterFahrzeug=" . $fahrzeugInfos[$i]->id . "#fahrzeug".$fahrzeugInfos[$i]->id."'>" . $fahrzeugInfos[$i]->name . " = " . $fahrzeugInfos[$i]->name_tag . "</a></div>" ; 
				}
				echo "</ul>";
				echo "</div>";
				
		#	}
		}
	}
	
	/**
	 * Ermöglicht das Anlegen eines neuen Fahrzeuges.
	 */
	function newFahrzeug() {
		if($this->userHasRight("12", 0) == true) {
		
			echo "<div class='newFahrt' style=\"display: none;\" id=\"neuesFahrzeug\">";
			echo "<a href=\"#\"  class='highlightedLink' onclick=\"document.getElementById('neuesFahrzeug').style.display = 'none'\">X</a>";
			echo "<h2>Neues Fahrzeug erstellen</h2>";
			
			echo "<form method=post>";
			
			echo "<table>";
				echo "<tr><td>Vollständiger Name</td><td><input type=text placeholder='Name des Fahrzeugs' name='name' value='' required /></td></tr>";
				echo "<tr><td>Verbrauch (l / 100km)</td><td><input type=text placeholder='Verbrauch des Fahrzeugs' name='verbrauch' value='5.5' required /></td></tr>";
				echo "<tr><td>Abkürzung</td><td><input type=text placeholder='z. B. AYGO oder BAHN' name='name_tag' value='' required /></td></tr>";
				echo "<tr><td><input type=submit name=FahrzeugOk value='Absenden' /></td></tr>";
			echo "</table>";
			echo "</form>";
			echo "</div>";
			
			################## ANLAGE ##########################
			
			if (isset ( $_POST ['FahrzeugOk'] )) {
				if ($_POST ['name'] != "" and $_POST ['verbrauch'] != "" and $_POST ['name_tag'] != "") {
					
					// Benötigte Variablen erstellen.
					$userID = $this->getUserID ( $_SESSION ['username'] );
					$name = $_POST ['name'];
					$verbrauch = $_POST ['verbrauch'];
					$name_tag = $_POST ['name_tag'];
					
					// Einfügen
					$query = "INSERT INTO fahrzeuge (besitzer, name, verbrauch, name_tag) values ('$userID','$name','$verbrauch','$name_tag')";
					
					if ($this->sql_insert_update_delete ( $query ) == true) {
						
						echo "<p class='erfolg'>Fahrzeug angelegt</p>";
					} else {
						
						echo "<p class='meldung'>Fehler beim speichern!</p>";
					}
				} else {
					echo "<p class='meldung'>Einige Felder waren leer!</p>";
				} // If alles leer ENDE
			} // If Isset OK ende
		
		}
		
		
	}
	
	/**
	 * Ermögluicht das editieren und löschen von Fahrzeugen
	 */
	function alterFahrzeug() {
		if($this->userHasRight("12", 0) == true) {
			if(isset($_GET['alterFahrzeug'])) {
				$id = $_GET['alterFahrzeug'];
				
				$userID = $this->getUserID($_SESSION['username']);
				
				$getFahrzeugInfo = $this->getObjektInfo("SELECT * FROM fahrzeuge WHERE besitzer = '$userID' AND id = '$id'");
				
				echo "<div id='draggable' class='summe'><ul>";
				echo "<a href='?' class='closeSumme'>X</a>";
				
				echo "<h2><a name=fahrzeug'".$getFahrzeugInfo[0]->id."'>" . $getFahrzeugInfo[0]->name . "</a></h2>";
				
				echo "Abkürzung: " . $getFahrzeugInfo[0]->name_tag . "<br>";
				echo "Verbrauch: " . $getFahrzeugInfo[0]->verbrauch . " Liter auf 100 km<br>";
				
				echo "<a href='?listFahrzeuge' class='buttonlink'>Speichern</a>";
				echo "<a href='?listFahrzeuge' class='buttonlink'>Zurück</a>";
				echo "</ul></div>";
			}
		
		}
	}
	
	/**
	 * Zeigt eine Liste der Ziele des Benutzers an.
	 */
	function listZiele() {
		
		if($this->userHasRight("11", 0) == true) {
			
		#	if(isset($_GET['listZiele'])) {
				$userID = $this->getUserID($_SESSION['username']);
				$zieleInfos = $this->getObjektInfo("SELECT id, name, entfernung FROM fahrkostenziele WHERE besitzer = '$userID'");
					
				# Ausgabe
				echo "<div class='summe' style=\"display: none;\" id=\"listZiele\">";
				echo "<a href=\"#\"  class='highlightedLink' onclick=\"document.getElementById('listZiele').style.display = 'none'\">X</a>";
			#	echo "<div id='draggable' class='summe'>";
				echo "<ul>";
			#	echo "<a href='?' class='closeSumme'>X</a>";
				for ($i = 0 ; $i < sizeof($zieleInfos) ; $i++) {
					echo "<div class='invSuchErgeb'><a href='?alterZiel=". $zieleInfos[$i]->id ."'>" . $zieleInfos[$i]->name . " = " . $zieleInfos[$i]->entfernung . " km</a></div>" ;
				}
				echo "</ul></div>";
		#	}
		}

	}
	
	/**
	 * Ermöglicht das erstellen eines neuen Ziels.
	 */
	function newZiel() {
		if($this->userHasRight("12", 0) == true) {
			
			echo "<div class='newFahrt' style=\"display: none;\" id=\"neuesZiel\">";
			echo "<a href=\"#\"  class='highlightedLink' onclick=\"document.getElementById('neuesZiel').style.display = 'none'\">X</a>";
			echo "<h2>Neues Ziel erstellen</h2>";
			
			echo "<form method=post>";
			
			echo "<table>";
			echo "<tr><td>Name</td><td><input type=text placeholder='Name des Ziels' name='name' value='' required /></td></tr>";
			echo "<tr><td>Entfernung</td><td><input type=text placeholder='Entfernung in Kilometern' name='entfernung' value='' required /></td></tr>";
			echo "<tr><td><input type=submit name=ZielOK value='Absenden' /></td></tr>";
			echo "</table>";
			echo "</form>";
			echo "</div>";
			
			################## ANLAGE ##########################
			
			if (isset ( $_POST ['ZielOK'] )) {
				if ($_POST ['name'] != "" and $_POST ['entfernung'] != "" ) {
			
					// Benötigte Variablen erstellen.
					$userID = $this->getUserID ( $_SESSION ['username'] );
					$name = $_POST ['name'];
					$entfernung = $_POST ['entfernung'];
			
					// Einfügen
					$query = "INSERT INTO fahrkostenziele (besitzer, name, entfernung) values ('$userID','$name','$entfernung')";
			
					if ($this->sql_insert_update_delete ( $query ) == true) {
							
						echo "<p class='erfolg'>Ziel angelegt</p>";
					} else {
							
						echo "<p class='meldung'>Fehler beim speichern!</p>";
					}
				} else {
					echo "<p class='meldung'>Einige Felder waren leer!</p>";
				} // If alles leer ENDE
			} // If Isset OK ende
		}
	}
	
	/**
	 * Ermöglicht das verändern und löschen von Zielen.
	 */
	function alterZiel() {
		if($this->userHasRight("12", 0) == true) {
			if(isset($_GET['alterZiel'])) {
				$id = $_GET['alterZiel'];
				
				$userID = $this->getUserID($_SESSION['username']);
				
				$getFahrzeugInfo = $this->getObjektInfo("SELECT * FROM fahrkostenziele WHERE besitzer = '$userID' AND id = '$id'");
				
				echo "<div id='draggable' class='summe'><ul>";
				echo "<a href='?' class='closeSumme'>X</a>";
				
				echo "<h2><a name=fahrzeug'".$getFahrzeugInfo[0]->id."'>" . $getFahrzeugInfo[0]->name . "</a></h2>";
				
				echo "Entfernung: " . $getFahrzeugInfo[0]->entfernung . "<br>";
				
				echo "<a href='?listZiele' class='buttonlink'>Speichern</a>";
				echo "<a href='?listZiele' class='buttonlink'>Zurück</a>";
				echo "</ul></div>";
			}
		}
	}
}