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
            $guildwars = NEW api;
            $guildwars->header();
            $guildwars->logged_in("redirect", "index.php");
            $guildwars->userHasRightPruefung("9");
            $suche = isset($_GET['suche']) ? $_GET['suche'] : '';
            echo $guildwars->suche($suche, "gw_animals", "tierName", "?");
            ?>
            <title>GW API Test</title>
        </head>
        <body>
            <div class='mainbodyDark'>
            <div id='api'>
                <?php $guildwars->SubNav("guildwars"); ?>
            </div>
                <a href="start.php" class="highlightedLink">Zur&uuml;ck</a>
                <?php $guildwars->apiTest(); ?>
            </div>

        </body>
    </div>
</html>
