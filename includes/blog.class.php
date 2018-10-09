<?php
/**
 * DOC COMMENT
 *
 * PHP Version 7
 *
 * @history Steven 20.08.2014 : angelegt.
 * @history Steven 24.08.2014 : Blog funktionalität zum Teil implementiert
 * @history Steven       2015 : Blog in Forum umprogrammiert.
 * @history Steven 06.09.2018 : Code Cleanup
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
 * Blog
 * Stellt die Blog Funktionalitäten zur Verfügung.
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
class Blog extends Functions
{

    /**
     * Zeigt die Forum Navigation an
     * 
     * @return void
     */
    public function showForumNav()
    {

        if (isset($_GET['showblogid'])) {
            $blogid = $_GET['showblogid'];

            $getBlogEintragInfos = $this->sqlselect("SELECT * FROM blogtexte WHERE id = '$blogid'");

            $category = $getBlogEintragInfos[0]->kategorie;

        } else {
            $category = "";
        }

        if (isset($_GET['ursprungKategorie'])) {
            $category = $_GET['ursprungKategorie'];
            echo "<a href=\"/flatnet2/forum/index.php?blogcategory=$category\" class=\"highlightedLink\">Zurück </a>";
            echo "<a href='/flatnet2/blog/addBlogEntry.php?blogcategory=$category' class='buttonlink'>Neues Thema</a>";

            if ($getBlogEintragInfos[0]->locked != 1) {
                echo "<a href='/flatnet2/blog/blogentry.php?showblogid=$blogid&newComment=$blogid#antwort' class='greenLink'>Antwort erstellen</a>";
            }
        } else {
            if ($category != "") {
                echo "<a href=\"/flatnet2/forum/index.php?blogcategory=$category\" class=\"highlightedLink\">Zurück </a>";
                echo "<a href='/flatnet2/blog/addBlogEntry.php?blogcategory=$category' class='buttonlink'>Neues Thema</a>";
                echo "<a href='/flatnet2/blog/blogentry.php?showblogid=$blogid&newComment=$blogid#antwort' class='greenLink'>Antwort erstellen</a>";
            } else {
                echo "<a href=\"/flatnet2/forum/index.php\" class=\"buttonlink\">Forum Startseite</a>";
            }

        }
        //AdminTools anzeigen
        $this->adminTools();
    }

    /**
     * Zeigt vorhandene Admintools an.
     * 
     * @return void
     */
    public function adminTools()
    {

        $this->lockthread();
        $this->unlockthread();

        //Check if Forum Admin
        if ($this->userHasRight("26", 0) == true) {
            if (isset($_GET['showblogid'])) {
                //CurrentBlogID Info
                $id = $_GET['showblogid'];
                $getbloginfo = $this->sqlselect("SELECT * FROM blogtexte WHERE id = '$id'");
                $kategorie = $getbloginfo[0]->kategorie;

                if (isset($getbloginfo[0]->kategorie)) {
                    if ($getbloginfo[0]->locked == 0) {
                        echo "<a class='rightRedLink' href='?showblogid=$id&ursprungKategorie=$kategorie&lockthread=$id'> &#128274; LOCK </a>";
                    } else {
                        echo "<a class='rightRedLink' name='thisThreadLocked'>Dieses Thema ist gesperrt</a>";
                        echo "<a class='rightGreenLink' href='?showblogid=$id&ursprungKategorie=$kategorie&unlockthread=$id'> &#128274; UNLOCK </a>";
                    }
                }
            }
        }
    }

    /**
     * Sperrt einen Thread im Forum
     * 
     * @return boolean
     */
    public function lockthread()
    {
        if ($this->userHasRight("27", 0) == true) {
            if (isset($_GET['lockthread'])) {
                $id = $_GET['lockthread'];
                if ($id > 0 and is_numeric($id) == true) {
                    $this->sqlInsertUpdateDelete("UPDATE blogtexte SET locked=1 WHERE id = '$id'");
                }

            }
        }
    }

    /**
     * Entsperrt einen Thread im Forum
     * 
     * @return boolean
     */
    public function unlockthread()
    {
        if ($this->userHasRight("27", 0) == true) {
            if (isset($_GET['unlockthread'])) {
                $id = $_GET['unlockthread'];
                if ($id > 0 and is_numeric($id) == true) {
                    $this->sqlInsertUpdateDelete("UPDATE blogtexte SET locked=0 WHERE id = '$id'");
                }
            }
        }
    }

    /**
     * Zeigt die Eingabefelder zum eingeben des Textes für einen neuen Blog.
     * 
     * @return void
     */
    public function newBlogEingabe()
    {
        if ($this->userHasRight("20", 0) == true) {
            echo "<div class='neuerBlog'>";
            echo '<form method="post">';
            $bloguser = $_SESSION['username'];

            //Gibt alle Kategorien aus.
            echo "<select name='blogkategorie' value='' size='1'>";
            $selectKat = "SELECT id, kategorie, rightWert FROM blogkategorien";
            $row = $this->sqlselect($selectKat);
            for ($i = 0; $i < sizeof($row); $i++) {

                //BenutzerRecht selektieren:
                //Check ob die Kategorie in der Auswahlliste auftauchen soll.
                $userID = $this->getUserID($_SESSION['username']);
                $selectBenutzerRecht = "SELECT id, forumRights FROM benutzer WHERE id = '$userID' LIMIT 1";
                $rowRecht = $this->sqlselect($selectBenutzerRecht);

                $aktuelleBenutzerrechte = $rowRecht[0]->forumRights;
                $kategorieRechte = $row[$i]->rightWert;

                if ($this->check($kategorieRechte, $aktuelleBenutzerrechte) == true) {
                    echo "<option";
                    //Checked die Kategorie, wenn der Link im Forum geklickt wurde.
                    if (isset($_GET['blogcategory'])) {
                        if ($_GET['blogcategory'] == $row[$i]->id) {
                            echo " selected ";
                        }
                    }

                    echo " value='" . $row[$i]->id . "'>";
                    echo $row[$i]->kategorie;
                    echo "</option>";
                }
            }
            echo "</select>";
            // Wenn Text bereits geschrieben wurde, 
            // das speichern aber nicht geklappt hat
            if (isset($_POST['blogtext'])) {
                $postText = $_POST['blogtext'];
            } else { 
                $postText = "";
            }
            //Wenn Titel bereits geschrieben wurde:
            if (isset($_POST['blogtitel'])) {
                $postTitel = $_POST['blogtitel'];
            } else { 
                $postTitel = "";
            }
            //Status #
            echo "<input type='radio' name='status' value='0' id='statusNull' >" . "<label for='statusNull'>Gesperrt</label>";
            echo "<input type='radio' name='status' value='1' id='statusEins' checked >" . "<label for='statusEins'>Freigegeben</label>";
            echo "<input type='submit' name='sendnewblog' value='Absenden' />";
            echo "<div class='spacer'>";
            echo "<input type='text' name='blogtitel' id='titel' value='$postTitel' placeholder='Themen Titel' />";
            echo "</div>";
            echo "<div class='editForumEintrag'>";

            echo "<textarea class='ckeditor' name='blogtext'>$postText</textarea>";

            echo "</div>";
            echo "<div id='absenden'>";
            echo "<input type='submit' name='sendnewblog' value='Absenden' />";
            echo "</div>";
            echo "</form>";
            echo "</div>";
        }

    }

    /**
     * Ermöglicht das speichern des neuen Blogs.
     * 
     * @return void
     */
    public function newBlogFunction()
    {
        if ($this->userHasRight(20, 0) == true) {

            if (isset($_POST['blogtitel']) and isset($_POST['blogtext']) and isset($_POST['blogkategorie'])) {
                if ($_POST['blogtitel'] == "" or $_POST['blogtext'] == "" or $_POST['blogkategorie'] == "") {
                    echo "<p class='info'>Bitte Titel und Text ausfüllen</p>";
                } else {
                    $titel = $_POST['blogtitel'];

                    //ID des Benutzers auswählen:
                    $autor = $_SESSION['username'];
                    $selectUserID = "SELECT id, Name FROM benutzer WHERE Name = '$autor' LIMIT 1";
                    $row = $this->sqlselect($selectUserID);
                    
                    if (isset($row[0]->id)) {
                        $autor = $row[0]->id;
                    } else { 
                        $autor = 0;
                    }

                    $text = $_POST['blogtext'];
                    $kategorie = $_POST['blogkategorie'];

                    //ID der Kategorie:
                    $selectKategorieID = "SELECT * FROM blogkategorien WHERE id = $kategorie";
                    $row2 = $this->sqlselect($selectKategorieID);
                    if (isset($row2[0]->id)) {
                        $kategorie = $row2[0]->id;
                    } else {
                        $kategorie = 0;
                    }

                    $status = $_POST['status'];
                    $insertNewBlog = "INSERT INTO blogtexte (autor, titel, text, kategorie, status) VALUES ('$autor','$titel','$text','$kategorie','$status')";

                    if ($this->sqlInsertUpdateDelete($insertNewBlog) == true) {
                        echo '<p class="erfolg">Der Foreneintrag mit dem Titel <strong>' . $titel . ' </strong>wurde erstellt.';
                    } else {
                        echo '<p class="meldung">Das speichern ist fehlgeschlagen!
                                Kopiere den unteren Text in einen anderen Editor
                                um ihn zu speichern und versuche es später erneut.</p>';
                    }
                }
            }
        }
    }

    /**
     * Zeigt einen Blogeintrag an + Kommentare
     * 
     * @param int $id ID des Blogs
     * 
     * @return void
     */
    public function showBlogId($id)
    {

        if ($this->userHasRight(2, 0) == true) {

            //Check ob eine ID angegeben wurde.
            if ($id != "" and is_numeric($id) == true) {

                $currentUser = $this->getUserID($_SESSION['username']);

                $selectCheck = "SELECT id, autor, status, kategorie FROM blogtexte WHERE id=$id";
                $rowCheck = $this->sqlselect($selectCheck);

                //Check, ob User den Artikel sehen darf:
                if (!isset($rowCheck[0]->autor)
                    or $rowCheck[0]->autor != $currentUser
                    and $rowCheck[0]->status == '0'
                    and $this->userHasRight("10", 0) == false
                    or $this->userIsAllowedToSeeCategory($currentUser, $id) == false
                ) {

                    echo "<p class='meldung'>Du kannst diesen Beitrag nicht anzeigen</p>";

                } else {

                    $select = "SELECT id, timestamp, autor, titel, text, status, locked
                        , year(timestamp) AS jahr
                        , month(timestamp) as monat
                        , day(timestamp) as tag
                        , hour(timestamp) AS stunde
                        , minute(timestamp) AS minute
                        FROM blogtexte WHERE id=$id";

                    $row = $this->sqlselect($select);
                    for ($i = 0; $i < sizeof($row); $i++) {

                        if (isset($_GET['editComment'])) {
                            $this->editKommentar($_GET['editComment']);
                        }
                        if (isset($_GET['del'])) {
                            echo "<form action = '#geloescht' method=post>";
                            echo "<p class='meldung'>Sicher, dass dieser Kommentar gelöscht werden soll?<br>";
                            $blogid = $_GET['showblogid'];
                            echo "<input type=submit name=sure value=Ja /><a href='?showblogid=$blogid' class='greenLink'>Nein</a></p>";
                            echo "</form>";
                            if (isset($_POST['sure'])) {
                                $this->delKommentar($_GET['del'], $_GET['showblogid']);
                            }

                        }

                        echo "<table class='forumPost'>";

                        //Wenn der Benutzer einen Titel hat:
                        $autorTitel = $this->sqlselect("SELECT id, titel FROM benutzer WHERE id = '" . $row[$i]->autor . "'");
                        if (isset($autorTitel->titel) and $autorTitel->titel != "") {
                            $titel = "<p id='smallLink' class='rightRedLink'>" . $autorTitel->titel . "</p>";
                        } else {
                            $titel = "";
                        }

                        //KOPFZEILE
                        echo "<thead><td colspan = '3'>Titel des Beitrags: " . $row[$i]->titel . "</td></thead>";
                        echo "<thead id='small'>";
                        echo "<td><img id='id' src='../images/avatar_122.png' style='width:30px; height: 30px;' />
                                    <a id='id' class='rightBlueLink' href='#ThreadID" . $row[$i]->id . "' name='#ThreadID" . $row[$i]->id . "'>ID: " . $row[$i]->id . "</a>
                                    <p id='smallLink' class='rightBlueLink'>Autor: " . $this->getUserName($row[$i]->autor) . "</p>
                            $titel</td>

                            <td>Erstellt: " . $row[$i]->tag . "." . $row[$i]->monat . "." . $row[$i]->jahr . " | " . $row[$i]->stunde . ":" . $row[$i]->minute . " Uhr</td>";

                        //Buttons
                        echo "<td>";

                        //EDIT KNOPF
                        if ($row[$i]->locked != 1) {
                            if ($this->userHasRight("32", 0) == "true" or $_SESSION['username'] == $this->getUserName($row[$i]->autor)) {
                                echo "<a class='rightRedLink' href='?bearbid=" . $row[$i]->id . "' >edit</a>";
                            }
                        }

                        //ANTWORT ERSTELLEN KNOPF
                        if ($row[$i]->locked != 1) {
                            echo "<a class='rightBlueLink' href='?showblogid=" . $row[$i]->id . "&newComment=" . $row[$i]->id . "&quote=" . $row[$i]->id . "#antwort'> Zitat </a>";
                            echo "<a class='rightGreenLink' href='?showblogid=" . $row[$i]->id . "&newComment=" . $row[$i]->id . "#antwort'> Antwort erstellen </a>";
                        }

                        echo "</td>";

                        echo "</thead>";
                        //ENDE KOPFZEILE

                        //TEXT ANZEIGE:
                        echo "<tbody>";
                        echo "<td colspan='3'>" . $row[$i]->text . "</td>";
                        echo "</tbody>";
                        echo "</table>";

                        //KommentarFunktion aufrufen
                        echo "<table class='forumPost'>";
                        $this->kommentare($row[$i]->id);
                        echo "</table>";

                    } //FOR Ende
                } //Check, ob der User den Post sehen darf.
            } else { //ID != 0 Ende
                echo "<p class='meldung'>Du kannst diesen Beitrag nicht anzeigen</p>";
            }
        } //Recht ende
    } //Function Ende

    /**
     * Bearbeitet einen Blog mit der angewählten ID.
     * 
     * @param int $bearbid ID des Blogs zur Bearbeitung
     * 
     * @return void
     */
    public function bearbBlogId($bearbid)
    {
        if ($this->userHasRight(20, 0) == true) {
            $update = isset($_GET['update']) ? $_GET['update'] : '';
            if ($update != "") {
                $titel = $_POST['newtitel'];

                //EDTIERT VON XYZ AM XYZ DATE
                $editiertAm = date('d.m.Y h:i:s', time());
                $userDerGeaendertHat = $_SESSION['username'];

                //TEXT MODIFIZIEREN UND EDITIERT VON HINZUFÜGEN
                $text = $_POST['newtext'] . "<p><span style=\"font-size:10px; padding:3px; background-Color: #e0e0e0;\">Editiert am $editiertAm von $userDerGeaendertHat</span></p>";

                $status = $_POST['status'];

                if ($_POST['newblogkategorie'] == "") {
                    $sqlupdate = "UPDATE blogtexte SET titel='$titel', text='$text', status='$status' WHERE id='$bearbid'";
                } else {
                    $kategorie = $_POST['newblogkategorie'];
                    $sqlupdate = "UPDATE blogtexte SET titel='$titel', text='$text', kategorie='$kategorie', status='$status' WHERE id='$bearbid'";
                }

                //Durchführung des Updates

                if ($this->sqlInsertUpdateDelete($sqlupdate) == true) {
                    echo "<p class='erfolg'>Foreneintrag <strong>$titel</strong> wurde geändert.</p>";
                } else {
                    echo "<p class='meldung'>Es ist ein Fehler aufgetreten. Kopiere den unteren Text in ein anderes Dokument um den Text nicht zu verlieren und versuche es später nochmal.</p>";
                }
            }

            //Es wird sichergestellt, dass ID eine Zahl ist.
            if ($bearbid > 0) {
                $select = "SELECT id, timestamp, autor, titel, text, kategorie, status FROM blogtexte WHERE id=$bearbid";
                $row = $this->sqlselect($select);

                for ($i = 0; $i < sizeof($row); $i++) {

                    $realUsername = $this->getUserName($row[$i]->autor);

                    if (isset($_GET['bearbid'])) {
                        $category = $_GET['bearbid'];
                        echo "<a href=\"blogentry.php?showblogid=" . $row[$i]->id . "\"
                        class=\"buttonlink\">&#8634; zurück zum Thema</a>";
                    }

                    echo "<div class='neuerBlog'>";

                    echo "<form action='?bearbid=" . $row[$i]->id . "&update=update' method=post>";
                    echo "<h2>Eintrag bearbeiten</h2>";
                    echo "<input type='text' value='" . $row[$i]->titel . "' name='newtitel' id='titel' /><br>"; //Titel

                    //Kategorie Bereich:
                    echo "<select name='newblogkategorie' value='' size='1'>";
                    $selectKat = "SELECT id, kategorie, rightWert FROM blogkategorien";
                    $row2 = $this->sqlselect($selectKat);
                    echo "<option></option>";

                    //BenutzerRecht selektieren:
                    //Check ob die Kategorie in der Auswahlliste auftauchen soll.
                    $userID = $this->getUserID($_SESSION['username']);
                    $selectBenutzerRecht = "SELECT id, forumRights FROM benutzer WHERE id = '$userID' LIMIT 1";
                    $rowRecht = $this->sqlselect($selectBenutzerRecht);

                    for ($j = 0; $j < sizeof($row2); $j++) {
                        $aktuelleBenutzerrechte = $rowRecht[0]->forumRights;
                        $kategorieRechte = $row2[$j]->rightWert;

                        if ($this->check($kategorieRechte, $aktuelleBenutzerrechte) == true) {
                            echo "<option";
                            if ($row2[$j]->id == $row[$i]->kategorie) {
                                echo ' selected ';
                            }echo " value='" . $row2[$j]->id . "'>" . $row2[$j]->kategorie . "</option>";
                        }

                    }
                    echo "</select>";

                    //Status #
                    echo "<input type='radio' name='status' value='0' id='statusNull' ";
                    if ($row[$i]->status == "0" or $row[$i]->status == null) {
                        echo " checked ";
                    } else {
                        echo " unchecked ";
                    }
                    echo ">" . "<label for='statusNull'>Gesperrt</label>";

                    echo "<input type='radio' name='status' value='1' id='statusEins' ";
                    if ($row[$i]->status == "1") {
                        echo " checked ";
                    } else {
                        echo " unchecked ";
                    }
                    echo ">" . "<label for='statusEins'>Freigegeben</label>";

                    echo "<input type='radio' name='status' value='4' id='statusVier' ";
                    if ($row[$i]->status == 4) {
                        echo " checked ";
                    } else {
                        echo " unchecked ";
                    }
                    echo ">" . "<label for='statusVier'>PUBLIC</label>";

                    echo "<input type='submit' name='bearbblogid' value='Speichern' />";

                    echo "<div class='editForumEintrag'><textarea class='ckeditor' name='newtext'>" . $row[$i]->text . "</textarea></div>"; //Text Output #

                    //Nur ein Administrator oder der Autor darf den Artikel bearbeiten oder löschen
                    if ($this->userHasRight("29", 0) == true or $_SESSION['username'] == $realUsername) {
                        echo "<input type='submit' name='bearbblogid' value='Speichern' />
                        <a href='?loeschid=" . $row[$i]->id . "' class='highlightedLink'>Beitrag entfernen</a>";
                    }
                    echo "</form>";
                    echo "</div>";

                } //Ende While Schleife
            }
        }
    }

    /**
     * Loescht einen Blog mit der angewaehlten ID.
     * Update vom 07.12.2016: Aktualisierung auf neue PDO Mysql Klassen
     * 
     * @param int $id ID des Blogeintrags welcher gelöscht wird
     * 
     * @return void
     */
    public function deleBlogId($id)
    {

        if ($this->userHasRight(28, 0) == true or $this->userHasRight(29, 0) == true) {

            $jaloeschen = isset($_POST['jaloeschen']) ? $_POST['jaloeschen'] : '';
            $loeschblogid = isset($_POST['blogid']) ? $_POST['blogid'] : '';
            if ($loeschblogid) {

                $row = $this->sqlselect("SELECT id, autor FROM blogtexte WHERE id=$id LIMIT 1");
                //namen des Benutzers ausw�hlen:
                if (!isset($row[0]->autor)) {
                    echo "<p class='info'>Beitrag exisitert nicht!</p>";
                    exit;
                } else {
                    $autor = $row[0]->autor;
                }

                $realUsername_select = $this->sqlselect("SELECT id, Name FROM benutzer WHERE id=$autor LIMIT 1");

                if (isset($realUsername_select[0]->Name)) {
                    $realUsername = $realUsername_select[0]->Name;
                } else {
                    echo "<p class='meldung'>Benutzer existiert nicht.</p>";
                    exit;
                }

                if ($this->userHasRight(29, 0) == true or $_SESSION['username'] == $realUsername) {

                    //Durchfuehrung der Loeschung.
                    $loeschQuery = "DELETE FROM blogtexte WHERE id=$loeschblogid";

                    if ($this->sqlInsertUpdateDelete($loeschQuery) == true) {
                        echo "<p class='erfolg'>Der Beitrag wurde erfolgreich gel�scht!</p>";
                        if ($this->sqlInsertUpdateDelete("DELETE FROM blog_kommentare WHERE blogid=$loeschblogid") == true) {
                            echo "<p class='erfolg'>Der Beitrag wurde erfolgreich gel�scht!</p>";
                            exit;
                        }
                    } else {
                        echo "<p class='meldung'>Beitrag konnte nicht gel�scht werden.</p>";
                    }
                }
            }

            if ($id > 0) { //check ob eine Zahl

                //Kontrolle, ob User den Artikel l�schen darf.
                $select = "SELECT id, autor FROM blogtexte WHERE id=$id LIMIT 1";
                $row = $this->sqlselect($select);

                //namen des Benutzers ausw�hlen:
                if (isset($row[0]->autor)) {
                    $autor = $row[0]->autor;
                    $selectUsername = "SELECT id, Name FROM benutzer WHERE id=$autor LIMIT 1";
                    $realUsername_select = $this->sqlselect($selectUsername);
                    if (isset($realUsername_select[0]->Name)) {
                        $realUsername = $realUsername_select[0]->Name;
                    } else {
                        echo "<p class='meldung'>Benutzer existiert nicht.</p>";
                        exit;
                    }
                } else {
                    exit;
                }

                if ($this->userHasRight(29, 0) == true or $_SESSION['username'] == $realUsername) {
                    //Abfrage, ob der User den Artikel wirklich l�schen will.
                    echo "<form method=post>";
                    echo "<p class='meldung'>Achtung, diese Aktion kann nicht R�ckg�ngig gemacht werden. Dies l�scht den Beitrag aus der Datenbank. <br>
                      Sind Sie sicher?</p>";
                    echo "<input type = hidden value = '$id' name ='blogid' readonly />";
                    echo "<p id='meldung'><input type=submit name='jaloeschen' value='Ja! Weg damit.'/></p><p id='erfolg'><a href='/flatnet2/blog/blog.php?action=showblogyes' class='buttonlink'>Nein, vergiss es.</a></p><br><br><br>";
                    echo "</form>";
                } else {
                    echo "<p class='meldung'>Sie sind nicht berechtigt, den Artikel zu l�schen.</p>";
                }
            }
        }
    }

    /**
     * Zeigt die Felder zum erstellen einer Antwort für einen Forenbeitrag an.
     * 
     * @param int $blogid ID des Blogeintrags
     * 
     * @return void
     */
    public function newKommentar($blogid)
    {

        if ($this->userHasRight(30, 0) == true) {

            $blogIDInfos = $this->sqlselect("SELECT id, locked FROM blogtexte WHERE id = '$blogid'");
            if ($blogIDInfos[0]->locked != 1) {

                if (!isset($_POST['absenden'])) {
                    if ($this->userHasRight(20, 0) == true) {
                        echo "<div class='newForumPost'>";
                        echo "<form method=post>";
                        //Edit mit Inline Editing
                        echo "<h2><a name='antwort'>Antworten</a></h2>";

                        //Wenn gequotet wurde:
                        if (isset($_GET['quote'])) {
                            $quoteID = $_GET['quote'];
                            $getQuoteInfo = $this->sqlselect(
                                "SELECT *
                                , day(timestamp) as tag
                                , month(timestamp) as monat
                                , year(timestamp) as jahr
                                , minute(timestamp) as minute
                                , hour(timestamp) as stunde
                                FROM blogtexte WHERE id = '$quoteID'"
                            );
                            $quote = "<blockquote><strong>" 
                            . $this->getUserName($getQuoteInfo[0]->autor)
                            . "</strong> schrieb am " 
                            . $getQuoteInfo[0]->tag 
                            . "." . $getQuoteInfo[0]->monat 
                            . "." . $getQuoteInfo[0]->jahr 
                            . " um " . $getQuoteInfo[0]->stunde 
                            . ":" . $getQuoteInfo[0]->minute 
                            . " Uhr zu <q>" . $getQuoteInfo[0]->titel 
                            . "</q>:<br>" . strip_tags($getQuoteInfo[0]->text, '<p></p><br><br/><br />') 
                            . "</blockquote><p>text...</p>";
                        }

                        //Wenn ein Kommentar gequotet wurde:
                        if (isset($_GET['quoteKomment'])) {
                            $quoteID = $_GET['quoteKomment'];
                            $getQuoteInfo = $this->sqlselect(
                                "SELECT *
                                , day(timestamp) as tag
                                , month(timestamp) as monat
                                , year(timestamp) as jahr
                                , minute(timestamp) as minute
                                , hour(timestamp) as stunde
                                FROM blog_kommentare WHERE id = '$quoteID'"
                            );

                            $quote = "<blockquote><strong>" 
                            . $this->getUserName($getQuoteInfo[0]->autor) 
                            . "</strong> schrieb am " 
                            . $getQuoteInfo[0]->tag 
                            . "." . $getQuoteInfo[0]->monat 
                            . "." . $getQuoteInfo[0]->jahr 
                            . " um " . $getQuoteInfo[0]->stunde 
                            . ":" . $getQuoteInfo[0]->minute 
                            . " Uhr:<br> " 
                            . strip_tags($getQuoteInfo[0]->text, '<p></p><br><br/><br />') 
                            . "</blockquote><p>text...</p>";
                        }
                        //Wenn nichts gequoted wurde:
                        if (!isset($quote)) {
                            $quote = "";
                        }
                        echo "<div class='editForumEintrag'><textarea class='ckeditor' name='kommentarText'>$quote</textarea></div>";
                        echo "<br><input type='submit' name='absenden' value='absenden' />";
                        echo "</form>";
                        echo "</div>";
                    }
                } else {
                    if ($_POST['kommentarText'] == "") {
                        echo "<p class='info'>Textfeld ist leer.</p>";
                    }
                    if ($_POST['kommentarText'] != "" and $_POST['absenden'] == "absenden") {
                        if ($this->userHasRight("20", 0) == true) {

                            //Alle Daten holen
                            $text = $_POST['kommentarText'];
                            //ID vom aktuellen Benutzer holen
                            $autor = $_SESSION['username'];
                            $selectBenutzer = "SELECT id, Name FROM benutzer WHERE Name = '$autor'";
                            $rowAutor = $this->sqlselect($selectBenutzer);
                            //ID geholt:
                            $autorID = $rowAutor[0]->id;
                            //$blogid

                            $insertKommentar = "INSERT INTO blog_kommentare (autor, text, blogid) VALUES ('$autorID','$text','$blogid')";

                            if ($this->sqlInsertUpdateDelete($insertKommentar) == true) {
                                echo "<p class='erfolg'>Antwort erstellt</p>";
                            } else {
                                echo "<p class='meldung'>Fehler</p>";
                            }
                        }
                    }
                }
            } //Wenn locked ENDE
        } //Recht Ende
    } //FUNCTION ENDE

    /**
     * Ermöglicht das betrachten von Kommentaren zu einer bestimmten BLOG ID
     * Anlage : Steven 15.09.2014
     * 
     * @param int $blogid Blog ID
     * 
     * @return void
     */
    public function kommentare($blogid)
    {
        if (isset($_GET['newComment'])) {
            $this->newKommentar($blogid);
        }

        if ($this->userHasRight(31, 0) == true) {
            //SQL Befehle
            $select = "
                SELECT
                id, timestamp, autor, text
                , year(timestamp) AS jahr
                , month(timestamp) as monat
                , day(timestamp) as tag
                , hour(timestamp) AS stunde
                , minute(timestamp) AS minute
                , blogid
                FROM blog_kommentare
                WHERE blogid=$blogid ORDER BY id
            ";
            $row = $this->sqlselect($select);
            $getLockedInfo = $this->sqlselect("SELECT * FROM blogtexte WHERE id = '$blogid'");

            //Ausgabe aller Kommentare
            for ($i = 0; $i < sizeof($row); $i++) {

                //KOPFZEILE
                echo "<thead id='small'>";

                //Wenn der Benutzer einen Titel hat:
                $autorTitel = $this->sqlselect("SELECT id, titel FROM benutzer WHERE id = '" . $row[$i]->autor . "'");
                if (isset($autorTitel->titel) and $autorTitel->titel != "") {
                    $titel = "<p id='smallLink' class='rightRedLink'>" . $autorTitel->titel . "</p>";
                } else {
                    $titel = "";
                }

                echo "<td><img id='id' src='../images/avatar_122.png' style='width:30px; height: 30px;' />
                            <a id='id' class='rightBlueLink' href='#KommID" . $row[$i]->id . "' name='#KommID" . $row[$i]->id . "'>ID: " . $row[$i]->id . "</a><p id='smallLink' class='rightBlueLink'>Autor: " . $this->getUserName($row[$i]->autor) . "</p> $titel</td>";
                echo "<td>Erstellt: " . $row[$i]->tag . "." . $row[$i]->monat . "." . $row[$i]->jahr . " | " . $row[$i]->stunde . ":" . $row[$i]->minute . " Uhr</td>";

                //EDIT
                echo "<td>";
                if ($getLockedInfo[0]->locked != 1) {
                    if ($this->userHasRight("32", 0) == "true" or $_SESSION['username'] == $this->getUserName($row[$i]->autor)) {
                        echo "<a href='?showblogid=$blogid&editComment=" . $row[$i]->id . "' class='rightRedLink'>edit</a>";
                    }
                }

                //Löschen
                if ($getLockedInfo[0]->locked != 1) {
                    if ($this->userHasRight("35", 0) == true or $this->userHasRight("34", 0) == true) {
                        echo "<a href='?showblogid=$blogid&del=" . $row[$i]->id . "' class='rightRedLink'>Löschen</a>";
                    }
                }

                if ($getLockedInfo[0]->locked != 1) {
                    echo "<a class='rightBlueLink' href='?showblogid=" . $row[$i]->blogid . "&newComment=" . $row[$i]->blogid . "&quoteKomment=" . $row[$i]->id . "#antwort'> Zitat </a>";
                }
                //Zitat

                echo "</td>";

                echo "</thead>";

                echo "<tbody>";
                echo "<td colspan='3'>" . $row[$i]->text . "</td>";
                echo "</tbody>";
            }
        }
    }

    /**
     * Ermöglicht das editieren eines Kommentars.
     * 
     * @param int $kommentarID KommentarID
     * 
     * @return void
     */
    public function editKommentar($kommentarID)
    {

        if (isset($kommentarID) and $kommentarID > 0 and $kommentarID != "") {

            //Autor des Kommentars bekommen:
            $kommentarAutor = $this->sqlselect("SELECT * FROM blog_kommentare WHERE id = '$kommentarID' LIMIT 1");

            $autorID = $kommentarAutor[0]->autor;
            $angemeldeterUserID = $this->getUserID($_SESSION['username']);

            if ($this->userHasRight(32, 0) == true or $this->userHasRight(33, 0) == true) {
                $kommentar = $this->sqlselect("SELECT * FROM blog_kommentare WHERE id ='$kommentarID' AND autor = '$autorID' LIMIT 1");
                if (!isset($kommentar) or $kommentar == "") {
                    echo "<p class='meldung'>Kommentar kann nicht editiert werden.</p>";
                } else {
                    if (!isset($_POST['saveKomment'])) {

                        //NEU
                        echo "<form method=post>";
                        //KOPFZEILE
                        echo "<table class='forumPost'>";
                        echo "<thead>";

                        echo "<td>Antwort editieren</td>";

                        echo "</thead>";

                        echo "<tbody>
                        <td>";
                        echo "<div class='editForumEintrag'><textarea class='ckeditor' name='kommentarText'>" . $kommentar[0]->text . "</textarea></div>"; //Text Output #
                        //   echo"<script> CKEDITOR.inline( 'kommentarText' ); </script>";
                        echo "</td>
                    </tbody>";

                        echo "<tfoot><td><input type=submit name=saveKomment value=Speichern /><a class='highlightedLink' href='?showblogid=" . $kommentar[0]->blogid . "'>Abbrechen</a></td></tfoot>";
                        echo "</table>";
                        echo "</form>";

                    }

                    //editierten Kommentar speichern.
                    if (isset($_POST['saveKomment'])) {
                        if (isset($_POST['kommentarText']) and $_POST['kommentarText'] != "") {

                            //EDTIERT VON XYZ AM XYZ DATE
                            $editiertAm = date('d.m.Y h:i:s', time());
                            $userDerGeaendertHat = $_SESSION['username'];

                            //TEXT MODIFIZIEREN UND EDITIERT VON HINZUFÜGEN
                            $text = $_POST['kommentarText'] . "<p><span style=\"font-size:10px; padding:3px; background-Color: #e0e0e0;\">Editiert am $editiertAm von $userDerGeaendertHat</span></p>";

                            $query = "UPDATE blog_kommentare SET text = '$text' WHERE id = '" . $kommentar[0]->id . "' AND autor = '$autorID'";
                            if ($this->sqlInsertUpdateDelete($query) == true) {
                                echo "<p class='erfolg'>Erfolgreich gespeichert</p>";
                                echo "<a href='/flatnet2/blog/blogentry.php?showblogid=" . $kommentar[0]->blogid . "' class='greenLink' > Zurück </a>";
                            } else {
                                echo "<p class='meldung'>Fehler beim speichern</p>";
                            }

                        }
                    }
                    //Rest ausblenden
                    exit;
                }
            }
        }
    }

    /**
     * Ermöglicht das löschen von Kommentaren
     * 
     * @param int $id     ID des Kommentars
     * @param int $blogid ID des Blogeintrags
     * 
     * @return void
     */
    public function delKommentar($id, $blogid)
    {

        //Wenn der Benutzer volle Berechtigung zum löschen hat.
        if ($this->userHasRight(35, 0) == true) {

            $loeschQuery = "DELETE FROM `blog_kommentare` WHERE id=$id AND blogid ='$blogid'";

            if ($this->sqlInsertUpdateDelete($loeschQuery) == true) {
                echo "<p class='erfolg'><a name='geloescht'>Kommentar gel&ouml;scht!</a><a href='?showblogid=$blogid' class='buttonlink'>OK</a></p>";
                exit;
            } else {
                echo "<p class='meldung'>Fehler</p>";
                exit;
            }

            //Wenn der Benutzer nur einfache Berechtigung hat.
        } else if ($this->userHasRight(34, 0) == true) {

            $currentUser = $this->getUserID($_SESSION['username']);
            $loeschQuery = "DELETE FROM `blog_kommentare` WHERE id=$id AND autor = '$currentUser' AND blogid ='$blogid'";
            $ergebnis = mysql_query($loeschQuery);

            if ($ergebnis == true) {
                echo "<p class='erfolg'><a name='geloescht'>Kommentar gelöscht!</a><a href='?showblogid=$blogid' class='buttonlink'>OK</a></p>";
                exit;
            } else {
                echo "<p class='meldung'>Fehler</p>";
                exit;
            }
        } else {
            echo "<p class='meldung'>Du hast keine Berechtigung zum löschen.</p>";
        }

    }

    /**
     * Prüft, ob der Benutzer die Kategorie des aktuellen Beitrags anzeigen darf.
     * 
     * @param int $user   UserID
     * @param int $blogID Blog ID
     * 
     * @return boolean
     */
    public function userIsAllowedToSeeCategory($user, $blogID)
    {
        $getblogidinfos = $this->sqlselect("SELECT id, kategorie FROM blogtexte WHERE id = '$blogID'");
        if (isset($getblogidinfos[0]->kategorie) and $getblogidinfos[0]->kategorie != "") {
            $kategorie = $getblogidinfos[0]->kategorie;

            //Benötigten Wert der Kategorie bekommen:
            $getKategorieRechteWert = $this->sqlselect("SELECT id, rightWert FROM blogkategorien WHERE id = '$kategorie'");

            //Rechte des aktuellen Benutzers bekommen
            $getRechteForumAktuellerBenutzer = $this->sqlselect("SELECT id, forumRights FROM benutzer WHERE id = '$user' ");

            //Rechte checken
            if ($this->check($getKategorieRechteWert[0]->rightWert, $getRechteForumAktuellerBenutzer[0]->forumRights) == true) {
                return true;
            } else {
                return false;
            }

        } else {
            return false;
        }
    }
}
