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
        if (!isset($_SESSION['eventid']) OR !isset($_SESSION['eventguest'])) {
            echo "<div class='login'>";
            echo "<h2>Leider ist etwas schief gelaufen!</h2>";
            echo "Du hast kein Event ausgewählt oder du bist nicht als Gast dieses Events eingeloggt.<br> <a href='index.php'>Bitte logge dich vorher ein.</a>";
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
        $this->changeGuest();
        // Administration
        if (isset($_SESSION['username'])) {
            $this->eventNavigation();
        } else {
            echo "<a class='grayLink' href='../index.php'>Admin</a>";
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
        echo "<div class=''>";

        if (!isset($_SESSION['username'])) {
            $this->infoMessage("Um diese Seite zu sehen musst du an der Hauptseite angemeldet sein.");
        }

        // Ansicht für Super Administratoren des Event Bereichs
        if (isset($_SESSION['username'])) { 
            // ChangeEvent
            $this->changeEvent();
            $this->changeGuest();
            $this->superAdministration();
        }
        echo "</div>";
       

        // Footer
        $this->showFooter();
    }

    /**
     * Zeigt die Hauptseite der Administration an.
     * 
     * @return void
     */
    function superAdministration() 
    {
        echo "<div class='newFahrt'>";
        echo "<h2>Event Management</h2>";
        echo "<p>Hier können Events angelegt und verwaltet werden</p>";

        $this->listAllEventAdministrators();
        $this->listAllEvents();

        if (isset($_SESSION['eventid'])) {
            $this->showManageCountdowns($_SESSION['eventid']);
            $this->showInviteCodes($_SESSION['eventid']);
            $this->showEventMembers($_SESSION['eventid']);
        }
        echo "</div>"; // newFahrt       END
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
        if ($this->checkEventOwner($eventid) == true) {
            $_SESSION['eventid'] = $eventid;
        } else {
            echo "<p class='meldung'>Du hast keine Berechtigung für diese Aktion.</p>";
        }
        
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
        echo "<h3><a name='allevents'>Alle Veranstaltungen</a></h3>";
        $this->createNewEvent();

        $allevents = $this->sqlselect("SELECT * FROM eventlist ORDER BY eventname,id");
        
        echo "<table class='kontoTable'>";
        echo "<thead>";
            echo "<td>ID</td><td>Erstelldatum</td><td>Name</td><td>Optionen</td>";
        echo "</thead>";
        for ($i = 0; $i < sizeof($allevents); $i++) {
            // Check ob der Admin das Event sehen darf.
            if ($this->checkEventOwner($allevents[$i]->id) == true) {
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
                    echo "<td>"; 
                    echo "<a class='greenLink'>aktiv</a>";
                    echo "<a class='buttonlink' href='/flatnet2/admin/control.php?action=3&table=eventlist&id=" . $allevents[$i]->id . "#angezeigteID'>Edit</a>";
                    echo "</td>";
                } else {
                    echo "<td>"; 
                    echo "<a class='buttonlink' href='?active=" . $allevents[$i]->id . "#allevents'>als aktiv markieren</a>"; 
                    echo "<a class='buttonlink' href='/flatnet2/admin/control.php?action=3&table=eventlist&id=" . $allevents[$i]->id . "#angezeigteID'>Edit</a>";
                    echo "</td>";
                }
                echo "</tbody>";
            }
            
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
     * Benötigt das Recht 79
     * 
     * @return void
     */
    function listAllEventAdministrators() 
    {
        if ($this->userHasRight(79, 0) == true) {
            echo "<div class='separateDivBox'>";
            echo "<h3><a name='admins'>Administratoren</a></h3>";
            
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
                            $this->erfolgMessage("Administrator gespeichert");
                        } else {
                            $this->errorMessage("Fehler beim speichern des neuen Administrators");
                        }
                    } else {
                        $this->errorMessage("Das Event existiert nicht.");
                    }
                } else {
                    $this->errorMessage("Benutzer existiert nicht.");
                }


            }
        }

        // AUSGABE
        $eventlist = $this->sqlselect("SELECT * FROM eventlist");
        $userlist = $this->sqlselect("SELECT * FROM benutzer");

        echo "<form method=get action='administration.php#admins'>";
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
            if ($this->userHasRight(79, 0) == true) {
                $neweventname = $this->checkString($_POST['eventname']);
                if (strlen($neweventname) > 5) {
                    if ($this->objectExists("SELECT id,eventname FROM eventlist WHERE eventname='$neweventname'") == false) {
                        $sql = "INSERT INTO eventlist (eventname) VALUES ('$neweventname')";
                        if ($this->sqlInsertUpdateDelete($sql) == true) {
                            $this->erfolgMessage("Event $neweventname erstellt");
                        } else {
                            $this->errorMessage("Fehler beim speichern ($neweventname)");
                        }
                    } else {
                        $this->infoMessage("Eine Veranstaltung mit dem Namen $neweventname existiert bereits.");
                    }
                    
                } else {
                    $this->infoMessage("Name muss mehr als 5 Zeichen haben.");
                }
            } else {
                $this->infoMessage("Du benötigst Super Administration Rechte für diese Aktion.");
            }
            
        }

        // AUSGABE
        if ($this->userHasRight(79, 0) == true) {
            echo "<div class=''>";
            echo "<form method=post action='administration.php#allevents' />";
            echo "<input type=text name=eventname placeholder=Event-Name required />";
            echo "<button type=submit>OK</button>";
            echo "</form>";
            echo "</div>";
        }
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
        echo "<meta name='viewport' content='width=500, initial-scale=0.7'>";

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
            echo "<p><strong><a href='/flatnet2/usermanager/usermanager.php'>" . $_SESSION['username'] . "</a></strong> | ";
            echo "<a href='/flatnet2/includes/logout.php'> Abmelden </a></p>";
            echo "<ul>";
                
            if ($this->userHasRight(36, 0) == true) {
                echo "<li><a href='/flatnet2/uebersicht.php'>&Uuml;bersicht </a></li>";
            } else {
                echo "<li><a href='/flatnet2/informationen/impressum.php'>Impressum</a></li>";
            }
            echo "</ul>";
        } else {
            echo "<p></p>";
        }
        
        echo "</div>";

        if (isset($_SESSION['username'])) {
            $this->showNaviLinks();
        }

        // Überschrift
        echo "<div id='ueberschrift'>";
            echo "<h1><a href='/flatnet2/planner/index.php'>Event Planner</a></h1>";
        echo "</div>";

        
        

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
        echo "<div class='footer'>";
            echo "<p>EventPlanner by Steven Schödel (c) Version 01.072019 | ";
            echo "<a href='/flatnet2/informationen/impressum.php'>Impressum</a> | ";
            echo "<a href='/index.php'>Login</a>";
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
        if (isset($_SESSION['eventid']) AND isset($_SESSION['eventguest'])) {
            echo "<li><a href='event.php'>" .$this->getEventname($_SESSION['eventid']). "</a></li>";
        }
        if (isset($_SESSION['eventid'])) {
            echo "<li><a href='index.php?eventchange'>Logout</a></li>";
        }
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
        if (isset($_SESSION['eventguest'])) {
            echo "<li><a href='?guestlogout'>Gast ausloggen</a></li>";
        }
        
    }

    /**
     * Fügt Gäste zum Event hinzu
     * 
     * @return void
     */
    function addGuests() 
    {
        // SPEICHERUNG
        if (isset($_POST['guestname']) AND isset($_POST['guestcount']) AND isset($_POST['fullname'])) {
            $newguestname = $this->checkString($_POST['guestname']);
            $eventid = $_SESSION['eventid'];

            if (is_numeric($_POST['guestcount'])) {
                $anzahl = $_POST['guestcount'];
                $mailadress = strip_tags($_POST['guestmail']);
                $fullname = strip_tags($_POST['fullname']);
                if (strlen($newguestname) > 2) {
                    if ($this->objectExists("SELECT * FROM eventguests WHERE guestname='$newguestname' AND eventid=$eventid") == false) {
                        $sql = "INSERT INTO eventguests (guestname, guestmailaddress, eventid, anzahl, fullname) VALUES ('$newguestname','$mailadress','$eventid', '$anzahl','$fullname')";
                        if ($this->sqlInsertUpdateDelete($sql) == true) {
                            $this->erfolgMessage("$newguestname erfolgreich hinzugefügt.");
                        } else {
                            $this->errorMessage("Gast konnte nicht hinzugefügt werden. Es gab einen Fehler beim speichern.");
                        }
                    } else {
                        $this->infoMessage("Ein Gast mit dem Namen existiert bereits.");
                    }
                    
                } else {
                    $this->infoMessage("Der Name des Gastes muss mehr als zwei Zeichen beeinhalten.");
                }
            }            
        }
        
        // AUSGABE
        echo "<div class=''>";
        echo "<form method=post action='administration.php#gaesteliste'>";
        echo "<input type=text name=guestname placeholder=Login-Token* required />";
        echo "<input type=text name=fullname placeholder=Anzeigename* required />";
        echo "<input type=number name=guestcount placeholder=Anzahl value=1 required />";
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
            $newcode = $this->checkString($_POST['code']);

            if (strlen($newcode) > 5) {
                // CHECK IF CODE ALREADY IN USE:
                if ($this->objectExists("SELECT * FROM eventinvitecodes WHERE eventinvitecode='$newcode' AND eventid=$eventid") == true) {
                    $this->infoMessage("Der Code ($newcode) existiert bereits und kann nicht hinzugefügt werden.");
                } else {
                    $sql = "INSERT INTO eventinvitecodes (eventid, eventinvitecode) VALUES ('$eventid','$newcode')";
                    if ($this->sqlInsertUpdateDelete($sql) == true) {
                        $this->erfolgMessage("Erfolgreich");
                    } else {
                        $this->errorMessage("Code konnte nicht hinzugefügt werden. Es gab einen Fehler beim speichern.");
                    }
                }
                
            } else {
                $this->infoMessage("Der Code muss mindestens 5 Zeichen lang sein.");
            }
        }

        // AUSGABE
        echo "<form method=post action='#codelist'>";
        echo "<input type=text name=code placeholder=Code required />";
        echo "<button type=submit>OK</button>";
        echo "</form>";
        echo "</div>";

    }

    /**
     * Holt den Namen des Gastes
     * 
     * @param int $guestid GastID
     * 
     * @return void
     */
    function getGuestName(int $guestid) 
    {
        $guestname = $this->sqlselect("SELECT id,guestname,fullname FROM eventguests WHERE id=$guestid LIMIT 1");
        if (isset($guestname[0]->fullname)) {
            if (strlen($guestname[0]->fullname) > 1) {
                return $guestname[0]->fullname;
            } else {
                return "Gast " . $guestname[0]->id;
            }
            
        } else {
            return "Gast";
        }
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
        
        echo "<h3><a name='codelist'>Codeliste</a></h3>";
        $this->addInviteCode($eventid);
        $this->deleteCodeOfEvent();
        $codeslist = $this->sqlselect("SELECT * FROM eventinvitecodes WHERE eventid=$eventid");
        
        echo "<table class='kontoTable'>";
        echo "<thead><td>Code</td><td id='width140px'>Optionen</td></thead>";
        for ($i = 0; $i < sizeof($codeslist); $i++) {
            echo "<tbody><td>".$codeslist[$i]->eventinvitecode."</td><td><a href='?delInvCode=".$codeslist[$i]->id."&eventid=$eventid#codelist'>X</a></td></tbody>";
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
     * Zeigt alle Countdowns an
     * 
     * @param int $eventid EventID
     * 
     * @return void
     */
    function showManageCountdowns(int $eventid) 
    {
        echo "<div class='separateDivBox'>";
        echo "<h3><a name='countdowns'>Countdowns</a></h3>";
        $this->addCountdown($eventid);
        $this->deleteCountdown();

        $cdlist = $this->sqlselect("SELECT * FROM eventcountdowns WHERE eventid=$eventid ORDER BY enddate");
        echo "<table class='kontoTable'>";
        echo "<thead><td>Enddate</td><td id='small'>Optionen</td></thead>";

        for ($i = 0; $i < sizeof($cdlist); $i++) {
            echo "<tbody>"; 
            echo "<td>".$cdlist[$i]->enddate."</td><td><a href='?delCD=".$cdlist[$i]->id."&eventid=$eventid#countdowns'>X</a></td>"; 
            
            echo "</tbody>";
        }
        echo "</table>";
        echo "</div>";
    }

    /**
     * Fügt einen neuen Countdown zu einem Event hinzu.
     * 
     * @param int $eventid ID des Events
     * 
     * @return void
     */
    function addCountdown(int $eventid) 
    {
        
        echo "<div class=''>";
        // SPEICHERUNG
        if (isset($_POST['Date'])) {
            $date = $_POST['Date'];
            if (isset($_POST['Date'])) {
                if ($this->objectExists("SELECT * FROM eventcountdowns WHERE enddate='$date' AND eventid=$eventid") == true) {
                    $this->infoMessage("Der Countdown ($date) existiert bereits und kann nicht hinzugefügt werden.");
                } else {
                    $sql = "INSERT INTO eventcountdowns (enddate, eventid) VALUES ('$date','$eventid')";
                    if ($this->sqlInsertUpdateDelete($sql) == true) {
                        $this->erfolgMessage("Erfolgreich");
                    } else {
                        $this->errorMessage("Countdown konnte nicht hinzugefügt werden. Es gab einen Fehler beim speichern.");
                    }
                }
                
            } else {
                $this->infoMessage("Das angegebene Datum ist ungültig.");
            }
        }

        // AUSGABE
        echo "<form method=post action='#countdowns'>";
        echo "<input type=date name=Date placeholder=Date required />";
        echo "<button type=submit>OK</button>";
        echo "</form>";
        echo "</div>";

    }

    /**
     * Löscht einen Countdown eines Events
     * 
     * @return void
     */
    function deleteCountdown()
    {
        if (isset($_GET['delCD'])) {
            if (isset($_GET['eventid'])) {
                $dateid = $_GET['delCD'];
                $eventid = $_GET['eventid'];
                if (is_numeric($eventid) == true AND is_numeric($dateid) == true) {
                    $sql = "DELETE FROM eventcountdowns WHERE eventid=$eventid AND id=$dateid LIMIT 1";
                    if ($this->sqlInsertUpdateDelete($sql) == true) {
                        echo "<p class='erfolg'>Datum gelöscht.</p>";
                    } else {
                        echo "<p class='meldung'>Datum kann nicht gelöscht werden.</p>";
                    }
                }
            }
        }
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
                    $sql = "SELECT * FROM eventguests WHERE eventid=$eventid AND guestname='$username' LIMIT 1";
                    if ($this->objectExists($sql) == true) {
                        // Namen gefunden
                        $guestid = $this->sqlselect($sql);
                        $_SESSION['eventid'] = $codes[0]->eventid;
                        $_SESSION['eventguest'] = $guestid[0]->id;
                        // Update Code:
                        $this->updateCodesUsed($codes[0]->id, $guestid[0]->id);

                        $this->sqlInsertUpdateDelete("UPDATE eventguests SET loggedin=1 WHERE guestname='$username'");
                    } else {
                        $this->infoMessage("Name ist nicht bekannt, bitte wende dich an deinen Event-Veranstalter");
                    }

                } else {
                    $this->infoMessage("Der Einladungscode existiert nicht. Bitte wende dich an deinen Event Veranstalter.");
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
        echo "<div class='eventlogin'>";
        
        if (!isset($_SESSION['eventid']) OR !isset($_SESSION['eventguest'])) {
            
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
                echo "<input type=text name=username value='$username' placeholder=Benutzername required />";
                echo "<input type=text name=eventinvitecode value='$eventinvitecode' placeholder=Einladungscode required />";
                echo "<button type=submit name=submit>Absenden</button>";
            echo "</form>";
        } else {
            echo "<div class=''>Du bist bereits eingeloggt. Um zur Veranstaltung zu wechseln klicke <a href='event.php'>hier</a> <br>"; 
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
     * Löscht den Inhalt der Var eventchange
     * 
     * @return void
     */
    function changeEvent() 
    {
        if (isset($_GET['eventchange'])) {
            unset($_SESSION['eventid']);
            unset($_SESSION['eventguest']);
        }
    }

    /**
     * Löscht den Inhalt von Variable eventguest
     * 
     * @return void
     */
    function changeGuest() 
    {
        if (isset($_GET['guestlogout'])) {
            unset($_SESSION['eventguest']);
        }
    }

    /**
     * Loggt den Admin als Nutzer ein.
     * 
     * @return void
     */
    function setGuest() 
    {
        if (isset($_GET['setGuest'])) {
            if (is_numeric($_GET['setGuest']) == true) {
                $_SESSION['eventguest'] = $_GET['setGuest'];
            }
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
        if (isset($_SESSION['eventid']) AND isset($_SESSION['eventguest'])) {
            // event und gast informationen
            //echo "<div class='newFahrt'>";
            echo "<div class='innerBody'>";
            $eventname = $this->getEventname($_SESSION['eventid']);
            echo "<h1><a href='event.php'>" .$eventname . "</a></h1>";
            echo "<h2>Willkommen  " . $this->getGuestName($_SESSION['eventguest']) . "</h2>";
            echo "</div>";
            
            echo "<div class='rightBody'>";
                $this->askUserForZusage($_SESSION['eventid']);
                $this->askUserForCount($_SESSION['eventid']);
                $this->showSmallGuestList();
            echo "</div>";
            echo "<div class='innerBody'>";
                $this->showCountdowns($_SESSION['eventid']);
                $this->showBlogMessages($_SESSION['eventid']);
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
        if (isset($_SESSION['eventguest'])) {
            echo "<div class='newFahrt'>";
            $currentid = $_SESSION['eventguest'];
            $userinfo = $this->sqlselect("SELECT * FROM eventguests WHERE id='$currentid' AND eventid=$eventid");
            
            // CHECK IF USER HASNT ZUGESAGT YET
            if ($userinfo[0]->zusage == 1) { 

                echo "<div class=''>";
            } else {
                echo "<div class=''>";
            }
            
            $this->absageGuest();
            $this->zusageGuest();
            
            
            if ($userinfo[0]->zusage == 1) { 
                echo "<p>Vielen Dank, dass du zugesagt hast!</p>";
                // echo "<a class='grayLink'>Du hast zugesagt</a>"; 
            } else {
                 echo "<p class=''>Bitte Zusagen!</p>";
                echo "<a class='greenLink' href='?zusageUser=".$userinfo[0]->id."'>Zusagen</a>"; 
            }
            
            if ($userinfo[0]->zusage == 1) { 
                echo "<a class='redLink' href='?absageUser=".$userinfo[0]->id."'>Absagen</a>"; 
            } else {
                // echo "<a class='grayLink'>Du nimmst nicht an der Veranstaltung teil.</a>";
            }
            
    
            echo "</div></div>";
        }
        
    }

    /**
     * Fragt den Nutzer wie viele Personen erscheinen werden.
     * 
     * @param int $eventid ID des Events
     * 
     * @return void
     */
    function askUserForCount(int $eventid) 
    {
        // CHECK IF USER HASNT ZUGESAGT YET
        // echo "<div class='newFahrt'>";
        // echo "<p>Bitte teilen uns mit, mit wie vielen Personen du bei der Veranstaltung erscheinst.</p>";

        // echo "</div>";
    }

    /**
     * Zeigt die Nachrichten zu diesem Event an.
     * 
     * @param int $eventid ID des Events
     * 
     * @return void
     */
    function showBlogMessages(int $eventid) 
    {
        // CHECK IF USER HASNT ZUGESAGT YET
        echo "<div class=''>";

        $entries = $this->sqlselect("SELECT * FROM eventtexts WHERE eventid=$eventid ORDER BY sort DESC");
        if ($this->checkEventOwner($_SESSION['eventid']) == true) {
            $this->createNewBlogMessage($eventid);
            $this->editBlogMessage($eventid);
            $this->delBlogMessage($eventid);
        }
        
        echo "<table class='eventPost'>";
        # echo "<thead><td colspan=3></td></thead>";
        for ($i = 0;$i < sizeof($entries); $i++) {
            echo "<thead id='small'>"; 
            echo "<td colspan=1>Autor - ".$this->getUserName($entries[$i]->author)."</td>";
            echo "<td colspan=2>Datum - ". $entries[$i]->timestamp ."";
            // echo "<td>";
            if ($this->checkEventOwner($eventid) == true) {
                echo "<a class='rightGreenLink' href='?blogedit=".$entries[$i]->id."#blog'>Edit</a>";
            }
            echo "</td>";
            echo "</thead>";
            echo "<tbody>";
                echo "<td colspan=3><div id='minheight'>"; 
                    echo $entries[$i]->text;
                echo "</div></td>";
            echo "</tbody>";
        }
        echo "</table>";

        echo "</div>";
    }

    /**
     * Erstellt einen neuen Blog Eintrag
     * 
     * @param int $eventid EventID
     * 
     * @return void
     */
    function createNewBlogMessage(int $eventid) 
    {
        // SPEICHERUNG
        if (isset($_POST['blogtext'])) {
            $text = $_POST['blogtext'];
            $counttext = $this->checkString($text);

            if (strlen($counttext) > 10) {
                if (isset($_SESSION['username'])) {
                    $author = $this->getUserID($_SESSION['username']);
                    $max = $this->sqlselect("SELECT sort,max(sort) as max FROM eventtexts WHERE eventid=$eventid");
                    $count = $max[0]->max + 1;
                    $sql = "INSERT INTO eventtexts (eventid, text, sort, author) VALUES ('$eventid','$text','$count','$author')";
                    if ($this->sqlInsertUpdateDelete($sql) == true) {
                        $this->erfolgMessage("Erfolgreich");
                    } else {
                        $this->errorMessage("Speichern nicht möglich.");
                    }
                    // $this->infoMessage("$count : $sql");
                } else {
                    $this->infoMessage("Du kannst nur Einträge erstellen wenn du angemeldet bist.");
                }
            } else {
                $this->infoMessage("Text muss mind. 10 Zeichen beeinhalten. Es waren nur " . strlen($counttext). " Zeichen.");
            }
        }

        if (isset($_GET['newblog'])) {
            echo "<div class='separateDivBox'>";
            echo "<h3>Neu</h3>";
            echo "<form method=post action='#blog'>";
            if (isset($_POST['blogtext'])) {
                $blogtext = $_POST['blogtext'];
            } else {
                $blogtext = "";
            }
            echo "<textarea class='ckeditor' name=blogtext>$blogtext</textarea><br>";
            echo "<a class='rightRedLink' href='?'>schließen</a>";
            echo "<button class='rightGreenLink' type=submit>Speichern</button><br>";
            echo "</form>";
            echo "</div>";
        }

        // AUSGABE
        echo "<div class=''>";
        echo "<a class='buttonlink' href='?newblog#blog'>NEU</a>";
        echo "</div>";
    }

    /**
     * Erstellt einen neuen Blog Eintrag
     * 
     * @param int $eventid EventID
     * 
     * @return void
     */
    function editBlogMessage(int $eventid) 
    {
        // SPEICHERUNG
        if (isset($_POST['editblogentry']) AND isset($_POST['editblogid'])) {
            if ($this->checkEventOwner($eventid) == true) {
                $text = $_POST['editblogentry'];
                $counttext = $this->checkString($text);
                
                $id = $_POST['editblogid'];

                if (is_numeric($id) == true) {
                    if (strlen($counttext) > 10) {
                        $sql = "UPDATE eventtexts SET text='$text' WHERE id='$id' LIMIT 1";
                        if ($this->sqlInsertUpdateDelete($sql) == true) {
                            $this->erfolgMessage("Erfolgreich.");
                        } else {
                            $this->errorMessage("Nicht erfolgreich.");
                        }
                    }
                }
            } else {
                $this->infoMessage("Du hast keine Berechtigung");
            }
        }
        if (isset($_GET['blogedit'])) {
            // Rechte checken
            if ($this->checkEventOwner($eventid) == true) {
                $id = $_GET['blogedit'];
                // Wert checken
                if (is_numeric($id) == true) {
                    $entry = $this->sqlselect("SELECT * FROM eventtexts WHERE id=$id AND eventid=$eventid LIMIT 1");

                    // Blog auf verfügbarkeit checken:
                    if (isset($entry[0]->id)) {
                        
                        // AUSGABE
                        echo "<div class='newFahrt'>";
                        echo "<form method=post>";
                        echo "<input type=number value='" .$entry[0]->id . "' name='editblogid'  hidden />";
                        echo "<textarea class='ckeditor' name='editblogentry'> " .$entry[0]->text . " </textarea>";
                        echo "<button type=submit>Änderung speichern</button>";
                        echo "<a class='grayLink' href='?delBlog=" .$entry[0]->id . "'>Löschen</a>";
                        echo "<a class='rightRedLink' href='?'>schließen</a>";
                        echo "</form>";
                        echo "</div>";
                    }
                }
                
            } else {
                $this->infoMessage("Du hast keine Berechtigung.");
            }
        }
    }

    /**
     * Löscht einen BlogEintrag
     * 
     * @param int $eventid EventID
     * 
     * @return void
     */
    function delBlogMessage(int $eventid)
    {
        if (isset($_GET['delBlog'])) {
            if (is_numeric($_GET['delBlog']) == true) {
                if ($this->checkEventOwner($eventid) == true) {
                    $id = $_GET['delBlog'];
                    $get = $this->sqlselect("SELECT id, eventid FROM eventtexts WHERE eventid=$eventid and id=$id LIMIT 1");
                    if (isset($get[0]->id)) {
                        $sql = "DELETE FROM eventtexts WHERE id=$id AND eventid=$eventid LIMIT 1";
                        if ($this->sqlInsertUpdateDelete($sql) == true) {
                            $this->erfolgMessage("Erfolgreich.");
                        } else {
                            $this->errorMessage("Löschen nicht erfolgreich.");
                        }
                    }
                }
            }
        }
    }

    /**
     * Zeigt Countdowns zu diesem Event an.
     * 
     * @param int $eventid ID des Events
     * 
     * @return void
     */
    function showCountdowns(int $eventid) 
    {
        // CHECK IF USER HASNT ZUGESAGT YET
        echo "<div class='separateDivBox'>";
        $countdowns = $this->sqlselect("SELECT * FROM eventcountdowns WHERE eventid=$eventid ORDER BY enddate");

        $datetime1 = new DateTime(date("Y-m-d"));
        for ($i = 0; $i < sizeof($countdowns); $i++) {

            $datetime2 = new DateTime($countdowns[$i]->enddate);
            $diff = $datetime1->diff($datetime2);
            echo "<h2>Noch " . $diff->format('%a Tage') . ", dann ist es soweit.</h2>";
        }

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
        $memberlist = $this->sqlselect("SELECT * FROM eventguests WHERE eventid=$eventid ORDER BY guestname");

        echo "<div class='separateDivBox'>";
        echo "<h3>Gästeliste</h3>";
        echo "<ul>";
        for ($i = 0; $i < sizeof($memberlist); $i++) {
            if (isset($_SESSION['eventguest'])) {
                if ($memberlist[$i]->id == $_SESSION['eventguest']) {
                    $css = "yellow";
                } else {
                    $css = "";
                }
            } else {
                $css = "";
            }
            if (strlen($memberlist[$i]->fullname) > 1) {
                // echo "<li id='$css'>".$memberlist[$i]->fullname."</li>";
                echo "<li>Gast ".$memberlist[$i]->id."</li>";
            } else {
                echo "<li>Gast ".$memberlist[$i]->id."</li>";
            }
            
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
            if ($this->userHasRight(79, 0) == true) {
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
        if (isset($_GET['zusageUser']) AND isset($_SESSION['eventid'])) {
            if (is_numeric($_GET['zusageUser']) AND is_numeric($_SESSION['eventid'])) {
                $userid = $_GET['zusageUser'];
                $eventid = $_SESSION['eventid'];

                if ($this->objectExists("SELECT id FROM eventguests WHERE id='$userid'") == true) {

                    if ($this->checkEventOwner($eventid) == true OR $userid == $_SESSION['eventguest']) {
                        $sql = "UPDATE eventguests SET zusage=1 WHERE id='$userid' LIMIT 1";
                        if ($this->sqlInsertUpdateDelete($sql) == true) {
                            $this->erfolgMessage("Zusage gespeichert.");
                        }
                    } else {
                        $this->errorMessage("Du kannst nur deine eigene Zusage erteilen!");
                    }
                    
                } else {
                    $this->errorMessage("Der Benutzer $userid existiert nicht.");
                }

            }
        }
    }

    /**
     * Setzt die ZusageOption eines Gastes auf Absage.
     * 
     * @return void
     */
    function absageGuest()
    {
        if (isset($_GET['absageUser']) AND isset($_SESSION['eventid'])) {
            if (is_numeric($_GET['absageUser']) AND is_numeric($_SESSION['eventid'])) {
                $userid = $_GET['absageUser'];
                $eventid = $_SESSION['eventid'];

                if ($this->objectExists("SELECT id FROM eventguests WHERE id='$userid'") == true) {

                    if ($this->checkEventOwner($eventid) == true OR $userid == $_SESSION['eventguest']) {
                        $sql = "UPDATE eventguests SET zusage=NULL WHERE id='$userid' LIMIT 1";
                        if ($this->sqlInsertUpdateDelete($sql) == true) {
                            $this->erfolgMessage("Absage gespeichert");
                        }
                    } else {
                        $this->errorMessage("Du kannst nur deine eigene Zusage erteilen!");
                    }
                } else {
                    $this->errorMessage("Der Benutzer $userid existiert nicht.");
                }

            }
        }
    }

    /**
     * Zählt die Gästeanzahl hoch oder runter.
     * 
     * @return void
     */
    function counterAction() 
    {
        // Runter zählen
        if (isset($_GET['CounterDOWN']) AND isset($_GET['eventid']) AND isset($_GET['wert'])) {
            if (is_numeric($_GET['CounterDOWN']) == true AND is_numeric($_GET['eventid']) == true AND is_numeric($_GET['wert']) == true) {
                $userid = $_GET['CounterDOWN'];
                $eventid = $_GET['eventid'];
                $counter = $_GET['wert'];

                if ($counter == 0) {
                    $newcounter = 0;
                } else {
                    $newcounter = $_GET['wert'] - 1;
                }

            }
        }

        // hoch zählen
        if (isset($_GET['CounterUP']) AND isset($_GET['eventid']) AND isset($_GET['wert'])) {
            if (is_numeric($_GET['CounterUP']) == true AND is_numeric($_GET['eventid']) == true AND is_numeric($_GET['wert']) == true) {
                $userid = $_GET['CounterUP'];
                $eventid = $_GET['eventid'];
                $counter = $_GET['wert'];

                $newcounter = $_GET['wert'] + 1;

            }
        }

        // Datenbank ändern
        if (isset($newcounter)) {
            if ($this->objectExists("SELECT id FROM eventguests WHERE id=$userid") == true) {
                $sql = "UPDATE eventguests SET anzahl=$newcounter WHERE id=$userid LIMIT 1";
                if ($this->sqlInsertUpdateDelete($sql) == true) {
                    $this->erfolgMessage("Gastanzahl auf $newcounter geändert.");
                }
            } else {
                $this->infoMessage("Der Benutzer existiert nicht.");
            }
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
        $memberlist = $this->sqlselect("SELECT * FROM eventguests WHERE eventid=$eventid ORDER BY zusage,guestname");
        if ($this->checkEventOwner($eventid) == true) { 
            $owner = true;
        } else {
            $owner = false;
        }
        echo "<h3><a name=gaesteliste>Gästeliste</a></h3>";
        if ($owner == true) { 
            $this->deleteEventGuest();
            $this->zusageGuest();
            $this->absageGuest();
            $this->counterAction();
            $this->addGuests();
            $this->setGuest();
        }
        echo "<table class='kontoTable'>";
        echo "<thead>";
        echo "<td id='small'>Login</td>";
        echo "<td>Gastname</td>";
        echo "<td id='small'>Anzahl</td>";
        echo "<td id='small'>Zusage</td>";
        // RESTRICTED START
        if ($owner == true) { 
            echo "<td id='small'>Options</td>";
        } 
        // RESTRICTED END
        echo "</thead>";
        for ($i = 0; $i < sizeof($memberlist); $i++) {
            if (isset($_SESSION['eventguest'])) {
                if ($_SESSION['eventguest'] == $memberlist[$i]->id) {
                    $css = "yellow";
                } else {
                    $css = "";
                }
            } else {
                $css = "";
            }
            echo "<tbody id='$css'>"; 
            echo "<td>";
            if ($memberlist[$i]->loggedin == 1) {
                echo "<a href='?setGuest=".$memberlist[$i]->id."#gaesteliste'> &#10004; </a>";
            } else {
                echo "<a href='?setGuest=".$memberlist[$i]->id."#gaesteliste'> &#8855; </a>";
            }
            echo "</td>";
            // NAME
            echo "<td>";
            if ($owner == true) {
                
                if (strlen($memberlist[$i]->guestmailaddress) > 0) {
                    echo "<a href='mailto:".$memberlist[$i]->guestmailaddress."'> &#9993; "; 
                }
                
            }

            echo $memberlist[$i]->fullname ." / ". $memberlist[$i]->guestname; 
            if ($owner == true) {
                if (strlen($memberlist[$i]->guestmailaddress) > 0) {  
                    echo "</a>";
                }
            }
            echo "</td>";

            // ANZAHL
            echo "<td>";
            if ($owner == true) {  
                echo "<a href='?CounterUP=".$memberlist[$i]->id."&eventid=".$memberlist[$i]->eventid."&wert=".$memberlist[$i]->anzahl."#gaesteliste'>+</a> ";
            }
           
            echo $memberlist[$i]->anzahl;
            if ($owner == true) {  
                echo " <a href='?CounterDOWN=".$memberlist[$i]->id."&eventid=".$memberlist[$i]->eventid."&wert=".$memberlist[$i]->anzahl."#gaesteliste'>-</a>";
            }
             echo "</td>";

            // ZUSAGE / ABSAGE
            echo "<td>";
            if ($memberlist[$i]->zusage == null) {
                if ($owner == true) { 
                    echo "<a href='?zusageUser=".$memberlist[$i]->id."&eventid=".$memberlist[$i]->eventid."#gaesteliste' class='rightRedLink'>nein</a>";
                } else {
                    echo "<a class='rightRedLink' href=''>nein</a>";
                }
            } else {
                if ($owner == true) { 
                    echo "<a href='?absageUser=".$memberlist[$i]->id."&eventid=".$memberlist[$i]->eventid."#gaesteliste' class='rightGreenLink'>&#10004;</a>";
                } else {
                    echo "<a class='rightGreenLink' href=''>&#10004;</a>";
                }
                
            }
            echo "</td>";
            
            // RESTRICTED START
            // LÖSCHEN
            if ($owner == true) {
                echo "<td>";
                    echo "<a class='rightRedLink'href='?delUser=".$memberlist[$i]->id."&eventid=".$memberlist[$i]->eventid."'>X</a>";
                echo "</td>";
            }
            // RESTRICTED END

            echo "</tbody>";
        }

        // FUSSZEILE
        $sumGuest = $this->sqlselect("SELECT *,count(id) as count FROM eventguests WHERE eventid=$eventid");
        $sumAnzahl = $this->sqlselect("SELECT *,sum(anzahl) as gesAnzahl FROM eventguests WHERE eventid=$eventid");
        $sumZusage = $this->sqlselect("SELECT *,sum(zusage) as zusagen FROM eventguests WHERE eventid=$eventid");
        echo "<tfoot>"; 
        echo "<td></td>";
        echo"<td>Summe Gäste: ".$sumGuest[0]->count." </td>"; 
        echo "<td>".$sumAnzahl[0]->gesAnzahl."</td>"; 
        echo "<td>".$sumZusage[0]->zusagen."</td>"; 
        echo "<td>".""."</td>"; 
        echo "</tfoot>";

        echo "</table>";
    }
}

?>