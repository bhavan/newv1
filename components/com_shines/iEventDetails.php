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

$action = "iEventDetails";
/**
	 * Arguments are:
	 * id = event id
	 * la/lo are only used to compute distance
	 * la = latitude
	 * lo = longitude
	 * 
	 */

$redirect = "/components/com_shines/ShowEventsDetails.php?option=com_jevents&task=icalrepeat.detail&tmpl=component";
$id = JRequest::getInt("id",0);
$redirect .= "&evid=".$id;
$redirect .= "&iphoneapp=1";
$la = JRequest::getFloat("la",0);
$redirect .= "&la=".$la;
$lo = JRequest::getFloat("lo",0);
$redirect .= "&lo=".$lo;

header( 'HTTP/1.1 303 Temporary Redirect' );
header( 'Location: ' . $redirect );
