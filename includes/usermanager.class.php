<?php
/**
 * @history Steven 20.08.2014 angelegt.
 * @author BSSCHOE
 * Verwaltet die Inhaltsausgabe im Usermanager.
 */
include 'objekt/functions.class.php';

class usermanager extends functions {

	/**
	 * Zeigt das Profil von einem bestimmten User
	 * @param unknown $user
	 */
	function showProfile($user) {
		if($user == "") {
			
			echo "<p class='info'>Der Benutzer existiert nicht</p>";
			
		} else {
			$select = "SELECT * FROM benutzer WHERE Name = '$user' LIMIT 1";
			$ergebnis = mysql_query($select);
			while($row = mysql_fetch_object($ergebnis)) {
				if(!isset($row->Name) OR $row->Name == "") {
					echo "<p class='info'>Der Benutzer existiert nicht</p>";
				} else {
					echo "<div class='spacer'>";
					echo "<p class='buttonlink'>Name: $row->Name</p>";
					if(isset($row->titel)) { echo "<p class='highlightedLink'>Titel: $row->titel</p>"; }
					echo "<p class='erfolg'>" . $row->Name . " ist Mitglied seit dem " . $row->timestamp . " </p>";
					echo "<h2>Beiträge von $row->Name</h2>";
					echo "<table class='flatnetTable'>";
					echo "<thead><td colspan='2'>Titel</td></thead>";
					
					$userid = $this->getUserID($user);
										
					if($this->userHasRight("10", 0) == true OR $user == $_SESSION['username']) {
						
						$selectBlog = "SELECT * FROM blogtexte WHERE autor = '$userid' ORDER BY timestamp DESC";
						
					} else {
						
						$selectBlog = "SELECT * FROM blogtexte WHERE autor = '$userid' AND status = '1' ORDER BY timestamp DESC";
						
					}
					
					$ergebnis2 = mysql_query($selectBlog);
					while($row2 = mysql_fetch_object($ergebnis2)) {
						
						echo "<tbody ";
						# Wenn der Eintrag gelocked it
						if($row2->locked == 1) {
							echo "id = 'illegal' ";
						}
						# Wenn der Eintrag für andere Benutzer gesperrt ist
						if($row2->status != 1) {
							echo "id = 'login'";
						}
						echo " >";
						
						echo "<td><a href='/flatnet2/blog/blogentry.php?showblogid=$row2->id'>$row2->titel</a></td><td>vom $row2->timestamp</td></tbody>";
						
					}
					
					echo "</table>";
					echo "</div>";
					
				} # else ende
			} # $row ende
		} # else $user == "" ende
	} # Function ende
	
	/**
	 * Zeigt eine Liste der registrierten Benutzer an.
	 */
	function showUserList() {
		if(isset($_GET['userlist']) OR isset($_GET['user'])) {
			# Benutzerauswahl
			$benutzerlisteUser="SELECT id, Name FROM benutzer ORDER BY name";
			$ergebnisUser = mysql_query($benutzerlisteUser);
			
			while($rowUser = mysql_fetch_object($ergebnisUser))
			{
				echo "<div class='gwstart1'><div id='gwnekromant'><h2><a href='?user=$rowUser->Name'>$rowUser->Name</a></h2></div></div>";
			}
			echo "<br><br><br><br><br><br><br><br><br><br><br>";
			echo "<br><br><br><br><br><br><br><br>";
		}
	}
	
