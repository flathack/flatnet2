<?php
include 'objekt/functions.class.php';

class finanzen extends functions {

	
	
	/**
	 * Zeigt die Gesamte Finanzverwaltung an. Hieraus werden die benötigten Funktionen selbst aufgerufen.
	 */
	function showFinanzen() {
		
		if($this->userHasRight("17", 0) == true) {
		
			# Fehleranzeige
			$this->showErrors();
	
			# Konten anzeigen
			if(!isset($_GET['newUeberweisung'])) {
				$this->listKonten();
			}
			
			# Buchungsbutton anzeigen
			if(isset($_GET['id'])) {
				$id = $_GET['id'];
				echo "<a href='?newUeberweisung&ursprungKonto=$id' class='highlightedLink'>Eine Buchung durchführen</a>";
			} else {
				echo "<a href='?newUeberweisung' class='highlightedLink'>Eine Buchung durchführen</a>";
			}
			
			
			$this->kontoVerwaltung();
			
			$this->insertUeberweisung();
	
			$this->soll_haben_konten();
	
			#echo "<a href='?newUmsatz' class='highlightedLink'>Neuen Umsatz</a>";
	
			if(isset($_GET['newUmsatz'])) {
				$this->insertUmsatz();
			}
			
			$this->alterUmsatz();
	
			# Umssätze anzeigen
			if(isset($_GET['umsaetze'])
			AND isset($_GET['id'])
			AND is_numeric($_GET['id'])
			AND $_GET['id'] > 0) {
	
				$this->getUmsaetze($_GET['id']);
			}
		
		}
	}

	/**
	 * Ermöglicht das Verwalten der eigenen Konten.
	 */
	function kontoVerwaltung() {
		if(isset($_GET['kontoverwaltung'])) {
			echo "<a href='?kontoverwaltung&neuesKonto' class='highlightedLink'>Neues Konto</a>";
			if(isset($_GET['neuesKonto'])) {
				echo "<div class='newChar'><form method=post><input type=text name=newKonto value='' placeholder='Kontoname' />";
				echo "<input type=submit name=insertNewKonto value=Speichern />";
				echo "</form></div>";

				if(isset($_POST['insertNewKonto']) AND $this->userHasRight("18", 0) == true) {
					$konto = $_POST['newKonto'];
					if($konto != "") {
						$besitzer = $this->getUserID($_SESSION['username']);
						$query = "INSERT INTO finanzen_konten (besitzer,konto) VALUES ('$besitzer','$konto') ";

						if ($this->sql_insert_update_delete($query) == true) {
							echo "<p class='erfolg'>Konto erstellt</p>";
						}
					}
				}
			}
			if(isset($_GET['alterKonto'])) {
				# @todo
				
			}
			if(isset($_GET['deleteKonto'])) {
				# @todo
			}
			$user = $this->getUserID($_SESSION['username']);
			$select = "SELECT * FROM finanzen_konten WHERE besitzer = '$user'"; $konten = $this->getObjektInfo($select);
			$i = 0;
			echo "<div>";
			echo "<table class='flatnetTable'><thead><td>KontoNr.</td><td>Kontoname</td><td>Kontostand</td><td>Optionen</td></thead>";
			for($i = 0; $i < sizeof($konten); $i++) {
				echo "<tbody><td>" . $konten[$i]->id . "</td><td>" . $konten[$i]->konto . "</td><td></td><td><a href='?kontoverwaltung&alterKonto' class='buttonlink'>edit</a></td>";
			}
			echo "</table></div>";
		}
	}

	/**
	 * Gibt eine Liste der vorhandenen Konten aus.
	 */
	function listKonten() {
		$user = $this->getUserID($_SESSION['username']);
		$select = "SELECT * FROM finanzen_konten WHERE besitzer = '$user'"; $konten = $this->getObjektInfo($select);

		if(!isset($_GET['umsaetze'])) {
			echo "<p class='info'>Bitte ein Kontoauswählen, um die Umsätze anzuzeigen.</p>";
		}

		$i = 0;
		echo "<div class=''>";
		echo "<h2 name='konten'>Konto wählen</h2>";
		echo "<form method=get><select class='bigSelect' name='id'>";
		for($i = 0; $i < sizeof($konten); $i++) {
			echo "<option value='" . $konten[$i]->id . "'";
			if(isset($_GET['id'])) {
				if($_GET['id'] == $konten[$i]->id) {
					echo " selected ";
				} 
			}
			echo ">" . $konten[$i]->konto. "</a></option>";
		}
		echo "</select><input type=submit name='umsaetze' value='Konto anzeigen'></form>";
		echo "</div>";
	}

	/**
	 * Zeigt ein Konto gemäß der buchhalterischen Maßstäben zu Soll und Haben.
	 */
	function soll_haben_konten() {
		if($this->userHasRight("18", 0) == true and isset($_GET['sollhabenKonten'])) {
			$user = $this->getUserID($_SESSION['username']);
			$select = "SELECT * FROM finanzen_konten WHERE besitzer = '$user'"; $konten = $this->getObjektInfo($select);
			echo "<table class='kontoTable'>";
			echo "<thead><td colspan='2'>Kontenplan</td></thead>";
			$i = 0;
			for($i = 0; $i < sizeof($konten); $i++) {

				if(isset($konten[$i]->id)) {
					echo "<thead><td colspan='2'>" . $konten[$i]->konto . "</td></thead>";
					$kontoID = $konten[$i]->id;
					$soll = "SELECT * FROM finanzen_umsaetze WHERE besitzer = '$user' AND konto = '$kontoID' AND umsatzWert > 0";
					$sollUmsatz = $this->getObjektInfo($soll);
					$haben = "SELECT * FROM finanzen_umsaetze WHERE besitzer = '$user' AND konto = '$kontoID' AND umsatzWert < 0";
					$habenUmsatz = $this->getObjektInfo($haben);
						
					$j = 0;
					echo "<tbody><td>SOLL</td><td>HABEN</td></tbody>";
					echo "<tbody><td>";
					# SOLL AUSGABE
					$sollSumme = 0;
					for ($j = 0; $j < sizeof($sollUmsatz); $j++) {
						if(isset($sollUmsatz[$j]->id)) {
							echo "<a href='#'>BuchNr. " . $sollUmsatz[$j]->buchungsnr . "</a>: " . $sollUmsatz[$j]->umsatzWert . " " . $sollUmsatz[$j]->umsatzName . "<br>";
							$sollSumme = $sollSumme + $sollUmsatz[$j]->umsatzWert;
						}
					}
					echo "</td>";
					$j = 0;
						
					echo "<td>";
					# HABEN AUSGABE
					$habenSumme = 0;
					for ($j = 0; $j < sizeof($habenUmsatz); $j++) {
						if(isset($habenUmsatz[$j]->id)) {
							echo "<a href='#'>BuchNr. " . $habenUmsatz[$j]->buchungsnr . "</a>: " . $habenUmsatz[$j]->umsatzWert . " " . $habenUmsatz[$j]->umsatzName . "<br>";
							$habenSumme = $habenSumme + $habenUmsatz[$j]->umsatzWert;
						}
					}
					echo "</td></tbody>";
						
					echo "<tfood><td>$sollSumme €</td><td>$habenSumme €</td></tfood>";
						
				}
			}
				
		}
	}

