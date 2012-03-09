<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: icals.php 1756 2010-05-05 09:19:47Z geraint $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined( 'JPATH_BASE' ) or die( 'Direct Access to this location is not allowed.' );

include_once(JEV_ADMINPATH."/controllers/icals.php");

class ICalsController extends AdminIcalsController   {

	function __construct($config = array())
	{
		parent::__construct($config);
		// TODO get this from config
		$this->registerDefaultTask( 'ical' );
		//		$this->registerTask( 'show',  'showContent' );

		// Ensure authorised to do this
		$cfg = & JEVConfig::getInstance();
		if ($cfg->get("disableicalexport",0)) {
			JError::raiseError(403, JText::_('ALERTNOTAUTH'));
		}

		// Load abstract "view" class
		$theme = JEV_CommonFunctions::getJEventsViewName();
		JLoader::register('JEvents'.ucfirst($theme).'View',JEV_VIEWS."/$theme/abstract/abstract.php");
	}

	// Thanks to HiFi
	function ical() {

		list($year,$month,$day) = JEVHelper::getYMD();
		$Itemid	= JEVHelper::getItemid();

		// get the view

		$document =& JFactory::getDocument();
		$viewType	= $document->getType();

		$cfg = & JEVConfig::getInstance();
		$theme = JEV_CommonFunctions::getJEventsViewName();

		$view = "icals";
		$this->addViewPath($this->_basePath.DS."views".DS.$theme);
		$this->view = & $this->getView($view,$viewType, $theme,
		array( 'base_path'=>$this->_basePath,
		"template_path"=>$this->_basePath.DS."views".DS.$theme.DS.$view.DS.'tmpl',
		"name"=>$theme.DS.$view));

		// Set the layout
		$this->view->setLayout('ical');

		$this->view->assign("Itemid",$Itemid);
		$this->view->assign("month",$month);
		$this->view->assign("day",$day);
		$this->view->assign("year",$year);
		$this->view->assign("task",$this->_task);

		// View caching logic -- simple... are we logged in?
		$cfg	 = & JEVConfig::getInstance();
		$useCache = intval($cfg->get('com_cache', 0));
		$user = &JFactory::getUser();
		if ($user->get('id') || !$useCache) {
			$this->view->display();
		} else {
			$cache =& JFactory::getCache(JEV_COM_COMPONENT, 'view');
			$cache->get($this->view, 'display');
		}
	}

