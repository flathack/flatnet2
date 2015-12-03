<?php
/**
 * @historie Steven 17.08.2014 angelegt.
 * Dies ist ein Projekt, welches Objektorientiert erstellt werden soll.
 */
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
$indexlogin->connectToDB();

?>
<title>Steven.NET - Login</title>
</head>

<body id='index'>
	<div class='login'>
		<h1>STEVEN.NET</h1>
		
		<div id="loginFelder">
		<?php # Zeigt die Logineingabefelder an.
		echo $indexlogin->anmeldeCheck(); ?>
		</div>
		
		<div id="register">
		<?php $indexlogin->registerNewUser(); ?>
		<a href='?createUser' class='highlightedLink'>Todestempler-Mitglieder bitte Registrieren</a>
		</div>
		
		<div id="funktion"> <?php # F�hrt die Anmeldung durch. 
		echo $indexlogin->login_user(); ?> </div>
		<p class="infoText">
			<br><br>
			";
		</p>
		
		<?php if(isset($_GET['impressum'])) { echo "<p class='meldung'>Es ist ein tempor�rer Fehler aufgetreten, das tut uns leid, bitte versuchen Sie es sp�ter erneut.</p>"; }?>
		
		
	</div>
</body>
</html>
