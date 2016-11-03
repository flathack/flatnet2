<?php

include 'objekt/functions.class.php';
class amazon extends functions {
	
	function checkUser() {
		if($this->userHasRight(80, 0) == false) {
			echo "<h2>Willkommen</h2>";
			echo "<p>Hier siehst du alle deine gebuchten Einkäufe und die Gesamtsumme zur Zahlung";
		}
	}
	
	/**
	 * Gibt alle Zugeordneten Amazon-Umsätze zurück, welche für den Benutzer vorhanden sind.
	 */
	function getAmazonPayments() {
		echo "<div class='newFahrt'>";
		
		if($this->userHasRight(80, 0) == false) {
			$userid = $this->getUserID($_SESSION['username']);
			$query = "SELECT * FROM amazon_infos WHERE autor=$userid AND hide=0";
			$userArticles = $this->getObjektInfo($query);
			
			$getRuecksendungSumme = $this->getObjektInfo("SELECT sum(value_of_article) as summe FROM amazon_infos WHERE autor = $userid AND payed=1 AND ruecksendung=1 and erstattet=0");
				
			# CHECK IF USER HAS PAYED EVERYTHING
			$getopenSumme = $this->getObjektInfo("SELECT sum(value_of_article) as summe FROM amazon_infos WHERE autor = $userid AND payed=0 AND ruecksendung=0 and erstattet=0");
			$offeneSumme = 0 + $getopenSumme[0]->summe;
			if($offeneSumme == 0) {
				echo "<p class='dezentInfo'>Du hast alles bezahlt!<br>";
			} else {
				echo "<p class='info'>Offener Betrag: " . $offeneSumme . " € <br>";
			}
			
			$ruecksendungSumme = 0 + $getRuecksendungSumme[0]->summe;
			if($ruecksendungSumme > 0) {
				echo "Erstattungen von Rücksendungen: " . $ruecksendungSumme . " € <br>";
				$endbetrag = $offeneSumme - $ruecksendungSumme;
				if($endbetrag <= 0) {
					$endbetragText = "Guthaben:";
					$endbetrag = $endbetrag * (-1);
				} else {
					$endbetragText = "Überweisungsbetrag:";
				}
				echo "<strong>$endbetragText $endbetrag €</strong></p>";
			}
			echo "</p>";
			
			echo "<table class='kontoTable'>";
			echo "<thead>";
			echo "<td>" . "Datum des Kaufes" . "</td>";
			echo "<td>" . "Name des Artikels" . "</td>";
			echo "<td>" . "Preis" . "</td>";
			echo "<td>" . "Fällig am" . "</td>";
		#	echo "<td>" . "bezahlt am" . "</td>";
			echo "<td>Status</td>";
			echo "</thead>";
			
			for($i = 0 ; $i < sizeof($userArticles) ; $i++) {
				
				$articleStatus = "offen";
				$statusIsOk = "";
				if($userArticles[$i]->payed == 1 
					AND $userArticles[$i]->ruecksendung == 0 
					AND $userArticles[$i]->erstattet == 0) {
					$articleStatus = "bezahlt & abgeschlossen";
					$statusIsOk = "ok";
				}
				 
				if($userArticles[$i]->ruecksendung == 1 
					AND $userArticles[$i]->payed == 0 
					AND $userArticles[$i]->erstattet == 0) {
					$articleStatus = "zurückgesendet, warte auf Amazon";
					$statusIsOk = "pending";
				}
				
				if($userArticles[$i]->ruecksendung == 1 
					AND $userArticles[$i]->payed == 0 
					AND $userArticles[$i]->erstattet == 1) {
					$articleStatus = "Rücksendung abgeschlossen";
					$statusIsOk = "ok";
				}
				
				if($userArticles[$i]->ruecksendung == 1 
					AND $userArticles[$i]->payed == 1 
					AND $userArticles[$i]->erstattet == 0) {
					$articleStatus = "Rücksendung läuft, Geld wird erstattet, wenn Amazon Geld gutschreibt.";
					$statusIsOk = "pending";
				}
				
				if($userArticles[$i]->erstattet == 1 AND $userArticles[$i]->ruecksendung == 1 AND $userArticles[$i]->payed == 1) {
					$articleStatus = "zurückgesendet & Geld wurde erstattet ";
					$statusIsOk = 1;
				}
				
			#	if($statusIsOk == 1) { $status = "ok"; } else { $status = ""; }
				
				echo "<tbody id='$statusIsOk'>";
				echo "<td>" . $userArticles[$i]->date_of_order . "</td>";
				echo "<td>" . substr($userArticles[$i]->name_of_article,0,40) . "...</td>";
				echo "<td>" . $userArticles[$i]->value_of_article . "</td>";
				echo "<td>" . $userArticles[$i]->date_of_faelligkeit . "</td>";
				echo "<td>" . $articleStatus . "</td>";
				echo "</tbody>";
			}
			echo "</table>";
			
			
	}
		echo "</div>";
		
		$this->legende();
	}
	
