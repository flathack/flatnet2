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
            $guildwars = NEW guildwars;
            $guildwars->header();
            $guildwars->logged_in("redirect", "index.php");
            $guildwars->pruefung("32");
            ?>
            <title>Guildwars - Statistiken</title>
        </head>
        <body>
            <div class='mainbodyDark'>
                <a href="start.php" class="highlightedLink">Zurück</a>
                <div id='statistik'>
                    <?php $guildwars->SubNav("guildwars"); ?>
                </div>
                <div class="innerBody">
                    <h1>Statistiken für <?php echo $_SESSION['username']; ?></h1>
                    <?php $guildwars->erkundung(); ?>
                </div>
                <div class=''>
                    <?php $guildwars->medallien(); ?>
                </div>
            </div>
        </body>
    </div>
</html>