	function export() {

		$years = JRequest::getVar(	'years','NONE' );
		$cats = JRequest::getVar(	'catids','NONE' );

		// validate the key
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		$icalkey = $params->get("icalkey","secret phrase");

		$privatecalendar = false;
		$k = JRequest::getString("k","NONE");
		$pk = JRequest::getString("pk","NONE");
		$userid = JRequest::getInt("i",0);
		if ($pk!="NONE"){
			if (!$userid) JError::raiseError(403,"JEV ERROR");
			$privatecalendar = true;
			$puser = JUser::getInstance($userid);
			$key = md5($icalkey . $cats . $years . $puser->password . $puser->username . $puser->id);

			if ($key != $pk) JError::raiseError(403,"JEV ERROR");

			// Get an ACL object
			$acl =& JFactory::getACL();

			// Get the user group from the ACL
			$grp = $acl->getAroGroup($puser->get('id'));

			//Mark the user as logged in
			$puser->set( 'guest', 0);
			$puser->set('aid', 1);

			// Fudge Authors, Editors, Publishers and Super Administrators into the special access group
			if ($acl->is_group_child_of($grp->name, 'Registered') || $acl->is_group_child_of($grp->name, 'Public Backend'))    {
				$puser->set('aid', 2);
			}

			// ensure "user" can access non-public categories etc.
			$this->dataModel->aid = $puser->aid;
			
			//Don'y need to set the usertype based on the ACL group name
			// $puser->set('usertype', $grp->name);

			$registry	=& JRegistry::getInstance("jevents");
			$registry->setValue("jevents.icaluser",$puser);

		}
		else if ($k!="NONE"){
			$key = md5($icalkey . $cats . $years );
			if ($key != $k) JError::raiseError(403,"JEV ERROR");
		}
		else {
			JError::raiseError(403,"JEV ERROR");
		}

		// Fix the cats
		$cats = explode( ',', $cats );
		// hardening!
		JEVHelper::forceIntegerArray($cats, false);
		JRequest::setVar("catids", implode("|",$cats));

		//Parsing variables from URL
		//Year
		if ($years != "NONE") {
			$years = explode(",",JRequest::getVar('years'));
			if (!is_array($years) || count($years)==0){
				list($y,$m,$d) = JEVHelper::getYMD();
				$years = array($y);
			}
			JArrayHelper::toInteger($years);
		}
		else {
			list($y,$m,$d) = JEVHelper::getYMD();
			$years = array($y);
		}

		// Lockin hte categories from the URL
		$this->dataModel->setupComponentCatids();

		//And then the real work
		// Force all only the one repeat
		$cfg = & JEVConfig::getInstance();
		$cfg->set('com_showrepeats',0);
		$icalEvents = array();
		foreach ($years as $year) {
			$startdate = $year."-01-01";
			$enddate = $year."-12-31";
			$rows = $this->dataModel->getRangeData($startdate, $enddate,0,0);
			if (!isset($rows["rows"])) continue;
			foreach ($rows["rows"] as $row) {
				if (!array_key_exists($row->ev_id(),$icalEvents)){
					$icalEvents[$row->ev_id()] = $row;
				}

			}
			unset($rows);
		}
		if ($userid) $user = JUser::getInstance($userid);

		$mainframe = JFactory::getApplication();

		ob_end_clean();

		// Define the file as an iCalendar file
		//header('Content-Type: text/calendar; method=request; charset=UTF-8');
		header('Content-Type: application/octet-stream; charset=UTF-8');
		// Give the file a name and force download
		header('Content-Disposition: attachment; filename=calendar.ics'     );

		echo "BEGIN:VCALENDAR\nVERSION:2.0\nPRODID:-//jEvents 1.5 for Joomla//EN\n";
		if(!empty($icalEvents)) {

			// Build Exceptions dataset - all done in big batches to save multiple queries
			$exceptiondata = array();
			$ids = array();
			foreach($icalEvents as $a) {
				$ids[]=	$a->ev_id();
				if (count($ids)>50){
					$db = JFactory::getDBO();
					$db->setQuery("SELECT * FROM #__jevents_exception where eventid IN (".implode(",",$ids).")");
					$rows = $db->loadObjectList();
					foreach ($rows as $row) {
						if (isset($exceptiondata[$row->eventid])){
							$exceptiondata[$row->eventid] = array();
						}
						$exceptiondata[$row->eventid][]=$row;
					}
					$ids = array();
				}
			}
			// mop up the last ones
			if (count($ids)>0){
				$db = JFactory::getDBO();
				$db->setQuery("SELECT * FROM #__jevents_exception where eventid IN (".implode(",",$ids).")");
				$rows = $db->loadObjectList();
				foreach ($rows as $row) {
					if (isset($exceptiondata[$row->eventid])){
						$exceptiondata[$row->eventid] = array();
					}
					$exceptiondata[$row->eventid][]=$row;
				}
			}

			foreach($icalEvents as $a) {
				// if event has repetitions I must find the first one to confirm the dates
				if ($a->hasrepetition()){
					$a = $a->getFirstRepeat();
				}
				echo "BEGIN:VEVENT";
				echo "\nUID:".$a->uid();
				echo "\nCATEGORIES:".$a->catname();
				if (!empty($a->_class)) echo "\nCLASS:".$a->_class;
				echo "\n"."SUMMARY:".$a->title()."\n";
				echo "LOCATION:".$this->wraplines($this->replacetags($a->location()))."\n";
				// We Need to wrap this according to the specs
				/*echo "DESCRIPTION:".preg_replace("'<[\/\!]*?[^<>]*?>'si","",preg_replace("/\n|\r\n|\r$/","",$a->content()))."\n";*/
				echo $this->setDescription($a->content())."\n";

				if ($a->hasContactInfo()) echo "CONTACT:".$this->replacetags($a->contact_info())."\n";
				if ($a->hasExtraInfo()) echo "X-EXTRAINFO:".$this->replacetags($a->_extra_info); echo "\n";

				$start = strftime("%Y%m%dT%H%M%S",$a->getUnixStartTime());
				$end = strftime("%Y%m%dT%H%M%S",$a->getUnixEndTime());
				$stamptime = strftime("%Y%m%dT%H%M%S",time());
				
				echo "DTSTAMP:".$stamptime."\n";
				echo "DTSTART:".$start."\n";
				if (!$a->noendtime()){
					echo "DTEND:".$end."\n";
				}
				echo "SEQUENCE:".$a->_sequence."\n";
				if($a->hasrepetition()){
					echo 'RRULE:';

					// TODO MAKE SURE COMPAIBLE COMBINATIONS
					echo 'FREQ='.$a->_freq;
					if($a->_until != "" && $a->_until != 0) {
						echo ';UNTIL='.strftime("%Y%m%dT235959",$a->_until);
					}
					else if($a->_count != "") {
						echo ';COUNT='.$a->_count;
					}
					if($a->_rinterval != "") echo ';INTERVAL='.$a->_rinterval;
					if ($a->_freq=="DAILY"){

					}
					else if ($a->_freq=="WEEKLY"){
						if($a->_byday != "") echo ';BYDAY='.$a->_byday;
					}
					else if ($a->_freq=="MONTHLY"){
						if($a->_bymonthday != "") {
							echo ';BYMONTHDAY='.$a->_bymonthday;
							if($a->_byweekno != "") echo ';BYWEEKNO='.$a->_byweekno;
						}
						else if($a->_byday != "") {
							echo ';BYDAY='.$a->_byday;
							if($a->_byweekno != "") echo ';BYWEEKNO='.$a->_byweekno;
						}
					}
					else if ($a->_freq=="YEARLY"){
						if($a->_byyearday != "") echo ';BYYEARDAY='.$a->_byyearday;
					}
					echo "\n";
				}

				// Now handle Exceptions
				$exceptions = array();
				if (array_key_exists($a->ev_id(),$exceptiondata)){
					$exceptions = $exceptiondata[$a->ev_id()];
				}

				$deletes = array();
				$changed = array();
				if (count($exceptions)>0){
					foreach ($exceptions as $exception) {
						if ($exception->exception_type == 0){
							$exceptiondate = strtotime($exception->startrepeat);
							$deletes[] = strftime("%Y%m%dT%H%M%S",$exceptiondate);
						}
						else $changed[] = $exception->rp_id;
					}
					if (count($deletes)>0){
						echo "EXDATE:".$this->wraplines(implode(",",$deletes))."\n";
					}
				}

				echo "TRANSP:OPAQUE\n";
				echo "END:VEVENT\n";


				if (count($changed)>0){
					foreach ($changed as $rpid) {
						$a = $this->dataModel->getEventData( $rpid, "icaldb", 0,0,0);
						if ($a && isset($a["row"])){
							$a = $a["row"];
							echo "BEGIN:VEVENT";
							echo "\nUID:".$a->uid();
							echo "\nCATEGORIES:".$a->catname();
							if (!empty($a->_class)) echo "\nCLASS:".$a->_class;
							echo "\n"."SUMMARY:".$a->title()."\n";
							echo "LOCATION:".$this->wraplines($this->replacetags($a->location()))."\n";
							// We Need to wrap this according to the specs
							echo $this->setDescription($a->content())."\n";

							if ($a->hasContactInfo()) echo "CONTACT:".$this->replacetags($a->contact_info())."\n";
							if ($a->hasExtraInfo()) echo "X-EXTRAINFO:".$this->replacetags($a->_extra_info); echo "\n";

							$chstart = strftime("%Y%m%dT%H%M%S",$a->getUnixStartTime());
							$end = strftime("%Y%m%dT%H%M%S",$a->getUnixEndTime());
							$stamptime = strftime("%Y%m%dT%H%M%S",time());

							echo "DTSTAMP:".$stamptime."\n";
							echo "DTSTART:".$chstart."\n";
							echo "DTEND:".$end."\n";
							// This is the original start date/time
							echo "RECURRENCE-ID:".$start."\n";
							echo "SEQUENCE:".$a->_sequence."\n";
							echo "TRANSP:OPAQUE\n";
							echo "END:VEVENT\n";
						}
					}
				}

			}

		}


		echo "END:VCALENDAR";
		exit();
	}

