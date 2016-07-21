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
 * Erm�glicht das speichern von Eintr�gen der Doku innerhalb der Datenbank.
 * @author Steven Sch�del
 * @history 03.02.2015 angelegt.
 * @history 10.02.2015 �berarbeitung auf Objektorientierung.
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
	 * Zeigt das Eingabefeld f�r neuen Dokueintrag
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
	 * L�scht einen Docueintrag.
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

# STELLT DEN HEADER ZUR VERF�GUNG
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
				<h2>Ank�ndigungen</h2>
				<?php $docu->showDocu(); ?>

			</div>

			<br> <br> <br> <a href="?showolddocu">alte Doku</a>
			<?php 
			if(isset($_GET['showolddocu'])) {
				echo "
				<div>
				<ul>
				<li>xx.08.2014: Kategorie kann jetzt im Blog ver�ndert werden</li>
				<li>xx.08.2014: Blog: Eintr�ge der verschiedenen Benutzer k�nnen
				gefiltert werden</li>
				<li>xx.08.2014: Seitlich k�nnen 'andere Beitr�ge' angesehen werden.
				Die Ausgabe wurde begrenzt.</li>
				<li>xx.08.2014: Zus�tzliche Liste mit allen Beitr�gen zu
				Verwaltungszwecken erstellt. Bei zu vielen Eintr�gen werden
				zus�tzliche Seiten erstellt.</li>
				<li>xx.08.2014: Textfelder werden durch Javascript durch einen
				einfachen Editor ausgetauscht. (Opensource)</li>
				<li>02.09.2014: Im Blog werden bei zu vielen Eintr�gen mehrere
				Seiten angezeigt.</li>
				<li>03.09.2014: Alle Felder f�r die einzelnen Charakter wurden
				hinzugef�gt.</li>
				<li>03.09.2014: Charakter im Guildwars-Bereich k�nnen jetzt
				bearbeitet und gel�scht werden.</li>
				<li>03.09.2014: Design Anpassungen.</li>
				<li>05.09.2014: Diese Neuerungen ben�tigen die aktualisierten
				Tabellen: vorschlaege, benutzer: rights hinzugef�gt, salt, email
				entfernt</li>
				<li>05.09.2014: Vorschlag System integriert. Admin kann den Status
				jedes Vorschlags �ndern oder den Vorschlag l�schen. Css ebenfalls
				erstellt. Vorschl�ge k�nnen von jedem 'registriertem' User
				eingereicht werden.</li>
				<li>05.09.2014: Ein Rechtesystem wurde erstellt, welches mit dem
				Exponenten und der Potenz von 2 arbeitet.</li>
				<li>06.09.2014: Bei den Guildwars Charakteren wird der n�chste
				Geburtstag in Tagen angezeigt.</li>
				<li>08.09.2014: Das Design der Guildwars Kacheln wurde verbessert.
				Jetzt wird der Klasse entsprechend der Hintergrund ausgetauscht
				und Farben angepasst.</li>
				<li>08.08.2014: Ein Bereich f�r Guildwars Tiere wurde erstellt.
				Dort k�nnen die Tiere, die in GW verteilt sind gesammelt werden.</li>
				<li>09.09.2014: Adressbuch �berarbeitet und mit neuen Funktionen
				versehen, z. B. wird ein Eintrag jetzt auf einer anderen Seite
				ge�ffnet und kann dort direkt bearbeitet werden. ShowPerson f�llt
				weg und ist nur noch zus�tzlich verf�gbar.</li>

				<h3>10.09.2014:</h3>
				<li>Fehler in Guildwars Charakter gefixt, ein Beruf fehlte.
				Administration: Infotext hinzugef�gt.</li>
				<li>Neue Startseite erstellt, mit verschiedenen Kacheln.
				Verschiedene Infos werden direkt in die Kacheln eingebunden.</li>
				<li>Usermanager wieder aktiviert. <strong>Zuk�nftig</strong> kann
				der User hier sein Passwort �ndern.
				</li>
				<li>Favicon erstellt</li>
				<li>Design der Bereichs-Kacheln angepasst. CSS.</li>
				<li>Einen Wrapper f�r jede Seite hinzugef�gt. Diese zentriert die
				Seite, nicht wie vorher der 'mainbody'.</li>

				<h3>11.09.2014</h3>
				<li>E-Mail Class erstellt und den dazugeh�rigen Bereich unter den
				Benutzereinstellungen</li>
				<li>Benutzerprofil Einstellungen hinzugef�gt, sowie eine separaten
				f�r die Emails (funktioniert aber noch nicht)</li>
				<li>Bilder f�r die Homeseite hinzugef�gt.</li>
				<li>Sleep.php unter Datenbanken hinzugef�gt.</li>

				<h3>12.09.2014</h3>
				<li>Blog: Neuer Beitrag jetzt auf einer separaten Seite.</li>
				<li>Blog: Jeder Benutzer darf jetzt einen Blog schreiben, ohne die
				des anderen Users zu sehen</li>
				<li>Blog: Positionen verschiedener Buttons angepasst und das Design
				leicht ver�ndert.</li>
				<li>Blog: An verschiedenen Stellen den RightsCheck eingebaut um
				ungewollte �nderungen an der DB zu verhindern.</li>
				<lI>Blog: Abstand der Spalten in der Blogliste angepasst</lI>
				<li>Blog: Es ist nicht mehr notwendig den Button OK zu dr�cken,
				wenn die Ansicht gewechselt werden soll.</li>
				<li>Administration: RightCheck eingebaut.</li>
				<li><strong>Sicherheitsfeature</strong>: Administration: Bei
				dreimaligem falschen eingeben vom Passwort, wird der Benutzer
				gesperrt.</li>
				<li>Administration: Das Loginscript �berarbeitet. Sollte nun
				sicherer sein.</li>

				<h3>13.09.2014</h3>
				<li>Benutzer-Profile erstellt. Zwar mit wenig Informationen, aber
				das Grundger�st steht. �ber die richtigen Links zum anzeigen des
				Profils gesetzt.</li>
					
				<h3>15.09.2014</h3>
				<li>Kommentarsystem f�r den Blog erstellt, mit dem Autor als Fremdschl�ssel.</li>
				<li>Blogs k�nnen jetzt f�r andere Benutzer freigegeben werden.</li>
					
				<h3>16.09.2014</h3>
				<li>Inventarsystem erstellt.</li>
				<li>Suche optimiert.</li>
				<li>Rechtesystem optimiert.</li>
					
				<h3>18.09.2014</h3>
				<li>Die Suche kann jetzt allgemein erfolgen. Es wird in allen Spalten nach dem Suchbegriff gesucht.</li>
				<li>Inventar: Kategorieauswahl hinzugef�gt.</li>
				<li>Suche: Suche jetzt verallgemeinert, und eine neue Class angelegt (functions) f�r allgemeine Funktionen.</li>
				<li>Header ver�ndert</li>
				<li>Einige Funktionen benutzen kein Echo mehr </li>
				<li>In der Navigation wird die aktive Seite hervorgehoben</li>
					
				<h3>19.09.2014</h3>
				<li>Code umgeschrieben, sodass keine Ausgabe innerhalb der Funktionen mehr erfolgt. Ausgabe stattdessen �ber RETURN $ausgabe;</li>
					
				<h3>22.09.2014</h3>
				<li>Rechteproblem behoben, Adressbuch jetzt separat geregelt.</li>
					
				<h3>26.09.2014</h3>
				<li>Design �berarbeitet. Adressbuch bearbeiten Design ge�ndert, entspricht jetzt dem des Erstellens.</li>
				<h3>02.02.2015</h3>
				<li>Design korrekturen angewendet.</li>
				<li>Rechtesystem weiter perfektioniert (grafische Oberfl�che im Adminmen�).</li>

				</ul>
				</div>

				<div class='blogeintrag'>
				<h2>Bekannte Fehler</h2>

				<ul>
				<li>Bei den GW Kacheln wird ein Notice-Fehler angezeigt, kein
				richtiger Fehler, aber unsch�n.</li>
				</ul>
				</div>

				<div class='blogeintrag'>
				<h2>Fehlende Features</h2>
				<ul>
				<li>Bilderupload bei Guildwars Charakteren, mit der M�glichkeit,
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