	/**
	 * Eine Legende für die Nutzer zur Beschreiben der verschiedenen Stati.
	 */
	function legende() {
		echo "<div class='newFahrt'>";
		echo "<h3>Legende der Stati</h3>";
			echo "<h4>" ."bezahlt & abgeschlossen". "</h4>";
			echo "<p>" ."Du hast den Artikel bestellt und fristgerecht bezahlt. Dieser Posten benötigt keine weitere Aufmerksamkeit von dir.". "</p>";
			
			echo "<h4>" ."zurückgesendet, warte auf Amazon". "</h4>";
			echo "<p>" ."Dieser Status beschreibt den Umstand, dass du einen Artikel zurückgesendet hast, 
					den du noch nicht bezahlt hast. Wenn Amazon das Geld vor Monatsende gutschreibt, musst 
					du diesen Artikel nicht mehr bezahlen.". "</p>";
			
			echo "<h4>" ."Rücksendung abgeschlossen". "</h4>";
			echo "<p>" ."Wie oben, nur jetzt hat Amazon den Betrag wieder gutgeschrieben. Der Artikel muss am
					Monatsende nicht mehr von dir bezahlt werden.". "</p>";
			
			echo "<h4>" ."Rücksendung läuft, Geld wird erstattet, wenn Amazon Geld gutschreibt.". "</h4>";
			echo "<p>" ."Du hast den Artikel im letzten Monat bestellt, bereits bezahlt und wieder zurückgeschickt. 
					Sobald das Geld auf Stevens Konto gutgeschrieben wird, wird dieses mit dem neuen Monat verrechnet
					oder dir zurücküberwiesen.". "</p>";
		echo "</div>";
	}
	
