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
            $finanzen->userHasRightPruefung(17);
            $suche = isset($_GET['suche']) ? $_GET['suche'] : '';
            echo $finanzen->suche($suche, "adressbuch", "nachname", "eintrag.php?bearbeiten");
            ?>
            <title>Steven.NET Finanzen</title>
        </head>
        <body>
            <div class='mainbodyDark' id="shares">
                <?php $finanzen->showNavigation(); ?>
                <h2>Finanzverwaltung</h2>
                <?php 
                // Zeigt die Finanzen an.
                $finanzen->mainShareFunction();
                ?>
            </div>
        </body>
    </div>
</html>