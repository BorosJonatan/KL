<?php

/**
 * 
 * @author Boros Jonatan
 * 
 */


require_once('classroom-helper.php');
require_once('classroom-html.php');
require_once('classroom-extra.php');
require_once('function-save.php');

showHead();

if (!isset($_SESSION['school'])) {
    $_SESSION['school'] = generateClasses();
}


if (isset($_POST['reGenerate'])){
    $_SESSION['school'] = generateClasses();
}

$school = $_SESSION['school'];
$filename = getFileName();
$classSelected = "*";
$classSelected = $_GET['class'] ?? '*';
$query = $_POST['query'] ?? 'school_averages';
$save = $_POST['save'] ?? '';


if (!isset($_GET['class'])) {
    // Alapértelmezett "Mindenki" funkció meghívása
    showAllClasses($school, '*');
    saveData($filename, $school, '*');
} elseif ($_GET['class'] == 'average') {
    showAverageMenu();
    if (!empty($_POST['query'])) {
        switch ($_POST['query']) {
            case 'school_averages':
                $data = calculateSchoolAverages($school);
                showSchoolAverages($data);
                if (!empty($_POST['save'])) {
                    switch ($_POST['save']){
                        case 'school_average':
                            saveSchoolAverages('school.csv', $data);
                            break;
                        default:
                            echo "<p>Ismeretlen</p>";
                    }
                }
                break;
            case 'class_averages':
                $classAverages = calculateClassAverages($school);
                showClassAverages($classAverages);
                if (!empty($_POST['save'])) {
                    switch ($_POST['save']){
                        case 'class_average':
                            saveClassAverages($classAverages);
                            break;
                        default:
                            echo "<p>Ismeretlen</p>";
                    }
                }
                break;
            default:
                echo "<p>Ismeretlen lekérdezés!</p>";
        }
    }
} elseif ($_GET['class'] == 'ranking') {
    showRankingMenu();
    if (!empty($_POST['query'])) {
        switch ($_POST['query']) {
            case 'school_rankings':
                $ranking = rankStudents($school);
                showSchoolRanking($ranking);
                showRankingSchoolSave();
                if (!empty($_POST['save'])) {
                    switch ($_POST['save']){
                        case 'school_ranking':
                            saveSchoolRanking($ranking);
                            break;
                        default:
                            echo "<p>Ismeretlen</p>";
                    }
                }
                break;
            case 'class_rankings':
                $ranking = rankStudents($school);
                showClassRanking($ranking);
                if (!empty($_POST['save'])) {
                    switch ($_POST['save']){
                        case 'class_ranking':
                            saveClassRanking($ranking);
                            break;
                        default:
                            echo "<p>Ismeretlen</p>";
                    }
                }
                break;
            default:
                echo "<p>Ismeretlen lekérdezés!</p>";
        }
    }
} elseif ($_GET['class'] == 'student_averages') {
    $data = getStudentAverage($school);
    showStudentAverage($data);
    if (!empty($_POST['save'])) {
        switch ($_POST['save']){
            case 'student_average':
                saveStudentAverages($data);
                break;
            default:
                echo "<p>Ismeretlen</p>";
        }
    }
} elseif ($_GET['class'] == 'worstbest'){
    $classAverages = calculateClassAverages($school);
    $results = findBestAndWorstClasses($classAverages);
    showBestAndWorstClasses($results, $classAverages);
    if (!empty($_POST['save'])) {
        switch ($_POST['save']){
            case 'best_worst_average':
                saveBestAndWorst($classAverages, $results);
                break;
            default:
                echo "<p>Ismeretlen</p>";
        }
    }
}
else {
    showAllClasses($school, $classSelected);
    saveData($filename, $school, $classSelected);
}



//session_destroy();