<?php
/**
 * copyright (C) 2008 GWE Systems Ltd - All rights reserved
 */

defined( 'JPATH_BASE' ) or die( 'Direct Access to this location is not allowed.' );

include_once(JPATH_COMPONENT_ADMINISTRATOR."/controllers/".basename(__FILE__));

class FrontPeopleController extends AdminPeopleController {
	function __construct($config = array())
	{
		parent::__construct($config);
		JModel::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR."/models/");
		$this->registerDefaultTask("people");
	}

	function detail() {

		$locid	= JRequest::getInt( 'pers_id', 0 );

		JRequest::setVar("cid",$locid);

		// get the view
		$viewName = "people";
		$this->view = & $this->getView($viewName,"html");

		// Set the layout
		$this->view->setLayout('detail');

		// Get/Create the model
		if ($model = & $this->getModel("person", "PeopleModel")) {
			// Push the model into the view (as default)
			$this->view->setModel($model, true);
		}
		$typemodel = & $this->getModel("type", "PeopleTypesModel") ;
		$this->view->setModel($typemodel, false);

		$this->view->detail();
	}

	function ajaxmap() {
		$modid = intval((JRequest::getVar('modid', 0)));
		if ($modid<=0){
			echo "<script>alert('bad mod id');</script>";
			return;
		}
		$x = JRequest::getFloat('x', 0);
		$y = JRequest::getFloat('y', 0);
		$zoom = JRequest::getInt('zoom', 10);

		$user =& JFactory::getUser();
		$query = "SELECT id, params"
		. "\n FROM #__modules AS m"
		. "\n WHERE m.published = 1"
		. "\n AND m.id = ". $modid
		. "\n AND m.access <= ". (int) $user->aid
		. "\n AND m.client_id != 1";
		$db	=& JFactory::getDBO();
		$db->setQuery( $query );
		$modules = $db->loadObjectList();
		if (count($modules)<=0){
			if (!$modid<=0){
				echo "<script>alert('bad mod id');</script>";
				return;
			}
		}
		$params = new JParameter( $modules[0]->params );

		JRequest::setVar("tmpl","component");

		// find the people nearest the address being searched
		// Assume 50km distance for now
		// See http://calgary.rasc.ca/latlong.htm
		//
		// Latitude is how far north/south you are
		// The Formula for Longitude Distance at a Given Latitude (theta) in Km:
		// 1° of Longitude = 111.41288 * cos(theta) - 0.09350 * cos(3 * theta) + 0.00012 * cos(5 * theta)
		//
		// The Formula for Latitude Distance at a Given Latitude (theta) in Km:
		// 1° of Latitude = 111.13295 - 0.55982 * cos(2 * theta) + 0.00117 * cos(4 * theta)
		//
		// In terms of inputs
		// Longitude = x
		// Latitude = y

		$lonscaling = 111.41288 * cos(deg2rad($y)) - 0.09350 * cos(deg2rad(3 * $y)) + 0.00012 * cos(deg2rad(5 * $y));
		$lonscaling = $lonscaling*$lonscaling;

		$latscaling = 111.13295 - 0.55982 * cos(deg2rad(2 * $y)) + 0.00117 * cos(deg2rad(4 * $y));
		$latscaling = $latscaling*$latscaling;

		$maxdistance = 50;
		$maxdistance2 = $maxdistance*$maxdistance ;

		$query = "SELECT * FROM jos_jev_people WHERE ($latscaling * (geolat - $y)* (geolat - $y) + $lonscaling * (geolon - $x)* (geolon - $x))< $maxdistance2 AND published=1 AND access <= ". (int) $user->aid;
		$db->setQuery( $query );
		$people = $db->loadObjectList();
		//echo $db->_sql."<br/><br/>";

		if (count($people)==0) return;

		$loclist = array();
		foreach ($people as $person) {
			$loclist[] = $person->pers_id;
		}
		$registry =& JFactory::getConfig();
		$registry->set("jevpeople.people",implode(",",$loclist));

		include_once(JPATH_SITE."/components/".JEV_COM_COMPONENT."/jevents.defines.php");
		$this->datamodel  =  new JEventsDataModel();

		list($year,$month,$day) = JEVHelper::getYMD();
		$startdate 	= mktime( 0, 0, 0,  $month,  $day, $year );
		$enddate 	= mktime( 0, 0, 0,  $month+1, $day, $year );

		$filters = jevFilterProcessing::getInstance(array("personlist","published","justmine","category","search"),JPATH_COMPONENT."/filters/");
		$events = $this->datamodel->queryModel->listIcalEvents($startdate,$enddate,"",$filters);
		
		// get the view
		$viewName = "people";
		$this->view = & $this->getView($viewName,"html");

		$points = new stdClass();
		// centering and zoom of map
		$points->zoom=$zoom;
		$points->x = $x;
		$points->y = $y;

		$points->data= array();

		foreach ($people as $person) {
			$point = new stdClass();
			$point->x = $person->geolon;
			$point->y = $person->geolat;
			$point->description = $person->title;
			$points->data[]= $point;
		}

		// Find bounding rectangle
		$swx=false;
		foreach ($points->data as $point) {
			if ($swx===false){
				$swx=$point->x;
				$swy=$point->y;
				$nex=$point->x;
				$ney=$point->y;
			}
			$swx=($point->x<$swx)?$point->x:$swx;
			$swy=($point->y<$swy)?$point->y:$swy;
			$nex=($point->x>$nex)?$point->x:$nex;
			$ney=($point->y>$ney)?$point->y:$ney;
		}
		$points->swx = $swx;
		$points->swy = $swy;
		$points->nex = $nex;
		$points->ney = $ney;

		$points = json_encode($points);

		// Set the layout
		$this->view->setLayout('ajaxmap');
		$this->view->assign("points",$points);

		$this->view->assign("modid",$modid);

		$this->view->display();
	}
	
	function people( )
	{
		// get the view
		$viewName = "people";
		$this->view = & $this->getView($viewName,"html");

		// Set the layout
		$this->view->setLayout('people');
		$this->view->assign('title'   , JText::_("People List"));

		// Get/Create the model
		if ($model = & $this->getModel($viewName, "PeopleModel")) {
			// Push the model into the view (as default)
			$this->view->setModel($model, true);
		}

		$this->view->people();
	}

}
