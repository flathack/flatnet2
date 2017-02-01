<?php
/** @historie Steven 17.08.2014 angelegt. */
header('Content-Type: text/html; charset=ISO-8859-1'); 
session_start(); ?>

<?php echo '<'.'?xml version="1.0" encoding="utf-8"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<link href="/flatnet2/css/style.css" type="text/css" rel="stylesheet" />
<link rel="icon" href="/favicon.ico" type="image/x-icon" />
<?php 
# Inclusions
include 'includes/login.class.php';
$indexlogin = NEW login();
?>

<div id="funktion"> 
	<?php # Führt die Anmeldung durch. 
	echo $indexlogin->login_user(); ?> 
</div>

<title>Steven.NET - Login</title>
</head>

<body id='index'>
	<div class='login'>
		<h1><a href='?'>steven.net</a></h1>
		<div id="optionen">
			<?php if(isset($_GET['createUser'])) { 
				echo "<li><a href='?'>Zurück zum Login</a></li>"; 
			} else { 
				echo "<li><a href='?createUser'>Account erstellen</a></li>"; 
			}?>
			<li><a href="#" class="" onclick="document.getElementById('Impressum').style.display = 'block'">Impressum</a></li>
		
			<li><a href="#" class="" onclick="document.getElementById('DatenschutzInfos').style.display = 'block'">Datenschutzinformationen</a></li>
			
			<li><a href="#" class="" onclick="document.getElementById('zweck').style.display = 'block'">Informationen zur Seite</a></li>
		
		</div>
		
		<div id="loginFelder">
			<p id='loginTitel'>Login</p>
			<?php echo $indexlogin->anmeldeCheck(); ?>
		</div>
		
		<div class="logininfos">
			<div style="display: none;" id="DatenschutzInfos">
				<a href="?" class="rightRedLink">OK</a>
				<?php include 'informationen/datenschutz.html'; ?>
			</div>
			
			<div style="display: none;" id="Impressum">
				<a href="?" class="rightRedLink">OK</a>
				<?php include 'informationen/impressum.html'; ?>
			</div>
			
			<div style="display: none;" id="zweck">
				<a href="?" class="rightRedLink">OK</a>
				<?php include 'informationen/zweck.html'; ?>
			</div>
		</div>
		
		
		<div id="register">
			<?php $indexlogin->registerNewUser(); ?>
		</div>
		
	</div>
</body>
</html>
