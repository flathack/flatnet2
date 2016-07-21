<?php echo '<'.'?xml version="1.0" encoding="utf-8"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" id='finanzen'>
<div id="wrapper">
	<?php # Wrapper start ?>
	<head>
<?php 
# Inclusions
include '../includes/finanzen.class.php';

# Eröffnet die Class datenbanken
$finanzen = NEW finanzen;

# STELLT DEN HEADER ZUR VERFÜGUNG
$finanzen->connectToDB();
$finanzen->header();

$finanzen->logged_in("redirect", "index.php");
$finanzen->pruefung("8192");

$suche = isset($_GET['suche']) ? $_GET['suche'] : '';
echo $finanzen->suche($suche, "adressbuch", "nachname", "eintrag.php?bearbeiten");

?>
<title>Steven.NET Finanzen: Fahrten</title>
	</head>

	<body>
		<div class='mainbody'>
		
		<div class='rightOuterBody'>
			<ul>
			<li><a href='index.php' >Konten</a></li>
			<li><a href='fahrten.php' >Fahrten</a></li>
			</ul>
		</div>
				
		<h2>Fahrten</h2>
		
<?php 
# Zeigt die Finanzen an.
$finanzen->fahrten();
?>
		</div>

	</body>
</div>
</html>
