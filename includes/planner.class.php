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
require 'objekt/functions.class.php';
/**
 * Planner
 * Stellt Funktionen für den Planner zur Verfügung
 *
 * PHP Version 7
 *
 * @category   Classes
 * @package    Flatnet2
 * @subpackage NONE
 * @author     Steven Schödel <steven.schoedel@outlook.com>
 * @license    https://www.gnu.org/licenses/gpl-3.0.txt GNU/GPLv3
 * @version    Release: <1>
 * @link       none
 */
class Planner Extends Functions
{
    /**
     * Comment
     * 
     * @return void
     */
    function plannerMainFunction() 
    {
        // Administration
        if (isset($_SESSION['username'])) {
            $this->eventAdministration();
        }
        if (!isset($_SESSION['eventid'])) {
            $this->eventSelector();
        }

        $this->showEvent();
    }

    /**
     * Comment
     * 
     * @return void
     */
    function newheader() 
    {
        echo '<meta http-equiv="content-type" content="text/html; charset=utf-8" />';
        echo '<link rel="apple-touch-icon" sizes="57x57" href="/apple-icon-57x57.png">';
        echo '<link rel="apple-touch-icon" sizes="60x60" href="/apple-icon-60x60.png">';
        echo '<link rel="apple-touch-icon" sizes="72x72" href="/apple-icon-72x72.png">';
        echo '<link rel="apple-touch-icon" sizes="76x76" href="/apple-icon-76x76.png">';
        echo '<script src="/flatnet2/tools/ckeditor/ckeditor.js"></script>';
        echo '<link href="/flatnet2/css/style.css" type="text/css" rel="stylesheet" />';

        // Quellen für JQUERY Scripte
        echo "<script src='//code.jquery.com/jquery-1.10.2.js'></script>";
        echo "<script src='//code.jquery.com/ui/1.11.4/jquery-ui.js'></script>";
        echo "<script src='/flatnet2/Chart.min.js'></script>";

        // Verschiebbare Fenster
        echo '<script> $(function() { $( "#draggable" ).draggable(); }); </script>';

        session_start();

        echo "<header class='header'>";

        echo "<div class='userInfo'>";

        if (isset($_SESSION["username"])) {
            echo "<p>Willkommen " . "<strong><a href='/flatnet2/usermanager/usermanager.php'>" . $_SESSION['username'] . "</a></strong> | ";
            echo "<a href='/flatnet2/includes/logout.php'> Abmelden </a></p>";
            echo "<ul>";
                
            if ($this->userHasRight(36, 0) == "true") {
                echo "<li><a href='/flatnet2/admin/control.php'>Admin </a></li>";
                echo "<li><a href='/flatnet2/informationen/impressum.php'>Impressum </a></li>";
                echo "<li><a href='/flatnet2/uebersicht.php'>&Uuml;bersicht </a></li>";
            } else {
                echo "<li><a href='/flatnet2/informationen/impressum.php'>Impressum</a></li>";
            }
            echo "</ul>";
        } else {
            echo "<p></p>";
        }
        
        echo "</div>";

        // Überschrift
        echo "<div id='ueberschrift'>";
            echo "<h1><a href='/flatnet2/planner/index.php'>Event Planner</a></h1>";
        echo "</div>";

        if (isset($_SESSION['username'])) {
            $this->showNaviLinks();
        }
        

        // Ende Header
        echo "</header>";
    }

    /**
     * Zeigt einen Link an um die Event Administration durchzuführen
     * 
     * @return void
     */
    function eventAdministration() 
    {   
        echo "<a class='buttonlink' href='index.php'>Hauptseite</a>";
        echo "<a class='buttonlink' href='administration.php'>Event-Administration</a>";
    }

    /**
     * Ermöglicht die Administration der Events die dem User zugeordnet sind.
     * 
     * @return void
     */
    function mainEventAdministration() 
    {
        $this->eventAdministration();
    }

    /**
     * Comment
     * 
     * @return void
     */
    function eventSelector() 
    {
        echo "<div class='login'>";
        if (isset($_POST['submit'])) {
            $username = strip_tags($_POST['username']);
            $code = strip_tags($_POST['eventinvitecode']);
            if (isset($username) AND isset($code)) {
                // code und name vorhanden!
                // check ob code vorhanden ist:
                $codes = $this->getObjektInfo("SELECT * FROM eventinvitecodes WHERE eventinvitecode='$code'");
                if (isset($codes[0]->eventinvitecode)) {
                    
                    $eventid = $codes[0]->eventid;
                    // Checke Benutzer:
                    $wert = $this->objectExists("SELECT * FROM eventguests WHERE eventid=$eventid AND guestname='$username'");
                    if ($wert == true) {
                        // Namen gefunden
                        $_SESSION['eventguest'] = $username;
                        $_SESSION['eventid'] = $codes[0]->eventid;
                        // Update Code:
                        $this->updateCodesUsed($codes[0]->id);

                        $this->sql_insert_update_delete("UPDATE eventguests SET loggedin=1 WHERE guestname='$username'");
                    } else {
                        echo "<p class='hinweis'>Name ist nicht bekannt, bitte wende dich an deinen Event-Veranstalter</p>";
                    }

                } else {
                    echo "<p class='hinweis'>Der Einladungscode existiert nicht. Bitte wende dich an deinen Event Veranstalter.</p>";
                }
            }
        }
        if (!isset($_SESSION['eventid'])) {
            
            if (isset($_POST['username'])) {
                $username = $_POST['username'];
            } else {
                $username = "";
            }
            if (isset($_POST['eventinvitecode'])) {
                $eventinvitecode = $_POST['eventinvitecode'];
            } else {
                $eventinvitecode = "";
            }
            echo "<h2>Willkommen!</h2>";
            echo "<p>Um ein Event anzuzeigen, musst du deinen Einladungscode angeben.</p>";
            echo "<form method=post>";
                echo "<input type=text name=username value='$username' placeholder=Benutzername />";
                echo "<input type=text name=eventinvitecode value='$eventinvitecode' placeholder=Einladungscode />";
                echo "<button type=submit name=submit>Absenden</button>";
            echo "</form>";
        }
        echo "</div>";
    }

    /**
     * Comment
     * 
     * @param int $codeid Code der Verwendet wird.
     * 
     * @return void
     */
    function updateCodesUsed($codeid) 
    {
        // Checke ob Eintrag bereits existiert
        $alreadyused = $this->getObjektInfo("SELECT * FROM eventcodeusage WHERE codeid=$codeid");
        if (isset($alreadyused[0]->codeid)) {
            $counter = $alreadyused[0]->codeusage + 1;
            $sql = "UPDATE eventcodeusage SET codeusage=$counter WHERE codeid=$codeid";
        } else {
            $counter = 1;
            $sql = "INSERT INTO eventcodeusage (codeid, codeusage) VALUES ($codeid, $counter)";
        }
        $this->sql_insert_update_delete($sql);
    }

    /**
     * EventTexts Legende
     * 1 : Willkommensmessage
     * 2 : 
     * 
     * @return void
     */
    function showEvent() 
    {
        if (isset($_SESSION['eventid'])) {
            if (isset($_GET['eventchange'])) {
                unset($_SESSION['eventid']);
                echo "<a href='index.php'>Zurück zur Startseite</a>";
            }
            // EVENT WECHSELN
            echo "<p>Event ID : " . $_SESSION['eventid'] . " | Gast: " . $_SESSION['eventguest'] . " <a href='?eventchange'>Event verlassen</a></p>";

            
            
        }
    }
}

?>