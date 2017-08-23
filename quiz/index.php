<!DOCTYPE html>
<html id="quiz">
<div id="wrapper">
<head>
<?php # Inclusions
include '../includes/quiz.class.php';
$quiz = NEW quiz;
$quiz->header();
$quiz->logged_in("redirect", "index.php");
$quiz->userHasRightPruefung("81");

$allgemein = NEW functions;
$suche = isset($_GET['suche']) ? $_GET['suche'] : '';
echo $allgemein->suche($suche, "quiz_themen", "name", "?id");

## Suchfunktion


?>
<title>Steven.NET Finanzen</title>
	</head>
	<body>
		<div class='mainbodyDark'>
		
			<?php $quiz->showNavigation(); ?>
					
			<h2>Quiz</h2>

			<?php # Zeigt die Finanzen an.
            $quiz->mainQuizFunction();	?>
			
		</div>
	</body>
</div>
</html>