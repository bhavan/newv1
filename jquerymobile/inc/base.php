<?php
function _init() {
  global $var;
  include_once($var->inc_path.'util.php');
  include_once($var->inc_path.'session.php');
  include_once($var->inc_path.'db.php');
  include_once($var->inc_path.'validate.php');
  include_once($var->inc_path.'mbase.php');
  
  date_default_timezone_set(@date_default_timezone_get());
  db_init();
  session_init();
  $var->get = $_GET;
  $var->post = $_POST;
  if(isset($var->post['formname'])) {
    validate();
  }
  $var->request_uri = $_SERVER['REQUEST_URI'];
  if ($_SERVER['REQUEST_URI']=='/')
  $var->request_uri='/index.php';
  $requestarr=explode('?',$var->request_uri);
  if ($requestarr)
  $var->request_uri=$requestarr[0];
  
  if(isset($_SERVER['HTTP_REFERER'])) {
    $var->http_referer = $_SERVER['HTTP_REFERER'];
  } else {
    $var->http_referer = '/';
  }
  load_config();
  $var->abs_srv_path = str_replace('testsite'.DS.'inc', '', dirname(__FILE__));
  $var->abs_srv_path = str_replace('inc', '', dirname(__FILE__));
  //fprint(dirname(__FILE__));
}

function load_config() {
  global $var;

  $pageglobal = db_fetch("select * from `jos_pageglobal`");
  $pagemeta = db_fetch("select * from `jos_pagemeta` where `uri` = '".$var->request_uri."'");
  $pagejevent = db_fetch("select * from `jos_components` where `option`='com_jevlocations'");
 
  $gmapkeys=explode('googlemapskey=',$pagejevent['params']);
  $gmapkeys1=explode("\n",$gmapkeys[1]);

  $var->site_name = $pageglobal['site_name'];
  $var->beach = $pageglobal['beach'];
  $var->email = $pageglobal['email'];
  $var->googgle_map_api_keys = $gmapkeys1[0];
  $var->location_code = $pageglobal['location_code'];
  $var->photo_mini_slider_cat = $pageglobal['photo_mini_slider_cat'];
  $var->photo_upload_cat = $pageglobal['photo_upload_cat'];
  $var->facebook = $pageglobal['facebook'];
  $var->iphone = $pageglobal['iphone'];
  $var->android = $pageglobal['android'];
  $var->googgle_analytics=$pageglobal['googgle_map_api_keys'];

  $var->page_title = isset($pagemeta['title'])?$pagemeta['title']:'';
  $var->metadesc = isset($pagemeta['metadesc'])?$pagemeta['metadesc']:'';
  $var->keywords = isset($pagemeta['keywords'])?$pagemeta['keywords']:'';
  $var->extra_meta = isset($pagemeta['extra_meta'])?$pagemeta['extra_meta']:'';

}

function m_event_list_intro() {
  global $var;
  $header = "Event Calendar";
  $intro = db_fetch("select introtext from `jos_content` where `title` = 'Events Page Introduction'");
  require($var->tpl_path."event_list.tpl");
}

function m_dining_intro() {
  global $var;
  $text = db_fetch("select `introtext` from `jos_content` where `title` = 'Dining Page Introduction'");
  echo $text;
}

function m_location_list($cat, $featured = true) {
  global $var;
  $title = '';
  if(is_array($cat)) {
    $cat_arr = $cat;
    $cat = "";
    $first = true;
    foreach($cat_arr as $v) {
      if($first) {
        $title .= $v;
        $cat .= "'$v'";
        $first = false;
      } else {
        $title .= " &amp; $v";
        $cat .= ",'$v'";
      }
    }
  } else {
    $title = $cat;
    $cat = "'$cat'";
  }
  if($featured) 
    $sql = "select jjl.loc_id, jjl.title, jjl.street, jjl.phone, jjl.loccat from `jos_jev_locations` jjl, jos_jev_customfields3 jjc where jjl.loc_id = jjc.target_id and jjc.value = 1 and jjl.published=1 order by jjl.title";
	//jjl.loccat = jc.id and and jc.title in($cat) 
  else
 $sql = "select jjl.loc_id, jjl.title, jjl.street, jjl.phone, jjl.loccat from `jos_jev_locations` jjl, `jos_categories` jc where jjl.loccat = jc.id and jc.title in($cat) and jjl.published=1 order by jjl.title";
	
  //fprint($sql);
  $data = db_fetch($sql, true, true);
  //fprint($sql); fprint($data); _x();
  require($var->tpl_path."location_list.tpl");
}
function category_list($catids=null) {
	if($catids=='') {
		$query_cat="SELECT c.id FROM jos_categories AS c LEFT JOIN jos_categories AS p ON p.id=c.parent_id LEFT JOIN jos_categories AS gp ON gp.id=p.parent_id LEFT JOIN jos_categories AS ggp ON ggp.id=gp.parent_id WHERE c.access <= 2 AND c.published = 1 AND c.section = 'com_jevents'";
			@$rec_cat=mysql_query($query_cat);
			while(@$row_cat=mysql_fetch_array($rec_cat))
			$array_cat[]=$row_cat['id'];
			@$arrstrcat=implode(',',array_merge(array(-1), $array_cat));
	} 
	return $arrstrcat;
}

