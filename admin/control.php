<!DOCTYPE html>
<html id="administration">
<div id="wrapper">
	<?php # Wrapper start ?>
	<head>
		<?php 
		#Inclusions:
		
		include '../includes/control.class.php';
		
		#Start 
		$admin = NEW control;
		
		# STELLT DEN HEADER ZUR VERFÜGUNG
		$admin->header();
		
		$admin->logged_in("redirect", "index.php");
		
		$admin->userHasRightPruefung("36");
		
		$suche = isset($_GET['suche']) ? $_GET['suche'] : '';
		echo $admin->suche($suche, "benutzer", "Name", "?action=1&user");
		?>
		<title>Administrator</title>
	</head>

	<body>

		<div class='mainbodyDark'>
		
			<?php $admin->SubNav("admin"); ?>
			
			<h2><a name='administration'>Administration</a></h2>
			<?php 
			$admin = NEW control;
			# Benutzer sperren / entsperren
			$statusID = (isset($_GET['statusID'])) ? $_GET['statusID'] : '';
			$status = (isset($_GET['status'])) ? $_GET['status'] : '';
			if(isset($_GET['action']) AND $_GET['action'] == 1) {
				$admin->modifyUsersStatus($statusID, $status);
			}
			
			?>
			
			<?php
			$action = (isset($_GET['action'])) ? $_GET['action'] : '';
			$admin->contentSelector($action);
			?>
		</div>
	</body>
</div>
</html>
