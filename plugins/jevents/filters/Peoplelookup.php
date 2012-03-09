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
class jevPeoplelookupFilter extends jevFilter
{
	function __construct($tablename, $filterfield, $isstring=true){
		$this->filterType="peoplelkup";
		$this->filterLabel=JText::_("Search By Person");
		$this->filterNullValue=0;
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
		if (intval($this->filter_value)==$this->filterNullValue) return "";

		$db = JFactory::getDBO();
		if (strpos( $this->filter_value,",")>0) {
			$parts = explode(",",$this->filter_value);
			JArrayHelper::toInteger($parts);
			$this->filter_value = implode(",",$parts);
			$this->needsgroupby = true;
			return " pers.pers_id IN ($this->filter_value)";
		}

		$value = intval( $this->filter_value);
		
		$filter = "pers.pers_id = $value";
		return $filter;
	}

	// No need join  the people is always joined
	// function _createJoinFilter($prefix=""){}

	function _createfilterHTML(){

		if (!$this->filterField) return "";

		// Find the accessible locations
		$user =& JFactory::getUser();
		$db = JFactory::getDBO();
				
		$query = "SELECT p.pers_id as value, p.title as text, t.title as ptype FROM #__jev_people as p ";
		$query .= "LEFT JOIN #__jev_peopletypes as t ON t.type_id=p.type_id ";
		$query .= "WHERE p.published=1 AND p.access <= ". (int) $user->aid. " ORDER BY t.title ASC, p.title ASC";
		$db->setQuery( $query );
		$people = $db->loadObjectList();

   		$list[] = JHTML::_( 'select.option', 0, JText::_("Search by Person"));
   		// count the types first
   		$types=array();
   		foreach ($people as $person) {
   			if (!in_array($person->ptype,$types)) $types[]=$person->ptype;
   		}
   		if (count($types)==1){
   			$list = array_merge($list, $people);
   		}
   		else {
   			$type = "";
   			$typecount = 1;
   			foreach ($people as $person){
   				if ($person->ptype!=$type){   					
   					$list[] = JHTML::_( 'select.option', -$typecount, $person->ptype,"value","text", true);
   					$type = $person->ptype;
   					$typecount++;
   				}
   				$list[] = JHTML::_( 'select.option', $person->value, "&nbsp;&nbsp;".$person->text);
   			}
   		}
  
		$filterList=array();
		$filterList["title"]="<label class='evppllkup_label' for='".$this->filterType."_fv'>".$this->filterLabel."</label>";
  		$filterList["html"] = JHTML::_( 'select.genericlist', $list, $this->filterType."_fv", "id='".$this->filterType."_fv' class='evppllkup'", 'value', 'text', $this->filter_value);

		$script = "JeventsFilters.filters.push({id:'".$this->filterType."_fv',value:0});";
		$document = JFactory::getDocument();
		$document->addScriptDeclaration($script);
		
		return $filterList;

	}
}