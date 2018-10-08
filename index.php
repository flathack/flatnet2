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

session_start(); 
?>
<!DOCTYPE html>
<html>
    <head>
        <link href="/flatnet2/css/style.css" type="text/css" rel="stylesheet" />
        <link rel="icon" href="/favicon.ico" type="image/x-icon" />
        <?php 
        require 'includes/login.class.php';
        $indexlogin = NEW login();
        ?>
        <div id="funktion"> 
        <?php
        echo $indexlogin->login_user(); 
        ?> 
        </div>
        <title>Steven.NET - Login</title>
    </head>
    <body id='index'>
        <div class='login'>
            <h1><a href='?'>steven.net</a></h1>
            <div id="optionen">
                <?php 
                if (isset($_GET['createUser'])) { 
                    echo "<li><a href='?'>Zur&uuml;ck zum Login</a></li>"; 
                } else { 
                    echo "<li><a href='?createUser'>Account erstellen</a></li>"; 
                }
                ?>
                <li><a href="#" class="" onclick="document.getElementById('Impressum').style.display = 'block'">Impressum</a></li>
                <li><a href="#" class="" onclick="document.getElementById('DatenschutzInfos').style.display = 'block'">Datenschutzinformationen</a></li>
                <li><a href="#" class="" onclick="document.getElementById('zweck').style.display = 'block'">Informationen zur Seite</a></li>
            </div>
            <div id="loginFelder">
                <p id='loginTitel'>Login</p>
                <?php echo $indexlogin->anmeldeCheck(); ?>
            </div>
            <div class="logininfos">
                <div style="display: none;" id="DatenschutzInfos">
                    <a href="?" class="rightRedLink">OK</a>
                    <?php require 'informationen/datenschutz.html'; ?>
                </div>
                <div style="display: none;" id="Impressum">
                    <a href="?" class="rightRedLink">OK</a>
                    <?php require 'informationen/impressum.html'; ?>
                </div>
                <div style="display: none;" id="zweck">
                    <a href="?" class="rightRedLink">OK</a>
                    <?php require 'informationen/zweck.html'; ?>
                </div>
            </div>
            <div class="hinweis"><a href="/flatnet2/planner/index.php">Gehe zum EventPlanner</a></div>
            
            <div id="register">
                <?php $indexlogin->registerNewUser(); ?>
            </div>
           
        </div>
        
    </body>
</html>
