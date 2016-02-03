<?php echo '<'.'?xml version="1.0" encoding="utf-8"?'.'>'; ?>
<html>
<div id=wrapper>
<div class='mainbody'>

<?php 
class lotto {
	
	/**
	 * Führt eine Lottoziehung durch und berechnet, wann diese gewonnen wird.
	 */
	function mainfunction() {
		
			if(isset($_GET['max'])) { $max = $_GET['max']; } else { $max = 1000; }
			if(isset($_GET['bis'])) { $bis = $_GET['bis']; } else { $bis = 49; }
			if(isset($_GET['richtige'])) { $richtige = $_GET['richtige']; } else { $richtige = 3; }
			
			if(isset($_GET['zahl1'])) { $zahl1 = $_GET['zahl1']; } else { $zahl1 = 1; }
			if(isset($_GET['zahl2'])) { $zahl2 = $_GET['zahl2']; } else { $zahl2 = 1; }
			if(isset($_GET['zahl3'])) { $zahl3 = $_GET['zahl3']; } else { $zahl3 = 1; }
			if(isset($_GET['zahl4'])) { $zahl4 = $_GET['zahl4']; } else { $zahl4 = 2; }
			if(isset($_GET['zahl5'])) { $zahl5 = $_GET['zahl5']; } else { $zahl5 = 2; }
			if(isset($_GET['zahl6'])) { $zahl6 = $_GET['zahl6']; } else { $zahl6 = 2; }
		
			echo "<div class='newCharWIDE'>";
			echo "<h2>Erweitertes Lotto</h2>";
			echo "<p>Grundeinstellungen</p>";
			echo "<form method=get>";
			echo "Wie oft willst du spielen? <br><input type=number name=max value=$max /><br>";
			echo "Aus wie vielen Zahlen soll gezogen werden? <br><input type=number name=bis value=$bis /><br>";
			echo "Richtige: <br><input type=number name=richtige value=$richtige />";
			echo "<input type=hidden name=lottoErweitert />";
			echo "<br>";
			echo "<br>Zahl 1: <input type=number name=zahl1 value=$zahl1 />";
			echo "<br>Zahl 2: <input type=number name=zahl2 value=$zahl2 />";
			echo "<br>Zahl 3: <input type=number name=zahl3 value=$zahl3 />";
			echo "<br>Zahl 4: <input type=number name=zahl4 value=$zahl4 />";
			echo "<br>Zahl 5: <input type=number name=zahl5 value=$zahl5 />";
			echo "<br>Zahl 6: <input type=number name=zahl6 value=$zahl6 />";
			echo "<br><input type=submit name=erweitertesLottoStart />";
			echo "</form>";
			
			echo "</p>";
			
			if(isset($_GET['erweitertesLottoStart'])) {
				$start = time();
				
				$eigeneZahlen = [$_GET['zahl1'],$_GET['zahl2'],$_GET['zahl3'],$_GET['zahl4'],$_GET['zahl5'],$_GET['zahl6']];
				
				$result = array_unique($eigeneZahlen);
				
				if(sizeof($eigeneZahlen) != sizeof($result)) {
					echo "<p class='meldung'>Du hast doppelte Zahlen gew&auml;hlt! </p>";
					exit;
				}

				$max = $_GET['max'];
				$bis = $_GET['bis'];
				$richtige = $_GET['richtige'];
				
				$counter = 1;
				while ( $this->lottoAbfrage($eigeneZahlen, $bis, $richtige) == false AND $counter < $max) {
					$counter += 1;
				}
				
				$end = time();
				$laufzeit = $end-$start;
				
				if($counter == $max) {
					echo "<p class='info'>Es wurden $max Ziehungen durchgef&uuml;hrt. ($laufzeit Sekunden)</p>";
				} else {
					echo "<p class='erfolg'>Es wurden " . $counter . " Ziehungen durchgeführt.</p>";
				} 
				
			}
			echo "</div>";
		
	}
	
	/**
	 * Man kann die Würfelseiten und die Anzahl der Würfe angeben, diese Würfe werden dann durchgeführt.
	 */
	function wuerfelWuerfe() {

		echo "<h3>Mehrere W&uuml;rfe: </h3>";
				
		if(isset($_GET['werfen'])) {
			echo "<form method=get>";
			echo "W&uuml;rfelseiten:<br> <input type=number name=wuerfelseiten value='6' /> <br>";
			echo "Anzahl:<br> <input type=number name=anzahl value='1000' /><br>";
			echo "<input type=hidden name=werfen />";
			echo "<br> <input type=submit />";
			echo "</form><br>";
		
			if(isset($_GET['werfen']) AND isset($_GET['wuerfelseiten']) AND isset($_GET['anzahl'])) {
				$anzahl = $_GET['anzahl'];
				$wuerfelSeiten = $_GET['wuerfelseiten'];
				$wuerfe = array();
				$wuerfe = array_fill(0,$_GET['wuerfelseiten']+1,0);
		
				if($anzahl < 0 OR $wuerfelSeiten < 0 ) {
					echo "<p class='meldung'>Die Zahlen dürfen nicht negativ sein!</p>";
					exit;
				}
		
				Echo "<p>OK! Es wird $anzahl x mit einem $wuerfelSeiten seitigen W&uuml;rfel gew&uuml;rfelt ... </p>";
		
				for($i = 0 ; $i < $anzahl ; $i++) {
					$zahl = rand(1,$wuerfelSeiten);
		
					$wuerfe[$zahl] += 1;
		
				}
		
				#	print_r($wuerfe);
		
				echo "<h3>Ergebnis</h3>";
		
				for ($k = 1 ; $k < sizeof($wuerfe) ; $k++) {
					echo $k . " | " . $wuerfe[$k] . " x <br>";
				}
			}
		
		}
		
	}
	
