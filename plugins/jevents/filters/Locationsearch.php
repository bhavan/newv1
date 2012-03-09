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
class jevLocationsearchFilter extends jevFilter
{
	function __construct($tablename, $filterfield, $isstring=true){
		$this->filterType="locsearch";
		$this->filterLabel=JText::_("Search By Location");
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
		if (trim($this->filter_value)==$this->filterNullValue || strlen(trim($this->filter_value))<3) return "";

		$db = JFactory::getDBO();
		$text = $db->Quote( '%'.$db->getEscaped( $this->filter_value, true ).'%', false );

		//$filter = "(det.summary LIKE $text OR det.description LIKE $text OR det.extra_info LIKE $text)";

		$compparams = JComponentHelper::getParams("com_jevlocations");
		$usecats = $compparams->get("usecats",0);
		if ($usecats){
			$filter = "(loc.title LIKE $text)";
		}
		else {
			$filter = "(loc.title LIKE $text OR loc.city LIKE $text OR loc.state LIKE $text OR loc.country LIKE $text OR loc.postcode LIKE $text)";
		}
		return $filter;
	}

	function _createJoinFilter($prefix=""){
		$compparams = JComponentHelper::getParams("com_jevlocations");
		$usecats = $compparams->get("usecats",0);
		//if (!$usecats){
			// always need the join!!
			//if (!$this->filterField ) return "";
			//if ($this->filter_value==$this->filterNullValue) return "";
			return " #__jev_locations as loc ON loc.loc_id=det.location";

		//}
		return "";
	}

	function _createfilterHTML(){

		if (!$this->filterField) return "";

		$db = JFactory::getDBO();

		$filterList=array();
		$filterList["title"]="<label class='evlocsearch_label' for='".$this->filterType."_fv'>".$this->filterLabel."</label>";
		$filterList["html"] = "<input type='text' name='".$this->filterType."_fv' id='".$this->filterType."_fv' class='evlocsearch'  value='".$this->filter_value."' />";

		$script = "JeventsFilters.filters.push({id:'".$this->filterType."_fv',value:''});";
		$document = JFactory::getDocument();
		$document->addScriptDeclaration($script);
		
		return $filterList;

	}

	public function setSearchKeywords(& $extrajoin ){
		$db = JFactory::getDBO();
		return array("loc.title LIKE (".$db->Quote("%".'###'."%").")");
	}

}