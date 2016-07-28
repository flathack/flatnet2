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
include '../includes/monitor.class.php';

#Forum Function
$fahrten = NEW monitor;

# STELLT DEN HEADER ZUR VERFÜGUNG
$fahrten->header();

$fahrten->logged_in("redirect", "index.php");

$fahrten->userHasRightPruefung("11");

$suche = isset($_GET['suche']) ? $_GET['suche'] : '';
echo $fahrten->suche($suche, "fahrkosten", "datum", "?edit");
?>
<title>Steven.NET - Spritmonitor</title>
	</head>
	<body>
		<div class='mainbodyDark'>
		
			<div class="navigationReiter">
				<ul>
					<li><a href="index.php" class='' >Fahrkosten</a></li>
				</ul>
			</div>
			
			<div class='rightBody'>
							
			</div>
			
			<div class='innerBody'>
			
			<?php $fahrten->showSpritmonitor(); ?>
				
			</div>
			
		</div>
	</body>
</div>
</html>
