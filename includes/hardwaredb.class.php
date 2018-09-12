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
        
        // Hersteller // newManu
        if (isset($_GET['newManu'])) {
            $this->createNewManufacturer();
        }
        if (isset($_GET['alterManuID'])) {
            $this->alterManufacturer($_GET['alterManuID']);
        }
        if (isset($_GET['showAllManus'])) {
            $this->showAllManufacturers();
        }

        // Alle Geräte
        if (isset($_GET['alterHWID'])) {
            $this->alterHardware($_GET['alterHWID']);
        }
        if (isset($_GET['newHardware'])) {
            $this->createNewHardware();
        }
        if (isset($_GET['start'])) {
            $this->showAllHardware();
        }

        // Alle Hardware Namen // newGeraet
        if (isset($_GET['alterGeraet'])) {
            $this->alterHardwareName($_GET['alterGeraetID']);
        }
        if (isset($_GET['newGeraet'])) {
            $this->createNewGeraet();
        }
        if (isset($_GET['showAllGeraet'])) {
            $this->showAllGeraet();
        }

        // Alle Lieferanten // newLieferant
        if (isset($_GET['alterLieferant'])) {
            $this->alterLieferant($_GET['alterLieferantID']);
        }
        if (isset($_GET['newLieferant'])) {
            $this->newLieferant();
        }
        if (isset($_GET['showAllLieferant'])) {
            $this->showAllLieferant();
        }

        // Alle Garantien // newGarantie
        if (isset($_GET['alterGarantie'])) {
            $this->alterLieferant($_GET['alterGarantieID']);
        }
        if (isset($_GET['newGarantie'])) {
            $this->newGarantie();
        }
        if (isset($_GET['showAllGarantie'])) {
            $this->showAllGarantie();
        }

        // Alle HardwareTypen // newHwType
        if (isset($_GET['alterHwType'])) {
            $this->alterLieferant($_GET['alterHwTypeID']);
        }
        if (isset($_GET['newHwType'])) {
            $this->newHwType();
        }
        if (isset($_GET['showAllHwType'])) {
            $this->showAllHwType();
        }
    }

    /**
     * Hardware Header Darstellung
     * 
     * @return void
     */
    public function hardwareHeader() 
    {
        echo "<div class='finanzNAV'>";
            echo "<li><a href='/flatnet2/toyota/index.php?start=1'>Gesamte Hardware</a></li>";
            echo "<li><a href='/flatnet2/toyota/index.php?showAllManus=1'>Hersteller</a></li>";
            echo "<li><a href='/flatnet2/toyota/index.php?showAllGeraet=1'>Hardware Namen</a></li>";
            echo "<li><a href='/flatnet2/toyota/index.php?showAllLieferant=1'>Lieferanten</a></li>";
            echo "<li><a href='/flatnet2/toyota/index.php?showAllGarantie=1'>Garantien</a></li>";
            echo "<li><a href='/flatnet2/toyota/index.php?showAllHwType=1'>Hardware Typen</a></li>";
        echo "</div>";
        echo "<div class='finanzNAV'>";
            echo "<li><a href='/flatnet2/toyota/index.php?showAllHardware=1&newHardware'>NEU</a></li>";
            echo "<li><a href='/flatnet2/toyota/index.php?showAllManus=1&newManu=1'>Neuer Hersteller</a></li>";
            echo "<li><a href='/flatnet2/toyota/index.php?showAllManus=1&newGeraet=1'>Neues Gerät erstellen</a></li>";
            echo "<li><a href='/flatnet2/toyota/index.php?showAllManus=1&newLieferant=1'>Neuer Lieferant</a></li>";
            echo "<li><a href='/flatnet2/toyota/index.php?showAllManus=1&newGarantie=1'>Neue Garantie</a></li>";
            echo "<li><a href='/flatnet2/toyota/index.php?showAllManus=1&newHwType=1'>Neuer Hardware Typ</a></li>";
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
    public function showAllManufacturers($manuDB = "hardwaremanufacturers")
    {
        $getAllManus = "SELECT * FROM $manuDB";
        $allManus = $this->getObjektInfo($getAllManus);

        if (count($allManus) > 0) {
            echo "<h2>Hersteller</h2>";
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
        }
    }

    /**
     * Stellt alle Hardware Definitionen dar
     * Tablename : $manuDB = hardwaredefinition
     * 
     * @param string $manuDB Tabellenname für hardwaredefinition
     * 
     * @return void
     */
    public function showAllGeraet($manuDB = "hardwaredefinition")
    {
        $getAllManus = "SELECT * FROM $manuDB";
        $allManus = $this->getObjektInfo($getAllManus);

        if (count($allManus) > 0) {
            echo "<h2>Hardware Definitionen</h2>";
            echo "<table class='kontoTable'>";
            echo "<thead>";
                echo "<td>ID</td>";
                echo "<td>Hardware Definition Name</td>";
                echo "<td></td>";
            echo "</thead>";
            for ($i = 0; $i < sizeof($allManus); $i++) {
                echo "<tbody>";
                    echo "<td>" . $allManus[$i]->id . "</td>";
                    echo "<td>" . $allManus[$i]->hwTypeName . "</td>";
                    echo "<td>" . "<a href='?showAllGerat=1&alterGeraet=".$allManus[$i]->id."'>Edit</a>" . "</td>";
                echo "</tbody>";
            }
            echo "</table>";
        }
    }

    /**
     * Stellt alle Lieferanten dar
     * Tablename : $manuDB = hardwaredeliverers
     * 
     * @param string $manuDB Tabellenname für hardwaredeliverers
     * 
     * @return void
     */
    public function showAllLieferant($manuDB = "hardwaredeliverers")
    {
        $getAllManus = "SELECT * FROM $manuDB";
        $allManus = $this->getObjektInfo($getAllManus);

        if (count($allManus) > 0) {
            echo "<h2>Lieferanten</h2>";
            echo "<table class='kontoTable'>";
            echo "<thead>";
                echo "<td>ID</td>";
                echo "<td>Lieferantenname</td>";
                echo "<td></td>";
            echo "</thead>";
            for ($i = 0; $i < sizeof($allManus); $i++) {
                echo "<tbody>";
                    echo "<td>" . $allManus[$i]->id . "</td>";
                    echo "<td>" . $allManus[$i]->hwDelName . "</td>";
                    echo "<td>" . "<a href='?showAllLieferant=1&alterLieferantID=".$allManus[$i]->id."'>Edit</a>" . "</td>";
                echo "</tbody>";
            }
            echo "</table>";
        }
    }

    /**
     * Stellt alle Garantien dar
     * Tablename : $manuDB = hardwaregarantietypes
     * 
     * @param string $manuDB Tabellenname für hardwaregarantietypes
     * 
     * @return void
     */
    public function showAllGarantie($manuDB = "hardwaregarantietypes")
    {
        $getAllManus = "SELECT * FROM $manuDB";
        $allManus = $this->getObjektInfo($getAllManus);

        if (count($allManus) > 0) {
            echo "<h2>Garantien</h2>";
            echo "<table class='kontoTable'>";
            echo "<thead>";
                echo "<td>ID</td>";
                echo "<td>Garantiename</td>";
                echo "<td></td>";
            echo "</thead>";
            for ($i = 0; $i < sizeof($allManus); $i++) {
                echo "<tbody>";
                    echo "<td>" . $allManus[$i]->id . "</td>";
                    echo "<td>" . $allManus[$i]->hwGarantieName . "</td>";
                    echo "<td>" . "<a href='?showAllGarantie=1&alterGarantieID=".$allManus[$i]->id."'>Edit</a>" . "</td>";
                echo "</tbody>";
            }
            echo "</table>";
        }
    }

    /**
     * Stellt alle Hardware Typen dar
     * Tablename : $manuDB = hardwaretypes
     * 
     * @param string $manuDB Tabellenname für hardwaretypes
     * 
     * @return void
     */
    public function showAllHwType($manuDB = "hardwaretypes")
    {
        $getAllManus = "SELECT * FROM $manuDB";
        $allManus = $this->getObjektInfo($getAllManus);

        if (count($allManus) > 0) {
            echo "<h2>Garantien</h2>";
            echo "<table class='kontoTable'>";
            echo "<thead>";
                echo "<td>ID</td>";
                echo "<td>HardwareTyp Name</td>";
                echo "<td></td>";
            echo "</thead>";
            for ($i = 0; $i < sizeof($allManus); $i++) {
                echo "<tbody>";
                    echo "<td>" . $allManus[$i]->id . "</td>";
                    echo "<td>" . $allManus[$i]->hwTypeName . "</td>";
                    echo "<td>" . "<a href='?showAllHwType=1&alterHwTypeID=".$allManus[$i]->id."'>Edit</a>" . "</td>";
                echo "</tbody>";
            }
            echo "</table>";
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
    public function createNewManufacturer($manuDB = "hardwaremanufacturers")
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
            "hardwaremanufacturers", 
            $dbinfo, 
            "newChar", 
            "kontoTable", 
            "Neuen Hersteller anlegen", 
            "?showAllManus=1", 
            "ersteller"
        );
    }

    /**
     * Erstellt ein neues Gerät
     * 
     * DB INFO:
     * id               PRIMARY             
     * timestamp        CURRENT_TIMESTAMP
     * hwTypeName       VAR_CHAR(250)
     * 
     * @param string $manuDB Tabellenname für Geraete
     * 
     * @return void
     */
    public function createNewGeraet($manuDB = "hardwaredefinition")
    {
        $dbinfo = array (
            array("id", "ID", "hidden", "number"),
            array("timestamp", "Timestamp", "hidden", "text"),
            array("hwTypeName","Gerät", "required", "text"),
        );

        $this->showCreateNewForm(
            "newGeraet", 
            $manuDB, 
            $dbinfo, 
            "newChar", 
            "kontoTable", 
            "Neues Gerät anlegen", 
            "?showAllGeraet=1", 
            " "
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
            array("hwManu","Hersteller", "required", array("options", "hardwaremanufacturers", "manuName")),
            array("hwName", "Name", "required", "text"),
            array("hwValue", "Wert", "required", "number", "0.01"),
            array("hwDescription", "Beschreibung", " ", "text"),
            array("hwBuydate", "Kaufdatum", "required", "date"),
            array("hwGarantieLengthMonth", "Garantie (Monate)", " ", "number" , "any"),
            array("hwType","Hardware", " ", array("options", "hardwaretypes", "hwTypeName")),
            array("hwSerial","Seriennummer", " ", "text"),
            array("hwDeliverer","Lieferant", " ", array("options", "hardwaredeliverers", "hwDelName")),
            array("hwDelNumber","Lieferscheinnummer", " ", "text"),
            array("hwDelDate","Lieferdatum", " ", "date"),
            array("hwReNumber","Rechnungsnummer", " ", "text"),
            array("hwReDate","Rechnungsdatum", " ", "date"),
            array("hwGarantieType","Garantietyp", " ", array("options", "hardwaregarantietypes", "hwGarantieName")),
            array("hwStandort","Arbeitsplatznummer", " ", "text"),
            array("hwSold","Verkauft / Entsorgt", " ", "date"),
            array("hwKostenstelle","Kostenstelle", " ", "text"),
            array("hwHardwareType","Hardware Typ", "required", array("options", "hardwaredefinition", "hwTypeName")),
        );

        $this->showCreateNewForm(
            "newHard", 
            "hardwareentries", 
            $dbinfo, 
            "newChar",
            "kontoTable", 
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
            echo "<h2>Hardware</h2>";
            echo "<table class='kontoTable'>";
            echo "<thead>";
                echo "<td><a href='?order=1'>Name</a></td>";
                echo "<td><a href='?order=2'>Wert</a></td>";
                echo "<td><a href='?order=3'>Beschreibung</a></td>";
                echo "<td><a href='?order=4'>Kaufdatum</a></td>";
                echo "<td><a href='?order=5'>Garantie</a></td>";
                echo "<td><a href='?order=6'>Hersteller</a></td>";
                echo "<td></td>";
            echo "</thead>";
            for ($i = 0; $i < sizeof($allManus); $i++) {
                echo "<tbody>";
                    echo "<td>" . $allManus[$i]->hwName . "</td>";
                    echo "<td>" . $allManus[$i]->hwValue . "</td>";
                    echo "<td>" . $allManus[$i]->hwDescription . "</td>";
                    echo "<td>" . $allManus[$i]->hwBuydate . "</td>";
                    echo "<td>" . $allManus[$i]->hwGarantieLengthMonth . "</td>";
                    $manu = $this->getObjektInfo("SELECT id,manuName FROM hardwaremanufacturers WHERE id=".$allManus[$i]->id);
                    echo "<td>" . $manu[$i]->manuName . "</td>";
                    echo "<td>" . "<a href='?start=1&alterHWID=".$allManus[$i]->id."'>Edit</a><a class='redLink' href='?start=1&deleteHWID=".$allManus[$i]->id."'>X</a>" . "</td>";
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
        echo "Alter HWID $id";
    }

}