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
class jevLocationlookupFilter extends jevFilter
{
	function __construct($tablename, $filterfield, $isstring=true){
		$this->filterType="loclkup";
		$this->filterLabel=JText::_("Search By Location");
		$this->filterNullValue=0;
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
		if (intval($this->filter_value)==$this->filterNullValue) return "";

		$db = JFactory::getDBO();
		$value = intval( $this->filter_value);

		$filter = "loc.loc_id = $value";
		return $filter;
	}

	// No need join  the location is always joined
	// function _createJoinFilter($prefix=""){}

	function _createfilterHTML(){

		if (!$this->filterField) return "";

		// Find the accessible locations
		$user =& JFactory::getUser();
		$db = JFactory::getDBO();

		// Do these need to be filtered by location category filter
		static $catid;
		if (!isset($catid)){
			include_once(JPATH_SITE."/plugins/jevents/filters/Locationcategory.php");
			$catfilter = new jevLocationcategoryFilter("","Locationcategory");
			$catid = intval($catfilter->filter_value);
			if ($catid>0){
				$catid = " AND loccat=$catid";
			}
			else {
				$catid = "";
			}
		}
		
		$query = "SELECT loc_id as value, title as text FROM #__jev_locations WHERE published=1 $catid AND access <= ". (int) $user->aid. " ORDER BY title ASC";
		$db->setQuery( $query );
		$locations = $db->loadObjectList();

		$list[] = JHTML::_( 'select.option', 0, JText::_("Search by location"));
		$list = array_merge($list, $locations);

		$filterList=array();
		$filterList["title"]="<label class='evloclkup_label' for='".$this->filterType."_fv'>".$this->filterLabel."</label>";
		$filterList["html"] = JHTML::_( 'select.genericlist', $list, $this->filterType."_fv", "id='".$this->filterType."_fv' class='evloclkup'", 'value', 'text', $this->filter_value);

		$script = "JeventsFilters.filters.push({id:'".$this->filterType."_fv',value:0});";
		$document = JFactory::getDocument();
		$document->addScriptDeclaration($script);

		return $filterList;

	}
}