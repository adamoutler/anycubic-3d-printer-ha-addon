<?php
##$testGD = get_extension_funcs("gd"); // Grab function list 
#echo $testGD;
#if (!$testGD){ echo "GD not even installed."; exit; }
#echo"<pre>".print_r($testGD,true)."</pre>";
parse_str($_SERVER['QUERY_STRING'], $_GET);
/* debug*/
if (!isset($_GET["server"])) {
    $_GET["server"] = "192.168.1.254";
}
if (!isset($_GET["port"])) {
    $_GET["port"] = 6000;
}
if (!isset($_GET["cmd"])) {
    $_GET["cmd"] = "getPreview1,177.pwmb,end\r\ngetPreview2,177.pwmb,end";
}
$command = trim(explode(",", $_GET["cmd"])[0]);
$address = gethostbyname($_GET["server"]);
function startsWith($haystack, $needle)
{
    $length = strlen($needle);
    if (gettype($haystack) === "array") {
        return substr($haystack[0], 0, $length) === $needle;
    }
    return substr($haystack, 0, $length) === $needle;
}
function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if (!$length) {
        return true;
    }
    return substr($haystack, -$length) === $needle;
}
function getFileFromPrinter($address)
{
    //SOL_UDP 
    $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    $result = socket_connect($socket, $address, $_GET["port"]);
    if ($result === false) {
        echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
    } else {
        echo "OK.\n";
    }


    if (!defined('STDIN')) {
        define('STDIN', fopen("php://stdin", "r"));
    }
    if (!$socket) return;
    if (endsWith($_GET["cmd"], ",end")) {
        socket_write($socket, trim($_GET["cmd"] . "\n"));
    } else {
        socket_write($socket, trim($_GET["cmd"] . ",end\n"));
    }








    $buf = 'This is my buffer.';
    $actualvalue = "";

    $waits = 0;
    while ($waits < 4) {
        $received = socket_recv($socket, $buf, 40000, MSG_DONTWAIT);
        $actualvalue .= $buf;
        if ($received > 0) {
            $waits = 0;
        } else {
            $waits++;
        }
        usleep(250 * 1000);
    }
    socket_close($socket);

    //
    //
    //IMPORTANT, do a split string on semicolon;
    //
    //
    $byte_array = unpack('C*', $actualvalue);
    $output_array = array();

    file_put_contents('OUTPUTFILE', $actualvalue);
}

getFileFromPrinter($address);

$actualvalue = file_get_contents('OUTPUTFILE');
//$actualvalue = trim(explode(";", $actualvalue)[1]);
$byte_array = unpack('S*', $actualvalue);



#getPreview1,0.pwmb,end
#getPreview1,0.pwmb,224,168,180,end
$zeroX = 0;
$zeroY = 0;
$width = 224;
$height = 160;
$dpi = 180;
$im = imagecreatetruecolor($width, $height);
$y = 0;
for ($pixel = 1; $pixel < sizeof($byte_array); $pixel++) {

    $p = $byte_array[$pixel];

    $x = floor(floor($pixel) % $width);
    $y = floor(floor($pixel) / $width);
    $aa = ($p >>32) & 0xff;
    $a = ($p >> 24) & 0xff;
    $r = ($p >>16) & 0xff;
    $g = ($p >> 8 & 0xff);
    $b = (($p) & 0xf);
    // $rgb = hsvtorgb ($r, $g, $b);
    $color = imagecolorallocate($im, $r,  $g,  $b);
   // $color = (int)((($r *0xF) * 6 / 256) * 36 + (($g*0xf) * 6 / 256) * 6 + (($b*0xf) * 6 / 256));

    imagesetpixel($im, $x, $y, $color);
}
//imagetruecolortopalette($im, true, 256);

imagecolortransparent($im, 25152); //transparent background
ImagePNG($im, 'name.png');
imagedestroy($im);
echo "done.";
