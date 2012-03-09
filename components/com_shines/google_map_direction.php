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

$direction = JRequest::getFloat('lat1',0).",".JRequest::getFloat('long1',0)." to ".JRequest::getFloat('lat2',0).",".JRequest::getFloat('long2',0);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml">
  <head>
    <title>Google Map</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <?php
    $params = JComponentHelper::getParams("com_jevlocations");
    $apikey = $params->get("googlemapskey");
    ?>
    <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?php echo $apikey;?>" type="text/javascript"></script>
      <script language="javascript">

      // Create a directions object and register a map and DIV to hold the
      // resulting computed directions

      var map;
      var directionsPanel;
      var directions;

      function create_map(lt, lng)
      {
      if (GBrowserIsCompatible())
      {
      map = new GMap2(document.getElementById("map_canvas"));
      map.setCenter(new GLatLng(lt, lng), 13);
      directionsPanel = document.getElementById("route");
      directions = new GDirections(map, directionsPanel);
      //directions.load("from: 500 Memorial Drive, Cambridge, MA to: 4 Yawkey Way, Boston, MA 02215 (Fenway Park)");
       	directions.load("<?php echo $direction; ?>");
		        
		    //map.addControl(new GSmallMapControl());
        //map.addControl(new GMapTypeControl());
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
  <body onload="create_map(<? echo  JRequest::getFloat('lat1'); ?>, <? echo  JRequest::getFloat('long1'); ?>)" onunload="GUnload()">
    <!--<div id="map_canvas" style="background-color=: #009ACF; width: 320px; height: 370px"></div>-->
    <div id="map_canvas" style="width: 320px; height: 480px; float:left; border: 1px solid black;"></div>
    <div id="route" style="width: 0px; height:480px; float:right; border; 1px solid black;display:none;"></div>
  </body>
</html>