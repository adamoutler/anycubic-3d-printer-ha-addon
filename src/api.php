<?php

/* global lock file for API use prevents multiple API calls at the same time */
$LOCKFILE = sys_get_temp_dir() . '/monox-api';
$DEBUG=false;
set_time_limit(4);

/**
 * Apply a lock so that other components will not be able to interfere.
 */
function lockAPI() {
    global $LOCKFILE;

    $contents = readLockFile();
    while (strlen($contents) > 1){  // While the lock is present
        $contents = readLockFile();
        if (strlen($contents) <=1){  // If lock becomes unlocked, proceed
            break;
        }
        if (time()-$contents > 15){ // If time exceeds 15 seconds unlock and proceed
            unlockAPI();
            break;
        }
        sleep(1);                    // Otherwise sleep for a second and try again
    }
    writeLockFile(time());
    register_shutdown_function('unlockAPI');
}

/**
 * Read the lock file and return the contents
 */
function readLockFile(){
    global $LOCKFILE;
    if (! file_exists($LOCKFILE)){
        return 0;
    }
    $handle = fopen($LOCKFILE, "r");
    while (!$handle){  //keep trying until we can get a lock on the file.
        $handle = fopen($LOCKFILE, "r");
    }
    if (filesize($LOCKFILE)==0){
        fclose($handle);
        return 0;
    }
    $contents = fread($handle, filesize($LOCKFILE));
    fclose($handle);
    return $contents;
}

function writeLockFile(string $value){
    global $LOCKFILE;
    $mylock= fopen( $LOCKFILE, "w");
    while (!$mylock){
        $mylock= fopen( $LOCKFILE, "w");
    }
    fwrite($mylock, $value);
    fclose($mylock);
}

/**
 * Unlock the API, allowing others to talk to the API.
 */
function unlockAPI() {
    writeLockFile(0);
    
}

function __destruct() {
    unlockAPI();
    echo 'Destruct: ' . __METHOD__ . '()' . PHP_EOL;
}
if ($_SERVER['QUERY_STRING']==NULL ){
    return;
}
parse_str($_SERVER['QUERY_STRING'], $_GET);
/* debug*/
if (!isset($_GET["server"])) {
    $_GET["server"] = "192.168.1.254";
}
if (!isset($_GET["port"])) {
    $_GET["port"] = 6000;
}
if (!isset($_GET["cmd"])) {
    $_GET["cmd"] = "getstatus";
}
if (isset($_GET["debug"])){
    $DEBUG=$_GET["debug"];
}

$start_time = time();
$socket = fsockopen($_GET["server"], $_GET["port"]);
if (!defined('STDIN')) {
    define('STDIN', fopen("php://stdin", "r"));
}

if (!$socket) {
    return;
}
lockAPI();

stream_set_blocking($socket, 0);
stream_set_blocking(STDIN, 0);
if (endsWith($_GET["cmd"], ",end")) {
    fwrite($socket, trim($_GET["cmd"] . "\n"));
} else {
    fwrite($socket, trim($_GET["cmd"] . ",end\n"));
}

$read = array($socket, STDIN);
if (!is_resource($socket)) {
    return;
}

$write = NULL;
$except = NULL;
@stream_select($read, $write, $except, null);

$data = "";
function startsWith($haystack, $needle) {
    $length = strlen($needle);
    if (gettype($haystack) === "array") {
        return substr($haystack[0], 0, $length) === $needle;
    }
    return substr($haystack, 0, $length) === $needle;
}
function endsWith($haystack, $needle) {
    $length = strlen($needle);
    if (!$length) {
        return true;
    }
    return substr($haystack, -$length) === $needle;
}
$endtime = time() + 15;
while (!endsWith($data, ",end") && !endsWith($data, "\n\n") && time() < $endtime) {
    $data .= fread($socket, 1);
}
$data = trim($data);
set_time_limit(30);
fclose($socket);
unlockAPI();
if ($DEBUG){
    echo "Response time: ". (time()-$start_time)."\n";
}
$array = explode(",", $data);

$newarray = (array)null;
foreach ($array as &$value) {
    $value = trim($value);
    if (strpos($value, "/") !== false) {
        $value = explode("/", $value);
    }
    array_push($newarray, $value);
}
if (sizeof($array) == 1 && $array[0] == "") {
    return;
}
function remove_keys(int $number, $arr) {
    for ($i = 0; $i <= $number + 1; $i++) {
        unset($arr[0]);
        $arr = array_values($arr);
    }
    if (isset($arr[0]) && !is_array($arr[0]) && startsWith($arr[0], "end")) {
        $newval = substr_replace($arr[0], "", 0, 3);
        $arr[0] = $newval;
    }
    return $arr;
}

