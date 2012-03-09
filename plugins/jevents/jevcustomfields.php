<?php

/**
 * copyright (C) 2009 GWE Systems Ltd - All rights reserved
 */
// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.plugin.plugin');

class plgJEventsJevcustomfields extends JPlugin
{

	function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);

		JPlugin::loadLanguage('plg_jevents_jevcustomfields', JPATH_ADMINISTRATOR);

		JLoader::register('JevCfParameter', dirname(__FILE__) . "/customfields/jevcfparameter.php");

		include_once(JPATH_SITE."/components/com_jevents/jevents.defines.php");
	}

	/**
	 * When editing a JEvents menu item can add additional menu constraints dynamically
	 *
	 */
	function onEditMenuItem(&$menudata, $value, $control_name, $name, $id, $param)
	{

		// already done this param
		if (isset($menudata[$id]))
			return;

		$html = "";

		// New parameterised fields - only filter items with attribute filtermenusandmodules = "1"
		$hasparams = false;
		$template = $this->params->get("template", "");
		if ($template != "")
		{
			$xmlfile = dirname(__FILE__) . "/customfields/templates/" . $template;
			if (file_exists($xmlfile))
			{
				$offerfilter = false;

				include_once(JPATH_ADMINISTRATOR . "/components/com_jevents/jevents.defines.php");
				$params = new JevCfParameter(array(), $xmlfile, null);
				$nodes = array();
				foreach ($params->_xml["_default"]->children() as $node)
				{
					if ($node->attributes('filtermenusandmodules'))
					{
						$offerfilter = true;
						$nodes[] = $node;
					}
				}
				if (!$offerfilter)
					return;
				$paramsarray = $params->renderToBasicArray();

				static $matchingextra = null;
				// find the parameter that matches jevcf: (if any)
				if (!isset($matchingextra))
				{
					$keyvals = $param->toArray();
					foreach ($keyvals as $key => $val)
					{
						if (strpos($key, "extras") === 0 && strpos($val, "jevcf:") === 0)
						{
							$matchingextra = str_replace("extras", "", $key);
							break;
						}
					}
					if (!isset($matchingextra))
					{
						$matchingextra = false;
					}
				}

				// either we found matching extra and this is the correct id or we didn't find matching extra and the value is blank
				if (($matchingextra == $id && strpos($value, "jevcf:") === 0) || (($value == "" || $value == "0") && $matchingextra === false))
				{

					$matchingextra = $id;

					$invalue = str_replace("jevcf:", "", $value);
					if ($invalue != "")
					{
						// assumes the data was stored in json encoded format
						$invalue = json_decode(htmlspecialchars_decode($invalue));
						if (!is_array($invalue)){
							$invalue = array();
						}
					}
					else
					{
						$invalue = array();
					}
					$values = array();
					foreach ($invalue as $inv)
					{
						$values[$inv->id] = $inv->val;
					}

					$script = <<<SCRIPT
var JevrCustomFields = {
	fields: new Array(),
	convert:function (){
		var values = new Array();
		//alert('convert '+JevrCustomFields.fields);
		JevrCustomFields.fields.each(function(el){
			var elem = $(el);
			if (elem){
				var id = elem.id;
				var val = elem.value;
				values.push({'id':id, 'val':val});
			}
			else {
				// else could be a radio box!
					\$ES('input[name='+el+']').each(function(elem){
						if (elem.checked) {
							var id = elem.name;
							var val = elem.value;
							values.push({'id':id, 'val':val});
						}
					});
			}
		});
		$('paramsextras$id').value = "jevcf:"+Json.toString(values);
	}
};
SCRIPT;
					$script .= "window.addEvent('load',function(){";
					$html = "<table id='frogswerehere'>";
					foreach ($nodes as $node)
					{
						$type = $node->attributes("type");
						$type = str_replace("jevr", "jevcf", $type);
						$elem = $params->loadElement($type);

						$label = $node->attributes("label");

						$elemname = $name . $node->attributes("name");
						if (array_key_exists("cfparams$elemname", $values))
						{
							$val = $values["cfparams$elemname"];
						}
						else
						{
							$val = $node->attributes('default');
						}
						$formelement = $elem->fetchElement($elemname, $val, $node, "cfparams");
						$html .= "<tr><td>$label</td><td>$formelement</td></tr>";
						$script .= "JevrCustomFields.fields.push('cfparams$elemname');\n";
						//$script .= "$('cfparams$elemname').addEvent('change',function(item){JevrCustomFields.convert();});\n";
						//$script .= "$('cfparams$elemname').addEvent('click',function(item){JevrCustomFields.convert();});\n";
						$script .= "$('frogswerehere').addEvent('mouseout',function(item){JevrCustomFields.convert();});\n";
					}
					$html .= "</table>";
					$script .= "});";

					$document = JFactory::getDocument();
					$document->addScriptDeclaration($script);

					$html .= "<textarea  name='params[extras$id]' id='paramsextras$id' style='display:none'>$value</textarea>";

					$data = new stdClass();
					$data->name = "jevcf";
					// This is where the form data goes
					// Note that we will need to convert the muliple field data inputs into a single field value - probably using json encoding in mootools Json.toString(...)
					$data->html = $html;
					$data->label = "JEV CUSTOM FIELD FILTER";
					$data->description = "JEV SPECIFY CUSTOM FIELD VALUES";
					$data->options = array();
					$menudata[$id] = $data;
				}
			}
		}
		return;

	}

	function onEditCustom(&$row, &$customfields)
	{
		$html = "";

		// New parameterised fields
		$hasparams = false;
		$template = $this->params->get("template", "");
		if ($template != "")
		{
			$xmlfile = dirname(__FILE__) . "/customfields/templates/" . $template;
			if (file_exists($xmlfile))
			{
				if ($row->evdet_id())
				{
					$db = JFactory::getDBO();
					$db->setQuery("SELECT * FROM #__jev_customfields WHERE evdet_id=" . intval($row->evdet_id()));

					$params = new JevCfParameter($db->loadObjectList(), $xmlfile, $row);
				}
				else
				{
					$params = new JevCfParameter(array(), $xmlfile, $row);
				}
				JHTML::_('behavior.tooltip');
				if ($params->getNumParams() > 0)
				{

					$params->render('custom_', '_default', $customfields);
				}
			}
		}

	}

	/**
	 * Clean out custom fields for event details not matching global event detail
	 *
	 * @param unknown_type $idlist
	 */
	function onCleanCustomDetails($idlist)
	{
		// TODO
		return true;

	}

	/**
	 * Store custom fields
	 *
	 * @param iCalEventDetail $evdetail
	 */
	function onStoreCustomDetails($evdetail)
	{
		// New parameterised fields
		$hasparams = false;
		$template = $this->params->get("template", "");
		if ($template != "")
		{
			$xmlfile = dirname(__FILE__) . "/customfields/templates/" . $template;
			if (file_exists($xmlfile))
			{

				$eventid = $evdetail->evdet_id;

				$sql = "SELECT * FROM #__jev_customfields WHERE evdet_id=" . intval($eventid);
				$db = JFactory::getDBO();
				$db->setQuery($sql);
				$customdata = $db->loadObjectList();

				$params = new JevCfParameter($customdata, $xmlfile, null);
				$params = $params->renderToBasicArray();

				$user = JFactory::getUser();
				foreach ($params as $param)
				{
					if (!empty($param["userid"]) && $param["userid"] != $user->id)
					{
						foreach ($customdata as $cd)
						{
							if ($cd->name == $param["name"])
							{
								$evdetail->_customFields[$param["name"]] = $cd->value;
							}
						}
					}
				}

				// clean out the defunct data but leave private data intact!!
				$sql = "DELETE FROM #__jev_customfields WHERE evdet_id=" . intval($eventid);
				$db->setQuery($sql);
				$success = $db->query();

				foreach ($params as $param)
				{
					if (!array_key_exists($param["name"], $evdetail->_customFields))
						continue;
					if (!is_array($evdetail->_customFields[$param["name"]]))
					{
						$customfield = JFilterInput::clean($evdetail->_customFields[$param["name"]]);
					}
					else
					{
						$customfield = implode(",", $evdetail->_customFields[$param["name"]]);
					}
					/*
					  // the delete takes care of this
					  if ($customdata && $customdata->id>0){

					  $sql = "UPDATE #__jev_customfields SET value=".$db->Quote($customfield)
					  .", evdet_id=".intval($eventid)
					  .", name=".$db->Quote($param["name"])
					  ." WHERE id=".intval($customdata->id)
					  ;
					  }
					  else {
					 */
					$sql = "INSERT INTO  #__jev_customfields (value, evdet_id, name ) VALUES(" . $db->Quote($customfield) . ", " . intval($eventid) . ", " . $db->Quote($param["name"]) . ")";
					//}
					$db->setQuery($sql);
					$success = $db->query();
				}
			}
		}

		return true;

	}

	/**
	 * Store custom fields
	 *
	 * @param iCalEventDetail $evdetail
	 */
	// TODO update reminder timestamps when event times have changed
	function onStoreCustomEvent($event)
	{
		return true;

	}

	/**
	 * Clean out custom details for deleted event details
	 *
	 * @param comma separated list of event detail ids $idlist
	 */
	function onDeleteEventDetails($idlist)
	{
		// you delete unwanted custom data here - housekeeping
		$db = JFactory::getDBO();
		$ids = explode(",", $idlist);
		JArrayHelper::toInteger($ids);
		$idlist = implode(",", $ids);
		$sql = "DELETE FROM #__jev_customfields WHERE ev_id IN (" . $idlist . ")";
		$db->setQuery($sql);
		$db->query();
		return true;

	}

	function onDeleteCustomEvent($idlist)
	{
		// TODO remove any records
		return true;

	}

	function onListIcalEvents(& $extrafields, & $extratables, & $extrawhere, & $extrajoin, & $needsgroupdby=false)
	{
		static $usefilter;

		if (!isset($usefilter))
		{
			global $mainframe;
			if ($mainframe->isAdmin())
			{
				$usefilter = false;
				return;
			}


			// Have we specified specific people for the menu item
			$compparams = JComponentHelper::getParams("com_jevents");

			// If loading from a module then get the modules params from the registry
			$reg = & JFactory::getConfig();
			$modparams = $reg->getValue("jev.modparams", false);
			if ($modparams)
			{
				$compparams = $modparams;
			}

			for ($extra = 0; $extra < 20; $extra++)
			{
				$extraval = $compparams->get("extras" . $extra, false);
				if (strpos($extraval, "jevcf:") === 0)
				{
					break;
				}
			}
			// if we have a conditional vauye then apply filter
			if ($extraval)
			{
				$invalue = str_replace("jevcf:", "", $extraval);
				if ($invalue != "")
				{
					// assumes the data was stored in json encoded format
					$invalue = json_decode(htmlspecialchars_decode($invalue));
				}
				else
				{
					$invalue = array();
				}
				foreach ($invalue as $inv)
				{
					JRequest::setVar(str_replace("cfparamsextras$extra", "",$inv->id)."_fv" , $inv->val);
				}
			}

			// find what is running - used by the filters
			$registry = & JRegistry::getInstance("jevents");
			$activeprocess = $registry->getValue("jevents.activeprocess", "");
			$moduleid = $registry->getValue("jevents.moduleid", 0);
			$moduleparams = $registry->getValue("jevents.moduleparams", false);

			$filters = jevFilterProcessing::getInstance(array("Customfield"), dirname(__FILE__) . DS . "filters" . DS, false, $moduleid);

			$filters->setWhereJoin($extrawhere, $extrajoin);
		}

		return true;

	}

	function onSearchEvents(& $extrasearchfields, & $extrajoin, & $needsgroupdby=false)
	{
		static $usefilter;

		if (!isset($usefilter))
		{
			global $mainframe;
			if ($mainframe->isAdmin())
			{
				$usefilter = false;
				return;
			}

			$extrawhere = array();
			$filters = jevFilterProcessing::getInstance(array("Customfield"), dirname(__FILE__) . DS . "filters" . DS, false, "customfields");
			$filters->setSearchKeywords($extrasearchfields, $extrajoin);
		}

		return true;

	}

	function onListEventsById(& $extrafields, & $extratables, & $extrawhere, & $extrajoin)
	{
		static $usefilter;

		if (!isset($usefilter))
		{
			global $mainframe;
			if ($mainframe->isAdmin())
			{
				$usefilter = false;
				return;
			}
			
			// Have we specified specific people for the menu item
			$compparams = JComponentHelper::getParams("com_jevents");

			// If loading from a module then get the modules params from the registry
			$reg = & JFactory::getConfig();
			$modparams = $reg->getValue("jev.modparams", false);
			if ($modparams)
			{
				$compparams = $modparams;
			}

			for ($extra = 0; $extra < 20; $extra++)
			{
				$extraval = $compparams->get("extras" . $extra, false);
				if (strpos($extraval, "jevcf:") === 0)
				{
					break;
				}
			}
			// if we have a conditional vauye then apply filter
			if ($extraval)
			{
				$invalue = str_replace("jevcf:", "", $extraval);
				if ($invalue != "")
				{
					// assumes the data was stored in json encoded format
					$invalue = json_decode(htmlspecialchars_decode($invalue));
				}
				else
				{
					$invalue = array();
				}
				foreach ($invalue as $inv)
				{
					JRequest::setVar(str_replace("cfparamsextras$extra", "",$inv->id)."_fv" , $inv->val);
				}
			}

			// find what is running - used by the filters
			$registry = & JRegistry::getInstance("jevents");
			$activeprocess = $registry->getValue("jevents.activeprocess", "");
			$moduleid = $registry->getValue("jevents.moduleid", 0);
			$moduleparams = $registry->getValue("jevents.moduleparams", false);

			$filters = jevFilterProcessing::getInstance(array("Customfield"), dirname(__FILE__) . DS . "filters" . DS, false, $moduleid);

			$filters->setWhereJoin($extrawhere, $extrajoin);
		}

		return true;

	}

	function onDisplayCustomFields(&$row)
	{
		// New parameterised fields
		$hasparams = false;
		$template = $this->params->get("template", "");
		if ($template != "")
		{
			$xmlfile = dirname(__FILE__) . "/customfields/templates/" . $template;
			if (file_exists($xmlfile))
			{

				$sql = "SELECT * FROM #__jev_customfields WHERE evdet_id=" . intval($row->evdet_id());
				$db = JFactory::getDBO();
				$db->setQuery($sql);
				$customdata = $db->loadObjectList();

				$params = new JevCfParameter($customdata, $xmlfile, $row);
				$customfields = $params->renderToBasicArray();
			}
		}
		else
		{
			return "";
		}
		$templatetop = $this->params->get("templatetop", "<table border='0'>");
		$templaterow = $this->params->get("templatebody", "<tr><td class='label'>{LABEL}</td><td>{VALUE}</td></tr>");
		$templatebottom = $this->params->get("templatebottom", "</table>");

		$row->customfields = $customfields;
		if (!$this->params->get("outputhtml", 1))
			return "";
		$html = $templatetop;
		$user = JFactory::getUser();
		foreach ($customfields as $customfield)
		{
			if ($user->aid < intval($customfield["access"]))
				continue;
			if (!is_null($customfield["hiddenvalue"]) && trim($customfield["value"]) == $customfield["hiddenvalue"])
				continue;
			$outrow = str_replace("{LABEL}", $customfield["label"], $templaterow);
			$outrow = str_replace("{VALUE}", nl2br($customfield["value"]), $outrow);
			$html .= $outrow;
		}
		$html .= $templatebottom;

		$row->customfieldsummary = $html;
		return $html;

	}

	function onDisplayCustomFieldsMultiRow(&$rows)
	{

		if (!$this->params->get("inlists", 0))
			return;

		if (count($rows) == 0)
			return;

		$ids = array();
		foreach ($rows as $row)
		{
			$ids[] = $row->evdet_id();
		}

		$templatetop = $this->params->get("templatetop", "<table border='0'>");
		$templaterow = $this->params->get("templatebody", "<tr><td class='label'>{LABEL}</td><td>{VALUE}</td>");
		$templatebottom = $this->params->get("templatebottom", "</table>");

		// New parameterised fields
		$customdata = array();
		$hasparams = false;
		$template = $this->params->get("template", "");
		if ($template != "")
		{
			$xmlfile = dirname(__FILE__) . "/customfields/templates/" . $template;
			if (file_exists($xmlfile))
			{

				$sql = "SELECT * FROM #__jev_customfields WHERE evdet_id IN (" . implode(",", $ids) . ") ORDER BY evdet_id";
				$db = JFactory::getDBO();
				$db->setQuery($sql);
				$customdata = $db->loadAssocList();
				if (is_null($customdata )) {
					return;
				}
			}
			else
			{
				return;
			}
		}
		else
		{
			return;
		}

		$user = JFactory::getUser();

		foreach ($rows as &$row)
		{
			$tempdata = array();
			foreach ($customdata as $data)
			{
				if ($data["evdet_id"] == $row->evdet_id())
				{
					$tempdata[] = $data;
				}
			}
			$params = new JevCfParameter($tempdata, $xmlfile, $row);
			$customfields = $params->renderToBasicArray();

			$row->customfields = $customfields;
			if (!$this->params->get("outputhtml", 1))
				return "";
			$html = $templatetop;

			foreach ($customfields as $customfield)
			{
				if ($user->aid < intval($customfield["access"]))
					continue;
				$outrow = str_replace("{LABEL}", $customfield["label"], $templaterow);
				$outrow = str_replace("{VALUE}", nl2br($customfield["value"]), $outrow);
				$html .= $outrow;
			}
			$html .= $templatebottom;

			$row->customfieldsummary = $html;
			unset($row);
		}

		return;

	}

	static function fieldNameArray($layout='detail')
	{

		// only offer in detail view
		//if ($layout != "detail") return array();

		$return = array();
		$return['group'] = JText::_("JEV CUSTOM FIELDS", true);

		$labels = array();
		$labels[] = JText::_("JEV CUSTOM FIELD SUMMARY", true);
		$values = array();
		$values[] = "JEV_CUSTOM_SUMMARY";

		$plugin = JPluginHelper::getPlugin("jevents", "jevcustomfields");
		$pluginparams = new JParameter($plugin->params);
		$template = $pluginparams->get("template", "");
		if ($template != "")
		{
			$xmlfile = dirname(__FILE__) . "/customfields/templates/" . $template;
			if (file_exists($xmlfile))
			{

				$params = new JevCfParameter(array(), $xmlfile, null);

				$customfields = $params->renderToBasicArray();

				if (count($customfields) > 0)
				{
					foreach ($customfields as $customfield)
					{
						$labels[] = $customfield["label"];
						$values[] = $customfield["name"];
					}
				}
			}
		}
		$return['values'] = $values;
		$return['labels'] = $labels;

		return $return;

	}

	static function substitutefield($row, $code)
	{
		if ($code == "JEV_CUSTOM_SUMMARY")
		{
			if (isset($row->customfieldsummary))
				return $row->customfieldsummary;
		}
		else if (isset($row->customfields) && array_key_exists($code, $row->customfields))
		{
			$user = JFactory::getUser();
			$customfield = $row->customfields[$code];
			if ($user->aid < intval($customfield["access"]))
				return "";

			return nl2br($row->customfields[$code]["value"]);
		}
		return "";

	}

}
