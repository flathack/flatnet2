<?php echo '<'.'?xml version="1.0" encoding="utf-8"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" id='forum'>
<div id="wrapper">
	<?php # Wrapper start ?>
	<head>
<?php
header('Content-Type: text/html; charset=UTF-8');
?>
<?php 
#Inclusions:
include '../includes/forum.class.php';

#Forum Function
$forum = NEW forum;

# STELLT DEN HEADER ZUR VERFÜGUNG
$forum->header();

$forum->logged_in("redirect", "index.php");

$forum->userHasRightPruefung("2");

$suche = isset($_GET['suche']) ? $_GET['suche'] : '';
echo $forum->suche($suche, "blogtexte", "titel", "/flatnet2/blog/blogentry.php?showblogid");
?>
<title>Steven.NET-Forum</title>
	</head>
	<body>
		
		<div class='mainbodyDark'>
		
		<?php 
		
		# Zeigt die Blogkategorien an:
		if(!isset($_GET['topic']) AND !isset($_GET['category']) AND !isset($_GET['blogcategory'])) {
			$forum->showBlogCategories();
		}
		$forum->showBlogPosts();
		?>
		<div class='spacer'>
			<a href="public.php" class="buttonlink">Öffentlicher Bereich</a>
		</div>
		</div>
	</body>
</div>
</html>
