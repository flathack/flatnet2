<?php
/**
 * @history Steven 20.08.2014 angelegt.
 * @history Steven 24.08.2014 Blog funktionalität teilweise implementiert, wie anzeigen, auswählen.
 * @history Steven Irgend wann 2015: Blog in Forum umprogrammiert.
 * @author Steven
 */
include 'objekt/functions.class.php';

class blog extends functions {
	
	function showForumNav() {
		
		if(isset($_GET['showblogid'])) {
			$blogid = $_GET['showblogid'];
			
			$getBlogEintragInfos = $this->getObjektInfo("SELECT * FROM blogtexte WHERE id = '$blogid'");
			
			$category = $getBlogEintragInfos->kategorie;
			
		} else {
			$category = "";
		}
		
		if(isset($_GET['ursprungKategorie'])) {
			$category = $_GET['ursprungKategorie'];
			echo "<a href=\"/flatnet2/forum/index.php?blogcategory=$category\" class=\"highlightedLink\">Zurück </a>";
			echo "<a href='/flatnet2/blog/addBlogEntry.php?blogcategory=$category' class='buttonlink'>Neues Thema</a>";
			
			if($getBlogEintragInfos->locked != 1) {
				echo "<a href='/flatnet2/blog/blogentry.php?showblogid=$blogid&newComment=$blogid#antwort' class='greenLink'>Antwort erstellen</a>"; 
			}
		} else {
			if($category != "") {
				echo "<a href=\"/flatnet2/forum/index.php?blogcategory=$category\" class=\"highlightedLink\">Zurück </a>";
				echo "<a href='/flatnet2/blog/addBlogEntry.php?blogcategory=$category' class='buttonlink'>Neues Thema</a>";
				echo "<a href='/flatnet2/blog/blogentry.php?showblogid=$blogid&newComment=$blogid#antwort' class='greenLink'>Antwort erstellen</a>";
			} else {
				echo "<a href=\"/flatnet2/forum/index.php\" class=\"buttonlink\">Forum Startseite</a>";
			}
			
		}
		# AdminTools anzeigen
		$this->adminTools();
	}
	
	/**
	 * Zeigt vorhandene Admintools an.
	 */
	function adminTools() {
		
		$this->lockthread();
		$this->unlockthread();
		
		# Check if Forum Admin
		if($this->userHasRight("26", 0) == true) {
			if(isset($_GET['showblogid'])) {
				# CurrentBlogID Info
				$id = $_GET['showblogid'];
				$getbloginfo = $this->getObjektInfo("SELECT * FROM blogtexte WHERE id = '$id'");
				$kategorie = $getbloginfo->kategorie;
				
				if(isset($getbloginfo->kategorie)) {
					if($getbloginfo->locked == 0) {
						echo "<a class='rightRedLink' href='?showblogid=$id&ursprungKategorie=$kategorie&lockthread=$id'> &#128274; LOCK </a>";
					} else {
						echo "<a class='rightRedLink' name='thisThreadLocked'>Dieses Thema ist gesperrt</a>";
						echo "<a class='rightGreenLink' href='?showblogid=$id&ursprungKategorie=$kategorie&unlockthread=$id'> &#128274; UNLOCK </a>";
					}
				}
			}
		}
	}
	
	function lockthread() {
		if($this->userHasRight("27", 0) == true) {
			if(isset($_GET['lockthread'])) {
				$id = $_GET['lockthread'];
				if($id > 0 AND is_numeric($id) == true) {
					$this->sql_insert_update_delete("UPDATE blogtexte SET locked=1 WHERE id = '$id'");
				}
				
			}
		}
	}
	function unlockthread() {
		if($this->userHasRight("27", 0) == true) {
			if(isset($_GET['unlockthread'])) {
				$id = $_GET['unlockthread'];
				if($id > 0 AND is_numeric($id) == true) {
					$this->sql_insert_update_delete("UPDATE blogtexte SET locked=0 WHERE id = '$id'");
				}
			}
		}
	}

