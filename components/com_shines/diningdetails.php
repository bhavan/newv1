<?php
include("connection.php");
function distance($lat1, $lon1, $lat2, $lon2, $unit) { 

  $theta = $lon1 - $lon2; 
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)); 
  $dist = acos($dist); 
  $dist = rad2deg($dist); 
  $miles = $dist * 60 * 1.1515;
  $unit = strtoupper($unit);

  if ($unit == "K") {
    return ($miles * 1.609344); 
  } else if ($unit == "N") {
      return ($miles * 0.8684);
    } else {
        return $miles;
      }
}
$did=$_REQUEST['did'];
if ($_REQUEST['lat']!="")
$lat1=$_REQUEST['lat'];
else
$lat1=0;
if ($_REQUEST['lon']!="")
$lon1=$_REQUEST['lon'];
else
$lon1=0;

$query="select * from jos_jev_locations where loc_id=$did";
$rec=mysql_query($query) or die(mysql_error());

function stripJunk($string) { 
$cleanedString = preg_replace("/[^A-Za-z0-9\s\.\-\/+\!;\n\t\r\(\)\'\"._\?>,~\*<}{\[\]\=\&\@\#\$\%\^` ]/","", $string); 
$cleanedString = preg_replace("/\s+/"," ",$cleanedString); 
return $cleanedString; 
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
<script language="javascript">
		function linkClicked(link) { document.location = link; } 
</script>
<title><?=$site_name?>
</title>
<!--<link href="pics/startup.png" rel="apple-touch-startup-image" /> -->
<meta content="destin, vacactions in destin florida, destin, florida, real estate, sandestin resort, beaches, destin fl, maps of florida, hotels, hotels in florida, destin fishing, destin hotels, best florida beaches, florida beach house rentals, destin vacation rentals for destin, destin real estate, best beaches in florida, condo rentals in destin, vacaction rentals, fort walton beach, destin fishing, fl hotels, destin restaurants, florida beach hotels, hotels in destin, beaches in florida, destin, destin fl" name="keywords" />
<meta content="Destin Florida's FREE iPhone application and website guide to local events, live music, restaurants and attractions" name="description" />
</head>

<body>

<div id="content">
	<ul class="pageitem">
      <?php 
	  while($row=mysql_fetch_array($rec))
	  {
		  
			
			$lat2=$row['geolat'];
			$lon2=$row['geolon'];
			
			
	  ?>
      <li class="textbox">
      <div style="width:100%"><strong><?=$row['title']?></strong>
      <br /><br />
      <div style="width:100%">
        <div style="width:10%;float:left;padding-right:50px;">Address:</div><div style="width:100%"><a href="javascript:linkClicked('APP30A:SHOWMAP:<?=$lat2?>:<?=$lon2?>')"><?=$row['street']?></a></div></div><br />
     <div style="width:100%">
       <div style="width:10%;float:left;padding-right:50px;">Phone:</div>
       <div style="width:90%"><a href="tel:<?=$row[phone]?>"><?=$row[phone]?></a></div></div><br />
       <div style="width:100%"><div style="width:10%;float:left;padding-right:50px;">Distance:</div><div style="width:100%"> <?=round(distance($lat1, $lon1, $lat2, $lon2, "m"),'2').' miles'?></div></div><br />
	<?php if ($row['url']!=''){ ?>
       <div style="width:100%"><div style="width:10%;float:left;padding-right:50px;">Website:</div><div style="width:90%"><a href="http://<?php echo str_replace('http://','',$row['url']); ?>" target="_blank"><?php echo str_replace('http://','',$row['url']); ?></a></div></div><br />
	<?php } ?>
	<?php if ($row['description']!=''){ ?>
       <div style="width:100%"><div style="width:10%;float:left;padding-right:50px;">Description:</div><div style="width:100%"><?php echo stripJunk($row['description']); ?></div></div><br />
	<?php } ?>
      </div>
      </li>
      <?php
	  }
	  ?>
		
	</ul>
</div>

<!-- <div id="footer">&copy; <?=date('Y');?> <?=$site_name?>, Inc. | <a href="mailto:<?=$email?>?subject=Feedback">Contact Us</a>  &nbsp;&nbsp;&nbsp; <a href="<?=$pageglobal['facebook']?>"><img src="images/icon_facebook_16x16.gif" alt="facebook_icon" width="16" height="16" /></a> &nbsp;&nbsp;&nbsp; </div> --> </div>
<div style='display:none;'><?php echo $pageglobal['googgle_map_api_keys']; ?></div>
</body>

</html>
