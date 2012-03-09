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

// use the default layout for the iphone app
setcookie("jevents_view","default",null,"/");
JRequest::setVar("iphoneapp",1);

$script = $_SERVER['REQUEST_URI'];
$urlparts = parse_url($_SERVER['REQUEST_URI']);

$parts = pathinfo($urlparts["path"]);
$filename = $parts["filename"];

$action = "iRestaurantDetails";

/**
	 * Arguments are:
	 * id = restaurant id
	 * la = latitude
	 * lo = longitude
	 *
	 * la/lo are only used to compute distance
	 * 
	 */

$id=JRequest::getInt("id",0);
$la = JRequest::getFloat("la",0);
$lo = JRequest::getFloat("lo",0);

// if frandom then set $id to random value
if ($id==0) {
	$db = JFactory::getDBO();
	$query = ' SELECT loc.loc_id FROM #__jev_locations AS loc  where loc.published = 1 AND loc.global = 1 ORDER BY rand() limit 1';
	$db->setQuery($query);
	$id = intval($db->loadResult());
}

$redirect = "/indexiphone.php?option=com_jevlocations&task=locations.detail&tmpl=component&loc_id=$id";
$redirect .= "&iphoneapp=1";

$redirect .= "&lat=".$la;
$redirect .= "&lon=".$lo;

header( 'HTTP/1.1 303 Temporary Redirect' );
header( 'Location: ' . $redirect );
