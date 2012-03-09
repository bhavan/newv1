<?php 
defined('_JEXEC') or die('Restricted access');

if (JRequest::getInt("pop",0)) return;

$compparams = JComponentHelper::getParams("com_jevlocations");
//$googlekey = $compparams->get("googlemapskey","");
$googlekey = JevLocationsHelper::getApiKey();//$compparams->get("googlemapskey","");
$googleurl = JevLocationsHelper::getApiUrl(); //$compparams->get("googlemaps",'http://maps.google.com');

$lang = JFactory::getLanguage();
$hl = substr($lang->getTag(),0,2);
if ($googlekey!=""){
	JHTML::script( '/maps?file=api&amp;v=2.x&amp;key='.$googlekey.'&hl='.$hl , $googleurl, true);
}
$task = JRequest::getString("jevtask","");

if (!$this->items || count($this->items)==0) return;

$zoom = 10;
$document = JFactory::getDocument();
$document->addStyleDeclaration("div.mainlocmap {clear:left;} div#gmapMulti{margin:5px auto}");
?>
<div class='mainlocmap'>
<?php
$root = JURI::root();
global $Itemid;
$script=<<<SCRIPT
var myMapMulti = false;

// Create a base icon for all of our markers that specifies the
// shadow, icon dimensions, etc.
var baseIcon = new GIcon(G_DEFAULT_ICON);
baseIcon.shadow = "http://www.google.com/mapfiles/shadow50.png";
baseIcon.iconSize = new GSize(32, 32);
baseIcon.shadowSize = new GSize(32, 32);
baseIcon.iconAnchor = new GPoint(16, 32);
baseIcon.infoWindowAnchor = new GPoint(32, 0);

function addPoint(lat, lon, locid, loctitle, evttitle){
		var markerOptions = {draggable:false };
		
		var point = new GLatLng(lat,lon);
		//see http://code.google.com/intl/cy/apis/maps/documentation/overlays.html#Custom_Icons re colour based icons ?
		
		var myMarkerMulti = new GMarker(point, markerOptions);
		myMapMulti.addOverlay(myMarkerMulti);
		
		GEvent.addListener(myMarkerMulti, "mouseover", function() {
			myMarkerMulti.openInfoWindowHtml("<div style='color:rgb(134,152,150);font-weight: bold;width:300px!important;'>"+evttitle+"<br/><br/><span style='color:#454545;font-weight:normal'>"+loctitle+"<span></div>");
		});

		GEvent.addListener(myMarkerMulti, "mouseout", function() {
			myMarkerMulti.closeInfoWindow();
		});
		
		GEvent.addListener(myMarkerMulti, "click", function() {
			// use for location detail in popup
			//SqueezeBox.initialize({});
			//SqueezeBox.setOptions(SqueezeBox.presets,{'handler': 'iframe','size': {'x': 750, 'y': 500},'closeWithOverlay': 0});
			//SqueezeBox.url = "$root/index.php?option=com_jevlocations&task=locations.detail&se=1&tmpl=component&loc_id="+locid;
			//SqueezeBox.setContent('iframe', SqueezeBox.url );
			
			// use for event detail page
			document.location.replace("$root/index.php?option=com_jevlocations&task=locations.detail&se=1&Itemid=$Itemid&loc_id="+locid);
		});		

}

function myMaploadMulti(){
	if (GBrowserIsCompatible()) {
	
		myMapMulti = new GMap2(document.getElementById("gmapMulti"));
		myMapMulti.setMapType(G_HYBRID_MAP);
        myMapMulti.addControl(new GSmallMapControl());
        myMapMulti.addControl(new GMapTypeControl());
		
SCRIPT;
$minlon = 0;
$minlat = 0;
$maxlon = 0;
$maxlat = 0;
$first = true;
foreach ($this->items as $location) {
	if ($location->geozoom==0) continue;
	if ($first){
		$minlon = floatval($location->geolon);
		$minlat = floatval($location->geolat);
		$maxlon = floatval($location->geolon);
		$maxlat = floatval($location->geolat);
		$first=false;
	}
	$minlon = floatval($location->geolon)>$minlon?$minlon:floatval($location->geolon);
	$minlat = floatval($location->geolat)>$minlat?$minlat:floatval($location->geolat);
	$maxlon = floatval($location->geolon)<$maxlon?$maxlon:floatval($location->geolon);
	$maxlat = floatval($location->geolat)<$maxlat?$maxlat:floatval($location->geolat);
}
if ($minlon==$maxlon){
	$minlon-=0.002;
	$maxlon+=0.002;
}
if ($minlat==$maxlat){
	$minlat-=0.002;
	$maxlat+=0.002;
}
$midlon = ($minlon + $maxlon)/2.0;
$midlat = ($minlat + $maxlat)/2.0;

$script.=<<<SCRIPT
var point = new GLatLng($midlat,$midlon);

zoom = myMapMulti.getBoundsZoomLevel(new GLatLngBounds(new GLatLng($minlat,$minlon), new GLatLng($maxlat,$maxlon)));
//zoom = Math.max(4,zoom);
myMapMulti.setCenter(point, zoom );
SCRIPT;

foreach ($this->items as $location) {
	if ($location->loc_id==0) continue;

	$script.="	addPoint($location->geolat,$location->geolon,$location->loc_id, '".addslashes(str_replace(array("\n","\r"),"",$location->description))."', '".addslashes(str_replace("\n","",$location->title))."');\n";
 	}
$script.=<<<SCRIPT
	}
};
window.addEvent("load",function (){window.setTimeout("myMaploadMulti()",1000);});
SCRIPT;
$document = JFactory::getDocument();
$document->addScriptDeclaration($script);
JHTML::_('behavior.modal');

?>
<div id="gmapMulti" style="width: 600px; height:600px;overflow:hidden;"></div>

</div>