	/**
	 * Zeigt die Eingabefelder zum eingeben des Textes für einen neuen Blog.
	 */
	function newBlogEingabe() {
		if($this->userHasRight("20", 0) == true) {
			echo "<div class='neuerBlog'>";
			echo '<form method="post">';
			$bloguser = $_SESSION['username'];
			
			# Gibt alle Kategorien aus.
			echo "<select name='blogkategorie' value='' size='1'>";
			$selectKat = "SELECT id, kategorie, rightWert FROM blogkategorien";
			$katergeb = mysql_query($selectKat);
			while($row = mysql_fetch_object($katergeb)) {
				
				# BenutzerRecht selektieren:
				# Check ob die Kategorie in der Auswahlliste auftauchen soll.
				$userID = $this->getUserID($_SESSION['username']);
				$selectBenutzerRecht = "SELECT id, forumRights FROM benutzer WHERE id = '$userID' LIMIT 1";
				$ergebRecht = mysql_query($selectBenutzerRecht);
				$rowRecht = mysql_fetch_object($ergebRecht);
				
				$aktuelleBenutzerrechte = $rowRecht->forumRights;
				$kategorieRechte = $row->rightWert;
				
				if($this->check($kategorieRechte, $aktuelleBenutzerrechte) == true) {
					echo "<option";
					# Checked die Kategorie, wenn der Link im Forum geklickt wurde.
					if(isset($_GET['blogcategory'])) {
						if($_GET['blogcategory'] == $row->id) {
							echo " selected ";
						}
					}
						
					echo " value='$row->id'>";
					echo $row->kategorie;
					echo "</option>";
				}
			}
			echo "</select>";
			# Wenn Text bereits geschrieben wurde, das speichern aber nicht geklappt hat:
			if(isset($_POST['blogtext'])) { $postText = $_POST['blogtext']; } else { $postText = ""; }
			# Wenn Titel bereits geschrieben wurde:
			if(isset($_POST['blogtitel'])) { $postTitel = $_POST['blogtitel']; } else { $postTitel = ""; }
			# Status #
			echo "<input type='radio' name='status' value='0' id='statusNull' >" . "<label for='statusNull'>Gesperrt</label>";
			echo "<input type='radio' name='status' value='1' id='statusEins' checked >" . "<label for='statusEins'>Freigegeben</label>";
			echo "<input type='submit' name='sendnewblog' value='Absenden' />";
			echo "<div class='spacer'>";
			echo "<input type='text' name='blogtitel' id='titel' value='$postTitel' placeholder='Themen Titel' />";
			echo "</div>";
			echo "<div class='editForumEintrag'>";
			
			echo "<textarea class='ckeditor' name='blogtext'>$postText</textarea>";

			echo "</div>";
			echo "<div id='absenden'>";
			echo "<input type='submit' name='sendnewblog' value='Absenden' />";
			echo "</div>";
			echo "</form>";
			echo "</div>";
		}
		
	}

	/**
	 * Ermöglicht das speichern des neuen Blogs.
	 */
	function newBlogFunction() {
		if($this->userHasRight("20", 0) == true) {
		
			if(isset($_POST['blogtitel']) AND isset($_POST['blogtext']) AND isset($_POST['blogkategorie'])) {
				if($_POST['blogtitel'] == "" OR $_POST['blogtext'] == "" OR $_POST['blogkategorie'] == "") {	
					echo "<p class='info'>Bitte Titel und Text ausfüllen</p>";
				} else {
					$titel = $_POST['blogtitel'];
					
					# ID des Benutzers auswählen:
					$autor = $_SESSION['username'];
					$selectUserID = "SELECT id, Name FROM benutzer WHERE Name = '$autor' LIMIT 1";
					$ergebnisUserID = mysql_query($selectUserID);
					while($row = mysql_fetch_object($ergebnisUserID)) {
						$autor = $row->id;
					}
					
					$text = $_POST['blogtext'];
					$kategorie = $_POST['blogkategorie'];
					$selectKategorieID = "SELECT id, kategorie FROM blogkategorien WHERE kategorie = '$kategorie' LIMIT 1";
					$ergebnisKategorieID = mysql_query($selectKategorieID);
					while($row2 = mysql_fetch_object($ergebnisKategorieID)) {
						$kategorie = $row2->id;
					}
					$status = $_POST['status'];
					$insertNewBlog="INSERT INTO blogtexte (autor, titel, text, kategorie, status) VALUES ('$autor','$titel','$text','$kategorie','$status')";
					$ergebnis = mysql_query($insertNewBlog);
					if($ergebnis == true) {
						echo '<p class="erfolg">Der Foreneintrag mit dem Titel <strong>' . $titel . ' </strong>wurde erstellt.';
					} else {
						echo '<p class="meldung">Das speichern ist fehlgeschlagen! 
								Kopiere den unteren Text in einen anderen Editor um ihn zu speichern und versuche es später erneut.</p>';
					}
				}
			} 
		}
	}

