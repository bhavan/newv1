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
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<link href="pics/homescreen.gif" rel="apple-touch-icon" />
<meta content="minimum-scale=1.0, width=device-width, maximum-scale=0.6667, user-scalable=no" name="viewport" />
<link href="css/style.css" rel="stylesheet" media="screen" type="text/css" />
<script src="javascript/functions.js" type="text/javascript"></script>
<title><?=$site_name?>
</title>
</head>

<body>

<div id="topbar">
<div id="leftnav"><a href="<?php echo $var->http_referer; ?>" style=":left;">&laquo;&nbsp;Back</a></div>

<div id="title">Upload Photos</div>

</div>
<div id="content">
	
<?php if(isset($var->photo_uploaded) && $var->photo_uploaded) echo "<h4>Thank you! Your photo has successfully been uploaded, waiting for admin approval.</h4><br />"; ?>

<ul class="pageitem">

		<li class="textbox"  style="padding-bottom:20px;">

		<form action="" enctype="multipart/form-data" method="post">
		<input type="hidden" name="MAX_FILE_SIZE" value="2000000">
		<input type="hidden" name="formname" value="upload.event.photo" />
		<input type="hidden" name="referralPage" value="<?=$var->http_referer?>" />
		
				<input type="file" value="Vali fail" name="image" id="image" ><br/><br/>
				Photo Caption (optional):<br/>
				<input style="width: 90%;" id="caption" name="caption" ><br/><br/>
				
				Photo Description (optional):<br/>
				<textarea rows="6" cols="43" style="width: 90%;" id="description" name="description" class="no-margin-top"></textarea><br/><br/>

				Your Name (optional):<br/>
				<input style="width: 90%;" id="username" name="username" class="no-margin-top"><br/><br/>

				Your Hometown (optional):<br/>
				<input style="width: 90%;" id="location" name="location" class="no-margin-top"><br/><br/>

				<input type="submit" name='uploadPhoto' value="Upload">
		</form>

	 </li>
</ul>

	
</div>

<div id="footer">

	&copy; <?=date('Y');?> <?=$site_name?>, Inc. | <a href="mailto:<?=$email?>?subject=Feedback">Contact Us</a></div></div>
</body>
<div style='display:none;'><?php echo $pageglobal['googgle_map_api_keys']; ?></div>
</html>