$output = (object)NULL;
$output->type = "monox";
while (sizeof($newarray) >= 1) {
    switch ($newarray[0]) {
    case ("sysinfo"):
        $output->sysinfo = (object)NULL;
        $output->sysinfo->model = $newarray[1];
        $output->sysinfo->firmware = $newarray[2];
        $output->sysinfo->serial = $newarray[3];
        $output->sysinfo->wifi = $newarray[4];
        $newarray = remove_keys(4, $newarray);

        break;
    case ("getfile"):
        unset($newarray[0]);
        $files = (array)null;
        foreach (range(1, sizeof($newarray) - 1) as $file) {
            if (startsWith($file, "end") || !is_array($newarray[$file])) {
                break;
            }
            array_push($files, $newarray[$file]);
        }
        $output->files = $files;
        $newarray = remove_keys(sizeof($files), $newarray);

        break;
    case ("getPreview1"):

        break;
    case ("getPreview2"):
        $path = "img";
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        break;
    case ("getpara"):
        $size = sizeof($newarray);
        $output->param = (object)NULL;
        $output->param->param = $newarray[1];
        
        if (startsWith($newarray[2], "end")) {
            $newarray = remove_keys(count(array_keys((array)$output->status)), $newarray);
            break;
        }

        switch (sizeof($newarray)){
            case 20:
            case 19:
            case 18:
                $output->param->NormalLayer1RetractSpeedMMperSec = $newarray[18];
            case 17:
                $output->param->NormalLayer1RisingSpeedMMperSec = $newarray[17];
            case 16:
                $output->param->NormalLayer1RisingHeightMM = $newarray[16];
            case 15:
                $output->param->NormalLayer1RetractSpeedMMperSec = $newarray[15];
            case 14:
                $output->param->NormalLayer0RisingSpeedMMperSec = $newarray[14];
            case 13:
                $output->param->NormalLayer0RisingHeightMM = $newarray[13];
            case 12:
                $output->param->NormalLayer0RetractSpeedMMperSec = $newarray[12];
            case 11:
                $output->param->BottomLayer1RetractSpeedMMperSec = $newarray[11];
            case 10:
                $output->param->BottomLayer1RisingSpeedMMperSec = $newarray[10];
            case 9:
                $output->param->BottomLayer1RisingHeightMM = $newarray[9];
            case 8:
                $output->param->BottomLayer0RetractSpeedMMperSec = $newarray[8];
            case 7:
                $output->param->BottomLayer0RisingSpeedMMperSec = $newarray[7];
            case 6:
                $output->param->BottomLayer0RisingHeightMM = $newarray[6];
            case 5:
                $output->param->TransitionLayerCount = $newarray[5];
            case 4:
                $output->param->NormalExposureSeconds = $newarray[4];
            case 3:
                $output->param->BottomExposureSeconds = $newarray[3];
            case 2:
                $output->param->ExposureOffTime = $newarray[2];
            case 1:
                $output->param->BottomLayerCount = $newarray[1];
                break;
            }

    case ("getstatus"):

        $size = sizeof($newarray);
        $output->status = (object)NULL;
        $output->status->status = $newarray[1];
        if (startsWith($newarray[2], "end")) {
            $newarray = remove_keys(count(array_keys((array)$output->status)), $newarray);
            break;
        }
        if ($size > 2) {
            $output->status->file = $newarray[2];
        }

        if (startsWith($newarray[3], "end")) {
            $newarray = remove_keys(count(array_keys((array)$output->status)), $newarray);
            break;
        }
        if ($size > 3) {
            $output->status->total_layers = $newarray[3];
        }

        if ($size > 4) {
            $output->status->percent_complete = $newarray[4];
        }

        if ($size > 5) {
            $output->status->current_layer = $newarray[5];
        }

        if ($size > 6) {
            $output->status->elapsed = $newarray[6];
        }

        if ($size > 7) {
            $output->status->seconds_remaining = $newarray[7];
        }

        if ($size > 8) {
            $output->status->total_volume = $newarray[8];
        }

        if ($size > 9) {
            $output->status->mode = $newarray[9];
        }

        if ($size > 10) {
            $output->status->unknown1 = $newarray[10];
        }

        if ($size > 11) {
            $output->status->layer_height = $newarray[11];
        }

        if ($size > 12) {
            $output->status->unknown2 = $newarray[12];
        }

        $newarray = remove_keys($size, $newarray);
        break;
    default:
        $output->extra = (array)NULL;
        if (sizeof($newarray) > 0) {
            $output->extra = (array)NULL;
        }
        if (sizeof($newarray) > 1) {
            $output->extra[0] = $newarray;
        }
        $newarray = (array)Null;
        break;
    }    
}

echo json_encode($output);
