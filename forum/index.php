<!DOCTYPE html>
<html id="forum">
<div id="wrapper">
<head>
<?php 
#Inclusions:
include '../includes/forum.class.php';
$forum = NEW forum;
$forum->header();
$forum->logged_in("redirect", "index.php");
$forum->userHasRightPruefung(2);
$suche = isset($_GET['suche']) ? $_GET['suche'] : '';
echo $forum->suche($suche, "blogtexte", "titel", "/flatnet2/blog/blogentry.php?showblogid"); ?>
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
		$forum->showPublicArea(); ?>
		<div class="spacer">
			<a href="public.php" class="buttonlink">Öffentlicher Bereich</a>
		</div>
		</div>
	</body>
</div>
</html>
