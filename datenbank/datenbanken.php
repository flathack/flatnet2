<!DOCTYPE html>
<html id="adressbuch">
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
require '../includes/datenbanken.class.php';
$datenbank = NEW datenbanken;
$datenbank->header();
$datenbank->logged_in("redirect", "index.php");
$datenbank->userHasRightPruefung(7);

// Umleiten wenn User keine Berechtigungen hat
if ($datenbank->userHasRight(14, 0) == false) {
    header("Location: kalender.php");
}
$suche = isset($_GET['suche']) ? $_GET['suche'] : '';
echo $datenbank->suche($suche, "adressbuch", "nachname", "eintrag.php?bearbeiten");

?>
<title>Steven.NET Adressbuch</title>
    </head>

    <body>
        <div class="mainbodyDark">
        
            <?php 
            // hinzufügen neuer Einträge
            if ($datenbank->userHasRight(14, 0) == true) {
                echo $datenbank->UserErstellen();
            } 
            ?>
            
            <div id='right'>	
                <?php 
                // hinzufügen neuer Einträge
                if ($datenbank->userHasRight(14, 0) == true) {
                    echo "	<a href='?eintragenja=1' class='buttonlink'>Neuen Eintrag</a>";
                }
                ?>
                <a href='kalender.php' class='buttonlink'>Geburtstagskalender</a>	
            </div>
            
        </div>

        <div class='adressbuchbody'>
            <?php 
            // zeigt alle DB Einträge im Adressbuch an.
            if ($datenbank->userHasRight(13, 0) == true) {
                echo $datenbank->datenbankListAllerEintraege();
            }
            ?>
        </div>
    </body>
</div>
</html>
