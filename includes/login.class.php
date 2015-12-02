<?php
/**
 * @history Steven 19.08.2014 angelegt.
 * @history Steven 27.08.2014 Der Login setzt jetzt den Blog beim einloggen auf "öffentlich"
 * @author Steven
 * Führt den Login des Benutzers am Server durch.
 */

include 'objekt/functions.class.php';

class login extends functions {

	/**
	 * Führt den Login durch und setzt verschiedene Session Variablen
	 * $_SESSION['selectUser'] auf $username
	 * Leitet auf eine entsprechende Seite weiter.
	 */
	public function login_user($umleitung = "uebersicht.php")  {

		$errorMessage = "<p class='meldung'>Der Benutzername oder das Passwort ist falsch. Überprüfen Sie die Schreibweise und versuchen Sie es erneut.</p>";

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {

			if(isset($_POST['username']) AND isset($_POST['passwort'])) {

				if($_POST['username'] == "" OR $_POST['passwort'] == "") {
					$this->logEintrag(false, "Name oder Passwort leer", "Error");
					return $errorMessage;
				}
				
				# HTML Tags und erlaubte Zeichen aus String herausfiltern:
				$username = strip_tags( stripslashes($_POST['username']));
				
				# Passwort mit md5 hash verändern: 
				$passwort = md5($_POST['passwort']);
					
				# Unbekannt?
				$hostname = $_SERVER['HTTP_HOST'];
				$path = dirname($_SERVER['PHP_SELF']);
					
				//SQL Abfrage
				$abfrage = "SELECT id, Name, Passwort, versuche FROM benutzer WHERE Name LIKE '$username' LIMIT 1";
				$ergebnis = mysql_query($abfrage);
				$row = mysql_fetch_object($ergebnis);

				if(!isset($row->Name)) {
					$this->logEintrag(false, "Login Fehlgeschlagen: Name $username existiert nicht in der Datenbank.", "Error");
					return $errorMessage;
				} else {
					$userID = $row->id;
				}

				if($row->versuche >= 3) {
					$gesperrterNutzer = $row->Name;
					$this->logEintrag(false, "Login Fehlgeschlagen: Benutzer $gesperrterNutzer ist gesperrt", "Error");
					return $errorMessage;
				} else {
					// Benutzername und Passwort werden gecheckt

					if($row->Passwort == $passwort) {
						#setzen der Session Variablen
						$_SESSION['angemeldet'] = true;
						$_SESSION['username'] = $row->Name;
							
						# Hilfseinstellung für Guildwars
						$_SESSION['selectUser'] = $row->Name;
						$_SESSION['selectGWUser'] = $row->Name;

						# Logeintrag
						$this->logEintrag(true, "hat sich eingeloggt", "login");
							
						#Versuche Updaten
						$update = "UPDATE benutzer SET versuche='0' WHERE Name = '$username'";
						$updateErgeb = mysql_query($update);
						if($updateErgeb == "true") {
							header("Location: $umleitung");
							return true;
						} else {
							$errorMessage .= "<p class='info'>Ein Login ist derzeit nicht möglich.</p>";
							$this->logEintrag(false, "Versuche von $username konnten nicht auf 0 gesetzt werden.", "Error");
							return $errorMessage;
						}
							
					} else {

						# Versuche hochzählen // ab hier ist sicher, dass der Benutzer existiert.
						$abfrage = "SELECT Name, versuche FROM benutzer WHERE Name LIKE '$username' LIMIT 1";
						$ergebnis = mysql_query($abfrage);
						$row2 = mysql_fetch_object($ergebnis);
						
						# Zur Sicherheit wird der Benutzername aus der Tabelle genommen
						$usernameSAVE = $row2->Name;

						# Neue Versuche ausrechnen
						$bisherigeVersuche = $row->versuche;
						$neueVersuche = $bisherigeVersuche + 1;

						# Versuche speichern
						$updateVersuche = "UPDATE benutzer SET versuche='$neueVersuche' WHERE Name = '$usernameSAVE'";
						$updateErgeb2 = mysql_query($updateVersuche);
						if($updateErgeb2 == "true") {
							$this->logEintrag(false, "Versuche von $usernameSAVE wurden hochgezählt (falsches Passwort).", "Error");
							return $errorMessage;
						} else {
							$this->logEintrag(false, "Versuche konnten nicht geupdatet werden.", "Error");
							return $errorMessage;
						} # else ende
					} # else ende
				} # else ende
			} # if isset ende
		} # server requiest ende
	} # function ende

