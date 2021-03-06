<?php
/**
 * DOC COMMENT
 * 
 * PHP Version 7
 * 
 * @history 03.02.2015 angelegt.
 * @history 10.02.2015 Überarbeitung auf Objektorientierung.
 *
 * @category   Document
 * @package    Flatnet2
 * @subpackage NONE
 * @author     Steven Schödel <steven.schoedel@outlook.com>
 * @license    https://www.gnu.org/licenses/gpl-3.0.txt GNU/GPLv3
 * @link       none
 */
require 'objekt\functions.class.php';

/**
 * Docu
 * Ermöglicht das speichern von Einträgen der Doku innerhalb der Datenbank.
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
class Docu extends Functions
{

    /**
     * Zeigt die Dokumentation, samt Eingabefeld an.
     * 
     * @return void
     */
    public function showDocu()
    {

        //Eingabefeld anzeigen.
        $this->showEingabeFeld();

        //Eintrag in DB speichern.
        if (isset($_POST['docuText'])) {
            $this->setDocuEintrag($_POST['docuText']);
        }

        if (isset($_GET['loeschid'])) {
            $this->deleteDocuEintrag();
        }

        //Ausgabe der Doku aus der Datenbank
        $query = "SELECT *, month(timestamp) AS monat, day(timestamp) AS tag, year(timestamp) AS jahr FROM docu ORDER BY timestamp DESC";
        $ergebnis = mysql_query($query);
        echo "<table class='flatnetTable'>";
        echo "<thead><td id='datum'>Datum</td><td id='text'>Text</td><td></td></thead>";
        while ($row = mysql_fetch_object($ergebnis)) {
            echo "<tbody><td>" . $row->tag . "." . $row->monat . "." . $row->jahr . "</td><td>" . $row->text . "</td><td><a href='?loeschen&loeschid=$row->id' class='highlightedLink'>X</a></td></tbody>";
        }

        echo "</table>";
    }

    /**
     * Zeigt das Eingabefeld für neuen Dokueintrag
     * 
     * @return void
     */
    public function showEingabeFeld()
    {
        //Neuer Eintrag in die Doku:
        if ($this->userHasRight("19", 0) == true) {
            echo "<form action='hilfe.php' method=post>";
            echo "<input type='text' name='docuText' id='titel' value='' placeholder='Beschreibung' />";
            echo "<input type='submit' name='sendDocu' value='Absenden' />";
            echo "</form>";
        }
    }

    /**
     * Speichert eintrag in die Dokumentation.
     * 
     * @param string $text Text
     * 
     * @return void
     */
    public function setDocuEintrag($text)
    {
        //Speichert den< Eintrag in die Datenbank.
        if ($_POST['docuText'] == "") {
            echo "<p class='meldung'>Feld ist leer.</p>";
        } else {
            $autor = $this->getUserID($_SESSION['username']);
            $insert = "INSERT INTO docu (text, autor) VALUES ('$text','$autor')";
            $ergebnis2 = mysql_query($insert);
            if ($ergebnis2 == true) {
                echo "<p class='erfolg'>Eintrag gespeichert.</p>";
            } else {
                echo "<p class='meldung'>Fehler beim speichern in der Datenbank.</p>";
            }
        }
    }

    /**
     * Löscht einen Docueintrag.
     * 
     * @return void
     */
    public function deleteDocuEintrag()
    {
        if ($this->userHasRight(66, 0) == true) {
            $this->sqlDelete("docu");
        }
    }

}
