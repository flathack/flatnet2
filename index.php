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
$indexlogin->connectToDB();

?>
<title>Steven.NET - Login</title>
</head>

<body id='index'>
	<div class='login'>
		<h1>steven.net</h1>
		
		<div id="loginFelder">
		<?php # Zeigt die Logineingabefelder an.
		echo $indexlogin->anmeldeCheck(); ?>
		<?php if(isset($_GET['createUser'])) { echo ""; } else { echo "<a href='?createUser' class='rightBlueLink' >Account erstellen</a>"; }?>
		</div>
		
		<div id="register">
			<?php $indexlogin->registerNewUser(); ?>
			
			
			
		</div>
		
		<div id="funktion"> <?php # F�hrt die Anmeldung durch. 
		echo $indexlogin->login_user(); ?> </div>
				
		<div style="display: none;" id="DatenschutzInfos">
			<h2>Datenschutz</h2>
				<p>
				Wir, (bzw. ich, <strong>Steven Sch�del</strong>, der Autor dieser Seite, 
				nachfolgend als <strong>Anbieter</strong> bezeichnet) 
				nehmen den Schutz Ihrer pers�nlichen Daten sehr ernst 
				und halten uns strikt an die Regeln der Datenschutzgesetze. 
				Personenbezogene Daten werden auf dieser Webseite 
				nur im technisch notwendigen Umfang erhoben. 
				In keinem Fall werden die erhobenen Daten verkauft oder aus anderen 
				Gr�nden an Dritte weitergegeben.
				Die nachfolgende Erkl�rung gibt 
				Ihnen einen �berblick dar�ber,
				wie wir diesen Schutz gew�hrleisten 
				und welche Art von Daten zu welchem 
				Zweck erhoben werden.</p>
				 
				<h2>Datenverarbeitung auf dieser Internetseite</h2>
				
				<p>Der Anbieter erhebt und speichert 
				automatisch in seinen Server Log Files 
				Informationen, die Ihr Browser an uns 
				�bermittelt. Dies sind:</p>
				
				<li>Browsertyp/ -version</li>
				<li>Hostname des zugreifenden Rechners (IP Adresse)</li>
				<li>Uhrzeit der Serveranfrage.</li>
				<p>Diese Daten sind f�r den Anbieter nicht bestimmten 
				Personen zuordenbar. Eine Zusammenf�hrung dieser Daten
				mit anderen Datenquellen wird nicht vorgenommen, die 
				Daten, speziell die IP Adresse dient dem Erkennen
				von Angriffen von au�en. Nach einer Auswertung 
				werden diese Daten aber gel�scht.</p>
				
				<h2>Cookies</h2>
				
				<p>Die Internetseiten verwenden an mehreren 
				Stellen so genannte Cookies. Sie dienen 
				dazu, unser Angebot nutzerfreundlicher, 
				effektiver und sicherer zu machen. Cookies 
				sind kleine Textdateien, die auf Ihrem Rechner 
				abgelegt werden und die Ihr Browser speichert.
				Die meisten der von uns verwendeten Cookies 
				sind so genannte �Session-Cookies�. Sie werden 
				nach Ende Ihres Besuchs automatisch gel�scht. 
				Cookies richten auf Ihrem Rechner keinen Schaden 
				an und enthalten keine Viren.</p>
				
				<h2>Newsletter</h2>
				
				<p>Diese Seite bietet keinen Newsletter an, daher werden
				hier in diesem Umfang keine Daten von Ihnen ben�tigt.</p>
				
				<h2>Auskunftsrecht</h2>
				
				<p>Sie haben jederzeit das Recht auf Auskunft �ber 
				die bez�glich Ihrer Person gespeicherten Daten, 
				deren Herkunft und Empf�nger sowie den Zweck der 
				Speicherung. Auskunft �ber die gespeicherten Daten 
				gibt der Anbieter.</p>
				
				<h2>Weitere Informationen</h2>
				
				<p>Ihr Vertrauen ist uns wichtig. Daher m�chten wir 
				Ihnen jederzeit Rede und Antwort bez�glich der 
				Verarbeitung Ihrer personenbezogenen Daten stehen.
				Wenn Sie Fragen haben, die Ihnen diese Datenschutzerkl�rung 
				nicht beantworten konnte oder wenn Sie zu einem Punkt
				vertiefte Informationen w�nschen, wenden Sie sich 
				bitte jederzeit an den Anbieter. <a href="mailto:todestempler@guildwars.pfweb.eu">Senden Sie mir eine E-Mail.</a> </p>
		</div>
	</div>
</body>
</html>
