<!DOCTYPE html>
<html id="administration">
<div id="wrapper">
    <head>
        <?php
        /**
         * CONTROL
         * Startseite der Verwaltungsseite für den Administrator
         *
         * PHP version 7
         * 
         * @category   Classes
         * @package    Flatnet2
         * @subpackage NONE
         * @author     Steven Schödel <steven.schoedel@outlook.com>
         * @license    https://www.gnu.org/licenses/gpl-3.0.txt GNU/GPLv3
         * @link       none
         */ 
        require '../includes/control.class.php';
        
        $admin = NEW control;
        $admin->header();
        $admin->logged_in("redirect", "index.php");
        $admin->userHasRightPruefung("36");
        $suche = isset($_GET['suche']) ? $_GET['suche'] : '';
        echo $admin->suche($suche, "benutzer", "Name", "?action=1&user");
        ?>
        <title>Administrator</title>
    </head>

    <body>

        <div class='mainbodyDark'>
        
            <?php $admin->SubNav("admin"); ?>
            
            <h2><a name='administration'>Administration</a></h2>
            
            <?php 
            $admin = NEW control;
            // Benutzer sperren / entsperren
            $statusID = (isset($_GET['statusID'])) ? $_GET['statusID'] : '';
            $status = (isset($_GET['status'])) ? $_GET['status'] : '';
            if (isset($_GET['action']) AND $_GET['action'] == 1) {
                $admin->modifyUsersStatus($statusID, $status);
            }
            
            $action = (isset($_GET['action'])) ? $_GET['action'] : '';
            $admin->contentSelector($action);
            ?>
        </div>
    </body>
</div>
</html>