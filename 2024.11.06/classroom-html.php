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
        <title>Document</title>
    <body>
        <a href="?class=*"><button type="button">Mindenki</button></a>
        <a href="?class=11a"><button type="button">11.a</button></a>
        <a href="?class=11b"><button type="button">11.b</button></a>
        <a href="?class=11c"><button type="button">11.c</button></a>
        <a href="?class=12a"><button type="button">12.a</button></a>
        <a href="?class=12b"><button type="button">12.b</button></a>
        <a href="?class=12c"><button type="button">12.c</button></a>
        <a href="?class=average"><button type="button">Átlagok</button></a>
        <a href="?class=ranking"><button type="button">Ranking</button></a>
        
    </body>
    </head>';
}

function showButton(){
    echo '<form method="POST"><button id="save" type="submit" name="export_csv">Mentés</button><form>';
    echo '<form method="POST"><button id="save" type="submit" name="reGenerate">Újra generálás</button><form>';

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
}

function showAverageMenu() {
    echo "<hr>";
    echo '
    <form method="POST" style="text-align:center; margin: 20px 0;">
        <button type="submit" name="query" value="school_averages">Iskolai átlag</button>
        <button type="submit" name="query" value="class_averages">Osztály átlag</button>
    </form>
    ';
}

function showRankingMenu() {
    echo "<hr>";
    echo '
    <form method="POST" style="text-align:center; margin: 20px 0;">
        <button type="submit" name="query" value="school_rankings">Iskolai rangsor</button>
        <button type="submit" name="query" value="class_rankings">Osztály rangsor</button>
    </form>
    ';
}