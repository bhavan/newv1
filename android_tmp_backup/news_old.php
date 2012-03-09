<?php
include("connection.php");
$query="select * from jos_content where id=116";
$rec=mysql_query($query) or die(mysql_error());
$row=mysql_fetch_array($rec);
$row['introtext']=str_replace('images/stories','http://www.tapdestin.com/images/stories',$row['introtext']);
$row['introtext']=str_replace('index.php','../index.php',$row['introtext']);
$row['introtext']=str_replace('’','\'',$row['introtext']);
$row['introtext']=str_replace('—','',$row['introtext']);


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
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
<!--<link href="pics/startup.png" rel="apple-touch-startup-image" /> -->
<meta content="destin, vacactions in destin florida, destin, florida, real estate, sandestin resort, beaches, destin fl, maps of florida, hotels, hotels in florida, destin fishing, destin hotels, best florida beaches, florida beach house rentals, destin vacation rentals for destin, destin real estate, best beaches in florida, condo rentals in destin, vacaction rentals, fort walton beach, destin fishing, fl hotels, destin restaurants, florida beach hotels, hotels in destin, beaches in florida, destin, destin fl" name="keywords" />
<meta content="Destin Florida's FREE iPhone application and website guide to local events, live music, restaurants and attractions" name="description" />
</head>

<body>

<!--Google Adsense -->

<div id="content">
	<ul class="pageitem">
		<li class="textbox">
      <?=html_entity_decode($row['introtext'])?>
		</li>
	</ul>
</div>

<div id="footer">

	<a href="http://www.tapdestin.com">&copy; <?=date('Y');?> <?=$site_name?>, Inc.</a> | <a href="mailto:<?=$email?>?subject=Attractions Feedback">Contact Us</a></div>
</body>

</html>
