<?php
ini_set('error_reporting',1);
ini_set('display_errors',1);
include("../connection.php");

/* All REQUEST paramter variable  */
$catId		= isset($_GET['category_id']) ? $_GET['category_id']:0;
$eventId	= isset($_GET['id']) ? $_GET['id']:0;
$glat		= isset($_GET['latitude']) ? $_GET['latitude']:'';
$glon		= isset($_GET['longitude']) ? $_GET['longitude']:'';
$dfrom		= isset($_GET['from']) ? $_GET['from']:0;
$dto		= isset($_GET['to']) ? $_GET['to']:0;
$offset		= isset($_GET['offset']) ? $_GET['offset']:0;
$limit		= isset($_GET['limit']) ? $_GET['limit']:0;


// Session varialbe set for Latitute to calculate distance
$_SESSION['lat_device1'] = '';
if(isset($glat) && $glat != '' ){
	$_SESSION['lat_device1']	= $glat;
}

/* Session varialbe set for Lontitutde to calculate distance */
$_SESSION['lon_device1'] = '';
if (isset($glon) && $glon != 0){
	$_SESSION['lon_device1']	= $glon;
}


/* 
CASE: 1
Result		: Listing of Events from CATEGORY ID
Parameter	: category_id
API Request	: /event/?category_id=1
*/

