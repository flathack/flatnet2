<?php
/**
 * DOC COMMENT
 * 
 * PHP Version 7
 * 
 * @history Steven 25.08.2014 angelegt.
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
 * Guildwars
 * Funktionen für den Bereich Guildwars
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
class Guildwars extends Functions
{

    /**
     * Gibt den Namen des Charakter zurück.
     * 
     * @param int $id CharID
     * 
     * @return void
     */
    public function getCharname($id)
    {
        $select = "SELECT id, name FROM gw_chars WHERE id = '$id' LIMIT 1";
        $row = $this->getObjektInfo($select);
        if (!isset($row[0]->name) or $row[0]->name == "") {
            $name = "Kein Name";
        } else {
            $name = $row[0]->name;
        }
        return $name;
    }

    /**
     * Gibt den Besitzer des Charakters zurück.
     * 
     * @param int $id CharID
     * 
     * @return string Name
     */
    public function getBesitzer($id)
    {
        $select = "SELECT id, besitzer FROM gw_chars WHERE id = '$id' LIMIT 1";
        $row = $this->getObjektInfo($select);

        if (!isset($row[0]->besitzer) or $row[0]->besitzer == "") {
            $name = "Kein Besitzer";
        } else {
            $name = $row[0]->besitzer;
        }
        return $name;
    }

    /**
     * Generiert einen zufälligen Namen, aus einer auf dem Server liegenden Liste.
     * 
     * @return void
     */
    public function nameGen()
    {
        srand((double) microtime() * 1000000);

        // Pfade für Namenslisten
        $pfad_nachnamen = "namegen/names.txt";
        $pfad_vornamen_maskulin = "namegen/names.txt";
        $pfad_vornamen_feminin = "namegen/names.txt";

        $vornamen_m = file($pfad_vornamen_maskulin);
        $vornamen_f = file($pfad_vornamen_feminin);
        $nachnamen = file($pfad_nachnamen);

        $anz = count($vornamen_m) - 1;
        $zufallszahl = rand(1, $anz);
        $m = $vornamen_m[$zufallszahl];

        $anz = count($vornamen_f) - 1;
        $zufallszahl = rand(1, $anz);
        $f = $vornamen_f[$zufallszahl];

        $anz = count($nachnamen) - 1;
        $zufallszahl = rand(1, $anz);
        $nachname1 = $nachnamen[$zufallszahl];

        $anz = count($nachnamen) - 1;
        $zufallszahl = rand(1, $anz);
        $nachname2 = $nachnamen[$zufallszahl];

        $m = substr($m, 1, 20);
        $f = substr($f, 1, 20);
        $nachname1 = substr($nachname1, 1, 20);
        $nachname2 = substr($nachname2, 1, 20);
        echo "<div class='newChar'>";
        echo "Name 1: $m | $nachname1 <br>";
        echo "Name 2: $f | $nachname2 <br>";
        echo "</div>";

    }

    /**
     * Erstellt für jeden Account eine Schaltfläche 
     * und ermöglicht, den Wechsel des Accounts.
     * 
     * @return void
     */
    public function selectAccount()
    {

        //Account umswitchen:
        if (isset($_POST['gw_account'])) {
            $account = $_POST['gw_account'];
            $_SESSION['gw_account'] = $account;
        }

        //Derzeitigen User bestimmen
        $user = $this->getUserID($_SESSION['selectGWUser']);

        //Accounts ermitteln
        $getAccounts = "SELECT * FROM gw_accounts WHERE besitzer = '$user'";
        $row2 = $this->getObjektInfo($getAccounts);

        $mengeGrund = $this->getObjektInfo("SELECT count(*) as anzahl FROM gw_accounts WHERE besitzer = '$user'");
        $menge = $mengeGrund[0]->anzahl;

        //Accounts
        echo "<form action='#account' method=post>";
        if ($menge == 0) {
            echo "<input type=radio value='1' name='gw_account' id='standard' onclick='' checked />";
            echo "<label for='standard'>Standard Account</label>";
        }
        for ($i = 0; $i < sizeof($row2); $i++) {
            if (!isset($_SESSION['gw_account'])) {
                $_SESSION['gw_account'] = 1;
            }

            echo "<input onChange='this.form.submit();' type=radio value='" . $row2[$i]->account . "' name='gw_account' id='" . $row2[$i]->mail . "' onclick=''";
            if ($_SESSION['gw_account'] == $row2[$i]->account) {
                echo "checked";
            } else {
                echo "unchecked";
            }
            echo " />";
            echo "<label for='" . $row2[$i]->mail . "'>" . $row2[$i]->mail . "</label>";

        }
        if ($menge != 0) {
            echo "<a href='/flatnet2/usermanager/usermanager.php?passChange' class='buttonlink' />Account-Einstellungen</a>";
        } else {
            echo "<a href='/flatnet2/usermanager/usermanager.php?passChange&edit=1' class='buttonlink' />Account-Einstellungen</a>";
        }

        echo "</form>";
    }

}

/**
 * GW_Costs
 * Funktionen für den Bereich Kosten
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
class Gw_Costs extends Guildwars
{

    /**
     * Zeigt das ausgegebene Geld für Guildwars an.
     * 
     * @return void
     */
    public function showCostCalcEntries()
    {
        if ($this->userHasRight(63, 0) == true) {
            //Eingabefeld anzeigen.
            $this->showEingabeFeld();

            //Eintrag in DB speichern.
            if (isset($_POST['text']) and isset($_POST['wert'])) {
                $this->setGWCostEintrag($_POST['text'], $_POST['wert'], $_POST['kaufdat']);
            }

            if (isset($_GET['loeschid'])) {
                $this->deleteDocuEintrag();
            }

            //Ausgabe der Doku aus der Datenbank
            $user = $this->getUserID($_SESSION['username']);

            $query = "SELECT *, month(kaufdat) AS monat, day(kaufdat) AS tag, year(kaufdat) AS jahr FROM gwcosts WHERE besitzer = '$user' ORDER BY kaufdat DESC";
            $row = $this->getObjektInfo($query);

            echo "<table class='flatnetTable'>";
            $summe = 0;
            echo "<thead><td id='datum'>Datum</td><td id=''>Text</td><td>Wert</td><td></td></thead>";
            for ($i = 0; $i < sizeof($row); $i++) {
                $summe = $summe + $row[$i]->wert;
                echo "<tbody>
                        <td>" . $row[$i]->tag . "." . $row[$i]->monat . "." . $row[$i]->jahr . "</td>
                        <td>" . $row[$i]->text . "</td>
                        <td>" . $row[$i]->wert . " €</td><td><a href='?loeschen&loeschid=" . $row[$i]->id . "' class='highlightedLink'>X</a></td>
                      </tbody>";
            }

            echo "</table>";
            echo "<h1>Gesamt: " . $summe . " €</h1>";
        }
    }

    /**
     * Zeigt das Eingabefeld zum Eintragen eines Eintrags in den Kosten Calculator.
     * 
     * @return void
     */
    public function showEingabeFeld()
    {
        //Neuer Eintrag in die Doku:
        if ($this->userHasRight("63", 0) == true) {
            echo "<div class='spacer'>";
            echo "<h3>Neuen Eintrag</h3>";
            echo "<form method=post>";
            echo "<input type='text' name='text' id='titel' value='' placeholder='Beschreibung' />";
            echo "<input type='text' name='wert' id='wert' value='' placeholder='Wert' />";
            $timestamp = time();
            $kaufdat = date("Y-m-d", $timestamp);
            echo "<input type='date' name='kaufdat' id='wert' value='$kaufdat' placeholder='Wert' />";
            echo "<input type='submit' name='sendDocu' value='Absenden' />";
            echo "</form>";
            echo "</div>";
        }
    }

    /**
     * Setzt einen Eintrag im Kosten Calculator
     * 
     * @param string $text    Text
     * @param float  $wert    Wert
     * @param date   $kaufdat Datum
     * 
     * @return void
     */
    public function setGWCostEintrag($text, $wert, $kaufdat)
    {
        if ($this->userHasRight(63, 0) == true) {
            //Speichert den< Eintrag in die Datenbank.
            if ($_POST['text'] == "" or $_POST['wert'] == "" or $_POST['kaufdat'] == "") {
                echo "<p class='meldung'>Bitte alle Felder ausfüllen</p>";
            } else {
                $autor = $this->getUserID($_SESSION['username']);
                $insert = "INSERT INTO gwcosts (text, wert, besitzer, kaufdat) 
                    VALUES ('$text','$wert','$autor','$kaufdat')";

                if ($this->sql_insert_update_delete($insert) == true) {
                    echo "<p class='erfolg'>Eintrag gespeichert.</p>";
                } else {
                    echo "<p class='meldung'>Fehler beim speichern in der Datenbank.</p>";
                }
            }
        } else {
            echo "<p class='meldung'>Keine Berechtigung</p>";
        }

    }

    /**
     * Löscht einen Eintrag aus dem Kosten Calculator
     * 
     * @return void
     */
    public function deleteDocuEintrag()
    {
        if ($this->userHasRight("63", 0) == true) {
            $this->sqlDelete("gwcosts");
        } else {
            echo "<p class='meldung'>Keine Berechtigung</p>";
        }
    }

    /**
     * Zeigt Informationen zum Account im Bereich Kosten Calculator an.
     * 
     * @return void
     */
    public function showAccountInfo()
    {

        if ($this->userHasRight(63, 0) == true) {
            echo "<div class='neuerBlog'>";
            echo "<h3>Account Info</h3>";
            $user = $this->getUserID($_SESSION['username']);
            $select2 = "SELECT DATEDIFF(NOW(), MIN(kaufdat)) AS differenz, sum(wert) AS summe FROM gwcosts WHERE besitzer = '$user'";

            $row = $this->getObjektInfo($select2);
            for ($i = 0; $i < sizeof($row); $i++) {
                $differenz = $row[0]->differenz;
                $summe = $row[0]->summe;
            }

            if ($differenz == 0) {
                $proTag = round($summe / 1, 2);
            } else {
                $proTag = round($summe / $differenz * 30, 2);
            }

            echo "<br>Dein Account ist " . $differenz . " Tage alt, pro Monat hast du also " . $proTag . " € ausgegeben.";
            echo "</div>";
        }
    }
}
/**
 * GW Kalender
 * Funktionen für den Bereich Kalender
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
class Gw_Kalender extends Guildwars
{

    /**
     * Zeigt einen Kalender für alle Guildwars Charakte an.
     * 
     * @return void
     */
    public function gwCharKalender()
    {

        if ($this->userHasRight(62, 0) == true) {

            for ($i = 1; $i <= 12; $i++) {

                echo "<div class='kalender'>";

                echo "<h2><a href='?month=$i#gebs'>" . $this->getMonthName($i) . "</a></h2>";

                $select = "SELECT *
                , month(geboren) AS monat
                , day(geboren) as tag FROM gw_chars
                WHERE month(geboren) = '$i' ORDER BY tag";

                $row = $this->getObjektInfo($select);

                $mengeGrund = $this->getObjektInfo("SELECT count(*) AS anzahl FROM gw_chars WHERE month(geboren) = '$i'");
                $menge = $mengeGrund[0]->anzahl;

                $j = 1;
                if ($menge > 4) {
                    for ($k = 0; $k < sizeof($row) and $j <= 4; $k++) {

                        $monat = $row[$k]->monat;

                        echo "<a href='charakter.php?charID=" . $row[$k]->id . "'><strong>" . $row[$k]->tag . ".</strong> "
                        . $row[$k]->name . " (" . $this->getUserName($row[$k]->besitzer) . ") </a><br>";
                        $j++;

                    }
                    echo "<br><a href='?month=$monat#gebs' class='kalenderLink'>alle</a>";
                } else { //wenn genug plazt ist.
                    for ($k = 0; $k < sizeof($row); $k++) {
                        echo "<a href='charakter.php?charID=" . $row[$k]->id . "'><strong>" . $row[$k]->tag . ".</strong> " . $row[$k]->name . " (" . $this->getUserName($row[$k]->besitzer) . ")</a><br>";
                    }
                }
                echo "</div>";
            }
        }
    }

    /**
     * Zeigt alle Geburtstage aus dem gewählten Monat an.
     * 
     * @return void
     */
    public function showMonthGesamt()
    {
        if ($this->userHasRight(62, 0) == true) {

            if (isset($_GET['month'])) {
                $monat = $_GET['month'];
                if ($monat != "") {
                    $select = "SELECT *
                    , month(geboren) AS monat
                    , day(geboren) as tag
                    , CURRENT_DATE,(YEAR(CURRENT_DATE)-YEAR(geboren))-(RIGHT(CURRENT_DATE,5)<RIGHT(geboren,5)) AS jahr
                    FROM gw_chars WHERE month(geboren) = '$monat' ORDER BY tag";

                    $row = $this->getObjektInfo($select);
                    $monthName = $this->getMonthName($monat);

                    echo "<div id='draggable' class='summe'><a class='closeSumme' href='?#gebs'>X</a>";
                    echo "<h2>Detailansicht $monthName</h2>";
                    for ($i = 0; $i < sizeof($row); $i++) {
                        //Nächsten Geb berechnen
                        $wirdalt = $row[$i]->jahr + 1;
                        echo "<div class=''>";
                        echo "<a href='charakter.php?charID=" . $row[$i]->id . "'><strong>" . $row[$i]->tag . "." . $row[$i]->monat . ":</strong> " . $row[$i]->name . " (" . $this->getUserName($row[$i]->besitzer) . ") wird " . $wirdalt . " Jahre alt</a>";
                        echo "</div>";
                    }
                    echo "</div>";

                }
            }

        } //Recht 62 Ende

    }

}

