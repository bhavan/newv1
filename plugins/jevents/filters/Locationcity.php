<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: Search.php 1410 2009-04-09 08:13:54Z geraint $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined('_JEXEC') or die( 'No Direct Access' );

// searches location of event
class jevLocationcityFilter extends jevFilter
{
	function __construct($tablename, $filterfield, $isstring=true){
		$this->filterType="loccity";
		$this->filterLabel=JText::_("JEV CITY SEARCH");
		$this->filterNullValue='0';
		parent::__construct($tablename,$filterfield, true);

		// Should these be ignored?
		$reg =& JFactory::getConfig();
		$modparams = $reg->getValue("jev.modparams",false);
		if ($modparams && $modparams->getValue("ignorefiltermodule",false)){
			$this->filter_value = $this->filterNullValue;
			return;
		}

		// Only have memory on page with the module visible for JEvents 1.5.4 onwards
		JLoader::register('JEventsVersion',JEV_ADMINPATH."/libraries/version.php");
		$version	= & JEventsVersion::getInstance();
		$versionnumber = $version->RELEASE.".".$version->DEV_LEVEL.".".$version->PATCH_LEVEL;

		if (version_compare($versionnumber,"1.5.4","<")){
			$this->filter_value =  JRequest::getVar($this->filterType.'_fv', $this->filterNullValue );
		}

	}

	function _createFilter($prefix=""){
		if (!$this->filterField ) return "";
		if ($this->filter_value===$this->filterNullValue) return "";

		$db = JFactory::getDBO();

		$compparams = JComponentHelper::getParams("com_jevlocations");
		$usecats = $compparams->get("usecats",0);
		if ($usecats){
			$filter = "loc.catid=".intval( $this->filter_value);
		}
		else {
			$text = $db->Quote( $db->getEscaped( $this->filter_value, true ), false );
			$filter = "loc.city = $text";
		}

		return $filter;
	}

	function _createfilterHTML(){

		if (!$this->filterField) return "";

		// Find the accessible locations
		$user =& JFactory::getUser();
		$db = JFactory::getDBO();
		$compparams = JComponentHelper::getParams("com_jevlocations");
		$usecats = $compparams->get("usecats",0);
		
		// is the state filter active and non-null in which case filter the list of cities
		$pluginsDir = JPATH_ROOT.DS.'plugins'.DS.'jevents';
		$filters = jevFilterProcessing::getInstance(array("locationstate"),$pluginsDir.DS."filters".DS);
		$citywhere = $cityjoin = array();
		$filters->setWhereJoin($citywhere,$cityjoin);
		$where = "";
		if (count($citywhere)==1){
			if (!$usecats && strpos($citywhere[0],"loc.state")!==false){
				$where = " AND ".str_replace("loc.state","state",$citywhere[0]);
			}
			else if ($usecats && strpos($citywhere[0],"locstate.id")!==false){
				$where = " AND ".str_replace("locstate.id","pcat.id",$citywhere[0]);
			}
		}
		
		if ($usecats){
			$query = "SELECT distinct cat.id as value, cat.title as text FROM #__categories as cat 
			LEFT JOIN  #__categories as pcat ON pcat.id=cat.parent_id 
			LEFT JOIN  #__categories as gpcat ON gpcat.id=pcat.parent_id 
			WHERE cat.published=1 AND cat.section='com_jevlocations' 
			AND pcat.id is not null
			AND gpcat.id is not null
			$where
			ORDER BY cat.title ASC";
			$db->setQuery( $query );
			$locations = $db->loadObjectList();
		}
		else {
			$query = "SELECT DISTINCT city as value, city as text FROM #__jev_locations WHERE published=1 $where AND city<>'' ORDER BY city ASC";
			$db->setQuery( $query );
			$locations = $db->loadObjectList();			
		}
		$list[] = JHTML::_( 'select.option', 0, JText::_("JEV CITY SEARCH"));
		$list = array_merge($list, $locations);

		$filterList=array();
		$filterList["title"]="<label class='evloccat_label' for='".$this->filterType."_fv'>".$this->filterLabel."</label>";
		$filterList["html"] = JHTML::_( 'select.genericlist', $list, $this->filterType."_fv", "id='".$this->filterType."_fv' class='evloccat_label'", 'value', 'text', $this->filter_value);

		$script = "JeventsFilters.filters.push({id:'".$this->filterType."_fv',value:0});";
		$document = JFactory::getDocument();
		$document->addScriptDeclaration($script);

		return $filterList;

	}
}