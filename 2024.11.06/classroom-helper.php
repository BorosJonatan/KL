<?php

require_once('classroom-data.php');

function getData($key)
{
    return DATA[$key];
}

function generateGrades()
{
    $grades = [];
    for ($i = 0; $i < count(DATA['subjects']);$i++)
    {
        $subject = DATA['subjects'][$i];

        for ($j = 0; $j < rand(0, 5);$j++)
        {
            $grades[$subject][] = rand(1,5);
        }
    }
    return $grades;
}

function generateName()
{
    $lastname = DATA['lastnames'][rand(0, count(DATA['lastnames'])-1)];
    $gender = rand(0, 1) ? 'men' : 'women';
    $firstname = DATA['firstnames'][$gender][rand(0, count(DATA['firstnames'][$gender])-1)];
    $name = [$lastname, $firstname];
    return $name;
}

function generateStudent()
{
    $name = generateName();
    $student = 
    [
        $name[0],
        $name[1],
        [generateGrades()]
    ];
    return $student;
}

function generateClasses()
{
    $Classes = [];
    for ($i = 0; $i < count(DATA['classes']);$i++)
    {
        
        $classname = DATA['classes'][$i];
        for ($j = 0; $j < rand(10, 15);$j++)
        {
            $Classes[$classname][$j] = generateStudent();
        }
    }
    //var_dump($Classes);
    return $Classes;
}



function getFileName()
{
    $date = date("Y-m-d_hi");
    $classSelected = $_GET['class'] ?? '*';
    if ($classSelected == '*'){
        $filename = "export/" . "mind-" . $date . ".csv";
    }
    else $filename = "export/" . $classSelected ."-". $date . ".csv";
    return  $filename;
}


function saveData($filename, $data, $classSelected)
{
    if (isset($_POST["export_csv"])){


        if(!is_dir("export")){
            mkdir("export");
        }

        $subjects = getData('subjects');

        if (!file_exists($filename)){
            $file = fopen( $filename, "w");
            $encoding = chr(0xEF) . chr(0xBB) . chr(0xBF);
            fputs($file, $encoding);
            $header = ["ID","Név", "Keresztnév", "Vezetéknév", "Matek","Történelem", "Biológia", "Kémia", "Fizika", "Informatika", "Alkímia", "Csillagászat"];
            fputcsv($file, $header, ";");
                foreach ($data as $clName => $students) {
                    if ($classSelected === '*' || $clName === $classSelected) {
                        foreach ($students as $index => $student){
                            $filler = [];
                            $filler[] = "$clName-$index";

                            $filler[] = $student[0] . " " . $student[1];
                            $filler[] = $student[1];
                            $filler[] = $student[0];
                            foreach($subjects as $subject){
                                $grades = isset($student[2][0][$subject]) ? implode(',' , $student[2][0][$subject]) : "";
                                $filler[] = $grades;
                            }
                            $filler[] = "";
                            $filler[] = "";

                            $line = implode(";",$filler) . ";\n";
                            fwrite($file, $line);
                        }
                    }
                }
            fclose($file);
        }
    }
}