<?php 
/**
 * @author Steven Sch�del
 * Flatnet2 Projekt
 */
?>
<?php echo '<'.'?xml version="1.0" encoding="utf-8"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" id='informationen'>
<div id="wrapper">
	<?php # Wrapper start ?>
	<head>
<?php 
#Inclusions:
include '../includes/objekt/functions.class.php';

# STELLT DEN HEADER ZUR VERF�GUNG
$quellen = NEW functions;
$quellen->header();
?>
<title>Quellen</title>
	</head>
	<body>
		<div class='mainbody'>
			<div class="rightOuterBody">
				<div id='quellen'>
				<?php $quellen->SubNav("informationen"); ?>
				</div>
			</div>
			<h2>Quellen</h2>

			<p>Dieses Projekt wird vollst�ndig in PHP programmiert. Diese Seite ist im Rahmen meines Informatikstudiums entstanden 
			und wird stetig weiterentwickelt. Sie dient lediglich meinem Zeitvertreib. 
			</p>
			<p>F�r die Erstellung wurde kein fertiges CMS System verwendet. Alle Codebestandteile wurden von mir h�ndisch mit Hilfe
			der Entwicklungsumgebung von Eclipse geschrieben. Der Verzicht auf die "klickiBunti" Programme gibt mir die M�glichkeit
			jede Funktion zu entwickeln, welche ich m�chte.</p>
			<p>
			Ich habe keinen code <b>geklaut oder kopiert</b>, dass liegt aber auch unter anderem daran,
			dass die meisten L�sungen im Netz teilweise schlecht programmiert oder unbrauchbar f�r meine Seite sind. 
			</p>
			<p>F�r dieses Projekt habe ich, also unter vorbehalt, dass diese Liste vollst�ndig ist, folgende Quellen verwendet:</p>
			<br>
			<table class='flatnetTable'>
			<thead>
			<td>Seiten</td>
			</thead>
			<tbody><td id='text'><a href="http://ckeditor.com/">CKEditor f�r das Forum und f�r alle m�glichen Textfelder.</a></td></tbody>
			<tbody><td id='text'><a href="http://www.webmasterpro.de/coding/article/php-ein-einfaches-flexibles-rechtesystem.html">Rechtesystem</a></td></tbody>
			<tbody><td id='text'><a href="http://www.php-einfach.de/tuts_php.php">Tutorials</a></td></tbody>
			<tbody><td id='text'><a href="http://www.guildwars2.com/">Guildwars2.com f�r die Bilder und Screenshots.</a></td></tbody>
			<tbody><td id='text'><a href="http://www.google.de/">Eclipse zum programmieren</a></td></tbody>
			<tbody><td id='text'><a href="http://www.muster-vorlagen.net/impressum-generator.html">Impressumsgenerator</a></td></tbody>
			<tbody><td id='text'><a href="http://www.webmasterpro.de/coding/article/sichere-php-web-applikationen-schreiben-ein-ueberblick.html">PHP Sicherheit</a></td></tbody>
			<tbody><td id='text'><a href="http://de.selfhtml.org/css/index.htm">SELF HTML</a></td></tbody>
			</table>
		</div>

	</body>
</div>
</html>

