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
        $this->doLogin();
        if (!isset($_SESSION['eventid'])) {
            echo "<div class='login'>";
            echo "<h2>Leider ist etwas schief gelaufen!</h2>";
            echo "Du hast kein Event ausgewählt,<br> <a href='index.php'>bitte logge dich vorher ein.</a>";
            echo "</div>";
        }
        // Navigation
        $this->eventNavigation();
        
        if (isset($_SESSION['eventid'])) {
            $this->showEvent();
        }

        $this->showFooter();

    }

    /**
     * Comment
     * Index.php
     * 
     * @return void
     */
    function plannerWelcome() 
    {
        // ChangeEvent
        $this->changeEvent();
        // Administration
        if (isset($_SESSION['username'])) {
            $this->eventAdministration();
        }
        $this->eventSelector();
        $this->showFooter();
    }

    /**
     * Header für den EventManager
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
     * Stellt den Footer dar.
     * 
     * @return void
     */
    function showFooter() 
    {
        echo "<div class='innerBody'>";
            echo "<p>EventPlanner by Steven Schödel (c) Version 9.10.2018";
        echo "</div>";
    }

    /**
     * Stellt die Navigation dar.
     * 
     * @return void
     */
    function eventNavigation() 
    {
        if (isset($_SESSION['username'])) {
            $this->eventAdministration();
        }
        echo "<a class='buttonlink' href='event.php'>Event</a>";
        echo "<a class='rightRedLink' href='index.php?eventchange'>Event verlassen</a>";
        
    }
    /**
     * Zeigt einen Link an um die Event Administration durchzuführen
     * 
     * @return void
     */
    function eventAdministration() 
    {   
        echo "<a class='buttonlink' href='index.php'>Login</a>";
        echo "<a class='buttonlink' href='administration.php'>Event-Administration</a>";
    }

    /**
     * Ermöglicht die Administration der Events die dem User zugeordnet sind.
     * 
     * @return void
     */
    function mainEventAdministration() 
    {
        $this->eventNavigation();
    }

    /**
     * Führt den Login eines Gastes aus.
     * 
     * @return void
     */
    function doLogin() 
    {
        if (isset($_POST['submit'])) {
            $username = strip_tags($_POST['username']);
            $code = strip_tags($_POST['eventinvitecode']);
            if (isset($username) AND isset($code)) {
                // code und name vorhanden!
                // check ob code vorhanden ist:
                $codes = $this->sqlselect("SELECT * FROM eventinvitecodes WHERE eventinvitecode='$code'");
                if (isset($codes[0]->eventinvitecode)) {
                    
                    $eventid = $codes[0]->eventid;
                    // Checke Benutzer:
                    $wert = $this->objectExists("SELECT * FROM eventguests WHERE eventid=$eventid AND guestname='$username'");
                    if ($wert == true) {
                        // Namen gefunden
                        $_SESSION['eventguest'] = $username;
                        $guestid = $this->sqlselect("SELECT id,guestname FROM eventguests WHERE guestname='$username' AND eventid=$eventid LIMIT 1");
                        $_SESSION['eventid'] = $codes[0]->eventid;
                        // Update Code:
                        $this->updateCodesUsed($codes[0]->id, $guestid[0]->id);

                        $this->sqlInsertUpdateDelete("UPDATE eventguests SET loggedin=1 WHERE guestname='$username'");
                    } else {
                        echo "<p class='hinweis'>Name ist nicht bekannt, bitte wende dich an deinen Event-Veranstalter</p>";
                    }

                } else {
                    echo "<p class='hinweis'>Der Einladungscode existiert nicht. Bitte wende dich an deinen Event Veranstalter.</p>";
                }
            }
        }
    }

    /**
     * Comment
     * 
     * @return void
     */
    function eventSelector() 
    {
        echo "<div class='login'>";
        
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

            // AUSGABE
            echo "<h2>Willkommen!</h2>";
            echo "<p>Um ein Event anzuzeigen, musst du deinen Einladungscode angeben.</p>";
            echo "<form method=post action='event.php'>";
                echo "<input type=text name=username value='$username' placeholder=Benutzername />";
                echo "<input type=text name=eventinvitecode value='$eventinvitecode' placeholder=Einladungscode />";
                echo "<button type=submit name=submit>Absenden</button>";
            echo "</form>";
        } else {
            echo "<div class='login'>Du bist bereits eingeloggt. Um zur Veranstaltung zu wechseln klicke <a href='event.php'>hier</a> <br>"; 
            echo "oder klicke <a href='?eventchange'>hier</a> um dich abzumelden.";
            echo "</div>";
        }
        echo "</div>";
    }

    /**
     * Zählt die Anzahl der Nutzungen des Einladungscodes hoch.
     * 
     * @param int $codeid  Code der Verwendet wird.
     * @param int $guestid Code UserID des Gastes
     * 
     * @return void
     */
    function updateCodesUsed(int $codeid, int $guestid) 
    {
        // Checke ob Eintrag bereits existiert
        $alreadyused = $this->sqlselect("SELECT * FROM eventcodeusage WHERE userid=$guestid");
        if (isset($alreadyused[0]->userid)) {
            $counter = $alreadyused[0]->codeusage + 1;
            $sql = "UPDATE eventcodeusage SET codeusage=$counter WHERE codeid=$codeid AND userid=$guestid";
        } else {
            $counter = 1;
            $sql = "INSERT INTO eventcodeusage (codeid, codeusage, userid) VALUES ($codeid, $counter, $guestid)";
        }
        $this->sqlInsertUpdateDelete($sql);
    }

    /**
     * Löscht den Inhalt der Var eventchange wenn die index.php besucht wird.
     * 
     * @return void
     */
    function changeEvent() 
    {
        if (isset($_GET['eventchange'])) {
            unset($_SESSION['eventid']);
        }
    }

    /**
     * EventTexts Legende
     * 1 : Willkommensmessage
     * 
     * @return void
     */
    function showEvent() 
    {
        if (isset($_SESSION['eventid'])) {
            // event und gast informationen
            //echo "<div class='newFahrt'>";
            $eventid = $_SESSION['eventid'];
            $eventname = $this->sqlselect("SELECT id,eventname FROM eventlist WHERE id=$eventid");
            echo "<h1><a href='event.php'>" .$eventname[0]->eventname . "</a></h1>";
            echo "<h2>Hallo " . $_SESSION['eventguest'] . ", schön dich hier zu sehen!</h2>";
            
            echo "<div class='rightBody'>";
            echo "</div>";
            echo "<div class='innerBody'>";
                $this->showEventMembers($_SESSION['eventid']);
            echo "</div>";

            
        }
    }

    /**
     * Überprüft ob der Nutzer der EventOwner ist.
     * 
     * @param int $eventid EventID
     * 
     * @return void
     */
    function checkEventOwner(int $eventid) 
    {
        $administrator = $this->sqlselect("SELECT * FROM eventlist WHERE id=$eventid LIMIT 1");
        if (isset($_SESSION['username'])) {
            $loggedinuser = $this->getUserID($_SESSION['username']);
            if ($loggedinuser == $administrator[0]->eventowner) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Zeigt die Mitglieder der Veranstaltung an.
     * 
     * @param int $eventid EventID
     * 
     * @return void
     */
    function showEventMembers(int $eventid) 
    {
        
        $memberlist = $this->sqlselect("SELECT * FROM eventguests WHERE eventid=$eventid");
        echo "<h2>Gästeliste</h2>";
        echo "<table class='kontoTable'>";
        echo "<thead>";
            echo "<td>Name</td>";
        if ($this->checkEventOwner($eventid) == true) {
            echo "<td>Mail</td>";
            echo "<td>Eingeloggt</td>";
        }
            
            echo "<td>Zusage</td>";
        echo "</thead>";
        for ($i = 0; $i < sizeof($memberlist); $i++) {
            echo "<tbody>"; 
                echo "<td>";
                    echo $memberlist[$i]->guestname;
                echo "</td>";
            if ($this->checkEventOwner($eventid) == true) {
                    echo "<td>";
                        echo $memberlist[$i]->guestmailaddress;
                    echo "</td>";
                    echo "<td>";
                if ($memberlist[$i]->loggedin == 1) {
                    echo "ja";
                } else {
                    echo "nein";
                }
                    echo "</td>";
            }
                echo "<td>";
            if ($memberlist[$i]->zusage == null) {
                echo "nein";
            } else {
                echo "ja";
            }
                echo "</td>";
            echo "</tbody>";
        }
        echo "</table>";
    }
}

?>