	/**
	 * Zeigt einen Blog eintrag mit einer besitmmten ID an und die dazugehörigen Kommentare.
	 */
	function showBlogId($id) {
		
		if($this->userHasRight("2", 0) == true) {

			# Check ob eine ID angegeben wurde.
			if($id != "" AND is_numeric($id) == true) {
						
				$selectCheck = "SELECT id, autor, status, kategorie FROM blogtexte WHERE id=$id";
				$ergebnisCheck = mysql_query($selectCheck);
				$rowCheck = mysql_fetch_object($ergebnisCheck);
				
				$currentUser = $this->getUserID($_SESSION['username']);
	
				# Check, ob User den Artikel sehen darf:
				if(
				
				!isset($rowCheck->autor) 
				
				OR $rowCheck->autor != $currentUser 
					AND $rowCheck->status == '0' 
					AND $this->userHasRight("10", 0) == false
							
				OR $this->userIsAllowedToSeeCategory($currentUser, $id) == false
				) {
					
					echo "<p class='meldung'>Du kannst diesen Beitrag nicht anzeigen</p>";
					
				} else {
						
					$select = "SELECT id, timestamp, autor, titel, text, status, locked
						, year(timestamp) AS jahr
						, month(timestamp) as monat
						, day(timestamp) as tag
						, hour(timestamp) AS stunde
						, minute(timestamp) AS minute
						FROM blogtexte WHERE id=$id";
			
					
					$ergebnis = mysql_query($select);
					while($row = mysql_fetch_object($ergebnis)) {
						
						if(isset($_GET['editComment'])) {
							$this->editKommentar($_GET['editComment']);
						}
						if(isset($_GET['del'])) {
							echo "<form action = '#geloescht' method=post>";
							echo "<p class='meldung'>Sicher, dass dieser Kommentar gelöscht werden soll?<br>";
							$blogid = $_GET['showblogid'];
							echo "<input type=submit name=sure value=Ja /><a href='?showblogid=$blogid' class='greenLink'>Nein</a></p>";
							echo "</form>";
							if(isset($_POST['sure'])) {
								$this->delKommentar($_GET['del'], $_GET['showblogid']);
							}
						
						}
						
						echo "<table class='forumPost'>";
						
						# Wenn der Benutzer einen Titel hat:
						$autorTitel = $this->getObjektInfo("SELECT id, titel FROM benutzer WHERE id = '" . $row->autor . "'");
						if(isset($autorTitel->titel) AND $autorTitel->titel != "") {
							$titel = "<p id='smallLink' class='rightRedLink'>" . $autorTitel->titel . "</p>";
						} else {
							$titel = "";
						}
						
						# KOPFZEILE
							echo "<thead><td colspan = '3'>Titel des Beitrags: " . $row->titel . "</td></thead>";
							echo "<thead id='small'>";
							echo "<td><img id='id' src='../images/avatar_122.png' style='width:30px; height: 30px;' />
									<a id='id' class='rightBlueLink' href='#ThreadID".$row->id."' name='#ThreadID".$row->id."'>ID: ".$row->id."</a>
									<p id='smallLink' class='rightBlueLink'>Autor: " . $this->getUserName($row->autor) . "</p>
							$titel</td>
							
							<td>Erstellt: " .$row->tag . "." . $row->monat . "." . $row->jahr . " | " . $row->stunde . ":" . $row->minute . " Uhr</td>"; 
							
							# Buttons
							echo "<td>";
							
							# EDIT KNOPF
							if($row->locked != 1) {
								if($this->userHasRight("32", 0) == "true" OR $_SESSION['username'] == $this->getUserName($row->autor)) {
									echo "<a class='rightRedLink' href='?bearbid=$row->id' >edit</a>";
								}
							}
							
							# ANTWORT ERSTELLEN KNOPF
							if($row->locked != 1) {
								echo "<a class='rightBlueLink' href='?showblogid=$row->id&newComment=$row->id&quote=$row->id#antwort'> Zitat </a>";
								echo "<a class='rightGreenLink' href='?showblogid=$row->id&newComment=$row->id#antwort'> Antwort erstellen </a>";
							}
							
							
							echo "</td>";
							
							echo "</thead>";
							# ENDE KOPFZEILE
							
							# TEXT ANZEIGE: 
							echo "<tbody>"; 
								echo "<td colspan='3'>" . $row->text . "</td>";
							echo "</tbody>";						
						echo "</table>";
						
						# KommentarFunktion aufrufen
						echo "<table class='forumPost'>";
						$this->kommentare($row->id);
					#	echo "<tfoot id='small'><td colspan='3'><a class='greenLink' href='?showblogid=$row->id&newComment=$row->id'> Antwort erstellen </a></td></tfoot>";
						echo "</table>";
	
					} # While Ende
				} # Check, ob der User den Post sehen darf.
			} else { # ID != 0 Ende
				echo "<p class='meldung'>Du kannst diesen Beitrag nicht anzeigen</p>";
			}
		} # Recht ende
	} # Function Ende

