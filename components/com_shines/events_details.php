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
$eid=$_REQUEST['eid'];
if ($_REQUEST['lat']!="")
$lat1=$_REQUEST['lat'];
else
$lat1=0;
if ($_REQUEST['lon']!="")
$lon1=$_REQUEST['lon'];
else
$lon1=0;

if ($_REQUEST['d']=="")
$today=date('d');
else
$today=$_REQUEST['d'];
if ($_REQUEST['m']=="")
$tomonth=date('m');
else
$tomonth=$_REQUEST['m'];
if ($_REQUEST['Y']=="")
$toyear=date('Y');
else
$toyear=$_REQUEST['Y'];

$todaestring=date('D, M j', mktime(0, 0, 0, $tomonth, $today, $toyear));

$query="select *,DATE_FORMAT(`startrepeat`,'%h:%i %p') as timestart  from jos_jevents_repetition where rp_id=$eid";
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
<!--<link href="css/style.css" rel="stylesheet" media="screen" type="text/css" />-->
<link href="css/style_new_24oct2011.css" rel="stylesheet" media="screen" type="text/css" />
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
<!--Google Adsense -->


<div id="topbar">
<div id="title">Event Info</div>
<div id="leftnav">   
<a href="events.php?d=<?=$today?>&m=<?=$tomonth?>&Y=<?=$toyear?>&lat=<?=$lat1?>&lon=<?=$lon1?>" ><img src="images/navlinkleft.png" alt="navlinkleft" width="37" height="37" style="margin-top:0px;" /></a></div>
        
        
<div id="rightnav"></div>
</div>

<div id="content">
	<ul class="pageitem">	
      <?php 
	  while($row=mysql_fetch_array($rec))
	  {
	  	//#DD#
		  $ev=mysql_query("select *  from jos_jevents_vevent where ev_id=".$row['eventid']) or die(mysql_error());
		  $evDetails=mysql_fetch_array($ev);
		  $evrawdata = unserialize($evDetails['rawdata']);
		  //#DD#	
		  
			//$queryvevdetail="select *  from jos_jevents_vevdetail where evdet_id=".$row['eventid'];
			$queryvevdetail="select *  from jos_jevents_vevdetail where evdet_id=".$row['eventdetail_id'];
			$recvevdetail=mysql_query($queryvevdetail) or die(mysql_error());
			$rowvevdetail=mysql_fetch_array($recvevdetail);
			if ((int) ($rowvevdetail['location']))
			{
			$querylocdetail="select *  from jos_jev_locations where loc_id=".$rowvevdetail['location'];
			$reclocdetail=mysql_query($querylocdetail) or die(mysql_error());
			$rowlocdetail=mysql_fetch_array($reclocdetail);
			$lat2=$rowlocdetail['geolat'];
			$lon2=$rowlocdetail['geolon'];
			
			}
	  ?>
      <li class="textbox">
      <div style="width:100%"><strong><?=$rowvevdetail['summary']?></strong>
      <br /><br />
      <div style="width:100%"><div class="gray" style="width:10%;float:left;padding-right:50px;">Date:</div><div style="width:100%"><?=$todaestring?></div></div><br />
     <div style="width:100%"><div class="gray" style="width:10%;float:left;padding-right:50px;">Time:</div><div style="width:100%">
			<?php
				//#DD#
				if($evrawdata['allDayEvent']=='on'){
					echo 'All Day Event';
				}else{
					echo ltrim($row[timestart], "0");
				}
				//#DD#
			?>      	
     </div></div><br />
     <div style="width:100%"><div class="gray" style="width:10%;float:left;padding-right:50px;">Location:</div><div style="width:100%"><?=$rowlocdetail['title']?></div></div><br />
     <!--<div style="width:100%"><div class="gray" style="width:10%;float:left;padding-right:50px;">Address:</div><div style="width:100%"><a href="map.php?lat=<?=$lat2?>&long=<?=$lon2?>"><?=$rowlocdetail['street']?></a></div></div><br />-->
     <div style="width:100%"><div class="gray" style="width:10%;float:left;padding-right:50px;">Address:</div><div style="width:100%"><a href="javascript:linkClicked('APP30A:SHOWMAP:<?=$lat2?>:<?=$lon2?>')" ><?=$rowlocdetail['street']?></a></div></div><br />
     <div style="width:100%"><div class="gray" style="width:10%;float:left;padding-right:50px;">Phone:</div><div style="width:100%"><a href="tel:<?=$rowlocdetail['phone']?>"><?=$rowlocdetail['phone']?></a></div></div><br />
     <div style="width:100%"><div class="gray" style="width:10%;float:left;padding-right:50px;">Distance:</div><div style="width:100%"> <?=round(distance($lat1, $lon1, $lat2, $lon2, "m"),'1').' miles'?></div></div><br />
	<?php if(trim($rowlocdetail['url']) != '') { ?>
     <div style="width:100%"><div class="gray" style="width:10%;float:left;padding-right:50px;">Website:</div><div style="width:100%"><a href="http://<?php echo str_replace('http://','',$rowlocdetail['url']); ?>" target="_blank"><?php echo str_replace('http://','',$rowlocdetail['url']); ?></a></div></div><br />
	<?php } ?>
     <div style="width:100%"><div class="gray" style="width:10%;float:left;padding-right:50px;">Description:</div><div style="width:100%"><?=$rowvevdetail['description']?></div></div><br />
      </div>
      </li>
      <?php
      
		//#DD#
		$mailContent.= "
		{$rowvevdetail['summary']} %0D%0A%0D%0A
		Date: {$todaestring} %0D%0A%0D%0A
		Time: " . ltrim($row[timestart], "0"). " %0D%0A%0D%0A
		Location: {$rowlocdetail['title']} %0D%0A%0D%0A
		Address: {$rowlocdetail['street']} %0D%0A%0D%0A
		Phone: {$rowlocdetail['phone']} %0D%0A%0D%0A
		";
		
		if(trim($rowlocdetail['url']) != '') { 
			$mailContent.="Website: ". str_replace('http://','',$rowlocdetail['url']) ."%0D%0A%0D%0A";
		} 
		
		$mailContent.="Description: {$rowvevdetail['description']} %0D%0A%0D%0A";
		$mailContent = str_replace('<br/>',"%0D%0A", $mailContent);
		$mailContent = str_replace('<br>',"%0D%0A", $mailContent);
		$mailContent = str_replace('<br />',"%0D%0A", $mailContent);
		$mailContent = str_replace('"','\"', $mailContent);
		$mailContent = strip_tags($mailContent);
		//#DD#

	  }
	  ?>

<!-- #DD# -->
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
 <a style="outline: medium none;margin-left:-15px;margin-top:-5px;" href="mailto:?body=<?=$mailContent;?>&subject=Check Out This Event!" rel="nofollow">
 	<img src="../../common/images/btn_email.gif" border="0" />
 </a>		
<!-- #DD# -->
		
	</ul>
</div>

<div id="footer">

	&copy; <?=date('Y');?> <?=$site_name?>, Inc. | <a href="mailto:<?=$email?>?subject=Feedback">Contact Us</a></div></div>
</body>

</html>
