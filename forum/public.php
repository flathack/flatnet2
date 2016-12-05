<!DOCTYPE html>
<html id="forum">
<div id="wrapper">
<?php 
include '../includes/objekt/functions.class.php';

class publicThings extends functions {
	
	function createEntry($blogid) {
		if(isset($_POST['submit']) AND isset($_POST['newText'])) {
			$text = $_POST['newText'];
			$test = $_POST['test'];
			$username = $_POST['username'];
			
			$text = "<h3>Response from " . $username . "</h3>" . $text;
			
			if($test == "Marcel" OR $test == "marcel" OR $test == "Steven" OR $test == "steven") {
				$query = "INSERT INTO blog_kommentare (autor, text, blogid) VALUES ('0','$text','$blogid')";
				$check = "SELECT count(*) as anzahl FROM blog_kommentare WHERE text ='$text'";
				$checkInfo = $this->getObjektInfo($query);
				
				if(isset($checkInfo[0]->anzahl) AND $checkInfo[0]->anzahl > 0) {
					echo "<p class='meldung'>Creation failed! You cannot create the same post twice!</p>";
					exit;
				}
				if($this->sql_insert_update_delete($query) == true) {
					echo "<p class='erfolg'>Post created!</p>";
				} else {
					echo "<p class='meldung'>Creation failed!</p>";
				}
				
			} else {
				echo "<p class='meldung'>The name of the student is not $test.</p>";
			}
		} else {
			echo "<p class='meldung'>You didnt fill out all necessary data.</p>";
		}
	}
	
	function createResponse($id) {
		if(isset($_POST['submit'])) { $this->createEntry($id); }
		echo "<div>";
		echo "<form method=post>";
			echo "<input type=text name=username placeholder=Username /> ";
			echo "What is the first name of the owner of this site? <input type=text name=test placeholder='e. g. Peter' />";
			echo "<textarea name=newText class=ckeditor></textarea> ";
			echo "<input type=submit name=submit value=Save /> ";
		echo "</form>";
		echo "</div>";
	}
	
	function showComments($blogid) {
		echo "<div class='kommentare'>";
		$query = "SELECT * FROM blog_kommentare WHERE blogid=$blogid ";
		
		$kommentare = $this->getObjektInfo($query);
		for ($i = 0 ; $i < sizeof($kommentare) ; $i++) {
			echo "<div class='publicInfo'>";
			$autorName = $this->getUserName($kommentare[$i]->autor);
				echo "<h3>Response from $autorName, ".$kommentare[$i]->timestamp. "</h3>";
				echo $kommentare[$i]->text;
			echo "</div>";
		}
		
		echo "</div>";
	}
	
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
					echo "<div class='publicInfo'>";
					
						echo "<h2>" . $beitrag[0]->titel . "</h2>";
						echo $beitrag[0]->text;
					
					echo "</div>"; 
					
					# Kommentare
					
					$this->showComments($beitrag[0]->id);
					
					$this->createResponse($beitrag[0]->id);
				} 
			} else {
				echo "<p class='meldung'>Error</p>";
			}
		}
	}
	
}
?>

<?php $public = NEW publicThings(); ?>

<header>
<link rel="apple-touch-icon" sizes="57x57" href="/apple-icon-57x57.png">
<link rel="apple-touch-icon" sizes="60x60" href="/apple-icon-60x60.png">
<link rel="apple-touch-icon" sizes="72x72" href="/apple-icon-72x72.png">
<link rel="apple-touch-icon" sizes="76x76" href="/apple-icon-76x76.png">
<script src="/flatnet2/tools/ckeditor/ckeditor.js"></script>

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
