<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

class plgJEventsJevlocations extends JPlugin
{
	var $_dbvalid = 0;

	function plgJEventsJevlocations(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$lang 		=& JFactory::getLanguage();
		$lang->load("com_jevlocations", JPATH_SITE);
		$lang->load("com_jevlocations", JPATH_ADMINISTRATOR);
		$lang->load("plg_jevents_jevlocations", JPATH_ADMINISTRATOR);
	}

	/**
	 * When editing a JEvents menu item can add additional menu constraints dynamically
	 */
	function onEditMenuItem(&$menudata, $value,$control_name,$name, $id, $param)
	{
		static $matchingextra = null;
		// find the parameter that matches jevl: (if any)
		if (!isset($matchingextra)){
			$keyvals = $param->toArray();
			foreach ($keyvals as $key=>$val) {
				if (strpos($key,"extras")===0 && strpos($val,"jevl:")===0){
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
		if (($matchingextra==$id && strpos($value,"jevl:")===0) || (($value==""||$value=="0") && $matchingextra===false)){

			$pwidth = $this->params->get("pwidth","750");
			$pheight = $this->params->get("pheight","500");

			$matchingextra = $id;
			$invalue = str_replace("jevl:","",$value);
			$invalue = str_replace(" ","",$invalue);
			$invalue = explode(",",$invalue);
			JArrayHelper::toInteger($invalue);
			JHTML::script('locations.js', 'administrator/components/com_jevlocations/assets/js/' );
			$location = " -- ";
			if (count($invalue)>0){
				$db = & JFactory::getDBO();
				$sql = "SELECT * FROM #__jev_locations where loc_id IN(".implode(",",$invalue).")";
				$db->setQuery($sql);
				$locations = @$db->loadObjectList('loc_id');
			}

			$link = JRoute::_("index.php?option=com_jevlocations&task=locations.select&tmpl=component");
			$input = "<div style='float: left;align-text:left;'><ul id='sortableLocations' style='margin:0px;padding:0px;'>";
			if (is_array($locations)){
				foreach ($invalue as $locid) {
					if (!array_key_exists($locid,$locations)) continue;
					$jpm = $locations[$locid];
					$locname=$jpm->title;
					$lid=$jpm->loc_id;
					$input .= "<li id='sortableloc$lid' >$locname</li>";
				}
			}
			$input .= "</ul>";
			$input .= '<input type="text"  name="'.$control_name.'['.$name.']"  id="menuloc" value="'.$value.'" style="display:none"/>';
			$input .= "</div>";
			$input .= "<img src='".JURI::Root()."administrator/images/publish_x.png' class='sortabletrash' id='trashimageloc' style='display:none;padding-right:2px;'/>";
			$input .= "<script type='text/javascript'>
			sortableLocations.setup();
			var jevlocations = {\n";
			$input .= "duplicateWarning : '".JText::_("Already Selected",true)."'\n";
			$input .= "		}
			</script>";
			$input.='<div class="button2-left"><div class="blank"><a href="javascript:sortableLocations.selectLocation(\''.$link.'\',\''.$pwidth.'\',\''.$pheight.'\')" title="'.JText::_("Select Location")	.'"  >'.JText::_("Select").'</a></div></div>';

			$data = new stdClass();
			$data->name = "location";
			$data->html = $input;
			$data->label = "Specified Location?";
			$data->description = "Specify a location for this menu item";
			$data->options = array();
			$menudata[$id] = $data;
			return;
		}

		// Now the location category
		static $matchingextra2 = null;
		// find the parameter that matches jevlc: (if any)
		if (!isset($matchingextra2)){
			$keyvals = $param->toArray();
			foreach ($keyvals as $key=>$val) {
				if (strpos($key,"extras")===0 && strpos($val,"jevlc:")===0){
					$matchingextra2 = str_replace("extras","",$key);
					break;
				}
			}
			if (!isset($matchingextra2)){
				$matchingextra2 = false;
			}
		}

		// either we found matching extra and this is the correct id or we didn't find matching extra and the value is blank
		if (($matchingextra2==$id && strpos($value,"jevlc:")===0) || (($value==""||$value=="0") && $matchingextra2===false)){

			$matchingextra2 = $id;
			$invalue = str_replace("jevlc:","",$value);
			$invalue = str_replace(" ","",$invalue);
			$invalue = explode(",",$invalue);
			JArrayHelper::toInteger($invalue);

			if (count($invalue)>0){
				$db = & JFactory::getDBO();
				$sql = 'SELECT c.id, c.title as ctitle,p.title as ptitle, gp.title as gptitle, ggp.title as ggptitle, ' .
				' CASE WHEN CHAR_LENGTH(p.title) THEN CONCAT_WS(" => ", p.title, c.title) ELSE c.title END as title'.
				' FROM #__categories AS c' .
				' LEFT JOIN #__categories AS p ON p.id=c.parent_id' .
				' LEFT JOIN #__categories AS gp ON gp.id=p.parent_id ' .
				' LEFT JOIN #__categories AS ggp ON ggp.id=gp.parent_id ' .
				' WHERE c.published = 1 ' .
				' AND c.section = "com_jevlocations2"' .
				' ORDER BY c.section, ggptitle, gptitle, ptitle, ctitle ';

				$db->setQuery($sql);
				$loccats = @$db->loadObjectList('id');
			}

			$input = "<div style='float: left;align-text:left;'>";
			$input .= "<select multiple='multiple' id='jevloccats'  size='5' onchange='updateJevLocCats();'>";
			$selected = (count($invalue)==0)?"selected='selected'":"";
			$input .= "<option value='' $selected>---</option>";
			foreach ($loccats as $loccat) {
				$title=$loccat->title;
				$catid=$loccat->id;
				$selected = in_array($loccat->id,$invalue)?"selected='selected'":"";
				$input .= "<option value='$catid' $selected>$title</option>";
			}
			$input .= "</select>";
			$input .= '<input type="hidden"  name="'.$control_name.'['.$name.']"  id="jevloccat" value="'.$value.'" style="display:block"/>';
			$input .= "</div>";

			$script = '
			function updateJevLocCats(){
				var select = document.getElement("select#jevloccats");
				var input = document.getElementById("jevloccat");
				input.value="";
				$A(select.options).each(
					function(item,index){
						if (item.selected) {
							// if select none - reset everything else
							if (item.value=="") {
								select.selectedIndex=0;
								return;
							}
							if (input.value!="") input.value+=",";
							input.value+="jevlc:"+item.value;
						}
					}
				);
			}
			';
			$document = JFactory::getDocument();
			$document->addScriptDeclaration($script);


			$data = new stdClass();
			$data->name = "loccat";
			$data->html = $input;
			$data->label = "Location Categories?";
			$data->description = "Specify location categories for this menu item";
			$data->options = array();
			$menudata[$id] = $data;
			return;
		}

		// Now the location country/state/city
		static $matchingextra3 = null;
		$locparams = JComponentHelper::getParams("com_jevlocations");

		// find the parameter that matches jevlcsc: (if any)
		if (!isset($matchingextra3)){
			$keyvals = $param->toArray();
			foreach ($keyvals as $key=>$val) {
				if (strpos($key,"extras")===0 && strpos($val,"jevlcsc:")===0){
					$matchingextra3 = str_replace("extras","",$key);
					break;
				}
			}
			if (!isset($matchingextra3)){
				$matchingextra3 = false;
			}
		}

		// either we found matching extra and this is the correct id or we didn't find matching extra and the value is blank
		if (($matchingextra3==$id && strpos($value,"jevlcsc:")===0) || (($value==""||$value=="0") && $matchingextra3===false)){

			$usecats = $locparams->get("usecats",0);
			if ($usecats){

				$matchingextra3 = $id;
				$invalue = str_replace("jevlcsc:","",$value);
				$invalue = str_replace(" ","",$invalue);
				$invalue = explode(",",$invalue);
				JArrayHelper::toInteger($invalue);

				if (count($invalue)>0){
					$db = & JFactory::getDBO();

					// Make sure there aren't too many
					$sql = 'SELECT count(c.id) FROM #__categories AS c WHERE c.published = 1 AND c.section = "com_jevlocations"';
					$db->setQuery($sql);
					if (intval($db->loadResult())>500)	{
						$sql = 'SELECT c.id, c.title as ctitle,p.title as ptitle,' .
						' CASE WHEN CHAR_LENGTH(p.title) THEN CONCAT_WS(" => ", p.title, c.title) ELSE c.title END as title'.
						' FROM #__categories AS c' .
						' LEFT JOIN #__categories AS p ON p.id=c.parent_id' .
						' LEFT JOIN #__categories AS gp ON gp.id=p.parent_id ' .
						' WHERE c.published = 1 ' .
						' AND c.section = "com_jevlocations" '.
						' ORDER BY  ptitle ASC, ctitle ASC';

					}
					else {

						$sql = 'SELECT c.id, c.title as ctitle,p.title as ptitle, gp.title as gptitle, ' .
						' CASE WHEN CHAR_LENGTH(p.title) THEN CONCAT_WS(" => ", p.title, c.title) ELSE c.title END as title'.
						' FROM #__categories AS c' .
						' LEFT JOIN #__categories AS p ON p.id=c.parent_id' .
						' LEFT JOIN #__categories AS gp ON gp.id=p.parent_id ' .
						' WHERE c.published = 1 ' .
						' AND c.section = "com_jevlocations" '.
						' ORDER BY gptitle ASC, ptitle ASC, ctitle ASC';

					}
					$db->setQuery($sql);
					$loccats = @$db->loadObjectList('id');
				}

				$input = "<div style='float: left;align-text:left;'>";
				$input .= "<select multiple='multiple' id='jevloccats2'  size='5'  onchange='updateJevLocCats2();'>";
				$selected = (count($invalue)==0)?"selected='selected'":"";
				$input .= "<option value='' $selected>---</option>";
				foreach ($loccats as $loccat) {
					$title = $loccat->ctitle;
					if (!is_null($loccat->ptitle)){
						$title = $loccat->ptitle."=>".$title;
					}
					if (isset($loccat->gptitle) && !is_null($loccat->gptitle)){
						$title = $loccat->gptitle."=>".$title;
					}
					$catid=$loccat->id;
					$selected = in_array($loccat->id,$invalue)?"selected='selected'":"";
					$input .= "<option value='$catid' $selected>$title</option>";
				}
				$input .= "</select>";
				$input .= '<input type="hidden"  name="'.$control_name.'['.$name.']"  id="jevloccat2" value="'.$value.'" style="display:block"/>';
				$input .= "</div>";

				$script = '
			function updateJevLocCats2(){
				var select = document.getElement("select#jevloccats2");
				var input = document.getElementById("jevloccat2");
				input.value="";
				$A(select.options).each(
					function(item,index){
						if (item.selected) {
							// if select none - reset everything else
							if (item.value=="") {
								select.selectedIndex=0;
								return;
							}
							if (input.value!="") input.value+=",";
							input.value+="jevlcsc:"+item.value;
						}
					}
				);
			}
			';
				$document = JFactory::getDocument();
				$document->addScriptDeclaration($script);

				$data = new stdClass();
				$data->name = "loccsc";
				$data->html = $input;
				$data->label = "Country/State Filter";
				$data->description = "Select matching countries or states for this menu item";
				$data->options = array();
				$menudata[$id] = $data;
				return;

			}
			else {
				$matchingextra3 = $id;
				$invalue = str_replace("jevlcsc:","",$value);
				$invalue = explode(",",$invalue);

				if (count($invalue)>0){
					$db = & JFactory::getDBO();

					// Make sure there aren't too many
					$sql = 'SELECT count(CONCAT(loc.country,loc.state,loc.city)) FROM #__jev_locations as loc where loc.published=1 AND loc.global=1  AND  (loc.country !="" OR loc.state !="" OR loc.city !="")';
					$db->setQuery($sql);
					$count = $db->loadResult();
					if (intval($count)>500)	{
						$sql = 'SELECT loc.country,loc.state,"" as city FROM #__jev_locations as loc ' .
						' WHERE loc.published = 1 AND loc.global=1' .
						' ORDER BY  loc.country ASC, loc.state ASC  ASC';

					}
					else {

						$sql = 'SELECT loc.country,loc.state,loc.city  FROM #__jev_locations as loc ' .
						' WHERE loc.published = 1 AND loc.global=1' .
						' AND  (loc.country !="" OR loc.state !="" OR loc.city !="") '.
						' ORDER BY  loc.country ASC, loc.state ASC,loc.city ASC';

					}
					$db->setQuery($sql);
					$rows = @$db->loadObjectList();

					$loccats = array();
					foreach ($rows as $row) {
						if (array_key_exists($row->country,$loccats)) continue;
						if ($row->country=="") continue;
						$crow = clone $row;
						$crow->state="";
						$crow->city="";
						$loccats[$row->country]=$crow;
					}
					foreach ($rows as $row) {
						if (array_key_exists($row->country."=>".$row->state,$loccats)) continue;
						if ($row->state=="") continue;
						$crow = clone $row;
						$crow->city="";
						$loccats[$row->country."=>".$row->state]=$crow;
					}
					foreach ($rows as $row) {
						if (array_key_exists($row->country."=>".$row->state."=>".$row->city,$loccats)) continue;
						if ($row->city=="") continue;
						$loccats[$row->country."=>".$row->state."=>".$row->city]=$row;
					}

					$input="";
					$input .= "<select id='jevloccats3'   onchange='updateJevLocCats3();'>";
					$selected = (count($invalue)==0)?"selected='selected'":"";
					$input .= "<option value='' $selected>---</option>";
					foreach ($loccats as $loccat) {
						$title = "";
						$lookup = array();
						if (isset($loccat->city) && !is_null($loccat->city) && $loccat->city!=""){
							$title = $loccat->city;
							$lookup["city"]=$loccat->city;
							if (!is_null($loccat->state) && $loccat->state!=""){
								$title = $loccat->state.($title!=""?"=>".$title:"");
								$lookup["state"]=$loccat->state;
							}
							else {
								$title = " -- =>".$title;
							}
							if (!is_null($loccat->country) && $loccat->country!=""){
								$title = $loccat->country.($title!=""?"=>".$title:"");
								$lookup["country"]=$loccat->country;
							}
							else {
								$title = " -- =>".$title;
							}
						}
						else {
							if (!is_null($loccat->state) && $loccat->state!=""){
								$title = $loccat->state;
								$lookup["state"]=$loccat->state;
							}
							if (isset($loccat->country) && !is_null($loccat->country)  && $loccat->country!=""){
								$title = $loccat->country.($title!=""?"=>".$title:"");
								$lookup["country"]=$loccat->country;
							}
							else {
								$title = " -- =>".$title;
							}

						}
						$fixedtitle = base64_encode(serialize($lookup));
						$selected = in_array($fixedtitle,$invalue)?"selected='selected'":"";
						$input .= "<option value='".$fixedtitle."' $selected>$title</option>";
					}
					$input .= "</select>";
					$input .= '<input type="hidden"  name="'.$control_name.'['.$name.']"  id="jevloccat3" value="'.$value.'" style="display:block"/>';
					$input .= "</div>";

					$script = '
					function updateJevLocCats3(){
						var select = document.getElement("select#jevloccats3");
						var input = document.getElementById("jevloccat3");
						input.value="";
						$A(select.options).each(
						function(item,index){
							if (item.selected) {
								// if select none - reset everything else
								if (item.value=="") {
									select.selectedIndex=0;
									return;
								}
								if (input.value!="") input.value+=",";
								input.value+="jevlcsc:"+item.value;
							}
						}
						);
					}
					';
					$document = JFactory::getDocument();
					$document->addScriptDeclaration($script);

				}

				$data = new stdClass();
				$data->name = "loccsc";
				$data->html = $input;
				$data->label = "Country/State/City Filter";
				$data->description = "Specify country, city or state for location to use in this menu item";
				$data->options = array();
				$menudata[$id] = $data;
				return;

			}

		}

	}

	/**
	 * Create custom event location setting code
	 *
	 * return true if value is set
	 * 
	 * @param jEventCal $row
	 * @return unknown
	 */
	function onEditLocation( & $row)
	{
		JHTML::script('locations.js', 'administrator/components/com_jevlocations/assets/js/' );
		$location = " -- ";
		if (intval($row->location())>0){
			$db = & JFactory::getDBO();
			$sql = "SELECT * FROM #__jev_locations where published=1 and loc_id=".intval($row->location());
			$db->setQuery($sql);
			$locations = @$db->loadObjectList();
			if (count($locations)>0){
				$location = $locations[0]->title;
			}
		}

		$pwidth = $this->params->get("pwidth","750");
		$pheight = $this->params->get("pheight","500");

		// don't call this id location it causes problems in javascript
		echo '<input type="hidden" name="location" id="locn" value="'.$row->location().'"/>';
		//echo '<input type="button" onclick="selectLocation(\''.$row->location().'\' ,\''.JRoute::_("index.php?option=com_jevlocations&task=locations.select&tmpl=component").'\')" value="'.JText::_("Select Location").'"/>';
		//echo  "<span id='evlocation' style='float:left;'>$location</span>";
		echo '<input type="text" name="evlocation_notused" disabled="disabled" id="evlocation" value="'.$location.'" style="float:left"/>';
		echo '<div class="button2-left"><div class="blank"><a href="javascript:selectLocation(\''.$row->location().'\' ,\''.JRoute::_("index.php?option=com_jevlocations&task=locations.select&tmpl=component").'\',\''.$pwidth.'\',\''.$pheight.'\')" title="'.JText::_("Select Location").'"  >'.JText::_("Select").'</a></div></div>';
		echo '<div class="button2-left"><div class="blank"><a href="javascript:removeLocation();" title="'.JText::_("Remove Location").'"  >'.JText::_("Remove").'</a></div></div>';

		return true;
	}

	function onEditCustom( &$row, &$customfields )
	{
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
		// if importing an ical then the location field will be non-text.  I need to convert this if possible
		$compparams = JComponentHelper::getParams("com_jevlocations");
		if ($compparams->get("importlocations",0)){
			if (isset($evdetail->location) && !is_numeric($evdetail->location) && trim($evdetail->location)!=""){
				// find matching locations
				// 1. Find the creator id via the event
				$db = JFactory::getDBO();
				$db->setQuery("SELECT * FROM #__jevents_vevent WHERE detail_id = ".intval($evdetail->evdet_id));
				$event = $db->loadObject();

				if (!$event || $event->created_by==0) return true;
				// Find global and common locations that match.
				$db->setQuery("SELECT * FROM #__jev_locations WHERE (global=1 OR created_by= ".intval($event->created_by).") AND title=".$db->Quote($evdetail->location)." ORDER BY global ASC");
				$locations = $db->loadObjectList();
				if (count($locations)>0){
					$evdetail->location = $locations[0]->loc_id;
					$db->setQuery("UPDATE #__jevents_vevdetail SET location = ".$locations[0]->loc_id. " WHERE evdet_id = ".intval($evdetail->evdet_id));
					$db->query();
				}
			}
		}
		return true;
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
		//	return;
		}

		$pluginsDir = JPATH_ROOT.DS.'plugins'.DS.'jevents';
		 JLoader::register('jevFilterProcessing',JEV_PATH."/libraries/filters.php");
		$filters = jevFilterProcessing::getInstance(array("locationsearch","locationlookup","locationcategory","locationcity","locationstate","geofilter"),$pluginsDir.DS."filters".DS);
		$filters->setWhereJoin($extrawhere,$extrajoin);

		// I always do a join on the location table so can always get this extra field
		$compparams = JComponentHelper::getParams("com_jevlocations");
		$usecats = $compparams->get("usecats",0);
		// Do we have any old style locations?
		$usehybrid = $compparams->get("hybrid",1);
		if (!$usecats){
			if ($usehybrid){
				$extrafields .= ", CASE WHEN loc.title IS NULL THEN det.location ELSE loc.title END as location,loc.title as loc_title, loc.loc_id, loc.street as loc_street, loc.description as loc_desc, loc.postcode as loc_postcode, loc.city as loc_city, loc.country as loc_country, loc.state as loc_state";
			}
			else {
				$extrafields .= ", loc.loc_id,loc.title as loc_title, loc.title as location, loc.street as loc_street, loc.description as loc_desc, loc.postcode as loc_postcode, loc.city as loc_city, loc.country as loc_country, loc.state as loc_state";
			}
		}
		else {
			if ($usehybrid){
				$extrafields .= ", loc.loc_id,CASE WHEN loc.title IS NULL THEN det.location ELSE loc.title END as location, loc.title as loc_title, loc.street as loc_street, loc.description as loc_desc, loc.postcode as loc_postcode";
			}
			else {
				$extrafields .= ", loc.loc_id,loc.title as location, loc.title as loc_title";
			}
		}

		$extrafields .= ", loc.phone as loc_phone	";
		$extrafields .= ", loc.image as loc_image	";
		$extrafields .= ", loc.url as loc_url	";
		$extrafields .= ", loc.geolon as loc_lon	";
		$extrafields .= ", loc.geolat as loc_lat	";
		$extrafields .= ", loc.geozoom as loc_zoom	";

		if ( $this->params->get("alwayscatlink",0)){
			$extrafields .= ", loccat.title as loc_category	";
		}

		// Have we specified specific locations for the menu item
		$compparams = JComponentHelper::getParams("com_jevents");

		$reg =& JFactory::getConfig();
		$modparams = $reg->getValue("jev.modparams",false);
		if ($modparams){
			$compparams = $modparams;
		}
		$extraval = false;
		for ($extra = 0;$extra<20;$extra++){
			$extraval = $compparams->get("extras".$extra, false);
			if (strpos($extraval,"jevl:")===0){
				break;
			}
		}
		if ($extraval) {

			$invalue = str_replace("jevl:","",$extraval);
			$invalue = str_replace(" ","",$invalue);
			if (substr($invalue,strlen($invalue)-1)==","){
				$invalue = substr($invalue,0,strlen($invalue)-1);
			}
			$invalue = explode(",",$invalue);
			JArrayHelper::toInteger($invalue);

			$extrawhere[]  = "det.location IN (".implode(",",$invalue).")";
		}

		// location categories
		$extraval = false;
		for ($extra = 0;$extra<20;$extra++){
			$extraval = $compparams->get("extras".$extra, false);
			if (strpos($extraval,"jevlc:")===0){
				break;
			}
		}
		if ($extraval) {
			$invalue = str_replace("jevlc:","",$extraval);
			$invalue = str_replace(" ","",$invalue);
			if (strlen($invalue)>0){
				$invalue = explode(",",$invalue);
				JArrayHelper::toInteger($invalue);
			}
			else {
				return true;
			}

			$extrawhere[]  = "loc.loccat IN (".implode(",",$invalue).")";
		}

		// location country, state, city
		$extraval = false;
		for ($extra = 0;$extra<20;$extra++){
			$extraval = $compparams->get("extras".$extra, false);
			if (strpos($extraval,"jevlcsc:")===0){
				break;
			}
		}
		if ($extraval) {
			$locparams = JComponentHelper::getParams("com_jevlocations");

			$usecats = $locparams->get("usecats",0);
			if ($usecats){
				$invalue = str_replace("jevlcsc:","",$extraval);
				$invalue = str_replace(" ","",$invalue);
				if (strlen($invalue)>0){
					$invalue = explode(",",$invalue);
					JArrayHelper::toInteger($invalue);

					$extrajoin[]  = ' #__categories AS locmcity ON loc.catid = locmcity.id AND locmcity.section="com_jevlocations"';
					$extrajoin[]  = ' #__categories AS locmstate ON locmcity.parent_id = locmstate.id AND locmstate.section="com_jevlocations"';
					$extrajoin[]  = ' #__categories AS locmcountry ON locmstate.parent_id = locmcountry.id AND locmcountry.section="com_jevlocations"';

					$extrawhere[]  = " (loc.catid IN (".implode(",",$invalue).") OR locmstate.id IN (".implode(",",$invalue).") OR locmcountry.id IN (".implode(",",$invalue).") ) ";

				}
				else {
					return true;
				}
			}
			else {
				$invalue = str_replace("jevlcsc:","",$extraval);
				$invalue = str_replace(" ","",$invalue);
				if (strlen($invalue)>0){
					$invalue = explode(",",$invalue);
					if (count($invalue)==1){
						$invalue = base64_decode($invalue[0]);
						$invalue = @unserialize($invalue);
						if (is_array($invalue)){
							$db = JFactory::getDBO();
							$whereparts = array();
							if (array_key_exists("country",$invalue)){
								$whereparts[] = "loc.country = ".$db->Quote($invalue['country']);
							}
							if (array_key_exists("state",$invalue)){
								$whereparts[] = "loc.state = ".$db->Quote($invalue['state']);
							}
							if (array_key_exists("city",$invalue)){
								$whereparts[] = "loc.city = ".$db->Quote($invalue['city']);
							}
							if (count($whereparts)>0){
								$extrawhere[]  = " (".implode(" AND ",$whereparts).")";
							}
						}
					}
				}

			}

		}

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
			$filters = jevFilterProcessing::getInstance(array("locationsearch"),$pluginsDir.DS."filters".DS, false, 'jevlocations');
			$filters->setSearchKeywords($extrasearchfields,  $extrajoin);
			
		}

		return true;
	}

	function onListEventsById( & $extrafields, & $extratables, & $extrawhere, & $extrajoin, & $needsgroupdby=false)
	{
		if (JRequest::getString("task")=="icals.icalevent"){
			return $this->onListIcalEvents( $extrafields, $extratables, $extrawhere, $extrajoin, $needsgroupdby);
		}
	}

	function onDisplayCustomFields(&$row){

		$db = & JFactory::getDBO();

		// Deal with location
		if (is_numeric($row->location()) && $row->location()>0){
			$loc_id = $row->location();

			$compparams = JComponentHelper::getParams("com_jevlocations");
			$usecats = $compparams->get("usecats",0);
			if ($usecats){
				$sql = "SELECT loc.*, cc1.title AS city, cc2.title AS state, cc3.title AS country FROM #__jev_locations as loc"
				. " LEFT JOIN #__categories AS cc1 ON cc1.id = loc.catid  AND cc1.section='com_jevlocations'"
				. ' LEFT JOIN #__categories AS cc2 ON cc1.parent_id = cc2.id '
				. ' LEFT JOIN #__categories AS cc3 ON cc2.parent_id = cc3.id ';
			}
			else {
				$sql = "SELECT loc.* FROM #__jev_locations as loc";
				$sql .= " LEFT JOIN #__categories AS cat ON cat.id=loc.catid AND cat.section='com_jevlocations'";
			}
			$sql .= " WHERE loc.loc_id=".$row->location();
			$db->setQuery($sql);
			$location = $db->loadObject();

			if ($location && JRequest::getString("task","")!="icalevent.edit" && JRequest::getString("task","")!="icalrepeat.edit"){
				//$row->location($location->title);

				$compparams = JComponentHelper::getParams("com_jevlocations");

				JLoader::register('JevLocationsHelper',JPATH_ADMINISTRATOR."/components/com_jevlocations/libraries/helper.php");
				$googlekey = JevLocationsHelper::getApiKey();//$compparams->get("googlemapskey","");
				$googleurl = JevLocationsHelper::getApiUrl(); //$compparams->get("googlemaps",'http://maps.google.com');

				if ($googlekey!=""){
					JHTML::script( '/maps?file=api&amp;v=2.x&amp;key='.$googlekey , $googleurl, true);
					$document =& JFactory::getDocument();
					$long =$location->geolon;
					$lat = $location->geolat;
					// zoome is reduced for the smaller map displayed here
					$zoom = $location->geozoom - $this->params->get("reducezoom",3);
	
					$detailpopup = $this->params->get("detailpopup",1);
					if ($detailpopup){
						$locurl = JRoute::_("index.php?option=com_jevlocations&task=locations.detail&tmpl=component&loc_id=$loc_id&title=".JFilterOutput::stringURLSafe($location->title));
					}
					else {
						$locurl = JRoute::_("index.php?option=com_jevlocations&task=locations.detail&se=1&loc_id=$loc_id&title=".JFilterOutput::stringURLSafe($location->title));
					}
	
					$pwidth = $this->params->get("pwidth","750");
					$pheight = $this->params->get("pheight","500");
	
					$maptype = $this->params->get("maptype","G_NORMAL_MAP");
	
					$script=<<<SCRIPT
var myMap = false;
var myMarker = false;
function myMapload(){
	if (GBrowserIsCompatible()) {
		if (!document.getElementById("gmap")) return;
		myMap = new GMap2(document.getElementById("gmap"));
		
		//myMap.addControl( new GSmallMapControl() );
		//myMap.addControl( new GMapTypeControl()) ;
		//myMap.addControl( new GOverviewMapControl(new GSize(60,60)) );

		myMap.setMapType($maptype);
		/*
		// Create our "tiny" marker icon
		var blueIcon = new GIcon(G_DEFAULT_ICON);
		blueIcon.image = "http://www.google.com/intl/en_us/mapfiles/ms/micons/blue-dot.png";
		                
		// Set up our GMarkerOptions object
		markerOptions = { icon:blueIcon, draggable:true };
		*/
		//markerOptions = {draggable:true };
		markerOptions = {draggable:false };
		
		var point = new GLatLng($lat,$long);
		myMap.setCenter(point, $zoom );

		myMarker = new GMarker(point, markerOptions);
		myMap.addOverlay(myMarker);

		GEvent.addListener(myMap, "click", function(overlay,latlng) {
			if ($detailpopup){
			SqueezeBox.initialize({});
			SqueezeBox.setOptions(SqueezeBox.presets,{'handler': 'iframe','size': {'x': $pwidth, 'y': $pheight},'closeWithOverlay': 0});
			SqueezeBox.url = "$locurl";
			SqueezeBox.setContent('iframe', SqueezeBox.url );
			}
			else {
				document.location = "$locurl";
			}
		});
		
		
	}
};
// delay by 1 second so that the page is properly rendered before we get the map
window.addEvent("load",function (){window.setTimeout("myMapload()",1000);});
//window.addEvent("load",myMapload);
SCRIPT;
					$document->addScriptDeclaration($script);
				}
				
				JHTML::_('behavior.modal');

				if ($detailpopup){
					$location->linkstart = "<a href='$locurl' class='modal' rel='{handler:\"iframe\",\"size\": {\"x\": $pwidth, \"y\": $pheight}}'>";
				}
				else {
					$location->linkstart = "<a href='$locurl'>";
				}

				$template = $this->params->get("template","");
				if ($template!=""){
					$text = $template;
					$text = str_replace("{TITLE}",$location->title==""?"":$location->title,$text);
					$text = str_replace("{STREET}",$location->street==""?"":$location->street,$text);
					$text = str_replace("{CITY}",$location->city==""?"":$location->city,$text);
					$text = str_replace("{STATE}",$location->state==""?"":$location->state,$text);
					$text = str_replace("{POSTCODE}",$location->postcode==""?"":$location->postcode,$text);
					$text = str_replace("{COUNTRY}",$location->country==""?"":$location->country,$text);
					$text = str_replace("{PHONE}",$location->phone==""?"":$location->phone,$text);

					if ($location->image!=""){
						$params =& JComponentHelper::getParams('com_media');
						$mediabase = JURI::root().$params->get('image_path', 'images/stories');
						// folder relative to media folder
						$locparams = JComponentHelper::getParams("com_jevlocations");
						$folder = "jevents/jevlocations";
						$thimg = '<img src="'.$mediabase.'/'.$folder.'/thumbnails/thumb_'.$location->image.'" />' ;
						$img = '<img src="'.$mediabase.'/'.$folder.'/'.$location->image.'" />' ;
						$text = str_replace("{IMAGE}",$img,$text);
						$text = str_replace("{THUMBNAIL}",$thimg,$text);
					}
					else {
						$text = str_replace("{IMAGE}","",$text);
						$text = str_replace("{THUMBNAIL}","",$text);
					}

					if (strlen($location->url)>0) {
						$pattern = '[a-zA-Z0-9&?_.,=%\-\/]';
						if (strpos($location->url,"http://")===false) $location->url = "http://".trim($location->url);
						$location->url = preg_replace('#(http://)('.$pattern.'*)#i', '<a href="\\1\\2"  target="_blank">\\1\\2</a>', $location->url);

						//$url = preg_replace('#(http://)('.$pattern.'*)#i', '<a href="\\1\\2"  target="_blank">', $location->url);
						$text = str_replace("{URL}",$location->url,$text);
					}
					else {
						$text = str_replace("{URL}","",$text);
					}

					$text = str_replace("{LINK}",$location->linkstart,$text);
					$text = str_replace("{/LINK}","</a>",$text);

					$text = str_replace("{DESCRIPTION}",$location->description==""?"":$location->description,$text);
					$map ='<div id="gmap" style="width:'.$this->params->get("gwidth",200).'px; height:'.$this->params->get("gheight",150).'px;overflow:hidden;"></div>';
					$location->map = $map;

					$text = str_replace("{MAP}",$map,$text);
				}
				else {
					$text =$location->title;
					if (strlen($location->street)>0) $text .='<br/>'.$location->street;
					if (strlen($location->city)>0) $text .='<br/>'.$location->city;
					if (strlen($location->state)>0) $text .="<br/>".$location->state;
					if (strlen($location->postcode)>0) $text .="<br/>".$location->postcode;
					if (strlen($location->country)>0) $text .="<br/>".$location->country;
					if (strlen($location->phone)>0) $text .="<br/>".$location->phone;

					if (strlen($location->url)>0) {
						$pattern = '[a-zA-Z0-9&?_.,=%\-\/]';
						if (strpos($location->url,"http://")===false) $location->url = "http://".trim($location->url);
						$location->url = preg_replace('#(http://)('.$pattern.'*)#i', '<a href="\\1\\2"  target="_blank">\\1\\2</a>', $location->url);
						$text .="<br/>".$location->url;
					}

					$map = '<div id="gmap" style="width:'.$this->params->get("gwidth",200).'px; height:'.$this->params->get("gheight",150).'px;overflow:hidden;"></div>';
					$location->map = $map;
					$text .= $map;
					if ($this->params->get("showdesc",0) && strlen($location->description)>0) $text .="<br/>".$location->description;
				}
				$row->location($text);

				$row->_locationsummary = $text;
				// Add reference to location info in the $event
				$row->_jevlocation = $location;

			}
			else {
				$row->_locationsummary = "";
			}
			/*
			// check if detail page layout is enabled
			// find published template
			static $template;
			if (!isset($template)){
				$db = JFactory::getDBO();
				$db->setQuery("SELECT * FROM #__jev_defaults WHERE state=1 AND name= 'icalevent.detail_body'");
				$template = $db->loadObject();
			}
			if (!is_null($template) && $template->value!=""){
				return $row->_locationsummary;
			}
			 /
			 */
		}

	}

	static function fieldNameArray($layout='detail'){

		// only offer in detail view
		//if ($layout != "detail") return array();

		$labels = array();
		$values = array();
		if ($layout == "detail") {
			$labels[] = JText::_("JEV LOCATION SUMMARY",true);
			$values[] = "JEVLOCATION_SUMMARY";
		}
		$labels[] = JText::_("JEV LOCATION TITLE",true);
		$values[] = "JEVLOCATION_TITLE";
		$labels[] = JText::_("JEV LOCATION DESCRIPTION",true);
		$values[] = "JEVLOCATION_DESCRIPTION";
		$labels[] = JText::_("JEV LOCATION STREET",true);
		$values[] = "JEVLOCATION_STREET";
		$labels[] = JText::_("JEV LOCATION CITY",true);
		$values[] = "JEVLOCATION_CITY";
		$labels[] = JText::_("JEV LOCATION STATE",true);
		$values[] = "JEVLOCATION_STATE";
		$labels[] = JText::_("JEV LOCATION COUNTRY",true);
		$values[] = "JEVLOCATION_COUNTRY";
		$labels[] = JText::_("JEV LOCATION POSTCODE",true);
		$values[] = "JEVLOCATION_POSTCODE";
		$labels[] = JText::_("JEV LOCATION PHONE",true);
		$values[] = "JEVLOCATION_PHONE";
		// only offer in detail view
		if ($layout == "detail") {
			$labels[] = JText::_("JEV LOCATION MAP",true);
			$values[] = "JEVLOCATION_MAP";
		}
		$labels[] = JText::_("JEV LOCATION IMAGE",true);
		$values[] = "JEVLOCATION_IMAGE";
		$labels[] = JText::_("JEV LOCATION URL",true);
		$values[] = "JEVLOCATION_URL";
		$labels[] = JText::_("JEV LOCATION LINK A",true);
		$values[] = "JEVLOCATION_A";
		$labels[] = JText::_("JEV LOCATION LINK CLOSE A",true);
		$values[] = "JEVLOCATION_CLOSE_A";

		$return  = array();
		$return['group'] = JText::_("JEV LOCATION ADDON",true);
		$return['values'] = $values;
		$return['labels'] = $labels;

		return $return;
	}

	static function substitutefield($row, $code){
		switch ($code) {
			case "JEVLOCATION_SUMMARY":
				if (isset($row->_locationsummary)) return $row->_locationsummary;
				break;
			case "JEVLOCATION_IMAGE":
				if (isset($row->_jevlocation)){
					$field = "image";

					if (strlen($row->_jevlocation->$field)>0) {
						$params =& JComponentHelper::getParams('com_media');
						$mediabase = JURI::root().$params->get('image_path', 'images/stories');
						// folder relative to media folder
						$locparams = JComponentHelper::getParams("com_jevlocations");
						$folder = "jevents/jevlocations";
						return '<img src="'.$mediabase.'/'.$folder.'/thumbnails/thumb_'.$row->_jevlocation->$field.'" />' ;
					}
				}
				// list version
				else if (isset($row->_loc_image)) {
					$params =& JComponentHelper::getParams('com_media');
					$mediabase = JURI::root().$params->get('image_path', 'images/stories');
					// folder relative to media folder
					$locparams = JComponentHelper::getParams("com_jevlocations");
					$folder = "jevents/jevlocations";
					return '<img src="'.$mediabase.'/'.$folder.'/thumbnails/thumb_'. $row->_loc_image.'" />' ;
				}
				break;
			case "JEVLOCATION_TITLE":
				if (isset($row->_jevlocation)){
					$field = "title";
					if (strlen($row->_jevlocation->$field)>0) return $row->_jevlocation->$field;
				}
				else if(is_string($row->location())){
					return $row->location();
				}
				// list version
				else if (isset($row->_loc_title))  return $row->_loc_title;
				break;
			case "JEVLOCATION_DESCRIPTION":
				if (isset($row->_jevlocation)){
					$field = "description";
					if (strlen($row->_jevlocation->$field)>0) return $row->_jevlocation->$field;
				}
				// list version
				else if (isset($row->_loc_desc))  return $row->_loc_desc;
				break;
			case "JEVLOCATION_STREET":
				if (isset($row->_jevlocation)){
					$field = "street";
					if (strlen($row->_jevlocation->$field)>0) return $row->_jevlocation->$field;
				}
				// list version
				else if (isset($row->_loc_street))  return $row->_loc_street;
				break;
			case "JEVLOCATION_CITY":
				if (isset($row->_jevlocation)){
					$field = "city";
					if (strlen($row->_jevlocation->$field)>0) return $row->_jevlocation->$field;
				}
				// list version
				else if (isset($row->_loc_city))  return $row->_loc_city;
				break;
			case "JEVLOCATION_STATE":
				if (isset($row->_jevlocation)){
					$field = "state";
					if (strlen($row->_jevlocation->$field)>0) return $row->_jevlocation->$field;
				}
				// list version
				else if (isset($row->_loc_state))  return $row->_loc_state;
				break;
			case "JEVLOCATION_COUNTRY":
				if (isset($row->_jevlocation)){
					$field = "country";
					if (strlen($row->_jevlocation->$field)>0) return $row->_jevlocation->$field;
				}
				// list version
				else if (isset($row->_loc_country))  return $row->_loc_country;
				break;
			case "JEVLOCATION_POSTCODE":
				if (isset($row->_jevlocation)){
					$field = "postcode";
					if (strlen($row->_jevlocation->$field)>0) return $row->_jevlocation->$field;
				}
				// list version
				else if (isset($row->_loc_postcode))  return $row->_loc_postcode;
				break;
			case "JEVLOCATION_PHONE":
				if (isset($row->_jevlocation)){
					$field = "phone";
					if (strlen($row->_jevlocation->$field)>0) return $row->_jevlocation->$field;
				}
				// list version
				else if (isset($row->_loc_phone))  return $row->_loc_phone;
				break;
			case "JEVLOCATION_MAP":
				if (isset($row->_jevlocation)){
					$field = "map";
					if (strlen($row->_jevlocation->$field)>0) return $row->_jevlocation->$field;
				}
				break;
			case "JEVLOCATION_URL":
				if (isset($row->_jevlocation)){
					$field = "url";
					if (strlen($row->_jevlocation->$field)>0) return $row->_jevlocation->$field;
				}
				// list version
				else if (isset($row->_loc_url))  return $row->_loc_url;
				break;
			case "JEVLOCATION_A":
				if (isset($row->_jevlocation)){
					$field = "linkstart";
					if (strlen($row->_jevlocation->$field)>0) return $row->_jevlocation->$field;
				}
				// list version
				else if (isset($row->_loc_id)) {

					$plugin =& JPluginHelper::getPlugin("jevents", "jevlocations");
					$params = new JParameter($plugin->params);
					$loc_id = $row->_loc_id;
					$detailpopup = $params->get("detailpopup",1);
					if ($detailpopup){
						JHTML::_('behavior.modal');
						$locurl = JRoute::_("index.php?option=com_jevlocations&task=locations.detail&tmpl=component&loc_id=$loc_id&title=".JFilterOutput::stringURLSafe($row->_loc_title));
						$pwidth = $params->get("pwidth","750");
						$pheight = $params->get("pheight","500");
						return 		"<a href='$locurl' class='modal' rel='{handler:\"iframe\",\"size\": {\"x\": $pwidth, \"y\": $pheight}}'>";
					}
					else {
						$locurl = JRoute::_("index.php?option=com_jevlocations&task=locations.detail&se=1&loc_id=$loc_id&title=".JFilterOutput::stringURLSafe($row->_loc_title));
						return 	"<a href='$locurl' >";
					}
				}
				break;
			case "JEVLOCATION_CLOSE_A":
				if (isset($row->_jevlocation)){
					$field = "linkstart";
					if (strlen($row->_jevlocation->$field)>0) return "</a>";
				}
				// list version
				else if (isset($row->_loc_id)) {
					return "</a>";
				}

				break;
		}
		return "";
	}
}
