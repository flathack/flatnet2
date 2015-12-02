<?php 
/**
 * @author Steven Schödel
 * Flatnet2 Projekt
 */
?>
<?php echo '<'.'?xml version="1.0" encoding="utf-8"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" id='adressbuch'>
<div id="wrapper">
	<?php # Wrapper start ?>
	<head>
<?php 
# Inclusions
include '../includes/datenbanken.class.php';

# Eröffnet die Class datenbanken
$datenbank = NEW datenbanken;

# STELLT DEN HEADER ZUR VERFÜGUNG
$datenbank->connectToDB();
$datenbank->header();

$datenbank->logged_in("redirect", "index.php");
$datenbank->userHasRightPruefung("22");

$suche = isset($_GET['suche']) ? $_GET['suche'] : '';
echo $datenbank->suche($suche, "adressbuch", "nachname", "eintrag.php?bearbeiten");

?>
<title>Steven.NET Kalender</title>
	</head>

	<body>
		<div class='mainbody'>
		
			<div class='topBody'>
				<a href='datenbanken.php' class='highlightedLink'>Zurück</a>	
				<h2><a name='gebs'>Geburtstagskalender</a></h2>
			</div>

		</div>
		
		<?php $datenbank->showMonthGesamt(); ?>

		<div class='mainBody'>
			
			<?php $datenbank->gebKalender(); ?>
			
		</div>
	</body>
</div>
</html>
