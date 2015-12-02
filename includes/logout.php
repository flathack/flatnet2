<?php
/**
 * @author Steven
 * Führt den Logout durch, löscht die aktuelle Session durch Aufruf der entsprechenden function
 * in der login class.
 */

# inclusions:
include "login.class.php";

# Aufruf der Class
$doTheLogout = NEW login;

# Aufruf der function
$doTheLogout->logout();