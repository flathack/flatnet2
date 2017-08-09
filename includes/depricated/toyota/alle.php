<!DOCTYPE html>
<html id="toyota">
<div id="wrapper">
<head>
<?php # Inclusions
include '../includes/toyota.class.php';
$toyota = NEW toyota;
$toyota->header();
$toyota->logged_in("redirect", "index.php");
$toyota->userHasRightPruefung(81);
$suche = isset($_GET['suche']) ? $_GET['suche'] : '';
echo $toyota->suche($suche);
?>
<title>Steven.NET Finanzen</title>
	</head>
	<body>
		<div class='mainbodyDark' id="">

			<?php $toyota->navigation(); ?>
			<?php $toyota->showMuepTable(); ?>
			
		</div>
	</body>
</div>
</html>