/**
 * GW_Charakter
 * Funktionen für den Bereich Charakter
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
class Gw_Charakter extends Guildwars
{

    /**
     * Auswahlwerkzeug zum Auswählen des Benutzers, dessen Charakter angezeigt werden sollen.
     * 
     * @return void
     */
    public function selectUser()
    {
        if ($this->userHasRight("3", 0) == true) {
            //Ausgabe initialisieren:
            $ausgabe = "";

            // Funktion
            if (isset($_POST['userNameForGW'])) {
                if ($_POST['userNameForGW'] != "") {
                    //Neue Sessionvariable setzen
                    $selectUser = $_POST['userNameForGW'];
                    $_SESSION['selectGWUser'] = $selectUser;

                    //Account wieder auf Hauptaccount setzen!
                    $_SESSION['gw_account'] = 1;
                }
            }

            $selectUser = isset($_SESSION['selectGWUser']) ? $_SESSION['selectGWUser'] : $_SESSION['username'];

            echo "<form action='start.php' method=post>";

            //Wenn es mehr als X Benutzer gibt, wird die Auswahlliste angezeigt.
            $limit = 5;

            //Menge aktueller Benutzer bekommen.
            $mengeGrund = $this->getObjektInfo("SELECT count(*) as anzahl FROM benutzer");
            $menge = $mengeGrund[0]->anzahl;

            //Benutzer in Array laden
            $allUsers = $this->getObjektInfo("SELECT id, Name, rights FROM benutzer ORDER BY Name");

            //Radio Buttons wenn weniger als Menge X, ansonsten Auswahlliste

            if ($menge <= $limit) {

                //Gibt Benutzer aus.
                for ($i = 0; $i < sizeof($allUsers); $i++) {

                    //Checken ob der Benutzer zugriff auf den GW-Bereich hat:
                    if ($this->userHasRight("9", $allUsers[$i]->id) == true) {

                        $currentName = $allUsers[$i]->Name;
                        echo "<input onChange='this.form.submit();' type='radio' value='$currentName' name='userNameForGW' id='$currentName' onclick=''";

                        if ($_SESSION['selectGWUser'] == $currentName) {
                            echo "checked";
                        } else {
                            echo "unchecked";
                        }

                        echo " />";

                        echo "<label for='$currentName'>$currentName</label>";

                    }
                }
            }

            //SELECT, ab bestimmter Menge
            if ($menge > $limit) {
                echo "<select class='selectUsers' onChange='this.form.submit();' name='userNameForGW' onclick='' >";

                // while($row = mysql_fetch_object($getusersErgeb)) {
                $allUsers = $this->getObjektInfo("SELECT Name FROM benutzer ORDER BY Name");

                //Gibt Benutzer aus.
                for ($i = 0; $i < sizeof($allUsers); $i++) {
                    echo "<option ";

                    if ($_SESSION['selectGWUser'] == $allUsers[$i]->Name) {
                        echo "selected";
                    }

                    echo ">" . $allUsers[$i]->Name . "</option>";
                }

                // }
                echo "</select>";
            }

            //Absenden Button
            echo "<input type='hidden' value='OK' name='selectAUser' id='buttonFocus' />";
            echo "</form>";
        }
    }

    /**
     * Zeigt die Charakter der verschiedenen Benutzer an. Diese werden in einem Raster von 3 x Y Blöcke
     * angezeigt. Dabei wird für jede Klasse ein verschiedenes Hintergrundbild angezeigt.
     * 
     * @return void
     */
    public function showChars()
    {
        if ($this->userHasRight("3", 0) == true) {

            $user = $_SESSION['selectGWUser'];

            //Setzen der Standardauswahl des Accounts
            if (!isset($_SESSION['gw_account'])) {
                $_SESSION['gw_account'] = 1;
                $account = $_SESSION['gw_account'];
            } else {
                $account = $_SESSION['gw_account'];
            }

            $user = $this->getUserID($_SESSION['selectGWUser']);
            //Wahl der Charakter-Anzeige.

            //Inititialisieren Ausgabewert:
            $ausgabe = "";

            $FehlerUNDAusgabe = "";

            //Abfrage
            //Sortierung:
            if (isset($_GET['sortierung'])) {
                $sort = $_GET['sortierung'];

                if ($sort == "name"
                    or $sort == "geboren"
                    or $sort == "rasse"
                    or $sort == "klasse"
                    or $sort == "stufe"
                    or $sort == "spielstunden"
                    or $sort == "erkundung"
                    or $sort == ""
                ) {
                    $sort = $_GET['sortierung'];
                } else {
                    $sort = "geboren";
                }

            } else {
                $sort = "geboren";
            }

            $birthdaysSelect = "SELECT
                    id, name, besitzer, geboren, rasse,	klasse,
                    stufe, handwerk1, handwerk2, handwerk1stufe,
                    handwerk2stufe, erkundung, spielstunden,
                    CURRENT_DATE,(YEAR(CURRENT_DATE)-YEAR(geboren))-(RIGHT(CURRENT_DATE,5)<RIGHT(geboren,5)) AS jahre,
                    year(timestamp) AS jahr, month(timestamp) AS monat, day(timestamp) AS tag
                    FROM gw_chars
                    WHERE besitzer = '$user' AND account = '$account' ORDER BY $sort";

            $dsatz = $this->getObjektInfo($birthdaysSelect);

            $today = date("Y/m/d");
            $tyear = date("Y");
            $tmp = array();

            //Ausrechnen: Spielstunden gesamt;
            $getAllSpielstunden = $this->getObjektInfo("SELECT sum(spielstunden) as summe FROM gw_chars WHERE besitzer = '$user' AND account = '$account'");

            //Ausgabe
            for ($i = 0; $i < sizeof($dsatz); $i++) {

                //Erstelldatum in Variable ziehen
                $gebdat = date("$tyear/m/d", strtotime($dsatz[$i]->geboren));

                //Wenn ein Geb schon vergangen ist, den fürs nächste Jahr ausrechnen
                if (strtotime($gebdat) < strtotime($today)) {
                    $gebdat = date("Y/m/d", strtotime("+1 year", strtotime($gebdat)));
                }

                //Die Tage in eineVariable laden
                $days = ceil((strtotime($gebdat) - strtotime($today)) / (24 * 3600));

                //Namen in die Variable laden
                $name = $dsatz[$i]->name;
                $klasse = $dsatz[$i]->klasse;
                $spielstunden = $dsatz[$i]->spielstunden;
                $stufe = $dsatz[$i]->stufe;
                $id = $dsatz[$i]->id;
                $rasse = $dsatz[$i]->rasse;

                //braucht man das?
                $tage = $days;
                $nameB = $name;

                //Wenn bisher keine Stunden eingetragen wurden und somit
                //die Summe aller Spielstunden NULL ist, dann entsteht
                //ein "division with NULL fehler".
                if ($getAllSpielstunden[0]->summe != 0) {
                    $ProzenzDerSpielstunden = round($spielstunden / $getAllSpielstunden[0]->summe * 100, 2);
                } else {
                    $ProzenzDerSpielstunden = 0;
                }

                //Ausgabe
                $ausgabe .= "<div class='gwstart1'>";

                $ausgabe .= "<div class='IconPlacement'>
                        <img height=60px width=60px src='../images/Icons/" . $dsatz[$i]->klasse . "Ico.png'></img></div>";

                //Klasse für CSS selektieren
                $ausgabe .= "<div id=";
                if ($dsatz[$i]->klasse == "Krieger") {
                    $ausgabe .= "'gwkrieger'";
                }
                if ($dsatz[$i]->klasse == "Wächter") {
                    $ausgabe .= "'gwwaechter'";
                }
                if ($dsatz[$i]->klasse == "Dieb") {
                    $ausgabe .= "'gwdieb'";
                }
                if ($dsatz[$i]->klasse == "Waldläufer") {
                    $ausgabe .= "'gwwaldlaeufer'";
                }
                if ($dsatz[$i]->klasse == "Ingenieur") {
                    $ausgabe .= "'gwengineer'";
                }
                if ($dsatz[$i]->klasse == "Elementarmagier") {
                    $ausgabe .= "'gwelementarmagier'";
                }
                if ($dsatz[$i]->klasse == "Nekromant") {
                    $ausgabe .= "'gwnekromant'";
                }
                if ($dsatz[$i]->klasse == "Mesmer") {
                    $ausgabe .= "'gwmesmer'";
                }
                if ($dsatz[$i]->klasse == "Widergänger") {
                    $ausgabe .= "'gwwidergaenger'";
                }
                $ausgabe .= ">";

                $ausgabe .= "<h3><a href='charakter.php?charID=" . $id . "'>" . substr($name, 0, 19) . " <br>[" . $stufe . "]</a></h3>";

                $ausgabe .= "Stunden: <strong>$spielstunden h | [$ProzenzDerSpielstunden %]</strong><br><br>";

                $ausgabe .= "
                <div class='IconRasse'>
                <img height=30px width=30px src='../images/Icons/rassen/" . $dsatz[$i]->rasse . "Ras.png'></img>
                </div>";

                if ($dsatz[$i]->geboren != "0000-00-00") {
                    $nextBday = $dsatz[$i]->jahre + 1;
                    $ausgabe .= "<p>" . $nextBday . " 'er Geburtstag in " . $tage . " Tagen </p>";
                }

                if ($dsatz[$i]->jahre < 100) {
                    $ausgabe .= "<p>Alter: " . $dsatz[$i]->jahre . " Jahre.</p>";
                }

                //Erkundungsbalken anzeigen:
                $erkundungsGroesse = $dsatz[$i]->erkundung / 100 * 270;
                if ($dsatz[$i]->erkundung == 100) {
                    $stern = "&#10039;";
                    $ausgabe .= "
                    <style>
                    .StyleGW" . $dsatz[$i]->id . " {
                            background-color: green;
                            width: " . $erkundungsGroesse . "px;
                            position: relative;
                            border: 1px solid green;
                            bottom: -6px;
                            left: -5px;
                            height: 3px;
                            box-shadow: 0px 0px 2px green;
                            border-radius: 0px 3px 3px 0px;
                            opacity: 0.4;
                    }
                    </style> ";

                } else {
                    $ausgabe .= "
                    <style>
                    .StyleGW" . $dsatz[$i]->id . " {
                            background-color: orange;
                            width: " . $erkundungsGroesse . "px;
                            position: relative;
                            border: 1px solid orange;
                            bottom: -6px;
                            left: -5px;
                            height: 3px;
                            box-shadow: 0px 0px 2px orange;
                            border-radius: 0px 3px 3px 0px;
                            opacity: 0.4;
                    }

                    </style> ";
                    $stern = "";
                }
                //Style für Erkundung: Ende

                //Ausgabe für Erkundung:
                $ausgabe .= "<div id='erkundungAussen'>" . $dsatz[$i]->erkundung . " % Erkundet <strong>$stern</strong>" . "</div><p class='StyleGW" . $dsatz[$i]->id . "'></p>";

                //Bearbeitet am:
                $ausgabe .= "<p id='lowerInfo'>Bearbeitet " . $dsatz[$i]->tag . "." . $dsatz[$i]->monat . "." . $dsatz[$i]->jahr . "</p>";

                //Close tags
                $ausgabe .= "</div>";
                $ausgabe .= "</div>";

            }

            //Seite erweitern, je nachdem wie viele Chars der Account / Benutzer hat.

            $getAnzahlGwCharsGrund = $this->getObjektInfo("SELECT count(*) as anzahl FROM gw_chars WHERE besitzer = '$user' AND account = '$account' ");
            $getAnzahlAnzahl = $getAnzahlGwCharsGrund[0]->anzahl;
            $getAnzahlAnzahl = $getAnzahlAnzahl / 3;

            for ($i = 0; $i < $getAnzahlAnzahl; $i++) {
                $ausgabe .= "<br><br><br><br><br><br><br><br><br><br>";
            }

            return $ausgabe;
        }
    }

    /**
     * Zeigt eine einfache Liste von den Charaktern, 
     * die dem angemeldeten User gehören.
     * 
     * @return void
     */
    public function ListOfChars()
    {
        if ($this->userHasRight(61, 0) == true) {
            //Ausgabe initialiseren:
            $ausgabe = "";
            //Wahl der Charakter-Anzeige.
            $user = $this->getUserID($_SESSION['selectGWUser']);
            //Account
            $account = $_SESSION['gw_account'];
            $getchars = "SELECT id, timestamp, name, besitzer, geboren, rasse, klasse, stufe, handwerk1, handwerk2,
            handwerk1stufe, handwerk2stufe, erkundung, spielstunden, account
            FROM gw_chars
            WHERE besitzer = '$user' AND account = '$account' ORDER BY geboren";
            $row = $this->getObjektInfo($getchars);
            $ausgabe .= "<ul>";
            for ($i = 0; $i < sizeof($row); $i++) {
                $ausgabe .= "<li><a href='?charID=" . $row[$i]->id . "'>" . $row[$i]->name . "</a></li>";
            }
            $ausgabe .= "</ul>";
            return $ausgabe;
        }
    }

    /**
     * Gibt die Informationen zu einem bestimmten Charakter aus.
     * 
     * @param unknown $id CharID
     * 
     * @return void
     */
    public function getInfoCharID($id)
    {
        global $row;
        $user = $_SESSION['selectGWUser'];
        $getFirstCharInfo = "SELECT id, name, besitzer, geboren, rasse, klasse, stufe, handwerk1, handwerk2,
        handwerk1stufe, handwerk2stufe, erkundung, spielstunden, notizen FROM gw_chars WHERE id = '$id'";
        $row = $this->getObjektInfo($getFirstCharInfo);

        //CURRENT USER:
        $currentUser = $this->getUserID($_SESSION['username']);

        //Abfangen, wenn kein Charakter gefunden wurde und die Variable leer ist.
        if (!isset($row[0]->besitzer)) {
            echo "<p class='meldung'>Hör auf in der Adressleiste rumzuspielen!!!</p>";
            exit;
        }
        $charOwner = $row[0]->besitzer;
        if ($this->userHasRight("9", 0) == false and $currentUser != $charOwner) {
            echo "<p class='meldung'>Du darfst dir diesen Charakter nicht ansehen.</p>";
            exit;
        }

        if ($row == "") {
            echo "<p class='meldung'>Hör auf in der Adressleiste rumzuspielen!!!</p>";
            exit;
        }
    }

    /**
     * Ermöglicht das erstellen eines neuen Chars.
     * 
     * @return void
     */
    public function newChar()
    {
        //Ausgabe initialiseren:
        $ausgabe = "";

        //Fehlermeldungen initialiseren:
        $fehlermeldung = "";

        //Prüfen ob ein neuer Char erstellt werden soll.
        if (isset($_GET['createNewChar'])) {

            //Wenn das JA ist:
            if ($_GET['createNewChar'] == "yes") {

                //Check ob User Aktion durchführen darf.
                if ($this->userHasRight("9", 0) == true) {

                    //Username zuweisen
                    $username = $_SESSION['selectGWUser'];
                    $userID = $this->getUserID($_SESSION['selectGWUser']);

                    $ausgabe .= "<div id='draggable' class='newChar'>";
                    if ($username != $_SESSION['username']) {
                        $ausgabe .= "<p class='info'>Achtung, du legst einen Charakter für <strong>$username</strong> an.</p>";
                    }
                    $ausgabe .= "<form method=post>";
                    $ausgabe .= "<div id='right'>

                            <a href='?#charErstellen' class='highlightedLink'>Schließen</a></div>";
                    $ausgabe .= "<h2><a name='charErstellen'>Charaktererstellung</a></h2>";

                    $ausgabe .= "<div>";
                    $ausgabe .= "<table>";
                    $ausgabe .= "<tr>";

                    $ausgabe .= "<td colspan = '4'>Charaktererstellung für: <input type=text name='besitzer' value='$username' readonly />";

                    //Accounts der Besitzer anzeigen
                    $ausgabe .= "<select name='account' value=''>";

                    //Accounts ermitteln
                    $getAccounts = "SELECT * FROM gw_accounts WHERE besitzer = '$userID'";
                    $row2 = $this->getObjektInfo($getAccounts);

                    for ($i = 0; $i < sizeof($row2); $i++) {

                        $ausgabe .= "<option ";

                        if ($_SESSION['gw_account'] == $row2[$i]->account) {
                            $ausgabe .= " selected ";
                        }
                        $ausgabe .= " value='" . $row2[$i]->account . "'>" . $row2[$i]->mail . "</option>";
                    }

                    $ausgabe .= "</select></td>";

                    $ausgabe .= "</tr>";

                    $ausgabe .= "<tr>";
                    $ausgabe .= "<td>Name</td>";
                    $ausgabe .= "<td><input type=text name='newCharName' value=''
                            required
                            autofocus
                            pattern='[ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyzÀÁÂÄÆÇÈÉÊËÍÎÑÓÔÖŒÙÚÛÜàáâäæçèéêëíîñóôöœùúûüß ]{3,19}'
                            /></td>";

                    $ausgabe .= "<td>Stufe</td><td><input type=number name='newstufe' value='' placeholder='0 - 80' /></td>";

                    $ausgabe .= "</tr>";

                    $ausgabe .= "<tr>";
                    $ausgabe .= "<td>Rasse</td>";
                    $ausgabe .= "<td><select name='newrasse' value='' />";
                    $ausgabe .= "<option>Menschen</option>";
                    $ausgabe .= "<option>Asura</option>";
                    $ausgabe .= "<option>Sylvari</option>";
                    $ausgabe .= "<option>Norn</option>";
                    $ausgabe .= "<option>Charr</option>";
                    $ausgabe .= "</select></td>";
                    $ausgabe .= "</tr>";

                    $ausgabe .= "<tr>";
                    $ausgabe .= "<td>Klasse</td>";
                    $ausgabe .= "<td><select name='newklasse' value='' />";
                    $ausgabe .= "<option>Krieger</option>";
                    $ausgabe .= "<option>Wächter</option>";
                    $ausgabe .= "<option>Dieb</option>";
                    $ausgabe .= "<option>Waldläufer</option>";
                    $ausgabe .= "<option>Ingenieur</option>";
                    $ausgabe .= "<option>Elementarmagier</option>";
                    $ausgabe .= "<option>Nekromant</option>";
                    $ausgabe .= "<option>Mesmer</option>";
                    $ausgabe .= "<option>Widergänger</option>";
                    $ausgabe .= "</select></td>";
                    $ausgabe .= "</tr>";

                    $ausgabe .= "<tr>";

                    $ausgabe .= "<td>Alter </td>";
                    $ausgabe .= "<td><input type=number name='daysOld' value='' placeholder='in Tagen ( GW /age)' /></td>";
                    $ausgabe .= "<td>oder Erstelldatum:</td>";
                    $ausgabe .= "<td><input type=date (yyyy-mm-dd) name='newbirth' value='' placeholder='z. B. 2014-06-24' /></td>";

                    $ausgabe .= "</tr>";

                    $ausgabe .= "<tr>";
                    $ausgabe .= "<td>Handwerk 1</td>";
                    $ausgabe .= "<td><select name='newhandwerk1' value='' />";
                    $ausgabe .= "<option></option>";
                    $ausgabe .= "<option>Lederer</option>";
                    $ausgabe .= "<option>Schneider</option>";
                    $ausgabe .= "<option>Koch</option>";
                    $ausgabe .= "<option>Rüstungsschmied</option>";
                    $ausgabe .= "<option>Waffenschmied</option>";
                    $ausgabe .= "<option>Waidmann</option>";
                    $ausgabe .= "<option>Konstukteur</option>";
                    $ausgabe .= "<option>Waffenschmied</option>";
                    $ausgabe .= "<option>Juwelier</option>";
                    $ausgabe .= "</select></td>";
                    $ausgabe .= "<td>Stufe</td><td><input type=number name='newhandwerk1stufe' value='' placeholder='0 - 500' /></td>";
                    $ausgabe .= "</tr>";

                    $ausgabe .= "<tr>";
                    $ausgabe .= "<td>Handwerk 2</td>";
                    $ausgabe .= "<td><select name='newhandwerk2' value='' />";
                    $ausgabe .= "<option></option>";
                    $ausgabe .= "<option>Lederer</option>";
                    $ausgabe .= "<option>Schneider</option>";
                    $ausgabe .= "<option>Koch</option>";
                    $ausgabe .= "<option>Rüstungsschmied</option>";
                    $ausgabe .= "<option>Waffenschmied</option>";
                    $ausgabe .= "<option>Waidmann</option>";
                    $ausgabe .= "<option>Konstukteur</option>";
                    $ausgabe .= "<option>Waffenschmied</option>";
                    $ausgabe .= "<option>Juwelier</option>";
                    $ausgabe .= "</select></td>";
                    $ausgabe .= "<td>Stufe</td><td><input type=number name='newhandwerk2stufe' value='' placeholder='0 - 500' /></td>";
                    $ausgabe .= "</tr>";

                    $ausgabe .= "<tr>";
                    $ausgabe .= "<td>Spielstunden</td>";
                    $ausgabe .= "<td><input type=number name='newspielstunden' value='' placeholder='z. B. 5' /></td>";
                    $ausgabe .= "</tr>";

                    $ausgabe .= "<tr>";
                    $ausgabe .= "<td>Erkundung</td>";
                    $ausgabe .= "<td><input type=number name='newerkundung' value='' placeholder='z. B. 76 ohne %' /></td>";
                    $ausgabe .= "</tr>";

                    $ausgabe .= "<tr></tr>";
                    $ausgabe .= "<tr><td><input type='submit' name='charerstellen' value='Speichern' /></td></tr>";
                    $ausgabe .= "</table>";
                    $ausgabe .= "</div>";
                    $ausgabe .= "</form>";
                    $ausgabe .= "</div>";
                } else {
                    $fehlermeldung .= "<p class='meldung'>Keine Berechtigung</p>";
                }
            }
        }

        /**
         * Speichert den neu erstellten Charakter in der Datenbank
         */
        if (isset($_POST['charerstellen'])) {
            if ($this->userHasRight("9", 0) == true) {

                if ($_POST['charerstellen'] != "") {
                    //Check ob alle Felder ausgefüllt sind.
                    if ($_POST['newCharName'] == "") {
                        $fehlermeldung .= "<p class='meldung'>Es muss mindestens der Charaktername eingetragen werden.</p>";
                    } else {
                        //Variablen zuweisen:
                        $charname = $_POST['newCharName'];
                        //check ob name bereits existiert.
                        $check = "SELECT name FROM gw_chars WHERE name = '$charname'";
                        $row = $this->getObjektInfo($check);
                        if (isset($row[0]->name)) {
                            if ($row[0]->name == $charname) {
                                $fehlermeldung .= "<p class='meldung'>Der Charakter existiert bereits.</p>";
                            }
                        } else {

                            //Rest der Variablen zuweisen
                            $charname = $_POST['newCharName'];
                            $besitzer = $this->getUserID($_POST['besitzer']);
                            $geboren = $_POST['newbirth'];
                            $rasse = $_POST['newrasse'];
                            $klasse = $_POST['newklasse'];
                            $stufe = $_POST['newstufe'];
                            $handwerk1 = $_POST['newhandwerk1'];
                            $handwerk2 = $_POST['newhandwerk2'];
                            $handwerk1stufe = $_POST['newhandwerk1stufe'];
                            $handwerk2stufe = $_POST['newhandwerk2stufe'];
                            $spielstunden = $_POST['newspielstunden'];
                            $erkundung = $_POST['newerkundung'];
                            // $notizen = $_POST['newnotizen'];

                            if (isset($_POST['daysOld'])) {
                                if ($_POST['daysOld'] != "") {
                                    $daysOld = $_POST['daysOld'];
                                    // Berechnung des Erstellungsdatums
                                    $timestamp = time();
                                    $tage = $daysOld * 24 * 60 * 60;
                                    $timestamp = $timestamp - $tage;
                                    $geboren = date("Y-m-d", $timestamp);
                                }
                            }

                            if (isset($_POST['account']) and $_POST['account'] != "") {
                                $account = $_POST['account'];
                            } else {
                                $account = 1;
                            }

                            if ($stufe > 80 or $handwerk1stufe > 500 or $handwerk2stufe > 500) {
                                $fehlermeldung .= "<p class='meldung'>Beachte die maximalen möglichen Stufen!</p>";
                            }

                            $insert = "INSERT INTO gw_chars (name, besitzer, geboren, rasse,
                            klasse, stufe, handwerk1, handwerk2, handwerk1stufe, handwerk2stufe,
                            erkundung, spielstunden, account)
                            VALUES
                            ('$charname','$besitzer','$geboren','$rasse','$klasse','$stufe',
                            '$handwerk1','$handwerk2','$handwerk1stufe','$handwerk2stufe',
                            '$erkundung','$spielstunden','$account')";

                            if ($this->sql_insert_update_delete($insert) == true) {
                                $fehlermeldung .= '<p class="erfolg">Charakter angelegt.</p>';
                            } else {
                                $fehlermeldung .= "<p class='meldung'>Fehler</p>";
                            }

                        }
                    }
                }
            }
        }
        //Ausgabe zusammenbauen:
        $FehlerUNDAusgabe = "<br>" . $fehlermeldung . "<br>" . $ausgabe;

        //Ausgabe
        return $FehlerUNDAusgabe;
    }

    /**
     * Speichert die Änderungen in der Charakter.php
     * oder löscht den entsprechenden Char aus der Datenbank
     * 
     * @return void
     */
    public function bearbChar()
    {

        if (isset($_POST['action'])) {
            if ($_POST['action'] == "speichern") {

                //Check ob User Aktion durchführen darf.
                if (isset($_POST['besitzer'])) {
                    $PostBesitzer = $_POST['besitzer'];
                    $charID = (isset($_GET['charID'])) ? $_GET['charID'] : '';
                    $besitzer = $this->getBesitzer($charID);

                    //Schutz: PHP Sicherheit!
                    if ($PostBesitzer != $besitzer) {
                        $this->logEintrag(true, "Es wurde versucht, die Sperrung im Bereich Guildwars der Charakterbearbeitung zu umgehen. ", "illegal");
                        return false;
                        exit;
                    }
                }

                if ($this->userHasRight("9", 0) == true and $this->getUserID($_SESSION['username']) == $besitzer or $this->userHasRight("21", 0) == true) {
                    //Übernahme der POST Variablen
                    $stufe = (isset($_POST['stufeChar'])) ? $_POST['stufeChar'] : '';
                    $geboren = (isset($_POST['geboren'])) ? $_POST['geboren'] : '';
                    $stunden = (isset($_POST['stunden'])) ? $_POST['stunden'] : '';
                    $handwerk1 = (isset($_POST['handwerk1'])) ? $_POST['handwerk1'] : '';
                    $handwerk2 = (isset($_POST['handwerk2'])) ? $_POST['handwerk2'] : '';
                    $handwerk1stufe = (isset($_POST['handwerk1stufe'])) ? $_POST['handwerk1stufe'] : '';
                    $handwerk2stufe = (isset($_POST['handwerk2stufe'])) ? $_POST['handwerk2stufe'] : '';
                    $erkundung = (isset($_POST['erkundung'])) ? $_POST['erkundung'] : '';
                    $notizen = (isset($_POST['charNotizen'])) ? $_POST['charNotizen'] : '';
                    $charID = (isset($_GET['charID'])) ? $_GET['charID'] : '';
                    $rasse = (isset($_POST['rasse'])) ? $_POST['rasse'] : '';
                    $klasse = (isset($_POST['klasse'])) ? $_POST['klasse'] : '';
                    $name = (isset($_POST['charName'])) ? $_POST['charName'] : '';

                    //mysql Befehle
                    $charUpdate = "UPDATE gw_chars SET
                    stufe='$stufe', handwerk1='$handwerk1', handwerk2='$handwerk2',
                    handwerk1stufe='$handwerk1stufe', handwerk2stufe='$handwerk2stufe',
                    notizen='$notizen', spielstunden='$stunden', geboren='$geboren',
                    erkundung='$erkundung', name='$name', rasse='$rasse', klasse='$klasse'
                    WHERE id='$charID'";

                    if ($this->sql_insert_update_delete($charUpdate) == true) {
                        echo "<p class='erfolg'>Der Charakter wurde geändert!</p>";
                    }
                } else {
                    echo "<p class='meldung'>Du bist nicht der Besitzer
                            oder hast keine Berechtigung Charakter zu ändern.</p>";
                }

            } else if ($_POST['action'] == "löschen") {
                //Check ob User Aktion durchführen darf.
                if ($this->userHasRight("9", 0) == true or $this->userHasRight("21", 0) == true) {

                    //Löschung an andere Funktion übergeben
                    $charID = (isset($_GET['charID'])) ? $_GET['charID'] : '';
                    $this->delChar($charID, "gw_chars");

                } else {

                    echo "<p class='meldung'>Keine Berechtigung</p>";

                }
            }
        }

    }

    /**
     * Löscht den ausgewählten Charakter aus der Datenbank, vorher wird eine Bestätigung eingeholt.
     * 
     * @param int    $id      CharID
     * @param string $tabelle Name der Tabelle
     * 
     * @return void
     */
    public function delChar($id, $tabelle)
    {

        if ($this->userHasRight("9", 0) == true and $this->getUserID($_SESSION['username']) == $this->getBesitzer($id) or $this->userHasRight("21", 0) == true) {
            echo "<p class='meldung'>Sicher, dass der Charakter <strong>" . $this->getCharname($id) . "</strong> gelöscht werden soll?</p>";
            echo "<form method=post>";
            echo "<input type=hidden value='$id' name='loeschid' readonly />";
            echo "<input type=hidden value='löschen' name='action' readonly />";
            echo "<input type=submit value='Ja' name='loeschen' />";
            echo "</form>";
        } else {
            echo "<p class='meldung'>Du darfst diesen Charakter nicht löschen.</p>";
        }

        /*
         * Führt die Löschung durch.
         */
        $jaloeschen = isset($_POST['loeschen']) ? $_POST['loeschen'] : '';
        $loeschid = isset($_POST['loeschid']) ? $_POST['loeschid'] : '';

        if ($this->userHasRight("9", 0) == true and $this->getUserID($_SESSION['username']) == $this->getBesitzer($loeschid) or $this->userHasRight("21", 0) == true) {

            if ($jaloeschen == "Ja") {

                //Stunden in Account_info schreiben
                $this->saveCharakterStunden($loeschid);

                $loeschQuery = "DELETE FROM `$tabelle` WHERE `id` = $loeschid";

                if ($this->sql_insert_update_delete($loeschQuery) == true) {
                    echo "<p class='erfolg'>Der Charakter wurde gelöscht!</p>";
                    exit;
                } else {
                    echo "<p id='meldung'>Es gab einen Fehler. <a href='start.php'>Zurück</a></p>";
                }
            }
        }
    }

    /**
     * Sichert die Stunden des zu löschenden Charakters in der Tabelle account_infos.
     * 
     * @param unknown $id CharID
     * 
     * @return void
     */
    public function saveCharakterStunden($id)
    {

        $userID = $this->getUserID($_SESSION['username']);

        //Charakterstunden bekommen
        $charInfos = $this->getObjektInfo("SELECT * FROM gw_chars WHERE besitzer = '$userID' AND id = '$id' LIMIT 1");
        $charAccount = $charInfos[0]->account;
        //bisherige gelöschte Stunden bekommen:
        $bisherigeStunden = $this->getObjektInfo("SELECT * FROM account_infos WHERE besitzer = '$userID' AND attribut = 'gw_geloschte_stunden' AND account = '$charAccount' LIMIT 1");
        //Neuen Wert ausrechnen
        $neuerWert = $bisherigeStunden[0]->wert + $charInfos[0]->spielstunden;

        //Update oder Insert durchführen.
        if (!isset($bisherigeStunden[0]->wert) or $bisherigeStunden[0]->wert == "") {
            $this->sql_insert_update_delete("INSERT INTO account_infos (besitzer, attribut, wert, account) VALUES ('$userID','gw_geloschte_stunden','0','$charAccount')");
            //jetzt updaten
            $this->sql_insert_update_delete("UPDATE account_infos SET wert='$neuerWert' WHERE besitzer = '$userID' AND attribut = 'gw_geloschte_stunden' AND account='$charAccount'");
        } else {
            $this->sql_insert_update_delete("UPDATE account_infos SET wert='$neuerWert' WHERE besitzer = '$userID' AND attribut = 'gw_geloschte_stunden' AND account='$charAccount'");
        }
    }

    /**
     * Erzeugt eine Kachel an der rechten Seite des Bildschirms.
     * Hier tauchen Informationen zum Account auf, darunter fällt:
     * Spielstunden,
     * davon gelöscht,
     * Welt erkundet,
     * Anzahl der Chars,
     * und wer demnächst Geburtstag hat.
     * 
     * @return void
     */
    public function globalCharInfos()
    {

        if ($this->userHasRight("64", 0) == true) {

            echo "<div class='GWCharInfos'>";
            //user Identifikation
            $userID = $this->getUserID($_SESSION['selectGWUser']);
            $username = $this->getUserName($userID);
            if (!isset($_SESSION['gw_account'])) {
                $aktuellerAccount = 1;
            } else {
                $aktuellerAccount = $_SESSION['gw_account'];
            }

            //Erkundung
            $eigeneWeltErkundet = $this->getObjektInfo("SELECT sum(erkundung) as summe FROM gw_chars WHERE besitzer = '$userID' AND account = '$aktuellerAccount'");
            $alleWeltErkundet = $this->getObjektInfo("SELECT sum(erkundung) as summe FROM gw_chars");
            $eigeneErkundung = $eigeneWeltErkundet[0]->summe / 100;
            $alleErkundung = $alleWeltErkundet[0]->summe / 100;

            //Anzahl Charakter
            $summeChars = $this->getObjektInfo("SELECT count(*) as anzahl FROM gw_chars WHERE besitzer = '$userID' AND account = '$aktuellerAccount'");

            //Spielstunden:
            $alleEigenenStunden = $this->getObjektInfo("SELECT sum(spielstunden) as summe FROM gw_chars WHERE besitzer = '$userID' AND account = '$aktuellerAccount'");
            $alleSpielstunden = $this->getObjektInfo("SELECT sum(spielstunden) as summe FROM gw_chars");

            $gelöschteStundenGesamt = $this->getObjektInfo("SELECT sum(wert) as summe FROM account_infos WHERE attribut = 'gw_geloschte_stunden'");
            $EigeneGelöschteStunden = $this->getObjektInfo("SELECT sum(wert) as summe FROM account_infos WHERE besitzer = '$userID' AND attribut = 'gw_geloschte_stunden' AND account = '$aktuellerAccount'");
            $eigeneStundenGesamt = $EigeneGelöschteStunden[0]->summe + $alleEigenenStunden[0]->summe;
            $allePlusGelöschte = $alleSpielstunden[0]->summe + $gelöschteStundenGesamt[0]->summe;
            if ($allePlusGelöschte > 0) {
                $prozent = round($alleEigenenStunden[0]->summe / $alleSpielstunden[0]->summe * 100, 2);
            } else {
                $prozent = 0;
            }

            echo "<h2>$username</h2>";

            echo "<table id='table'>";

            echo "<tr><td>Spielstunden:<br> <strong>" . $eigeneStundenGesamt . " [$prozent %] </strong><br>
            davon gelöscht:<br> <strong>" . $EigeneGelöschteStunden[0]->summe . "</strong> <br>
            von insgesamt <strong><br> " . $allePlusGelöschte . "</strong> Stunden</td></tr>";

            echo "<tr><td>Welt erkundet:<br> " . $eigeneErkundung . "x von " . $alleErkundung . "x</td></tr>";
            echo "<tr><td>Anzahl Chars: " . $summeChars[0]->anzahl . " Stk.</td></tr>";

            //Der Nächste Geburtstag:

            $timestamp = time();
            $monat = date("m", $timestamp);
            $tag = date("d", $timestamp);
            $currentMonth = $monat;

            $query = "SELECT *, day(geboren) as tag FROM `gw_chars` WHERE month(geboren) = '$currentMonth' AND day(geboren) >= '$tag' ORDER BY `tag` ASC ";
            if ($this->getObjektInfo($query) == true) {
                echo "<tr><td>Nächster Geb.:</td></tr>";
                $naechsterGeb = $this->getObjektInfo($query);
                echo "<tr><td><p class='erfolg'>" . $naechsterGeb[0]->name . " am " . $naechsterGeb[0]->tag . ".$currentMonth</p></td></tr>";
            }

            echo "</table>";
            echo "</div>";
        }
    }
}