	/**
	 * Zeigt alle Umsätze für Amazon an, für alle User
	 */
	function getAmazonPaymentsAdmin() {
	
		if($this->userHasRight(80, 0) == true) {
			
			
			$queryusers = "SELECT * FROM amazon_infos GROUP BY autor ORDER BY autor";
			$usersWithArticles = $this->getObjektInfo($queryusers);
			for ($i = 0 ; $i < sizeof($usersWithArticles) ; $i++) {
				
				echo "<div class='newFahrt'>";
								
				$userid = $usersWithArticles[$i]->autor;
				$username = $this->getUserName($usersWithArticles[$i]->autor);
				echo "<h2>" . $username . "</h2>";
				
				$offeneSumme = $this->getObjektInfo("SELECT sum(value_of_article) as summe FROM amazon_infos WHERE autor=$userid AND payed=0 AND ruecksendung=0");
				echo "<p class='dezentInfo'>";
				$openSum = 0 + $offeneSumme[0]->summe;
				if($openSum > 0) {
					echo "Überweisungsbetrag: $openSum € <br>";
				}
				
				$erstattenDuMusst = $this->getObjektInfo("SELECT sum(value_of_article) as summe FROM amazon_infos WHERE autor=$userid AND payed=1 AND ruecksendung=1 AND erstattet=0");
				
				$sumOfErstattung = 0 + $erstattenDuMusst[0]->summe;
				if($sumOfErstattung > 0) {
					echo "Erstattungsbetrag: $sumOfErstattung € <br> ";
				}
				
				$endsumme = 0 + $openSum - $sumOfErstattung;
				if($endsumme != $openSum) {
					echo "Endsumme: $endsumme €<br>";
				}
				echo "</p>";
				
				$query = "SELECT * FROM amazon_infos WHERE autor=$userid AND hide=0 ORDER BY payed, date_of_order, date_of_faelligkeit, date_of_payment";
				$adminArticles = $this->getObjektInfo($query);
				
				echo "<table class='kontoTable'>";
				echo "<thead>";
				#	echo "<td>" . "ID" . "</td>";
					echo "<td" ." id='' ". ">" . "Name" . "</td>";
					echo "<td" ." id='small' ". ">" . "Preis" . "</td>";
					echo "<td" ." id='date' ". ">" . "Kaufdatum" . "</td>";
					echo "<td" ." id='date' ". ">" . "Fällig am" . "</td>";
					echo "<td" ." id='date' ". ">" . "bezahlt" . "</td>";
					echo "<td" ." id='width70px' ". ">" . "Rücksendung" . "</td>";
					echo "<td" ." id='small' ". ">" . "Erstattet" . "</td>";
					echo "<td" ." id='width140px' ". ">" . "Optionen" . "</td>";
				echo "</thead>";
				
				for ($j = 0 ; $j < sizeof($adminArticles) ; $j++) {
					
					# Checken ob ein Artikel nicht zurückgeschickt aber erstattet wurde.
					if($adminArticles[$j]->ruecksendung == 0 AND $adminArticles[$j]->erstattet == 1) {
						echo "<tbody id='notOK'>";
						echo "<td colspan='8'>" . "Dieser FOLGENDE Artikel wurde erstattet, obwohl dieser nicht zurückgeschickt wurde:" . "</td>";
						echo "</tbody>";
					}
					
					# Zeile einfärben
					echo "<tbody"; 
					if($adminArticles[$j]->payed == 1 AND $adminArticles[$j]->ruecksendung == 0 AND $adminArticles[$j]->erstattet == 0) {
						echo " id='ok' ";
					}
					
					if($adminArticles[$j]->payed == 0 AND $adminArticles[$j]->ruecksendung == 1 AND $adminArticles[$j]->erstattet == 1) {
						echo " id='ok' ";
					}
					
					if($adminArticles[$j]->payed == 1 AND $adminArticles[$j]->ruecksendung == 1 AND $adminArticles[$j]->erstattet == 0) {
						echo " id='notOK' ";
					}
					
					if($adminArticles[$j]->payed == 1 AND $adminArticles[$j]->ruecksendung == 1 AND $adminArticles[$j]->erstattet == 1) {
						echo " id='ok' ";
					}
					
					if($adminArticles[$j]->ruecksendung == 0 AND $adminArticles[$j]->erstattet == 1) {
						echo " id='notOK' ";
					}
					
					echo ">";
					echo "<td>" . substr($adminArticles[$j]->name_of_article,0,40) . "...</td>";
					echo "<td>" . $adminArticles[$j]->value_of_article . "</td>";
					echo "<td>" . $adminArticles[$j]->date_of_order . "</td>";
					echo "<td>" . $adminArticles[$j]->date_of_faelligkeit . "</td>";
						
					# GEZAHLT LINK
					echo "<td>";
						if($adminArticles[$j]->payed == 1) {
							echo "<a href='?id=" . $adminArticles[$j]->id . "&PayedUNDO'>".$adminArticles[$j]->date_of_payment."</a>";
						}
						if($adminArticles[$j]->payed == 0) {
							echo "<a href='?id=" . $adminArticles[$j]->id . "&PayedDO'>nein</a>";
						}
					echo "</td>";
					
					# RÜCKSENDUNG LINK
					echo "<td>"; 
						if($adminArticles[$j]->ruecksendung == 1) {
							echo "<a href='?id=" . $adminArticles[$j]->id . "&sendbackUNDO'>zurückgesendet</a>";
						}
						if($adminArticles[$j]->ruecksendung == 0) {
							echo "<a href='?id=" . $adminArticles[$j]->id . "&sendbackDO'>nein</a>";
						}
					echo "</td>";
					
					# ERSTATTET LINK
					echo "<td>"; 
						if($adminArticles[$j]->erstattet == 1) {
							echo "<a href='?id=" . $adminArticles[$j]->id . "&erstattetUNDO'>erstattet</a>";
						}
						if($adminArticles[$j]->erstattet == 0) {
							echo "<a href='?id=" . $adminArticles[$j]->id . "&erstattetDO'>nein</a>";
						}
					echo "</td>";
					
					echo "<td>";
						if($adminArticles[$j]->hide == 1) {
							echo "<a href='?id=" . $adminArticles[$j]->id . "&HideUNDO'>verborgen</a>";
						}
						if($adminArticles[$j]->hide == 0) {
							echo "<a href='?id=" . $adminArticles[$j]->id . "&HideDO'>verbergen</a>";
						}
						echo " <a class='rightBlueLink'href=/flatnet2/admin/control.php?action=3&table=amazon_infos&id=" . $adminArticles[$j]->id . "#angezeigteID>edit</a> ";
					echo "</td>";
						
					echo "</tbody>";
					
				}
				echo "</table>";
				echo "</div>";
			}
			
		}
	}
	
