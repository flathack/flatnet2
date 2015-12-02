<?php echo '<'.'?xml version="1.0" encoding="utf-8"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" id='uebersicht'>
<div id="wrapper">
	<?php # Wrapper start ?>
	<head>
		<?php 
		include 'includes/objekt/functions.class.php';
		
		# new function
		$uebersicht = NEW functions();
		
		# STELLT DEN HEADER ZUR VERFÜGUNG
		$uebersicht->connectToDB();
		$uebersicht->header();
		
		$uebersicht->logged_in("redirect", "index.php");
		$uebersicht->userHasRightPruefung("7");
		
		#
		# ANZAHL DER TILES
		#
		$anzahlanTiles = 3;
		
		?>
		
		<title>Steven.NET - Home</title>

	</head>
	<body>
	
		<div class='mainbody'>

					
			<?php 
			if($uebersicht->userHasRight("13", 0) == true) { 
				echo "<div class='bereiche'>";
				echo "<div id='adressbuch'>";
				echo "<a href='datenbank/datenbanken.php'><h2>Adressbuch</h2>";
				echo "<p id='limited'>Ein einfachen Adressbuch und Kalender</p> </a>";
				echo "<p>";
				
				# SQL Abfrage für die Anzahl der Einträge
				$menge = $uebersicht->getAmount("SELECT id FROM adressbuch");
				
				# SQL Abfrage für den Vornamen und Nachnamen
				$row = $uebersicht->getObjektInfo("SELECT id, vorname, nachname FROM adressbuch ORDER by TIMESTAMP DESC LIMIT 1");
				echo "<p id='limited'>Anzahl: " . $menge . " Einträge <br>";
				echo "Letzter Eintrag:<br>" . " " . $row->vorname . " " . $row->nachname . "</p>";
				echo "</p>";
				echo "</div>";
				echo "</div>";
			} 
			?>
			
			<?php 
			if($uebersicht->userHasRight("2", 0) == true) { 
				
				# Menge aller Einträge
				$menge = $uebersicht->getAmount("SELECT id FROM blogtexte");
				
				# Menge aller Einträge (KOMMENTARE)
				$menge3 = $uebersicht->getAmount("SELECT id FROM blog_kommentare");
				
				# Menge eigener
				$userid = $uebersicht->getUserID($_SESSION['username']);
				$menge2 = $uebersicht->getAmount("SELECT id FROM blogtexte WHERE autor = '$userid'");
				$menge4 = $uebersicht->getAmount("SELECT id FROM blog_kommentare WHERE autor = '$userid'");

				echo "<div class='bereiche'>";
				echo "<div id='forum'>";
				echo "<a href='forum/index.php'><h2>Forum</h2></a>";
			#	echo "Ein kleines Forum";
				echo "<p class='bereichLink'>";
				$alle = $menge + $menge3;
				$eigene = $menge2 + $menge4;
				echo "<a href='forum/index.php'>Du hast " . $eigene . " Beiträge und es gibt " . $alle . " Beiträge im Forum</a>";
				echo "</p>";
				echo "</div>";
				echo "</div>";
			} 
			?>
			
			<?php 
			if($uebersicht->userHasRight("3", 0) == true) { 
			echo "<div class='bereiche'>";
			echo "<div id='guildwars'>";
			echo "<a href='guildwars/start.php'><h2>Guildwars</h2></a>";
			echo "<p>";
						
			$username = $uebersicht->getUserID($_SESSION['username']);
			$menge = $uebersicht->getAmount("SELECT id, besitzer FROM gw_chars WHERE besitzer = '$username'");
			echo "<a href='guildwars/start.php'>Du hast " . $menge . " Charakter </a>";
						
			echo "</p>";
			echo "<a class='lowerTabs' href='guildwars/handwerk.php'>Handwerk</a><a class='lowerTabs' href='http://gw2cartographers.com/'>Karte</a>";
			echo "</div>";
			echo "</div>";
			}
			?>
			
			<?php /*
			if($uebersicht->check("128", 0) == true) {
			echo "<div class='bereiche'>";
			echo "	<div id='inventar'>";
			echo "		<a href='datenbank/inventar/inventar.php'><h2>Inventar</h2>";
			echo "			<p>Eine Inventarverwaltung</p> </a>";
			echo "		<p>";
							
							$userID = $uebersicht->getUserID($_SESSION['username']);
							$menge = $uebersicht->getAmount("SELECT id FROM inventar WHERE ersteller = '$userID'");
							
							# Zeigt die Anzahl der Einträge nur dann, wenn der Benutzer auch Einträge anlegen darf.
							
							if($uebersicht->check("128", 0) == "true") {
								echo "<a href='datenbank/inventar/inventar.php' class='bereichLink'>Du hast " . $menge . " Einträge.</a>";
							}
						
			echo "		</p>";
			echo "	</div>";
			echo "</div>";
			
			} */
			?>
			<?php 
			if($uebersicht->userHasRight("7", 0) == true) {
			echo "<div class='bereiche'>";
			echo "	<div id='profil'>";
			echo "		<a href='usermanager/usermanager.php'><h2>Profil</h2>";
			echo "			<p>";
			echo "				Willkommen <strong>" . $_SESSION['username'] . "," . "</strong>";
			echo "			</p>";
			echo "			<p>Dein Profil</p></a>";
			echo "<a class='bereichLink' href='usermanager/usermanager.php?passChange'>Passwort ändern</a><br>
				<a class='bereichLink' href='usermanager/usermanager.php?passChange'>Guildwars-Accounts Verwalten</a>";
			echo "	</div>";
			echo "</div>";
			
			} 
			?>
			
			<?php /*
			if($uebersicht->check("64", 0) == true) { 
			echo "<div class='bereiche'>";
			echo "	<div id='dokumentation'>";
			echo "		<a href='informationen/hilfe.php'><h2>Ankündigungen</h2> ";
						echo "<p>Ankündigungen zu dieser Seite.</p>";
						echo "<a href='informationen/kontakt.php' class='info'>Vorschlag an Steven senden</a>";
			echo "		 </a>";
			echo "	</div>";
			echo "</div>";
			
			} */
			?>
			
			<?php 
			if($uebersicht->userHasRight("36", 0) == true) {
			echo "<div class='bereiche'>";
			echo "<div id='administration'>";
			echo "<a href='admin/control.php'><h2>Administration</h2>";
			echo "<p>Seitenverwaltung</p> </a>";
			echo "<p>";
						
			$menge = $uebersicht->getAmount("SELECT id, status FROM vorschlaege WHERE status = 'offen'");
			$menge2 = $uebersicht->getAmount("SELECT versuche FROM benutzer WHERE versuche = '3'");
			$menge3 = $uebersicht->getAmount("SELECT id, status FROM vorschlaege WHERE status = 'illegal'");
							
			if ($menge3 > 0) {
				echo "<p class='meldung'>Achtung: " . $menge3 . " illegale Aktivitäten</p>";
			} else if($menge > 0 OR $menge2 > 0) {
				echo "<p class='bereichLink'>TODO: " . $menge . " Einträge</p>";
				echo "<p class='bereichLink'>Gesperrt: " . $menge2 . " Benutzer</p>";
			}
						
			echo "</p>";
			echo "</div>";
			echo "</div>"; 
			} ?>
			
			<?php 
			if($uebersicht->userHasRight("17", 0) == true) { 
				echo "<div class='bereiche'>";
				echo "<div id='finanzen'>";
				echo "<a href='finanzen/index.php'><h2>Finanzbereich</h2>";
				echo "<p>Bietet eine Finanzverwaltung zum planen von Ausgaben und Einnahmen.</p> </a>";
				echo "</div>";
				echo "</div>";
			} 
			?>
			
			<?php 
			
			
			if($uebersicht->userHasRight("11", 0) == true) { 
				$userID = $uebersicht->getUserID($_SESSION['username']);
				$heutigerEintrag = $uebersicht->getAmount("SELECT id FROM fahrkosten WHERE besitzer = '$userID' AND timestamp > curdate()");
				$infosZurAktuellenFahrt = $uebersicht->getObjectsToArray("SELECT * FROM fahrkosten WHERE besitzer = '$userID' AND timestamp > curdate()");
				echo "<div class='bereiche'>";
				echo "<div id='fahrten'>";
				echo "<a href='fahrten/index.php'><h2>Fahrkosten</h2>";
				echo "<p>Fahrkostenverwaltung</p> </a>";
				if($heutigerEintrag > 0) {
					echo "<p class='bereichLink'>Du hast heute bereits $heutigerEintrag x gebucht, 
					du bist mit " . $infosZurAktuellenFahrt[0]->fahrart . " nach " . $infosZurAktuellenFahrt[0]->ziel . " gefahren</p>";
				} else {
					echo "<p class='hinweis'><a href='fahrten/index.php'>Du bist heute nirgend wo hingefahren.</a>";
				}
				
				echo "</div>";
				echo "</div>";
			} 
			?>
			
			<?php 
			
			if($uebersicht->userHasRight("70", 0) == true) { 
				$userID = $uebersicht->getUserID($_SESSION['username']);
				echo "<div class='bereiche'>";
				echo "<div id='lernen'>";
				echo "<a href='lernen/index.php'><h2>Lernbereich</h2>";
				echo "<p>Lernbereich</p> </a>";
				echo "</div>";
				echo "</div>";
			} 
			?>
			
			<?php 		
			/*
			if($uebersicht->check("8", 0) == true) { 
				echo "<div class='bereiche'>";
				echo "<div id='starcitizen'>";
				echo "<a href='starcitizen/index.php'><h2>Starcitizen</h2>";
				echo "<p>Management von Starcitizen</p> </a>";
								
				echo "</div>";
				echo "</div>";
			} */
			?>
			
			<?php 
			# Leerzeichen einfügen:
			for ($i = 0; $i < $anzahlanTiles; $i++) {
				echo "<br><br><br><br><br><br><br><br>";
			}
			?>
		</div>

	</body>
</div>
</html>
