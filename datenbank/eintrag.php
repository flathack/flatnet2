<!DOCTYPE html>
<html id="adressbuch">
<div id="wrapper">
<head>
<?php 
#Inclusions:
include '../includes/datenbanken.class.php';
$datenbank = NEW datenbanken;
$datenbank->header();
if($datenbank->userHasRight(14, 0) == false) {
	header("Location: kalender.php");
}
$datenbank->logged_in("redirect", "index.php");
$datenbank->userHasRightPruefung(13);
$suche = isset($_GET['suche']) ? $_GET['suche'] : '';
echo $datenbank->suche($suche, "adressbuch", "nachname", "?bearbeiten");
?>
<title>Stevens Blog - Eintrag</title>
</head>
	<body>
		<div class='mainbodyDark'>
			<a href="/flatnet2/datenbank/datenbanken.php" class="buttonlink">&#8634; zur Übersicht</a>
			<?php 
			# bearbeiten von Datensätzen
			$datenbank->UserBearbeiten();
			$datenbank->UserLoeschen();
			?>
			<a href="/flatnet2/datenbank/datenbanken.php" class="buttonlink">&#8634; zur Übersicht</a>
		</div>
	</body>
</div>
</html>
