<?php
include("connection.php");


$query="select * from jos_phocagallery where catid=2 order by id desc";
$rec=mysql_query($query) or die(mysql_error());

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
<!--<link href="pics/startup.png" rel="apple-touch-startup-image" /> -->
<meta content="destin, vacactions in destin florida, destin, florida, real estate, sandestin resort, beaches, destin fl, maps of florida, hotels, hotels in florida, destin fishing, destin hotels, best florida beaches, florida beach house rentals, destin vacation rentals for destin, destin real estate, best beaches in florida, condo rentals in destin, vacaction rentals, fort walton beach, destin fishing, fl hotels, destin restaurants, florida beach hotels, hotels in destin, beaches in florida, destin, destin fl" name="keywords" />
<meta content="Destin Florida's FREE iPhone application and website guide to local events, live music, restaurants and attractions" name="description" />
</head>

<body>
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

<div id="content">
	<ul class="pageitem">
		
      <?php 
	  while($row=mysql_fetch_array($rec))
	  {
		  $arr=explode('src="',$row['videocode']);
		  $arr1=explode('&',$arr[1]);
		  
		  
	  ?>
      <li class="textbox"  style="padding-bottom:20px;">
     <a href="videos_view.php?vid=<?=$row['id']?>"><img src="http://www.tapdestin.com/images/phocagallery/<?=$row['filename']?>" border="0" align="left" style="padding-right:10px;" /></a><font color="#999999"><strong><a href="videos_view.php?vid=<?=$row['id']?>"><img src="images/next-videos.gif" align="right" style="padding-top:20px;"  border="0"/></a>
     <a href="videos_view.php?vid=<?=$row['id']?>"><?=$row['title']?></a>
     </strong></font> </li>
			<?php
      }
      ?>
		
	</ul>
</div>

<div id="footer">

	<a href="http://www.tapdestin.com">&copy; <?=date('Y');?> <?=$site_name?></a> | <a href="mailto:<?=$email?>?subject=Attractions Feedback">Contact Us</a></div>
</body>

</html>
