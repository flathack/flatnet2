<?php
/**
 * @history Steven 19.08.2014 angelegt.
 * @history Steven 21.08.2014 Änderung der Methoden.
 * @author Steven
 * Gibt die richtige Ausgabe aus, wenn der richtige GET Selector angewählt wird.
 * Prüft bereits beim aufrufen die GET Variablen.
 */
include 'objekt/functions.class.php';
class control extends functions {
	
	/**
	 * Ließt den GET Wert aus und leitet an die entsprechende Methode weiter.
	 */
	function contentSelector($action) {
		/**
		 * Nichts selektiert
		 */
		if ($action == "") {
			$this->newBenutzerFunction ();
			$this->showBenutzer ();
			$this->newBenutzerEingabe ();
			$this->bearbBenutzerEingabe ();
			$this->bearbBenutzerFunction ();
			$this->codeVerwaltung ();
			$this->Aufraeumen ();
		}
		
		/**
		 * Benutzerverwaltung
		 */
		if ($action == 1) {
			$this->newBenutzerFunction ();
			$this->showBenutzer ();
			$this->newBenutzerEingabe ();
			$this->bearbBenutzerEingabe ();
			$this->bearbBenutzerFunction ();
			$this->codeVerwaltung ();
			$this->Aufraeumen ();
		}
		
		/*
		 * Vorschlaege anzeigen
		 */
		if ($action == 2) {
			$this->adminVorschlaegeDELETE_Eintraege ();
			$this->adminVorschlaege ();
		}
		
		if ($action == 3) {
			
			if (isset ( $_GET ['globalQuery'] )) {
				$this->globalQueryBox ();
			}
			$this->objektControl ();
		}
		
		/*
		 * Rechtesystem
		 */
		if ($action == 4) {
			
		}
		
		if ($action == 5) {
			$this->newForumCategory ();
			$this->alternateForumCategory ();
			$this->showForumCategories ();
			$this->setUserForumRights ();
		}
		
		if ($action == 6) {
			$this->rechteverwaltung();
			$this->rechtekategorienVerwaltung();
		}
	}
	
	/**
	 * Zeigt alle Benutzer aus der Tabelle "benutzer" an.
	 */
	function showBenutzer() {
		
		if($this->userHasRight("37", 0) == true) {
		
			echo "<table class='flatnetTable'>";
			echo "<thead>
					<td>ID</td>
					<td>Benutzername</td>
					<td>Titel</td>
					<td><a href='?action=4'>Rechte</a>
					<td><a href='?action=5'>Forum Rechte</a>
					</td><td>Versuche</td>
					<td>Optionen</td></thead>";
			$benutzerliste = "SELECT timestamp
					, id
					, Name
					, titel
					, rights
					, forumRights
					, versuche
					, day(timestamp) AS tag
					, month(timestamp) AS monat
					, year(timestamp) AS jahr
					FROM benutzer
					ORDER BY id";
			$ergebnis = mysql_query ( $benutzerliste );
			while ( $row = mysql_fetch_object ( $ergebnis ) ) {
				
				echo "<tbody";
				
				if (isset ( $_GET ['bearb'] )) {
					if ($_GET ['bearb'] == $row->id) {
						echo " id='offen' ";
					}
				}
				echo ">";
				echo "<td>$row->id</td>";
				echo "<td><a href='?bearb=$row->id&userverw=1#bearbeiten'>$row->Name</a></td>";
				echo "<td>$row->titel</td>";
				echo "<td>$row->rights</td>";
				echo "<td>$row->forumRights</td>";
				if ($row->versuche == 3) {
					echo "<td>gesperrt</td>";
				} else {
					echo "<td>$row->versuche</td>";
				}
				echo "<td>
				<a href='?bearb=$row->id&userverw=1' class='rightBlueLink'>Edit</a>
				<a href='?statusID=$row->id&status=entsperren&userverw=1&action=1' class='rightGreenLink'>entsperren</a>
				<a href='?statusID=$row->id&status=sperren&userverw=1&action=1' class='rightRedLink'>sperren</a>
				</td>";
				echo "</tbody>";
			}
			echo "</table>";
		} else {
			echo "<p class=''>Du darfst die Benutzerverwaltung nicht anzeigen</p>";
		}
	}
	
	/**
	 * Ermöglicht das hinzufügen eines neuen Benutzers.
	 */
	function newBenutzerEingabe() {
		if($this->userHasRight("38", 0) == true) {
			echo "<a href='?regnewuser' class='buttonlink'>Neuer Benutzer</a><br>";
			if (isset ( $_GET ['regnewuser'] )) {
				echo "<div class='newChar'>";
				echo "<a href=\"?\"  class='highlightedLink'>X</a>";
				echo "<h2>Einen neuen Benutzer anlegen</h2>";
				echo "
						<form action='?' method='post'>
						<input type='text' size='24' maxlength='50' name='username' placeholder='Benutzername' required autofocus> <br/>
						<input type='password' size='24' maxlength='50' name='passwort' placeholder='Passwort' required> <br/>
						<input type='password' size='24' maxlength='50' name='passwort2' placeholder='Passwort wiederholen' required> <br/><br/>
						<input type='submit' value='Benutzer anlegen' name='regnewuser'> <br/><br />
						</form>";
				echo "</div>";
			}
		} else {
			echo "<p class=''>Du darfst keine neuen Benutzer anlegen.</p>";
		}
	}
	
	/**
	 * Erstellt einen neuen User in der Datenbank.
	 * Returns false or true.
	 */
	function newBenutzerFunction() {
		if($this->userHasRight("38", 0) == true) {
			if (isset ( $_POST ['passwort'] ) and isset ( $_POST ['passwort2'] ) and isset ( $_POST ['username'] ) and isset ( $_POST ['regnewuser'] )) {
				
				// RightCheck:
				if($this->userHasRight("38", 0) == true) {
					// Deklaration der Variablen
					$passwort1 = $_POST ['passwort'];
					$passwort2 = $_POST ['passwort2'];
					$username = $_POST ['username'];
					$regnewuser = $_POST ['regnewuser'];
					if ($username == "" or $passwort1 == "" or $passwort2 == "") {
						echo "<p class='meldung'>Fehler, es wurden nicht alle Felder ausgefüllt.</p>";
					} else {
						$check = "SELECT * FROM benutzer WHERE Name LIKE '$username' LIMIT 1";
						$checkergebnis = mysql_query ( $check );
						$row = mysql_fetch_object ( $checkergebnis );
						
						/*
						 * Hier tritt ein Notice Fehler auf, ist aber normal,
						 * da im Normalfall kein Benutzer gefunden wird.
						 */
						if (isset ( $row->Name )) {
							echo "<p class='meldung'>Fehler, der Benutzer <strong>$username</strong> existiert bereits.</p>";
						} else {
							if ($passwort1 == $passwort2) {
								$passwortneu = md5 ( $passwort1 );
								$query = "INSERT INTO benutzer (Name, Passwort, rights, versuche, forumRights) VALUES ('$username','$passwortneu','1','3','1')";
								$ergebnis = mysql_query ( $query );
								if ($ergebnis == true) {
									echo "<p class='erfolg'>Benutzer angelegt</p>";
								} else {
									echo "<p class='meldung'>Fehler.</p>";
								}
							} else {
								echo "<p class='meldung'>Die Passwörter sind nicht identisch!</p>";
							} // Else Ende
						} // Else Ende
					} // Else Ende
				} // Right Check Ende
			} // If Isset ende
		}
	} // Function Ende
	
	/**
	 * Ermöglicht das bearbeiten von Benutzern aus der Tabelle "benutzer".
	 * bearb ist in dem Fall eine ID.
	 */
	function bearbBenutzerEingabe() {
		if($this->userHasRight("39", 0) == true) {
			if (isset ( $_GET ['bearb'] )) {
				$bearb = $_GET ['bearb'];
				if ($bearb) {
					echo "<div class='newChar'>";
					echo "<h2><a name='bearbeiten'>Benutzerbearbeitung</a> <a href='?userverw=1' class='highlightedLink'>X</a></h2>";
					$userinfo = "SELECT timestamp, id, Name, Passwort, rights FROM benutzer WHERE id LIKE $bearb";
					$userergebnis = mysql_query ( $userinfo );
					echo "<table>";
					while ( $row = mysql_fetch_object ( $userergebnis ) ) {
						echo "<form action='?' method=post>";
						echo "<tr><td>Neuer Benutzername: </td><td><input type=text value='$row->Name' name=newname autofocus></td></tr>";
						echo "<tr><td>Neues Passwort: </td><td><input type=password value='' name=newpass ></td></tr>";
						echo "<tr><td><input type=hidden value='$row->id' name=id readonly></td></tr>";
						echo "<tr><td><input type=hidden value='$row->Name' name=name readonly></td></tr>";
						echo "<tr><td>Rechte:</td><td><input type=text value='$row->rights' name=rights required></td></tr>";
						echo "<tr><td>
								<input type=submit name='bearbuser' value='Ausführen'></td>
								<td><input type=submit name='bearbuser' value='Benutzer Informationen anzeigen' />";
						echo "<a class='highlightedLink' href='?action=3&table=benutzer&id=$row->id#angezeigteID'>Löschen</a></td></tr>";
						echo "</form>";
					}
					echo "</table>";
					echo "</div>";
					
					// # Guildwars
					$selectGuildwars = "SELECT * FROM gw_chars WHERE besitzer = '$bearb'";
					$ergebnisGuildwars = mysql_query ( $selectGuildwars );
					$mengeGuildwars = mysql_num_rows ( $ergebnisGuildwars );
					if (! isset ( $mengeGuildwars )) {
						$mengeGuildwars = 0;
					}
					
					// # Guildwars Kost calc
					$selectGuildwars2 = "SELECT * FROM gwcosts WHERE besitzer = '$bearb'";
					$ergebnisGuildwars2 = mysql_query ( $selectGuildwars2 );
					$mengeGuildwars2 = mysql_num_rows ( $ergebnisGuildwars2 );
					if (! isset ( $mengeGuildwars2 )) {
						$mengeGuildwars2 = 0;
					}
					
					// # Dokumentareinträge
					$selectDocu = "SELECT * FROM docu WHERE autor = '$bearb'";
					$ergebnisDocu = mysql_query ( $selectDocu );
					$mengeDocu = mysql_num_rows ( $ergebnisDocu );
					if (! isset ( $mengeDocu )) {
						$mengeDocu = 0;
					}
					
					// # BlogKommentare
					$selectBlogKommentare = "SELECT * FROM blog_kommentare WHERE autor = '$bearb'";
					$ergebnisBlogKommentare = mysql_query ( $selectBlogKommentare );
					$mengeBlogKommentare = mysql_num_rows ( $ergebnisBlogKommentare );
					if (! isset ( $mengeBlogKommentare )) {
						$mengeBlogKommentare = 0;
					}
					
					// # Vorschläge
					$selectVorschlaege = "SELECT * FROM vorschlaege WHERE autor = '$bearb'";
					$ergebnisVorschlaege = mysql_query ( $selectVorschlaege );
					$mengeVorschlaege = mysql_num_rows ( $ergebnisVorschlaege );
					if (! isset ( $mengeVorschlaege )) {
						$mengeVorschlaege = 0;
					}
					
					// # Blogtexte
					$selectBlogTexte = "SELECT * FROM blogtexte WHERE autor = '$bearb'";
					$ergebnisBlogTexte = mysql_query ( $selectBlogTexte );
					$mengeBlogTexte = mysql_num_rows ( $ergebnisBlogTexte );
					if (! isset ( $mengeBlogTexte )) {
						$mengeBlogTexte = 0;
					}
					
					// # Konten
					$selectKonten = "SELECT * FROM finanzen_konten WHERE besitzer = '$bearb'";
					$ergebnisKonten = mysql_query ( $selectKonten );
					$mengeKonten = mysql_num_rows ( $ergebnisKonten );
					if (! isset ( $mengeKonten )) {
						$mengeKonten = 0;
					}
					
					// # Umsätze
					$selectUmsaetze = "SELECT * FROM finanzen_umsaetze WHERE besitzer = '$bearb'";
					$ergebnisUmsaetze = mysql_query ( $selectUmsaetze );
					$mengeUmsaetze = mysql_num_rows ( $ergebnisUmsaetze );
					if (! isset ( $mengeUmsaetze )) {
						$mengeUmsaetze = 0;
					}
					
					// if($this->getUserName($loeschid) == "steven") {
					// echo "<p class='meldung'><strong>STEVEN kann nicht gelöscht werden, da er der Administrator auf dieser Seite ist.</strong></p>";
					// return false;
					// }
					
					// Ausgabe der Meldungen:
					if ($mengeBlogKommentare + $mengeBlogTexte + $mengeVorschlaege + $mengeDocu + $mengeGuildwars > 0) {
						echo "<p class=''><h2>Dieser Benutzer hat folgende Objekte: </h2>";
						if ($mengeGuildwars > 0) {
							echo $mengeGuildwars . " Guildwars-Charakter, <br>";
						}
						if ($mengeGuildwars2 > 0) {
							echo $mengeGuildwars2 . " Einträge in Kosten, <br>";
						}
						if ($mengeBlogKommentare > 0) {
							echo $mengeBlogKommentare . " Kommentare, <br>";
						}
						
						if ($mengeBlogTexte > 0) {
							echo $mengeBlogTexte . " Foreneinträge, <br>";
						}
						
						if ($mengeVorschlaege > 0) {
							echo $mengeVorschlaege . " Vorschläge, ";
						}
						
						if ($mengeDocu > 0) {
							echo $mengeDocu . " Doku Einträge, <br>";
						}
						
						echo "<br><br><br><br><br><br><br><br><br><br><br><br>";
					}
				}
			}
		} else {
			echo "<p class=''>Du darfst bestehende Benutzer nicht bearbeiten.</p>";
		}
	}
	
