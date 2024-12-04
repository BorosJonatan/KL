<?php

function calculateClassAverages($school)
{
    $subjects = getData('subjects');
    $classAverages = [];
    foreach ($school as $className => $class) {
        $averages = [];
        foreach ($subjects as $subject) {
            $totalGrades = 0;
            $countGrades = 0;
            foreach ($class as $student) {
                if (isset($student[2][0][$subject])) {
                    $grades = $student[2][0][$subject];
                    $totalGrades += array_sum($grades);
                    $countGrades += count($grades);
                }
            }
            $averages[$subject] = $countGrades > 0 ? round($totalGrades / $countGrades, 2) : 0;
        }
        $classAverages[$className] = $averages;
    }
    return $classAverages;
}
function calculateSchoolAverages($school)
{
    $subjects = getData('subjects');
    $Averages = [];
        foreach ($subjects as $subject) {
            $totalGrades = 0;
            $countGrades = 0;
            foreach ($school as $class) {
                foreach ($class as $student) {
                    if (isset($student[2][0][$subject])) {
                        $grades = $student[2][0][$subject];
                        $totalGrades += array_sum($grades);
                        $countGrades += count($grades);
                    }
                }
        }
        $Averages[$subject] = $countGrades > 0 ? round($totalGrades / $countGrades, 2) : 0;
    }
    return $Averages;
}

function showSchoolAvarages($school){
    $avarages = calculateSchoolAverages($school);
    echo "<h3 id='avarage_header'>Iskola szintű tantárgyi átlagok:</h3>";
    echo "<table border='1'>";
    echo "<tr><th>Tantárgy</th><th>Átlag</th></tr>";
    foreach ($avarages as $subject => $avarage){
        echo "<tr><td>" . ucfirst($subject) . "</td><td>" . $avarage . "</td></tr>";
    }
    echo "</table>";
}
function showClassAvarages($school){
    $classAvarages = calculateClassAverages($school);
    echo "<h3>Osztály szintű tantárgyi átlagok:</h3>";
    foreach ($classAvarages as $className => $avarages){
        echo "<h4>$className</h4>";
        echo "<table>";
        echo "<tr><th>Tantárgy</th><th>Átlag</th></tr>";
        foreach ($avarages as $subject => $avarage){
            echo "<tr><td>" . ucfirst($subject) . "</td><td>" . $avarage . "</td><tr>";
        }
        echo "</table>";
    }
}


function sortRanking(&$ranking) {
    usort($ranking, function($a, $b) {
        return $b['average'] <=> $a['average'];
    });
}

function rankStudents($school) {
    $subjects = getData('subjects');
    $ranking = [
        'school' => [
            'overall' => [],
            'subjects' => [],
        ],
        'classes' => []
    ];
 
    foreach ($school as $clName => $class) {
        $ranking['classes'][$clName] = [
            'overall' => [],
            'subjects' => []
        ];
        foreach ($subjects as $subject) {
            $ranking['classes'][$clName]['subjects'][$subject] = [];
        }
 
        foreach ($class as $student) {
            $name = $student[0] . " " . $student[1];
            $overallSum = 0;
            $overallCount = 0;
 
            foreach ($subjects as $subject) {
                if (!empty($student[2][0][$subject])) {
                    $grades = $student[2][0][$subject];
                    $average = round(array_sum($grades) / count($grades), 2);
                } else {
                    $average = "-";
                }
 
                // Tantárgyi rangsorhoz hozzáadás (osztály szinten)
                $ranking['classes'][$clName]['subjects'][$subject][] = [
                    'name' => $name,
                    'average' => $average
                ];
 
                // Tantárgyi rangsorhoz hozzáadás (iskola szinten)
                $ranking['school']['subjects'][$subject][] = [
                    'name' => $name,
                    'average' => $average
                ];
 
                if ($average !== "-") {
                    $overallSum += array_sum($grades);
                    $overallCount += count($grades);
                }
            }
 
            // Összesített átlag számítása
            if ($overallCount > 0) {
                $overallAverage = round($overallSum / $overallCount, 2);
            } else {
                $overallAverage = "-";
            }
 
            // Összesített rangsorhoz hozzáadás (osztály szinten)
            $ranking['classes'][$clName]['overall'][] = [
                'name' => $name,
                'average' => $overallAverage
            ];
 
            // Összesített rangsorhoz hozzáadás (iskola szinten)
            $ranking['school']['overall'][] = [
                'name' => $name,
                'average' => $overallAverage
            ];
        }
    }
 
    // Iskola és osztály rangsorok rendezése
    sortRanking($ranking['school']['overall']);
    foreach ($ranking['school']['subjects'] as &$students) {
        sortRanking($students);
    }
    foreach ($ranking['classes'] as &$class) {
        sortRanking($class['overall']);
        foreach ($class['subjects'] as &$students) {
            sortRanking($students);
        }
    }
 
    return $ranking;
}

function showClassRanking($ranking) {
    echo "<h3>Osztályonkénti rangsor</h3>";
    foreach ($ranking['classes'] as $clName => $class) {
        echo "<h4>Osztály: $clName</h4>";
 
        echo "<h5>Összesített rangsor:</h5>";
        echo "<table border='1'>";
        echo "<tr><th>Helyezés</th><th>Név</th><th>Átlag</th></tr>";
        foreach ($class['overall'] as $i => $student) {
            $average = $student['average'] === "-" ? "-" : $student['average'];
            echo "<tr><td>" . ($i + 1) . "</td><td>{$student['name']}</td><td>{$average}</td></tr>";
        }
        echo "</table>";
 
        foreach ($class['subjects'] as $subject => $students) {
            echo "<h5>" . ucfirst($subject) . "</h5>";
            echo "<table border='1'>";
            echo "<tr><th>Helyezés</th><th>Név</th><th>Átlag</th></tr>";
            foreach ($students as $i => $student) {
                $average = $student['average'] === "-" ? "-" : $student['average'];
                echo "<tr><td>" . ($i + 1) . "</td><td>{$student['name']}</td><td>{$average}</td></tr>";
            }
            echo "</table>";
        }
    }
}

function showSchoolRanking($ranking) {
    echo "<h3>Iskola rangsor</h3>";
    
    // Összesített rangsor megjelenítése
    echo "<h4>Összesített rangsor</h4>";
    echo "<table border='1'>";
    echo "<tr><th>Helyezés</th><th>Név</th><th>Átlag</th></tr>";
    foreach ($ranking['school']['overall'] as $i => $student) {
        echo "<tr><td>" . ($i + 1) . "</td><td>" . htmlspecialchars($student['name']) . "</td><td>" . $student['average'] . "</td></tr>";
    }
    echo "</table>";
 
    // Tantárgyankénti rangsor megjelenítése
    foreach ($ranking['school']['subjects'] as $subject => $students) {
        echo "<h4>" . ucfirst($subject) . " tantárgy rangsora</h4>";
        echo "<table border='1'>";
        echo "<tr><th>Helyezés</th><th>Név</th><th>Átlag</th></tr>";
        foreach ($students as $i => $student) {
            $averageDisplay = $student['average'] === null ? "-" : $student['average'];
            echo "<tr><td>" . ($i + 1) . "</td><td>" . htmlspecialchars($student['name']) . "</td><td>" . $averageDisplay . "</td></tr>";
        }
        echo "</table>";
    }
}