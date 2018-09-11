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
 * HardwareDB
 * Stellt die Funktionen der Hardware Datenbank zur Verfuegung.
 *
 * PHP Version 7
 * 
 * @history 2018-09-11: Erstellt
 *
 * @category   Classes
 * @package    Flatnet2
 * @subpackage NONE
 * @author     Steven Schödel <steven.schoedel@outlook.com>
 * @license    https://www.gnu.org/licenses/gpl-3.0.txt GNU/GPLv3
 * @version    Release: <1>
 * @link       none
 */
class HardwareDB extends Functions
{
    /**
     * Hauptfunktion der Hardware Datenbank
     * 
     * @return void
     */
    public function mainHWFunction()
    {
        $this->hardwareHeader();
        
        if (isset($_GET['newManu'])) {
            $this->createNewManufacturer();
        }
        if (isset($_GET['alterManuID'])) {
            $this->alterManufacturer($_GET['alterManuID']);
        }
        if (isset($_GET['showAllManus'])) {
            $this->showAllManufacturers();
        }
        if (isset($_GET['alterHWID'])) {
            $this->alterHardware($_GET['alterHWID']);
        }
        if (isset($_GET['newHardware'])) {
            $this->createNewHardware();
        }

        $this->showAllHardware();
        
    }

    /**
     * Hardware Header Darstellung
     * 
     * @return void
     */
    public function hardwareHeader() 
    {
        echo "<div class='finanzNAV'>";
            echo "<li><a href='/flatnet2/toyota/index.php?start=1'>Start</a></li>";
            echo "<li><a href='/flatnet2/toyota/index.php?showAllManus=1'>Hersteller</a></li>";
            echo "<li><a href='/flatnet2/toyota/index.php?showAllManus=1&newManu=1'>Neuer Hersteller</a></li>";
            echo "<li><a href='/flatnet2/toyota/index.php?showAllHardware=1&newHardware'>Neue Hardware</a></li>";
        echo "</div>";
    }

    /**
     * Stellt alle Manufacturers dar
     * Tablename : $manuDB = hardwaremanufacturers
     * 
     * @param string $manuDB Tabellenname für Manufacturers
     * 
     * @return void
     */
    public function showAllManufacturers($manuDB = "hardwaremanfacturers")
    {
        $getAllManus = "SELECT * FROM $manuDB";
        $allManus = $this->getObjektInfo($getAllManus);

        if (count($allManus) > 0) {
            //echo "<div class='newFahrt'";
            echo "<table class='kontoTable'>";
            echo "<thead>";
                echo "<td>Hersteller</td>";
                echo "<td>Beschreibung</td>";
                echo "<td></td>";
            echo "</thead>";
            for ($i = 0; $i < sizeof($allManus); $i++) {
                echo "<tbody>";
                    echo "<td>" . $allManus[$i]->manuName . "</td>";
                    echo "<td>" . $allManus[$i]->manuDescription . "</td>";
                    echo "<td>" . "<a href='?showAllManus=1&alterManuID=".$allManus[$i]->id."'>Edit</a>" . "</td>";
                echo "</tbody>";
            }
            echo "</table>";
            //echo "</div>";
        }
        
    }

    /**
     * Erstellt einen neuen Manufacturer
     * 
     * DB INFO:
     * id               PRIMARY             
     * timestamp        CURRENT_TIMESTAMP
     * manuName         VAR_CHAR(250)
     * manuDescription  TEXT
     * besitzer         INT                 FK für UserID auf benutzer table
     * 
     * @param string $manuDB Tabellenname für Manufacturers
     * 
     * @return void
     */
    public function createNewManufacturer($manuDB = "hardwaremanfacturers")
    {
        $dbinfo = array (
            array("id", "ID", "hidden", "number"),
            array("timestamp", "Timestamp", "hidden", "text"),
            array("manuName","Hersteller", "required", "text"),
            array("manuDescription","Beschreibung", "required", "text"),
            array("ersteller", "Besitzer", "hidden", "number"),
        );

        $this->showCreateNewForm(
            "newManu", 
            "hardwaremanfacturers", 
            $dbinfo, 
            "newChar", 
            "Neuen Hersteller anlegen", 
            "?showAllManus=1", 
            "ersteller"
        );
    }

    /**
     * Bearbeitet einen Manufacturer
     * 
     * @param int $id ID des Herstellers
     * 
     * @return void
     */
    public function alterManufacturer($id)
    {
        if (is_numeric($id)) {
            echo "Alter ManuID $id";
        }
    }

    /**
     * Erstellt einen neuen Hardware Eintrag
     * 
     * @param string $db Datenbank
     * 
     * @return void
     */
    public function createNewHardware($db = "hardwareentries") 
    {
        $dbinfo = array (
            array("id", "ID", "hidden", "number"),
            array("timestamp", "Timestamp", "hidden", "text"),
            array("besitzer","Besitzer", "hidden", "number", "any"),
            array("manufacturer","Hersteller", "required", array("options", "hardwaremanfacturers", "manuName")),
            array("hwName", "Name", "required", "text"),
            //                                              STEP
            array("hwValue", "Wert", "required", "number", "0.01"),
            array("hwDescription", "Beschreibung", " ", "text"),
            array("hwBuydate", "Kaufdatum", "required", "date"),
            array("hwGarantieLengthMonth", "Garantie (Monate)", " ", "number" , "any"),
        );

        $this->showCreateNewForm(
            "newHard", 
            "hardwareentries", 
            $dbinfo, 
            "newChar", 
            "Neue Hardware anlegen", 
            "?start=1", 
            "besitzer"
        );
    }

    /**
     * Stellt alle Hardwarekomponenten dar
     * Tablename : $manuDB = hardwareentries
     * 
     * @param string $manuDB Tabellenname für Manufacturers
     * 
     * @return void
     */
    public function showAllHardware($manuDB = "hardwareentries")
    {
        $besitzer = $this->getUserID($_SESSION['username']);
        $getAllManus = "SELECT * FROM $manuDB WHERE besitzer=$besitzer ORDER BY hwBuydate";
        $allManus = $this->getObjektInfo($getAllManus);

        if (count($allManus) > 0) {
            //echo "<div class='newFahrt'";
            echo "<table class='kontoTable'>";
            echo "<thead>";
                echo "<td>Name</td>";
                echo "<td>Wert</td>";
                echo "<td>Beschreibung</td>";
                echo "<td>Kaufdatum</td>";
                echo "<td>Garantie</td>";
                echo "<td>Hersteller</td>";
                echo "<td></td>";
            echo "</thead>";
            for ($i = 0; $i < sizeof($allManus); $i++) {
                echo "<tbody>";
                    echo "<td>" . $allManus[$i]->hwName . "</td>";
                    echo "<td>" . $allManus[$i]->hwValue . "</td>";
                    echo "<td>" . $allManus[$i]->hwDescription . "</td>";
                    echo "<td>" . $allManus[$i]->hwBuydate . "</td>";
                    echo "<td>" . $allManus[$i]->hwGarantieLengthMonth . "</td>";
                    echo "<td>" . $allManus[$i]->manufacturer . "</td>";
                    echo "<td>" . "<a href='?start=1&alterHWID=".$allManus[$i]->id."'>Edit</a>" . "</td>";
                echo "</tbody>";
            }
            echo "</table>";
            //echo "</div>";
        }
        
    }

    /**
     * Ermöglicht die Bearbeitung einer HandwareKomponente
     * 
     * @param string $id ID der Hardware
     * 
     * @return void
     */
    public function alterHardware($id)
    {

    }

}