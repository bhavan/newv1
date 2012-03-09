<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: jevboolean.php 1569 2009-09-16 06:22:03Z geraint $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

include_once(JPATH_SITE.'/libraries/joomla/html/parameter/element/radio.php');

class JElementJevcfradio extends JElementRadio
{
	function fetchElement($name, $value, &$node, $control_name)
	{
		$class = ( $node->attributes('class') ? 'class="'.$node->attributes('class').'"' : '' );
		$options = array ();
		foreach ($node->children() as $option)
		{
			if ($option->attributes('archive')) continue;
			$val	= intval($option->attributes('value'));
			$text	= $option->data();
			$options[] = JHTML::_('select.option', $val, JText::_($text));
		}

		return JHTML::_('select.radiolist', $options, ''.$control_name.$name, $class, 'value', 'text', $value, $control_name.$name );
	}


	public function convertValue($value, $node){
		static $values;
		if (!isset($values)){
			$values =  array();
		}
		if (!isset($values[$node->attributes('name')])){
			$values[$node->attributes('name')]=array();
			foreach ($node->children() as $option)
			{
				$val	= $option->attributes('value');
				$text	= $option->data();
				$values[$node->attributes('name')][$val] = $text;
			}
		}
		if (array_key_exists($value,$values[$node->attributes('name')])){
			return $values[$node->attributes('name')][$value];
		}
		else {
			return "";
		}
	}



	public function constructFilter($node){
		$this->node = $node;
		$this->filterType = str_replace(" ","",$this->node->attributes("name"));
		$this->filterLabel = $this->node->attributes("label");
		$this->filterNullValue = $this->node->attributes("default");
		$this->filter_value = $this->filterNullValue;
		$this->map = "csf".$this->filterType;

		$registry	=& JRegistry::getInstance("jevents");
		$this->indexedvisiblefilters = $registry->getValue("indexedvisiblefilters",false);
		if ($this->indexedvisiblefilters === false) return;
		
		// This is our best guess as to whether this filter is visible on this page.
		$this->visible = in_array("customfield",$this->indexedvisiblefilters);
		
		// If using caching should disable session filtering if not logged in
		$cfg	 = & JEVConfig::getInstance();
		$useCache = intval($cfg->get('com_cache', 0));
		$user = &JFactory::getUser();
		global $mainframe;
		if (intval(JRequest::getVar('filter_reset',0))){
			$this->filter_value = $mainframe->setUserState( $this->filterType.'_fv_ses', $this->filterNullValue );
		}
		// if not logged in and using cache then do not use session data
		// ALSO if this filter is not visible on the page then should not use filter value - does this supersede the previous condition ???
		else if ( ($user->get('id')==0 && $useCache) || !$this->visible) {
			$this->filter_value =  JRequest::getVar($this->filterType.'_fv', $this->filterNullValue,"request", "int" );
		}
		else {
			$this->filter_value = $mainframe->getUserStateFromRequest( $this->filterType.'_fv_ses', $this->filterType.'_fv', $this->filterNullValue );
		}
		$this->filter_value = intval($this->filter_value );
		
		//$this->filter_value = JRequest::getInt($this->filterType.'_fv', $this->filterNullValue );
}

	public function createJoinFilter(){
		if (trim($this->filter_value)==$this->filterNullValue) return "";
		return " #__jev_customfields AS $this->map ON det.evdet_id=$this->map.evdet_id";
	}

	public function createFilter(){
		if (trim($this->filter_value)==$this->filterNullValue) return "";
		$db = JFactory::getDBO();
		return "$this->map.name=".$db->Quote($this->filterType)." AND $this->map.value=".$db->Quote($this->filter_value);
	}

	public function createFilterHTML(){
		$filterList=array();
		$filterList["title"]="<label class='evdate_label' for='".$this->filterType."_fv'>".$this->filterLabel."</label>";
		$name = $this->filterType."_fv";
		$filterList["html"] =  $this->fetchElement($name, $this->filter_value, $this->node, "");

		$name .= $this->filterNullValue;
		$script = "function reset".$this->filterType."_fv(){\$('$name').checked=true;};\n";
		$script .= "JeventsFilters.filters.push({action:'reset".$this->filterType."_fv()',id:'".$this->filterType."_fv',value:".$this->filterNullValue."});\n";
		$document = JFactory::getDocument();
		$document->addScriptDeclaration($script);

		return $filterList;
	}


}