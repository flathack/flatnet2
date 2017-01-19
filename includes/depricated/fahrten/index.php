<!DOCTYPE html>
<html id="fahrten">
<div id="wrapper">
<head>
<?php #Inclusions:
include '../includes/fahrten.class.php';
$fahrten = NEW fahrten;
$fahrten->header();
$fahrten->logged_in("redirect", "index.php");
$fahrten->userHasRightPruefung(11);

$suche = isset($_GET['suche']) ? $_GET['suche'] : '';
echo $fahrten->suche($suche, "fahrkosten", "datum", "?edit");
?>
<title>Steven.NET - Fahrkosten</title>
	</head>
	<body>
		<div class='mainbodyDark'>
			<div class="navigationReiter">
				<ul>
					<li><a href="#" class='' onclick="document.getElementById('neuesFahrzeug').style.display = 'block'">Fahrzeug erstellen</a></li>
					<li><a href="#" class='' onclick="document.getElementById('neuesZiel').style.display = 'block'">Ziel erstellen</a></li>
					<li><a href="#" class='' onclick="document.getElementById('listFahrzeuge').style.display = 'block'">Fahrzeuge anzeigen</a></li>
					<li><a href="#" class='' onclick="document.getElementById('listZiele').style.display = 'block'">Ziele anzeigen</a></li>
					<li><a href="monitor.php" class='' >Spritmonitor</a></li>
				</ul>
			</div>
            
			<div class='rightBody'>
				<?php $fahrten->showStatistik(); ?>
			</div>
			
			<div class='innerBody'>
				<?php $fahrten->listFahrzeuge(); ?>
				<?php $fahrten->listZiele(); ?>
				<?php $fahrten->alterFahrt(); ?>
				<?php $fahrten->alterFahrzeug(); ?>
				<?php $fahrten->alterZiel(); ?>
				<?php $fahrten->newFahrzeug(); ?>
				<?php $fahrten->newZiel(); ?>
				<?php $fahrten->newFahrt(); ?>
				<?php $fahrten->showFahrten(); ?>
			</div>
		</div>
	</body>
</div>
</html>
