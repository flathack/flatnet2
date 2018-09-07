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

/**
 * SQL
 * Klasse um eine Verbindung zue Datenbank herzustellen.
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
class Sql
{
    /**
     * Fuehrt die Verbindung zur Datenbank aus.
     * 
     * @return $db
     */
    function connectToDB() 
    {
        $this->connectToDBNewWay();
    }

    /**
     * Stellt die Verbindung zur Datenbank her.
     * 
     * @return $db
     */
    function connectToDBNewWay()
    {
        try {
            $dbname = $this->getDBName();
            $db = new PDO(
                "mysql:host=localhost;dbname=$dbname", "62_flathacksql1", "12141214", array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
            );
        } catch (Exception $e) {
            $css = '<link href="/flatnet2/css/error.css" type="text/css" rel="stylesheet" />';
            $errorText = "<p class='info'>Datenbank Error</p>";
            $bild = "<img src='/flatnet2/images/fehler/grund.PNG' name='' alt='DatenbankError'>";
            $errorBeschreibung = "<p>Quggan traurig, Quaggan kann die Datenbank nicht finden, nur Errors.</p>";
            die(
                "<body><div><div class='wrapper'>" 
                . $css 
                . $errorText 
                . $bild 
                . $errorBeschreibung 
                . "</div></div></body></html>"
            );
        }
        return $db;
    }
    
    /**
     * Setzt den DB Namen fest.
     * 
     * @return string
     */
    function getDBName() 
    {
        $name = "62_flathacksql1";
        return $name;
    }
    
    /**
     * Function um Inhalte aus einer Tabelle zu löschen.
     * 
     * @param String $tabelle Gibt die Tabelle an, wo geloescht werden soll
     * 
     * @return void
     */
    function sqlDelete($tabelle) 
    {
        if (isset($_GET['loeschen']) AND isset($_GET['loeschid']) ) {
                
            $id = $_GET['loeschid'];
                
            if ($id > 0 AND !isset($_POST['jaloeschen'])) {

                // Abfrage, ob der User den Artikel wirklich löschen will.
                echo "<form method=post>";
                echo "<p class='meldung'>Soll der Eintrag gelöscht werden?<br>";
                echo "<input type = hidden value = '$id' name ='id' readonly />";
                echo "<input type=submit name='jaloeschen' value='Ja'/>
                        <a href='?' class='buttonlink'>Nein</a></p>";
                echo "</form>";
            }
        }

        /*
         * Führt die Löschung durch.
        */
        if (isset($_POST['jaloeschen']) ) {
            $jaloeschen = isset($_POST['jaloeschen']) ? $_POST['jaloeschen'] : '';
            $loeschid = isset($_POST['id']) ? $_POST['id'] : '';
            if ($loeschid) {

                // Durchführung der Löschung.
                $loeschQuery = "DELETE FROM `$tabelle` WHERE `id` = $loeschid";
                    
                if ($this->sql_insert_update_delete($loeschQuery) == true) {
                    echo "<p class='erfolg'>Eintrag gelöscht.</p>";
                } else {
                    echo "<p class='meldung'>Fehler beim löschen!</p>";
                }

            }
        }

    }
    
    /**
     * Loescht einen Eintrag aus der Datenbank
     * 
     * @param String $query Query fuer die Loeschung
     * 
     * @return void
     */
    function sqlDeleteCustom($query) 
    {

        if (isset($_GET['loeschen']) AND isset($_GET['loeschid'])) {

            $id = $_GET['loeschid'];

            if ($id > 0 AND !isset($_POST['jaloeschen'])) {
                // Abfrage, ob der User den Artikel wirklich löschen will.
                echo "<form method=post>";
                echo "<p class='meldung'>Soll die Löschung durchgeführt werden?<br>";
                echo "<input type = hidden value = '$id' name ='id' readonly />";
                echo "<input type=submit name='jaloeschen' value='Ja'/>
                        <a href='?' class='buttonlink'>Nein</a></p>";
                echo "</form>";
            }
        }

        /*
         * Führt die Löschung durch.
        */
        if (isset($_POST['jaloeschen']) ) {
            $jaloeschen = isset($_POST['jaloeschen']) ? $_POST['jaloeschen'] : '';
            $loeschid = isset($_POST['id']) ? $_POST['id'] : '';
            if ($loeschid) {

                    
                if ($this->sql_insert_update_delete($query) == true) {
                    echo "<p class='erfolg'>Erfolgreich gelöscht</p>";
                } else {
                    echo "<p class='meldung'>Fehler beim löschen!</p>";
                }

            }
        }

    }

    /** 
     * Führt die Funktionen Insert, Update und Delete durch. 
     * Loggt nur, wenn Änderungen tatsächlich durchgeführt werden.
     * 
     * @param String $query Query fuer die Loeschung
     * 
     * @return boolean
     */
    function sql_insert_update_delete($query) 
    {
        
        $db = $this->connectToDBNewWay();
        
        $affected_rows = $db->exec($query);
        
        if ($affected_rows > 0) {
            // Log durchführen:
            $this->logme($query);
            return true;
        } else {
            return false;
        }
        
    }
    
    /**
     * Extra sql Methode für gw.class.php in Zeile 1446
     * Hier wird nicht geloggt.
     * 
     * @param String $query Query fuer die Loeschung
     * 
     * @return boolean
     */
    function sql_insert_update_delete_hw($query) 
    {
        $db = $this->connectToDBNewWay();
        $affected_rows = $db->exec($query);

        if ($affected_rows > 0) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * NO INFO
     * 
     * @param String $relation_name Tabellenname
     * @param String $structure 
     * 
     * @return boolean
     */
    function sql_db_check($relation_name, $structure) 
    {
        
        echo "<li>$relation_name</li>";
        try {
            $columns = $this->getColumns($relation_name);
            if (!$columns) {
                throw new Exception('Error');
            }
            
            $start = 0;
            foreach ($structure as $c) {
                
                if ($c == $columns[$start]) {
                    // echo "<li>$start : $c ist ok</li>";
                } else {
                    echo "<li class='hinweis'>$start : $c ist FEHLERHAFT - erforderlicher Name : " . $columns[$start] . "</li>";
                }
                
                $start++;
            }
            
        } catch (Exception $e) {
            echo "<p class='meldung'>Caught Exception $e</p>";
        }
    }
    
    /**
     * Loggt transaktionen auf der Datenbank
     * 
     * @param String $received_query Query fuer die Abfrage
     * 
     * @return boolean
     */
    function logme($received_query) 
    {
        if (isset($_SESSION['username'])) {
            $username = $_SESSION['username'];
        } else {
            $username = "Unknown-User";
        }
        
        // UserID bekommen
        $getuserid = $this->getObjektInfo("SELECT id, Name FROM benutzer WHERE Name='$username' LIMIT 1");
        if (!isset($getuserid[0]->id)) {
            $id=0;
        } else {
            $id=$getuserid[0]->id;
        }
        
        // IP herausfinden:
        if (!isset($_SERVER['HTTP_X_FORWARDED_FOR']) ) { 
            $ip = $_SERVER['REMOTE_ADDR']; 
        } else {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR']; 
        }
        
        // Querytext bauen:
        $text_for_log = $username ." : ". strip_tags(stripslashes($received_query));
        $query = "INSERT INTO log (benutzer, log_text, ip_adress) VALUES (\"$id\",\"$text_for_log\",\"$ip\")";
        // Logeintrag hinzufügen
        $db = $this->connectToDBNewWay();
        $affected_rows = $db->exec($query);
        
        if ($affected_rows > 0) {
            return true;
        } else {
            echo "<p class='dezentInfo'>Es konnte nicht geloggt werden: $query</p>";
        }
        
    }

    /**
     * Gibt die Menge der Zeilen einer Anfrage wieder.
     * 
     * @param unknown $query Query fuer die Abfrage
     * 
     * @return int
     */
    function getAmount($query) 
    {
        $db = $this->connectToDBNewWay();
        $stmt = $db->query($query);
        $count = $stmt->rowCount();
        return $count;
    }

    /**
     * Speichert die Informationen !eines! Objekts in der Variable $row
     * 
     * @param String $query Query fuer die Abfrage
     * 
     * @return object
     */
    function getObjektInfo($query) 
    {
        // echo "<p class='dezentInfo'>" .$query . "</p>";
        
        $db = $this->connectToDBNewWay();
        $stmt = $db->query($query);
        $results = $stmt->fetchAll(PDO::FETCH_OBJ);
                        
        return $results;
    }
    
    /**
     * Laed Informationen in ein Objekt.
     * 
     * @param String $query Query fuer die Abfrage
     * 
     * @return object
     */
    function getObjectsToArray($query) 
    {
         $this->getObjektInfo($query);
    }
    
    /**
     * Ermöglicht das Abfragen, ob ein Objekt in einer Datenbank bereits existiert.
     * und gibt True oder False zurück.
     * 
     * @param String $query Query fuer die Abfrage
     * 
     * @return boolean
     */
    function objectExists($query) 
    {
        $row = $this->getObjektInfo($query);

        if (isset($row[0])) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Gibt die Spalten einer Tabelle zurück.
     * 
     * @param String $table Tablename
     * 
     * @return unknown
     */
    function getColumns($table) 
    {
        // COLUMNS SPEICHERN;
        $select1 = "SHOW COLUMNS FROM $table";
        $row = $this->getObjektInfo($select1);
        //for ($i = 0 ; $i < sizeof($row); $i++) {
        //    $columns[$i] = $row[$i]->Field;
        //}
        
        return $row;
    }
    
    /**
     * Gibt die Spalten einer Query zurück.
     *
     * @param String $query Query fuer die Abfrage
     * 
     * @return $columns
     */
    function getColumnsFromQuery($query) 
    {
        $createTempTable = "CREATE TEMPORARY TABLE IF NOT EXISTS tempTable AS ($query);";
        $this->sql_insert_update_delete($createTempTable);
        
        $select1 = "SHOW COLUMNS FROM tempTable";
        $row = $this->getObjektInfo($select1);
        for ($i = 0 ; $i < sizeof($row); $i++) {
            $columns[$i]=$row[$i]->Field;
        }
        
        return $columns;
    }
    
    /**
     * Gibt die Anzahl der Spalten an.
     *
     * @param String $query Query fuer die Abfrage
     * 
     * @return int
     */
    function getColumnAnzahl($query) 
    {
        $createTempTable = "
        CREATE TEMPORARY TABLE
        IF NOT EXISTS tempTable AS ($query);";
        $this->sql_insert_update_delete($createTempTable);
        
        $select1 = "SHOW COLUMNS FROM tempTable";
        
        $row = $this->getAmount($select1);
        return $row;
    }
}
?>