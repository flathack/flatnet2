<?php echo '<'.'?xml version="1.0" encoding="utf-8"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" id='inventar'>
<div id="wrapper">
<head>
<?php
header('Content-Type: text/html; charset=UTF-8');
?>
<?php 
#Inclusions:
include '../../includes/inventar.class.php';

# Inventar öffnen
$inventar = NEW inventar;
# STELLT DEN HEADER ZUR VERFÜGUNG
$inventar->connectToDB();
$inventar->header();

$inventar->logged_in("redirect", "index.php");
$inventar->pruefung("256");


?>
<title>Inventarliste</title>
</head>
<body>
	<div class='mainbody'>		
		<?php 
		$action = isset($_GET['action']) ? $_GET['action'] : 'Speichern';
		$id = isset($_GET['id']) ? $_GET['id'] : '';
		if(isset($_GET['id']) AND isset($_GET['action'])) {
			echo "<div class='mainbody'>";
			echo $inventar->newInventar($action, $id);
			echo "</div>";
		}
		?>
	

</body>
</div>
</html>