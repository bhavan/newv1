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
if ($_REQUEST['lat']!="")
$lat1=$_REQUEST['lat'];
else
$lat1=30.393534;
if ($_REQUEST['lon']!="")
$lon1=$_REQUEST['lon'];
else
$lon1=-86.495783;


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

//#DD#
$_REQUEST['eventdate'] = trim($_REQUEST['eventdate']);
if(!empty($_REQUEST['eventdate'])){
	$today = date('d',strtotime($_REQUEST['eventdate']));
	$tomonth = date('m',strtotime($_REQUEST['eventdate']));
	$toyear = date('Y',strtotime($_REQUEST['eventdate']));
}
//#DD#

$todaestring=date('D, M j', mktime(0, 0, 0, $tomonth, $today, $toyear));

$query_cat="SELECT c.id FROM jos_categories AS c LEFT JOIN jos_categories AS p ON p.id=c.parent_id LEFT JOIN jos_categories AS gp ON gp.id=p.parent_id LEFT JOIN jos_categories AS ggp ON ggp.id=gp.parent_id WHERE c.access <= 2 AND c.published = 1 AND c.section = 'com_jevents'";
$rec_cat=mysql_query($query_cat);
while($row_cat=mysql_fetch_array($rec_cat))
$array_cat[]=$row_cat['id'];
$byday= strtoupper(substr(date('D',mktime(0, 0, 0, $tomonth, $today, $toyear)),0,2));
$arrstrcat=implode(',',array_merge(array(-1), $array_cat));
$query_filter="SELECT rpt.*, ev.*, rr.*, det.*, ev.state as published , loc.loc_id,loc.title as loc_title, loc.title as location, loc.street as loc_street, loc.description as loc_desc, loc.postcode as loc_postcode, loc.city as loc_city, loc.country as loc_country, loc.state as loc_state, loc.phone as loc_phone	, loc.url as loc_url	, loc.geolon as loc_lon	, loc.geolat as loc_lat	, loc.geozoom as loc_zoom	 , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup , YEAR(rpt.endrepeat ) as ydn, MONTH(rpt.endrepeat ) as mdn, DAYOFMONTH(rpt.endrepeat ) as ddn , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup , HOUR(rpt.endrepeat ) as hdn, MINUTE(rpt.endrepeat ) as mindn, SECOND(rpt.endrepeat ) as sdn FROM jos_jevents_repetition as rpt LEFT JOIN jos_jevents_vevent as ev ON rpt.eventid = ev.ev_id LEFT JOIN jos_jevents_icsfile as icsf ON icsf.ics_id=ev.icsid LEFT JOIN jos_jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id LEFT JOIN jos_jevents_rrule as rr ON rr.eventid = rpt.eventid LEFT JOIN jos_jev_locations as loc ON loc.loc_id=det.location LEFT JOIN jos_jev_peopleeventsmap as persmap ON det.evdet_id=persmap.evdet_id LEFT JOIN jos_jev_people as pers ON pers.pers_id=persmap.pers_id WHERE ev.catid IN(".$arrstrcat.") AND rpt.endrepeat >= '".$toyear."-".$tomonth."-".$today." 00:00:00' AND rpt.startrepeat <= '".$toyear."-".$tomonth."-".$today." 23:59:59' AND ev.state=1 AND rpt.endrepeat>='".date('Y')."-".date('m')."-".date('d')." 00:00:00' AND ev.access <= 0 AND icsf.state=1 AND icsf.access <= 0 and ((YEAR(rpt.startrepeat)=".$toyear." and MONTH(rpt.startrepeat )=".$tomonth." and DAYOFMONTH(rpt.startrepeat )=".$today.") or freq<>'WEEKLY')GROUP BY rpt.rp_id";

$rec_filter=mysql_query($query_filter);
while($row_filter=mysql_fetch_array($rec_filter)){
	$arr_rr_id[]=$row_filter['rp_id'];
}

