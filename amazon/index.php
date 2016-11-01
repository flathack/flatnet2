<?php echo '<'.'?xml version="1.0" encoding="utf-8"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" id='amazon'>
<div id="wrapper">
	<?php # Wrapper start ?>
	<head>
<?php
header('Content-Type: text/html; charset=UTF-8');
?>
<?php 
#Inclusions:
include '../includes/amazon.class.php';

#Forum Function
$amazon = NEW amazon;

# STELLT DEN HEADER ZUR VERFÜGUNG
$amazon->header();

$amazon->logged_in("redirect", "index.php");

# $amazon->userHasRightPruefung("11");

$suche = isset($_GET['suche']) ? $_GET['suche'] : '';
echo $amazon->suche($suche, "amazon_infos", "name_of_article", "?edit");
?>
<title>Steven.NET - Amazonliste</title>
	</head>
	<body>
		<div class='mainbodyDark'>
			<?php $amazon->setErstattet(); ?>
			<?php $amazon->setPayed(); ?>
			<?php $amazon->setRuecksendung(); ?>
			<?php $amazon->createAmazonArticle(); ?>
			<?php $amazon->editPayment(); ?>
			<?php $amazon->checkUser(); ?>
			<?php $amazon->getAmazonPaymentsAdmin() ?>
			<?php $amazon->getAmazonPayments(); ?>
		</div>
	</body>
</div>
</html>
