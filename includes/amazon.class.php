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
		echo "<div class=''>";
		
		if($this->userHasRight(80, 0) == false) {
			$userid = $this->getUserID($_SESSION['username']);
			$query = "SELECT * FROM amazon_infos WHERE autor=$userid ";
			$userArticles = $this->getObjektInfo($query);
			
			$getRuecksendungSumme = $this->getObjektInfo("SELECT sum(value_of_article) as summe FROM amazon_infos WHERE autor = $userid AND payed=1 AND ruecksendung=1 and erstattet=0");
				
			# CHECK IF USER HAS PAYED EVERYTHING
			$getopenSumme = $this->getObjektInfo("SELECT sum(value_of_article) as summe FROM amazon_infos WHERE autor = $userid AND payed=0 AND ruecksendung=0 and erstattet=0");
			$offeneSumme = 0 + $getopenSumme[0]->summe;
			if($offeneSumme == 0) {
				echo "<p class='erfolg'>Du hast alles bezahlt!</p>";
			} else {
				echo "<p class='info'> - Du musst bezahlen: " . $offeneSumme . " €</p>";
			}
			
			$ruecksendungSumme = 0 + $getRuecksendungSumme[0]->summe;
			if($ruecksendungSumme > 0) {
				echo "<p class='erfolg'>Steven muss dir überweisen: " . $ruecksendungSumme . " €</p>";
			}
			
			
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
				if($userArticles[$i]->payed == 1 AND $userArticles[$i]->ruecksendung == 0
						OR $userArticles[$i]->ruecksendung == 1 AND $userArticles[$i]->payed == 0) {
					$statusIsOk=1;
				} else {
					$statusIsOk=0;
				}
				
				if($statusIsOk == 1) {
					$status = "ok";
				} else {
					$status = "notOK";
				}
				
				echo "<tbody id='$status'>";
				echo "<td>" . $userArticles[$i]->date_of_order . "</td>";
				echo "<td>" . $userArticles[$i]->name_of_article . "</td>";
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
					echo "& zurückgesendet ";
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
	 * Zeigt alle Umsätze für Amazon an, für alle User
	 */
	function getAmazonPaymentsAdmin() {
	
		if($this->userHasRight(80, 0) == true) {
			
			echo "<h2>Umsätze deiner User</h2>";
			
			echo "<div class='ok'>";
			
			$queryusers = "SELECT * FROM amazon_infos GROUP BY autor";
			$usersWithArticles = $this->getObjektInfo($queryusers);
			
			for ($i = 0 ; $i < sizeof($usersWithArticles) ; $i++) {
								
				$userid = $usersWithArticles[$i]->autor;
				$username = $this->getUserName($usersWithArticles[$i]->autor);
				echo "<h3>" . $username . "</h3>";
				
				$offeneSumme = $this->getObjektInfo("SELECT sum(value_of_article) as summe FROM amazon_infos WHERE autor=$userid AND payed=0 AND ruecksendung=0");
				
				$openSum = 0 + $offeneSumme[0]->summe;
				if($openSum > 0) {
					echo "<p class='meldung'>$openSum € sind offen</p>";
				}
				
				$erstattenDuMusst = $this->getObjektInfo("SELECT sum(value_of_article) as summe FROM amazon_infos WHERE autor=$userid AND payed=1 AND ruecksendung=1 AND erstattet=0");
				
				$sumOfErstattung = 0 + $erstattenDuMusst[0]->summe;
				if($sumOfErstattung > 0) {
					echo "<p class='meldung'>Du musst $sumOfErstattung € erstatten</p>";
				}
				
				$query = "SELECT * FROM amazon_infos WHERE autor =$userid ORDER BY date_of_order, date_of_faelligkeit, date_of_payment";
				$adminArticles = $this->getObjektInfo($query);
				
				echo "<table class='kontoTable'>";
				echo "<thead>";
					echo "<td>" . "ID" . "</td>";
					echo "<td>" . "Name" . "</td>";
					echo "<td>" . "Preis" . "</td>";
					echo "<td>" . "Kaufdatum" . "</td>";
					echo "<td>" . "Fällig am" . "</td>";
				#	echo "<td>" . "gezahlt am" . "</td>";
					echo "<td>" . "Rücksendung" . "</td>";
					echo "<td>" . "Erstattet" . "</td>";
					echo "<td>" . "Optionen" . "</td>";
				echo "</thead>";
				for ($j = 0 ; $j < sizeof($adminArticles) ; $j++) {
					echo "<tbody>";
						echo "<td>" . $adminArticles[$j]->id . "</td>";
					#	echo "<td>" . $adminArticles[$j]->autor . "</td>";
						echo "<td>" . $adminArticles[$j]->name_of_article . "</td>";
						echo "<td>" . $adminArticles[$j]->value_of_article . "</td>";
						echo "<td>" . $adminArticles[$j]->date_of_order . "</td>";
						echo "<td>" . $adminArticles[$j]->date_of_faelligkeit . "</td>";
					#	echo "<td>" . $adminArticles[$j]->date_of_payment . "</td>";
					#	echo "<td>" . $adminArticles[$j]->payed . "</td>";
						echo "<td>"; 
							if($adminArticles[$j]->ruecksendung == 1) {
								echo "<a href='?id=" . $adminArticles[$j]->id . "&sendbackUNDO'>zurückgesendet</a>";
							}
							if($adminArticles[$j]->ruecksendung == 0) {
								echo "<a href='?id=" . $adminArticles[$j]->id . "&sendbackDO'>nein</a>";
							}
						echo "</td>";
						echo "<td>"; 
							if($adminArticles[$j]->erstattet == 1) {
								echo "<a href='?id=" . $adminArticles[$j]->id . "&erstattetUNDO'>wurde erstattet</a>";
							}
							if($adminArticles[$j]->erstattet == 0) {
								echo "<a href='?id=" . $adminArticles[$j]->id . "&erstattetDO'>nein</a>";
							}
						echo "</td>";
						echo "<td>" . "<a href='?id=" . $adminArticles[$j]->id . "&edit=yes'>edit</a>" . "</td>";
						
					echo "</tbody>";
				}
				echo "<tfoot><td colspan=10>Endpreis:  €</td></tfoot>";
				echo "</table>";
				
				
			}
			
			echo "</div>";
			
		}
	}
	
	/**
	 * Ermöglicht das einfügen neuer Zeilen in die Amazon Payments
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
						echo "<td>Kaufdatum</td>";
						echo "<td>Fällig am</td>";
					echo "</thead>";
					echo "<tbody><td>";
					echo "<select name=user>";
					for($i = 0 ; $i < sizeof($alluser) ; $i++) {
						echo "<option value='".$alluser[$i]->id."'>" . $alluser[$i]->Name. "</option>";
					}
					echo "</select></td>";
					
					echo "<td><input name=name type=text placeholder='Name des Artikels' /></td>";
					echo "<td><input name=preis type=text placeholder='Preis' /></td>";
					echo "<td><input name=kdate type=date placeholder='Kaufdatum' /></td>";
					echo "<td><input name=fdate type=date placeholder='Fälligkeit' /></td>";
					echo "<td><input type=submit name=absenden value=speichern /></td>";
					echo "</form>";
				
				echo "</table>";
				
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
					
					$query="INSERT INTO amazon_infos (autor, name_of_article, value_of_article, date_of_order, date_of_faelligkeit, date_of_payment, payed, ruecksendung, erstattet) 
							VALUES ('$user', '$name', '$preis', '$kdate', '$fdate', '0000-00-00', '0', '0', '0')";
					
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
			# Es wird zurückgesendet
			if(isset($_GET['sendbackDO']) AND isset($_GET['id'])) {
				$id = $_GET['id'];
				$sqlupdate = "UPDATE amazon_infos SET ruecksendung=1 WHERE id=$id";
				if ($this->sql_insert_update_delete($sqlupdate) == true) {
					echo "<p class='erfolg'>OK</p>";
				} else {
					echo "<p class='meldung'>ERROR</p>";
				}
				
			}
			
			#Rücksendung wird auf 0 gesetzt
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
	
}