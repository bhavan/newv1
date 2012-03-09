<?php include("connection.php"); ?>
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
<!--<link href="pics/startup.png" rel="apple-touch-startup-image" /> -->
<meta content="destin, vacactions in destin florida, destin, florida, real estate, sandestin resort, beaches, destin fl, maps of florida, hotels, hotels in florida, destin fishing, destin hotels, best florida beaches, florida beach house rentals, destin vacation rentals for destin, destin real estate, best beaches in florida, condo rentals in destin, vacaction rentals, fort walton beach, destin fishing, fl hotels, destin restaurants, florida beach hotels, hotels in destin, beaches in florida, destin, destin fl" name="keywords" />
<meta content="Destin Florida's FREE iPhone application and website guide to local events, live music, restaurants and attractions" name="description" />
</head>

<body>

<div id="topbar">
	<div id="title">News</div>
	<div id="leftbutton">
		<!--<a href="http://iwebkit.net" class="noeffect">PC Site</a> --> </div>
</div>
<!--<div id="tributton">
	<div class="links">
		<a id="pressed" href="#">Home</a><a href="changelog.html">Changelog</a><a href="about.html">About</a></div>
</div> -->
<div id="content">
	<!--<span class="graytitle">Attractions</span> -->
	<ul class="pageitem">
		<li class="textbox"><!--<span class="header">Discover Destin's Greatest Attractions</span> -->
        <p>Select a Category:</p>
		</li>
		<li class="menu"><a href="dolphincruises.php"><img alt="Dolphin Cruises" src="thumbs/ds-icon-32.png" /><span class="name">Dolphin Cruises</span><span class="arrow"></span></a></li>
		<li class="menu"><a href="parasailing.php"><img alt="Parasailing" src="thumbs/ds-icon-32.png" /><span class="name">Parasailing</span><span class="arrow"></span></a></li>
		<li class="menu"><a href="watercraftrental.php"><img alt="Watercraft Rental" src="thumbs/ds-icon-32.png" /><span class="name">Watercraft Rental</span><span class="arrow"></span></a></li>
		<li class="menu"><a href="shopping.php"><img alt="Shopping" src="thumbs/ds-icon-32.png" /><span class="name">Shopping</span><span class="arrow"></span></a></li>
		<!---<li class="menu"><a href="blogrss.php"><img alt="wordpress" src="thumbs/ds-icon-32.png" /><span class="name">Scuba Diving</span><span class="arrow"></span></a></li>
        <li class="menu"><a href="blogrss.php"><img alt="wordpress" src="thumbs/ds-icon-32.png" /><span class="name">Shopping Centers</span><span class="arrow"></span></a></li>
        <li class="menu"><a href="blogrss.php"><img alt="wordpress" src="thumbs/ds-icon-32.png" /><span class="name">Golf</span><span class="arrow"></span></a></li>
        <li class="menu"><a href="blogrss.php"><img alt="wordpress" src="thumbs/ds-icon-32.png" /><span class="name">Beach Supplies</span><span class="arrow"></span></a></li> -->
	</ul>
<!--	<ul class="pageitem">
		<li class="store"><a href="classiclist.html"><span class="image" style="background-image: url('pics/classiclist.png')"></span>
		<span class="name">Classic list</span><span class="arrow"></span></a></li>
		<li class="store"><a href="applist.html"><span class="image" style="background-image: url('pics/applist.png')"></span>
		<span class="name">Appstore List</span><span class="arrow"></span></a></li>
		<li class="store"><a href="storelist.html"><span class="image" style="background-image: url('pics/ituneslist.png')"></span>
		<span class="name">iTunes classic list</span><span class="arrow"></span></a></li>
		<li class="store"><a href="ituneslist.html"><span class="image" style="background-image: url('pics/itunesmusiclist.png')"></span>
		<span class="name">iTunes music list</span><span class="arrow"></span></a></li>
		<li class="store"><a href="ipodlist.html"><span class="image" style="background-image: url('pics/ipodlist.png')"></span>
		<span class="name">iPod List</span><span class="arrow"></span></a></li>
	</ul> -->
</div>

<div id="footer">

	<a href="http://www.tapdestin.com">&copy; <?=date('Y');?> <?=$site_name?></a> | <a href="mailto:<?=$email?>?subject=Attractions Feedback">Contact Us</a></div>
<div style='display:none;'><?php echo $pageglobal['googgle_map_api_keys']; ?></div>
</body>
    <script type="text/javascript"><!--
window.googleAfmcRequest = {
  client: 'ca-mb-pub-8222418544314001',
  ad_type: 'text_image',
  output: 'html',
  channel: '0010277277',
  format: '320x50_mb',
  oe: 'utf8',
  color_border: '336699',
  color_bg: 'FFFFFF',
  color_link: '0000FF',
  color_text: '000000',
  color_url: '008000',
};
//--></script>
<script type="text/javascript" 
   src="http://pagead2.googlesyndication.com/pagead/show_afmc_ads.js"></script>

</html>
