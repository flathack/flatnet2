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
?>
<!DOCTYPE html>
<html id='uebersicht'>
<div id="wrapper">
    <head>
        <?php 
        require 'includes/uebersicht.class.php';
        $uebersicht = NEW uebersicht;
        $uebersicht->header();
        $uebersicht->logged_in("redirect", "index.php");
        ?>
        <title>Steven.NET - Home</title>
    </head>
    <body>
        <?php $uebersicht->mainUebersichtFunction(); ?>
    </body>
</div>
</html>
