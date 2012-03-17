<?php

include("connection.php");

global $var;
include_once('upload_class.php');
_init();

if(isset($_SERVER['HTTP_REFERER'])) {
	$var->http_referer = $_SERVER['HTTP_REFERER'];
} else {
	$var->http_referer = 'photos.php';
}

$pos = strpos($_SERVER['HTTP_REFERER'], "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}");
if ($pos === false) {
}else{
	$var->http_referer = $var->post['referralPage'];
}

if($var->post['uploadPhoto']){
	m_upload_photo($pageglobal);
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="yes" name="apple-mobile-web-app-capable" />
<meta content="index,follow" name="robots" />
<meta content="text/html; charset=iso-8859-1" http-equiv="Content-Type" />
<link href="pics/homescreen.gif" rel="apple-touch-icon" />
<meta content="minimum-scale=1.0, width=device-width, maximum-scale=0.6667, user-scalable=no" name="viewport" />
<link href="css/style.css" rel="stylesheet" media="screen" type="text/css" />
<script src="javascript/functions.js" type="text/javascript"></script>
<title><?=$site_name?>
</title>
</head>

<body>

<?php
	/* Code added for upload.tpl */
	
	require("../partner/".$_SESSION['tpl_folder_name']."/tpl/android_upload.tpl");
	?>
</html>