	/**
	 * Ermöglicht das einfügen neuer Zeilen in die Amazon Payments
	 */
	function createAmazonArticle() {
		if($this->userHasRight(80, 0) == true) {
			
			# Check if there was data before
			if(isset($_POST['kdate'])) { $kdate = $_POST['kdate']; } else { $kdate = ""; }
			if(isset($_POST['fdate'])) { $fdate = $_POST['fdate']; } else { $fdate = ""; }
			
			echo "<div class='newFahrt'>";
				echo "<h2>Neuer Datensatz</h2>";
				
				$getusers = "SELECT * FROM benutzer";
				$alluser = $this->getObjektInfo($getusers);
				
				echo "<form method=post>";
				echo "<table>";
					echo "<thead>";
						echo "<td>User</td>";
						echo "<td>Name</td>";
						echo "<td>Preis</td>";
					echo "</thead>";
					
					echo "<tbody>";
						echo "<td><select name=user>";
						for($i = 0 ; $i < sizeof($alluser) ; $i++) {
							
							if(isset($_POST['user'])) {
								if($_POST['user'] == $alluser[$i]->id) {
									$selected="selected";
								} else {
									$selected="";
								}
							}
							
							echo "<option $selected value='".$alluser[$i]->id."'>" . $alluser[$i]->Name. "</option>";
						}
						echo "</select></td>";
						echo "<td><input name=name type=text placeholder='Name des Artikels' /></td>";
						echo "<td><input name=preis type=text placeholder='Preis' /></td>";
					echo "</tbody>";
					
				echo "</table>";
				
				echo "<table>";
					echo "<thead>";
						echo "<td>Kaufdatum</td>";
						echo "<td>Fällig am</td>";
					echo "</thead>";
					echo "<tbody>";
						echo "<td><input name=kdate value='$kdate' type=date placeholder='Kaufdatum' /></td>";
						echo "<td><input name=fdate value='$fdate' type=date placeholder='Fälligkeit' /></td>";
						echo "<td><input type=submit name=absenden value=speichern /></td>";
					echo "</tbody>";
					
				echo "</table>";
				echo "</form>";
				
			
			
			### Umsatz speichern ###
			
			if (isset($_POST['user']) and isset($_POST['name']) and isset($_POST['preis']) and isset($_POST['kdate']) and isset($_POST['fdate'])) {
				
				$user = $_POST['user'];
				$name = $_POST['name'];
				$preis = str_replace ( ',', '.', $_POST['preis'] );
				$kdate = $_POST['kdate'];
				$fdate = $_POST['fdate'];
				
				$getusername = $this->getObjektInfo("SELECT id, Name FROM benutzer WHERE id='$user' LIMIT 1");
				
				echo "<p class='dezentInfo'>Folgende Daten wurden eingegeben: <br>Benutzer: " .$getusername[0]->Name. " | Name des Artikels: $name | Preis: $preis | Kaufdatum: $kdate | Fälligkeitsdatum: $fdate</p>";
				
				if($user > 0 AND $preis != "" AND $kdate != "" AND $fdate != "") {
					
					$query="INSERT INTO amazon_infos (autor, name_of_article, value_of_article, date_of_order, date_of_faelligkeit, date_of_payment, payed, ruecksendung, erstattet,hide) 
							VALUES ('$user', '$name', '$preis', '$kdate', '$fdate', '0000-00-00', '0', '0', '0', '0')";
					
					if ($this->sql_insert_update_delete($query) == true) {
						
						echo "<p class='erfolg'>Der Artikel wurde erstellt.</p>";
						
					} else {
						
						echo "<p class='meldung'>Beim erstellen ist ein Fehler aufgetreten.</p>" ;
						
					}
				} else {
					echo "<p class='meldung'>Nicht alle Felder befüllt.</p>";
				}
				
			}
			
			echo "</div>";
		}
		
	}
	
