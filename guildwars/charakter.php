<!DOCTYPE html>
<html id="guildwars">
<div id="wrapper">
<head>
<?php #Inclusions:
include '../includes/gw.class.php';

# Checken, ob die Seite geöffet sein darf. Anonsten wird umgeleitet.
if(!isset($_GET['charID'])) {
	header("Location: /flatnet2/index.php");
} else {
	if($_GET['charID'] == "" OR is_numeric($_GET['charID']) == false) {
		header("Location: /flatnet2/index.php");
	}
}
$guildwars = NEW gw_charakter;
$guildwars->header();
$guildwars->logged_in("redirect", "index.php");
$guildwars->userHasRightPruefung(3);
$id = isset($_GET['charID']) ? $_GET['charID'] : '';
$guildwars->getInfoCharID($id); ?>
<title>Charakterbearbeitung</title>
<style>
<?php $breite = 640 / 100 * $row[0]->erkundung; ?>
.gwShowChar<?php echo $row[0]->klasse; ?> #erkundungInfo{
	position: absolute;
	width: <?php echo $breite; ?>px;
	margin-left: 65px;
	margin-top: -24px;
	height: 8px;
	padding: 8px;
	border-radius: 10px;
	background-color: orange;
	opacity: 0.3;
}
</style>
</head>
<body>
	<div class="mainbodyDark">
		<div id="home">
<?php $guildwars->SubNav("guildwars"); ?>
		</div>
		
		<div class="rightBody">
<div class="blogeintrag">
	<h3>Charakter dieses Accounts</h3>
	<?php echo $guildwars->ListOfChars() ?>
				</div>
		
		</div>
			<div class="innerBody">
		<a href="/flatnet2/guildwars/start.php" class="buttonlink">Zurück zur Übersicht</a><br>
<?php 
# Speichern der Änderungen
echo $guildwars->bearbChar();