	/**
	 * Ermöglicht, dass das Passwort und andere Daten mit den Benutzern geändert werden können.
	 * Ergänzt die bearbBenutzerEingabe() Funktion.
	 */
	function bearbBenutzerFunction() {
		if (isset ( $_POST ['bearbuser'] ) and isset ( $_POST ['id'] )) {
			if($this->userHasRight("39", 0) == true) {
				if ($_POST ['bearbuser'] == "Ausführen") {
					
					if (! isset ( $_POST ['newname'] )) {
						echo "<p class='meldung'>Benutzername leer</p>";
						return false;
						exit ();
					}
					
					// Deklaration der Variablen
					$updatepass = $_POST ['newpass'];
					// schauen, ob Passwort geändert wurde, ansonsten nicht ändern.
					if ($updatepass == "") {
						$noPassChange = 1;
					} else {
						$updatepassmd5 = md5 ( $updatepass );
					}
					$rights = $_POST ['rights'];
					$newUserName = $_POST ['newname'];
					$selectedid = $_POST ['id'];
					
					// Prüfen, ob die Daten überhaupt geändert wurden:
					$checkRights = "SELECT id, Name, rights FROM benutzer WHERE id = '$selectedid' LIMIT 1";
					$ergebnisRights = mysql_query ( $checkRights );
					$rightsCheck = mysql_fetch_object ( $ergebnisRights );
					
					if ($rights == $rightsCheck->rights and $updatepass == "" and $newUserName == $rightsCheck->Name) {
						echo "<p class='info'>Es gab keine Änderung</p>";
						return false;
					}
					
					// SQL Befehle
					if ($noPassChange == 1) {
						$sqlupdate = "UPDATE benutzer SET rights='$rights', Name='$newUserName' WHERE id='$selectedid'";
					} else {
						$sqlupdate = "UPDATE benutzer SET Passwort='$updatepassmd5', rights='$rights', Name='$newUserName' WHERE id='$selectedid'";
					}
					
					$sqlupdategesamt = mysql_query ( $sqlupdate );
					if ($sqlupdategesamt == true) {
						echo "<p class='erfolg'>Der Datensatz wurde aktualisiert!";
					} else {
						echo "<p class='meldung'>Fehler beim speichern der Daten.";
					}
				} else if ($_POST ['bearbuser'] == "Benutzer Informationen anzeigen") {
					$this->deleteBenutzerFunction ();
				}
			} else {
				echo "Keine Berechtigung für diese Aktion.";
			}
		}
	}
	
	/**
	 * Löscht den Benutzer, wenn er keine anderen Einträge mehr hat.
	 * @todo
	 * @return boolean
	 */
	function deleteSingleUser() {
		if($this->userHasRight("40", 0) == true) {
			echo "<p class='meldung'>Der Benutzer $name wird gelöscht. Sicher? ";
			echo "<input type=hidden value='$loeschid' name='id' readonly />";
			echo "<input type=hidden value='löschen' name='bearbuser' readonly />";
			echo "<input type=hidden value='$name' name='name' readonly />";
			echo "<input type=submit value='Ja' name='submit' /></p>";
			echo "</form>";
			if (isset ( $_POST ['submit'] )) {
				if ($_POST ['submit'] != "") {
					
					if ($this->getUserName ( $loeschid ) == "steven") {
						echo "<p class='info'>STEVEN kann nicht gelöscht werden, da er der Administrator auf dieser Seite ist.</p>";
						return false;
						exit ();
					}
					$sql = "DELETE FROM benutzer WHERE id='$loeschid'";
					$del = mysql_query ( $sql );
					if ($del == true) {
						echo "<p class='erfolg'>Datensatz wurde gelöscht.";
						echo "<a href='?action=1' class='buttonlink'>Zurück</a></p>";
					} else {
						echo "<p class='meldung'>Fehler</p>";
					}
				}
			}
		} else {
			echo "<p class=''>Du darfst Benutzer nicht löschen.</p>";
		}
	}
	
	/**
	 * Zeigt alle Informationen eines Benutzers an und ermöglicht die Löschung der bestehenden Informationen.
	 */
	function deleteBenutzerFunction() {
		
		if($this->userHasRight("41", 0) == true) {
			
			if (isset ( $_POST ['id'] )) {
				$loeschid = $_POST ['id'];
			} else {
				echo "<p class='meldung'>Achtung, es wurde keine ID gefunden.</p>";
				exit ();
			}
			
			echo "<form method=post>";
			if (isset ( $_POST ['name'] )) {
				$name = $_POST ['name'];
			} else {
				echo "<p class='meldung'>Achtung, es wurde kein Name gefunden.</p>";
				exit ();
			}
			
			// NEUER CHECK:
			
			echo "<h2>Aktuelle Benutzerinformationen</h2>";
			
			// Alle Tables bekommen:
			$allTables = $this->getObjectsToArray ( "SHOW TABLES FROM flathacksql1" );
			
			$columnNamesForBesitzer = array (
					'besitzer',
					'xxx',
					'xxx',
					'autor',
					'xxx',
					'autor',
					'autor',
					'besitzer',
					'besitzer',
					'besitzer',
					'besitzer',
					'besitzer',
					'besitzer',
					'xxx',
					'besitzer',
					'besitzer',
					'xxx',
					'besitzer',
					'ersteller',
					'xxx',
					'xxx',
					'xxx',
					'besitzer',
					'xxx',
					'xxx' 
			);
			
			$userID = $_POST ['id'];
			
			// Pro Table durchlaufen ...
			for($i = 0; $i < sizeof ( $allTables ); $i ++) {
				
				// Nur anzeigen, wenn Table nicht excluded ist.
				if ($columnNamesForBesitzer [$i] != "xxx") {
					// Table anzeigen:
					
					$table = $allTables [$i]->Tables_in_flathacksql1;
					
					// Spalten bekommen
					$columns = $this->getColumns ( $table );
					
					// Informationen des aktuellen Tables auslesen
					$currentTableInfo = $this->getObjectsToArray ( "SELECT * FROM $table WHERE $columnNamesForBesitzer[$i] = '$userID'" );
					
					// Table nur anzeigen, wenn etwas vom Benutzer da ist:
					// if(isset($currentTableInfo[$i])) {
					
					echo "<table class='flatnetTable'>";
					
					// Überschrift anzeigen
					$spaltenanzahlMinusEins = sizeof ( $columns ) -1;
					echo "<thead><td colspan ='" . $spaltenanzahlMinusEins . "'>$i ) " . $allTables [$i]->Tables_in_flathacksql1 . " ($columnNamesForBesitzer[$i])" . "</td><td>
				 				<a class='highlightedLink' href='?action=1&loeschen&loeschid=" . $userID . "&table=" . $table . "&tableNumber=$i'>LÖSCHEN</a>
				 				</td></thead>";
					
					// Gibt die aktuelle Kopfzeile der aktuellen Tabelle aus
					echo "<thead>";
					for($k = 0; $k < sizeof ( $columns ); $k ++) {
						echo "<td>$columns[$k]</td>";
					}
					echo "</thead>";
					
					for($j = 0; $j < sizeof ( $currentTableInfo ); $j ++) {
						echo "<tbody>";
						echo "<td><a class='rightBlueLink' href='?action=3&table=$table&id=".$currentTableInfo [$j]->id."'>open</a></td>";
						
						// Gibt die Information der aktuellen Zelle aus:
						for($k = 0; $k < sizeof ( $columns ); $k ++) {
							echo "<td>";
							echo substr ( strip_tags($currentTableInfo [$j]->$columns [$k]), 0, 20 );
							echo "</td>";
						}
						
						echo "</tbody>";
					}
					echo "</table>";
					
					// }
				}
			}
		}
	}
	
