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

JLoader::register('JevCfParameter',JPATH_SITE."/plugins/jevents/customfields/jevcfparameter.php");

// searches location of event
class jevCustomfieldfilter extends jevFilter
{
	private $params;
	private $fieldparams;
	
	function __construct($tablename, $filterfield, $isstring=true){
		$plugin = JPluginHelper::getPlugin("jevents","jevcustomfields");
		if (!$plugin) return "";
		$this->params = new JParameter($plugin->params);
		
		$this->filterType = "customfield";
		$this->fieldparams = false;

		// Should these be ignored?
		$reg =& JFactory::getConfig();
		$modparams = $reg->getValue("jev.modparams",false);
		if ($modparams && $modparams->getValue("ignorefiltermodule",false)){
			return "";
		}
		
		
		$template = $this->params->get("template","");
		if ($template!=""){
			$xmlfile = JPATH_SITE."/plugins/jevents/customfields/templates/".$template;
			if (file_exists($xmlfile)){
				$this->fieldparams = new JevCfParameter(array(),$xmlfile,  null);	
			}
		}
		else {
			return;
		}		
		
		$this->fieldparams->constructFilters();
	}		

	function _createFilter($prefix=""){
		if (!$this->fieldparams) return "";
		return $this->fieldparams->createFilters();

	}

	function _createJoinFilter($prefix=""){
		if (!$this->fieldparams) return "";
		// Always do the join
		$this->needsgroupby = true;
		return $this->fieldparams->createJoinFilters();
	}

	public function setSearchKeywords(& $extrajoin ){
		if (!$this->fieldparams) return "";
		// Always do the join
		$this->needsgroupby = true;
		return $this->fieldparams->setSearchKeywords($extrajoin);		
	}

	function _createfilterHTML(){
		if (!$this->fieldparams) return array();
		return $this->fieldparams->createFiltersHTML();
	}
}