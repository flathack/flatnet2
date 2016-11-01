<?php

include 'objekt/functions.class.php';
class amazon extends functions {
	
	function checkUser() {
		if($this->userHasRight(80, 0) == false) {
			echo "<h2>Willkommen</h2>";
			echo "<p>Hier siehst du alle deine gebuchten Eink�ufe und die Gesamtsumme zur Zahlung";
		}
		
	}
	
	/**
	 * Gibt alle Zugeordneten Amazon-Ums�tze zur�ck, welche f�r den Benutzer vorhanden sind.
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
				echo "<p class='erfolg'>Du hast alles bezahlt!</p>";
			} else {
				echo "<p class='info'>Offener Betrag: " . $offeneSumme . " � <br>";
			}
			
			$ruecksendungSumme = 0 + $getRuecksendungSumme[0]->summe;
			if($ruecksendungSumme > 0) {
				echo "Erstattungen von R�cksendungen: " . $ruecksendungSumme . " � <br>";
				$endbetrag = $offeneSumme - $ruecksendungSumme;
				echo "<strong>Endbetrag: $endbetrag �</strong></p>";
			}
			echo "</p>";
			
			
			echo "<table class='kontoTable'>";
			echo "<thead>";
			echo "<td>" . "Datum des Kaufes" . "</td>";
			echo "<td>" . "Name des Artikels" . "</td>";
			echo "<td>" . "Preis" . "</td>";
			echo "<td>" . "F�llig am" . "</td>";
		#	echo "<td>" . "bezahlt am" . "</td>";
			echo "<td>Status</td>";
			echo "</thead>";
			
			for($i = 0 ; $i < sizeof($userArticles) ; $i++) {
				if($userArticles[$i]->payed == 1 AND $userArticles[$i]->ruecksendung == 0
						OR $userArticles[$i]->ruecksendung == 1 AND $userArticles[$i]->payed == 0
						OR $userArticles[$i]->ruecksendung == 1 AND $userArticles[$i]->payed == 1) {
					$statusIsOk=1;
				} else {
					$statusIsOk=0;
				}
				
				if($statusIsOk == 1) {
					$status = "ok";
				} else {
					$status = "";
				}
				
				echo "<tbody id='$status'>";
				echo "<td>" . $userArticles[$i]->date_of_order . "</td>";
				echo "<td>" . substr($userArticles[$i]->name_of_article,0,40) . "...</td>";
				echo "<td>" . $userArticles[$i]->value_of_article . "</td>";
				echo "<td>" . $userArticles[$i]->date_of_faelligkeit . "</td>";
			#	echo "<td>" . $userArticles[$i]->date_of_payment . "</td>";
				echo "<td>";
				if($userArticles[$i]->payed == 0 AND $userArticles[$i]->ruecksendung == 0) {
					echo " offen ";
				}
				
				if($userArticles[$i]->payed == 1) {
					echo "& bezahlt ";
				}
				if($userArticles[$i]->ruecksendung == 1) {
					echo "& zur�ckgesendet ";
				}
				
				if($userArticles[$i]->erstattet == 1) {
					echo "& Geld wurde erstattet ";
				}
				
				if($userArticles[$i]->payed == 1 AND $userArticles[$i]->ruecksendung == 0) {
					echo "& ABGESCHLOSSEN | ";
				}
				echo "</td>";
				echo "</tbody>";
			}
			echo "</table>";
			
	}
		echo "</div>";
	}
	
	/**
	 * Zeigt alle Ums�tze f�r Amazon an, f�r alle User
	 */
	function getAmazonPaymentsAdmin() {
	
		if($this->userHasRight(80, 0) == true) {
			
			echo "<div class='newFahrt'>";
						
			$queryusers = "SELECT * FROM amazon_infos GROUP BY autor";
			$usersWithArticles = $this->getObjektInfo($queryusers);
			
			for ($i = 0 ; $i < sizeof($usersWithArticles) ; $i++) {
								
				$userid = $usersWithArticles[$i]->autor;
				$username = $this->getUserName($usersWithArticles[$i]->autor);
				echo "<h2>" . $username . "</h2>";
				
				$offeneSumme = $this->getObjektInfo("SELECT sum(value_of_article) as summe FROM amazon_infos WHERE autor=$userid AND payed=0 AND ruecksendung=0");
				echo "<p class='info'>";
				$openSum = 0 + $offeneSumme[0]->summe;
				if($openSum > 0) {
					echo "$openSum � sind offen <br>";
				}
				
				$erstattenDuMusst = $this->getObjektInfo("SELECT sum(value_of_article) as summe FROM amazon_infos WHERE autor=$userid AND payed=1 AND ruecksendung=1 AND erstattet=0");
				
				$sumOfErstattung = 0 + $erstattenDuMusst[0]->summe;
				if($sumOfErstattung > 0) {
					echo "Du musst $sumOfErstattung � erstatten <br> ";
				}
				echo "</p>";
				
				$query = "SELECT * FROM amazon_infos WHERE autor=$userid AND hide=0 ORDER BY payed, date_of_order, date_of_faelligkeit, date_of_payment";
				$adminArticles = $this->getObjektInfo($query);
				
				echo "<table class='kontoTable'>";
				echo "<thead>";
				#	echo "<td>" . "ID" . "</td>";
					echo "<td>" . "Name" . "</td>";
					echo "<td>" . "Preis" . "</td>";
					echo "<td>" . "Kaufdatum" . "</td>";
					echo "<td>" . "F�llig am" . "</td>";
					echo "<td>" . "bezahlt" . "</td>";
					echo "<td>" . "R�cksendung" . "</td>";
					echo "<td>" . "Erstattet" . "</td>";
					echo "<td>" . "Verbergen" . "</td>";
				echo "</thead>";
				for ($j = 0 ; $j < sizeof($adminArticles) ; $j++) {
					
					# Zeile einf�rben
					echo "<tbody"; 
					if($adminArticles[$j]->payed == 1 AND $adminArticles[$j]->ruecksendung == 0) {
						echo " id='ok' ";
					}
					
					if($adminArticles[$j]->payed == 1 AND $adminArticles[$j]->ruecksendung == 1 AND $adminArticles[$j]->erstattet == 0) {
						echo " id='notOK' ";
					}
					
					if($adminArticles[$j]->payed == 1 AND $adminArticles[$j]->ruecksendung == 1 AND $adminArticles[$j]->erstattet == 1) {
						echo " id='ok' ";
					}
					
					echo ">";
					
					#	echo "<td>" . $adminArticles[$j]->id . "</td>";
					#	echo "<td>" . $adminArticles[$j]->autor . "</td>";
						echo "<td>" . substr($adminArticles[$j]->name_of_article,0,40) . "...</td>";
						echo "<td>" . $adminArticles[$j]->value_of_article . "</td>";
						echo "<td>" . $adminArticles[$j]->date_of_order . "</td>";
						echo "<td>" . $adminArticles[$j]->date_of_faelligkeit . "</td>";
					#	echo "<td>" . $adminArticles[$j]->date_of_payment . "</td>";
						
						# GEZAHLT LINK
						echo "<td>";
						if($adminArticles[$j]->payed == 1) {
							echo "<a href='?id=" . $adminArticles[$j]->id . "&PayedUNDO'>bezahlt</a>";
						}
						if($adminArticles[$j]->payed == 0) {
							echo "<a href='?id=" . $adminArticles[$j]->id . "&PayedDO'>nein</a>";
						}
						echo "</td>";
						
						# R�CKSENDUNG LINK
						echo "<td>"; 
							if($adminArticles[$j]->ruecksendung == 1) {
								echo "<a href='?id=" . $adminArticles[$j]->id . "&sendbackUNDO'>zur�ckgesendet</a>";
							}
							if($adminArticles[$j]->ruecksendung == 0) {
								echo "<a href='?id=" . $adminArticles[$j]->id . "&sendbackDO'>nein</a>";
							}
						echo "</td>";
						
						# ERSTATTET LINK
						echo "<td>"; 
							if($adminArticles[$j]->erstattet == 1) {
								echo "<a href='?id=" . $adminArticles[$j]->id . "&erstattetUNDO'>wurde erstattet</a>";
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
						echo "</td>";
					
						
					echo "</tbody>";
				}
				echo "<tfoot><td colspan=10>Endpreis:  �</td></tfoot>";
				echo "</table>";
				
				
			}
			
			echo "</div>";
			
		}
	}
	
	/**
	 * Erm�glicht das einf�gen neuer Zeilen in die Amazon Payments
	 */
	function createAmazonArticle() {
		if($this->userHasRight(80, 0) == true) {
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
							echo "<option value='".$alluser[$i]->id."'>" . $alluser[$i]->Name. "</option>";
						}
						echo "</select></td>";
						echo "<td><input name=name type=text placeholder='Name des Artikels' /></td>";
						echo "<td><input name=preis type=text placeholder='Preis' /></td>";
					echo "</tbody>";
					
				echo "</table>";
				
				echo "<table>";
					echo "<thead>";
						echo "<td>Kaufdatum</td>";
						echo "<td>F�llig am</td>";
					echo "</thead>";
					echo "<tbody>";
						echo "<td><input name=kdate type=date placeholder='Kaufdatum' /></td>";
						echo "<td><input name=fdate type=date placeholder='F�lligkeit' /></td>";
						echo "<td><input type=submit name=absenden value=speichern /></td>";
					echo "</tbody>";
					
				echo "</table>";
				echo "</form>";
				
			echo "</div>";
			
			### Umsatz speichern ###
			
			if (isset($_POST['user']) and isset($_POST['name']) and isset($_POST['preis']) and isset($_POST['kdate']) and isset($_POST['fdate'])) {
				
				$user = $_POST['user'];
				$name = $_POST['name'];
				$preis = $_POST['preis'];
				$kdate = $_POST['kdate'];
				$fdate = $_POST['fdate'];
				
				echo "$user . $name . $preis . $kdate . $fdate";
				
				if($user > 0 AND $preis != "" AND $kdate != "" AND $fdate != "") {
					
					$query="INSERT INTO amazon_infos (autor, name_of_article, value_of_article, date_of_order, date_of_faelligkeit, date_of_payment, payed, ruecksendung, erstattet,hide) 
							VALUES ('$user', '$name', '$preis', '$kdate', '$fdate', '0000-00-00', '0', '0', '0', '0')";
					
					if ($this->sql_insert_update_delete($query) == true) {
						
						echo "<p class='erfolg'>ERFOLG</p>";
						
					} else {
						
						echo "<p class='meldung'>FAIL</p>" ;
						
					}
				}
				
			}
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
			# Es wird zur�ckgesendet
			if(isset($_GET['sendbackDO']) AND isset($_GET['id'])) {
				$id = $_GET['id'];
				$sqlupdate = "UPDATE amazon_infos SET ruecksendung=1 WHERE id=$id";
				if ($this->sql_insert_update_delete($sqlupdate) == true) {
					echo "<p class='erfolg'>OK</p>";
				} else {
					echo "<p class='meldung'>ERROR</p>";
				}
				
			}
			
			#R�cksendung wird auf 0 gesetzt
			if(isset($_GET['sendbackUNDO']) AND isset($_GET['id'])) {
				$id = $_GET['id'];
				$sqlupdate = "UPDATE amazon_infos SET ruecksendung=0 WHERE id=$id";
				if ($this->sql_insert_update_delete($sqlupdate) == true) {
					echo "<p class='erfolg'>OK</p>";
				} else {
					echo "<p class='meldung'>ERROR</p>";
				}
			
			}
		}
	}
	
