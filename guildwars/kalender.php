<!DOCTYPE html>
<html id='guildwars'>
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
            require '../includes/gw.class.php';

            $guildwars = NEW gw_kalender();
            $guildwars->header();
            $guildwars->logged_in("redirect", "index.php");
            $guildwars->userHasRightPruefung("3");
            $suche = isset($_GET['suche']) ? $_GET['suche'] : '';
            echo $guildwars->suche($suche, "gw_chars", "name", "charakter.php?charID");
            ?>
            <title>GW Kalender</title>
        </head>
        <body>
            <div class='mainbody'>
                <a href="start.php" class="highlightedLink">Zurück</a>
                
                <div id='kalender'>
                <?php $guildwars->SubNav("guildwars"); ?>
                </div>
                
                <h2><a name='gebs'>Geburtstage der Charakter</a></h2>
                
                <?php $guildwars->showMonthGesamt(); ?>
                
                <?php $guildwars->gwCharKalender(); ?>
                
                <div class='innerBody'>
                </br></br></br></br></br></br></br></br></br></br></br> 
                </br></br></br></br></br></br></br></br></br></br></br> 
                </br></br></br></br></br></br></br></br></br></br></br></br></br>
                </br></br></br>
                </div>
            </div>

        </body>
    </div>

    <script>
    var dt = new Date();

    //Display the month, day, and year. getMonth() returns a 0-based number.
    var month = dt.getMonth()+1;
    var day = dt.getDate();
    var year = dt.getFullYear();
    document.getElementById("datum").innerHTML = year + '-' + month + '-' + day;
    </script>
</html>
