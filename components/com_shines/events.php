<?php
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





$_REQUEST['eventdate1'] = trim($_REQUEST['eventdate1']);



if(!empty($_REQUEST['eventdate1'])){



    $today = date('d',strtotime($_REQUEST['eventdate1']));

    $tomonth = date('m',strtotime($_REQUEST['eventdate1']));

    $toyear = date('Y',strtotime($_REQUEST['eventdate1']));

}





$todaestring=date('D, M j', mktime(0, 0, 0, $tomonth, $today, $toyear));



$query_cat="SELECT c.id FROM jos_categories AS c LEFT JOIN jos_categories AS p ON p.id=c.parent_id LEFT JOIN jos_categories AS gp ON gp.id=p.parent_id LEFT JOIN jos_categories AS ggp ON ggp.id=gp.parent_id WHERE c.access <= 2 AND c.published = 1 AND c.section = 'com_jevents'";

$rec_cat=mysql_query($query_cat);

while($row_cat=mysql_fetch_array($rec_cat))

$array_cat[]=$row_cat['id'];

$byday= strtoupper(substr(date('D',mktime(0, 0, 0, $tomonth, $today, $toyear)),0,2));

$arrstrcat=implode(',',array_merge(array(-1), $array_cat));

$query_filter="SELECT rpt.*, ev.*, rr.*, det.*, ev.state as published , loc.loc_id,loc.title as loc_title, loc.title as location, loc.street as loc_street, loc.description as loc_desc, loc.postcode as loc_postcode, loc.city as loc_city, loc.country as loc_country, loc.state as loc_state, loc.phone as loc_phone , loc.url as loc_url    , loc.geolon as loc_lon , loc.geolat as loc_lat , loc.geozoom as loc_zoom    , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup , YEAR(rpt.endrepeat ) as ydn, MONTH(rpt.endrepeat ) as mdn, DAYOFMONTH(rpt.endrepeat ) as ddn , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup , HOUR(rpt.endrepeat ) as hdn, MINUTE(rpt.endrepeat ) as mindn, SECOND(rpt.endrepeat ) as sdn FROM jos_jevents_repetition as rpt LEFT JOIN jos_jevents_vevent as ev ON rpt.eventid = ev.ev_id LEFT JOIN jos_jevents_icsfile as icsf ON icsf.ics_id=ev.icsid LEFT JOIN jos_jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id LEFT JOIN jos_jevents_rrule as rr ON rr.eventid = rpt.eventid LEFT JOIN jos_jev_locations as loc ON loc.loc_id=det.location LEFT JOIN jos_jev_peopleeventsmap as persmap ON det.evdet_id=persmap.evdet_id LEFT JOIN jos_jev_people as pers ON pers.pers_id=persmap.pers_id WHERE ev.catid IN(".$arrstrcat.") AND rpt.endrepeat >= '".$toyear."-".$tomonth."-".$today." 00:00:00' AND rpt.startrepeat <= '".$toyear."-".$tomonth."-".$today." 23:59:59' AND ev.state=1 AND rpt.endrepeat>='".date('Y')."-".date('m')."-".date('d')." 00:00:00' AND ev.access <= 0 AND icsf.state=1 AND icsf.access <= 0 and ((YEAR(rpt.startrepeat)=".$toyear." and MONTH(rpt.startrepeat )=".$tomonth." and DAYOFMONTH(rpt.startrepeat )=".$today.") or freq<>'WEEKLY')GROUP BY rpt.rp_id";



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

<meta content="text/html; charset=iso-8859-1" http-equiv="Content-Type" />

<link href="pics/homescreen.gif" rel="apple-touch-icon" />

<meta content="minimum-scale=1.0, width=device-width, maximum-scale=0.6667, user-scalable=no" name="viewport" />

<meta content="destin, vacactions in destin florida, destin, florida, real estate, sandestin resort, beaches, destin fl, maps of florida, hotels, hotels in florida, destin fishing, destin hotels, best florida beaches, florida beach house rentals, destin vacation rentals for destin, destin real estate, best beaches in florida, condo rentals in destin, vacaction rentals, fort walton beach, destin fishing, fl hotels, destin restaurants, florida beach hotels, hotels in destin, beaches in florida, destin, destin fl" name="keywords" />

<meta content="Destin Florida's FREE iPhone application and website guide to local events, live music, restaurants and attractions" name="description" />

