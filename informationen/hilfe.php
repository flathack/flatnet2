<?php echo '<'.'?xml version="1.0" ?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" id="informationen"
	xml:lang="de" lang="de">

<div id="wrapper">
	<?php # Wrapper start ?>
	<head>

<?php
/**
 * Ermöglicht das speichern von Einträgen der Doku innerhalb der Datenbank.
 * @author Steven Schödel
 * @history 03.02.2015 angelegt.
 * @history 10.02.2015 Überarbeitung auf Objektorientierung.
 *
 */

include '../includes/objekt/functions.class.php';

class docu extends functions {

	/**
	 * Zeigt die Dokumentation, samt Eingabefeld an.
	 */
	function showDocu() {

		# Eingabefeld anzeigen.
		$this->showEingabeFeld();

		# Eintrag in DB speichern.
		if(isset($_POST['docuText'])) {
			$this->setDocuEintrag($_POST['docuText']);
		}

		if(isset($_GET['loeschid'])) {
			$this->deleteDocuEintrag();
		}

		# Ausgabe der Doku aus der Datenbank
		$query = "SELECT *, month(timestamp) AS monat, day(timestamp) AS tag, year(timestamp) AS jahr FROM docu ORDER BY timestamp DESC";	
		$row = $this->getObjektInfo($query);
		
		echo  "<table class='flatnetTable'>";
		echo  "<thead><td id='datum'>Datum</td><td id='text'>Text</td><td></td></thead>";
		for ($i = 0 ; $i < sizeof($row); $i++) {
			echo  "<tbody><td>" . $row[$i]->tag . "." . $row[$i]->monat . "." . $row[$i]->jahr . "</td><td>" . $row[$i]->text . "</td><td>";
			if($this->userHasRight("19", 0) == true) {
				echo "<a href='?loeschen&loeschid=".$row[$i]->id."' class='highlightedLink'>X</a>";
			}
			echo"</td></tbody>";
		}

		echo  "</table>";
	}

	/**
	 * Zeigt das Eingabefeld für neuen Dokueintrag
	 */
	function showEingabeFeld() {
		# Neuer Eintrag in die Doku:
		if($this->userHasRight("19", 0) == true) {
			echo "<form action='hilfe.php' method=post>";
			echo "<textarea name='docuText' class='ckeditor' placeholder='Beschreibung'> </textarea>";
		#	echo "<input type='text' name='docuText' id='titel' value='' placeholder='Beschreibung' />";
			echo "<input type='submit' name='sendDocu' value='Absenden' />";
			echo "</form>";
		}
	}

	/**
	 * Speichert Eintrag in die Dokumentation.
	 * @param unknown $text
	 * @param unknown $autor
	 */
	function setDocuEintrag($text) {
		if($this->userHasRight("19", 0) == true) {
			# Speichert den< Eintrag in die Datenbank.
			if($_POST['docuText'] == "") {
				echo "<p class='meldung'>Feld ist leer.</p>";
			} else {
				$autor = $this->getUserID($_SESSION['username']);
				$insert = "INSERT INTO docu (text, autor) VALUES ('$text','$autor')";
				if($this->sql_insert_update_delete($insert) == true) {
					echo "<p class='erfolg'>Eintrag gespeichert.</p>";
				} else {
					echo "<p class='meldung'>Fehler beim speichern in der Datenbank.</p>";
				}
			}
		}

	}

	/**
	 * Löscht einen Docueintrag.
	 */
	function deleteDocuEintrag() {
		if($this->userHasRight("66", 0) == true) {
			$this->sqlDelete("docu");
		}
	}

}
# ENDE DER KLASSE
?>

<?php 
$docu = NEW docu();

# STELLT DEN HEADER ZUR VERFÜGUNG
$docu->header();
$docu->logged_in();

#Check ob User Seite betrachten darf:
$docu->userHasRightPruefung("8");

$suche = isset($_GET['suche']) ? $_GET['suche'] : '';
echo $docu->suche($suche, "docu", "text", "?");

