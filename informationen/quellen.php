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
<html id='informationen'>
    <div id="wrapper">
        <head>
            <?php 
            require '../includes/objekt/functions.class.php';
            $quellen = NEW functions;
            $quellen->header();
            ?>
            <title>Quellen</title>
        </head>
        <body>
            <div class='mainbody'>
                <div class="rightOuterBody">
                    <div id='quellen'>
                    <?php $quellen->SubNav("informationen"); ?>
                    </div>
                </div>
                <h2>Quellen & Beschreibung dieses Projektes</h2>
                <p>Dieses Projekt wird vollständig in PHP programmiert. Diese Seite ist im Rahmen meines Informatikstudiums entstanden 
                und wird stetig weiterentwickelt. Sie dient lediglich meinem Zeitvertreib. Ein Anspruch auf fehlerfreiheit besteht nicht
                und wird auch nicht garantiert.
                </p>
                <p>Alle Codebestandteile wurden von mir persönlich erstellt und unterliegen dem Urheberrecht. Ein kopieren oder
                weiterverwenden dieses Codes ist nicht gestattet.</p>
                <p>
                </p>
                <h2>Quellenverzeichnis</h2>
                <table class='kontoTable'>
                <thead>
                <td>Seiten</td>
                </thead>
                <tbody><td id='text'>
                    <a href="http://ckeditor.com/">CKEditor für das Forum und für alle möglichen Textfelder.</a>
                </td></tbody>
                <tbody><td id='text'>
                    <a href="http://www.webmasterpro.de/coding/article/php-ein-einfaches-flexibles-rechtesystem.html">Rechtesystem</a>
                </td></tbody>
                <tbody><td id='text'>
                    <a href="http://www.php-einfach.de/tuts_php.php">Tutorials</a>
                </td></tbody>
                <tbody><td id='text'>
                    <a href="http://www.guildwars2.com/">Guildwars2.com für die Bilder und Screenshots.</a>
                </td></tbody>
                <tbody><td id='text'>
                    <a href="http://www.google.de/">Eclipse zum programmieren</a>
                </td></tbody>
                <tbody><td id='text'>
                    <a href="http://www.muster-vorlagen.net/impressum-generator.html">Impressumsgenerator</a>
                </td></tbody>
                <tbody><td id='text'>
                    <a href="http://www.webmasterpro.de/coding/article/sichere-php-web-applikationen-schreiben-ein-ueberblick.html">PHP Sicherheit</a>
                </td></tbody>
                <tbody><td id='text'>
                    <a href="http://de.selfhtml.org/css/index.htm">SELF HTML</a>
                </td></tbody>
                </table>
            </div>
        </body>
    </div>
</html>