	/**
	 * bearbeitet einen Blog mit der angewählten ID.
	 * @param unknown $id
	 */
	function bearbBlogId($bearbid) {
		if($this->userHasRight("20", 0) == true) {
			/**
			 * Ermöglicht das speichern der Inhalte.
			 * @param $bearbid und $update
			 */
			$update = isset($_GET['update']) ? $_GET['update'] : '';
			if($update != "") {
				$titel = $_POST['newtitel'];
				
				# EDTIERT VON XYZ AM XYZ DATE
				$editiertAm = date('d.m.Y h:i:s', time());
				$userDerGeaendertHat = $_SESSION['username'];
				
				# TEXT MODIFIZIEREN UND EDITIERT VON HINZUFÜGEN
				$text = $_POST['newtext'] . "<p><span style=\"font-size:10px; padding:3px; background-Color: #e0e0e0;\">Editiert am $editiertAm von $userDerGeaendertHat</span></p>";
				
				$status = $_POST['status'];
	
				if($_POST['newblogkategorie'] == "") {
					$sqlupdate = "UPDATE blogtexte SET titel='$titel', text='$text', status='$status' WHERE id='$bearbid'";
				} else {
					$kategorie = $_POST['newblogkategorie'];
					$sqlupdate = "UPDATE blogtexte SET titel='$titel', text='$text', kategorie='$kategorie', status='$status' WHERE id='$bearbid'";
				}
	
				# Durchführung des Updates
				$update = mysql_query($sqlupdate);
				if($update == true) {
					echo "<p class='erfolg'>Foreneintrag <strong>$titel</strong> wurde geändert.</p>";
				} else {
					echo "<p class='meldung'>Es ist ein Fehler aufgetreten. Kopiere den unteren Text in ein anderes Dokument um den Text nicht zu verlieren und versuche es später nochmal.</p>";
				}
			}
	
			# Es wird sichergestellt, dass ID eine Zahl ist.
			if($bearbid > 0) {
				$select = "SELECT id, timestamp, autor, titel, text, kategorie, status FROM blogtexte WHERE id=$bearbid";
				$ergebnis = mysql_query($select);
	
				while($row = mysql_fetch_object($ergebnis)) {
					
					$realUsername = $this->getUserName($row->autor);
					
					if(isset($_GET['bearbid'])) {
						$category = $_GET['bearbid'];
						echo "<a href=\"blogentry.php?showblogid=$row->id\"
						class=\"buttonlink\">&#8634; zurück zum Thema</a>";
					}
					
					echo "<div class='neuerBlog'>";
					
					echo "<form action='?bearbid=$row->id&update=update' method=post>";
					echo "<h2>Eintrag bearbeiten</h2>";
					echo "<input type='text' value=\"$row->titel\" name='newtitel' id='titel' /><br>"; # Titel
					
					# Kategorie Bereich:
					echo "<select name='newblogkategorie' value='' size='1'>";
					$selectKat = "SELECT id, kategorie, rightWert FROM blogkategorien";
					$katergeb = mysql_query($selectKat);
					echo "<option></option>";
					
					# BenutzerRecht selektieren:
					# Check ob die Kategorie in der Auswahlliste auftauchen soll.
					$userID = $this->getUserID($_SESSION['username']);
					$selectBenutzerRecht = "SELECT id, forumRights FROM benutzer WHERE id = '$userID' LIMIT 1";
					$ergebRecht = mysql_query($selectBenutzerRecht);
					$rowRecht = mysql_fetch_object($ergebRecht);
					
					while($row2 = mysql_fetch_object($katergeb)) {
						$aktuelleBenutzerrechte = $rowRecht->forumRights;
						$kategorieRechte = $row2->rightWert;
						
						if($this->check($kategorieRechte, $aktuelleBenutzerrechte) == true) {
							echo "<option";
							if($row2->id == $row->kategorie) {
								echo ' selected ';
							} echo " value='$row2->id'>$row2->kategorie</option>";
						}
						
					}
					echo "</select>";
					
					# Status #
					echo "<input type='radio' name='status' value='0' id='statusNull' ";
					if($row->status == "0" OR $row->status == NULL) {
						echo " checked ";
					} else {
						echo " unchecked ";
					}
					echo ">" . "<label for='statusNull'>Gesperrt</label>";
	
					echo "<input type='radio' name='status' value='1' id='statusEins' ";
					if($row->status == "1") {
						echo " checked ";
					} else {
						echo " unchecked ";
					}
					echo ">" . "<label for='statusEins'>Freigegeben</label>";
					
					echo "<input type='submit' name='bearbblogid' value='Speichern' />";
					
					echo "<div class='editForumEintrag'><textarea class='ckeditor' name='newtext'>$row->text</textarea></div>"; # Text Output #
				#	echo"	<script> CKEDITOR.inline( 'newtext' ); </script>";
					
					# Nur ein Administrator oder der Autor darf den Artikel bearbeiten oder löschen
					if($this->userHasRight("29", 0) == "true" OR $_SESSION['username'] == $realUsername) {
						echo "<input type='submit' name='bearbblogid' value='Speichern' />
						<a href='?loeschid=$row->id' class='highlightedLink'>Beitrag entfernen</a>";
					}
					echo "</form>";
					echo "</div>";
	
				} # Ende While Schleife
			}
		}
	}

