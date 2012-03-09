<?php 
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

$todaestring=date('D, M j', mktime(0, 0, 0, $tomonth, $today, $toyear));


$query_cat="SELECT c.id FROM jos_categories AS c LEFT JOIN jos_categories AS p ON p.id=c.parent_id LEFT JOIN jos_categories AS gp ON gp.id=p.parent_id LEFT JOIN jos_categories AS ggp ON ggp.id=gp.parent_id WHERE c.access <= 2 AND c.published = 1 AND c.section = 'com_jevents'";
$rec_cat=mysql_query($query_cat);
while($row_cat=mysql_fetch_array($rec_cat)) {
$array_cat[]=$row_cat['id'];
$byday= strtoupper(substr(date('D',mktime(0, 0, 0, $tomonth, $today, $toyear)),0,2));
$arrstrcat=implode(',',array_merge(array(-1), $array_cat));
}

$start_date = date("Y-m-d");
$check_date = $start_date;
$end_date = date("Y-m-d", strtotime ("+7 day", strtotime($check_date)));
$i = 0;

$query_filter="SELECT rpt.*, ev.*, rr.*, det.*, ev.state as published,ev.created as created, loc.loc_id,loc.title as loc_title, loc.title as location, loc.street as loc_street, loc.description as loc_desc, loc.postcode as loc_postcode, loc.city as loc_city, loc.country as loc_country, loc.state as loc_state, loc.phone as loc_phone, loc.url as loc_url, loc.geolon as loc_lon,loc.geolat as loc_lat,loc.geozoom as loc_zoom, YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup , YEAR(rpt.endrepeat ) as ydn, MONTH(rpt.endrepeat ) as mdn, DAYOFMONTH(rpt.endrepeat ) as ddn , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup,HOUR(rpt.endrepeat) as hdn, MINUTE(rpt.endrepeat ) as mindn, SECOND(rpt.endrepeat) as sdn FROM jos_jevents_repetition as rpt LEFT JOIN jos_jevents_vevent as ev ON rpt.eventid = ev.ev_id LEFT JOIN jos_jevents_icsfile as icsf ON icsf.ics_id=ev.icsid LEFT JOIN jos_jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id LEFT JOIN jos_jevents_rrule as rr ON rr.eventid = rpt.eventid LEFT JOIN jos_jev_locations as loc ON loc.loc_id=det.location
LEFT JOIN jos_jev_locations as gloc ON gloc.loc_id=det.location LEFT JOIN jos_jev_peopleeventsmap as persmap ON det.evdet_id=persmap.evdet_id LEFT JOIN jos_jev_people as pers ON pers.pers_id=persmap.pers_id WHERE ev.catid IN(".$arrstrcat.") AND rpt.startrepeat BETWEEN '".$start_date." 00:00:00' AND '".$end_date." 23:59:59' AND ev.state=1 AND rpt.endrepeat BETWEEN '".$start_date." 00:00:00' AND '".$end_date." 23:59:59' AND ev.access <= 0 AND icsf.state=1 AND icsf.access <= 0 GROUP BY rpt.rp_id";

//WHERE ev.catid IN(".$arrstrcat.") AND rpt.endrepeat >= '".$end_date." 00:00:00' AND rpt.startrepeat <= '".$end_date." 23:59:59' AND ev.state=1 AND rpt.endrepeat>='".$start_date." 00:00:00' AND ev.access <= 0 AND icsf.state=1 AND icsf.access <= 0 GROUP BY rpt.rp_id";

$rec_filter=mysql_query($query_filter);
?>
		<li data-role="list-divider"><?php echo $_GET['id']; ?></li>
		<li style="height:65px;">
			<span>
				<img src="http://www.tapdestin.com/jquerymobile/dp-img.png" />
			</span>
			<div style="margin-left: 75px; margin-top: -6px; text-align: justify;"><span style="width:100px; vertical-align:t">
				<?php $intro = db_fetch("select introtext from `jos_content` where `title` = 'Events Page Introduction'");
				echo $intro; 
				?>.
			</span>
		</li>
		<li style="background:#FFFFFF;height:.5px;">&nbsp;</li>
		<?php while($row_filter=mysql_fetch_array($rec_filter)) {
				$startrpt=$row_filter['startrepeat'];
				$events_dates=substr($startrpt, 0, 10);
				$realtime=date("D M d",strtotime($events_dates));?>
		     	
				<li><a href="events_list.php?det=<?php echo $row_filter['eventdetail_id'];?>&ed=<?php echo $events_dates ?>&end=<?php echo $end_date;?>&rd=<?php echo $startrpt?>"><?php echo $realtime ?></a></li>
		<?php } ?>
		<!--<li><?//=$todaestring?></li>
		<li>Monday, May 02</li>
		<li>Tuesday, May 03</li>
		<li>Wednesday, May 04</li>
		<li>Thursday, May 05</li>
		<li>Friday, May 06</li>
		<li>Saturday, May 07</li>-->
    