<script type="text/javascript">
	// TOUCH-EVENTS SINGLE-FINGER SWIPE-SENSING JAVASCRIPT
	// Courtesy of PADILICIOUS.COM and MACOSXAUTOMATION.COM
	
	// this script can be used with one or more page elements to perform actions based on them being swiped with a single finger

	var triggerElementID = null; // this variable is used to identity the triggering element
	var fingerCount = 0;
	var startX = 0;
	var startY = 0;
	var curX = 0;
	var curY = 0;
	var deltaX = 0;
	var deltaY = 0;
	var horzDiff = 0;
	var vertDiff = 0;
	var minLength = 72; // the shortest distance the user may swipe
	var swipeLength = 0;
	var swipeAngle = null;
	var swipeDirection = null;
	
	// The 4 Touch Event Handlers
	
	// NOTE: the touchStart handler should also receive the ID of the triggering element
	// make sure its ID is passed in the event call placed in the element declaration, like:
	// <div id="picture-frame" ontouchstart="touchStart(event,'picture-frame');"  ontouchend="touchEnd(event);" ontouchmove="touchMove(event);" ontouchcancel="touchCancel(event);">

	function touchStart(event,passedName) {
		// disable the standard ability to select the touched object
		// event.preventDefault();
		// get the total number of fingers touching the screen
		fingerCount = event.touches.length;
		// since we're looking for a swipe (single finger) and not a gesture (multiple fingers),
		// check that only one finger was used
		if ( fingerCount == 1 ) {
			// get the coordinates of the touch
			startX = event.touches[0].pageX;
			startY = event.touches[0].pageY;
			// store the triggering element ID
			triggerElementID = passedName;
		} else {
			// more than one finger touched so cancel
			touchCancel(event);
		}
	}

	function touchMove(event) {
		//event.preventDefault();
		if ( event.touches.length == 1 ) {
			curX = event.touches[0].pageX;
			curY = event.touches[0].pageY;
		} else {
			touchCancel(event);
		}
	}
	
	function touchEnd(event) {
		event.preventDefault();
		// check to see if more than one finger was used and that there is an ending coordinate
		if ( fingerCount == 1 && curX != 0 ) {
			// use the Distance Formula to determine the length of the swipe
			swipeLength = Math.round(Math.sqrt(Math.pow(curX - startX,2) + Math.pow(curY - startY,2)));
			// if the user swiped more than the minimum length, perform the appropriate action
			if ( swipeLength >= minLength ) {
				caluculateAngle();
				determineSwipeDirection();
				processingRoutine();
				touchCancel(event); // reset the variables
			} else {
				touchCancel(event);
			}	
		} else {
			touchCancel(event);
		}
	}

	function touchCancel(event) {
		// reset the variables back to default values
		fingerCount = 0;
		startX = 0;
		startY = 0;
		curX = 0;
		curY = 0;
		deltaX = 0;
		deltaY = 0;
		horzDiff = 0;
		vertDiff = 0;
		swipeLength = 0;
		swipeAngle = null;
		swipeDirection = null;
		triggerElementID = null;
	}
	
	function caluculateAngle() {
		var X = startX-curX;
		var Y = curY-startY;
		var Z = Math.round(Math.sqrt(Math.pow(X,2)+Math.pow(Y,2))); //the distance - rounded - in pixels
		var r = Math.atan2(Y,X); //angle in radians (Cartesian system)
		swipeAngle = Math.round(r*180/Math.PI); //angle in degrees
		if ( swipeAngle < 0 ) { swipeAngle =  360 - Math.abs(swipeAngle); }
	}
	
	function determineSwipeDirection() {
		if ( (swipeAngle <= 45) && (swipeAngle >= 0) ) {
			swipeDirection = 'left';
		} else if ( (swipeAngle <= 360) && (swipeAngle >= 315) ) {
			swipeDirection = 'left';
		} else if ( (swipeAngle >= 135) && (swipeAngle <= 225) ) {
			swipeDirection = 'right';
		} else if ( (swipeAngle > 45) && (swipeAngle < 135) ) {
			swipeDirection = 'down';
		} else {
			swipeDirection = 'up';
		}
	}
	
	function processingRoutine() {
		var swipedElement = document.getElementById(triggerElementID);
		if ( swipeDirection == 'right' ) {
			// REPLACE WITH YOUR ROUTINES
			window.location.href = 'events.php?d=<?=date('d',mktime(0, 0, 0,$tomonth ,$today-1, $toyear));?>&m=<?=date('m',mktime(0, 0, 0,$tomonth ,$today-1, $toyear));?>&Y=<?=date('Y',mktime(0, 0, 0,$tomonth ,$today-1, $toyear));?>&lat=<?php $lat1?>&lon=<?php $lon1?>';
		} else if ( swipeDirection == 'left' ) {
			// REPLACE WITH YOUR ROUTINES
			window.location.href = 'events.php?d=<?=date('d',mktime(0, 0, 0,$tomonth ,$today+1, $toyear));?>&m=<?=date('m',mktime(0, 0, 0,$tomonth ,$today+1, $toyear));?>&Y=<?=date('Y',mktime(0, 0, 0,$tomonth ,$today+1, $toyear));?>&lat=<?php $lat1?>&lon=<?php $lon1?>';
		}
	}

</script>

<link href="/components/com_shines/css/style_new_24oct2011.css" rel="stylesheet" media="screen" type="text/css" />

<script src="/components/com_shines/javascript/functions.js" type="text/javascript"></script>

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

                if(document.getElementById('date3').value != ''){

                    document.events1.submit(); //#DD#

                }else{

                    document.events.submit(); //#DD#

                }   

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

    require($_SERVER['DOCUMENT_ROOT']."/partner/".$_SESSION['tpl_folder_name']."/tpl/iphone_events.tpl");

    ?>

<!--

<div id="footer">&copy; <?=date('Y');?> <?=$site_name?>, Inc. | <a href="mailto:<?=$email?>?subject=Feedback">Contact Us</a> &nbsp;&nbsp;&nbsp; <a href="<?=$pageglobal['facebook']?>"><img src="images/icon_facebook_16x16.gif" alt="facebook_icon" width="16" height="16" /></a> &nbsp;&nbsp;&nbsp; </div>

<div style='display:none;'><?php echo $pageglobal['googgle_map_api_keys']; ?></div>

-->

</body>

</html>

