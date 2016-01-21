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
		$row = $this->getObjektInfo($selectCategories);
		$userID = $this->getUserID($_SESSION['username']);
		for ($i = 0 ; $i < sizeof($row) ; $i++) {
			
			# Prüfen ob der Benutzer die Rechte hat, das aktuelle Forum zu betrachten.
			$benutzerliste="SELECT id, Name, forumRights FROM benutzer WHERE id = '$userID' LIMIT 1";
			
			$row2 = $this->getObjektInfo($benutzerliste);
			
			for ($j = 0 ; $j < sizeof($row2) ; $j++) {
				# Check
				if($this->check($row[$i]->rightWert, $row2[$j]->forumRights) == true) {
					$kategorie = $row[$i]->id;
					$query = "SELECT count(*) as anzahl FROM blogtexte WHERE kategorie = $kategorie";
					$anzahlPosts = $this->getObjektInfo($query);
					$anzahlPosts = $anzahlPosts[0]->anzahl;
					# Wenn der Check bestanden ist, dann anzeigen.
					echo "<tbody><td><a href='?blogcategory=".$row[$i]->id."'><strong>".$row[$i]->kategorie."</strong></a>
					<br>".$row[$i]->beschreibung."</td><td>$anzahlPosts</td></tbody>";
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
			$row = $this->getObjektInfo($selectCategories);
			
			for ($i = 0 ; $i < sizeof($row) ; $i++) {
				# Prüfen ob der Benutzer die Rechte hat, das aktuelle Forum zu betrachten.
				$userID = $this->getUserID($_SESSION['username']);
				$benutzerliste="SELECT id, Name, forumRights FROM benutzer WHERE id = '$userID' LIMIT 1";
				$row2 = $this->getObjektInfo($benutzerliste);
				for ($j = 0 ; $j < sizeof($row2) ; $j++) {
					# Check
					if($row2[$j]->forumRights == 0) {
						$benutzerrechteVorher = 1;
					} else {
						$benutzerrechteVorher = $row2[$j]->forumRights;
					} 
					if($this->check($row[$i]->rightWert, $benutzerrechteVorher) == false) {
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
			
			$row = $this->getObjektInfo($selectBlogEintraege);
			
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
			for ($i = 0 ; $i < sizeof($row) ; $i++) {
				
				$lockedInfo = $this->getObjektInfo("SELECT * FROM blogtexte WHERE id = '" . $row[$i]->id . "'");
				if(!isset($lockedInfo[0]->locked)) {
					$locked = "";
				} else if ($lockedInfo[0]->locked == 1)  {
					$locked = "&#128274;";
				} else {
					$locked = "";
				}
				
				# Benutzer finden:
				$autor = $this->getUserName($row[$i]->autor);
				
				if($row[$i]->status == 1 OR $_SESSION['username'] == $autor OR $this->userHasRight("10", 0) == true) {
					
					# Anzahl der Kommentare herausfinden
					$countList = "SELECT COUNT(blogid) as anzahl FROM blog_kommentare WHERE blogid = '".$row[$i]->id."'";
					$menge = $this->getObjektInfo($countList);
					$menge = $menge[0]->anzahl;
					
					echo "<tbody>
					<td><a href='/flatnet2/blog/blogentry.php?showblogid=".$row[$i]->id."&ursprungKategorie=".$row[$i]->kategorie."'>".$row[$i]->titel."</a></td>
					<td><a href='/flatnet2/usermanager/usermanager.php'>$autor</a></td>
					<td> $menge </td>";
					echo "<td>" . $row[$i]->tag . "." . $row[$i]->monat . "." . $row[$i]->jahr . " " . $row[$i]->stunde . ":" . $row[$i]->minute . " Uhr" . "</td>";
					if($row[$i]->status == 1) {
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