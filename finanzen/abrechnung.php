<!DOCTYPE html>
<html id="finanzen">
<div id="wrapper">
<head>
<?php # Inclusions
include '../includes/finanzen.class.php';
$finanzen = NEW finanzenNEW;
session_start();
echo '<script src="/flatnet2/tools/ckeditor/ckeditor.js"></script>';
echo '<link href="/flatnet2/css/style.css" type="text/css" rel="stylesheet" />';
echo "<script src='//code.jquery.com/jquery-1.10.2.js'></script>";
echo "<script src='//code.jquery.com/ui/1.11.4/jquery-ui.js'></script>";
echo "<script src='/flatnet2/Chart.min.js'></script>";
#$finanzen->header();
#$finanzen->logged_in("redirect", "index.php");
#$finanzen->userHasRightPruefung("17");
#$suche = isset($_GET['suche']) ? $_GET['suche'] : '';
#echo $finanzen->finanzSuche($suche);
?>
<?php 
if(isset($_GET['month'])) { 
    $monat = $_GET['month']; 
} else {
    $monat = "";
}
?>
<title>Abrechnung <?php echo $monat; ?> </title>
	</head>
	<body>
		<div class='mainbodyDark'>

<?php

if(isset($_GET['month']) AND isset($_GET['konto'])) {
    $monat = $_GET['month'];
    $konto = $_GET['konto'];
    if(is_numeric($monat) AND is_numeric($konto)) {
        $finanzen->mainAbrechnung($monat, $konto);
    }
   
}
?>
			
		</div>
	</body>
</div>
</html>