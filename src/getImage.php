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
    $byte_array = unpack('S*', $actualvalue);
    $output_array = array();

    file_put_contents('OUTPUTFILE', $actualvalue);
}

//getFileFromPrinter($address);

$actualvalue = file_get_contents('OUTPUTFILE');
//$actualvalue = trim(explode(";", $actualvalue)[1]);
$byte_array = unpack('v*', $actualvalue);



#getPreview1,0.pwmb,end
#getPreview1,0.pwmb,224,168,180,end
$zeroX = 0;
$zeroY = 0;
$width = 224;
$height = 160;
$dpi = 180;
$im = imagecreatetruecolor($width, $height);
$bla = imagecolorallocate($im, 0, 0, 0);
$y = 0;
for ($pixel = 1; $pixel < sizeof($byte_array); $pixel++) {
    $p = $byte_array[$pixel];
    //if ($p == 25152) continue;
    $x = floor(floor($pixel) % $width);
    $y = floor(floor($pixel) / $width);
    $bg = ($p >> 8 & 0xff);
    $r = (($p) & 0xf) * 0xf;
    $g = ($bg >> 4  & 0xf) * 0xf;
    $b = ($bg >>8 & 0xf) * 0xf;
    $color = intval(imagecolorallocate($im, $r, $g, $g));

    imagesetpixel($im, $x, $y, $color);
}
function ColorHSLToRGB($h, $s, $l)
{

    $r = $l;
    $g = $l;
    $b = $l;
    $v = ($l <= 0.5) ? ($l * (1.0 + $s)) : ($l + $s - $l * $s);
    if ($v > 0) {
        $m;
        $sv;
        $sextant;
        $fract;
        $vsf;
        $mid1;
        $mid2;

        $m = $l + $l - $v;
        $sv = ($v - $m) / $v;
        $h *= 6.0;
        $sextant = floor($h);
        $fract = $h - $sextant;
        $vsf = $v * $sv * $fract;
        $mid1 = $m + $vsf;
        $mid2 = $v - $vsf;

        switch ($sextant) {
            case 0:
                $r = $v;
                $g = $mid1;
                $b = $m;
                break;
            case 1:
                $r = $mid2;
                $g = $v;
                $b = $m;
                break;
            case 2:
                $r = $m;
                $g = $v;
                $b = $mid1;
                break;
            case 3:
                $r = $m;
                $g = $mid2;
                $b = $v;
                break;
            case 4:
                $r = $mid1;
                $g = $m;
                $b = $v;
                break;
            case 5:
                $r = $v;
                $g = $m;
                $b = $mid2;
                break;
        }
    }
    return array('r' => $r * 255.0, 'g' => $g * 255.0, 'b' => $b * 255.0);
}
imagecolortransparent($im, 25152); //transparent background
ImagePNG($im, 'name.png');
imagedestroy($im);
echo "done.";
