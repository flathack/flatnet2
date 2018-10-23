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
 * FinanzenNEW
 * Stellt die Funktionen der Finanzen zur Verfuegung.
 *
 * PHP Version 7
 * 
 * @history 2018-09-08: Nummerformat angepasst
 *
 * @category   Classes
 * @package    Flatnet2
 * @subpackage NONE
 * @author     Steven Schödel <steven.schoedel@outlook.com>
 * @license    https://www.gnu.org/licenses/gpl-3.0.txt GNU/GPLv3
 * @version    Release: <1>
 * @link       none
 */
class FinanzenNEW extends functions
{

    /**
     * Stellt Funktionen auf der finanzen/index.php bereit
     *
     * @return void
     */
    public function mainFinanzFunction()
    {
        $besitzer = $this->getUserID($_SESSION['username']);

        $this->showCreateNewUeberweisung();

        if (!isset($_GET['newUeberweisung'])) {
            $this->showKontoHinweis($besitzer);
            // Navigationslinks
            $this->showKontenInSelect($besitzer);
            $this->showJahreLinks();
            $this->showMonateLinks();

            // Informationsmeldungen
            $this->showErrors();
            $this->showFuturePastJahr();
            $this->checkJahresabschluss();
            $this->showJahresabschlussIfAvailable();

            // Umsatzveraenderungen
            $this->alterUmsatz();

            // Hauptansicht Monat
            if (isset($_GET['ganzesJahr'])) {
                $this->showWholeYear($besitzer);
            } else {
                
                $kontoID = $this->getKontoIDFromGet();
                $monat = $this->getMonatFromGet();
                $currentYear = $this->getJahrFromGet();
                $this->showMonthInKonto($besitzer, $kontoID, $monat, $currentYear);
            }

            $this->diagrammOptionen($besitzer);
            // automatische Jahresabschluss-Generation.
            $this->erstelleJahresabschluesseFromOldEintraegen();

        }
    }

    /**
     * Stellt Funktionen auf der finanzen/konten.php bereit
     *
     * @return void
     */
    public function mainKontoFunction()
    {
        if ($this->userHasRight(18, 0) == true) {
            // Besitzer:
            $besitzer = $this->getUserID($_SESSION['username']);

            // Buttons
            echo "<div class='innerBody'>";
            echo "<a href='?neuesKonto' class='rightBlueLink'>Neues Konto</a>";
            
            // Kontomanipulationen
            $this->showCreateNewUeberweisung();
            $this->showCreateNewKonto($besitzer);
            echo "</div>";

            // Konto&uuml;bersicht:
            $this->showKontoUebersicht($besitzer);
        } else {
            echo "<p class='info'>Du besitzt keine Schreibrechte in diesem Bereich.</p>";
        }

    }

    /**
     * Zeigt Statisken zu seinen eigenen Konten an.
     *
     * @return void
     */
    public function mainStatistikFunction()
    {
        if ($this->userHasRight(18, 0) == true) {
            $besitzer = $this->getUserID($_SESSION['username']);

            $anzahlKonten = $this->getAmount("SELECT * FROM finanzen_konten WHERE besitzer=$besitzer") + 0;
            $anzahlGuthabenK = $this->getAmount("SELECT * FROM finanzen_konten WHERE besitzer=$besitzer AND art=1") + 0;
            $anzahlAktiveK = $this->getAmount("SELECT * FROM finanzen_konten WHERE besitzer=$besitzer AND aktiv=1") + 0;

            $queryFK = "SELECT * FROM finanzen_konten WHERE besitzer=$besitzer AND art=3";
            $anzahlForderungsK = $this->getAmount($queryFK) + 0;

            $queryVK = "SELECT * FROM finanzen_konten WHERE besitzer=$besitzer AND art=4";
            $anzahlVerbK = $this->getAmount($queryVK) + 0;
            $queryKS = "SELECT * FROM finanzen_konten WHERE besitzer=$besitzer AND art=2";
            $anzahlKS = $this->getAmount($queryKS) + 0;
            $queryNO = "SELECT * FROM finanzen_konten WHERE besitzer=$besitzer AND art=0";
            $anzahlNO = $this->getAmount($queryNO) + 0;

            $summe = $anzahlAktiveK + $anzahlGuthabenK + $anzahlKonten + $anzahlForderungsK;
            if ($summe > 0) {

                echo "<div class='separateDivBox'>";
                echo "<h2>Statistiken</h2>";
                echo "<table class='kontoTable'>";
                echo "<p>Du hast $anzahlKonten Konten</p>";
                echo "<tbody><td><strong>Aktiv</strong></td><td><strong>$anzahlAktiveK</strong></td></tbody>";
                echo "<tbody><td>Guthabenkonten</td><td>$anzahlGuthabenK</td></tbody>";
                echo "<tbody><td>Forderungen</td><td>$anzahlForderungsK</td></tbody>";
                echo "<tbody><td>Verbindlichkeiten</td><td>$anzahlVerbK</td></tbody>";
                echo "<tbody><td>Kein Saldo</td><td>$anzahlKS</td></tbody>";
                echo "<tbody><td>Keine Kategorie</td><td>$anzahlNO</td></tbody>";
                echo "</table>";
                echo "</div>";
            }

            // Forderungen anzeigen (Art = 3)
            $getForderungskonten = $this->sqlselect($queryFK);
            $anzahlForderungsK = $this->getAmount($queryFK) + 0;
            $sumofFord = 0;
            if ($anzahlForderungsK > 0) {
                
                echo "<div class='separateDivBox'>";
                echo "<table class='kontoTable'>";
                echo "<thead><td colspan=2>Forderungen</td></thead>";
                for ($i = 0; $i < sizeof($getForderungskonten); $i++) {
                    $summe = "SELECT sum(umsatzWert) as summe FROM finanzen_umsaetze WHERE konto=" . $getForderungskonten[$i]->id . " AND datum <= CURDATE()";
                    $umsaetze = $this->sqlselect($summe);
                    $wert = round($umsaetze[0]->summe + 0, 2);

                    $sumofFord = $sumofFord + $wert;
                    if ($wert > 0) {
                        $classCSS = "";
                    } else {
                        $classCSS = "";
                    }

                    echo "<tbody id='$classCSS'><td>" . $getForderungskonten[$i]->konto . "</td><td>" . $wert . " &euro;</td></tbody>";
                }
                echo "<tfoot><td>Gesamt</td><td>$sumofFord &euro;</td></tfoot>";
                echo "</table>";
                echo "</div>";
            }

            // Verbindlichkeiten anzeigen (Art = 4)
            $getVerbindK = $this->sqlselect($queryVK);
            $anzahlVerbK = $this->getAmount($queryVK) + 0;
            $sumofVerbind = 0;
            if ($anzahlVerbK > 0) {
                echo "<div class='separateDivBox'>";
                echo "<table class='kontoTable'>";
                echo "<thead><td colspan=2>Verbindlichkeiten</td></thead>";
                for ($i = 0; $i < sizeof($getVerbindK); $i++) {
                    $summe = "SELECT sum(umsatzWert) as summe FROM finanzen_umsaetze WHERE konto=" . $getVerbindK[$i]->id . " AND datum <= CURDATE()";
                    $umsaetze = $this->sqlselect($summe);
                    $wert = round($umsaetze[0]->summe + 0, 2);

                    $sumofVerbind = $sumofVerbind + $wert;

                    if ($wert > 0) {
                        $classCSS = "";
                    } else {
                        $classCSS = "";
                    }

                    echo "<tbody id='$classCSS'><td>" . $getVerbindK[$i]->konto . "</td><td>" . $wert . " &euro;</td></tbody>";
                }
                echo "<tfoot><td>Gesamt</td><td>$sumofVerbind &euro;</td></tfoot>";
                echo "</table>";
                echo "</div>";
            }

            // Guthaben anzeigen (Art = 1)
            $queryGH = "SELECT * FROM finanzen_konten WHERE besitzer=$besitzer AND art=1";
            $getGuthaben = $this->sqlselect($queryGH);
            $sumofGuthaben = 0;
            echo "<div class='separateDivBox'>";
            echo "<table class='kontoTable'>";
            echo "<thead><td colspan=2>Guthaben</td></thead>";

            for ($i = 0; $i < sizeof($getGuthaben); $i++) {
                $summe = "SELECT sum(umsatzWert) as summe FROM finanzen_umsaetze WHERE konto=" . $getGuthaben[$i]->id . " AND datum <= CURDATE()";
                $umsaetze = $this->sqlselect($summe);
                $wert = round($umsaetze[0]->summe + 0, 2);

                $sumofGuthaben = $sumofGuthaben + $wert;

                if ($wert > 0) {
                    $classCSS = "";
                } else {
                    $classCSS = "";
                }

                echo "<tbody id='$classCSS'><td>" . $getGuthaben[$i]->konto . "</td><td>" . $wert . " &euro;</td></tbody>";
            }
            echo "<tfoot><td>Gesamt</td><td>$sumofGuthaben &euro;</td></tfoot>";
            echo "</table>";
            echo "</div>";

            // Gesamtauswertung
            echo "<div class='separateDivBox'>";
            echo "<h3>Gesamt</h3>";

            $ergebnis = round($sumofVerbind + $sumofFord, 2);
            $ergebnis2 = round($sumofVerbind + $sumofFord + $sumofGuthaben, 2);

            echo "<h4>nach Forderungen</h4>";
            if ($ergebnis >= 0) {
                echo "<p class=''>&Uuml;berschuss: $ergebnis</p>";
            } else {
                echo "<p class=''>Differenz: $ergebnis &euro;</p>";
            }

            echo "<h4>nach Guthaben</h4>";
            if ($ergebnis2 >= 0) {
                echo "<p class=''>&Uuml;berschuss: $ergebnis2</p>";
            } else {
                echo "<p class=''>Schulden: $ergebnis2 &euro;</p>";
            }

            echo "</div>";

        }
    }
    
    /**
     * Stellt die Funktionalitaeten fuer das teilen von Konten zur Verfuegung
     * 
     * @return void
     */
    public function mainShareFunction()
    {
        if ($this->userHasRight(18, 0) == true) {
            $besitzer = $this->getUserID($_SESSION['username']);
            $this->sharenav($besitzer);
            $this->createKontoShare($besitzer);
            $this->deleteKontoShare($besitzer);
            $this->showAllShares($besitzer);
        } else {
            echo "<p class='info'>Du besitzt keine Schreibrechte in diesem Bereich.</p>";
        }
    }

    /**
     * Zeigt die Kontoansicht an.
     * 
     * @return void
     */
    public function mainKontoDetail()
    {
        $besitzer = $this->getUserID($_SESSION['username']);

        $this->showBuchungRange();
        // Kontomanipulationen
        $this->showCreateNewUeberweisung();
        $this->UmbuchungUmsatz($besitzer);
        $this->showCreateNewKonto($besitzer);
        $this->showDeleteKonto($besitzer);

        // Hauptfunktion
        $this->showEditKonto($besitzer);
    }

    /**
     * Zeigt das aktuelle Konto im aktuellen Monat an.
     * 
     * @param int $monat aktueller Monat
     * @param int $konto aktuelles Konto
     * 
     * @return void
     */
    public function mainAbrechnung($monat, $konto, $currentYear)
    {
        $besitzer = $this->getUserID($_SESSION['username']);
        echo "<ul class='finanzNAV'>";
        echo "<li><a href='index.php?konto=$konto&monat=$monat'>Zur&uuml;ck</a></li>";
        echo "<li><a href=\"javascript:window.print();\">Drucken</a></li>";
        echo "</ul>";
        $monthname = $this->getMonthName($monat);
        $kontoname = $this->getKontoname($konto);
        echo "<h3>$kontoname Abrechnung f&uuml;r $monthname</h3>";

        $this->showMonthInKonto($besitzer, $konto, $monat, $currentYear);

    }

