<?php
header('Access-Control-Allow-Methods: POST, GET, DELETE');

$url     = $_GET['url'];
$datearr = array();

if ($url == 'historical/') {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $requestData = file_get_contents('php://input');
        $obj         = json_decode($requestData);
        $addData     = $obj->DATE . ',' . $obj->TMAX . ',' . $obj->TMIN;
        $addData     = trim($addData, ',');
        $addData     = trim($addData);
        if (!empty($addData)) {
            $myfile = fopen("daily.csv", "a") or die("Unable to open file!");
            fwrite($myfile, PHP_EOL . $addData);
            $datearr['DATE'] = $obj->DATE;
            if (substr($sapi_type, 0, 3) == 'cgi')
                header("Status: 201 Success");
            else
                header("HTTP/1.1 201 Success");
            echo json_encode($datearr);
            fclose($myfile);
        }
    }
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $myfile = fopen("daily.csv", "r") or die("Unable to open file!");
        while (!feof($myfile)) {
            $datestr           = fgets($myfile);
            $arr               = explode(',', $datestr);
            $arr[0] = trim($arr[0]);
            if(!empty($arr[0]))
                $datearr[]['DATE'] = $arr[0];
        }
        echo json_encode($datearr);
        fclose($myfile);
    }
}

if (preg_match("/^historical\/[0-9]{8}$/", trim($url))) {
    $dateUrl = explode('/', $url);
    $date    = $dateUrl[1];
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $flag = 0;
        $myfile = fopen("daily.csv", "r") or die("Unable to open file!");
        while (!feof($myfile)) {
            $datestr = fgets($myfile);
            $arr     = explode(',', $datestr);
            if ($arr[0] == $date) {
                $datearr["DATE"] = trim($arr[0]);
                $datearr["TMAX"] = trim($arr[1]);
                $datearr["TMIN"] = trim($arr[2]);
                $flag            = 1;
                break;
            }
        }
        if ($flag == 1) {
            echo json_encode($datearr);
        } else {
            $sapi_type = php_sapi_name();
            if (substr($sapi_type, 0, 3) == 'cgi')
                header("Status: 404 Not Found");
            else
                header("HTTP/1.1 404 Not Found");
            echo json_encode('404:entry not found');
        }
        fclose($myfile);
    }
    
    if ($_SERVER["REQUEST_METHOD"] == "DELETE") {
        $flag = 0;
        $out  = array();
        $myfile = fopen("daily.csv", "r") or die("Unable to open file!");
        while (!feof($myfile)) {
            $datestr = fgets($myfile);
            $arr     = explode(',', $datestr);
            if ($arr[0] != $date) {
                $out[] = $datestr;
            } else {
                $flag = 1;
            }
        }
        fclose($myfile);
        if ($flag == 1) {
            $myfile = fopen("daily.csv", "w") or die("Unable to open file!");
            foreach ($out as $line) {
                fwrite($myfile, $line);
            }
            fclose($myfile);
        }
        echo json_encode('200:Success');
    }
}

if (preg_match("/forecast\/[0-9]{8}$/", trim($url))) {
    $forecast = 0;
    $dateUrl  = explode('/', $url);
    $date     = $dateUrl[1];
    $myfile = fopen("daily.csv", "r") or die("Unable to open file!");
    // Output one line until end-of-file    
    while (!feof($myfile)) {
        $datestr = fgets($myfile);
        $arr     = explode(',', $datestr);
        if ($forecast == 7) {
            break;
        }
        if ($arr[0] == $date) {
            $datearr[$forecast]["DATE"] = trim($arr[0]);
            $datearr[$forecast]["TMAX"] = trim($arr[1]);
            $datearr[$forecast]["TMIN"] = trim($arr[2]);
            $date                       = getNextDate($date);
            $forecast += 1;
        }
    }
    if ($forecast < 7) {
        for ($i = 0; $i < 7; $i++) {
            if (!$datearr[$i]) {
                $datearr[$i]["DATE"] = trim($date);
                $datearr[$i]["TMAX"] = trim(25.1 + $i + $i / 10);
                $datearr[$i]["TMIN"] = trim(12.3 + $i + $i / 10);
                $date                = getNextDate($date);
            }
        }
    }
    echo json_encode($datearr);
    fclose($myfile);
}

function getNextDate($date)
{
    $year  = substr($date, 0, 4);
    $month = substr($date, 4, 2);
    $day   = substr($date, 6, 2);
    if (checkdate($month, $day + 1, $year)) {
        $day  = (str_pad($day + 1, 2, '0', STR_PAD_LEFT));
        $date = $year . $month . $day;
    } elseif (checkdate($month + 1, 1, $year)) {
        $month = (str_pad($month + 1, 2, '0', STR_PAD_LEFT));
        $date  = $year . $month . '01';
    } else {
        $date = ($year + 1) . '01' . '01';
    }
    return $date;
}
?>