	/**
	 * Zeigt alle möglichen Betreffs an eines 
	 * @param unknown $user
	 * @param unknown $id
	 */
	function themenAnzeigen($user, $id) {
		# Themen:
		$themen = $this->getObjektInfo("SELECT id, umsatzName FROM finanzen_umsaetze
		WHERE besitzer = $user AND konto = '$id' GROUP BY umsatzName");
		$i = 0;
		for ($i = 0; $i < sizeof($themen); $i++)  {
			echo "<a href='?umsatzKategorie=" .$themen[$i]->id. "&id=$id&umsaetze' class='buttonlink'>" . $themen[$i]->umsatzName . "</a>";
		}
	}

	/**
	 * zeigt die Umsätze eines Kontos an.
	 * @param unknown $konto
	 */
	function getUmsaetze($konto) {
		$id = $_GET['id'];
		$user = $this->getUserID($_SESSION['username']);

		$timestamp = time(); $jetzigerMonat = date("m", $timestamp);

		# Nur bestimmten Umsatznamen anzeigen
		if(isset($_GET['umsatzKategorie']) AND isset($_GET['id'])) {
			$umsatzKategorie = $_GET['umsatzKategorie'];

			$umsatzName = $this->getObjektInfo("SELECT id, umsatzName FROM finanzen_umsaetze WHERE besitzer = '$user' AND id='$umsatzKategorie'");
			$umsaetze = $this->getObjektInfo("SELECT *, day(datum) as tag, month(datum) as monat, year(datum) as jahr
					FROM finanzen_umsaetze
					WHERE besitzer = '$user' AND konto = '$id' AND umsatzName='" . $umsatzName[0]->umsatzName . "'");
		} else {
			$umsaetze = $this->getObjektInfo("SELECT *, day(datum) as tag, month(datum) as monat, year(datum) as jahr
					FROM finanzen_umsaetze
					WHERE besitzer = '$user' AND konto = '$id' ORDER BY datum ASC, id ASC");
		}

		#Alle Umsätze anzeigen

		$kontostand = $this->getObjektInfo("SELECT sum(umsatzWert) AS summe FROM finanzen_umsaetze
		WHERE besitzer = '$user'
		AND konto = '$id'");
		$kontostandArray = $this->getObjektInfo("
				SELECT * FROM finanzen_umsaetze
				WHERE besitzer = '$user'
				AND konto = '$id'");

		# Themen:
		if(isset($_POST['showThemen'])) {
			$this->themenAnzeigen($user, $id);
		}

		# Themen anzeigen
	#	echo "<form method=post><input type=submit name=showThemen value='Themen Anzeigen' /></form>";
		echo "<form method=post><input type=submit name=showEveryEntry value='Verborgene Einträge anzeigen' /></form>";
		
		
		# AUSGBE
		echo "<a href='#today' class='highlightedLink'>zu heute gehen</a>";
		echo "<table class='kontoTable'>";
		echo "<thead><td>Umsatz</td><td>Betrag</td><td>Datum</td><td colspan='2'>Kontostand</td></thead>";

		# Berechnet die vergangen Tage
		$timestamp = time(); $tag = date("d", $timestamp); $monat = date("m", $timestamp);
		$vergangen = 30.41 * ($monat - 1) + $tag; $restTage = 365 - $vergangen; $protag = ceil($kontostand->summe / $restTage);

		# Ausgabe des Kontostandes
		echo "<h2>Noch $restTage Tage bis Jahresende</h2>";
		$i = 0;	$j = 0;	$aktuellerMonat = 0;

		for ($i = 0; $i < sizeof($umsaetze); $i++)  {

			#if(isset($themen)) {
			#	$themen[$i]->umsatzName;
			#}

			# Zeigt eine Zeile pro Monat an.
			# Zwölf mal wird diese Funktion aufgerufen.
			for ($j = 0 ; $j < 12 ; $j++) {
				if($umsaetze[$i]->monat != $aktuellerMonat) {
					
					if(!isset($umsaetze[$i-1]->jahr)) {

					} else {
						if($umsaetze[$i]->jahr != $umsaetze[$i-1]->jahr AND $i != 0) {
							$aktuellerMonat = 0;
						}
					}
					$aktuellerMonat = $aktuellerMonat + 1;
					$name = $this->getMonthName($aktuellerMonat);
					
					# prüfen ob der Monat im Umsatz schon größer als der aktuelle Monat ist:
					$UmsatzMonat = $umsaetze[$i]->monat;
					
					if($UmsatzMonat > $aktuellerMonat) {
						
						# Der Umsatzmonat ist kleiner als der aktuelle Monat, also wird übersprungen, damit keine doppelten Monatsnamen auftreten
						
					} else {
						# Ausgabe der Monatszeile
						echo "<thead><td colspan='4'>" . $name . " " . $umsaetze[$i]->jahr; 
						
						if(isset($zwischenergebnis)) { 
							
							echo " </td><td>Saldo: " .$zwischenergebnis; 
						} else {
							echo "</td><td>";
						}
						echo "</td></thead>";
					
					}
				}
			}

			# Zeilenfarben
			if($umsaetze[$i]->umsatzWert < 0) {
				$zeile = "<tbody id=''>";
			} else {
				$zeile = "<tbody id='offen'>";
			}
			$kontoID = $umsaetze[$i]->gegenkonto;
			$gegenkonto = $this->getObjektInfo("SELECT * FROM finanzen_konten WHERE id = '$kontoID'");
			$zeile .= "<td><a name='#showRow" . $umsaetze[$i]->id . "' href='?umsaetze&id=$id&UmsatzID=" . $umsaetze[$i]->id . "#showRow" . $umsaetze[$i]->id . "'>BuchNr. ". $umsaetze[$i]->buchungsnr . ":</a> " . $umsaetze[$i]->umsatzName . " von " . $gegenkonto[0]->konto . "</td>";
			if($umsaetze[$i]->umsatzWert < 0) {
				$zeile .= "<td id='minus'>";
			} elseif($umsaetze[$i]->umsatzWert > 0) {
				$zeile .= "<td id='plus'>";
			}

			$zeile .= $umsaetze[$i]->umsatzWert . " €</td>
					<td>" . $umsaetze[$i]->tag . ".</td>";
			if(!isset($zwischenergebnis)) {
				$zwischenergebnis = 0;
			}
			$zwischenergebnis = $zwischenergebnis + $umsaetze[$i]->umsatzWert;
			# Wenn Zwischenergebnis kleiner als 0 ist, wird die Zeile rot.
			if($zwischenergebnis < 0) {
				$spaltenFarbe = "rot";
			} else {
				$spaltenFarbe = "rightAlign";
			}

			$zeile .= "<td id='$spaltenFarbe'>" . $zwischenergebnis .  " €</td><td><a href='?umsaetze&id=$id&UmsatzID=" . $umsaetze[$i]->id . "#showRow" . $umsaetze[$i]->id . "' 
					class=''>
					<img name='edit' style='width:20px;height:20px' alt='edit' src='data:image/png;base64,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' />
					</a></td></tbody>";
			
			# Aktuelle Zeile ausgeben:
			# Test, ob Zeile ausgegeben werden soll:
			$timestamp2 = time(); $heutigerTag = date("Y-m-d", $timestamp2);
			$monat = date("m", $timestamp2);
			$jahr = date("Y", $timestamp2);
			
			if(!isset($_POST['showEveryEntry'])) {
				if($umsaetze[$i]->monat < $monat AND $umsaetze[$i]->jahr == $jahr) {
					#echo "<tbody><td colspan='5'>Einträge verborgen...";
					#echo "</td></tbody>";
				} else {
					echo $zeile;
				}
			} else {
				echo $zeile;
			}
			
			# HEUTE Zeile anzeigen
			$timestamp = time(); $heute = date("Y-m-d", $timestamp);
			if($umsaetze[$i]->datum < $heute AND isset($umsaetze[$i+1]->datum) AND $umsaetze[$i+1]->datum >= $heute) {
				$heute = date("d.m.Y", $timestamp);
				echo "<tbody id='today'><td colspan='5'><a name='heute'>Heute ist der $heute</a> nächster Umsatz: " . $umsaetze[$i+1]->umsatzName . "</td></tbody>";
			}
		}
		echo "</table>";
	}




} # Class Ende

class finanzenNEW extends finanzen {
	
	function mainFinanzFunction() {
		$besitzer = $this->getUserID($_SESSION['username']);
		
		# Navigationslinks
		$this->showKontenInSelect($besitzer);
		$this->showJahreLinks();
		$this->showMonateLinks();
		
		# Informationsmeldungen
		$this->showErrors();
		$this->showFuturePastJahr();
		$this->showFuturePastMonat();
		$this->checkJahresabschluss();
		
		# Umsatzveränderungen
		$this->showCreateNewUeberweisung();
		$this->alterUmsatz();
		
		# Hauptansicht Monat
		$this->showCurrentMonthInKonto($besitzer);
		$this->diagrammOptionen($besitzer);
		
		# automatische Jahresabschluss-Generation.
		$this->erstelleJahresabschluesseFromOldEintraegen();
		
		# automatische Monatsabschlussgeneration
		$this->erstelleMonatsabschluesseFromOldEintraegen();
		
	}
	
	function mainKontoFunction() {
		$besitzer = $this->getUserID($_SESSION['username']);
		$this->showCreateNewKonto($besitzer);
		$this->showEditKonto($besitzer);
		$this->showDeleteKonto($besitzer);
		$this->showKontoUebersicht($besitzer);
	}
	
	private $shows;
	
	/**
	 * Zeigt den aktuellen Monat an.
	 * @param unknown $besitzer
	 */
	function showCurrentMonthInKonto($besitzer) {
		# Kontoinfo bekommen:
		$kontoID = $this->getKontoIDFromGet();
		$monat = $this->getMonatFromGet();
		$currentMonth = $monat;
		# jetziger Monat:
	
		$currentYear = $this->getJahrFromGet();
	
		if($kontoID > 0) {
			$umsaetze = $this->getUmsaetzeMonthFromKonto($besitzer, $currentMonth, $currentYear, $kontoID);
				
			# Jahresanfangssaldo bekommen:
			$letztesJahr = $currentYear - 1;
			$summeJahresabschluesseBisJetzt = $this->getJahresabschluesseBISJETZT($besitzer, $kontoID, $currentYear);
			$summeUmsaetzeDiesesJahr = $this->getObjektInfo("SELECT sum(umsatzWert) as summe 
					FROM finanzen_umsaetze
					WHERE besitzer = $besitzer
					AND konto = $kontoID
					AND year(datum) = $currentYear
					AND month(datum) < $currentMonth");
			
			# Wenn das Jahr in der Zukunft liegt, werden alle 
			# Umsätze der Vergangenheit einzeln addiert.
			$diesesJahr = date("Y");
			if($this->checkIfJahrIsInFuture($diesesJahr, $currentYear) == true) {
				$getSaldoUntilNow = $this->getObjektInfo("SELECT sum(umsatzWert) as summe 
					FROM finanzen_umsaetze
					WHERE besitzer = $besitzer
					AND konto = $kontoID
					AND year(datum) < $currentYear");
				$startsaldo = $getSaldoUntilNow[0]->summe + $summeUmsaetzeDiesesJahr[0]->summe;
			} else {
				$startsaldo = $summeJahresabschluesseBisJetzt + $summeUmsaetzeDiesesJahr[0]->summe;
			}
			$zwischensumme = $startsaldo;
			echo "<table class='kontoTable'>";
				
			echo "<thead>";
			echo "<td colspan=7>Monat:<strong> $currentMonth </strong>im Jahr <strong>$currentYear</strong> / Startsaldo diesen Monat: <strong>$startsaldo €</strong></td>";
			echo "</thead>";
	
			echo "<thead>";
			echo "<td>BuchNr.</td>";
			echo "<td>Gegenkonto.</td>";
			echo "<td>Umsatz</td>";
			echo "<td>Tag</td>";
			echo "<td>Wert</td>";
			echo "<td>Saldo</td>";
			echo "<td>Optionen</td>";
			echo "</thead>";
	
			if(isset($umsaetze[0]->id)) {
				
				# Zeilengeneration #
				
				for ($i = 0 ; $i < sizeof($umsaetze); $i++) {
					$zwischensumme = $zwischensumme + $umsaetze[$i]->umsatzWert;
					
					# Daten in Array laden:
					$zahlen[$i] = $zwischensumme;
					
					if($zwischensumme < 0) {
						$spaltenFarbe = "rot";
					} else {
						$spaltenFarbe = "rightAlign";
					}
					
					if($zwischensumme < 0) { $zeile = " id='minus' "; } else { $zeile = ""; }
					if($umsaetze[$i]->umsatzWert < 0) { $zelle = " id='minus' "; } else { $zelle = " id='plus' "; }
					echo "<tbody>";
					echo "<td>" . $umsaetze[$i]->buchungsnr . "</td>";
					# Name des Gegenkontos bekommen
					$nameGegenkonto = $this->getObjektInfo("SELECT * FROM finanzen_konten WHERE besitzer = $besitzer AND id = ".$umsaetze[$i]->gegenkonto." LIMIT 1");
					echo "<td>" . $nameGegenkonto[0]->konto . "</td>";
					echo "<td>" . $umsaetze[$i]->umsatzName . "</td>";
					echo "<td>" . $umsaetze[$i]->tag . "</td>";
					echo "<td $zelle>" . $umsaetze[$i]->umsatzWert . "</td>";
					echo "<td id='$spaltenFarbe'>" . $zwischensumme . "</td>";
					echo "<td>" . "<a class='rightBlueLink' href='?konto=$kontoID&monat=$monat&jahr=$currentYear&edit=" . $umsaetze[$i]->id . "'>edit</a>" . "</td>";
					echo "</tbody>";
					
					# HEUTE Zeile anzeigen
					$timestamp = time(); $heute = date("Y-m-d", $timestamp);
					if($umsaetze[$i]->datum < $heute AND isset($umsaetze[$i+1]->datum) AND $umsaetze[$i+1]->datum >= $heute) {
						$heute = date("d.m.Y", $timestamp);
						echo "<tbody id='today'><td colspan='7'><a name='heute'>Heute ist der $heute</a> nächster Umsatz: " . $umsaetze[$i+1]->umsatzName . "</td></tbody>";
					}
				}
			} else {
				echo "<tbody><td colspan=6>In diesem Monat gibt es keine Umsätze.</td></tbody>";
			}
			$differenz = $zwischensumme - $startsaldo;
			echo "<tfoot><td colspan=5 id='rightAlign'>Endsaldo: </td><td id='rightAlign'>$zwischensumme</td><td></td></tfoot>";
	
			echo "</table>";
			echo "<p class='info'>Kontostandveränderung: $differenz €</p>";
			
			# Zeigt das Diagramm an
			if(isset($zahlen)) {
				$this->showDiagramme($zahlen, "700", "200");
			}
			
		}
	}
	
	/**
	 * Zeigt Optionen für das Diagramm an.
	 */
	function diagrammOptionen($besitzer) {
		if(isset($_GET['konto']) AND isset($_GET['jahr'])) {
			echo "<div>";
			$konto = $_GET['konto'];
			
			$jahr = $_GET['jahr'];
			echo "<a class='buttonlink' href='?kontoOpt=$konto&jahr=$jahr&gesamtesJahr'>Jahr anzeigen</a>";
			echo "<a class='buttonlink' href='?kontoOpt=$konto&alles'>Alles anzeigen</a>";
			echo "</div>";
		}
	
		if(isset($_GET['gesamtesJahr']) AND isset($_GET['kontoOpt']) AND isset($_GET['jahr'])) {
			
			$konto = $_GET['kontoOpt'];
				
			$jahr = $_GET['jahr'];
			
			$query = "SELECT * FROM finanzen_umsaetze WHERE besitzer = $besitzer AND konto = $konto AND year(datum) = $jahr";
			$zahlen = $this->getObjektInfo($query);
			$zwischensumme = 0;
			
			# Zwischensummen bilden und in Var schreiben
			
			for ($i = 0 ; $i < sizeof($zahlen) ; $i++) {
				$zwischensumme = $zahlen[$i]->umsatzWert + $zwischensumme;
				$arrayFuerDiagramm[$i] = $zwischensumme;
			}
			
			if(isset($arrayFuerDiagramm)) {
				$this->showDiagramme($arrayFuerDiagramm, "700", "200");
			}
			
		}
		
		if(isset($_GET['alles']) AND isset($_GET['kontoOpt'])) {
			$konto = $_GET['kontoOpt'];
			$query = "SELECT * FROM finanzen_umsaetze WHERE besitzer = $besitzer AND konto = $konto";
			$zahlen = $this->getObjektInfo($query);
			$zwischensumme = 0;
			
			for ($i = 0 ; $i < sizeof($zahlen) ; $i++) {
				$zwischensumme = $zahlen[$i]->umsatzWert + $zwischensumme;
				$arrayFuerDiagramm[$i] = $zwischensumme;
			}
			
			if(isset($arrayFuerDiagramm)) {
				$this->showDiagramme($arrayFuerDiagramm, "700", "200");
			}
		}
		
	}
	
	/**
	 * Zeigt die Navigation innerhalb der Finanzanwendung an.
	 */
	function showNavigation() {
		
		$kontoID = $this->getKontoIDFromGet();
		$monat = $this->getMonatFromGet();
		$jahr = $this->getJahrFromGet();
		
		echo "<div class='rightOuterBody'>
				<ul>
					<li><a href='index.php' >Start</a></li>
					<li><a href='konten.php' >Konten</a></li>
					<li><a href='?konto=$kontoID&monat=$monat&jahr=$jahr&newUeberweisung' >Neue Buchung</a></li>
					<li><a href='?konto=$kontoID&monat=$monat&jahr=$jahr&checkJahresabschluesse' >Jahresabschlusscheck</a></li>
				</ul>
			</div>";
	}
	
	function showMonateLinks() {
		
		if(isset($_GET['konto'])) {
			$konto = $_GET['konto'];
		} else {
			$konto = 0;
		}
		
		if(isset($_GET['jahr'])) {
			$jahr = $_GET['jahr'];
		} else {
			$jahr = date("Y");
		}
		
		if(isset($_GET['monat'])) {
			$monat = $_GET['monat'];
		} else {
			$monat = "";
		}
		
		echo "<ul class='FinanzenMonate'>";
		
			
			echo "<li "; if($monat == 1) { echo " id='selected' "; } echo "><a href='?konto=$konto&monat=1&jahr=$jahr'>Januar</a></li>";
			echo "<li "; if($monat == 2) { echo " id='selected' "; } echo "><a href='?konto=$konto&monat=2&jahr=$jahr'>Februar</a></li>";
			echo "<li "; if($monat == 3) { echo " id='selected' "; } echo "><a href='?konto=$konto&monat=3&jahr=$jahr'>März</a></li>";
			echo "<li "; if($monat == 4) { echo " id='selected' "; } echo "><a href='?konto=$konto&monat=4&jahr=$jahr'>April</a></li>";
			echo "<li "; if($monat == 5) { echo " id='selected' "; } echo "><a href='?konto=$konto&monat=5&jahr=$jahr'>Mai</a></li>";
			echo "<li "; if($monat == 6) { echo " id='selected' "; } echo "><a href='?konto=$konto&monat=6&jahr=$jahr'>Juni</a></li>";
			echo "<li "; if($monat == 7) { echo " id='selected' "; } echo "><a href='?konto=$konto&monat=7&jahr=$jahr'>Juli</a></li>";
			echo "<li "; if($monat == 8) { echo " id='selected' "; } echo "><a href='?konto=$konto&monat=8&jahr=$jahr'>August</a></li>";
			echo "<li "; if($monat == 9) { echo " id='selected' "; } echo "><a href='?konto=$konto&monat=9&jahr=$jahr'>September</a></li>";
			echo "<li "; if($monat == 10) { echo " id='selected' "; } echo "><a href='?konto=$konto&monat=10&jahr=$jahr'>Oktober</a></li>";
			echo "<li "; if($monat == 11) { echo " id='selected' "; } echo "><a href='?konto=$konto&monat=11&jahr=$jahr'>November</a></li>";
			echo "<li "; if($monat == 12) { echo " id='selected' "; } echo "><a href='?konto=$konto&monat=12&jahr=$jahr'>Dezember</a></li>";
		echo "</ul>";
	}
	
	function showJahreLinks() {
	
		if(isset($_GET['konto'])) {
			$konto = $_GET['konto'];
		} else {
			$konto = 0;
		}
		
		if(isset($_GET['monat'])) {
			$monat = $_GET['monat'];
		} else {
			$monat = date("m");
		}
		
		if(isset($_GET['jahr'])) {
			$jahr = $_GET['jahr'];
		} else {
			$jahr = date("Y");
		}
		
		echo "<ul class='FinanzenMonate'>";
		echo "<li "; if($jahr == 2013) { echo " id='selected' "; } echo "><a href='?konto=$konto&monat=$monat&jahr=2013'>2013</a></li>";
		echo "<li "; if($jahr == 2014) { echo " id='selected' "; } echo "><a href='?konto=$konto&monat=$monat&jahr=2014'>2014</a></li>";
		echo "<li "; if($jahr == 2015) { echo " id='selected' "; } echo "><a href='?konto=$konto&monat=$monat&jahr=2015'>2015</a></li>";
		echo "<li "; if($jahr == 2016) { echo " id='selected' "; } echo "><a href='?konto=$konto&monat=$monat&jahr=2016'>2016</a></li>";
		echo "<li "; if($jahr == 2017) { echo " id='selected' "; } echo "><a href='?konto=$konto&monat=$monat&jahr=2017'>2017</a></li>";
		echo "<li "; if($jahr == 2018) { echo " id='selected' "; } echo "><a href='?konto=$konto&monat=$monat&jahr=2018'>2018</a></li>";
		echo "</ul>";
	}
	
	/**
	 * Zeigt ein Select mit den Konten des Nutzers an.
	 * @param unknown $besitzer
	 */
	function showKontenInSelect($besitzer) {
		$monat = $this->getMonatFromGet();
		$jahr = $this->getJahrFromGet();
		
		$konten = $this->getAllKonten($besitzer);
		
		if(isset($_GET['konto'])) {
			$konto = $_GET['konto'];
		} else {
			$konto = "";
		}
		if(isset($konten[0]->id) AND $konten[0]->id != "") {
			echo "<ul class='FinanzenKonten'>";
			for ($i = 0; $i < sizeof($konten); $i++) {
				echo "<li ";
				if($konto == $konten[$i]->id) { echo " id='selected' "; }
				echo "><a href='?konto=".$konten[$i]->id."&monat=$monat&jahr=$jahr'>" .$konten[$i]->konto. "</a></li>";
			}
			echo "</ul>";
		}
		
	}
	
	/**
	 * Gibt die Get Variable KONTO zurück.
	 * @return unknown|boolean
	 */
	function getKontoIDFromGet() {
		if(isset($_GET['konto'])) {
			$kontoID = $_GET['konto'];
			
			return $kontoID;
		} else {
			return false;
		}
	}
	
	/**
	 * Gibt den Monat zurück.
	 * @return unknown|boolean
	 */
	function getMonatFromGet() {
		if(isset($_GET['monat'])) {
			$monat = $_GET['monat'];
		} else {
			$monat = date("m");
		}
		
		return $monat;
	}
	
	/**
	 * Gibt das Jahr zurück.
	 * @return unknown|boolean
	 */
	function getJahrFromGet() {
		if(isset($_GET['jahr'])) {
			$jahr = $_GET['jahr'];
		} else {
			$jahr = date("Y");
		}
	
		return $jahr;
	}
	
	/**
	 * Zeigt eine Meldung an, wenn das gewählte Jahr in der Zukunft liegt.
	 */
	function showFuturePastJahr() {
		if(isset($_GET['jahr'])) {
			$jahr = $_GET['jahr'];
			$currentJahr = date("Y");
			
			if($this->checkIfJahrIsInFuture($currentJahr, $jahr) == true) {
				echo "<p class='info'>Das gewählte Jahr liegt in der Zukunft.";
			}
			
			if($this->checkIfJahrIsInPast($currentJahr, $jahr) == true) {
				echo "<p class='info'>Das Jahr liegt in der Vergangenheit.";
			}
		}
	}
	
	/**
	 * Zeigt eine Meldung an, wenn der gewählte Monat in der Zukunft liegt.
	 */
	function showFuturePastMonat() {
		if(isset($_GET['monat'])) {
			$monat = $_GET['monat'];
			$currentMonat = date("m");

			$jahr = $_GET['jahr'];
			$currentJahr = date("Y");
				
			if($this->checkIfMonatIsInFuture($currentMonat, $monat) == true) {
				# echo "<p class='info'>Der Monat liegt in der Zukunft";
			}
				
			if($this->checkIfMonatIsInPast($currentMonat, $monat, $currentJahr, $jahr) == true) {
				echo "<p class='info'>Der Monat liegt in der Vergangenheit.";
			}
		}
	}
	
	/**
	 * Prüft, ob Jahr in der Zukunft ist.
	 * @param unknown $currentJahr
	 * @param unknown $zuPruefendesJahr
	 * @return boolean
	 */
	function checkIfJahrIsInFuture($currentJahr, $zuPruefendesJahr) {
		if($zuPruefendesJahr > $currentJahr) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Prüft, ob Jahr in der Vergangenheit liegt.
	 * @param unknown $currentJahr
	 * @param unknown $zuPruefendesJahr
	 * @return boolean
	 */
	function checkIfJahrIsInPast($currentJahr, $zuPruefendesJahr) {
		if($zuPruefendesJahr < $currentJahr) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Prüft, ob Monat in der Zukunft ist.
	 * @param unknown $currentJahr
	 * @param unknown $zuPruefendesJahr
	 * @return boolean
	 */
	function checkIfMonatIsInFuture($currentMonat, $zuPruefenderMonat) {
		if($zuPruefenderMonat > $currentMonat) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Prüft, ob Monat in der Vergangenheit liegt.
	 * @param unknown $currentJahr
	 * @param unknown $zuPruefendesJahr
	 * @return boolean
	 */
	function checkIfMonatIsInPast($currentMonat, $zuPruefenderMonat, $currentJahr, $pruefendesJahr) {
		if($this->checkIfJahrIsInFuture($currentJahr, $pruefendesJahr) == true) {
			return false;
		} else {
			if($zuPruefenderMonat < $currentMonat) {
				return true;
			} else {
				return false;
			}
		}
	}
	
	private $reCalc;
	
	/**
	 * Berechnet die Monatsabschlüsse neu, wenn sich z. B. in der Vergangenheit etwas geändert hat.
	 * @param unknown $besitzer
	 * @param unknown $konto
	 */
	function reCalcMonatsabschluesse($besitzer, $konto, $jahr) {
		
	}
	
	/**
	 * Berechnet die Jahresabschlüsse neu, wenn sich in der Vergangenheit etwas geändert hat.
	 * @param unknown $besitzer
	 * @param unknown $konto
	 */
	function reCalcJahresabschluesse($besitzer, $konto) {
		
	}
	
	private $errors;
	
	function showErrors() {
		# Summe aller Umsätze
		$kontostand = $this->getObjektInfo("SELECT sum(umsatzWert) AS summe FROM finanzen_umsaetze");
	
		# Buchungsnummer Check:
	
		# Wenn eine Buchung korrupt ist:
		if($kontostand[0]->summe > 0 OR $kontostand[0]->summe < 0) {
	
			echo "<div class='newChar'>";
			echo "Achtung, es wurde ein Fehler in mindestens einer Buchung entdeckt.
				Die Werte innerhalb einer Buchungsnummer sind unterschiedlich,
				dies kann verschiedene Gründe haben. Der Administrator wurde über
				dieses Problem informiert, doch falls dir die Buchungsnummer bekannt
				ist, kannst du diesen Fehler auch selbst über einen EDIT korrigieren.";
	
			$selectProblem = "SELECT max(buchungsnr) as max FROM finanzen_umsaetze";
			$max = $this->getObjektInfo($selectProblem);
	
			$max = $max[0]->max;
	
			$i = 0;
			for ($i = 0 ; $i <= $max ; $i++) {
				$select = "SELECT * FROM finanzen_umsaetze WHERE buchungsnr = $i ";
	
				# ANZAHL ÜBERPRÜFEN:
				$selectAnzahl = "SELECT * FROM finanzen_umsaetze WHERE buchungsnr = $i";
				$anzahl = $this->getAmount($selectAnzahl);
	
				if($anzahl != 2 AND $anzahl != 0) {
					echo "<p class='meldung'>Es wurde eine Unvollständige Buchung entdeckt, diese wird jetzt automatisch gelöscht...</p>";
					$delete = "DELETE FROM finanzen_umsaetze
					WHERE buchungsnr = '$i' LIMIT 1";
					if($this->sql_insert_update_delete($delete) == true) {
						$this->logEintrag(true, "Buchung $i wurde wegen Unvollständigkeit gelöscht.", "Error");
						echo "<p class='erfolg'>Fehlerhafte Buchung gelöscht</p>";
					}
				}
	
				$buchung = $this->getObjektInfo($select);
				$j = 0;
				for($j = 0; $j < 2 ; $j++) {
					# $buchung[$j]->umsatzName . " " . $buchung[$j]->umsatzWert . "<br>";
					if(isset($buchung[$j]->umsatzWert) AND isset($buchung[$j+1]->umsatzWert)) {
						if($buchung[$j]->umsatzWert * (-1) != $buchung[$j+1]->umsatzWert) {
							echo "<p class='meldung'>Es gibt ein Problem bei  <strong>Buchungs-Nr. $i</strong>: Werte: "
							. $buchung[$j]->umsatzWert
							. " und "
									. $buchung[$j+1]->umsatzWert
									. " <a href='?surpress&id=" . $buchung[$j]->konto . "&UmsatzID=" . $buchung[$j]->id . "'>klicke hier um einen EDIT vorzunehmen</a></p>";
								
						}
					}
				}
			}
			echo "Wenn du fertig bist, klicke hier: <a href='?' class='buttonlink'>OK</a>";
			echo "</div>";
			if(!isset($_GET['surpress'])) {
				exit;
			}
		}
	}
	
	private $umsaetze;
	
	/**
	 * Ermöglicht das modifizieren eines Umsatzes.
	 */
	function alterUmsatz() {
		if($this->userHasRight("18", 0) == true) {
			if(isset($_GET['edit'])) {
				
				$kontoLink = $this->getKontoIDFromGet();
				$monat = $this->getMonatFromGet();
				$jahr = $this->getJahrFromGet();
	
				$id = $_GET['edit'];
				$besitzer = $this->getUserID($_SESSION['username']);
				$umsatzInfo = $this->getObjektInfo("SELECT * FROM finanzen_umsaetze WHERE id = '$id' and besitzer = '$besitzer'");
	
				echo "<div id='draggable' class='alterUmsatz'>";
				echo "<a href='?konto=$kontoLink&monat=$monat&jahr=$jahr' class='neuerSchliessKnopf'>X</a>";
				echo "<form method=post>";
				echo "<p class=''>Umsatz Nr. " . $umsatzInfo[0]->id . "</p>";
				echo "<input type=text name=umsatzName value='" . $umsatzInfo[0]->umsatzName . "' /><br>";
				$kontoID = $umsatzInfo[0]->gegenkonto;
				$konto2ID = $umsatzInfo[0]->konto . "<br>";
				$gegenkonto = $this->getObjektInfo("SELECT * FROM finanzen_konten WHERE id = '$kontoID'");
				$konto = $this->getObjektInfo("SELECT * FROM finanzen_konten WHERE id = '$konto2ID'");
				echo "Buchung auf: " . $konto[0]->konto . " ";
				echo " - " . $gegenkonto[0]->konto . "<br>";
				echo "<input type=text name=umsatzWert value='" . $umsatzInfo[0]->umsatzWert . "' /><br>";
				echo "<input type=date name=umsatzDatum value='" . $umsatzInfo[0]->datum . "'  /><br>";
				echo "<input type=submit name=alterUmsatz value=Speichern />";
				echo "<input type=submit name=loeschUmsatz value=Löschen />";
				echo "</form>";
				echo "</div>";
	
				if(isset($_POST['alterUmsatz'])) {
					$text = $_POST['umsatzName'];
					$wert = str_replace(',', '.', $_POST['umsatzWert']);
					$datum = $_POST['umsatzDatum'];
					$besitzer = $this->getUserID($_SESSION['username']);
					$id = $_GET['edit'];
	
					# Buchungsnummer herausfinden;
					$objektBuchungsNr = $this->getObjektInfo("SELECT id, buchungsnr FROM finanzen_umsaetze WHERE id = '$id'");
					$buchungsnr = $objektBuchungsNr[0]->buchungsnr;
	
					# Werte errechnen:
	
					if($wert > 0) {
						$minusWert = $wert * (-1);
						$plusWert = $wert;
					} else {
						$minusWert = $wert;
						$plusWert = $wert * (-1);
					}
	
					if($minusWert > 0 OR $plusWert < 0) {
						exit;
					}
	
	
					# ID mit Minuswert herausfinden:
					$minusObjekt = $this->getObjektInfo("SELECT * FROM finanzen_umsaetze WHERE buchungsnr = '$buchungsnr' AND umsatzWert < 0 LIMIT 1");
					$minusID = $minusObjekt[0]->id;
	
					# ID mit Minuswert herausfinden:
					$plusObjekt = $this->getObjektInfo("SELECT id, buchungsnr, umsatzWert FROM finanzen_umsaetze WHERE buchungsnr = '$buchungsnr' AND umsatzWert > 0 LIMIT 1");
					$plusID = $plusObjekt[0]->id;
	
					if($text != "" AND $wert != "" AND $besitzer != "" AND $id != "" AND $buchungsnr != "") {
						$plusQuery = "UPDATE finanzen_umsaetze set umsatzName='$text',umsatzWert='$plusWert' ,datum='$datum' WHERE besitzer='$besitzer' and id = '$plusID'";
	
						$minusQuery = "UPDATE finanzen_umsaetze set umsatzName='$text',umsatzWert='$minusWert' ,datum='$datum' WHERE besitzer='$besitzer' and id = '$minusID'";
						if($this->userHasRight("18", 0) == true) {
							if($this->sql_insert_update_delete($plusQuery) == true
									AND $this->sql_insert_update_delete($minusQuery) == true) {
										echo "<p class='erfolg'>Umsatz gespeichert</p>";
									} else {
										echo "<p class='erfolg'>Fehler</p>";
									}
						} else {
							echo "<p class='meldung'>Keine Berechtigung</p>";
						}
					}
				}
				if (isset($_POST['loeschUmsatz'])) {
					$text = $_POST['umsatzName'];
					$wert = $_POST['umsatzWert'];
					$besitzer = $this->getUserID($_SESSION['username']);
					$datum = $_POST['umsatzDatum'];
					$id = $_GET['edit'];
	
					#Buchungsnummer herausfinden;
					$objektBuchungsNr = $this->getObjektInfo("SELECT id, buchungsnr FROM finanzen_umsaetze WHERE id = '$id'");
					$buchungsnr = $objektBuchungsNr[0]->buchungsnr;
	
					if(!isset($buchungsnr) OR $buchungsnr == "") {
						exit;
					}
					if($this->userHasRight("18", 0) == true) {
						$delete = "DELETE FROM finanzen_umsaetze
						WHERE besitzer = '$besitzer' AND buchungsnr = '$buchungsnr' LIMIT 2";
						if($this->sql_insert_update_delete($delete) == true) {
							echo "<p class='erfolg'>Umsatz gelöscht</p>";
						} else {
							echo "<p class='erfolg'>Fehler</p>";
						}
					} else {
						echo "<p class='meldung'>Keine Berechtigung</p>";
					}
	
				}
			}
		}
	}
	
	/**
	 * Neue Überweisung. Ermöglicht das Überweisen von Geld von einem auf ein anderes Konto.
	 */
	function showCreateNewUeberweisung() {
		if(isset($_GET['newUeberweisung'])) {
			if($this->userHasRight("18", 0) == true) {
				
				$kontoID = $this->getKontoIDFromGet();
				$monat = $this->getMonatFromGet();				
				$jahr = $this->getJahrFromGet();
					
				echo "<a href='?konto=$kontoID&monat=$monat&jahr=$jahr' class='highlightedLink'>Zurück</a>";
	
				echo "<h2>Eine Buchung durchführen</h2>";
				echo "<div><form method=post>";
				echo "<table class='kontoTable'>";
				echo "<tbody><td>Beschreibung</td><td><input type=text value='' placeholder='Text' name='textUeberweisung'/></td></tbody>";
					
				$besitzer = $this->getUserID($_SESSION['username']);
				$select = "SELECT * FROM finanzen_konten WHERE besitzer = '$besitzer'";
				$absenderKonten = $this->getObjektInfo($select);
					
				echo "<tbody><td>Gutschrift hier</td><td><select name='zielKonto'>";
				$i = 0;
				for ($i = 0 ; $i < sizeof($absenderKonten) ; $i++) {
					echo "<option value='". $absenderKonten[$i]->id . "'>" . $absenderKonten[$i]->konto . "</option>";
				}
				echo "</select></td></tbody>";
					
				echo "<tbody><td>Absender: </td><td><select name='absenderKonto'>";
				$i = 0;
				for ($i = 0 ; $i < sizeof($absenderKonten) ; $i++) {
					echo "<option value='". $absenderKonten[$i]->id . "'>" . $absenderKonten[$i]->konto . "</option>";
				}
				echo "</select></td></tbody>";
				$timestamp = time(); $date = date("Y-m-d", $timestamp);
				echo "<tbody><td>Betrag</td><td><input type=text value='' placeholder='Betrag' name='valueUeberweisung'/></td></tbody>";
				echo "<tbody><td>Datum</td><td><input type=date value='$date' placeholder='Datum' name='dateUeberweisung'/></td></tbody>";
				echo "<tbody><td colspan='2'><input type=submit name=sendnewUeberweisung value='Absenden' /></td></tbody>";
				echo "<tbody><td colspan='2'>";
					
				echo "<input type=checkbox name=weitere value='1' class='checkbox' />
						<label for='weitere'>Diese Überweisung für folgende weitere Tage durchführen</label>
						";
				echo "</td></tbody>";
				$j = 0;
	
				for ($j = 0 ; $j < 12 ; $j++) {
					echo "<tbody><td colspan='2'><input type=date name=dates[$j] value='' placeholder='weiteres Datum' /></td></tbody>";
				}
	
				echo "</form></div>";
	
				if(isset($_POST['sendnewUeberweisung'])
						AND isset($_POST['valueUeberweisung'])
						AND isset($_POST['textUeberweisung'])
						AND isset($_POST['dateUeberweisung'])
						AND isset($_POST['zielKonto'])
						AND isset($_POST['absenderKonto'])
				) {
					$von = $_POST['absenderKonto'];
					$datum = $_POST['dateUeberweisung'];
					$nach = $_POST['zielKonto'];
					$betrag =str_replace(',', '.', $_POST['valueUeberweisung']);
	
					$betragMinus = $betrag * (-1);
	
					# Wenn Absender und Ziel gleich ist:
					if($von == $nach) {
						echo "<p class='meldung'>Absende und Zielkonto ist gleich. Buchung abgebrochen.</p>";
						exit;
					}
	
					# Nächste Buchungsnummer herausfinden:
					$nextBuchungsnummer = $this->getObjektInfo("SELECT max(buchungsnr) as max FROM finanzen_umsaetze");
					$buchungsnummer = $nextBuchungsnummer[0]->max;
					if(!isset($buchungsnummer)) {
						$buchungsnummer = 0;
					}
					$buchungsnummer = $buchungsnummer + 1;
	
					$text = $_POST['textUeberweisung'];
					$besitzer = $this->getUserID($_SESSION['username']);
	
					if($von != "" AND $nach != "" AND isset($besitzer) AND $betrag != "" AND $betrag > 0 AND $datum != "" AND $text != "") {
						$query = "INSERT INTO finanzen_umsaetze (buchungsnr, besitzer, konto, gegenkonto, umsatzName, umsatzWert, datum)
						VALUES ('$buchungsnummer','$besitzer','$von','$nach','$text','$betragMinus','$datum')";
	
						$query2 = "INSERT INTO finanzen_umsaetze (buchungsnr, besitzer, konto, gegenkonto, umsatzName, umsatzWert, datum)
						VALUES ('$buchungsnummer','$besitzer','$nach','$von','$text','$betrag','$datum')";
	
						if ($this->sql_insert_update_delete($query) == true AND $this->sql_insert_update_delete($query2) == true) {
							echo "<p class='erfolg'>Überweisung durchgeführt</p>";
						}
	
						# Weitere Daten einfügen:
						if(isset($_POST['weitere'])) {
	
							$dates = $_POST['dates'];
							$j = 0;
							for ($j = 0; $j < 12 ; $j++) {
									
								# Nur gefüllte Inputfelder verwenden.
								if($dates[$j] != "") {
	
									# Nächste Buchungsnummer herausfinden:
									$nextBuchungsnummer = $this->getObjektInfo("SELECT max(buchungsnr) as max FROM finanzen_umsaetze");
									$buchungsnummer = $nextBuchungsnummer[0]->max;
									$buchungsnummer = $buchungsnummer + 1;
	
									$query = "INSERT INTO finanzen_umsaetze (buchungsnr, besitzer, konto, gegenkonto, umsatzName, umsatzWert, datum)
									VALUES ('$buchungsnummer','$besitzer','$von','$nach','$text','$betragMinus','$dates[$j]')";
	
									$query2 = "INSERT INTO finanzen_umsaetze (buchungsnr, besitzer, konto, gegenkonto, umsatzName, umsatzWert, datum)
									VALUES ('$buchungsnummer','$besitzer','$nach','$von','$text','$betrag','$dates[$j]')";
	
									if ($this->sql_insert_update_delete($query) == true AND $this->sql_insert_update_delete($query2) == true) {
										echo "<p class='erfolg'>Überweisung durchgeführt</p>";
									}
								}
							}
						}
					}
				}
			} else {
				echo "<p class='meldung'>Keine Berechtigung</p>";
			}
		}
	}
	
	/**
	 * Gibt die Umsaetze des angegeben Monats aus dem Konto zurück.
	 * @param unknown $besitzer
	 * @param unknown $monat
	 * @param unknown $jahr
	 * @param unknown $konto
	 * @return unknown|boolean
	 */
	function getUmsaetzeMonthFromKonto($besitzer, $monat, $jahr, $konto) {
		$umsaetze = "SELECT *
		, day(datum) as tag
		, year(datum) as jahr
		, month(datum) as monat 
		FROM finanzen_umsaetze
		WHERE besitzer = $besitzer
		AND konto = $konto
		HAVING jahr = $jahr
		AND monat = $monat
		ORDER BY tag, id";
		
		$ergebnis = $this->getObjektInfo($umsaetze);
		
		if(isset($ergebnis[0]->id)) {
			return $ergebnis;
		} else {
			return false;
		}
	}
	
	private $konto;
	
	/**
	 * Zeigt eine Übersicht aller Konten des besitzers in einer Auflistung an.
	 * @param unknown $besitzer
	 */
	function showKontoUebersicht($besitzer) {
		$konten = $this->getAllKonten($besitzer);
		
		# Wenn es Konten gibt:
		if(isset($konten[0]->id)) {
			echo "<table class='flatnetTable'>";
			echo "<thead>" . "<td>ID</td>" . "<td>Name</td>"."<td>Optionen</td>"."</thead>";
			for($i = 0; $i < sizeof($konten); $i++) {
				echo "<tbody><td>" .$konten[$i]->id. "</td><td>" .$konten[$i]->konto. "</td><td><a href='?editKonto=".$konten[$i]->id."'>Edit</a>
						<a href='?deleteKonto=".$konten[$i]->id."'>Löschen</a></td></tbody>";
			}
			echo "</table>";
		}
	}
	
	/**
	 * Gibt alle Konten des Besitzers zurück.
	 * @param unknown $besitzer
	 * @return object|boolean
	 */
	function getAllKonten($besitzer) {
		$konten = $this->getObjektInfo("SELECT * FROM finanzen_konten WHERE besitzer = '$besitzer'");
		
		if(isset($konten)) {
			return $konten;
		} else {
			return false;
		}
	}
	
	/**
	 * Ermöglicht die Erstellung eines neuen Kontos.
	 */
	function showCreateNewKonto($besitzer) {
		echo "<a href='?neuesKonto' class='buttonlink'>Neues Konto</a>";
		if(isset($_GET['neuesKonto'])) {
			echo "<div class='newChar'><form method=post><input type=text name=newKonto value='' placeholder='Kontoname' />";
			echo "<input type=submit name=insertNewKonto value=Speichern />";
			echo "</form></div>";
		
			if(isset($_POST['insertNewKonto']) AND $this->userHasRight("18", 0) == true) {
				$konto = $_POST['newKonto'];
				if($konto != "") {
					if($this->createNewKonto($besitzer, $konto) == true) {
						echo "<p class='erfolg'>Konto wurde erstellt.";
					} else {
						echo "<p class='meldung'>Es gab einen Fehler beim erstellen des Kontos</p>";
					}
				}
			}
		}
	}
	
	/**
	 * Erstellt ein Konto für den Besitzer.
	 * @param unknown $besitzer
	 * @param unknown $kontoname
	 * @return boolean
	 */
	function createNewKonto($besitzer, $kontoname) {
		$query = "INSERT INTO finanzen_konten (besitzer,konto) VALUES ('$besitzer','$kontoname') ";
		
		if ($this->sql_insert_update_delete($query) == true) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Ermöglicht das editieren von Konten
	 * @param unknown $besitzer
	 */
	function showEditKonto($besitzer) {
		if(isset($_GET['editKonto'])) {
			echo "<h2>Konto bearbeiten</h2>";
			
			echo "<div class='mainbody'>";
			echo "</div>";
		}
	}
	
	/**
	 * Ermöglicht das löschen von Konten. 
	 * @param unknown $besitzer
	 */
	function showDeleteKonto($besitzer) {
		if(isset($_GET['deleteKonto'])) {
				
		}
	}
	
	private $abschluesse;
	
	/**
	 * Summiert die Umsätze der vergangenen Monate
	 * @param unknown $besitzer
	 * @param unknown $jetzigerMonat
	 * @param unknown $jahr
	 * @param unknown $konto
	 * @return number
	 */
	function getMonatsabschluesseBISJETZT($besitzer, $jetzigerMonat, $jahr, $konto) {
		$summierendeMonate = $jetzigerMonat - 1;
		$summe = 0;
		for ($i = 1 ; $i <= $summierendeMonate; $i++) {
			$summe = $summe + $this->getMonatsabschluss($besitzer, $konto, $i, $jahr);
		}
		
		return $summe;
		
	}
	
	/**
	 * Gibt die Summe der Jahresabschlüsse wieder.
	 * @param unknown $besitzer
	 * @param unknown $jahr
	 * @param unknown $konto
	 * @return unknown
	 */
	function getJahresabschluesseBISJETZT($besitzer, $konto, $jetzigesJahr) {
		$query = "SELECT sum(wert) as summe 
		FROM finanzen_jahresabschluss 
		WHERE besitzer = $besitzer 
		AND konto = $konto 
		AND jahr < $jetzigesJahr";	
		$summe = $this->getObjektInfo($query);
		
		if(isset($summe[0]->summe)) {
			$summeWert = $summe[0]->summe;
		} else {
			$summeWert = 0;
		}
		
		return $summeWert;
	}
	
	private $monat;
	
	/**
	 * Gibt den Monatsabschluss aus den angegebenen Daten wieder.
	 * @param unknown $besitzer
	 * @param unknown $konto
	 * @param unknown $monat
	 * @param unknown $jahr
	 */
	function getMonatsabschluss($besitzer, $konto, $monat, $jahr) {
		$query = "SELECT * 
		FROM finanzen_monatsabschluss 
		WHERE konto = '$konto' 
		AND besitzer = '$besitzer' 
		AND year = '$jahr' 
		AND monat = '$monat'";
		
		$monatsabschluss = $this->getObjektInfo($query);
		
		if(isset($monatsabschluss[0]->year)) {
			return $monatsabschluss[0]->wert;
		} else {
			return false;
		}
	}
	
	/**
	 * gibt den Saldo des Monats im aktuellen Jahr, dieses Besitzers, zu diesem Konto zurück.
	 * @param unknown $besitzer
	 * @param unknown $konto
	 * @param unknown $jahr
	 * @param unknown $monat
	 */
	function getSaldoFromMonat($besitzer, $konto, $monat, $jahr) {
		$query = "SELECT *
		FROM finanzen_umsaetze
		WHERE konto = $konto
		AND besitzer = $besitzer
		HAVING year(datum) = $jahr
		AND month(datum) = $monat;";
	
		$umsaetze = $this->getObjektInfo($query);
	
		# SUMME bilden
		$summe = 0;
		for($i = 0 ; $i < sizeof($umsaetze); $i++) {
			$summe = $summe + $umsaetze[$i]->umsatzWert;
		}
	
		return $summe;
	}
	
	/**
	 * Erstellt Monatsabschlüsse aus alten Einträgen.
	 */
	function erstelleMonatsabschluesseFromOldEintraegen() {
		$besitzer = $this->getUserID($_SESSION['username']);
		
		# jetzigen Monat ziehen:
		$currentMonat = date("m");
		$currentJahr = date("Y");
		$abzueglicheMonate = $currentMonat - 1;
		$anzahlMonate = 12;
		
		# Konten des Nutzers ziehen:
		$konten = $this->getAllKonten($besitzer);
		
		# Prüfen, ob der Benutzer konten hat.
		if(!isset($konten[0]->id)) {
			echo "<p class='info'>Du hast noch keine Konten, du brauchst mindestens zwei Konten. Klicke <a href='?'>hier</a> um eine Standardkonfiguration zu erstellen</p>";
			exit;
		}
		
		# Anzahl Monatsabschlüsse zählen
		$counter = 0;
		
		# Pro Konto durchlaufen
		for ($i = 0; $i < sizeof($konten); $i++) {
			# prüfen, ob der aktuelle Monatsabschluss existiert.
			
			# Pro vergangenen Monat durchlaufen:
			for($gepruefterMonat = 1; $gepruefterMonat < $currentMonat; $gepruefterMonat++) {
				if($this->getMonatsabschluss($besitzer, $konten[$i]->id, $gepruefterMonat, $currentJahr) == false) {
					
					$saldo = $this->getSaldoFromMonat($besitzer, $konten[$i]->id, $gepruefterMonat, $currentJahr);
					
					$konto = $konten[$i]->id;
					$query = "INSERT INTO finanzen_monatsabschluss (besitzer, monat, year, wert, konto) 
					VALUES 
					('$besitzer','$gepruefterMonat','$currentJahr','$saldo','$konto')";
					
					if($this->sql_insert_update_delete($query) == true) {
						$counter = $counter + 1;
					} else {
						echo "<p class='meldung'>Es gab einen Fehler beim erstellen des Monatsabschlusses $currentJahr, im Monat $gepruefterMonat.</p>";
					}
				
				}
			}
			
		}
		
		if($counter > 0) {
			echo "<p class='erfolg'>Es wurden $counter Monatsabschlüsse erstellt.</p>";
		}
	}
	
	private $jahr;
	
	/**
	 * Gibt zurück, ob ein Jahresabschluss vorhanden ist, wenn ja, dann wird der wert zurückgegeben.
	 * @param unknown $besitzer
	 * @param unknown $konto
	 * @param unknown $jahr
	 * @return boolean
	 */
	function getJahresabschluss($besitzer, $konto, $jahr) {
		$query = "SELECT * FROM finanzen_jahresabschluss WHERE konto = '$konto' AND besitzer = '$besitzer' AND jahr = '$jahr'";
		
		$jahresabschluss = $this->getObjektInfo($query);
		
		if(isset($jahresabschluss[0]->jahr)) {
			return $jahresabschluss[0]->wert;
		} else {
			return false;
		}
	}
	
	/**
	 * gibt den Saldo des Jahres, dieses Besitzers zu diesem Konto zurück.
	 * @param unknown $besitzer
	 * @param unknown $konto
	 * @param unknown $jahr
	 */
	function getSaldoFromYear($besitzer, $konto, $jahr) {
		$query = "SELECT *
		FROM finanzen_umsaetze
        WHERE konto = $konto
        AND besitzer = $besitzer
        HAVING year(datum) = $jahr;";
		
		$umsaetze = $this->getObjektInfo($query);
		
		# SUMME bilden
		$summe = 0;
		for($i = 0 ; $i < sizeof($umsaetze); $i++) {
			$summe = $summe + $umsaetze[$i]->umsatzWert;
		}
		
		return $summe;
	}
	
	/**
	 * Gibt das Erstellungsjahr des aktuellen Kontos zurück.
	 * @param unknown $besitzer
	 * @param unknown $kontonummer
	 * @return boolean
	 */
	function getErstellungsdatumKonto($besitzer, $kontonummer) {
		# frühesten Eintrag im Konto finden:
		
		$frueherEintrag = $this->getObjektInfo("SELECT min(year(datum)) as min FROM finanzen_umsaetze
				WHERE besitzer = '$besitzer' AND konto = '$kontonummer'");
		
		if(isset($frueherEintrag[0]->min)) {
			return $frueherEintrag[0]->min;
		} else {
			$frueherEintrag = date("Y");
			return $frueherEintrag;
		}
	}
	
	/**
	 * Checkt den Jahresabschluss.
	 * @param unknown $besitzer
	 * @param unknown $konto
	 */
	function checkJahresabschluss() {
		
		$besitzer = $this->getUserID($_SESSION['username']);
		
		# Konten des Nutzers ziehen:
		$kontenDesNutzers = $this->getAllKonten($besitzer);
		
		for ($i = 0 ; $i < sizeof($kontenDesNutzers); $i++) {
			
			if(isset($_GET['checkJahresabschluesse'])) {
				
				$konto = $kontenDesNutzers[$i]->id;
				
				$currentYear = date("Y");
				# Prüfen, ob Jahresabschluss KORREKT ist.
				$jahresabschlusswert = $this->getObjektInfo("SELECT sum(wert) as summe
				FROM finanzen_jahresabschluss
				WHERE besitzer = $besitzer
				AND konto = $konto");
				
				$tatsaechlicheSumme = $this->getObjektInfo("SELECT sum(umsatzWert) as summe
						FROM finanzen_umsaetze
						WHERE besitzer = $besitzer
						AND konto = $konto
						AND year(datum) < $currentYear");
				
				if(isset($jahresabschlusswert[0]->summe)) {
					if($jahresabschlusswert[0]->summe != $tatsaechlicheSumme[0]->summe) {
						echo "<p class='meldung'>Fehler bei Konto $konto, die tatsächliche Summe ist " . $tatsaechlicheSumme[0]->summe . ", aber der eingetragene ist " . $jahresabschlusswert[0]->summe . "
					Lösche jetzt die Jahresabschlüsse.";
					
						if($this->sql_insert_update_delete("DELETE FROM finanzen_jahresabschluss WHERE besitzer = $besitzer AND konto = $konto") == true) {
							echo "<br><br>Fehlerhaften Abschluss gelöscht!.</p>";
						}
					} else {
						# echo "<p class='erfolg'>Für Konto $konto gibt es keine Fehler.</p>";
					}
				} else {
					# echo "<p class='erfolg'>Für Konto $konto gibt es scheinbar keinen Abschluss.</p>";
				}
				
				
			}
		}
	}
	
	/**
	 * Erstellt Jahresabschlüsse aus den alten Einträgen.
	 */
	function erstelleJahresabschluesseFromOldEintraegen() {
		$besitzer = $this->getUserID($_SESSION['username']);
		
		# Konten des Nutzers ziehen:
		$kontenDesNutzers = $this->getAllKonten($besitzer);
		
		# Prüfen, ob der Benutzer konten hat.
		if(!isset($kontenDesNutzers[0]->id)) {
			echo "<p class='info'>Du hast noch keine Konten, du brauchst mindestens zwei Konten. Klicke <a href='?createStandardConfig'>hier</a> um eine Standardkonfiguration zu erstellen</p>";
			
			if(isset($_GET['createStandardConfig'])) {
				if($this->objectExists("SELECT * FROM finanzen_konten WHERE besitzer = $besitzer AND konto = 'Hauptkonto'") == false) {
					if($this->sql_insert_update_delete("INSERT INTO finanzen_konten (konto, besitzer) VALUES ('Hauptkonto','$besitzer')") == true) {
						echo "<p class='erfolg'>Ein Hauptkonto wurde erstellt.</p>";
					}
				}
				
				if($this->objectExists("SELECT * FROM finanzen_konten WHERE besitzer = $besitzer AND konto = 'Hauptkonto'") == false) {
					if($this->sql_insert_update_delete("INSERT INTO finanzen_konten (konto, besitzer) VALUES ('Ausgaben','$besitzer')") == true){
						echo "<p class='erfolg'>Ein Ausgabenkonto wurde erstellt.</p>";
					}
				}
			}
			
			exit;
		}
		
		# Jetziges Jahr:
		$currentYear = date("Y");
		
		# Pro Konto durchführen:
		for($i = 0; $i < sizeof($kontenDesNutzers); $i++) {
			
			echo "<table class='flatnetTable'>";
			# Ersten Eintrag im Konto suchen:
			$erstellungsDatumKonto = $this->getErstellungsdatumKonto($besitzer, $kontenDesNutzers[$i]->id);
			
			if($erstellungsDatumKonto < $currentYear) {
				$differnz = $currentYear - $erstellungsDatumKonto;
				$geprueftesJahr = $erstellungsDatumKonto;
				
				# Für Anzahl der Jahre:
				for ($j = 0; $j < $differnz; $j++) {
					if($this->getJahresabschluss($besitzer, $kontenDesNutzers[$i]->id, $geprueftesJahr) == false) {
						echo "<tbody><td>Es gibt keinen Jahresabschluss für Konto <strong>" . $kontenDesNutzers[$i]->konto . "</strong>, dieser wird jetzt erstellt:</td></tbody>";
					
						echo "<tbody><td>";
						# Saldo dieses Jahres bekommen:
						$saldo = $this->getSaldoFromYear($besitzer, $kontenDesNutzers[$i]->id, $geprueftesJahr);
						echo "Der Saldo ist: " . $saldo;
						$konto = $kontenDesNutzers[$i]->id;
						$query = "INSERT INTO finanzen_jahresabschluss (besitzer, jahr, wert, konto) VALUES ('$besitzer','$geprueftesJahr','$saldo','$konto')";
						
						if($this->sql_insert_update_delete($query) == true) {
							echo "<p class='erfolg'>Der Jahresabschluss wurde erstellt.</p>";
							# alte Monatsabschlüsse löschen: 
		
							if($this->sql_insert_update_delete("DELETE FROM finanzen_monatsabschluss WHERE besitzer = $besitzer AND year < $currentYear") == true) {
								echo "<p class='info'>Alte Monatsabschlüsse gelöscht</p>";
							} else {
								echo "<p class='meldung'>Alte Monatsabschlüsse konnten nicht gelöscht werden.</p>";
							}
						} else {
							echo "<p class='meldung'>Es gab einen Fehler.</p>";
						}
					
						echo "</td></tbody>";
						
						$geprueftesJahr = $geprueftesJahr + 1;
					
					}
				}
			}
			
			echo "</table>";
			
		}
		
				
	}
		
}

?>