	/**
	 * TODO
	 */
	function editPayment() {
		
		if($this->userHasRight(80, 0) == true) {
			
			if(isset($_GET['edit'])) {
				if(isset($_GET['id'])) {
					
					echo "<div>";
					echo "<h3>Edit des Umsatzes Nr " . $_GET['id'] . "</h3>";
					echo "</div>";
					
				}
				
			}
			
		}
		
	}
	
	function setRuecksendung() {
		if($this->userHasRight(80, 0) == true) {
			# Es wird zurückgesendet
			if(isset($_GET['sendbackDO']) AND isset($_GET['id'])) {
				$id = $_GET['id'];
				$articleInfos=$this->getObjektInfo("SELECT id, name_of_article, payed,ruecksendung,erstattet FROM amazon_infos WHERE id=$id");
				$sqlupdate = "UPDATE amazon_infos SET ruecksendung=1 WHERE id=$id";
				if ($this->sql_insert_update_delete($sqlupdate) == true) {
					echo "<p class='erfolg'>Der Artikel " .$articleInfos[0]->name_of_article. " wurde auf <strong>zurückgeschickt</strong> gesetzt.</p>";
				} else {
					echo "<p class='meldung'>SQL ERROR</p>";
				}
				
			}
			
			#Rücksendung wird auf 0 gesetzt
			if(isset($_GET['sendbackUNDO']) AND isset($_GET['id'])) {
				$id = $_GET['id'];
				$articleInfos=$this->getObjektInfo("SELECT id, name_of_article, payed,ruecksendung,erstattet FROM amazon_infos WHERE id=$id");
				$sqlupdate = "UPDATE amazon_infos SET ruecksendung=0 WHERE id=$id";
				if ($this->sql_insert_update_delete($sqlupdate) == true) {
					echo "<p class='erfolg'>Der Artikel " .$articleInfos[0]->name_of_article. " wurde auf <strong>nicht zurückgeschickt</strong> gesetzt.</p>";
				} else {
					echo "<p class='meldung'>SQL_ERROR</p>";
				}
			
			}
		}
	}
	
