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
$guildwars = NEW gw_costs;

# STELLT DEN HEADER ZUR VERFÜGUNG
$guildwars->header();

$guildwars->logged_in("redirect", "index.php");
$guildwars->userHasRightPruefung("9");
		?>
		<title>Kosten Calculator</title>
		
	</head>
	<body>
		<div class='mainbodyDark'>
			<a href="start.php" class="highlightedLink">Zurück</a>
			<div id='calc'>
			<?php $guildwars->SubNav("guildwars"); ?>
			</div>
			<h2>Kosten Calculator</h2>
				<?php $guildwars->showAccountInfo(); ?>				
				<?php $guildwars->showCostCalcEntries(); ?>
		</div>
	</body>
</div>
</html>