    /**
     * Speziell für die Finanzen eine Suche.
     * 
     * @param string $suchWort Suchbegriff
     * 
     * @return void
     */
    public function FinanzSuche($suchWort)
    {
        if ($this->userHasRight(23, 0) == true) {
            if (isset($suchWort) and $suchWort != "") {

                $besitzer = $this->getUserID($_SESSION['username']);

                // Suche mit Wildcards best&uuml;cken
                $suchWort = "%" . $suchWort . "%";

                // Spalten der Tabelle selektieren:
                $colums = "SHOW COLUMNS FROM finanzen_umsaetze";

                $rowSpalten = $this->sqlselect($colums);

                // SuchQuery bauen:
                // Start String:
                $querySuche = "SELECT *
                , month(datum) as monat
                , year(datum) as jahr
                FROM finanzen_umsaetze
                WHERE (besitzer=$besitzer AND id LIKE '$suchWort' ";

                // OR + Spaltenname LIKE Suchwort
                for ($i = 0; $i < sizeof($rowSpalten); $i++) {
                    $querySuche .= " OR besitzer = $besitzer AND " . $rowSpalten[$i]->Field . " LIKE '$suchWort'";
                }
                // Klammer am Ende schließen-
                $querySuche .= ")";

                // Query f&uuml;r die Suche
                $suchfeld = $this->sqlselect($querySuche);

                echo "<div id='draggable' class='summe'>";
                echo "Die Suche nach <strong>($suchWort)</strong> ergab folgendes Ergebnis:";
                echo "<div class='mainbody'>";
                echo "<table class='flatnetTable'>";
                echo "<thead><td>Konto</td><td>Umsatzname</td><td>Wert</td><td>Datum</td></thead>";
                for ($i = 0; $i < sizeof($suchfeld); $i++) {

                    $kontoname = $this->sqlselect("SELECT * FROM finanzen_konten WHERE id=" . $suchfeld[$i]->konto . " LIMIT 1");
                    $kontoname = $kontoname[0]->konto;

                    echo "<tr><td>" . $kontoname . "</td><td> " . "<a href='?konto=" . $suchfeld[$i]->konto . "&jahr=" . $suchfeld[$i]->jahr . "&monat=" . $suchfeld[$i]->monat . "&selected=" . $suchfeld[$i]->buchungsnr . "'>" . substr($suchfeld[$i]->umsatzName, 0, 20) . "</a></td>" . "<td>" . $suchfeld[$i]->umsatzWert . "</td>" . "<td>" . $suchfeld[$i]->datum . "</td>" . "</td></tr>";
                }
                echo "</table>";
                echo "</div>";

                echo "</div>";
            }
        }
    }

    /**
     * Ordnet dem Umsatz eine Kategorie zu und gibt diese aus.
     * 
     * @param int $id UmsatzID
     * 
     * @return string
     */
    function getCategoryForUmsatz($id) 
    {
        $list = [
        "amazon",
        "gehalt",
        ];

        // get umsatz information
        $umsatz = $this->sqlselect("SELECT * FROM finanzen_umsaetze WHERE id=$id");
        if (isset($umsatz[0]->id)) {
            foreach ($list as $listobject) {
                if (stristr($umsatz[0]->umsatzName, "$listobject") === false) {
                    $cat = "#none";
                } else {
                    $cat = "#$listobject";
                }
            }
        } else {
            echo "<p>UMSATZ EXISTIERT NICHT</p>";
            $cat = "#none";
        }
        return $cat;
    }

    /**
     * Ueberprüft ob ein User das Konto ansehen darf.
     * 
     * @param int $kontoid Konto ID welche geprüft wird.
     * 
     * @return void
     */
    public function checkKontoSicherheit($kontoid)
    {
        if (isset($kontoid) and is_numeric($kontoid) == true) {
            $currentuser = $this->getUserID($_SESSION['username']);

            $kontoinfos = $this->sqlselect("SELECT * FROM finanzen_konten WHERE id=$kontoid");
            $getshareInfos = $this->sqlselect("SELECT * FROM finanzen_shares WHERE target_user=$currentuser AND konto_id=$kontoid");

            if (!isset($kontoinfos[0]->id)) {
                $error = 1;
            }

            if (isset($kontoinfos[0]->besitzer) and $kontoinfos[0]->besitzer != $currentuser) {
                $error = 2;
            }
            if (isset($kontoinfos[0]->besitzer) and $kontoinfos[0]->besitzer == $currentuser) {
                $error = 3;
            }

            // Checken ob ein Share fuer diesen Benutzer vorhanden ist ...
            if (isset($getshareInfos[0]->konto_id)) {
                $error = 5;
            }
        } else {
            $error = 4;
        }

        if ($error == 1 or $error == 2 or $error == 4) {
            echo "<p class='dezentInfo'>Bitte ein g&uuml;ltiges Konto angeben.</p>";
            exit;
        }
        if ($error == 3) {
            // echo "Du darfst! Du bist der Besitzer!";
        }
        if ($error == 5) {
            $getusername = $this->getUserName($kontoinfos[0]->besitzer);
            if ($currentuser == $kontoinfos[0]->besitzer) {
                echo "<p class='dezentInfo'>Du hast dieses Konto geteilt.</p>";
            } else {
                echo "<p class='dezentInfo'>Dieses Konto geh&ouml;rt: $getusername. </p>";
            }

        }

    }

    /**
     * Zeigt die Buchungsdescription an
     * 
     * @param int $buchnr  Buchungsnummer
     * @param int $kontoid KontoID
     * @param int $monat   Monat
     * @param int $jahr    Jahr
     * 
     * @return void
     */
    function getBuchDesc(int $buchnr, int $kontoid, int $monat, int $jahr, string $view, int $besitzer) 
    {
        $sql = "SELECT * FROM finanzen_buchungsnr_desc WHERE buchnr=$buchnr LIMIT 1";
        $info = $this->sqlselect($sql);
        if (isset($_POST['buchdesctext'])) {
            
            $text = $_POST['buchdesctext'];
            if (strlen($text) > 0) {
                if (!isset($info[0]->description)) {
                    $sql = "INSERT INTO finanzen_buchungsnr_desc (buchnr, description, besitzer) values ($buchnr, '$text', '$besitzer')";
                } else {
                    $sql = "UPDATE finanzen_buchungsnr_desc SET description='$text' WHERE buchnr=$buchnr LIMIT 1";
                }
                if ($this->sqlInsertUpdateDelete($sql) == true) {
                    $this->erfolgMessage("Erfolgreich");
                } else {
                    $this->errorMessage("Nicht Erfolgreich");
                }
            } else {
                $this->infoMessage("Text leer");
            }
            
        }

        // Ausgabe
        echo "<div class='separateDivBox'>";
        echo "<form method=post>";
        echo "<h3></h3>";
        if ($view == "edit") {
            if (isset($info[0]->description)) {
                echo "<textarea name=buchdesctext class='ckeditor'> " .$info[0]->description . "</textarea>";
            } else {
                echo "<textarea name=buchdesctext class='ckeditor'> " ."" . "</textarea>";
            }
        } else {
            if (isset($info[0]->description)) {
                echo "<div class='newFahrt'>";
                echo $info[0]->description;
                echo "</div>";
            }
        }
        
        echo "<button type=submit>OK</button>";
        echo "<a class='rightRedLink' href='?konto=$kontoid&monat=$monat&jahr=$jahr'>Schließen</a>";
        echo "</form>";
        echo "</div>";
        
    }

    /**
     * Zeigt den aktuellen Monat in der Finanz&uuml;bersicht an.
     *
     * @param int $besitzer    UserID
     * @param int $kontoID     KontoID
     * @param int $monat       Monat
     * @param int $currentYear Jahr
     * 
     * @return void
     */
    public function showMonthInKonto(int $besitzer, int $kontoID, int $monat, int $currentYear)
    {
        // Kontoinfo bekommen:
        $currentMonth = $monat;
        // get konto information
        $kontoinformation = $this->sqlselect("SELECT * FROM finanzen_konten WHERE id=$kontoID AND besitzer=$besitzer");
        $this->checkKontoSicherheit($kontoID);
        if (isset($_GET['buchdesc']) AND is_numeric($_GET['buchdesc']) == true) {
            $this->getBuchDesc($_GET['buchdesc'], $kontoID, $monat, $currentYear, "edit", $besitzer);
        }

        if (isset($_GET['buchdescview']) AND is_numeric($_GET['buchdescview']) == true) {
            $this->getBuchDesc($_GET['buchdescview'], $kontoID, $monat, $currentYear, "view", $besitzer);
        }
        

        if ($kontoID > 0) {
            $umsaetze = $this->getUmsaetzeMonthFromKonto($currentMonth, $currentYear, $kontoID);

            // Jahresanfangssaldo bekommen:
            $letztesJahr = $currentYear - 1;
            $summeJahresabschluesseBisJetzt = $this->getJahresabschluesseBISJETZT($kontoID, $currentYear);
            $summeUmsaetzeDiesesJahr = $this->sqlselect(
                "SELECT sum(umsatzWert) as summe
                FROM finanzen_umsaetze
                WHERE konto = $kontoID
                AND year(datum) = $currentYear
                AND month(datum) < $currentMonth"
            );

            // Wenn das Jahr in der Zukunft liegt, werden alle
            // Ums&auml;tze der Vergangenheit einzeln addiert.
            $diesesJahr = date("Y");
            if ($this->checkIfJahrIsInFuture($diesesJahr, $currentYear) == true) {
                $getSaldoUntilNow = $this->sqlselect(
                    "SELECT sum(umsatzWert) as summe
                    FROM finanzen_umsaetze
                    WHERE konto = $kontoID
                    AND year(datum) < $currentYear"
                );
                $startsaldo = $getSaldoUntilNow[0]->summe + $summeUmsaetzeDiesesJahr[0]->summe;
            } else {
                $startsaldo = $summeJahresabschluesseBisJetzt + $summeUmsaetzeDiesesJahr[0]->summe;
            }
            $zwischensumme = $startsaldo;
            echo "<table class='kontoTable'>";

            // Update vom 30.12.2016:
            // Bei Konten Art 2 wird kein Saldo angezeigt ...
            $kontoinfos = $this->sqlselect("SELECT * FROM finanzen_konten WHERE id=$kontoID");

            if ($kontoinfos[0]->art == 2) {
                echo "<thead><td colspan=6 id='notOK'>Dieses Konto hat keinen Saldo!</td></thead>";
            }
            if ($kontoinfos[0]->art == 1) {
                echo "<thead><td colspan=6 id='ok'>Guthabenkonto</td></thead>";
            }

            // Keinen Startsaldo anzeigen:
            if ($kontoinfos[0]->art == 2) {
                $saldotext = "";
            } else {
                $saldotext = "/ Startsaldo diesen Monat: <strong>$startsaldo €";
            }
            echo "<thead>";

            if (!isset($kontoinformation[0]->mail) or $kontoinformation[0]->mail == "") {
                $mailinfo = "| Keine Mail";
            } else {
                $mailinfo = "| Send <a href='mailto:" . $kontoinformation[0]->mail . "'>MAIL</a>";
            }

            $abrechnung = "<a href='abrechnung.php?month=" . $currentMonth . "&konto=" . $kontoinformation[0]->id . "'>Abrechnung</a>";
            echo "<td colspan=6>Monat:<strong> $currentMonth </strong>im Jahr <strong>$currentYear</strong> $saldotext $mailinfo $abrechnung </td>";
            echo "</thead>";

            echo "<thead>";
            echo "<td id='small'></td>";
            echo "<td></td>";
            echo "<td id='width200px'>Umsatz</td>";
            echo "<td id='small'>Tag</td>";
            echo "<td id=''>Wert</td>";
            // echo "<td id='width140px'>Kategorien</td>";
            echo "<td>Saldo</td>";

            echo "</thead>";

            if (isset($umsaetze[0]->id)) {

                // Zeilengeneration #

                for ($i = 0; $i < sizeof($umsaetze); $i++) {
                    $zwischensumme = $zwischensumme + $umsaetze[$i]->umsatzWert;

                    $zs_farbe = round($zwischensumme, 2);
                    // Daten in Array laden:
                    $zahlen[$i] = $zwischensumme;

                    if ($zs_farbe < 0) {
                        $spaltenFarbe = "rot";
                    } else { 
                        $spaltenFarbe = "rightAlign";
                    }

                    if ($zs_farbe < 0) {
                        $zeile = " id='minus' ";
                    } else { 
                        $zeile = "";
                    }

                    if ($umsaetze[$i]->umsatzWert < 0) {
                        $zelle = " id='minus' ";
                    } else { 
                        $zelle = " id='plus' ";
                    }

                    // Wenn der Umsatz ausgew&auml;hlt wurde, dann wird er rot markiert.
                    if (isset($_GET['selected'])) {
                        if ($_GET['selected'] == $umsaetze[$i]->buchungsnr) {
                            $selected = "id='yellow'";
                        } else { 
                            $selected = "";
                        }
                    } else { 
                        $selected = "";
                    }

                    //START
                    echo "<tbody $selected>";

                    //BuchungsNR
                    echo "<td><a href='?konto=" . $umsaetze[$i]->gegenkonto . "&monat=$monat&jahr=$currentYear&selected=" . $umsaetze[$i]->buchungsnr . "'>" . $umsaetze[$i]->buchungsnr . "</a></td>";

                    //Gegenkonto
                    // Name des Gegenkontos bekommen
                    $nameGegenkonto = $this->sqlselect("SELECT * FROM finanzen_konten WHERE id=" . $umsaetze[$i]->gegenkonto . " LIMIT 1");
                    echo "<td>" . $nameGegenkonto[0]->konto . "</td>";

                    //Umsatz
                    echo "<td><a href='?konto=$kontoID&monat=$monat&jahr=$currentYear&edit=" . $umsaetze[$i]->id . "'>" . $umsaetze[$i]->umsatzName . "</a>";
                    
                    echo "<a href='?konto=$kontoID&monat=$monat&jahr=$currentYear&buchdesc=" . $umsaetze[$i]->buchungsnr . "'>I</a>"; 
                    if (isset($umsaetze[$i]->link) and $umsaetze[$i]->link != "") {
                        echo "<a href='" . $umsaetze[$i]->link . "'>link</a>";
                    }
                    $sql = "SELECT * FROM finanzen_buchungsnr_desc WHERE buchnr=" . $umsaetze[$i]->buchungsnr . " LIMIT 1";
                    $infotext = $this->sqlselect($sql);
                    if (isset($infotext[0]->buchnr)) {
                        echo "<a href='?konto=$kontoID&monat=$monat&jahr=$currentYear&buchdescview=" . $umsaetze[$i]->buchungsnr . "' name=info>&#9432;</a>";
                    }
                    echo "</td>";

                    //TAG
                    echo "<td>" . $umsaetze[$i]->tag . "</td>";

                    //WERT
                    echo "<td $zelle>"; 
                        echo number_format(round($umsaetze[$i]->umsatzWert, 4), 2, ",", ".");
                    echo " &#8364;</td>";

                    //Kategorie
                    // $cat = $this->getCategoryForUmsatz($umsaetze[$i]->id);
                    // echo "<td>$cat</td>";

                    //SALDO
                    if ($kontoinfos[0]->art == 2) {
                        $spaltenFarbe = "";
                        echo "<td id='$spaltenFarbe'>" . "-" . "</td>";
                    } else {
                        // Update vom 08.09.2018: Links werden im Saldo angezeigt und 
                        // ermöglichen eine direkte Erstellung einer Buchung

                        echo "<td id='$spaltenFarbe'>";
                            $k = $umsaetze[$i]->konto;
                            $tk = $umsaetze[$i]->gegenkonto;
                            $w = round($zwischensumme, 4);
                            $link = "konto=$k&targetkonto=$tk&monat=$monat&jahr=$currentYear&wert=$w&newUeberweisung";
                            echo "<a id='nostyle' href='?$link'>";
                                echo number_format(round($zwischensumme, 4), 2, ",", ".") . " &#8364;";
                            echo "</a>";
                        echo "</td>";
                    }
                    echo "</tbody>";

                    // HEUTE Zeile anzeigen
                    $timestamp = time();
                    $heute = date("Y-m-d", $timestamp);
                    if ($umsaetze[$i]->datum < $heute 
                        AND isset($umsaetze[$i + 1]->datum) 
                        AND $umsaetze[$i + 1]->datum >= $heute
                    ) {
                        $heute = date("d.m.Y", $timestamp);
                        echo "<tbody id='today'>"; 
                            echo "<td colspan='7'><a name='heute'>Heute ist der $heute</a> n&auml;chster Umsatz: " . $umsaetze[$i + 1]->umsatzName . "</td>"; 
                        echo "</tbody>";
                    }
                }
            } else {

                // Wenn kein Umsatz gefunden wird, den n&auml;chsten anzeigen:

                // CURDATE zusammenbauen.
                if ($monat < 10) {
                    $monat = "0" . $monat;
                }
                $curdate = $currentYear . "-" . $monat . "-01";

                $naechsterUmsatz = $this->sqlselect("SELECT *, month(datum) as monat, year(datum) as jahr FROM finanzen_umsaetze WHERE besitzer=$besitzer AND konto=$kontoID AND datum > '$curdate' ORDER BY datum ASC LIMIT 1");

                echo "<tbody id='plus'><td colspan=8>$curdate In diesem Monat gibt es keine Ums&auml;tze, der n&auml;chste Umsatz lautet: </td></tbody>";

                for ($k = 0; $k < sizeof($naechsterUmsatz); $k++) {
                    $naechsterMonat = $naechsterUmsatz[$k]->monat;
                    echo "<tbody id=''>" . "<td colspan=2>" . $naechsterUmsatz[$k]->datum . "</td>" . "<td colspan=6>" . $naechsterUmsatz[$k]->umsatzName . ",  <a href='?konto=$kontoID&monat=$naechsterMonat&jahr=" . $naechsterUmsatz[$k]->jahr . "'>springe zu Monat</a></td>" . "</tbody>";
                }
            }
            $differenz = number_format(round($zwischensumme - $startsaldo, 4), 2, ",", ".");

            if ($kontoinfos[0]->art == 2) {
                echo "<tfoot>";
                    echo "<td colspan=6 id='rightAlign'>Endsaldo: </td>";
                    echo "<td id='rightAlign'> - </td>"; 
                echo "</tfoot>";
            } else {
                echo "<tfoot><td colspan=5 id='rightAlign'>Endsaldo: </td><td id='rightAlign'>" . number_format(round($zwischensumme, 2), 2, ",", ".") . " &#8364; </td></tfoot>";
            }

            echo "</table>";
            if ($differenz > 0) {
                echo "<p class='dezentInfo'>Kontostandver&auml;nderung: $differenz &euro;, h&ouml;herer Saldo als im Vormonat.</p>";
            } else if ($differenz < 0) {
                echo "<p class='info'>Kontostandver&auml;nderung: $differenz &euro;, geringerer Saldo als im Vormonat.</p>";
            } else if ($differenz == 0) {
                echo "<p class='dezentInfo'>Keine Saldo Ver&auml;nderung</p>";
            }
            // Zeigt das Diagramm an
            if (isset($zahlen)) {
                $this->showDiagramme($zahlen, "400", "200");
            }
        } else {
            echo "<div class='newCharWIDE'><h3>Um Fortzufahren musst du ein Konto ausw&auml;hlen</h3>";

            echo "</div>";
        }
    }

    /**
     * Loescht markierte Zeilen / Umsaetze in der showWholeYear Funktion
     * 
     * @param int $besitzer UserID
     * 
     * @return void
     */
    public function deleteMarkedUmsaetze($besitzer)
    {

        if (isset($_POST['numbers'])) {
            $numbers = $_POST['numbers'];

            echo "<div class='alterUmsatz'><h2>L&ouml;sche ... </h2>";
            $i = 0;
            foreach ($numbers as $buchungsnr) {
                $ownerofUmsatz = $this->sqlselect("SELECT id, besitzer FROM finanzen_umsaetze WHERE buchungsnr=$buchungsnr LIMIT 1");
                $ownerofUmsatzBesitzer = $ownerofUmsatz[0]->besitzer;
                if ($ownerofUmsatzBesitzer != $besitzer) {
                    echo "<p class='meldung'>Du darfst diese Aktion nicht ausf&uuml;hren.</p>";
                } else {
                    echo "<p>$i - Buchungsnummer: <input type=number name=numbers[$i] value=$buchungsnr readonly /> </p>";
                    $query = "DELETE FROM finanzen_umsaetze WHERE buchungsnr=$buchungsnr AND besitzer=$besitzer";
                    if ($this->sqlInsertUpdateDelete($query) == true) {
                        echo "<p class='erfolg'>$buchungsnr gel&ouml;scht!</p>";
                    }
                }

                $i++;
            }
            echo "</div>";

        }

        if (isset($_POST['marked'])) {
            $marked = $_POST['marked'];

            echo "<div class='alterUmsatz'><h2>Zum l&ouml;schen ausgew&auml;hlt</h2>";
            echo "<form method=post>";
            $i = 0;
            foreach ($marked as $buchungsnr) {
                echo "<p>$i - Buchungsnummer: <input type=number name=numbers[$i] value=$buchungsnr readonly /> </p>";
                $i++;
            }
            echo "<p class='info'>Die angegebenen Buchungen wirklich l&ouml;schen? Der Vorgang kann nicht R&uuml;ckg&auml;ngig gemacht werden! <input type=submit value='Ja l&ouml;schen' /></p>";
            echo "</form>";
            echo "</div>";

        } else {
            $marked = "";
        }
    }

    /**
     * Export
     * TODO
     * 
     * @return void
     */
    public function export()
    {

        //Sicherheitscheck:
        $currentuser = $this->getUserID($_SESSION['username']);
        $konto = $_GET['konto'];

        $kontouser = $this->sqlselect("SELECT id,konto,besitzer FROM finanzen_konten WHERE id=$konto LIMIT 1");

        if ($kontouser[0]->besitzer == $currentuser) {
            echo "das Konto gehoert dir!";

        } else {
            echo "FALSCH";
        }

    }

    /**
     * Zeigt alle Ums&auml;tze des Jahres an.
     * 
     * @param int $besitzer UserID
     * 
     * @return void
     */
    public function showWholeYear($besitzer)
    {
        if (isset($_GET['konto']) and isset($_GET['ganzesJahr'])) {
            $jahr = $_GET['ganzesJahr'];
            $konto = $_GET['konto'];

            $this->deleteMarkedUmsaetze($besitzer);

            $query = "SELECT *, day(datum) as tag, year(datum) as jahr, month(datum) as monat
            FROM finanzen_umsaetze	WHERE konto = $konto HAVING jahr=$jahr ORDER BY monat,tag,id";
            $umsaetze = $this->sqlselect($query);

            $startSaldo = $this->getJahresabschluesseBISJETZT($konto, $jahr);

            $zwischensumme = round($startSaldo, 4);
            echo "<form method=post>";
            echo "<table class='kontoTable'>";
            echo "<thead>" 
            . "<td>Mark</td>" 
            . "<td>Buchungsnr</td>" 
            . "<td>Gegenkonto</td>" 
            . "<td>Umsatz</td>" 
            . "<td>Wert</td>" 
            . "<td>Tag</td>" 
            . "<td>Saldo</td>" 
            . "</thead>";
            echo "<thead>" 
            . "<td><input type=submit name=delete value=delete /></td>" 
            . "<td>" 
            . "Startsaldo: $startSaldo" 
            . "</td>" 
            . "<td colspan=5><a href='export.php?year=$jahr&konto=$konto'>EXPORT TO CSV</a></td>"
            . "</thead>";

            if (!isset($umsaetze[0]->id)) {
                echo "<tbody><td id='minus' colspan=6>F&uuml;r das Jahr $jahr sind keine Ums&auml;tze verf&uuml;gbar</td></tbody>";
            }

            for ($i = 0; $i < sizeof($umsaetze); $i++) {

                // Monatszeilen einfuegen
                if (isset($umsaetze[$i - 1]->monat)) {
                    if ($umsaetze[$i - 1]->monat != $umsaetze[$i]->monat) {
                        echo "<thead><td></td><td colspan=5><a href='#" 
                        . $this->getMonthName($umsaetze[$i]->monat) 
                        . "'>" . $this->getMonthName($umsaetze[$i]->monat) 
                        . "</a></td><td>$zwischensumme &#8364;</td></thead>";
                    }
                }

                $nameGegenkonto = $this->sqlselect("SELECT * FROM finanzen_konten WHERE id=" . $umsaetze[$i]->gegenkonto . " LIMIT 1");

                echo "<tbody>";

                if ($umsaetze[$i]->umsatzWert < 0) {
                    $zelle = " id='minus' ";
                } else {
                    $zelle = " id='plus' ";
                }

                // Radio Button
                echo "<td>";
                echo "<input type=checkbox id=" . $umsaetze[$i]->buchungsnr . " name=marked[$i] value=" . $umsaetze[$i]->buchungsnr . ">";
                echo "</td>";

                echo "<td><a href='?konto=" . $umsaetze[$i]->gegenkonto . "&monat=" . $umsaetze[$i]->monat . "&jahr=$jahr&selected=" . $umsaetze[$i]->buchungsnr . "'>" . $umsaetze[$i]->buchungsnr . "</a></td>";
                echo "<td>" . $nameGegenkonto[0]->konto . "</td>";
                echo "<td>" . $umsaetze[$i]->umsatzName . "</td>";
                echo "<td $zelle>" . $umsaetze[$i]->umsatzWert . "</td>";
                echo "<td>" . $umsaetze[$i]->tag . "</td>";

                // Berechnung Zwischensumme:
                $zwischensumme = $zwischensumme + $umsaetze[$i]->umsatzWert;

                echo "<td>" . $zwischensumme . "</td>";
                echo "</tbody>";
            }
            echo "<tfoot><td><input type=submit name=delete value=delete /></td><td colspan=6>Endsaldo: $zwischensumme</td></tfoot>";
            echo "</table>";
            echo "</form>";
        }

    }

    /**
     * Zeigt Optionen f&uuml;r das Diagramm an.
     *
     * @param int $besitzer ID Des Besitzers
     *
     * @return void
     */
    public function diagrammOptionen($besitzer)
    {
        if (isset($_GET['konto']) and isset($_GET['jahr'])) {
            echo "<div>";
            $konto = $_GET['konto'];

            $jahr = $_GET['jahr'];
            //echo "<a class='buttonlink' href='?kontoOpt=$konto&jahr=$jahr&gesamtesJahr'>Jahr anzeigen</a>";
            //echo "<a class='buttonlink' href='?kontoOpt=$konto&alles'>Alles anzeigen</a>";
            echo "<a class='buttonlink' href='/flatnet2/finanzen/detail.php?editKonto=$konto'>Konto-Einstellungen</a>";
            echo "</div>";
        }

        if (isset($_GET['ganzesJahr']) and isset($_GET['konto']) and isset($_GET['ganzesJahr'])) {

            $konto = $_GET['konto'];

            $jahr = $_GET['ganzesJahr'];

            $query = "SELECT * FROM finanzen_umsaetze WHERE besitzer = $besitzer AND konto = $konto AND year(datum) = $jahr";
            $zahlen = $this->sqlselect($query);
            $zwischensumme = 0;

            // Zwischensummen bilden und in Var schreiben

            for ($i = 0; $i < sizeof($zahlen); $i++) {
                $zwischensumme = $zahlen[$i]->umsatzWert + $zwischensumme;
                $arrayFuerDiagramm[$i] = $zwischensumme;
            }

            if (isset($arrayFuerDiagramm)) {
                $this->showDiagramme($arrayFuerDiagramm, "400", "200");
            }
        }

    }

    /**
     * Zeigt die Navigation innerhalb der Finanzanwendung an.
     * 
     * @return void
     */
    public function showNavigation()
    {
        $kontoID = $this->getKontoIDFromGet();
        $monat = $this->getMonatFromGet();
        $jahr = $this->getJahrFromGet();

        echo "<div class='finanzNAV'>";
        echo "<ul>";
        echo "<li id='monate'><a href='index.php'>Finanzverwaltung - Startseite</a></li>";
        echo "<li id='konten'><a href='konten.php'>Konten</a></li>";
        echo "<li id='shares'><a href='shares.php'>Shared Konten</a></li>";
        echo "<li><a href='?konto=$kontoID&monat=$monat&jahr=$jahr&newUeberweisung' >Neue Buchung</a></li>";
        echo "</ul>";
        echo "</div>";
    }

    /**
     * Zeigt die Navigation der Monate an
     * 
     * @history 2018-09-07: // non numeric value "notice" behoben.
     * 
     * @return void
     */
    public function showMonateLinks()
    {

        $jahr = $this->getJahrFromGet();
        $konto = $this->getKontoIDFromGet();
        $monat = $this->getMonatFromGet();
        echo "<ul class='FinanzenMonate'>";
        if ($monat == 1) {
            $monatzurueck = 12;
            $jahrzurueck = $jahr - 1;
        } else {
            // "encountered a non numeric value" Fehler behoben, 
            // wenn kein Monat ausgewaehlt ist
            if (is_numeric($monat)) {
                $jahrzurueck = $jahr;
                $monatzurueck = $monat - 1;
            } else {
                $jahrzurueck = 2017;
                $monatzurueck = 1;
            }
        }
        if ($monat == 12) {
            $monatvor = 1;
            $jahrvor = $jahr + 1;
        } else {
            // "encountered a non numeric value" Fehler behoben, 
            // wenn kein Monat ausgewaehlt ist
            if (is_numeric($monat)) {
                $monatvor = $monat + 1;
                $jahrvor = $jahr;
            } else {
                $monatvor = 1;
                $jahrvor = 2019;
            }
        }
        echo "<li>";
        echo "<a href='?konto=$konto&monat=$monatzurueck&jahr=$jahrzurueck'> &laquo; </a>";
        echo "</li>";

        echo "<li ";
        if ($monat == 1) {
            echo " id='selected' ";
        }
        echo "><a href='?konto=$konto&monat=1&jahr=$jahr'>Januar</a></li>";
        echo "<li ";
        if ($monat == 2) {
            echo " id='selected' ";
        }
        echo "><a href='?konto=$konto&monat=2&jahr=$jahr'>Februar</a></li>";
        echo "<li ";
        if ($monat == 3) {
            echo " id='selected' ";
        }
        echo "><a href='?konto=$konto&monat=3&jahr=$jahr'>M&auml;rz</a></li>";
        echo "<li ";
        if ($monat == 4) {
            echo " id='selected' ";
        }
        echo "><a href='?konto=$konto&monat=4&jahr=$jahr'>April</a></li>";
        echo "<li ";
        if ($monat == 5) {
            echo " id='selected' ";
        }
        echo "><a href='?konto=$konto&monat=5&jahr=$jahr'>Mai</a></li>";
        echo "<li ";
        if ($monat == 6) {
            echo " id='selected' ";
        }
        echo "><a href='?konto=$konto&monat=6&jahr=$jahr'>Juni</a></li>";
        echo "<li ";
        if ($monat == 7) {
            echo " id='selected' ";
        }
        echo "><a href='?konto=$konto&monat=7&jahr=$jahr'>Juli</a></li>";
        echo "<li ";
        if ($monat == 8) {
            echo " id='selected' ";
        }
        echo "><a href='?konto=$konto&monat=8&jahr=$jahr'>August</a></li>";
        echo "<li ";
        if ($monat == 9) {
            echo " id='selected' ";
        }
        echo "><a href='?konto=$konto&monat=9&jahr=$jahr'>September</a></li>";
        echo "<li ";
        if ($monat == 10) {
            echo " id='selected' ";
        }
        echo "><a href='?konto=$konto&monat=10&jahr=$jahr'>Oktober</a></li>";
        echo "<li ";
        if ($monat == 11) {
            echo " id='selected' ";
        }
        echo "><a href='?konto=$konto&monat=11&jahr=$jahr'>November</a></li>";
        echo "<li ";
        if ($monat == 12) {
            echo " id='selected' ";
        }
        echo "><a href='?konto=$konto&monat=12&jahr=$jahr'>Dezember</a></li>";

        echo "<li>";
        echo "<a href='?konto=$konto&monat=$monatvor&jahr=$jahrvor'> &raquo; </a>";
        echo "</li>";

        /* Ganzes Jahr anzeigen*/
        echo "<li>";
        echo "<a href='?konto=$konto&ganzesJahr=$jahr'>JAHR $jahr</a>";
        echo "</li>";

        echo "</ul>";

    }

    /**
     * Zeigt die Links der Jahre an.
     * 
     * @return void
     */
    public function showJahreLinks()
    {

        $jahr = $this->getJahrFromGet();
        $konto = $this->getKontoIDFromGet();
        $monat = $this->getMonatFromGet();

        echo "<ul class='FinanzenMonate'>";

        $currentYear = date("Y");

        //Previous Year:
        $jahrprev = $jahr - 1;
        $jahrforw = $jahr + 1;
        echo "<li id=''><a href='?konto=$konto&monat=$monat&jahr=$jahrprev'>$jahrprev</a></li>";
        echo "<li id='selected'><a href='?konto=$konto&monat=$monat&jahr=$jahr'>$jahr</a></li>";
        echo "<li id=''><a href='?konto=$konto&monat=$monat&jahr=$jahrforw'>$jahrforw</a></li>";
        echo "</ul>";
    }

    /**
     * Zeigt einen Kontohinweis an.
     * 
     * @param int $besitzer UserID
     * 
     * @return void
     */
    public function ShowKontoHinweis($besitzer)
    {
        if (!isset($_GET['konto'])) {
            echo "<p class='dezentInfo'>Um Fortzufahren, musst du ein Konto unterhalb dieser Box ausw&auml;hlen.</p>";
            $umsatzVorhanden = $this->sqlselect("SELECT id FROM finanzen_umsaetze WHERE besitzer=$besitzer");
            if (!isset($umsatzVorhanden[0]->id)) {
                echo "<p class='hinweis'>Du hast noch keine Buchungen auf deinen Konten! Erstelle mit einem Klick auf <strong>Neue Buchung</strong> eine neue Buchung. Bei Problemen werde dich an das Forum oder an einen Administrator.</p>";
            }
        }
    }

    /**
     * Zeigt ein Select mit den Konten des Nutzers an.
     *
     * @param int $besitzer UserID
     * 
     * @return void
     */
    public function showKontenInSelect($besitzer)
    {
        $monat = $this->getMonatFromGet();
        $jahr = $this->getJahrFromGet();

        $konten = $this->getAllKonten($besitzer);

        // Ueberpruefen, ob der Nutzer konten hat (Update vom 11.08.2017)
        if (!isset($konten[0]->id)) {
            $nokonto = 1;
        } else {
            $nokonto = 0;
        }
        $shared = $this->sqlselect("SELECT konto_id, target_user FROM finanzen_shares WHERE target_user=$besitzer");

        if (isset($_GET['konto'])) {
            $konto = $_GET['konto'];
        } else {
            $konto = "";
        }

        echo "<ul class='FinanzenMonate'>";

        // Wenn Nutzer kein Konto hat:
        if ($nokonto == 1) {
            echo "<li><a href='konten.php?neuesKonto'>Du hast keine eigenen Konten! Kick hier f&uuml;r Hilfe</a></li>";
        }

        if (isset($konten[0]->id) and $konten[0]->id != "" or isset($shared[0]->konto_id)) {

            for ($i = 0; $i < sizeof($konten); $i++) {

                if ($konten[$i]->aktiv == 1) {
                    echo "<li ";
                    if ($konto == $konten[$i]->id) {
                        echo " id='selected' ";
                    }
                    echo "><a href='?konto=" . $konten[$i]->id . "&monat=$monat&jahr=$jahr'>" . $konten[$i]->konto . "</a></li>";
                }
            }
            for ($j = 0; $j < sizeof($shared); $j++) {
                echo "<li ";
                if ($konto == $shared[$j]->konto_id) {
                    echo " id='selected' ";
                }
                $sharedid = $shared[$j]->konto_id;
                $name = $this->sqlselect("SELECT id, konto FROM finanzen_konten WHERE id=$sharedid");
                echo "><a href='?konto=" . $shared[$j]->konto_id . "&monat=$monat&jahr=$jahr'>" . $name[0]->konto . " (shared)</a></li>";
            }

        }
        echo "</ul>";
    }

    /**
     * Gibt die Get Variable KONTO zur&uuml;ck.
     *
     * @return int|boolean
     */
    public function getKontoIDFromGet()
    {
        $kontoID = 0;
        if (isset($_GET['konto'])) {
            if (is_numeric($_GET['konto']) == true) {
                $kontoID = $_GET['konto'];
            } else {
                $kontoID = 0;
            }
        } else {
            $kontoID = 0;
        }

        return $kontoID;
    }

    /**
     * Gibt den Monat zur&uuml;ck.
     *
     * @return unknown|boolean
     */
    public function getMonatFromGet()
    {
        if (isset($_GET['monat'])) {
            if (is_numeric($_GET['monat']) == true) {
                $monat = $_GET['monat'];
            } else {
                $monat = date("m");
            }

        } else {
            $monat = date("m");
        }

        return $monat;
    }

    /**
     * Gibt das Jahr zur&uuml;ck.
     * Min Jahr: 1930
     * Max Jahr: 2090
     *
     * @return int|numeric
     */
    public function getJahrFromGet()
    {
        if (isset($_GET['jahr'])) {
            if (is_numeric($_GET['jahr']) == true) {
                $min = 1930;
                $max = 2090;
                if ($_GET['jahr'] > $max OR $_GET['jahr'] < $min) {
                    $jahr = date("Y");
                } else {
                    $jahr = $_GET['jahr'];
                }
            } else {
                $jahr = date("Y");
            }

        } else {
            $jahr = date("Y");
        }

        return $jahr;
    }

    /**
     * Zeigt eine Meldung an, wenn das gew&auml;hlte Jahr in der Zukunft liegt.
     * 
     * @return void
     */
    public function showFuturePastJahr()
    {
        if (isset($_GET['jahr'])) {

            $jahr = $this->getJahrFromGet();
            $currentJahr = date("Y");

            if ($this->checkIfJahrIsInFuture($currentJahr, $jahr) == true) {
                echo "<p class='dezentInfo'>Das gew&auml;hlte Jahr liegt in der Zukunft.";
            }

            if ($this->checkIfJahrIsInPast($currentJahr, $jahr) == true) {
                echo "<p class='dezentInfo'>Das Jahr liegt in der Vergangenheit.";
            }
        }
    }

    /**
     * Prueft, ob Jahr in der Zukunft ist.
     *
     * @param int $currentJahr      Derzeitiges Jahr
     * @param int $zuPruefendesJahr zu prüfendes Jahr
     * 
     * @return boolean
     */
    public function checkIfJahrIsInFuture($currentJahr, $zuPruefendesJahr)
    {
        if ($zuPruefendesJahr > $currentJahr) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Prueft, ob Jahr in der Vergangenheit liegt.
     *
     * @param int $currentJahr      Derzeitiges Jahr
     * @param int $zuPruefendesJahr zu prüfendes Jahr
     * 
     * @return boolean
     */
    public function checkIfJahrIsInPast($currentJahr, $zuPruefendesJahr)
    {
        if ($zuPruefendesJahr < $currentJahr) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Prueft, ob Monat in der Zukunft ist.
     *
     * @param int $currentMonat      Derzeitiger Monat
     * @param int $zuPruefenderMonat zu prüfender Monat
     * 
     * @return boolean
     */
    public function checkIfMonatIsInFuture($currentMonat, $zuPruefenderMonat)
    {
        if ($zuPruefenderMonat > $currentMonat) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Zeigt Fehler bei den Umsätzen an.
     * 
     * @return void
     */
    public function showErrors()
    {
        // Summe aller Ums&auml;tze
        $kontostand = $this->sqlselect("SELECT sum(umsatzWert) AS summe FROM finanzen_umsaetze");

        // Buchungsnummer Check:

        // Wenn eine Buchung korrupt ist:
        if ($kontostand[0]->summe > 0 or $kontostand[0]->summe < 0) {

            echo "<div class='newChar'>";
            echo "Achtung, es wurde ein Fehler in mindestens einer Buchung entdeckt.
                Die Werte innerhalb einer Buchungsnummer sind unterschiedlich,
                dies kann verschiedene Gr&uuml;nde haben. Der Administrator wurde &uuml;ber
                dieses Problem informiert. Die betroffene Buchung wird jetzt gel&ouml;scht!";

            $selectProblem = "SELECT max(buchungsnr) as max FROM finanzen_umsaetze";
            $max = $this->sqlselect($selectProblem);

            $max = $max[0]->max;

            $i = 0;
            for ($i = 0; $i <= $max; $i++) {
                $select = "SELECT * FROM finanzen_umsaetze WHERE buchungsnr = $i ";

                // ANZAHL &Uuml;BERPR&Uuml;FEN:
                $selectAnzahl = "SELECT * FROM finanzen_umsaetze WHERE buchungsnr = $i";
                $anzahl = $this->getAmount($selectAnzahl);
                $infos = $this->sqlselect($select);

                if ($anzahl != 2 and $anzahl != 0) {
                    echo "<p class='meldung'>Unvollst&auml;ndige Buchung: " . $infos[0]->umsatzName . ", " . $infos[0]->umsatzWert . "</p>";
                    $delete = "DELETE FROM finanzen_umsaetze
                    WHERE buchungsnr = '$i' LIMIT 1";
                    if ($this->sqlInsertUpdateDelete($delete) == true) {
                        $this->logEintrag(true, "Buchung $i wurde wegen Unvollst&auml;ndigkeit gel&ouml;scht.", "Error");
                        echo "<p class='erfolg'>Fehlerhafte Buchung gel&ouml;scht</p>";
                    }
                }

                $buchung = $this->sqlselect($select);
                $j = 0;
                for ($j = 0; $j < 2; $j++) {
                    // $buchung[$j]->umsatzName . " " . $buchung[$j]->umsatzWert . "<br>";
                    if (isset($buchung[$j]->umsatzWert) and isset($buchung[$j + 1]->umsatzWert)) {
                        if ($buchung[$j]->umsatzWert * (-1) != $buchung[$j + 1]->umsatzWert) {
                            echo "<p class='meldung'>Es gibt ein Problem bei  <strong>Buchungs-Nr. $i</strong>: Werte: " . $buchung[$j]->umsatzWert . " und " . $buchung[$j + 1]->umsatzWert . " <a href='?surpress&id=" . $buchung[$j]->konto . "&UmsatzID=" . $buchung[$j]->id . "'>klicke hier um einen EDIT vorzunehmen</a></p>";
                        }
                    }
                }
            }
            echo "Klicke OK um Fortzufahren <a href='?' class='buttonlink'>OK</a>";
            echo "</div>";
            if (!isset($_GET['surpress'])) {
                exit();
            }
        }
    }

    /**
     * Zeigt mehrere Buchungen zum Thema.
     * 
     * @param object $umsatz   Derzeitiger Umsatz
     * @param int    $besitzer UserID
     * 
     * @return void
     */
    public function regelmaessigeZahlung($umsatz, $besitzer)
    {
        // Aehnliche Zahlungen vom Nutzer suchen ...
        $konto = $umsatz[0]->konto;
        $name = $umsatz[0]->umsatzName;
        $gegenkonto = $umsatz[0]->gegenkonto;
        $datum = $umsatz[0]->datum;
        $select = "SELECT * 
        FROM finanzen_umsaetze 
        WHERE besitzer=$besitzer 
        AND konto=$konto 
        AND gegenkonto=$gegenkonto 
        AND umsatzName='$name' 
        AND datum > '$datum'";
        $ergebnis = $this->sqlselect($select);

        if (isset($ergebnis[0]->id)) {
            
            $monat = $this->getMonatFromGet();
            $kontoID = $this->getKontoIDFromGet();
            $currentYear = $this->getJahrFromGet();
            $count = sizeof($ergebnis);
            $allowed = 5;
            if ($count > $allowed) {
                $max = $allowed;
            } else {
                $max = $count;
            }
            echo "<p>$count &auml;hnliche Zahlungen gefunden (Konto, Gegenkonto und Name gleich)</p>";
            echo "<table class='kontoTable'>";
            echo "<thead>"; 
            echo "<td id='small'>BuchNR</td>"; 
            echo "<td>UmsatzText</td>"; 
            echo "<td id='width140px'>Datum</td>"; 
            echo "<td id='width70px'>Betrag</td>"; 
            echo "<td id='small'></td>"; 
            echo "</thead>";
            for ($i = 0; $i < $max; $i++) {
                echo "<tbody><td>" . $ergebnis[$i]->buchungsnr . "</td>
                        <td>" . $ergebnis[$i]->umsatzName . "</td>
                        <td>" . $ergebnis[$i]->datum . "</td>
                        <td>" . $ergebnis[$i]->umsatzWert . "</td>
                        <td>" . "<a href='?konto=$kontoID&monat=$monat&jahr=$currentYear&edit=" . $ergebnis[$i]->id . "#umsatz'>edit</a>" . "</td></tbody>";
            }
            if ($count > $max) {
                echo "<tbody id=''><td colspan=5>weitere</td></tbody>";
            }
            echo "</table>";
        }

    }

    /**
     * Überprüft ein Datum
     * 
     * @param date   $date   Datum
     * @param string $format Format
     * 
     * @return date $date
     */
    public function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    /**
     * Erm&ouml;glicht das modifizieren eines Umsatzes.
     * 
     * @return void
     */
    public function alterUmsatz()
    {
        if ($this->userHasRight(18, 0) == true) {
            if (isset($_GET['edit'])) {

                $kontoLink = $this->getKontoIDFromGet();
                $monat = $this->getMonatFromGet();
                $jahr = $this->getJahrFromGet();

                $id = $_GET['edit'];
                $besitzer = $this->getUserID($_SESSION['username']);
                $umsatzInfo = $this->sqlselect("SELECT * FROM finanzen_umsaetze WHERE id = '$id' and besitzer = '$besitzer'");

                if (isset($_POST['alterUmsatz'])) {
                    $text = $_POST['umsatzName'];
                    if (isset($_POST['link'])) {
                        $link = $_POST['link'];
                    } else {
                        $link = "";
                    }

                    //Komma durch punkt ersetzen
                    $wert = str_replace(',', '.', $_POST['umsatzWert']);

                    $datum = $_POST['umsatzDatum'];
                    if ($this->validateDate($datum, "Y-m-d") == 0) {
                        // Date Variable f&uuml;llen:
                        $timestamp = time();
                        $beispieldate = date("Y-m-d", $timestamp);
                        echo "<p class='meldung'>Achtung, der Umsatz kann nicht gespeichert werden, <br>
                        Grund: Falsches Format im Datum! Bitte das Datum wie folgt angeben: z. B. $beispieldate</p>";
                        exit;
                    }
                    $besitzer = $this->getUserID($_SESSION['username']);
                    $id = $_GET['edit'];

                    // Buchungsnummer herausfinden;
                    $objektBuchungsNr = $this->sqlselect("SELECT id, buchungsnr FROM finanzen_umsaetze WHERE id = '$id'");
                    $buchungsnr = $objektBuchungsNr[0]->buchungsnr;

                    // Werte errechnen:

                    if ($wert > 0) {
                        $minusWert = $wert * (-1);
                        $plusWert = $wert;
                    } else {
                        $minusWert = $wert;
                        $plusWert = $wert * (-1);
                    }

                    if ($minusWert > 0 or $plusWert < 0) {
                        echo "<p class='meldung'>Achtung, das Vorzeichen eines Umsatzes darf nicht ver&auml;ndert werden!</p>";
                        exit();
                    }

                    // ID mit Minuswert herausfinden:
                    $minusObjekt = $this->sqlselect("SELECT * FROM finanzen_umsaetze WHERE buchungsnr = '$buchungsnr' AND umsatzWert < 0 LIMIT 1");
                    $minusID = $minusObjekt[0]->id;

                    // ID mit Minuswert herausfinden:
                    $plusObjekt = $this->sqlselect(
                        "SELECT id, buchungsnr, umsatzWert 
                        FROM finanzen_umsaetze 
                        WHERE buchungsnr = '$buchungsnr' 
                        AND umsatzWert > 0 LIMIT 1"
                    );
                    $plusID = $plusObjekt[0]->id;

                    if ($text != "" and $wert != "" and $besitzer != "" and $id != "" and $buchungsnr != "") {
                        $plusQuery = "UPDATE finanzen_umsaetze 
                        set umsatzName='$text',umsatzWert='$plusWert',datum='$datum',link='$link' 
                        WHERE besitzer='$besitzer' 
                        and id = '$plusID'";

                        $minusQuery = "UPDATE finanzen_umsaetze 
                        set umsatzName='$text',umsatzWert='$minusWert',datum='$datum',link='$link' 
                        WHERE besitzer='$besitzer' 
                        and id = '$minusID'";
                        if ($this->userHasRight(18, 0) == true) {
                            if ($this->sqlInsertUpdateDelete($plusQuery) == true and $this->sqlInsertUpdateDelete($minusQuery) == true) {
                                echo "<p class='erfolg'>Umsatz gespeichert</p>";
                            } else {
                                echo "<p class='info'>Fehler</p>";
                            }
                        } else {
                            echo "<p class='meldung'>Keine Berechtigung</p>";
                        }
                    }
                }

                if (isset($umsatzInfo[0]->id)) {
                    echo "<div id='' class='alterUmsatz'>";
                    echo "<a href='?konto=$kontoLink&monat=$monat&jahr=$jahr' class='rightRedLink'>schließen</a>";
                    echo "<form method=post>";
                    echo "<h2><a name=umsatz>" . $umsatzInfo[0]->umsatzName . "</a></h2>";
                    echo "<input type=text name=umsatzName value='" . $umsatzInfo[0]->umsatzName . "' /><br>";
                    $kontoID = $umsatzInfo[0]->gegenkonto;
                    $konto2ID = $umsatzInfo[0]->konto . "<br>";
                    $gegenkonto = $this->sqlselect("SELECT * FROM finanzen_konten WHERE id = '$kontoID'");
                    $konto = $this->sqlselect("SELECT * FROM finanzen_konten WHERE id = '$konto2ID'");
                    echo "Buchung auf: " . $konto[0]->konto . " ";
                    echo " - " . $gegenkonto[0]->konto . "<br>";
                    echo "<input type=text name=umsatzWert value='" . $umsatzInfo[0]->umsatzWert . "' /><br>";
                    echo "<input type=date name=umsatzDatum value='" . $umsatzInfo[0]->datum . "'  /><br>";
                    echo "<input type=text name=link value='" . $umsatzInfo[0]->link . "'  /><br>";
                    //echo "<input type=submit name=alterUmsatz value=Speichern />";
                    echo "<button type=submit name=alterUmsatz>Speichern</button>";
                    //echo "<input type=submit name=loeschUmsatz value=L&ouml;schen />";
                    echo "<button type=submit name=loeschUmsatz>L&ouml;schen</button>";

                    $this->regelmaessigeZahlung($umsatzInfo, $besitzer);

                    echo "</form>";
                    echo "</div>";
                } else {
                    //   echo "<div id='' class='alterUmsatz'>";
                    $isshared = $this->sqlselect("SELECT * FROM finanzen_shares WHERE konto_id=$kontoLink AND target_user=$besitzer");
                    if (isset($isshared[0]->target_user)) {
                        echo "<p class='meldung'>Du darfst ein fremdes Konto nicht bearbeiten!</p>";
                    } else {
                        $this->infoMessage("Umsatz existiert nicht");
                    }
                    //   echo "</div>";
                }

                if (isset($_POST['loeschUmsatz'])) {
                    $text = $_POST['umsatzName'];
                    $wert = $_POST['umsatzWert'];
                    $besitzer = $this->getUserID($_SESSION['username']);
                    $datum = $_POST['umsatzDatum'];
                    $id = $_GET['edit'];

                    // Buchungsnummer herausfinden;
                    $objektBuchungsNr = $this->sqlselect("SELECT id, buchungsnr FROM finanzen_umsaetze WHERE id = '$id'");
                    

                    if (!isset($objektBuchungsNr[0]->buchungsnr) or $objektBuchungsNr[0]->buchungsnr == "") {
                        $this->infoMessage("Das Objekt ist nicht vorhanden");
                    } else {
                        $buchungsnr = $objektBuchungsNr[0]->buchungsnr;
                        if ($this->userHasRight(18, 0) == true) {
                            
                            if ($this->objectExists("SELECT * FROM finanzen_buchungsnr_desc WHERE buchnr=$buchungsnr") == true) {
                                if ($this->sqlInsertUpdateDelete("DELETE FROM finanzen_buchungsnr_desc WHERE buchnr=$buchungsnr") == true) {
                                    $this->erfolgMessage("Umsatzinformationen gelöscht");
                                }
                            }
                            if ($this->objectExists("SELECT * FROM finanzen_buchungsnr_desc WHERE buchnr=$buchungsnr") == false) { 
                                if ($this->sqlInsertUpdateDelete("DELETE FROM finanzen_umsaetze WHERE besitzer=$besitzer AND buchungsnr=$buchungsnr LIMIT 2") == true) {
                                    $this->erfolgMessage("Umsatz gelöscht");
                                } else {
                                    $this->errorMessage("Fehler beim löschen des Umsatzes");
                                }
                            } else {
                                $this->errorMessage("Umsatzinformationen konnten nicht gelöscht werden, daher wird der Umsatz nicht gelöscht.");
                            }
                            
                        } else {
                            echo "<p class='meldung'>Keine Berechtigung</p>";
                        }
                    }
                    
                }
            }
        }
    }

    /**
     * Gibt die n&auml;chste freie Buchungsnummer wieder, verwendet auch alte, 
     * freie Nummern, welche kleiner als MAX sind.
     * 
     * @return nextnumber
     */
    public function nextBuchungsnummer_with_old_numbers()
    {
        $allBNRs = $this->sqlselect("SELECT buchungsnr FROM finanzen_umsaetze ORDER BY buchungsnr");
        $maxbuchung = $this->sqlselect("SELECT max(buchungsnr) as max FROM finanzen_umsaetze");
        $maxnummer = $maxbuchung[0]->max + 0;

        $counter = 0;
        for ($i = 1; $i <= $maxnummer; $i++) {
            $stop = 0;
            for ($j = 0; $j < sizeof($allBNRs); $j++) {
                if ($i == $allBNRs[$j]->buchungsnr) {
                    $stop = 1;
                }
            }

            if ($stop == 0) {
                $counter += 1;
                $nextnumber = $i;
            }
        }

        if ($counter == 0) {
            $nextnumber = $maxnummer + 1;
        }

        return $nextnumber;

    }

    /**
     * Gibt immer die n&auml;chste MAX nummer zur&uuml;ck.
     * Wird wieder verwendet, weil die Performance schlecht ist.
     * 
     * @return $buchungsnummer
     */
    public function nextBuchungsnummer()
    {
        $nextBuchungsnummer = $this->sqlselect("SELECT max(buchungsnr) as max FROM finanzen_umsaetze");
        $buchungsnummer = $nextBuchungsnummer[0]->max;
        if (!isset($buchungsnummer)) {
            $buchungsnummer = 0;
        }
        $buchungsnummer = $buchungsnummer + 1;
        return $buchungsnummer;
    }

    /**
     * Erstellt eine Überweisung
     * 
     * @param int    $besitzer UserID
     * @param int    $von      Ursprung-KontoID
     * @param int    $nach     Ziel-KontoID
     * @param string $text     Text für Buchung
     * @param string $betrag   Wert der Buchung
     * @param date   $datum    Datum
     * @param string $link     Sendeverfolgungslink
     * 
     * @return void
     */
    public function createUeberweisung($besitzer, $von, $nach, $text, $betrag, $datum, $link)
    {
        $buchungsnummer = $this->nextBuchungsnummer();
        $betragMinus = $betrag * (-1);

        $query = "INSERT INTO finanzen_umsaetze (buchungsnr, besitzer, konto, gegenkonto, umsatzName, umsatzWert, datum, link)
            VALUES ('$buchungsnummer','$besitzer','$von','$nach','$text','$betragMinus','$datum','$link')";
        $query2 = "INSERT INTO finanzen_umsaetze (buchungsnr, besitzer, konto, gegenkonto, umsatzName, umsatzWert, datum, link)
            VALUES ('$buchungsnummer','$besitzer','$nach','$von','$text','$betrag','$datum','$link')";

        if ($this->sqlInsertUpdateDelete($query) == true and $this->sqlInsertUpdateDelete($query2) == true) {
            echo "<p class='erfolg'>&Uuml;berweisung von $text durchgef&uuml;hrt (Buchungsnummer: $buchungsnummer)</p>";
        }
    }

    /**
     * Neue Überweisung.
     * Erm&ouml;glicht das Überweisen von Geld von einem auf ein anderes Konto.
     * 
     * @return void
     */
    public function showCreateNewUeberweisung()
    {
        if (isset($_GET['newUeberweisung'])) {
            if ($this->userHasRight(18, 0) == true) {

                $min = 1;
                $max = 60;

                $anzahlWeitereFelder = 12;

                $maxpast = "2008-01-01";

                if (isset($_POST['sendnewUeberweisung'])
                    and isset($_POST['valueUeberweisung'])
                    and isset($_POST['textUeberweisung'])
                    and isset($_POST['dateUeberweisung'])
                    and isset($_POST['zielKonto'])
                    and isset($_POST['absenderKonto'])
                ) {
                    $von = $_POST['absenderKonto'];
                    $datum = $_POST['dateUeberweisung'];
                    $nach = $_POST['zielKonto'];
                    if (isset($_POST['link'])) {
                        $link = $_POST['link'];
                    } else { 
                        $link = "";
                    }

                    $betrag = str_replace(',', '.', $_POST['valueUeberweisung']);

                    $betragMinus = $betrag * (-1);

                    // Wenn Absender und Ziel gleich ist:
                    if ($von == $nach) {
                        echo "<p class='meldung'>Absende und Zielkonto ist gleich. Keine Buchung m&ouml;glich.</p>";
                        exit();
                    }

                    if ($datum < $maxpast) {
                        echo "<p class='meldung'>Keine Buchung vor $maxpast m&ouml;glich!</p>";
                        exit();
                    }

                    $text = $_POST['textUeberweisung'];
                    $besitzer = $this->getUserID($_SESSION['username']);

                    if ($von != "" and $nach != "" and isset($besitzer) and $betrag != "" and $betrag > 0 and $datum != "" and $text != "") {

                        //Normale Buchung durchf&uuml;hren:
                        $this->createUeberweisung($besitzer, $von, $nach, $text, $betrag, $datum, $link);

                        //Weitere Buchungen anhand Anzahl der n&auml;chsten Monate:
                        if (isset($_POST['weiterenumber'])) {
                            if (isset($_POST['monthweitere'])) {
                                if (is_numeric($_POST['monthweitere']) == true) {
                                    $number = $_POST['monthweitere'];

                                    //Maximal 5 Jahre pro Buchung erlaubt.
                                    if ($number > $max or $number < $min) {
                                        echo "<p class='meldung'>Zu viele Buchungen, maximal 60 Buchungen erlaubt.</p>";
                                    } else {
                                        for ($x = 1; $x <= $number; $x++) {
                                            $newdate = new DateTime($datum);
                                            $newdate->modify("+$x month");
                                            echo "<p class='dezentInfo'>${x}: Buchung f&uuml;r Monat " . $newdate->format("Y-m-d") . "</p>";

                                            $this->createUeberweisung($besitzer, $von, $nach, $text, $betrag, $newdate->format("Y-m-d"), $link);

                                        }
                                    }
                                }
                            }
                        }

                        //Manuelles hinzuf&uuml;gen von Buchungen anhand des genauen Datums:
                        if (isset($_POST['weitere'])) {

                            $dates = $_POST['dates'];
                            $j = 0;
                            for ($j = 0; $j < $anzahlWeitereFelder; $j++) {

                                // Nur gef&uuml;llte Inputfelder verwenden.
                                if ($dates[$j] != "") {
                                    $this->createUeberweisung($besitzer, $von, $nach, $text, $betrag, $dates[$j], $link);
                                }
                            }
                        }
                    }
                }

                $kontoID = $this->getKontoIDFromGet();
                $monat = $this->getMonatFromGet();
                $jahr = $this->getJahrFromGet();
                echo "<div class='newFahrt'>";

                //   echo "<a href='#' onclick='window.history.go(-1)' target='_self' class='highlightedLink'>Zur&uuml;ck</a>";
                echo "<a href='?konto=$kontoID&monat=$monat&jahr=$jahr' target='_self' class='highlightedLink'>Zur&uuml;ck</a>";

                // echo "<p class='dezentInfo'>Du kommst von hier: Konto: $kontoID, Monat: $monat, Jahr: $jahr</p>";
                echo "<h2>Buchen</h2>";

                echo "<form method=post>";
                echo "<table class='kontoTable'>";
                if (isset($_POST['textUeberweisung'])) {
                    $textInput = $_POST['textUeberweisung'];
                } else {
                    $textInput = "";
                }
                echo "<tbody><td>Beschreibung</td><td><input type=text value='$textInput' placeholder='Text' name='textUeberweisung' required /></td></tbody>";

                $besitzer = $this->getUserID($_SESSION['username']);
                $select = "SELECT * FROM finanzen_konten WHERE besitzer = '$besitzer' ORDER BY konto";
                $absenderKonten = $this->sqlselect($select);

                echo "<tbody><td>Gutschrift hier</td><td><select name='zielKonto'>";
                $i = 0;
                for ($i = 0; $i < sizeof($absenderKonten); $i++) {
                    echo "<option";
                    if (isset($_POST['zielKonto']) and $absenderKonten[$i]->id == $_POST['zielKonto']) {
                        echo " selected ";
                    } elseif (isset($_GET['konto']) and $absenderKonten[$i]->id == $_GET['konto']) {
                        echo " selected ";
                    } else {
                        echo "";
                    }
                    echo " value='" . $absenderKonten[$i]->id . "'>" . $absenderKonten[$i]->konto . "</option>";
                }
                echo "</select></td></tbody>";

                echo "<tbody>"; 
                echo "<td>Absender: </td><td><select name='absenderKonto'>";
                $i = 0;
                for ($i = 0; $i < sizeof($absenderKonten); $i++) {

                    echo "<option ";
                    if (isset($_POST['absenderKonto']) 
                        and $absenderKonten[$i]->id == $_POST['absenderKonto']
                    ) {
                        echo " selected ";
                    } elseif (isset($_GET['targetkonto']) 
                        and $absenderKonten[$i]->id == $_GET['targetkonto']
                    ) {
                        echo " selected ";
                    } else {
                        echo "";
                    }
                    echo "value='" . $absenderKonten[$i]->id . "'>" . $absenderKonten[$i]->konto . "</option>";
                }
                echo "</select></td>"; 
                echo "</tbody>";

                // Date Variable f&uuml;llen:
                $timestamp = time();
                if (isset($_POST['dateUeberweisung'])) {
                    $date = $_POST['dateUeberweisung'];
                } else {
                    $date = date("Y-m-d", $timestamp);
                }
                if (isset($_GET['wert'])) {
                    $wert = $_GET['wert'];
                } else {
                    $wert = 0;
                }
                echo "<tbody><td>Betrag</td><td><input type=number step=0.01 value='$wert' placeholder='Betrag' name='valueUeberweisung' required /></td></tbody>";
                echo "<tbody><td>Datum</td><td><input type=date value='$date' placeholder='Datum' name='dateUeberweisung' required /></td></tbody>";
                echo "<tbody><td colspan='2'><input type=submit name=sendnewUeberweisung value='Absenden' /></td></tbody>";

                //LINK PRO BUCHUNG

                echo "<tbody>";
                echo "<td>Link</td><td><input type=text name=link placeholder=Link /></td>";
                echo "</tbody>";

                //WEITERE BUCHUNGEN DURCHFÜHREN

                //ENTWEDER PER ANZAHL DER MONATE
                echo "<tbody><td colspan='2'>";
                echo "<input type=checkbox name=weiterenumber value='1' class='checkbox' >";
                echo "<label for='weiterenumber'>weitere Monate ausf&uuml;hren: </label>";
                echo "</td></tbody>";
                echo "<tbody><td colspan='2'>";
                echo "<input type=number name=monthweitere value='' min=$min max=$max /> <span><br>(z.B: 1 bedeutet, dass die Buchung auch im n&auml;chsten Monat ausgef&uuml;hrt wird)</span>";
                echo "</td></tbody>";

                //ODER ALS GENAUES DATUM
                echo "<tbody><td colspan='2'>";
                echo "<input type=checkbox name=weitere value='1' class='checkbox' />";
                echo "<label for='weitere'>Diese Überweisung f&uuml;r folgende weitere Tage durchf&uuml;hren</label>";
                echo "</td></tbody>";
                $j = 0;

                for ($j = 0; $j < $anzahlWeitereFelder; $j++) {
                    echo "<tbody><td colspan='2'><input type=date name=dates[$j] value='' placeholder='$j ...' /></td></tbody>";
                }
                echo "</table>";
                echo "</form>";
                echo "</div>";
            } else {
                echo "<p class='meldung'>Keine Berechtigung</p>";
            }
        }
    }

    /**
     * Gibt die Umsaetze des angegeben Monats aus dem Konto zur&uuml;ck.
     * 
     * @param int $monat Monat
     * @param int $jahr  Jahr
     * @param int $konto KontoID
     * 
     * @return object|boolean
     */
    public function getUmsaetzeMonthFromKonto($monat, $jahr, $konto)
    {
        $umsaetze = "SELECT *
        , day(datum) as tag
        , year(datum) as jahr
        , month(datum) as monat
        FROM finanzen_umsaetze
        WHERE konto = $konto
        HAVING jahr = $jahr
        AND monat = $monat
        ORDER BY tag, id";

        $ergebnis = $this->sqlselect($umsaetze);

        if (isset($ergebnis[0]->id)) {
            return $ergebnis;
        } else {
            return false;
        }
    }

    /**
     * Zeigt eine Auswahl an Jahre an, 
     * womit die Buchungen innerhalb der Konten 
     * selektiert werden k&ouml;nnen.
     * 
     * @return void
     */
    public function showBuchungRange()
    {

        if (isset($_POST["yearRange"])) {
            $_SESSION["anzuzeigendesjahr"] = $_POST["yearRange"];
        }

        if (!isset($_SESSION["anzuzeigendesjahr"])) {
            $_SESSION["anzuzeigendesjahr"] = 0;
        }
        echo "<form method=post>";
        for ($i = 2013; $i < 2020; $i++) {
            echo "<input onChange='this.form.submit();' type='radio' value='$i' name='yearRange' id='$i' onclick=''";

            if (!isset($_SESSION["anzuzeigendesjahr"]) or $_SESSION["anzuzeigendesjahr"] != $i) {
                echo " unchecked";
            } else {
                echo " checked";
            }
            echo " />";
            echo "<label for='$i'>$i</label>";
        }

        // Alle
        echo "<input onChange='this.form.submit();' type='radio' value='0' name='yearRange' id='0' onclick=''";
        if (!isset($_SESSION["anzuzeigendesjahr"]) or $_SESSION["anzuzeigendesjahr"] != 0) {
            echo " unchecked";
        } else {
            echo " checked";
        }

        echo " />";
        echo "<label for='0'>Alles anzeigen</label>";
        echo "</form>";
    }

    /**
     * Zeigt eine &Uuml;bersicht aller Konten des besitzers in einer Auflistung an.
     * Beschreibung Konto-Art:
     * 0 normales Konto
     * 1 gr&uuml;nes Konto
     * 2 Konto ohne Saldo
     * 3 Forderungskonto
     *
     * @param int $besitzer UserID
     * 
     * @return void
     */
    public function showKontoUebersicht($besitzer)
    {
        $konten = $this->getAllKonten($besitzer);

        // Wenn es Konten gibt:
        if (isset($konten[0]->id)) {
            // Aktive Konten
            echo "<div class='innerBody'>";
            echo "<h3>Aktive Konten</h3>";
            for ($i = 0; $i < sizeof($konten); $i++) {
                if ($konten[$i]->aktiv == 1) {
                    $select2 = "SELECT sum(umsatzWert) as summe FROM finanzen_umsaetze WHERE konto=" . $konten[$i]->id . " AND datum <= CURDATE()";
                    $umsaetze = $this->sqlselect($select2);
                    if ($konten[$i]->art == 1) {
                        $mark = "Guthabenkonto";
                    } elseif ($konten[$i]->art == 2) {
                        $mark = "nolimit";
                    } elseif ($konten[$i]->art == 3) {
                        $mark = "Ford";
                    } elseif ($konten[$i]->art == 4) {
                        $mark = "Verb";
                    } else {
                        $mark = "Konto";
                    }
                    
                    echo "<div class='mainkonto'>";
                    echo "<div class='$mark'>";
                    
                    echo "<p>" . $konten[$i]->id . ": <a href='index.php?konto=" . $konten[$i]->id . "'>" . $konten[$i]->konto . "</a><br>";
                    echo "<a href='detail.php?editKonto=" . $konten[$i]->id . "'>Einstellungen</a><br>";
                    if ($konten[$i]->art == 2) {
                        echo "x";
                    } else {
                        $summe = number_format($umsaetze[0]->summe + 0, 2, ",", ".");
                        echo $summe . " €";
                    }
                    if ($konten[$i]->mail) {
                        echo "<br>" . $konten[$i]->mail . "</p>";
                    } else {
                        echo "<br>" . "..." . "</p>";
                    }

                    echo "</div>";
                    echo "</div>";
                }
            }

            echo "</div>";
            $i = 0;

            // Inaktive Konten
            echo "<div class='innerBody'>";
            echo "<h3>Nicht sichtbare Konten</h3>";
            echo "<table class='kontoTable'>";
            echo "<thead><td>ID</td><td>Name</td><td>Art</td><td>Konto Details</td><td>Saldo</td></thead>";
            for ($i = 0; $i < sizeof($konten); $i++) {
                $select2 = "SELECT sum(umsatzWert) as summe FROM finanzen_umsaetze WHERE konto=" . $konten[$i]->id . " AND datum <= CURDATE()";
                $umsaetze = $this->sqlselect($select2);
                if ($konten[$i]->aktiv == 0) {
                    if ($konten[$i]->art == 1) {
                        $mark = "Guthabenkonto";
                    } elseif ($konten[$i]->art == 2) {
                        $mark = "nolimit";
                    } else {
                        $mark = "Konto";
                    }
                    // echo "<div class='$mark'>";
                    echo "<tbody>";
                    echo "<td>" . $konten[$i]->id . "</td><td> <a href='index.php?konto=" . $konten[$i]->id . "'>" . $konten[$i]->konto; 
                    if (isset($konten[$i]->mail) AND strlen($konten[$i]->mail) > 0) {
                        echo "<a href='mailto:".$konten[$i]->mail."'>mail</a>";
                    }
                    echo "</td>";
                    echo "<td>"; 
                    if ($konten[$i]->art == 0) {
                        echo "Standard";
                    }
                    if ($konten[$i]->art == 1) {
                        echo "Guthabenkonto";
                    }
                    if ($konten[$i]->art == 2) {
                        echo "Kein Saldo";
                    }
                    if ($konten[$i]->art == 3) {
                        echo "Forderungskonto";
                    }
                    if ($konten[$i]->art == 4) {
                        echo "Verbindlichkeitskonto";
                    }
                    echo "</td>";
                    echo "<td><a href='detail.php?editKonto=" . $konten[$i]->id . "'>Details</a></td><td>";
                    if ($konten[$i]->art == 2) {
                        echo "x";
                    } else {
                        $summe = $umsaetze[0]->summe + 0;
                        echo $summe . " €";
                    }
                    echo "</td></tbody>";
                }
            }
            $inactive = $this->sqlselect("SELECT id, count(*) as anzahl FROM finanzen_konten WHERE besitzer=$besitzer AND aktiv=0");
            if ($inactive[0]->anzahl == 0) {
                echo "<tbody><td colspan=6>Zur Zeit hast du keine Konten in diesem Bereich</td></tbody>";
            }
            echo "</table>";
            echo "</div>";
        }
    }

    /**
     * Gibt alle Konten des Besitzers zur&uuml;ck.
     *
     * @param int $besitzer UserID
     * 
     * @return object|boolean
     */
    public function getAllKonten($besitzer)
    {
        $konten = $this->sqlselect("SELECT id, timestamp, konto, besitzer, aktiv, art, notizen, mail FROM finanzen_konten WHERE besitzer=$besitzer ORDER BY konto");

        if (isset($konten)) {
            return $konten;
        } else {
            return false;
        }
    }

    /**
     * Erm&ouml;glicht die Erstellung eines neuen Kontos.
     * 
     * @param int $besitzer UserID
     * 
     * @return void
     */
    public function showCreateNewKonto($besitzer)
    {
        if (isset($_GET['neuesKonto'])) {

            echo "<div class='newChar'>";
            
            echo "<a href='?' class='rightRedLink'>X</a>";

            $konten = $this->getAllKonten($besitzer);
            if (!isset($konten[0]->id) and !isset($konten[1]->id)) {
                echo "<p class='hinweis'>HILFE: Um das Finanzprodukt normal benutzen zu k&ouml;nnen,
                        brauchst du mindestens zwei Konten, als Beispiel:<br>";
                echo "Ein Konto mit dem Namen Girokonto und ein Arbeitgeber-Konto.
                        Im Anschluss an die Kontoerstellung, k&ouml;nntest du eine Buchung erstellen, welche
                        lautet: Gehalt 2500 Euro vom Arbeitgeberkonto auf dein Girokonto. Nach diesem Prinzip
                        l&auml;sst sich eine eigene Finanzverwaltung realisieren, welche dir Hilft, den
                        finanziellen &Uuml;berblick zu behalten. F&uuml;r weitere Hilfe, schau dir im Forum die komplette Dokumentation
                        dieses Produktes an.<br><br>
                        Erstelle nun zwei Konten, die Mailadresse kannst du erstmal leer lassen.</p>";

            }
            echo "<form method=post>";

            echo "<table class='kontoTable'>";
            echo "<tbody><td>Kontoname*</td><td><input type=text name=newKonto value='' placeholder='Kontoname*' required /></td></tbody>";
            echo "<tbody><td>Mailadresse f&uuml;r das Konto</td><td><input type=text name=mail value='' placeholder=Mail /></td></tbody>";
            echo "<tbody><td colspan=2><button type=submit name=insertNewKonto>Speichern</button></td></tbody>";
            echo "</table>";

            echo "</form>";

            if (isset($_POST['insertNewKonto']) and $this->userHasRight("18", 0) == true) {
                $konto = $_POST['newKonto'];
                $mail = $_POST['mail'];
                if ($konto != "") {
                    if ($this->createNewKonto($besitzer, $konto, $mail) == true) {
                        $this->erfolgMessage("Konto wurde erstellt.");
                    } else {
                        $this->infoMessage("Das Konto $konto konnte nicht erstellt werden");
                    }
                }
            }
            echo "</div>";
        }
    }

    /**
     * Erstellt ein Konto f&uuml;r den Besitzer.
     *
     * @param int    $besitzer  UserID
     * @param string $kontoname Kontoname
     * @param string $mail      Mailadresse
     * 
     * @return boolean
     */
    public function createNewKonto($besitzer, $kontoname, $mail)
    {
        // check ob Konto bereits existiert
        $check = $this->sqlselect("SELECT id,besitzer,konto FROM finanzen_konten WHERE besitzer=$besitzer AND konto='$kontoname' LIMIT 1");
        if (isset($check[0]->konto)) {
            return false;
        } else {
            $query = "INSERT INTO finanzen_konten (besitzer,konto,aktiv,mail) VALUES ('$besitzer','$kontoname',1,'$mail')";

            if ($this->sqlInsertUpdateDelete($query) == true) {
                return true;
            } else {
                return false;
            }
        }
        
    }

    /**
     * Bucht meherere Umsaetze um. 
     * Hierfuer wird der POST Befehl und die Checkboxen verwendet.
     * 
     * @param int $besitzer UserID
     * 
     * @return void
     */
    public function umbuchenMehrere($besitzer)
    {
        if (isset($_POST['saveUmbuchung']) 
            AND isset($_POST['absender']) 
            AND isset($_POST['ziel']) 
            AND isset($_POST['umbuchenNumbers'])
        ) {
            $von = $_POST['absender'];
            $nach = $_POST['ziel'];
            $buchungsnummern = $_POST['umbuchenNumbers'];

            if ($von > 0 AND $nach > 0) {

                if ($von == $nach) {
                    echo "<p class='meldung'>Absender und Zielkonto ist gleich!</p>";
                } else {
                    foreach ($buchungsnummern as $buchungsnummer) {

                        //Info ueber diese Buchung bekommen:
                        $getBuchungInfos = $this->sqlselect("SELECT * FROM finanzen_umsaetze WHERE buchungsnr = $buchungsnummer");

                        echo "Neues Absenderkonto $von, neues Gutschriftkonto = $nach";

                        for ($i = 0; $i < sizeof($getBuchungInfos); $i++) {
                            // Änderung f&uuml;r den ABSENDER
                            if ($getBuchungInfos[$i]->umsatzWert < 0) {
                                $update = "UPDATE finanzen_umsaetze SET konto=$von, gegenkonto=$nach WHERE buchungsnr=$buchungsnummer AND umsatzWert < 0";

                                if ($this->sqlInsertUpdateDelete($update) == true) {
                                    echo "<p class='erfolg'>Umbuchung erfolgt</p>";
                                } else {
                                    echo "<p class='meldung'>Es gab einen Fehler.</p>";
                                }
                            }

                            // ÄNDERUNG f&uuml;r die GUTSCHRIFT
                            if ($getBuchungInfos[$i]->umsatzWert > 0) {

                                $update = "UPDATE finanzen_umsaetze SET konto=$nach, gegenkonto=$von WHERE buchungsnr=$buchungsnummer AND umsatzWert > 0";

                                if ($this->sqlInsertUpdateDelete($update) == true) {
                                    echo "<p class='erfolg'>Umbuchung f&uuml;r $buchungsnummer erfolgt</p>";
                                } else {
                                    echo "<p class='meldung'>Es gab einen Fehler.</p>";
                                }
                            }
                        }
                    }
                }
            }
        }

        if (isset($_POST['umbuchen'])) {
            if (isset($_POST['marked'])) {
                $marked = $_POST['marked'];

                echo "<div class='newFahrt'>";
                echo "<form method=post>";
                echo "<h2>Ums&auml;tze umbuchen</h2>";
                $i = 0;
                foreach ($marked as $buchung) {
                    echo "<p>$i : Buchungsnummer: <input type=number name=umbuchenNumbers[$i] value='$buchung' readonly /></p>";
                    $i++;
                }

                $getallkonten = $this->getAllKonten($besitzer);

                echo "<p>Absender: <select name=absender>";
                for ($j = 0; $j < sizeof($getallkonten); $j++) {
                    echo "<option value='" . $getallkonten[$j]->id . "'>" . $getallkonten[$j]->konto . "</option>";
                }
                echo "</select></p>";
                echo "<p>Ziel: <select name=ziel>";
                for ($j = 0; $j < sizeof($getallkonten); $j++) {
                    echo "<option value='" . $getallkonten[$j]->id . "'>" . $getallkonten[$j]->konto . "</option>";
                }
                echo "</select></p>";

                echo "<input type=submit name=saveUmbuchung value='Sicher' />";
                echo "</form>";
                echo "</div>";
            }
        }
    }

    /**
     * Erm&ouml;glicht das editieren von Konten
     *
     * @param int $besitzer UserID
     * 
     * @return void
     */
    public function showEditKonto($besitzer)
    {
        $this->umbuchenMehrere($besitzer);

        if (isset($_POST['absenden']) 
            AND isset($_GET['editKonto']) 
            AND isset($_POST['kontoname']) 
            AND isset($_POST['notizen']) 
            AND isset($_POST['art']) 
            AND isset($_POST['mail'])
        ) {
            if ($_POST['kontoname'] != "") {
                $name = $_POST['kontoname'];
                $id = $_GET['editKonto'];
                $art = $_POST['art'];
                $mail = $_POST['mail'];
                $notizen = strip_tags(stripslashes($_POST['notizen']));
                if (isset($_POST['aktiv'])) {
                    $aktiv = 1;
                } else {
                    $aktiv = 0;
                }

                $update = "UPDATE finanzen_konten SET art=$art, konto='$name', aktiv=$aktiv, notizen='$notizen', mail='$mail' WHERE besitzer = $besitzer AND id = $id";

                if ($this->sqlInsertUpdateDelete($update)) {
                    echo "<p class='erfolg'>Konto gespeichert.</p>";
                } else {
                    echo "<p class='meldung'>Es gab einen Fehler beim speichern der Informationen.</p>";
                }
            }
        }

        if (isset($_GET['editKonto'])) {
            $id = $_GET['editKonto'];

            $select = "SELECT * FROM finanzen_konten WHERE besitzer = $besitzer AND id = $id";

            $kontoInfo = $this->sqlselect($select);

            if (isset($kontoInfo[0]->aktiv) and $kontoInfo[0]->aktiv == 0) {
                $checked = "";
            } else {
                $checked = "checked";
            }

            if (!isset($kontoInfo[0]->id)) {
                echo "<p class='meldung'>Dieses Konto gibt es nicht, oder du hast keinen Zugriff darauf.</p>";
                exit();
            }

            echo "<div class='newCharWIDE'>";
            echo "<h2>Detailinformationen von " . $kontoInfo[0]->konto . "</h2>";
            echo "<a class='rightBlueLink' href='index.php?konto=" . $kontoInfo[0]->id . "'>Gehe zu diesem Konto</a>";

            echo "<form method=post>";
            echo "<table>";
            echo "<tr><td>Name: </td><td><input type=text value='" . $kontoInfo[0]->konto . "' name=kontoname /></td>";
            echo "<td><input value=1 type=checkbox " . $checked . " name=aktiv /><label for=aktiv>Konto in &Uuml;bersicht anzeigen</label>" . "</td></tr>";

            // echo "<td>" . "<input type=number value=" . $kontoInfo [0]->art . " name=art placeholder=Kontoart /><td>Kontoart</td>";
            echo "<tr><td>Kontoart</td><td>" . "<select name=art>";
            echo "<option value=0"; 
            if ($kontoInfo[0]->art == 0) {
                echo " selected ";
            }
            echo ">Standard</option>";
            echo "<option value=1";
            if ($kontoInfo[0]->art == 1) {
                echo " selected ";
            }
            echo ">Guthabenkonto</option>";
            echo "<option value=2";
            if ($kontoInfo[0]->art == 2) {
                echo " selected ";
            }
            echo ">Kein Saldo</option>";
            echo "<option value=3";
            if ($kontoInfo[0]->art == 3) {
                echo " selected ";
            }
            echo ">Forderungskonto</option>";
            echo "<option value=4";
            if ($kontoInfo[0]->art == 4) {
                echo " selected ";
            }
            echo ">Verbindlichkeitskonto</option>";
            echo "</select>";

            echo "</tr>";

            echo "<tr><td>Mail:</td><td><input type=text value='" . $kontoInfo[0]->mail . "' name=mail /></td></tr>";

            echo "<tr><td><input type=submit name=absenden value=Speichern /></td></tr>";

            echo "</table>";

            echo "<div class='scrollContainer'>";

            echo "<p>&Uuml;bersicht vorhandener Buchungen</p>";
            echo "<table class='kontoTable'>";

            $selectKonten = "SELECT *
                , year(datum) as jahr
                , month(datum) as monat
                , day(datum) as tag
                FROM finanzen_umsaetze
                WHERE besitzer = $besitzer
                AND konto = $id
                GROUP BY gegenkonto";
            $getKonten = $this->sqlselect($selectKonten);

            for ($j = 0; $j < sizeof($getKonten); $j++) {

                //Schauen, ob das Jahr gefiltert wurde:
                if ($_SESSION["anzuzeigendesjahr"] > 0) {

                    $select2 = "SELECT *
                        , year(datum) as jahr
                        , month(datum) as monat
                        , day(datum) as tag
                        FROM finanzen_umsaetze
                        WHERE besitzer = $besitzer
                        AND konto = $id
                        AND gegenkonto = " . $getKonten[$j]->gegenkonto . "
                        AND year(datum) = " . $_SESSION['anzuzeigendesjahr'] . "
                        ORDER BY datum ASC";

                } else {
                    $select2 = "SELECT *
                        , year(datum) as jahr
                        , month(datum) as monat
                        , day(datum) as tag
                        FROM finanzen_umsaetze
                        WHERE besitzer = $besitzer
                        AND konto = $id
                        AND gegenkonto = " . $getKonten[$j]->gegenkonto . "
                        ORDER BY datum ASC";
                }

                // Buchungen dieses Kontos bekommen:

                $getKontoBuchungen = $this->sqlselect($select2);

                $getKontoName = $this->sqlselect("SELECT * FROM finanzen_konten WHERE id=" . $getKonten[$j]->gegenkonto . " ");
                $kontoname = $getKontoName[0]->konto;

                echo "<form method=post>";
                echo "<thead>";
                echo "<td><input type=submit name=umbuchen value='Umbuchen' /></td>";
                echo "<td></td>";
                echo "<td colspan=11>";
                echo $kontoname;
                echo "</td>";
                echo "</thead>";

                for ($i = 0; $i < sizeof($getKontoBuchungen); $i++) {

                    $timestamp = time();
                    $heute = date("Y-m-d", $timestamp);
                    if ($getKontoBuchungen[$i]->datum < $heute and isset($getKontoBuchungen[$i + 1]->datum) and $getKontoBuchungen[$i + 1]->datum >= $heute) {
                        $heute = date("d.m.Y", $timestamp);
                        echo "<tbody id='error'><td colspan='7'><a name='heute'>Heute ist der $heute</a> n&auml;chster Umsatz: " . $getKontoBuchungen[$i + 1]->umsatzName . "</td></tbody>";
                    }

                    echo "<tbody>";
                    echo "<td><input type=checkbox name=marked[$i] value='" . $getKontoBuchungen[$i]->buchungsnr . "'/></td>";
                    $gegenkonto = $this->sqlselect("SELECT * FROM finanzen_konten WHERE id = '" . $getKontoBuchungen[$i]->gegenkonto . "'");
                    echo "<td><a href='index.php?konto=" . $getKontoBuchungen[$i]->konto . "&jahr=" . $getKontoBuchungen[$i]->jahr . "&monat=" . $getKontoBuchungen[$i]->monat . "&selected=" . $getKontoBuchungen[$i]->buchungsnr . "'>" . $getKontoBuchungen[$i]->buchungsnr . "</a></td>
                        <td>" . $getKontoBuchungen[$i]->umsatzName . "</td>
                        <td>" . $getKontoBuchungen[$i]->umsatzWert . "</td>";
                    echo "<td>" . $getKontoBuchungen[$i]->datum . "</td>
                        <td>" . "<a href='?umbuchungBuchNr=" . $getKontoBuchungen[$i]->buchungsnr . "&editKonto=$id'>Umbuchen</a>" . "</td>";
                    echo "</tbody>";
                }

                echo "</form>";
            }

            if (!isset($getKontoBuchungen[0]->buchungsnr)) {
                echo "<tbody><td>Es gibt keine Buchungen auf diesem Konto.<a href='?deleteKonto=" . $kontoInfo[0]->id . "' class='rightRedLink'>Konto l&ouml;schen</a></td></tbody>";
            }

            echo "</table>";

            echo "</div>";
            echo "<textarea name=notizen>" . $kontoInfo[0]->notizen . "</textarea>";
            echo "</form>";
            echo "</div>";
        }
    }

    /**
     * Ändert das Konto einer Buchungsnummer
     *
     * @param int $buchungsnr Buchungsnummer
     * 
     * @return void
     */
    public function umbuchungDurchfuehren($buchungsnr)
    {
        if (isset($_POST['absenden'])) {
            if (isset($_POST['gutschriftKonto']) and isset($_POST['absenderKonto'])) {
                $absenderKonto = $_POST['absenderKonto'];
                $gutschriftKonto = $_POST['gutschriftKonto'];
                $getBuchungInfos = $this->sqlselect("SELECT * FROM finanzen_umsaetze WHERE buchungsnr = $buchungsnr");

                echo "Neues Absenderkonto $absenderKonto, neues Gutschriftkonto = $gutschriftKonto";

                for ($i = 0; $i < sizeof($getBuchungInfos); $i++) {
                    // Änderung f&uuml;r den ABSENDER
                    if ($getBuchungInfos[$i]->umsatzWert < 0) {
                        $update = "UPDATE finanzen_umsaetze SET konto=$absenderKonto, gegenkonto=$gutschriftKonto WHERE buchungsnr=$buchungsnr AND umsatzWert < 0";

                        if ($this->sqlInsertUpdateDelete($update) == true) {
                            echo "<p class='erfolg'>Umbuchung erfolgt</p>";
                        } else {
                            echo "<p class='meldung'>Es gab einen Fehler.</p>";
                        }
                    }

                    // ÄNDERUNG f&uuml;r die GUTSCHRIFT
                    if ($getBuchungInfos[$i]->umsatzWert > 0) {

                        $update = "UPDATE finanzen_umsaetze SET konto=$gutschriftKonto, gegenkonto=$absenderKonto WHERE buchungsnr=$buchungsnr AND umsatzWert > 0";

                        if ($this->sqlInsertUpdateDelete($update) == true) {
                            echo "<p class='erfolg'>Umbuchung erfolgt</p>";
                        } else {
                            echo "<p class='meldung'>Es gab einen Fehler.</p>";
                        }
                    }
                }
            }
        }
    }

    /**
     * Zeigt eine Auswahl zum Ändern deiner Buchungsnummer bezuegl.
     * der betreffenden Konten.
     *
     * @param int $besitzer UserID
     * 
     * @return void
     */
    public function UmbuchungUmsatz($besitzer)
    {
        if (isset($_GET['umbuchungBuchNr'])) {
            $buchungsnr = $_GET['umbuchungBuchNr'];

            // Pr&uuml;fen ob Nummer existiert:
            $query = "SELECT * FROM finanzen_umsaetze WHERE buchungsnr = '$buchungsnr' AND besitzer = $besitzer";
            if ($this->objectExists($query) == true) {
                $buchInfos = $this->sqlselect($query);

                echo "<div class='newFahrt'><form method=post>";

                $this->umbuchungDurchfuehren($buchungsnr);

                echo "<h2>Umsatz umbuchen</h2>";
                echo "<table class='kontoTable'>";
                for ($i = 0; $i < sizeof($buchInfos); $i++) {
                    if ($buchInfos[$i]->umsatzWert > 0) {

                        echo "<thead><td colspan=10>GUTSCHRIFT</td></thead>";
                        echo "<tbody><td>" . $buchInfos[$i]->umsatzName . " " . $buchInfos[$i]->umsatzWert . "</td></tbody>";
                        echo "<tbody>";
                        $selectKonten = "SELECT * FROM finanzen_konten WHERE besitzer = $besitzer ORDER BY konto";
                        $konten = $this->sqlselect($selectKonten);
                        echo "<td>";
                        echo "<select name=gutschriftKonto />";
                        for ($j = 0; $j < sizeof($konten); $j++) {
                            echo "<option value='" . $konten[$j]->id . "'";

                            if ($buchInfos[$i]->konto == $konten[$j]->id) {
                                echo " selected ";
                            }
                            echo ">";
                            echo $konten[$j]->konto;
                            echo "</option>";
                        }
                        echo "</select>";
                        echo "</td>";
                        echo "</tbody>";
                    }

                    if ($buchInfos[$i]->umsatzWert < 0) {
                        echo "<thead><td colspan=10>ABSENDER</td></thead>";
                        echo "<tbody><td>" . $buchInfos[$i]->umsatzName . " " . $buchInfos[$i]->umsatzWert . "</td></tbody>";

                        echo "<tbody>";
                        $selectKonten = "SELECT * FROM finanzen_konten WHERE besitzer = $besitzer ORDER BY konto";
                        $konten = $this->sqlselect($selectKonten);
                        echo "<td>";
                        echo "<select name=absenderKonto />";

                        for ($j = 0; $j < sizeof($konten); $j++) {
                            echo "<option value='" . $konten[$j]->id . "'";
                            if ($buchInfos[$i]->konto == $konten[$j]->id) {
                                echo " selected ";
                            }
                            echo ">";
                            echo $konten[$j]->konto;
                            echo "</option>";
                        }
                        echo "</select>";
                        echo "</td>";
                        echo "</tbody>";
                    }
                }
                echo "</table>";
                echo "<input type=submit name=absenden value=speichern />";

                echo "</form></div>";
            } else {
                echo "<p class='info'>Diese Buchung gibt es nicht, oder du hast keinen Zugriff auf diese Information.</p>";
            }
        }
    }

    /**
     * Ermoeglicht das loeschen von Konten.
     * Monatsabschluesse und Jahresabschluesse werden ebenfalls geloescht.
     * 
     * @param int $besitzer UserID
     * 
     * @return void
     */
    public function showDeleteKonto($besitzer)
    {
        if (isset($_GET['deleteKonto'])) {
            $konto = $_GET['deleteKonto'];
            $select = "SELECT id, besitzer FROM finanzen_konten WHERE besitzer=$besitzer AND id=$konto";
            $select2 = "SELECT id, besitzer, konto FROM finanzen_umsaetze WHERE besitzer=$besitzer AND konto=$konto";
            if ($this->objectExists($select) == true and $this->objectExists($select2) == false) {

                $delquery = "DELETE FROM finanzen_konten WHERE besitzer=$besitzer AND id=$konto LIMIT 1";
                $delAbschluesseQuery = "DELETE FROM finanzen_monatsabschluss WHERE konto=$konto";
                $delAbschluesseQuery2 = "DELETE FROM finanzen_jahresabschluss WHERE konto=$konto";
                $delShares = "DELETE FROM finanzen_shares WHERE konto_id=$konto";

                if ($this->sqlInsertUpdateDelete($delquery) == true) {
                    if ($this->sqlInsertUpdateDelete($delAbschluesseQuery) == true) {
                        $this->erfolgMessage("Monatsabschlüsse gelöscht");
                    }
                    if ($this->sqlInsertUpdateDelete($delAbschluesseQuery2) == true) {
                        $this->erfolgMessage("Jahresabschlüsse gelöscht");
                    }
                    if ($this->sqlInsertUpdateDelete($delShares) == true) {
                        $this->erfolgMessage("Shares gelöscht.");
                    }
                    $this->erfolgMessage("Konto wurde gel&ouml;scht.");
                } else {
                    echo "<p class='meldung'>Beim l&ouml;schen ist ein Fehler aufgetreten.</p>";
                }
            } else {
                $this->errorMessage("Konto darf nicht gel&ouml;scht werden, entweder das Konto existiert nicht, oder es sind noch Buchungen auf dem Konto vorhanden.");
            }
        }
    }

    /**
     * Zeigt eine Saldenübersicht an.
     * 
     * @param int $besitzer UserId
     * 
     * @return void
     */
    public function saldenUebersicht($besitzer)
    {
        if (isset($_GET['Salden'])) {
            echo "<div>";
            $select = "SELECT * FROM finanzen_konten WHERE besitzer=$besitzer";
            $konten = $this->sqlselect($select);

            for ($i = 0; $i < sizeof($konten); $i++) {
                $select2 = "SELECT sum(umsatzWert) as summe FROM finanzen_umsaetze WHERE konto=" . $konten[$i]->id;
                $umsaetze = $this->sqlselect($select2);
                echo "<div class='newChar'>";
                echo "<h2>" . $konten[$i]->konto . "</h2>";
                echo "Summe: " . $umsaetze[0]->summe;
                echo "</div>";
            }
            echo "</div>";
        }
    }

    /**
     * Gibt den Kontonamen einer ID zurueck.
     * 
     * @param int $kontoid KontoID
     * 
     * @return unknown|boolean
     */
    public function getKontoname($kontoid)
    {
        if (is_numeric($kontoid)) {
            $kontoinfo = $this->sqlselect("SELECT * FROM finanzen_konten WHERE id=$kontoid");

            if (isset($kontoinfo[0]->konto)) {
                return $kontoinfo[0]->konto;
            } else {
                return "n/a";
            }
        } else {
            return "n/a";
        }

    }

    /**
     * Navigation der Share Seite
     * 
     * @param int $besitzer UserID
     * 
     * @return void
     */
    public function sharenav($besitzer)
    {
        echo "<div class='innerBody'>";
        echo "<a class='greenLink' href='?newShare'>" . "Share erstellen" . "</a>";
        echo "</div>";
    }

    /**
     * Erstellt ein Konto Share
     * 
     * @param int $besitzer UserID
     * 
     * @return void
     */
    public function createKontoShare($besitzer)
    {
        if (isset($_GET['newShare'])) {

            //$j
            $getusers = $this->sqlselect("SELECT * FROM benutzer ORDER BY Name");
            //$i
            $getkonten = $this->sqlselect("SELECT * FROM finanzen_konten WHERE besitzer=$besitzer ORDER BY konto");

            echo "<div class='newFahrt'>";
            echo "<h2>Share erstellen</h2>";
            echo "<p class='hinweis'>Beachte: Der Nutzer bekommt nach Freigabe Leserechte auf das Konto. Shares k&ouml;nnen jederzeit wieder gel&ouml;scht werden.</p>";
            echo "<form method=post>";
            echo "<select name=konto>";
            echo "<option>Konto ausw&auml;hlen ...</option>";

            for ($i = 0; $i < sizeof($getkonten); $i++) {
                echo "<option value='" . $getkonten[$i]->id . "'>";
                echo $getkonten[$i]->konto;
                echo "</option>";
            }

            echo "</select>";
            echo "<select name=user>";
            echo "<option>Benutzer ausw&auml;hlen ...</option>";

            for ($j = 0; $j < sizeof($getusers); $j++) {
                if ($getusers[$j]->id != $besitzer) {
                    echo "<option value='" . $getusers[$j]->id . "'>";
                    echo $getusers[$j]->Name;
                    echo "</option>";
                }
            }

            echo "</select>";
            echo "<br><input type=submit name=absenden value=Absenden>";
            echo "</form>";

            //Speichern der Inhalte
            if (isset($_POST['absenden'])) {
                if (isset($_POST['konto']) and isset($_POST['user'])) {
                    $konto = $_POST['konto'];
                    $user = $_POST['user'];

                    if ($konto > 0 and $user > 0) {
                        if ($this->sqlInsertUpdateDelete("INSERT INTO finanzen_shares (besitzer, konto_id, target_user) VALUES ($besitzer, $konto, $user) ")) {
                            echo "<p class='erfolg'>Share wurde gespeichert.</p>";
                        } else {
                            echo "<p class='meldung'>Share konnte nicht gespeichert werden, m&ouml;glicherweise existiert der Share bereits!</p>";
                        }
                    }
                }
            }
            echo "</div>";
        }
    }

    /**
     * Loescht ein KontoShare.
     * 
     * @param int $besitzer UserID
     * 
     * @return void
     */
    public function deleteKontoShare($besitzer)
    {
        if (isset($_GET['del']) and isset($_GET['user']) and isset($_GET['konto'])) {
            $user = $_GET['user'];
            $konto = $_GET['konto'];
            if ($this->sqlInsertUpdateDelete("DELETE FROM finanzen_shares WHERE besitzer=$besitzer AND konto_id=$konto AND target_user=$user")) {
                echo "<p class='erfolg'>Share entfernt!</p>";
            } else {
                echo "<p class='meldung'>Share kann nicht entfernt werden, m&ouml;glicherweise existiert der Share nicht mehr.</p>";
            }
        }
    }

    /**
     * Zeigt alle Shares des Benutzers an.
     * 
     * @param int $besitzer UserID
     * 
     * @return void
     */
    public function showAllShares($besitzer)
    {

        $shares = $this->sqlselect("SELECT * FROM finanzen_shares WHERE besitzer=$besitzer");
        $sharesAmount = $this->getAmount("SELECT * FROM finanzen_shares WHERE besitzer=$besitzer") + 0;

        echo "<table class='kontoTable'>";
        echo "<thead><td>Konto</td><td>Freigegeben f&uuml;r</td><td></td></thead>";

        //Wenn kein Share vorhanden ist:
        if ($sharesAmount == 0) {
            echo "<tbody><td colspan=3>Du hast keine Shares erstellt, zum erstellen eines Shares, klicke auf <strong><a href='?newShare'>Share erstellen</a></strong></td></tbody>";
            echo "<tbody><td colspan=3></td></tbody>";
        }

        for ($i = 0; $i < sizeof($shares); $i++) {
            $id = $shares[$i]->target_user;
            $username = $this->sqlselect("SELECT id, Name FROM benutzer WHERE id=$id LIMIT 1");
            $kontoid = $shares[$i]->konto_id;
            $kontoname = $this->sqlselect("SELECT id, konto FROM finanzen_konten WHERE id=$kontoid LIMIT 1");
            echo "<tbody>";
            echo "<td>" . $kontoname[0]->konto . "</td>";
            echo "<td>" . $username[0]->Name . "</td>";
            echo "<td>" . "<a class='rightRedLink' href='?del&konto=" . $kontoid . "&user=" . $id . "'>X</a>" . "</td>";
            echo "</tbody>";
        }
        echo "</table>";
    }

    /**
     * Summiert die Ums&auml;tze der vergangenen Monate
     *
     * @param int $besitzer      UserID
     * @param int $jetzigerMonat CurrentMonat
     * @param int $jahr          jetziges Jahr
     * @param int $konto         KontoID
     * 
     * @return number
     */
    public function getMonatsabschluesseBISJETZT($besitzer, $jetzigerMonat, $jahr, $konto)
    {
        $summierendeMonate = $jetzigerMonat - 1;
        $summe = 0;
        for ($i = 1; $i <= $summierendeMonate; $i++) {
            $summe = $summe + $this->getMonatsabschluss($besitzer, $konto, $i, $jahr);
        }

        return $summe;
    }

    /**
     * Zeigt Jahresabschlüsse an.
     *
     * @return void
     */
    public function showJahresabschlussIfAvailable()
    {
        if (isset($_GET['konto']) and isset($_GET['jahr'])) {

            $konto = $this->getKontoIDFromGet();
            $jahr = $this->getJahrFromGet();
            $besitzer = $this->GetUserID($_SESSION['username']);
            $query = "SELECT besitzer, jahr, wert, konto FROM finanzen_jahresabschluss WHERE besitzer=$besitzer AND $konto=$konto AND jahr=$jahr";
            $jahresabschlussFuerDiesesJahr = $this->sqlselect($query);

            if (isset($jahresabschlussFuerDiesesJahr[0]->wert) and $konto > 0) {
                echo "<div class='dezentInfo'>";
                echo "<p>" . "F&uuml;r dieses Jahr gibt es einen Jahresabschluss, der Endwert betr&auml;gt: " . $jahresabschlussFuerDiesesJahr[0]->wert . " €" . "</p>";
                echo "</div>";
            }

        }
    }
    /**
     * Gibt die Summe der Jahresabschl&uuml;sse wieder.
     *
     * @param int $konto        KontoID
     * @param int $jetzigesJahr jetziges Jahr
     * 
     * @return number
     */
    public function getJahresabschluesseBISJETZT($konto, $jetzigesJahr)
    {
        $query = "SELECT sum(wert) as summe
        FROM finanzen_jahresabschluss
        WHERE konto = $konto
        AND jahr < $jetzigesJahr";
        $summe = $this->sqlselect($query);

        if (isset($summe[0]->summe)) {
            $summeWert = $summe[0]->summe;
        } else {
            $summeWert = 0;
        }

        return $summeWert;
    }

    /**
     * Gibt den Monatsabschluss aus den angegebenen Daten wieder.
     *
     * @param int $besitzer UserID
     * @param int $konto    KontoID
     * @param int $monat    Monat
     * @param int $jahr     Jahr
     * 
     * @return void
     */
    public function getMonatsabschluss($besitzer, $konto, $monat, $jahr)
    {
        $query = "SELECT *
        FROM finanzen_monatsabschluss
        WHERE konto = '$konto'
        AND besitzer = '$besitzer'
        AND year = '$jahr'
        AND monat = '$monat'";

        $monatsabschluss = $this->sqlselect($query);

        if (isset($monatsabschluss[0]->year)) {
            return $monatsabschluss[0]->wert;
        } else {
            return false;
        }
    }

    /**
     * Gibt den Saldo des Monats im aktuellen Jahr, dieses Besitzers, zu diesem Konto zurueck.
     *
     * @param int $besitzer UserID
     * @param int $konto    KontoID
     * @param int $monat    Monat
     * @param int $jahr     Jahr
     * 
     * @return void
     */
    public function getSaldoFromMonat($besitzer, $konto, $monat, $jahr)
    {
        $query = "SELECT *
        FROM finanzen_umsaetze
        WHERE konto = $konto
        AND besitzer = $besitzer
        HAVING year(datum) = $jahr
        AND month(datum) = $monat;";

        $umsaetze = $this->sqlselect($query);

        // SUMME bilden
        $summe = 0;
        for ($i = 0; $i < sizeof($umsaetze); $i++) {
            $summe = $summe + $umsaetze[$i]->umsatzWert;
        }

        return $summe;
    }

    /**
     * Erstellt Monatsabschluesse aus alten Einträgen.
     * 
     * @return void
     */
    public function erstelleMonatsabschluesseFromOldEintraegen()
    {
        $besitzer = $this->getUserID($_SESSION['username']);

        // jetzigen Monat ziehen:
        $currentMonat = date("m");
        $currentJahr = date("Y");
        $abzueglicheMonate = $currentMonat - 1;
        $anzahlMonate = 12;

        // Konten des Nutzers ziehen:
        $konten = $this->getAllKonten($besitzer);

        // Pr&uuml;fen, ob der Benutzer konten hat.
        if (!isset($konten[0]->id)) {
            echo "<p class='info'>Du hast noch keine Konten, 
            du brauchst mindestens zwei Konten. Klicke 
            <a href='?'>hier</a> um eine Standardkonfiguration zu erstellen</p>";
            exit();
        }

        // Anzahl Monatsabschl&uuml;sse z&auml;hlen
        $counter = 0;

        // Pro Konto durchlaufen
        for ($i = 0; $i < sizeof($konten); $i++) {
            // pr&uuml;fen, ob der aktuelle Monatsabschluss existiert.

            // Pro vergangenen Monat durchlaufen:
            for ($gepruefterMonat = 1; $gepruefterMonat < $currentMonat; $gepruefterMonat++) {
                if ($this->getMonatsabschluss($besitzer, $konten[$i]->id, $gepruefterMonat, $currentJahr) == false) {

                    $saldo = $this->getSaldoFromMonat($besitzer, $konten[$i]->id, $gepruefterMonat, $currentJahr);

                    $konto = $konten[$i]->id;
                    $query = "INSERT INTO finanzen_monatsabschluss (besitzer, monat, year, wert, konto)
                    VALUES
                    ('$besitzer','$gepruefterMonat','$currentJahr','$saldo','$konto')";

                    if ($this->sqlInsertUpdateDelete($query) == true) {
                        $counter = $counter + 1;
                    } else {
                        echo "<p class='meldung'>Es gab einen Fehler beim erstellen des Monatsabschlusses $currentJahr, im Monat $gepruefterMonat.</p>";
                    }
                }
            }
        }

        if ($counter > 0) {
            echo "<p class='erfolg'>Es wurden $counter Monatsabschl&uuml;sse erstellt.</p>";
        }
    }

    /**
     * Gibt zurueck, ob ein Jahresabschluss vorhanden ist, 
     * wenn ja, dann wird der wert zurueckgegeben.
     *
     * @param int $besitzer UserID
     * @param int $konto    KontoID
     * @param int $jahr     Jahr
     * 
     * @return boolean
     */
    public function getJahresabschluss($besitzer, $konto, $jahr)
    {
        $query = "SELECT * FROM finanzen_jahresabschluss WHERE konto = '$konto' AND besitzer = '$besitzer' AND jahr = '$jahr'";

        $jahresabschluss = $this->sqlselect($query);

        if (isset($jahresabschluss[0]->jahr)) {
            return $jahresabschluss[0]->wert;
        } else {
            return false;
        }
    }

    /**
     * Gibt den Saldo des Jahres, dieses Besitzers zu diesem Konto zur&uuml;ck.
     *
     * @param int $besitzer UserID
     * @param int $konto    KontoID
     * @param int $jahr     Jahr
     * 
     * @return boolean
     */
    public function getSaldoFromYear($besitzer, $konto, $jahr)
    {
        $query = "SELECT *
        FROM finanzen_umsaetze
        WHERE konto = $konto
        AND besitzer = $besitzer
        HAVING year(datum) = $jahr;";

        $umsaetze = $this->sqlselect($query);

        // SUMME bilden
        $summe = 0;
        for ($i = 0; $i < sizeof($umsaetze); $i++) {
            $summe = $summe + $umsaetze[$i]->umsatzWert;
        }

        return $summe;
    }

    /**
     * Gibt das Erstellungsjahr des aktuellen Kontos zur&uuml;ck.
     *
     * @param int $besitzer    UserID
     * @param int $kontonummer KontoID
     * 
     * @return boolean
     */
    public function getErstellungsdatumKonto($besitzer, $kontonummer)
    {
        // fr&uuml;hesten Eintrag im Konto finden:
        $frueherEintrag = $this->sqlselect(
            "SELECT min(year(datum)) 
            as min 
            FROM finanzen_umsaetze
            WHERE besitzer = '$besitzer' 
            AND konto = '$kontonummer'"
        );

        if (isset($frueherEintrag[0]->min)) {
            if ($frueherEintrag[0]->min < 2010) {
                return 2010;
            } else {
                return $frueherEintrag[0]->min;
            }
        } else {
            $frueherEintrag = date("Y");
            return $frueherEintrag;
        }
    }

    /**
     * Überprueft alle Jahresabschluesse auf Richtigkeit und loescht diese, fall noetig.
     * Eine andere Methode erstellt die fehlenden Abschluesse direkt wieder.
     * 
     * @return void
     */
    public function checkJahresabschluss()
    {
        if (isset($_GET['konto'])) {
            if ($_GET['konto'] > 0) {
                $besitzer = $this->getUserID($_SESSION['username']);

                // Konten des Nutzers ziehen:
                $kontenDesNutzers = $this->getAllKonten($besitzer);

                for ($i = 0; $i < sizeof($kontenDesNutzers); $i++) {

                    $konto = $kontenDesNutzers[$i]->id;
                    $kontoname = $kontenDesNutzers[$i]->konto;

                    $currentYear = date("Y");
                    // Pr&uuml;fen, ob Jahresabschluss KORREKT ist.
                    $jahresabschlusswert = $this->sqlselect("SELECT sum(wert) as summe FROM finanzen_jahresabschluss WHERE besitzer=$besitzer AND konto=$konto");

                    $tatsaechlicheSumme = $this->sqlselect("SELECT sum(umsatzWert) as summe FROM finanzen_umsaetze WHERE besitzer = $besitzer AND konto = $konto AND year(datum)<$currentYear");

                    if (isset($jahresabschlusswert[0]->summe)) {
                        if ($jahresabschlusswert[0]->summe != $tatsaechlicheSumme[0]->summe) {
                            $differenz = $jahresabschlusswert[0]->summe - $tatsaechlicheSumme[0]->summe;
                            echo "<p class='dezentInfo'>Achtung: Konto $kontoname (Nr. $konto), Jahresabschluss korrigiert, Differenz: $differenz. ";

                            if ($this->sqlInsertUpdateDelete("DELETE FROM finanzen_jahresabschluss WHERE besitzer=$besitzer AND konto=$konto") == true) {
                                echo "<br>Abschluss gel&ouml;scht.</p>";
                            } else {
                                echo "<br>falscher Abschluss kann nicht gel&ouml;scht werden.</p>";
                            }
                        } else {
                            // echo "<p class='dezentInfo'>F&uuml;r Konto $konto gibt es keine Fehler.</p>";
                        }
                    } else {
                        // echo "<p class='erfolg'>F&uuml;r Konto $konto gibt es scheinbar keinen Abschluss.</p>";
                    }
                }
            }
        }

    }

    /**
     * Erstellt Jahresabschl&uuml;sse aus den alten Eintr&auml;gen.
     * 
     * @return void
     */
    public function erstelleJahresabschluesseFromOldEintraegen()
    {
        $besitzer = $this->getUserID($_SESSION['username']);

        // Konten des Nutzers ziehen:
        $kontenDesNutzers = $this->getAllKonten($besitzer);

        // Pr&uuml;fen, ob der Benutzer konten hat.
        if (!isset($kontenDesNutzers[0]->id)) {
            echo "<p class='info'>Du hast noch keine Konten, du brauchst mindestens zwei Konten. Klicke <a href='?createStandardConfig'>hier</a> um eine Standardkonfiguration zu erstellen</p>";

            if (isset($_GET['createStandardConfig'])) {
                if ($this->userHasRight(18, 0) == true) {
                    if ($this->objectExists("SELECT * FROM finanzen_konten WHERE besitzer = $besitzer AND konto = 'Hauptkonto'") == false) {
                        if ($this->sqlInsertUpdateDelete("INSERT INTO finanzen_konten (konto, besitzer, aktiv) VALUES ('Hauptkonto','$besitzer', 1)") == true) {
                            echo "<p class='erfolg'>Ein Hauptkonto wurde erstellt.</p>";
                        }
                    }

                    if ($this->objectExists("SELECT * FROM finanzen_konten WHERE besitzer = $besitzer AND konto = 'Ausgaben'") == false) {
                        if ($this->sqlInsertUpdateDelete("INSERT INTO finanzen_konten (konto, besitzer, aktiv) VALUES ('Ausgaben','$besitzer', 0)") == true) {
                            echo "<p class='erfolg'>Ein Ausgabenkonto wurde erstellt.</p>";
                        }
                    }
                }
            }

            exit();
        }

        // Jetziges Jahr:
        $currentYear = date("Y");

        // Pro Konto durchf&uuml;hren:
        for ($i = 0; $i < sizeof($kontenDesNutzers); $i++) {

            echo "<table class='flatnetTable'>";
            // Ersten Eintrag im Konto suchen:
            $erstellungsDatumKonto = $this->getErstellungsdatumKonto($besitzer, $kontenDesNutzers[$i]->id);

            if ($erstellungsDatumKonto < $currentYear) {
                $differnz = $currentYear - $erstellungsDatumKonto;
                $geprueftesJahr = $erstellungsDatumKonto;

                // F&uuml;r Anzahl der Jahre:
                for ($j = 0; $j < $differnz; $j++) {
                    if ($this->getJahresabschluss($besitzer, $kontenDesNutzers[$i]->id, $geprueftesJahr) == false) {
                        $saldo = $this->getSaldoFromYear($besitzer, $kontenDesNutzers[$i]->id, $geprueftesJahr);
                        echo "<tbody><td>Jahresabschluss f&uuml;r <strong>" . $kontenDesNutzers[$i]->konto . "(Jahr: $geprueftesJahr, Saldo: $saldo)</strong> wird erstellt ... </td></tbody>";

                        $konto = $kontenDesNutzers[$i]->id;
                        $query = "INSERT INTO finanzen_jahresabschluss (besitzer, jahr, wert, konto) VALUES ('$besitzer','$geprueftesJahr','$saldo','$konto')";

                        if ($this->sqlInsertUpdateDelete($query) == true) {

                            if ($this->sqlInsertUpdateDelete("DELETE FROM finanzen_monatsabschluss WHERE besitzer = $besitzer AND year < $currentYear") == true) {
                                echo "<tbody><td>";
                                echo "<p class='info'>Monatsabschl&uuml;sse gel&ouml;scht</p>";
                                echo "</td></tbody>";
                            } else {
                                // echo "<p class='meldung'>Monatsabschl&uuml;sse konnten nicht gel&ouml;scht werden.</p>";
                            }
                        } else {
                            echo "<tbody><td>";
                            echo "<p class='meldung'>Es gab einen Fehler.</p>";

                        }

                        $geprueftesJahr = $geprueftesJahr + 1;
                    }
                }
            }
            echo "</table>";
        }
    }
}
