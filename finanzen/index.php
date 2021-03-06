<!DOCTYPE html>
<html id="finanzen">
    <div id="wrapper">
        <head>
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
            require '../includes/finanzen.class.php';
            $finanzen = NEW finanzenNEW;
            $finanzen->header();
            $finanzen->logged_in("redirect", "index.php");
            $finanzen->userHasRightPruefung("17");
            $suche = isset($_GET['suche']) ? $_GET['suche'] : '';
            echo $finanzen->finanzSuche($suche);
            ?>
            <title>Steven.NET Finanzen</title>
        </head>
        <body>
            <div class='mainbodyDark' id="monate">
                <?php $finanzen->showNavigation(); ?>
                <?php
                // Zeigt die Finanzen an.
                $finanzen->mainFinanzFunction();
                ?>
            </div>
        </body>
    </div>
</html>