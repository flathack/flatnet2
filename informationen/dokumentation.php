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
			<h2>Programmierung</h2>
			
				<p>Das Kapitel Programmierung befasst sich mit der Erstellung neuer Abschnitte, 
				Aufteilung des Projekts und andere wichtige Informationen, zum programmieren dieser Webseite.</p>
				
					<h3>Hinzuf&uuml;gen einer neuen Sektion innerhalb der Seite</h3>
					
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
						
			<h2>Benutzerverwaltung</h2>
				<p></p>
			
		
	</div>

</body>
</div>
</html>