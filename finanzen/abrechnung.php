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
            session_start();
            echo '<script src="/flatnet2/tools/ckeditor/ckeditor.js"></script>';
            echo '<link href="/flatnet2/css/style.css" type="text/css" rel="stylesheet" />';
            echo "<script src='//code.jquery.com/jquery-1.10.2.js'></script>";
            echo "<script src='//code.jquery.com/ui/1.11.4/jquery-ui.js'></script>";
            echo "<script src='/flatnet2/Chart.min.js'></script>";
            ?>
            <?php 
            if (isset($_GET['month'])) { 
                $monat = $_GET['month']; 
            } else {
                $monat = "";
            }
            ?>
            <title>Abrechnung <?php echo $monat; ?> </title>
        </head>
        <body>
            <div class='mainbodyDark'>
                <?php
                if (isset($_GET['month']) AND isset($_GET['konto'])) {
                    $monat = $_GET['month'];
                    $konto = $_GET['konto'];
                    if (is_numeric($monat) AND is_numeric($konto)) {
                        $finanzen->mainAbrechnung($monat, $konto);
                    }
                }
                ?>
            </div>
        </body>
    </div>
</html>