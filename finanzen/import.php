<?php echo '<'.'?xml version="1.0" encoding="utf-8"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" id='finanzen'>
<div id="wrapper">
	<?php # Wrapper start ?>
	<head>
<?php 
# Inclusions
include '../includes/finanzen.class.php';

# Eröffnet die Class datenbanken
$finanzen = NEW finanzenNEW;

# STELLT DEN HEADER ZUR VERFÜGUNG
$finanzen->header();

$finanzen->logged_in("redirect", "index.php");
$finanzen->userHasRightPruefung("17");

$suche = isset($_GET['suche']) ? $_GET['suche'] : '';
echo $finanzen->finanzSuche($suche);

?>
<title>Steven.NET Finanzen Import</title>
	</head>
	<body>
		<div class='mainbodyDark'>
		
			<?php $finanzen->showNavigation(); ?>
					
			<h2>Finanzverwaltung</h2>

			<?php # Zeigt die Finanzen an.
			$finanzen->mainImportFunction();	?>
			
		</div>
	</body>
</div>
</html>
