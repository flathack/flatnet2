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
 * Forum
 * Funktionen für das Forum
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
class Forum extends Functions
{

    /**
     * Zeigt das gesamte Forum an.
     * 
     * @return void
     */
    public function showBlogCategories()
    {

        if (!isset($_GET['publicArea'])) {

            //get info
            $selectCategories = "SELECT * FROM blogkategorien ORDER BY sortierung, kategorie ASC";
            $userID = $this->getUserID($_SESSION['username']);
            $row = $this->getObjektInfo($selectCategories);

            echo "<table class='forum'>";

            echo "<thead><td>Steven.NET Forum</td><td>Themen</td></thead>";

            for ($i = 0; $i < sizeof($row); $i++) {

                //Prüfen ob der Benutzer die Rechte hat, das aktuelle Forum zu betrachten.
                $benutzerliste = "SELECT id, Name, forumRights FROM benutzer WHERE id = '$userID' LIMIT 1";

                $row2 = $this->getObjektInfo($benutzerliste);

                for ($j = 0; $j < sizeof($row2); $j++) {
                    //Check
                    if ($this->check($row[$i]->rightWert, $row2[$j]->forumRights) == true) {
                        $kategorie = $row[$i]->id;
                        $query = "SELECT count(*) as anzahl FROM blogtexte WHERE kategorie = $kategorie";
                        $anzahlPosts = $this->getObjektInfo($query);
                        $anzahlPosts = $anzahlPosts[0]->anzahl;
                        //Wenn der Check bestanden ist, dann anzeigen.
                        echo "<tbody><td><a href='?blogcategory=" . $row[$i]->id . "'><strong>" . $row[$i]->kategorie . "</strong></a>
                        <br>" . $row[$i]->beschreibung . "</td><td>$anzahlPosts</td></tbody>";
                    }
                }

            }

            if (!isset($row[0])) {
                echo "<tbody><td><strong>Es gibt leider noch kein Forum</strong>
                        <br>Der Administrator muss das Forum zunächst einrichten. Als Administrator klicke <a href='/flatnet2/admin/control.php?action=5'>HIER</a></td><td>x</td></tbody>";
            }

            echo "<tbody><td><a href='?publicArea'><strong>Public Area</strong></a>
                        <br>Shows Public Information</td><td></td></tbody>";

            echo "</table>";
        }
    }

