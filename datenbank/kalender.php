<!DOCTYPE html>
<html id="gebkalender">
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
            require '../includes/datenbanken.class.php';
            $datenbank = NEW datenbanken;
            $datenbank->header();
            $datenbank->logged_in("redirect", "index.php");
            $datenbank->userHasRightPruefung(22);
            $suche = isset($_GET['suche']) ? $_GET['suche'] : '';
            echo $datenbank->suche($suche, "adressbuch", "nachname", "eintrag.php?bearbeiten");
            ?>
            <title>Steven.NET Kalender</title>
        </head>

        <body>
            <div class="mainbody">
                <div class="topBody">
                    <a href="datenbanken.php" class="highlightedLink">Zurück</a>	
                    <h2><a name="gebs">Geburtstagskalender</a></h2>
                </div>
            </div>
                
            <?php $datenbank->showMonthGesamt(); ?>

            <div class="mainBody">
                <?php $datenbank->gebKalender(); ?>
            </div>
        </body>
    </div>
</html>
