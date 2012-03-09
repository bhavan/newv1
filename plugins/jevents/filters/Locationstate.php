<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: Locationstate.php 1191 2010-09-27 07:44:36Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined('_JEXEC') or die( 'No Direct Access' );

// searches location of event
class jevLocationstateFilter extends jevFilter
{
	function __construct($tablename, $filterfield, $isstring=true){
		$this->filterType="locstate";
		$this->filterLabel=JText::_("JEV STATE SEARCH");
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
			$filter = "locstate.id=".intval( $this->filter_value);
		}
		else {
			$text = $db->Quote( $db->getEscaped( $this->filter_value, true ), false );
			$filter = "loc.state = $text";
		}

		return $filter;
	}

	function _createJoinFilter($prefix=""){
		if (!$this->filterField ) return "";
		if ($this->filter_value===$this->filterNullValue) return "";
		$compparams = JComponentHelper::getParams("com_jevlocations");
		$usecats = $compparams->get("usecats",0);
		if ($usecats){
			return ' #__categories AS loccity ON loc.catid = loccity.id AND loccity.section="com_jevlocations" LEFT JOIN #__categories AS locstate ON loccity.parent_id = locstate.id AND locstate.section="com_jevlocations"';
		}
	}
	
	function _createfilterHTML(){

		if (!$this->filterField) return "";

		// Find the accessible locations
		$user =& JFactory::getUser();
		$db = JFactory::getDBO();

		$compparams = JComponentHelper::getParams("com_jevlocations");
		$usecats = $compparams->get("usecats",0);
		if ($usecats){
			$query = "SELECT distinct pcat.id as value, pcat.title as text FROM #__categories as cat 
			LEFT JOIN  #__categories as pcat ON pcat.id=cat.parent_id 
			LEFT JOIN  #__categories as gpcat ON gpcat.id=pcat.parent_id 
			WHERE cat.published=1 AND cat.section='com_jevlocations' 
			AND pcat.id is not null
			AND gpcat.id is not null
			ORDER BY cat.title ASC";
			$db->setQuery( $query );
			$locations = $db->loadObjectList();
		}
		else {
			$query = "SELECT DISTINCT state as value, state as text FROM #__jev_locations WHERE published=1 AND state<>'' ORDER BY state ASC";
			$db->setQuery( $query );
			$locations = $db->loadObjectList();			
		}
		$list[] = JHTML::_( 'select.option', 0, JText::_("JEV STATE SEARCH"));
		$list = array_merge($list, $locations);

		$filterList=array();
		$filterList["title"]="<label class='evlocstate_label' for='".$this->filterType."_fv'>".$this->filterLabel."</label>";
//		$filterList["html"] = JHTML::_( 'select.genericlist', $list, $this->filterType."_fv", "id='".$this->filterType."_fv' class='evlocstate_label' onchange='this.form.submit()'", 'value', 'text', $this->filter_value);
		$filterList["html"] = JHTML::_( 'select.genericlist', $list, $this->filterType."_fv", "id='".$this->filterType."_fv' class='evlocstate_label'", 'value', 'text', $this->filter_value);

		$script = "JeventsFilters.filters.push({id:'".$this->filterType."_fv',value:0});";
		$document = JFactory::getDocument();
		$document->addScriptDeclaration($script);

		return $filterList;

	}
}