	/**
	 * Zeigt eingereichte Vorschläge der Benutzer an
	 */
	function adminVorschlaege() {
		if($this->userHasRight("42", 0) == true) {
			// Einfügen der Function
			if (isset ( $_GET ['submit'] ) and isset ( $_GET ['status'] ) and isset ( $_GET ['hiddenID'] )) {
				$this->vorschlaegeAction ( $_GET ['submit'], $_GET ['status'], $_GET ['hiddenID'] );
			}
			
			echo "<div class='neuerBlog'>";
			echo "<h2>Logeinträge</h2>";
			// ANZAHL ###################################################
			$select = "SELECT count(*) as anzahl FROM `vorschlaege`"; // #
			$ergebnis = mysql_query ( $select ); // #
			$row = mysql_fetch_object ( $ergebnis ); // #
			                                      // ###########################################################
			echo "Es gibt " . $row->anzahl . " Einträge im LOG!";
			echo "</div>";
			
			// Select from Database
			echo "<table class='flatnetTable'>";
			echo "<thead>";
			echo "<td>ID</td>
					<td>Datum</td>
					<td>Autor</td>
					<td>Text</td>
					<td>ART</td>
					<td>Optionen</td>";
			echo "</thead>";
			
			// ILLEGALE AKTIONEN
			$vorschlaege = "SELECT
					id, timestamp, autor, text, status, year(timestamp) as jahr, month(timestamp) as monat, day(timestamp) as tage
					FROM vorschlaege
					WHERE status = 'illegal'
					ORDER BY timestamp DESC";
			$ergebnis = mysql_query ( $vorschlaege );
			while ( $row = mysql_fetch_object ( $ergebnis ) ) {
				echo "<thead id='illegal'><td colspan = '6' >WARNUNG</td></thead>";
				echo "<form method=get>";
				echo "<input type=hidden name='action' value='2' />";
				echo "<tbody";
				
				if ($row->status == "offen") {
					echo " id='offen' ";
				} else if ($row->status == "illegal") {
					echo " id='illegal' ";
				} else if ($row->status == "Error") {
					echo " id='error' ";
				} else if ($row->status == "login") {
					echo " id='login' ";
				}
				
				echo ">
				<td>$row->id <input type=hidden value='$row->id' name='hiddenID' /></td>
				<td>$row->tage.$row->monat.$row->jahr</td>";
				if ($row->autor == 0) {
					echo "<td>System</td>";
				} else {
					echo "<td>" . $this->getUserName ( $row->autor ) . "</td>";
				}
				echo "<td>$row->text</td>
				<td>
				<select name='status' value='' size='1'>";
				
				echo "<option";
				if ($row->status == "offen") {
					echo " selected='selected' ";
				}
				echo "> offen </option>";
				
				echo "<option";
				if ($row->status == "erledigt") {
					echo " selected='selected' ";
				}
				echo "> erledigt </option>";
				
				echo "<option";
				if ($row->status == "verworfen") {
					echo " selected='selected' ";
				}
				echo "> verworfen </option>";
				
				echo "<option";
				if ($row->status == "illegal") {
					echo " selected='selected' ";
				}
				echo "> illegal </option>";
				
				echo "<option";
				if ($row->status == "login") {
					echo " selected='selected' ";
				}
				echo "> login </option>";
				
				echo "<option";
				if ($row->status == "Error") {
					echo " selected='selected' ";
				}
				echo "> Error </option>";
				
				echo "</select>
						</td>
						<td id='options'>
						<input type=submit value='OK' name='submit' />
						<input type=submit value='X' name='submit' />
						</td>
						</tbody>";
				echo "</form>";
			}
			
			// VORSCHLAEGE
			echo "<thead><td colspan = '6'>Benutzer Vorschläge</td></thead>";
			$vorschlaege = "SELECT id, timestamp, autor, text, status, year(timestamp) as jahr, month(timestamp) as monat, day(timestamp) as tage
					FROM vorschlaege
					WHERE status = 'offen'
					OR status = 'verworfen'
					OR status = 'erledigt'
					ORDER BY id ASC";
			$ergebnis = mysql_query ( $vorschlaege );
			while ( $row = mysql_fetch_object ( $ergebnis ) ) {
				echo "<form method=get>";
				echo "<input type=hidden name='action' value='2' />";
				echo "<tbody";
				
				if ($row->status == "offen") {
					echo " id='offen' ";
				} else if ($row->status == "illegal") {
					echo " id='illegal' ";
				} else if ($row->status == "Error") {
					echo " id='error' ";
				} else if ($row->status == "login") {
					echo " id='login' ";
				}
				
				echo ">
				<td>$row->id <input type=hidden value='$row->id' name='hiddenID' /></td>
				<td>$row->tage.$row->monat.$row->jahr</td>";
				if ($row->autor == 0) {
					echo "<td>System</td>";
				} else {
					echo "<td>" . $this->getUserName ( $row->autor ) . "</td>";
				}
				echo "<td>$row->text</td>
				<td>
				<select name='status' value='' size='1'>";
				
				echo "<option";
				if ($row->status == "offen") {
					echo " selected='selected' ";
				}
				echo "> offen </option>";
				
				echo "<option";
				if ($row->status == "erledigt") {
					echo " selected='selected' ";
				}
				echo "> erledigt </option>";
				
				echo "<option";
				if ($row->status == "verworfen") {
					echo " selected='selected' ";
				}
				echo "> verworfen </option>";
				
				echo "<option";
				if ($row->status == "illegal") {
					echo " selected='selected' ";
				}
				echo "> illegal </option>";
				
				echo "<option";
				if ($row->status == "login") {
					echo " selected='selected' ";
				}
				echo "> login </option>";
				
				echo "<option";
				if ($row->status == "Error") {
					echo " selected='selected' ";
				}
				echo "> Error </option>";
				
				echo "</select>
						</td>
						<td id='options'>
						<input type=submit value='OK' name='submit' />
						<input type=submit value='X' name='submit' />
						</td>
						</tbody>";
				echo "</form>";
			}
			
			// Login Log ############################################################################################################################################
			
			$vorschlaegeNEW = "SELECT count(id) as anzahl, id, timestamp, autor, text, hour(timestamp) as hour, minute(timestamp) as minute, status,year(timestamp) as jahr, month(timestamp) as monat, day(timestamp) as tage
					FROM vorschlaege
					WHERE status = 'login'
					GROUP BY autor ORDER BY timestamp ASC";
			$ergebnisNEW = mysql_query ( $vorschlaegeNEW );
			
			echo "<table class='flatnetTable'>";
			echo "<thead><td>Autor</td><td>Text</td><td>Anzahl dieser Einträge</td><td>Letzter Eintrag</td><td>Optionen</td></thead>";
			
			while ( $rowNEW = mysql_fetch_object ( $ergebnisNEW ) ) {
				
				// letzter Eintag:
				$letzterEintrag = $this->getObjektInfo ( "SELECT * FROM vorschlaege WHERE text = '$rowNEW->text' ORDER BY timestamp DESC" );
				
				echo "<tbody>";
				echo "<td>" . $this->getUserName ( $rowNEW->autor ) . "</td>";
				echo "		<td>" . $rowNEW->text . "</td><td>$rowNEW->anzahl</td>
						<td>" . $letzterEintrag->timestamp . "</td>
						<td><a href='?action=2&kategorieLoeschen=$rowNEW->id&loeschen=ja&loeschid=$rowNEW->id' class='highlightedLink'>X</a></td>
						</tbody>";
			}
			// ##########################################################################################################################################################
			// Error Log:
			$vorschlaegeNEW = "SELECT count(id) as anzahl, id, timestamp, autor, text, hour(timestamp) as hour, minute(timestamp) as minute, status, year(timestamp) as jahr, month(timestamp) as monat, day(timestamp) as tage
					FROM vorschlaege
					WHERE status = 'Error'
					GROUP BY text ORDER BY timestamp DESC";
			$ergebnisNEW = mysql_query ( $vorschlaegeNEW );
			
			echo "<thead><td></td><td>Login Error:</td><td>Anzahl dieser Einträge</td><td>Letzter Eintrag</td><td>Optionen</td></thead>";
			
			while ( $rowNEW = mysql_fetch_object ( $ergebnisNEW ) ) {
				
				// letzter Eintag:
				$letzterEintrag = $this->getObjektInfo ( "SELECT * FROM vorschlaege WHERE text = '$rowNEW->text' ORDER BY timestamp DESC" );
				
				echo "<tbody><td></td>
						<td>" . $rowNEW->text . "</td><td>$rowNEW->anzahl</td>
						<td>" . $letzterEintrag->timestamp . "</td>
						<td><a href='?action=2&kategorieLoeschen=$rowNEW->id&loeschen=ja&loeschid=$rowNEW->id' class='highlightedLink'>X</a></td>
						</tbody>";
			}
			
			echo "</table>";
		} else {
			echo "<p class=''>Du darfst die globalen Informationen zu einem Benutzer nicht anzeigen.</p>";
		}
	}
	
	/**
	 * Löscht die Logeinträge aus dem gewählten Text.
	 */
	function adminVorschlaegeDELETE_Eintraege() {
		if($this->userHasRight("42", 0) == true) {
			if (isset ( $_GET ['kategorieLoeschen'] ) and isset ( $_GET ['loeschen'] ) and isset ( $_GET ['loeschid'] )) {
				$id = $_GET ['kategorieLoeschen'];
				if ($id != "" and $id > 0) {
					
					// Text der zu löschenden ID bekommen:
					$deleteObjectWithThisText = $this->getObjektInfo ( "SELECT * FROM vorschlaege WHERE id = '$id' LIMIT 1" );
					$text = $deleteObjectWithThisText->text;
					// JETZT wird $text gelöscht!
					$this->sqlDeleteCustom ( "DELETE FROM vorschlaege WHERE text = '$text'" );
				}
			}
		} else {
			echo "<p class=''>Du darfst keine Logeinträge löschen.</p>";
		}
	}
	
	/**
	 * Zeigt eine Vorschlagsliste für die Benutzer an.
	 */
	function userVorschlaege() {
		
		if($this->userHasRight("8", 0) == true) {
			// Einfügen der Function
			if (isset ( $_GET ['submit'] ) and isset ( $_GET ['status'] ) and isset ( $_GET ['hiddenID'] )) {
				$this->vorschlaegeAction ( $_GET ['submit'], $_GET ['status'], $_GET ['hiddenID'] );
			}
			
			// Select from Database
			$vorschlaege = "SELECT id, timestamp, autor, text, status FROM vorschlaege WHERE status = 'offen' ORDER BY status DESC";
			$ergebnis = mysql_query ( $vorschlaege );
			echo "<table class='flatnetTable'>";
			echo "<thead>";
			echo "<td>Text</td><td>Status</td>";
			echo "</thead>";
			while ( $row = mysql_fetch_object ( $ergebnis ) ) {
				echo "<form method=get>";
				echo "<input type=hidden name='action' value='2' />";
				echo "<tbody";
				if ($row->status == "offen") {
					echo " id='offen' ";
				}
				echo ">
				<td>$row->text</td>
				<td>$row->status</td></tbody>";
				echo "</form>";
			}
			echo "</table>";
		} else {
			echo "<p class=''>Du darfst die Uservorschläge nicht anzeigen.</p>";
		}
	}
	