	/**
	 * Zeigt seine eigenen Informationen bezüglich Anzahl verschiedener Einträge in der Datenbank.
	 */
	function userInfo() {
		
		echo "<div class='gwEinstellungen'>";

		$user = $this->getUserID($_SESSION['username']);

		# General Account Information
		$select = "SELECT * FROM benutzer WHERE Name = '$user'";
		$ergebnis = mysql_query($select);
		while($row = mysql_fetch_object($ergebnis)) {
			echo "<div id='left'>";
			echo "<h2>Allgemein</h2>";
			echo "<br>Realname: " . $row->realName;
			echo "<br>Dein Account existiert seit dem: " . $row->timestamp . "";
			echo "<br>Du hast die ID: " . $row->id . "";
			echo "<br>und die Rechte " . $row->rights . "";
			echo "</div>";
		}

		# Guildwars Account Information
		$select = "SELECT * FROM gw_chars WHERE besitzer = '$user'";
		$ergebnis = mysql_query($select);
		$menge = mysql_num_rows($ergebnis);
		echo "<div id=''>";	
		echo "<h2>Guildwars</h2>";
		echo "<p>Du hast " . $menge . " Charakter</p></div>";

		# Inventar Account Information
		$selectRealName = "SELECT id, Name, realName FROM benutzer WHERE id = '$user'";
		$realNameergebnis = mysql_query($selectRealName);
		$rowRealName = mysql_fetch_object($realNameergebnis);
		if($rowRealName->realName == "") {
			$select = "SELECT * FROM inventar WHERE besitzer = '$user'";
		} else {
			$select = "SELECT * FROM inventar 
						WHERE 
						ersteller = '$user' ";
		}
		$ergebnis = mysql_query($select);
		$menge = mysql_num_rows($ergebnis);
		echo "<div id=''>";
		echo "<h2>Inventar</h2>";
		echo "<p>Besitzer von " . $menge . " Inventareinträgen.";
		echo "</div>";

		# Dokumentation Account Information
		$select = "SELECT * FROM docu WHERE autor = '$user'";
		$ergebnis = mysql_query($select);
		$menge = mysql_num_rows($ergebnis);
		echo "<div id=''>";
		echo "<h2>Dokumentation</h2>";
		echo "<p>Anzahl der Einträge in der Dokumentation: " . $menge;
		echo "</div>";

		# Vorschläge Account Information
		$select = "SELECT * FROM vorschlaege WHERE autor = '$user'";
		$ergebnis = mysql_query($select);
		$menge = mysql_num_rows($ergebnis);
		echo "<div id=''>";
		echo "</div>";
		
		# Vorschläge Account Information
		$select = "SELECT * FROM blogtexte WHERE autor = '$user'";
		$ergebnis = mysql_query($select);
		$menge = mysql_num_rows($ergebnis);
		echo "<div id=''>";
		echo "<h2>Foreneinträge</h2>";
		echo "<p>Anzahl: " . $menge;
		echo "</div>";
		echo "</div>";
	}

	/**
	 * Zeigt die Blogeinträge des Benutzers an.
	 */
	function userBlogTexte() {
		$user = $this->getUserID($_SESSION['username']);
		if(!isset($_GET['passChange'])) {  
		echo "<div class='mainbody'><h2>Deine Blog Einträge</h2>";
		echo "<div id='left'>";
		$selectBlog = "SELECT * 
		, year(timestamp) AS jahr
		, month(timestamp) as monat
		, day(timestamp) as tag
		, hour(timestamp) AS stunde
		, minute(timestamp) AS minute
		FROM blogtexte 
		WHERE autor = '$user' 
		ORDER BY timestamp DESC";
		$ergebnis2 = mysql_query($selectBlog);
		$menge = mysql_num_rows($ergebnis2);
		while($row2 = mysql_fetch_object($ergebnis2)) {
			echo "<div class='adresseintrag'><a href='/flatnet2/blog/blogentry.php?showblogid=$row2->id'>$row2->titel</a><br>";
			echo $row2->tag . "." . $row2->monat . "." . $row2->jahr . " / " . $row2->stunde . ":" . $row2->minute . " Uhr";
					
			echo "<br>";
			echo $this->getCatName($row2->kategorie);
			echo " / ";
			echo "</div>";
		}
		echo "</div>";
		if($menge == 0) {
			echo "<div class='adresseintrag'>Es gibt leider keine Eintrage die angezeigt werden können. :(</div>";
		}
		
		echo "</div>";
		}
	}
	
