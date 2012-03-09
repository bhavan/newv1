<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: jevtextarea.php 1569 2009-09-16 06:22:03Z geraint $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

class JElementJevcftextarea extends JElement
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'Jevcftextarea';
	
	function fetchElement($name, $value, &$node, $control_name)
	{

		$rows = $node->attributes('rows');
		$cols = $node->attributes('cols');
		$class = ( $node->attributes('class') ? 'class="'.$node->attributes('class').'"' : 'class="text_area"' );
		// convert <br /> tags so they are not visible when editing
		$value = str_replace('<br />', "\n", $value);

		return '<textarea name="'.$control_name.$name.'" cols="'.$cols.'" rows="'.$rows.'" '.$class.' id="'.$control_name.$name.'" >'.$value.'</textarea>';
	}
	
	function fetchRequiredScript($name, &$node, $control_name) 
	{
		return "JevrRequiredFields.fields.push({'name':'".$control_name.$name."', 'default' :'".$node->attributes('default') ."' ,'reqmsg':'".trim(JText::_($node->attributes('requiredmessage'),true))."'}); ";
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
			$this->filter_value =  JRequest::getVar($this->filterType.'_fv', $this->filterNullValue,"request", "string" );
		}
		else {
			$this->filter_value = $mainframe->getUserStateFromRequest( $this->filterType.'_fv_ses', $this->filterType.'_fv', $this->filterNullValue , "string");
		}
		
	}
		
	public function createJoinFilter(){
		if (trim($this->filter_value)==$this->filterNullValue) return "";
		return " #__jev_customfields AS $this->map ON det.evdet_id=$this->map.evdet_id";
	}
	
	public function createFilter(){
		if (trim($this->filter_value)==$this->filterNullValue) return "";
		$db = JFactory::getDBO();
		return "$this->map.name=".$db->Quote($this->filterType)." AND $this->map.value LIKE (".$db->Quote($this->filter_value."%").")";
	}
	
	public function setSearchKeywords( &$extrajoin ){
		if ($this->node->attributes("searchable")){
			$db = JFactory::getDBO();
			if (strpos($extrajoin, " #__jev_customfields AS $this->map ON det.evdet_id=$this->map.evdet_id")===false){
				$extrajoin .= "\nLEFT JOIN #__jev_customfields AS $this->map ON det.evdet_id=$this->map.evdet_id";
			}
			return "($this->map.name=".$db->Quote($this->filterType)." AND $this->map.value LIKE (".$db->Quote('###'."%")."))";
		}
	}
	
	function fetchFilterElement($name, $value, &$node, $control_name)
	{
		$size = ( $node->attributes('size') ? 'size="'.$node->attributes('size').'"' : '' );
		$class = ( $node->attributes('class') ? 'class="'.$node->attributes('class').'"' : 'class="text_area"' );
		/*
		* Required to avoid a cycle of encoding &
		* html_entity_decode was used in place of htmlspecialchars_decode because
		* htmlspecialchars_decode is not compatible with PHP 4
		*/
		$value = htmlspecialchars(html_entity_decode($value, ENT_QUOTES), ENT_QUOTES);

		return '<input type="text" name="'.$control_name.$name.'" id="'.$control_name.$name.'" value="'.$value.'" '.$class.' '.$size.' />';

	}
	

	public function createFilterHTML(){
		$filterList=array();
		$filterList["title"]="<label class='evdate_label' for='".$this->filterType."_fv'>".$this->filterLabel."</label>";
		$filterList["html"] =  $this->fetchFilterElement($this->filterType."_fv", $this->filter_value, $this->node, "");
		
		$script = "JeventsFilters.filters.push({id:'".$this->filterType."_fv',value:'".addslashes($this->filterNullValue)."'});";
		$document = JFactory::getDocument();
		$document->addScriptDeclaration($script);
		
		return $filterList;
	}
	
}