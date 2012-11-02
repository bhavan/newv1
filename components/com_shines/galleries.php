<?php

//ini_set('error_reporting',1);
//ini_set('display_errors',1);
include("iadbanner.php");
include("connection.php");


$query = "select * from jos_phocagallery_categories where id<>2 and published=1 and approved=1 order by id desc";

$rec=mysql_query($query) or die(mysql_error());

//*********************************************


  //$param = db_fetch("select * from `jos_phocagallery_categories` where id != 2 and `published` = 1 and `approved` = 1 order by ordering asc", true, true);
  
  $query = "select * from jos_phocagallery_categories where id<>2 and published=1 and approved=1 order by ordering";
	$rec=mysql_query($query) or die(mysql_error());
	
	$param = array();
	while($r=mysql_fetch_assoc($rec)) {
		$param[] = $r;
	}
		
	 foreach($param as $k => $v) {

			$query1 = "select id, filename from `jos_phocagallery` where `published` = 1 and `approved` = 1 and `catid` = ".$v['id'] ." ORDER BY ordering"; 
			$rec1=mysql_query($query1) or die(mysql_error());

			$v['photos'] = array();
			while($r1=mysql_fetch_assoc($rec1)) {
				$v['photos'][] = $r1;
			}
			
			$id = rand(0, (count($v['photos']) - 1));
			
			$tmp_arr = explode('/', $v['photos'][$id]['filename']);
			$userfolder = '';
			$filename = $v['photos'][$id]['filename'];
			if(count($tmp_arr) > 1) {
				$userfolder = $tmp_arr[0].'/';
				$filename = $tmp_arr[1];
			}
			unset($tmp_arr);
			if(trim($userfolder) == '' && trim($filename) == '')
				$param[$k]['avatar'] = '';
			else
			$param[$k]['avatar'] = '/partner/'.$_SESSION['partner_folder_name'].'/images/phocagallery/'.$userfolder.'thumbs/phoca_thumb_s_'.$filename;
      
	 }
	

//*********************************************


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="yes" name="apple-mobile-web-app-capable" />
<meta content="index,follow" name="robots" />
<meta content="text/html; charset=iso-8859-1" http-equiv="Content-Type" />
<link href="pics/homescreen.gif" rel="apple-touch-icon" />
<meta content="minimum-scale=1.0, width=device-width, maximum-scale=0.6667, user-scalable=no" name="viewport" />
<link href="/components/com_shines/css/style_new_24oct2011.css" rel="stylesheet" media="screen" type="text/css" />
<script src="javascript/functions.js" type="text/javascript"></script>
<title><?=$site_name?></title>
<!--<link href="pics/startup.png" rel="apple-touch-startup-image" /> -->
<meta content="destin, vacactions in destin florida, destin, florida, real estate, sandestin resort, beaches, destin fl, maps of florida, hotels, hotels in florida, destin fishing, destin hotels, best florida beaches, florida beach house rentals, destin vacation rentals for destin, destin real estate, best beaches in florida, condo rentals in destin, vacaction rentals, fort walton beach, destin fishing, fl hotels, destin restaurants, florida beach hotels, hotels in destin, beaches in florida, destin, destin fl" name="keywords" />
<meta content="Destin Florida's FREE iPhone application and website guide to local events, live music, restaurants and attractions" name="description" />
<?php include("../../ga.php"); ?>
</head>

<body>
<?php

$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
if(stripos($ua,'android') == true) { ?>
  
  <?php } 
  else {
  ?>
  <div class="iphoneads" style=" vertical-align:top">
    <?php m_show_banner('iphone-photos-screen'); ?>
  </div>
  <?php } ?>
  


<?php
	/* Code added for iphone_galleries.tpl */
	require("../../partner/".$_SESSION['tpl_folder_name']."/tpl/iphone_galleries.tpl");
	?>
</body>

</html>
