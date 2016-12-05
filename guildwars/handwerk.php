<!DOCTYPE html>
<html id="guildwars">
<div id="wrapper">
<head>
<?php #Inclusions:
include '../includes/gw.class.php';
$guildwars = NEW gw_handwerk;
$guildwars->header();
$guildwars->logged_in("redirect", "index.php");
$guildwars->userHasRightPruefung(3);
$suche = isset($_GET['suche']) ? $_GET['suche'] : '';
echo $guildwars->suche($suche, "gw_chars", "name", "charakter.php?charID");
?>
<title>Handwerk-Guides</title>
</head>
<body>
		<div class="mainbodyDark">
				<a href="start.php" class="highlightedLink">Zurück</a>
				<div id='handwerk'>
				<?php $guildwars->SubNav("guildwars"); ?>
				</div>
				<h2>HandwerkGuides</h2>
				
				<p>Hier werden alle deine Charakter und deren Berufe angezeigt.</p>
				
				<div class="hilfe">
					<h2>Hilfe</h2>
					<p class="gruppierung">
						Schritt 1: <br><a href='#handwerksmats' class='buttonlink'>Handwerksmaterialien eingeben.</a><br>
						<p class='dezentInfo'>Es sollten mindestens die gewöhnlichen und die edlen Materialien eingegeben werden. 
						Für den Koch und Juwelier sind zudem die Kochzutaten und die Juwelen sinnvoll.</p>
					</p>
					<p class="gruppierung">
						Schritt 2: <br><a href='#einkaufslisten' class='buttonlink'>Handwerksberuf auswählen und Materialien einkaufen</a>
					</p>
					<p class="gruppierung">
						Schritt 3: <a href='#guides' class='buttonlink'>Externen Guide öffnen und Anleitung befolgen</a>
					</p>

					<div class="account">
						<h2><a name='account'>Account</a></h2>
                        
						<?php $_SESSION['selectGWUser'] = $_SESSION['username'];
						$guildwars->selectAccount(); ?>
                        
					</div>
			</div>
			<div class='innerBody'>
					<div class="bereichlederer">
					<a href='http://gw.gameplorer.de/guides/lederer-leveling-guide/' target='_blank' class='rightLink'>Guide öffnen</a>
						<h3>
							<a name='guides' href="http://gw.gameplorer.de/guides/lederer-leveling-guide/" target='_blank' >Lederer</a>
						</h3>
						<?php echo $guildwars->charInBeruf("Lederer"); ?>
					</div>

					<div class="bereichschneider">
					<a href='http://gw.gameplorer.de/guides/schneider-leveling-guide/' class='rightLink' target='_blank' >Guide öffnen</a>
					<h3>
						<a href="http://gw.gameplorer.de/guides/schneider-leveling-guide/" target='_blank' >Schneider</a>
					</h3>
					<?php echo $guildwars->charInBeruf("Schneider"); ?>
					</div>

					<div class="bereichkonstrukteur">
					<a href='http://gw.gameplorer.de/guides/konstrukteur-leveling-guide/' class='rightLink' target='_blank' >Guide öffnen</a>
					<h3>
						<a
							href="http://gw.gameplorer.de/guides/konstrukteur-leveling-guide/" target='_blank' >Konstrukteur</a>
					</h3>
					<?php echo $guildwars->charInBeruf("Konstrukteur"); ?>
					</div>

					<div class="bereichkoch">
					<a href='http://gw.gameplorer.de/guides/koch-leveling-guide/' class='rightLink' target='_blank' >Guide öffnen</a>
					<h3>
						<a href="http://gw.gameplorer.de/guides/koch-leveling-guide/" target='_blank' >Koch</a>
					</h3>
					<?php echo $guildwars->charInBeruf("Koch"); ?>
					</div>

					<div class="bereichwaffenschmied">
					<a href='http://gw.gameplorer.de/guides/waffenschmied-leveling-guide/' class='rightLink' target='_blank' >Guide öffnen</a>
					<h3>
						<a href="http://gw.gameplorer.de/guides/waffenschmied-leveling-guide/" target='_blank' >Waffenschmied</a>
					</h3>
					<?php  echo $guildwars->charInBeruf("Waffenschmied"); ?>
					</div>

					<div class="bereichruestungsschmied">
					<a href='http://gw.gameplorer.de/guides/ruestungsschmied-leveling-guide/' class='rightLink' target='_blank' >Guide öffnen</a>
					<h3>
						<a href="http://gw.gameplorer.de/guides/ruestungsschmied-leveling-guide/" target='_blank' >Rüstungsschmied</a>
					</h3>
					<?php echo $guildwars->charInBeruf("Rüstungsschmied"); ?>
					</div>

					<div class="bereichjuwelier">
					<a href='http://gw.gameplorer.de/guides/juwelier-leveling-guide/' class='rightLink' target='_blank' >Guide öffnen</a>
					<h3>
						<a href="http://gw.gameplorer.de/guides/juwelier-leveling-guide/" target='_blank' >Juwelier</a>
					</h3>
					<?php echo $guildwars->charInBeruf("Juwelier"); ?>
					</div>

					<div class="bereichwaidmann">
					<a href='http://gw.gameplorer.de/guides/waidmann-leveling-guide/' class='rightLink' target='_blank' >Guide öffnen</a>
					<h3>
						<a href="http://gw.gameplorer.de/guides/waidmann-leveling-guide/" target='_blank' >Waidmann</a>
					</h3>
					<?php echo $guildwars->charInBeruf("Waidmann"); ?>
					</div>
			</div>
		
		</div>
		<div class='mainbodyDark'>
			<div class='einkaufslisten'>
				<h2><a name='einkaufslisten'>Einkaufslisten berechnen</a></h2>
				<?php $guildwars->showBerufLinks(); ?>
				<?php $guildwars->getBerufInfo(); ?>
			</div>
		</div>
		
		<div class='mainbodyDark'>
			<?php $guildwars->rankingHandwerk(); ?>
			<h2><a name='handwerksmats'>Deine Handwerksmaterialien</a></h2>
			<p>Gib hier deine Handwerksmaterialien ein. Daraus werden dann deine fehlenden Materialien berechnet. Wenn du nichts eingibst, werden einfach nur die benötigten Materialien für die Handwerksberufe angezeigt.</p>
			<br><br>
			<?php $guildwars->showMatList(); ?>
        </div>
</body>
</html>