	/**
	 * Setzt einen Artikel in der Datenbank amazon_infos in der Spalte erstattet auf 1 oder 0
	 */
	function setErstattet() {
		if($this->userHasRight(80, 0) == true) {
			# Es wird erstattet
			if(isset($_GET['erstattetDO']) AND isset($_GET['id'])) {
				$id = $_GET['id'];
				$articleInfos=$this->getObjektInfo("SELECT id, name_of_article, payed,ruecksendung,erstattet FROM amazon_infos WHERE id=$id");
				# Man darf nur etwas erstatten, wenn es zurückgesendet wurde
				if($articleInfos[0]->ruecksendung == 1) {
					$sqlupdate = "UPDATE amazon_infos SET erstattet=1 WHERE id=$id";
					if ($this->sql_insert_update_delete($sqlupdate) == true) {
						echo "<p class='erfolg'>Der Artikel ".$articleInfos[0]->name_of_article ." wurde auf <strong>erstattet</strong> gesetzt.</p>";
					} else {
						echo "<p class='meldung'>SQL ERROR</p>";
					}
				} else {
					echo "<p class='meldung'>Du darfst nichts erstatten, was nicht zurückgeschickt wurde.</p>";
				}
			}
				
			# Erstattung wird auf 0 gesetzt
			if(isset($_GET['erstattetUNDO']) AND isset($_GET['id'])) {
				$id = $_GET['id'];
				$articleInfos=$this->getObjektInfo("SELECT id, name_of_article, payed,ruecksendung,erstattet FROM amazon_infos WHERE id=$id");
				$sqlupdate = "UPDATE amazon_infos SET erstattet=0 WHERE id=$id";
				if ($this->sql_insert_update_delete($sqlupdate) == true) {
					echo "<p class='erfolg'>Der Artikel ".$articleInfos[0]->name_of_article ." wurde auf <strong>nicht erstattet</strong> gesetzt</p>";
				} else {
					echo "<p class='meldung'>ERROR</p>";
				}
			}
		}
	}
	
	/**
	 * Setzt das gezahlt Attribut in der Relation amazon_infos von 0 auf 1 und umgekehrt
	 */
	function setPayed() {
		if($this->userHasRight(80, 0) == true) {
				
			# payed Attribut wird auf 0 gesetzt
			if(isset($_GET['PayedDO']) AND isset($_GET['id'])) {
				$id = $_GET['id'];
				$sqlupdate="UPDATE amazon_infos SET payed=1,date_of_payment=CURRENT_TIMESTAMP WHERE id=$id";
				if ($this->sql_insert_update_delete($sqlupdate) == true) {
					echo "<p class='erfolg'>OK</p>";
				} else {
					echo "<p class='meldung'>ERROR</p>";
				}
			}
	
			# payed Attribut wird auf 0 gesetzt
			if(isset($_GET['PayedUNDO']) AND isset($_GET['id'])) {
				$id = $_GET['id'];
				$sqlupdate="UPDATE amazon_infos SET payed=0,date_of_payment='0000-00-00' WHERE id=$id";
				if ($this->sql_insert_update_delete($sqlupdate) == true) {
					echo "<p class='erfolg'>OK</p>";
				} else {
					echo "<p class='meldung'>ERROR</p>";
				}
			}
		}
	}
	
	/**
	 * Setzt das gezahlt Attribut von 0 auf 1 und umgekehrt
	 */
	function setHide() {
		if($this->userHasRight(80, 0) == true) {
	
			# payed Attribut wird auf 0 gesetzt
			if(isset($_GET['HideDO']) AND isset($_GET['id'])) {
				$id = $_GET['id'];
				$sqlupdate = "UPDATE amazon_infos SET hide=1 WHERE id=$id";
				if ($this->sql_insert_update_delete($sqlupdate) == true) {
					echo "<p class='erfolg'>OK</p>";
				} else {
					echo "<p class='meldung'>ERROR</p>";
				}
			}
	
			# payed Attribut wird auf 0 gesetzt
			if(isset($_GET['HideUNDO']) AND isset($_GET['id'])) {
				$id = $_GET['id'];
				$sqlupdate = "UPDATE amazon_infos SET hide=0 WHERE id=$id";
				if ($this->sql_insert_update_delete($sqlupdate) == true) {
					echo "<p class='erfolg'>OK</p>";
				} else {
					echo "<p class='meldung'>ERROR</p>";
				}
					
			}
	
		}
	}
	
}