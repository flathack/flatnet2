<?php echo '<'.'?xml version="1.0" encoding="utf-8"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" id='forum'>
<div id="wrapper">
	<?php # Wrapper start ?>


<?php 
/**
 * Zeigt öffentliche Beiträge an.
 * @author BSSCHOE
 *
 */
include '../includes/objekt/functions.class.php';

class publicThings extends functions {
	
	function showPublicTopics() {
		# Prüfen ob Var gesetzt ist.
		if(isset($_GET['topicID'])) {
			$topicID = round($_GET['topicID'], 0);
			
			# Prüfen ob topicID eine Nummer ist.
			if(is_numeric($topicID) == true AND $topicID > 0) {
				$query = "SELECT * FROM blogtexte WHERE id=$topicID AND status=4";
				$beitrag = $this->getObjektInfo($query);
				
				# Prüfen ob ID existiert,
				if(isset($beitrag[0]->titel)) {
					echo "<div class='newCharWIDE'>";
					
						echo "<h2>" . $beitrag[0]->titel . "</h2>";
						echo $beitrag[0]->text;
					
					echo "</div>"; 
				} 
			} else {
				echo "<p class='meldung'>Error</p>";
			}
		}
	}
	
}

?>

<?php 
$public = NEW publicThings();
?>

<header>
<?php
header ( 'Content-Type: text/html; charset=UTF-8' );
?>
<link rel="apple-touch-icon" sizes="57x57" href="/apple-icon-57x57.png">
<link rel="apple-touch-icon" sizes="60x60" href="/apple-icon-60x60.png">
<link rel="apple-touch-icon" sizes="72x72" href="/apple-icon-72x72.png">
<link rel="apple-touch-icon" sizes="76x76" href="/apple-icon-76x76.png">
<script src="/flatnet2/tools/ckeditor/ckeditor.js"></script>

<link href="/flatnet2/css/style.css" type="text/css" rel="stylesheet" />
<link href="/flatnet2/css/style.css" type="text/css" rel="stylesheet" />

<title>Öffentlich</title>
</header>
<body>
	<div class='mainbodyDark'>
	<a class='buttonlink' href="/flatnet2/index.php">Hauptseite</a>
	
	<?php $public->showPublicTopics(); ?>
	
	<?php if(!isset($_GET['topicID'])) { echo "<p class='spacer'>Keinen Beitrag ausgewaehlt.</p>"; }?>
	

	</div>
</body>
</div>
</html>
