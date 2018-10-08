<!DOCTYPE html>
<html id="planner">
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
            $planner = NEW Planner;
            $planner->newheader(); ?>
            <title>Steven.NET-Event Planner</title>
        </head>
        <body>
            <div class='mainbodyDark'>
                <?php $planner->plannerMainFunction(); ?>
            </div>
        </body>
    </div>
</html>
