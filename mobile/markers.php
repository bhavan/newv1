<?php
//get the locations


//$query = "SELECT * FROM jos_categories WHERE section='com_jevlocations2' AND published=1 ORDER BY id DESC";

//$connection = mysql_connect($host, $user, $pass) or die ("Unable to connect!");
//mysql_select_db($db, $connection) or die ("Unable to select database!");
//$result = mysql_query($query);

//use joomla db class
require_once("configuration.php");
$jconfig = new JConfig();

function parseToXML($htmlStr) 
{ 
	$xmlStr=str_replace('<','&lt;',$htmlStr); 
	$xmlStr=str_replace('>','&gt;',$xmlStr); 
	$xmlStr=str_replace('"','&quot;',$xmlStr); 
	$xmlStr=str_replace("'",'&#39;',$xmlStr); 
	$xmlStr=str_replace("&",'&amp;',$xmlStr); 
	return $xmlStr; 
} 

//db establish
$db_error = "I am sorry! We are maintaining the website, please try again later.";
$db_config = mysql_connect( $jconfig->host, $jconfig->user, $jconfig->password ) or die( $db_error );
mysql_select_db( $jconfig->db, $db_config ) or die( $db_error );

/*$result = mysql_query($query);


while($row = mysql_fetch_object($result))
{
	
	//echo $row->title."<br>";
	
}

$sql = "SELECT * FROM jos_categories WHERE section='com_jevents' AND published=1 ORDER BY id DESC";

//get the sections
$values = mysql_query($sql);


while($value = mysql_fetch_object($values))
{
	
	//echo $value->title."<br>";
	
}

//get all the locations
$sql = "SELECT * FROM jos_jev_locations";

//get the sections
$locations = mysql_query($sql);
while($location = mysql_fetch_object($locations))
{
	
	//echo $location->title."<br>";
	
}*/
//get all the events
//get all the locations
$sql = "SELECT DISTINCT (d.summary) as event, d.*, e.*, l.*, c.id as category, rp.* FROM  jos_jevents_vevent as e LEFT JOIN jos_jevents_vevdetail as d ON d.evdet_id=e.ev_id LEFT JOIN jos_jev_locations as l ON l.loc_id=d.location LEFT JOIN jos_jevents_categories AS c ON c.id=e.catid LEFT JOIN jos_categories AS jc ON jc.id=e.catid LEFT JOIN jos_jevents_repetition as rp ON rp.eventid=e.ev_id WHERE d.summary<>'' AND c.id<>'' AND jc.section='com_jevents' AND jc.published=1 AND l.title<>''";

//get the sections
$events = mysql_query($sql);
	header("Content-type: text/xml");

	// Creates an array of strings to hold the lines of the KML file.
	
	echo '<markers>';	
	$count = 0;
	while($event = mysql_fetch_object($events))
	{
			$now = strtotime(date('Y-m-d'));
			if($event->dtend>=$now)
			{
				$locationlink = "location_details.php?id=".$event->loc_id."&loccat=".$event->loccat;
				$datestart = date('Y-m-d',$event->dtstart);
				$dateend = date('Y-m-d',$event->dtend);
				$eventlink = "event_details.php?event_id=".$event->ev_id."&title=".urlencode($event->event)."&date=".$datestart."&rp_id=".$event->rp_id;
				if($event->image=='')
					$image = '';
				else
					$image = "images/stories/jevents/jevlocations/thumbnails/thumb_".$event->image;
				//echo '<marker name="'.parseToXML(mysql_real_escape_string($event->title)).'" address="'.parseToXML(mysql_real_escape_string($event->event)).'" lng="'. $event->geolon.'" lat="'.$event->geolat.'" category="'.$event->category.'"/>';
				echo '<marker ';
				echo 'name="' . parseToXML(utf8_encode($event->title)) . '" ';
				echo 'address="' . parseToXML(utf8_encode($event->event)) . '" ';
				echo 'lat="' . $event->geolat . '" ';
				echo 'lng="' . $event->geolon . '" ';
				echo 'category="' . $event->category . '" ';
				echo 'locationlink="' . parseToXML(utf8_encode($locationlink)) . '" ';
				echo 'eventlink="' . parseToXML(utf8_encode($eventlink)). '" ';
				echo 'image="' . parseToXML(utf8_encode($image)) . '" ';
				echo '/>';
				$count++;
			}

	}
	// End XML file
	echo '</markers>';
?> 