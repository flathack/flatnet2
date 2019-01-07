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
?>
<!DOCTYPE html>
<html id='profil'>
    <div id="wrapper">
        <head>
            <?php 
            require '../includes/usermanager.class.php';
            $usermanager = NEW usermanager;
            $usermanager->header();
            $usermanager->logged_in("redirect", "index.php");
            $benutzername = $_SESSION['username'];
            $usermanager->userHasRightPruefung(7);
            $allgemein = NEW functions;
            if (isset($_GET['table'])) {
                $suche = $_SESSION['username'];
                $table = isset($_GET['table']) ? $_GET['table'] : '';
                $spalte = isset($_GET['spalte']) ? $_GET['spalte'] : '';
                $link = "?";
                echo $allgemein->suche($suche, $table, $spalte, $link);
            }
            ?>
            <title><?php echo $benutzername . "'s Profil"; ?></title>
        </head>
        <body>
            <div class='mainbody'>
            <?php $usermanager->subNav("profil"); ?>
                    <h2>Willkommen <?php echo $benutzername ?></h2>
                    <div>
                        <?php 
                        if (isset($_GET['user'])) { 
                            $usermanager->showProfile($_GET['user']); 
                        } 
                        ?>
                        
                    <div class='innerBody'>
                        <?php $usermanager->manageGWAccounts(); ?>
                        <?php $usermanager->showUserList(); ?>
                        <?php $usermanager->userInfo(); ?>
                        <?php $usermanager->showPassChange(); ?>
                    </div>
                    </div>
                <?php 
                // $usermanager->userBlogTexte();
                ?>
            </div>
        </body>
    </div>
</html>
