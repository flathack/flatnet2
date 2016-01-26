<?php echo '<'.'?xml version="1.0" encoding="utf-8"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" id='learn'>
<div id="wrapper">
	<?php # Wrapper start ?>
	<head>
<?php
header('Content-Type: text/html; charset=UTF-8');
?>
<?php 
#Inclusions:
include '../includes/learn.class.php';

#Forum Function
$learn = NEW learn;

# STELLT DEN HEADER ZUR VERFÜGUNG
$learn->header();

$learn->logged_in("redirect", "index.php");

$learn->userHasRightPruefung("70");

$suche = isset($_GET['suche']) ? $_GET['suche'] : '';
echo $learn->suche($suche, "learnlernkarte", "frage", "?frage");
?>
<title>Steven.NET - Export</title>
	</head>
	<body>
		<div class='mainbodyDark'>
		<a href='index.php'>Zurück</a>
			<?php $learn->export(); ?>
		</div>
	</body>
</div>
</html>