	/**
	 * Einfache Zahlenabfrage
	 * @param unknown $meineZahl
	 * @param unknown $von
	 * @param unknown $bis
	 * @return boolean
	 */
	function habichgewonnen($meineZahl, $von, $bis) {
		
		$zufallszahl = rand($von,$bis);
		
		if($zufallszahl == $meineZahl) {
			return true;
		} else {
			return false;
		}
		
	}
	
	/**
	 * Prüft, ob die eigenen Lottozahlen gezogen wurden.
	 * @param unknown $meineLottoZahlen
	 * @param unknown $bis
	 * @return boolean
	 */
	function lottoAbfrage($meineLottoZahlen, $bis, $richtige) {
		
		$lottoZiehung = [0,0,0,0,0,0];
		
		for($i = 0 ; $i < 6 ; $i++) {
			
			$zufallszahl = rand(1,$bis);
			if(in_array($zufallszahl, $lottoZiehung)) {
				$i--;
			} else {
				$lottoZiehung[$i] = $zufallszahl;
			}
			
			
		}
		
		# Hier wird geprüft, ob eine Zahl in den Lottozahlen ist
		$counter = 0;
		
		for ($i = 0 ; $i < sizeof($lottoZiehung) ; $i++) {
			if(in_array($meineLottoZahlen[$i], $lottoZiehung)) {
				$counter++;
			}
		}
		
		if($counter >= $richtige) {
			echo "<p class='erfolg'>Du wolltest $richtige Richtige, du hast $counter Richtige in dieser Ziehung erhalten!</p>";
			echo "<p>Die Lottozahlen sind: </p>";
			for($i = 0 ; $i < sizeof($lottoZiehung); $i++) {
				echo " " . $lottoZiehung[$i] . " | ";
			}
			echo "<p>Die eigenen Zahlen sind:<br> ";
			for ($i = 0 ; $i < sizeof($meineLottoZahlen) ; $i++) {
				echo " " .$meineLottoZahlen[$i]. " | ";
			}
			return true;
		} else {
			return false;
		}
						
	}
	
	/**
	 * Macht einmal eine Lottoziehung
	 */
	function einfachesLotto() {
		echo "<div class='mainbody'>";
		echo "<h2>Einfaches Lotto</h2>";
		echo "<p>Bitte deine Lottozahlen eingeben</p>";
		echo "<form method=get>";
		for($i = 1 ; $i <= 6 ; $i++) {
			echo "<br>$i Eintrag: <input type=number name=eigeneLottoZahlen$i value='' />";
		}
		echo "<input type=hidden name=lotto />";
		echo "<input type=submit name=startLotto />";
		echo "</form>";
		echo "</div>";
		
		if(isset($_GET['startLotto'])) {
			for($i = 0 ; $i < 6 ; $i++) {
				$lottoZiehung[$i] = rand(1,49);
			}
			
			echo "<p>Deine Lottozahlen:</p>";
			$eigeneZahlen[0] = $_GET['eigeneLottoZahlen1'];
			$eigeneZahlen[1] = $_GET['eigeneLottoZahlen2'];
			$eigeneZahlen[2] = $_GET['eigeneLottoZahlen3'];
			$eigeneZahlen[3] = $_GET['eigeneLottoZahlen4'];
			$eigeneZahlen[4] = $_GET['eigeneLottoZahlen5'];
			$eigeneZahlen[5] = $_GET['eigeneLottoZahlen6'];
			for ($i = 0 ; $i < sizeof($lottoZiehung) ; $i++) {
				echo "" .$eigeneZahlen[$i]. " | ";
			}
			echo "<br><br>";
			echo "<p class=''>Die Lottoziehung ergab folgende Ziehung: <br>";
			for ($i = 0 ; $i < sizeof($lottoZiehung) ; $i++) {
				echo "" .$lottoZiehung[$i]. " | ";
			}
			echo "</p>";
			
			if($eigeneZahlen == $lottoZiehung) {
				echo "<p class='erfolg'>Du hast gewonnen!</p>";
			} else {
				echo "<p class='meldung'>Du hast verloren :(</p>";
			}
		}
		
	}
	
	/**
	 * Addiert zahlen bis hundert
	 */
	function additionBisHundert() {
		echo "<h2>Zahlen Addition von 1 bis 100</h2>";
		
		# Lösung:
		$summe = 0;
		
		for($i = 1 ; $i <= 100 ; $i++) {
			$summe = $summe + $i;
		}
		
		echo "<p class='info'>Die L&ouml;sung ist: " . $summe . "</p>";
	}
}

?>
<link href="/flatnet2/css/style.css" type="text/css" rel="stylesheet" />

<h1>&Uuml;bungen</h1>
<p>
<a href='?werfen' class='buttonlink'>W&uuml;rfelspiel</a>
<a href='?lotto' class='buttonlink'>Lotto</a>
<a href='?lottoErweitert' class='buttonlink'>Erweitertes Lotto</a>
<a href='?zahlenBisHundert' class='buttonlink'>Z&auml;hle bis 100</a>
</p>
<?php 
# Klasse initialisieren
$lotto = NEW lotto;

# Zahlen bis 100
if(isset($_GET['zahlenBisHundert'])) {
	$lotto->additionBisHundert();
}

# Lottospiel
if(isset($_GET['lotto'])) {
	$lotto->einfachesLotto();
}

if(isset($_GET['lottoErweitert'])) {
	$lotto->mainfunction();
}

# Würfelspiel
if(isset($_GET['werfen'])) {
	$lotto->wuerfelWuerfe();
}
?>

</div>
</div>
</html>