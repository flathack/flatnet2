<?php 
/**
 * DOC COMMENT 
 * 
 * PHP Version 7
 * 
 * @category   Document
 * @package    Flatnet2
 * @subpackage NONE
 * @author     Steven Schödel <steven.schoedel@outlook.com>
 * @license    https://www.gnu.org/licenses/gpl-3.0.txt GNU/GPLv3
 * @link       none
 */
?>
<!DOCTYPE html>
<html id="quiz">
<div id="wrapper">
<head>
<?php
require '../includes/quiz.class.php';
$quiz = NEW quiz;
$quiz->header();
$quiz->logged_in("redirect", "index.php");
$quiz->userHasRightPruefung("81");

$allgemein = NEW functions;
$suche = isset($_GET['suche']) ? $_GET['suche'] : '';
echo $allgemein->suche($suche, "quiz_themen", "name", "?id");
?>
<title>Steven.NET Quiz</title>
    </head>
    <body>
        <div class='mainbodyDark'>
            <?php $quiz->showNavigation(); ?>
            <h2>Quiz</h2>
            <?php
            $quiz->mainQuizFunction();
            ?>
        </div>
    </body>
</div>
</html>