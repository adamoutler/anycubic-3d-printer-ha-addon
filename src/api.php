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

    while (strlen($contents) > 1){
        $contents = readLockFile();
        if (time()-$contents > 15){
            unlockAPI();
            break;
        }
        if (strlen($contents) <=1){
            break;
        }
    }
    $handle = fopen($LOCKFILE, "w");

    fwrite($handle, time());
    fclose( $handle);
    register_shutdown_function('unlockAPI');

    
    
}
function readLockFile(){
    global $LOCKFILE;
    $handle = fopen($LOCKFILE, "r");
    while (!$handle){
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

/**
 * Unlock the API, allowing others to talk to the API.
 */
function unlockAPI() {
    global $LOCKFILE;
    $mylock= fopen( $LOCKFILE, "w");
    while (!$mylock){
        $mylock= fopen( $LOCKFILE, "x+");
    }
    fwrite($mylock, 0);
    fclose($mylock);

    
}

function __destruct() {
    unlockAPI();
    echo 'Destruct: ' . __METHOD__ . '()' . PHP_EOL;
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