if (count($arr_rr_id))
$strchk=implode(',',$arr_rr_id);
else
$strchk=0;
$query="select *,DATE_FORMAT(`startrepeat`,'%h:%i %p') as timestart, DATE_FORMAT(`endrepeat`,'%h:%i%p') as timeend from jos_jevents_repetition where rp_id in ($strchk) ORDER BY  DATE_FORMAT(`startrepeat`,'%H%i') ASC ";
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
<meta content="destin, vacactions in destin florida, destin, florida, real estate, sandestin resort, beaches, destin fl, maps of florida, hotels, hotels in florida, destin fishing, destin hotels, best florida beaches, florida beach house rentals, destin vacation rentals for destin, destin real estate, best beaches in florida, condo rentals in destin, vacaction rentals, fort walton beach, destin fishing, fl hotels, destin restaurants, florida beach hotels, hotels in destin, beaches in florida, destin, destin fl" name="keywords" />
<meta content="Destin Florida's FREE iPhone application and website guide to local events, live music, restaurants and attractions" name="description" />
<!--<link href="css/style.css" rel="stylesheet" media="screen" type="text/css" />-->
<link href="css/style_new_24oct2011.css" rel="stylesheet" media="screen" type="text/css" />
<script src="javascript/functions.js" type="text/javascript"></script>
<title><?=$site_name?></title>
<link href="pics/startup.png" rel="apple-touch-startup-image" />
<script type="text/javascript">
	function linkClicked(link) { document.location = link; }
</script>

<script src="http://code.jquery.com/jquery-1.6.2.min.js"></script>
<script src="../../mobiscroll/js/mobiscroll-1.5.1.js" type="text/javascript"></script>
<link href="../../mobiscroll/css/mobiscroll-1.5.1.css" rel="stylesheet" type="text/css" />

<style type="text/css">
	body {
		font-family: arial, verdana, sans-serif;
		font-size: 12px;
	}
	.dww img {
		width: 30px;
		height: 30px;
		margin: 5px 30px;
	}
</style>

<script type="text/javascript">

			function submitForm() {
				document.events.submit(); //#DD#
			}

	$(document).ready(function () {
		// Date with external button
		$('#date1').scroller({ showOnFocus: false });
		$('#show').click(function() { $('#date1').scroller('show'); return false; });
		// Time
		$('#date2').scroller({ preset: 'time' });
		// Datetime
		$('#date3').scroller({ preset: 'datetime' });

		$('#custom').scroller({
			width: 90,
			wheels: wheels,
			parseValue: function (s) {
				if (s !== '') {
					var d = s.split(' ');
				}
				else {
					var d = [1,1,1];
				}
				return d;
			}
		});
		$('#custom').click(function() { $(this).scroller('show'); });

		$('#disable').click(function() {
			$('#date1').scroller('disable');
			return false;
		});

		$('#enable').click(function() {
			$('#date1').scroller('enable');
			return false;
		});

		$('#get').click(function() {
			alert($('#date1').scroller('getDate'));
			return false;
		});

		$('#set').click(function() {
			$('#date1').scroller('setDate', new Date(), true);
			return false;
		});

		$('#theme, #mode').change(function() {
			var t = $('#theme').val();
			var m = $('#mode').val();
			$('#date1').scroller('destroy').scroller({ showOnFocus: false, theme: t, mode: m });
			$('#date2').scroller('destroy').scroller({ preset: 'time', theme: t, mode: m });
			$('#date3').scroller('destroy').scroller({ preset: 'datetime', theme: t, mode: m });
			$('#custom').scroller('option', { theme: t, mode: m });
		});
	});
</script>
    
</head>

<body>

<div id="topbar" style="height:40px; margin-top:5px; width:100%;">
<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr>
		<td width="50" height="50" valign="top" align="left">
			<a href="events.php?d=<?=date('d',mktime(0, 0, 0,$tomonth ,$today-1, $toyear));?>&m=<?=date('m',mktime(0, 0, 0,$tomonth ,$today-1, $toyear));?>&Y=<?=date('Y',mktime(0, 0, 0,$tomonth ,$today-1, $toyear));?>&lat=<?=$lat1?>&lon=<?=$lon1?>">
				<img src="images/navlinkleft.png" alt="navlinkleft" width="37" height="37" />
			</a> 
		</td>
		<td width="200" height="50" valign="top" align="center">
			<table cellpadding="0" cellspacing="0" border="0" width="200">
				<tr>
					<td width="150" height="50" valign="top" align="right">
						<div style="width:150px;text-align:right;font-size:16pt;color:#333333; font-weight:bold;"><?=$todaestring?></div>
					</td>
					<td width="50" height="50" valign="top" align="left">
						<div style="width:50px;text-align:left;">
							<form name='events' id='events' action='events.php' method='post'>
								<input type="text" value="" class="mobiscroll ui-input-text ui-body-null ui-corner-all ui-shadow-inset ui-body-d scroller" id="date1" name="eventdate" style="width:0px;height:0px;border:0px;">
								<button data-theme="a" style='background: url("images/calendar.jpg") no-repeat center;width: 25px;height: 25px;margin-top:0px;border:0px solid blue;margin:0px;padding:0px;' id="show" class="ui-btn-hidden" aria-disabled="false"></button>
							</form>
						</div>
					</td>
				</tr>
			</table>
		</td>
		<td width="50" height="50" valign="top" align="right">
			<a href="events.php?d=<?=date('d',mktime(0, 0, 0,$tomonth ,$today+1, $toyear));?>&m=<?=date('m',mktime(0, 0, 0,$tomonth ,$today+1, $toyear));?>&Y=<?=date('Y',mktime(0, 0, 0,$tomonth ,$today+1, $toyear));?>&lat=<?=$lat1?>&lon=<?=$lon1?>">
				<img src="images/navlinkright.png" alt="navlinkright" width="37" height="37" />
			</a>
		</td>
	</tr>