?>
<title>Dokumentation</title>
	</head>
	<body>

		<div class="mainbody">
			<div class="rightOuterBody">
				<div id='doku'>
					<?php $docu->SubNav("informationen"); ?>
				</div>
			</div>

			<div class="docuEintrag">
				<h2>Ankündigungen</h2>
				<?php $docu->showDocu(); ?>

			</div>

			<br> <br> <br> <a href="?showolddocu">alte Doku</a>
			<?php 
			if(isset($_GET['showolddocu'])) {
				echo "
				<div>
				<ul>
				<li>xx.08.2014: Kategorie kann jetzt im Blog verändert werden</li>
				<li>xx.08.2014: Blog: Einträge der verschiedenen Benutzer können
				gefiltert werden</li>
				<li>xx.08.2014: Seitlich können 'andere Beiträge' angesehen werden.
				Die Ausgabe wurde begrenzt.</li>
				<li>xx.08.2014: Zusätzliche Liste mit allen Beiträgen zu
				Verwaltungszwecken erstellt. Bei zu vielen Einträgen werden
				zusätzliche Seiten erstellt.</li>
				<li>xx.08.2014: Textfelder werden durch Javascript durch einen
				einfachen Editor ausgetauscht. (Opensource)</li>
				<li>02.09.2014: Im Blog werden bei zu vielen Einträgen mehrere
				Seiten angezeigt.</li>
				<li>03.09.2014: Alle Felder für die einzelnen Charakter wurden
				hinzugefügt.</li>
				<li>03.09.2014: Charakter im Guildwars-Bereich können jetzt
				bearbeitet und gelöscht werden.</li>
				<li>03.09.2014: Design Anpassungen.</li>
				<li>05.09.2014: Diese Neuerungen benötigen die aktualisierten
				Tabellen: vorschlaege, benutzer: rights hinzugefügt, salt, email
				entfernt</li>
				<li>05.09.2014: Vorschlag System integriert. Admin kann den Status
				jedes Vorschlags ändern oder den Vorschlag löschen. Css ebenfalls
				erstellt. Vorschläge können von jedem 'registriertem' User
				eingereicht werden.</li>
				<li>05.09.2014: Ein Rechtesystem wurde erstellt, welches mit dem
				Exponenten und der Potenz von 2 arbeitet.</li>
				<li>06.09.2014: Bei den Guildwars Charakteren wird der nächste
				Geburtstag in Tagen angezeigt.</li>
				<li>08.09.2014: Das Design der Guildwars Kacheln wurde verbessert.
				Jetzt wird der Klasse entsprechend der Hintergrund ausgetauscht
				und Farben angepasst.</li>
				<li>08.08.2014: Ein Bereich für Guildwars Tiere wurde erstellt.
				Dort können die Tiere, die in GW verteilt sind gesammelt werden.</li>
				<li>09.09.2014: Adressbuch überarbeitet und mit neuen Funktionen
				versehen, z. B. wird ein Eintrag jetzt auf einer anderen Seite
				geöffnet und kann dort direkt bearbeitet werden. ShowPerson fällt
				weg und ist nur noch zusätzlich verfügbar.</li>

				<h3>10.09.2014:</h3>
				<li>Fehler in Guildwars Charakter gefixt, ein Beruf fehlte.
				Administration: Infotext hinzugefügt.</li>
				<li>Neue Startseite erstellt, mit verschiedenen Kacheln.
				Verschiedene Infos werden direkt in die Kacheln eingebunden.</li>
				<li>Usermanager wieder aktiviert. <strong>Zukünftig</strong> kann
				der User hier sein Passwort ändern.
				</li>
				<li>Favicon erstellt</li>
				<li>Design der Bereichs-Kacheln angepasst. CSS.</li>
				<li>Einen Wrapper für jede Seite hinzugefügt. Diese zentriert die
				Seite, nicht wie vorher der 'mainbody'.</li>

				<h3>11.09.2014</h3>
				<li>E-Mail Class erstellt und den dazugehörigen Bereich unter den
				Benutzereinstellungen</li>
				<li>Benutzerprofil Einstellungen hinzugefügt, sowie eine separaten
				für die Emails (funktioniert aber noch nicht)</li>
				<li>Bilder für die Homeseite hinzugefügt.</li>
				<li>Sleep.php unter Datenbanken hinzugefügt.</li>

				<h3>12.09.2014</h3>
				<li>Blog: Neuer Beitrag jetzt auf einer separaten Seite.</li>
				<li>Blog: Jeder Benutzer darf jetzt einen Blog schreiben, ohne die
				des anderen Users zu sehen</li>
				<li>Blog: Positionen verschiedener Buttons angepasst und das Design
				leicht verändert.</li>
				<li>Blog: An verschiedenen Stellen den RightsCheck eingebaut um
				ungewollte Änderungen an der DB zu verhindern.</li>
				<lI>Blog: Abstand der Spalten in der Blogliste angepasst</lI>
				<li>Blog: Es ist nicht mehr notwendig den Button OK zu drücken,
				wenn die Ansicht gewechselt werden soll.</li>
				<li>Administration: RightCheck eingebaut.</li>
				<li><strong>Sicherheitsfeature</strong>: Administration: Bei
				dreimaligem falschen eingeben vom Passwort, wird der Benutzer
				gesperrt.</li>
				<li>Administration: Das Loginscript überarbeitet. Sollte nun
				sicherer sein.</li>

				<h3>13.09.2014</h3>
				<li>Benutzer-Profile erstellt. Zwar mit wenig Informationen, aber
				das Grundgerüst steht. Über die richtigen Links zum anzeigen des
				Profils gesetzt.</li>
					
				<h3>15.09.2014</h3>
				<li>Kommentarsystem für den Blog erstellt, mit dem Autor als Fremdschlüssel.</li>
				<li>Blogs können jetzt für andere Benutzer freigegeben werden.</li>
					
				<h3>16.09.2014</h3>
				<li>Inventarsystem erstellt.</li>
				<li>Suche optimiert.</li>
				<li>Rechtesystem optimiert.</li>
					
				<h3>18.09.2014</h3>
				<li>Die Suche kann jetzt allgemein erfolgen. Es wird in allen Spalten nach dem Suchbegriff gesucht.</li>
				<li>Inventar: Kategorieauswahl hinzugefügt.</li>
				<li>Suche: Suche jetzt verallgemeinert, und eine neue Class angelegt (functions) für allgemeine Funktionen.</li>
				<li>Header verändert</li>
				<li>Einige Funktionen benutzen kein Echo mehr </li>
				<li>In der Navigation wird die aktive Seite hervorgehoben</li>
					
				<h3>19.09.2014</h3>
				<li>Code umgeschrieben, sodass keine Ausgabe innerhalb der Funktionen mehr erfolgt. Ausgabe stattdessen über RETURN $ausgabe;</li>
					
				<h3>22.09.2014</h3>
				<li>Rechteproblem behoben, Adressbuch jetzt separat geregelt.</li>
					
				<h3>26.09.2014</h3>
				<li>Design überarbeitet. Adressbuch bearbeiten Design geändert, entspricht jetzt dem des Erstellens.</li>
				<h3>02.02.2015</h3>
				<li>Design korrekturen angewendet.</li>
				<li>Rechtesystem weiter perfektioniert (grafische Oberfläche im Adminmenü).</li>

				</ul>
				</div>

				<div class='blogeintrag'>
				<h2>Bekannte Fehler</h2>

				<ul>
				<li>Bei den GW Kacheln wird ein Notice-Fehler angezeigt, kein
				richtiger Fehler, aber unschön.</li>
				</ul>
				</div>

				<div class='blogeintrag'>
				<h2>Fehlende Features</h2>
				<ul>
				<li>Bilderupload bei Guildwars Charakteren, mit der Möglichkeit,
				das Bild zu schneiden</li>
				</ul>
				</div>

				<div class='blogeintrag'>
				<h2>Anstehende Projekte</h2>
				<ul>
				<li>Kostenverwaltung</li>
				<li>Spritmonitor (auf Eis gelegt)</li>
				<li>Notrufsystem</li>
				</ul>
				</div>
				</div>
				";
			}
			?>

		</div>
		<?php # Wrapper Ende ?>
	</body>

</html>
