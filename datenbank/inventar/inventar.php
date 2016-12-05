<!DOCTYPE html>
<html id="inventar">
<div id="wrapper">
<head>
<?php 
#Inclusions:
include '../../includes/inventar.class.php';

# Inventar öffnen
$inventar = NEW inventar;

# STELLT DEN HEADER ZUR VERFÜGUNG
$inventar->connectToDB();
$inventar->header();
$inventar->logged_in("redirect", "index.php");
$inventar->pruefung(128);?>
<title>Inventarliste</title>
</head>
<body>
	<div class="mainbodyDark">
		<a href="?action=Speichern&newEintrag" class="buttonlink">Neuer Eintrag</a>
		<a href="?showSumme=1" class="buttonlink">Summe aller Kategorien anzeigen</a>
		
			<?php 
			$showSumme = isset($_GET['showSumme']) ? $_GET['showSumme'] : '';
			if($showSumme == "1") { echo $inventar->summe(); } 
			
			$suche = isset($_GET['suche']) ? $_GET['suche'] : '';
			echo $inventar->suche($suche, "inventar", "name", "Inventar_neu.php?action=update&id");
			?>
		
		<div class="topBody">
			<h2>Inventar</h2>
			<div>
			<?php 
			$selectedFilter = isset($_GET['status']) ? $_GET['status'] : '';
			echo $inventar->filter($selectedFilter); ?>
			</div>
			
		</div>
		<div class="rightBody">
		
			<?php 
			$inventar->recentEntries();
			?>
		</div>
		
		<?php # NEUEN INVENTAR EINTRAG
		#######################################################################
		$action = isset($_GET['action']) ? $_GET['action'] : 'Speichern';
		$id = isset($_GET['id']) ? $_GET['id'] : '';
		if(isset($_GET['newEintrag']) AND isset($_GET['action'])) {
			echo $inventar->newInventar($action, $id);
		}
		
		$inventar->saveNewEintrag();
		####################################################################### ?>
		
		<div class="innerBody">
			<?php
				$id = isset($_GET['del']) ? $_GET['del'] : '';	
				echo $inventar->delInventar($id); 
			?>
			<table class="flatnetTable">
				<thead>
					<td>Status</td>
					<td id='long'>Name</td>
					<td id='options'>Standort</td>
					<td>Kategorie</td>
					<td id='options'>Preis</td>
					<td id='options'>Aktionen</td>
				</thead>
				
				<?php 
				# Füllt den BODY mit Informationen
				$status = isset($_GET['status']) ? $_GET['status'] : '';
				echo $inventar->showInventar($status);
				?>
				
			</table>
		</div>
		
		
	</div>

</body>
</div>
</html>
