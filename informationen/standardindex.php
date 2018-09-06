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
<html id='ANPASSEN'>
<div id="wrapper">
    <head>
<?php 
require '../includes/functions.class.php';

//$ANPASSEN = NEW ANPASSEN;
$ANPASSEN->header();
$ANPASSEN->logged_in("redirect", "index.php");
$ANPASSEN->userHasRightPruefung();
$suche = isset($_GET['suche']) ? $_GET['suche'] : '';
// In welcher Tabelle, Spalte soll gesucht werden, dann ?edit Was soll als Link eingefügt werden
echo $fahrten->suche($suche, "fahrkosten", "datum", "?edit");

?>
<title>Steven.NET - ANPASSEN</title>
    </head>
    <body>
        <div class='mainbodyDark'>
            <div class="navigationReiter">
            </div>
            <div class='rightBody'>
                
            </div>
            <div class='innerBody'>
            </div>
        </div>
    </body>
</div>
</html>