	/**
	 * Löscht einen Blog mit der angewählten ID.
	 * @param unknown $id
	 */
	function deleBlogId($id) {
		
		if($this->userHasRight("28", 0) == true OR $this->userHasRight("29", 0) == true) {

			$jaloeschen = isset($_POST['jaloeschen']) ? $_POST['jaloeschen'] : '';
			$loeschblogid = isset($_POST['blogid']) ? $_POST['blogid'] : '';
			if($loeschblogid) {
				
	
				# Kontrolle, ob User den Artikel löschen darf.
				$select = "SELECT id, autor FROM blogtexte WHERE id=$id";
				$ergebnis = mysql_query($select);
				$row = mysql_fetch_object($ergebnis);
				
				# namen des Benutzers auswählen:
				
				
				if(!isset($row->autor)) {
					echo "<p class='info'>Beitrag exisitert nicht!</p>";
					exit;
				} else {
					$autor = $row->autor;
				}
				
				$selectUsername = "SELECT id, Name FROM benutzer WHERE id = '$autor' LIMIT 1";
				$ergebnisUsername = mysql_query($selectUsername);
				while ($rowUsername = mysql_fetch_object($ergebnisUsername)) {
					$realUsername = $rowUsername->Name;
				}
	
				if($this->userHasRight("29", 0) == true OR $_SESSION['username'] == $realUsername) {
	
					# Durchführung der Löschung.
					$loeschQuery = "DELETE FROM `blogtexte` WHERE `id` = $loeschblogid";
					$ergebnis = mysql_query($loeschQuery);
	
					if($ergebnis == true) {
						$this->sql_insert_update_delete("DELETE FROM blog_kommentare WHERE blogid = '$loeschblogid'");
						echo "<p class='erfolg'>Der Beitrag wurde erfolgreich gelöscht!</p>";
						exit;
					} else {
						echo "<p class='meldung'>Es gab einen Fehler. Der Fehler war: </p>";
						echo mysql_error();
					}
				}
			}
			
			if($id > 0) { # check ob eine Zahl
				
				# Kontrolle, ob User den Artikel löschen darf.
				$select = "SELECT id, autor FROM blogtexte WHERE id=$id";
				$ergebnis = mysql_query($select);
				$row = mysql_fetch_object($ergebnis);
				
				# namen des Benutzers auswählen:
				$autor = $row->autor;
				$selectUsername = "SELECT id, Name FROM benutzer WHERE id = '$autor' LIMIT 1";
				$ergebnisUsername = mysql_query($selectUsername);
				while ($rowUsername = mysql_fetch_object($ergebnisUsername)) {
					$realUsername = $rowUsername->Name;
				}
	
				if($this->userHasRight("29", 0) == true OR $_SESSION['username'] == $realUsername) {
					# Abfrage, ob der User den Artikel wirklich löschen will.
					echo "<form method=post>";
					echo "<p class='meldung'>Achtung, diese Aktion kann nicht Rückgängig gemacht werden. Dies löscht den Beitrag aus der Datenbank. <br>
					  Sind Sie sicher?</p>";
					echo "<input type = hidden value = '$id' name ='blogid' readonly />";
					echo "<p id='meldung'><input type=submit name='jaloeschen' value='Ja! Weg damit.'/></p><p id='erfolg'><a href='/flatnet2/blog/blog.php?action=showblogyes' class='buttonlink'>Nein, vergiss es.</a></p><br><br><br>";
					echo "</form>";
				} else {
					echo "<p class='meldung'>Sie sind nicht berechtigt, den Artikel zu löschen.</p>";
				}
			}
		}
	}