	function icalevent(){
		$this->exportEvent(true);
	}

	function icalrepeat(){
		$this->exportEvent(false);
	}

	private function exportEvent($withrepeats = true){
		$rpid = JRequest::getInt("evid",0);
		if (!$rpid) return;

		JRequest::setVar("tmpl","component");
		// Define the file as an iCalendar file
		header('Content-Type: text/calendar; method=request; charset=UTF-8');
		// Give the file a name and force download
		header('Content-Disposition: attachment; filename=calendar.ics' );

		list($year,$month,$day) = JEVHelper::getYMD();
		$repeat = $this->dataModel-> getEventData( $rpid, "icaldb", $year, $month, $day);
		if ($repeat && is_array($repeat) && isset($repeat["row"]) && $repeat["row"]->rp_id()==$rpid){
			$a = $repeat["row"];
			$this->dataModel->setupComponentCatids();

			if ($withrepeats && $a->hasrepetition()){
				$a = $a->getFirstRepeat();
			}

			if ($withrepeats){
				// Build Exceptions dataset - all done in big batches to save multiple queries
				$exceptiondata = array();
				$ids = array();
				$ids[]=	$a->ev_id();
				$db = JFactory::getDBO();
				$db->setQuery("SELECT * FROM #__jevents_exception where eventid IN (".implode(",",$ids).")");
				$rows = $db->loadObjectList();
				foreach ($rows as $row) {
					if (isset($exceptiondata[$row->eventid])){
						$exceptiondata[$row->eventid] = array();
					}
					$exceptiondata[$row->eventid][]=$row;
				}
			}

			echo "BEGIN:VCALENDAR\nVERSION:2.0\nPRODID:-//jEvents 1.5 for Joomla//EN\n";
			echo "BEGIN:VEVENT";
			echo "\nUID:".$a->uid();
			echo "\nCATEGORIES:".$a->catname();
			if (!empty($a->_class)) echo "\nCLASS:".$a->_class;
			echo "\n"."SUMMARY:".$a->title()."\n";
			echo "LOCATION:".$this->wraplines($this->replacetags($a->location()))."\n";
			// We Need to wrap this according to the specs
			/*echo "DESCRIPTION:".preg_replace("'<[\/\!]*?[^<>]*?>'si","",preg_replace("/\n|\r\n|\r$/","",$a->content()))."\n";*/
			echo $this->setDescription($a->content())."\n";

			if ($a->hasContactInfo()) echo "CONTACT:".$this->replacetags($a->contact_info())."\n";
			if ($a->hasExtraInfo()) echo "X-EXTRAINFO:".$this->replacetags($a->_extra_info); echo "\n";

			$start = strftime("%Y%m%dT%H%M%S",$a->getUnixStartTime());
			$end = strftime("%Y%m%dT%H%M%S",$a->getUnixEndTime());
			$stamptime = strftime("%Y%m%dT%H%M%S",time());

			echo "DTSTAMP:".$stamptime."\n";
			echo "DTSTART:".$start."\n";
			echo "DTEND:".$end."\n";
			echo "SEQUENCE:".$a->_sequence."\n";
			if($withrepeats && $a->hasrepetition()){
				echo 'RRULE:';

				// TODO MAKE SURE COMPAIBLE COMBINATIONS
				echo 'FREQ='.$a->_freq;
				if($a->_until != "" && $a->_until != 0) {
					echo ';UNTIL='.strftime("%Y%m%dT235959",$a->_until);
				}
				else if($a->_count != "") {
					echo ';COUNT='.$a->_count;
				}
				if($a->_rinterval != "") echo ';INTERVAL='.$a->_rinterval;
				if ($a->_freq=="DAILY"){

				}
				else if ($a->_freq=="WEEKLY"){
					if($a->_byday != "") echo ';BYDAY='.$a->_byday;
				}
				else if ($a->_freq=="MONTHLY"){
					if($a->_bymonthday != "") {
						echo ';BYMONTHDAY='.$a->_bymonthday;
						if($a->_byweekno != "") echo ';BYWEEKNO='.$a->_byweekno;
					}
					else if($a->_byday != "") {
						echo ';BYDAY='.$a->_byday;
						if($a->_byweekno != "") echo ';BYWEEKNO='.$a->_byweekno;
					}
				}
				else if ($a->_freq=="YEARLY"){
					if($a->_byyearday != "") echo ';BYYEARDAY='.$a->_byyearday;
				}
				echo "\n";
			}

			if($withrepeats) {
				// Now handle Exceptions
				$exceptions = array();
				if (array_key_exists($a->ev_id(),$exceptiondata)){
					$exceptions = $exceptiondata[$a->ev_id()];
				}

				$deletes = array();
				$changed = array();
				if (count($exceptions)>0){
					foreach ($exceptions as $exception) {
						if ($exception->exception_type == 0){
							$exceptiondate = strtotime($exception->startrepeat);
							$deletes[] = strftime("%Y%m%dT%H%M%S",$exceptiondate);
						}
						else $changed[] = $exception->rp_id;
					}
					if (count($deletes)>0){
						echo "EXDATE:".$this->wraplines(implode(",",$deletes))."\n";
					}
				}
			}
			echo "TRANSP:OPAQUE\n";
			echo "END:VEVENT\n";

			if($withrepeats) {

				if (count($changed)>0){
					foreach ($changed as $rpid) {
						$a = $this->dataModel->getEventData( $rpid, "icaldb", 0,0,0);
						if ($a && isset($a["row"])){
							$a = $a["row"];
							echo "BEGIN:VEVENT";
							echo "\nUID:".$a->uid();
							echo "\nCATEGORIES:".$a->catname();
							if (!empty($a->_class)) echo "\nCLASS:".$a->_class;
							echo "\n"."SUMMARY:".$a->title()."\n";
							echo "LOCATION:".$this->wraplines($this->replacetags($a->location()))."\n";
							// We Need to wrap this according to the specs
							echo $this->setDescription($a->content())."\n";

							if ($a->hasContactInfo()) echo "CONTACT:".$this->replacetags($a->contact_info())."\n";
							if ($a->hasExtraInfo()) echo "X-EXTRAINFO:".$this->replacetags($a->_extra_info); echo "\n";

							$start = strftime("%Y%m%dT%H%M%S",$a->getUnixStartTime());
							$end = strftime("%Y%m%dT%H%M%S",$a->getUnixEndTime());
							$stamptime = strftime("%Y%m%dT%H%M%S",time());

							echo "DTSTAMP:".$stamptime."\n";
							echo "DTSTART:".$start."\n";
							echo "DTEND:".$end."\n";
							echo "RECURRENCE-ID:".$start."\n";
							echo "SEQUENCE:".$a->_sequence."\n";
							echo "TRANSP:OPAQUE\n";
							echo "END:VEVENT\n";
						}
					}
				}
			}

			echo "END:VCALENDAR";

			exit();

		}

	}

