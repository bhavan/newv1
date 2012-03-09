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

$action = "iEvent";
/**
	 * the list of today's events
	 * la/lo are only used to compute distance
	 * order is always by time
	 *
	 * Arguments are:
	 * d = YYYY-MM-DD
	 * la = latitude
	 * lo = longitude
	 * 
	 */

$redirect = "/indexiphone.php?option=com_jevents&task=day.listevents&tmpl=component";

$d = JRequest::getString("d",false);
if ($d) {
	list($y,$m,$day) = explode("-",$d);
	$y = intval($y);
	$m = intval($m);
	$day = intval($day);
	$redirect .= "&year=$y&month=$m&day=$day";
}
$redirect .= "&iphoneapp=1";
$la = JRequest::getFloat("la",0);
$redirect .= "&la=".$la;
$lo = JRequest::getFloat("lo",0);
$redirect .= "&lo=".$lo;

header( 'HTTP/1.1 303 Temporary Redirect' );
header( 'Location: ' . $redirect );
