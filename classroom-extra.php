<?php

function calculateClassAverages($school) {
    $subjects = getData('subjects');
    $classAverages = [];

    foreach ($school as $className => $class) {
        $classTotalSum = 0;
        $classTotalCount = 0;
        $subjectAverages = [];
        foreach ($subjects as $subject) {
            $subjectSum = 0;
            $subjectCount = 0;
            foreach ($class as $student) {
                if (!empty($student[2][0][$subject])) {
                    $grades = $student[2][0][$subject];
                    $subjectSum += array_sum($grades);
                    $subjectCount += count($grades);
                }
            }
            $subjectAverages[$subject] = $subjectCount > 0 ? round($subjectSum / $subjectCount, 2) : "-";
            $classTotalSum += $subjectSum;
            $classTotalCount += $subjectCount;
        }
        $overallAverage = $classTotalCount > 0 ? round($classTotalSum / $classTotalCount, 2) : "-";
        $classAverages[$className] = [
            'overall' => $overallAverage,
            'subjects' => $subjectAverages
        ];
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

function showSchoolAverages($averages){
    echo "<h3 id='avarage_header'>Iskola szintű tantárgyi átlagok:</h3>";
    echo "<div class='table-group'>";
    echo "<table>";
    echo "<tr><th>Tantárgy</th><th>Átlag</th></tr>";
    foreach ($averages as $subject => $average){
        echo "<tr><td>" . ucfirst($subject) . "</td><td>" . $average . "</td></tr>";
    }
    echo "</table>";
    echo "</div >";
    showAverageSchoolSave();
}

function showClassAverages($classAverages){
    echo "<h3>Osztály szintű tantárgyi átlagok:</h3>";
    echo "<div class='table-container'>";
    foreach ($classAverages as $className => $avarages){
        echo "<div class='table-group'>";
        echo "<h4>$className</h4>";
        echo "<table>";
        echo "<tr><th>Tantárgy</th><th>Átlag</th></tr>";
        
        foreach ($avarages['subjects'] as $subject => $avarage){
            echo "<tr><td>" . ucfirst($subject) . "</td><td>" . $avarage . "</td><tr>";
        }
        echo "<tr><td>Overall</td><td>" . $avarages['overall'] . "</td></tr>";
        echo "</table>";
        echo "</div>";
    }
    
    echo "</div>";
    showAverageClassSave();
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
    echo "<h1>Osztályonkénti rangsor</h1>";

    foreach ($ranking['classes'] as $clName => $class) {
        echo "<h2>Osztály: $clName</h2>";
        echo "<button style='' class='toggle-btn' onclick=\"toggleVisibility('" . $clName . "class-overall')\">
        Összesített táblázat megjelenítése/elrejtése
        </button>";

        echo "<div id='" . $clName . "class-overall' class='dropdown-content hidden'>";
        echo "<h3>Összesített rangsor:</h3>";

        echo "<table>";
        echo "<tr><th>Helyezés</th><th>Név</th><th>Átlag</th></tr>";
        foreach ($class['overall'] as $i => $student) {
            $average = $student['average'] === "-" ? "-" : $student['average'];
            echo "<tr><td>" . ($i + 1) . "</td><td>{$student['name']}</td><td>{$average}</td></tr>";
        }
        echo "</table>";
        echo "</div>";
        echo "<script>
        function toggleVisibility(id) {
            const element = document.getElementById(id);
            if (element.classList.contains('hidden')) {
                element.classList.remove('hidden');
            } else {
                element.classList.add('hidden');
            }
        }
    </script>";

        echo "<div class='table-container'>";
        foreach ($class['subjects'] as $subject => $students) {
            echo "<div class='table-group'>";
            echo "<h4>" . ucfirst($subject) . "</h4>";
            echo "<table>";
            echo "<tr><th>Helyezés</th><th>Név</th><th>Átlag</th></tr>";
            foreach ($students as $i => $student) {
                $average = $student['average'] === "-" ? "-" : $student['average'];
                echo "<tr><td>" . ($i + 1) . "</td><td>{$student['name']}</td><td>{$average}</td></tr>";
            }
            echo "</table>";
            echo "</div>";
        }
        echo "</div>";
    }
    showRankingClassSave();
}

function showSchoolRanking($ranking) {
    echo "<h3>Egész iskola rangsora</h3>";
    echo "<button class='toggle-btn' onclick=\"toggleVisibility('school-overall')\">
            Összesített táblázat megjelenítése/elrejtése
        </button>";
    
    // Összesített rangsor megjelenítése

    echo "<div id='school-overall' class='dropdown-content hidden'>";
    echo "<h4>Összesített rangsor</h4>";
    echo "<table>";
    echo "<tr><th>Helyezés</th><th>Név</th><th>Átlag</th></tr>";
    foreach ($ranking['school']['overall'] as $i => $student) {
        echo "<tr><td>" . ($i + 1) . "</td><td>" . htmlspecialchars($student['name']) . "</td><td>" . $student['average'] . "</td></tr>";
    }
    echo "</table>";
    echo "</div>";
    echo "<script>
    function toggleVisibility(id) {
        const element = document.getElementById(id);
        if (element.classList.contains('hidden')) {
            element.classList.remove('hidden');
        } else {
            element.classList.add('hidden');
        }
    }
</script>";
    echo "<div class='table-container'>";

    // Tantárgyankénti rangsor megjelenítése
    foreach ($ranking['school']['subjects'] as $subject => $students) {
        echo "<div class='table-group'>";
        echo "<h4>" . ucfirst($subject) . "</h4>";
        echo "<table>";
        echo "<tr><th>Helyezés</th><th>Név</th><th>Átlag</th></tr>";
        foreach ($students as $i => $student) {
            $averageDisplay = $student['average'] === null ? "-" : $student['average'];
            echo "<tr><td>" . ($i + 1) . "</td><td>" . htmlspecialchars($student['name']) . "</td><td>" . $averageDisplay . "</td></tr>";
        }
        echo "</table>";
        echo "</div>";
    }
    echo "</div>";

}

function getStudentAverage($school){
    $subjects = getData('subjects');
    $output = [];

    foreach($school as $class){

        foreach ($class as $student) {
            $name = $student[0] . " " . $student[1];
            $studentAverages = [];
 
            foreach ($subjects as $subject) {
                if (!empty($student[2][0][$subject])) {
                    $grades = $student[2][0][$subject];
                    $average = round(array_sum($grades) / count($grades), 2);
                }
                else {
                    $average = "-";
                }
                $studentAverages[$subject] = $average;
            }
            $output[] = [
                'name' => $name,
                'average' => $studentAverages
            ];
        }
    }
    return $output;
}

function showStudentAverage($averages){
    $subjects = getData('subjects');
    echo "<h4 style='margin-bottom:20px;margin-top:20px;'>Tanulók átlagai</h4>";
    echo "<table>";
    echo "<tr><th>Név</th>";
    foreach($subjects as $subject){
        echo "<th>{$subject}</th>";
    }
    echo "</tr>";
    foreach($averages as $student){
        echo "<tr><td>" . htmlspecialchars($student['name']) . "</td>";
        foreach ($subjects as $subject){
            echo "<td>" . htmlspecialchars($student['average'][$subject]) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
    showStudentAveragesSave();
}

function findBestAndWorstClasses($classAverages) {
    $subjects = getData('subjects');
    $results = [
        'overall' => [
            'best' => null,
            'worst' => null,
        ],
        'subjects' => []
    ];

    foreach ($subjects as $subject) {
        $results['subjects'][$subject] = [
            'best' => null,
            'worst' => null,
        ];
    }

    foreach ($classAverages as $className => $averages) {
        // Összesített átlag
        if ($averages['overall'] !== "-") {
            if (
                !$results['overall']['best'] || 
                $averages['overall'] > $classAverages[$results['overall']['best']]['overall']
            ) {
                $results['overall']['best'] = $className;
            }
            if (
                !$results['overall']['worst'] || 
                $averages['overall'] < $classAverages[$results['overall']['worst']]['overall']
            ) {
                $results['overall']['worst'] = $className;
            }
        }

        // Tantárgyankénti átlagok
        foreach ($subjects as $subject) {
            if ($averages['subjects'][$subject] !== "-") {
                if (
                    !$results['subjects'][$subject]['best'] || 
                    $averages['subjects'][$subject] > $classAverages[$results['subjects'][$subject]['best']]['subjects'][$subject]
                ) {
                    $results['subjects'][$subject]['best'] = $className;
                }
                if (
                    !$results['subjects'][$subject]['worst'] || 
                    $averages['subjects'][$subject] < $classAverages[$results['subjects'][$subject]['worst']]['subjects'][$subject]
                ) {
                    $results['subjects'][$subject]['worst'] = $className;
                }
            }
        }
    }

    return $results;
}


function showBestAndWorstClasses($school, $classAverages) {
    $results = findBestAndWorstClasses($classAverages);
    $subjects = getData('subjects');

    echo "<h1 class='bestworst-header'>Legjobb és leggyengébb osztályok</h1>";

    // Összesített eredmények
    echo "<p id='bestworstheader'>Összesített</p>";
    echo "<table>";
    echo "<tr><th>Típus</th><th>Osztály</th><th>Átlag</th></tr>";
    echo "<tr><td>Legjobb</td><td>" . $results['overall']['best'] . "</td><td>" . $classAverages[$results['overall']['best']]['overall'] . "</td></tr>";
    echo "<tr><td>Leggyengébb</td><td>" . $results['overall']['worst'] . "</td><td>" . $classAverages[$results['overall']['worst']]['overall'] . "</td></tr>";
    echo "</table>";
    echo "<div class='table-container'>";
    

    // Tantárgyankénti eredmények
    foreach ($subjects as $subject) {
        echo "<div class='table-group'>";
        echo "<p id='bestworstsubj'>". ucfirst($subject) . "</p>";
        echo "<table>";
        echo "<tr><th>Típus</th><th>Osztály</th><th>Átlag</th></tr>";
        echo "<tr><td>Legjobb</td><td>" . $results['subjects'][$subject]['best'] . "</td><td>" . $classAverages[$results['subjects'][$subject]['best']]['subjects'][$subject] . "</td></tr>";
        echo "<tr><td>Leggyengébb</td><td>" . $results['subjects'][$subject]['worst'] . "</td><td>" . $classAverages[$results['subjects'][$subject]['worst']]['subjects'][$subject] . "</td></tr>";
        echo "</table>";
        echo "</div>";
    }
    echo "</div>";
    showBestAndWorstSave();
}