	/**
	 * Ermöglicht dem Nutzer sein Passwort zu ändern.
	 */
	function changePass() {
		$user = $_SESSION['username'];

		# Variablen Check
		if(isset($_POST['oldPass'])) {
			$altesPasswort = $_POST['oldPass'];
		}

		if(isset($_POST['absenden'])) {
			$absenden = $_POST['absenden'];
		}

		if(isset($_POST['newPass'])) {
			$neuesPass = $_POST['newPass'];
		}

		if(isset($_POST['newPass2'])) {
			$neuesPass2 = $_POST['newPass2'];
		}
		
		if(!isset($altesPasswort)) {
			return false;
		}

		# Altes Passwort holen aus der Datenbank holen:
		$abfrage = "SELECT Name, Passwort FROM benutzer WHERE Name LIKE '$user' LIMIT 1";
		$ergebnis = mysql_query($abfrage);
		$row = mysql_fetch_object($ergebnis);

		# Check ob altes passwort = dem alten eingegeben Passwort ist:
		$hashOldPass = md5($altesPasswort);
		if(isset($absenden)) {
				
			if($this->userHasRight("7", 0) == true) {
					
				if($altesPasswort == "") {
					echo "<p class='info'>Du musst erst etwas in die Felder eintragen :)</p>";
					return false;
				}
					
				if($row->Passwort != $hashOldPass) {
					echo "<p class='meldung'>Altes Passwort stimmt nicht!</p>";
				} else {

					if($neuesPass == "" OR $neuesPass2 == "") {
						echo "<p class='meldung'>Neues Passwort ist leer!</p>";
						return false;
					}
					else {
						if($neuesPass != $neuesPass2) {
							echo "<p class='meldung'>Die beiden Passwörte stimmen nicht überein.</p>";
						} else {
							$hashedPass = md5($neuesPass);
							$sqlupdate="UPDATE benutzer SET Passwort='$hashedPass' WHERE Name='$user' LIMIT 1";
							$ergebnis = mysql_query($sqlupdate);
							if($ergebnis == true) {
								echo "<p class='erfolg'>Passwort geändert</p>";
							} else {
								echo "<p class='meldung'>Eigentlich hätte es klappen müssen :( Es tut mir leid. Das Passwort
										kann nicht geändert werden.</p>";
							}
								
						}
					}
				}
			} else {
				echo "<p class='info'>Du hast für diesen Bereich keine Berechtigung</p>";
				return false;
			}
		}
	}
	
	/**
	 * Zeigt Informationen bei fehlenden Inhalten der Seite.
	 */
	function noobUser() {
		
		$user = $this->getUserID($_SESSION['username']);
		
		if($this->userHasRight("7", 0) == true) {
			# Guildwars Account Information
			$select = "SELECT * FROM benutzer WHERE id = '$user'";
			$ergebnis = mysql_query($select);
			$row = mysql_fetch_object($ergebnis);
			if($row->realName == "") {
				echo "<p class='info'>Bitte trage deinen echten Namen ein! Damit kann ich dir 
						Inventareinträge und andere nützliche Dinge zuordnen. Klicke dazu 
					<a href='?addRealName=1' class='buttonlink'>Hier</a></p>";
			}
		}
		
		if($this->userHasRight("9", 0) == true) {
			# Guildwars Account Information
			$select = "SELECT * FROM gw_chars WHERE besitzer = '$user'";
			$ergebnis = mysql_query($select);
			$menge = mysql_num_rows($ergebnis);
			if($menge == 0) {
				echo "<p class='erfolg'>Du hast keine Guildwars-Charakter gespeichert,<br>
					lege doch welche im Bereich <a href='/flatnet2/guildwars/start.php' class='buttonlink'>Guildwars</a> an!</p>";
			}
		}
		
		if($this->userHasRight("19", 0) == true) {
			# Dokumentation Account Information
			$select = "SELECT * FROM docu WHERE autor = '$user'";
			$ergebnis = mysql_query($select);
			$menge = mysql_num_rows($ergebnis);
			if($menge == 0) {
				echo "<p class='erfolg'>Lieber Administrator, es gibt keine Dokumentareinträge!.</p>";
			}	
		}
		
		if($this->userHasRight("20", 0) == true AND $this->userHasRight("2", 0) == true) {
			# Blog Account Information	
			$select = "SELECT * FROM blogtexte WHERE autor = '$user'";
			$ergebnis = mysql_query($select);
			$menge = mysql_num_rows($ergebnis);
			if($menge == 0) {
				echo "<p class='erfolg'>Mache doch ein paar Blogeinträge wenn du Lust hast, klicke
					hierzu auf <a href='/flatnet2/blog/addBlogEntry.php' class='buttonlink'>Blogeintrag verfassen</a>!</p>";
			}		
		}
	}
	
