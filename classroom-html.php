<?php
session_start();

require_once('classroom-helper.php');

function showHead()
{
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="style.css">
        <script src="averageButtonScript.js"></script>
        <title>Document</title>
    <body>
        <a href="?class=*"><button id="a" type="button">Mindenki</button></a>
        <a href="?class=11a"><button id="a" type="button">11.a</button></a>
        <a href="?class=11b"><button id="a" type="button">11.b</button></a>
        <a href="?class=11c"><button id="a" type="button">11.c</button></a>
        <a href="?class=12a"><button id="a" type="button">12.a</button></a>
        <a href="?class=12b"><button id="a" type="button">12.b</button></a>
        <a href="?class=12c"><button id="a" type="button">12.c</button></a>
        <a href="?class=average"><button id="a" type="button">Átlagok</button></a>
        <a href="?class=ranking"><button id="a" type="button">Ranking</button></a>
        <a href="?class=student_averages"><button id="a" type="button">Student averages</button></a>   
        <a href="?class=worstbest"><button id="a" type="button">Legjobb-legrosszabb osztályok</button></a>

        
    </body>
    </head>';
}

function showNormalSaveButton(){
    echo '<form method="POST"><button  id="a" type="submit" name="export_csv">Mentés</button></form>';
    echo '<form method="POST"><button  id="a" type="submit" name="reGenerate">Újra generálás</button></form>';
}

//subject averages save buttons
function showAverageSchoolSave(){
    echo '<form method="POST">
    <input hidden="true" name="query" value="school_averages">
    <button id="a" type="submit" name="save" value="school_average">Mentés</button>
    </form>';
}

function showAverageClassSave(){
    echo '<form method="POST"><input hidden="true" name="query" value="class_averages"><button id="a" name="save" type="submit" value="class_average">Mentés</button></form>';
}

//ranking save buttons
function showRankingSchoolSave(){
    echo '<form method="POST"><input hidden="true" name="query" value="school_rankings"><button id="a" name="save" type="submit" value="school_ranking">Mentés</button></form>';
}

function showRankingClassSave(){
    echo '<form method="POST"><input hidden="true" name="query" value="class_rankings"><button id="a" name="save" type="submit" value="class_ranking">Mentés</button></form>';
}

//student averages save button
function showStudentAveragesSave(){
    echo '<form method="POST"><button id="a" name="save" type="submit" value="student_average">Mentés</button><form>';
}

function showBestAndWorstSave(){
    echo '<form method="POST"><button id="a" name="save" type="submit" value="best_worst_average">Mentés</button><form>';
}


function showAverageMenu() {
    echo "<hr>";
    echo '
    <form method="POST" style="text-align:center; margin: 20px 0;">
        <button id="a" type="submit" name="query" value="school_averages">Iskolai átlag</button>
        <button id="a" type="submit" name="query" value="class_averages">Osztály átlag</button>
    </form>
    ';
}

function showRankingMenu() {
    echo "<hr>";
    echo '
    <form method="POST" style="text-align:center; margin: 20px 0;">
        <button id="a" type="submit" name="query" value="school_rankings">Iskolai rangsor</button>
        <button id="a" type="submit" name="query" value="class_rankings">Osztály rangsor</button>
    </form>
    ';
}



function showAllClasses($school, $classSelected)
{
    $subjects = getData('subjects');
    echo '<body>';
    foreach ($school as $clName => $students) {
        if ($classSelected === '*' || $classSelected === $clName) {

            echo "<table>";
            echo "<caption>$clName</caption>";
            echo "<tr><th>Név</th>";

            foreach ($subjects as $subject) {
                echo "<th>" . ucfirst($subject) . "</th>";
            }
            echo "</tr>";
            foreach ($students as $student) {
                echo "<tr>";
                echo "<td id='nev_oszlop'>" . $student[0] . " " . $student[1] . "</td>";
                foreach ($subjects as $subject) {
                    $grades = isset($student[2][0][$subject]) ? implode(", ", $student[2][0][$subject]) : '-';
                    echo "<td>" . $grades . "</td>";
                }
                echo "</tr>";
            }
        }
        echo "</table>";
    }
    echo "</body></html>";
    showNormalSaveButton();
}