	/**
	 * prüft, ob die Felder zum anmelden angezeigt werden sollen.
	 */
	public function anmeldeCheck() {
		$ausgabe = "";
		$notLoggedIn = "<p>Ein Login ist notwendig</p>";

		if(!isset($_SESSION['angemeldet'])) {
			$ausgabe .= $notLoggedIn;
			$ausgabe .= '<form action="index.php" method=post>';
			$ausgabe .= '<input type="text" value="" name="username" placeholder="Benutzername" /><br><br>';
			$ausgabe .= '<input type="password" value = "" name = "passwort" placeholder = "Passwort" /><br><br>';
			$ausgabe .= '<input type="submit" value="Einloggen" />';
			$ausgabe .= '</form>';
		} else {
			$ausgabe .= "<p>Du bist bereits angemeldet <a href='uebersicht.php' class='buttonlink'>Weiter</a></p>";
		}
		if(!isset($_GET['createUser'])) { return $ausgabe; }
	}

	/**
	 * Prüft, ob der User eingeloggt ist. Dabei ist es möglich nur abzufragen,
	 * ob der User eingeloggt ist oder ob er zu einem $pfad geschickt werden soll.
	 */
	public function logged_inOLD($redirect = "redirect", $pfad = "/flatnet2/index.php" ) {
		$hostname = $_SERVER['HTTP_HOST'];
		$path = dirname($_SERVER['PHP_SELF']);

		if (!isset($_SESSION['angemeldet']) || !$_SESSION['angemeldet'] AND $pfad !="") {
			header("Location: $pfad");
		} else {
			return true;
		}
	}