	function setErstattet() {
		if($this->userHasRight(80, 0) == true) {
			
			# Es wird erstattet
			if(isset($_GET['erstattetDO']) AND isset($_GET['id'])) {
				$id = $_GET['id'];
				$sqlupdate = "UPDATE amazon_infos SET erstattet=1 WHERE id=$id";
				if ($this->sql_insert_update_delete($sqlupdate) == true) {
					echo "<p class='erfolg'>OK</p>";
				} else {
					echo "<p class='meldung'>ERROR</p>";
				}
			}
				
			# Erstattung wird auf 0 gesetzt
			if(isset($_GET['erstattetUNDO']) AND isset($_GET['id'])) {
				$id = $_GET['id'];
				$sqlupdate = "UPDATE amazon_infos SET erstattet=0 WHERE id=$id";
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
	function setPayed() {
		if($this->userHasRight(80, 0) == true) {
				
			# payed Attribut wird auf 0 gesetzt
			if(isset($_GET['PayedDO']) AND isset($_GET['id'])) {
				$id = $_GET['id'];
				$sqlupdate = "UPDATE amazon_infos SET payed=1 WHERE id=$id";
				if ($this->sql_insert_update_delete($sqlupdate) == true) {
					echo "<p class='erfolg'>OK</p>";
				} else {
					echo "<p class='meldung'>ERROR</p>";
				}
			}
	
			# payed Attribut wird auf 0 gesetzt
			if(isset($_GET['PayedUNDO']) AND isset($_GET['id'])) {
				$id = $_GET['id'];
				$sqlupdate = "UPDATE amazon_infos SET payed=0 WHERE id=$id";
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