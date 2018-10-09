<!DOCTYPE html>
<html id="toyota">
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
            require '../includes/hardwaredb.class.php';
            $hw = NEW HardwareDB;
            $hw->header();
            $hw->logged_in("redirect", "index.php");
            $hw->userHasRightPruefung(81);
            ?>
            <title>Steven.NET Hardware Datenbank</title>
        </head>
        <body>
            <div class='mainbodyDark' id="monate">
                <h2>Hardware Verwaltung</h2>
                <?php
                // Zeigt die Finanzen an.
                $hw->mainHWFunction();
                ?>
            </div>
        </body>
    </div>
</html>