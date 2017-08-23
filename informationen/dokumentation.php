<!DOCTYPE html>
<html>

<div id="wrapper">
<head>
<meta charset="UTF-8">
<link href="/flatnet2/css/style.css" type="text/css" rel="stylesheet" />
<script src="/flatnet2/tools/ckeditor/ckeditor.js"></script>
<title>Dokumenation</title>
</head>

<body>
<?php $counter = 1; ?>
	<div class='newFahrt'>
		<nav class='header'>
			<ul id='navigation'>
				<li><a href="/flatnet2/index.php">Startseite</a></li>
				<li><a href="/flatnet2/uebersicht.php">&Uuml;bersichtsseite</a></li>
			</ul>
		</nav>
		<h1>Dokumentation</h1>
		<p>Die Dokumentation beschreibt alle Funktionen und Methoden des Projekts 
		Flatnet2, programmiert von Steven S. im Zeitraum 2013 bis 2016.</p>
		<div class="publicInfo">
			<h1><?php echo $counter; $counter++; ?> Programmierung</h1>
			<button  onclick="document.getElementById('addpage').style.display = 'block'">anzeigen</button>
			<button  onclick="document.getElementById('addpage').style.display = 'none'">verstecken</button>
				<p class="einleitung"></p>
					
					<div id="addpage" style="display:none;">
						<h3>1.1 Hinzuf&uuml;gen einer neuen Sektion innerhalb der Seite</h3>
						<p>Das Kapitel Programmierung befasst sich mit der Erstellung neuer Abschnitte, 
						Aufteilung des Projekts und andere wichtige Informationen, zum programmieren dieser Webseite.</p>
					
						<p>Um eine neue Sektion der Seite hinzuzuf&uuml;gen muss folgendes gemacht werden:</p>
						
						<h4>Verzeichnisstruktur</h4>
						<ul>
						<li>Ordner in Verzeichnisstruktur erstellen, z. B. Amazon</li>
						<li>In diesem Ordner eine index.php erstellen und mit einem Standard-Layout 
						versehen (includes/standardindex.php). </li>
						<li>Eine Class-Datei erstellen im Ordner "include", z. B. amazon.class.php. Hier die class amazon erstellen, diese wird als child von functions deklariert (extends functions). Dazu noch ein include 'objekt/functions.class.php' und das wars.</li>
						</ul>
						<h4>Rechte</h4>
						<ul>
						<li>Entweder&uuml;ber PHPMYADMIN oder die Objekte-Verwaltung eine neue Kategorie f&uuml;r den neuen Bereich erstellen.</li>
						<li>Recht zum ansehen der Seite erstellen (und ggf. weitere n&ouml;tige Rechte)</li>
						<li>Sicherstellen, dass der Administrator alle Rechte zum ansehen und verwalten der Seite hat.</li>
						</ul>
						<h4>CSS Anpassungen</h4>
						<ul>
						<li>in der CSS m&uuml;ssen verscheidene Anpassungen gemacht werden: Einmal ab ca. 
						Zeile 250 die Anpassung an "#uebersicht #uebersicht" (muss dem Seitenheader der Standardindex.php entsprechen</li>
						<li>Dann in der CSS ab Zeile 350 folgende Zeilen hinzuf&uuml;gen .bereichfahrten h2, 
						.bereichfahrten p,</li>
						<li>In der CSS eine eigene .bereich<strong>fahrten</strong> (beispiel) erstellen. 
						Jetzt ist die CSS Anpassung abgeschlossen</li>
						</ul>
						<h4>Datenbank</h4>
						<ul>
						<li>Tabelle erstellen, wenn n&ouml;tig</li>
						<li>in der includes/control.class.php in Zeile 369 in der Funktion 
						<strong>function setBesitzerArray()</strong> die besitzer Spalte hinzuf&uuml;gen (falls vorhanden). 
						Dies ist dann der Fall, wenn ein Benutzer eigene Eintr&auml;ge hinzuf&uuml;gen kann, z. B. neue Guildwars Charakter. Diese k&ouml;nnen
						dem Benutzer direkt zugeordnet werden!
						Dies ist wichtig, damit die Eintr&auml;ge nachher von jedem Benutzer wieder vom Adminpanel gel&ouml;scht werden k&ouml;nnen.</li>
						</ul>
						<h4>&Uuml;bersichtsseite</h4>
						<ul>
						<li>Auf der&uuml;bersichtsseite eine Kachel f&uuml;r den Bereich hinzuf&uuml;gen. 
						Hier ist die CSS Klasse des Bereichs sowie die Rechte ID auszuw&auml;hlen.</li>
						</ul>
					 </div>
					 
			</div>	
			<div class="publicInfo">
			<h1><?php echo $counter; $counter++; ?> Administration</h1>
				<p class="einleitung">Der Administrationsbereich stellt alle administativen Funktionen der Seite zur Verf&uuml;gung.</p>
				
				<button  onclick="document.getElementById('administration').style.display = 'block'">anzeigen</button>
				<button  onclick="document.getElementById('administration').style.display = 'none'">verstecken</button>
				<div id="administration" style="display:none;">

				<h3>2.1 Benutzerverwaltung</h3>
					<p>Die Benutzerverwaltung erm&ouml;glicht das erstellen, bearbeiten und konfigurieren der Benutzer der Seite.</p>
				<h3>2.2 Logs</h3>
					<p>Auf der Unterseite Logs sind verschiedene Log-Eintr&auml;ge zu sehen. Unterschieden wird hier zwischen einer SQL-Query Log und einer vorschlaege log. Die Vorschlaege
					k&ouml;nnen von den Benutzern der Seite an den Administrator gesendet werden. Das Vorschl&auml;ge-Log ist sozusagen, der Helpdesk 
					dieser Webseite.</p>
					<p>Da drunter ist eine Tabelle, in welcher angezeigt wird, wann sich welcher Benutzer das letzte mal eingeloggt hat. Dies wird nach Benutzer gruppiert. Es wird also immer
					der letzte Login angezeigt. Auf der Datenbank sind aber alle Logins zu erkennen.</p>
					<p>Dann gibt es noch ein SQL-Log. Hier werden alle inserts,updates,deletes der Seite gespeichert. Unterschieden wird hier
					zwischen den verschiedenen Benutzern. Sortierung erfolgt nach Datum und Uhrzeit.</p>
				<h3>2.3 Objekte</h3>
					<p>Die Seite Objekte zeigt eine Miniaturversion der phpmyadmin Seite an. Hier k&ouml;nnen einzelne Eintr&auml;ge der Datenbank angezeigt,
					bearbeitet oder gel&ouml;scht werden.  </p>
				<h3>2.4 Forum</h3>
					<p>Die Forumadministration erm&ouml;glicht die Erstellung und Bearbeitung von KAtegorien f&uuml;r die Forum-Sektion
					der Webseite. Auch Rechte k&ouml;nnen f&uuml;r jede Sektion an die Benutzer verteilt werden. Dabei wird 
					das 2er Potenz Rechtesystem verwendet. Es ist daher wichtig, dass der vorgeschlagene Potenzwert
					beibehalten wird. Sonst kann es zu Dopplungen innerhalb des Rechtesystems kommen.</p>
				<h3>2.5 Rechteverwaltung</h3>
					<p>Die Rechteverwaltung erm&ouml;glicht es, den Benutzern zugriff auf verschiedene Bereiche der Webseite
					zu geben.</p>
				<h3>2.6 Ank&uuml;ndigungen</h3>
					<p>Der Bereich Ank&uuml;ndigungen erm&ouml;glicht die Erstellung von Ank&uuml;ndigungen f&uuml;r den Header-Bereich der Webseite.</p>
				</div>
				
			</div>
			<div class="publicInfo">
			<h1><?php echo $counter; $counter++; ?> Adressbuch</h1>
				<p class="einleitung">Das Adressbuch ist eine einfache Datenbank zur Speicherung von Adressdaten</p>
				<button  onclick="document.getElementById('adressbuch').style.display = 'block'">anzeigen</button>
				<button  onclick="document.getElementById('adressbuch').style.display = 'none'">verstecken</button>
				<p id="adressbuch" style="display:none;">Anhand der Adressdaten wird auch ein Geburtstagskalender angezeigt.</p>
				
			</div>
			<div class="publicInfo">
			<h1><?php echo $counter; $counter++; ?> Forum</h1>
				<p class="einleitung">Hilfe f&uuml;r das Forum</p>
				<button  onclick="document.getElementById('forum').style.display = 'block'">anzeigen</button>
				<button  onclick="document.getElementById('forum').style.display = 'none'">verstecken</button>
				<p id="forum" style="display:none;">Das Forum ist eine einfache M&ouml;glichkeit sich mit anderen Personen auszutauschen. Grundfunktionalit&auml;ten
				sind hierf&uuml;r gegeben. F&uuml;r fast alle Funktionen sind eigene Rechtebereiche eingerichtet und erm&ouml;glicht so,
				theoretisch, eine gro�e Anzahl von Benutzern.</p>
				
			</div>
			<div class="publicInfo">
			<h1><a name="finanzen"><?php echo $counter; $counter++; ?> Finanzverwaltung</a></h1>
				<p class="einleitung">Die Finanzverwaltung erm&ouml;glicht eine &uuml;bersichtliche Darstellung seiner eigenen Konten und der zuk&uuml;nftigen
				Finanzplanung. Das Grundprinzip ist die Schaffung einer Soll und Haben Seite eines jeden Kontos.</p>
				<button  onclick="document.getElementById('finanzen').style.display = 'block'">anzeigen</button>
				<button  onclick="document.getElementById('finanzen').style.display = 'none'">verstecken</button>
				<div id="finanzen" style="display:none;">
				
				<h2>Startseite</h2>
				
				<p>Die Startseite zeigt eine &Uuml;bersicht aller aktiven Konten an. Bei einem Klick auf ein aktives Konto, werden
				die Ums&auml;tze des aktuellen Monats im aktuellen Jahr angezeigt. &Uuml;ber die Navigation kann die Ansicht in
				auf Monate ver&auml;ndert werden. Zudem kann von jeder Position aus ein anderes Konto aufgerufen werden. Der ausgew&auml;hlte
				Monat und das Jahr bleiben erhalten.</p>
				
				<h2>Konten</h2>
				<p></p>
				
				<h2>Shares</h2>
				
				<h2>Erstellen / Bearbeiten / L&ouml;schen</h2>
				
				
				</div>
				
			</div>
			<div class="publicInfo">
			<h1><?php echo $counter; $counter++; ?> Guildwars</h1>
			<p class="einleitung">Der Guildwars-Bereich erm&ouml;glicht eine Ansicht und Auswertung seines Guildwars-Accounts</p>
				<button  onclick="document.getElementById('guildwars').style.display = 'block'">anzeigen</button>
				<button  onclick="document.getElementById('guildwars').style.display = 'none'">verstecken</button>
				<div id="guildwars" style="display:none;">
				
				<p>Der Guildwars-Bereich erm&ouml;glicht eine Ansicht und Auswertung seiner eigenen Charakter. Die Hauptfunktion
				ist allerdings die Eingabe von Daten in das Handwerksfeld. Hier k&ouml;nnen die bestehenden Daten dazu genutzt werden
				ben&ouml;tigte Rohstoffe, f&uuml;r das erlernen der Handwerksberufe, zu sehen.</p>
				
				</div>
			
			</div>
			<div class="publicInfo">	
			<h1><?php echo $counter; $counter++; ?> Benutzerprofil</h1>
			<p class="einleitung">Das Benutzerprofil erm&ouml;glicht das bearbeiten seines eigenen Accounts.</p>
			<button  onclick="document.getElementById('profil').style.display = 'block'">anzeigen</button>
			<button  onclick="document.getElementById('profil').style.display = 'none'">verstecken</button>
				<div id="profil" style="display:none;">
					<h2>Passwort &auml;ndern</h2>
					<p>Um ein Passwort zu ändern, klicken Sie rechts oben auf Ihren Namen. Im oberen Bereich der Webseite klicken Sie nun auf
						"Benutzereinstellungen". Hier können Sie Ihr Passwort &auml;ndern.
					</p>
				</div>
				
		
	</div>

</body>
</div>
</html>