<?php 
/**
 * @author Steven Schödel
 * Flatnet2 Projekt
 */
?>
<?php echo '<'.'?xml version="1.0" encoding="utf-8"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" id='kontakt'>
<div id="wrapper">
	<?php # Wrapper start ?>

	<head>
<?php 
#Inclusions:
include '../includes/control.class.php';

# Admin
$admin = NEW control;

#Start
$kontakt = NEW functions();
# STELLT DEN HEADER ZUR VERFÜGUNG
$kontakt->connectToDB();
$kontakt->header();
$kontakt->logged_in();


#Check ob User Seite betrachten darf:
$kontakt->userHasRightPruefung("7");

?>
<title>Kontakt</title>
	</head>
	<body>
		<div class="mainbody">
			<div class="rightOuterBody">
				<div id='kontakt2'>
				<?php $kontakt->SubNav("informationen"); ?>
				</div>
			</div>

			<div class="docuEintrag">
				

				<h2>Neuigkeiten</h2>
				<p class='info'>Manchmal können Fehler auftreten. Oder du hast einen Vorschlag, dann schreibe mir bitte.</p>
				<form method=post>
					<input type="text" name="vorschlagText" id="titel"
						placeholder="Problembeschreibung" /> <input type="submit"
						name="Absenden" value="SENDEN" />
				</form>
				<?php 			
				if(isset($_POST['vorschlagText'])) {
					if($_POST['vorschlagText'] == "") {
						echo "<p class='meldung'>Feld ist leer.</p>";
					} else {
						$autor = $kontakt->getUserID($_SESSION['username']);
						$text = strip_tags( stripslashes($_POST['vorschlagText']));
		
		
						# Query in die Class schieben:
						if($kontakt->sql_insert_update_delete("INSERT INTO vorschlaege (autor, text, status) VALUES ('$autor','$text','offen')") == "true") {
							echo "<p class='erfolg'>Vorschlag eingereicht</p>";
						} else {
							echo "<p class='info'>Sorry! Gerade ist was kaputt. Der Vorschlag wurde nicht gesendet, aber eingetragen</p>";
						}
					}
				}

			?>
			</div>
			<div class="docuEintrag">
				<?php $admin->userVorschlaege(); ?>
			</div>
		</div>

	</body>
</div>
</html>
