<!DOCTYPE html>
<html id="learner">
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
            require '../includes/learner.class.php';
            $learner = NEW Learner;
            $learner->newheader(); 
            //$learner->userHasRightPruefung(70);?>
            <title>Steven.NET-Vokabeln lernen</title>
        </head>
        <body>
        <?php $learner->learnerWelcome(); ?>
        </body>
    </div>
</html>
