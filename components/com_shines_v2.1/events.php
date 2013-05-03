<?php

if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();

session_start();
include("connection.php");
include("iadbanner.php");

function distance($lat1, $lon1, $lat2, $lon2, $unit) { 
  $theta = $lon1 - $lon2; 
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)); 
  $dist = acos($dist); 
  $dist = rad2deg($dist); 
  $miles = $dist * 60 * 1.1515;
  $unit = strtoupper($unit);

  if ($unit == "KMS") {
    return ($miles * 1.609344); 
  } else if ($unit == "N") {
      return ($miles * 0.8684);
    } else {
        return $miles;
      }
}

if (isset($_REQUEST['lat']) && $_REQUEST['lat'] != "" )
{$_SESSION['lat_device1']=$_REQUEST['lat'];
$lat1=$_SESSION['lat_device1'];
}

if (isset($_REQUEST['lon']) && $_REQUEST['lon'] != "" )
{$_SESSION['lon_device1']=$_REQUEST['lon'];
$lon1=$_SESSION['lon_device1'];
}

$timeZoneArray 	= explode(':',$timezone);
$totalHours 	= date("H") + $timeZoneArray[0];
$totalMinutes = date("i") + $timeZoneArray[1];
$totalSeconds = date("s") + $timeZoneArray[2];

if ($_REQUEST['d']=="")
$today=date('d', mktime($totalHours, $totalMinutes, $totalSeconds));
else
$today=$_REQUEST['d'];
if ($_REQUEST['m']=="")
$tomonth=date('m',mktime($totalHours, $totalMinutes, $totalSeconds));
else
$tomonth=$_REQUEST['m'];
if ($_REQUEST['Y']=="")
$toyear=date('Y',mktime($totalHours, $totalMinutes, $totalSeconds));
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

$todaestring=date('l, j M', mktime(0, 0, 0, $tomonth, $today, $toyear));

$query_cat="SELECT c.id FROM jos_categories AS c LEFT JOIN jos_categories AS p ON p.id=c.parent_id LEFT JOIN jos_categories AS gp ON gp.id=p.parent_id LEFT JOIN jos_categories AS ggp ON ggp.id=gp.parent_id WHERE c.access <= 2 AND c.published = 1 AND c.section = 'com_jevents'";

$rec_cat=mysql_query($query_cat);
while($row_cat=mysql_fetch_array($rec_cat))
$array_cat[]=$row_cat['id'];
$byday= strtoupper(substr(date('D',mktime(0, 0, 0, $tomonth, $today, $toyear)),0,2));
$arrstrcat=implode(',',array_merge(array(-1), $array_cat));

$query_filter="SELECT rpt.*, ev.*, rr.*, det.*, ev.state as published , loc.loc_id,loc.title as loc_title, loc.title as location, loc.street as loc_street, loc.description as loc_desc, loc.postcode as loc_postcode, loc.city as loc_city, loc.country as loc_country, loc.state as loc_state, loc.phone as loc_phone , loc.url as loc_url    , loc.geolon as loc_lon , loc.geolat as loc_lat , loc.geozoom as loc_zoom    , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup , YEAR(rpt.endrepeat ) as ydn, MONTH(rpt.endrepeat ) as mdn, DAYOFMONTH(rpt.endrepeat ) as ddn , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup , HOUR(rpt.endrepeat ) as hdn, MINUTE(rpt.endrepeat ) as mindn, SECOND(rpt.endrepeat ) as sdn FROM jos_jevents_repetition as rpt LEFT JOIN jos_jevents_vevent as ev ON rpt.eventid = ev.ev_id LEFT JOIN jos_jevents_icsfile as icsf ON icsf.ics_id=ev.icsid LEFT JOIN jos_jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id LEFT JOIN jos_jevents_rrule as rr ON rr.eventid = rpt.eventid LEFT JOIN jos_jev_locations as loc ON loc.loc_id=det.location LEFT JOIN jos_jev_peopleeventsmap as persmap ON det.evdet_id=persmap.evdet_id LEFT JOIN jos_jev_people as pers ON pers.pers_id=persmap.pers_id WHERE ev.catid IN(".$arrstrcat.") AND rpt.endrepeat >= '".$toyear."-".$tomonth."-".$today." 00:00:00' AND rpt.startrepeat <= '".$toyear."-".$tomonth."-".$today." 23:59:59' AND ev.state=1 AND rpt.endrepeat>='".date('Y',mktime($totalHours, $totalMinutes, $totalSeconds))."-".date('m',mktime($totalHours, $totalMinutes, $totalSeconds))."-".date('d', mktime($totalHours, $totalMinutes, $totalSeconds))." 00:00:00' AND ev.access <= 0 AND icsf.state=1 AND icsf.access <= 0 and ((YEAR(rpt.startrepeat)=".$toyear." and MONTH(rpt.startrepeat )=".$tomonth." and DAYOFMONTH(rpt.startrepeat )=".$today.") or freq<>'WEEKLY')GROUP BY rpt.rp_id";

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
	
	/*Feature Event Query By Akash*/