	/**
	 * Verändert den Status der eingereichten Vorschläge
	 */
	function vorschlaegeAction($submit, $status, $id) {
		
		if($this->userHasRight("8", 0) == true) {
		
			if ($submit == "OK") {
				// Vars zuweisen
				$sqlupdate = "UPDATE vorschlaege SET status='$status' WHERE id='$id'";
				$update = mysql_query ( $sqlupdate );
				if ($update == true) {
					return true;
				} else {
					return false;
				}
			} else if ($submit == "X") {
				// @todo Rechtesystem einbauen
				$loeschen = "DELETE FROM vorschlaege WHERE id = '$id'";
				$loeschErgeb = mysql_query ( $loeschen );
				if ($loeschErgeb == true) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		
		}
	}
	
	/**
	 * Gibt den Namen einer RightKategorie zurück.
	 * 
	 * @param unknown $id        	
	 * @return unknown
	 */
	function getRightKategorieName($id) {
		$this->connectToDB ();
		$selectUsername = "SELECT id, name FROM rightkategorien WHERE id = '$id' LIMIT 1";
		$ergebnisUsername = mysql_query ( $selectUsername );
		while ( $rowUsername = mysql_fetch_object ( $ergebnisUsername ) ) {
			$username = $rowUsername->name;
		}
		// DB CLOSE
		if (! isset ( $username )) {
			$username = "Kein Name";
		}
		return $username;
	}
	
	/**
	 * Ermöglicht das erstellen eines neuen Rechts
	 */
	function newRight() {
		if($this->userHasRight("15", 0) == true) {
			
			// Eintragung des Rechts in die DB
			if (isset ( $_POST ['newRightName'] ) and isset ( $_POST ['kategorie'] )) {
				if ($_POST ['newRightName'] != "" and $_POST ['kategorie'] != "") {
					$newRightName = $_POST ['newRightName'];
					$kategorie = $_POST ['kategorie'];
					
					// SQL
					$query = "INSERT INTO userrights (recht, kategorie) VALUES ('$newRightName','$kategorie')";
					$ergebnis = mysql_query ( $query );
					if ($ergebnis == true) {
						echo "<p class='erfolg'>Das Recht wurde erstellt.</p>";
					} else {
						echo "<p class='meldung'>Es ist ein Fehler aufgetreten.</p>";
					}
				} else {
					echo "<p class='meldung'>Die Felder sind leer! Das darf nicht!</p>";
				}
			}
			
			// Feld ausblenden, wenn bereits geklickt.
			if (! isset ( $_POST ['newRightName'] )) {
				echo "<a href='?action=6&new=right' class='buttonlink'>Neues Recht erstellen</a>";
			}
			
			// Input Felder für das Eintragen von einem Recht in die DB
			if (isset ( $_GET ['new'] )) {
				if ($_GET ['new'] == "right") {
					echo "<div class='newChar'>";
					echo "<a href=\"?action=6\"  class='highlightedLink'>X</a>";
					echo "<h2>Neues Recht erstellen</h2>";
					echo "<form method=post>";
					echo "<input type='text' value='' name='newRightName' placeholder='Infotext des Rechts' required autofocus /> <br> ";
					# Select für Kategorien
					$getKats = $this->getObjectsToArray("SELECT * FROM rightkategorien ORDER BY name");
					echo "<select name='kategorie'>";
					for ($i = 0 ; $i < sizeof($getKats) ; $i++) {
						echo "<option value='".$getKats[$i]->id."'>" . $getKats[$i]->name . "</option>";
					}
					echo "</select>";
					
					#
					#echo "<input type='text' value='' name='kategorie' placeholder='Kategorie' required /> Kategorie wählen (eine Zahl)<br> ";
					echo "<input type='submit' name='absenden' value='OK' />";
					echo "</form></div>";
				}
			}
		} else {
			echo "<p class=''>Du darfst keine neuen Rechte erstellen.</p>";
		}
	}
	
	/**
	 * Entsperrt einen Benutzer
	 */
	function modifyUsersStatus($id, $status) {
		if($this->userHasRight("45", 0) == true) {
			if (isset ( $id ) and isset ( $status )) {
				if ($status == "entsperren") {
					$updateVersuche = "UPDATE benutzer SET versuche='0' WHERE id = '$id'";
					$updateErgeb2 = mysql_query ( $updateVersuche );
					if ($updateErgeb2 == "true") {
						echo "<p class='erfolg'>Benutzer entsperrt</p>";
					}
				} else if ($status == "sperren") {
					$updateVersuche = "UPDATE benutzer SET versuche='3' WHERE id = '$id'";
					$updateErgeb2 = mysql_query ( $updateVersuche );
					if ($updateErgeb2 == "true") {
						echo "<p class='meldung'>Benutzer gesperrt</p>";
					}
				}
			}
		} else {
			echo "<p class=''>Du darfst keine Benutzer entsperren oder sperren.</p>";
		}
	}
	
	/**
	 * Ermöglicht das freischalten von Rechten für den Bereich "Forum".
	 */
	function setUserForumRights() {
		if($this->userHasRight("47", 0) == true) {
			
			// Benutzerauswahl
			echo "<div>";
			echo "<h3><a name='forumRechte'>Forum - Rechte verteilen</a></h3>";
			$benutzerlisteUser = "SELECT id, Name FROM benutzer ORDER BY name";
			$ergebnisUser = mysql_query ( $benutzerlisteUser );
			
			while ( $rowUser = mysql_fetch_object ( $ergebnisUser ) ) {
				echo "<a href='?action=5&user=$rowUser->id#forumRechte' class='buttonlink'>$rowUser->Name</a>";
			}
			echo "</table>" . "</div>";
			
			// Status der Rechte Änderung:
			if (isset ( $_GET ['right'] )) {
				if($this->userHasRight("47", 0) == true) {
					// ALTE BENUTZERRECHTE BEKOMMEN:
					$userid = $_GET ['user'];
					$rightFromCurrentUser = "SELECT forumRights FROM benutzer WHERE id = '$userid'";
					$ergebnisGetRights = mysql_query ( $rightFromCurrentUser );
					$rowGetRights = mysql_fetch_object ( $ergebnisGetRights );
					$benutzerrechte = $rowGetRights->forumRights;
					if ($benutzerrechte == 0) {
						$benutzerrechte = 1;
					}
					
					// Dezimalwert der RechteID bekommen:
					$rightID = $_GET ['right'];
					$getRightValue = "SELECT id, rightWert FROM blogkategorien WHERE id = '$rightID'";
					$ergebnisGetValues = mysql_query ( $getRightValue );
					$rowGetValue = mysql_fetch_object ( $ergebnisGetValues );
					$rightValue = $rowGetValue->rightWert;
					
					// Status der Änderung:
					if ($_GET ['status'] == "nein") {
						// dann wird das Recht gewährt
						$neueBenutzerRechte = $benutzerrechte + $rightValue;
					}
					
					if ($_GET ['status'] == "ja") {
						// Dann wird das recht verweigert
						$neueBenutzerRechte = $benutzerrechte - $rightValue;
					}
					
					// Neues Recht in die Datenbank schreiben
					
					$sqlRightUpdate = "UPDATE benutzer SET forumRights='$neueBenutzerRechte' WHERE id='$userid'";
					
					$sqlupdategesamt = mysql_query ( $sqlRightUpdate );
					if ($sqlupdategesamt == true) {
						echo "<p class='erfolg'>Das Recht wurde aktualisiert</p>";
					} else {
						echo "<p class='meldung'>Es ist ein Fehler aufgetreten</p>";
					}
				}
			}
			
			// Rechte setzen / lesen
			if (isset ( $_GET ['user'] )) {
				$id = $_GET ['user'];
				
				$benutzerliste = "SELECT id, Name, forumRights FROM benutzer WHERE id = '$id' ORDER BY name";
				$ergebnis = mysql_query ( $benutzerliste );
				
				echo "<div id=''>";
				
				while ( $row = mysql_fetch_object ( $ergebnis ) ) {
					echo "<div class=''>";
					echo "<h2>$row->Name</h2>";
					if ($row->forumRights == 0) {
						$benutzerrechteVorher = 1;
					} else {
						$benutzerrechteVorher = $row->forumRights;
					}
					echo "hat die Rechte: $benutzerrechteVorher";
					echo "</div>";
					
					// Rechteliste des Benutzer erstellen
					$rightListe = "SELECT * FROM blogkategorien ORDER BY rightPotenz DESC";
					$ergebnis2 = mysql_query ( $rightListe );
					
					echo "<h3>Rechteliste</h3>";
					echo "<table class='flatnetTable'>";
					echo "<thead><td id='text'>Recht</td><td>vorhanden</td><td>Ändern</td></thead>";
					while ( $row2 = mysql_fetch_object ( $ergebnis2 ) ) {
						if ($this->check ( $row2->rightWert, $benutzerrechteVorher ) == true) {
							
							echo "<tbody id='offen'>" . "<td>$row2->kategorie</td>" . "<td>Ja</td> " . "<td><a href='?action=5&user=$id&status=ja&right=$row2->id#forumRechte'>verweigern</a></td>" . "<tbody>";
						} else {
							echo "<tbody>" . "<td>$row2->kategorie</td>" . "<td>Nein</td> " . "<td><a href='?action=5&user=$id&status=nein&right=$row2->id#forumRechte'>gewähren</a></td>" . "<tbody>";
						}
					}
					echo "</table>";
					echo "</div>";
				}
			}
		} else {
			echo "<p class=''>Du darfst keine Rechte im Forum vergeben.</p>";
		}
	}
	
	/**
	 * Zeigt alle Kategorien an.
	 */
	function showForumCategories() {
		
		if($this->userHasRight("48", 0) == true) {
			
			echo "<table class='flatnetTable'>";
			echo "<thead><td>Kategorie</td><td>Erstellungsdatum</td><td>Rechte</td><td>Optionen</td></thead>";
			$selectCategories = "SELECT *
					, year(timestamp) AS jahr
					, month(timestamp) as monat
					, day(timestamp) as tag
					, hour(timestamp) AS stunde
					, minute(timestamp) AS minute
					FROM blogkategorien
					ORDER BY kategorie";
			$ergebnis = mysql_query ( $selectCategories );
			while ( $row = mysql_fetch_object ( $ergebnis ) ) {
				echo "<tbody><td>";
				echo "<a href='/flatnet2/forum/index.php?blogcategory=$row->id'>" . $row->kategorie . "</a></td>";
				echo "<td><a href='?action=5'>" . $row->tag . "." . $row->monat . "." . $row->jahr . "</a></td>";
				echo "<td><a href='?action=5'>" . $row->rightPotenz . " / " . $row->rightWert . "</a></td>";
				echo "<td><a href='?action=5&editid=$row->id' class='buttonlink'>Edit</a>";
				echo "<a href='?action=5&loeschid=$row->id' class='buttonlink'>X</a></td></tbody>";
			}
			
			echo "</table>";
		
		} else {
			echo "<p class=''>Du darfst die Forenkategorien nicht anzeigen.</p>";
		}
	}
	
	/**
	 * Ermöglicht das setzen einer neuen Kategorie.
	 */
	function newForumCategory() {
		if($this->userHasRight("49", 0) == true) {
			if (! isset ( $_GET ['editid'] )) {
				
				# Höchste Potenz bekommen:
				
				$getMaxPotenz = $this->getObjektInfo("SELECT max(rightPotenz) as max FROM blogkategorien");
				if(!isset($getMaxPotenz->max)) {
					# Wenn es noch keine Kategorien gibt: 
					$max = 0;
				} else {
					$max = $getMaxPotenz->max + 1;
				}
				
				echo "<h2>Neue Kategorie:</h2>";
				echo "<form method=post>";
				echo "<input type=text value='' placeholder='Kategoriename' name='nameNewCat' required />";
				echo "<input type=text value='' placeholder='Beschreibung' name='description' required />";
				echo "<input type=number value='$max' placeholder='Potenz' name='potenz' required />";
				echo "<input type=number value='100' placeholder='Sortierung' name='sortierung' required />";
				echo "<input type=submit name=absenden value='speichern' />";
				echo "</form>";
				
				// Category hinzufügen:
				if (isset ( $_POST ['absenden'] )) {
					$this->addNewForumCategory ();
				}
			}
		} else {
			echo "<p class=''>Du darfst keine neuen Kategorien erstellen.</p>";
		}
	}
	/**
	 * Funktionalität: speichert Kategorieeintrag bei Aufruf und vorhandensein von
	 * $_POST['nameNewCat'] und $_POST['description'],
	 * siehe $this->newForumCategory();
	 */
	function addNewForumCategory() {
		if($this->userHasRight("49", 0) == true) {
			// leere Felder abfangen:
			if (isset ( $_POST ['nameNewCat'] ) and isset ( $_POST ['description'] )) {
				if ($_POST ['nameNewCat'] == "" or $_POST ['description'] == "" or $_POST ['potenz'] == "" or $_POST ['sortierung'] == "") {
					echo "<p class='meldung'>Leeres Feld</p>";
					return false;
				} else {
					$kategorie = $_POST ['nameNewCat'];
					$description = $_POST ['description'];
					$potenz = $_POST ['potenz'];
					$sortierung = $_POST ['sortierung'];
					$wert = pow ( 2, $potenz );
					
					// Püfen ob es diesen Namen schon gibt:
					$check = "SELECT * FROM blogkategorien WHERE kategorie = '$kategorie'";
					$checkergebnis = mysql_query ( $check );
					$row = mysql_fetch_object ( $checkergebnis );
					if (isset ( $row->kategorie )) {
						echo "<p class='meldung'>Diese Kategorie existiert bereits.</p>";
						return false;
						exit ();
					}
					
					$insert = "INSERT INTO blogkategorien (kategorie, beschreibung, rightPotenz, rightWert, sortierung) VALUES('$kategorie', '$description', '$potenz', '$wert', '$sortierung')";
					$ergebnis = mysql_query ( $insert );
					if ($ergebnis == true) {
						
						echo "<p class='erfolg'>Kategorie wurde erfolgreich angelegt.</p>";
						return true;
					} else {
						
						echo "<p class='meldung'>Fehler beim speichern</p>";
						return false;
					}
				}
			} else {
				return false;
			}
		} else {
			echo "<p class='meldung'>Keine Berechtigung</p>";
			return false;
		}
	}
	
	/**
	 * Ermöglicht das verändern einer bestehendes Kategorie.
	 */
	function alternateForumCategory() {
		if($this->userHasRight("50", 0) == true) {
			if (isset ( $_GET ['editid'] ) or isset ( $_GET ['loeschid'] )) {
				if (isset ( $_GET ['loeschid'] ) and $_GET ['loeschid'] != "") {
					// ##########################
					// LOESCH METHODE ##########
					// ##########################
					$loeschid = $_GET ['loeschid'];
					
					$query = "DELETE FROM blogkategorien WHERE id = '$loeschid'";
					
					// nicht zugeordnet Kategorie finden:
					$select = "SELECT id, kategorie FROM blogkategorien WHERE kategorie = 'nicht zugeordnet' LIMIT 1";
					$ergebnis = mysql_query ( $select );
					while ( $row = mysql_fetch_object ( $ergebnis ) ) {
						$notAssigned = $row->id;
					}
					
					if (! isset ( $notAssigned )) {
						echo "<p class='meldung'>Es gibt keine Kategorie mit dem Namen <strong>nicht zugeordnet</strong>!
								bestehende Foreneinträge können somit verloren gehen, wenn diezu löschende Kategorie entfernt wird.
								</p><p class='meldung'>Kategorie mit Namen: nicht zugeordnet erstellen!</p><p class='meldung'>Vorgang abgebrochen.</p>";
						return false;
					} else {
						// prüfen, ob die kategorie "nicht zugeordnet" gelöscht werden soll,
						// wenn ja, dann return false
						if ($this->getCatName ( $loeschid ) == "nicht zugeordnet") {
							echo "<p class='meldung'>Die Kategorie nicht zugeordnet darf nicht gelöscht werden.</p>";
							return false;
						} else {
							//
							// Abfrage ob fortgefahren werden soll.
							//
							echo "<form method=post>";
							echo "<p class='info'>Sicher, dass die Kategorie <strong>" . $this->getCatName ( $loeschid ) . "</strong> gelöscht werden soll? <br>Alle Beiträge werden in die Kategorie
									<strong>nicht zugeordnet</strong> verschoben";
							echo "<input type=hidden name='loeschid' value='$loeschid' />";
							echo "<input type=hidden name='notAssigned' value='$notAssigned' />";
							echo "<input type=submit name='ja' value='JA' />";
							echo "</p></form>";
							
							//
							// JETZT ist alles OK und die Kategorie darf gelöscht werden.
							//
							if (isset ( $_POST ['ja'] )) {
								if (! isset ( $_POST ['notAssigned'] ) or ! isset ( $_POST ['loeschid'] )) {
									echo "<p class='meldung'>Die IDs für nicht zugeordnet oder die LoeschID wurde nicht korrekt übergeben. Es
											könnten Beiträge verloren gehen.</p>";
									return false;
									exit ();
								} else {
									if ($this->sql_insert_update_delete ( $query ) == true) {
										echo "<p class='erfolg'>Kategorie gelöscht.</p>";
										
										$notAssigned = $_POST ['notAssigned'];
										$loeschid = $_POST ['loeschid'];
										// BLOGEINTRÄGE auf ANDERE KATEGORIE UMÄNDERN:
										
										$update = "UPDATE blogtexte SET kategorie ='$notAssigned' WHERE kategorie = '$loeschid'";
										if ($this->sql_insert_update_delete ( $update ) == true) {
											echo "<p class='erfolg'>Beiträge erfolgreich verschoben.</p>";
											return true;
										} else {
											echo "<p class='info'>Es wurden keine Beiträge verschoben.</p>";
											return false;
										}
									} else {
										echo "<p class='meldung'>Kategorie konnte nicht gelöscht werden.</p>";
										return false;
									}
								}
							}
						}
					}
				} else if (isset ( $_GET ['editid'] ) and $_GET ['editid'] != "") {
					// ##########################
					// EDIT METHODE ############
					// ##########################
					$editid = $_GET ['editid'];
					$select = "SELECT * FROM blogkategorien WHERE id = '$editid'";
					$ergebnis = mysql_query ( $select );
					while ( $row = mysql_fetch_object ( $ergebnis ) ) {
						echo "<div class='innerBody'>";
						echo "<form method=post>";
						echo "<input type=text name=newCatName value='$row->kategorie' placeholder='Kategoriename' /><br>";
						echo "<input type=text id='long' name=newCatDescription value='$row->beschreibung' placeholder='Beschreibung' /><br>";
						echo "<input type=number name=newPotenz value='$row->rightPotenz' placeholder='Potenz von 2 eingeben' /><br>";
						echo "<input type=number name=newSortierung value='$row->sortierung' placeholder='Sortierung eingeben' /><br>";
						echo "<input type=submit name=submit value='OK' />";
						echo "</form>";
						echo "</div>";
					}
					
					if (isset ( $_POST ['newCatName'] ) and isset ( $_POST ['newCatDescription'] ) and isset ( $_POST ['newPotenz'] ) and isset ( $_POST ['submit'] )) {
						if ($_POST ['newCatName'] == "" or $_POST ['newCatDescription'] == "" or $_POST ['newPotenz'] == "" or $_POST ['newSortierung'] == "") {
							return false;
						} else {
							
							$kategorie = $_POST ['newCatName'];
							$description = $_POST ['newCatDescription'];
							$potenz = $_POST ['newPotenz'];
							$sortierung = $_POST ['newSortierung'];
							$wert = pow ( 2, $potenz );
							$query = "UPDATE blogkategorien SET kategorie='$kategorie', beschreibung = '$description', rightPotenz = '$potenz', rightWert = '$wert', sortierung = '$sortierung' WHERE id='$editid' LIMIT 1";
							
							if ($this->sql_insert_update_delete ( $query ) == true) {
								echo "<p class='erfolg'>Kategorie abgeändert.</p>";
								return true;
							} else {
								echo "<p class='meldung'>Kategorie konnte nicht geändert werden.</p>";
								return false;
							}
						}
					}
					
					// echo "<p class='info'>Deaktiviert</p>";
					/*
					 * $query="UPDATE blogkategorien SET kategorie='$kategorie', beschreibung = '$description' WHERE id='$editid' LIMIT 1";
					 *
					 * if($this->sql_insert_update_delete($query) == true) {
					 * echo "<p class='erfolg'>Kategorie abgeändert.</p>";
					 * return true;
					 * } else {
					 * echo "<p class='meldung'>Kategorie konnte nicht geändert werden.</p>";
					 * return false;
					 * }
					 */
				} else {
					echo "<p class='meldung'>Keine Auswahl</p>";
					return false;
				}
			}
		} else {
			echo "<p class='meldung'>Keine Berechtigung zum ändern von Forenkategorien</p>";
			return false;
		}
	}
	
	/**
	 * Gibt die Spalten einer Tabelle zurück.
	 * 
	 * @param unknown $table        	
	 * @return unknown
	 */
	function getColumns($table) {
		// COLUMNS SPEICHERN;
		$select1 = "SHOW COLUMNS FROM $table";
		$ergebnis1 = mysql_query ( $select1 );
		$i = 0;
		while ( $row = mysql_fetch_object ( $ergebnis1 ) ) {
			$columns [$i] = $row->Field;
			$i ++;
		}
		
		return $columns;
	}
	
	/**
	 * Gibt die Spalten einer Query zurück.
	 * 
	 * @param unknown $query        	
	 */
	function getColumnsFromQuery($query) {
		$createTempTable = "
		CREATE TEMPORARY TABLE
		IF NOT EXISTS tempTable AS ($query);";
		mysql_query ( $createTempTable );
		
		$select1 = "SHOW COLUMNS FROM tempTable";
		$ergebnis1 = mysql_query ( $select1 );
		$i = 0;
		while ( $row = mysql_fetch_object ( $ergebnis1 ) ) {
			$columns [$i] = $row->Field;
			$i ++;
		}
		
		return $columns;
	}
	function getColumnAnzahl($query) {
		$createTempTable = "
		CREATE TEMPORARY TABLE
		IF NOT EXISTS tempTable AS ($query);";
		mysql_query ( $createTempTable );
		
		$select1 = "SHOW COLUMNS FROM tempTable";
		
		$ergebnis = mysql_query ( $select1 );
		$menge = mysql_num_rows ( $ergebnis );
		return $menge;
	}
	
	/**
	 * gibt die Kommentare der Spalten einer Tabelle wieder und speichert diese in einem Array.
	 * 
	 * @param unknown $table        	
	 * @return unknown
	 */
	function getColumnComments($table) {
		
		// COLUMNS SPEICHERN;
		mysql_close ();
		$this->connectToSpecialDB ( "information_schema", "flathacksql1", "f12245f8@sql" );
		$select1 = "SELECT column_name, column_comment FROM columns WHERE TABLE_SCHEMA = 'flathacksql1' AND TABLE_NAME='$table'";
		$ergebnis1 = mysql_query ( $select1 );
		$i = 0;
		while ( $row = mysql_fetch_object ( $ergebnis1 ) ) {
			$comments [$i] = $row->column_comment;
			$i ++;
		}
		
		$this->connectToDB ();
		
		return $comments;
	}
	
	/**
	 * Speichert die Inhalte erneut.
	 */
	function saveObjects() {
		if($this->userHasRight("51", 0) == true) {
			
			// UPDATE TABLE ENTRY
			if (isset ( $_POST ['ok'] )) {
				if($this->userHasRight("52", 0) == true) {
					$table = $_GET ['table'];
					$currentObject = $_POST ['currentObject'];
					$columns = $this->getColumns ( $table );
					$id = $_GET ['id'];
					
					// Menge bekommen
					$query = "SHOW COLUMNS FROM $table";
					$menge = $this->getAmount ( $query );
					
					$query = "UPDATE $table SET ";
					
					for($i = 0; $i < $menge; $i ++) {
						$query .= "$columns[$i]='$currentObject[$i]'";
						if ($i != $menge - 1) {
							$query .= ", ";
						} else {
							$query .= " ";
						}
					}
					$query .= " WHERE id='$id'";
					$ergebnis = mysql_query ( $query ) or die ( mysql_error () );
					
					if ($ergebnis == true) {
						$this->insertQuery ( $query );
						echo "<p class='erfolg'>Update erfolgt</p>";
					} else {
						echo "<p class='info'>" . $query . "</p>";
					}
				} else {
					echo "<p class='meldung'>Keine Berechtigung ein Update in der Objektverwaltung durchzuführen!</p>";
				}
			}
			
			// LÖSCHEN AUS TABLE
			if (isset ( $_POST ['loeschen'] )) {
				if($this->userHasRight("53", 0) == true) {
					$table = $_GET ['table'];
					$id = $_GET ['id'];
					$sql = "DELETE FROM $table WHERE id='$id'";
					$del = mysql_query ( $sql );
					if ($del == true) {
						$this->insertQuery ( $sql );
						echo "<p class='erfolg'>Datensatz wurde gelöscht.";
						echo "<a href='?action=3' class='buttonlink'>Zurück</a></p>";
					} else {
						echo "<p class='meldung'>Fehler</p>";
					}
				} else {
					echo "<p class='meldung'>Keine Berechtigung in der Objektverwaltung zu löschen!</p>";
				}
			}
			
			// INSERT INTO TABLE
			if (isset ( $_POST ['insertOK'] )) {
				
				if($this->userHasRight("54", 0) == true) {
				
					$table = $_GET ['table'];
					$currentObject = $_POST ['currentObject'];
					$columns = $this->getColumns ( $table );
					
					// Menge bekommen
					$query = "SHOW COLUMNS FROM $table";
					$menge = $this->getAmount ( $query );
					
					// Query bauen
					$query = "INSERT INTO $table (";
					for($i = 0; $i < $menge; $i ++) {
						$query .= "" . $columns [$i] . "";
						if ($i != $menge - 1) {
							$query .= ", ";
						} else {
							$query .= ") VALUES (";
						}
					}
					$i = 0;
					for($i = 0; $i < $menge; $i ++) {
						$query .= "'$currentObject[$i]'";
						if ($i != $menge - 1) {
							$query .= ", ";
						} else {
							$query .= ")";
						}
					}
					
					$this->insertQuery ( $query );
					
					$ergebnis = mysql_query ( $query ) or die ( mysql_error () );
					
					if ($ergebnis == true) {
						echo "<p class='erfolg'>Datensatz eingefügt</p>";
					} else {
						echo "<p class='meldung'>Fehler</p>";
						echo mysql_error ();
					}
				} else {
					echo "<p class='meldung'>Keine Berechtigung in der Objektverwaltung Einträge zu erstellen!</p>";
				}
			}
		} else {
			echo "<p class=''>Du darfst Objekte nicht abspeichern.</p>";
		}
	}
	
	/**
	 * Zeigt eine Datenzeile aus einer Tabelle an.
	 */
	function showObject() {
		if($this->userHasRight("55", 0) == true) {
			if (isset ( $_GET ['id'] ) and isset ( $_GET ['table'] )) {
				$table = $_GET ['table'];
				$id = $_GET ['id'];
				
				// Menge bekommen
				$query = "SHOW COLUMNS FROM $table";
				$menge = $this->getAmount ( $query );
				
				$select = "SELECT * FROM $table WHERE id = '$id'";
				$ergebnis = mysql_query ( $select );
				
				// Kommentar der Spalte auslesen:
				$comments = $this->getColumnComments ( $table );
				
				// Columns bekommen:
				$columns = $this->getColumns ( $table );
				$i = 0;
				$this->insertQuery ( $select );
				
				if(isset($_GET['von'])) {
					$von = "&von=" . $_GET['von'];
				} else {
					$von = "";
				}
				echo "<table class='flatnetTable'><form method=post>";
				echo "<thead>" . "<td>Table: $table</td>" . "<td><a name='angezeigteID'>Angezeigte ID</a>: $id <a href='?action=3$von&table=$table' class='highlightedLink'>X</a></td>" . "</thead>";
				while ( $row = mysql_fetch_object ( $ergebnis ) ) {
					for($i = 0; $i < $menge; $i ++) {
						if (strlen ( $row->$columns [$i] ) > 50) {
							echo "<tbody><td>" . $columns [$i] . "</td>
							<td><textarea rows=10 cols=100 name=currentObject[$i]";
							echo ">" . $row->$columns [$i] . "</textarea>$comments[$i]</td></tbody>";
						} else {
							echo "<tbody><td>" . $columns [$i] . "</td><td><input type=text class='' name=currentObject[$i] value='";
							echo $row->$columns [$i];
							
							echo "' placeholder='$columns[$i]'/> $comments[$i]</td></tbody>";
						}
					}
				}
				
				echo "<tfoot><td><input type=submit name=ok value='speichern' /></td><td><input type=submit name=loeschen value='sofort löschen' /></td></tfoot>";
				
				echo "</form></table>";
			}
		} else {
			echo "<p class=''>Du Darf keine Objekte in der Objektverwaltung anzeigen</p>";
			
		}
	}
	
	function tableStructure($table) {
		if (isset ( $_GET ['table'] ) and isset ( $_GET ['showStructure'] )) {
			// Aktuelle DB schließen
			mysql_close ();
			
			// Neue DB öffnen
			$this->connectToSpecialDB ( "information_schema", "flathacksql1", "f12245f8@sql" );
			
			// Columns bekommen:
			$select1 = "SHOW COLUMNS FROM columns";
			$ergebnis1 = mysql_query ( $select1 );
			$i = 0;
			while ( $row = mysql_fetch_object ( $ergebnis1 ) ) {
				$columns [$i] = $row->Field;
				$i ++;
			}
			
			if (sizeof ( $columns ) > 5) {
				$changedColumns = true;
				if (isset ( $_GET ['setWeitere'] ) and $_GET ['setWeitere'] == true) {
					$changedColumns = false;
				} else {
					$menge = 5;
				}
			}
			
			// Table erstellen
			echo "<table class='flatnetTable'>";
			echo "<thead>";
			$i = 0;
			for($i = 0; $i < sizeof ( $columns ); $i ++) {
				echo "<td>";
				echo $columns [$i];
				echo "</td>";
			}
			echo "</thead>";
			
			// Select des Inhalts
			$select1 = "SELECT * FROM columns WHERE TABLE_SCHEMA = 'flathacksql1' AND TABLE_NAME='$table'";
			$ergebnis1 = mysql_query ( $select1 );
			while ( $row = mysql_fetch_object ( $ergebnis1 ) ) {
				echo "<tbody>";
				$j = 0;
				for($j = 0; $j < sizeof ( $columns ); $j ++) {
					echo "<td>";
					echo substr ( $row->$columns [$j], 0, 30 );
					echo "</td>";
				}
				echo "</tbody>";
			}
			
			// Normale Datenbank wieder öffnen.
			$this->connectToDB ();
		}
	}
	
	/**
	 * Ermöglicht die direkte manipulation der Datenbank über das Webinterface.
	 */
	function insertQuery($query) {
		echo "<div class='sqlbox'>";
		if (! isset ( $query )) {
			$query = "";
		}
		echo "<textarea cols='120' name='sqlbox' placeholder='INSERT SQL HERE'>$query</textarea>";
		echo "</div>";
	}
	
	/**
	 * Fügt eine neue Zeile ein.
	 * 
	 * @param unknown $table        	
	 */
	function insertIntoTable($table) {
		
		if($this->userHasRight("54", 0) == true) {
			
			// Menge bekommen
			$query = "SHOW COLUMNS FROM $table";
			$menge = $this->getAmount ( $query );
			
			$ergebnis = mysql_query ( $query );
			// Columns bekommen:
			$columns = $this->getColumns ( $table );
			$i = 0;
			
			if(isset($_GET['von'])) {
				$von = "&von=" . $_GET['von'];
			} else {
				$von = "";
			}
			
			echo "<table class='flatnetTable'><form method=post>";
			echo "<thead>" . "<td>Table: $table</td>" . "<td><a href='?action=3$von&table=$table' class='highlightedLink'>X</a></td>" . "</thead>";
			for($i = 0; $i < $menge; $i ++) {
				echo "<tbody><td>" . $columns [$i] . "</td><td><input type=text class='' name=currentObject[$i] value=''
				placeholder='$columns[$i]'/></td></tbody>";
			}
			
			echo "<tfoot><td><input type=submit name=insertOK value='speichern' /></td><td></td></tfoot>";
			
			echo "</form></table>";
		} else {
			echo "<p class=''>Du darfst keine neuen Einträge in der Objektdatenbank hinzufügen.</p>";
		}
		
	}
	
	/**
	 * Zeigt eine QueryBox an, mit deren Hilfe man jede Art von Abfrage starten kann.
	 */
	function globalQueryBox() {
		if($this->userHasRight("56", 0) == true) {
			echo "<div class='sqlbox'>";
			if (isset ( $_POST ['sqlbox'] )) {
				$query = $_POST ['sqlbox'];
			} else {
				$query = "";
			}
			echo "<form method=post>";
			echo "<textarea cols='125' rows='3' name='sqlbox' placeholder='SQL Query hier eingeben, UPDATE, DELETE, INSERT'>$query</textarea>";
			echo "<br><br><input type=submit name=globalqueryok value='Absenden' />";
			echo "</div>";
			echo "</form>";
			
			if (isset ( $_POST ['globalqueryok'] )) {
				$query = $_POST ['sqlbox'];
				
				$sqlquery = mysql_query ( $query ) or die ( mysql_error () );
				if ($sqlquery == true) {
					echo "<p class='erfolg'>SQL Query wurde erfolgreich durchgeführt.</p>";
				} else {
					echo "<p class='meldung'>Es ist ein Fehler aufgetreten: <br>";
					echo mysql_error ();
					echo "</p>";
				}
				
				// Abstandshalter nach unten:
				echo "<br><br><br>";
			}
		} else {
			echo "<p class=''>Du kannst die Global Query Box nicht anzeigen.</p>";
		}
	}
	
	/**
	 * Ermöglicht so etwas ähnliches wie PHP MY ADMIN.
	 * Aber Designtechnisch auf meinem Niveau.
	 */
	function objektControl() {
		if($this->userHasRight("51", 0) == true) {
			
			// Ausgabe aller Tables
			$select = "SHOW TABLES FROM flathacksql1";
			$ergebnis = mysql_query ( $select );
			echo "<form method=get action='?action=3'>";
			echo "<input type=hidden name=action value=3 />";
			echo "<select class='bigSelect' name='table'>";
			while ( $row = mysql_fetch_object ( $ergebnis ) ) {
				echo "<option ";
				if (isset ( $_GET ['table'] ) and $_GET ['table'] == $row->Tables_in_flathacksql1) {
					echo " selected ";
				} else {
					echo "";
				}
				echo " value='$row->Tables_in_flathacksql1' >$row->Tables_in_flathacksql1</option>";
			}
			echo "</select><input type=submit name='submit' value='Table wechseln'/></form>";
			// Table Variable zuweisen
			if (isset ( $_GET ['table'] )) {
				$table = $_GET ['table'];
				// Optionen für ausgewählter Table anzeigen
				echo "<br><a href='?action=3&table=$table&showStructure#struktur' class='greenLink'>Struktur</a>";
			}
			
			// Objekte speichern
			$this->saveObjects ();
			echo "<h2>Objekt Verwaltung</h2>";
			
			$overrideSetAllColumns = false;
			$changedColumns = true;
			
			// Strukturverwaltung
			if (isset ( $_GET ['table'] )) {
				$this->tableStructure ( $table );
			}
			
			// Objektbetrachtung.
			$this->showObject ();
			
			// Wenn ein Table angeklickt wurde.
			if (isset ( $_GET ['table'] )) {
				$table = $_GET ['table'];
				// Columns bekommen
				
				// Spalten der Query abfangen, damit diese unten in der Tabelle angezeigt werden können,
				// ansonsten werden einfach die Spalten der Tabelle abgefragt.
				if (isset ( $_POST ['sqlbox'] )) {
					$query = $_POST ['sqlbox'];
					$columns = $this->getColumnsFromQuery ( $query );
					
					// Menge für die Spalten bekommen
					$menge = $this->getColumnAnzahl ( $query );
					
					// alle Spalten anzeigen:
					$changedColumns = false;
					$overrideSetAllColumns = true;
				} else {
					$columns = $this->getColumns ( $table );
					
					// Menge bekommen
					$query = "SHOW COLUMNS FROM $table";
					$menge = $this->getAmount ( $query );
				}
				
				// Spaltenbegrenzung;
				if ($menge > 5 and $overrideSetAllColumns == false) {
					$changedColumns = true;
					if (isset ( $_GET ['setWeitere'] ) and $_GET ['setWeitere'] == true) {
						$changedColumns = false;
					} else {
						$menge = 5;
					}
				}
				
				if(isset($_GET['von'])) {
					$von = "&von=" . $_GET['von'];
				} else {
					$von = "";
				}
				
				// Optionsknöpfe
				echo "<div>";
				if($this->userHasRight("56", 0) == true) {
					echo "<a href='?action=3$von&globalQuery&table=$table#administration' class='buttonlink'>manuelle Query</a>"; 
				}
				
				if($this->userHasRight("54", 0) == true) {
					echo "<a href='?action=3$von&table=$table&insert#administration' class='greenLink'>INSERT</a>";
				}
				
				echo "</div>";
				
				// Allesknopf gedrückt?
				if (isset ( $_GET ['alle'] )) {
					$proSeite = 100000;
				} else {
					$proSeite = 15;
				}
				
				// ####################################################################################################
				// VON BIS HERAUSFINDEN:
				if (! isset ( $_GET ['von'] )) {
					// von
					$count1 = 0;
				} else {
					$count1 = $_GET ['von'];
					if ($count1 == "") {
						$count1 = 1;
					}
					$count1 = $count1 * $proSeite - 1 * $proSeite;
				}
				// SQL Abfrage abfangen ###############################################################################
				if (isset ( $_POST ['newQuery'] )) {
					$select = $_POST ['sqlbox'];
				} else {
					$select = "SELECT * FROM $table ORDER BY $columns[0] ASC LIMIT $proSeite OFFSET $count1";
				}
				
				// #####################################################################################################
				
				// Anzeigen, wie groß die menge für den gesamten Table ist
				$pageSelect = "SELECT * FROM $table";
				$menge2 = $this->getAmount ( $pageSelect );
				
				// Seitenanzahl bestimmen
				$seitenAnzahl = ceil ( $menge2 / $proSeite );
				
				// Seitenanzahl der Query bestimmen
				// #####################################################################################################
				
				echo "<form action='#administration' method=get>";
				echo "<a class='seitenLink' href='?action=3&table=$table&alle'>Alle</a>";
				
				for($a = 1; $a <= $seitenAnzahl; $a ++) {
					
					if ($a > 3 and $a < $seitenAnzahl - 2) {
						// bis zu vier seiten dürfen angezeigt werden.
						if ($a == 4) {
							echo "<input type=hidden name=action value='3' />";
							echo "<input type=hidden name=table value='$table' />";
							echo "<input type=text class='smallInput' name=von value='' placeholder='Seite' />";
							$showOK = true;
						}
					} else {
						// Buttonfilter: ausgewählt oder nicht?
						echo "<a  ";
						if (isset ( $_GET ['von'] )) {
							if ($_GET ['von'] == $a) {
								echo " class='seitenLinkActive' ";
							} else {
								echo " class='seitenLink' ";
							}
						} else {
							if ($a == 1) {
								echo " class='seitenLinkActive' ";
							} else {
								echo " class='seitenLink' ";
							}
						}
						echo "href='?action=3&table=$table&von=$a#administration'>$a</a>";
					} // If seitenanzahl > 3 ENDE
				} // FOR ENDE
				  // ####################### ENDE SEITEN ANZEIGE #################################
				  
				// INSERT INTO
				if (isset ( $showOK ) and $showOK == true) {
					echo "<input type=submit name=showpage value='ok' /> ";
				}
				echo "</form>";
				
				if (isset ( $_GET ['insert'] )) {
					$this->insertIntoTable ( $table );
				}
				
				// Ergebnis der SQL Abfrage
				$ergebnis = mysql_query ( $select );
				$abfrageMenge = $this->getAmount ( $pageSelect );
				$currentSelect = $this->getAmount ( $select );
				
				// SQL BOX
				echo "<form method=post>";
				echo "<input type=submit name=newQuery value='SELECT' />";
				$this->insertQuery ( $select );
				echo "</form>";
				
				// Ausgabe der Tabelle
				
				# Schauen, ob eine bestimmte Seite angeklickt wurde:
				if(isset($_GET['von'])) {
					$von = "&von=" . $_GET['von'];
				} else {
					$von = "";
				}
				
				echo "<table class='flatnetTable'>";
				echo "<thead><td colspan='8'>Einträge in $table: $abfrageMenge / Momentane Abfrage: $currentSelect</td></thead>";
				echo "<thead>";
				echo "<td>anzeigen</td>";
				for($j = 0; $j < $menge; $j ++) {
					echo "<td>$columns[$j]</td>";
				}
				if ($changedColumns == true) {
					echo "<td>verborgen</td>";
				}
				
				echo "</thead> ";
				
				// Gibt den Tabellen Inhalt zeilenweise aus.
				while ( $row = mysql_fetch_object ( $ergebnis ) ) {
					echo "<tbody>";
					echo "<td><a href='?action=3$von&table=$table&id=$row->id#angezeigteID' class='buttonlink'>EDIT</a></td>";
					for($j = 0; $j < $menge; $j ++) {
						echo "<td>";
						echo substr ( strip_tags($row->$columns [$j]), 0, 30 );
						echo "</td>";
					}
					if ($changedColumns == true) {
						echo "<td><a href='?setWeitere=true&action=3$von&table=$table'>... weitere</a></td>";
					}
					echo "</tbody>";
				}
				echo "<table>";
			}
		} else {
			echo "<p class=''>Du darfst die Objektverwaltung nicht verwenden.</p>";
		}
	}
	
	/**
	 * Ermöglicht die Erstellung von Codes, damit sich User registrieren können.
	 */
	function codeVerwaltung() {
		if($this->userHasRight("57", 0) == true) {
			echo "<h2>Codeverwaltung</h2>";
			echo "<a href='/flatnet2/admin/control.php?action=3&table=registercode' class='buttonlink'>Codes anzeigen</a><a href='?action=1&createNewCode=1' class='buttonlink'>Neuen Code</a>";
			
			if (isset ( $_GET ['createNewCode'] )) {
				echo "<form method=post>";
				echo "<input type=text name=newCode value='' placeholder='Einladungscode eingeben' />";
				echo "<input type=number name=usageTimes value='' placeholder='Erlaubte Nutzungen' />";
				echo "<input type=submit name=newCodeAbsenden value='Erstellen' />";
				echo "</form>";
				
				if (isset ( $_POST ['newCodeAbsenden'] ) and isset ( $_POST ['newCode'] ) and $_POST ['newCode'] != "" and $_POST ['usageTimes'] > 0) {
					$code = $_POST ['newCode'];
					$usageTimes = $_POST ['usageTimes'];
					$query = "INSERT INTO registercode (code, used, usageTimes) VALUES ('$code','0','$usageTimes')";
					$ergebnis = mysql_query ( $query );
					if ($ergebnis == true) {
						echo "<p class='erfolg'>Code eingefügt</p>";
					} else {
						echo "<p class='meldung'>Fehler</p>";
					}
				}
			}
		} else {
			echo "<p class=''>Du darfst die Codeverwaltung nicht anzeigen.</p>";
		}
	}
	
	/**
	 */
	function getBesitzerColumnName($table, $tableNumber) {
		$columnNamesForBesitzer = array (
				'besitzer',
				'xxx',
				'xxx',
				'autor',
				'xxx',
				'autor',
				'autor',
				'besitzer',
				'besitzer',
				'besitzer',
				'besitzer',
				'besitzer',
				'besitzer',
				'xxx',
				'besitzer',
				'besitzer',
				'xxx',
				'besitzer',
				'ersteller',
				'xxx',
				'xxx',
				'xxx',
				'besitzer',
				'xxx',
				'xxx' 
		);
		
		$column = $columnNamesForBesitzer [$tableNumber];
		
		if ($column == "xxx") {
			$column = "";
		}
		
		return $column;
	}
	
	/**
	 * Zeigt Einträge an, bei denen der Benutzer nicht mehr existiert und bietet an, diese zu löschen.
	 */
	function Aufraeumen() {
		if($this->userHasRight("58", 0) == true) {
			if (isset ( $_POST ['loeschid'] ) and isset ( $_POST ['table'] ) and isset ( $_POST ['endgueltigLoeschen'] ) and isset ( $_POST ['tableNumber'] )) {
				echo "<p class='info'>Es wird gelöscht ....</p>";
				$table = $_POST ['table'];
				$userID = $_POST ['loeschid'];
				$tableNumber = $_POST ['tableNumber'];
				$column = $this->getBesitzerColumnName ( $table, $tableNumber );
				
				if ($column == "") {
					exit ();
				}
				$query = "DELETE FROM $table WHERE $column = $userID";
				if ($this->sql_insert_update_delete ( $query ) == true) {
					echo "<p class='erfolg'>Benutzerdaten wurden gelöscht.</p>";
				}
			}
			
			if (isset ( $_GET ['loeschen'] ) and isset ( $_GET ['loeschid'] ) and isset ( $_GET ['table'] ) and isset ( $_GET ['tableNumber'] )) {
				echo "<div class='meldung'>";
				
				echo "<p>Der Benutzer mit der ID " . $_GET ['loeschid'] . "</p>";
				echo "<p>wird aus dem table <strong>" . $_GET ['table'] . "</strong> gelöscht!</p>";
				if($this->userHasRight("58", 0) == true) {
					echo "<form method=post>";
					echo "<input type=hidden name=loeschid value='" . $_GET ['loeschid'] . "' />";
					echo "<input type=hidden name=tableNumber value='" . $_GET ['tableNumber'] . "' />";
					echo "<input type=hidden name=table value='" . $_GET ['table'] . "' />";
					echo "<input type=submit name=endgueltigLoeschen value='Fortfahren' />";
					echo "</form>";
				}
				
				echo "</div>";
			}
		} else {
			echo "<p class=''>Du darfst die Aufräumfunktion nicht ausführen</p>";
		}
	}
	
	/**
	 * Dieses Rechtesystem ersetzt das bestehende System. Dabei ist es egal, wie viele Rechte der Benutzer hat.
	 * Dabei wird die entsprechende Rechte ID und die Benutzer ID in die Tabelle rights gepackt.
	 * Wenn der Benutzer das Recht besitzt, dann wird true zurück gegegeben. Ist das Recht nicht vorhanden ist es automatisch
	 * verweigert.
	 */
	function rechteverwaltung() {
		if($this->userHasRight("44", 0) == true) {
			echo "<h2>Neue Rechteverwaltung</h2>";
			
			if($this->userHasRight("15", 0) == true) {
				$this->newRight();
			}
			
			if($this->userHasRight("46", 0) == true) {
				$this->rechteverwaltung_setright();
			
			
				# Liste von allen Benutzern anzeigen:
				#####################################################################################################################
				$allusers = $this->getObjectsToArray("SELECT * FROM benutzer");														#
																																	#
				for ($i = 0 ; $i < sizeof($allusers) ; $i++) {																		#
					echo "<a class='buttonlink' href='?action=6&userid=".$allusers[$i]->id."'>".$allusers[$i]->Name."</a>";			#
				}																													#
				#####################################################################################################################
			
				# Wenn ein Benutzer angeklickt wurde:
				if(isset($_GET['userid'])) {
					$id = $_GET['userid'];
					$userInformation = $this->getObjektInfo("SELECT * FROM benutzer WHERE id = '$id'");
					if(!isset($userInformation->Name)) {
						echo "<p class='meldung'>Diesen Benutzer gibt es nicht</p>";
					} else {
						echo "<h2>Benutzer: $userInformation->Name</h2>";
					}
					
					# Liste erstellen, aber mit Kategorien:
					
					$getAllKategorien = $this->getObjectsToArray("SELECT * FROM rightkategorien");
					
					echo "<table class='flatnetTable'>";
					
					for ($i = 0; $i < sizeof($getAllKategorien); $i++) {
						
						# Jetzt alle Einträge die zu dieser Kategorie gehören selektieren:
						$getEintraegeVonDieserKategorie = $this->getObjectsToArray("SELECT * FROM userrights WHERE kategorie = '".$getAllKategorien[$i]->id."' ORDER BY recht");
						
						echo "<thead>";
							echo "<td>" . $getAllKategorien[$i]->name . "</td>";
							echo "<td>Optionen</td>";
							echo "<td>Edit</td>";
						echo "</thead>";
						
						# Jetzt Einträge ausgeben:
						for($j = 0; $j < sizeof($getEintraegeVonDieserKategorie);$j++) {
								echo "<tbody";
								if($this->userHasRight($getEintraegeVonDieserKategorie[$j]->id, $userInformation->id) == true) {
									echo " id = 'offen' ";
								} else {
									echo " id = '' ";
								}
								echo ">";
								echo "<td>" .$getEintraegeVonDieserKategorie[$j]->recht. "</td>";
								
								echo "<td>";
								if($this->userHasRight($getEintraegeVonDieserKategorie[$j]->id, $userInformation->id) == true) {
									echo "<a class='' href='?action=6&userid=".$userInformation->id."&verweigern=".$getEintraegeVonDieserKategorie[$j]->id."'>verweigern</a>";
								} else {
									echo "<a class='' href='?action=6&userid=".$userInformation->id."&gewaehren=".$getEintraegeVonDieserKategorie[$j]->id."'>gewähren</a><br>";
								}
								
								echo "</td>";
								
								echo "<td>" . "<a href='?action=3&table=userrights&id=".$getEintraegeVonDieserKategorie[$j]->id."'>EDIT</a>" . "</td>";
								
							echo "</tbody>";
						}
					}
					
					echo "</table>";
					
				} 
			} # Recht 46 Ende
		} else {
			echo "<p class=''>Du darfst die Rechteverwaltung nicht anzeigen.</p>";
		}
		
	}
	
	/**
	 * Verweigert oder gewährt eine Berechtigung.
	 */
	function rechteverwaltung_setright() {
		
		if($this->userHasRight("46", 0) == true) {
			
			if(isset($_GET['userid']) AND isset($_GET['gewaehren'])) {
			#	echo "<p class='meldung'>Recht wird gewährt!</p>";
				
				# Check ob es Benutzer gibt..
				$userid = $_GET['userid'];
				$userInformation = $this->getObjektInfo("SELECT * FROM benutzer WHERE id = '$userid'");
				if(!isset($userInformation->Name)) {
					echo "<p class='meldung'>Diesen Benutzer gibt es nicht</p>";
					exit;
				}
				
				# Check ob es Recht gibt...
				$rechteID = $_GET['gewaehren'];
				$rightInformation = $this->getObjektInfo("SELECT * FROM userrights WHERE id = '$rechteID'");
				if(!isset($rightInformation->id)) {
					echo "<p class='meldung'>Dieses Recht gibt es nicht!</p>";
					exit;
				}
				
				# Check ob der Benutzer das Recht schon hat
				$getAllRights = $this->getObjektInfo("SELECT * FROM userrights WHERE id = '$rechteID'");
				$userInformation = $this->getObjektInfo("SELECT * FROM benutzer WHERE id = '$userid'");
				if($this->userHasRight($getAllRights->id, $userInformation->id) == true) {
					echo "<p class='meldung'>Der Benutzer hat das Recht bereits!</p>";
					exit;
				}
				
				# Recht gewähren: 
				$query = "INSERT INTO rights (besitzer, right_id) VALUES ('$userid','$rechteID')";
				if($this->sql_insert_update_delete($query) == true) {
					echo "<p class='erfolg'>Recht wurde gewährt!</p>";
				} else {
					echo "<p class='meldung'>Es gab einen Fehler beim speichern.</p>";
				}
				
			}
			
			if(isset($_GET['userid']) AND isset($_GET['verweigern'])) {
				
				# Check ob es Benutzer gibt..
				$userid = $_GET['userid'];
				$userInformation = $this->getObjektInfo("SELECT * FROM benutzer WHERE id = '$userid'");
				if(!isset($userInformation->Name)) {
					echo "<p class='meldung'>Diesen Benutzer gibt es nicht</p>";
					exit;
				}
					
				# Check ob es Recht gibt...
				$rechteID = $_GET['verweigern'];
				$rightInformation = $this->getObjektInfo("SELECT * FROM userrights WHERE id = '$rechteID'");
				if(!isset($rightInformation->id)) {
					echo "<p class='meldung'>Dieses Recht gibt es nicht!</p>";
					exit;
				}
					
				# Check ob der Benutzer das Recht schon hat
				$getAllRights = $this->getObjektInfo("SELECT * FROM userrights WHERE id = '$rechteID'");
				$userInformation = $this->getObjektInfo("SELECT * FROM benutzer WHERE id = '$userid'");
				if($this->userHasRight($getAllRights->id, $userInformation->id) == false) {
					echo "<p class='meldung'>Der Benutzer hat das Recht nicht, also kann es ihn auch nicht verweigert werden!</p>";
					exit;
				}
					
				# Recht LÖSCHEN:
				$query = "DELETE FROM rights WHERE besitzer = '$userid' AND right_id = '$rechteID'";
				if($this->sql_insert_update_delete($query) == true) {
					echo "<p class='erfolg'>Recht wurde gelöscht!</p>";
				} else {
					echo "<p class='meldung'>Es gab einen Fehler beim speichern.</p>";
				}
				
			}
		} else {
			echo "<p class=''>Du darfst keine Rechte in der Rechteverwaltung vergeben.</p>";
		}
	}
	
	/**
	 * Ermöglicht die Verwaltung von Kategorien für die Rechte
	 */
	function rechtekategorienVerwaltung() {
		
		if($this->userHasRight("68", 0) == true) {
		
			# Alle Kategorien bekommen:
			$getallcategories = $this->getObjectsToArray("SELECT * FROM rightkategorien");
			echo "<p class='spacer'>Rechtekategorien</p>";
			echo "<table class='flatnetTable'>";
			for ($i = 0 ; $i < sizeof($getallcategories) ; $i++) {
				echo "<tbody>";
					echo "<td>" . $getallcategories[$i]->id  . "</td>";
					echo "<td>" . $getallcategories[$i]->name  . "</td>";
					echo "<td>" . "<a href='?action=3&table=rightkategorien&id=".$getallcategories[$i]->id."'>EDIT</a>" . "</td>";
				echo "</tbody>";
			}
			echo "</table>";
		
		}
	}
	
}