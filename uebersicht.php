<?php echo '<'.'?xml version="1.0" encoding="utf-8"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" id='uebersicht'>
<div id="wrapper">
	<?php # Wrapper start ?>
	<head>
		<?php 
		include 'includes/uebersicht.class.php';
		
		# new function
		$uebersicht = NEW uebersicht;
		
		# STELLT DEN HEADER ZUR VERFÜGUNG
		$uebersicht->header();
		
		$uebersicht->logged_in("redirect", "index.php");
		$uebersicht->userHasRightPruefung("7");
			
		#
		# ANZAHL DER TILES
		#
		$anzahlanTiles = 3;
		
		?>
		
		<title>Steven.NET - Home</title>

	</head>
	<body>
	
		<div class='mainbody'>
		
			<?php $uebersicht->mainUebersichtFunction(); ?>
			
			<?php 
			# Leerzeichen einfügen:
			for ($i = 0; $i < $anzahlanTiles; $i++) {
				echo "<br><br><br><br><br><br><br><br>";
			}
			?>
		</div>

	</body>
</div>
</html>
