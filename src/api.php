<?php

parse_str($_SERVER['QUERY_STRING'], $_GET);
/* debug*/
if (! isset($_GET["server"])){$_GET["server"]="192.168.1.254";}
if (! isset($_GET["port"])){$_GET["port"]=6000;}
if (! isset($_GET["cmd"])){ $_GET["cmd"]="getstatus";}

$socket = fsockopen($_GET["server"], $_GET["port"]);
define('STDIN',fopen("php://stdin","r"));

if (!$socket) return;
stream_set_blocking($socket, 0);
stream_set_blocking(STDIN, 0);

fwrite($socket, trim($_GET["cmd"] . "\n"));

$read   = array($socket, STDIN);
if (!is_resource($socket)) return;
$write  = NULL;
$except = NULL;
sleep(1);
@stream_select($read, $write, $except, null);

$data="";

function endsWith( $haystack, $needle ) {
	$length = strlen( $needle );
    if( !$length ) {
        return true;
    }
    return substr( $haystack, -$length ) === $needle;
}
set_time_limit(10);
$endtime=time()+5;
while ( ! endsWith($data,"end") && time()<$endtime)  {
	$data = fread($socket, 8192);
}
set_time_limit(30);

fclose($socket);
$array = explode(",", $data);
$newarray=(array) null;
foreach ($array as &$value) {
	$value = trim($value, "\n\r\t\v\x00");
	if (strpos($value, "/")!==false) {
		$value = explode("/", $value);
	}
    if ($value =="end"){
		continue;
	}
	array_push($newarray,$value);
}
$output=(Object) NULL;
$output->type="monox";
switch ($newarray[0]){
	case ("sysinfo"):
		$output->model=$newarray[1];
		$output->firmware=$newarray[2];
		$output->serial=$newarray[3];
		$output->wifi=$newarray[4];
		break; 
	case ("getfile"):
		$files=(array) null;
		foreach (range(1,sizeof($newarray)-1) as $file){
			array_push($files,$newarray[$file]);
		}
		$output->files=$files;
		break;
	case ("getstatus"):
			$size=sizeof($newarray);
			if($size>1)$output->status=$newarray[1];
			if($size>2)$output->file=$newarray[2];
			if($size>3)$output->total_layers=$newarray[3];
			if($size>4)$output->layers_remaining=$newarray[4];
			if($size>5)$output->current_layer=$newarray[5];
			if($size>6)$output->seconds_elapsed=$newarray[6];
			if($size>7)$output->seconds_remaining=$newarray[7];
			if($size>8)$output->total_volume=$newarray[8];
			if($size>9)$output->mode=$newarray[9];
			if($size>10)$output->unknown1=$newarray[10];
			if($size>11)$output->layer_height=$newarray[11];
			if($size>12)$output->unknown2=$newarray[12];
		break;
	default:
		$output->retval=$newarray[2];
		
}
echo json_encode($output); 
?>