/*Last Day of the Month*/
$LD = Date('d', strtotime("+30 days"));
$LM = Date('m', strtotime("+30 days"));
$LY = Date('y', strtotime("+30 days"));
	
$query_featuredeve="SELECT rpt.*, ev.*, rr.*, det.*, ev.state as published , loc.loc_id,loc.title as loc_title, loc.title as location, loc.street as loc_street, loc.description as loc_desc, loc.postcode as loc_postcode, loc.city as loc_city, loc.country as loc_country, loc.state as loc_state, loc.phone as loc_phone , loc.url as loc_url    , loc.geolon as loc_lon , loc.geolat as loc_lat , loc.geozoom as loc_zoom , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup , YEAR(rpt.endrepeat ) as ydn, MONTH(rpt.endrepeat ) as mdn, DAYOFMONTH(rpt.endrepeat ) as ddn , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup , HOUR(rpt.endrepeat ) as hdn, MINUTE(rpt.endrepeat ) as mindn, SECOND(rpt.endrepeat ) as sdn FROM jos_jevents_repetition as rpt LEFT JOIN jos_jevents_vevent as ev ON rpt.eventid = ev.ev_id LEFT JOIN jos_jevents_icsfile as icsf ON icsf.ics_id=ev.icsid LEFT JOIN jos_jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id LEFT JOIN jos_jevents_rrule as rr ON rr.eventid = rpt.eventid LEFT JOIN jos_jev_locations as loc ON loc.loc_id=det.location LEFT JOIN jos_jev_peopleeventsmap as persmap ON det.evdet_id=persmap.evdet_id LEFT JOIN jos_jev_people as pers ON pers.pers_id=persmap.pers_id WHERE ev.catid IN(".$arrstrcat.") AND rpt.endrepeat >= '".$toyear."-".$tomonth."-".$today." 00:00:00' AND rpt.startrepeat <= '".$LY."-".$LM."-".$LD." 23:59:59' AND ev.state=1 AND rpt.endrepeat>='".date('Y',mktime($totalHours, $totalMinutes, $totalSeconds))."-".date('m',mktime($totalHours, $totalMinutes, $totalSeconds))."-".date('d', mktime($totalHours, $totalMinutes, $totalSeconds))." 00:00:00' AND ev.access <= 0 AND icsf.state=1 AND icsf.access <= 0 and ((YEAR(rpt.startrepeat)=".$LY." and MONTH(rpt.startrepeat )=".$LM." and DAYOFMONTH(rpt.startrepeat )=".$LD.") or freq<>'WEEKLY')GROUP BY rpt.rp_id";	

$featured_filter=mysql_query($query_featuredeve);
while($fea_filter=mysql_fetch_array($featured_filter)){
    $fea[]=$fea_filter['rp_id'];

}
if (count($fea))
	$feachk=implode(',',$fea);
