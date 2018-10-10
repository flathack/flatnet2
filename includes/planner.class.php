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
     * Eventseite
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
     * Startseite des Logins für den Event Planner
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
            $this->eventNavigation();
        }
        $this->eventSelector();
        $this->showFooter();
    }

    /**
     * Administrationsstartseite
     * 
     * @return void
     */
    function mainEventAdministration() 
    {
        
        $this->eventNavigation();
        echo "<div class='innerBody'>";

        if (!isset($_SESSION['username'])) {
            echo "<p class='hinweis'>Um diese Seite zu sehen musst du an der Hauptseite angemeldet sein.</p>";
        }

        // Ansicht für Super Administratoren des Event Bereichs
        if (isset($_SESSION['username'])) {
            $this->superAdministration();
        }
        
        // Ansicht für Administratoren eines Events
        if (isset($_SESSION['username'])) {
            if ($this->userHasRight(79, 0)) {
                echo "<p class='hinweis'>Basic Administration ausgeblendet.</p>";
            } else {
                $this->basicEventsAdministration();
            }
        }
        
        echo "</div>";
       

        // Footer
        $this->showFooter();
    }

    /**
     * Comment
     * 
     * @return void
     */
    function superAdministration() 
    {
        if ($this->userHasRight(79, 0)) {
            echo "<div class='newFahrt'>";
            echo "<h2>Event Management</h2>";
            echo "<p>Hier können Events angelegt und verwaltet werden</p>";

            $this->listAllEventAdministrators();
            $this->listAllEvents();

            if (isset($_SESSION['eventid'])) {
                $this->showEventMembers($_SESSION['eventid']);
                $this->showInviteCodes($_SESSION['eventid']);
            }
            echo "</div>"; // newFahrt       END
        }
    }

    /**
     * Administration für Event Administratoren.
     * 
     * @return void
     */
    function basicEventsAdministration() 
    {
        if (isset($_SESSION['eventid'])) {
            if ($this->checkEventOwner($_SESSION['eventid']) == true) {
                echo "<div class='newFahrt'>";
                $eventname = $this->getEventname($_SESSION['eventid']);
                echo "<h2> " . $eventname . " Event Administration</h2>";
                echo "<p>Hier kannst du dein eigenes Event administrieren.</p>";

                $this->addGuests();
                $this->showEventMembers($_SESSION['eventid']);

                echo "</div>";
            }
            
        }
    }

    /**
     * Markiert ein Event als Aktiv
     * 
     * @param int $eventid EventID welches aktiviert werden soll
     * 
     * @return true
     */
    function setActive(int $eventid) 
    {
        // echo "<div class='hinweis'>";
        if (isset($_SESSION['eventid'])) {
            // echo "Derzeit Aktiv: " . $_SESSION['eventid'] . " <br> ";
        }
        // echo "Wird neu gesetzt: $eventid";
        $_SESSION['eventid'] = $eventid;
        // echo "</div>";
    }

    /**
     * Listet alle auf dem Server verfügbaren Events auf.
     * 
     * @return void
     */
    function listAllEvents() 
    {

        
        // Event aus Liste als aktiv markieren:
        if (isset($_GET['active'])) {
            $this->setActive($_GET['active']);
        }
        echo "<div class='separateDivBox'>";
        echo "<h3>Alle Veranstaltungen</h3>";
        $this->createNewEvent();
        $allevents = $this->sqlselect("SELECT * FROM eventlist");

        echo "<table class='kontoTable'>";
        echo "<thead>";
            echo "<td>ID</td><td>Datum</td><td>Name</td><td>Optionen</td>";
        echo "</thead>";
        for ($i = 0; $i < sizeof($allevents); $i++) {
            $currentevent = 0;
            // Checken ob aktuelles Event dem aus der Liste entspricht:
            if (isset($_SESSION['eventid'])) {
                if ($_SESSION['eventid'] == $allevents[$i]->id) {
                    $currentevent = 1;
                    $css = "yellow";
                } else {
                    $css = "";
                }
            } else {
                $css = "";
            }
            
            echo "<tbody id='$css'>";
                echo "<td>" . $allevents[$i]->id . "</td>";
                echo "<td>" . $allevents[$i]->timestamp . "</td>";
                echo "<td>" . $allevents[$i]->eventname . "</td>";
            if ($currentevent == 1) {
                echo "<td>" . "<a class='greenLink'>aktiv</a>" . "</td>";
            } else {
                echo "<td>" . "<a class='buttonlink' href='?active=" . $allevents[$i]->id . "'>als aktiv markieren</a>" . "</td>";
            }
            echo "</tbody>";
        }
        echo "</table>";
        echo "</div>";
    }

    /**
     * Löscht einen Admin von einem Event
     * 
     * @return void
     */
    function deleteAdminOfEvent() 
    {
        if (isset($_GET['deladmin'])) {
            if (isset($_GET['eventid'])) {
                $userid = $_GET['deladmin'];
                $eventid = $_GET['eventid'];
                if (is_numeric($userid) == true AND is_numeric($eventid) == true) {
                    $sql = "DELETE FROM eventadministrators WHERE userid=$userid AND eventid=$eventid LIMIT 1";
                    if ($this->sqlInsertUpdateDelete($sql) == true) {
                        echo "<p class='erfolg'>Administrator gelöscht.</p>";
                    } else {
                        echo "<p class='meldung'>Admin kann nicht gelöscht werden.</p>";
                    }
                }
            }
        }
    }

    /**
     * Löscht einen InviteCode eines Events
     * 
     * @return void
     */
    function deleteCodeOfEvent()
    {
        if (isset($_GET['delInvCode'])) {
            if (isset($_GET['eventid'])) {
                $code = $_GET['delInvCode'];
                $eventid = $_GET['eventid'];
                if (is_numeric($code) == true AND is_numeric($eventid) == true) {
                    $sql = "DELETE FROM eventinvitecodes WHERE eventid=$eventid AND id=$code LIMIT 1";

                    // CODE USAGE LÖSCHEN
                    $codeusages = $this->sqlselect("SELECT * FROM eventcodeusage WHERE codeid=$code");
                    if (isset($codeusages[0]->codeid)) {
                        $sql2 = "DELETE FROM eventcodeusage WHERE codeid=$code";
                        if ($this->sqlInsertUpdateDelete($sql2) == true) {
                            echo "<p class='erfolg'>Code Referenzen gelöscht</p>";
                            $coderefs = true;
                        } else {
                            $coderefs = false;
                            echo "<p class='meldung'>Code Referenzen konnten nicht gelöscht werden.</p>";
                        }
                    } else {
                        echo "<p class='erfolg'>Code Referenzen nicht vorhanden.</p>";
                        $coderefs = true;
                    }
                    
                    if ($coderefs == true) {
                        if ($this->sqlInsertUpdateDelete($sql) == true) {
                            echo "<p class='erfolg'>Code gelöscht.</p>";
                        } else {
                            echo "<p class='meldung'>Code kann nicht gelöscht werden.</p>";
                        }
                    } else {
                        echo "<p class='meldung'>Code kann nicht gelöscht werden, weil die Codeusages nicht gelöscht werden können.</p>";
                    }
                }
            }
        }
    }

    /**
     * Listet alle Administratoren aller Events auf.
     * 
     * @return void
     */
    function listAllEventAdministrators() 
    {
        echo "<div class='separateDivBox'>";
        echo "<h3>Administratoren</h3>";
        
        $this->createNewAdminOfEvent();
        $this->deleteAdminOfEvent();
        $events = $this->sqlselect("SELECT id, eventname FROM eventlist");
        echo "<table class='kontoTable'>";
        for ($i = 0; $i < sizeof($events); $i++) {
            $eventid = $events[$i]->id;
        
            echo "<thead><td> EventID : " .$events[$i]->id. "</td><td>". $this->getEventname($events[$i]->id) ."</td><td></td></thead>";
            $admins = $this->sqlselect("SELECT * FROM eventadministrators WHERE eventid=$eventid");

            for ($j = 0; $j < sizeof($admins); $j++) {
                echo "<tbody><td>UserID : ".$admins[$j]->userid."</td><td>" . $this->getUserName($admins[$j]->userid) . "</td><td><a class='rightRedLink' href='?deladmin=".$admins[$j]->userid."&eventid=" .$events[$i]->id. "'>X</a></td></tbody>";
            }
        }
        echo "</table>";
        echo "</div>";
    }

    /**
     * Erstellt einen neuen Administrator eines Events
     * 
     * @return void
     */
    function createNewAdminOfEvent() 
    {
        
        echo "<div class=''>";
        // SPEICHERUNG
        if (isset($_GET['newuserid']) AND isset($_GET['neweventid'])) {
            $eventid = $_GET['neweventid'];
            $userid = $_GET['newuserid'];

            if (is_numeric($eventid) == true AND is_numeric($userid) == true) {
                // Check ob User existiert:
                $correctuserid = $this->sqlselect("SELECT id,Name FROM benutzer WHERE id=$userid LIMIT 1");
                if (isset($correctuserid[0]->id)) {
                    // Benutzer existiert
                    $newuserid = $correctuserid[0]->id;

                    // Check ob Event existiert:
                    $correcteventid = $this->sqlselect("SELECT id,eventname FROM eventlist WHERE id=$eventid LIMIT 1");
                    if (isset($correcteventid[0]->id)) {
                        // Event existiert
                        $neweventid = $correcteventid[0]->id;

                        $sql = "INSERT INTO eventadministrators (eventid, userid) VALUES ('$neweventid','$newuserid')";
                        if ($this->sqlInsertUpdateDelete($sql) == true) {
                            echo "<p class='erfolg'>Administrator gespeichert</p>";
                        } else {
                            echo "<p class='meldung'>Fehler beim speichern des neuen Administrators</p>";
                        }
                    } else {
                        echo "<p class='meldung'>Das Event existiert nicht.</p>";
                    }
                } else {
                    echo "<p class='meldung'>Benutzer existiert nicht.</p>";
                }


            }
        }

        // AUSGABE
        $eventlist = $this->sqlselect("SELECT * FROM eventlist");
        $userlist = $this->sqlselect("SELECT * FROM benutzer");

        echo "<form method=get>";
        echo "<select name=neweventid>";
        for ($i = 0; $i < sizeof($eventlist); $i++) {
            echo "<option value=".$eventlist[$i]->id.">".$eventlist[$i]->eventname."</option>";
        }
        echo "</select>";

        echo "<select name=newuserid>";
        for ($j = 0; $j < sizeof($userlist); $j++) {
            echo "<option value=".$userlist[$j]->id.">".$userlist[$j]->Name."</option>";
        }
        echo "</select>";
        echo "<button type=submit>OK</button>";
        echo "</form>";

        echo "<div>";
    }

    /**
     * Erstellt ein neues Event
     * 
     * @return void
     */
    function createNewEvent() 
    {
        // SPEICHERUNG
        if (isset($_POST['eventname'])) {
            $eventname = strip_tags(stripslashes($_POST['eventname']));
            $eventname = str_replace(' ', '-', $eventname);
            $neweventname = preg_replace('/[^A-Za-z0-9\-]/', '', $eventname);
            if (strlen($neweventname) > 5) {
                
                $sql = "INSERT INTO eventlist (eventname) VALUES ('$neweventname')";
                if ($this->sqlInsertUpdateDelete($sql) == true) {
                    echo "<p class='erfolg'>Erstellt.</p>";
                } else {
                    echo "<p class='meldung'>Fehler beim speichern ($neweventname)</p>";
                }
            } else {
                echo "<p class='hinweis'>Name muss mehr als 5 Zeichen haben.</p>";
            }
        }

        // AUSGABE
        echo "<div class=''>";
        echo "<form method=post />";
        echo "<input type=text name=eventname placeholder=Event-Name required />";
        echo "<button type=submit>OK</button>";
        echo "</form>";
        echo "</div>";
    }

    /**
     * Gibt den Namen des Events zurück.
     * 
     * @param int $eventid ID des Events
     * 
     * @return string
     */
    function getEventname(int $eventid) 
    {
        $eventname = $this->sqlselect("SELECT id, eventname FROM eventlist WHERE id=$eventid LIMIT 1");

        return $eventname[0]->eventname;
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
                
            if ($this->userHasRight(36, 0) == true) {
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
        // AUSGABE
        echo "<div class='innerBody'>";
            echo "<p>EventPlanner by Steven Schödel (c) Version 9.10.2018 | ";
            echo "<a href='/flatnet2/informationen/impressum.php'>Impressum</a>";

            echo "</p>";
        echo "</div>";
    }

    /**
     * Stellt die Navigation dar.
     * 
     * @return void
     */
    function eventNavigation() 
    {
        echo "<ul class='finanzNAV'>";
        if (isset($_SESSION['username'])) {
            $this->eventAdministration();
        }
        if (isset($_SESSION['eventid'])) {
            echo "<li><a href='event.php'>" .$this->getEventname($_SESSION['eventid']). "</a></li>";
        }
        
        echo "<li><a href='index.php?eventchange'>Logout</a></li>";
        echo "</ul>";
        
    }
    /**
     * Zeigt einen Link an um die Event Administration durchzuführen
     * 
     * @return void
     */
    function eventAdministration() 
    {   
        echo "<li><a href='index.php'>Login</a></li>";
        echo "<li><a href='administration.php'>Administration</a></li>";
    }

    /**
     * Fügt Gäste zum Event hinzu
     * 
     * @return void
     */
    function addGuests() 
    {
        // SPEICHERUNG
        if (isset($_POST['guestname'])) {
            $guestname = strip_tags(stripslashes($_POST['guestname']));
            $guestname = str_replace(' ', '-', $guestname);
            $newguestname = preg_replace('/[^A-Za-z0-9\-]/', '', $guestname);
            $eventid = $_SESSION['eventid'];

            $mailadress = strip_tags($_POST['guestmail']);
            if (strlen($newguestname) > 2) {
                $sql = "INSERT INTO eventguests (guestname, guestmailaddress, eventid) VALUES ('$newguestname','$mailadress','$eventid')";
                if ($this->sqlInsertUpdateDelete($sql) == true) {
                    echo "<p class='erfolg'>Erfolgreich</p>";
                } else {
                    echo "<p class='meldung'>Gast konnte nicht hinzugefügt werden. Es gab einen Fehler beim speichern.</p>";
                }
            } else {
                echo "<p class='hinweis'>Der Name des Gastes muss mehr als zwei Zeichen beinhalten.</p>";
            }
        }
        
        // AUSGABE
        echo "<div class=''>";
        echo "<form method=post>";
        echo "<input type=text name=guestname placeholder=Gastname required />";
        echo "<input type=text name=guestmail placeholder=Mailadresse />";
        echo "<button type=submit>OK</button>";
        echo "</form>";
        echo "</div>";
    }

    /**
     * Fügt einen neuen Code zu einem Event hinzu.
     * 
     * @param int $eventid ID des Events
     * 
     * @return void
     */
    function addInviteCode(int $eventid) 
    {
        
        echo "<div class=''>";
        // SPEICHERUNG
        if (isset($_POST['code'])) {
            $code = strip_tags(stripslashes($_POST['code']));
            $code = str_replace(' ', '-', $code);
            $newcode = preg_replace('/[^A-Za-z0-9\-]/', '', $code);

            if (strlen($newcode) > 5) {
                // CHECK IF CODE ALREADY IN USE:
                $codealready = $this->sqlselect("SELECT * FROM eventinvitecodes WHERE eventinvitecode='$newcode' AND eventid=$eventid");
                if (isset($codealready[0]->id)) {
                    echo "<p class='meldung'>Der Code existiert bereits.</p>";
                } else {
                    $sql = "INSERT INTO eventinvitecodes (eventid, eventinvitecode) VALUES ('$eventid','$newcode')";
                    if ($this->sqlInsertUpdateDelete($sql) == true) {
                        echo "<p class='erfolg'>Erfolgreich</p>";
                    } else {
                        echo "<p class='meldung'>Code konnte nicht hinzugefügt werden. Es gab einen Fehler beim speichern.</p>";
                    }
                }
                
            } else {
                echo "<p class='hinweis'>Der Code muss mindestens 5 Zeichen lang sein.</p>";
            }
        }

        // AUSGABE
        echo "<form method=post>";
        echo "<input type=text name=code placeholder=Code required />";
        echo "<button type=submit>OK</button>";
        echo "</form>";
        echo "</div>";

    }

    /**
     * Holt den Namen des Gastes
     * 
     * @return void
     */
    function getGuestName(int $guestid) 
    {
        $guestname = $this->sqlselect("SELECT id,guestname FROM eventguests WHERE id=$guestid LIMIT 1");

        return $guestname[0]->guestname;
    }

    /**
     * Zeigt alle Einladungscodes für die Veranstaltung an.
     * 
     * @param int $eventid ID des Events
     * 
     * @return void
     */
    function showInviteCodes(int $eventid)
    {
        echo "<div class='separateDivBox'>";
        
        echo "<h3>Codeliste</h3>";
        $this->addInviteCode($eventid);
        $this->deleteCodeOfEvent();
        $codeslist = $this->sqlselect("SELECT * FROM eventinvitecodes WHERE eventid=$eventid");
        
        echo "<table class='kontoTable'>";
        echo "<thead><td>Code</td><td>Optionen</td></thead>";
        for ($i = 0; $i < sizeof($codeslist); $i++) {
            echo "<tbody><td>".$codeslist[$i]->eventinvitecode."</td><td><a href='?delInvCode=".$codeslist[$i]->id."&eventid=$eventid'>X</a></td></tbody>";
            $codeid = $codeslist[$i]->id;
            $usages = $this->sqlselect("SELECT * FROM eventcodeusage WHERE codeid=$codeid");
            if (isset($usages[0]->codeid)) {
                echo "<tbody id='today'><td colspan=2>Nutzungen:</td></tbody>";
            }
            for ($j = 0; $j < sizeof($usages);$j++) {
                echo "<tbody>";
                echo "<td>".$this->getGuestName($usages[$j]->userid)."</td>";
                echo "<td>Anzahl: ".$usages[$j]->codeusage."</td>";
                echo "</tbody>";
            }
        }
        echo "</table>";
        echo "</div>";
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
     * LoginFeld für die Gäste der Events.
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
            $eventname = $this->getEventname($_SESSION['eventid']);
            echo "<h1><a href='event.php'>" .$eventname . "</a></h1>";
            echo "<h2>Hallo " . $_SESSION['eventguest'] . ", schön dich hier zu sehen!</h2>";
            
            echo "<div class='rightBody'>";
                $this->showSmallGuestList();
            echo "</div>";
            echo "<div class='innerBody'>";
                $this->askUserForZusage($_SESSION['eventid']);
                //$this->showEventMembers($_SESSION['eventid']);
            echo "</div>";

            
        }
    }

    /**
     * Fragt den Nutzer ob er zusagen möchte.
     * 
     * @param int $eventid ID des Events
     * 
     * @return void
     */
    function askUserForZusage(int $eventid) 
    {
        // CHECK IF USER HASNT ZUGESAGT YET
        echo "<div class='separateDivBox'>";
        echo "<p>Hast du schon zugesagt?</p>";

        echo "</div>";
    }

    /**
     * Zeigt eine abgespeckte Gästeliste an.
     * 
     * @return void
     */
    function showSmallGuestList() 
    {
        $eventid = $_SESSION['eventid'];
        $memberlist = $this->sqlselect("SELECT * FROM eventguests WHERE eventid=$eventid");

        echo "<div>";
        echo "<h3>Gästeliste</h3>";
        echo "<ul>";
        for ($i = 0; $i < sizeof($memberlist); $i++) {
            echo "<li>".$memberlist[$i]->guestname."</li>";
        }
        echo "</ul>";
        echo "</div>";
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
        $administrator = $this->sqlselect("SELECT * FROM eventadministrators WHERE eventid=$eventid");
        $found = 0;
        if (isset($_SESSION['username'])) {
            if ($this->userHasRight(78, 0) == true) {
                // Benutzer ist SuperAdmin, daher darf er alles.
                return true;
                $found = 1;
            } else {
                $loggedinuser = $this->getUserID($_SESSION['username']);
                for ($i = 0; $i < sizeof($administrator); $i++) {
                    if ($loggedinuser == $administrator[$i]->userid) {
                        return true;
                        $found = 1;
                    }
                }
    
                // Wenn kein EventAdministrator gefunden wird, false zurückgeben:
                if ($found == 0) {
                    return false;
                }
            }
            
        } else {
            return false;
        }
    }

    /**
     * Löscht einen Gast aus einem Event
     * 
     * @return void
     */
    function deleteEventGuest()
    {
        if (isset($_GET['delUser']) AND isset($_GET['eventid'])) {
            $userid = $_GET['delUser'];
            $eventid = $_GET['eventid'];

            if (is_numeric($userid) == true AND is_numeric($eventid) == true) {
                if ($this->checkEventOwner($eventid) == true) {

                    $sql = "DELETE FROM eventguests WHERE id=$userid AND eventid=$eventid LIMIT 1";
                    if ($this->sqlInsertUpdateDelete($sql) == true) {
                        echo "<p class='erfolg'>Der Gast wurde erfolgreich gelöscht.</p>";

                    } else {
                        echo "<p class='meldung'>Gast konnte nicht gelöscht werden.</p>";
                    }
                } else {
                    echo "<p class='meldung'>Du hast nicht die Berechtigung einen Gast zu löschen.</p>";
                }
            }
        }
    }

    /**
     * Setzt die ZusageOption eines Gastes auf Zusage.
     * 
     * @return void
     */
    function zusageGuest()
    {

    }

    /**
     * Setzt die ZusageOption eines Gastes auf Absage.
     * 
     * @return void
     */
    function absageGuest()
    {

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
        if ($this->checkEventOwner($eventid) == true) { 
            $owner = true;
        } else {
            $owner = false;
        }

        if ($owner == true) { 
            $this->deleteEventGuest();
            $this->zusageGuest();
            $this->absageGuest();
        }
        
        echo "<h3>Gästeliste</h3>";
        if ($owner == true) { 
            $this->addGuests();
        }
        echo "<table class='kontoTable'>";
        echo "<thead>";
        echo "<td>Name</td>";
        // RESTRICTED START
        if ($owner == true) { 
            echo "<td>Mail</td>";
            echo "<td>Eingeloggt</td>";
        } 
        // RESTRICTED END

        echo "<td>Zusage</td>";

        // RESTRICTED START
        if ($owner == true) { 
            echo "<td>Options</td>";
        } 
        // RESTRICTED END
        echo "</thead>";
        for ($i = 0; $i < sizeof($memberlist); $i++) {
            echo "<tbody>"; 
                echo "<td>";
                    echo $memberlist[$i]->guestname;
                echo "</td>";

            // RESTRICTED START
            if ($owner == true) {
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
            // RESTRICTED END
            
                echo "<td>";
            if ($memberlist[$i]->zusage == null) {
                echo "nein";
            } else {
                echo "ja";
            }
                echo "</td>";
            
            // RESTRICTED START
            if ($owner == true) {
                echo "<td>";
                    echo "<a href='?zusageUser=".$memberlist[$i]->id."&eventid=".$memberlist[$i]->eventid."'>Zusage</a>";
                    echo "<a class='rightRedLink'href='?delUser=".$memberlist[$i]->id."&eventid=".$memberlist[$i]->eventid."'>X</a>";
                echo "</td>";
            }
            // RESTRICTED END

            echo "</tbody>";
        }
        echo "</table>";
    }
}

?>