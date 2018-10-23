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

            // GW start
            $guildwars = NEW gw_charakter();

            // STELLT DEN HEADER ZUR VERFÜGUNG
            $guildwars->header();

            $guildwars->logged_in("redirect", "index.php");
            $guildwars->userHasRightPruefung("3");

            $suche = isset($_GET['suche']) ? $_GET['suche'] : '';
            echo $guildwars->suche($suche, "gw_chars", "name", "charakter.php?charID");
            ?>


            <title>Guildwars</title>
        </head>
        <body>
            <div class='mainbody'>
            
            <?php 
            // Check ob alle Variablen gesetzt sind:
            if (!isset($_SESSION['selectGWUser'])) {
                echo "<p class='meldung'>Fehler, Variablen sind falsch gesetzt, bitte neu Anmelden.</p>";
                echo "</div>";
                echo "</body>";
                echo "</html>";
                exit;
            }
            ?>
            <div id='home'>
                    <?php $guildwars->SubNav("guildwars"); ?>
                    </div>
                <div class='GWtopBody'>
                    <div id='left'>
                        <?php $guildwars->selectUser(); ?>
                    </div>
                    
                    <div id='left'>
                        <?php $guildwars->selectAccount(); ?>
                    </div>
                    
                    <div id='right'>
                        <a href="?createNewChar=yes#charErstellen" class="greenLink">Neuen Charakter</a>
                        <a href="?createNewChar=yes&namen#charErstellen" class="greenLink">Namegen</a>
                    </div>
                    
            
                </div>
                <?php echo $guildwars->newChar(); ?>
                <?php 
                if (isset($_GET['namen'])) { 
                    $guildwars->nameGen(); 
                } 
                ?>
                
                <div class='showChars'>
                    <?php echo $guildwars->showChars(); ?>
                </div>
                <?php $guildwars->globalCharInfos(); ?>
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
