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
 * Uebersicht
 * Stellt die Übersicht zur Verfügung
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
class Uebersicht extends Functions
{

    /**
     * Hauptfunktion
     * 
     * @return void
     */
    public function mainUebersichtFunction()
    {
        //Übersicht Manipulation
        $this->changeStatusFunction();

        //Uebersicht anzeigen:
        $this->showUebersicht();

        //Administrative-Funktionen

        if ($this->userHasRight("73", 0) == true and isset($_GET['newEntry'])) {
            $this->showCreateEntity();
        }

    }

    /**
     * Erstellt einen Eintrag in der Tabelle.
     * 
     * @param unknown $id           ID
     * @param unknown $timestamp    Timestamp
     * @param unknown $name         Name
     * @param unknown $link         Link
     * @param unknown $beschreibung Beschreibung
     * @param unknown $sortierung   Sortierung
     * @param unknown $active       Aktiv
     * @param unknown $css          Css
     * @param unknown $rightID      rightID
     * 
     * @return void
     */
    public function createEntity($id, $timestamp, $name, $link, $beschreibung, $sortierung, $active, $css, $rightID)
    {

        $query = "INSERT INTO uebersicht_kacheln (id, timestamp, name, link, beschreibung, sortierung, active, cssID, rightID)
                VALUES
                ('$id', CURRENT_TIMESTAMP,'$name','$link','$beschreibung','$sortierung','$active','$css','$rightID')";
        if ($this->sqlInsertUpdateDelete($query) == true) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * Ermöglicht die Detailansicht der Übersichtkacheln.
     * 
     * @return void
     */
    public function showEditEntry()
    {

        if ($this->userHasRight(73, 0) == true) {

            $select = "SELECT * FROM uebersicht_kacheln";
            $kacheln = $this->sqlselect($select);

            echo "<table class='kontoTable'>";

            echo "<thead><td>ID</td>
                    <td>Name</td>
                    <td>Link</td>
                    <td>Beschreibung</td>
                    <td>Sortierung</td>
                    <td>Aktiv</td>
                    <td>Css ID</td>
                    <td>RightID</td>
                    <td></td>
                    </thead>";
            for ($i = 0; $i < sizeof($kacheln); $i++) {

                echo "<tbody>";
                echo "<td>" . $kacheln[$i]->id . "</td>";
                echo "<td>" . $kacheln[$i]->name . "</td>";
                echo "<td>" . $kacheln[$i]->link . "</td>";
                echo "<td>" . $kacheln[$i]->beschreibung . "</td>";
                echo "<td>" . $kacheln[$i]->sortierung . "</td>";
                echo "<td>" . $kacheln[$i]->active . "</td>";
                echo "<td>" . $kacheln[$i]->cssID . "</td>";
                echo "<td>" . $kacheln[$i]->rightID . "</td>";
                echo "<td>" . "<a href='admin/control.php?action=3&table=uebersicht_kacheln'>edit</a>" . "</td>";
                echo "</tbody>";

            }
            echo "</table>";
        }
    }

    /**
     * Zeigt die Kachel zum erstelen einer Kachel
     * 
     * @return void
     */
    public function showCreateEntity()
    {
        if ($this->userHasRight(73, 0) == true) {

            if (isset($_POST['name']) and isset($_POST['link'])) {
                if ($_POST['name'] != "" and $_POST['link'] != "" and $_POST['rightID'] != "") {
                    if (!isset($_POST['active'])) {
                        $active = 0;
                    } else {
                        $active = 1;
                    }

                    if ($this->createEntity("", "CURRENT_TIMESTAMP", $_POST['name'], $_POST['link'], $_POST['beschreibung'], $_POST['sortierung'], $active, $_POST['css'], $_POST['rightID']) == true) {
                        echo "<p class='erfolg'>Die Übersicht wurde hinzugefügt</p>";
                    } else {
                        echo "<p class='meldung'>Es gab einen Fehler</p>";
                    }

                }

            }

            echo "<div class='bereichNEW'><form method=post>";
            if (isset($_POST['name'])) {
                $name = $_POST['name'];
            } else {
                $name = "";
            }
            if (isset($_POST['link'])) {
                $link = $_POST['link'];
            } else {
                $link = "";
            }
            if (isset($_POST['beschreibung'])) {
                $beschreibung = $_POST['beschreibung'];
            } else {
                $beschreibung = "";
            }
            if (isset($_POST['sortierung'])) {
                $sortierung = $_POST['sortierung'];
            } else {
                $sortierung = "";
            }
            if (isset($_POST['css'])) {
                $css = $_POST['css'];
            } else {
                $css = "";
            }
            if (isset($_POST['rightID'])) {
                $rightID = $_POST['rightID'];
            } else {
                $rightID = "";
            }
            if (isset($_POST['active'])) {
                $active = $_POST['active'];
            } else {
                $active = "";
            }
            echo "<input id='name' type=text name=name value='" . $name . "' placeholder=Name />";
            echo "<input id='link' type=text name=link value='" . $link . "' placeholder=Link />";
            echo "<input id='desc' type=text name=beschreibung value='" . $beschreibung . "' placeholder=Beschreibung />";
            echo "<input id='sort' type=number name=sortierung value='" . $sortierung . "' placeholder=sort />";
            echo "<input id='css' type=text name=css value='" . $css . "' placeholder=css />";

            echo "<select name=rightID>";
            $getAllRights = $this->sqlselect("SELECT * FROM userrights ORDER BY kategorie, id");
            for ($i = 0; $i < sizeof($getAllRights); $i++) {
                echo "<option value='";
                echo $getAllRights[$i]->id;
                echo "'>";
                echo $getAllRights[$i]->kategorie . " - " . $getAllRights[$i]->id . " - " . $getAllRights[$i]->recht;
                echo "</option>";
            }
            echo "</select>";

            //   echo "<input id='right' type=number name=rightID value='" .$rightID. "' placeholder=Recht />";
            echo "<input id='checkbox' type=checkbox checked value='" . $active . "' name=active /><label for=active>Aktiv</label>";
            echo "<input id='submit' type=submit value=speichern />";
            echo "</form></div>";
        }
    }

    /**
     * Führt die Änderung aus.
     * 
     * @return void
     */
    public function changeStatusFunction()
    {
        if ($this->userHasRight(77, 0) == true) {
            if (isset($_POST['changeStatus'])) {

                $id = $_POST['id'];
                $status = $_POST['changeStatus'];

                if ($this->sqlInsertUpdateDelete("UPDATE uebersicht_kacheln SET active=$status WHERE id=$id LIMIT 1") == true) {
                    echo "<p class='erfolg'>Wurde geändert</p>";
                } else {
                    echo "<p class='meldung'>Es gab einen Fehler.</p>";
                }
            }
        }

    }

    /**
     * Ändert den Status einer Kachel.
     * 
     * @param int $id StatusID
     * 
     * @return void
     */
    public function changeStatus($id)
    {

        if ($this->userHasRight(77, 0) == true) {
            $getKachelInfo = $this->sqlselect("SELECT * FROM uebersicht_kacheln WHERE id = $id");

            echo "<div class='changeStatus'><form method=post>";
            echo "<input type=hidden name=id value=$id />";

            echo "<select name=changeStatus>";

            echo "<option";
            if ($getKachelInfo[0]->active == 1) {
                echo " selected ";
            }
            echo " value=1>Aktiv</option>";

            echo "<option";
            if ($getKachelInfo[0]->active == 0) {
                echo " selected ";
            }
            echo " value=0>Inaktiv</option>";

            echo "<option";
            if ($getKachelInfo[0]->active == 2) {
                echo " selected ";
            }
            echo " value=2>gesperrt</option>";

            echo "</select>";

            echo "<input type=submit name=submit value=ok />";
            echo "</form></div>";
        }
    }

    /**
     * Zeigt die Kacheluebersicht an.
     * 
     * @return void
     */
    public function showUebersicht()
    {
        $kacheln = $this->sqlselect("SELECT * FROM uebersicht_kacheln WHERE active=1 ORDER BY sortierung, name, id");
        $kachelnInactive = $this->sqlselect("SELECT * FROM uebersicht_kacheln WHERE active=0 ORDER BY sortierung, name, id");
        $kachelnGesperrt = $this->sqlselect("SELECT * FROM uebersicht_kacheln WHERE active=2 ORDER BY sortierung, name, id");

        //Normale Kacheln
        //Counter zum erkennen von Nutzern ohne Berechtigungen f�r eine Seite
        $counter = 0;
        for ($i = 0; $i < sizeof($kacheln); $i++) {

            if ($this->userHasRight($kacheln[$i]->rightID, 0) == true) {
                $counter++;
                echo "<div class=bereich" . $kacheln[$i]->cssID . ">";
                echo "<a href='" . $kacheln[$i]->link . "'><h2>" . $kacheln[$i]->name . "</h2></a>";

                echo "<p>";
                echo $kacheln[$i]->beschreibung;
                echo "</p>";
                $this->changeStatus($kacheln[$i]->id);
                echo "</div>";

            }
        }

        if ($counter == 0) {
            echo "<div class=bereichfinanzen>";
            echo "<h2> Oh ... ein neuer ! </h2>";
            echo "<p>";
            echo "Du hast noch keine Zug&auml;nge freigeschaltet, wende dich bitte an einen Administrator der Seite um f&uuml;r einen Bereich freigeschaltet zu werden.";
            echo "<a href='/flatnet2/informationen/kontakt.php'>Klicke hier</a>";
            echo "</p>";

            echo "</div>";
        }

        //Bereiche, welche für den Benutzer nicht sichtbar sind.
        for ($i = 0; $i < sizeof($kacheln); $i++) {
            if ($this->userHasRight($kacheln[$i]->rightID, 0) == false) {
                //   echo "<div class='bereichINACTIVE'>";
                echo "<h2> - </h2>";
                echo "<p></p>";
                $this->changeStatus($kacheln[$i]->id);
                //   echo "</div>";
            }
        }

        //Gesperrte Bereiche
        for ($i = 0; $i < sizeof($kachelnGesperrt); $i++) {
            if ($this->userHasRight($kachelnGesperrt[$i]->rightID, 0) == true or $this->userHasRight("45", 0) == true) {
                echo "<div class='bereichGesperrt'>";
                echo "<h2>" . $kachelnGesperrt[$i]->name . "</h2>";
                echo "<p>Der Inhalt wurde kurzfristig aufgrund eines Fehlers gesperrt. Versuche es später erneut.</p>";
                $this->changeStatus($kachelnGesperrt[$i]->id);
                echo "</div>";
            }
        }

        //Inaktive Bereiche
        for ($i = 0; $i < sizeof($kachelnInactive); $i++) {
            if ($this->userHasRight("52", 0) == true) {
                echo "<div class='bereichINACTIVE'>";
                echo "<h2>" . $kachelnInactive[$i]->name . "</h2>";
                echo "<p>Dieser Inhalt ist deaktiviert, da er nicht fertig entwickelt ist oder die Entwicklung eingestellt wurde.</p>";
                $this->changeStatus($kachelnInactive[$i]->id);
                echo "</div>";
            }
        }

        if ($this->userHasRight("45", 0) == true) {
            echo "<div class='adminKachel'>";

            echo "<h2>Administrationsübersicht</h2>";
            if ($this->userHasRight("54", 0) == true) {
                echo "<a class='buttonlink' href='?newEntry'>Neue Kachel</a>";
                echo "<a class='buttonlink' href='?editEntry'>Kacheln bearbeiten</a>";
            }
            $select = "SELECT * FROM benutzer WHERE versuche >= 3";
            $gesperrteUser = $this->sqlselect($select);
            echo "<div id='gesperrt'>";
            for ($i = 0; $i < sizeof($gesperrteUser); $i++) {
                echo "<li>" . $gesperrteUser[$i]->Name . " ist gesperrt <a class='redLink' href='/flatnet2/admin/control.php?statusID=" . $gesperrteUser[$i]->id . "&status=entsperren&userverw=1&action=1'>entsperren</a></li>";
            }
            echo "</div>";
            if ($this->userHasRight("54", 0) == true and isset($_GET['editEntry'])) {
                $this->showEditEntry();
            }

            echo "</div>";
        }

    }

}
