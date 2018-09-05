<!DOCTYPE html>
<html id="forum">
    <div id="wrapper">
        <head>
            <script src="/flatnet2/tools/ckeditor/ckeditor.js"></script>
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
            $blog = NEW blog;
            $blog->header();
            $blog->logged_in("redirect", "index.php");
            $blog->userHasRightPruefung(2);
            $allgemein = NEW functions;
            $suche = isset($_GET['suche']) ? $_GET['suche'] : '';
            echo $allgemein->suche($suche, "blogtexte", "titel", "?showblogid");
            ?>
            <title>Forum - Thema anzeigen</title>
        </head>
        <body>
            <div class="mainbodyDark">			
                <?php 
                // zeigt Navigation an
                $blog->showForumNav();
                $bearbid = (isset($_GET['bearbid'])) ? $_GET['bearbid'] : '';
                $blogid = (isset($_GET['showblogid'])) ? $_GET['showblogid'] : '';
                $loeschid = (isset($_GET['loeschid'])) ? $_GET['loeschid'] : '';
                $selectContentForBlog = NEW blog;
                if ($blog->userHasRight("20", 0) == true) {
                    $selectContentForBlog->bearbBlogId($bearbid);
                    $selectContentForBlog->deleBlogId($loeschid);
                }
                if (!isset($_GET['bearbid'])) {
                    $selectContentForBlog->showBlogId($blogid);
                }
                $blog->showForumNav();
                ?>
            </div>

        </body>
    </div>
</html>
