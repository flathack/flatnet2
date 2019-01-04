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
 * Learner
 * Funktionen für das Lern-Programm
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
class Learner extends Functions
{
    /**
     * Header für den EventManager
     * 
     * @return void
     */
    function newheader() 
    {
        echo '<meta http-equiv="content-type" content="text/html; charset=utf-8" />';
        echo '<link rel="apple-touch-icon" sizes="57x57" href="/apple-icon-57x57.png">';
        echo '<link rel="apple-touch-icon" sizes="60x60" href="/apple-icon-60x60.png">';
        echo '<link rel="apple-touch-icon" sizes="72x72" href="/apple-icon-72x72.png">';
        echo '<link rel="apple-touch-icon" sizes="76x76" href="/apple-icon-76x76.png">';
        echo '<script src="/flatnet2/tools/ckeditor/ckeditor.js"></script>';
        echo '<link href="/flatnet2/css/style.css" type="text/css" rel="stylesheet" />';
        echo "<meta name='viewport' content='width=390, initial-scale=1'>";

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
            echo "<p><strong><a href='/flatnet2/usermanager/usermanager.php'>" . $_SESSION['username'] . "</a></strong> | ";
            echo "<a href='/flatnet2/includes/logout.php'> Abmelden </a></p>";
            echo "<ul>";
                
            if ($this->userHasRight(36, 0) == true) {
                echo "<li><a href='/flatnet2/uebersicht.php'>&Uuml;bersicht </a></li>";
            } else {
                echo "<li><a href='/flatnet2/informationen/impressum.php'>Impressum</a></li>";
            }
            echo "</ul>";
        } else {
            echo "<p></p>";
        }
        
        echo "</div>";

        // Überschrift
        echo "<div id='ueberschrift'>";
            echo "<h1><a href='/flatnet2/learner/index.php'>Vokabeltrainer</a></h1>";
        echo "</div>";

        if (isset($_SESSION['username'])) {
            // $this->showNaviLinks();
        }
        

