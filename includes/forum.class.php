<?php
/**
 * Dient der Ausgabe und der Funktionen von dem Forum
 */
include 'objekt/functions.class.php';

class forum extends functions {
	
	
	
	/**
	 * AB HIER WIRD DER BLOG BEREICH DARGESTELLT
	 */
	function showBlogCategories() {
		# BLOG DARSTELLEN
		echo "<table class='forum'>";
		echo "<thead><td>Steven.NET Forum</td><td>Themen</td></thead>";
		$selectCategories = "SELECT * FROM blogkategorien ORDER BY sortierung, kategorie ASC";
		$ergebnis = mysql_query($selectCategories);
		$userID = $this->getUserID($_SESSION['username']);
		while($row = mysql_fetch_object($ergebnis)) {
			
			# Prüfen ob der Benutzer die Rechte hat, das aktuelle Forum zu betrachten.
			$benutzerliste="SELECT id, Name, forumRights FROM benutzer WHERE id = '$userID' LIMIT 1";
			$ergebnis2 = mysql_query($benutzerliste);
			
			while($row2 = mysql_fetch_object($ergebnis2)) {
				# Check
				if($this->check($row->rightWert, $row2->forumRights) == true) {
					$kategorie = $row->id;
					$anzahlPosts = $this->getAmount("SELECT id FROM blogtexte WHERE kategorie = $kategorie");
					# Wenn der Check bestanden ist, dann anzeigen.
					echo "<tbody><td><a href='?blogcategory=$row->id'><strong>$row->kategorie</strong></a>
					<br>$row->beschreibung</td><td>$anzahlPosts</td></tbody>";
				}
			}
		}
		
		echo "</table>";
	}
	
	function showBlogPosts() {
		if(isset($_GET['blogcategory'])) {
			
			$kategorie = $_GET['blogcategory'];
			
			# Check ob der Benutzer das aktuelle Forum anzeigen darf.
			$selectCategories = "SELECT * FROM blogkategorien WHERE id = '$kategorie'";
			$ergebnis = mysql_query($selectCategories);
			while($row = mysql_fetch_object($ergebnis)) {
				# Prüfen ob der Benutzer die Rechte hat, das aktuelle Forum zu betrachten.
				$userID = $this->getUserID($_SESSION['username']);
				$benutzerliste="SELECT id, Name, forumRights FROM benutzer WHERE id = '$userID' LIMIT 1";
				$ergebnis2 = mysql_query($benutzerliste);
				while($row2 = mysql_fetch_object($ergebnis2)) {
					# Check
					if($row2->forumRights == 0) {
						$benutzerrechteVorher = 1;
					} else {
						$benutzerrechteVorher =$row2->forumRights;
					} 
					if($this->check($row->rightWert, $benutzerrechteVorher) == false) {
						echo "<p class='meldung'>Du darfst dir dieses Forum nicht ansehen.</p>";
						exit;
					}
				}
			}
			
			# Fortfahren mit Thread Anzeige.
			
			
			$selectBlogEintraege = "SELECT * 
			, year(timestamp) AS jahr
			, month(timestamp) as monat
			, day(timestamp) as tag
			, hour(timestamp) AS stunde
			, minute(timestamp) AS minute
			FROM blogtexte 
			WHERE kategorie = '$kategorie' 
			ORDER BY timestamp DESC";
			$ergebnis = mysql_query($selectBlogEintraege);
			
			echo "<a href='?' class='highlightedLink'>Zurück</a> ";
			if(isset($_GET['blogcategory'])) {
				$category = $_GET['blogcategory'];
			}
			echo "<a href='/flatnet2/blog/addBlogEntry.php?blogcategory=$category' class='greenLink'>Neues Thema</a>";
			
			echo "<table class='forum'>";
			echo "
				<thead>
					<td id='titel'>Titel</td>
					<td>Autor</td>
					<td>Antworten</td>
					<td id='datum'>Erstelldatum</td>
					<td></td>
					
				</thead>";
			while($row = mysql_fetch_object($ergebnis)) {
				
				$lockedInfo = $this->getObjektInfo("SELECT * FROM blogtexte WHERE id = '" . $row->id . "'");
				if(!isset($lockedInfo->locked)) {
					$locked = "";
				} else if ($lockedInfo->locked == 1)  {
					$locked = "&#128274;";
				} else {
					$locked = "";
				}
				
				# Benutzer finden:
				$autor = $this->getUserName($row->autor);
				
				if($row->status == 1 OR $_SESSION['username'] == $autor OR $this->userHasRight("10", 0) == true) {
					
					# Anzahl der Kommentare herausfinden
					$countList = "SELECT COUNT(blogid) FROM blog_kommentare WHERE blogid = '$row->id'";
					$countErgebList = mysql_query($countList);
					$menge = mysql_fetch_row($countErgebList);
					$menge = $menge[0];
					
					echo "<tbody>
					<td><a href='/flatnet2/blog/blogentry.php?showblogid=$row->id&ursprungKategorie=$row->kategorie'>$row->titel</a></td>
					<td><a href='/flatnet2/usermanager/usermanager.php'>$autor</a></td>
					<td> $menge </td>";
					echo "<td>" . $row->tag . "." . $row->monat . "." . $row->jahr . " " . $row->stunde . ":" . $row->minute . " Uhr" . "</td>";
					if($row->status == 1) {
						$offen = "&#10004;";
					} else {
						$offen = "&#10008;";
					}
					echo "<td>$locked $offen</td>";
					echo "</tbody>";
				}
			}
			echo "</table>";
		}
	}

}

?>