<?php echo '<'.'?xml version="1.0" encoding="utf-8"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" id='gesundheit'>
<div id="wrapper">
	<?php # Wrapper start ?>
	<head>
<?php
header('Content-Type: text/html; charset=UTF-8');
?>
<?php 
#Inclusions:
include '../includes/gesundheit.class.php';

#Forum Function
$gesundheit = NEW gesundheit;

# STELLT DEN HEADER ZUR VERFÜGUNG
$gesundheit->header();

$gesundheit->logged_in("redirect", "index.php");

$gesundheit->userHasRightPruefung("74");

$suche = isset($_GET['suche']) ? $_GET['suche'] : '';
echo $gesundheit->suche($suche, "fahrkosten", "datum", "?edit");
?>
<title>Steven.NET - Gesundheit</title>
	</head>
	<body>
		<div class='mainbodyDark'>
			
			<h2>Willkommen im Gesundheitsbereich</h2>
			
			<?php $gesundheit->mainGesundheitFunction(); ?>

		</div>
	</body>
</div>
</html>
