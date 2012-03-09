<?php
/**
 * 
 *  COPYRIGHT 2010 GWE Systems Ltd 
 * 
 * All Rights resereved - not to be used without permission
 * 
 **/

$debug = false;
$h = 32;
$w = 32;
$base = "black-dot.png";

if (!$debug) {
	header("Content-type: image/png");
}
else {
	list ($usec,$sec) = explode(" ", microtime());
	define("_SC_START", (floatval($usec)+floatval($sec)));
	
	ini_set("error_reporting",true);
	error_reporting(E_ALL);
}

function showerror_image(){
	$errim = imagecreate(1, 1);
	$background_color = imagecolorallocate($errim, 50, 50, 50);
	imagepng($errim);
	imagedestroy($errim);
	return;
}

function iniget($ini,$key,$default){
	if (array_key_exists($key,$ini)){
		return $ini[$key];
	}
	else {
		return $default;
	}
}


function HexToRGB($hex) {
	$hex = ereg_replace("#", "", $hex);
	$color = array();
	if(strlen($hex) == 3) {
		$color['r'] = hexdec(substr($hex, 0, 1) . $r);
		$color['g'] = hexdec(substr($hex, 1, 1) . $g);
		$color['b'] = hexdec(substr($hex, 2, 1) . $b);
	}
	else if(strlen($hex) == 6) {
		$color['r'] = hexdec(substr($hex, 0, 2));
		$color['g'] = hexdec(substr($hex, 2, 2));
		$color['b'] = hexdec(substr($hex, 4, 2));
	}
	return $color;
}

$request = $_SERVER["REQUEST_URI"];
$pathinfo = @pathinfo($request);
$basename = $pathinfo["basename"];
$temp = @explode(".",$basename);
if (isset($temp) && count($temp)>0){
	$filename = $temp[0];
	$colour = str_replace("icon","",$filename);
}
else {
	return showerror_image();
}

$im  = @imagecreatefrompng(dirname(__FILE__)."/".$base);
imagesavealpha($im, true);

if (!$im){
	if ($debug){
		echo "failed to create image from png<br/>";
	}
	return showerror_image();
}

$colours = HexToRGB($colour);
//$color = @imagecolorallocate($im, $colours['r'], $colours['g'], $colours['b']);

imagefilter($im, IMG_FILTER_COLORIZE,$colours['r'], $colours['g'], $colours['b'], 0);

if (!@imagepng($im)){
	return showerror_image();
}
imagedestroy($im);


if ($debug) {
	list($usec, $sec) = explode(" ", microtime());
	$time_end = (float)$usec + (float)$sec;
	echo "<br/>Total time of ".round($time_end - _SC_START,4)." seconds";
}

