<?php
/**
* @copyright	Copyright (C) 2008 GWE Systems Ltd. All rights reserved.
 * @license		By negoriation with author via http://www.gwesystems.com
*/
ini_set("display_errors",0);

list($usec, $sec) = explode(" ", microtime());
define('_SC_START', ((float)$usec + (float)$sec));

// Set flag that this is a parent file
define( '_JEXEC', 1 );
define( 'DS', DIRECTORY_SEPARATOR );
$x = realpath(dirname(__FILE__)."/../../") ;
// SVN version
if (!file_exists($x.DS.'includes'.DS.'defines.php')){
	$x = realpath(dirname(__FILE__)."/../../../") ;

}
define( 'JPATH_BASE', $x );

ini_set("display_errors",0);

require_once JPATH_BASE.DS.'includes'.DS.'defines.php';
require_once JPATH_BASE.DS.'includes'.DS.'framework.php';

global $mainframe;
$mainframe =& JFactory::getApplication('site');
$mainframe->initialise();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <title>Google Maps</title>
    <?php
    $params = JComponentHelper::getParams("com_jevlocation");
    $apikey = $params->get("googlemapskey");
    ?>
    <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?php echo $apikey;?>" type="text/javascript"></script>

    <script language="javascript">
    
    	////////////////////////////////////////////////////////////////////
    	// GMap script talks to Obj C
 		var version 	= "1.0";
 		var map 		= null;
		//var baseicon 	= null;
 		
    	function get_version()
    	{
    		document.location = "gmapApp:version:" + version;
    	}

		////////////////////////////////////////////////
		function add_marker(lat, lng)
		{
			var latlng = new GLatLng(lat,lng);
       		
       		var marker = new GMarker(latlng,{draggable: true});
       		map.addOverlay(marker);
		
		
		    GEvent.addListener(marker, "click", function() 
		    {
    	        sText = marker.getLatLng().lat() + ":" + marker.getLatLng().lng();
    	        window.opener.document.getElementById("ctl00_mainContent_txtLatitude").value = marker.getLatLng().lat().toFixed(8);
                window.opener.document.getElementById("ctl00_mainContent_txtLongitude").value = marker.getLatLng().lng().toFixed(8);
		    });
    	    return marker;
		}


 		////////////////////////////////////////
		// Create a GMap with center lying
		// on specified center
		function create_map(lt, lng)
		{
			if (GBrowserIsCompatible())
			{
				map = new GMap2(document.getElementById("map_canvas"));
		        map.setCenter(new GLatLng(lt, lng), 13);
		        map.addControl(new GSmallMapControl());
        		map.addControl(new GMapTypeControl());
        		
		        add_marker(lt,lng);
			}
		}
    </script>
    <style>
    	body
    	{
    		margin-top:0px;
    		margin-left:0px;
    	}
    </style>
  </head>

  <body onload="create_map(<?php echo JRequest::getFloat("lat");?>, <?php echo JRequest::getFloat("long");?>)" onunload="GUnload()">
    <div id="map_canvas" style="background-color=: #009ACF; width: 320px; height: 460px"></div>
  </body>
</html>