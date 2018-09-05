<!DOCTYPE html>
<html id="forum">
    <div id="wrapper">
        <head>
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
            require '../includes/blog.class.php';
            $addBlog = NEW blog;
            $addBlog->header();
            $addBlog->userHasRightPruefung(3);
            $addBlog->logged_in("redirect", "index.php");
            ?>
            <title>Forum - Eintrag erstellen</title>
        </head>
        <body>
            <div class='mainbodyDark'>
                <?php 
                $addBlog->showForumNav();
                if (isset($_GET['blogcategory'])) {
                    $kategorie = $_GET['blogcategory'];
                    echo "<a href=\"/flatnet2/forum/index.php?blogcategory=$kategorie\" class=\"highlightedLink\">&#8634; Zurück</a>";
                } 
                $addBlog = NEW blog;
                echo "<h2>Neues Thema erstellen</h2>";
                $addBlog->newBlogFunction();
                $addBlog->newBlogEingabe();
                
                if (isset($_GET['blogcategory'])) {
                    $kategorie = $_GET['blogcategory'];
                    echo "<a href=\"/flatnet2/forum/index.php?blogcategory=$kategorie\" class=\"highlightedLink\">&#8634; Zurück</a>";
                } ?>
                <?php 
                $addBlog->showForumNav();
                ?>
            </div>
        </body>
    </div>
</html>
