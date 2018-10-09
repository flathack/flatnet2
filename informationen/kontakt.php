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
<html id='kontakt'>
<div id="wrapper">
<head>
<?php
require '../includes/control.class.php';
$admin = NEW control;
$kontakt = NEW functions();
$kontakt->header();
$kontakt->logged_in();
?>
<title>Kontakt</title>
    </head>
    <body>
        <div class="mainbody">
            <div class="rightOuterBody">
                <div id='kontakt2'>
                <?php $kontakt->SubNav("informationen"); ?>
                </div>
            </div>
            <div class="docuEintrag">
                <div class="newChar">
                    <h2>Anfrage an den Administrator senden...</h2>
                    <form method=post>
                        <input type="text" name="vorschlagText" id="titel"
                            placeholder="Problembeschreibung" /> <input type="submit"
                            name="Absenden" value="SENDEN" />
                    </form>
                </div>
                <?php
                if (isset($_POST['vorschlagText'])) {
                    if ($_POST['vorschlagText'] == "") {
                        echo "<p class='meldung'>Feld ist leer.</p>";
                    } else {
                        $autor = $kontakt->getUserID($_SESSION['username']);
                        $text = strip_tags(stripslashes($_POST['vorschlagText']));
                        // Query in die Class schieben:
                        $query = "INSERT INTO vorschlaege (autor, text, status) VALUES ('$autor','$text','offen')";
                        if ($kontakt->sqlInsertUpdateDelete($query) == true) {
                            echo "<p class='erfolg'>Vorschlag eingereicht</p>";
                        } else {
                            echo "<p class='info'>Sorry! Gerade ist was kaputt. Der Vorschlag wurde nicht gesendet, aber eingetragen</p>";
                        }
                    }
                }
                ?>
            </div>
            <div class="docuEintrag">
                <?php $admin->userVorschlaege(); ?>
            </div>
        </div>
    </body>
</div>
</html>
