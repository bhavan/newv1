<?php

if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();

include("connection.php");

function advertise_intro() {
  global $var;
  $text = mysql_query("select `introtext` from `jos_content` where `title` = 'New_Advertise with Us'");
  $res=mysql_fetch_array($text);
  $text=$res[introtext];
  echo $text;
}
?>
	
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<link rel="image_src" href="http://<?php echo $_SERVER['HTTP_HOST']?>/partner/<?php echo $_SESSION['partner_folder_name']?>/images/logo/logo.png" />  
	<meta property="og:image" content="http://<?php echo $_SERVER['HTTP_HOST']?>/partner/<?php echo $_SESSION['partner_folder_name']?>/images/logo/logo.png"/>
	<meta property="og:title" content="<?php echo utf8_encode($site_name).' | Advertise';?>"/>
	<meta content="yes" name="apple-mobile-web-app-capable" />
	<meta content="index,follow" name="robots" />
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
	<meta name="HandheldFriendly" content="True">
    <meta name="MobileOptimized" content="320">
    <meta name="viewport" content="width=device-width">
	<meta http-equiv="cleartype" content="on">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="img/h/apple-touch-icon.png">
  	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="img/m/apple-touch-icon.png">
  	<link rel="apple-touch-icon-precomposed" href="img/l/apple-touch-icon-precomposed.png">
  	<link rel="shortcut icon" href="img/l/apple-touch-icon.png">
	
	<link href="pics/homescreen.gif" rel="apple-touch-icon" />
	<meta content="minimum-scale=1.0, width=device-width, maximum-scale=0.6667, user-scalable=no" name="viewport" />
	<link href="/components/com_shines_v2.1/css/style.css" rel="stylesheet" media="screen" type="text/css" />
	
	<title><?php echo utf8_encode($site_name).' | Advertise';?></title>
	<meta content="destin, vacactions in destin florida, destin, florida, real estate, sandestin resort, beaches, destin fl, maps of florida, hotels, hotels in florida, destin fishing, destin hotels, best florida beaches, florida beach house rentals, destin vacation rentals for destin, destin real estate, best beaches in florida, condo rentals in destin, vacaction rentals, fort walton beach, destin fishing, fl hotels, destin restaurants, florida beach hotels, hotels in destin, beaches in florida, destin, destin fl" name="keywords" />
	<meta content="Destin Florida's FREE iPhone application and website guide to local events, live music, restaurants and attractions" name="description" />
	<?php include($_SERVER['DOCUMENT_ROOT']."/ga.php"); ?>
	</head>
	<body>
				
				<header>
					<a id="navBack" href="javascript:history.go(-1)">Back</a>
					<h1>Advertise</h1>
					<div class="fRight">
						<a class="headerButton" id="nearby">w</a>
						<a class="headerButton" id="search">s</a>
					</div>
   				</header>
				
				<div id="main"><ul class="mainList"><li><?php advertise_intro(); ?></li></ul></div>
				<!-- AddThis Button END -->
				</body>
</html>