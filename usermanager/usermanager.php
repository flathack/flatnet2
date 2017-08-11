<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" id='profil'>
<div id="wrapper">
	<?php # Wrapper start ?>
	<head>
<?php 
#Inclusions:
include '../includes/usermanager.class.php';
# Usermanager öffnen
$usermanager = NEW usermanager;
$usermanager->header();
$usermanager->logged_in("redirect", "index.php");

$benutzername = $_SESSION['username'];
$usermanager->userHasRightPruefung("7");
## Suchfunktion
$allgemein = NEW functions;
if(isset($_GET['table'])) {
	$suche = $_SESSION['username'];
	$table = isset($_GET['table']) ? $_GET['table'] : '';
	$spalte = isset($_GET['spalte']) ? $_GET['spalte'] : '';
	$link = "?";
	echo $allgemein->suche($suche, $table, $spalte, $link);
}

?>

<title><?php echo $benutzername . "'s Profil"; ?></title>
	</head>

	<body>

		<div class='mainbody'>
		
		<?php $usermanager->subNav("profil"); ?>
			<div class="mainBody">
				<h2>
					Willkommen
					<?php echo $benutzername ?>
				</h2>
				<div>
					<?php if(isset($_GET['user'])) { $usermanager->showProfile($_GET['user']); } ?>
				</div>	
			
				<?php # $usermanager->addRealName(); ?>	
				<?php $usermanager->showPassChange(); ?>
				<?php $usermanager->showUserList(); ?>
				<?php $usermanager->userInfo(); ?>
			
			</div>
			
		</div>
		<div class='mainbody'>
				
		<?php 
		$usermanager->userBlogTexte();
		?>
		</div>
	</body>
</div>
</html>