function events_list($time,$date,$end_date) {
global $var;
$allday='';
$noend='';

	if(!empty($date)) {
		   $query_filter="SELECT rpt.*, ev.*, rr.*, det.*,icsf.* FROM jos_jevents_repetition as rpt,jos_jevents_vevent as ev,jos_jevents_icsfile as icsf,jos_jevents_vevdetail as det,jos_jevents_rrule as rr
		WHERE 
rpt.eventid=ev.ev_id 
and icsf.ics_id=ev.icsid
and det.evdet_id=rpt.eventdetail_id 
and rr.eventid = rpt.eventid

and ev.state='1'
AND ev.access <= 0 AND icsf.state=1 AND icsf.access <= 0
AND rpt.endrepeat >= '$date 00:00:00' AND rpt.startrepeat <= '$date 23:59:59' GROUP BY rpt.rp_id";
	
		$data = db_fetch($query_filter, true, true);
		require($var->tpl_path."eventsondate.tpl");
	}

}

function m_location_count($cat, $featured = true) {
  global $var;
  $title = '';
  if(is_array($cat)) {
    $cat_arr = $cat;
    $cat = "";
    $first = true;
    foreach($cat_arr as $v) {
      if($first) {
        $title .= $v;
        $cat .= "'$v'";
        $first = false;
      } else {
        $title .= " &amp; $v";
        $cat .= ",'$v'";
      }
    }

  } else {
    $title = $cat;
    $cat = "'$cat'";
  }
  if($featured) 
    $sql = "select jjl.loc_id, jjl.title, jjl.street, jjl.phone, jjl.loccat from `jos_jev_locations` jjl, jos_jev_customfields3 jjc where jjl.loc_id = jjc.target_id and jjc.value = 1 and jjl.published=1 order by jjl.title ";
	//jjl.loccat = jc.id and and jc.title in($cat) 
  else
   $sql = "select jjl.loc_id, jjl.title, jjl.street, jjl.phone, jjl.loccat from `jos_jev_locations` jjl, `jos_categories` jc where jjl.loccat = jc.id and jc.title in($cat) and jjl.published=1 order by jjl.title";
  //fprint($sql);
  $data =mysql_query($sql);
  //fprint($sql); fprint($data); _x();

if($data)
	return mysql_num_rows($data);
else
	return 0;
}



	function _populateHourData(&$data, $rows, $target_date){
		$num_events			= count( $rows );

		$data['hours']=array();
		$data['hours']['timeless']=array();
		$data['hours']['timeless']['events']=array();

		// Timeless events
		for( $r = 0; $r < $num_events; $r++ ){
			$row =& $rows[$r];
			if ($row->checkRepeatDay($target_date))  {

				if ($row->alldayevent() || (!$row->noendtime() && ($row->hup()==$row->hdn() && $row->minup()==$row->mindn() && $row->sup()==$row->sdn()))){
					$count = count($data['hours']['timeless']['events']);
					$data['hours']['timeless']['events'][$count]=$row;
				}
			}
		}

		for ($h=0;$h<24;$h++){
			$data['hours'][$h]=array();
			$data['hours'][$h]['hour_start'] = $target_date+($h*3600);
			$data['hours'][$h]['hour_end'] = $target_date+59+(59*60)+($h*3600);
			$data['hours'][$h]['events'] = array();

			for( $r = 0; $r < $num_events; $r++ ){
				$row =& $rows[$r];
				if (!isset($row->alreadyHourSlotted) && $row->checkRepeatDay($target_date))  {
					if ($row->alldayevent() || (!$row->noendtime() && ($row->hup()==$row->hdn() && $row->minup()==$row->mindn() && $row->sup()==$row->sdn()))){
						// Ignore timeless events
					}
					// if first hour of the day get the previous days events here!!
					else if ($h==0 && $row->getUnixStartDate()<$target_date){
						$count = count($data['hours'][$h]['events']);
						$data['hours'][$h]['events'][$count]=$row;
						$row->alreadyHourSlotted = 1;
					}
					else if ($row->hup()==$h && $row->minup()<=59 && $row->sup()<=59){

						$count = count($data['hours'][$h]['events']);
						$data['hours'][$h]['events'][$count]=$row;
						$row->alreadyHourSlotted = 1;
					}

				}
			}
			// sort events of this day by time
			@usort($data['hours'][$h]['events'],array("JEventsDataModel", "_sortEventsByTime"));
		}

	}

