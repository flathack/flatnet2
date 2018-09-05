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
            $guildwars = NEW gw_costs;

            // STELLT DEN HEADER ZUR VERFÜGUNG
            $guildwars->header();

            $guildwars->logged_in("redirect", "index.php");
            $guildwars->userHasRightPruefung("9");
            ?>
            <title>Kosten Calculator</title>
        </head>
        <body>
            <div class='mainbodyDark'>
                <a href="start.php" class="highlightedLink">Zurück</a>
                <div id='calc'>
                <?php $guildwars->SubNav("guildwars"); ?>
                </div>
                <h2>Kosten Calculator</h2>
                    <?php $guildwars->showAccountInfo(); ?>
                    <?php $guildwars->showCostCalcEntries(); ?>
            </div>
        </body>
    </div>
</html>