	/**
	 * Ermöglicht das Speichern des realen Benutzernamens in die Datenbank.
	 */
	function addRealName() {
		$user = $_SESSION['username'];
		$select = "SELECT * FROM benutzer WHERE Name = '$user'";
		$ergebnis = mysql_query($select);
		$row = mysql_fetch_object($ergebnis);
		
		if($row->realName == "" AND !isset($_GET['addRealName'])) {
			echo "<div class='gwstart1'><div id='gwwaechter'>";
			echo "<a href='?addRealName=1' class='highlightedLink'>Realnamen hinzufügen</a>";
			echo "</div></div>";
		}
		
		if(isset($_GET['addRealName'])) {
			# Wurde link gedrückt?
			echo "<div class='gwstart1'><div id='gwwaechter'>
				<form method=post>
					<input type='text' name='realUserName' placeholder='Vorname Nachname' />
					<input type='submit' name='realNameabsenden' value='OK' />
				</form></div></div><br><br><br><br><br><br><br><br><br><br>";
			# Wurde abgesendet?
			if(isset($_POST['realNameabsenden'])) {
				if($_POST['realUserName'] == "") {
					# Ist das Feld leer?
					echo "<div class='newChar'><p class='meldung'>Name ist leer</p></div>";
				} else { 
					$realName = $_POST['realUserName'];
					$sqlupdate="UPDATE benutzer SET realName='$realName' WHERE Name='$user' LIMIT 1";
					$ergebnis = mysql_query($sqlupdate);
					if($ergebnis == true) {
						echo "<div class='newChar'><p class='erfolg'>Realname hinzugefügt <a href='?'>OK</a></p></div>";
					} else {
						echo "<div class='newChar'><p class='meldung'>Es ist ein Fehler aufgetreten</p></div>";
					}
				}
			}
		}
		
	}
	
	/**
	 * gibt die ID der kategorie aus.
	 * @param unknown $kategorieName
	 * @return unknown
	 */
	function getCatID($kategorieName) {
	
		$selectKat = "SELECT id, kategorie FROM blogkategorien WHERE kategorie = '$kategorieName' LIMIT 1";
		$katergeb = mysql_query($selectKat);
		while($row = mysql_fetch_object($katergeb)) {
			$catID = $row->id;
		}
	
		return $catID;
	}
	
	/**
	 * gibt den Namen der Kategorie aus.
	 * @param unknown $kategorieID
	 * @return unknown
	 */
	function getCatName($kategorieID) {
	
		$selectKat = "SELECT id, kategorie FROM blogkategorien WHERE id = '$kategorieID' LIMIT 1";
		$katergeb = mysql_query($selectKat);
		while($row = mysql_fetch_object($katergeb)) {
			$catName = $row->kategorie;
		}
	
		return $catName;
	}
	
	/**
	 * Zeigt die Benutzereinstellungen an, wenn der Link im Unsermanager geklickt wurde.
	 */
	function showPassChange() {
		if(isset($_GET['passChange'])) {
			$this->changePass();
			
			echo "<div class='gwEinstellungen'>
							<h2>Passwort ändern</h2>
							<form method='post'>
								<input type='password' name='oldPass' placeholder='Altes Passwort' />
								<input type='password' name='newPass' placeholder='Neues Passwort' />
								<input type='password' name='newPass2' placeholder='Neues Passwort wiederholen' />
								<input type='submit' name='absenden' value='absenden' />
							</form>
					</div>";
			$this->manageGWAccounts();
		}
		
		
	}
	
