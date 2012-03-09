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

include_once(JPATH_SITE.'/libraries/joomla/html/parameter/element/list.php');

class JElementJevcfmultilist extends JElementList
{

	function fetchElement($name, $value, &$node, $control_name)
	{
		if ($value != ""){
			$value = explode(",",$value);
			JArrayHelper::toInteger($value);
		}
		else {
			$value = array();
		}
		$class = ( $node->attributes('class') ? 'class="'.$node->attributes('class').'"' : 'class="inputbox"' );
		$size =  ( $node->attributes('size') ? ' size="'.$node->attributes('size').'"' : '' );

		$multiple = ' multiple="multiple"';

		$options = array ();
		foreach ($node->children() as $option)
		{
			if ($option->attributes('archive')) continue;
			$val	= intval($option->attributes('value'));
			$text	= $option->data();
			$options[] = JHTML::_('select.option', $val, JText::_($text));
		}

		return JHTML::_('select.genericlist',  $options, ''.$control_name.$name."[]", $class.$size.$multiple, 'value', 'text', $value, $control_name.$name);
	}

	function fetchRequiredScript($name, &$node, $control_name)
	{
		return "JevrRequiredFields.fields.push({'name':'".$control_name.$name."', 'default' :'".$node->attributes('default') ."' ,'reqmsg':'".trim(JText::_($node->attributes('requiredmessage'),true))."'}); ";
	}

	public function convertValue($value, $node){
		if ($value =="") return;
		$value = explode(",",$value);
		JArrayHelper::toInteger($value);

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
		$output = array();
		foreach ($value as $v) {
			if (array_key_exists($v,$values[$node->attributes('name')])) $output[] = $values[$node->attributes('name')][$v];
		}

		return implode(", ",$output);
	}

	public function constructFilter($node){
		$this->node = $node;
		$this->filterType = str_replace(" ","",$this->node->attributes("name"));
		$this->filterLabel = $this->node->attributes("label");
		$this->filterNullValue = array($this->node->attributes("default"));
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
			$this->filter_value =  JRequest::getVar($this->filterType.'_fv', $this->filterNullValue,"request", "array" );
			JArrayHelper::toInteger($this->filter_value);
		}
		else {
			$this->filter_value = $mainframe->getUserStateFromRequest( $this->filterType.'_fv_ses', $this->filterType.'_fv', $this->filterNullValue );
			JArrayHelper::toInteger($this->filter_value);
		}
		
		/*	
		$this->filter_value = JRequest::getVar($this->filterType.'_fv', $this->filterNullValue ,"request", "array");
		JArrayHelper::toInteger($this->filter_value);
		*/
	}

	public function createJoinFilter(){
		if ($this->filter_value==$this->filterNullValue) return "";
		return " #__jev_customfields AS $this->map ON det.evdet_id=$this->map.evdet_id";
	}

	public function createFilter(){
		if ($this->filter_value==$this->filterNullValue) return "";
		if (count($this->filter_value)==0) return "";
		$db = JFactory::getDBO();
		$filter =  "$this->map.name=".$db->Quote($this->filterType). " AND ( ";
		$bits = array();
		foreach ($this->filter_value as $fv) {
			$bits[] = " $this->map.value RLIKE ".$db->Quote(",*".$fv.",*");
		}
		$filter .= implode(" OR ",$bits);
		$filter .= ")";
		return $filter;
	}

	public function createFilterHTML(){
		$filterList=array();
		$filterList["title"]="<label class='evdate_label' for='".$this->filterType."_fv'>".$this->filterLabel."</label>";
		$filterList["html"] =  $this->fetchElement($this->filterType."_fv", implode(",",$this->filter_value), $this->node, "");

		$script = "JeventsFilters.filters.push({id:'".$this->filterType."_fv',value:".$this->filterNullValue[0] ."});";
		$document = JFactory::getDocument();
		$document->addScriptDeclaration($script);

		return $filterList;
	}

}