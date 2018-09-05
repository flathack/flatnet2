<?php
/**
 * Datei beeinhaltet die Klasse: SQLChecker
 * 
 * PHP Version 7
 * 
 * @category   Documents
 * @package    Flatnet2
 * @subpackage NONE
 * @author     Steven Schödel <steven.schoedel@outlook.com>
 * @license    https://www.gnu.org/licenses/gpl-3.0.txt GNU/GPLv3
 * @link       none
 */
require '../includes/objekt/functions.class.php';
/**
 * SQLChecker
 * Checkt ob die Datenbank richtig konfiguriert ist.
 *
 * PHP Version 7
 * 
 * @category   Classes
 * @package    Flatnet2
 * @subpackage NONE
 * @author     Steven Schödel <steven.schoedel@outlook.com>
 * @license    https://www.gnu.org/licenses/gpl-3.0.txt GNU/GPLv3
 * @link       none
 */
class Sqlcheck extends functions
{
    /**
     * MAIN
     * 
     * @return void
     */
    function mainSQLCheck() 
    {
        echo "<h4>Finanzbereich</h4>";
        $this->finanzCheck();
        
        echo "<h4>Guildwars</h4>";
        $this->gwCheck();
        
        echo "<h4>Administrationsbereich</h4>";
        $this->adminCheck();
        
        echo "<h4>Forum</h4>";
        $this->forumCheck();
        
        echo "<h4>Adressbuch</h4>";
        $this->adressbuchCheck();
        
        echo "<h4>Profil</h4>";
        $this->profileCheck();
        
        echo "<h4>Quiz</h4>";
        $this->quizCheck();
    }
    
    /**
     * Checkt den Bereich Finanzen
     * 
     * @return void
     */
    function finanzCheck() 
    {
        $relation_name = "finanzen_umsaetze";
        $structure = array (
            "id",
            "timestamp",
            "buchungsnsr",
            "besitzer",
            "konto",
            "gegenkonto",
            "umsatzName",
            "umsatzWert",
            "datum",
            "link"
        );
        $this->sql_db_check($relation_name, $structure);
        
        $relation_name = "finanzen_konten";
        $structure = array (
            "id",
            "timestamp",
            "konto",
            "besitzer",
            "aktiv",
            "notizen",
            "art",
            "mail"
        );
        $this->sql_db_check($relation_name, $structure);
        
        $relation_name = "finanzen_monatsabschluss";
        $structure = array (
            "id",
            "timestamp",
            "besitzer",
            "monat",
            "year",
            "wert",
            "konto"
        );
        $this->sql_db_check($relation_name, $structure);
        
        $relation_name = "finanzen_shares";
        $structure = array (
            "besitzer",
            "konto_id",
            "target_user"
        );
        $this->sql_db_check($relation_name, $structure);
        
        $relation_name = "finanzen_jahresabschluss";
        $structure = array (
            "timestamp",
            "besitzer",
            "jahr",
            "wert",
            "konto"
        );
        $this->sql_db_check($relation_name, $structure);
        
    }
    
    /**
     * Check fuer Guildwars
     * 
     * @return void
     */
    function gwCheck() 
    {
        $relation_name = "account_infos";
        $structure = array (
            "id",
            "timestamp",
            "besitzer",
            "attribut",
            "wert",
            "account"
        );
        $this->sql_db_check($relation_name, $structure);
    }
    
    /**
     * Check fuer Administrationsbereich
     * 
     * @return void
     */
    function adminCheck() 
    {
        echo "Nothing Inside";
    }
    
    /**
     * Check fuer Forum
     * 
     * @return void
     */
    function forumCheck() 
    {
        echo "Nothing Inside";
    }
    
    /**
     * Check fuer Adressbuch
     * 
     * @return void
     */
    function adressbuchCheck() 
    {
        $relation_name = "adressbuch";
        $structure = array (
            "id",
            "timestamp",
            "vorname",
            "nachname",
            "strasse",
            "hausnummer",
            "postleitzahl",
            "bundesland",
            "land",
            "telefon1",
            "telefon2",
            "telefon3",
            "telefon4",
            "telefon1art",
            "telefon2art",
            "telefon3art",
            "telefon4art",
            "skype",
            "facebook",
            "notizen",
            "fax",
            "gruppe",
            "email",
            "geburtstag",
            "stadt"
        );
        $this->sql_db_check($relation_name, $structure);
    }

    /**
     * Check fuer Profil
     * 
     * @return void
     */
    function profileCheck() 
    {
        
    }

    /**
     * Check fuer Quiz
     * 
     * @return void
     */
    function quizCheck() 
    {
        
    }
}

?>

<html>
<head>
<?php 
$admin = NEW sqlcheck();
$admin->header();
?>
</head>
<header>

</header>
<body>
    <div class="mainbody">
    <h4>SQL - Checker</h4>
    
    <?php $admin->mainSQLCheck();?>
    </div>
</body>


</html>