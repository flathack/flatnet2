<?php 
/**
 * @author Steven Schödel
 * Flatnet2 Projekt
 */
?>
<?php echo '<'.'?xml version="1.0" encoding="utf-8"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" id='guildwars'>
<div id="wrapper">
	<?php # Wrapper start ?>
	<head>
<?php 
#Inclusions:
include '../includes/gw.class.php';

# GW start
$guildwars = NEW gw_kalender();

# STELLT DEN HEADER ZUR VERFÜGUNG
$guildwars->header();

$guildwars->logged_in("redirect", "index.php");
$guildwars->userHasRightPruefung("3");

$suche = isset($_GET['suche']) ? $_GET['suche'] : '';
echo $guildwars->suche($suche, "gw_chars", "name", "charakter.php?charID");
?>


<title>GW Kalender</title>
	</head>
	<body>
		<div class='mainbody'>
			<a href="start.php" class="highlightedLink">Zurück</a>
			
			<div id='kalender'>
			<?php $guildwars->SubNav("guildwars"); ?>
			</div>
			
			<h2><a name='gebs'>Geburtstage der Charakter</a></h2>
			
			<?php $guildwars->showMonthGesamt(); ?>
			
			<?php $guildwars->gwCharKalender(); ?>
			
			<div class='innerBody'>
			</br></br></br></br></br></br></br></br></br></br></br> 
			</br></br></br></br></br></br></br></br></br></br></br> 
			</br></br></br></br></br></br></br></br></br></br></br></br></br>
			</br></br></br>
			</div>
		</div>

	</body>
</div>

<script>
var dt = new Date();

//Display the month, day, and year. getMonth() returns a 0-based number.
var month = dt.getMonth()+1;
var day = dt.getDate();
var year = dt.getFullYear();
document.getElementById("datum").innerHTML = year + '-' + month + '-' + day;
</script>
</html>
