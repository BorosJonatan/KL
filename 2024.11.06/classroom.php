<?php

/**
 * 
 * @author Boros Jonatan
 * 
 */


require_once('classroom-helper.php');
require_once('classroom-html.php');
require_once('classroom-extra.php');

showHead();

if (!isset($_SESSION['school'])) {
    $_SESSION['school'] = generateClasses();
}


if (isset($_POST['reGenerate'])){
    $_SESSION['school'] = generateClasses();
}

$school = $_SESSION['school'];
$filename = getFileName();
$classSelected = $_GET['class'] ?? '*';
$query = $_POST['query'] ?? 'school_averages';


if (isset($_GET['class'])){
    if ($_GET['class'] == 'average'){
        showAverageMenu();
        if (!empty($_POST['query'])){
            switch ($_POST['query']) {
                case 'school_averages':
                    showSchoolAvarages($school);
                    break;
                case 'class_averages':
                    showClassAvarages($school);
                    break;
                /*case 'best_worst_classes':
                    showBestWorstClasses($school);
                    break;*/
                default:
                    echo "<p>Ismeretlen lekérdezés!</p>";
            }
        }
    }
    elseif ($_GET["class"] == "ranking"){
        showRankingMenu();
        if (!empty($_POST['query'])){
            switch ($_POST['query']) {
                case 'school_rankings':
                    $ranking = rankStudents($school);
                    showSchoolRanking($ranking);
                    break;
                case 'class_rankings':
                    $ranking = rankStudents($school);
                    showClassRanking($ranking);
                    break;
                default:
                    echo "<p>Ismeretlen lekérdezés!</p>";
            }
        }
    }
    else {
        showAllClasses($school, $classSelected);
        showButton();
        saveData($filename, $school, $classSelected);
    }
}



//session_destroy();