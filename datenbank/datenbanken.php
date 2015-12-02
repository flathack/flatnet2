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
$datenbank->userHasRightPruefung("7");

if($datenbank->userHasRight("14", 0) == false) {
	header("Location: kalender.php");
}

$suche = isset($_GET['suche']) ? $_GET['suche'] : '';
echo $datenbank->suche($suche, "adressbuch", "nachname", "eintrag.php?bearbeiten");

?>
<title>Steven.NET Adressbuch</title>
	</head>

	<body>
		<div class='mainbodyDark'>
		
			<?php # hinzufügen neuer Einträge
			if($datenbank->userHasRight("14", 0) == true) {
				echo $datenbank->UserErstellen();
			} ?>
		
			<div id='right'>	
			<?php # hinzufügen neuer Einträge
			if($datenbank->userHasRight("14", 0) == true) {
				echo "	<a href='?eintragenja=1' class='buttonlink'>Neuen Eintrag</a>";
			}
			?>
				
				<a href='kalender.php' class='buttonlink'>Geburtstagskalender</a>	
			</div>
			
		</div>

		<div class='adressbuchbody'>
			<?php 
			# zeigt alle DB Einträge im Adressbuch an.
			if($datenbank->userHasRight("13", 0) == true) {
				echo $datenbank->datenbankListAllerEintraege("SELECT * FROM adressbuch ORDER BY nachname");
			}
			?>
		</div>
	</body>
</div>
</html>
