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

require 'mysql.class.php';
/**
 * Functions
 * Stellt grundlegende Funktionen für die Webseite zur Verfügung.
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
class Functions extends Sql
{

    /**
     * Allgemeine Suche
     *
     * @param string $suchWort // Das Wort wonach gesucht werden soll
     * @param string $tabelle  // In welcher Tabelle gesucht werden soll
     * @param string $name     // Anzeige Spaltenname nach Suche.
     * @param string $pfad     // Welcher Pfad hinterlegt werden soll.
     *
     * @return string // Gibt die komplette Suche, samt umgebenen DIV wieder.
     */
    public function suche($suchWort = "test", $tabelle = "docu", $name = "text", $pfad = "?id")
    {
        if ($this->userHasRight(23, 0) == true) {
            if ($suchWort) {

                $ursprünglicheSuche = $suchWort;
                $ergebnis_der_suche = "";
                // Suche mit Wildcards bestücken
                $suchWort = "%" . $suchWort . "%";

                // Spalten der Tabelle selektieren:
                $colums = "SHOW COLUMNS FROM $tabelle";

                $rowSpalten = $this->getObjektInfo($colums);

                // SuchQuery bauen:
                // Start String:
                $querySuche = "SELECT * FROM $tabelle WHERE (id LIKE '$suchWort' ";

                // OR + Spaltenname LIKE Suchwort
                for ($i = 0; $i < sizeof($rowSpalten); $i++) {
                    $querySuche .= " OR " . $rowSpalten[$i]->Field . " LIKE '$suchWort'";
                }
                // Klammer am Ende schließen-
                $querySuche .= ")";

                // Query für die Suche
                $suchfeld = $this->getObjektInfo($querySuche);

                // Ausgabe der Suche
                $ergebnis_der_suche .= "<h2>Suchergebnis (= $ursprünglicheSuche)</h2>";
                for ($i = 0; $i < sizeof($suchfeld); $i++) {
                    $ergebnis_der_suche .= "<div class='invSuchErgeb'>";
                    $ergebnis_der_suche .= "<a href='" . $pfad = $suchfeld[$i]->id . "'>" . substr($suchfeld[$i]->$name, 0, 20) . "..</a>";
                    $ergebnis_der_suche .= "</div>";
                }
                $close = "<a href='?' class='closeSumme'>X</a>";

                $return = "<div id='draggable' class='summe'>" . $close . $ergebnis_der_suche . "</div>";

                return $return;
            }
        }
    }

    /**
     * Zeigt die Links an, um die Seiten zu wechseln.
     *
     * @param string $query    Query fuer die Anzeige
     * @param int    $proSeite Anzahl Ergebnisse pro Seite
     *
     * @return void
     */
    public function SeitenZahlen($query, $proSeite)
    {
        $anzahl = $this->getAmount($query);
        // plus eins, weil ansosnten eine Seite fehlt!
        $anzahlSeiten = $anzahl / $proSeite + 1;

        for ($i = 1; $i <= $anzahlSeiten; $i++) {
            echo "<a";
            if (isset($_GET['von'])) {
                if ($_GET['von'] == $i) {
                    echo " class='seitenLinkActive' ";
                } else {
                    echo " class='seitenLink' ";
                }
            } else {
                echo " class='seitenLink' ";
            }
            echo " href='?von=$i'>$i</a>";
        }
    }

    /**
     * Zeigt alle Verfügbaren Seiten an.
     *
     * @param int $proSeite Ergebnisse pro Seite
     *
     * @return String $ergebnis
     */
    public function seitenAnzeigen($proSeite)
    {
        // VON BIS HERAUSFINDEN:
        if (!isset($_GET['von'])) {
            $count1 = 0;
        } else {
            $count1 = $_GET['von'];
            if ($count1 == "") {
                $count1 = 1;
            }
            $count1 = $count1 * $proSeite - 1 * $proSeite;
        }
        $ergebnis = "LIMIT $proSeite OFFSET $count1";
        return $ergebnis;
    }

    /**
     * Gibt die Benutzer ID aus.
     *
     * @param string $username Benutzername
     *
     * @return int $userID
     */
    public function getUserID($username)
    {
        // Namen des Benutzers auswählen:
        $userInfo = $this->getObjektInfo("SELECT id, Name FROM benutzer WHERE Name = '$username' LIMIT 1");
        if (isset($userInfo[0]->id)) {
            $userID = $userInfo[0]->id;
        } else {
            $userID = 0;
        }
        return $userID;
    }

    /**
     * Gibt den Namen des Benutzers aus.
     *
     * @param int $userid UserID
     *
     * @return String $username
     */
    public function getUserName($userid)
    {
        // Namen des Benutzers auswählen:
        $selectUsername = "SELECT id, Name FROM benutzer WHERE id = '$userid' LIMIT 1";
        $rowUsername = $this->getObjektInfo($selectUsername);
        if (isset($rowUsername[0]->Name)) {
            $username = $rowUsername[0]->Name;
        } else {
            $username = "anonymus User";
        }
        return $username;

    }

    /**
     * Gibt die ID der kategorie aus.
     *
     * @param String $kategorieName Name der Kategorie
     *
     * @return int $catID
     */
    public function getCatID($kategorieName)
    {
        $selectKat = "SELECT id, kategorie FROM blogkategorien WHERE kategorie = '$kategorieName' LIMIT 1";
        $row = $this->getObjektInfo($selectKat);
        if (isset($row[0]->id)) {
            $catID = $row[0]->id;
        } else {
            $catID = 0;
        }
        return $catID;
    }

    /**
     * Gibt den Namen der Kategorie aus.
     *
     * @param int $kategorieID ID der Kategorie
     *
     * @return String $catName
     */
    public function getCatName($kategorieID)
    {
        $selectKat = "SELECT id, kategorie FROM blogkategorien WHERE id = '$kategorieID' LIMIT 1";
        $row = $this->getObjektInfo($selectKat);
        if (isset($row[0]->kategorie)) {
            $catName = $row[0]->kategorie;
        } else {
            $catName = "Unbekannt";
        }
        return $catName;
    }

    /**
     * VERALTET
     * Quelle: http://www.webmasterpro.de/coding/article/php-ein-einfaches-flexibles-rechtesystem.html
     * Einstieg
     *
     * @param int $benoetigt ID des Rechts welches benötigt wird
     *
     * @return boolean / void
     */
    public function pruefung($benoetigt)
    {

        $user = $_SESSION['username'];
        $rightFromCurrentUser = "SELECT rights, titel FROM benutzer WHERE Name = '$user'";
        $row = $this->getObjektInfo($rightFromCurrentUser);

        $benutzerrechte = $row[0]->rights;
        $rechte = array();

        // Menge der Rechte prüfen:
        $selectAnzahl = "SELECT count(*) as anzahl FROM userrights";
        $menge = $this->getObjektInfo($selectAnzahl);
        // Anzahl prüfen Ende

        for ($i = $menge[0]->anzahl; $i >= 0; $i--) {
            $wert = pow(2, $i);
            if ($benutzerrechte >= $wert) {
                $rechte[] = $wert;
                $benutzerrechte -= $wert;
            }
        }
        if (in_array($benoetigt, $rechte)) {
            return true;
        } else {
            if (isset($row[0]->titel) and $row[0]->titel == "Super User") {
                return true;
            } else {
                echo "<p class='meldung'>Du bist nicht berechtigt, diese Seite zu sehen.</p>";
                exit;
            }
        }
    }

    /**
     * Neue PrüfungsFunktion
     * Nimmt den aktuellen Benutzer und prüft, ob der Benutzer die Seite sehen darf oder nicht.
     *
     * @param int $rightID ID des Rechts
     *
     * @return boolean
     */
    public function userHasRightPruefung($rightID)
    {
        $user = $this->getUserID($_SESSION['username']);
        $query = "SELECT besitzer, right_id FROM rights WHERE besitzer = $user AND right_id = $rightID LIMIT 1";
        $userHasRight = $this->getObjektInfo($query);
        if (isset($userHasRight[0]->besitzer) and isset($userHasRight[0]->right_id)) {
            if ($userHasRight[0]->besitzer == $user and $userHasRight[0]->right_id == $rightID) {
                return true;
            } else {
                echo "<p class='meldung'>Du bist nicht berechtigt, diese Seite zu sehen.</p>";
                exit;
            }
        } else {
            echo "<p class='meldung'>Du bist nicht berechtigt, diese Seite zu sehen.</p>";
            exit;
        }
    }

    /**
     * Prüft, ob der Benutzer das Recht besitzt oder nicht.
     * 
     * @param int $id   ID des Rechts
     * @param int $user ID des Users
     * 
     * @return boolean
     */
    public function userHasRight($id, $user)
    {

        // Wenn nur gechecked werden soll ob der aktuelle Benutzer zugriff hat:
        if ($user == 0) {
            $user = $this->getUserID($_SESSION['username']);
        }

        $query = "SELECT besitzer, right_id FROM rights WHERE besitzer = $user AND right_id = $id LIMIT 1";

        $userHasRight = $this->getObjektInfo($query);

        if (isset($userHasRight[0]->besitzer) and isset($userHasRight[0]->right_id)) {
            if ($userHasRight[0]->besitzer == $user and $userHasRight[0]->right_id == $id) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }

    }

    /**
     * Checkt Rechte eines Benutzers im 2er Potenzsystem.
     * Wird noch im Forum verwendet.
     * 
     * @param int $benoetigt      ID des Rechts
     * @param int $benutzerrechte Summe Rechte des Users
     * 
     * @return boolean
     */
    public function check($benoetigt, $benutzerrechte)
    {
        if ($benutzerrechte == 0) {
            $user = $_SESSION['username'];
            $rightFromCurrentUser = "SELECT * FROM benutzer WHERE Name = '$user' LIMIT 1";
            $row = $this->getObjektInfo($rightFromCurrentUser);
            $benutzerrechte = $row[0]->rights;
        }

        $rechte = array();

        // Anzahl der Rechte prüfen
        $selectAnzahl = "SELECT count(*) as anzahl FROM userrights";
        $menge = $this->getObjektInfo($selectAnzahl);
        $menge = $menge[0]->anzahl;
        // Anzahl prüfen Ende

        for ($i = $menge; $i >= 0; $i--) {
            $wert = pow(2, $i);
            if ($benutzerrechte >= $wert) {
                $rechte[] = $wert;
                $benutzerrechte -= $wert;
            }
        }
        if (in_array($benoetigt, $rechte)) {
            return true;
        } else {
            if (isset($row[0]->titel) and $row[0]->titel == "Super User") {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Prüft, ob der User eingeloggt ist. Dabei ist es möglich nur abzufragen,
     * ob der User eingeloggt ist oder ob er zu einem $pfad geschickt werden soll.
     * 
     * @param string $redirect Art und Weise der Umleitung
     * @param string $pfad     Wohin soll User geschickt werden
     * 
     * @return boolean
     */
    public function logged_in($redirect = "redirect", $pfad = "/flatnet2/index.php")
    {
        $hostname = $_SERVER['HTTP_HOST'];
        $path = dirname($_SERVER['PHP_SELF']);

        if (!isset($_SESSION['angemeldet']) || !$_SESSION['angemeldet'] and $pfad != "") {
            header("Location: $pfad");
        } else {
            return true;
        }
    }

    /**
     * Erstellt den Logeintrag
     * 
     * @param boolean $state  Ist user bekannt oder nicht?
     * @param string  $art    Aktion des Users
     * @param string  $status Art des Logs
     * 
     * @return boolean
     */
    public function logEintrag($state, $art, $status)
    {

        if (!isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } else {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        if ($state == true) {
            // Erstellt Log wenn user bekannt ist.
            // User herausfinden:
            if (isset($_SESSION['username'])) {
                $username = $_SESSION['username'];
            } else {
                $username = "Unknown User";
            }

            $userID = $this->getUserID($username);
            $insert = "INSERT INTO vorschlaege (text, autor, status, ipadress) VALUES ('$username $art','$userID','$status','$ip')";

            if ($this->sql_insert_update_delete_hw($insert) == true) {
                return true;
            } else {
                return false;
            }

        } else {
            // Log Eintrag erstellen wenn User nicht bekannt ist.
            $insert = "INSERT INTO vorschlaege (text, autor, status, ipadress) VALUES ('Ein Login ist fehlgeschlagen: $art ($ip).','0','Error','$ip')";

            if ($this->sql_insert_update_delete_hw($insert) == true) {
                return true;
            } else {
                return false;
            }

        }
    }

    /**
     * Gibt den richtigen Monat aus.
     * 
     * @param int $number Kalendarische Nummer des Monats
     * 
     * @return string
     */
    public function getMonthName($number)
    {

        if (!isset($number)) {
            $monat = "Fehler";
        }

        if ($number == 1) {
            $monat = "Januar";
        }

        if ($number == 2) {
            $monat = "Februar";
        }

        if ($number == 3) {
            $monat = "März";
        }

        if ($number == 4) {
            $monat = "April";
        }

        if ($number == 5) {
            $monat = "Mai";
        }

        if ($number == 6) {
            $monat = "Juni";
        }

        if ($number == 7) {
            $monat = "Juli";
        }

        if ($number == 8) {
            $monat = "August";
        }

        if ($number == 9) {
            $monat = "September";
        }

        if ($number == 10) {
            $monat = "Oktober";
        }

        if ($number == 11) {
            $monat = "November";
        }

        if ($number == 12) {
            $monat = "Dezember";
        }

        return $monat;
    }

    /**
     * Stellt eine Navigation zur Verfügung.
     * RIGHT-IDS: 8, 37, 42, 44, 48, 51, 62, 63, 67
     * 
     * @param string $site Filter nach welcher Seite
     * 
     * @return void
     */
    public function subNav($site)
    {
        if ($site == "guildwars") {
            echo "<div class='rightOuterBody'><ul>";

            echo "<li><a href='start.php' id='home'>Charakter</a></li>";
            echo "<li><a href='http://gw2cartographers.com/' target='_blank' class='extern'>Maps</a></li>";

            if ($this->userHasRight(62, 0) == true) {
                echo "	<li><a href='kalender.php' id='kalender'>Kalender</a></li>";
            }

            if ($this->userHasRight(63, 0) == true) {
                echo "<li><a href='kosten.php' id='calc'>Kostenrechner</a></li>";
            }
            echo "<li><a href='handwerk.php' id='handwerk'>Handwerk</a></li>";
            echo "</ul>";
            echo "</div>";
        }

        if ($site == "profil") {
            echo "<div class='rightOuterBody'><ul>";

            if (!isset($_GET['passChange']) and !isset($_GET['userlist'])) {
                echo "<li><a href='?' id='marked'>Profil</a></li>	";
            } else {
                echo "<li><a href='?' id=''>Profil</a></li>	";
            }
            if (isset($_GET['passChange'])) {
                echo "<li><a href='?passChange' id='marked'>Benutzereinstellungen</a></li>	";
            } else {
                echo "<li><a href='?passChange' id=''>Benutzereinstellungen</a></li>	";
            }
            if (isset($_GET['userlist'])) {
                echo "<li><a href='?userlist' id='marked'>Mitgliederliste</a></li>";
            } else {
                echo "<li><a href='?userlist' id=''>Mitgliederliste</a></li>";
            }
            echo "</ul></div>";
        }

        if ($site == "admin") {
            echo "<div class='rightOuterBody'><ul>";

            if ($this->userHasRight(37, 0) == true) {
                echo "<li><a href='?action=1'";
                
                if (!isset($_GET['action']) or $_GET['action'] == 1) {
                    echo " id='marked' ";
                } else {
                    echo " id='' ";
                }
                echo ">Benutzer</a></li>";
            }

            if ($this->userHasRight(42, 0) == true) {
                echo "<li><a href='?action=2'";
                if (isset($_GET['action']) and $_GET['action'] == 2) {
                    echo " id='marked' ";
                } else {
                    echo " id='' ";
                }
                echo ">Logs</a></li>";
            }

            if ($this->userHasRight(51, 0) == true) {
                echo "<li><a href='?action=3'";
                if (isset($_GET['action']) and $_GET['action'] == 3) {
                    echo " id='marked' ";
                } else {
                    echo " id='' ";
                }
                echo ">Objekte</a></li>";
            }

            if ($this->userHasRight(48, 0) == true) {
                echo "<li><a href='?action=5'";
                if (isset($_GET['action']) and $_GET['action'] == 5) {
                    echo " id='marked' ";
                } else {
                    echo " id='' ";
                }
                echo ">Forum</a></li>";
            }

            if ($this->userHasRight(44, 0) == true) {
                echo "<li><a href='?action=6'";
                if (isset($_GET['action']) and $_GET['action'] == 6) {
                    echo " id='marked' ";
                } else {
                    echo " id='' ";
                }
                echo ">Rechteverwaltung</a></li>";
            }

            if ($this->userHasRight(8, 0) == true) {
                echo "<li><a href='/flatnet2/informationen/hilfe.php'";
                echo ">Ankündigungen</a></li>";
            }

            if ($this->userHasRight(67, 0) == true) {
                echo "<li><a href='http://localhost/phpmyadmin/' target='_blank' class='extern'>PMA Local</a></li>
                <li><a href='https://bernd.php-friends.de:4443/' target='_blank' class='extern'>Verwaltungskonsole</a></li>";

            }
            if ($this->userHasRight(67, 0) == true) {
                echo "<li><a href='/flatnet2/admin/sqlcheck.php' target='_blank' class='extern'>SQL Checker</a></li>";

            }

            // Abschluss
            echo "</ul></div>";
        }

        if ($site == "informationen") {
            echo '
            <li><a href="kontakt.php" id="kontakt">Kontakt</a></li>
            <li><a href="hilfe.php" id="doku">Ankündigungen</a></li>
            <li><a href="quellen.php" id="quellen">Quellen</a></li>
            <li><a href="impressum.php" id="impressum">Impressum</a></li>';
        }

    }

    /**
     * Gibt den Header wieder.
     * 
     * @return void
     */
    public function header()
    {
        header("Content-type:text/html; charset=utf-8");
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
        } else {
            echo "<p class='hinweis'>Du hast versucht auf eine Seite zuzugreifen, 
            für die du nicht angemeldet bist. Bitte melde dich an und versuche es 
            erneut<br>
            <a class='rightBlueLink' href='/flatnet2/index.php'>Hauptseite</a></p>
            </div>
            </header>";
            exit;
        }
        echo "<ul>
            <li><a href='/flatnet2/informationen/impressum.php'>Impressum</a></li>
            <li><a href='/flatnet2/informationen/quellen.php'>Quellen</a></li>";
        if ($this->userHasRight(36, 0) == "true") {
            echo "<li><a href='/flatnet2/admin/control.php'>Admin</a></li>";
            echo "<li><a href='/flatnet2/informationen/dokumentation.php'>Dokumentation</a></li>";
        } else {
            echo "<li><a href='/flatnet2/informationen/kontakt.php'>Kontakt</a></li>";
        }
        echo "</ul>
            <div id='suche'>
            <form method='get'>
            <input type=text name='suche' value='' placeholder='Suche ...' id='suche' />
            <input type=submit value='OK' id='sucheSubmit' />
            </form>";

        // Benachrichtigungscenter:
        //    $this->benachrichtigungsCenter();

        echo "</div></div>";

        // Zeigt die Navigation an:
        $this->showNaviLinks();

        echo "
        <div id='ueberschrift'>
        <h1><a href='/flatnet2/uebersicht.php'>Steven.NET</a></h1>
        </div>";

        // Zeigt die Ankündigung an:
        $this->ankuendigung();
        echo "</header>";
    }

    /**
     * Zeigt die Navigation an:
     * 
     * @return void
     */
    public function showNaviLinks()
    {
        echo "<ul id='navigation'>";

        $kacheln = $this->getObjektInfo("SELECT * FROM uebersicht_kacheln WHERE active=1 ORDER BY sortierung");
        if ($this->userHasRight(7, 0) == "true") {
            echo "<li>	<a href='/flatnet2/uebersicht.php' id='uebersicht'>Startseite</a></li>";
        }

        for ($i = 0; $i < sizeof($kacheln); $i++) {

            if ($this->userHasRight($kacheln[$i]->rightID, 0) == true) {
                echo "<li>";
                echo "<a id='" . $kacheln[$i]->cssID . "' href='" . $kacheln[$i]->link . "'>" . $kacheln[$i]->name . "</a>";
                echo "</li>";
            }

        }

        echo "</ul>";
    }

    /**
     * Zeigt die Ankündigung im Header an.
     * 
     * @return void
     */
    public function ankuendigung()
    {
        if ($this->userHasRight(24, 0) == true) {
            echo "<div class='InfoCenter'>";
            $docuInfo = $this->getObjektInfo("SELECT *, month(timestamp) AS monat, day(timestamp) AS tag, year(timestamp) AS jahr FROM docu ORDER BY timestamp DESC LIMIT 1");
            if (isset($docuInfo[0]->text)) {
                echo "<a href='/flatnet2/informationen/hilfe.php'>" . $docuInfo[0]->tag . "." . $docuInfo[0]->monat . "." . $docuInfo[0]->jahr . "</a>: " . substr($docuInfo[0]->text, 0, 80);
            }
            echo "</div>";
        }
    }

    /**
     * Zeigt ein Benachrichtigungscenter an.
     * 
     * @return void
     */
    public function benachrichtigungsCenter()
    {

        if ($this->userHasRight(25, 0) == true) {

            $text = "%<strong>" . $_SESSION['username'] . "</strong>%";

            $linkname = "Es gibt nichts neues";
            $class = "rightBlueLink";

            $anzahlBenachrichtigungen = $this->getObjektInfo(
                "SELECT count(*) as anzahl
                FROM blog_kommentare WHERE text LIKE '$text'
                AND datediff(curdate(), timestamp) < 5
                ORDER BY timestamp DESC"
            );

            if ($anzahlBenachrichtigungen[0]->anzahl > 0) {
                $linkname = "Du hast " . $anzahlBenachrichtigungen[0]->anzahl . " Nachrichten";
                $class = "highlightedLink";
            }

            echo "<a href=\"#\" class='$class' onclick=\"document.getElementById('BenCenter').style.display = 'block'\">$linkname</a>";
            echo "<div class='benCenter' style=\"display: none;\" id=\"BenCenter\">";
            echo "<a href=\"#\"  class='highlightedLink' onclick=\"document.getElementById('BenCenter').style.display = 'none'\">Schließen</a>";
            echo "<p class='spacer'>Grüne Links sind NEU und graue sind ALT! (max 5 Tage alt)</p>";

            // Antworten auf eigene Beiträge suchen

            $query = "SELECT *
            , day(timestamp) as tag
            , month(timestamp) as monat
            , year(timestamp) as jahr
            FROM blog_kommentare
            WHERE text LIKE '$text'
            AND datediff(curdate(), timestamp) < 5
            ORDER BY timestamp DESC";

            $gefundeneForeneintraege = $this->getObjectsToArray($query);

            if ($gefundeneForeneintraege > 0) {

                for ($i = 0; $i < sizeof($gefundeneForeneintraege); $i++) {

                    $blogidInfos = $this->getObjektInfo("SELECT * FROM blogtexte WHERE id = '" . $gefundeneForeneintraege[$i]->blogid . "' LIMIT 1 ");

                    echo "<a href='/flatnet2/blog/blogentry.php?showblogid="
                    . $gefundeneForeneintraege[$i]->blogid
                    . "#KommID" . $gefundeneForeneintraege[$i]->id
                    . "' id='benachrichtigung'>
                        " . $gefundeneForeneintraege[$i]->tag
                    . "." . $gefundeneForeneintraege[$i]->monat
                    . "." . $gefundeneForeneintraege[$i]->jahr
                    . " von " . $this->getUserName($gefundeneForeneintraege[$i]->autor)
                    . " zum Thema <strong>" . $blogidInfos->titel . "</strong></a><br>";
                }
            }
            echo "</div>";
        }
    }

    /**
     * Zeigt den Titel des Benutzers an.
     * 
     * @return void
     */
    public function showTitel()
    {
        // Titel anzeigen:
        $userID = $this->getUserID($_SESSION['username']);
        $userTitel = $this->getObjektInfo("SELECT id, titel FROM benutzer WHERE id = '$userID' LIMIT 1");
        if (isset($userTitel) and $userTitel[0]->titel != "") {
            echo "<div class='spacer'><a class='rightGreenLink' href='/flatnet2/forum/index.php?blogcategory=5'>Titel: " . $userTitel[0]->titel . "</a></div>";
        }
    }

    /**
     * Zeigt Diagramme mit JQUERY an.
     * 
     * @param int $zahlen Wert
     * @param int $width  Breite
     * @param int $height Höhe
     * 
     * @return void
     */
    public function showDiagramme($zahlen, $width, $height)
    {

        if (isset($zahlen[0])) {
            // Erstellung der Labels
            $labels = "[";
            for ($l = 0; $l < sizeof($zahlen); $l++) {
                $labels .= $l . ",";
            }
            $labels .= "]";

            // Erstellung der Daten
            $data = "[";
            for ($k = 0; $k < sizeof($zahlen); $k++) {
                $data .= $zahlen[$k] . ",";
            }
            $data .= "]";

            echo '<canvas id="buyers" width=' . $width . ' height=' . $height . '></canvas>';
            echo '
                <script>
                var buyerData = {
                        labels : ' . $labels . ',
                        datasets : [
                            {
                                fillColor : "rgba(172,194,132,0.4)",
                                strokeColor : "#ACC26D",
                                pointColor : "#fff",
                                pointStrokeColor : "#9DB86D",
                                data : ' . $data . '
                            }
                        ]
                    }

                    var buyers = document.getElementById(\'buyers\').getContext(\'2d\');
                    new Chart(buyers).Line(buyerData);
                </script>
                    ';
        }
    }
}
