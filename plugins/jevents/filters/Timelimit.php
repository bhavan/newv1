<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: Timelimit.php 1400 2009-03-30 08:45:17Z geraint $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined('_JEXEC') or die( 'No Direct Access' );

// Event repeat timelimit fitler
class jevTimelimitFilter extends jevFilter
{

	function __construct($tablename, $filterfield, $isstring=true){
		$this->fieldset=true;

		$this->filterType="timelimit";
		$this->dmap = "rpt";
		parent::__construct($tablename,$filterfield, true);
	}

	function _createFilter($prefix=""){
		if (!$this->filterField ) return "";

		$reg =& JFactory::getConfig();
		$modparams = $reg->getValue("jev.modparams",false);
		if ($modparams && $modparams->getValue("ignorefiltermodule",false)){
			return "";
		}
		
		// get plugin params
		$plugin =& JPluginHelper::getPlugin('jevents', 'jevtimelimit');
		if (!$plugin) return "";
		$params = new JParameter($plugin->params);

		if (intval($params->get("override",0)) && $this->filter_value==1){
			return "";
		}

		$past = intval($params->get("past",-1));
		$future = intval($params->get("future",-1));

		jimport('joomla.utilities.date');

		$filter = "";
		if ($past>=0 && $future>=0){
			$pastdate = new JDate("-$past days");
			$pastdate = $pastdate->toFormat("%Y-%m-%d 00:00:00");
			$futuredate = new JDate("$future days");
			$futuredate = $futuredate->toFormat("%Y-%m-%d 23:59:59");
			$filter = "(".$this->dmap.".endrepeat>='$pastdate' AND ".$this->dmap.".startrepeat<='$futuredate')";
		}
		else if ($past>=0){
			$pastdate = new JDate("-$past days");
			$pastdate = $pastdate->toFormat("%Y-%m-%d 00:00:00");
			$filter = $this->dmap.".endrepeat>='$pastdate'";
		}
		else if ($future>=0) {
			$futuredate = new JDate("$future days");
			$futuredate = $futuredate->toFormat("%Y-%m-%d 23:59:59");
			$filter = $this->dmap.".startrepeat<='$futuredate'";
		}

		return $filter;
	}

	function _createfilterHTML(){

		$filterList=array();
		$filterList["title"]="";
		$filterList["html"]="";

		// get plugin params
		$plugin =& JPluginHelper::getPlugin('jevents', 'jevtimelimit');
		if(!$plugin) {
			// Filter not active
			return $filterList;
		}
		$params = new JParameter($plugin->params);
		if (!intval($params->get("override",0))){

			// A hidden filter
			return $filterList;
		}
		else {

			$lang =& JFactory::getLanguage();
			$lang->load("plg_jevents_timelimit");

			$this->filterLabel=JText::_("Show Past Events?");
			$this->yesLabel = JText::_("Yes");
			$this->noLabel =  JText::_("No");

			$filterList=array();
			$filterList["title"]="<label class='evtimelimit_label' for='".$this->filterType."_fv'>".$this->filterLabel."</label>";

			$options = array();
			$options[] = JHTML::_('select.option', "0", $this->noLabel,"value","yesno");
			$options[] = JHTML::_('select.option',  "1", $this->yesLabel,"value","yesno");
			$filterList["html"] = JHTML::_('select.genericlist',$options, $this->filterType.'_fv', 'class="inputbox" size="1" onchange="form.submit();"', 'value', 'yesno', $this->filter_value );

		}
		return $filterList;


	}
}
