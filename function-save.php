<?php

require_once('classroom-html.php');
require_once('classroom-extra.php');
require_once('classroom-helper.php');
require_once('classroom.php');

function encoding() {
    return chr(0xEF) . chr(0xBB) . chr(0xBF); 
}

function makeFile($directory, $filename){
    $exportmappapath = __DIR__ . "\\export\\" . $directory;
    if (!is_dir($exportmappapath)) {
        mkdir($exportmappapath);
    }
    $filePath = $exportmappapath . "\\" . $filename;

    return $file = fopen($filePath, "w");
}

function saveSchoolAverages($filename, $data) {
    $file = makeFile('averages', $filename);
    fputs($file, encoding());
    $header = ["Tantárgy", "Átlag"];
    fputcsv($file, $header, ";");

    foreach ($data as $subject => $average) {
        $line = [$subject, $average];
        fputcsv($file, $line, ";");
    }
    fclose($file);
}

function saveClassAverages($data){
    foreach ($data as $className => $avarages){
        $file = makeFile('averages', $className.".csv");
        fputs($file, encoding());
        fputcsv($file, ["Tantárgy", "Átlag"], ";");

        foreach ($avarages['subjects'] as $subject => $average){
            fputcsv($file, [$subject, $average], ";");
        }
        fclose($file);
    }
}

function saveSchoolRanking($ranking) {

    $file = makeFile('ranking', 'overall.csv');
    fputs($file, encoding());
    fputcsv($file, ["Helyezés", "Név", "Átlag"], ";");
    foreach ($ranking['school']['overall'] as $i => $student) {
        fputcsv($file, [$i+1, $student['name'], $student['average']], ";");
    }
    fclose($file);
    foreach ($ranking['school']['subjects'] as $subject => $students) {
        $file = makeFile('ranking', $subject . ".csv");
        fputs($file, encoding());
        fputcsv($file, ['Helyzés', 'Név', 'Átlag'], ';');
        foreach ($students as $i => $student) {
            $averageDisplay = $student['average'] === null ? "-" : $student['average'];
            fputcsv($file, [$i+1, $student['name'], $averageDisplay], ';');
        }
        fclose($file);
    }
}

function saveClassRanking($ranking){
    foreach ($ranking['classes'] as $clName => $class){
        $file = makeFile('ranking', $clName . '_overall.csv');
        fputs($file, encoding());
        fputcsv($file, ["Helyezés", "Név", "Átlag"], ";");
        foreach ($ranking['school']['overall'] as $i => $student) {
            fputcsv($file, [$i+1, $student['name'], $student['average']], ";");
        }
        fclose($file);
        foreach ($ranking['school']['subjects'] as $subject => $students) {
            $file = makeFile('ranking', $clName . "_" . $subject . ".csv");
            fputs($file, encoding());
            fputcsv($file, ['Helyzés', 'Név', 'Átlag'], ';');
            foreach ($students as $i => $student) {
                $averageDisplay = $student['average'] === null ? "-" : $student['average'];
                fputcsv($file, [$i+1, $student['name'], $averageDisplay], ';');
            }
            fclose($file);
        }
    }
}

function saveStudentAverages($data){
    
}