<?php
/**
 * DOC COMMENT
 * 
 * PHP Version 7
 * 
 * @history Steven 20.08.2014 angelegt.
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
 * Datenbanken
 * Stellt die Funktionen des Adressbuchs zur Verfügung.
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
class Datenbanken extends Functions
{
    /**
     * Ermoeglicht das exportieren von Dateien, 
     * wenn ueber GET die Zahl 1 angegeben wird und der User Steven ist.
     * Status : getestet, funktioniert, Ausgabe muss noch formatiert werden.
     * VERALTET
     * 
     * @param string $tabelle Tabellenname
     * 
     * @return void
     */
    public function export($tabelle)
    {
        if (isset($_GET['export'])) {
            if ($_GET['export'] == 1 and $_SESSION['username'] == "steven") {
                $select = "SELECT * FROM $tabelle";
                $export = mysql_query($select);
                $fields = mysql_num_fields($export);

                for ($i = 0; $i < $fields; $i++) {
                    $header .= mysql_field_name($export, $i) . ";"; // #\t
                }

                while ($row = mysql_fetch_row($export)) {
                    $line = '';
                    foreach ($row as $value) {
                        if ((!isset($value)) or ($value == "")) {
                            $value = ";"; // #\t
                        } else {
                            $value = str_replace('"', '""', $value);
                            $value = '"' . $value . '"' . ";"; // #\t
                        }
                        $line .= $value;
                    }
                    $data .= trim($line) . "#";
                }
                $data = str_replace("\r", "", $data);

                if ($data == "") {
                    $data = "\n(0) Records Found!\n";
                }

                header("Content-type: application/octet-stream");
                header("Content-Disposition: attachment; filename=ausgabe.txt");
                header("Pragma: no-cache");
                header("Expires: 0");
                print "$header\n$data";
            }
        }
    }

    /**
     * Verbindet Function und Eingabe von: UserBearbeiten
     * 
     * @return void
     */
    public function UserBearbeiten()
    {
        if ($this->userHasRight(14, 0) == true) {
            echo $this->UserBearbeitenEingabe();
            echo $this->UserBearbeitenFunction();
        }
    }

    /**
     * Ermφglicht das bearbeiten von Daten im Adressbuch.
     * Benφtigt folgende GET variablen zum funktionieren:
     * bearbeiten != "", idanzeigen=(userid, z. b. 1)
     * 
     * @return void
     */
    public function UserBearbeitenEingabe()
    {
        if ($this->userHasRight(14, 0) == false) {
            exit();
        }
        // Var zuweisen:
        $bearbeiten = isset($_GET['bearbeiten']) ? $_GET['bearbeiten'] : '';

        // Erst los legen, wenn in Adresszeile Bearbeiten steht.
        if (isset($_GET["bearbeiten"])) {
            $userid = $_GET['bearbeiten'];
            echo "<div class='newFahrt'>";
            echo "<h2>Eintrag anzeigen und bearbeiten</h2>";

            // FORM
            echo "<form action='?' METHOD=GET>";
                $bearbeiten = $_GET["bearbeiten"];

                // Informationen heranholen
                $modsql = "SELECT * FROM adressbuch WHERE id=$bearbeiten";
                $rowbearb = $this->sqlselect($modsql);

                $_GET['userid'] = $userid;
                echo "<input type='hidden' name='id' value='" . $rowbearb[0]->id . "' readonly>";

                echo "<div class='rightBody'>";
                    // Fax
                    echo "<h4>Fax: </h4>";
                    echo "<input placeholder='Fax' type='text'   name='fax' value='" . $rowbearb[0]->fax . "'>";

                    // Gruppenzugehörigkeit
                    echo "<h4>Gruppenzugeh&ouml;rigkeit: </h4>";
                    echo "<input placeholder='Gruppe' type='text'   name='gruppe' value='" . $rowbearb[0]->gruppe . "' />";
                    // E-Mail
                    echo "<h4>E-Mail: </h4>";
                    echo "<input placeholder='E-Mail' type='text'   name='email' value='" . $rowbearb[0]->email . "'>";

                    // Geburtstag
                    echo "<h4>Geburtstag: </h4>";
                    echo "<input placeholder='Geburtstag' type='date'  maxlength='10' name='geburtstag' value='" . $rowbearb[0]->geburtstag . "' />";

                    // Social-Media
                    echo "<h4>Social Media: </h4>";
                    echo "<input placeholder='Skype' type='text'   name='skype' value='" . $rowbearb[0]->skype . "' />";
                    echo "<input placeholder='Facebook' type='text'   name='facebook' value='" . $rowbearb[0]->facebook . "' />";
                echo "</div>";

                echo "<div class='innerBody'>";
                    echo "<h3>" . $rowbearb[0]->vorname . " " . $rowbearb[0]->nachname . "</h3>";

                    echo "<input type='text'   name='vorname' value='" . $rowbearb[0]->vorname . "'  placeholder='Vorname' />";
                    echo "<input type='text'   name='nachname' value='" . $rowbearb[0]->nachname . "'  placeholder='Nachname' />";

                    echo "<h4>Adresse: </h4>";

                    echo "<input type='text'   name='strasse' value='" . $rowbearb[0]->strasse . "'  placeholder='Straίe' />";
                    echo "<input  type='text'   name='hausnummer' value='" . $rowbearb[0]->hausnummer . "' placeholder='Nr.' />";
                    echo "<br>";
                    echo "<input placeholder='PLZ' type='text'   name='postleitzahl' value='" . $rowbearb[0]->postleitzahl . "' />";
                    echo "<input placeholder='Stadt' type='text'   name='stadt' value='" . $rowbearb[0]->stadt . "' />";
                    echo "<br>";
                    echo "<input placeholder='Bundesland' type='text'   name='bundesland' value='" . $rowbearb[0]->bundesland . "' />";
                    echo "<input placeholder='Land' type='text'   name='land' value='" . $rowbearb[0]->land . "' />";

                    echo "<h4>Kontaktdaten: </h4>";

                    echo "<input type='text'  size='10'  name='telefon1art' value='" . $rowbearb[0]->telefon1art . "' placeholder='Handy'>";
                    echo "<input placeholder='Telefon 1' type='text'   name='telefon1' value='" . $rowbearb[0]->telefon1 . "' />";
                    echo "<br>";
                    echo "<input type='text'  size='10'  name='telefon2art' value='" . $rowbearb[0]->telefon2art . "' placeholder='Home' />";
                    echo "<input placeholder='Telefon 2' type='text'   name='telefon2' value='" . $rowbearb[0]->telefon2 . "' />";
                    echo "<br>";
                    echo "<input type='text'  size='10'  name='telefon3art' value='" . $rowbearb[0]->telefon3art . "' placeholder='Arbeit' />";
                    echo "<input placeholder='Telefon 3' type='text'   name='telefon3' value='" . $rowbearb[0]->telefon3 . "' />";
                    echo "<br>";
                    echo "<input  type='text' size='10'  name='telefon4art' value='" . $rowbearb[0]->telefon4art . "' placeholder='Privat' />";
                    echo "<input placeholder='Telefon 4' type='text'   name='telefon4' value='" . $rowbearb[0]->telefon4 . "' />";
                    echo "</div>";

                    echo "<h4>Notizen: </h4>";
                    echo "<textarea name='notizen' class='ckeditor' cols='52' rows='5' wrap='physical'>" . $rowbearb[0]->notizen . "</textarea>";
                    echo "<input type='submit' name='update' value='Speichern' class='buttonlink' />";
                    echo "<a href='?loeschen=ja&loeschid=" . $rowbearb[0]->id . "' class='buttonlink'>&#10008; l&ouml;schen</a>";
                echo "</div>";

            echo "</form>";
        }
    }

    /**
     * Ermoeglicht das Updaten der Informationen zu einem Eintrag im Adressbuch.
     * Benoetigt GET Var "update" == "Speichern".
     * 
     * @return void
     */
    public function UserBearbeitenFunction()
    {
        if ($this->userHasRight(14, 0) == true) {
            $fehler = "";
            $ausgabe = "";

            $update = isset($_GET['update']) ? $_GET['update'] : '';
            if ($update == 'Speichern') {
                $geburtstag = $_GET["geburtstag"];
                $vorname = $_GET["vorname"];
                $nachname = $_GET["nachname"];
                $strasse = $_GET["strasse"];
                $hausnummer = $_GET["hausnummer"];
                $postleitzahl = $_GET["postleitzahl"];
                $stadt = $_GET["stadt"];
                $bundesland = $_GET["bundesland"];
                $land = $_GET["land"];
                $telefon1 = $_GET["telefon1"];
                $telefon2 = $_GET["telefon2"];
                $telefon3 = $_GET["telefon3"];
                $telefon4 = $_GET["telefon4"];
                $telefon1art = $_GET["telefon1art"];
                $telefon2art = $_GET["telefon2art"];
                $telefon3art = $_GET["telefon3art"];
                $telefon4art = $_GET["telefon4art"];
                $email = $_GET["email"];
                $skype = $_GET["skype"];
                $facebook = $_GET["facebook"];
                $fax = $_GET["fax"];
                $gruppe = $_GET["gruppe"];
                $notizen = $_GET["notizen"];
                $updid = $_GET["id"];
                if ($nachname == "" or $vorname == "") {
                    $fehler .= "<p class='meldung'>Eingabefehler. Es muss mindestens ein Vorname und ein Nachname eingegeben werden. <a href='?'>Zurόck</a></p>";
                } else {
                    $sqlupdate = "UPDATE adressbuch SET geburtstag='$geburtstag', vorname='$vorname', nachname='$nachname', strasse='$strasse', hausnummer='$hausnummer', postleitzahl='$postleitzahl',
                    stadt='$stadt', bundesland='$bundesland', land='$land', telefon1='$telefon1', telefon2='$telefon2', telefon3='$telefon3', telefon4='$telefon4', telefon1art='$telefon1art', telefon2art='$telefon2art',
                    telefon3art='$telefon3art', telefon4art='$telefon4art', email='$email', skype='$skype', facebook='$facebook', fax='$fax', gruppe='$gruppe', notizen='$notizen' WHERE id='$updid'";

                    if ($this->sqlInsertUpdateDelete($sqlupdate) == true) {
                        $fehler .= "<p class='erfolg'>Adressbucheintrag wurde erfolgreich geδndert!</p>";
                    } else {
                        $fehler .= "<p class='meldung'>Fehler beim speichern</p>";
                    }
                }
            }

            return $fehler . $ausgabe;
        }
    }

    /**
     * Ermoeglicht das loeschen von Datensδtzen aus dem Adressbuch.
     * 
     * @return void
     */
    public function UserLoeschen()
    {
        try {
            $this->sqlDelete("adressbuch");
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Verbindet Function und Eingabe von: UserErstellen
     * 
     * @return void
     */
    public function UserErstellen()
    {
        if ($this->userHasRight(59, 0) == true) {
            echo $this->UserErstellenFunction();
            echo $this->UserErstellenEingabe();
        }
    }

    /**
     * Blendet die Eingabemaske zum erstellen eines Datensatzes in der Datenbank ein.
     * 
     * @return void
     */
    public function UserErstellenEingabe()
    {
        if ($this->userHasRight(59, 0) == true) {

            $ausgabe = "";
            $vorname = isset($_GET['vorname']) ? $_GET['vorname'] : '';
            $nachname = isset($_GET['nachname']) ? $_GET['nachname'] : '';
            $strasse = isset($_GET['strasse']) ? $_GET['strasse'] : '';
            $hausnummer = isset($_GET['hausnummer']) ? $_GET['hausnummer'] : '';
            $postleitzahl = isset($_GET['postleitzahl']) ? $_GET['postleitzahl'] : '';
            $stadt = isset($_GET['stadt']) ? $_GET['stadt'] : '';
            $bundesland = isset($_GET['bundesland']) ? $_GET['bundesland'] : '';
            $land = isset($_GET['land']) ? $_GET['land'] : '';
            $telefon1 = isset($_GET['telefon1']) ? $_GET['telefon1'] : '';
            $telefon2 = isset($_GET['telefon2']) ? $_GET['telefon2'] : '';
            $telefon3 = isset($_GET['telefon3']) ? $_GET['telefon3'] : '';
            $telefon4 = isset($_GET['telefon4']) ? $_GET['telefon4'] : '';
            $telefon1art = isset($_GET['telefon1art']) ? $_GET['telefon1art'] : '';
            $telefon2art = isset($_GET['telefon2art']) ? $_GET['telefon2art'] : '';
            $telefon3art = isset($_GET['telefon3art']) ? $_GET['telefon3art'] : '';
            $telefon4art = isset($_GET['telefon4art']) ? $_GET['telefon4art'] : '';
            $email = isset($_GET['email']) ? $_GET['email'] : '';
            $fax = isset($_GET['fax']) ? $_GET['fax'] : '';
            $gruppe = isset($_GET['gruppe']) ? $_GET['gruppe'] : '';
            $skype = isset($_GET['skype']) ? $_GET['skype'] : '';
            $facebook = isset($_GET['facebook']) ? $_GET['facebook'] : '';
            $geburtstag = isset($_GET['geburtstag']) ? $_GET['geburtstag'] : '';
            $notizen = isset($_GET['notizen']) ? $_GET['notizen'] : '';

            if (isset($_GET["eintragenja"])) {
                echo "";
                echo "<form method=post>";
                
                    echo "<div class='newFahrt'>";
                        echo "<a href='?' class='highlightedLink'>Exit</a></h2>";
                        echo "<h2>Eingabebereich</h2>";

                        echo "<table class='AdressTable'>";

                            // Vorname
                            echo "<tr>";
                                echo "<td><input type='text'   name='vorname' value='$vorname' placeholder='Vorname*' required autofocus></td>";
                                // Nachname
                                echo "<td><input type='text'  name='nachname' value='$nachname' placeholder='Nachname*' required></td>";
                            echo "</tr>";

                            // Straße
                            echo "<tr>";
                                echo "<td><input type='text'   name='strasse' value='$strasse' placeholder='Straße' >";
                                // Hausnummer
                                echo "<input type='text'   name='hausnummer' value='$hausnummer' placeholder='Nr.' ></td>";
                                // Geburtstag
                                echo "<td><input type='text'  name='geburtstag' value='$geburtstag'  placeholder='Geburtstag'></td>";
                            echo "</tr>";

                            // PLZ
                            echo "<tr>";
                                echo "<td><input type='text' name='postleitzahl' value='$postleitzahl' placeholder='PLZ'></td>";
                                echo "<td><input type='text' name='bundesland' value='$bundesland' placeholder='Bundesland'></td>";
                            echo "</tr>";

                            // Stadt
                            echo "<tr>";
                                echo "<td><input type='text'  name='stadt' value='$stadt' placeholder='Stadt'></td>";
                                // Land
                                echo "<td><input type='text'  name='land' value='$land' placeholder='Land'></td>";
                            echo "</tr>";

                            // Telefon1
                            echo "<tr>";
                                echo "<td><input type='text'   name='telefon1' value='$telefon1' placeholder='Telefon1' >";
                                echo "<input type='text' size='10'  name='telefon1art' value='$telefon1art' placeholder='Handy' ></td>";
                                // Fax
                                echo "<td><input type='text'   name='fax' value='$fax' placeholder='Fax'></td>";
                            echo "</tr>";

                            // Telefon 2
                            echo "<tr>";
                                echo "<td><input type='text'   name='telefon2' value='$telefon2' placeholder='Telefon2' >";
                                echo "<input type='text' size='10'  name='telefon2art' value='$telefon2art' placeholder='Home' ></td>";
                                // Gruppe
                                echo "<td><input type='text'   name='gruppe' value='$gruppe' placeholder='Gruppe'></td>";
                            echo "</tr>";

                            // Telefon 3
                            echo "<tr>";
                                echo "<td><input type='text'   name='telefon3' value='$telefon3' placeholder='Telefon 3' >";
                                echo "<input type='text' name='telefon3art' value='$telefon3art' placeholder='Arbeit' ></td>";
                                // E-Mail
                                echo "<td><input type='text'   name='email' value='$email' placeholder='E-Mail'></td>";
                            echo "</tr>";

                            // Telefon 4
                            echo "<tr>";
                                echo "<td><input type='text'   name='telefon4' value='$telefon4' placeholder='Telefon 4' >";
                                echo "<input type='text' name='telefon4art' value='$telefon4art' placeholder='Privat' ></td>";
                            echo "</tr>";

                            // Skypename
                            echo "<tr>";
                                echo "<td><input type='text'   name='skype' value='$skype'  placeholder='Skypename'></td>";
                                // Facebook
                                echo "<td><input type='text'   name='facebook' value='$facebook' placeholder='facebook'>";
                            echo "</tr>";

                            // Notizen
                            echo "<tr>";
                            echo "<td colspan='4'>";
                                //echo "<textarea name='notizen' class='ckeditor'>$notizen</textarea>";
                            echo "</td>";
                            echo "</tr>";

                        echo "</table>";
                        
                        // Buttons
                        echo "<input type='hidden' name='eintragenja' value='' />";
                        echo "<input type='submit' name='eintragen' value='Speichern'>";
                        echo "<a href='?' class='highlightedLink'>Exit</a></h2>";
                    echo "</div>";
                
                echo "</form>";
            }
        }
    }

    /**
     * Ermöglicht das eintragen von Datensätzen zum Adressbuch in die Datenbank, 
     * die nötigen GET Vars liefert UserErstellenEingabe();
     * 
     * @return void
     */
    public function UserErstellenFunction()
    {
        if ($this->userHasRight(59, 0) == true) {

            if (isset($_POST['eintragen'])) {
                if ($_POST['eintragen'] != "") {
                    $vorname = $_POST["vorname"];
                    $nachname = $_POST["nachname"];
                    $strasse = $_POST["strasse"];
                    $hausnummer = $_POST["hausnummer"];
                    $postleitzahl = $_POST["postleitzahl"];
                    $stadt = $_POST["stadt"];
                    $bundesland = $_POST["bundesland"];
                    $land = $_POST["land"];
                    $telefon1 = $_POST["telefon1"];
                    $telefon2 = $_POST["telefon2"];
                    $telefon3 = $_POST["telefon3"];
                    $telefon4 = $_POST["telefon4"];
                    $telefon1art = $_POST["telefon1art"];
                    $telefon2art = $_POST["telefon2art"];
                    $telefon3art = $_POST["telefon3art"];
                    $telefon4art = $_POST["telefon4art"];
                    $email = $_POST["email"];
                    $fax = $_POST["fax"];
                    $gruppe = $_POST["gruppe"];
                    $skype = $_POST["skype"];
                    $facebook = $_POST["facebook"];
                    $geburtstag = $_POST["geburtstag"];
                    // $notizen = $_POST["notizen"];
                    if ($nachname == "" or $vorname == "") {
                        echo "<p class='meldung'>Eingabefehler. Es muss mindestens ein Vorname 
                        und ein Nachname eingegeben werden. <a href='?eintragenja=1' class='buttonlink'>Zurόck</a></p>";
                    } else {
                        $eintrag = "
                            INSERT INTO adressbuch (geburtstag, vorname, nachname, strasse, hausnummer, postleitzahl,
                            stadt, bundesland, land, telefon1, telefon2, telefon3, telefon4, telefon1art, telefon2art,
                            telefon3art, telefon4art, email, skype, facebook, fax, gruppe) VALUES ('$geburtstag',
                            '$vorname','$nachname','$strasse','$hausnummer','$postleitzahl','$stadt','$bundesland','$land'
                            ,'$telefon1','$telefon2','$telefon3','$telefon4','$telefon1art','$telefon2art','$telefon3art',
                            '$telefon4art','$email','$skype','$facebook','$fax','$gruppe'
                        )";

                        if ($this->sqlInsertUpdateDelete($eintrag) == true) {
                            echo "<p class='erfolg'>Adressbucheintrag wurde erfolgreich hinzugef&uuml;gt! <a href='?eintragenja=1' class='buttonlink'>Zurück</a></p>";
                        } else {
                            echo "<p class='meldung'>Fehler beim speichern der Daten<a href='?eintragenja=1' class='buttonlink'>Zurück</a></p>";
                        }
                    }
                }
            }
        }
    }

    /**
     * Ließt alle eingetragenen Datensaetze aus dem Adressbuch aus.
     * $query z. B.: "SELECT id, vorname, nachname FROM adressbuch ORDER BY nachname"
     * Gibt aus: $row->tabellenspalte
     * 
     * @return $ausgabe
     */
    public function datenbankListAllerEintraege()
    {
        if ($this->userHasRight(13, 0) == true) {
            $ausgabe = "";
            $query = "SELECT * FROM adressbuch ORDER BY nachname";
            $row = $this->sqlselect($query);
            echo "<table class='kontoTable'>";
            echo "<thead>";
                echo "<td>Name</td>"; 
                echo "<td id='width140px'>Telefonnummer</td>"; 
                // echo "<td>E-Mail</td>"; 
                echo "<td>Gruppe</td>"; 
                echo "<td>Geburtstag</td>";
            echo "</thead>";
            for ($i = 0; $i < sizeof($row); $i++) {

                echo "<tbody>";
                echo "<td><a href='eintrag.php?bearbeiten=" . $row[$i]->id . "'>" . substr($row[$i]->vorname, 0, 10) . " " . substr($row[$i]->nachname, 0, 10) . "</a></td>";
                echo "<td>" . $row[$i]->telefon1 . "</td>";
                // echo "<td><a href='mailto:" . $row[$i]->email . "'>" . $row[$i]->email . "</a></td>";
                echo "<td>" . $row[$i]->gruppe . "</td>";
                if ($row[$i]->geburtstag == "0000-00-00") {
                    echo "<td></td>";
                } else {
                    echo "<td>" . $row[$i]->geburtstag . "</td>";
                }
                echo "</tbody>";
            }
            echo "</table>";
        }
    }

    /**
     * Zeigt alle Geburtstage aus dem gewδhlten Monat an.
     * 
     * @return void
     */
    public function showMonthGesamt()
    {

        if ($this->userHasRight(22, 0) == true) {

            if (isset($_GET['month'])) {
                $monat = $_GET['month'];
                if ($monat != "") {
                    $select = "SELECT *, month(geburtstag) AS monat, day(geburtstag) as tag FROM adressbuch WHERE month(geburtstag) = '$monat' ORDER BY tag";

                    $row = $this->sqlselect($select);
                    echo "<div id='draggable' class='summe'>";
                    echo "<a href='?#gebs' class='closeSumme'>X</a>";
                    echo "<h2>Detailansicht Monat</h2>";
                    for ($i = 0; $i < sizeof($row); $i++) {
                        echo "<a href='eintrag.php?bearbeiten=" . $row[$i]->id . "'><strong>" . $row[$i]->tag . ".</strong> " . $row[$i]->vorname . " " . $row[$i]->nachname . "</a><br>";
                    }

                    echo "</div>";
                }
            }
        }
    }

    /**
     * Gibt die Kontakte einer bestimmten Gruppe aus.
     * 
     * @param string $group Gruppenname
     * 
     * @return array
     */
    public function showContractsFromGroup($group)
    {
        if ($this->userHasRight(22, 0) == true) {
            $contacts = $this->sqlselect("SELECT * FROM adressbuch WHERE gruppe = '$group'");

            return $contacts;
        }
    }

    /**
     * Gibt ein Array zeilenweise aus.
     * 
     * @param string $array Array
     * 
     * @return void
     */
    public function arrayAusgeben($array)
    {
        for ($i = 0; $i < sizeof($array); $i++) {
            echo "<p>" . $array[$i] . "</p>";
        }
    }

    /**
     * Zeigt die Geburtstage aus der Adressbuchdatenbank an.
     * 
     * @return void
     */
    public function gebKalender()
    {
        if ($this->userHasRight(22, 0) == true) {
            echo "<div class='mainbodyDark'>Gruppen:";
            // Alle Gruppen aus DB ziehen:
            $allgroups = $this->sqlselect("SELECT * FROM adressbuch GROUP BY gruppe");

            // Checkboxen erstellen:
            echo "<form action='#gebs' method=post>";
            for ($i = 0; $i < sizeof($allgroups); $i++) {

                //Kontakte ohne Gruppe ignorieren:
                if ($allgroups[$i]->gruppe != "") {
                    echo " <input onChange='this.form.submit();' type=checkbox name='checkedGroups[$i]' value='" . $allgroups[$i]->gruppe . "' ";
                }

                if (isset($_POST['checkedGroups'])) {
                    if (isset($_POST['checkedGroups'][$i])) {
                        if ($_POST['checkedGroups'][$i] == $allgroups[$i]->gruppe) {
                            echo " checked=checked ";
                        }
                    }
                }

                echo " ></input> ";
                if ($allgroups[$i]->gruppe == "") {
                    //echo "<label for='" . $allgroups [$i]->gruppe . "' >Ohne Gruppe</label>";
                } else {
                    echo "<label for='" . $allgroups[$i]->gruppe . "' >" . $allgroups[$i]->gruppe . "</label>";
                }
            }

            echo "</form>";
            echo "</div>";

            $order = " ORDER BY tag";

            if (isset($_POST['checkedGroups'])) {
                $selectedGroups = $_POST['checkedGroups'];
            } else { 
                $selectedGroups = "";
            }

            $counterFόrOR = 0;

            for ($i = 1; $i <= 12; $i++) {
                echo "<div class='kalender'>";
                echo "<h2><a href='?month=$i#gebs'>" . $this->getMonthName($i) . "</a></h2>";
                if ($this->userHasRight("22", 0) == true) {

                    if ($selectedGroups == "") {
                        $query = "SELECT *
                        , month(geburtstag) AS monat
                        , day(geburtstag) as tag
                        FROM adressbuch
                        WHERE month(geburtstag) = '$i' ORDER BY tag, vorname";
                    } else {
                        //Query zusammenbauen:
                        $query = "SELECT *, month(geburtstag) AS monat, day(geburtstag) as tag
                        FROM adressbuch
                        WHERE ";

                        $counterFόrOR = 0;

                        //WHERE Klauseln bauen:
                        for ($y = 0; $y < sizeof($allgroups); $y++) {
                            if (isset($selectedGroups[$y]) and $selectedGroups[$y] != "") {

                                $query .= "month(geburtstag) = '$i' AND gruppe = '" . $selectedGroups[$y] . "' ";
                                $counterFόrOR = $counterFόrOR + 1;

                                if ($counterFόrOR < sizeof($selectedGroups)) {
                                    $query .= "OR ";
                                }

                            }
                        }
                        //Query ordnen nach:

                        $query .= " ORDER BY tag, vorname";
                    }

                    //Aus Datenbank laden:
                    $getGeburtstage = $this->sqlselect($query);

                    $j = 1;
                    if (sizeof($getGeburtstage) > 6) { // wenn nicht genug Platz ist.
                        for ($x = 0; $x < sizeof($getGeburtstage) and $j <= 5; $x++) {

                            $monat = $getGeburtstage[$x]->monat;

                            echo "<a href='eintrag.php?bearbeiten=" . $getGeburtstage[$x]->id . "'><strong>" . $getGeburtstage[$x]->tag . ".</strong> " . $getGeburtstage[$x]->vorname . " " . $getGeburtstage[$x]->nachname . "</a> <br> ";
                            $j++;
                        }
                        echo "<br><a href='?month=$monat#gebs' class='kalenderLink'>alle</a>";
                    } else { // wenn genug plazt ist.
                        for ($x = 0; $x < sizeof($getGeburtstage); $x++) {
                            echo "<a href='eintrag.php?bearbeiten=" . $getGeburtstage[$x]->id . "'><strong>" . $getGeburtstage[$x]->tag . ".</strong> " . $getGeburtstage[$x]->vorname . " " . $getGeburtstage[$x]->nachname . "</a><br>";
                        }
                    } //ende else
                } //ende check
                echo "</div>";
            } //ende FOR
        }
    }
}
