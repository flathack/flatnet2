<?php 
/**
 * @author Steven Schödel
 * Flatnet2 Projekt
 */
?>
<?php echo '<'.'?xml version="1.0" encoding="utf-8"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" id='forum'>
<div id="wrapper">
	<?php # Wrapper start ?>
	<head>
<link href="/flatnet2/css/style.css" type="text/css" rel="stylesheet" />
<script src="/flatnet2/tools/ckeditor/ckeditor.js"></script>
<?php 
#Inclusions:
include '../includes/blog.class.php';
# Blog start
$addBlog = NEW blog;
$addBlog->connectToDB();
$addBlog->header();
$addBlog->userHasRightPruefung("3");
$addBlog->logged_in("redirect", "index.php");


?>
<title>Forum - Eintrag erstellen</title>
	</head>
	<body>
		<div class='mainbodyDark'>
		<?php 
		$addBlog->showForumNav();
		?>
		
		<?php 
		if(isset($_GET['blogcategory'])) {
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