/**
 * GW_Handwerk
 * Funktionen für den Bereich Handwerk
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
class Gw_Handwerk extends Guildwars
{

    /**
     * Zeigt die Materialien die der Account bestitzt.
     * ES GIBT 390 MATERIALIEN!!!! Mit der Erweiterung von HOT!!!
     * 
     * @return void
     */
    public function showMatList()
    {
        if ($this->userHasRight("16", 0) == true) {

            //   $gesamtanzahl = 351;
            //Maximale matID bekommen:
            $query = "SELECT max(matID) as max FROM gwmatlist";
            $max = $this->getObjektInfo($query);
            $neueGesamtZahlMitErweiterung = $max[0]->max;

            $this->saveAllMats($neueGesamtZahlMitErweiterung);

            echo "<form action ='#handwerksmats' method=post>";

            echo "<table class='handwerkField'>";

            echo "<thead><td colspan='2'><input type=submit name=absenden value='Materialien speichern' /></td>
                    <td></td><td></td><td></td><td></td><td></td><td></td>
                    <td colspan='2'>";
            echo "</td>
                    </thead>";

            echo "<tbody>";

            /* Neue Materialien */

            $katNames = ["Gewöhnliche Handwerksmaterialien"
                , "Edle Handwerksmaterialien"
                , "Seltene Handwerksmaterialien"
                , "Aufgestiegene Handwerksmaterialien"
                , "Edelsteine und Juwelen"
                , "Zutaten zum Kochen"
                , "Festliche Materialien"];

            $anzahlKategorien = 7;
            for ($i = 0; $i < $anzahlKategorien; $i++) {
                echo "<thead>";
                echo "<td colspan='7'><h2>$katNames[$i]</h2></td>
                <td colspan='3'><input type=submit name=absenden value='Materialien speichern' /></td>";
                echo "</thead>";

                //Materialien dieser Kategorie rausfiltern:
                $kat = $i + 1;
                $matsDieserKat = $this->getObjektInfo("SELECT * FROM gwmatlist WHERE kategorie = '$kat' ORDER BY matID");
                $zähler = 0;
                echo "<tbody>";
                for ($j = 0; $j < sizeof($matsDieserKat); $j++) {

                    if ($zähler == 10) {
                        echo "</tbody>";
                        echo "<tbody>";
                        $zähler = 0;
                    }

                    $name = $matsDieserKat[$j]->matID;
                    //Aktuelle Anzahl des aktuellen Accounts bekommen:
                    $matAnzahl = $this->getMatAnzahlFromCurrentUser($name);
                    if ($matsDieserKat[$j]->matID > 351) {
                        echo "<td id='thorns'>";
                    } else {
                        echo "<td>";
                    }
                    echo "" . $matsDieserKat[$j]->matName . "<br>" . "<input type=number name='name[$name]' value='$matAnzahl' placeholder='" . $matsDieserKat[$j]->matName . "' />" . "</td>";
                    $zähler = $zähler + 1;

                }

            }

            echo "</table>";
            echo "<input type=submit name=absenden value='Speichern' />";
            echo "</form>";

            $this->createNewMats();
        }
    }

    /**
     * Ermöglicht es, neue Mats zu erstellen
     * 
     * @return void
     */
    public function createNewMats()
    {
        if ($this->userHasRight("21", 0) == true) { //Nur wenn er GW Admin ist.
            //Maximale matID bekommen:
            $query = "SELECT max(matID) as max FROM gwmatlist";
            $max = $this->getObjektInfo($query);
            $nextUsefulID = $max[0]->max + 1;

            if (isset($_POST['saveNewMats'])) {

                //Übername
                $matName = $_POST['newMatName'];
                $matKat = $_POST['newMatKat'];
                $matPrice = $_POST['newMatPrice'];

                for ($i = 0; $i < 10; $i++) {
                    if ($matName[$i] != "" and $matKat[$i] != "" and $matPrice[$i] != "") {

                        $name = $matName[$i];
                        $kat = $matKat[$i];
                        $price = $matPrice[$i];

                        $query = "INSERT INTO gwmatlist (matID, matName, matPrice, kategorie) VALUES ('$nextUsefulID', '$name', '$price', '$kat')";

                        if ($this->sql_insert_update_delete($query) == true) {
                            echo "<p class='erfolg'>Erfolg, $name in Kategorie $kat mit Preis $price gespeichert.</p>";
                            $nextUsefulID = $nextUsefulID + 1;
                        } else {
                            echo "<p class='meldung'>Fehler, $name in Kategorie $kat mit Preis $price konnte nicht gespeichert werden.</p>";
                            exit;
                        }
                    }

                }

            }

            $katNames = ["Gewöhnliche Handwerksmaterialien"
                , "Edle Handwerksmaterialien"
                , "Seltene Handwerksmaterialien"
                , "Aufgestiegene Handwerksmaterialien"
                , "Edelsteine und Juwelen"
                , "Zutaten zum Kochen"
                , "Festliche Materialien"];

            echo "<div class='newChar'><form method=post action='#newMats'>";

            echo "<h2><a name='newMats'>Neue Materialien erstellen</a></h2>";
            echo "<a href='?reload#newMats' class='buttonlink' >Seite neu laden</a>";

            echo "<p>Hier können neue Materialien erstellt werden.</p>";

            //Infos:
            echo "<h3>Informationen</h3>";
            echo "<p>Es gibt derzeit " . $max[0]->max . " Materialien. </p>";

            echo "<table class='flatnetTable'>";
            echo "<thead><td>Name</td><td>Kategorie</td><td>Preis in Kupfer beim Kaufmann</td></thead>";
            for ($i = 0; $i < 10; $i++) {
                echo "<tbody>";
                echo "<td><input type=text name=newMatName[$i] value='' placeholder='Materialname' /></td>";
                echo "<td>";
                echo "<select name=newMatKat[$i]>";
                echo "<option></option>";
                for ($j = 0; $j < sizeof($katNames); $j++) {
                    $value = $j + 1;
                    echo "<option value='$value'>";
                    echo $katNames[$j];
                    echo "</option>";
                }
                echo "</select>";
                echo "</td>";
                echo "<td><input type=number name=newMatPrice[$i] value='0' placeholder='Materialpreis in Kupfer (Kaufmann)' /></td>";
                echo "</tbody>";
            }
            echo "</table>";
            echo "<input type=submit name=saveNewMats value='Absenden' />";

            echo "</form></div>";
        }
    }

    /**
     * Gibt den Namen des Materials zurück
     * 
     * @param int $id MatID
     * 
     * @return string
     */
    public function getMatName($id)
    {
        $select = "SELECT * FROM gwmatlist WHERE matID = '$id' LIMIT 1";

        $info = $this->getObjektInfo($select);
        if (isset($info[0]->matName)) {
            $name = $info[0]->matName;
        } else {
            $name = "none";
        }

        return $name;
    }

    /**
     * Gibt die Anzahl der Mats des aktuzellen Benutzers wieder.
     * 
     * @param unknown $mat MatID
     * 
     * @return number
     */
    public function getMatAnzahlFromCurrentUser($mat)
    {
        $user = $this->getUserID($_SESSION['username']);

        $account = $_SESSION['gw_account'];
        if ($account == "") {
            $account = 1;
        }
        $select = "SELECT * FROM gwusersmats WHERE matID = '$mat' AND besitzer = '$user' AND account = '$account' LIMIT 1";
        $row = $this->getObjektInfo($select);

        if (isset($row[0]->matAnzahl)) {
            $anzahl = $row[0]->matAnzahl;
        } else {
            $anzahl = 0;
        }

        return $anzahl;
    }

    /**
     * Speichert alle Materialien für diesen Benutzer.
     * 
     * @param int $gesamtzahl Anzahl aller Mats
     * 
     * @return void
     */
    public function saveAllMats($gesamtzahl)
    {
        if (isset($_POST['absenden'])) {

            if ($this->userHasRight("16", 0) == true) {

                //   $gesamtzahl = 390;
                $user = $this->getUserID($_SESSION['username']);

                $erfolg = 0;
                $fehler = 0;

                //Setzen der Standardauswahl des Accounts
                $account = $_SESSION['gw_account'];
                if ($account == "") {
                    $account = 1;
                }

                for ($i = 1; $i <= $gesamtzahl; $i++) {

                    //MatAnzahl bekommen
                    $matAnzahl = $_POST['name'][$i];
                    $matID = $i;
                    $account = $_SESSION['gw_account'];

                    //Hier wird geprüft, ob der Wert größer oder gleich Null ist.
                    //Früher wurden die Einträge nur gespeichert, wenn die Anzahl größer als Null ist,
                    //aber das führte dazu, dass man seine Einträge nicht wieder auf Null setzen konnte ...
                    if ($matAnzahl >= 0) {

                        //Schauen ob es die MAT ID Schon gibt für diesen User:
                        if ($this->matIDexistsForUser($matID) == true) {
                            $query = "UPDATE gwusersmats
                            SET matAnzahl='$matAnzahl'
                            WHERE matID='$matID'
                            AND besitzer = '$user'
                            AND account='$account'";
                        } else {
                            $query = "INSERT INTO gwusersmats (besitzer, matID, matAnzahl, account)
                            VALUES ('$user','$matID','$matAnzahl','$account')";
                        }

                        //Extrawurst für das Handwerk, weil sonst das Log vollgespammt wird.
                        if ($this->sql_insert_update_delete_hw($query) == true) {
                            $erfolg = $erfolg + 1;
                        } else {
                            $fehler = $fehler + 1;
                        }
                    }
                }
                if ($fehler > 0) {
                    echo "<p class='dezentInfo'>Übersprungene Felder: " . $fehler . " (wurden nicht geändert)</p>";
                    if ($erfolg > 0) {
                        echo "<p class='erfolg'>Erfolgreich gespeicherte Änderungen: " . $erfolg . "</p>";
                        $this->logme("Handwerksmats gespeichert, gespeicherte Änderungen: $erfolg, nicht gespeicherte Änderungen: $fehler");
                    } else {
                        echo "<p class='dezentInfo'>Du hast keine Ändeurngen vorgenommen.</p>";
                    }
                } else {
                    echo "<p class='erfolg'>Deine Materialien wurden alle gespeichert!</p>";
                    $this->logme("INITIAL Handwerkmats gespeichert (alle Materialien)");
                }
            }
        }
    }

    /**
     * Prüft, ob der Benutzer dieses Material bereits in seiner Liste hat.
     * 
     * @param unknown $id MatID
     * 
     * @return boolean
     */
    public function matIDexistsForUser($id)
    {

        //Setzen der Standardauswahl des Accounts
        if (!isset($_SESSION['gw_account'])) {
            $_SESSION['gw_account'] = 1;
            $account = $_SESSION['gw_account'];
        } else {
            $account = $_SESSION['gw_account'];
        }

        $user = $this->getUserID($_SESSION['username']);
        $select = "SELECT * FROM gwusersmats
        WHERE matID = '$id'
        AND besitzer = '$user'
        AND account='$account'";

        $row = $this->getObjektInfo($select);

        if (isset($row[0]->id) and $row[0]->id > 0) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * Gibt die Einkaufsliste aus.
     * 
     * @param int    $matids    MatIDs
     * @param int    $matAnzahl Anzahl(en)
     * @param string $matNames  Namen
     * 
     * @return void
     */
    public function einkaufsliste($matids, $matAnzahl, $matNames)
    {

        if ($this->userHasRight("3", 0) == true) {

            echo "<table class='flatnetTable'>";
            echo "<thead><td>Benötigt</td><td></td><td></td><td>Vorhanden</td><td><strong>Du benötigst</strong></td><td></td></thead>";
            for ($i = 1; $i < sizeof($matids); $i++) {

                if (isset($matids[$i]) and isset($matAnzahl[$i]) and isset($matNames[$i])) {
                    //checken ob es die ID zu dem MatNamen gibt:
                    $matID = $this->getMatID($matNames[$i]);
                    if ($matID == 0) {
                        echo "<tbody>";
                        echo "<td>$matNames[$i] </td>
                        <td><strong>(Achtung - Name fehlt in Tabelle)</strong> </td>
                        <td>$matAnzahl[$i]</td><td>nicht möglich</td>
                        <td>$matAnzahl[$i]</td>
                        <td><input type='checkbox' name='HabIch' value='habich'></td>";
                        echo "</tbody>";
                    } else {
                        echo "<tbody>";
                        //Vorhandene Selektieren:
                        $vorhanden = $this->getMatAnzahl($matNames[$i]);
                        $rest = $matAnzahl[$i] - $vorhanden;
                        if ($rest < 0) {
                            $rest = 0;
                        }
                        if ($rest != 0) {
                            echo "<td><a href='https://www.google.com/webhp?tab=ww&ei=bi3TVJSGIpDSaK--gsAL&ved=0CAkQ1S4#q=Guild+Wars+2+$matNames[$i]' target='no_blank'>$matNames[$i]</a></td>
                            <td></td><td>$matAnzahl[$i]</td>
                            <td>$vorhanden</td>
                            <td id='hervorgehoben'>$rest</td>
                            <td><input type='checkbox' name='HabIch' value='habich'></td>";
                            echo "</tbody>";
                        }
                    }
                }
            }
            echo "</table>";

            echo "<a href='#top' class='buttonlink'>OK hab alles</a>";
        }
    }

    /**
     * Gibt die MatID zurück.
     * 
     * @param unknown $name Name des Materials
     * 
     * @return int
     */
    public function getMatID($name)
    {
        $user = $this->getUserID($_SESSION['username']);
        $select = "SELECT * FROM gwmatlist WHERE matName = '$name' LIMIT 1";
        $row = $this->getObjektInfo($select);

        if (!isset($row[0]->matID)) {
            $matid = 0;
        } else {
            $matid = $row[0]->matID;
        }

        return $matid;
    }

    /**
     * Gibt die Anzahl der vorhandenen Mats zurück.
     * 
     * @param string $matName MatName
     * 
     * @return void
     */
    public function getMatAnzahl($matName)
    {
        //Berechnet die benötigten Mats.
        $user = $this->getUserID($_SESSION['username']);
        $matID = $this->getMatID($matName);
        $account = $_SESSION['gw_account'];
        $select = "SELECT * FROM gwusersmats WHERE besitzer = '$user' AND matID = '$matID' AND account = '$account' LIMIT 1";
        $row = $this->getObjektInfo($select);

        if (!isset($row[0]->matAnzahl)) {
            $matAnzahl = 0;
        } else {
            $matAnzahl = $row[0]->matAnzahl;
        }

        return $matAnzahl;
    }

    /**
     * Gibt aus, ob der Benutzer einen Char hat, der diesen Beruf erlernt hat.
     * Dabei wird der gerade ausgewählte Account berücksichtigt.
     * 
     * @param int    $user  UserID
     * @param string $beruf Handwerksberuf
     * 
     * @return boolean
     */
    public function userHasACharWithHandwerkOverMax($user, $beruf)
    {

        //gerade gewählter Account:
        $account = $_SESSION['gw_account'];

        //Select
        $select1 = "SELECT * FROM gw_chars WHERE handwerk1stufe >= 400 AND besitzer = $user AND handwerk1 = '$beruf' AND account = $account";
        $select2 = "SELECT * FROM gw_chars WHERE handwerk2stufe >= 400 AND besitzer = $user AND handwerk2 = '$beruf' AND account = $account";
        $info1 = $this->getObjektInfo($select1);
        $info2 = $this->getObjektInfo($select2);

        //Check ob der Nutzer einen Charakter hat.
        if (isset($info1[0]->id) or isset($info2[0]->id)) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * Gibt die Berufeinformationen zurück.
     * 
     * @return void
     */
    public function getBerufInfo()
    {
        if (isset($_GET['berufInfo'])) {
            if ($_GET['berufInfo'] == "Waidmann") {
                $this->berufInfoWaidmann();
            }

            if ($_GET['berufInfo'] == "Lederer") {
                $this->berufInfoLederer();
            }

            if ($_GET['berufInfo'] == "Schneider") {
                $this->berufInfoSchneider();
            }

            if ($_GET['berufInfo'] == "Konstrukteur") {
                $this->berufInfoKonstrukteur();
            }

            if ($_GET['berufInfo'] == "Koch") {
                $this->berufInfoKoch();
            }

            if ($_GET['berufInfo'] == "Waffenschmied") {
                $this->berufInfoWaffenschmied();
            }

            if ($_GET['berufInfo'] == "Rüstungsschmied") {
                $this->berufInfoRuestungsschmied();
            }

            if ($_GET['berufInfo'] == "Juwelier") {
                $this->berufInfoJuwelier();
            }
        }
    }

    /**
     * Zeigt die Handwerksberufe-Links an. 
     * Jenachdem ob der Beruf bereits erlernt wurde, 
     * erscheint der Link grün.
     * 
     * @return void
     */
    public function showBerufLinks()
    {
        echo "<p>Einen Beruf auswählen, um die benötigten Materialien anzuzeigen. Wird der Knopf in grün angezeigt, dann hast du bereits einen Charakter der über Stufe 400 ist.</p>";
        echo "<a href='?berufInfo=Lederer#einkaufslisten'";

        if ($this->userHasACharWithHandwerkOverMax($this->getUserID($_SESSION['username']), "Lederer") == true) {
            echo " class='greenLink'";
        } else {
            echo " class='buttonlink'";
        }
        echo ">Lederer</a>";
        echo "<a href='?berufInfo=Schneider#einkaufslisten'";

        if ($this->userHasACharWithHandwerkOverMax($this->getUserID($_SESSION['username']), "Schneider") == true) {
            echo " class='greenLink'";
        } else {
            echo " class='buttonlink'";
        }
        echo ">Schneider</a>";
        echo "<a href='?berufInfo=Konstrukteur#einkaufslisten'";

        if ($this->userHasACharWithHandwerkOverMax($this->getUserID($_SESSION['username']), "Konstrukteur") == true) {
            echo " class='greenLink'";
        } else {
            echo " class='buttonlink'";
        }
        echo ">Konstrukteur</a>";
        echo "<a href='?berufInfo=Koch#einkaufslisten'";

        if ($this->userHasACharWithHandwerkOverMax($this->getUserID($_SESSION['username']), "Koch") == true) {
            echo " class='greenLink'";
        } else {
            echo " class='buttonlink'";
        }
        echo ">Koch</a>";
        echo "<a href='?berufInfo=Waffenschmied#einkaufslisten'";

        if ($this->userHasACharWithHandwerkOverMax($this->getUserID($_SESSION['username']), "Waffenschmied") == true) {
            echo " class='greenLink'";
        } else {
            echo " class='buttonlink'";
        }
        echo ">Waffenschmied</a>";
        echo "<a href='?berufInfo=Rüstungsschmied#einkaufslisten'";

        if ($this->userHasACharWithHandwerkOverMax($this->getUserID($_SESSION['username']), "Rüstungsschmied") == true) {
            echo " class='greenLink'";
        } else {
            echo " class='buttonlink'";
        }
        echo ">Rüstungsschmied</a>";
        echo "<a href='?berufInfo=Juwelier#einkaufslisten'";

        if ($this->userHasACharWithHandwerkOverMax($this->getUserID($_SESSION['username']), "Juwelier") == true) {
            echo " class='greenLink'";
        } else {
            echo " class='buttonlink'";
        }
        echo ">Juwelier</a>";
        echo "<a href='?berufInfo=Waidmann#einkaufslisten'";

        if ($this->userHasACharWithHandwerkOverMax($this->getUserID($_SESSION['username']), "Waidmann") == true) {
            echo " class='greenLink'";
        } else {
            echo " class='buttonlink'";
        }
        echo ">Waidmann</a>";
    }

    /**
     * Zeigt, welche Charakter einen bestimmten Beruf ausüben
     * 
     * @param string $beruf Name des Berufs wie in der DB
     * 
     * @return void
     */
    public function charInBeruf($beruf)
    {

        if ($this->userHasRight(3, 0) == true) {
            //Ausgabe initialiseren:
            $ausgabe = "";

            //Ausgabe initialiseren:
            $meldung = "";

            $besitzer = $this->getUserID($_SESSION['username']);

            $select = "SELECT * FROM gw_chars 
                WHERE handwerk1 = '$beruf' 
                AND besitzer = '$besitzer' 
                OR handwerk2 = '$beruf' 
                AND besitzer = '$besitzer'";
            if ($this->getAmount($select) == 0) {
                $meldung .= "Keine Chars in diesem Beruf";
            } else {
                $meldung .= "";
            }
            $row = $this->getObjektInfo($select);

            for ($i = 0; $i < sizeof($row); $i++) {
                if ($row[$i]->handwerk1 == $beruf) {
                    if ($row[$i]->handwerk1stufe == 500 or $row[$i]->handwerk1stufe >= 400) {
                        $class = "bereichLinkMaster";
                        $mark = "&checkmark;";
                    } else { 
                        $class = "bereichLink2";
                        $mark = "";
                    }
                } else {
                    if ($row[$i]->handwerk2stufe == 500 or $row[$i]->handwerk2stufe >= 400) {
                        $class = "bereichLinkMaster";
                        $mark = "&checkmark;";
                    } else { 
                        $class = "bereichLink2";
                        $mark = "";
                    }
                }

                $ausgabe .= "<div class='$class'><a href='charakter.php?charID=" . $row[$i]->id . "' class=''>" . $row[$i]->name . " ";
                if ($row[$i]->handwerk1 == $beruf) {
                    $ausgabe .= "(" . $row[$i]->handwerk1stufe . ") $mark</a></div>";
                } else {
                    $ausgabe .= "(" . $row[$i]->handwerk2stufe . ") $mark</a></div>";
                }
            }

            //Ausgabe
            $gesamt = $meldung . $ausgabe;
            return $gesamt;
        }
    }

    /**
     * Beinhaltet die benötigten Mats für den Beruf.
     * 
     * @return void
     */
    public function berufInfoWaidmann()
    {
        //Benötigte Mats:
        $matids[0] = 0;
        $matAnzahl[0] = 0;
        $matNames[0] = "";
        $matids[1] = 1;
        $matAnzahl[1] = 279;
        $matNames[1] = "Grüner Holzblock";
        $matids[2] = 2;
        $matAnzahl[2] = 440;
        $matNames[2] = "Geschmeidiger Holzblock";
        $matids[3] = 3;
        $matAnzahl[3] = 330;
        $matNames[3] = "Abgelagerter Holzblock";
        $matids[4] = 3;
        $matAnzahl[4] = 330;
        $matNames[4] = "Harter Holzblock";
        $matids[5] = 3;
        $matAnzahl[5] = 372;
        $matNames[5] = "Alter Holzblock";
        $matids[6] = 3;
        $matAnzahl[6] = 100;
        $matNames[6] = "Kupfererz";
        $matids[7] = 3;
        $matAnzahl[7] = 306;
        $matNames[7] = "Eisenerz";
        $matids[8] = 3;
        $matAnzahl[8] = 120;
        $matNames[8] = "Platinerz";
        $matids[9] = 3;
        $matAnzahl[9] = 174;
        $matNames[9] = "Mithrilerz";
        $matids[10] = 3;
        $matAnzahl[10] = 32;
        $matNames[10] = "Rohlederstücke";
        $matids[11] = 3;
        $matAnzahl[11] = 48;
        $matNames[11] = "Dünnes Lederstück";
        $matids[12] = 3;
        $matAnzahl[12] = 48;
        $matNames[12] = "Raues Lederstück";
        $matids[13] = 3;
        $matAnzahl[13] = 48;
        $matNames[13] = "Robustes Lederstück";
        $matids[14] = 3;
        $matAnzahl[14] = 72;
        $matNames[14] = "Dickes Lederstück";
        $matids[15] = 3;
        $matAnzahl[15] = 10;
        $matNames[15] = "Zinnbrocken";
        $matids[16] = 3;
        $matAnzahl[16] = 50;
        $matNames[16] = "Holzkohlebrocken";
        $matids[17] = 3;
        $matAnzahl[17] = 60;
        $matNames[17] = "Primordiumbrocken";
        $matids[18] = 3;
        $matAnzahl[18] = 9;
        $matNames[18] = "Knochensplitter";
        $matids[19] = 3;
        $matAnzahl[19] = 56;
        $matNames[19] = "Winziger Giftbeutel";
        $matids[20] = 3;
        $matAnzahl[20] = 12;
        $matNames[20] = "Winzige Klauen";
        $matids[21] = 3;
        $matAnzahl[21] = 12;
        $matNames[21] = "Winzige Totem";
        $matids[22] = 3;
        $matAnzahl[22] = 12;
        $matNames[22] = "Winzige Schuppe";
        $matids[23] = 3;
        $matAnzahl[23] = 9;
        $matNames[23] = "Knochenscherbe";
        $matids[24] = 3;
        $matAnzahl[24] = 56;
        $matNames[24] = "Kleiner Giftbeutel";
        $matids[25] = 3;
        $matAnzahl[25] = 12;
        $matNames[25] = "Kleiner Fangzahn";
        $matids[26] = 3;
        $matAnzahl[26] = 12;
        $matNames[26] = "Kleines Totem";
        $matids[27] = 3;
        $matAnzahl[27] = 12;
        $matNames[27] = "Kleine Schuppen";
        $matids[28] = 3;
        $matAnzahl[28] = 9;
        $matNames[28] = "Knochen";
        $matids[29] = 3;
        $matAnzahl[29] = 56;
        $matNames[29] = "Giftbeutel";
        $matids[30] = 3;
        $matAnzahl[30] = 12;
        $matNames[30] = "Fangzahn";
        $matids[31] = 3;
        $matAnzahl[31] = 12;
        $matNames[31] = "Totem";
        $matids[32] = 3;
        $matAnzahl[32] = 12;
        $matNames[32] = "Schuppen";
        $matids[33] = 3;
        $matAnzahl[33] = 9;
        $matNames[33] = "Dicker Knochen";
        $matids[34] = 3;
        $matAnzahl[34] = 58;
        $matNames[34] = "Voller Giftbeutel";
        $matids[35] = 3;
        $matAnzahl[35] = 12;
        $matNames[35] = "Scharfe Klaue";
        $matids[36] = 3;
        $matAnzahl[36] = 12;
        $matNames[36] = "Graviertes Totem";
        $matids[37] = 3;
        $matAnzahl[37] = 12;
        $matNames[37] = "Glatte Schuppen";
        $matids[38] = 3;
        $matAnzahl[38] = 56;
        $matNames[38] = "Wirkungsvolle Giftbeutel";
        $matids[39] = 3;
        $matAnzahl[39] = 12;
        $matNames[39] = "Verziertes Totem";
        $matids[40] = 3;
        $matAnzahl[40] = 54;
        $matNames[40] = "Großer Knochen";
        $matids[41] = 3;
        $matAnzahl[41] = 42;
        $matNames[41] = "Große Klaue";

        echo "<h2>Waidmann</h2>";

        //Ausgabe der Liste
        $this->einkaufsliste($matids, $matAnzahl, $matNames);
    }
    /**
     * Beinhaltet die benötigten Mats für den Beruf.
     * 
     * @return void
     */
    public function berufInfoWaffenschmied()
    {
        //Benötigte Mats:
        $matids[0] = 0;
        $matAnzahl[0] = 0;
        $matNames[0] = "";
        $matids[1] = 1;
        $matAnzahl[1] = 280;
        $matNames[1] = "Kupfererz";
        $matids[2] = 2;
        $matAnzahl[2] = 216;
        $matNames[2] = "Grüner Holzblock";
        $matids[3] = 3;
        $matAnzahl[3] = 28;
        $matNames[3] = "Zinnbrocken";
        $matids[4] = 3;
        $matAnzahl[4] = 71;
        $matNames[4] = "Phiole mit Schwachem Blut";
        $matids[5] = 3;
        $matAnzahl[5] = 372;
        $matNames[5] = "Alter Holzblock";
        $matids[6] = 3;
        $matAnzahl[6] = 264;
        $matNames[6] = "Geschmeidiger Holzblock";
        $matids[7] = 3;
        $matAnzahl[7] = 576;
        $matNames[7] = "Eisenerz";
        $matids[8] = 3;
        $matAnzahl[8] = 192;
        $matNames[8] = "Platinerz";
        $matids[9] = 3;
        $matAnzahl[9] = 226;
        $matNames[9] = "Mithrilerz";
        $matids[10] = 3;
        $matAnzahl[10] = 264;
        $matNames[10] = "Abgelagerter Holzblock";
        $matids[11] = 3;
        $matAnzahl[11] = 198;
        $matNames[11] = "Harter Holzblock";
        $matids[16] = 3;
        $matAnzahl[16] = 100;
        $matNames[16] = "Holzkohlebrocken";
        $matids[17] = 3;
        $matAnzahl[17] = 96;
        $matNames[17] = "Primordiumbrocken";
        $matids[18] = 3;
        $matAnzahl[18] = 12;
        $matNames[18] = "Knochensplitter";
        $matids[19] = 3;
        $matAnzahl[19] = 12;
        $matNames[19] = "Winziger Giftbeutel";
        $matids[20] = 3;
        $matAnzahl[20] = 12;
        $matNames[20] = "Winzige Klauen";
        $matids[21] = 3;
        $matAnzahl[21] = 12;
        $matNames[21] = "Winzige Totem";
        $matids[22] = 3;
        $matAnzahl[22] = 12;
        $matNames[22] = "Winzige Schuppe";
        $matids[24] = 3;
        $matAnzahl[24] = 62;
        $matNames[24] = "Kleiner Giftbeutel";
        $matids[25] = 3;
        $matAnzahl[25] = 12;
        $matNames[25] = "Kleiner Fangzahn";
        $matids[26] = 3;
        $matAnzahl[26] = 6;
        $matNames[26] = "Kleines Totem";
        $matids[27] = 3;
        $matAnzahl[27] = 12;
        $matNames[27] = "Kleine Schuppen";
        $matids[29] = 3;
        $matAnzahl[29] = 56;
        $matNames[29] = "Giftbeutel";
        $matids[30] = 3;
        $matAnzahl[30] = 6;
        $matNames[30] = "Fangzahn";
        $matids[31] = 3;
        $matAnzahl[31] = 12;
        $matNames[31] = "Totem";
        $matids[32] = 3;
        $matAnzahl[32] = 12;
        $matNames[32] = "Schuppen";
        $matids[15] = 3;
        $matAnzahl[15] = 56;
        $matNames[15] = "Klaue";
        $matids[34] = 3;
        $matAnzahl[34] = 68;
        $matNames[34] = "Voller Giftbeutel";
        $matids[35] = 3;
        $matAnzahl[35] = 12;
        $matNames[35] = "Scharfe Klaue";
        $matids[36] = 3;
        $matAnzahl[36] = 6;
        $matNames[36] = "Graviertes Totem";
        $matids[37] = 3;
        $matAnzahl[37] = 6;
        $matNames[37] = "Glatte Schuppen";
        $matids[38] = 3;
        $matAnzahl[38] = 68;
        $matNames[38] = "Wirkungsvolle Giftbeutel";
        $matids[39] = 3;
        $matAnzahl[39] = 9;
        $matNames[39] = "Verziertes Totem";
        $matids[40] = 3;
        $matAnzahl[40] = 45;
        $matNames[40] = "Großer Fangzahn";
        $matids[41] = 3;
        $matAnzahl[41] = 12;
        $matNames[41] = "Große Klaue";
        $matids[42] = 3;
        $matAnzahl[42] = 36;
        $matNames[42] = "Große Schuppe";

        echo "<h2>Waffenschmied</h2>";
        echo "<p class='dezentInfo'>Nicht mit Rüstungsschmied kombinieren, da hier die gleichen Materialien verwendet werden
                und man sich so gegenseitig die Mats wegschnappt.</p>";
        //Ausgabe der Liste
        $this->einkaufsliste($matids, $matAnzahl, $matNames);
    }
    /**
     * Beinhaltet die benötigten Mats für den Beruf.
     * 
     * @return void
     */
    public function berufInfoRuestungsschmied()
    {
        //Benötigte Mats:
        $matids[0] = 0;
        $matAnzahl[0] = 0;
        $matNames[0] = "";
        $matids[1] = 1;
        $matAnzahl[1] = 40;
        $matNames[1] = "Juterest";
        $matids[2] = 2;
        $matAnzahl[2] = 200;
        $matNames[2] = "Kupfererz";
        $matids[3] = 3;
        $matAnzahl[3] = 20;
        $matNames[3] = "Zinnbrocken";
        $matids[4] = 3;
        $matAnzahl[4] = 18;
        $matNames[4] = "Knochensplitter";
        $matids[5] = 3;
        $matAnzahl[5] = 18;
        $matNames[5] = "Winziger Giftbeutel";
        $matids[6] = 3;
        $matAnzahl[6] = 18;
        $matNames[6] = "Phiole mit Schwachem Blut";
        $matids[7] = 3;
        $matAnzahl[7] = 60;
        $matNames[7] = "Spule Jutefaden";
        $matids[8] = 3;
        $matAnzahl[8] = 18;
        $matNames[8] = "Winzige Schuppe";
        $matids[9] = 3;
        $matAnzahl[9] = 18;
        $matNames[9] = "Winzige Klauen";
        $matids[10] = 3;
        $matAnzahl[10] = 18;
        $matNames[10] = "Winzige Totem";
        $matids[11] = 3;
        $matAnzahl[11] = 96;
        $matNames[11] = "Wollrest";
        $matids[16] = 3;
        $matAnzahl[16] = 216;
        $matNames[16] = "Eisenerz";
        $matids[17] = 3;
        $matAnzahl[17] = 21;
        $matNames[17] = "Spule Wollfaden";
        $matids[18] = 3;
        $matAnzahl[18] = 38;
        $matNames[18] = "Kleiner Giftbeutel";
        $matids[19] = 3;
        $matAnzahl[19] = 12;
        $matNames[19] = "Kleiner Fangzahn";
        $matids[20] = 3;
        $matAnzahl[20] = 12;
        $matNames[20] = "Kleine Schuppen";
        $matids[21] = 3;
        $matAnzahl[21] = 12;
        $matNames[21] = "Kleines Totem";
        $matids[22] = 3;
        $matAnzahl[22] = 24;
        $matNames[22] = "Knochenscherbe";
        $matids[24] = 3;
        $matAnzahl[24] = 96;
        $matNames[24] = "Baumwollrest";
        $matids[26] = 3;
        $matAnzahl[26] = 21;
        $matNames[26] = "Spule Baumwollfaden";
        $matids[27] = 3;
        $matAnzahl[27] = 38;
        $matNames[27] = "Giftbeutel";
        $matids[29] = 3;
        $matAnzahl[29] = 12;
        $matNames[29] = "Fangzahn";
        $matids[30] = 3;
        $matAnzahl[30] = 12;
        $matNames[30] = "Totem";
        $matids[31] = 3;
        $matAnzahl[31] = 12;
        $matNames[31] = "Schuppen";
        $matids[32] = 3;
        $matAnzahl[32] = 24;
        $matNames[32] = "Knochen";
        $matids[15] = 3;
        $matAnzahl[15] = 96;
        $matNames[15] = "Leinenrest";
        $matids[34] = 3;
        $matAnzahl[34] = 108;
        $matNames[34] = "Platinerz";
        $matids[35] = 3;
        $matAnzahl[35] = 21;
        $matNames[35] = "Spule Leinenfaden";
        $matids[36] = 3;
        $matAnzahl[36] = 6;
        $matNames[36] = "Scharfer Fangzahn";
        $matids[37] = 3;
        $matAnzahl[37] = 12;
        $matNames[37] = "Scharfe Klaue";
        $matids[38] = 3;
        $matAnzahl[38] = 12;
        $matNames[38] = "Graviertes Totem";
        $matids[39] = 3;
        $matAnzahl[39] = 44;
        $matNames[39] = "Voller Giftbeutel";
        $matids[40] = 3;
        $matAnzahl[40] = 24;
        $matNames[40] = "Dicker Knochen";
        $matids[41] = 3;
        $matAnzahl[41] = 189;
        $matNames[41] = "Seidenrest";
        $matids[42] = 3;
        $matAnzahl[42] = 128;
        $matNames[42] = "Mithrilerz";
        $matids[43] = 3;
        $matAnzahl[43] = 26;
        $matNames[43] = "Spule Seidenfaden";
        $matids[44] = 3;
        $matAnzahl[44] = 38;
        $matNames[44] = "Großer Fangzahn";
        $matids[45] = 3;
        $matAnzahl[45] = 12;
        $matNames[45] = "Große Klauen";
        $matids[46] = 3;
        $matAnzahl[46] = 72;
        $matNames[46] = "Verziertes Totem";
        $matids[47] = 3;
        $matAnzahl[47] = 57;
        $matNames[47] = "Wirkungsvoller Giftbeutel";
        $matids[48] = 3;
        $matAnzahl[48] = 24;
        $matNames[48] = "Großer Knochen";

        //Bis LVL 500
        $matidslvl500[1] = 0;
        $matAnzahllvl500[1] = 80;
        $matNameslvl500[1] = "Orichalcumerz";
        $matidslvl500[2] = 0;
        $matAnzahllvl500[2] = 304;
        $matNameslvl500[2] = "Gazerest";
        $matidslvl500[3] = 0;
        $matAnzahllvl500[3] = 504;
        $matNameslvl500[3] = "Spule Gazefaden";
        $matidslvl500[4] = 0;
        $matAnzahllvl500[4] = 120;
        $matNameslvl500[4] = "Ektoplasmakugel";
        $matidslvl500[5] = 0;
        $matAnzahllvl500[5] = 15;
        $matNameslvl500[5] = "Scheußliche Klaue";
        $matidslvl500[6] = 0;
        $matAnzahllvl500[6] = 15;
        $matNameslvl500[6] = "Kunstvolles Totem";
        $matidslvl500[7] = 0;
        $matAnzahllvl500[7] = 15;
        $matNameslvl500[7] = "Scheußlicher Fangzahn";
        $matidslvl500[8] = 0;
        $matAnzahllvl500[8] = 15;
        $matNameslvl500[8] = "Kraftvoller Giftbeutel";
        $matidslvl500[9] = 0;
        $matAnzahllvl500[9] = 15;
        $matNameslvl500[9] = "Anktiker Knochen";
        $matidslvl500[10] = 0;
        $matAnzahllvl500[10] = 15;
        $matNameslvl500[10] = "Phiole mit kraftvollem Blut";
        $matidslvl500[11] = 0;
        $matAnzahllvl500[11] = 15;
        $matNameslvl500[11] = "Gepanzerte Schuppe";
        $matidslvl500[12] = 0;
        $matAnzahllvl500[12] = 90;
        $matNameslvl500[12] = "Karka-Panzer";

        echo "<h2>Rüstungsschmied</h2>";
        echo "<p class='dezentInfo'>Nicht mit Waffenschmied kombinieren, da hier die gleichen Materialien verwendet werden
                und man sich so gegenseitig die Mats wegschnappt.</p>";

        //Ausgabe der Liste
        $this->einkaufsliste($matids, $matAnzahl, $matNames);
    }
    /**
     * Beinhaltet die benötigten Mats für den Beruf.
     * 
     * @return void
     */
    public function berufInfoJuwelier()
    {
        $matids[0] = 0;
        $matAnzahl[0] = 0;
        $matNames[0] = "";
        $matids[1] = 1;
        $matAnzahl[1] = 279;
        $matNames[1] = "Kupfererz";
        $matids[2] = 2;
        $matAnzahl[2] = 198;
        $matNames[2] = "Silbererz";
        $matids[3] = 3;
        $matAnzahl[3] = 198;
        $matNames[3] = "Golderz";
        $matids[4] = 3;
        $matAnzahl[4] = 198;
        $matNames[4] = "Platinerz";
        $matids[5] = 3;
        $matAnzahl[5] = 240;
        $matNames[5] = "Mithrilerz";
        $matids[6] = 3;
        $matAnzahl[6] = 3;
        $matNames[6] = "Bernsteinkiesel";
        $matids[7] = 3;
        $matAnzahl[7] = 3;
        $matNames[7] = "Granatkiesel";
        $matids[8] = 3;
        $matAnzahl[8] = 3;
        $matNames[8] = "Türkiskiesel";
        $matids[9] = 3;
        $matAnzahl[9] = 3;
        $matNames[9] = "Tigeraugenkiesel";
        $matids[10] = 3;
        $matAnzahl[10] = 13;
        $matNames[10] = "Malachitkiesel";
        $matids[11] = 3;
        $matAnzahl[11] = 3;
        $matNames[11] = "Perlen";
        $matids[16] = 3;
        $matAnzahl[16] = 6;
        $matNames[16] = "Sonnensteinnugget";
        $matids[17] = 3;
        $matAnzahl[17] = 3;
        $matNames[17] = "Lapisnugget";
        $matids[18] = 3;
        $matAnzahl[18] = 3;
        $matNames[18] = "Peridotnugget";
        $matids[19] = 3;
        $matAnzahl[19] = 3;
        $matNames[19] = "Spinellnugget";
        $matids[20] = 3;
        $matAnzahl[20] = 6;
        $matNames[20] = "Topasbrocken";
        $matids[21] = 3;
        $matAnzahl[21] = 3;
        $matNames[21] = "Lapisbrocken";
        $matids[22] = 3;
        $matAnzahl[22] = 3;
        $matNames[22] = "Peridotbrocken";
        $matids[24] = 3;
        $matAnzahl[24] = 3;
        $matNames[24] = "Spinellbrocken";
        $matids[26] = 3;
        $matAnzahl[26] = 6;
        $matNames[26] = "Smaragd-Scherbe";
        $matids[27] = 3;
        $matAnzahl[27] = 3;
        $matNames[27] = "Chrysokoll-Scherben";
        $matids[29] = 3;
        $matAnzahl[29] = 3;
        $matNames[29] = "Opal-Scherben";
        $matids[30] = 3;
        $matAnzahl[30] = 3;
        $matNames[30] = "Saphir-Scherben";
        $matids[31] = 3;
        $matAnzahl[31] = 36;
        $matNames[31] = "Smaragdkristalle";
        $matids[32] = 3;
        $matAnzahl[32] = 3;
        $matNames[32] = "Chrysokollkristall";
        $matids[15] = 3;
        $matAnzahl[15] = 3;
        $matNames[15] = "Opalkristalle";
        $matids[34] = 3;
        $matAnzahl[34] = 3;
        $matNames[34] = "Saphirkristalle";
        $matids[35] = 3;
        $matAnzahl[35] = 6;
        $matNames[35] = "Ektoplasmakugel";

        //Zusatzinfo:
        echo "<h2>Juwelier</h2>";
        echo "<p class='dezentInfo'>Kiesel, Klumpen, Brocken und Splitter, also alle Edelsteine können aus den jeweils niedrigeren hergestellt werden,
                falls welche fehlen sollten.</p>";
        echo "<p class='dezentInfo'>Abgesehen von den Erzen ein sehr billiger Handwerksberuf.</p>";
        //Ausgabe der Liste
        $this->einkaufsliste($matids, $matAnzahl, $matNames);
    }
    /**
     * Beinhaltet die benötigten Mats für den Beruf.
     * 
     * @return void
     */
    public function berufInfoKonstrukteur()
    {
        //Benötigte Mats:
        $matids[0] = 0;
        $matAnzahl[0] = 0;
        $matNames[0] = "";
        $matids[1] = 1;
        $matAnzahl[1] = 198;
        $matNames[1] = "Haufen glitzernden Staubs";
        $matids[2] = 2;
        $matAnzahl[2] = 200;
        $matNames[2] = "Haufen funkelnder Staub";
        $matids[3] = 3;
        $matAnzahl[3] = 199;
        $matNames[3] = "Haufen strahlenden Staub";
        $matids[4] = 3;
        $matAnzahl[4] = 200;
        $matNames[4] = "Haufen Leuchtenden Staub";
        $matids[5] = 3;
        $matAnzahl[5] = 212;
        $matNames[5] = "Haufen weißglühenden Staub";
        $matids[6] = 3;
        $matAnzahl[6] = 24;
        $matNames[6] = "Kupfererz";
        $matids[7] = 3;
        $matAnzahl[7] = 50;
        $matNames[7] = "Eisenerz";
        $matids[8] = 3;
        $matAnzahl[8] = 24;
        $matNames[8] = "Silbererz";
        $matids[9] = 3;
        $matAnzahl[9] = 52;
        $matNames[9] = "Golderz";
        $matids[10] = 3;
        $matAnzahl[10] = 70;
        $matNames[10] = "Platinerz";
        $matids[11] = 3;
        $matAnzahl[11] = 96;
        $matNames[11] = "Mithrilerz";
        $matids[16] = 3;
        $matAnzahl[16] = 26;
        $matNames[16] = "Dünnes Lederstück";
        $matids[17] = 3;
        $matAnzahl[17] = 23;
        $matNames[17] = "Raues Lederstück";
        $matids[18] = 3;
        $matAnzahl[18] = 24;
        $matNames[18] = "Robustes Lederstück";
        $matids[19] = 3;
        $matAnzahl[19] = 24;
        $matNames[19] = "Dickes Lederstück";
        $matids[20] = 3;
        $matAnzahl[20] = 97;
        $matNames[20] = "Karotte";
        $matids[21] = 3;
        $matAnzahl[21] = 25;
        $matNames[21] = "Salatkopf";
        $matids[22] = 3;
        $matAnzahl[22] = 25;
        $matNames[22] = "Spinatblatt";
        $matids[24] = 3;
        $matAnzahl[24] = 25;
        $matNames[24] = "Winziger Giftbeutel";
        $matids[26] = 3;
        $matAnzahl[26] = 60;
        $matNames[26] = "Großer Knochen";
        $matids[27] = 3;
        $matAnzahl[27] = 30;
        $matNames[27] = "Zinnbrocken";
        $matids[29] = 3;
        $matAnzahl[29] = 102;
        $matNames[29] = "Alter Holzblock";
        $matids[30] = 3;
        $matAnzahl[30] = 270;
        $matNames[30] = "Krug Wasser";

        //Bis LVL 500
        $matidslvl500[1] = 0;
        $matAnzahllvl500[1] = 840;
        $matNameslvl500[1] = "Orichalcumerz";
        $matidslvl500[2] = 0;
        $matAnzahllvl500[2] = 1218;
        $matNameslvl500[2] = "Antiker Holzblock";
        $matidslvl500[3] = 0;
        $matAnzahllvl500[3] = 140;
        $matNameslvl500[3] = "Ektoplasmakugel";
        $matidslvl500[4] = 0;
        $matAnzahllvl500[4] = 20;
        $matNameslvl500[4] = "Scheußliche Klaue";
        $matidslvl500[5] = 0;
        $matAnzahllvl500[5] = 20;
        $matNameslvl500[5] = "Kunstvolles Totem";
        $matidslvl500[6] = 0;
        $matAnzahllvl500[6] = 20;
        $matNameslvl500[6] = "Scheußlicher Fangzahn";
        $matidslvl500[7] = 0;
        $matAnzahllvl500[7] = 20;
        $matNameslvl500[7] = "Kraftvoller Giftbeutel";
        $matidslvl500[8] = 0;
        $matAnzahllvl500[8] = 20;
        $matNameslvl500[8] = "Anktiker Knochen";
        $matidslvl500[9] = 0;
        $matAnzahllvl500[9] = 20;
        $matNameslvl500[9] = "Gepanzerte Schuppe";
        $matidslvl500[10] = 0;
        $matAnzahllvl500[10] = 20;
        $matNameslvl500[10] = "Phiole mit kraftvollem Blut";

        echo "<h2>Konstrukteur</h2>";

        //Ausgabe der Liste
        $this->einkaufsliste($matids, $matAnzahl, $matNames);
    }

    /**
     * Beinhaltet die benötigten Mats für den Beruf.
     * 
     * @return void
     */
    public function berufInfoLederer()
    {
        //Benötigte Mats:
        $matids[0] = 0;
        $matAnzahl[0] = 0;
        $matNames[0] = "";
        $matids[1] = 1;
        $matAnzahl[1] = 260;
        $matNames[1] = "Rohlederstücke";
        $matids[2] = 2;
        $matAnzahl[2] = 29;
        $matNames[2] = "Knochensplitter";
        $matids[3] = 3;
        $matAnzahl[3] = 18;
        $matNames[3] = "Winziger Giftbeutel";
        $matids[4] = 3;
        $matAnzahl[4] = 18;
        $matNames[4] = "Phiole mit Schwachem Blut";
        $matids[5] = 3;
        $matAnzahl[5] = 66;
        $matNames[5] = "Spule Jutefaden";
        $matids[6] = 3;
        $matAnzahl[6] = 18;
        $matNames[6] = "Winzige Schuppe";
        $matids[7] = 3;
        $matAnzahl[7] = 18;
        $matNames[7] = "Winzige Klauen";
        $matids[8] = 3;
        $matAnzahl[8] = 18;
        $matNames[8] = "Winzige Totem";
        $matids[9] = 3;
        $matAnzahl[9] = 3;
        $matNames[9] = "Haufen glitzernden Staubs";
        $matids[10] = 3;
        $matAnzahl[10] = 86;
        $matNames[10] = "Wollrest";
        $matids[11] = 3;
        $matAnzahl[11] = 92;
        $matNames[11] = "Dünnes Lederstück";
        $matids[16] = 3;
        $matAnzahl[16] = 56;
        $matNames[16] = "Spule Wollfaden";
        $matids[17] = 3;
        $matAnzahl[17] = 6;
        $matNames[17] = "Knochenscherbe";
        $matids[18] = 3;
        $matAnzahl[18] = 12;
        $matNames[18] = "Kleiner Fangzahn";
        $matids[19] = 3;
        $matAnzahl[19] = 36;
        $matNames[19] = "Kleine Schuppen";
        $matids[20] = 3;
        $matAnzahl[20] = 12;
        $matNames[20] = "Phiole mit Dünnem Blut";
        $matids[21] = 3;
        $matAnzahl[21] = 32;
        $matNames[21] = "Kleine Klaue";
        $matids[22] = 3;
        $matAnzahl[22] = 56;
        $matNames[22] = "Spule Baumwollfaden";
        $matids[24] = 3;
        $matAnzahl[24] = 88;
        $matNames[24] = "Baumwollrest";
        $matids[26] = 3;
        $matAnzahl[26] = 88;
        $matNames[26] = "Raues Lederstück";
        $matids[27] = 3;
        $matAnzahl[27] = 6;
        $matNames[27] = "Klaue";
        $matids[29] = 3;
        $matAnzahl[29] = 12;
        $matNames[29] = "Totem";
        $matids[30] = 3;
        $matAnzahl[30] = 12;
        $matNames[30] = "Schuppen";
        $matids[31] = 3;
        $matAnzahl[31] = 24;
        $matNames[31] = "Giftbeutel";
        $matids[32] = 3;
        $matAnzahl[32] = 44;
        $matNames[32] = "Phiole mit Blut";
        $matids[15] = 3;
        $matAnzahl[15] = 56;
        $matNames[15] = "Spule Leinenfaden";
        $matids[34] = 3;
        $matAnzahl[34] = 88;
        $matNames[34] = "Leinenrest";
        $matids[35] = 3;
        $matAnzahl[35] = 88;
        $matNames[35] = "Robustes Lederstück";
        $matids[36] = 3;
        $matAnzahl[36] = 30;
        $matNames[36] = "Glatte Schuppen";
        $matids[37] = 3;
        $matAnzahl[37] = 12;
        $matNames[37] = "Graviertes Totem";
        $matids[38] = 3;
        $matAnzahl[38] = 12;
        $matNames[38] = "Phiole mit Dickem Blut";
        $matids[39] = 3;
        $matAnzahl[39] = 44;
        $matNames[39] = "Voller Giftbeutel";
        $matids[40] = 3;
        $matAnzahl[40] = 111;
        $matNames[40] = "Spule Seidenfaden";
        $matids[41] = 3;
        $matAnzahl[41] = 171;
        $matNames[41] = "Seidenrest";
        $matids[42] = 3;
        $matAnzahl[42] = 150;
        $matNames[42] = "Dickes Lederstück";
        $matids[43] = 3;
        $matAnzahl[43] = 6;
        $matNames[43] = "Großer Fangzahn";
        $matids[44] = 3;
        $matAnzahl[44] = 12;
        $matNames[44] = "Wirkungsvoller Giftbeutel";
        $matids[45] = 3;
        $matAnzahl[45] = 12;
        $matNames[45] = "Verziertes Totem";
        $matids[46] = 3;
        $matAnzahl[46] = 42;
        $matNames[46] = "Phiole mit Wirkungsvollem Blut";
        $matids[47] = 3;
        $matAnzahl[47] = 32;
        $matNames[47] = "Große Klaue";
        $matids[48] = 3;
        $matAnzahl[48] = 54;
        $matNames[48] = "Große Schuppe";
        $matids[49] = 0;
        $matAnzahl[49] = 180;
        $matNames[49] = "Juterest";

        //Bis LVL 500
        $matidslvl500[1] = 0;
        $matAnzahllvl500[1] = 80;
        $matNameslvl500[1] = "Gehärtetes Lederstück";
        $matidslvl500[2] = 0;
        $matAnzahllvl500[2] = 304;
        $matNameslvl500[2] = "Gazerest";
        $matidslvl500[3] = 0;
        $matAnzahllvl500[3] = 504;
        $matNameslvl500[3] = "Spule Gazefaden";
        $matidslvl500[4] = 0;
        $matAnzahllvl500[4] = 120;
        $matNameslvl500[4] = "Ektoplasmakugel";
        $matidslvl500[5] = 0;
        $matAnzahllvl500[5] = 15;
        $matNameslvl500[5] = "Scheußliche Klaue";
        $matidslvl500[6] = 0;
        $matAnzahllvl500[6] = 15;
        $matNameslvl500[6] = "Kunstvolles Totem";
        $matidslvl500[7] = 0;
        $matAnzahllvl500[7] = 15;
        $matNameslvl500[7] = "Scheußlicher Fangzahn";
        $matidslvl500[8] = 0;
        $matAnzahllvl500[8] = 15;
        $matNameslvl500[8] = "Kraftvoller Giftbeutel";
        $matidslvl500[9] = 0;
        $matAnzahllvl500[9] = 15;
        $matNameslvl500[9] = "Anktiker Knochen";
        $matidslvl500[10] = 0;
        $matAnzahllvl500[10] = 15;
        $matNameslvl500[10] = "Phiole mit kraftvollem Blut";
        $matidslvl500[11] = 0;
        $matAnzahllvl500[11] = 15;
        $matNameslvl500[11] = "Gepanzerte Schuppe";
        $matidslvl500[12] = 0;
        $matAnzahllvl500[12] = 90;
        $matNameslvl500[12] = "Karka-Panzer";

        echo "<h2>Lederer</h2>";

        //Ausgabe der Liste
        $this->einkaufsliste($matids, $matAnzahl, $matNames);
    }

    /**
     * Beinhaltet die benötigten Mats für den Beruf.
     * 
     * @return void
     */
    public function berufInfoSchneider()
    {
        //Benötigte Mats:
        $matids[0] = 0;
        $matAnzahl[0] = 0;
        $matNames[0] = "";
        $matids[1] = 1;
        $matAnzahl[1] = 66;
        $matNames[1] = "Spule Jutefaden";
        $matids[2] = 2;
        $matAnzahl[2] = 270;
        $matNames[2] = "Juterest";
        $matids[3] = 3;
        $matAnzahl[3] = 60;
        $matNames[3] = "Rohlederstücke";
        $matids[4] = 3;
        $matAnzahl[4] = 21;
        $matNames[4] = "Knochensplitter";
        $matids[5] = 3;
        $matAnzahl[5] = 26;
        $matNames[5] = "Phiole mit Schwachem Blut";
        $matids[6] = 3;
        $matAnzahl[6] = 18;
        $matNames[6] = "Winziger Giftbeutel";
        $matids[7] = 3;
        $matAnzahl[7] = 18;
        $matNames[7] = "Winzige Schuppe";
        $matids[8] = 3;
        $matAnzahl[8] = 18;
        $matNames[8] = "Winzige Klauen";
        $matids[9] = 3;
        $matAnzahl[9] = 18;
        $matNames[9] = "Winzige Totem";
        $matids[10] = 3;
        $matAnzahl[10] = 3;
        $matNames[10] = "Haufen glitzernden Staubs";
        $matids[11] = 3;
        $matAnzahl[11] = 59;
        $matNames[11] = "Spule Wollfaden";
        $matids[16] = 3;
        $matAnzahl[16] = 140;
        $matNames[16] = "Wollrest";
        $matids[17] = 3;
        $matAnzahl[17] = 70;
        $matNames[17] = "Dünnes Lederstück";
        $matids[18] = 3;
        $matAnzahl[18] = 38;
        $matNames[18] = "Kleiner Giftbeutel";
        $matids[20] = 3;
        $matAnzahl[20] = 12;
        $matNames[20] = "Phiole mit Dünnem Blut";
        $matids[19] = 3;
        $matAnzahl[19] = 12;
        $matNames[19] = "Kleiner Fangzahn";
        $matids[21] = 3;
        $matAnzahl[21] = 12;
        $matNames[21] = "Kleines Totem";
        $matids[22] = 3;
        $matAnzahl[22] = 24;
        $matNames[22] = "Knochenscherbe";
        $matids[24] = 3;
        $matAnzahl[24] = 56;
        $matNames[24] = "Spule Baumwollfaden";
        $matids[26] = 3;
        $matAnzahl[26] = 60;
        $matNames[26] = "Raues Lederstück";
        $matids[26] = 3;
        $matAnzahl[26] = 140;
        $matNames[26] = "Baumwollrest";
        $matids[27] = 3;
        $matAnzahl[27] = 6;
        $matNames[27] = "Giftbeutel";
        $matids[29] = 3;
        $matAnzahl[29] = 12;
        $matNames[29] = "Totem";
        $matids[30] = 3;
        $matAnzahl[30] = 12;
        $matNames[30] = "Fangzahn";
        $matids[31] = 3;
        $matAnzahl[31] = 14;
        $matNames[31] = "Phiole mit Blut";
        $matids[32] = 3;
        $matAnzahl[32] = 32;
        $matNames[32] = "Knochen";
        $matids[15] = 3;
        $matAnzahl[15] = 24;
        $matNames[15] = "Klaue";
        $matids[34] = 3;
        $matAnzahl[34] = 56;
        $matNames[34] = "Spule Leinenfaden";
        $matids[35] = 3;
        $matAnzahl[35] = 70;
        $matNames[35] = "Robustes Lederstück";
        $matids[36] = 3;
        $matAnzahl[36] = 140;
        $matNames[36] = "Leinenrest";
        $matids[37] = 3;
        $matAnzahl[37] = 6;
        $matNames[37] = "Scharfer Fangzahn";
        $matids[38] = 3;
        $matAnzahl[38] = 12;
        $matNames[38] = "Voller Giftbeutel";
        $matids[39] = 3;
        $matAnzahl[39] = 12;
        $matNames[39] = "Scharfe Klaue";
        $matids[40] = 3;
        $matAnzahl[40] = 12;
        $matNames[40] = "Phiole mit Dickem Blut";
        $matids[41] = 3;
        $matAnzahl[41] = 32;
        $matNames[41] = "Glatte Schuppen";
        $matids[42] = 3;
        $matAnzahl[42] = 24;
        $matNames[42] = "Dicker Knochen";
        $matids[43] = 3;
        $matAnzahl[43] = 60;
        $matNames[43] = "Spule Seidenfaden";
        $matids[44] = 3;
        $matAnzahl[44] = 105;
        $matNames[44] = "Dickes Lederstück";
        $matids[45] = 3;
        $matAnzahl[45] = 210;
        $matNames[45] = "Seidenrest";
        $matids[46] = 3;
        $matAnzahl[46] = 6;
        $matNames[46] = "Großer Fangzahn";
        $matids[47] = 3;
        $matAnzahl[47] = 12;
        $matNames[47] = "Wirkungsvoller Giftbeutel";
        $matids[48] = 3;
        $matAnzahl[48] = 12;
        $matNames[48] = "Große Klaue";
        $matids[49] = 0;
        $matAnzahl[49] = 12;
        $matNames[49] = "Phiole mit Wirkungsvollem Blut";
        $matids[50] = 0;
        $matAnzahl[50] = 12;
        $matNames[50] = "Großer Fangzahn";
        $matids[51] = 0;
        $matAnzahl[51] = 12;
        $matNames[51] = "Großer Knochen";

        echo "<h2>Schneider</h2>";

        //Ausgabe der Liste
        $this->einkaufsliste($matids, $matAnzahl, $matNames);

    }

    /**
     * Beinhaltet die benötigten Mats für den Beruf.
     * 
     * @return void
     */
    public function berufInfoKoch()
    {
        //Benötigte Mats:
        $matids[0] = 0;
        $matAnzahl[0] = 0;
        $matNames[0] = "";
        $matids[1] = 1;
        $matAnzahl[1] = 75;
        $matNames[1] = "Krug Wasser";
        $matids[2] = 2;
        $matAnzahl[2] = 15;
        $matNames[2] = "Krug Pflanzenöl";
        $matids[3] = 3;
        $matAnzahl[3] = 180;
        $matNames[3] = "Beutel Mehl";
        $matids[4] = 3;
        $matAnzahl[4] = 68;
        $matNames[4] = "Paket Salz";
        $matids[5] = 3;
        $matAnzahl[5] = 15;
        $matNames[5] = "Flasche Essig";
        $matids[6] = 3;
        $matAnzahl[6] = 257;
        $matNames[6] = "Sack Zucker";
        $matids[7] = 3;
        $matAnzahl[7] = 25;
        $matNames[7] = "Paket Backpulver";
        $matids[8] = 3;
        $matAnzahl[8] = 52;
        $matNames[8] = "Beutel mit Stärke";
        $matids[9] = 3;
        $matAnzahl[9] = 255;
        $matNames[9] = "Stück Butter";
        $matids[10] = 3;
        $matAnzahl[10] = 43;
        $matNames[10] = "Schwarzes Pfefferkorn";
        $matids[11] = 3;
        $matAnzahl[11] = 75;
        $matNames[11] = "Ei";
        $matids[16] = 3;
        $matAnzahl[16] = 25;
        $matNames[16] = "Erdbeere";
        $matids[17] = 3;
        $matAnzahl[17] = 25;
        $matNames[17] = "Schoko-Riegel";
        $matids[18] = 3;
        $matAnzahl[18] = 80;
        $matNames[18] = "Brombeere";
        $matids[19] = 3;
        $matAnzahl[19] = 28;
        $matNames[19] = "Stück Wildfleisch";
        $matids[20] = 3;
        $matAnzahl[20] = 28;
        $matNames[20] = "Spargelstange";
        $matids[21] = 3;
        $matAnzahl[21] = 25;
        $matNames[21] = "Birne";
        $matids[22] = 3;
        $matAnzahl[22] = 55;
        $matNames[22] = "Zitrone";
        $matids[24] = 3;
        $matAnzahl[24] = 77;
        $matNames[24] = "Kirsche";
        $matids[26] = 3;
        $matAnzahl[26] = 75;
        $matNames[26] = "Pfirsich";

        echo "<h2>Koch</h2>";
        echo "<p class='dezentInfo'>Billigster Handwerksberuf überhaupt. Einziger Nachteil: Es werden Accountgebundene Handwerksmaterialien
                benötigt. Also muss man durch die Gegend laufen und diese gegen Karma eintauschen. </p>";

        //Ausgabe der Liste
        $this->einkaufsliste($matids, $matAnzahl, $matNames);
    }

    /**
     * Zeigt ein Handwerksranking.
     *  
     * @return void
     */
    public function rankingHandwerk()
    {
        echo "<div class='neuerBlog'>";
        echo "<h2>Handwerks-Ranking</h2>";

        $currentUserID = $this->getUserID($_SESSION['username']);
        $UserRanking = $this->getObjektInfo("SELECT *, sum(matAnzahl) as summe FROM gwusersmats WHERE besitzer = '$currentUserID'");
        $UserRanking2 = $this->getObjektInfo("SELECT *, sum(matAnzahl) as summe FROM gwusersmats GROUP BY besitzer ORDER BY summe DESC");

        echo "Du hast insgesamt ";
        if (!isset($UserRanking[0]->summe)) {
            $summeDerMats = 0;
        } else {
            $summeDerMats = $UserRanking[0]->summe;
        }
        echo "<strong>" . $summeDerMats . "</strong> eingelagerte Materialien in deiner Truhe!<br> ";

        echo "<ul class='classicUl'>";
        $counter = 1;
        for ($i = 0; $i < sizeof($UserRanking2); $i++) {
            $currentUser = $this->getUserName($UserRanking2[$i]->besitzer);
            $userID = $UserRanking2[$i]->besitzer;
            $letzterEintrag = $this->getObjektInfo(
                "SELECT *
                , day(timestamp) as tag
                , month(timestamp) as monat
                , year(timestamp) as jahr
                , minute(timestamp) as minute
                , hour(timestamp) as stunde
                FROM gwusersmats WHERE besitzer = '$userID' ORDER BY timestamp DESC"
            );
            echo "<li><strong>$counter</strong> - " . $UserRanking2[$i]->summe . " Materialien von <strong>" . $currentUser . "</strong> zuletzt aktualisiert am ";
            echo $letzterEintrag[$i]->tag . "." . $letzterEintrag[$i]->monat . "." . $letzterEintrag[$i]->jahr . " / " . $letzterEintrag[$i]->stunde . ":" . $letzterEintrag[$i]->minute;
            echo "</li>";
            $counter += 1;
        }
        echo "</ul>";
        echo "</div>";
    }
}

/**
 * Guildwars API
 * Funktionen für Guildwars API
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
class Api extends Guildwars
{
    /**
     * ApiTest
     * 
     * @return void
     */
    public function apiTest()
    {
        echo "<h2>Es gibt noch keine Api Verbindung ...</h2>";
    }
}
