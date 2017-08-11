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
			$this->Aufraeumen ();
		}
		
		/*
		 * Vorschlaege anzeigen
		 */
		if ($action == 2) {
			$this->adminVorschlaegeDELETE_Eintraege ();
			$this->adminVorschlaege();
			$this->log_verwaltung();
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
		
		
		/*
		 * Finanzbereich
		 */
		if ($action == 7) {
			$this->FinanzVerwaltung();
		}
	}
	
	/**
	 * Zeigt alle Benutzer aus der Tabelle "benutzer" an.
	 */
	function showBenutzer() {
		
		if($this->userHasRight(37, 0) == true) {
			
			echo "<div class='newFahrt'>";
		
			echo "<table class='logTable'>";
			echo "<thead>
					<td>ID</td>
					<td>Benutzername</td>
					<td>Titel</td>
					<td><a href='?action=6'>Rechte</a>
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
			
			$row = $this->getObjektInfo($benutzerliste);
			for ($i = 0 ; $i < sizeof($row) ; $i++) {
				
				echo "<tbody";
				
				if (isset ( $_GET ['bearb'] )) {
					if ($_GET ['bearb'] == $row[$i]->id) {
						echo " id='offen' ";
					}
				}
				echo ">";
				echo "<td>".$row[$i]->id."</td>";
				echo "<td><a href='?bearb=". $row[$i]->id."&userverw=1#bearbeiten'>".$row[$i]->Name."</a></td>";
				if(isset($row[$i]->titel) AND $row[$i]->titel != "") {
					$titel = "id='smallLink' class='rightRedLink'";
				} else {
					$titel = "";
				}
				echo "<td><span $titel>".$row[$i]->titel."</span></td>";
				echo "<td>".$row[$i]->rights."</td>";
				echo "<td>".$row[$i]->forumRights."</td>";
				if ($row[$i]->versuche == 3) {
					echo "<td>gesperrt</td>";
				} else {
					echo "<td>".$row[$i]->versuche."</td>";
				}
				echo "<td>
				<a href='?bearb=".$row[$i]->id."&userverw=1' class='rightBlueLink'>Edit</a>
				<a href='?statusID=".$row[$i]->id."&status=entsperren&userverw=1&action=1' class='rightGreenLink'>entsperren</a>
				<a href='?statusID=".$row[$i]->id."&status=sperren&userverw=1&action=1' class='rightRedLink'>sperren</a>
				</td>";
				echo "</tbody>";
			}
			echo "</table>";
		} else {
			echo "<p class=''>Du darfst die Benutzerverwaltung nicht anzeigen</p>";
		}
		
		echo "</div>";
	}
	
	/**
	 * Ermöglicht das hinzufügen eines neuen Benutzers.
	 */
	function newBenutzerEingabe() {
		if($this->userHasRight(38, 0) == true) {
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
		if($this->userHasRight(38, 0) == true) {
			if (isset ( $_POST ['passwort'] ) and isset ( $_POST ['passwort2'] ) and isset ( $_POST ['username'] ) and isset ( $_POST ['regnewuser'] )) {
				
				// RightCheck:
				if($this->userHasRight(38, 0) == true) {
					// Deklaration der Variablen
					$passwort1 = $_POST ['passwort'];
					$passwort2 = $_POST ['passwort2'];
					$username = $_POST ['username'];
					$regnewuser = $_POST ['regnewuser'];
					if ($username == "" or $passwort1 == "" or $passwort2 == "") {
						echo "<p class='meldung'>Fehler, es wurden nicht alle Felder ausgefüllt.</p>";
					} else {
						$check = "SELECT * FROM benutzer WHERE Name LIKE '$username' LIMIT 1";
						$row = $this->getObjektInfo($check);
						
						/*
						 * Hier tritt ein Notice Fehler auf, ist aber normal,
						 * da im Normalfall kein Benutzer gefunden wird.
						 */
						if (isset ( $row[0]->Name )) {
							echo "<p class='meldung'>Fehler, der Benutzer <strong>$username</strong> existiert bereits.</p>";
						} else {
							if ($passwort1 == $passwort2) {
								$passwortneu = md5 ( $passwort1 );
								$query = "INSERT INTO benutzer (Name, Passwort, rights, versuche, forumRights) VALUES ('$username','$passwortneu','1','3','1')";
								
								if ($this->sql_insert_update_delete($query) == true) {
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
		if($this->userHasRight(39, 0) == true) {
			if (isset ( $_GET ['bearb'] )) {
				$bearb = $_GET ['bearb'];
				if ($bearb) {
					echo "<div class='newFahrt'>";
					echo "<h2><a name='bearbeiten'>Benutzerbearbeitung</a> <a href='?userverw=1' class='rightRedLink'>X</a></h2>";
					$userinfo = "SELECT timestamp, id, Name, Passwort, titel, forumRights FROM benutzer WHERE id = $bearb";
					$row = $this->getObjektInfo($userinfo);
					echo "<table>";
					
					$i = 0;
					
					echo "<form action='?' method=post>";
					echo "<tr><td>Neuer Benutzername: </td><td><input type=text value='".$row[$i]->Name."' name=newname autofocus required></td></tr>";
					echo "<tr><td>Neues Passwort: </td><td><input type=password value='' name=newpass ></td></tr>";
					echo "<tr><td><input type=hidden value='".$row[$i]->id."' name=id readonly></td></tr>";
					echo "<tr><td><input type=hidden value='".$row[$i]->Name."' name=name readonly></td></tr>";
					echo "<tr><td>Titel: </td><td><input type=text value='".$row[$i]->titel."' name=titel></td></tr>";
					echo "<tr><td>Forum Rechte: </td><td><input type=text value='".$row[$i]->forumRights."' name=forumRights></td></tr>";
					echo "<tr><td><input type=submit name='bearbuser' value='Ausführen'></td>
							<td><input type=submit name='bearbuser' value='Benutzer Informationen anzeigen' />";
					echo "</td></tr>";
					echo "</form>";
					echo "</table>";
					
					
					echo "<h3>Diese Inhalte hat der Benutzer veröffentlicht </h3>";
					
					$columnNames = $this->setBesitzerArray();
					
					foreach ($columnNames as $tabelle => $spalte) {
						
						if($spalte != "xxx") {
							$query = "SELECT count(*) as anzahl FROM ".$tabelle." WHERE ".$spalte." = $bearb ";
							$amount = $this->getObjektInfo($query);
								
							if($amount[0]->anzahl > 0) {
								echo "<div class='kleineBox'>";
								echo "<h3>".$tabelle."</h3>";
								echo "<p>Einträge: " .$amount[0]->anzahl. "</p>";
								echo "</div>";
							}
						}
					
					}
					
					echo "</div>";
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
			if($this->userHasRight(39, 0) == true) {
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
						$noPassChange = 0;
						$updatepassmd5 = md5 ( $updatepass );
					}
					$forumRights = $_POST ['forumRights'];
					$newUserName = $_POST ['newname'];
					$selectedid = $_POST ['id'];
					$titel = $_POST ['titel'];
					
					// Prüfen, ob die Daten überhaupt geändert wurden:
					$checkRights = "SELECT * FROM benutzer WHERE id = '$selectedid' LIMIT 1";
					$rightsCheck = $this->getObjektInfo($checkRights);
					
					if ($forumRights == $rightsCheck[0]->forumRights and $updatepass == "" and $newUserName == $rightsCheck[0]->Name and $titel == $rightsCheck[0]->titel) {
						echo "<p class='info'>Es gab keine Änderung</p>";
						return false;
					}
					
					// SQL Befehle
					if ($noPassChange == 1) {
						$sqlupdate = "UPDATE benutzer SET forumRights='$forumRights', Name='$newUserName', titel='$titel' WHERE id='$selectedid'";
					} else {
						$sqlupdate = "UPDATE benutzer SET Passwort='$updatepassmd5', forumRights='$forumRights', Name='$newUserName', titel='$titel' WHERE id='$selectedid'";
					}
					
					if ($this->sql_insert_update_delete($sqlupdate) == true) {
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
		if($this->userHasRight(40, 0) == true) {
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
					if ($this->sql_insert_update_delete($sql) == true) {
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
	 * Ordnet die Besitzer-Spalte einer Tabelle zu.
	 * @return string
	 */
	function setBesitzerArray() {
		
		/**
		 * Richtige Zuordnung
		 * @var unknown
		 */
		$zuordnung = array (
				"account_infos" => "besitzer",
				"adressbuch" => "xxx",
				"amazon_infos" => "autor",
				"benutzer" => "xxx",
				"blogtexte" => "autor",
				"blogkategorien" => "xxx",
				"blog_kommentare" => "autor",
				"docu" => "autor",
				"fahrkosten" => "besitzer",
				"fahrkostenziele" => "besitzer",
				"fahrzeuge" => "besitzer",
				"finanzen_jahresabschluss" => "besitzer",
				"finanzen_konten" => "besitzer",
				"finanzen_monatsabschluss" => "besitzer",
				"finanzen_umsaetze" => "besitzer",
				"gw_accounts" => "besitzer",
				"gw_chars" => "besitzer",
				"gwcosts" => "besitzer",
				"gwmatlist" => "xxx",
				"gwusersmats" => "besitzer",
				"learnkategorie" => "besitzer",
				"learnlernkarte" => "besitzer",
				"log" => "besitzer",
				"lernstatus" => "besitzer",
				"registercode" => "xxx",
				"rightkategorien" => "xxx",
				"rights" => "besitzer",
				"uebersicht_kacheln" => "xxx",
				"userrights" => "xxx",
				"vorschlaege" => "xxx"
		);
		
		/**
		 * Umgekehrte Zuordnung
		 * @var unknown
		 */
		$zuordnung2 = array (
				"besitzer" => "account_infos",
				"xxx" => "adressbuch",
				"autor" => "amazon_infos",
				"xxx" => "benutzer",
				"autor" => "blogtexte",
				"xxx" => "blogkategorien",
				"autor" => "blog_kommentare",
				"autor" => "docu",
				"besitzer" => "fahrkosten",
				"besitzer" => "fahrkostenziele",
				"besitzer" => "fahrzeuge",
				"besitzer" => "finanzen_jahresabschluss",
				"besitzer" => "finanzen_konten",
				"besitzer" => "finanzen_monatsabschluss",
				"besitzer" => "finanzen_umsaetze",
				"besitzer" => "gw_accounts",
				"besitzer" => "gw_chars",
				"besitzer" => "gwcosts",
				"xxx" => "gwmatlist",
				"besitzer" => "gwusersmats",
				"besitzer" => "learnkategorie",
				"besitzer" => "learnlernkarte",
				"besitzer" => "log",
				"besitzer" => "lernstatus",
				"xxx" => "registercode",
				"xxx" => "rightkategorien",
				"besitzer" => "rights",
				"xxx" => "uebersicht_kacheln",
				"xxx" => "userrights",
				"xxx" => "vorschlaege"
		);
				
		return $zuordnung;
	}
	
	/**
	 * Gibt den Namen der Besitzerspalte zurück.
	 * @param unknown $tabelle
	 */
	function getBesitzerSpalte($tabelle) {
		
		
	}
	
	/**
	 * Zeigt alle Informationen eines Benutzers an und ermöglicht die Löschung der bestehenden Informationen.
	 */
	function deleteBenutzerFunction() {
		
		if($this->userHasRight(41, 0) == true) {
			
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
			
			$columnNames = $this->setBesitzerArray();
			$userID = $_POST ['id'];
			
			$i = 0;
				
			foreach ($columnNames as $table => $spalte) {
				if ($spalte != "xxx") {
					// Table anzeigen:
					
					echo "<p class='dezentInfo'>Suche nach Spalte: ".$spalte."</p>";
				
					// Spalten bekommen
					$columns = $this->getColumns ( $table );
				
					// Informationen des aktuellen Tables auslesen
					$currentTableInfo = $this->getObjektInfo ( "SELECT * FROM $table WHERE $spalte = $userID" );
				
					// Table nur anzeigen, wenn etwas vom Benutzer da ist:
					// if(isset($currentTableInfo[$i])) {
				
					echo "<table class='logTable'>";
				
					// Überschrift anzeigen
					$spaltenanzahlMinusEins = sizeof ( $columns );
					echo "<thead><td colspan ='" . $spaltenanzahlMinusEins . "'>$i) " . $table . " (".$spalte.")" . "</td><td>
					<a class='rightGreenLink' href='?action=1&loeschen&loeschid=" . $userID . "&table=" . $table . "'>alle Einträge entfernen</a>
									</td></thead>";
					
					// Gibt die aktuelle Kopfzeile der aktuellen Tabelle aus
					echo "<thead>";
					echo "<td></td>";
					for($k = 0; $k < sizeof ( $columns ); $k++) {
						echo "<td>$columns[$k]</td>";
					}
					echo "</thead>";
					for($j = 0; $j < sizeof ( $currentTableInfo ); $j++) {
						echo "<tbody>";
						if(!isset($currentTableInfo[$j]->id)) {
							echo "<td><a class='' href='?action=3&table=$table'>open</a></td>";
						} else {
							echo "<td><a class='' href='?action=3&table=$table&id=".$currentTableInfo [$j]->id."'>open</a></td>";
						}
							
						// Gibt die Information der aktuellen Zelle aus:
						for($k = 0; $k < sizeof ( $columns ); $k ++) {
							echo "<td>";
							echo substr ( strip_tags($currentTableInfo [$j]->$columns [$k]), 0, 20 );
							echo "</td>";
						}
							
						echo "</tbody>";
					}
					echo "</table>";
					
					
				}
				$i++;
					
			}

		}
	}
	
	function getLogEintraege() {
		echo "<div class='newFahrt'>";
		echo "<h3>Logeinträge</h3>";
			
		$select = "SELECT count(*) as anzahl FROM `vorschlaege`";
		$row = $this->getObjektInfo($select);
			
		echo "Das Log umfasst " . $row[0]->anzahl . " Einträge.";
		echo "</div>";
	}
	
	/**
	 * Zeigt eingereichte Vorschläge der Benutzer an
	 */
	function adminVorschlaege() {
		if($this->userHasRight(42, 0) == true) {
			// Einfügen der Function
			if (isset ( $_GET ['submit'] ) and isset ( $_GET ['status'] ) and isset ( $_GET ['hiddenID'] )) {
				$this->vorschlaegeAction ( $_GET ['submit'], $_GET ['status'], $_GET ['hiddenID'] );
			}
			
			# Zeigt eine Box an, in der die Anzahl der Logeinträge angezeigt wird.
			$this->getLogEintraege();
			
			// Select from Database
			echo "<div class='newFahrt'>";
			echo "<h3>" . "Benutzervorschläge" . "</h3>";
			echo "<table class='logTable'>";
			echo "<thead>";
			echo "<td id='smallLaenge'>ID</td>
					<td id='smallLaenge'>Datum</td>
					<td id='smallLaenge'>Benutzer</td>
					<td>Text</td>
					<td id='fixedLaenge'>ART</td>
					<td id='smallLaenge'>Optionen</td>";
			echo "</thead>";
			
			// ILLEGALE AKTIONEN
			$vorschlaege = "SELECT
					id, timestamp, autor, text, status, year(timestamp) as jahr, month(timestamp) as monat, day(timestamp) as tage
					FROM vorschlaege
					WHERE status = 'illegal'
					ORDER BY timestamp DESC";
			$row = $this->getObjektInfo($vorschlaege);
			for($i = 0; $i < sizeof($row) ; $i++) {
				echo "<thead id='illegal'><td colspan = '6' >WARNUNG</td></thead>";
				echo "<form method=get>";
				echo "<input type=hidden name='action' value='2' />";
				echo "<tbody";
				
				if ($row[$i]->status == "offen") {
					echo " id='offen' ";
				} else if ($row[$i]->status == "illegal") {
					echo " id='illegal' ";
				} else if ($row[$i]->status == "Error") {
					echo " id='error' ";
				} else if ($row[$i]->status == "login") {
					echo " id='login' ";
				}
				
				echo ">
				<td>". $row[$i]->id." <input type=hidden value='".$row[$i]->id."' name='hiddenID' /></td>
				<td>".$row[$i]->tage.".".$row[$i]->monat.".".$row[$i]->jahr."</td>";
				if ($row[$i]->autor == 0) {
					echo "<td>System</td>";
				} else {
					echo "<td>" . $this->getUserName ( $row[$i]->autor ) . "</td>";
				}
				echo "<td>". $row[$i]->text . "</td>
				<td>
				<select name='status' value='' size='1'>";
				
				echo "<option";
				if ($row[$i]->status == "offen") {
					echo " selected='selected' ";
				}
				echo "> offen </option>";
				
				echo "<option";
				if ($row[$i]->status == "erledigt") {
					echo " selected='selected' ";
				}
				echo "> erledigt </option>";
				
				echo "<option";
				if ($row[$i]->status == "verworfen") {
					echo " selected='selected' ";
				}
				echo "> verworfen </option>";
				
				echo "<option";
				if ($row[$i]->status == "illegal") {
					echo " selected='selected' ";
				}
				echo "> illegal </option>";
				
				echo "<option";
				if ($row[$i]->status == "login") {
					echo " selected='selected' ";
				}
				echo "> login </option>";
				
				echo "<option";
				if ($row[$i]->status == "Error") {
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
			$vorschlaege = "SELECT id, timestamp, autor, text, status, year(timestamp) as jahr, month(timestamp) as monat, day(timestamp) as tage
					FROM vorschlaege
					WHERE status = 'offen'
					OR status = 'verworfen'
					OR status = 'erledigt'
					ORDER BY id ASC";
			$row = $this->getObjektInfo($vorschlaege);
			for($i = 0 ;  $i < sizeof($row) ; $i++) {
				echo "<form method=get>";
				echo "<input type=hidden name='action' value='2' />";
				echo "<tbody";
				
				if ($row[$i]->status == "offen") {
					echo " id='offen' ";
				} else if ($row[$i]->status == "illegal") {
					echo " id='illegal' ";
				} else if ($row[$i]->status == "Error") {
					echo " id='error' ";
				} else if ($row[$i]->status == "login") {
					echo " id='login' ";
				}
				
				echo ">
				<td>".$row[$i]->id."<input type=hidden value='".$row[$i]->id."' name='hiddenID' /></td>
				<td>".$row[$i]->tage.".".$row[$i]->monat.".".$row[$i]->jahr."</td>";
				if ($row[$i]->autor == 0) {
					echo "<td>System</td>";
				} else {
					echo "<td>" . $this->getUserName ( $row[$i]->autor ) . "</td>";
				}
				echo "<td>".$row[$i]->text."</td>
				<td>
				<select name='status' value='' size='1'>";
				
				echo "<option";
				if ($row[$i]->status == "offen") {
					echo " selected='selected' ";
				}
				echo "> offen </option>";
				
				echo "<option";
				if ($row[$i]->status == "erledigt") {
					echo " selected='selected' ";
				}
				echo "> erledigt </option>";
				
				echo "<option";
				if ($row[$i]->status == "verworfen") {
					echo " selected='selected' ";
				}
				echo "> verworfen </option>";
				
				echo "<option";
				if ($row[$i]->status == "illegal") {
					echo " selected='selected' ";
				}
				echo "> illegal </option>";
				
				echo "<option";
				if ($row[$i]->status == "login") {
					echo " selected='selected' ";
				}
				echo "> login </option>";
				
				echo "<option";
				if ($row[$i]->status == "Error") {
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
			
			echo "</table>";
			echo "</div>";
			
			// Login Log ############################################################################################################################################
			echo "<div class='newFahrt'>";
			echo "<h3>" . "Login-Log" . "</h3>";
			
			$vorschlaegeNEW = "SELECT count(id) as anzahl, id, timestamp, autor, text, hour(timestamp) as hour, minute(timestamp) as minute, status,year(timestamp) as jahr, month(timestamp) as monat, day(timestamp) as tage
					FROM vorschlaege
					WHERE status = 'login'
					GROUP BY autor ORDER BY timestamp ASC";
			$rowNEW = $this->getObjektInfo($vorschlaegeNEW);
			
			echo "<table class='logTable'>";
			echo "<thead><td id='smallLaenge'>Benutzer</td><td>Text</td><td id='smallLaenge'>Anzahl</td><td id='fixedLaenge'>Letzter Eintrag</td><td id='smallLaenge'>Optionen</td></thead>";
			
			for($i = 0 ; $i < sizeof($rowNEW) ; $i++) {
				
				// letzter Eintag:
				$letzterEintrag = $this->getObjektInfo ( "SELECT * FROM vorschlaege WHERE text = '".$rowNEW[$i]->text."' ORDER BY timestamp DESC" );
				
				echo "<tbody>";
				echo "<td>" . $this->getUserName ( $rowNEW[$i]->autor ) . "</td>";
				echo "		<td>" . $rowNEW[$i]->text . "</td><td>".$rowNEW[$i]->anzahl."</td>
						<td>" . $letzterEintrag[0]->timestamp . "</td>
						<td><a href='?action=2&kategorieLoeschen=".$rowNEW[$i]->id."&loeschen=ja&loeschid=".$rowNEW[$i]->id."' class='rightGreenLink'>X</a></td>
						</tbody>";
			}
			echo "</table>";
			echo "</div>";
			// ##########################################################################################################################################################
			// Error Log:
			$vorschlaegeNEW = "SELECT count(id) as anzahl, id, timestamp, autor, text, hour(timestamp) as hour, minute(timestamp) as minute, status, year(timestamp) as jahr, month(timestamp) as monat, day(timestamp) as tage
					FROM vorschlaege
					WHERE status = 'Error'
					GROUP BY text ORDER BY timestamp DESC";
			$rowNEW = $this->getObjektInfo($vorschlaegeNEW);
			
			echo "<div class='newFahrt'>";
			echo "<h3>" ."Fehlgeschlagene Logins oder Registrierungen". "</h3>";
			echo "<table class='logTable'>";
			echo "<thead><td>Fehlertext</td><td id='smallLaenge'>Anzahl</td><td id='fixedLaenge'>Letzter Eintrag</td><td id='smallLaenge'>Optionen</td></thead>";
			
			for($i = 0 ; $i < sizeof($rowNEW) ; $i++) {
				
				// letzter Eintag:
				$letzterEintrag = $this->getObjektInfo ( "SELECT * FROM vorschlaege WHERE text = '".$rowNEW[$i]->text."' ORDER BY timestamp DESC" );
				
				echo "<tbody>
						<td>" . $rowNEW[$i]->text . "</td><td>".$rowNEW[$i]->anzahl."</td>
						<td>" . $letzterEintrag[0]->timestamp . "</td>
						<td><a href='?action=2&kategorieLoeschen=".$rowNEW[$i]->id."&loeschen=ja&loeschid=".$rowNEW[$i]->id."' class='rightGreenLink'>X</a></td>
						</tbody>";
			}
			
			echo "</table>";
			echo "</div>";
		} else {
			echo "<p class=''>Du darfst die globalen Informationen zu einem Benutzer nicht anzeigen.</p>";
		}
	}
	
	/**
	 * Löscht die Logeinträge aus dem gewählten Text.
	 */
	function adminVorschlaegeDELETE_Eintraege() {
		if($this->userHasRight(42, 0) == true) {
			if (isset ( $_GET ['kategorieLoeschen'] ) and isset ( $_GET ['loeschen'] ) and isset ( $_GET ['loeschid'] )) {
				$id = $_GET ['kategorieLoeschen'];
				if ($id != "" and $id > 0) {
					
					// Text der zu löschenden ID bekommen:
					$deleteObjectWithThisText = $this->getObjektInfo ( "SELECT * FROM vorschlaege WHERE id = '$id' LIMIT 1" );
					$text = $deleteObjectWithThisText[0]->text;
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
		
		if($this->userHasRight(8, 0) == true) {
			// Einfügen der Function
			if (isset ( $_GET ['submit'] ) and isset ( $_GET ['status'] ) and isset ( $_GET ['hiddenID'] )) {
				$this->vorschlaegeAction ( $_GET ['submit'], $_GET ['status'], $_GET ['hiddenID'] );
			}
			
			// Select from Database
			$vorschlaege = "SELECT id, timestamp, autor, text, status FROM vorschlaege WHERE status = 'offen' ORDER BY status DESC";
			$row = $this->getObjektInfo($vorschlaege);
			echo "<table class='logTable'>";
			echo "<thead>";
			echo "<td>Text</td><td>Status</td>";
			echo "</thead>";
			for($i = 0 ; $i < sizeof($row) ; $i++) {
				echo "<form method=get>";
				echo "<input type=hidden name='action' value='2' />";
				echo "<tbody";
				if ($row[$i]->status == "offen") {
					echo " id='offen' ";
				}
				echo ">
				<td>".$row[$i]->text."</td>
				<td>".$row[$i]->status."</td></tbody>";
				echo "</form>";
			}
			echo "</table>";
		} 
	}
	
	/**
	 * Verändert den Status der eingereichten Vorschläge
	 */
	function vorschlaegeAction($submit, $status, $id) {
		
		if($this->userHasRight(8, 0) == true) {
		
			if ($submit == "OK") {
				// Vars zuweisen
				$sqlupdate = "UPDATE vorschlaege SET status='$status' WHERE id='$id'";
				if ($this->sql_insert_update_delete($sqlupdate) == true) {
					return true;
				} else {
					return false;
				}
			} else if ($submit == "X") {
				// @todo Rechtesystem einbauen
				$loeschen = "DELETE FROM vorschlaege WHERE id = '$id'";
				if ($this->sql_insert_update_delete($loeschen) == true) {
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
		$rowUsername = $this->getObjektInfo($selectUsername);
		if(isset($rowUsername[0]->name)) {
			$username = $rowUsername[0]->name;
		} else {
			$username = "Unbekannt";
		}

		return $username;
	}
	
	/**
	 * Ermöglicht das erstellen eines neuen Rechts
	 */
	function newRight() {
		if($this->userHasRight(15, 0) == true) {
			
			// Eintragung des Rechts in die DB
			if (isset ( $_POST ['newRightName'] ) and isset ( $_POST ['kategorie'] )) {
				if ($_POST ['newRightName'] != "" and $_POST ['kategorie'] != "") {
					$newRightName = $_POST ['newRightName'];
					$kategorie = $_POST ['kategorie'];
					
					// SQL
					$query = "INSERT INTO userrights (recht, kategorie) VALUES ('$newRightName','$kategorie')";
					if ($this->sql_insert_update_delete($query) == true) {
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
					$getKats = $this->getObjektInfo("SELECT * FROM rightkategorien ORDER BY name");
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
		if($this->userHasRight(45, 0) == true) {
			if (isset ( $id ) and isset ( $status )) {
				if ($status == "entsperren") {
					$updateVersuche = "UPDATE benutzer SET versuche='0' WHERE id = '$id'";
					
					if ($this->sql_insert_update_delete($updateVersuche) == true) {
						echo "<p class='erfolg'>Benutzer entsperrt</p>";
					}
					
				} else if ($status == "sperren") {
					$updateVersuche = "UPDATE benutzer SET versuche='3' WHERE id = '$id'";
					
					if ($this->sql_insert_update_delete($updateVersuche) == true) {
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
		if($this->userHasRight(47, 0) == true) {
			
			// Benutzerauswahl
			echo "<div>";
			echo "<h3><a name='forumRechte'>Forum - Rechte verteilen</a></h3>";
			$benutzerlisteUser = "SELECT id, Name FROM benutzer ORDER BY name";
			$rowUser = $this->getObjektInfo($benutzerlisteUser);
			
			for($i = 0 ; $i < sizeof($rowUser) ; $i++) {
			    echo "<a href='?action=5&user=".$rowUser[$i]->id."#forumRechte' class='buttonlink'>".$rowUser[$i]->Name."</a>";
			}
			
			
			echo "</table>" . "</div>";
			
			// Status der Rechte Änderung:
			if (isset ( $_GET ['right'] )) {
				if($this->userHasRight(47, 0) == true) {
					// ALTE BENUTZERRECHTE BEKOMMEN:
					$userid = $_GET ['user'];
					$rightFromCurrentUser = "SELECT forumRights FROM benutzer WHERE id = '$userid'";
					$rowGetRights = $this->getObjektInfo($rightFromCurrentUser);
					$benutzerrechte = $rowGetRights[0]->forumRights;
					if ($benutzerrechte == 0) {
						$benutzerrechte = 1;
					}
					
					// Dezimalwert der RechteID bekommen:
					$rightID = $_GET ['right'];
					$getRightValue = "SELECT id, rightWert FROM blogkategorien WHERE id = '$rightID'";
					$rowGetValue = $this->getObjektInfo($getRightValue);
					$rightValue = $rowGetValue[0]->rightWert;
					
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
					
					if ($this->sql_insert_update_delete($sqlRightUpdate) == true) {
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
				$row = $this->getObjektInfo($benutzerliste);
				
				echo "<div id=''>";
				
				for($i = 0 ; $i < sizeof($row) ; $i++) {
					echo "<div class=''>";
					echo "<h2>".$row[$i]->Name."</h2>";
					if ($row[$i]->forumRights == 0) {
						$benutzerrechteVorher = 1;
					} else {
						$benutzerrechteVorher = $row[$i]->forumRights;
					}
					echo "hat die Rechte: $benutzerrechteVorher";
					echo "</div>";
					
					// Rechteliste des Benutzer erstellen
					$rightListe = "SELECT * FROM blogkategorien ORDER BY rightPotenz DESC";
					$row2 = $this->getObjektInfo($rightListe);
					
					echo "<h3>Rechteliste</h3>";
					echo "<table class='flatnetTable'>";
					echo "<thead><td id='text'>Recht</td><td>vorhanden</td><td>Ändern</td></thead>";
					for($j = 0 ; $j < sizeof($row2) ; $j++) {
						if ($this->check($row2[$j]->rightWert, $benutzerrechteVorher ) == true) {
							
							echo "<tbody id='offen'>" . "<td>".$row2[$j]->kategorie."</td>" . "<td>Ja</td> " . "<td><a href='?action=5&user=$id&status=ja&right=".$row2[$j]->id."#forumRechte'>verweigern</a></td>" . "<tbody>";
						} else {
							echo "<tbody>" . "<td>".$row2[$j]->kategorie."</td>" . "<td>Nein</td> " . "<td><a href='?action=5&user=$id&status=nein&right=".$row2[$j]->id."#forumRechte'>gewähren</a></td>" . "<tbody>";
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
		
		if($this->userHasRight(48, 0) == true) {
			
			echo "<table class='flatnetTable'>";
			echo "<thead><td>Kategorie</td><td>Rechte</td><td>Optionen</td></thead>";
			$selectCategories = "SELECT *
					, year(timestamp) AS jahr
					, month(timestamp) as monat
					, day(timestamp) as tag
					, hour(timestamp) AS stunde
					, minute(timestamp) AS minute
					FROM blogkategorien
					ORDER BY kategorie";
			
			$row = $this->getObjektInfo($selectCategories);
			for ($i = 0 ; $i < sizeof($row) ; $i++) {
				echo "<tbody><td>";
				echo "<a href='/flatnet2/forum/index.php?blogcategory=".$row[$i]->id."'>" . $row[$i]->kategorie . "</a></td>";
				echo "<td><a href='?action=5'>" . $row[$i]->rightPotenz . " / " . $row[$i]->rightWert . "</a></td>";
				echo "<td><a href='?action=5&editid=".$row[$i]->id."' class='buttonlink'>Edit</a>";
				echo "<a href='?action=5&loeschid=".$row[$i]->id."' class='buttonlink'>X</a></td></tbody>";
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
		if($this->userHasRight(49, 0) == true) {
			if (! isset ( $_GET ['editid'] )) {
				
				# Höchste Potenz bekommen:
				
				$getMaxPotenz = $this->getObjektInfo("SELECT max(rightPotenz) as max FROM blogkategorien");
				if(!isset($getMaxPotenz[0]->max)) {
					# Wenn es noch keine Kategorien gibt: 
					$max = 0;
				} else {
					$max = $getMaxPotenz[0]->max + 1;
				}
				
				echo "<div class='newChar'>";
				echo "<h2>Kategorie erstellen</h2>";
				echo "<form method=post>";
				echo "<table>";
				echo "<tbody>" . "<td>Kategoriename</td><td> <input type=text value='' placeholder='Kategoriename' name='nameNewCat' required /></td>" . "</tbody>";
				echo "<tbody>" . "<td>Beschreibung</td><td> <input type=text value='' placeholder='Beschreibung' name='description' required /></td>" . "</tbody>";
				echo "<tbody>" . "<td>Potenz</td><td> <input type=number value='$max' placeholder='Potenz' name='potenz' required /> (Standard ausgef&uuml;llt lassen)</td>" . "</tbody>";
				echo "<tbody>" . "<td>Sortierung</td><td> <input type=number value='100' placeholder='Sortierung' name='sortierung' required /></td>" . "</tbody>";
				echo "<tbody>" . "<td><input type=submit name=absenden value='speichern' /></td>" . "</tbody>";
				echo "</table>";
				echo "</form>";
				echo "</div>";
				
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
		if($this->userHasRight(49, 0) == true) {
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
					$row = $this->getObjektInfo($check);
					if (isset ( $row[0]->kategorie )) {
						echo "<p class='meldung'>Diese Kategorie existiert bereits.</p>";
						return false;
						exit ();
					}
					
					$insert = "INSERT INTO blogkategorien (kategorie, beschreibung, rightPotenz, rightWert, sortierung) VALUES('$kategorie', '$description', '$potenz', '$wert', '$sortierung')";
					
					if ($this->sql_insert_update_delete($insert) == true) {
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
		if($this->userHasRight(50, 0) == true) {
			if (isset ( $_GET ['editid'] ) or isset ( $_GET ['loeschid'] )) {
				if (isset ( $_GET ['loeschid'] ) and $_GET ['loeschid'] != "") {
					// ##########################
					// LOESCH METHODE ##########
					// ##########################
					$loeschid = $_GET ['loeschid'];
					
					$query = "DELETE FROM blogkategorien WHERE id = $loeschid";
					
					// nicht zugeordnet Kategorie finden:
					$select = "SELECT id, kategorie FROM blogkategorien WHERE kategorie = 'nicht zugeordnet' LIMIT 1";
					$row = $this->getObjektInfo($select);
					if(isset($row[0]->id)) {
						$notAssigned = $row[0]->id;
					}					
					
					if (!isset($notAssigned)) {
						echo "<p class='meldung'>Es gibt keine Kategorie mit dem Namen <strong>nicht zugeordnet</strong>!
								bestehende Foreneinträge können somit verloren gehen, wenn diezu löschende Kategorie entfernt wird.
								</p><p class='meldung'>Kategorie mit Namen: nicht zugeordnet erstellen!</p><p class='meldung'>Vorgang abgebrochen.</p>";
						return false;
					} else {
						// prüfen, ob die kategorie "nicht zugeordnet" gelöscht werden soll,
						// wenn ja, dann return false
						if ($this->getCatName ($loeschid) == "nicht zugeordnet") {
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
					$row = $this->getObjektInfo($select);
					for($i = 0 ; $i < sizeof($row) ; $i++) {
						echo "<div class='newChar'>";
						echo "<form method=post>";
						echo "<input type=text name=newCatName value='".$row[$i]->kategorie."' placeholder='Kategoriename' /><br>";
						echo "<input type=text id='long' name=newCatDescription value='".$row[$i]->beschreibung."' placeholder='Beschreibung' /><br>";
						echo "<input type=number name=newPotenz value='".$row[$i]->rightPotenz."' placeholder='Potenz von 2 eingeben' /><br>";
						echo "<input type=number name=newSortierung value='".$row[$i]->sortierung."' placeholder='Sortierung eingeben' /><br>";
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
		$row = $this->getObjektInfo($select1);
		for ($i = 0 ; $i < sizeof($row) ; $i++) {
			$columns [$i] = $row[$i]->Field;
		}
		
		return $columns;
	}
	
	/**
	 * Gibt die Spalten einer Query zurück.
	 * 
	 * @param unknown $query        	
	 */
	function getColumnsFromQuery($query) {
		$createTempTable = "CREATE TEMPORARY TABLE IF NOT EXISTS tempTable AS ($query);";
		$this->sql_insert_update_delete($createTempTable);
		
		$select1 = "SHOW COLUMNS FROM tempTable";
		$row = $this->getObjektInfo($select1);
		for ($i = 0 ; $i < sizeof($row) ; $i++) {
			$columns[$i]=$row[$i]->Field;
		}
		
		return $columns;
	}
	
	
	function getColumnAnzahl($query) {
		$createTempTable = "
		CREATE TEMPORARY TABLE
		IF NOT EXISTS tempTable AS ($query);";
		$this->sql_insert_update_delete($createTempTable);
		
		$select1 = "SHOW COLUMNS FROM tempTable";
		
		$row = $this->getAmount($select1);
		return $row;
	}
	
	/**
	 * Gibt die Kommentare der Spalten einer Tabelle wieder und speichert diese in einem Array.
	 * 
	 * @param unknown $table        	
	 * @return unknown
	 */
	function getColumnComments($table) {
		
	#	$this->connectToSpecialDB ( "information_schema", "info_user", "GCbzZFw2ppBwJp7Q" );
		$dbname = $this->getDBName();
		$select1 = "SELECT column_name, column_comment FROM columns WHERE TABLE_SCHEMA = '$dbname' AND TABLE_NAME='$table'";
		$row = $this->getObjektInfo($select1);
		for ($i = 0 ; $i < sizeof($row); $i++) {
			$comments[$i] = $row[$i]->column_comment;
		}		
		return $comments;
	}
	
	/**
	 * Speichert die Inhalte erneut.
	 */
	function saveObjects() {
		if($this->userHasRight(51, 0) == true) {
			
			// UPDATE TABLE ENTRY
			if (isset ( $_POST ['ok'] )) {
				if($this->userHasRight(52, 0) == true) {
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
					
					if ($this->sql_insert_update_delete($query) == true) {
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
				if($this->userHasRight(53, 0) == true) {
					$table = $_GET ['table'];
					$id = $_GET ['id'];
					$sql = "DELETE FROM $table WHERE id='$id'";
					if ($this->sql_insert_update_delete($sql) == true) {
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
				
				if($this->userHasRight(54, 0) == true) {
				
					$table = $_GET ['table'];
					$currentObject = $_POST ['currentObject'];
					$columns = $this->getColumns ( $table );
					
					// Menge bekommen
					$dbname = $this->getDBName();
					$query = "SELECT COUNT(*) as anzahl FROM information_schema.columns WHERE table_schema = '$dbname' and table_name = '$table'";
					$mengeGrund = $this->getObjektInfo($query);
					$menge = $mengeGrund[0]->anzahl;
					
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
										
					if ($this->sql_insert_update_delete($query) == true) {
						echo "<p class='erfolg'>Datensatz eingefügt</p>";
					} else {
						echo "<p class='meldung'>Fehler</p>";
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
		if($this->userHasRight(55, 0) == true) {
			if (isset ( $_GET ['id'] ) and isset ( $_GET ['table'] )) {
				$table = $_GET ['table'];
				$id = $_GET ['id'];
				
				// Menge bekommen
				$query = "SHOW COLUMNS FROM $table";
				$menge = $this->getAmount ( $query );
				
				$select = "SELECT * FROM $table WHERE id = '$id'";
				$row = $this->getObjektInfo($select);
				
				// Kommentar der Spalte auslesen:
			#	$comments = $this->getColumnComments ( $table );
				
				// Columns bekommen:
				$columns = $this->getColumns ( $table );
				$this->insertQuery ( $select );
				
				if(isset($_GET['von'])) {
					$von = "&von=" . $_GET['von'];
				} else {
					$von = "";
				}
				echo "<table class='flatnetTable'><form method=post>";
				echo "<thead>" . "<td>Table: $table</td>" . "<td><a name='angezeigteID'>Angezeigte ID</a>: $id <a href='?action=3$von&table=$table' class='highlightedLink'>X</a></td>" . "</thead>";
				
				for ($i = 0 ; $i < sizeof($row) ; $i++) {
					for($j = 0; $j < $menge; $j ++) {
						if (strlen ( $row[$i]->$columns [$j] ) > 50) {
							echo "<tbody><td>" . $columns [$j] . "</td>
							<td><textarea rows=10 cols=100 name=currentObject[$j]";
							echo ">" . $row[$i]->$columns [$j] . "</textarea></td></tbody>";
						} else {
							echo "<tbody><td>" . $columns [$j] . "</td><td><input type=text class='' name=currentObject[$j] value='";
							echo $row[$i]->$columns [$j];
							
							echo "' placeholder='$columns[$j]'/> </td></tbody>";
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
			// Neue DB öffnen
		#	$this->connectToSpecialDB ( "information_schema", "info_user", "GCbzZFw2ppBwJp7Q" );
			
			// Columns bekommen:
			echo "<p class='meldung'>Diese Funktion ist außer Betrieb</p>";
			/*
			$select1 = "SHOW COLUMNS FROM columns";
			$row = $this->getObjektInfo($select1);
			for ($i = 0 ; $i < sizeof($row) ; $i++) {
				$columns [$i] = $row[$i]->Field;
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
			for($i = 0; $i < sizeof ( $columns ); $i ++) {
				echo "<td>";
				echo $columns [$i];
				echo "</td>";
			}
			echo "</thead>";
			
			// Select des Inhalts
			$dbname = $this->getDBName();
			$select1 = "SELECT * FROM columns WHERE TABLE_SCHEMA = '$dbname' AND TABLE_NAME='$table'";
			$row = $this->getObjektInfo($select1);
			for($i = 0; $i < sizeof ($row); $i ++) {
				echo "<tbody>";
				for($j = 0; $j < sizeof ($columns); $j ++) {
					echo "<td>";
					echo substr ( $row[$i]->$columns[$j], 0, 30 );
					echo "</td>";
				}
				echo "</tbody>";
			} */
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
		
		if($this->userHasRight(54, 0) == true) {
			
			// Menge bekommen
			$query = "SHOW COLUMNS FROM $table";
			$menge = $this->getAmount ( $query );
			
			// Columns bekommen:
			$columns = $this->getColumns ( $table );
			
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
		if($this->userHasRight(56, 0) == true) {
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
				
				if ($this->sql_insert_update_delete($query) == true) {
					echo "<p class='erfolg'>SQL Query wurde erfolgreich durchgeführt.</p>";
				} else {
					echo "<p class='meldung'>Es ist ein Fehler aufgetreten</p>";
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
		if($this->userHasRight(51, 0) == true) {
			
			// Ausgabe aller Tables
			$DBName = $this->getDBName();
			$select = "SHOW TABLES FROM $DBName";
			$columnName = "Tables_in_".$DBName;

			$row = $this->getObjektInfo($select);
			echo "<form method=get action='?action=3'>";
			echo "<input type=hidden name=action value=3 />";
			echo "<select class='bigSelect' name='table'>";
			for($i = 0 ; $i < sizeof($row) ; $i++) {
				echo "<option ";
				if (isset ( $_GET ['table'] ) and $_GET ['table'] == $row[$i]->$columnName) {
					echo " selected ";
				} else {
					echo "";
				}
				echo " value='".$row[$i]->$columnName."' >".$row[$i]->$columnName."</option>";
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
				if($this->userHasRight(56, 0) == true) {
					echo "<a href='?action=3$von&globalQuery&table=$table#administration' class='buttonlink'>manuelle Query</a>"; 
				}
				
				if($this->userHasRight(54, 0) == true) {
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
				$row = $this->getObjektInfo($select);
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
				for ($i = 0 ; $i < sizeof($row) ; $i++){
					echo "<tbody>";
					echo "<td><a href='?action=3$von&table=$table&id=".$row[$i]->id."#angezeigteID' class='buttonlink'>EDIT</a></td>";
					for($j = 0; $j < $menge; $j ++) {
						echo "<td>";
						echo substr ( strip_tags($row[$i]->$columns [$j]), 0, 30 );
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
		if($this->userHasRight(57, 0) == true) {
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
					if ($this->sql_insert_update_delete($query) == true) {
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
	function getBesitzerColumnName($lookingForThisTable) {
		
		$columnNames = $this->setBesitzerArray();
		
		foreach ($columnNames as $tabelle => $spalte) {
			if($tabelle == $lookingForThisTable) {
				$column=$spalte;
			}
		}
		
		if($column == "") {
			echo "Keinen Spaltennamen gefunden (getBesitzerColumnName) ";
		}
		return $column;
	}
	
	/**
	 * Zeigt Einträge an, bei denen der Benutzer nicht mehr existiert und bietet an, diese zu löschen.
	 */
	function Aufraeumen() {
		if($this->userHasRight(58, 0) == true) {
			if (isset($_POST ['loeschid']) AND isset($_POST['table']) AND isset($_POST['endgueltigLoeschen'])) {
				$table = $_POST ['table'];
				$userID = $_POST ['loeschid'];
				$column = $this->getBesitzerColumnName($table);
				
				if ($column == "") {
					echo "<p class='meldung'>Keine Spalte gefunden!
					<br>Debuginfo: Table: $table, UserID: $userID, $column</p>";
					exit ();
				}
				
				$query = "DELETE FROM $table WHERE $column=$userID";
				if ($this->sql_insert_update_delete($query)==true) {
					echo "<p class='erfolg'>Benutzerdaten wurden gelöscht.</p>";
				}
			}
			
			if (isset ( $_GET ['loeschen'] ) and isset ( $_GET ['loeschid'] ) and isset ( $_GET ['table'] )) {
				echo "<div class='meldung'>";
				
				echo "<p>Der Benutzer mit der ID " . $_GET ['loeschid'] . "</p>";
				echo "<p>wird aus dem table <strong>" . $_GET ['table'] . "</strong> gelöscht!</p>";
				if($this->userHasRight(58, 0) == true) {
					echo "<form method=post>";
					echo "<input type=hidden name=loeschid value='".$_GET ['loeschid']."' />";
					echo "<input type=hidden name=table value='".$_GET ['table']."' />";
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
		if($this->userHasRight(44, 0) == true) {
			echo "<h2>Neue Rechteverwaltung</h2>";
			
			if($this->userHasRight(15, 0) == true) {
				$this->newRight();
			}
			
			if($this->userHasRight(46, 0) == true) {
				$this->rechteverwaltung_setright();
			
			
				# Liste von allen Benutzern anzeigen:
				#####################################################################################################################
				$allusers = $this->getObjektInfo("SELECT * FROM benutzer");															#
																																	#
				for ($i = 0 ; $i < sizeof($allusers) ; $i++) {																		#
					echo "<a class='buttonlink' href='?action=6&userid=".$allusers[$i]->id."'>".$allusers[$i]->Name."</a>";			#
				}																													#
				#####################################################################################################################
			
				# Wenn ein Benutzer angeklickt wurde:
				if(isset($_GET['userid'])) {
					$id = $_GET['userid'];
					$userInformation = $this->getObjektInfo("SELECT * FROM benutzer WHERE id = '$id'");
					if(!isset($userInformation[0]->Name)) {
						echo "<p class='meldung'>Diesen Benutzer gibt es nicht</p>";
					} else {
						echo "<h2>Benutzer: ".$userInformation[0]->Name."</h2>";
					}
					
					# Liste erstellen, aber mit Kategorien:
					
					$getAllKategorien = $this->getObjektInfo("SELECT * FROM rightkategorien");
					
					echo "<table class='flatnetTable'>";
					
					for ($i = 0; $i < sizeof($getAllKategorien); $i++) {
						
						# Jetzt alle Einträge die zu dieser Kategorie gehören selektieren:
						$getEintraegeVonDieserKategorie = $this->getObjektInfo("SELECT * FROM userrights WHERE kategorie = '".$getAllKategorien[$i]->id."' ORDER BY recht");
						
						echo "<thead>";
							echo "<td>" . $getAllKategorien[$i]->name . "</td>";
							echo "<td>Optionen</td>";
							echo "<td>Edit</td>";
						echo "</thead>";
						
						# Jetzt Einträge ausgeben:
						for($j = 0; $j < sizeof($getEintraegeVonDieserKategorie);$j++) {
								echo "<tbody";
								if($this->userHasRight($getEintraegeVonDieserKategorie[$j]->id, $userInformation[0]->id) == true) {
									echo " id = 'offen' ";
								} else {
									echo " id = '' ";
								}
								echo ">";
								echo "<td>" .$getEintraegeVonDieserKategorie[$j]->id." - ".$getEintraegeVonDieserKategorie[$j]->recht. "</td>";
								
								echo "<td>";
								if($this->userHasRight($getEintraegeVonDieserKategorie[$j]->id, $userInformation[0]->id) == true) {
									echo "<a class='' href='?action=6&userid=".$userInformation[0]->id."&verweigern=".$getEintraegeVonDieserKategorie[$j]->id."'>verweigern</a>";
								} else {
									echo "<a class='' href='?action=6&userid=".$userInformation[0]->id."&gewaehren=".$getEintraegeVonDieserKategorie[$j]->id."'>gewähren</a><br>";
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
		
		if($this->userHasRight(46, 0) == true) {
			
			if(isset($_GET['userid']) AND isset($_GET['gewaehren'])) {
			#	echo "<p class='meldung'>Recht wird gewährt!</p>";
				
				# Check ob es Benutzer gibt..
				$userid = $_GET['userid'];
				$userInformation = $this->getObjektInfo("SELECT * FROM benutzer WHERE id = $userid");
				if(!isset($userInformation[0]->Name)) {
					echo "<p class='meldung'>Diesen Benutzer gibt es nicht</p>";
					exit;
				}
				
				# Check ob es Recht gibt...
				$rechteID = $_GET['gewaehren'];
				$rightInformation = $this->getObjektInfo("SELECT * FROM userrights WHERE id = '$rechteID'");
				if(!isset($rightInformation[0]->id)) {
					echo "<p class='meldung'>Dieses Recht gibt es nicht!</p>";
					exit;
				}
				
				# Check ob der Benutzer das Recht schon hat
				$getAllRights = $this->getObjektInfo("SELECT * FROM userrights WHERE id = '$rechteID'");
				$userInformation = $this->getObjektInfo("SELECT * FROM benutzer WHERE id = '$userid'");
				if($this->userHasRight($getAllRights[0]->id, $userInformation[0]->id) == true) {
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
				if(!isset($userInformation[0]->Name)) {
					echo "<p class='meldung'>Diesen Benutzer gibt es nicht</p>";
					exit;
				}
					
				# Check ob es Recht gibt...
				$rechteID = $_GET['verweigern'];
				$rightInformation = $this->getObjektInfo("SELECT * FROM userrights WHERE id = '$rechteID'");
				if(!isset($rightInformation[0]->id)) {
					echo "<p class='meldung'>Dieses Recht gibt es nicht!</p>";
					exit;
				}
					
				# Check ob der Benutzer das Recht schon hat
				$getAllRights = $this->getObjektInfo("SELECT * FROM userrights WHERE id = '$rechteID'");
				$userInformation = $this->getObjektInfo("SELECT * FROM benutzer WHERE id = '$userid'");
				if($this->userHasRight($getAllRights[0]->id, $userInformation[0]->id) == false) {
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
		
		if($this->userHasRight(68, 0) == true) {
		
			# Alle Kategorien bekommen:
			$getallcategories = $this->getObjektInfo("SELECT * FROM rightkategorien");
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
	
	/**
	 * Zeigt eine Ansicht der Tabelle "log"
	 */
	function log_verwaltung() {
		if($this->userHasRight(42, 0) == true) {
			
			echo "<div class='newFahrt'>";
			
			
			
			# setlimit get-action
			if(isset($_GET['setlimit'])) { if(is_numeric($_GET['setlimit']) == true) { $limit = $_GET['setlimit']; } else { $limit = 100; } } else { $limit = 100; }
			if(isset($_GET['textLimit'])) {
				if(is_numeric($_GET['textLimit']) == true) { $textLimit = $_GET['textLimit']; } else { $textLimit = 70; } 
			} else { $textLimit = 70; }
			
			$getLogs = $this->getObjektInfo("SELECT * FROM log ORDER BY timestamp DESC LIMIT $limit");
			$getanzahl = $this->getAmount("SELECT * FROM log");
			echo "<h3>" ."Datenbankänderungen (insert, update, delete, html-tag bereinigt)". "(" .$getanzahl ." Einträge)"."</h3>";
			echo "<form method=get><input type=number name=setlimit value=$limit /><input type=hidden name=action value=2 /><input type=submit class='' /></form>";
			echo "<table class='logTable'>";
			echo "<thead>";
				echo "<td>"."ID"."</td>";
				echo "<td id='fixedLaenge'>"."Datum & Zeit"."</td>";
				echo "<td id='fixedLaenge'>"."Benutzer"."</td>";
				echo "<td>"."Text"; 
					echo"<form method=get>
					<input type=number value=$textLimit name=textLimit />
					<input type=hidden value=$limit name=setlimit />
					<input type=hidden value=2 name=action />
					<input type=submit />
					</form>"; 
				echo "</td>";
				echo "<td id='fixedLaenge'>"."IP"."</td>";
			echo "</thead>";
			for($i = 0 ; $i < sizeof($getLogs) ; $i++) {
				echo "<tbody>";
					$username = $this->getUserName($getLogs[$i]->benutzer);
					echo "<td>" . $getLogs[$i]->id . "</td>";
					echo "<td>" . $getLogs[$i]->timestamp . "</td>";
					echo "<td>" . $getLogs[$i]->benutzer ."-". $username . "</td>";
					echo "<td>" . substr(strip_tags(stripslashes($getLogs[$i]->log_text)),0,$textLimit) . "</td>";
					echo "<td>" . $getLogs[$i]->ip_adress . "</td>";
					
				echo "</tbody>";
			}
			echo "</table>";
			
			echo "</div>";
		
		}
	}
	
	/**
	 * Erm�glicht die Administration der Finanzen f�r mehrere Benutzer und erm�glicht die Problembehandlung von verschiedenen Buchungen.
	 */
	function FinanzVerwaltung() {
		echo "<h2>Finanzverwaltung</h2>";
		
		# Selektion von Benutzern mit Konten:
		echo "<div class='newFahrt'>";
		$this->users_with_content();
		echo "</div>";
		
		
		
	}
	
	/**
	 * Zeigt Nutzer an, welche Konten �ber das Finanzsystem angelegt haben.
	 */
	function users_with_content() {
		$user_with_content = $this->getObjektInfo("SELECT * FROM finanzen_konten GROUP BY besitzer");
		echo "<h2>Benutzer mit Konten</h2>";
		echo "<ul>";
		for($i = 0 ; $i < sizeof($user_with_content) ; $i++) {
			$username = $this->getUserName($user_with_content[$i]->besitzer);
			echo "<li><a href='?action=7&userid=".$user_with_content[$i]->besitzer."'>" . $username . "</a></li>";
		}
		echo "</ul>";
		
		$this->show_accounts_for_user();
	}
	
	function show_accounts_for_user() {
		if(isset($_GET['userid'])) {
			$id = $_GET['userid'];
			$selectuserInfos = $this->getObjektInfo("SELECT * FROM benutzer WHERE id=$id");
			
			if(isset($selectuserInfos[0]->id)) {
				# USER INFOS
				echo "<h2>" .$selectuserInfos[0]->Name. " (" .$selectuserInfos[0]->id. ")</h2>";
				
				$konten = $this->getObjektInfo("SELECT * FROM finanzen_konten WHERE besitzer=$id");
				
				echo "<ul>";
				for($i = 0 ; $i < sizeof($konten) ; $i++) {
					echo "<li><a href='?action=7&userid=$id&konto=" . $konten[$i]->id . "'>" . $konten[$i]->id . " - " . $konten[$i]->konto . "</a></li>";
				}
				echo "<ul>";
				
				$this->show_konto_details();
				
			} else {
				echo "<p class='meldung'>Benutzer existiert nicht in der Datenbank.</p>";
			}
		}
	}
	
	/**
	 * Zeigt Informationen zum betroffenen Konto an.
	 */
	function show_konto_details() {
		if(isset($_GET['konto'])) {
			$kontoid = $_GET['konto'];
		}
	}
}