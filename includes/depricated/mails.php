<?php 
/**
 * @author Steven Schödel
 * Flatnet2 Projekt
 */
?>
<?php echo '<'.'?xml version="1.0" encoding="utf-8"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" id='profil'>
<div id="wrapper">
	<?php # Wrapper start ?>
	<head>
<?php 

# Check ob User eingeloggt ist:
$checkIfLoggedIn = NEW login;
$checkIfLoggedIn->logged_in("redirect", "index.php");

# Benutzername:
$benutzername = $_SESSION['username'];

$usermanager->userHasRightPruefung("8");
?>

<title><?php echo $benutzername . "'s Emails"; ?></title>
	</head>

	<body>

		<div class='mainbody'>
			<div class="topBody">
				<h2>Emails</h2>
				<a href="usermanager.php" class="buttonlink">Zurück</a>
			</div>

			<div class="rightBody">Aktionen</div>

			<div class="innerBody">
				<?php 
				if(!isset($_GET['action'])) {
			echo '
 		<div class="bereiche">
 		<a href="?action=eingang"><h2>Posteingang</h2></a>
 		</div>

 		<div class="bereiche">
 		<a href="?action=newmail"><h2>Neue Mail</h2></a>
 		</div>
 		';
		} else {
			if($_GET['action'] == "eingang") {
				$posteingang = NEW mail;
				$posteingang->mailEingang();
			}

			if($_GET['action'] == "newmail") {
				$createMail = NEW mail;
				$createMail->mailFormular();
			}
		}
		?>

				<br> <br> <br> <br> <br> <br> <br> <br> <br>

			</div>

		</div>
	</body>
</div>
</html>
