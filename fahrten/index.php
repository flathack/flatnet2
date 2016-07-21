<?php echo '<'.'?xml version="1.0" encoding="utf-8"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" id='fahrten'>
<div id="wrapper">
	<?php # Wrapper start ?>
	<head>
<?php
header('Content-Type: text/html; charset=UTF-8');
?>
<?php 
#Inclusions:
include '../includes/fahrten.class.php';

#Forum Function
$fahrten = NEW fahrten;

# STELLT DEN HEADER ZUR VERFÜGUNG
$fahrten->header();

$fahrten->logged_in("redirect", "index.php");

$fahrten->userHasRightPruefung("11");

$suche = isset($_GET['suche']) ? $_GET['suche'] : '';
echo $fahrten->suche($suche, "fahrkosten", "datum", "?edit");
?>
<title>Steven.NET - Fahrkosten</title>
	</head>
	<body>
		<div class='mainbodyDark'>
		
			<a href="#" class='buttonlink' onclick="document.getElementById('neueFahrt').style.display = 'block'"> Neue Fahrt</a>
			<a href="#" class='buttonlink' onclick="document.getElementById('neuesFahrzeug').style.display = 'block'"> Neues Fahrzeug</a>
			<a href="#" class='buttonlink' onclick="document.getElementById('neuesZiel').style.display = 'block'"> Neues Ziel</a>
			<a href="#" class='buttonlink' onclick="document.getElementById('listFahrzeuge').style.display = 'block'"> Fahrzeuge anzeigen</a>
			<a href="#" class='buttonlink' onclick="document.getElementById('listZiele').style.display = 'block'"> Ziele anzeigen</a>
			
			<div class='rightBody'>
			
				<?php $fahrten->showStatistik(); ?>
				
			</div>
			
			<div class='innerBody'>
				<?php $fahrten->newFahrt(); ?>
				<?php $fahrten->newFahrzeug(); ?>
				<?php $fahrten->newZiel(); ?>
				<?php $fahrten->listFahrzeuge(); ?>
				<?php $fahrten->listZiele(); ?>
				<?php $fahrten->alterFahrt(); ?>
				<?php $fahrten->alterFahrzeug(); ?>
				<?php $fahrten->alterZiel(); ?>
				<?php $fahrten->showFahrten(); ?>
				
			</div>
			
		</div>
	</body>
</div>
</html>
