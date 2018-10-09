<?php
/**
 * DOC COMMENT 
 * 
 * PHP Version 7
 * 
 * @history Steven 19.08.2014 angelegt.
 * @history Steven 27.08.2014 Der Login setzt jetzt den Blog beim einloggen auf "öffentlich"
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
 * Login
 * Funktionen zum Login an den Server
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
class Login extends Functions
{
    /**
     * Führt den Login durch und setzt verschiedene Session Variablen
     * $_SESSION['selectUser'] auf $username
     * Leitet auf eine entsprechende Seite weiter.
     * 
     * @param string $umleitung Seite auf die umgeleitet werden soll.
     * 
     * @return void
     */
    public function login_user($umleitung = "uebersicht.php")
    {

        $errorMessage = "<p class='meldung'>Der Benutzername oder 
        das Passwort ist falsch. Überprüfen Sie die Schreibweise und 
        versuchen Sie es erneut.</p>";

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            if (isset($_POST['username']) and isset($_POST['passwort'])) {

                if ($_POST['username'] == "" or $_POST['passwort'] == "") {
                    $this->logEintrag(false, "Name oder Passwort leer", "Error");
                    return $errorMessage;
                }

                //HTML Tags und erlaubte Zeichen aus String herausfiltern:
                $username = strip_tags(stripslashes($_POST['username']));

                //Passwort mit md5 hash verändern:
                $passwort = md5($_POST['passwort']);

                //Unbekannt?
                $hostname = $_SERVER['HTTP_HOST'];
                $path = dirname($_SERVER['PHP_SELF']);

                //SQL Abfrage
                $abfrage = "SELECT id, Name, Passwort, versuche FROM benutzer WHERE Name LIKE '$username' LIMIT 1";

                $row = $this->sqlselect($abfrage);

                if (!isset($row[0]->Name)) {
                    $this->logEintrag(false, "Login Fehlgeschlagen: Name $username existiert nicht in der Datenbank.", "Error");
                    return $errorMessage;
                } else {
                    $userID = $row[0]->id;
                }

                if ($row[0]->versuche >= 3) {
                    $gesperrterNutzer = $row[0]->Name;
                    $this->logEintrag(false, "Login Fehlgeschlagen: Benutzer $gesperrterNutzer ist gesperrt", "Error");
                    return $errorMessage;
                } else {
                    // Benutzername und Passwort werden gecheckt

                    if ($row[0]->Passwort == $passwort) {
                        //setzen der Session Variablen
                        $_SESSION['angemeldet'] = true;
                        $_SESSION['username'] = $row[0]->Name;

                        //Hilfseinstellung für Guildwars
                        $_SESSION['selectUser'] = $row[0]->Name;
                        $_SESSION['selectGWUser'] = $row[0]->Name;

                        //Logeintrag
                        $this->logEintrag(true, " logged in", "login");

                        //Versuche Updaten
                        $getversucheAnzahl = $this->sqlselect("SELECT id, Name, versuche FROM benutzer WHERE Name='" . $row[0]->Name . "' LIMIT 1");

                        //Prüfen ob die Versuche > 0 sind.
                        if ($getversucheAnzahl[0]->versuche == 0) {
                            $noupdateneeded = 1;
                        } else { 
                            $noupdateneeded = 0;
                        }

                        if ($noupdateneeded == 1) {
                            header("Location: $umleitung");
                        } else {
                            $update = "UPDATE benutzer SET versuche=0 WHERE Name = '" . $row[0]->Name . "' LIMIT 1";

                            if ($this->sql_insert_update_delete_hw($update) == false) {
                                echo "<p class='info'>Ein Login ist derzeit nicht möglich.</p>";
                                $this->logEintrag(false, "Versuche von $username konnten nicht auf 0 gesetzt werden.", "Error");
                            } else {
                                header("Location: $umleitung");
                            }

                        }

                    } else {

                        //Versuche hochzählen // ab hier ist sicher, dass der Benutzer existiert.
                        $abfrage = "SELECT Name, versuche FROM benutzer WHERE Name LIKE '$username' LIMIT 1";
                        $row2 = $this->sqlselect($abfrage);

                        //Zur Sicherheit wird der Benutzername aus der Tabelle genommen
                        $usernameSAVE = $row2[0]->Name;

                        //Neue Versuche ausrechnen
                        $bisherigeVersuche = $row[0]->versuche;
                        $neueVersuche = $bisherigeVersuche + 1;

                        //Versuche speichern
                        $updateVersuche = "UPDATE benutzer SET versuche='$neueVersuche' WHERE Name = '$usernameSAVE'";

                        if ($this->sql_insert_update_delete($updateVersuche) == true) {
                            $this->logEintrag(false, "Versuche von $usernameSAVE wurden hochgezählt (falsches Passwort).", "Error");
                            return $errorMessage;
                        } else {
                            $this->logEintrag(false, "Versuche konnten nicht geupdatet werden.", "Error");
                            return $errorMessage;
                        } //else Ende

                    } //else ende
                } //else ende
            } //if isset ende
        } //server requiest ende
    } //function ende

    /**
     * Prüft, ob die Felder zum anmelden angezeigt werden sollen.
     * 
     * @return void
     */
    public function anmeldeCheck()
    {
        $ausgabe = "";
        $notLoggedIn = "";

        if (!isset($_SESSION['angemeldet'])) {
            $ausgabe .= $notLoggedIn;
            $ausgabe .= '<form action="index.php" method=post>';
            $ausgabe .= '<input type=text name=username placeholder=Benutzername required />';
            $ausgabe .= '<input type=password name=passwort placeholder=Passwort required />';
            $ausgabe .= '<input type="submit" value="Einloggen" />';
            $ausgabe .= '</form>';
        } else {
            $ausgabe .= "<p id='loginTitel'><p class='hinweis'>Du bist bereits angemeldet.</p><br><a class='buttonlink' href='uebersicht.php'>Gehe zur &Uuml;bersicht</a></p>";
        }
        if (!isset($_GET['createUser'])) {
            return $ausgabe;
        }
    }

    /**
     * Prüft, ob der User eingeloggt ist. Dabei ist es möglich nur abzufragen,
     * ob der User eingeloggt ist oder ob er zu einem $pfad geschickt werden soll.
     * 
     * @param string $redirect Art und Weise der Umleitung
     * @param string $pfad     Pfad zu dem umgeleitet werden soll
     * 
     * @return void
     */
    public function loggedInOLD($redirect = "redirect", $pfad = "/flatnet2/index.php")
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
     * Gibt zurück, ob ein User eingeloggt ist.
     * 
     * @return boolean
     */
    public function is_logged_in()
    {
        if (isset($_SESSION['angemeldet'])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Loggt den User aus.
     * 
     * @return void
     */
    public function logout()
    {

        session_start();
        session_destroy();
        header("Location: /flatnet2/index.php");
    }

    /**
     * Ermöglicht das registrieren mit Hilfe eines Einladungscodes.
     *  
     * @return void
     */
    public function registerNewUser()
    {
        if (isset($_GET['createUser'])) {
            echo "<form action='?createUser' method=post>";
            echo "<input type=text name=user placeholder='Benutzername' value='' />";
            echo "<input type=password name=pass placeholder='Passwort' value='' />";
            echo "<input type=password name=pass2 placeholder='Passwort wiederholen' value='' />";
            echo "<input type=text name=code placeholder='Einladungscode' value='' />";
            echo "<input type=checkbox name=authorisation value=1 /><label for=authorisation>Datenschutzklausel</label>";
            echo '<br><br><a href="#" class="" onclick="document.getElementById(\'DatenschutzInfos\').style.display = \'block\'"> Datenschutzinformationen anzeigen</a><br><br>';

            echo "<input type=submit name=register value='Registrieren' /><a href='?' class='greenLink'>Zum Login</a>";
            echo "</form>";

            if (isset($_POST['register']) and isset($_POST['pass']) and isset($_POST['pass2']) and isset($_POST['code']) and isset($_POST['user']) and isset($_POST['authorisation'])) {
                $code = strip_tags(stripslashes($_POST['code']));
                $name = strip_tags(stripslashes($_POST['user']));
                $pass = strip_tags(stripslashes($_POST['pass']));
                $pass2 = strip_tags(stripslashes($_POST['pass2']));

                if ($pass != $pass2) {
                    return false;
                }

                //Deklaration der Variablen
                $passwort1 = $pass;
                $passwort2 = $pass2;
                $username = $name;
                $regnewuser = $_POST['register'];
                if ($username == "" or $passwort1 == "" or $passwort2 == "") {
                    echo "<p class='meldung'>Fehler, es wurden nicht alle Felder ausgefüllt.</p>";
                } else {
                    $check = "SELECT * FROM benutzer WHERE Name LIKE '$username' LIMIT 1";
                    $row = $this->sqlselect($check);

                    if (isset($row[0]->Name)) {
                        echo "<p class='meldung'>Fehler, der Name $username ist bereits vergeben.</p>";
                    } else {
                        if ($passwort1 == $passwort2) {

                            //   echo "<p class='meldung'>Passwörter sind gleich</p>";

                            //Register Code Check

                            $codeExists = $this->sqlselect("SELECT * FROM registercode WHERE code = '$code' LIMIT 1 ");

                            if (!isset($codeExists[0]->code)) {
                                // echo "<p class='meldung'>Code gibts nicht</p>";
                                echo "<p class='meldung'>Es gibt Probleme mit dem Code, kontaktiere den Administrator.</p>";
                                //Logeintrag
                                $this->logEintrag(true, "hat den Code $code benutzt, dieser existiert aber nicht.", "Error");
                                return false;

                            } else {

                                //   echo "<p class='meldung'>Code gibt es</p>";

                                //Den Code überprüfen
                                $select = "SELECT * FROM registercode WHERE code = '$code' LIMIT 1";
                                $row = $this->sqlselect($select);

                                //Check, ob der Code zu oft benutzt wurde
                                if ($row[0]->used >= $row[0]->usageTimes) {
                                    echo "<p class='meldung'>Es gibt Probleme mit dem Code, kontaktiere den Administrtor.</p>";
                                    //Logeintrag
                                    $this->logEintrag(true, "hat den Code $code benutzt, die vorgeschriebene Nutzungszahl wurde überschritten.", "Error");
                                    return false;

                                } else {
                                    //IP Check
                                    if (!isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                                        $ip = $_SERVER['REMOTE_ADDR'];
                                    } else {
                                        //IP Check
                                        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                                    }
                                    //neue Benutzung ausrechnen
                                    $used = $row[0]->used + 1;

                                    //Coderechte für den Benutzer übernehmen
                                    //$codeRechte = $row[0]->rights;

                                    //Update des Codes.
                                    $update = "UPDATE registercode SET used='$used',usedBy='$username',ipadress='$ip' WHERE code = '$code'";
                                    if ($this->sql_insert_update_delete($update) == true) {
                                        //Logeintrag
                                        $this->logEintrag(true, "hat den Code $code benutzt, dieser wurde von " . $row[0]->used . " nach $used geupdatet", "Error");
                                    } else {
                                        echo "<p class='meldung'>Es gibt Probleme mit dem Code, kontaktiere den Administrtor.</p>";
                                        //Logeintrag
                                        $this->logEintrag(true, "wollte sich registrieren, der Code konnte aber nicht geupdated werden.", "Error");
                                        return false;
                                    }
                                }
                            }

                            //Benutzer anlegen
                            $passwortneu = md5($passwort1);
                            $query = "INSERT INTO benutzer (Name, Passwort, rights, versuche) VALUES ('$username','$passwortneu','0','0')";
                            if ($this->sql_insert_update_delete($query) == true) {
                                echo "<p class='erfolg'>Hallo $username! Du hast dich erfolgreich registriert! <a href='uebersicht.php'>Klicke hier</a></p>";
                                //Logeintrag
                                $this->logEintrag(true, "hat sich registriert", "login");

                                $_SESSION['angemeldet'] = true;
                                $_SESSION['username'] = $username;

                                //Hilfseinstellung für Guildwars
                                $_SESSION['selectUser'] = $username;
                                $_SESSION['selectGWUser'] = $username;

                                //Logeintrag
                                $this->logEintrag(true, "hat sich eingeloggt", "login");

                            } else {
                                echo "<p class='meldung'>Bei der registrierung ist ein Fehler aufgetreten.</p>";
                                //Logeintrag
                                $this->logEintrag(true, "wollte sich registrieren, der DB Eintrag konnte aber nicht erstellt werden.", "Error");
                            }
                        } else {
                            echo "<p class='meldung'>Die Passwörter sind nicht identisch!</p>";
                        } //Else Ende
                    } //Else Ende
                } //Else Ende
            } else {
                return false;
            }
        }
    }

}