?>
<form action='?saveActions=yes&charID=<?php echo $row[0]->id;?>' method=post>
<input type=submit name=action value=speichern />
<input type=submit name=action value=löschen />
                
	<div class='gwShowChar<?php echo $row[0]->klasse ?>'>
		
		<?php
    
		if($row[0]->stufe > 80) {
			echo "<p class='info'>Der Charakter hat eine höhere Stufe als 80!</p>";
		}
		if($row[0]->handwerk1stufe > 500) {
			echo "<p class='info'>Die Handwerksstufe des ersten Berufs ist größer als 500.</p>";
		}
		if($row[0]->handwerk2stufe > 500) {
			echo "<p class='info'>Die Handwerksstufe des zweiten Berufs ist größer als 500.</p>";
		}
		
		if($row[0]->erkundung > 100) {
			echo "<p class='info'>Der Explorator kann nicht mehr als 100 % erkunden.</p>";
		}
			
	?>
		<h2>
			<?php echo $row[0]->name . ", " . $row[0]->klasse .", " . $row[0]->rasse ?>
		</h2>
		
		<input type=hidden name=besitzer value="<?php echo $row[0]->besitzer; ?>" />
		
						<p>
		Stufe <input type=number value="<?php echo $row[0]->stufe; ?>"
				name=stufeChar />
						</p>
	<h3>Geburtstag</h3>
		<input type=date (yyyy-mm-dd) name="geboren"
							value="<?php echo $row[0]->geboren; ?>" />
		<h3>Handwerksberufe:</h3>
		<select name="handwerk1" value="">
			<option></option>
			<option
			<?php if($row[0]->handwerk1 == "Lederer") {echo "selected='selected'"; }?>>Lederer</option>
		<option
			<?php if($row[0]->handwerk1 == "Schneider") {echo "selected='selected'"; }?>>Schneider</option>
							<option
		<?php if($row[0]->handwerk1 == "Koch") {echo "selected='selected'"; }?>>Koch</option>
							<option
			<?php if($row[0]->handwerk1 == "Rüstungsschmied") {echo "selected='selected'"; }?>>Rüstungsschmied</option>
			<option
			<?php if($row[0]->handwerk1 == "Waffenschmied") {echo "selected='selected'"; }?>>Waffenschmied</option>
			<option
			<?php if($row[0]->handwerk1 == "Waidmann") {echo "selected='selected'"; }?>>Waidmann</option>
			<option
			<?php if($row[0]->handwerk1 == "Konstrukteur") {echo "selected='selected'"; }?>>Konstrukteur</option>
			<option
			<?php if($row[0]->handwerk1 == "Juwelier") {echo "selected='selected'"; }?>>Juwelier</option>
		</select> Stufe <input type=number
			value="<?php echo $row[0]->handwerk1stufe;?>" name=handwerk1stufe />
		<br> <select name=handwerk2 value="">
			<option></option>
			<option
			<?php if($row[0]->handwerk2 == "Lederer") {echo "selected='selected'"; }?>>Lederer</option>
			<option
			<?php if($row[0]->handwerk2 == "Schneider") {echo "selected='selected'"; }?>>Schneider</option>
			<option
			<?php if($row[0]->handwerk2 == "Koch") {echo "selected='selected'"; }?>>Koch</option>
			<option
			<?php if($row[0]->handwerk2 == "Rüstungsschmied") {echo "selected='selected'"; }?>>Rüstungsschmied</option>
			<option
			<?php if($row[0]->handwerk2 == "Waffenschmied") {echo "selected='selected'"; }?>>Waffenschmied</option>
			<option
			<?php if($row[0]->handwerk2 == "Waidmann") {echo "selected='selected'"; }?>>Waidmann</option>
			<option
			<?php if($row[0]->handwerk2 == "Konstrukteur") {echo "selected='selected'"; }?>>Konstrukteur</option>
			<option
			<?php if($row[0]->handwerk2 == "Juwelier") {echo "selected='selected'"; }?>>Juwelier</option>
		</select> Stufe <input type=number
			value="<?php echo $row[0]->handwerk2stufe;?>" name="handwerk2stufe" />
						<h3>Spielzeit</h3>
		<input type=number name=stunden
			value="<?php echo $row[0]->spielstunden; ?>" />
		<?php echo " (in Stunden)"; ?>
		<br>
		<h3>Erkundung in Prozent</h3>
		<input type=number name=erkundung value="<?php echo $row[0]->erkundung; ?>" placeholder="z. B. 76" />
		
		<div class="innerBody">
		
			<h2>Namensänderung</h2>
			<input type=text name=charName id='long' value='<?php echo $row[0]->name ?>' />
			
			<h2>Klassenänderung</h2>
			Rasse
				<select name=rasse value="" >
				<option <?php if($row[0]->rasse == "Menschen") { echo "selected='selected'"; }?> >Menschen</option>
				<option <?php if($row[0]->rasse == "Asura") { echo "selected='selected'"; }?> >Asura</option>
				<option <?php if($row[0]->rasse == "Sylvari") { echo "selected='selected'"; }?> >Sylvari</option>
				<option <?php if($row[0]->rasse == "Norn") { echo "selected='selected'"; }?> >Norn</option>
				<option <?php if($row[0]->rasse == "Charr") { echo "selected='selected'"; }?> >Charr</option>
				</select>
			Klasse
				<select name=klasse value="" >
				<option <?php if($row[0]->klasse == "Krieger") { echo "selected='selected'"; }?> >Krieger</option>
				<option <?php if($row[0]->klasse == "Wächter") { echo "selected='selected'"; }?> >Wächter</option>
				<option <?php if($row[0]->klasse == "Dieb") { echo "selected='selected'"; }?> >Dieb</option>
				<option <?php if($row[0]->klasse == "Waldläufer") { echo "selected='selected'"; }?> >Waldläufer</option>
				<option <?php if($row[0]->klasse == "Ingenieur") { echo "selected='selected'"; }?> >Ingenieur</option>
				<option <?php if($row[0]->klasse == "Elementarmagier") { echo "selected='selected'"; }?> >Elementarmagier</option>
				<option <?php if($row[0]->klasse == "Nekromant") { echo "selected='selected'"; }?> >Nekromant</option>
				<option <?php if($row[0]->klasse == "Mesmer") { echo "selected='selected'"; }?> >Mesmer</option>
				<option <?php if($row[0]->klasse == "Widergänger") { echo "selected='selected'"; }?> >Widergänger</option>
                </select>
		
	</div>
		
		
	<br> <br> <br><br> <br>
		
	<h3>Notizen</h3>
	<textarea class='ckeditor' id='charNotizen' name='charNotizen'><?php echo $row[0]->notizen; ?></textarea>	
		
		
</div>
</form>
</div>
</div>
</body>
</div>
</html>