</table>
</div>


<div id="content">
  <ul class="pageitem">
	<li class="textbox">

    <div style="float:right;width:18%">distance</div>
    </li>	
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
			$lat2=$rowlocdetail[geolat];
			$lon2=$rowlocdetail[geolon];
			
			}

			#DD#
			$displayTime = '';
			if($evrawdata['allDayEvent']=='on')
			{
				$displayTime.='All Day Event';
			}
			else
			{
				$displayTime.= ltrim($row[timestart], "0");
				if($evrawdata['NOENDTIME']!=1){
					$displayTime.='-'.ltrim($row[timeend], "0");
				}
			}	
			#DD#
	  ?>
      <li class="textbox">
      <div style="float:left;padding-right:10px;width:21%;font-size:11px;" class="small"><?=$displayTime?></div>
      <div style="float:left;width:55%;text-align:left;">
      	<strong><?=$rowvevdetail['summary']?></strong><br />
      	<span class="grayplan"><?=$rowlocdetail['title']?></span><br />
      	<a href="tel:<?=$rowlocdetail['phone']?>">call</a> |
      	<a href="javascript:linkClicked('APP30A:FBCHECKIN:<?php echo $lat2; ?>:<?php echo $lon2; ?>')">check in</a> | 
      	<a href="events_details.php?eid=<?=$row['rp_id']?>&d=<?=$today?>&m=<?=$tomonth?>&Y=<?=$toyear?>&lat=<?=$lat1?>&lon=<?=$lon1?>">more info</a>  
      </div>
      <div style="float:right;width:15%"><?=round(distance($lat1, $lon1, $lat2, $lon2, "m"),'1').' mi'?></div>
  
      </li>
      <?php
	  }
	  ?>
		
	</ul>
</div>

<!-- Bottom Nav -->
<div id="tributton2">
	<div class="links" style="width:100%;">
	     <div id="leftnav" style="left:0px;">   
                 <a href="events.php?d=<?=date('d',mktime(0, 0, 0,$tomonth ,$today-1, $toyear));?>&m=<?=date('m',mktime(0, 0, 0,$tomonth ,$today-1, $toyear));?>&Y=<?=date('Y',mktime(0, 0, 0,$tomonth ,$today-1, $toyear));?>&lat=<?=$lat1?>&lon=<?=$lon1?>">&nbsp;</a>
             </div>
                 <a id="pressed" href="#"><?=$todaestring?></a>
             <div id="rightnav" style="right:0px;">
                 <a href="events.php?d=<?=date('d',mktime(0, 0, 0,$tomonth ,$today+1, $toyear));?>&m=<?=date('m',mktime(0, 0, 0,$tomonth ,$today+1, $toyear));?>&Y=<?=date('Y',mktime(0, 0, 0,$tomonth ,$today+1, $toyear));?>&lat=<?=$lat1?>&lon=<?=$lon1?>">&nbsp;</a>   
             </div>
       </div>
</div>
<!--
<div id="footer">&copy; <?=date('Y');?> <?=$site_name?>, Inc. | <a href="mailto:<?=$email?>?subject=Feedback">Contact Us</a> &nbsp;&nbsp;&nbsp; <a href="<?=$pageglobal['facebook']?>"><img src="images/icon_facebook_16x16.gif" alt="facebook_icon" width="16" height="16" /></a> &nbsp;&nbsp;&nbsp; </div>
<div style='display:none;'><?php echo $pageglobal['googgle_map_api_keys']; ?></div>
-->
</body>
</html>
