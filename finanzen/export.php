<!DOCTYPE html>
<html id="finanzen">
<div id="wrapper">
<head>
<?php # Inclusions
include '../includes/finanzen.class.php';
$finanzen = NEW finanzenNEW;
$finanzen->header();
$finanzen->logged_in("redirect", "index.php");
$finanzen->userHasRightPruefung(17);
$suche = isset($_GET['suche']) ? $_GET['suche'] : '';
echo $finanzen->suche($suche, "adressbuch", "nachname", "eintrag.php?bearbeiten");
?>
<title>Steven.NET Finanzen</title>
	</head>
	<body>
		
		<div class='mainbodyDark' id="konten">
				
			<h2>Export to CSV</h2>
<?php 

$finanzen->export();

?>
			
		</div>
		
	</body>
</div>
</html>