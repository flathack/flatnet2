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
            require '../includes/planner.class.php';
            $learner = NEW Learner;
            $learner->header(); ?>
            <title>Steven.NET-Vokabeln lernen</title>
        </head>
        <body>
            <div class='mainbody'>
                <?php $learner->learnerWelcome(); ?>
            </div>
        </body>
    </div>
</html>
