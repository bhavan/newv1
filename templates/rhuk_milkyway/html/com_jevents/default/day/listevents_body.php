<?php 
defined('_JEXEC') or die('Restricted access');

$cfg	 = & JEVConfig::getInstance();

$data = $this->datamodel->getDayData( $this->year, $this->month, $this->day );

$cfg = & JEVConfig::getInstance();
$Itemid = JEVHelper::getItemid();
$cfg = & JEVConfig::getInstance();

ob_end_clean();

header('Content-type: text/xml', true);
echo '<?xml version="1.0" encoding="UTF-8"?>';?>
<TodayEvents>
<?php
//FOR DISTANCE IN KMS - MULTIPLY BY 1.609344
//DISTANCE IN MILES
//Select (((acos(sin((@Lat * pi() / 180)) * sin((E.Event_Latitude * pi() / 180)) + cos((@Lat * pi() / 180)) * cos((E.Event_Latitude * pi() / 180)) * cos(((@Lon - E.Event_Longitude) * pi() / 180)))) * 180 / pi()) * 60 * 1.1515) as distance
// Timeless Events First
//print_r($data);

if (count($data['hours']['timeless']['events'])>0){
	$start_time = JText::_("Timeless");

	foreach ($data['hours']['timeless']['events'] as $row) {
		rowentry($row, $start_time);

	}
}

for ($h=0;$h<24;$h++){
	if (count($data['hours'][$h]['events'])>0){
		$start_time = ($cfg->get('com_calUseStdTime')== '1') ? strftime("%I:%M%p",$data['hours'][$h]['hour_start']) : strftime("%H:%M",$data['hours'][$h]['hour_start']);
		foreach ($data['hours'][$h]['events'] as $row) {
			$start_time = ($cfg->get('com_calUseStdTime')== '1') ? strftime("%I:%M%p",$row->getUnixStartTime()) : strftime("%H:%M",$row->getUnixStartTime());
			rowentry($row, $start_time);
			/*
			if (count($data['hours'][$h]['events'])>0){
			$start_time = ($cfg->get('com_calUseStdTime')== '1') ? strftime("%I:%M%p",$data['hours'][$h]['hour_start']) : strftime("%H:%M",$data['hours'][$h]['hour_start']);

			echo '<tr><td class="ev_td_left">' . $start_time . '</td>' . "\n";
			echo '<td class="ev_td_right"><ul class="ev_ul">' . "\n";
			foreach ($data['hours'][$h]['events'] as $row) {
			$listyle = 'style="border-color:'.$row->bgcolor().';"';
			echo "<li class='ev_td_li' $listyle>\n";

			$this->viewEventRowNew ( $row);
			echo '&nbsp;::&nbsp;';
			$this->viewEventCatRowNew($row);
			echo "</li>\n";
			}
			echo "</ul></td></tr>\n";
			}
			*/
		}
	}
}

?>
</TodayEvents>
<?php
exit();

function rowentry ($row,$start_time ){
	//var_dump($row);echo "<br/>";
	$lat = JRequest::getFloat("la",0);
	$lon = JRequest::getFloat("lo",0);
	$distance = (((acos(sin(($lat * pi() / 180)) * sin(($row->_loc_lat * pi() / 180)) + cos(($lat * pi() / 180)) * cos(($row->_loc_lat * pi() / 180)) * cos((($lon - $row->_loc_lon) * pi() / 180)))) * 180 / pi()) * 60 * 1.1515);
	//$location = $row->location()." lo=".$lon." la=".$lat;
	
	$location = $row->location();
	echo '<Event id="'.$row->rp_id().'" name="'.htmlspecialchars($row->title(),ENT_NOQUOTES).'" timing="'.$start_time.'" location="'.htmlspecialchars($location).'" phone="'.$row->_loc_phone.'" lat="'.$row->_loc_lat.'" long="'.$row->_loc_lon.'" distance="'.round($distance,1).'" community="" />';

}