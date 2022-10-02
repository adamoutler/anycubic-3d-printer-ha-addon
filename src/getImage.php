<?php
namespace anycubic;
include 'AnycubicImage.php';
include 'api.php';
use anycubic\AnycubicImage as AnycubicImage;


$DEBUG=false;
/* global lock file for API use prevents multiple API calls at the same time */

/**
 * parse query string or handle input from
 */
function getCommands() {
    $server = $_GET["server"] ?? "192.168.1.254";
    $filename = $_GET["file"] ?? "1.pwmb";
    $port = $_GET["port"] ?? 6000;
    $file = preg_replace("/[^a-zA-Z0-9.]+/", "", trim(explode(",", $filename)[0]));
    $address = preg_replace("/[^a-zA-Z0-9.]+/", "", gethostbyname($server));
    // maybe you want $url = "https://" . $address . ":" . $port . "/" . $file;
    return [$address, $port, $file];
}



/**
 * Get the contents from the printer, including metadata.
 * @param string $address the ip address or network name of the printer.
 * @param int $port the name of the printer port
 * @param String $file the Internal Name of the file to be pulled.
 * Note, this is not the display name, it is the internal name from the getFile command.
 * @return Image an image object representing the image and metadata.
 */
function getImageFromPrinter($address, $port, $file) {
    global $DEBUG;
    lockAPI();
    ini_set('max_execution_time', '20');

    //SOL_UDP
    $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die("Could not create socket\n");
    socket_setopt($socket, SOL_SOCKET, SO_RCVTIMEO, array('sec' => 1, 'usec' => 500000));
    socket_connect($socket, $address, $port) or die("Could not connect to server\n");
    $preview1 = getPreviewCmd(1, $file);
    socket_write($socket, $preview1, strlen($preview1)) or die("Could not send data to server\n");
    $image_metadata = socket_read($socket, 32) or die("Could not read server response\n");
    $image = new AnycubicImage($image_metadata);
    if ($DEBUG){
        print("read metadata from " . ($image->getFilename()) . "\n");
    }
    while (($currentByte = socket_read($socket, 1)) != "") {
        // Empty the buffer
    }
    $preview2 = getPreviewCmd(2, $file);
    socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array('sec' => 4, 'usec' => 10));
    socket_set_option($socket, SOL_SOCKET, SO_RCVBUF, $image->getExpectedBytes());
    $end_time = microtime(true) + (15 * 100000);
    $image_contents = "";
    socket_set_nonblock($socket);
    socket_write($socket, $preview2, strlen($preview2)) or die("Could not send data to server\n");
    while (microtime(true) < $end_time && strlen($image_contents) < $image->getExpectedBytes()) {
        $image_contents .= socket_read($socket, $image->getExpectedBytes()-strlen($image_contents)."\n");
    }

    $image->setContents($image_contents);
    unlockAPI();
    if ($DEBUG){
        echo "Expected bytes:" . ($image->getExpectedBytes());
        echo "Actual bytes:" . (strlen($image->getContents()));
        print("Valid Image: " . ($image->isValid() == 0 ? "true" : "false") . "\n");
    }
    return $image;
}

/**
 * Get the preview command to be sent to the printer.
 * param $number, preview 1/2
 */
function getPreviewCmd(int $number, String $file) {
    return "getPreview${number},${file},end\r\n";
}

[$address, $port, $file] = getCommands();
$filename="img/".$file.".png";
if (file_exists($filename)){
    echo $filename;
    return;
}

$image = getImageFromPrinter($address, $port, $file);
$file= $filename;

if (! $image->isValid()){
    $file=$file.".invalid.png";
}


ImagePNG($image->getImage(), $file);

$handle = fopen($file, "r");
while (!$handle){
    $handle = fopen($file, "r");
}

$contents = fread($handle, filesize($LOCKFILE));
fclose($handle); 

print $file; 
