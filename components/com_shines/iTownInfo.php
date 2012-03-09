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

$params = JComponentHelper::getParams("com_shines");
$townname = $params->get("townname","");
$zip = $params->get("zip","");
$email = $params->get("email","");

$lparams = JComponentHelper::getParams("com_jevlocations");
$long = $lparams->get("long",50);
$lat = $lparams->get("lat",20);
ob_end_clean();

header('Content-type: text/xml;charset=utf-8', true);
echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";?>
<AllInfo>
<Info townname="<?php echo htmlentities($townname);?>" zip="<?php echo htmlentities($zip);?>" latitude="<?php echo $lat;?>" longitude="<?php echo $long;?>" email="<?php echo $email;?>" />
</AllInfo>
