<?php echo '<'.'?xml version="1.0" encoding="utf-8"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" id='ANPASSEN'>
<div id="wrapper">
	<?php # Wrapper start ?>
	<head>
<?php
header('Content-Type: text/html; charset=UTF-8');
?>
<?php 
#Inclusions: Beispiel an Fahrten.class.php
include '../includes/fahrten.class.php';

#Forum Function
$ANPASSEN = NEW ANPASSEN;

# STELLT DEN HEADER ZUR VERFÜGUNG
$ANPASSEN->header();

# Wohin soll redirected werden
$ANPASSEN->logged_in("redirect", "index.php");

# ID des Rechtes, damit ein User die Seite ansurfen darf.
$ANPASSEN->userHasRightPruefung();

# Sucheinstellungen
$suche = isset($_GET['suche']) ? $_GET['suche'] : '';
# In welcher Tabelle, Spalte soll gesucht werden, dann ?edit Was soll als Link eingefügt werden
echo $fahrten->suche($suche, "fahrkosten", "datum", "?edit");

?>
<title>Steven.NET - Fahrkosten</title>
	</head>
	<body>
		<div class='mainbodyDark'>
			<div class="navigationReiter">
				
			</div>
			
			
			
			<div class='rightBody'>
			
				<?php $fahrten->showStatistik(); ?>
				
			</div>
			
			<div class='innerBody'>
				
			</div>
			
		</div>
	</body>
</div>
</html>
