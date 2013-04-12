<?php
include("connection.php");
include("iadbanner.php");

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
<link href="/components/com_shines_v2.1/css/style.css" rel="stylesheet" media="screen" type="text/css" />
<script src="/components/com_shines/javascript/functions.js" type="text/javascript"></script>

<title>
<?php echo $site_name.' | ';
echo ($_SESSION['tpl_folder_name'] == 'defaultspanish')?'Videos':'Videos';?>
</title>

<!--<link href="pics/startup.png" rel="apple-touch-startup-image" /> -->
<meta content="destin, vacactions in destin florida, destin, florida, real estate, sandestin resort, beaches, destin fl, maps of florida, hotels, hotels in florida, destin fishing, destin hotels, best florida beaches, florida beach house rentals, destin vacation rentals for destin, destin real estate, best beaches in florida, condo rentals in destin, vacaction rentals, fort walton beach, destin fishing, fl hotels, destin restaurants, florida beach hotels, hotels in destin, beaches in florida, destin, destin fl" name="keywords" />
<meta content="Destin Florida's FREE iPhone application and website guide to local events, live music, restaurants and attractions" name="description" />
<?php include($_SERVER['DOCUMENT_ROOT']."/ga.php"); ?>
</head>

<body>

 <?php
$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
if(stripos($ua,'android') == true) { ?>
  <div class="iphoneads" style="vertical-align:bottom;">
 	 <?php m_show_banner('android-videos-screen'); ?>
  </div>
  <?php } 
  else {
  ?>
  <div class="iphoneads" style="vertical-align:bottom;">
    <?php m_show_banner('iphone-videos-screen'); ?>
  </div>
  <?php } ?>
<div id="main" role="main">  
<div id="zigzag" style="vertical-align:bottom;"> </div>
<div id="content">
	<ul class="mainList" id="placesList">
		
      <?php 
	  while($row=mysql_fetch_array($rec))
	  {
		  $arr=explode('/v/',$row['videocode']);
		  $arr1=explode('?',$arr[1]);
		  $arr2=explode('&',$arr1[0]);
		  $arr2[0]='http://www.youtube.com/watch?v='.$arr2[0];
	  ?>
      <li class="textbox"  style="padding-bottom:20px;">
     <a href="<?=$arr2[0]?>"><img src="/partner/<?=$_SESSION['partner_folder_name']?>/images/phocagallery/<?=$row['filename']?>" border="0" align="left" style="padding-right:10px;width:100px; height:100px;" /></a><font color="#999999"><strong><a href="<?=$arr2[0]?>"><img src="images/next-videos.gif" align="right" style="padding-top:20px;"  border="0"/></a>
     <a href="<?=$arr2[0]?>"><?=$row['title']?></a>
     </strong></font> </li>
			<?php
      }
      ?>
		
	</ul>
</div>

</div>
<div style='display:none;'><?php echo $pageglobal['googgle_map_api_keys']; ?></div>
</body>

</html>
