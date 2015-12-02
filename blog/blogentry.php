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
<script src="/flatnet2/tools/ckeditor/ckeditor.js"></script>
<?php 
#Inclusions:
include '../includes/blog.class.php';
$blog = NEW blog;
# STELLT DEN HEADER ZUR VERFÜGUNG
$blog->connectToDB();
$blog->header();
$blog->logged_in("redirect", "index.php");

$blog->userHasRightPruefung("2");

## Suchfunktion
$allgemein = NEW functions;

$suche = isset($_GET['suche']) ? $_GET['suche'] : '';
echo $allgemein->suche($suche, "blogtexte", "titel", "?showblogid");
?>
<title>Forum - Thema anzeigen</title>
	</head>
	<body>
		<div class='mainbodyDark'>			
			<?php 
			# zeigt Navigation an
			$blog->showForumNav();
			
			# $blogid = (isset($_GET['showblogid'])) ? $_GET['showblogid'] : '';
			$bearbid = (isset($_GET['bearbid'])) ? $_GET['bearbid'] : '';
			$blogid = (isset($_GET['showblogid'])) ? $_GET['showblogid'] : '';
			$loeschid = (isset($_GET['loeschid'])) ? $_GET['loeschid'] : '';

			$selectContentForBlog = NEW blog;
			if($blog->userHasRight("20", 0) == true) {
				$selectContentForBlog->bearbBlogId($bearbid);
				$selectContentForBlog->deleBlogId($loeschid);
			}
			
			if(!isset($_GET['bearbid'])) {
			#	$bearbid = (isset($_GET['loeschid'])) ? $_GET['loeschid'] : '';
				$selectContentForBlog->showBlogId($blogid);
			}
			
			# zeigt Navigation an
			$blog->showForumNav(); ?>
			
		</div>

	</body>
</div>
</html>