        // Ende Header
        echo "</header>";
    }
    /**
     * Zeigt die Willkommensseite an.
     * 
     * @return void
     */
    function learnerWelcome() 
    {
        echo "<div class='VokUebung'>";
        $this->setgetLang();
        $this->setgetkat();
        $this->uebungsSelector();

        $getsprachen = $this->getObjektInfo("SELECT * FROM vokabeln_sprachauswahl");

        echo "<ul class='finanzNAV'>";
        for ($i = 0; $i < sizeof($getsprachen); $i++) {
            
            echo "<li"; 
            if (isset($_SESSION['language'])) {
                if ($_SESSION['language'] == $getsprachen[$i]->id) {
                    echo " id=selected ";
                }
            }
            echo">";
                echo "<a href='?setLang=".$getsprachen[$i]->id."'>" . $getsprachen[$i]->sprach_name . "</a>";
            echo "</li>";
        }

        echo "</ul>";
        $this->showLang();
        echo "</div>";
    }

    function setgetLang() 
    {

        if (isset($_GET['setLang'])) {
            if (is_numeric($_GET['setLang']) == true) {
                $_SESSION['language'] = $_GET['setLang'];

                # Vokabel-Kategorie loeschen:
                unset($_SESSION['vokabelkat']);
            }
        }
    }

    function setgetKat() 
    {

        if (isset($_GET['setKat'])) {
            if (is_numeric($_GET['setKat']) == true) {
                $_SESSION['vokabelkat'] = $_GET['setKat'];
            }
        }
    }

    function uebungsSelector() 
    {
        if (!isset($_SESSION['LangDiff'])) {
            $_SESSION['LangDiff'] = 0;
        }
        if (isset($_GET['setLangDiff'])) {
            $_SESSION['LangDiff'] = $_GET['setLangDiff'];
        }
        echo "<div class='spacer'><ul class='finanzNAV'>";

        echo "<li"; 
        if ($_SESSION['LangDiff'] == 0) {
            echo " id='selected' ";
        }
        echo "><a href='?setLangDiff=0'>Deutsch - Fremdsprache</a></li>";
        echo "<li"; 
        if ($_SESSION['LangDiff'] == 1) {
            echo " id='selected' ";
        }
        echo"><a href='?setLangDiff=1'>Fremdsprache - Deutsch</a></li>";
        echo "</ul></div>";
    }

    function showLang() 
    {

        if (isset($_SESSION['language'])) {

            # Kategorien anzeigen:
            $sprachid = $_SESSION['language'];
            $kategorien = $this->getObjektInfo("SELECT * FROM vokabelkategorien WHERE sprach_id=$sprachid");

            echo "<div class='spacer'><ul class='finanzNAV'>";
            for ($i = 0; $i < sizeof($kategorien); $i++) {

                echo "<li"; 
                if (isset($_SESSION['vokabelkat'])) {
                    if ($_SESSION['vokabelkat'] == $kategorien[$i]->id) {
                        echo " id='selected' ";
                    }
                }
                echo "><a href='?setKat=".$kategorien[$i]->id."'>" .$kategorien[$i]->kat_name. "</a></li>";
            }
            echo "</ul></div>";

            # Vokabeln anzeigen:

            if (isset($_SESSION['vokabelkat'])) {
                $kategorie = $_SESSION['vokabelkat'];
                $userid = $this->getUserID($_SESSION['username']);
                $vokabeln = $this->getObjektInfo("SELECT * FROM vokabelliste WHERE vok_kat=$kategorie ORDER BY rand() LIMIT 1");

                for ($j = 0; $j < sizeof($vokabeln); $j++) {
                    $vokid = $vokabeln[$j]->id;
                    $fortschritt = $this->getObjektInfo("SELECT * FROM vokabelnfortschritt WHERE user_id=$userid AND vokabel_id=$vokid");
                    if (isset($fortschritt[0]->positiv)) {
                        $positiv = $fortschritt[0]->positiv;
                    } else {
                        $positiv = 0;
                    }
                    if (isset($fortschritt[0]->negativ)) {
                        $negativ = $fortschritt[0]->negativ;
                    } else {
                        $negativ = 0;
                    }
                    if ($_SESSION['LangDiff'] == 1) {
                        echo "<div class='vokabel'>"; 
                        echo "<p>" . $vokabeln[$j]->vok_name_ori . " ($positiv/$negativ)</p>";
                        echo '<p id="versteckt" style="display:none;">' . $vokabeln[$j]->vok_name_ueb. '</p>';
                        echo "</div>";
                        
                    }
                    if ($_SESSION['LangDiff'] == 0) {
                        echo "<div class='vokabel'>"; 
                        echo "<div>" . $vokabeln[$j]->vok_name_ueb . " ($positiv/$negativ)</p>";
                        echo '<p id="versteckt" style="display:none;">' . $vokabeln[$j]->vok_name_ori. '</p>';
                        echo "</div>";
                        echo "</div>";
                    }
                }

                echo "<div class='spacer'>";
                    $this->setPositiv();
                    $this->setNegativ();
                    if (isset($vokid)) {
                        echo "<button onclick=\"document.getElementById('versteckt').style.display = 'block'\">L&ouml;sung</button>";
                        echo "<a class='greenLink' href='?weiterPositiv&vokid=".$vokid."'>Postiv</a>";
                        echo "<a class='redLink' href='?weiterNegativ&vokid=".$vokid."'>Negativ</a>";
                    } else {
                        echo "<p class='hinweis'>In dieser Lektion sind keine Vokabeln vorhanden.</p>";
                    }
                    
                echo "</div>";

            }
            
        } else {
            echo "Keine Sprache ausgewaehlt.";
        }
    }

    function setPositiv() 
    {
        if (isset($_GET['weiterPositiv']) AND isset($_GET['vokid'])) {
            $vokid = $_GET['vokid'];
            $userid = $this->getUserID($_SESSION['username']);

            $wertung  = $this->getObjektInfo("SELECT * FROM vokabelnfortschritt WHERE user_id=$userid AND vokabel_id=$vokid LIMIT 1");

            if (isset($wertung[0]->positiv)) {
                $newvalue = $wertung[0]->positiv + 1;
                $this->sqlInsertUpdateDelete("UPDATE vokabelnfortschritt SET positiv=$newvalue WHERE user_id=$userid AND vokabel_id=$vokid LIMIT 1");
                // echo "<p class='erfolg'>New Value: $newvalue</p>";
            } else {
                // echo "<p class='erfolg'>Wertung noch nicht vorhanden</p>";
                $this->sqlInsertUpdateDelete("INSERT INTO vokabelnfortschritt (user_id, vokabel_id, positiv, negativ) VALUES ('$userid','$vokid','1','0')");
            }
        }
    }

    function setNegativ() 
    {
        if (isset($_GET['weiterNegativ']) AND isset($_GET['vokid'])) {
            $vokid = $_GET['vokid'];
            $userid = $this->getUserID($_SESSION['username']);

            $wertung  = $this->getObjektInfo("SELECT * FROM vokabelnfortschritt WHERE user_id=$userid AND vokabel_id=$vokid LIMIT 1");

            if (isset($wertung[0]->negativ)) {
                $newvalue = $wertung[0]->negativ + 1;
                $this->sqlInsertUpdateDelete("UPDATE vokabelnfortschritt SET negativ=$newvalue WHERE user_id=$userid AND vokabel_id=$vokid LIMIT 1");
                // echo "<p class='erfolg'>New Value: $newvalue</p>";
            } else {
                // echo "<p class='erfolg'>Wertung noch nicht vorhanden</p>";
                $this->sqlInsertUpdateDelete("INSERT INTO vokabelnfortschritt (user_id, vokabel_id, positiv, negativ) VALUES ('$userid','$vokid','0','1')");
            }
        }
    }
}
?>