if(isset($catId) && $catId != 0){
	
	$today = date('d'); $tomonth = date('m'); $toyear = date('Y');
	$select_query	= "SELECT rpt.rp_id,rpt.startrepeat,rpt.endrepeat,ev.catid,cat.title,evd.description,evd.location,evd.summary,cf.value FROM jos_jevents_vevent AS ev,jos_jevents_vevdetail AS evd, jos_categories AS cat,jos_jevents_repetition AS rpt,jos_jev_customfields AS cf WHERE rpt.eventid = ev.ev_id AND rpt.eventdetail_id = evd.evdet_id AND rpt.eventdetail_id = cf.evdet_id";
	
	if((isset($dfrom) && $dfrom != 0) && (isset($dto) && $dto != 0)){
		$fda = explode('-',$dfrom); $tda = explode('-',$dto);
		$select_query .= " AND rpt.startrepeat >= '".$fda[0]."-".$fda[1]."-".$fda[2]." 00:00:00' AND rpt.endrepeat<='".$tda[0]."-".$tda[1]."-".$tda[2]." 23:59:59'";
	}else{
		$select_query .= " AND rpt.startrepeat >= '".$toyear."-".$tomonth."-".$today." 00:00:00'";
	}	
	$select_query .= "AND ev.catid = cat.id AND ev.catid = $catId AND ev.state = 1";
	
	/* To check if Limit is given then apply in query */
	if(isset($limit) && $limit != 0){
		if(isset($offset) && $offset != 0)
			$select_query .= " limit $offset,$limit";
		else	
			$select_query .= " limit $limit";
	}
		
	$result			= mysql_query($select_query);
	$num_records	= mysql_num_rows($result);
	
	if($num_records > 0){
	
		/* Looping for Event Data */
		while($rs_ev_tbl = mysql_fetch_array($result)){
		
			/* Location table */
			if ((int) ($rs_ev_tbl['location'])) {
				$loc_qry = "select * from jos_jev_locations where loc_id=".$rs_ev_tbl['location'];		
				$location_query		= mysql_query($loc_qry);
				$rs_loc_tbl			= mysql_fetch_array($location_query);
				$lat2				= $rs_loc_tbl['geolat'];
				$lon2				= $rs_loc_tbl['geolon'];
			}
			/* Creating Jason Array variable $data */
			$value['id'] 					= $rs_ev_tbl['rp_id'];
			$value['title'] 				= $rs_ev_tbl['summary'];
			$value['category'] 				= $rs_ev_tbl['title'];
			$value['category_id']			= $rs_ev_tbl['catid'];
			$value['location']['latitude']	= $lat2;
			$value['location']['longitude']	= $lon2;
			$value['location']['zip']		= $rs_loc_tbl['postcode'];
			$value['location']['address']	= $rs_loc_tbl['street'];
			$value['location']['name']		= $rs_loc_tbl['title'];
			$value['location']['phone']		= $rs_loc_tbl['phone'];
			$value['location']['website']	= $rs_loc_tbl['url'];
			if($_SESSION['lat_device1'] != '' && $_SESSION['lon_device1']){
				$value['location']['distance']	= round(distance($_SESSION['lat_device1'], $_SESSION['lon_device1'], $lat2, $lon2,$dunit),'1');
			}else{
				$value['location']['distance'] = '';
			}
			$value['is_featured_event']		= $rs_ev_tbl['value'];
			$value['description']			= $rs_ev_tbl['description'];
			$value['image_url']				= "";
			$value['start_time']			= $rs_ev_tbl['startrepeat'];
			$value['end_time']				= $rs_ev_tbl['endrepeat'];
			
			/* Assigning Array values to $data array variable */
			$data[] = $value;
		}	
		$response = array(
	    	'data' => $data,
	    	'meta' => array(
	        'total' => $num_records,
	        'limit' => $limit != 0?$limit:$num_records,
	        'offset' => $offset != 0?$offset:0
	    	)
		);
		//echo "<pre>";
		//print_r($response);
		header('Content-type: application/json');
		echo json_encode($response);
	}else{
		$data["error"] = "Not Found";
		header('Content-type: application/json');
		echo json_encode($data);
	}
/*------------------------------------*/
	
}elseif(isset($eventId) && $eventId != 0){
	/* 
	CASE: 2
	Result		: Listing of Events from EVENT ID (This will be REPETATION ID))
	Parameter	: id
	API Request	: /event/?id=1
	*/

	$select_query	= "SELECT rpt.rp_id,ev.catid,cat.title,rpt.startrepeat,rpt.endrepeat,evd.description,evd.location,evd.summary,cf.value FROM jos_jevents_vevent AS ev,jos_jevents_repetition AS rpt,jos_categories AS cat,jos_jevents_vevdetail
 AS evd, jos_jev_customfields AS cf	WHERE ev.ev_id= rpt.eventid AND ev.catid=cat.id AND rpt.eventdetail_id = evd.evdet_id AND rpt.eventdetail_id = cf.evdet_id AND rpt.rp_id = $eventId AND ev.state=1";
	
	if((isset($dfrom) && $dfrom != 0) && (isset($dto) && $dto != 0)){
		$fda = explode('-',$dfrom); $tda = explode('-',$dto);
		$select_query .= " AND rpt.startrepeat >= '".$fda[0]."-".$fda[1]."-".$fda[2]." 00:00:00' AND rpt.endrepeat<='".$tda[0]."-".$tda[1]."-".$tda[2]." 23:59:59'";
	}	

	$result			= mysql_query($select_query);
	$num_records	= mysql_num_rows($result);

	if($num_records > 0){

		//Looping Repetation table data
		while($rs_ev_tbl = mysql_fetch_array($result)){

			//Creating Image array from Event description
			$imgArray = explode('<img src="',$rs_ev_tbl['description']);
			$evImageArray = array();
			
			for($i=0;$i<count($imgArray);$i++){
				if(strstr($imgArray[$i],'" />',true) != '')
					$evImageArray[] = strstr($imgArray[$i],'" />',true); // As of PHP 5.3.0
			}	
				
			// Location table
			if ((int) ($rs_ev_tbl['location'])) {
				$loc_qry		= "select *  from jos_jev_locations where loc_id=".$rs_ev_tbl['location'];
				$location_query	= mysql_query($loc_qry);
				$rs_loc_tbl		= mysql_fetch_array($location_query);
				$lat2			= $rs_loc_tbl['geolat'];
				$lon2			= $rs_loc_tbl['geolon'];
			}

			// Creating Jason Array variable $data	
			$data['id'] 					= $rs_ev_tbl['rp_id'];
			$data['title'] 					= $rs_ev_tbl['summary'];
			$data['category'] 				= $rs_ev_tbl['title'];
			$data['category_id']			= $rs_ev_tbl['catid'];
			$data['location']['latitude']	= $lat2;
			$data['location']['longitude']	= $lon2;
			$data['location']['zip']		= $rs_loc_tbl['postcode'];
			$data['location']['address']	= $rs_loc_tbl['street'];
			$data['location']['name']		= $rs_loc_tbl['title'];
			$data['location']['phone']		= $rs_loc_tbl['phone'];
			$data['location']['website']	= $rs_loc_tbl['url'];
			
			if($_SESSION['lat_device1'] != '' && $_SESSION['lon_device1']){
				$data['location']['distance']	= round(distance($_SESSION['lat_device1'], $_SESSION['lon_device1'], $lat2, $lon2,$dunit),'1');
			}else{
				$data['location']['distance'] = '';
			}
			
			$data['is_featured_event']		= $rs_ev_tbl['value'];
			$data['description']			= $rs_ev_tbl['description'];
			$data['image_url']				= "";
			$data['start_time']				= $rs_ev_tbl['startrepeat'];
			$data['end_time']				= $rs_ev_tbl['endrepeat'];
			$data['images']					= $evImageArray;
		}
	}else{
		$data["error"] = "Not Found";
	}	
	//echo "<pre>";
	//print_r($data);
	header('Content-type: application/json');
	echo json_encode($data);
/*------------------------------------*/

}else{
	
	/* 
	CASE: 0
	Result		: Listing of All Events
	Parameter	: N/A
	API Request	: /event/
	*/
	$today = date('d'); $tomonth = date('m'); $toyear = date('Y');
	$select_query	= "SELECT rpt.rp_id,rpt.startrepeat,rpt.endrepeat,ev.ev_id,ev.catid,cat.title,evd.description,evd.location,evd.summary,cf.value FROM jos_jevents_vevent AS ev,jos_jevents_vevdetail AS evd, jos_categories AS cat,jos_jevents_repetition AS rpt,jos_jev_customfields AS cf WHERE rpt.eventid = ev.ev_id AND rpt.eventdetail_id = evd.evdet_id AND rpt.eventdetail_id = cf.evdet_id";
	
	if((isset($dfrom) && $dfrom != 0) && (isset($dto) && $dto != 0)){
		$fda = explode('-',$dfrom); $tda = explode('-',$dto);
		$select_query .= " AND rpt.startrepeat >= '".$fda[0]."-".$fda[1]."-".$fda[2]." 00:00:00' AND rpt.endrepeat<='".$tda[0]."-".$tda[1]."-".$tda[2]." 23:59:59'";
	}else{
		$select_query .= " AND rpt.startrepeat >= '".$toyear."-".$tomonth."-".$today." 00:00:00'";
	}	
	
	$select_query .= " AND ev.catid = cat.id AND ev.state = 1";
	
	/* To check if Limit is given then apply in query */
	if(isset($limit) && $limit != 0){
		if(isset($offset) && $offset != 0)
			$select_query .= " limit $offset,$limit";
		else	
			$select_query .= " limit $limit";
	}
		
	$result			= mysql_query($select_query);
	$num_records	= mysql_num_rows($result);
	
	if($num_records > 0){
	
		/* Looping for Event Data */
		while($rs_ev_tbl = mysql_fetch_array($result)){
			
			/* Location table */
			if ((int) ($rs_ev_tbl['location'])) {
				$loc_qry = "select * from jos_jev_locations where loc_id=".$rs_ev_tbl['location'];		
				$location_query		= mysql_query($loc_qry);
				$rs_loc_tbl			= mysql_fetch_array($location_query);
				$lat2				= $rs_loc_tbl['geolat'];
				$lon2				= $rs_loc_tbl['geolon'];
			}
			/* Creating Jason Array variable $data */
			$value['id'] 					= $rs_ev_tbl['rp_id'];
			$value['title'] 				= $rs_ev_tbl['summary'];
			$value['category'] 				= $rs_ev_tbl['title'];
			$value['category_id']			= $rs_ev_tbl['catid'];
			$value['location']['latitude']	= $lat2;
			$value['location']['longitude']	= $lon2;
			$value['location']['zip']		= $rs_loc_tbl['postcode'];
			$value['location']['address']	= $rs_loc_tbl['street'];
			$value['location']['name']		= $rs_loc_tbl['title'];
			$value['location']['phone']		= $rs_loc_tbl['phone'];
			$value['location']['website']	= $rs_loc_tbl['url'];
			
			if($_SESSION['lat_device1'] != '' && $_SESSION['lon_device1']){
				$value['location']['distance']	= round(distance($_SESSION['lat_device1'], $_SESSION['lon_device1'], $lat2, $lon2,$dunit),'1');
			}else{
				$value['location']['distance'] = '';
			}
			
			$value['is_featured_event']		= $rs_ev_tbl['value'];
			$value['description']			= $rs_ev_tbl['description'];
			$value['image_url']				= "";
			$value['start_time']			= $rs_ev_tbl['startrepeat'];
			$value['end_time']				= $rs_ev_tbl['endrepeat'];
			
			/* Assigning Array values to $data array variable */
			$data[] = $value;
		}	
		$response = array(
	    	'data' => $data,
	    	'meta' => array(
	        'total' => $num_records,
	        'limit' => $limit != 0?$limit:$num_records,
			'offset' => $offset != 0?$offset:0
	    	)
		);
		//echo "<pre>";
		//print_r($response);
		header('Content-type: application/json');
		echo json_encode($response);
	}else{
		if($dto < $dfrom){
			$data["error"] = "Bad Request";
		}else{
			$data["error"] = "Not Found";	
		}	
		header('Content-type: application/json');
		echo json_encode($data);
	}
}	




/* ************************************************************ */
/* All Useful Functions */

// Function to calculate Location Distance
function distance($lat1, $lon1, $lat2, $lon2, $unit) { 

	$theta = $lon1 - $lon2; 
	$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)); 
	$dist = acos($dist); 
	$dist = rad2deg($dist); 
	$miles = $dist * 60 * 1.1515;
	$unit = strtoupper($unit);
	if($unit == "KMS") {
		return ($miles * 1.609344); 
	}else if($unit == "N"){
		return ($miles * 0.8684);
	}else{
		return $miles;
	}
}

?>