	/**
	 * Gibt zurück, ob ein User eingeloggt ist.
	 * @return boolean
	 */
	public function is_logged_in() {
		if(isset($_SESSION['angemeldet'])) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * @history Steven 19.08.2014 angelegt.
	 * @author Steven
	 * Loggt den User aus.
	 */
	public function logout() {

		session_start();
		session_destroy();
		header("Location: /flatnet2/index.php");
	}
	
	/**
	 * Ermöglicht das registrieren mit Hilfe eines Einladungscodes.
	 * März 2015
	 * @autor: Steven Schödel
	 * @return boolean
	 */
	function registerNewUser() {
		if(isset($_GET['createUser'])) {
			echo "<form action='?createUser' method=post>";
			echo "<input type=text name=user placeholder='Benutzername' value='' /><br><br>";
			echo "<input type=password name=pass placeholder='Passwort' value='' /><br><br>";
			echo "<input type=password name=pass2 placeholder='wiederholen' value='' /><br><br>";
			echo "<input type=text name=code placeholder='Einladungscode' value='' /><br><br>";
			echo "<p class='dezentInfo'>Hinweis: Es werden keine persönlichen Daten gespeichert. </p>";
			echo "<input type=submit name=register value='Registrieren' /><a href='?' class='greenLink'>Zum Login</a><br><br>";
			echo "</form>";
				
			if(isset($_POST['register']) AND isset($_POST['pass']) AND isset($_POST['pass2']) AND isset($_POST['code']) AND isset($_POST['user'])) {
				$code = strip_tags( stripslashes($_POST['code']));
				$name = strip_tags( stripslashes($_POST['user']));
				$pass = strip_tags( stripslashes($_POST['pass']));
				$pass2 = strip_tags( stripslashes($_POST['pass2']));

				if($pass != $pass2) {
					return false;
				}

				# Deklaration der Variablen
				$passwort1 = $pass;
				$passwort2 = $pass2;
				$username = $name;
				$regnewuser = $_POST['register'];
				if($username == "" OR $passwort1 == "" OR $passwort2 == "") {
					echo "<p class='meldung'>Fehler, es wurden nicht alle Felder ausgefüllt.</p>";
				} else {
					$check = "SELECT * FROM benutzer WHERE Name LIKE '$username' LIMIT 1";
					$checkergebnis = mysql_query($check);
					$row = mysql_fetch_object($checkergebnis);

					/* Hier tritt ein Notice Fehler auf, ist aber normal,
					 * da im Normalfall kein Benutzer gefunden wird.
					*/
					if(isset($row->Name)) {
						echo "<p class='meldung'>Fehler, der Benutzer <strong>$username</strong> existiert bereits.</p>";
					} else {
						if($passwort1 == $passwort2) {
							
							# Register Code Check
							
							if($this->objectExists("registercode", "code", $code) == false) {
								echo "<p class='meldung'>Es gibt Probleme mit dem Code, kontaktiere den Administrtor.</p>";
								# Logeintrag
								$this->logEintrag(true, "hat den Code $code benutzt, dieser existiert aber nicht.", "Error");
								return false;
								
							} else {
								
								# Den Code überprüfen
								$select = "SELECT * FROM registercode WHERE code = '$code' LIMIT 1";
								$checkergebnis = mysql_query($select);
								$row = mysql_fetch_object($checkergebnis);
								
								# Check, ob der Code zu oft benutzt wurde
								if($row->used >= $row->usageTimes) {
									echo "<p class='meldung'>Es gibt Probleme mit dem Code, kontaktiere den Administrtor.</p>";
									# Logeintrag
									$this->logEintrag(true, "hat den Code $code benutzt, die vorgeschriebene Nutzungszahl wurde überschritten.", "Error");
									return false;
									
								} else {
									# IP Check
									if (!isset($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
										$ip = $_SERVER['REMOTE_ADDR'];
									}
									else {
										# IP Check
										$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
									}
									# neue Benutzung ausrechnen
									$used = $row->used + 1;
									
									# Coderechte für den Benutzer übernehmen
									$codeRechte = $row->rights;
									
									# Update des Codes.
									$update = "UPDATE registercode SET used='$used',usedBy='$username',ipadress='$ip' WHERE code = '$code'";
									$sqlupdategesamt = mysql_query($update);
									if($sqlupdategesamt == true) {
										# Logeintrag
										$this->logEintrag(true, "hat den Code $code benutzt, dieser wurde von $row->used nach $used geupdatet", "Error");
									} else {
										echo "<p class='meldung'>Es gibt Probleme mit dem Code, kontaktiere den Administrtor.</p>";
										# Logeintrag
										$this->logEintrag(true, "wollte sich registrieren, der Code konnte aber nicht geupdated werden.", "Error");
										return false;
									}
								}
							}
							
							# Benutzer anlegen
							$passwortneu = md5($passwort1);
							$query="INSERT INTO benutzer (Name, Passwort, rights, versuche) VALUES ('$username','$passwortneu','$codeRechte','0')";
							$ergebnis = mysql_query($query);
							if($ergebnis == true) {
								echo "<p class='erfolg'>Hallo $username! Du hast dich erfolgreich registriert.</p>";
								# Logeintrag
								$this->logEintrag(true, "hat sich registriert", "login");
							} else {
								echo "<p class='meldung'>Bei der registrierung ist ein Fehler aufgetreten.</p>";
								# Logeintrag
								$this->logEintrag(true, "wollte sich registrieren, der DB Eintrag konnte aber nicht erstellt werden.", "Error");
							}
						} else {
							echo "<p class='meldung'>Die Passwörter sind nicht identisch!</p>";
						} # Else Ende
					} # Else Ende
				} # Else Ende
			} else {
				return false;
			}
		}
	}

}