	/**
	 * Ermöglicht das verwalten von Guildwars Accounts.
	 */
	function manageGWAccounts() {
		# Rechtecheck
		if($this->userHasRight("9", 0) == true) {
			
			# Speicherung:
			$this->saveNewGWAcc();
			echo "<div class='gwEinstellungen'>";
			
			$this->getInfoGWAcc();
			$this->editGWAcc();
			$this->deleteGWAcc();
			
			echo "<h2><a name='gwAccs'>Guildwars Accounts</a></h2>";
			$user = $this->getUserID($_SESSION['username']);
			$select = "SELECT * FROM gw_accounts WHERE besitzer = '$user'";
			$ergebnis = mysql_query($select);
			$menge = mysql_num_rows($ergebnis);
			
			if($menge == 0) {
				echo "<div>
				<a href='?passChange&getInfo=1#gwAccs' class='optionLink'>Standard Account </a>
				<a href='?passChange&edit=1#gwAccs' class='quadratOption'>edit</a>
				<a href='?passChange&delete=1#gwAccs' class='quadratOption'>X</a> </div>";
			
			} else {
			$i = 0;
				while($row = mysql_fetch_object($ergebnis)) {
					$i = $i + 1;
					echo "<div><a href='?passChange&getInfo=$row->account#gwAccs' class='optionLink'>$i. " 
						. substr($row->mail, 0, 20) . "</a>
						<a href='?passChange&edit=$row->account#gwAccs' class='quadratOption'>edit</a>
						<a href='?passChange&delete=$row->account#gwAccs' class='quadratOption'>X</a> </div>";
				}
			}
				
			echo "<br><div><form method=post><input type=text name='accName' value='' placeholder='Neuen Acc erstellen' />
						<input type=submit name=ok value=ok /></form></div>";
			
			echo "</div>";			
		}
	}
	
	/**
	 * Speichert einen neuen Account.
	 * @return boolean
	 */
	function saveNewGWAcc() {
		if(isset($_POST['ok'])) {
			if($_POST['ok'] != "" AND $_POST['accName']) {
				if($this->userHasRight("9", 0) == true) {
					
					$mail = $_POST['accName'];
					$user = $this->getUserID($_SESSION['username']);
					
					
					
					# nächste Acc Nr bekommen:
					$accnummer = "SELECT MAX(account) AS max FROM gw_accounts WHERE besitzer = '$user'";
					$ergebnis = mysql_query($accnummer);
					$row = mysql_fetch_object($ergebnis);
					$nextAccNo = $row->max + 1;
					
					if($mail == "" OR $nextAccNo == "") {
						echo "<p class='meldung'>Bitte alle Felder ausfüllen</p>";
						return false;
					} else {
						$query="INSERT INTO gw_accounts (besitzer, account, mail) VALUES ('$user','$nextAccNo','$mail')";
						$ergebnis = mysql_query($query);
						if($ergebnis == true) {
							echo "<div class='gwEinstellungen'><p class='erfolg'>Account angelegt</p></div>";
						} else {
							echo "<div class='gwEinstellungen'><p class='meldung'>Fehler</p></div>";
						}
					}
					
					
					
				}
			}
		}
	}
	
