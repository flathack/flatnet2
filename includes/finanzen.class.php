<?php
include 'objekt/functions.class.php';

class finanzenNEW extends functions {
	
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
		# Besitzer:
		$besitzer = $this->getUserID($_SESSION['username']);
		
		# Buttons
		echo "<a href='?neuesKonto' class='rightBlueLink'>Neues Konto</a>";
		echo "<a href='?Salden' class='rightBlueLink'>Salden</a>";
				
		# Saldenübersicht:
		$this->saldenUebersicht($besitzer);
		
		# Kontomanipulationen
		$this->showCreateNewUeberweisung();
		$this->UmbuchungUmsatz($besitzer);
		$this->showCreateNewKonto($besitzer);
		$this->showDeleteKonto($besitzer);
		$this->showEditKonto($besitzer);
		
		# Kontoübersicht:
		$this->showKontoUebersicht($besitzer);
	}
	
	private $suche;
	
	function FinanzSuche($suchWort) {
		if($this->userHasRight("23", 0) == true) {
			if(isset($suchWort) AND $suchWort != "") {
				
				$besitzer = $this->getUserID($_SESSION['username']);
				
				$ursprünglicheSuche = $suchWort;
				# Suche mit Wildcards bestücken
				$suchWort = "%" . $suchWort . "%";
				
				# Spalten der Tabelle selektieren:
				$colums  = "SHOW COLUMNS FROM finanzen_umsaetze";
				
				$rowSpalten = $this->getObjektInfo($colums);
				
				# SuchQuery bauen:
				# Start String:
				$querySuche = "SELECT *
				, month(datum) as monat
				, year(datum) as jahr 
				FROM finanzen_umsaetze 
				WHERE (besitzer=$besitzer AND id LIKE '$suchWort' ";
				
				# OR + Spaltenname LIKE Suchwort
				for ($i = 0 ; $i < sizeof($rowSpalten) ; $i++) {
					$querySuche .= " OR besitzer = $besitzer AND " . $rowSpalten[$i]->Field . " LIKE '$suchWort'";
				}
				# Klammer am Ende schließen-
				$querySuche .= ")";
				
				# Query für die Suche
				$suchfeld = $this->getObjektInfo($querySuche);
				
				echo "<div id='draggable' class='summe'>";
					echo "Die Suche nach <strong>($suchWort)</strong> ergab folgendes Ergebnis:";
					echo "<div class='mainbody'>";
					echo "<table class='flatnetTable'>";
					echo "<thead><td>Konto</td><td>Umsatzname</td><td>Wert</td><td>Datum</td></thead>";
					for ($i = 0; $i < sizeof($suchfeld); $i++) {
						
						$kontoname = $this->getObjektInfo("SELECT * FROM finanzen_konten WHERE id=".$suchfeld[$i]->konto." LIMIT 1");
						$kontoname = $kontoname[0]->konto;
						
						echo "<tr><td>".$kontoname."</td><td> " . "<a href='?konto=".$suchfeld[$i]->konto."&jahr=".$suchfeld[$i]->jahr."&monat=".$suchfeld[$i]->monat."&selected=".$suchfeld[$i]->buchungsnr."'>" . substr($suchfeld[$i]->umsatzName, 0, 20) . "</a></td>" ."<td>".$suchfeld[$i]->umsatzWert."</td>"."<td>".$suchfeld[$i]->datum."</td>". "</td></tr>";
						
					}
					echo "</table>";
					echo "</div>";
					
				echo "</div>";
			}
		}
		
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
					
					# Wenn der Umsatz ausgewählt wurde, dann wird er rot markiert.
					if(isset($_GET['selected'])) { 
						if($_GET['selected'] == $umsaetze[$i]->buchungsnr) {
							$selected = "id='rot'";
						} else {
							$selected = "";
						}
					} else { 
						$selected = ""; 
					}
					
					
					echo "<tbody $selected>";
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
				
				# Wenn kein Umsatz gefunden wird, den nächsten anzeigen: 
				
				# CURDATE zusammenbauen.
				if($monat < 10) { $monat = "0" . $monat; } $curdate = $currentYear . "-" . $monat . "-01";
				
				$naechsterUmsatz = $this->getObjektInfo("SELECT *, month(datum) as monat, year(datum) as jahr FROM finanzen_umsaetze WHERE besitzer=$besitzer AND konto=$kontoID AND datum > '$curdate' ORDER BY datum ASC LIMIT 1");
				
				echo "<tbody id='plus'><td colspan=7>$curdate In diesem Monat gibt es keine Umsätze, der nächste Umsatz lautet: </td></tbody>";
				
				for ($k = 0 ; $k < sizeof($naechsterUmsatz) ; $k++) {
					$naechsterMonat = $naechsterUmsatz[$k]->monat;
					echo "<tbody id=''>" ."<td>".$naechsterUmsatz[$k]->datum."</td>"."<td colspan=6>".$naechsterUmsatz[$k]->umsatzName.",  <a href='?konto=$kontoID&monat=$naechsterMonat&jahr=".$naechsterUmsatz[$k]->jahr."'>springe zu Monat</a></td>". "</tbody>";
				}
				
			}
			$differenz = $zwischensumme - $startsaldo;
			echo "<tfoot><td colspan=5 id='rightAlign'>Endsaldo: </td><td id='rightAlign'>$zwischensumme</td><td></td></tfoot>";
	
			echo "</table>";
			echo "<p class='info'>Kontostandveränderung: $differenz €</p>";
			
			# Zeigt das Diagramm an
			if(isset($zahlen)) {
				$this->showDiagramme($zahlen, "970", "200");
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
		echo "<li "; if($jahr == 2019) { echo " id='selected' "; } echo "><a href='?konto=$konto&monat=$monat&jahr=2019'>2019</a></li>";
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
				
				if($konten[$i]->aktiv == 1) {
					echo "<li ";
					if($konto == $konten[$i]->id) { echo " id='selected' "; }
					echo "><a href='?konto=".$konten[$i]->id."&monat=$monat&jahr=$jahr'>" .$konten[$i]->konto. "</a></li>";
				}
			
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
			
			echo "<div class='innerBody'>";
			
		#1	echo "<table class='flatnetTable'>";
		#1	echo "<thead>" . "<td>ID</td>" . "<td>Name</td>"."<td>Optionen</td>"."</thead>";
			for($i = 0; $i < sizeof($konten); $i++) {
				if($konten[$i]->aktiv == 1) {
					$select2 = "SELECT sum(umsatzWert) as summe FROM finanzen_umsaetze WHERE konto=".$konten[$i]->id . " AND datum <= CURDATE()";
					$umsaetze = $this->getObjektInfo($select2);
					echo "<div class='adresseintrag'>";
						echo "<p><a href='?editKonto=".$konten[$i]->id."'>" .$konten[$i]->id." | ". $konten[$i]->konto . "</a> | ";
						echo $umsaetze[0]->summe. " €</p>";
					echo "</div>";
				#1	echo "<tbody><td>" .$konten[$i]->id. "</td><td>" .$konten[$i]->konto. "</td><td><a class='rightBlueLink' href='?editKonto=".$konten[$i]->id."'>Edit</a></td></tbody>";
				}
			}
			
			echo "</div>";
			$i = 0;
		#1	echo "<thead><td colspan = 3>Deaktiverte Konten</td></thead>";
		echo "<div class='rightBody'>";
			for ($i = 0 ; $i < sizeof($konten) ; $i++) {
				$select2 = "SELECT sum(umsatzWert) as summe FROM finanzen_umsaetze WHERE konto=".$konten[$i]->id . " AND datum <= CURDATE()";
				$umsaetze = $this->getObjektInfo($select2);
				if($konten[$i]->aktiv == 0) {
					echo "<div class='adresseintragGrey'>";
						echo "<p><a href='?editKonto=".$konten[$i]->id."'>".$konten[$i]->id." | ". $konten[$i]->konto . "</a> | ";
						echo $umsaetze[0]->summe. " €</p>";
					echo "</div>";
		#1			echo "<tbody><td>" .$konten[$i]->id. "</td><td>" .$konten[$i]->konto. "</td><td><a class='rightBlueLink' href='?editKonto=".$konten[$i]->id."'>Edit</a></td></tbody>";
				}
			}
			echo "</div>";
		#1	echo "</table>";
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
		$query = "INSERT INTO finanzen_konten (besitzer,konto,aktiv) VALUES ('$besitzer','$kontoname',1) ";
		
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
		
		if(isset($_POST['absenden']) AND isset($_GET['editKonto']) AND isset($_POST['kontoname']) AND isset($_POST['notizen'])) {
			if($_POST['kontoname'] != "") {
				$name = $_POST['kontoname'];
				$id = $_GET['editKonto'];
				$notizen = strip_tags( stripslashes($_POST['notizen']));
				if(isset($_POST['aktiv'])) {
					$aktiv = 1;
				} else {
					$aktiv = 0;
				}
				
				$update = "UPDATE finanzen_konten SET konto='$name', aktiv=$aktiv, notizen='$notizen' WHERE besitzer = $besitzer AND id = $id";
				
				if($this->sql_insert_update_delete($update)) {
					echo "<p class='erfolg'>Konto gespeichert.</p>";
				} else {
					echo "<p class='meldung'>Es gab einen Fehler beim speichern der Informationen.</p>";
				}
			}
		}
		
		if(isset($_GET['editKonto'])) {
			$id = $_GET['editKonto'];
			$select = "SELECT * FROM finanzen_konten WHERE besitzer = $besitzer AND id = $id";
			$kontoInfo = $this->getObjektInfo($select);
			
			if(!isset($kontoInfo[0]->id)) {
				echo "<p class='meldung'>Dieses Konto gibt es nicht, oder du hast keinen Zugriff darauf.</p>";
				exit;
			}
						
			echo "<div class='newCharWIDE'>";
			echo "<h2>Konto bearbeiten</h2>";
			echo "<a class='rightBlueLink' href='index.php?konto=".$kontoInfo[0]->id."'>Gehe zu diesem Konto</a>";
			
			echo "<form method=post>";
			echo "<table>";
			echo "<tr><td>Name: </td><td><input type=text value='" . $kontoInfo[0]->konto . "' name=kontoname /></td></tr>";
			if(isset($kontoInfo[0]->aktiv) AND $kontoInfo[0]->aktiv == 0) {
				$checked = "";
			} else {
				$checked = "checked";
			}
			echo "<tr><td>Aktiv: </td>" . "<td><input value=1 type=checkbox " .$checked. " name=aktiv />" . "</td></tr>";
			
			echo "<tr><td><input type=submit name=absenden value=Speichern /></td></tr>";
			$select2 = "SELECT *
			, year(datum) as jahr
			, month(datum) as monat
			, day(datum) as tag 
			FROM finanzen_umsaetze 
			WHERE besitzer = $besitzer 
			AND konto = $id 
			ORDER BY datum";
			$getKontoBuchungen = $this->getObjektInfo($select2);
			echo "</table>";
			
			echo "<div class='scrollContainer'>";
			echo "<p>Übersicht vorhandener Buchungen</p>";			
			echo "<table class='flatnetTable'>";
			for ($i = 0 ; $i < sizeof($getKontoBuchungen) ; $i++) {
				echo "<tbody>";
				$gegenkonto = $this->getObjektInfo("SELECT * FROM finanzen_konten WHERE id = '".$getKontoBuchungen[$i]->gegenkonto."'");
					echo "<td><a href='index.php?konto=".$getKontoBuchungen[$i]->konto."&jahr=".$getKontoBuchungen[$i]->jahr."&monat=".$getKontoBuchungen[$i]->monat."&selected=".$getKontoBuchungen[$i]->buchungsnr."'>" .$getKontoBuchungen[$i]->buchungsnr. "</a></td>
						<td>" . $getKontoBuchungen[$i]->umsatzName . "</td>
						<td>" .$getKontoBuchungen[$i]->umsatzWert. "</td>
						<td>AN <a href='?editKonto=".$gegenkonto[0]->id."'>" .$gegenkonto[0]->konto. "</a></td>
						<td>" .$getKontoBuchungen[$i]->datum. "</td>
						<td>" ."<a href='?umbuchungBuchNr=".$getKontoBuchungen[$i]->buchungsnr."&editKonto=$id'>Umbuchen</a>"."</td>";
				echo "</tbody>";
			}
			
			if(!isset($getKontoBuchungen[0]->buchungsnr)) {
				echo "<tbody><td>Es gibt keine Buchungen auf diesem Konto.<a href='?deleteKonto=".$kontoInfo[0]->id."' class='rightRedLink'>Konto löschen</a></td></tbody>";
			}
			
			echo "</table>";
			
			echo "</div>";
			echo "<textarea name=notizen>" . $kontoInfo[0]->notizen . "</textarea>";
			echo "</form>";
			echo "</div>";
		}
	}
	
	/**
	 * Ändert das Konto einer Buchungsnummer
	 * @param unknown $buchungsnr
	 */
	function umbuchungDurchfuehren($buchungsnr) {
		if(isset($_POST['absenden'])) {
			if(isset($_POST['gutschriftKonto']) AND isset($_POST['absenderKonto'])) {
				$absenderKonto = $_POST['absenderKonto'];
				$gutschriftKonto = $_POST['gutschriftKonto'];
				$getBuchungInfos = $this->getObjektInfo("SELECT * FROM finanzen_umsaetze WHERE buchungsnr = $buchungsnr");
				
				echo "Neues Absenderkonto $absenderKonto, neues Gutschriftkonto = $gutschriftKonto";
				
				for ($i = 0 ; $i < sizeof($getBuchungInfos) ; $i++) {
					# Änderung für den ABSENDER
					if($getBuchungInfos[$i]->umsatzWert < 0) {
						$update = "UPDATE finanzen_umsaetze SET konto=$absenderKonto, gegenkonto=$gutschriftKonto WHERE buchungsnr=$buchungsnr AND umsatzWert < 0";
						
						if($this->sql_insert_update_delete($update) == true) {
							echo "<p class='erfolg'>Umbuchung erfolgt</p>";
						} else {
							echo "<p class='meldung'>Es gab einen Fehler.</p>";
						}
					}
					
					# ÄNDERUNG für die GUTSCHRIFT
					if($getBuchungInfos[$i]->umsatzWert > 0) {
						
						
						$update = "UPDATE finanzen_umsaetze SET konto=$gutschriftKonto, gegenkonto=$absenderKonto WHERE buchungsnr=$buchungsnr AND umsatzWert > 0";
						
						if($this->sql_insert_update_delete($update) == true) {
							echo "<p class='erfolg'>Umbuchung erfolgt</p>";
						} else {
							echo "<p class='meldung'>Es gab einen Fehler.</p>";
						}
					}
				}
			}
		}
	}
	
	/**
	 * Zeigt eine Auswahl zum Ändern deiner Buchungsnummer bezügl. der betreffenden Konten.
	 * @param unknown $besitzer
	 */
	function UmbuchungUmsatz($besitzer) {
		if(isset($_GET['umbuchungBuchNr'])) {
			$buchungsnr = $_GET['umbuchungBuchNr'];
			
			# Prüfen ob Nummer existiert:
			$query = "SELECT * FROM finanzen_umsaetze WHERE buchungsnr = $buchungsnr AND besitzer = $besitzer";
			if($this->objectExists($query) == true) {
				$buchInfos = $this->getObjektInfo($query);
				
				echo "<div class='newChar'><form method=post>";
				
				$this->umbuchungDurchfuehren($buchungsnr);
				
				echo "<h2>Umsatzinformationen</h2>";
				for ($i = 0 ; $i < sizeof($buchInfos) ; $i++) {
					if($buchInfos[$i]->umsatzWert > 0) {
						echo "<table class='flatnetTable'>";
						echo "<thead><td colspan=10>GUTSCHRIFT</td></thead>";
						echo "<tbody><td>". $buchInfos[$i]->umsatzName . " ". $buchInfos[$i]->umsatzWert . "</td></tbody>";
						echo "<tbody>";
						$selectKonten = "SELECT * FROM finanzen_konten WHERE besitzer = $besitzer";
						$konten = $this->getObjektInfo($selectKonten);
						echo "<td>";
						echo "<select name=gutschriftKonto />";
							for ($j = 0 ; $j < sizeof($konten) ; $j++) {
								echo "<option value='".$konten[$j]->id."'";
								
								if($buchInfos[$i]->konto == $konten[$j]->id) {
									echo " selected ";
								}
								echo ">";
									echo $konten[$j]->konto;
								echo "</option>";
							}
						echo "</select>";
						echo "</td>";
						echo "</tbody>";
					}
					
					if($buchInfos[$i]->umsatzWert < 0) {
						echo "<table class='flatnetTable'>";
						echo "<thead><td colspan=10>ABSENDER</td></thead>";
						echo "<tbody><td>". $buchInfos[$i]->umsatzName . " ". $buchInfos[$i]->umsatzWert . "</td></tbody>";
						
						echo "<tbody>";
						$selectKonten = "SELECT * FROM finanzen_konten WHERE besitzer = $besitzer";
						$konten = $this->getObjektInfo($selectKonten);
						echo "<td>";
						echo "<select name=absenderKonto />";
						
						for ($j = 0 ; $j < sizeof($konten) ; $j++) {
							echo "<option value='".$konten[$j]->id."'";
							if($buchInfos[$i]->konto == $konten[$j]->id) {
								echo " selected ";
							}
							echo ">";
							echo $konten[$j]->konto;
							echo "</option>";
						}
						
						echo "</select>";
						echo "</td>";
						echo "</tbody>";
					}
					
					echo "</table>";
				}
				
				echo "<input type=submit name=absenden value=speichern />";
				
				echo "</form></div>";
			}
		}
	}
	
	/**
	 * Ermöglicht das löschen von Konten. 
	 * @param unknown $besitzer
	 */
	function showDeleteKonto($besitzer) {
		if(isset($_GET['deleteKonto'])) {
			$konto = $_GET['deleteKonto'];
			$select = "SELECT * FROM finanzen_konten WHERE besitzer = $besitzer AND id = $konto";
			$select2 = "SELECT * FROM finanzen_umsaetze WHERE besitzer = $besitzer AND konto = $konto";
			if($this->objectExists($select) == true AND $this->objectExists($select2) == false) {
				if($this->sql_insert_update_delete("DELETE FROM finanzen_konten WHERE besitzer = $besitzer AND id = $konto") == true) {
					echo "<p class='info'>Konto wurde gelöscht.</p>";
				} else {
					echo "<p class='meldung'>Beim löschen ist ein Fehler aufgetreten.</p>";
				}
				
			} else {
				echo "<p class='meldung'>Konto darf nicht gelöscht werden, entweder das Konto existiert nicht, oder es sind noch Buchungen auf dem Konto vorhanden.</p>";
			}
		}
	}
	
	function saldenUebersicht($besitzer) {
		if(isset($_GET['Salden'])) {
			echo "<div>";
			$select = "SELECT * FROM finanzen_konten WHERE besitzer=$besitzer";
			$konten = $this->getObjektInfo($select);
			
			for ($i = 0 ; $i < sizeof($konten) ; $i++) {
				$select2 = "SELECT sum(umsatzWert) as summe FROM finanzen_umsaetze WHERE konto=".$konten[$i]->id;
				$umsaetze = $this->getObjektInfo($select2);	
				echo "<div class='newChar'>";
				echo "<h2>".$konten[$i]->konto."</h2>";
				echo "Summe: " . $umsaetze[0]->summe;
				echo "</div>";
				
			}
			echo "</div>";
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
						echo "<tbody><td>Es gibt keinen Jahresabschluss für Konto <strong>" . $kontenDesNutzers[$i]->konto . "($geprueftesJahr)</strong>, dieser wird jetzt erstellt:</td></tbody>";
					
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