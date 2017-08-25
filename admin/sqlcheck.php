<?php
include '../includes/objekt/functions.class.php';
class sqlcheck extends functions {
    
    function mainSQLCheck() {
        echo "<h4>Finanzbereich</h4>";
        $this->finanzsqlcheck();
        
        echo "<h4>Guildwars</h4>";
        $this->gw_check();
        
        echo "<h4>Administrationsbereich</h4>";
        $this->admin_check();
        
        echo "<h4>Forum</h4>";
        $this->forum_check();
        
        echo "<h4>Adressbuch</h4>";
        $this->adressbuch_check();
        
        echo "<h4>Profil</h4>";
        $this->profil_check();
        
        echo "<h4>Quiz</h4>";
        $this->quiz_check();
    }
    
    /**
     * Checkt den Bereich Finanzen
     */
    function finanzsqlcheck() {
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
     * Check für Guildwars
     */
    function gw_check() {
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
     * Check für Administrationsbereich
     */
    function admin_check() {
        
    }
    
    /**
     * Check für Forum
     */
    function forum_check() {
        
    }
    
    /**
     * Check für Adressbuch
     */
    function adressbuch_check() {
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
    function profil_check() {
        
    }
    function quiz_check() {
        
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