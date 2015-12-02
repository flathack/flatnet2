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
#Inclusions:
include '../includes/datenbanken.class.php';

# Eröffnet die Class datenbanken
$datenbank = NEW datenbanken;

# STELLT DEN HEADER ZUR VERFÜGUNG
$datenbank->connectToDB();
$datenbank->header();

if($datenbank->userHasRight("14", 0) == false) {
	header("Location: kalender.php");
}

$datenbank->logged_in("redirect", "index.php");
$datenbank->userHasRightPruefung("13");

$suche = isset($_GET['suche']) ? $_GET['suche'] : '';
echo $datenbank->suche($suche, "adressbuch", "nachname", "?bearbeiten");
?>
<title>Stevens Blog - Eintrag</title>
	</head>
	<body>
		<div class='mainbodyDark'>
			<a href="/flatnet2/datenbank/datenbanken.php" class="buttonlink">&#8634; zur Übersicht</a>
			<?php 
			# bearbeiten von Datensätzen
			$datenbank->UserBearbeiten();
			$datenbank->UserLoeschen();
			?>
			<a href="/flatnet2/datenbank/datenbanken.php" class="buttonlink">&#8634; zur Übersicht</a>
		</div>
	</body>
</div>
</html>