	/**
	 * Editiert einen bestehenden Account
	 */
	function editGWAcc() {
		if(isset($_GET['edit'])) {
			if($_GET['edit'] != "") {
				$nummer = $_GET['edit'];
				$user = $this->getUserID($_SESSION['username']);
				
				$query = "SELECT * FROM gw_accounts WHERE besitzer = '$user' AND account = '$nummer' LIMIT 1";
				$ergebnis = mysql_query($query);
				$row = mysql_fetch_object($ergebnis);
				
				# Gelöschte Stunden bekommen:
				$stunden = $this->getObjektInfo("SELECT * FROM account_infos WHERE besitzer = '$user' AND account = '$nummer' AND attribut = 'gw_geloschte_stunden' LIMIT 1");
				
				if(!isset($stunden->wert)) {
					$stundenWert = 0;
				} else {
					$stundenWert = $stunden->wert;
				}
				
				if(!isset($row->mail)) {
						$insert = "INSERT INTO gw_accounts (besitzer, account, mail) VALUES ('$user','1','Standard Account')";
						$ergebnis = mysql_query($insert);
						if($ergebnis == true) {
							echo "<div class='gwEinstellungen'>";
								
								echo "<p class='erfolg'>Dein Account wurde einmalig konfiguriert. Du kannst hier jetzt mehrere Accounts
										anlegen, falls du mehrere besitzt, und diese dann später in der Charakterübersicht wechseln!</p>";
								echo "<a href='?passChange' class='buttonlink'/>OK</a>";
					
							echo "</div>";
						}
				} else {
					echo "<div class='rightSpacer'>";
					echo "<h2>" .$row->mail . "</h2>";
					echo "<form action='?passChange' method=post>";
					echo "<strong>Accountname</strong><br> <input type=text name='newAccName' value='$row->mail' />";
					echo "<p class=''>Hier kannst du die gespeicherten gelöschten Spielstunden selbst anpassen: </p>";
					echo "<strong>Gelöschte Stunden</strong><br> <input type=text name='newStunden' value='$stundenWert' />";
					echo "<input type=hidden name='nummer' value='$nummer' />";
					echo "<br><input type=submit name='newAccNameSubmit' value='OK' />";
					echo "</form>";
					
					echo "</div>";
				}
			}
		} 
		if(isset($_POST['newAccNameSubmit'])) {
			
			$user = $this->getUserID($_SESSION['username']);
			$nummer = $_POST['nummer'];
			$mail = $_POST['newAccName'];
			$stunden = $_POST['newStunden'];
			if($mail == "" OR $nummer == "" OR $stunden == "") {
				return false;
			}
			
			$query = "SELECT * FROM gw_accounts WHERE besitzer = '$user' AND account = '$nummer' LIMIT 1";
			$ergebnis = mysql_query($query);
			$row = mysql_fetch_object($ergebnis);
			
			$sqlupdate= "UPDATE gw_accounts SET mail='$mail' WHERE besitzer='$user' AND account='$nummer'";
			$sqlupdategesamt = mysql_query($sqlupdate);
			
			# Gelöschte Stunden bekommen:
			$stundenOld = $this->getObjektInfo("SELECT * FROM account_infos WHERE besitzer = '$user' AND account = '$nummer' AND attribut = 'gw_geloschte_stunden' LIMIT 1");
			
			
			if($sqlupdategesamt == true) {
				
				# Stunden speichern: 
				
				if(!isset($stundenOld->wert)) {
					# Neuen Eintrag erstellen:
					$this->sql_insert_update_delete("INSERT INTO account_infos (besitzer, attribut, wert, account) VALUES ('$user','gw_geloschte_stunden','$stunden','$nummer')");
				} else {
					# Bestehenden Eintrag ändern:
					$this->sql_insert_update_delete("UPDATE account_infos SET wert='$stunden' WHERE besitzer='$user' AND account='$nummer' AND attribut = 'gw_geloschte_stunden'");
				}
								
				echo "<div class='erfolg'>";
					echo "<h2>Ergebnis</h2>";
					echo "<p >Eintrag geändert</p>";
				echo "</div>";
			} else {
				echo "<div class='meldung'>";
					echo "<h2>Ergebnis</h2>";
					echo "<p>Fehler</p>";
				echo "</div>";
			}
		}
	}
	
