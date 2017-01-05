<?php
include 'objekt/functions.class.php';
class finanzenNEW extends functions {
	
    /**
     * Stellt Funktionen auf der finanzen/index.php bereit
     */
    function mainFinanzFunction() {
		$besitzer = $this->getUserID ( $_SESSION ['username'] );
		
		$this->showCreateNewUeberweisung ();
		
		if (!isset ($_GET['newUeberweisung'])) {
			
			// automatische Jahresabschluss-Generation.
			$this->erstelleJahresabschluesseFromOldEintraegen ();
			
			// Navigationslinks
			$this->showKontenInSelect ( $besitzer );
			$this->showJahreLinks ();
			$this->showMonateLinks ();
			
			// Informationsmeldungen
			$this->showErrors ();
			$this->showFuturePastJahr ();
			$this->showFuturePastMonat ();
			$this->checkJahresabschluss ();
            $this->showJahresabschlussIfAvailable();
            
			// Umsatzveränderungen
			$this->alterUmsatz ();
			
			// Hauptansicht Monat
			$this->showCurrentMonthInKonto ( $besitzer );
			$this->diagrammOptionen ( $besitzer );
			
		}
	}
    
    /**
     * Stellt Funktionen auf der finanzen/konten.php bereit
     */
	function mainKontoFunction() {
		// Besitzer:
		$besitzer = $this->getUserID ( $_SESSION ['username'] );
		
		// Buttons
		echo "<a href='?neuesKonto' class='rightBlueLink'>Neues Konto</a>";
	#	echo "<a href='?Salden' class='rightBlueLink'>Salden</a>";
		
		// Saldenübersicht:
	#	$this->saldenUebersicht ( $besitzer );
		
		// Kontomanipulationen
		$this->showCreateNewUeberweisung ();
		$this->showCreateNewKonto ( $besitzer );
		
		// Kontoübersicht:
		$this->showKontoUebersicht ( $besitzer );
		
	}
	function mainKontoDetail() {
		$besitzer = $this->getUserID ( $_SESSION ['username'] );
		
		$this->showBuchungRange();
		// Kontomanipulationen
		$this->showCreateNewUeberweisung ();
		$this->UmbuchungUmsatz ( $besitzer );
		$this->showCreateNewKonto ( $besitzer );
		$this->showDeleteKonto ( $besitzer );
		
		// Hauptfunktion
		$this->showEditKonto ( $besitzer );
	}
	private $suche;
	function FinanzSuche($suchWort) {
		if ($this->userHasRight ( "23", 0 ) == true) {
			if (isset ( $suchWort ) and $suchWort != "") {
				
				$besitzer = $this->getUserID ( $_SESSION ['username'] );
				
				$ursprünglicheSuche = $suchWort;
				// Suche mit Wildcards bestücken
				$suchWort = "%" . $suchWort . "%";
				
				// Spalten der Tabelle selektieren:
				$colums = "SHOW COLUMNS FROM finanzen_umsaetze";
				
				$rowSpalten = $this->getObjektInfo ( $colums );
				
				// SuchQuery bauen:
				// Start String:
				$querySuche = "SELECT *
				, month(datum) as monat
				, year(datum) as jahr 
				FROM finanzen_umsaetze 
				WHERE (besitzer=$besitzer AND id LIKE '$suchWort' ";
				
				// OR + Spaltenname LIKE Suchwort
				for($i = 0; $i < sizeof ( $rowSpalten ); $i ++) {
					$querySuche .= " OR besitzer = $besitzer AND " . $rowSpalten [$i]->Field . " LIKE '$suchWort'";
				}
				// Klammer am Ende schließen-
				$querySuche .= ")";
				
				// Query für die Suche
				$suchfeld = $this->getObjektInfo ( $querySuche );
				
				echo "<div id='draggable' class='summe'>";
				echo "Die Suche nach <strong>($suchWort)</strong> ergab folgendes Ergebnis:";
				echo "<div class='mainbody'>";
				echo "<table class='flatnetTable'>";
				echo "<thead><td>Konto</td><td>Umsatzname</td><td>Wert</td><td>Datum</td></thead>";
				for($i = 0; $i < sizeof ( $suchfeld ); $i ++) {
					
					$kontoname = $this->getObjektInfo ( "SELECT * FROM finanzen_konten WHERE id=" . $suchfeld [$i]->konto . " LIMIT 1" );
					$kontoname = $kontoname [0]->konto;
					
					echo "<tr><td>" . $kontoname . "</td><td> " . "<a href='?konto=" . $suchfeld [$i]->konto . "&jahr=" . $suchfeld [$i]->jahr . "&monat=" . $suchfeld [$i]->monat . "&selected=" . $suchfeld [$i]->buchungsnr . "'>" . substr ( $suchfeld [$i]->umsatzName, 0, 20 ) . "</a></td>" . "<td>" . $suchfeld [$i]->umsatzWert . "</td>" . "<td>" . $suchfeld [$i]->datum . "</td>" . "</td></tr>";
				}
				echo "</table>";
				echo "</div>";
				
				echo "</div>";
			}
		}
	}
	private $shows;
	
	function checkKontoSicherheit($kontoid) {
	#	echo "<p class='info'>$kontoid</p>";
		if(isset($kontoid) AND is_numeric($kontoid) == true) {
			$currentuser = $this->getUserID($_SESSION['username']);
						
			$kontoinfos = $this->getObjektInfo("SELECT * FROM finanzen_konten WHERE id=$kontoid");
			
			if(!isset($kontoinfos[0]->id)) {
				$error = 1;
			}
			
			if(isset($kontoinfos[0]->besitzer) AND $kontoinfos[0]->besitzer != $currentuser) {
				$error = 2;
			}
			if(isset($kontoinfos[0]->besitzer) AND $kontoinfos[0]->besitzer == $currentuser) {
				$error = 3;
			}
		} else {
			$error = 4;
		}
		
		if($error == 1 OR $error == 2 OR $error == 4) {
			echo "<p class='newChar'>Um die Konto&uuml;bersicht zu sehen, muss ein g&uuml;ltiges Konto angegeben werden.</p>";
			exit;
		}
		if($error = 3) {
			
		}
		
	}
	
	/**
	 * Zeigt den aktuellen Monat in der Finanz&uuml;bersicht an.
	 * 
	 * @param unknown $besitzer        	
	 */
	function showCurrentMonthInKonto($besitzer) {
		// Kontoinfo bekommen:
		$kontoID = $this->getKontoIDFromGet ();
		$monat = $this->getMonatFromGet ();
		$currentMonth = $monat;
		// jetziger Monat:
		
		$currentYear = $this->getJahrFromGet ();
		
		$this->checkKontoSicherheit($kontoID);
		
		if ($kontoID > 0) {
			$umsaetze = $this->getUmsaetzeMonthFromKonto ( $besitzer, $currentMonth, $currentYear, $kontoID );
			
			// Jahresanfangssaldo bekommen:
			$letztesJahr = $currentYear - 1;
			$summeJahresabschluesseBisJetzt = $this->getJahresabschluesseBISJETZT ( $besitzer, $kontoID, $currentYear );
			$summeUmsaetzeDiesesJahr = $this->getObjektInfo ( "SELECT sum(umsatzWert) as summe 
					FROM finanzen_umsaetze
					WHERE besitzer = $besitzer
					AND konto = $kontoID
					AND year(datum) = $currentYear
					AND month(datum) < $currentMonth" );
			
			// Wenn das Jahr in der Zukunft liegt, werden alle
			// Umsätze der Vergangenheit einzeln addiert.
			$diesesJahr = date ( "Y" );
			if ($this->checkIfJahrIsInFuture ( $diesesJahr, $currentYear ) == true) {
				$getSaldoUntilNow = $this->getObjektInfo ( "SELECT sum(umsatzWert) as summe 
					FROM finanzen_umsaetze
					WHERE besitzer = $besitzer
					AND konto = $kontoID
					AND year(datum) < $currentYear" );
				$startsaldo = $getSaldoUntilNow [0]->summe + $summeUmsaetzeDiesesJahr [0]->summe;
			} else {
				$startsaldo = $summeJahresabschluesseBisJetzt + $summeUmsaetzeDiesesJahr [0]->summe;
			}
			$zwischensumme = $startsaldo;
			echo "<table class='kontoTable'>";
			
			# Update vom 30.12.2016:
			# Bei Konten Art 2 wird kein Saldo angezeigt ...
			$kontoinfos = $this->getObjektInfo("SELECT * FROM finanzen_konten WHERE id = $kontoID");
				
			if($kontoinfos[0]->art == 2) {
				echo "<thead><td colspan=8 id='notOK'>Dieses Konto hat keinen Saldo!</td></thead>";
			}
			if($kontoinfos[0]->art == 1) {
				echo "<thead><td colspan=8 id='ok'>Guthabenkonto</td></thead>";
			}
			
			# Keinen Startsaldo anzeigen: 
			if($kontoinfos[0]->art == 2) {
				$saldotext = "";
			} else {
				$saldotext = "/ Startsaldo diesen Monat: <strong>$startsaldo €";
			}
			echo "<thead>";
			echo "<td colspan=7>Monat:<strong> $currentMonth </strong>im Jahr <strong>$currentYear</strong> $saldotext </td>";
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
					
			if (isset ( $umsaetze [0]->id )) {
				
				// Zeilengeneration #
				
				for($i = 0; $i < sizeof ( $umsaetze ); $i ++) {
					$zwischensumme = $zwischensumme + $umsaetze [$i]->umsatzWert;
					
					// Daten in Array laden:
					$zahlen [$i] = $zwischensumme;
					
					if ($zwischensumme < 0) { $spaltenFarbe = "rot"; } else { $spaltenFarbe = "rightAlign"; }
					
					if ($zwischensumme < 0) { $zeile = " id='minus' "; } else { $zeile = ""; }
					
					if ($umsaetze [$i]->umsatzWert < 0) { $zelle = " id='minus' "; } else { $zelle = " id='plus' "; }
					
					// Wenn der Umsatz ausgewählt wurde, dann wird er rot markiert.
					if (isset ( $_GET ['selected'] )) { if ($_GET ['selected'] == $umsaetze [$i]->buchungsnr) {
						$selected = "id='yellow'"; } else { $selected = ""; } } else { $selected = ""; }
					
					echo "<tbody $selected>";
					echo "<td><a href='?konto=".$umsaetze [$i]->gegenkonto."&monat=$monat&jahr=$currentYear&selected=" . $umsaetze [$i]->buchungsnr . "'>" . $umsaetze [$i]->buchungsnr . "</a></td>";
					// Name des Gegenkontos bekommen
					$nameGegenkonto = $this->getObjektInfo ( "SELECT * FROM finanzen_konten WHERE besitzer = $besitzer AND id = " . $umsaetze [$i]->gegenkonto . " LIMIT 1" );
					echo "<td>" . $nameGegenkonto [0]->konto . "</td>";
					echo "<td>" . $umsaetze [$i]->umsatzName . "</td>";
					echo "<td>" . $umsaetze [$i]->tag . "</td>";
					$ausgabeZwischensumme = round($zwischensumme, 2);
					echo "<td $zelle>" . $umsaetze [$i]->umsatzWert . "</td>";
					if($kontoinfos[0]->art == 2) {
						$spaltenFarbe = "";
						echo "<td id='$spaltenFarbe'>" . "-" . "</td>";
					} else {
						echo "<td id='$spaltenFarbe'>" . $ausgabeZwischensumme . "</td>";
					}
					
					echo "<td>" . "<a class='rightBlueLink' href='?konto=$kontoID&monat=$monat&jahr=$currentYear&edit=" . $umsaetze [$i]->id . "'>edit</a>" . "</td>";
					echo "</tbody>";
					
					// HEUTE Zeile anzeigen
					$timestamp = time ();
					$heute = date ( "Y-m-d", $timestamp );
					if ($umsaetze [$i]->datum < $heute and isset ( $umsaetze [$i + 1]->datum ) and $umsaetze [$i + 1]->datum >= $heute) {
						$heute = date ( "d.m.Y", $timestamp );
						echo "<tbody id='today'><td colspan='7'><a name='heute'>Heute ist der $heute</a> n&auml;chster Umsatz: " . $umsaetze [$i + 1]->umsatzName . "</td></tbody>";
					}
				}
			} else {
				
				// Wenn kein Umsatz gefunden wird, den nächsten anzeigen:
				
				// CURDATE zusammenbauen.
				if ($monat < 10) {
					$monat = "0" . $monat;
				}
				$curdate = $currentYear . "-" . $monat . "-01";
				
				$naechsterUmsatz = $this->getObjektInfo ( "SELECT *, month(datum) as monat, year(datum) as jahr FROM finanzen_umsaetze WHERE besitzer=$besitzer AND konto=$kontoID AND datum > '$curdate' ORDER BY datum ASC LIMIT 1" );
				
				echo "<tbody id='plus'><td colspan=7>$curdate In diesem Monat gibt es keine Umsätze, der nächste Umsatz lautet: </td></tbody>";
				
				for($k = 0; $k < sizeof ( $naechsterUmsatz ); $k ++) {
					$naechsterMonat = $naechsterUmsatz [$k]->monat;
					echo "<tbody id=''>" . "<td>" . $naechsterUmsatz [$k]->datum . "</td>" . "<td colspan=6>" . $naechsterUmsatz [$k]->umsatzName . ",  <a href='?konto=$kontoID&monat=$naechsterMonat&jahr=" . $naechsterUmsatz [$k]->jahr . "'>springe zu Monat</a></td>" . "</tbody>";
				}
			}
			$differenz = $zwischensumme - $startsaldo;
			if($differenz < 0.01 AND $differenz > 0) {
				$differenz = 0;
			}
			if($zwischensumme < 0.01 AND $zwischensumme > 0) {
				$zwischensumme = 0;
			}
			
			if($kontoinfos[0]->art == 2) {
				echo "<tfoot><td colspan=5 id='rightAlign'>Endsaldo: </td><td id='rightAlign'> - </td><td></td></tfoot>";
			} else {
				echo "<tfoot><td colspan=5 id='rightAlign'>Endsaldo: </td><td id='rightAlign'>$zwischensumme</td><td></td></tfoot>";
			}
			
			echo "</table>";
			if($differenz > 0) {
				echo "<p class='dezentInfo'>Kontostandver&auml;nderung: $differenz &euro;, h&ouml;herer Saldo als im Vormonat.</p>";
			} else if($differenz < 0) {
				echo "<p class='info'>Kontostandver&auml;nderung: $differenz &euro;, geringerer Saldo als im Vormonat.</p>";
			} else if($differenz == 0) {
				echo "<p class='dezentInfo'>Keine Saldo Ver&auml;nderung</p>";
			}
			
			
			// Zeigt das Diagramm an
			if (isset ( $zahlen )) {
				$this->showDiagramme ( $zahlen, "970", "200" );
			}
		} else {
			echo "<div class='newCharWIDE'><h3>Um Fortzufahren musst du ein Konto auswählen</h3>";
			// echo "<img width=950px height=150px src='/flatnet2/images/waiting.gif' />";
			echo "</div>";
		}
	}
	
	/**
	 * Zeigt Optionen für das Diagramm an.
	 */
	function diagrammOptionen($besitzer) {
		if (isset ( $_GET ['konto'] ) and isset ( $_GET ['jahr'] )) {
			echo "<div>";
			$konto = $_GET ['konto'];
			
			$jahr = $_GET ['jahr'];
		#	echo "<a class='buttonlink' href='?kontoOpt=$konto&jahr=$jahr&gesamtesJahr'>Jahr anzeigen</a>";
		#	echo "<a class='buttonlink' href='?kontoOpt=$konto&alles'>Alles anzeigen</a>";
			echo "<a class='buttonlink' href='/flatnet2/finanzen/detail.php?editKonto=$konto'>Konto-Einstellungen</a>";
			echo "</div>";
		}
		
		if (isset ( $_GET ['gesamtesJahr'] ) and isset ( $_GET ['kontoOpt'] ) and isset ( $_GET ['jahr'] )) {
			
			$konto = $_GET ['kontoOpt'];
			
			$jahr = $_GET ['jahr'];
			
			$query = "SELECT * FROM finanzen_umsaetze WHERE besitzer = $besitzer AND konto = $konto AND year(datum) = $jahr";
			$zahlen = $this->getObjektInfo ( $query );
			$zwischensumme = 0;
			
			// Zwischensummen bilden und in Var schreiben
			
			for($i = 0; $i < sizeof ( $zahlen ); $i ++) {
				$zwischensumme = $zahlen [$i]->umsatzWert + $zwischensumme;
				$arrayFuerDiagramm [$i] = $zwischensumme;
			}
			
			if (isset ( $arrayFuerDiagramm )) {
				$this->showDiagramme ( $arrayFuerDiagramm, "700", "200" );
			}
		}
		
		if (isset ( $_GET ['alles'] ) and isset ( $_GET ['kontoOpt'] )) {
			$konto = $_GET ['kontoOpt'];
			$query = "SELECT * FROM finanzen_umsaetze WHERE besitzer = $besitzer AND konto = $konto";
			$zahlen = $this->getObjektInfo ( $query );
			$zwischensumme = 0;
			
			for($i = 0; $i < sizeof ( $zahlen ); $i ++) {
				$zwischensumme = $zahlen [$i]->umsatzWert + $zwischensumme;
				$arrayFuerDiagramm [$i] = $zwischensumme;
			}
			
			if (isset ( $arrayFuerDiagramm )) {
				$this->showDiagramme ( $arrayFuerDiagramm, "700", "200" );
			}
		}
	}
	
	/**
	 * Zeigt die Navigation innerhalb der Finanzanwendung an.
	 */
	function showNavigation() {
		$kontoID = $this->getKontoIDFromGet ();
		$monat = $this->getMonatFromGet ();
		$jahr = $this->getJahrFromGet ();
		
		echo "<div class='finanzNAV'>";
				echo "<ul>";
					echo "<li id='monate'><a href='index.php' >Finanzverwaltung - Startseite</a></li>";
					echo "<li id='konten'><a href='konten.php' >Konten</a></li>";
					echo "<li><a href='?konto=$kontoID&monat=$monat&jahr=$jahr&newUeberweisung' >Neue Buchung</a></li>";
				#	echo "<li><a href='?konto=$kontoID&monat=$monat&jahr=$jahr&checkJahresabschluesse' >Jahresabschlusscheck</a></li>";
				echo "</ul>";
			echo "</div>";
	}
	function showMonateLinks() {
		if (isset ( $_GET ['konto'] )) {
			$konto = $_GET ['konto'];
		} else {
			$konto = 0;
		}
		
		if (isset ( $_GET ['jahr'] )) {
			$jahr = $_GET ['jahr'];
		} else {
			$jahr = date ( "Y" );
		}
		
		if (isset ( $_GET ['monat'] )) {
			$monat = $_GET ['monat'];
		} else {
			$monat = date ( "M" );
		}
		
		echo "<ul class='FinanzenMonate'>";
		
		if($monat == 1) {
			$monatzurueck = 12;
			$jahrzurueck = $jahr - 1;
		} else {
			$jahrzurueck = $jahr;
			$monatzurueck = $monat - 1;
		}
		
		if($monat == 12) {
			$monatvor = 1;
			$jahrvor = $jahr + 1;
		} else {
			$monatvor = $monat + 1;
			$jahrvor = $jahr;
		}

		echo "<li>";
		echo "<a href='?konto=$konto&monat=$monatzurueck&jahr=$jahrzurueck'> &laquo; </a>";
		echo "</li>";
				
		echo "<li ";
		if ($monat == 1) {
			echo " id='selected' ";
		}
		echo "><a href='?konto=$konto&monat=1&jahr=$jahr'>Januar</a></li>";
		echo "<li ";
		if ($monat == 2) {
			echo " id='selected' ";
		}
		echo "><a href='?konto=$konto&monat=2&jahr=$jahr'>Februar</a></li>";
		echo "<li ";
		if ($monat == 3) {
			echo " id='selected' ";
		}
		echo "><a href='?konto=$konto&monat=3&jahr=$jahr'>M&auml;rz</a></li>";
		echo "<li ";
		if ($monat == 4) {
			echo " id='selected' ";
		}
		echo "><a href='?konto=$konto&monat=4&jahr=$jahr'>April</a></li>";
		echo "<li ";
		if ($monat == 5) {
			echo " id='selected' ";
		}
		echo "><a href='?konto=$konto&monat=5&jahr=$jahr'>Mai</a></li>";
		echo "<li ";
		if ($monat == 6) {
			echo " id='selected' ";
		}
		echo "><a href='?konto=$konto&monat=6&jahr=$jahr'>Juni</a></li>";
		echo "<li ";
		if ($monat == 7) {
			echo " id='selected' ";
		}
		echo "><a href='?konto=$konto&monat=7&jahr=$jahr'>Juli</a></li>";
		echo "<li ";
		if ($monat == 8) {
			echo " id='selected' ";
		}
		echo "><a href='?konto=$konto&monat=8&jahr=$jahr'>August</a></li>";
		echo "<li ";
		if ($monat == 9) {
			echo " id='selected' ";
		}
		echo "><a href='?konto=$konto&monat=9&jahr=$jahr'>September</a></li>";
		echo "<li ";
		if ($monat == 10) {
			echo " id='selected' ";
		}
		echo "><a href='?konto=$konto&monat=10&jahr=$jahr'>Oktober</a></li>";
		echo "<li ";
		if ($monat == 11) {
			echo " id='selected' ";
		}
		echo "><a href='?konto=$konto&monat=11&jahr=$jahr'>November</a></li>";
		echo "<li ";
		if ($monat == 12) {
			echo " id='selected' ";
		}
		echo "><a href='?konto=$konto&monat=12&jahr=$jahr'>Dezember</a></li>";
		
		echo "<li>";
		echo "<a href='?konto=$konto&monat=$monatvor&jahr=$jahrvor'> &raquo; </a>";
		echo "</li>";
		
		echo "</ul>";
		
	}
	function showJahreLinks() {
		if (isset ( $_GET ['konto'] )) {
			$konto = $_GET ['konto'];
		} else {
			$konto = 0;
		}
		
		if (isset ( $_GET ['monat'] )) {
			$monat = $_GET ['monat'];
		} else {
			$monat = date ( "m" );
		}
		
		if (isset ( $_GET ['jahr'] )) {
			$jahr = $_GET ['jahr'];
		} else {
			$jahr = date ( "Y" );
		}
		
		echo "<ul class='FinanzenMonate'>";
		
		$currentYear = date ("Y");
		
		# Previous Year:
		$jahrprev = $jahr - 1;
		$jahrforw = $jahr + 1;
		echo "<li id=''><a href='?konto=$konto&monat=$monat&jahr=$jahrprev'>$jahrprev</a></li>";
		echo "<li id='selected'><a href='?konto=$konto&monat=$monat&jahr=$jahr'>$jahr</a></li>";
		echo "<li id=''><a href='?konto=$konto&monat=$monat&jahr=$jahrforw'>$jahrforw</a></li>";
		echo "</ul>";
	}
	
	/**
	 * Zeigt ein Select mit den Konten des Nutzers an.
	 * 
	 * @param unknown $besitzer        	
	 */
	function showKontenInSelect($besitzer) {
		$monat = $this->getMonatFromGet ();
		$jahr = $this->getJahrFromGet ();
		
		$konten = $this->getAllKonten ( $besitzer );
		
		if (isset ( $_GET ['konto'] )) {
			$konto = $_GET ['konto'];
		} else {
			$konto = "";
		}
		if (isset ( $konten [0]->id ) and $konten [0]->id != "") {
			echo "<ul class='FinanzenKonten'>";
			for($i = 0; $i < sizeof ( $konten ); $i ++) {
				
				if ($konten [$i]->aktiv == 1) {
					echo "<li ";
					if ($konto == $konten [$i]->id) {
						echo " id='selected' ";
					}
					echo "><a href='?konto=" . $konten [$i]->id . "&monat=$monat&jahr=$jahr'>" . $konten [$i]->konto . "</a></li>";
				}
			}
			echo "</ul>";
		}
	}
	
	/**
	 * Gibt die Get Variable KONTO zurück.
	 * 
	 * @return unknown|boolean
	 */
	function getKontoIDFromGet() {
		if (isset ( $_GET ['konto'] )) {
			$kontoID = $_GET ['konto'];
			
			return $kontoID;
		} else {
			return false;
		}
	}
	
	/**
	 * Gibt den Monat zurück.
	 * 
	 * @return unknown|boolean
	 */
	function getMonatFromGet() {
		if (isset ( $_GET ['monat'] )) {
			$monat = $_GET ['monat'];
		} else {
			$monat = date ( "m" );
		}
		
		return $monat;
	}
	
	/**
	 * Gibt das Jahr zurück.
	 * 
	 * @return unknown|boolean
	 */
	function getJahrFromGet() {
		if (isset ( $_GET ['jahr'] )) {
			$jahr = $_GET ['jahr'];
		} else {
			$jahr = date ( "Y" );
		}
		
		return $jahr;
	}
	
	/**
	 * Zeigt eine Meldung an, wenn das gewählte Jahr in der Zukunft liegt.
	 */
	function showFuturePastJahr() {
		if (isset ( $_GET ['jahr'] )) {
			$jahr = $_GET ['jahr'];
			$currentJahr = date ( "Y" );
			
			if ($this->checkIfJahrIsInFuture ( $currentJahr, $jahr ) == true) {
				echo "<p class='dezentInfo'>Das gewählte Jahr liegt in der Zukunft.";
			}
			
			if ($this->checkIfJahrIsInPast ( $currentJahr, $jahr ) == true) {
				echo "<p class='dezentInfo'>Das Jahr liegt in der Vergangenheit.";
			}
		}
	}
	
	/**
	 * Zeigt eine Meldung an, wenn der gewählte Monat in der Zukunft liegt.
	 */
	function showFuturePastMonat() {
		if (isset ( $_GET ['monat'] )) {
			#$monat = $_GET ['monat'];
			#$currentMonat = date ("m");
			
			#$jahr = $_GET['jahr'];
			#$currentJahr = date ("Y");
			
			#if ($this->checkIfMonatIsInFuture ( $currentMonat, $monat ) == true) {
			#	echo "<p class='info'>Der Monat liegt in der Zukunft";
			#}
			
			#if ($this->checkIfMonatIsInPast ( $currentMonat, $monat, $currentJahr, $jahr ) == true) {
			#	echo "<p class='dezentInfo'>Der Monat liegt in der Vergangenheit.";
			#}
		}
	}
	
	/**
	 * Prüft, ob Jahr in der Zukunft ist.
	 * 
	 * @param unknown $currentJahr        	
	 * @param unknown $zuPruefendesJahr        	
	 * @return boolean
	 */
	function checkIfJahrIsInFuture($currentJahr, $zuPruefendesJahr) {
		if ($zuPruefendesJahr > $currentJahr) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Prüft, ob Jahr in der Vergangenheit liegt.
	 * 
	 * @param unknown $currentJahr        	
	 * @param unknown $zuPruefendesJahr        	
	 * @return boolean
	 */
	function checkIfJahrIsInPast($currentJahr, $zuPruefendesJahr) {
		if ($zuPruefendesJahr < $currentJahr) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Prüft, ob Monat in der Zukunft ist.
	 * 
	 * @param unknown $currentJahr        	
	 * @param unknown $zuPruefendesJahr        	
	 * @return boolean
	 */
	function checkIfMonatIsInFuture($currentMonat, $zuPruefenderMonat) {
		if ($zuPruefenderMonat > $currentMonat) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Prüft, ob Monat in der Vergangenheit liegt.
	 * 
	 * @param unknown $currentJahr        	
	 * @param unknown $zuPruefendesJahr        	
	 * @return boolean
	 */
	function checkIfMonatIsInPast($currentMonat, $zuPruefenderMonat, $currentJahr, $pruefendesJahr) {
		if ($this->checkIfJahrIsInFuture ( $currentJahr, $pruefendesJahr ) == true) {
			return false;
		} else {
			if ($zuPruefenderMonat < $currentMonat) {
				return true;
			} else {
				return false;
			}
		}
	}
	private $reCalc;
	

	/**
	 * Berechnet die Jahresabschlüsse neu, wenn sich in der Vergangenheit etwas geändert hat.
	 * 
	 * @param unknown $besitzer        	
	 * @param unknown $konto        	
	 */
	function reCalcJahresabschluesse($besitzer, $konto) {
        
	}
	private $errors;
	function showErrors() {
		// Summe aller Umsätze
		$kontostand = $this->getObjektInfo ( "SELECT sum(umsatzWert) AS summe FROM finanzen_umsaetze" );
		
		// Buchungsnummer Check:
		
		// Wenn eine Buchung korrupt ist:
		if ($kontostand [0]->summe > 0 or $kontostand [0]->summe < 0) {
			
			echo "<div class='newChar'>";
			echo "Achtung, es wurde ein Fehler in mindestens einer Buchung entdeckt.
				Die Werte innerhalb einer Buchungsnummer sind unterschiedlich,
				dies kann verschiedene Gr&uuml;nde haben. Der Administrator wurde &uuml;ber
				dieses Problem informiert. Die betroffene Buchung wird jetzt gel&ouml;scht!";
			
			$selectProblem = "SELECT max(buchungsnr) as max FROM finanzen_umsaetze";
			$max = $this->getObjektInfo ( $selectProblem );
			
			$max = $max [0]->max;
			
			$i = 0;
			for($i = 0; $i <= $max; $i ++) {
				$select = "SELECT * FROM finanzen_umsaetze WHERE buchungsnr = $i ";
				
				// ANZAHL &Uuml;BERPR&Uuml;FEN:
				$selectAnzahl = "SELECT * FROM finanzen_umsaetze WHERE buchungsnr = $i";
				$anzahl = $this->getAmount ( $selectAnzahl );
				$infos = $this->getObjektInfo($select);
				
				if ($anzahl != 2 and $anzahl != 0) {
					echo "<p class='meldung'>Unvollst&auml;ndige Buchung: " .$infos[0]->umsatzName. ", ".$infos[0]->umsatzWert."</p>";
					$delete = "DELETE FROM finanzen_umsaetze
					WHERE buchungsnr = '$i' LIMIT 1";
					if ($this->sql_insert_update_delete ( $delete ) == true) {
						$this->logEintrag ( true, "Buchung $i wurde wegen Unvollständigkeit gel&ouml;scht.", "Error" );
						echo "<p class='erfolg'>Fehlerhafte Buchung gel&ouml;scht</p>";
					}
				}
				
				$buchung = $this->getObjektInfo ( $select );
				$j = 0;
				for($j = 0; $j < 2; $j ++) {
					// $buchung[$j]->umsatzName . " " . $buchung[$j]->umsatzWert . "<br>";
					if (isset ( $buchung [$j]->umsatzWert ) and isset ( $buchung [$j + 1]->umsatzWert )) {
						if ($buchung [$j]->umsatzWert * (- 1) != $buchung [$j + 1]->umsatzWert) {
							echo "<p class='meldung'>Es gibt ein Problem bei  <strong>Buchungs-Nr. $i</strong>: Werte: " . $buchung [$j]->umsatzWert . " und " . $buchung [$j + 1]->umsatzWert . " <a href='?surpress&id=" . $buchung [$j]->konto . "&UmsatzID=" . $buchung [$j]->id . "'>klicke hier um einen EDIT vorzunehmen</a></p>";
						}
					}
				}
			}
			echo "Klicke OK um Fortzufahren <a href='?' class='buttonlink'>OK</a>";
			echo "</div>";
			if (! isset ( $_GET ['surpress'] )) {
				exit ();
			}
		}
	}
	private $umsaetze;
	
	/**
	 * Ermöglicht das modifizieren eines Umsatzes.
	 */
	function alterUmsatz() {
		if ($this->userHasRight (18, 0 ) == true) {
			if (isset ( $_GET ['edit'] )) {
				
				$kontoLink = $this->getKontoIDFromGet ();
				$monat = $this->getMonatFromGet ();
				$jahr = $this->getJahrFromGet ();
				
				$id = $_GET['edit'];
				$besitzer = $this->getUserID ($_SESSION ['username']);
				$umsatzInfo = $this->getObjektInfo("SELECT * FROM finanzen_umsaetze WHERE id = '$id' and besitzer = '$besitzer'");
				
				echo "<div id='' class='alterUmsatz'>";
				echo "<a href='?konto=$kontoLink&monat=$monat&jahr=$jahr' class=''>schließen</a>";
				echo "<form method=post>";
				echo "<h2><a name=umsatz>Umsatz Nr. " . $umsatzInfo [0]->id . "</a></h2>";
				echo "<input type=text name=umsatzName value='" . $umsatzInfo [0]->umsatzName . "' /><br>";
				$kontoID = $umsatzInfo [0]->gegenkonto;
				$konto2ID = $umsatzInfo [0]->konto . "<br>";
				$gegenkonto = $this->getObjektInfo ( "SELECT * FROM finanzen_konten WHERE id = '$kontoID'" );
				$konto = $this->getObjektInfo ( "SELECT * FROM finanzen_konten WHERE id = '$konto2ID'" );
				echo "Buchung auf: " . $konto [0]->konto . " ";
				echo " - " . $gegenkonto [0]->konto . "<br>";
				echo "<input type=text name=umsatzWert value='" . $umsatzInfo [0]->umsatzWert . "' /><br>";
				echo "<input type=date name=umsatzDatum value='" . $umsatzInfo [0]->datum . "'  /><br>";
				echo "<input type=submit name=alterUmsatz value=Speichern />";
				echo "<input type=submit name=loeschUmsatz value=Löschen />";
				echo "</form>";
				echo "</div>";
				
				if (isset ( $_POST ['alterUmsatz'] )) {
					$text = $_POST ['umsatzName'];
					$wert = str_replace ( ',', '.', $_POST ['umsatzWert'] );
					$datum = $_POST ['umsatzDatum'];
					$besitzer = $this->getUserID ( $_SESSION ['username'] );
					$id = $_GET ['edit'];
					
					// Buchungsnummer herausfinden;
					$objektBuchungsNr = $this->getObjektInfo ( "SELECT id, buchungsnr FROM finanzen_umsaetze WHERE id = '$id'" );
					$buchungsnr = $objektBuchungsNr [0]->buchungsnr;
					
					// Werte errechnen:
					
					if ($wert > 0) {
						$minusWert = $wert * (- 1);
						$plusWert = $wert;
					} else {
						$minusWert = $wert;
						$plusWert = $wert * (- 1);
					}
					
					if ($minusWert > 0 or $plusWert < 0) {
						exit ();
					}
					
					// ID mit Minuswert herausfinden:
					$minusObjekt = $this->getObjektInfo ( "SELECT * FROM finanzen_umsaetze WHERE buchungsnr = '$buchungsnr' AND umsatzWert < 0 LIMIT 1" );
					$minusID = $minusObjekt [0]->id;
					
					// ID mit Minuswert herausfinden:
					$plusObjekt = $this->getObjektInfo ( "SELECT id, buchungsnr, umsatzWert FROM finanzen_umsaetze WHERE buchungsnr = '$buchungsnr' AND umsatzWert > 0 LIMIT 1" );
					$plusID = $plusObjekt [0]->id;
					
					if ($text != "" and $wert != "" and $besitzer != "" and $id != "" and $buchungsnr != "") {
						$plusQuery = "UPDATE finanzen_umsaetze set umsatzName='$text',umsatzWert='$plusWert' ,datum='$datum' WHERE besitzer='$besitzer' and id = '$plusID'";
						
						$minusQuery = "UPDATE finanzen_umsaetze set umsatzName='$text',umsatzWert='$minusWert' ,datum='$datum' WHERE besitzer='$besitzer' and id = '$minusID'";
						if ($this->userHasRight ( "18", 0 ) == true) {
							if ($this->sql_insert_update_delete ( $plusQuery ) == true and $this->sql_insert_update_delete ( $minusQuery ) == true) {
								echo "<p class='erfolg'>Umsatz gespeichert</p>";
							} else {
								echo "<p class='erfolg'>Fehler</p>";
							}
						} else {
							echo "<p class='meldung'>Keine Berechtigung</p>";
						}
					}
				}
				if (isset ( $_POST ['loeschUmsatz'] )) {
					$text = $_POST ['umsatzName'];
					$wert = $_POST ['umsatzWert'];
					$besitzer = $this->getUserID ( $_SESSION ['username'] );
					$datum = $_POST ['umsatzDatum'];
					$id = $_GET ['edit'];
					
					// Buchungsnummer herausfinden;
					$objektBuchungsNr = $this->getObjektInfo ( "SELECT id, buchungsnr FROM finanzen_umsaetze WHERE id = '$id'" );
					$buchungsnr = $objektBuchungsNr [0]->buchungsnr;
					
					if (! isset ( $buchungsnr ) or $buchungsnr == "") {
						exit ();
					}
					if ($this->userHasRight (18, 0 ) == true) {
						$delete = "DELETE FROM finanzen_umsaetze
						WHERE besitzer='$besitzer' AND buchungsnr = '$buchungsnr' LIMIT 2";
						if ($this->sql_insert_update_delete ( $delete ) == true) {
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
	 * Gibt die n&auml;chste freie Buchungsnummer wieder, verwendet auch alte, freie Nummern, welche kleiner als MAX sind.
	 * @return nextnumber
	 */
	function nextBuchungsnummer() {
		$allBNRs = $this->getObjektInfo("SELECT buchungsnr FROM finanzen_umsaetze ORDER BY buchungsnr");
		$maxbuchung = $this->getObjektInfo("SELECT max(buchungsnr) as max FROM finanzen_umsaetze");
		$maxnummer = $maxbuchung[0]->max + 0;
		
		$counter = 0;		
		for ($i = 1 ; $i <= $maxnummer; $i++) {
			$stop = 0;
			for($j = 0 ; $j < sizeof($allBNRs) ; $j++) {
				if($i == $allBNRs[$j]->buchungsnr) {
					$stop = 1;
				}
			}
				
			if($stop == 0) {
				$counter += 1;
				$nextnumber = $i;
			}
		}
		
		if($counter == 0) {
			$nextnumber = $maxnummer + 1;
		}
				
		return $nextnumber;
		
	}
	
	/**
	 * Gibt immer die n&auml;chste MAX nummer zur&uuml;ck.
	 * WIRD NICHT MEHR VERWENDET.
	 * @return $buchungsnummer
	 */
    function nextBuchungsnummerOLD() {
        $nextBuchungsnummer = $this->getObjektInfo ( "SELECT max(buchungsnr) as max FROM finanzen_umsaetze" );
		$buchungsnummer = $nextBuchungsnummer [0]->max;
		if (!isset($buchungsnummer)) {
		  $buchungsnummer = 0;
        }
		$buchungsnummer = $buchungsnummer + 1;
        return $buchungsnummer;
    }
    
    function createUeberweisung($besitzer, $von, $nach, $text, $betrag, $datum) {
        $buchungsnummer = $this->nextBuchungsnummer();
        $betragMinus = $betrag * (-1);
        
		$query = "INSERT INTO finanzen_umsaetze (buchungsnr, besitzer, konto, gegenkonto, umsatzName, umsatzWert, datum)
			VALUES ('$buchungsnummer','$besitzer','$von','$nach','$text','$betragMinus','$datum')";
		$query2 = "INSERT INTO finanzen_umsaetze (buchungsnr, besitzer, konto, gegenkonto, umsatzName, umsatzWert, datum)
			VALUES ('$buchungsnummer','$besitzer','$nach','$von','$text','$betrag','$datum')";
									
		if ($this->sql_insert_update_delete ( $query ) == true and $this->sql_insert_update_delete ( $query2 ) == true) {
            echo "<p class='erfolg'>&Uuml;berweisung von $text durchgef&uuml;hrt (Buchungsnummer: $buchungsnummer)</p>";
		}
    }
	
	/**
	 * Neue Überweisung.
	 * Ermöglicht das Überweisen von Geld von einem auf ein anderes Konto.
	 */
	function showCreateNewUeberweisung() {
		if (isset ( $_GET ['newUeberweisung'] )) {
			if ($this->userHasRight (18, 0) == true) {
                
                $anzahlWeitereFelder = 12;
                
                $maxpast = "2010-01-01";
				
				if (isset ( $_POST ['sendnewUeberweisung'] ) and isset ( $_POST ['valueUeberweisung'] ) and isset ( $_POST ['textUeberweisung'] ) and isset ( $_POST ['dateUeberweisung'] ) and isset ( $_POST ['zielKonto'] ) and isset ( $_POST ['absenderKonto'] )) {
					$von = $_POST ['absenderKonto'];
					$datum = $_POST ['dateUeberweisung'];
					$nach = $_POST ['zielKonto'];
					$betrag = str_replace ( ',', '.', $_POST ['valueUeberweisung'] );
					
					$betragMinus = $betrag * (- 1);
					
					// Wenn Absender und Ziel gleich ist:
					if ($von == $nach) {
						echo "<p class='meldung'>Absende und Zielkonto ist gleich. Keine Buchung m&ouml;glich.</p>";
						exit ();
					}
                    
                    if($datum < $maxpast) {
                        echo "<p class='meldung'>Keine Buchung vor $maxpast m&ouml;glich!</p>";
                        exit();
                    }
										
					$text = $_POST ['textUeberweisung'];
					$besitzer = $this->getUserID ( $_SESSION ['username'] );
					
					if ($von != "" and $nach != "" and isset ( $besitzer ) and $betrag != "" and $betrag > 0 and $datum != "" and $text != "") {
                        
                        # Normale Buchung durchführen: 
                        $this->createUeberweisung($besitzer, $von, $nach, $text, $betrag, $datum);
                                                
                        # Weitere Buchungen anhand Anzahl der nächsten Monate:
                        if(isset($_POST['weiterenumber'])) {
                            if(isset($_POST['monthweitere'])) {
                                if(is_numeric($_POST['monthweitere']) == true) {
                                    $number = $_POST['monthweitere'];
                                    
                                    for ($x = 1 ; $x <= $number ; $x++) {
                                        $newdate = new DateTime($datum);
                                        $newdate->modify("+$x month");
                                        echo "<p class='dezentInfo'>${x}: Buchung f&uuml;r Monat " . $newdate->format("Y-m-d") . "</p>";
                                        
                                        $this->createUeberweisung($besitzer, $von, $nach, $text, $betrag, $newdate->format("Y-m-d"));
                                        
                                    }
                                    
                                }
                            }
                        }
						
						# Manuelles hinzufügen von Buchungen anhand des genauen Datums:
						if (isset ( $_POST ['weitere'] )) {
							
							$dates = $_POST ['dates'];
							$j = 0;
							for($j = 0; $j < $anzahlWeitereFelder; $j ++) {
								
								// Nur gef&uuml;llte Inputfelder verwenden.
								if ($dates [$j] != "") {
                                    $this->createUeberweisung($besitzer, $von, $nach, $text, $betrag, $dates[$j]);
								}
							}
						}
					}
				}
				
				$kontoID = $this->getKontoIDFromGet ();
				$monat = $this->getMonatFromGet ();
				$jahr = $this->getJahrFromGet ();
				echo "<div class='newChar'>";
				
			#	echo "<a href='#' onclick='window.history.go(-1)' target='_self' class='highlightedLink'>Zurück</a>";
                echo "<a href='?konto=$kontoID&monat=$monat&jahr=$jahr' target='_self' class='highlightedLink'>Zurück</a>";
				
                echo "<p class='dezentInfo'>Du kommst von hier: Konto: $kontoID, Monat: $monat, Jahr: $jahr</p>";
				echo "<h2>Eine Buchung durchf&uuml;hren</h2>";
				
				echo "<form method=post>";
				echo "<table class='kontoTable'>";
				if (isset ( $_POST ['textUeberweisung'] )) {
					$textInput = $_POST ['textUeberweisung'];
				} else {
					$textInput = "";
				}
				echo "<tbody><td>Beschreibung</td><td><input type=text value='$textInput' placeholder='Text' name='textUeberweisung'/></td></tbody>";
				
				$besitzer = $this->getUserID ( $_SESSION ['username'] );
				$select = "SELECT * FROM finanzen_konten WHERE besitzer = '$besitzer' ORDER BY konto";
				$absenderKonten = $this->getObjektInfo ( $select );
				
				echo "<tbody><td>Gutschrift hier</td><td><select name='zielKonto'>";
				$i = 0;
				for($i = 0; $i < sizeof ( $absenderKonten ); $i ++) {
					echo "<option";
					if (isset ( $_POST ['zielKonto'] ) and $absenderKonten [$i]->id == $_POST ['zielKonto']) {
						echo " selected ";
					} elseif(isset($_GET['konto']) AND $absenderKonten [$i]->id == $_GET['konto']) {
						echo " selected ";
					} else {
						echo "";
					}
					echo " value='" . $absenderKonten [$i]->id . "'>" . $absenderKonten [$i]->konto . "</option>";
				}
				echo "</select></td></tbody>";
				
				echo "<tbody><td>Absender: </td><td><select name='absenderKonto'>";
				$i = 0;
				for($i = 0; $i < sizeof ( $absenderKonten ); $i ++) {
					
					echo "<option ";
					if (isset ( $_POST ['absenderKonto'] ) and $absenderKonten [$i]->id == $_POST ['absenderKonto']) {
						echo " selected ";
					} elseif(isset($_GET['konto']) AND $absenderKonten [$i]->id == $_GET['konto']) {
						echo " selected ";
					} else {
						echo "";
					}
					echo "value='" . $absenderKonten [$i]->id . "'>" . $absenderKonten [$i]->konto . "</option>";
				}
				echo "</select></td></tbody>";
				
				// Date Variable füllen:
				$timestamp = time ();
				if (isset ( $_POST ['dateUeberweisung'] )) {
					$date = $_POST ['dateUeberweisung'];
				} else {
					$date = date ( "Y-m-d", $timestamp );
				}
				
				echo "<tbody><td>Betrag</td><td><input type=text value='' placeholder='Betrag' name='valueUeberweisung'/></td></tbody>";
				echo "<tbody><td>Datum</td><td><input type=date value='$date' placeholder='Datum' name='dateUeberweisung'/></td></tbody>";
				echo "<tbody><td colspan='2'><input type=submit name=sendnewUeberweisung value='Absenden' /></td></tbody>";
				
                # WEITERE BUCHUNGEN DURCHFÜHREN
                
                # ENTWEDER PER ANZAHL DER MONATE
                echo "<tbody><td colspan='2'>";
                echo "<input type=checkbox name=weiterenumber value='1' class='checkbox' >";
				echo "<label for='weiterenumber'>weitere Monate ausführen: </label>";
                echo "</td></tbody>";
                echo "<tbody><td colspan='2'>";
                echo "<input type=number name=monthweitere value='' /> <span><br>(z.B: 1 bedeutet, dass die Buchung auch im nächsten Monat ausgeführt wird)</span>";
                echo "</td></tbody>";
                
                # ODER ALS GENAUES DATUM
                echo "<tbody><td colspan='2'>";
				echo "<input type=checkbox name=weitere value='1' class='checkbox' />";
				echo "<label for='weitere'>Diese Überweisung für folgende weitere Tage durchführen</label>";
				echo "</td></tbody>";
				$j = 0;
				
				for($j = 0; $j < $anzahlWeitereFelder; $j ++) {
					echo "<tbody><td colspan='2'><input type=date name=dates[$j] value='' placeholder='$j ...' /></td></tbody>";
				}
				echo "</table>";
				echo "</form>";
				echo "</div>";
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
		
		$ergebnis = $this->getObjektInfo ( $umsaetze );
		
		if (isset ( $ergebnis [0]->id )) {
			return $ergebnis;
		} else {
			return false;
		}
	}
	private $konto;
	
	/**
	 * Zeigt eine Auswahl an Jahre an, womit die Buchungen innerhalb der Konten selektiert werden können.
	 */
	function showBuchungRange() {
		
	#	print_r($_SESSION);
		
		if(isset($_POST["yearRange"])) {
			$_SESSION["anzuzeigendesjahr"] = $_POST["yearRange"];
		}
		
		if(!isset($_SESSION["anzuzeigendesjahr"])) {
			$_SESSION["anzuzeigendesjahr"] = 0;
		}
		echo "<form method=post>";
		for($i = 2013 ; $i < 2020 ; $i++) {
			echo "<input onChange='this.form.submit();' type='radio' value='$i' name='yearRange' id='$i' onclick=''";
			
			if (!isset($_SESSION["anzuzeigendesjahr"]) OR $_SESSION["anzuzeigendesjahr"] != $i) {
				echo " unchecked";
			} else {
				echo " checked";
			}
			
			echo " />";
			
			echo "<label for='$i'>$i</label>"; 
		}
		
		# Alle
		echo "<input onChange='this.form.submit();' type='radio' value='0' name='yearRange' id='0' onclick=''";
			
		if (!isset($_SESSION["anzuzeigendesjahr"]) OR $_SESSION["anzuzeigendesjahr"] != 0) {
			echo " unchecked";
		} else {
			echo " checked";
		}
			
		echo " />";
			
		echo "<label for='0'>Alles anzeigen</label>";
		echo "</form>";
	}
	
	/**
	 * Zeigt eine &Uuml;bersicht aller Konten des besitzers in einer Auflistung an.
	 * Beschreibung Konto-Art:
	 * 0 normales Konto
	 * 1 gr&uuml;nes Konto
	 * 2 Konto ohne Saldo
	 * 
	 * @param unknown $besitzer        	
	 */
	function showKontoUebersicht($besitzer) {
		$konten = $this->getAllKonten ( $besitzer );
		
		// Wenn es Konten gibt:
		if (isset ( $konten [0]->id )) {
			// Aktive Konten
			echo "<div class='innerBody'>";
			echo "<h3>Aktive Konten</h3>";
			for($i = 0; $i < sizeof ( $konten ); $i ++) {
				if ($konten [$i]->aktiv == 1) {
					$select2 = "SELECT sum(umsatzWert) as summe FROM finanzen_umsaetze WHERE konto=" . $konten [$i]->id . " AND datum <= CURDATE()";
					$umsaetze = $this->getObjektInfo ( $select2 );
					if ($konten [$i]->art == 1) {
						$mark = "Guthabenkonto";
					} elseif($konten [$i]->art == 2) {
						$mark = "nolimit";
					} else {
						$mark = "Konto";
					}
					echo "<div class='$mark'>";
					echo "<p>" .$konten [$i]->id. ": <a href='index.php?konto=" . $konten [$i]->id . "'>" . $konten [$i]->konto . "</a><br>";
					echo "<a href='detail.php?editKonto=" . $konten [$i]->id . "'>Einstellungen</a><br>";
					if($konten[$i]->art == 2) {
						echo "x</p>";
					} else {
						$summe = $umsaetze [0]->summe + 0;
						echo $summe . " €</p>";
					}
					
					echo "</div>";
				}
			}
			
			echo "</div>";
			$i = 0;
			
			// Inaktive Konten
			echo "<div class='innerBody'>";
			echo "<h3>Nicht sichtbare Konten</h3>";
			for($i = 0; $i < sizeof ( $konten ); $i ++) {
				$select2 = "SELECT sum(umsatzWert) as summe FROM finanzen_umsaetze WHERE konto=" . $konten [$i]->id . " AND datum <= CURDATE()";
				$umsaetze = $this->getObjektInfo ( $select2 );
				if ($konten [$i]->aktiv == 0) {
					if ($konten [$i]->art == 1) {
						$mark = "Guthabenkonto";
					} elseif($konten [$i]->art == 2) {
						$mark = "nolimit";
					} else {
						$mark = "Konto";
					}
					echo "<div class='$mark'>";
					echo "<p>" .$konten [$i]->id. ": <a href='index.php?konto=" . $konten [$i]->id . "'>" . $konten [$i]->konto . "</a><br>";
					echo "<a href='detail.php?editKonto=" . $konten [$i]->id . "'>Einstellungen</a><br>";
					if($konten[$i]->art == 2) {
						echo "x</p>";
					} else {
						$summe = $umsaetze [0]->summe + 0;
						echo $summe . " €</p>";
					}
					echo "</div>";
				}
			}
			echo "</div>";
		}
	}
	
	/**
	 * Gibt alle Konten des Besitzers zurück.
	 * 
	 * @param unknown $besitzer        	
	 * @return object|boolean
	 */
	function getAllKonten($besitzer) {
		$konten = $this->getObjektInfo ( "SELECT id, timestamp, konto, besitzer, aktiv, art, notizen FROM finanzen_konten WHERE besitzer = '$besitzer' ORDER BY konto" );
		
		if (isset ( $konten )) {
			return $konten;
		} else {
			return false;
		}
	}
	
	/**
	 * Ermöglicht die Erstellung eines neuen Kontos.
	 */
	function showCreateNewKonto($besitzer) {
		if (isset ( $_GET ['neuesKonto'] )) {
			echo "<div class='newChar'><form method=post><input type=text name=newKonto value='' placeholder='Kontoname' />";
			echo "<input type=submit name=insertNewKonto value=Speichern />";
			echo "</form></div>";
			
			if (isset ( $_POST ['insertNewKonto'] ) and $this->userHasRight ( "18", 0 ) == true) {
				$konto = $_POST ['newKonto'];
				if ($konto != "") {
					if ($this->createNewKonto ( $besitzer, $konto ) == true) {
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
	 * 
	 * @param unknown $besitzer        	
	 * @param unknown $kontoname        	
	 * @return boolean
	 */
	function createNewKonto($besitzer, $kontoname) {
		$query = "INSERT INTO finanzen_konten (besitzer,konto,aktiv) VALUES ('$besitzer','$kontoname',1) ";
		
		if ($this->sql_insert_update_delete ( $query ) == true) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Ermöglicht das editieren von Konten
	 * 
	 * @param unknown $besitzer        	
	 */
	function showEditKonto($besitzer) {
		if (isset ( $_POST ['absenden'] ) and isset ( $_GET ['editKonto'] ) and isset ( $_POST ['kontoname'] ) and isset ( $_POST ['notizen'] ) and isset ( $_POST ['art'] )) {
			if ($_POST ['kontoname'] != "") {
				$name = $_POST ['kontoname'];
				$id = $_GET ['editKonto'];
				$art = $_POST ['art'];
				$notizen = strip_tags ( stripslashes ( $_POST ['notizen'] ) );
				if (isset ( $_POST ['aktiv'] )) {
					$aktiv = 1;
				} else {
					$aktiv = 0;
				}
				
				$update = "UPDATE finanzen_konten SET art=$art, konto='$name', aktiv=$aktiv, notizen='$notizen' WHERE besitzer = $besitzer AND id = $id";
				
				if ($this->sql_insert_update_delete ( $update )) {
					echo "<p class='erfolg'>Konto gespeichert.</p>";
				} else {
					echo "<p class='meldung'>Es gab einen Fehler beim speichern der Informationen.</p>";
				}
			}
		}
		
		if (isset ( $_GET ['editKonto'] )) {
			$id = $_GET ['editKonto'];
						
			$select = "SELECT * FROM finanzen_konten WHERE besitzer = $besitzer AND id = $id";
			
			$kontoInfo = $this->getObjektInfo ( $select );
			
			if (isset ( $kontoInfo [0]->aktiv ) and $kontoInfo [0]->aktiv == 0) {
				$checked = "";
			} else {
				$checked = "checked";
			}
			
			if (! isset ( $kontoInfo [0]->id )) {
				echo "<p class='meldung'>Dieses Konto gibt es nicht, oder du hast keinen Zugriff darauf.</p>";
				exit ();
			}
			
			echo "<div class='newCharWIDE'>";
			echo "<h2>Detailinformationen von " . $kontoInfo [0]->konto . "</h2>";
			echo "<a class='rightBlueLink' href='index.php?konto=" . $kontoInfo [0]->id . "'>Gehe zu diesem Konto</a>";
			
			echo "<form method=post>";
			echo "<table>";
			echo "<tr><td>Name: </td><td><input type=text value='" . $kontoInfo [0]->konto . "' name=kontoname /></td>";
			echo "<td><input value=1 type=checkbox " . $checked . " name=aktiv /><label for=aktiv>Aktiv</label>" . "</td>";
			
			echo "<td>" . "<input type=number value=" . $kontoInfo [0]->art . " name=art placeholder=Kontoart /><td>Kontoart</td>" . "</tr>";
			
			echo "<tr><td><input type=submit name=absenden value=Speichern /></td></tr>";
			
			echo "</table>";
			
			echo "<div>Beschreibung Kontoart: 0 = normales Konto, 1 = Konto mit gr&uuml;ner Umrandung, 2 = Konto ohne Saldo</div>";
			
			echo "<div class='scrollContainer'>";
			echo "<p>Übersicht vorhandener Buchungen</p>";
			echo "<table class='flatnetTable'>";
			
			$selectKonten = "SELECT *
			, year(datum) as jahr
			, month(datum) as monat
			, day(datum) as tag 
			FROM finanzen_umsaetze 
			WHERE besitzer = $besitzer 
			AND konto = $id 
			GROUP BY gegenkonto";
			$getKonten = $this->getObjektInfo ( $selectKonten );
			
			for($j = 0; $j < sizeof ( $getKonten ); $j ++) {
				
				# Schauen, ob das Jahr gefiltert wurde:
				if($_SESSION["anzuzeigendesjahr"] > 0) {
					
					$select2 = "SELECT *
					, year(datum) as jahr
					, month(datum) as monat
					, day(datum) as tag
					FROM finanzen_umsaetze
					WHERE besitzer = $besitzer
					AND konto = $id
					AND gegenkonto = " . $getKonten [$j]->gegenkonto . "
					AND year(datum) = " .$_SESSION['anzuzeigendesjahr']. "
					ORDER BY datum ASC";
					
				} else {
					$select2 = "SELECT *
					, year(datum) as jahr
					, month(datum) as monat
					, day(datum) as tag
					FROM finanzen_umsaetze
					WHERE besitzer = $besitzer
					AND konto = $id
					AND gegenkonto = " . $getKonten [$j]->gegenkonto . "
					ORDER BY datum ASC";
				}
				
				// Buchungen dieses Kontos bekommen:
				
				$getKontoBuchungen = $this->getObjektInfo ( $select2 );
				
				$getKontoName = $this->getObjektInfo ( "SELECT * FROM finanzen_konten WHERE id=" . $getKonten [$j]->gegenkonto . " " );
				$kontoname = $getKontoName [0]->konto;
				
				echo "<thead>";
				echo "<td colspan=10>";
				echo $kontoname;
				echo "</td>";
				echo "</thead>";
				
				for($i = 0; $i < sizeof ( $getKontoBuchungen ); $i ++) {
					
					$timestamp = time ();
					$heute = date ( "Y-m-d", $timestamp );
					if ($getKontoBuchungen [$i]->datum < $heute and isset ( $getKontoBuchungen [$i + 1]->datum ) and $getKontoBuchungen [$i + 1]->datum >= $heute) {
						$heute = date ( "d.m.Y", $timestamp );
						echo "<tbody id='error'><td colspan='7'><a name='heute'>Heute ist der $heute</a> nächster Umsatz: " . $getKontoBuchungen [$i + 1]->umsatzName . "</td></tbody>";
					}
					
					echo "<tbody>";
					$gegenkonto = $this->getObjektInfo ( "SELECT * FROM finanzen_konten WHERE id = '" . $getKontoBuchungen [$i]->gegenkonto . "'" );
					echo "<td><a href='index.php?konto=" . $getKontoBuchungen [$i]->konto . "&jahr=" . $getKontoBuchungen [$i]->jahr . "&monat=" . $getKontoBuchungen [$i]->monat . "&selected=" . $getKontoBuchungen [$i]->buchungsnr . "'>" . $getKontoBuchungen [$i]->buchungsnr . "</a></td>
						<td>" . $getKontoBuchungen [$i]->umsatzName . "</td>
						<td>" . $getKontoBuchungen [$i]->umsatzWert . "</td>
					";
				#	echo "<td>AN <a href='?editKonto=" . $gegenkonto [0]->id . "'>" . $gegenkonto [0]->konto . "</a></td>";
					echo "<td>" . $getKontoBuchungen [$i]->datum . "</td>
						<td>" . "<a href='?umbuchungBuchNr=" . $getKontoBuchungen [$i]->buchungsnr . "&editKonto=$id'>Umbuchen</a>" . "</td>";
					echo "</tbody>";
				}
			}
			
			if (! isset ( $getKontoBuchungen [0]->buchungsnr )) {
				echo "<tbody><td>Es gibt keine Buchungen auf diesem Konto.<a href='?deleteKonto=" . $kontoInfo [0]->id . "' class='rightRedLink'>Konto löschen</a></td></tbody>";
			}
			
			echo "</table>";
			
			echo "</div>";
			echo "<textarea name=notizen>" . $kontoInfo [0]->notizen . "</textarea>";
			echo "</form>";
			echo "</div>";
		}
	}
	
	/**
	 * Ändert das Konto einer Buchungsnummer
	 * 
	 * @param unknown $buchungsnr        	
	 */
	function umbuchungDurchfuehren($buchungsnr) {
		if (isset ( $_POST ['absenden'] )) {
			if (isset ( $_POST ['gutschriftKonto'] ) and isset ( $_POST ['absenderKonto'] )) {
				$absenderKonto = $_POST ['absenderKonto'];
				$gutschriftKonto = $_POST ['gutschriftKonto'];
				$getBuchungInfos = $this->getObjektInfo ( "SELECT * FROM finanzen_umsaetze WHERE buchungsnr = $buchungsnr" );
				
				echo "Neues Absenderkonto $absenderKonto, neues Gutschriftkonto = $gutschriftKonto";
				
				for($i = 0; $i < sizeof ( $getBuchungInfos ); $i ++) {
					// Änderung für den ABSENDER
					if ($getBuchungInfos [$i]->umsatzWert < 0) {
						$update = "UPDATE finanzen_umsaetze SET konto=$absenderKonto, gegenkonto=$gutschriftKonto WHERE buchungsnr=$buchungsnr AND umsatzWert < 0";
						
						if ($this->sql_insert_update_delete ( $update ) == true) {
							echo "<p class='erfolg'>Umbuchung erfolgt</p>";
						} else {
							echo "<p class='meldung'>Es gab einen Fehler.</p>";
						}
					}
					
					// ÄNDERUNG für die GUTSCHRIFT
					if ($getBuchungInfos [$i]->umsatzWert > 0) {
						
						$update = "UPDATE finanzen_umsaetze SET konto=$gutschriftKonto, gegenkonto=$absenderKonto WHERE buchungsnr=$buchungsnr AND umsatzWert > 0";
						
						if ($this->sql_insert_update_delete ( $update ) == true) {
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
	 * Zeigt eine Auswahl zum Ändern deiner Buchungsnummer bezügl.
	 * der betreffenden Konten.
	 * 
	 * @param unknown $besitzer        	
	 */
	function UmbuchungUmsatz($besitzer) {
		if (isset ( $_GET ['umbuchungBuchNr'] )) {
			$buchungsnr = $_GET ['umbuchungBuchNr'];
			
			// Prüfen ob Nummer existiert:
			$query = "SELECT * FROM finanzen_umsaetze WHERE buchungsnr = '$buchungsnr' AND besitzer = $besitzer";
			if ($this->objectExists ( $query ) == true) {
				$buchInfos = $this->getObjektInfo ( $query );
				
				echo "<div class='newChar'><form method=post>";
				
				$this->umbuchungDurchfuehren ( $buchungsnr );
				
				echo "<h2>Umsatzinformationen</h2>";
				for($i = 0; $i < sizeof ( $buchInfos ); $i ++) {
					if ($buchInfos [$i]->umsatzWert > 0) {
						echo "<table class='flatnetTable'>";
						echo "<thead><td colspan=10>GUTSCHRIFT</td></thead>";
						echo "<tbody><td>" . $buchInfos [$i]->umsatzName . " " . $buchInfos [$i]->umsatzWert . "</td></tbody>";
						echo "<tbody>";
						$selectKonten = "SELECT * FROM finanzen_konten WHERE besitzer = $besitzer";
						$konten = $this->getObjektInfo ( $selectKonten );
						echo "<td>";
						echo "<select name=gutschriftKonto />";
						for($j = 0; $j < sizeof ( $konten ); $j ++) {
							echo "<option value='" . $konten [$j]->id . "'";
							
							if ($buchInfos [$i]->konto == $konten [$j]->id) {
								echo " selected ";
							}
							echo ">";
							echo $konten [$j]->konto;
							echo "</option>";
						}
						echo "</select>";
						echo "</td>";
						echo "</tbody>";
					}
					
					if ($buchInfos [$i]->umsatzWert < 0) {
						echo "<table class='flatnetTable'>";
						echo "<thead><td colspan=10>ABSENDER</td></thead>";
						echo "<tbody><td>" . $buchInfos [$i]->umsatzName . " " . $buchInfos [$i]->umsatzWert . "</td></tbody>";
						
						echo "<tbody>";
						$selectKonten = "SELECT * FROM finanzen_konten WHERE besitzer = $besitzer";
						$konten = $this->getObjektInfo ( $selectKonten );
						echo "<td>";
						echo "<select name=absenderKonto />";
						
						for($j = 0; $j < sizeof ( $konten ); $j ++) {
							echo "<option value='" . $konten [$j]->id . "'";
							if ($buchInfos [$i]->konto == $konten [$j]->id) {
								echo " selected ";
							}
							echo ">";
							echo $konten [$j]->konto;
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
			} else {
				echo "<p class='info'>Diese Buchung gibt es nicht, oder du hast keinen Zugriff auf diese Information.</p>";
			}
		}
	}
	
	/**
	 * Ermöglicht das löschen von Konten.
	 * Monatsabschlüsse und Jahresabschlüsse werden ebenfalls gelöscht.
	 * @param unknown $besitzer        	
	 */
	function showDeleteKonto($besitzer) {
		if (isset ( $_GET ['deleteKonto'] )) {
			$konto = $_GET ['deleteKonto'];
			$select = "SELECT id, besitzer FROM finanzen_konten WHERE besitzer=$besitzer AND id=$konto";
			$select2 = "SELECT id, besitzer, konto FROM finanzen_umsaetze WHERE besitzer=$besitzer AND konto=$konto";
			if ($this->objectExists($select) == true and $this->objectExists($select2) == false) {
				
				$delquery="DELETE FROM finanzen_konten WHERE besitzer=$besitzer AND id=$konto";
				$delAbschluesseQuery="DELETE FROM finanzen_monatsabschluss WHERE konto=$konto";
				$delAbschluesseQuery2="DELETE FROM finanzen_jahresabschluss WHERE konto=$konto";
				
				if ($this->sql_insert_update_delete ($delquery) == true) {
					# Monatsabschlüsse löschen ...
					$this->sql_insert_update_delete ($delAbschluesseQuery);
					# Jahresabschlüsse löschen ...
					$this->sql_insert_update_delete ($delAbschluesseQuery2);
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
		if (isset ( $_GET ['Salden'] )) {
			echo "<div>";
			$select = "SELECT * FROM finanzen_konten WHERE besitzer=$besitzer";
			$konten = $this->getObjektInfo ( $select );
			
			for($i = 0; $i < sizeof ( $konten ); $i ++) {
				$select2 = "SELECT sum(umsatzWert) as summe FROM finanzen_umsaetze WHERE konto=" . $konten [$i]->id;
				$umsaetze = $this->getObjektInfo ( $select2 );
				echo "<div class='newChar'>";
				echo "<h2>" . $konten [$i]->konto . "</h2>";
				echo "Summe: " . $umsaetze [0]->summe;
				echo "</div>";
			}
			echo "</div>";
		}
	}
	private $abschluesse;
	
	/**
	 * Summiert die Umsätze der vergangenen Monate
	 * 
	 * @param unknown $besitzer        	
	 * @param unknown $jetzigerMonat        	
	 * @param unknown $jahr        	
	 * @param unknown $konto        	
	 * @return number
	 */
	function getMonatsabschluesseBISJETZT($besitzer, $jetzigerMonat, $jahr, $konto) {
		$summierendeMonate = $jetzigerMonat - 1;
		$summe = 0;
		for($i = 1; $i <= $summierendeMonate; $i ++) {
			$summe = $summe + $this->getMonatsabschluss ( $besitzer, $konto, $i, $jahr );
		}
		
		return $summe;
	}
    
    /**
     * 
     */
	function showJahresabschlussIfAvailable() {
        if(isset($_GET['konto']) AND isset($_GET['jahr'])) {
            $konto = $_GET['konto'];
            $jahr= $_GET['jahr'];
            $besitzer = $this->GetUserID($_SESSION['username']);
            $query = "SELECT besitzer, jahr, wert, konto FROM finanzen_jahresabschluss WHERE besitzer=$besitzer AND $konto=$konto AND jahr=$jahr";
            $jahresabschlussFuerDiesesJahr=$this->getObjektInfo($query);
            
            if(isset($jahresabschlussFuerDiesesJahr[0]->wert) AND $konto > 0) {
                echo "<div class='dezentInfo'>";
                    echo "<p>" ."Für dieses Jahr gibt es einen Jahresabschluss, der Endwert beträgt: " .$jahresabschlussFuerDiesesJahr[0]->wert. " €". "</p>";
                echo "</div>";
            }
            
            
        }
    }
	/**
	 * Gibt die Summe der Jahresabschlüsse wieder.
	 * 
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
		$summe = $this->getObjektInfo ( $query );
		
		if (isset ( $summe [0]->summe )) {
			$summeWert = $summe [0]->summe;
		} else {
			$summeWert = 0;
		}
		
		return $summeWert;
	}
	private $monat;
	
	/**
	 * Gibt den Monatsabschluss aus den angegebenen Daten wieder.
	 * 
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
		
		$monatsabschluss = $this->getObjektInfo ( $query );
		
		if (isset ( $monatsabschluss [0]->year )) {
			return $monatsabschluss [0]->wert;
		} else {
			return false;
		}
	}
	
	/**
	 * gibt den Saldo des Monats im aktuellen Jahr, dieses Besitzers, zu diesem Konto zurück.
	 * 
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
		
		$umsaetze = $this->getObjektInfo ( $query );
		
		// SUMME bilden
		$summe = 0;
		for($i = 0; $i < sizeof ( $umsaetze ); $i ++) {
			$summe = $summe + $umsaetze [$i]->umsatzWert;
		}
		
		return $summe;
	}
	
	/**
	 * Erstellt Monatsabschlüsse aus alten Einträgen.
	 */
	function erstelleMonatsabschluesseFromOldEintraegen() {
		$besitzer = $this->getUserID ( $_SESSION ['username'] );
		
		// jetzigen Monat ziehen:
		$currentMonat = date ( "m" );
		$currentJahr = date ( "Y" );
		$abzueglicheMonate = $currentMonat - 1;
		$anzahlMonate = 12;
		
		// Konten des Nutzers ziehen:
		$konten = $this->getAllKonten ( $besitzer );
		
		// Prüfen, ob der Benutzer konten hat.
		if (! isset ( $konten [0]->id )) {
			echo "<p class='info'>Du hast noch keine Konten, du brauchst mindestens zwei Konten. Klicke <a href='?'>hier</a> um eine Standardkonfiguration zu erstellen</p>";
			exit ();
		}
		
		// Anzahl Monatsabschlüsse zählen
		$counter = 0;
		
		// Pro Konto durchlaufen
		for($i = 0; $i < sizeof ( $konten ); $i ++) {
			// prüfen, ob der aktuelle Monatsabschluss existiert.
			
			// Pro vergangenen Monat durchlaufen:
			for($gepruefterMonat = 1; $gepruefterMonat < $currentMonat; $gepruefterMonat ++) {
				if ($this->getMonatsabschluss ( $besitzer, $konten [$i]->id, $gepruefterMonat, $currentJahr ) == false) {
					
					$saldo = $this->getSaldoFromMonat ( $besitzer, $konten [$i]->id, $gepruefterMonat, $currentJahr );
					
					$konto = $konten [$i]->id;
					$query = "INSERT INTO finanzen_monatsabschluss (besitzer, monat, year, wert, konto) 
					VALUES 
					('$besitzer','$gepruefterMonat','$currentJahr','$saldo','$konto')";
					
					if ($this->sql_insert_update_delete ( $query ) == true) {
						$counter = $counter + 1;
					} else {
						echo "<p class='meldung'>Es gab einen Fehler beim erstellen des Monatsabschlusses $currentJahr, im Monat $gepruefterMonat.</p>";
					}
				}
			}
		}
		
		if ($counter > 0) {
			echo "<p class='erfolg'>Es wurden $counter Monatsabschlüsse erstellt.</p>";
		}
	}
	private $jahr;
	
	/**
	 * Gibt zurück, ob ein Jahresabschluss vorhanden ist, wenn ja, dann wird der wert zurückgegeben.
	 * 
	 * @param unknown $besitzer        	
	 * @param unknown $konto        	
	 * @param unknown $jahr        	
	 * @return boolean
	 */
	function getJahresabschluss($besitzer, $konto, $jahr) {
		$query = "SELECT * FROM finanzen_jahresabschluss WHERE konto = '$konto' AND besitzer = '$besitzer' AND jahr = '$jahr'";
		
		$jahresabschluss = $this->getObjektInfo ( $query );
		
		if (isset ( $jahresabschluss [0]->jahr )) {
			return $jahresabschluss [0]->wert;
		} else {
			return false;
		}
	}
	
	/**
	 * gibt den Saldo des Jahres, dieses Besitzers zu diesem Konto zurück.
	 * 
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
		
		$umsaetze = $this->getObjektInfo ( $query );
		
		// SUMME bilden
		$summe = 0;
		for($i = 0; $i < sizeof ( $umsaetze ); $i ++) {
			$summe = $summe + $umsaetze [$i]->umsatzWert;
		}
		
		return $summe;
	}
	
	/**
	 * Gibt das Erstellungsjahr des aktuellen Kontos zurück.
	 * 
	 * @param unknown $besitzer        	
	 * @param unknown $kontonummer        	
	 * @return boolean
	 */
	function getErstellungsdatumKonto($besitzer, $kontonummer) {
		// frühesten Eintrag im Konto finden:
		$frueherEintrag = $this->getObjektInfo ( "SELECT min(year(datum)) as min FROM finanzen_umsaetze
				WHERE besitzer = '$besitzer' AND konto = '$kontonummer'" );
		
		if (isset ( $frueherEintrag [0]->min )) {
            if($frueherEintrag[0]->min < 2010) {
                return 2010;
            } else {
                return $frueherEintrag [0]->min;
            }
		} else {
			$frueherEintrag = date ( "Y" );
			return $frueherEintrag;
		}
	}
	
	/**
	 * Überprüft alle Jahresabschlüsse auf Richtigkeit und löscht diese, fall nötig.
     * Eine andere Methode erstellt die fehlenden Abschlüsse direkt wieder.
	 * 
	 * @param unknown $besitzer        	
	 * @param unknown $konto        	
	 */
	function checkJahresabschluss() {
        if(isset($_GET['konto'])) {
            if($_GET['konto'] > 0) {
                $besitzer = $this->getUserID ( $_SESSION ['username'] );
		
                // Konten des Nutzers ziehen:
                $kontenDesNutzers = $this->getAllKonten ( $besitzer );

                for($i = 0; $i < sizeof ( $kontenDesNutzers ); $i ++) {

                    #if (isset ( $_GET ['checkJahresabschluesse'] )) {

                        $konto = $kontenDesNutzers[$i]->id;
                        $kontoname = $kontenDesNutzers[$i]->konto;

                        $currentYear = date ( "Y" );
                        // Prüfen, ob Jahresabschluss KORREKT ist.
                        $jahresabschlusswert = $this->getObjektInfo("SELECT sum(wert) as summe FROM finanzen_jahresabschluss WHERE besitzer=$besitzer AND konto=$konto");

                        $tatsaechlicheSumme = $this->getObjektInfo("SELECT sum(umsatzWert) as summe FROM finanzen_umsaetze WHERE besitzer = $besitzer AND konto = $konto AND year(datum)<$currentYear");

                        if (isset ( $jahresabschlusswert [0]->summe )) {
                            if ($jahresabschlusswert [0]->summe != $tatsaechlicheSumme [0]->summe) {
                            	$differenz = $jahresabschlusswert [0]->summe - $tatsaechlicheSumme [0]->summe;
                                echo "<p class='dezentInfo'>Achtung: Konto $kontoname (Nr. $konto), Jahresabschluss korrigiert, Differenz: $differenz. ";

                                if ($this->sql_insert_update_delete ("DELETE FROM finanzen_jahresabschluss WHERE besitzer=$besitzer AND konto=$konto") == true) {
                                    echo "<br>Abschluss gel&ouml;scht.</p>";
                                } else {
                                	echo "<br>falscher Abschluss kann nicht gel&ouml;scht werden.</p>";
                                }
                            } else {
                                #echo "<p class='dezentInfo'>Für Konto $konto gibt es keine Fehler.</p>";
                            }
                        } else {
                            #echo "<p class='erfolg'>Für Konto $konto gibt es scheinbar keinen Abschluss.</p>";
                        }
                    #}
                }
            } 
        }
		
	}
	
	/**
	 * Erstellt Jahresabschlüsse aus den alten Einträgen.
	 */
	function erstelleJahresabschluesseFromOldEintraegen() {
		$besitzer = $this->getUserID ( $_SESSION ['username'] );
		
		// Konten des Nutzers ziehen:
		$kontenDesNutzers = $this->getAllKonten ( $besitzer );
		
		// Prüfen, ob der Benutzer konten hat.
		if (! isset ( $kontenDesNutzers [0]->id )) {
			echo "<p class='info'>Du hast noch keine Konten, du brauchst mindestens zwei Konten. Klicke <a href='?createStandardConfig'>hier</a> um eine Standardkonfiguration zu erstellen</p>";
			
			if (isset ( $_GET ['createStandardConfig'] )) {
				if($this->userHasRight(18, 0) == true) {
					if ($this->objectExists ( "SELECT * FROM finanzen_konten WHERE besitzer = $besitzer AND konto = 'Hauptkonto'" ) == false) {
						if ($this->sql_insert_update_delete ( "INSERT INTO finanzen_konten (konto, besitzer, aktiv) VALUES ('Hauptkonto','$besitzer', 1)" ) == true) {
							echo "<p class='erfolg'>Ein Hauptkonto wurde erstellt.</p>";
						}
					}
					
					if ($this->objectExists("SELECT * FROM finanzen_konten WHERE besitzer = $besitzer AND konto = 'Ausgaben'") == false) {
						if ($this->sql_insert_update_delete ("INSERT INTO finanzen_konten (konto, besitzer, aktiv) VALUES ('Ausgaben','$besitzer', 0)" ) == true) {
							echo "<p class='erfolg'>Ein Ausgabenkonto wurde erstellt.</p>";
						}
					}
				}
			}
			
			exit ();
		}
		
		// Jetziges Jahr:
		$currentYear = date ( "Y" );
		
		// Pro Konto durchführen:
		for($i = 0; $i < sizeof ( $kontenDesNutzers ); $i ++) {
			
			echo "<table class='flatnetTable'>";
			// Ersten Eintrag im Konto suchen:
			$erstellungsDatumKonto = $this->getErstellungsdatumKonto ( $besitzer, $kontenDesNutzers [$i]->id );
			
			if ($erstellungsDatumKonto < $currentYear) {
				$differnz = $currentYear - $erstellungsDatumKonto;
				$geprueftesJahr = $erstellungsDatumKonto;
				
				// Für Anzahl der Jahre:
				for($j = 0; $j < $differnz; $j ++) {
					if ($this->getJahresabschluss ( $besitzer, $kontenDesNutzers [$i]->id, $geprueftesJahr ) == false) {
						$saldo = $this->getSaldoFromYear ( $besitzer, $kontenDesNutzers [$i]->id, $geprueftesJahr );
						echo "<tbody><td>Jahresabschluss f&uuml;r <strong>" . $kontenDesNutzers [$i]->konto . "(Jahr: $geprueftesJahr, Saldo: $saldo)</strong> wird erstellt ... </td></tbody>";
						
						$konto = $kontenDesNutzers [$i]->id;
						$query = "INSERT INTO finanzen_jahresabschluss (besitzer, jahr, wert, konto) VALUES ('$besitzer','$geprueftesJahr','$saldo','$konto')";
						
						if ($this->sql_insert_update_delete ( $query ) == true) {
							#echo "<p class='dezentInfo'>Der Jahresabschluss wurde erstellt.</p>";
							// alte Monatsabschlüsse löschen:
							
							if ($this->sql_insert_update_delete ( "DELETE FROM finanzen_monatsabschluss WHERE besitzer = $besitzer AND year < $currentYear" ) == true) {
								echo "<tbody><td>";
								echo "<p class='info'>Monatsabschlüsse gelöscht</p>";
								echo "</td></tbody>";
							} else {
								# echo "<p class='meldung'>Monatsabschlüsse konnten nicht gelöscht werden.</p>";
							}
						} else {
							echo "<tbody><td>";
							echo "<p class='meldung'>Es gab einen Fehler.</p>";
							
						}
						
						
						
						$geprueftesJahr = $geprueftesJahr + 1;
					}
				}
			}
			echo "</table>";
		}
	}
}

?>