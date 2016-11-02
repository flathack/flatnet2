<?php 
/**
 * @author Steven Schödel
 * Flatnet2 Projekt
 */
?>
<?php echo '<'.'?xml version="1.0" encoding="utf-8"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" id='informationen'>
<div id="wrapper">
	<?php # Wrapper start ?>
	<head>
<?php 
#Inclusions:
#include '../includes/objekt/functions.class.php';

echo '<link href="/flatnet2/css/style.css" type="text/css" rel="stylesheet" />
			<link href="/flatnet2/css/style.css" type="text/css" rel="stylesheet" />';

# STELLT DEN HEADER ZUR VERFÜGUNG
#$impressum = NEW functions;
#$impressum->connectToDB();
#$impressum->header();
?>
<title>Impressum</title>
	</head>
	<body>
		<div class='mainbody'>
			<a class='buttonlink' href='../index.php'>Zur&uuml;ck</a>
			<h2>Disclaimer</h2>
			
			<a href="http://www.disclaimer.de/disclaimer.htm?farbe=FFFFFF/000000/000000/000000"><img src="http://www.disclaimer.de/images/d_aniwhite.gif" 
			width="84" height="20" border=0 alt="disclaimer"></a>
			
			<?php include 'impressum.html';?>
			

		</div>

	</body>
</div>
</html>
