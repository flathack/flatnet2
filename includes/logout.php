<?php
/**
 * @author Steven
 * F�hrt den Logout durch, l�scht die aktuelle Session durch Aufruf der entsprechenden function
 * in der login class.
 */

# inclusions:
include "login.class.php";

# Aufruf der Class
$doTheLogout = NEW login;

# Aufruf der function
$doTheLogout->logout();