	private function setDescription($desc) {
		// TODO - run this through plugins first ?

		$description = $this->replacetags($desc);
		// wraplines	from vCard class
		return "DESCRIPTION;ENCODING=QUOTED-PRINTABLE:".$this->wraplines($description);
	}

	private function replacetags($description){
		$description 	= str_replace( '<p>', '\n\n', $description );
		$description 	= str_replace( '<P>', '\n\n', $description );
		$description 	= str_replace( '</p>', '\n' ,$description );
		$description 	= str_replace( '</P>', '\n' ,$description );
		$description 	= str_replace( '<p/>', '\n\n', $description );
		$description 	= str_replace( '<P/>', '\n\n', $description );
		$description 	= str_replace( '<br />', '\n', $description );
		$description 	= str_replace( '<br/>', '\n', $description );
		$description 	= str_replace( '<br>', '\n' ,$description );
		$description 	= str_replace( '<BR />', '\n', $description );
		$description 	= str_replace( '<BR/>', '\n', $description );
		$description 	= str_replace( '<BR>', '\n' ,$description );
		$description 	= str_replace( '<li>', '\n - ', $description );
		$description 	= str_replace( '<LI>', '\n - ', $description );
		$description 	= strip_tags( $description );
		$description 	= strtr( $description,	array_flip(get_html_translation_table( HTML_ENTITIES ) ) );
		$description 	= preg_replace( "/&#([0-9]+);/me","chr('\\1')", $description );
		return $description;
	}

	private function wraplines($input, $line_max = 76, $quotedprintable = true) {
		$hex 		= array('0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F');
		$eol 		= "\r\n";
		$input 		= str_replace($eol,"", $input);
		$escape 	= '=';
		$output 	= '';
		$outline 	= "";
		$newline 	= ' ';

		$linlen 	= strlen($input);

		for($i = 0; $i < $linlen; $i++) {
			$c 		= substr($input, $i, 1);
			$dec 	= ord($c);

			if (!$quotedprintable){
				if ( ($dec == 32) && ($i == ($linlen - 1)) ) { // convert space at eol only
					$c = '=20';
				} elseif ( ($dec == 61) || ($dec < 32 ) || ($dec > 126) ) { // always encode "\t", which is *not* required
					$h2 = floor($dec/16);
					$h1 = floor($dec%16);
					$c 	= $escape.$hex["$h2"] . $hex["$h1"];
				}
			}
			if ( (strlen($outline) + strlen($c)) >= $line_max ) { // CRLF is not counted
				$output .= $outline. $eol. $newline ; // soft line break; "\r\n" is okay
				$outline = $c;
				//$newline .= " ";
			}
			else {
				$outline .= $c;
			}
		} // end of for
		$output .= $outline;

		return trim($output);
	}

}