function accessibleCategoryList($aid=null, $catids=null, $catidList=null){
	return accessibleCategoryList($aid, $catids, $catidList);
}
function listIcalEvents($startdate,$enddate, $order="", $filters = false, $extrafields="", $extratables="", $limit=""){

		if (strpos($startdate,"-")===false) {
			 $startdate = strftime('%Y-%m-%d 00:00:00',$startdate);
			 $enddate = strftime('%Y-%m-%d 23:59:59',$enddate);
		}

		// process the new plugins
		// get extra data and conditionality from plugins
		$extrawhere =array();
		$extrajoin = array();
		//	$extrafields = "";  // must have comma prefix
		//	$extratables = "";  // must have comma prefix
		$needsgroup = false;

		/*if (!$filters){
			$filterarray = array("published","justmine","category","search");

			// If there are extra filters from the module then apply them now
			$reg =& JFactory::getConfig();
			$modparams = $reg->getValue("jev.modparams",false);
			if ($modparams && $modparams->getValue("extrafilters",false)){
				$filterarray  = array_merge($filterarray, explode(",",$modparams->getValue("extrafilters",false)));
			}

			$filters = jevFilterProcessing::getInstance($filterarray);

			$filters->setWhereJoin($extrawhere,$extrajoin );
			$needsgroup = $filters->needsGroupBy();

			$dispatcher	=& JDispatcher::getInstance();
			$dispatcher->trigger('onListIcalEvents', array (& $extrafields, & $extratables, & $extrawhere, & $extrajoin, & $needsgroup));

			// What if join multiplies the rows?
			// Useful MySQL link http://forums.mysql.com/read.php?10,228378,228492#msg-228492
			// concat with group
			// http://www.mysqlperformanceblog.com/2006/09/04/group_concat-useful-group-by-extension/

		}
		else {
			$filters->setWhereJoin($extrawhere,$extrajoin);
		}*/

		//echo $extrajoin = ( count( $extrajoin  ) ?  " \n LEFT JOIN ". implode( " \n LEFT JOIN ", $extrajoin ) : '' );
		//echo $extrawhere = ( count( $extrawhere ) ? ' AND '. implode( ' AND ', $extrawhere ) : '' );

		// This version picks the details from the details table
		// ideally we should check if the event is a repeat but this involves extra queries unfortunately
		

		$query = "SELECT rpt.*, ev.*, rr.*, det.*, ev.state as published, ev.created as created, loc.loc_id,loc.title as loc_title, loc.title as location, loc.street as loc_street, loc.description as loc_desc, loc.postcode as loc_postcode, loc.city as loc_city, loc.country as loc_country, loc.state as loc_state, loc.phone as loc_phone , loc.image as loc_image , loc.url as loc_url , loc.geolon as loc_lon , loc.geolat as loc_lat , loc.geozoom as loc_zoom"
		. "\n , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup"
		. "\n , YEAR(rpt.endrepeat) as ydn, MONTH(rpt.endrepeat   ) as mdn, DAYOFMONTH(rpt.endrepeat) as ddn"
		. "\n , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat) as sup"
		. "\n , HOUR(rpt.endrepeat) as hdn, MINUTE(rpt.endrepeat   ) as mindn, SECOND(rpt.endrepeat) as sdn"
		. "\n FROM jos_jevents_repetition as rpt"
		. "\n LEFT JOIN jos_jevents_vevent as ev ON rpt.eventid = ev.ev_id"
		. "\n LEFT JOIN jos_jevents_icsfile as icsf ON icsf.ics_id=ev.icsid "
		. "\n LEFT JOIN jos_jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
		. "\n LEFT JOIN jos_jevents_rrule as rr ON rr.eventid = rpt.eventid"
		. "\n LEFT JOIN jos_jev_locations as loc ON loc.loc_id=det.location"
		. "\n LEFT JOIN jos_jev_locations as gloc ON gloc.loc_id=det.location"
		 . "\n LEFT JOIN jos_jev_peopleeventsmap as persmap ON det.evdet_id=persmap.evdet_id"
		. "\n LEFT JOIN jos_jev_people as pers ON pers.pers_id=persmap.pers_id"
		. "\n WHERE ev.catid IN(SELECT c.id FROM jos_categories AS c LEFT JOIN jos_categories AS p ON p.id=c.parent_id LEFT JOIN jos_categories AS gp ON gp.id=p.parent_id LEFT JOIN jos_categories AS ggp ON ggp.id=gp.parent_id WHERE c.access <= 0 AND c.published = 1 AND c.section = 'com_jevents')"
		// New equivalent but simpler test
		. "\n AND rpt.endrepeat >= '$startdate' AND rpt.startrepeat <= '$enddate' AND ev.state=1" 
		
		/*
		. "\n AND ((rpt.startrepeat >= '$startdate' AND rpt.startrepeat <= '$enddate')"
		. "\n OR (rpt.endrepeat >= '$startdate' AND rpt.endrepeat <= '$enddate')"
		// This is redundant!!
		//. "\n OR (rpt.startrepeat >= '$startdate' AND rpt.endrepeat <= '$enddate')"
		// This slows the query down
		. "\n OR (rpt.startrepeat <= '$startdate' AND rpt.endrepeat >= '$enddate')"
		. "\n )"
		*/
		// Radical alternative - seems slower though
		/*
		. "\n WHERE rpt.rp_id IN (SELECT  rbd.rp_id
		FROM jos_jevents_repbyday as rbd
		WHERE  rbd.catid IN(".$this->accessibleCategoryList().")
		AND rbd.rptday >= '$startdate' AND rbd.rptday <= '$enddate' )"
		*/

		//. $extrawhere

		. "\n AND ev.access <= '0'  AND icsf.state=1 AND icsf.access <= '0'"
		// published state is now handled by filter
		//. "\n AND ev.state=1"
		. "\n GROUP BY rpt.rp_id"
		;

		if ($order !="") {
			$query .= " ORDER BY ".$order;
		}
		if ($limit !="") {
			$query .= " LIMIT ".$limit;
		}

		$record=mysql_query($query);
		$rows=mysql_num_rows($record);
		$result=mysql_fetch_array($record);
		/*$cache=& JFactory::getCache(JEV_COM_COMPONENT);
		$rows =  $cache->call('JEventsDBModel::_cachedlistIcalEvents', $query, $langtag );

		$dispatcher =& JDispatcher::getInstance();
		$dispatcher->trigger( 'onDisplayCustomFieldsMultiRowUncached', array( &$rows ));*/
		return $result;

	}

function listIcalEventsByDay($targetdate){
	// targetdate is midnight at start of day - but just in case
	list ($y,$m,$d) =	explode(":",strftime( '%Y:%m:%d',$targetdate));
	$startdate 	= mktime( 0, 0, 0, $m, $d, $y );
 	$enddate 	= mktime( 23, 59, 59, $m, $d, $y );

echo listIcalEvents($startdate,$enddate);
die("hre");
	return listIcalEvents($startdate,$enddate);
}
function listEvents( $startdate, $enddate, $order=""){
	if (!legacyEvents) {
		return array();
	}
}
function listEventsByDateNEW( $select_date ){
	return listEvents($select_date." 00:00:00",$select_date." 23:59:59");
}
function getDayData($year, $month, $day) {
        global $mainframe;

        include_once("/home/tapdesti/public_html/administrator/components/com_jevents/libraries/colorMap.php");

        $data = array();

        $target_date = mktime(0,0,0,$month,$day,$year);
        $rows	= listEventsByDateNEW( strftime("%Y-%m-%d",$target_date ));
        $icalrows =listIcalEventsByDay($target_date);

        @$rows = array_merge($icalrows,$rows);

        _populateHourData($data, $rows, $target_date);

        return $data;
}


?>