<?php
include 'objekt/functions.class.php';
class quiz extends functions {
    
    /**
     * Navigation
     */
    function showNavigation() {
        $query = "SELECT * FROM quiz_themen";
        $themen=$this->getObjektInfo($query);
        
        echo "<div class='finanzNAV'><ul>";
        if(isset($_GET['themeid'])) {
            $themeid = $_GET['themeid'];
        } else {
            $themeid = 0;
        }
        echo "<li><a href='?neueFrage&themeid=$themeid'>Neue Frage</a></li>";
        for($i = 0 ; $i < sizeof($themen) ; $i++) {
            echo "<li><a href='?themeid=".$themen[$i]->id."'>".$themen[$i]->name."</a></li>";
        }
        echo "</ul></div>";
        
    }
    
    /**
     * Hauptfunktion
     */
    function mainQuizFunction () {
        $this->newFrage();
        $this->editFrage();
        $this->deleteFrage();
        
        # Fragen anzeigen
        $this->showQuestions();
    }
    
    /**
     * Zeigt die Fragen zu dem Thema an.
     */
    function showQuestions() {
        if(isset($_GET['themeid'])) {
            if(is_numeric($_GET['themeid'])) {
                
                $thema = $_GET['themeid'];
                $query = "SELECT * FROM quiz_fragen WHERE thema_id=$thema";
                $fragen=$this->getObjektInfo($query);
                
                for ($i = 0 ; $i < sizeof($fragen) ; $i++) {
                    echo "<div class='newCharWIDE'>";
                        echo "<h2>" .$fragen[$i]->frage_text. "</h2>";
                        echo "<form method=post>";
                        $frageid = $fragen[$i]->id;
                        $antwortquery = "SELECT * FROM quiz_antworten WHERE frage_id=$frageid";
                        $antworten=$this->getObjektInfo($antwortquery);
                        $checked = "";
                        echo "<ul>";
                        echo "<input type=number name=frageid value='$frageid' hidden />";
                        for ($j = 0 ; $j < sizeof($antworten) ; $j++) {
                            echo "<input type=checkbox name=answer[$j] value='".$antworten[$j]->id."' $checked />";
                            echo "".$antworten[$j]->antwort_text."<br>";
                        }
                        
                        echo "</ul>";
                        echo "<input type=submit />";
                        echo "</form>";
                    echo "</div>";
                }
                echo "<div class='newCharWIDE'>";
                if(isset($_POST['answer']) AND isset($_POST['frageid'])) {
                    $answers = $_POST['answer'];
                    $frageid = $_POST['frageid'];
                    $this->checkAnswers($answers, $frageid);
                }
                echo "</div>";
            }
        }
    }
    
    /**
     * Pr�ft ob die Antworten richtig sind.
     * @param unknown $antworten
     * @param unknown $frageid
     */
    function checkAnswers($antworten, $frageid) {
        
        $besitzer = $this->getUserID($_SESSION['username']);
        $counter = 0;
        
        foreach ($antworten as $antwort) {
            
            $trueorfalse=$this->getObjektInfo("SELECT * FROM quiz_antworten WHERE id=$antwort LIMIT 1");
            
            if($trueorfalse[0]->true_or_false == 1) {
                echo "<li>" . $trueorfalse[0]->antwort_text . " ist richtig</li>";
                
            } else {
                echo "<li>" . $trueorfalse[0]->antwort_text . " ist falsch</li>";
                $counter = $counter + 1;
                
            }
        }
        
        if($counter > 0) {
            echo "<p>Frage wurde falsch beantwortet</p>";
            $this->saveFortschritt($frageid, $besitzer, "falsch");
        } else {
            echo "<p>Frage wurde richtig beantwortet</p>";
            $this->saveFortschritt($frageid, $besitzer, "richtig");
        }
        
    }
    
    /**
     * Speichert den Fortschritt in der quiz_fortschritt Relation.
     * @param unknown $frageid
     * @param unknown $besitzer
     * @param unknown $wert
     * @return boolean
     */
    function saveFortschritt($frageid, $besitzer, $wert) {
        
        $getFortschritt = $this->getObjektInfo("SELECT * FROM quiz_fortschritt WHERE frage_id=$frageid AND besitzer=$besitzer LIMIT 1");
        
        if(!isset($getFortschritt[0]->id)) {
            $query="INSERT INTO quiz_fortschritt (besitzer, frage_id, richtig, falsch) VALUES ($besitzer, $frageid, 1, 0)";
            if($this->sql_insert_update_delete($query) == true) {
                return true;
            } else {
                return false;
            }
        } else {
            if($wert == "richtig") {
                $alterRichtigWert = $getFortschritt[0]->richtig;
                $neu = $alterRichtigWert + 1;
                
                if($this->sql_insert_update_delete("UPDATE quiz_fortschritt SET richtig=$neu WHERE frage_id=$frageid AND besitzer=$besitzer") == true) {
                    
                } else {
                    echo "update konnte nicht gespeichert werden";
                }
            }
            if($wert == "falsch") {
                $alterFalschWert = $getFortschritt[0]->falsch;
                $neu=$alterFalschWert + 1;
                
                if($this->sql_insert_update_delete("UPDATE quiz_fortschritt SET falsch=$neu WHERE frage_id=$frageid AND besitzer=$besitzer") == true) {
                    
                } else {
                    echo "update konnte nicht gespeichert werden";
                }
            }
        }
        
        
        
    }
    
    function newTheme() {
        
    }
    
    function editTheme() {
        
    }
    
    function deleteTheme() {
        
    }
    
    function newFrage() {
        if(isset($_GET['neueFrage'])) {
            echo "<div>";
            
                if(isset($_POST['newFrageSubmit'])) {
                    $fragetext=$_POST['fragetext'];
                    $antworten = $_POST['antwort'];
                    $richtig = $_POST['richtig'];
                    
                    
                    
                }
                echo "<h2>Neue Frage erstellen</h2>";
                echo "<p>Die richtigen markieren</p>";
                echo "<form method=post>";
                echo "<p><input type=text name=fragetext placeholder='Fragetext'/> </p>";
                
                $antwortenzahl = 5;
                for($i = 0 ; $i < $antwortenzahl ; $i++) {
                    echo "<input type=text name=antwort[$i] placeholder='Antwort $i' /> <input type=checkbox value=1 name=richtig[$i] /> <br>";
                }
                
                echo "<input type=submit name=newFrageSubmit />";
                echo "</form>";
            echo "</div>";
        }
    }
    
    function editFrage() {
        if(isset($_GET['editFrage'])) {
            
        }
    }
    
    function deleteFrage() {
        if(isset($_GET['deleteFrage'])) {
            
        }
    }
}

?>