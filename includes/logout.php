<?php
/**
 * Seite fuehrt den Logout durch in dem die Funktion 
 * in der Klasse login.class.php genutzt wird.
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

// inclusions:
require "login.class.php";

// Aufruf der Class
$doTheLogout = NEW login;

// Aufruf der function
$doTheLogout->logout();