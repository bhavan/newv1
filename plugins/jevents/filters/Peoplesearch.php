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

// searches people of event
class jevPeoplesearchFilter extends jevFilter
{
	function __construct($tablename, $filterfield, $isstring=true){
		$this->filterType="peoplesearch";
		$this->filterLabel=JText::_("Search By People");
		$this->filterNullValue="";
		parent::__construct($tablename,$filterfield, true);
		// Should these be ignored?
		$reg =& JFactory::getConfig();
		$modparams = $reg->getValue("jev.modparams",false);
		if ($modparams && $modparams->getValue("ignorefiltermodule",false)){
			$this->filter_value = $this->filterNullValue;
		}
		
	}

	function _createFilter($prefix=""){
		if (!$this->filterField ) return "";

		// get plugin params
		$plugin =& JPluginHelper::getPlugin('jevents', 'jevpeople');
		if (!$plugin) return "";
		$params = new JParameter($plugin->params);

		if (trim($this->filter_value)==$this->filterNullValue || (!is_numeric($this->filter_value)  && strlen(trim($this->filter_value)))<3) return "";

		if (is_numeric($this->filter_value)){
			$filter = "pers.pers_id = ".intval($this->filter_value);
		}
		else {
			$db = JFactory::getDBO();
			$text = $db->Quote( '%'.$db->getEscaped( $this->filter_value, true ).'%', false );

			$filter = "pers.title LIKE $text";
		}
		return $filter;

		// This version would give us multiple rows so do as separate query instead
		$db = JFactory::getDBO();
		$db->setQuery("SELECT pers.pers_id FROM #__jev_people as pers WHERE pers.title LIKE $text");
		$people = $db->loadResultArray();
		$people[] = -1;
		$people[] = 2;

		$filter = "pers.pers_id IN (".implode(",",$people).")";

		return $filter;
	}

	function _createJoinFilter($prefix=""){

		// Always do the join
		$this->needsgroupby = true;
		return " #__jev_peopleeventsmap as persmap ON det.evdet_id=persmap.evdet_id LEFT JOIN #__jev_people as pers ON pers.pers_id=persmap.pers_id ";
	}

	function _createfilterHTML(){

		if (!$this->filterField) return "";

		$db = JFactory::getDBO();

		$filterList=array();
		$filterList["title"]="<label class='evpeplesearch_label' for='".$this->filterType."_fv'>".$this->filterLabel."</label>";
		$filterList["html"] = "<input type='text' name='".$this->filterType."_fv'  id='".$this->filterType."_fv'  class='evpeoplesearch'  value='".$this->filter_value."' />";

		$script = "JeventsFilters.filters.push({id:'".$this->filterType."_fv',value:''});";
		$document = JFactory::getDocument();
		$document->addScriptDeclaration($script);

		return $filterList;

	}

	public function setSearchKeywords(& $extrajoin ){
		$db = JFactory::getDBO();
		return array("pers.title LIKE (".$db->Quote("%".'###'."%").")");
	}

}