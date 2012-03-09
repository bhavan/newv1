<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

class plgJEventsJevpeople extends JPlugin
{
	var $_dbvalid = 0;

	function plgJEventsJevpeople(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$lang 		=& JFactory::getLanguage();
		$lang->load("com_jevpeople", JPATH_SITE);
		$lang->load("com_jevpeople", JPATH_ADMINISTRATOR);
		$lang->load("plg_jevents_jevpeople", JPATH_ADMINISTRATOR);
	}

	/**
	 * When editing a JEvents menu item can add additional menu constraints dynamically
	 *
	 */
	function onEditMenuItem(&$menudata, $value,$control_name,$name, $id, $param)
	{
		static $matchingextra = null;
		// find the parameter that matches jevp: (if any)
		if (!isset($matchingextra)){
			$keyvals = $param->toArray();
			foreach ($keyvals as $key=>$val) {
				if (strpos($key,"extras")===0 && strpos($val,"jevp:")===0){
					$matchingextra = str_replace("extras","",$key);
					break;
				}
			}
			if (!isset($matchingextra)){
				$matchingextra = false;
			}
		}

		// already done this param
		if (isset($menudata[$id])) return;

		// either we found matching extra and this is the correct id or we didn't find matching extra and the value is blank
		if (($matchingextra==$id && strpos($value,"jevp:")===0) || (($value==""||$value=="0") && $matchingextra===false)){
			$matchingextra = $id;
			$invalue = str_replace("jevp:","",$value);
			$invalue = str_replace(" ","",$invalue);
			$invalue = explode(",",$invalue);
			JArrayHelper::toInteger($invalue);
			JHTML::script('people.js', 'administrator/components/com_jevpeople/assets/js/' );
			$person = " -- ";
			if (count($invalue)>0){
				$db = & JFactory::getDBO();
				$sql = "SELECT jp.pers_id, jp.title, jpt.title as typename FROM #__jev_people as jp
						LEFT JOIN #__jev_peopletypes as jpt ON jpt.type_id = jp.type_id 				
						where jp.pers_id IN(".implode(",",$invalue).")";
				$db->setQuery($sql);
				$people = @$db->loadObjectList('pers_id');
			}

			$link = JRoute::_("index.php?option=com_jevpeople&task=people.select&tmpl=component");
			$input = "<div style='float: left;align-text:left;'><ul id='sortablePeople' style='margin:0px;padding:0px;cursor:move;'>";
			foreach ($invalue as $persid) {
				if (!array_key_exists($persid,$people)) continue;
				$jpm = $people[$persid];
				$persname=$jpm->title;
				$pid=$jpm->pers_id;
				$type=$jpm->typename;
				$persname = $persname ." ($type)";
				$input .= "<li id='sortablepers$pid' >$persname</li>";
			}
			$input .= "</ul>";
			$input .= '<input type="text"  name="'.$control_name.'['.$name.']"  id="menuperson" value="'.$value.'" style="display:none"/>';
			$input .= "</div>";
			$input .= "<img src='".JURI::Root()."administrator/images/publish_x.png' class='sortabletrash' id='trashimage' style='display:none;padding-right:2px;cursor:pointer;' title='".JText::_("JEV REMOVE PERSON",true)."'/>";
			$input .= "<script type='text/javascript'>
			peopleDeleteWarning='".JText::_("JEV REMOVE PERSON WARNING",true)."';
			sortablePeople.setup();
			var jevpeople = {\n";
			$input .= "duplicateWarning : '".JText::_("Already Selected",true)."'\n";
			$input .= "		}
			</script>";
			$input.='<div class="button2-left"><div class="blank"><a href="javascript:sortablePeople.selectPerson(\''.JRoute::_("index.php?option=com_jevpeople&task=people.select&tmpl=component").'\')" title="'.JText::_('SELECT A PERSON').'"  >'.JText::_('SELECT').'</a></div></div>';

			$data = new stdClass();
			$data->name = "jevpeople";
			$data->html = $input;
			$data->label = "Specified Person?";
			$data->description = "Specify a person for this menu item";
			$data->options = array();
			$menudata[$id] = $data;
		}
	}


	function onEditCustom( &$row, &$customfields )
	{
		global $mainframe;

		JHTML::script('people.js', 'administrator/components/com_jevpeople/assets/js/' );
		$jevuser = JEVHelper::getAuthorisedUser();

		$compparams = JComponentHelper::getParams("com_jevpeople");

		$db = & JFactory::getDBO();

		// get the data from database and attach to row
		$detailid = intval($row->evdet_id());

		$db = & JFactory::getDBO();
		$sql = "SELECT * from #__jev_peopletypes as jpt";
		$db->setQuery($sql);
		$types = @$db->loadObjectList('type_id');

		$sql = "SELECT jp.pers_id, jp.title, jpt.title as typename, jpt.type_id as type_id , jpt.categories ,jpt.calendars  FROM #__jev_peopleeventsmap as jpm
		LEFT JOIN #__jev_people as jp ON jp.pers_id = jpm.pers_id 
		LEFT JOIN #__jev_peopletypes as jpt ON jpt.type_id = jp.type_id 
		WHERE evdet_id=".$detailid."
		ORDER BY jpm.ordering,jp.type_id, jp.ordering,jp.title";
		
		$db->setQuery($sql);
		$jpmlist = $db->loadObjectList();

		JHTML::_('behavior.modal');
		JHTML::_('behavior.tooltip');
		$link = JRoute::_("index.php?option=com_jevpeople&task=people.select&tmpl=component");
		if ($compparams->get("personselect",0)==0){
			$input = "<div style='float: left;'><ul id='sortablePeople' style='margin:0px;cursor:move;'>";
		}
		else {
			$input = "<div style='margin-bottom:5px;'><ul id='sortablePeople' style='margin:0px;cursor:move;'>";
		}
		foreach ($jpmlist as $jpm) {

			$jp  = $jpm;
			if ($jp->categories!="all" && $jp->categories!=""){
				$cats = explode("|",$jp->categories);
				JArrayHelper::toInteger($cats);
				if (!in_array($row->catid(),$cats)) continue;
			}
			if ($jp->calendars!="all" && $jp->calendars!=""){
				$cals = explode("|",$jp->calendars);
				JArrayHelper::toInteger($cals);
				if (!in_array($row->_icsid,$cals)) continue;
			}

			$name=$jpm->title;
			$pid=$jpm->pers_id;
			$type=$jpm->typename;
			$name = $name ." ($type)";
			$input .= "<li id='sortablepers$pid' >$name</li>";
		}
		$input .= "</ul>";
		$input  .= '<select multiple="multiple" name="custom_person[]" id="custom_person" size="4" style="display:none" >';
		foreach ($jpmlist as $jpm) {
			$name=$jpm->title;
			$pid=$jpm->pers_id;
			$type=$jpm->typename;
			$name = $name ." ($type)";
			$input .= "<option value='$pid' selected='selected' id='sortablepers".$pid."option'>$name</option>";
		}
		$input .= "</select>";
		$input .= "</div>";

		// If configured for a single person selection
	
		if($compparams->get("personselect",0)==0){
 
			$selectPerson = JText::_("Select Person");
			$selectPersonTip = JText::_("Select Person TIP");
if(strstr($_SERVER['REQUEST_URI'],'administrator/index.php')) {
			$input .= '<div class="button2-left" ><div class="blank"><a href="javascript:sortablePeople.selectPerson(\''.$link.'\');" title="'.$selectPerson.'::'.$selectPersonTip.'"  class="hasTip">'.JText::_("JEV Select").'</a></div></div>';
			$input .= "<img src='".JURI::Root()."administrator/images/publish_x.png' class='sortabletrash' id='trashimage' style='display:none;padding-right:2px;cursor:pointer;'/>";
			$input .= "<script type='text/javascript'>
			peopleDeleteWarning='".JText::_("JEV REMOVE PERSON WARNING",true)."';
			sortablePeople.setup();
			var jevpeople = {\n";
			$input .= "duplicateWarning : '".JText::_("Already Selected",true)."'\n";
			$input .= "		}

			</script>";

			$label = JText::_("Select Person");
}
			$customfield = array("label"=>$label,"input"=>$input);
			$customfields["people.one"]=$customfield;
		 } 
		else {
			$customfield = array("label"=>"","input"=>$input);
			$customfields["people"]=$customfield;

			$input = "";

			$firstpass = true;
			$style = "";
			$script = "";
			foreach ($types as $type) {

				$showtype = true;
				if ($type->categories!="all" && $type->categories!=""){
					$cats = explode("|",$type->categories);
					JArrayHelper::toInteger($cats);
					if (!in_array($row->catid(),$cats)) {
						$style .= ".jevplugin_people".$type->type_id." {display:none;}";
						$showtype = false;
					}
				}
				else {
					$cats = array();
				}
				if ($showtype && $type->calendars!="all" && $type->calendars!=""){
					$cals = explode("|",$type->calendars);
					JArrayHelper::toInteger($cals);
					if (!in_array($row->_icsid,$cals)) {
						$style .= ".jevplugin_people".$type->type_id." {display:none;}";
					}
				}
				else {
					$cals = array();
				}

				$typelink = JRoute::_("index.php?option=com_jevpeople&task=people.select&tmpl=component&type_id=".intval($type->type_id));
				$input = "";
				$selectPerson = JText::sprintf("Select by type",$type->title);
				$selectPersonTip = JText::sprintf("Select by type TIP",$type->title);
				$input .= '<div class="button2-left" style="cursor:move'.$style.'"><div class="blank"><a href="javascript:sortablePeople.selectPerson(\''.$typelink.'\');" title="'.$selectPerson.'::'.$selectPersonTip.'"  class="hasTip">'.JText::_("JEV Select").'</a></div></div>';
				$input .= "<img src='".JURI::Root()."administrator/images/publish_x.png' class='sortabletrash' id='trashimage' style='display:none;padding-right:2px;cursor:pointer;'/>";
				if ($firstpass){
					$input .= "<script type='text/javascript'>
			peopleDeleteWarning='".JText::_("JEV REMOVE PERSON WARNING",true)."';
			sortablePeople.setup();
			var jevpeople = {\n";
					$input .= "duplicateWarning : '".JText::_("Already Selected",true)."'\n";
					$input .= "		}
			</script>";
					$firstpass = false;
				}

				$label = JText::sprintf("Select by type",$type->title);

				$script  .= "JevrCategoryPeople.fields.push({'id':'".$type->type_id."' ,'catids':".  json_encode($cats).",'calids':".  json_encode($cals)."});\n ";

				$customfield = array("label"=>$label,"input"=>$input);
				$customfields["people".$type->type_id]=$customfield;
			}
			if ($style!=""){
				$document = JFactory::getDocument();
				$document->addStyleDeclaration($style);

				$this->setupCategorySpecificTypes($script);
			}
		}
		return true;
	}

	/**
	 * Clean out custom fields for event details not matching global event detail
	 *
	 * @param unknown_type $idlist
	 */
	function onCleanCustomDetails($idlist){
		// TODO
		return true;
	}

	/**
	 * Store custom fields
	 *
	 * @param iCalEventDetail $evdetail
	 */
	function onStoreCustomDetails($evdetail){
		$detailid = intval($evdetail->evdet_id);
		$person = array_key_exists("person",$evdetail->_customFields)?$evdetail->_customFields["person"]:"0";
		$db = & JFactory::getDBO();

		// first of all remove all the old mappings
		$sql = "DELETE FROM #__jev_peopleeventsmap WHERE evdet_id=".$detailid;
		$db->setQuery($sql);
		$success = $db->query();

		if ($person!=0 && count($person)>0){
			$order = 0;
			foreach ($person as $val) {
				$sql = "INSERT INTO #__jev_peopleeventsmap SET pers_id=".intval($val).",  evdet_id=".$detailid.", ordering=".$order;
				$db->setQuery($sql);
				$success =  $db->query();
				$order++;
			}
		}
		return $success;

	}

	/**
	 * Clean out custom details for deleted event details
	 *
	 * @param comma separated list of event detail ids $idlist
	 */
	function onDeleteEventDetails($idlist){
		return true;
	}

	function onListIcalEvents( & $extrafields, & $extratables, & $extrawhere, & $extrajoin, & $needsgroupdby=false)
	{
		global $mainframe;
		if($mainframe->isAdmin()) {
			return;
		}

		$pluginsDir = JPATH_ROOT.DS.'plugins'.DS.'jevents';
		$filters = jevFilterProcessing::getInstance(array("peoplesearch","peoplelookup"),$pluginsDir.DS."filters".DS);
		$filters->setWhereJoin($extrawhere,$extrajoin);
		if (!$needsgroupdby) $needsgroupdby=$filters->needsGroupBy();

		// Have we specified specific people for the menu item
		$compparams = JComponentHelper::getParams("com_jevents");

		// If loading from a module then get the modules params from the registry
		$reg =& JFactory::getConfig();
		$modparams = $reg->getValue("jev.modparams",false);
		if ($modparams){
			$compparams = $modparams;
		}

		for ($extra = 0;$extra<20;$extra++){
			$extraval = $compparams->get("extras".$extra, false);
			if (strpos($extraval,"jevp:")===0){
				break;
			}
		}
		if (!$extraval) return true;

		$invalue = str_replace("jevp:","",$extraval);
		$invalue = str_replace(" ","",$invalue);
		if (substr($invalue,strlen($invalue)-1)==","){
			$invalue = substr($invalue,0,strlen($invalue)-1);
		}
		$invalue = explode(",",$invalue);
		JArrayHelper::toInteger($invalue);

		$extrawhere[]  = "pers.pers_id IN (".implode(",",$invalue).")";
		$needsgroupdby = true;
		return true;
	}

	function onSearchEvents( & $extrasearchfields, & $extrajoin, & $needsgroupdby=false){
		static $usefilter;

		if (!isset($usefilter)) {
			global $mainframe;
			if($mainframe->isAdmin()) {
				$usefilter = false;
				return;
			}

			$pluginsDir = JPATH_ROOT.DS.'plugins'.DS.'jevents';
			$filters = jevFilterProcessing::getInstance(array("peoplesearch"),$pluginsDir.DS."filters".DS, false, 'peoplesearch');
			$filters->setSearchKeywords($extrasearchfields,  $extrajoin);

		}

		return true;
	}

	function onListEventsById( & $extrafields, & $extratables, & $extrawhere, & $extrajoin)
	{
		return true;
	}

	function onDisplayCustomFields(&$row){

		$db = & JFactory::getDBO();

		// get the data from database and attach to row
		$detailid = intval($row->evdet_id());

		$sql = "SELECT jp.*, jpt.title as typename,jpt.type_id as type_id, jpt.categories ,jpt.calendars  FROM #__jev_peopleeventsmap as jpm
		LEFT JOIN #__jev_people as jp ON jp.pers_id = jpm.pers_id 
		LEFT JOIN #__jev_peopletypes as jpt ON jpt.type_id = jp.type_id 
		WHERE evdet_id=".$detailid."
		ORDER BY jpm.ordering,jp.type_id, jp.ordering,jp.title";

		JHTML::_('behavior.modal');

		$compparams = JComponentHelper::getParams("com_jevpeople");
		/*
		<option value="0">Person Type in DIVs, Person in UL/LIs</option>
		<option value="1">Person Type in UL/LIs, Person in UL/LIs</option>
		<option value="2">Person Type in DIVs, Persons in single div with separator</option>
		*/
		
		$presentation = $compparams->getValue("presentation",0);
		$separator = $compparams->getValue("separator",",");
		$db->setQuery($sql);
		$jpmlist = $db->loadObjectList();
		$text = "";
		if (count($jpmlist)>0){
			$ptype = false;
			$persopen = false;
			$ulopen =false;
			$divopen = false;
			$needsseparator= false;
			$text .= "<div class='jevpeople'>\n";
			foreach ($jpmlist as $jp) {

				if ($jp->categories!="all" && $jp->categories!=""){
					$cats = explode("|",$jp->categories);
					JArrayHelper::toInteger($cats);
					if (!in_array($row->catid(),$cats)) continue;
				}
				if ($jp->calendars!="all" && $jp->calendars!=""){
					$cals = explode("|",$jp->calendars);
					JArrayHelper::toInteger($cals);
					if (!in_array($row->_icsid,$cals)) continue;
				}

				if ($jp->typename!=$ptype){
					if ($presentation==0 || $presentation==1){
						if ($ulopen){
							$text .= "</ul>\n";
							$ulopen = false;
						}
					}
					else if ($presentation==2){
						if ($divopen){
							$text .= "</div>\n";
							$divopen = false;
						}
					}

					if ($presentation==1){
						if ($ptype){
							$text .= "</li></ul>\n";
						}
						$text .= "<ul class='jevpeople_title'>\n";
						$text .= "<li>".$jp->typename;
					}
					else {
						$text .= "<div class='jevpeople_title'>\n";
						$text .= $jp->typename;
						$text .= "</div>\n";
					}

					if ($presentation==0 || $presentation==1){
						$text .= "<ul>\n";
						$ulopen =true;
					}
					if ($presentation==2){
						$text .= "<div class='jevpeople_entries'>\n";
						$divopen = true;
					}
					$ptype = $jp->typename;
					$needsseparator = false;
				}
				if ($presentation==0 || $presentation==1){
					$text .= "<li class='jevpeople_entries'>\n";
				}

				if ($compparams->getValue("jomsociallist",0) && $jp->linktouser>0){
					/*
					require_once ( JPATH_ROOT.DS.'components'.DS.'com_community'.DS.'defines.community.php');

					// Require the base controller
					require_once (COMMUNITY_COM_PATH.DS.'libraries'.DS.'error.php');
					require_once (COMMUNITY_COM_PATH.DS.'controllers'.DS.'controller.php');
					require_once (COMMUNITY_COM_PATH.DS.'libraries'.DS.'apps.php' );
					require_once (COMMUNITY_COM_PATH.DS.'libraries'.DS.'core.php');
					
					$juser = CFactory::getUser($jp->linktouser);
					$info = $juser->getInfo("FIELD_PAYPAL");
					*/
					$link = JRoute::_("index.php?option=com_community&view=profile&userid=".$jp->linktouser);
				}
				else if ($compparams->getValue("cblist",0) && $jp->linktouser>0){
					$link = JRoute::_("index.php?option=com_comprofiler&task=userProfile&user=".$jp->linktouser);
				}
				else {
					$link = JRoute::_("index.php?option=com_jevpeople&task=people.detail&tmpl=component&pers_id=".$jp->pers_id);
				}
				if ($presentation==2){
					if ($needsseparator){
						$text .= $separator;
					}
					$text .= "<span>";
					$needsseparator = true;
				}

				if ($compparams->getValue("jomsociallist",0) && $jp->linktouser>0){
					$text .= "<a href='$link' >".$jp->title."</a>\n";
				}
				else if ($compparams->getValue("cblist",0) && $jp->linktouser>0){
					$text .= "<a href='$link' >".$jp->title."</a>\n";
				}
				else {
					$text .= "<a href='$link' class='modal' rel='{\"handler\": \"iframe\",\"size\": {\"x\": 750, \"y\": 500},\"closeWithOverlay\": 0}'>".$jp->title."</a>\n";
				}
				if ($presentation==0 || $presentation==1){
					$text .= "</li>\n";
				}
				if ($presentation==2){
					$text .= "</span>\n";
				}

				// New custom fields
				JLoader::register('JevCfParameter',JPATH_SITE."/plugins/jevents/customfields/jevcfparameter.php");
				$compparams = JComponentHelper::getParams("com_jevpeople");
				$template = $compparams->get("template","");
				if ($template!=""){
					$html = "";
					$xmlfile = JPATH_SITE."/plugins/jevents/customfields/templates/".$template;
					if (file_exists($xmlfile)){
						$db = JFactory::getDBO();
						$db->setQuery("SELECT * FROM #__jev_customfields2 WHERE target_id=".intval($jp->pers_id). " AND targettype='com_jevpeople'");
						$customdata = $db->loadObjectList();

						$jcfparams = new JevCfParameter($customdata,$xmlfile,  $jp);
						$customfields = $jcfparams->renderToBasicArray();
					}
					$templatetop = $compparams->get("templatetop","<table border='0'>");
					$templaterow = $compparams->get("templatebody","<tr><td class='label'>{LABEL}</td><td>{VALUE}</td>");
					$templatebottom = $compparams->get("templatebottom","</table>");

					$row->jevp_custompeople_raw = $customfields;
					$row->jevp_custompeople = array();
					$user = JFactory::getUser();
					foreach ($customfields as $customfield) {
						if ($user->aid < intval($customfield["access"])) continue;
						if (!is_null($customfield["hiddenvalue"]) && trim($customfield["value"])==$customfield["hiddenvalue"]) continue;
						$field = array();
						$field["label"]=$customfield["label"];
						$field["value"]=$customfield["value"];
						$row->jevp_custompeople[$customfield["name"]] = $field;
					}
				}

				// Now add the image
				if (!isset($row->_personimage) && $jp->image!=""){
					// Get the media component configuration settings
					$mparams =& JComponentHelper::getParams('com_media');
					// Set the path definitions
					$mediapath =  JURI::root(true).'/'.$mparams->get('image_path', 'images/stories');

					$row->_personimage = '<img src="'.$mediapath.'/jevents/jevpeople/'.$jp->image.'" alt="'.htmlspecialchars($jp->imagetitle).'"/>';
					$row->_personthumb = '<img src="'.$mediapath.'/jevents/jevpeople/thumbnails/thumb_'.$jp->image.'" alt="'.htmlspecialchars($jp->imagetitle).'"/>';
				}
			}
			if ($ulopen){
				$text .= "</ul>\n";
			}
			if ($presentation==1){
				if ($ptype){
					$text .= "</li></ul>\n";
				}
			}
			if ($divopen){
				$text .= "</div>\n";
			}
			$text .= "</div>\n";
		}

		// Add reference to people info in the $event
		$row->_jevpeople = $jpmlist;
		$row->_jevpeopletext = $text;

		return $text;
	}

	function onDisplayCustomFieldsMultiRow(&$rows){
		if ($this->params->get("inlists")==0 || count($rows)==0) {
			return true;
		}
		$db = & JFactory::getDBO();

		// get the data from database and attach to rows
		$detailids = array();
		foreach ($rows as $row){
			$detailids[] = intval($row->evdet_id());
		}

		// New custom fields
		$extrafields = "";
		$extrajoin = "";
		$xmlfile = "";

		JLoader::register('JevCfParameter',JPATH_SITE."/plugins/jevents/customfields/jevcfparameter.php");
		$compparams = JComponentHelper::getParams("com_jevpeople");
		$template = $compparams->get("template","");
		if ($template!=""){
			$html = "";
			$xmlfile = JPATH_SITE."/plugins/jevents/customfields/templates/".$template;
			if (file_exists($xmlfile)){
				$extrafields = ", cf.* ";
				$extrajoin = " LEFT JOIN #__jev_customfields2 as cf ON  cf.target_id=jp.pers_id AND cf.targettype='com_jevpeople'";

				$nullparams = new JevCfParameter(array() ,$xmlfile,  null);
				$nullfields =  $nullparams->renderToBasicArray();
			}
		}

		$sql = "SELECT evdet_id, jp.*, jpt.title as typename,jpt.type_id as type_id, jpt.categories ,jpt.calendars   $extrafields  FROM #__jev_peopleeventsmap as jpm
		LEFT JOIN #__jev_people as jp ON jp.pers_id = jpm.pers_id
		$extrajoin
		LEFT JOIN #__jev_peopletypes as jpt ON jpt.type_id = jp.type_id
		WHERE evdet_id IN (".implode(",",$detailids).")
		GROUP BY jp.pers_id, jpm.evdet_id
		ORDER BY jpm.ordering, jp.type_id, jp.ordering,jp.title, jp.pers_id		";

		$db->setQuery($sql);
		$jpmlist = $db->loadObjectList();

		if ($nullfields){
			foreach ($rows as & $row) {
					$row->jevp_custompeople = array();
					$row->jevp_custompeople_jpm  = array();
					
					$customdata = array();
					
					$foundMatch = false;
					foreach ($jpmlist as $jpm){

						$jp = $jpm;
						if ($jp->categories!="all" && $jp->categories!=""){
							$cats = explode("|",$jp->categories);
							JArrayHelper::toInteger($cats);
							if (!in_array($row->catid(),$cats)) continue;
						}
						if ($jp->calendars!="all" && $jp->calendars!=""){
							$cals = explode("|",$jp->calendars);
							JArrayHelper::toInteger($cals);
							if (!in_array($row->_icsid,$cals)) continue;
						}

						if ($jpm->evdet_id== $row->_eventdetail_id){
							$foundMatch = true;
							 $row->jevp_custompeople_jpm[] = $jpm;
						}

					}
					if ($foundMatch){
						foreach ($nullfields as $nullfield){
							if (isset($customdata[$nullfield["name"]])) continue;

							foreach($row->jevp_custompeople_jpm as $jpm) {
								if ($jpm->name != $nullfield["name"]) continue;
								$customrecord = array();
								$customrecord["id"]=0;
								$customrecord["target_id"]=$jpm->pers_id;
								$customrecord["targettype"]="com_jevpeople";
								$customrecord["name"]=$jpm->name;
								$customrecord["value"]= $jpm->value;
								$customdata[$nullfield["name"]] = $customrecord;

							}


						}

						// Now add the image
						if (!isset($row->_personimage) && $jpm->image!=""){
							// Get the media component configuration settings
							$mparams =& JComponentHelper::getParams('com_media');
							// Set the path definitions
							$mediapath =  JURI::root(true).'/'.$mparams->get('image_path', 'images/stories');

							$row->_personimage = '<img src="'.$mediapath.'/jevents/jevpeople/'.$jpm->image.'" alt="'.htmlspecialchars($jpm->imagetitle).'"/>';
							$row->_personthumb = '<img src="'.$mediapath.'/jevents/jevpeople/thumbnails/thumb_'.$jpm->image.'" alt="'.htmlspecialchars($jpm->imagetitle).'"/>';
						}


						// redindex numerically;
						$customdata = array_values($customdata);

						// convert and format
						$jcfparams = new JevCfParameter($customdata,$xmlfile, $row->jevp_custompeople_jpm[0]);
						$customfields = $jcfparams->renderToBasicArray();

						$row->jevp_custompeople_raw = $customfields;

						$user = JFactory::getUser();
						foreach ($customfields as $customfield) {
							if (isset($row->jevp_custompeople[$customfield["name"]] )) continue;
							
							if ($user->aid < intval($customfield["access"])) continue;
							if (!is_null($customfield["hiddenvalue"]) && trim($customfield["value"])==$customfield["hiddenvalue"]) continue;

							$field = array();
							$field["label"]=$customfield["label"];
							$field["value"]=nl2br($customfield["value"]);
							$row->jevp_custompeople[$customfield["name"]] = $field;
						}



					}

			}
			unset($row);


		}


		foreach ($rows as & $row) {

			if (!$nullfields){
				$row->jevp_custompeople_jpm  = array();
				$foundMatch = false;
				foreach ($jpmlist as $jpm){

					$jp = $jpm;
					if ($jp->categories!="all" && $jp->categories!=""){
						$cats = explode("|",$jp->categories);
						JArrayHelper::toInteger($cats);
						if (!in_array($row->catid(),$cats)) continue;
					}
					if ($jp->calendars!="all" && $jp->calendars!=""){
						$cals = explode("|",$jp->calendars);
						JArrayHelper::toInteger($cals);
						if (!in_array($row->_icsid,$cals)) continue;
					}

					if ($jpm->evdet_id== $row->_eventdetail_id){
						$foundMatch = true;
						 $row->jevp_custompeople_jpm[] = $jpm;
					}

				}
			}
			
			if (isset($row->jevp_custompeople_jpm) && count($row->jevp_custompeople_jpm)>0){
				// Now the people summary
				$compparams = JComponentHelper::getParams("com_jevpeople");
				$presentation = $compparams->getValue("presentation",0);
				$separator = $compparams->getValue("separator",",");

				$ptype = false;
				$persopen = false;
				$ulopen =false;
				$divopen = false;
				$needsseparator= false;
				$text = "<div class='jevpeople'>\n";
				foreach ($row->jevp_custompeople_jpm as $jp) {
					if ($jp->typename!=$ptype){
						if ($presentation==0 || $presentation==1){
							if ($ulopen){
								$text .= "</ul>\n";
								$ulopen = false;
							}
						}
						else if ($presentation==2){
							if ($divopen){
								$text .= "</div>\n";
								$divopen = false;
							}
						}

						if ($presentation==1){
							if ($ptype){
								$text .= "</li></ul>\n";
							}
							$text .= "<ul class='jevpeople_title'>\n";
							$text .= "<li>".$jp->typename;
						}
						else {
							$text .= "<div class='jevpeople_title'>\n";
							$text .= $jp->typename;
							$text .= "</div>\n";
						}

						if ($presentation==0 || $presentation==1){
							$text .= "<ul>\n";
							$ulopen =true;
						}
						if ($presentation==2){
							$text .= "<div class='jevpeople_entries'>\n";
							$divopen = true;
						}
						$ptype = $jp->typename;
						$needsseparator = false;
					}
					if ($presentation==0 || $presentation==1){
						$text .= "<li class='jevpeople_entries'>\n";
					}

					if ($compparams->getValue("jomsociallist",0) && $jp->linktouser>0){
						/*
						require_once ( JPATH_ROOT.DS.'components'.DS.'com_community'.DS.'defines.community.php');

						// Require the base controller
						require_once (COMMUNITY_COM_PATH.DS.'libraries'.DS.'error.php');
						require_once (COMMUNITY_COM_PATH.DS.'controllers'.DS.'controller.php');
						require_once (COMMUNITY_COM_PATH.DS.'libraries'.DS.'apps.php' );
						require_once (COMMUNITY_COM_PATH.DS.'libraries'.DS.'core.php');

						$juser = CFactory::getUser($jp->linktouser);
						$info = $juser->getInfo("FIELD_PAYPAL");
						*/
						$link = JRoute::_("index.php?option=com_community&view=profile&userid=".$jp->linktouser);
					}
					else if ($compparams->getValue("cblist",0) && $jp->linktouser>0){
						$link = JRoute::_("index.php?option=com_comprofiler&task=userProfile&user=".$jp->linktouser);
					}
					else {
						$link = JRoute::_("index.php?option=com_jevpeople&task=people.detail&tmpl=component&pers_id=".$jp->pers_id);
					}
					if ($presentation==2){
						if ($needsseparator){
							$text .= $separator;
						}
						$text .= "<span>";
						$needsseparator = true;
					}

					if ($compparams->getValue("jomsociallist",0) && $jp->linktouser>0){
						$text .= "<a href='$link' >".$jp->title."</a>\n";
					}
					else if ($compparams->getValue("cblist",0) && $jp->linktouser>0){
						$text .= "<a href='$link' >".$jp->title."</a>\n";
					}
					else {
						$text .= "<a href='$link' class='modal' rel='{\"handler\": \"iframe\",\"size\": {\"x\": 750, \"y\": 500},\"closeWithOverlay\": 0}'>".$jp->title."</a>\n";
					}
					if ($presentation==0 || $presentation==1){
						$text .= "</li>\n";
					}
					if ($presentation==2){
						$text .= "</span>\n";
					}
				}
				$text .= "</div>\n";
				$row->_jevpeopletext = $text;
			}
		}


	}

	private function setupCategorySpecificTypes($script)
	{
		// Get all the categories and their parentage
		$db = JFactory::getDBO();
		$db->setQuery("SELECT id, parent_id from #__categories where section='com_jevents' and published=1");
		$catlist = $db->loadObjectList("id");

		$cats = array();
		foreach ($catlist as $cat){
			// extract the complete ancestry
			if (!array_key_exists($cat->id, $cats)){
				$cats[$cat->id]=array();
				$cats[$cat->id][]=$cat->id;
				$parent = ($cat->parent_id>0 && array_key_exists($cat->parent_id,$catlist))?$catlist[$cat->parent_id]:false;
				while($parent){
					$cats[$cat->id][]=$parent->id;
					$parent = ($parent->parent_id>0 && array_key_exists($parent->parent_id,$catlist))?$catlist[$parent->parent_id]:false;
				}
			}
		}
		
		// Must set this up for empty category too
		$cats[0]=array();
		$cats[][]=0;

		$cats = json_encode($cats);

		// setup required fields script
		$doc = JFactory::getDocument ();
		$script2 = <<<SCRIPT
// category conditional people
var JevrCategoryPeople = {
	fields: new Array(),
	cats: $cats,
	setup:function (){
		if (!$('catid')) return;
		var catid = $('catid').value;
		var cats = this.cats[catid];
		// These are the ancestors of this cat
		
		this.fields.each(function (item,i) {
			if (item.catids.length==0) return;
			
			var elem = $(document).getElement(".jevplugin_people"+item.id);

			// hide the item by default
			elem.style.display="none";
			\$A(cats).each (function(cat,i){
				\$A(item.catids).each (function(cat2,i){
					if (cat==cat2){
						elem.style.display="table-row";
					}
				});
				if (\$A(item.catids).contains(parseInt(cat))){
					//alert("matched "+cat + " cf "+item.catids);
					elem.style.display="table-row";
				}
			});

		});
	}
};
window.addEvent("domready",function(){
	if (JevrCategoryPeople){
		JevrCategoryPeople.setup();
		$('catid').addEvent('change',function(){
			JevrCategoryPeople.setup();
		});
		if (!$('ics_id')) return;
		$('ics_id').addEvent('change',function(){
			setTimeout("JevrCategoryPeople.setup()",500);
		});
	}
});
SCRIPT;
		$doc->addScriptDeclaration ( $script2 . $script );


	}

	
	static function fieldNameArray($layout='detail'){
		// only offer in detail view
		$plugin = JPluginHelper::getPlugin("jevents","jevpeople");
		if (!$plugin) return "";
		$params = new JParameter($plugin->params);

		if ($params->get("inlists")==0 && $layout != "detail") return array();

		$labels = array();
		$values = array();

		$labels[] = JText::_("JEV PEOPLE SUMMARY",true);
		$values[] = "JEV_PEOPLE_SUMMARY";

		$labels[] = JText::_("JEV PERSON IMAGE",true);
		$values[] = "JEV_PIMAGE";

		$labels[] = JText::_("JEV PERSON THUMBNAIL",true);
		$values[] = "JEV_PTHUMB";

		JLoader::register('JevCfParameter',JPATH_SITE."/plugins/jevents/customfields/jevcfparameter.php");
		$compparams = JComponentHelper::getParams("com_jevpeople");
		$template = $compparams->get("template","");
		if ($template!=""){
			$html = "";
			$xmlfile = JPATH_SITE."/plugins/jevents/customfields/templates/".$template;
			if (file_exists($xmlfile)){
				$extrafields = ", cf.* ";
				$extrajoin = " LEFT JOIN #__jev_customfields2 as cf ON  cf.target_id=jp.pers_id AND targettype='com_jevpeople'";

				$nullparams = new JevCfParameter(array() ,$xmlfile,  null);
				$nullfields =  $nullparams->renderToBasicArray();

				foreach ($nullfields as $field){
					$values[] = "JEVPCF_".$field["name"];
					$labels[]  = $field["label"];
				}
			}
		}

		$return  = array();
		$return['group'] = JText::_("JEV PEOPLE ADDON",true);
		$return['values'] = $values;
		$return['labels'] = $labels;

		return $return;
	}

	static function substitutefield($row, $code){
		if ($code == "JEV_PEOPLE_SUMMARY"){
			if (isset($row->_jevpeopletext)) return $row->_jevpeopletext;
		}
		if ($code == "JEV_PIMAGE"){
			if (isset($row->_personimage)) return $row->_personimage;
		}
		if ($code == "JEV_PTHUMB"){
			if (isset($row->_personthumb)) return $row->_personthumb;
		}
		if (strpos($code,"JEVPCF_")===0){
			$code = str_replace("JEVPCF_","",$code) ;
				if(isset($row->jevp_custompeople[$code])){
					return $row->jevp_custompeople[$code]["value"];
				}

		}
		return "";
	}
}