else
	$feachk=0;
	$fea_query="select *,DATE_FORMAT(`startrepeat`,'%h:%i %p') as timestart, DATE_FORMAT(`endrepeat`,'%h:%i%p') as timeend from jos_jevents_repetition where rp_id in ($feachk) ORDER BY  DATE_FORMAT(`startrepeat`,'%H%i') ASC ";
	$fea_rec=mysql_query($fea_query) or die(mysql_error());
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?=$site_name?></title>
<meta content="yes" name="apple-mobile-web-app-capable" />
<meta content="index,follow" name="robots" />
<meta content="text/html; charset=iso-8859-1" http-equiv="Content-Type" />
<link rel="shortcut icon" href="images/l/apple-touch-icon.png">
<link href="pics/startup.png" rel="apple-touch-startup-image" />
<meta content="minimum-scale=1.0, width=device-width, maximum-scale=0.6667, user-scalable=no" name="viewport" />
<meta content="destin, vacactions in destin florida, destin, florida, real estate, sandestin resort, beaches, destin fl, maps of florida, hotels, hotels in florida, destin fishing, destin hotels, best florida beaches, florida beach house rentals, destin vacation rentals for destin, destin real estate, best beaches in florida, condo rentals in destin, vacaction rentals, fort walton beach, destin fishing, fl hotels, destin restaurants, florida beach hotels, hotels in destin, beaches in florida, destin, destin fl" name="keywords" />
<meta content="Destin Florida's FREE iPhone application and website guide to local events, live music, restaurants and attractions" name="description" />
<link href="/components/com_shines_v2.1/css/style.css" rel="stylesheet" media="screen" type="text/css" />
<link href="../../mobiscroll/css/mobiscroll-1.5.1.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="/components/com_shines_v2.1/javascript/mobileswipe.js"></script>
<script type="text/javascript">
var iWebkit;if(!iWebkit){iWebkit=window.onload=function(){function fullscreen(){var a=document.getElementsByTagName("a");for(var i=0;i<a.length;i++){if(a[i].className.match("noeffect")){}else{a[i].onclick=function(){window.location=this.getAttribute("href");return false}}}}function hideURLbar(){window.scrollTo(0,0.9)}iWebkit.init=function(){fullscreen();hideURLbar()};iWebkit.init()}}
</script>
<script type="text/javascript">
    function linkClicked(link) { document.location = link; }
</script>
<script src="http://code.jquery.com/jquery-1.6.2.min.js"></script>
<script src="../../mobiscroll/js/mobiscroll-1.5.1.js" type="text/javascript"></script>
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
             $('#date3').scroller({ preset: 'date' });
            $('#custom').scroller({ showOnFocus: false });
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
               $('#date3').scroller('destroy').scroller({ preset: 'date', theme: t, mode: m });
                $('#custom').scroller('destroy').scroller({ showOnFocus: false, theme: t, mode: m });
            });
        });
</script>
    <?php include($_SERVER['DOCUMENT_ROOT']."/ga.php"); ?>
</head>
<body>
<?php
$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
if(stripos($ua,'android') == true) { ?>
  <div class="iphoneads" style="vertical-align:bottom;"></div>
  <?php } 
  else {
  ?>
  <div class="iphoneads" style="vertical-align:bottom;">
    <?php m_show_banner('iphone-events-screen'); ?>
  </div>
  <?php } ?>
<?php
    /* Code added for iphone_places.tpl */
    require($_SERVER['DOCUMENT_ROOT']."/partner/".$_SESSION['tpl_folder_name']."/tpl_v2.1/iphone_events.tpl");
    ?>
<!--
<div id="footer">&copy; <?=date('Y');?> <?=$site_name?>, Inc. | <a href="mailto:<?=$email?>?subject=Feedback">Contact Us</a> &nbsp;&nbsp;&nbsp; <a href="<?=$pageglobal['facebook']?>"><img src="images/icon_facebook_16x16.gif" alt="facebook_icon" width="16" height="16" /></a> &nbsp;&nbsp;&nbsp; </div>
<div style='display:none;'><?php echo $pageglobal['googgle_map_api_keys']; ?></div>
-->
</body>
</html>