    /**
     * Zeigt Blog Posts an
     * 
     * @return void
     */
    public function showBlogPosts()
    {
        if (isset($_GET['blogcategory'])) {

            $kategorie = $_GET['blogcategory'];

            //Check ob der Benutzer das aktuelle Forum anzeigen darf.
            $selectCategories = "SELECT * FROM blogkategorien WHERE id = '$kategorie'";
            $row = $this->getObjektInfo($selectCategories);

            for ($i = 0; $i < sizeof($row); $i++) {
                //Prüfen ob der Benutzer die Rechte hat, das aktuelle Forum zu betrachten.
                $userID = $this->getUserID($_SESSION['username']);
                $benutzerliste = "SELECT id, Name, forumRights FROM benutzer WHERE id = '$userID' LIMIT 1";
                $row2 = $this->getObjektInfo($benutzerliste);
                for ($j = 0; $j < sizeof($row2); $j++) {
                    //Check
                    if ($row2[$j]->forumRights == 0) {
                        $benutzerrechteVorher = 1;
                    } else {
                        $benutzerrechteVorher = $row2[$j]->forumRights;
                    }
                    if ($this->check($row[$i]->rightWert, $benutzerrechteVorher) == false) {
                        echo "<p class='meldung'>Du darfst dir dieses Forum nicht ansehen.</p>";
                        exit;
                    }
                }
            }

            //Fortfahren mit Thread Anzeige.

            $selectBlogEintraege = "SELECT *
            , year(timestamp) AS jahr
            , month(timestamp) as monat
            , day(timestamp) as tag
            , hour(timestamp) AS stunde
            , minute(timestamp) AS minute
            FROM blogtexte
            WHERE kategorie = '$kategorie'
            ORDER BY timestamp DESC";

            $row = $this->getObjektInfo($selectBlogEintraege);

            echo "<a href='?' class='highlightedLink'>Zurück</a> ";
            if (isset($_GET['blogcategory'])) {
                $category = $_GET['blogcategory'];
            }
            echo "<a href='/flatnet2/blog/addBlogEntry.php?blogcategory=$category' class='greenLink'>Neues Thema</a>";

            echo "<table class='forum'>";
            echo "
                <thead>
                    <td id='titel'>Titel</td>
                    <td>Autor</td>
                    <td>Antworten</td>
                    <td id='datum'>Erstelldatum</td>
                    <td></td>

                </thead>";
            for ($i = 0; $i < sizeof($row); $i++) {

                $lockedInfo = $this->getObjektInfo("SELECT * FROM blogtexte WHERE id = '" . $row[$i]->id . "'");
                if (!isset($lockedInfo[0]->locked)) {
                    $locked = "";
                } else if ($lockedInfo[0]->locked == 1) {
                    $locked = "&#128274;";
                } else {
                    $locked = "";
                }

                //Benutzer finden:
                $autor = $this->getUserName($row[$i]->autor);

                if ($row[$i]->status == 1 or $_SESSION['username'] == $autor or $this->userHasRight("10", 0) == true) {

                    //Anzahl der Kommentare herausfinden
                    $countList = "SELECT COUNT(blogid) as anzahl FROM blog_kommentare WHERE blogid = '" . $row[$i]->id . "'";
                    $menge = $this->getObjektInfo($countList);
                    $menge = $menge[0]->anzahl;

                    if ($row[$i]->status == 4) {
                        $publicLink = "<a class='rightGreenLink' href='public.php?topicID=" . $row[$i]->id . "'>Public Link</a>";
                    } else {
                        $publicLink = "";
                    }

                    echo "<tbody>
                    <td><a href='/flatnet2/blog/blogentry.php?showblogid=" . $row[$i]->id . "&ursprungKategorie=" . $row[$i]->kategorie . "'>" . $row[$i]->titel . "</a> $publicLink</td>
                    <td><a href='/flatnet2/usermanager/usermanager.php'>$autor</a></td>
                    <td> $menge </td>";
                    echo "<td>" . $row[$i]->tag . "." . $row[$i]->monat . "." . $row[$i]->jahr . " " . $row[$i]->stunde . ":" . $row[$i]->minute . " Uhr" . "</td>";
                    if ($row[$i]->status == 1) {
                        $offen = "&#10004;";
                    } else { 
                        $offen = "&#10008;";
                    }
                    if ($row[$i]->status == 4) {
                        $status = "&#937;";
                    } else { 
                        $status = "";
                    }

                    echo "<td>$status $locked $offen</td>";
                    echo "</tbody>";
                }
            }
            echo "</table>";
        }
    }

    /**
     * Zeigt den öffentlichen Bereich an
     * 
     * @return void
     */
    public function showPublicArea()
    {
        if (isset($_GET['publicArea'])) {
            echo "<a href='?'>Zurück</a>";
            $query = "SELECT * FROM blogtexte WHERE status=4";

            $countList = "SELECT count(*) as anzahl FROM blogtexte WHERE status=4";
            $menge = $this->getObjektInfo($countList);
            $menge = $menge[0]->anzahl;

            $beitraege = $this->getObjektInfo($query);

            echo "<table class='forum'>";
            echo "<thead><td>Titel</td></thead>";
            for ($i = 0; $i < sizeof($beitraege); $i++) {
                echo "<tbody>
                    <td><a href='/flatnet2/blog/blogentry.php?showblogid=" . $beitraege[$i]->id . "&ursprungKategorie=" . $beitraege[$i]->kategorie . "'>" . $beitraege[$i]->titel . "</a></td>
                    <td><a href='/flatnet2/usermanager/usermanager.php'>$autor</a></td>
                    <td> $menge </td>";
                echo "<td>" . $beitraege[$i]->tag . "." . $beitraege[$i]->monat . "." . $beitraege[$i]->jahr . " " . $beitraege[$i]->stunde . ":" . $beitraege[$i]->minute . " Uhr" . "</td>";
                if ($beitraege[$i]->status == 1) {
                    $offen = "&#10004;";
                } else {
                    $offen = "&#10008;";
                }

                if ($beitraege[$i]->status == 4) {
                    $status = "&#937;";
                } else {
                    $status = "";
                }
                echo "<td>$status $locked $offen</td>";
                echo "</tbody>";
            }
            echo "</table>";
        }
    }

}
