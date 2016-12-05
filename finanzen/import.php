<!DOCTYPE html>
<html id='finanzen'>
<div id="wrapper">
<head>
<?php # Inclusions
include '../includes/finanzen.class.php';
$finanzen = NEW finanzenNEW;
$finanzen->header();
$finanzen->logged_in("redirect", "index.php");
$finanzen->userHasRightPruefung("17");
$suche = isset($_GET['suche']) ? $_GET['suche'] : '';
echo $finanzen->finanzSuche($suche);
?>
<title>Steven.NET Finanzen Import</title>
	</head>
	<body>
		<div class='mainbodyDark'>
		
			<?php $finanzen->showNavigation(); ?>
					
			<h2>Finanzverwaltung</h2>

			<?php # Zeigt die Finanzen an.
			$finanzen->mainImportFunction();	?>
			
		</div>
	</body>
</div>
</html>