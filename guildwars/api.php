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

?>

<?php 
class api extends guildwars {
	
	function apiTest() {
		echo "<h2>Api Test</h2>";
		
		# include_once 'https://api.guildwars2.com/v1/world_names.json';
	}
	
}
?>

<?php 

# GW start
$guildwars = NEW api;

# STELLT DEN HEADER ZUR VERFÜGUNG
$guildwars->connectToDB();
$guildwars->header();

$guildwars->logged_in("redirect", "index.php");
$guildwars->userHasRightPruefung("9");

$suche = isset($_GET['suche']) ? $_GET['suche'] : '';
echo $guildwars->suche($suche, "gw_animals", "tierName", "?");
?>
<title>GW API Test</title>
	</head>
	<body>
		<div class='mainbodyDark'>
		<div id='api'>
		
			<?php $guildwars->SubNav("guildwars"); ?>
		
		</div>
			<a href="start.php" class="highlightedLink">Zurück</a>
				
			<?php $guildwars->apiTest(); ?>
		</div>

	</body>
</div>
</html>