	/**
	 * zeigt die Felder zum erstellen einer Antwort für einen Forenbeitrag an.
	 */
	function newKommentar($blogid) {
		
		if($this->userHasRight("30", 0) == true) {
		
			$blogIDInfos = $this->getObjektInfo("SELECT id, locked FROM blogtexte WHERE id = '$blogid'");
			if($blogIDInfos->locked != 1) {
			
				if(!isset($_POST['absenden'])) {
					if($this->userHasRight("20", 0) == true) {
						echo "<div class='newForumPost'>";
						echo "<form method=post>";
						# Edit mit Inline Editing
						echo "<h2><a name='antwort'>Antworten</a></h2>";
						
						# Wenn gequotet wurde:
						if(isset($_GET['quote'])) {
							$quoteID = $_GET['quote'];
							$getQuoteInfo = $this->getObjektInfo("SELECT *
							, day(timestamp) as tag
							, month(timestamp) as monat
							, year(timestamp) as jahr
							, minute(timestamp) as minute
							, hour(timestamp) as stunde
							FROM blogtexte WHERE id = '$quoteID'");
							$quote = "<blockquote><strong>" . $this->getUserName($getQuoteInfo->autor) . "</strong> schrieb am " . $getQuoteInfo->tag . "." . $getQuoteInfo->monat . "." . $getQuoteInfo->jahr . " um " . $getQuoteInfo->stunde . ":" .$getQuoteInfo->minute. " Uhr zu <q>".$getQuoteInfo->titel."</q>:<br>" . strip_tags($getQuoteInfo->text, '<p></p><br><br/><br />') . "</blockquote><p>text...</p>";
						}
						
						# Wenn ein Kommentar gequotet wurde:
						if(isset($_GET['quoteKomment'])) {
							$quoteID = $_GET['quoteKomment'];
							$getQuoteInfo = $this->getObjektInfo("SELECT *
							, day(timestamp) as tag
							, month(timestamp) as monat
							, year(timestamp) as jahr
							, minute(timestamp) as minute
							, hour(timestamp) as stunde
							FROM blog_kommentare WHERE id = '$quoteID'");
							
							$quote = "<blockquote><strong>" . $this->getUserName($getQuoteInfo->autor) . "</strong> schrieb am " . $getQuoteInfo->tag . "." . $getQuoteInfo->monat . "." . $getQuoteInfo->jahr . " um " . $getQuoteInfo->stunde . ":" .$getQuoteInfo->minute. " Uhr:<br> " . strip_tags($getQuoteInfo->text, '<p></p><br><br/><br />') . "</blockquote><p>text...</p>";
						}
						
						
						# Wenn nichts gequoted wurde: 
						if(!isset($quote)) {
							$quote = "";
						}
						
						echo "<div class='editForumEintrag'><textarea class='ckeditor' name='kommentarText'>$quote</textarea></div>"; # Text Output #
					#	echo"<script> CKEDITOR.inline( 'kommentarText' ); </script>";
						echo "<br><input type='submit' name='absenden' value='absenden' />";
						echo "</form>";
						echo "</div>";
					}
				} else {
					if($_POST['kommentarText'] == "") {
						echo "<p class='info'>Textfeld ist leer.</p>";
					}
					if($_POST['kommentarText'] != "" AND $_POST['absenden'] == "absenden") {
						if($this->userHasRight("20", 0) == true) {
							
						# Alle Daten holen
						$text = $_POST['kommentarText'];
						# ID vom aktuellen Benutzer holen
						$autor = $_SESSION['username'];
						$selectBenutzer = "SELECT id, Name FROM benutzer WHERE Name = '$autor'";
						$ergebnisBenutzer = mysql_query($selectBenutzer);
						$rowAutor = mysql_fetch_object($ergebnisBenutzer);
						# ID geholt:
						$autorID = $rowAutor->id;
						# $blogid
							
						$insertKommentar="INSERT INTO blog_kommentare (autor, text, blogid) VALUES ('$autorID','$text','$blogid')";
						$ergebnis = mysql_query($insertKommentar);
						if($ergebnis == true) {
							echo "<p class='erfolg'>Antwort erstellt</p>";
						} else {
							echo "<p class='meldung'>Fehler</p>";
						}
						}
					}
				}
			} # Wenn locked ENDE
		} # Recht Ende
	} # FUNCTION ENDE

	/**
	 * KOMMENTAR AUSGABE
	 * @history Steven 15.09.2014 angelegt.
	 * Ermöglicht das betrachten von Kommentaren zu einer bestimmten BLOG ID
	 */
	function kommentare($blogid) {
		
		if(isset($_GET['newComment'])) {
			$this->newKommentar($blogid);
		}
		
		if($this->userHasRight("31", 0) == true) {
			# SQL Befehle
			$select = "SELECT 
						id, timestamp, autor, text
						, year(timestamp) AS jahr
						, month(timestamp) as monat
						, day(timestamp) as tag
						, hour(timestamp) AS stunde
						, minute(timestamp) AS minute
						, blogid
						FROM blog_kommentare 
						WHERE blogid=$blogid ORDER BY id";
			$ergebnis = mysql_query($select);
			
			$getLockedInfo = $this->getObjektInfo("SELECT * FROM blogtexte WHERE id = '$blogid'");
	
			# Ausgabe aller Kommentare
			while( $row = mysql_fetch_object($ergebnis) ) {
				
				# KOPFZEILE
				echo "<thead id='small'>";
				
					# Wenn der Benutzer einen Titel hat:
					$autorTitel = $this->getObjektInfo("SELECT id, titel FROM benutzer WHERE id = '" . $row->autor . "'");
					if(isset($autorTitel->titel) AND $autorTitel->titel != "") {
						$titel = "<p id='smallLink' class='rightRedLink'>" . $autorTitel->titel . "</p>";
					} else {
						$titel = "";
					}
				
					echo "<td><img id='id' src='../images/avatar_122.png' style='width:30px; height: 30px;' />
							<a id='id' class='rightBlueLink' href='#KommID".$row->id."' name='#KommID".$row->id."'>ID: ".$row->id."</a><p id='smallLink' class='rightBlueLink'>Autor: " . $this->getUserName($row->autor) . "</p> $titel</td>";
					echo "<td>Erstellt: " .$row->tag . "." . $row->monat . "." . $row->jahr . " | " . $row->stunde . ":" . $row->minute . " Uhr</td>";
					
					# EDIT
					echo "<td>";
					if($getLockedInfo->locked != 1) {
						if($this->userHasRight("32", 0) == "true" OR $_SESSION['username'] == $this->getUserName($row->autor)) {
							echo "<a href='?showblogid=$blogid&editComment=$row->id' class='rightRedLink'>edit</a>";
						}
					}
					
					# Löschen
					if($getLockedInfo->locked != 1) {
						if($this->userHasRight("35", 0) == true OR $this->userHasRight("34", 0) == true) {
							echo "<a href='?showblogid=$blogid&del=$row->id' class='rightRedLink'>Löschen</a>";
						}
					}
					
					if($getLockedInfo->locked != 1) {
						echo "<a class='rightBlueLink' href='?showblogid=$row->blogid&newComment=$row->blogid&quoteKomment=$row->id#antwort'> Zitat </a>";
					}
					# Zitat
					
					echo "</td>";
				
					
				
				echo "</thead>";
				
				
				echo "<tbody>";
					echo "<td colspan='3'>" . $row->text . "</td>";
				echo "</tbody>";
			}
		}
	}
	
	/**
	 * Ermöglicht das editieren eines Kommentars.
	 * @param unknown $kommentarID
	 * @stil NEUER STIL: Auf alle anderen Funktionen auch anwenden.
	 */
	function editKommentar($kommentarID) {
		
	#	$blogIDInfos = $this->getObjektInfo("SELECT id, locked FROM blogtexte WHERE id = '$blogid'");
	#	if($blogIDInfos->locked != 1) {
			# @todo 
	#	}
	
		if(isset($kommentarID) AND $kommentarID > 0 AND $kommentarID != "") {
			
			# Autor des Kommentars bekommen:
			$kommentarAutor = $this->getObjektInfo("SELECT * FROM blog_kommentare WHERE id = '$kommentarID' LIMIT 1");
			
			$autorID = $kommentarAutor->autor;
			$angemeldeterUserID = $this->getUserID($_SESSION['username']);
			
			if($this->userHasRight("32", 0) == true OR $this->userHasRight("33", 0) == true) {
				$kommentar = $this->getObjektInfo("SELECT * FROM blog_kommentare WHERE id ='$kommentarID' AND autor = '$autorID' LIMIT 1");
				if(!isset($kommentar) OR $kommentar == "") {
					echo "<p class='meldung'>Kommentar kann nicht editiert werden</p>";
				} else {
					if(!isset($_POST['saveKomment'])) {
				
						# NEU
						echo "<form method=post>";
						# KOPFZEILE
						echo "<table class='forumPost'>";
						echo "<thead>";
				
						echo "<td>Antwort editieren</td>";
				
						echo "</thead>";
							
						echo "<tbody>
						<td>";
						echo "<div class='editForumEintrag'><textarea class='ckeditor' name='kommentarText'>" . $kommentar->text . "</textarea></div>"; # Text Output #
					#	echo"<script> CKEDITOR.inline( 'kommentarText' ); </script>";
						echo "</td>
					</tbody>";
				
						echo "<tfoot><td><input type=submit name=saveKomment value=Speichern /><a class='highlightedLink' href='?showblogid=$kommentar->blogid'>Abbrechen</a></td></tfoot>";
						echo "</table>";
						echo "</form>";
							
					}
				
					# editierten Kommentar speichern.
					if(isset($_POST['saveKomment'])) {
						if(isset($_POST['kommentarText']) AND $_POST['kommentarText'] != "") {
							
							# EDTIERT VON XYZ AM XYZ DATE
							$editiertAm = date('d.m.Y h:i:s', time());
							$userDerGeaendertHat = $_SESSION['username'];
								
							# TEXT MODIFIZIEREN UND EDITIERT VON HINZUFÜGEN
							$text = $_POST['kommentarText'] . "<p><span style=\"font-size:10px; padding:3px; background-Color: #e0e0e0;\">Editiert am $editiertAm von $userDerGeaendertHat</span></p>";
							
							$query = "UPDATE blog_kommentare SET text = '$text' WHERE id = '$kommentar->id' AND autor = '$autorID'";
							if($this->sql_insert_update_delete($query) == true) {
								echo "<p class='erfolg'>Erfolgreich gespeichert</p>";
								echo "<a href='/flatnet2/blog/blogentry.php?showblogid=$kommentar->blogid' class='greenLink' > Zurück </a>";
							} else {
								echo "<p class='meldung'>Fehler beim speichern</p>";
							}
				
						}
					}
					# Rest ausblenden
					exit;
				}
			}
		}
	}

	/**
	 * Ermöglicht das löschen von Kommentaren
	 */
	function delKommentar($id, $blogid) {
	
		# Wenn der Benutzer volle Berechtigung zum löschen hat.
		if($this->userHasRight("35", 0) == true) {
			
			$loeschQuery = "DELETE FROM `blog_kommentare` WHERE id=$id AND blogid ='$blogid'";
			$ergebnis = mysql_query($loeschQuery);
			
			if($ergebnis == true) {
				echo "<p class='erfolg'><a name='geloescht'>Kommentar gelöscht!</a><a href='?showblogid=$blogid' class='buttonlink'>OK</a></p>";
				exit;
			} else {
				echo "<p class='meldung'>Fehler</p>";
				exit;
			}
			
		# Wenn der Benutzer nur einfache Berechtigung hat.
		} else if ($this->userHasRight("34", 0) == true) {
			
			$currentUser = $this->getUserID($_SESSION['username']);
			$loeschQuery = "DELETE FROM `blog_kommentare` WHERE id=$id AND autor = '$currentUser' AND blogid ='$blogid'";
			$ergebnis = mysql_query($loeschQuery);
				
			if($ergebnis == true) {
				echo "<p class='erfolg'><a name='geloescht'>Kommentar gelöscht!</a><a href='?showblogid=$blogid' class='buttonlink'>OK</a></p>";
				exit;
			} else {
				echo "<p class='meldung'>Fehler</p>";
				exit;
			}
		} else {
			echo "<p class='meldung'>Du hast keine Berechtigung zum löschen.</p>";
		}
		
	}
	
	/**
	 * Prüft, ob der Benutzer die Kategorie des aktuellen Beitrags anzeigen darf.
	 * @param unknown $user
	 * @param unknown $category
	 */
	function userIsAllowedToSeeCategory($user, $blogID) {
		$getblogidinfos = $this->getObjektInfo("SELECT id, kategorie FROM blogtexte WHERE id = '$blogID'");
		if(isset($getblogidinfos->kategorie) AND $getblogidinfos->kategorie != "") {
			$kategorie = $getblogidinfos->kategorie;
			
			# Benötigten Wert der Kategorie bekommen:
			$getKategorieRechteWert = $this->getObjektInfo("SELECT id, rightWert FROM blogkategorien WHERE id = '$kategorie'");
			
			# Rechte des aktuellen Benutzers bekommen
			$getRechteForumAktuellerBenutzer = $this->getObjektInfo("SELECT id, forumRights FROM benutzer WHERE id = '$user' ");
			
			# Rechte checken
			if($this->check($getKategorieRechteWert->rightWert, $getRechteForumAktuellerBenutzer->forumRights) == true) {
				return true;
			} else {
				return false;
			}
			
		} else {
			return false;
		}
		
	}

}
?>