	/**
	 * Löscht einen bestehenden Account.
	 */
	function deleteGWAcc() {
		if(isset($_GET['delete'])) {
			if($_GET['delete'] != "" AND $_GET['delete'] != 1) {
				$nummer = $_GET['delete'];
				$user = $this->getUserID($_SESSION['username']);
				# nächste Acc Nr bekommen:
				$query = "SELECT * FROM gw_accounts WHERE besitzer = '$user' AND account = '$nummer' LIMIT 1";
				$ergebnis = mysql_query($query);
				$row = mysql_fetch_object($ergebnis);
				
				if(isset($_GET['Sure'])) {
					if($_GET['Sure'] == 1) {
						if(isset($row->besitzer) AND $user == $row->besitzer AND $this->userHasRight("9", 0) == true) {
							
							$sqlupdate="UPDATE gw_chars SET account='1' WHERE besitzer='$user' AND account='$nummer'";
							
							$sqlupdategesamt = mysql_query($sqlupdate);
							if($sqlupdategesamt == true) {
								
								$sql = "DELETE FROM gw_accounts WHERE account='$nummer' AND besitzer = '$user'";
								$del= mysql_query($sql);
								if($del == true) {
									
									# Löscht die Informationen zum account in der Tabelle Account Infos:
									$this->sql_insert_update_delete("DELETE FROM account_infos WHERE besitzer = '$user' AND account = '$nummer'");
									
									# Löscht alle Informationen in der gwusersmats
									$this->sql_insert_update_delete("DELETE FROM gwusersmats WHERE besitzer = '$user' AND account = '$nummer'");
																	
									echo "<div class='erfolg'>";
									echo "<h2>Erfolg</h2>";
									echo "<p>Account gelöscht</p>";
									echo "</div>";
								} else {
									echo "<div class='meldung'>";
									echo "<h2>Fehler</h2>";
									echo "<p>Konnte nicht gelöscht werden</p>";
									echo "</div>";
								}
							}
						} else {
							echo "<div class='meldung'>";
							echo "<h2>Fehler</h2>";
							echo "<p>Konnte nicht gelöscht werden</p>";
							echo "</div>";
						}
					}
				} else {
					if($this->userHasRight("9", 0) == true AND $row->besitzer == $user) {
						echo "<div class='rightSpacer'>";
							echo "<h2>" .$row->mail . "</h2>";
							echo "Willst du diesen Account wirklich löschen? ";
							echo "<a href='?delete=$nummer&passChange&Sure=1' class='highlightedLink'/> JA </a>";
							echo "<p class='info'>Die Charakter werden zum Hauptaccount geschoben.</p>";
						echo "</div>";
					}
				}
				
			} else {
				echo "<div class='meldung'>";
				echo "<h2>Fehler</h2>";
				echo "<p class=''>Der Hauptaccount kann nicht gelöscht werden</p>";
				echo "</div>";
			}
		}
	}
	
	/**
	 * Zeigt eine Kachel für den gewählten Account an.
	 */
	function getInfoGWAcc() {
		if(isset($_GET['getInfo'])) {
			
			$nummer = $_GET['getInfo'];
			$user = $this->getUserID($_SESSION['username']);
			$menge = $this->getAmount("SELECT id, besitzer FROM gw_chars WHERE besitzer = '$user' AND account = '$nummer'");
			
			# nächste Acc Nr bekommen:
			$row = $this->getObjektInfo("SELECT * FROM gw_accounts WHERE besitzer = '$user' AND account = '$nummer' LIMIT 1");
			$stundenThisAcc = $this->getObjektInfo("SELECT sum(spielstunden) as summe FROM gw_chars WHERE besitzer = '$user' AND account = '$nummer' LIMIT 1");
			$geloschteStundenCurrentAccount = $this->getObjektInfo("SELECT besitzer, attribut, wert, account FROM account_infos WHERE besitzer = '$user' AND attribut = 'gw_geloschte_stunden' AND account = '$nummer'");
			
			if(!isset($geloschteStundenCurrentAccount->wert)) {
				$geloschteStunden = 0;
			} else {
				$geloschteStunden = $geloschteStundenCurrentAccount->wert;
			}
			
			if($this->userHasRight("9", 0) == true AND $row->besitzer == $user) {
				echo "<div class='rightSpacer'>";
					echo "<h2>" .$row->mail . "</h2>";
					echo "Informationen:<br><strong>" . $menge . "</strong> Charakter,";
					echo "<br>gespielte Stunden: <strong>" . $stundenThisAcc->summe . " </strong>";
					echo "<br>und <strong> ". $geloschteStunden ." </strong> gelöschte Stunden.";
				echo "</div>";
			}
		}
	}
	
}
?>