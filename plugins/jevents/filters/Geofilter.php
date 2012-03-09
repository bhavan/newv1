<?php
/**
* JEvents Component for Joomla 1.5.x
*
* @version $Id: Geofilter.php 1402 2010-11-08 11:20:55Z geraintedwards $
* @package JEvents
* @copyright Copyright (C) 2008-2009 GWE Systems Ltd
* @license GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
* @link http://www.jevents.net
*/

defined('_JEXEC') or die( 'No Direct Access' );

// searches location of event
class jevGeofilterFilter extends jevFilter
{
	function __construct($tablename, $filterfield, $isstring=true){
		$this->filterType="geosearch";
		$this->filterLabel=JText::_("Search Near Location");
		$this->filterNullValue="";
		parent::__construct($tablename,$filterfield, true);
		$this->filter_value = JRequest::getVar($this->filterType.'_fv', $this->filterNullValue );

		if (intval(JRequest::getVar('filter_reset',0))){
			$this->filter_value = "";
		}

		$plugin = JPluginHelper::getPlugin("jevents","jevlocations");
		$this->params = new JParameter($plugin->params);
	}

	function _createFilter($prefix=""){

		if (JRequest::getInt("skipfilter",0)) return "";
		if (!$this->filterField ) return "";
		if (trim($this->filter_value)==$this->filterNullValue || strlen(trim($this->filter_value))<3) return "";
		if (strpos($this->filter_value,",")<=0) return "";

		$googlesearchdistance= JRequest::getInt("geosearchdistance",10);
		// convert to kilometers
		if ($this->params->get("scale","miles")=="miles"){
			$googlesearchdistance *= 1.609344;
		}

		list($lat,$lon) = explode(",",$this->filter_value);
		$lon = deg2rad($lon);
		$lat = deg2rad($lat);

		// see http://www.movable-type.co.uk/scripts/latlong.html
		// http://www.artfulsoftware.com/infotree/queries.php?&bw=1280
		$db = JFactory::getDBO();
		$filter = "ACOS(SIN(gloc.geolat*PI()/180)*SIN($lat)+COS(gloc.geolat*PI()/180)*COS($lat)*COS($lon-(gloc.geolon*PI()/180)))*6371 < $googlesearchdistance";

		return $filter;
	}

	function _createJoinFilter($prefix=""){
		return " #__jev_locations as gloc ON gloc.loc_id=det.location";
		return "";
	}

	function _createfilterHTML(){

		if (!$this->filterField) return "";

		$db = JFactory::getDBO();

		JHTML::script('jevlocation.js', 'plugins/jevents/' );

		$compparams = JComponentHelper::getParams("com_jevlocations");
		$googlekey = $compparams->get("googlemapskey","");
		$googleurl = $compparams->get("googlemaps",'http://maps.google.com');
		if ($googlekey!=""){
			JHTML::script( '/maps?file=api&amp;v=2.x&amp;key='.$googlekey ,$googleurl , true);
		}

		$googleaddress = JRequest::getString("googleaddress","");
		if (intval(JRequest::getVar('filter_reset',0))){
			$googleaddress = "";
		}

		$filterList=array();
		$filterList["title"]="<label class='evlocsearch_label' for='".$this->filterType."_fv'>".$this->filterLabel."</label>";
		$filterList["html"] = '<input type="text" size="27" name="googleaddress" id="googleaddress" value="'.htmlspecialchars($googleaddress).'" onchange="clearlonlat();"/>';
		$filterList["html"] .= "<input type='hidden' name='".$this->filterType."_fv' id='".$this->filterType."_fv' class='evlocsearch' value='".$this->filter_value."' />";

		$filterList["html"] .= "<span class='maxdistance'> ".JText::_("JEV MAX DISTANCE")."</span>";

		$scale = " ".$this->params->get("scale","miles");
		$list = array();
		$list[] = JHTML::_( 'select.option', 5,     "5".$scale);
		$list[] = JHTML::_( 'select.option', 10,   "10".$scale);
		$list[] = JHTML::_( 'select.option', 20,   "20".$scale);
		$list[] = JHTML::_( 'select.option', 30,   "30".$scale);
		$list[] = JHTML::_( 'select.option', 50,   "50".$scale);
		$list[] = JHTML::_( 'select.option', 100, "100".$scale);

		// build the select list itself
		$googlesearchdistance= JRequest::getInt("geosearchdistance",10);
		$filterList["html"] .= JHTML::_( 'select.genericlist', $list, "geosearchdistance", "", 'value', 'text', $googlesearchdistance);
		if (intval(JRequest::getVar('filter_reset',0))){
			$googlesearchdistance = 5;
		}
		//$filterList["html"] .= '<input type="button" name="findaddress" onclick="findAddressGeo();" value="'.JText::_("Find Address").'" />';

		$script = "JeventsFilters.filters.push({id:'".$this->filterType."_fv',value:''});";
		$document = JFactory::getDocument();
		$document->addScriptDeclaration($script);

		return $filterList;

	}
}