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

$action = "iAd";

/**
	 * Arguments are:
	 * screeen = from list of screens
	 */

$screen = JRequest::getString("screen","");
$bits = parse_url( JURI::root()."../../");
$root = $bits["scheme"]."://".$bits["host"]."/".realpath($bits["path"]);
$db = JFactory::getDBO();
$db->setQuery("SELECT b.*, cat.title as catname FROM #__banner as b LEFT JOIN #__categories as cat ON cat.id=b.catid where b.showBanner=1 AND (b.imptotal=0 OR b.impmade<b.imptotal) AND cat.title=".$db->Quote($screen)." ORDER BY RAND()");
$ads = $db->loadObjectList();
header('Content-type: text/xml', true);
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<RandomAd>';
// Use Category in place of screen name
if ($ads) foreach ($ads as $ad){
	$url = $root."indexiphone.php?option=com_banners&task=click&bid=".$ad->bid;
	echo '<Ad id="'.$ad->bid.'" image="'.($root."images/banners/".$ad->imageurl).'" type="URL" details="'.htmlspecialchars($url).'" />';
}
echo '</RandomAd>';

