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

$action = "iRestaurantPage";


/**
		 * Restaurant list
		 * 
	 * Arguments are:
	 * la = latitude
	 * lo = longitude
	 * bIPhone = iphone - is it an iphone so can make the phone call
	 * name = REstaurant name=1 => search by name
	 * d = distance (units?) - defunct
	 * commid = community id ??? - defunct
	 * alpha = 1 => order alphabetical, 
	 * shake = 1 => random 10 restaurants otherwise nearest
	 * 
	 */		
$redirect = "/indexiphone.php?option=com_jevlocations&task=locations.listlocations&tmpl=component";

$redirect .= "&needdistance=1";
if (!JRequest::getInt("alpha",0)){
	$redirect .= "&sortdistance=1";
}
$la = JRequest::getFloat("la",0);
$redirect .= "&lat=".$la;
$lo = JRequest::getFloat("lo",0);
$redirect .= "&lon=".$lo;
$bIPhone = JRequest::getInt("bIPhone",0);
$redirect .= "&bIPhone=".$bIPhone;
$iphoneapp = JRequest::getInt("iphoneapp",0);
$redirect .= "&iphoneapp=".$iphoneapp;

// alpha sort
if (JRequest::getInt("alpha",0)){
	$redirect .= "&filter_order=loc.title&filter_order_Dir=asc";
	$redirect .= "&search=";
}
else {
	$name = JRequest::getString("name","");
	if ($name!=""){
		$redirect .= "&search=".$name;
	}
	else {
		$redirect .= "&search=";
	}
}
$redirect .= "&limit=0";
// swtich off filter
$redirect .= "&jlpriority_fv=0";
$redirect .= "&filter_loccat=0";

header( 'HTTP/1.1 303 Temporary Redirect' );
header( 'Location: ' . $redirect );
exit();

