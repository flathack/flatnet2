<!DOCTYPE html>
<html id="forum">
<div id="wrapper">
<?php # Wrapper start ?>
<head>
<?php 
#Inclusions:
include '../includes/blog.class.php';
# Blog start
$addBlog = NEW blog;
$addBlog->header();
$addBlog->userHasRightPruefung(3);
$addBlog->logged_in("redirect", "index.php");


?>
<title>Forum - Eintrag erstellen</title>
	</head>
	<body>
		<div class='mainbodyDark'>
		<?php $addBlog->showForumNav();	?>
		
		<?php if(isset($_GET['blogcategory'])) {
			$kategorie = $_GET['blogcategory'];
			echo "<a href=\"/flatnet2/forum/index.php?blogcategory=$kategorie\" class=\"highlightedLink\">&#8634; Zurück</a>";
		} ?>
			
		<?php 
		$addBlog = NEW blog;
		echo "<h2>Neues Thema erstellen</h2>";
		$addBlog->newBlogFunction();
		$addBlog->newBlogEingabe();
		?>
		<?php 
		if(isset($_GET['blogcategory'])) {
			$kategorie = $_GET['blogcategory'];
			echo "<a href=\"/flatnet2/forum/index.php?blogcategory=$kategorie\" class=\"highlightedLink\">&#8634; Zurück</a>";
		} ?>
		<?php 
		$addBlog->showForumNav();
		?>
		</div>

	</body>
</div>
</html>
