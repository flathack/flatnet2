<!DOCTYPE html>
<html id="inventar">
<div id="wrapper">
<head>
<?php #Inclusions:
include '../../includes/inventar.class.php';

# Inventar öffnen
$inventar = NEW inventar;
$inventar->connectToDB();
$inventar->header();
$inventar->logged_in("redirect", "index.php");
$inventar->pruefung(256); ?>
<title>Inventarliste</title>
</head>
<body>
<div class="mainbody">		
	<?php 
	$action = isset($_GET['action']) ? $_GET['action'] : 'Speichern';
	$id = isset($_GET['id']) ? $_GET['id'] : '';
	if(isset($_GET['id']) AND isset($_GET['action'])) {
		echo "<div class='mainbody'>";
		echo $inventar->newInventar($action, $id);
		echo "</div>";
	}
	?>

</div>
</body>
</html>