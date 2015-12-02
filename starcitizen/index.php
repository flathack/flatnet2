<?php echo '<'.'?xml version="1.0" encoding="utf-8"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" id='starcitizen'>
<div id="wrapper">
	<?php # Wrapper start ?>
	<head>
<?php 
#Inclusions:
include '../includes/starcitizen.class.php';

# GW start
$rsi = NEW rsi;

# STELLT DEN HEADER ZUR VERFÜGUNG
$rsi->connectToDB();
$rsi->header();

$rsi->logged_in("redirect", "index.php");
$rsi->userHasRightPruefung("65");

$suche = isset($_GET['suche']) ? $_GET['suche'] : '';
echo $rsi->suche($suche, "gw_chars", "name", "charakter.php?charID");
?>

<title>StarCitizen - Management</title>
	</head>
	<body>
		<div class='mainbodyStarCitizen'>
		
			<h2>Starcitizen-Managementkonsole</h2>
						
			<div class='SCChars'>
				<h3>Joe Sulkatent</h3>
				
				Hangar:
				<a href='?'>Constellation Andromeda</a>
				<a href='?'>RSI Aurora ME</a>
				<a href='?'>Retaliator</a>
			
			</div>
		
		</div>

	</body>
</div>

</html>
