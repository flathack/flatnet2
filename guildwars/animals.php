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
$guildwars = NEW gw_animals;

# STELLT DEN HEADER ZUR VERFÜGUNG
$guildwars->connectToDB();
$guildwars->header();

$guildwars->logged_in("redirect", "index.php");
$guildwars->userHasRightPruefung("9");

$suche = isset($_GET['suche']) ? $_GET['suche'] : '';
echo $guildwars->suche($suche, "gw_animals", "tierName", "?");
?>
<title>Tierstandorte</title>
	</head>
	<body>
		<div class='mainbodyDark'>
		<div id='tiere'>
		<?php $guildwars->SubNav("guildwars"); ?>
		</div>
				<a href="start.php" class="highlightedLink">Zurück</a>
				<?php 
				echo $guildwars->nav_functions("animals");
				?>
			
				<?php  
				# neues Tier:
				echo $guildwars->newAnimal();
				?>
				<p>Bitte die Tiere hier eintragen wenn du sie gefunden hast!</p>
				<p>
					<a href="http://wiki-de.guildwars2.com/wiki/Kategorie:Tiergef%C3%A4hrte">
						Guildwiki Eintrag aller Tiere
					</a>
				</p>
				
				<h2>Liste aller Tiere</h2>

				<?php 
				# Tiere bearbeiten:
				$tierID = (isset($_GET['bearb'])) ? $_GET['bearb'] : '';
				echo $guildwars->bearbAnimal($tierID);
					
				# Tier löschen
				$loeschID = (isset($_GET['loesch'])) ? $_GET['loesch'] : '';
				echo $guildwars->delAnimal($loeschID);
					
				echo $guildwars->showAnimals();
				?>
		</div>

	</body>
</div>
</html>
