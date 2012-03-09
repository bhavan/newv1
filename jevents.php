<?php

// tocheck: tallylife.com/templates/rt_quantive_j15/html/com_jevents/ext/day/listevents_body.php

// Set flag that this is a parent file

define( '_JEXEC', 1 );

define( 'DS', DIRECTORY_SEPARATOR );

define('JPATH_BASE', dirname(__FILE__).DS);

//exit(JPATH_BASE);

//exit(JPATH_BASE .DS.'..'.DS.'includes'.DS.'defines.php');
require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );

JDEBUG ? $_PROFILER->mark( 'afterLoad' ) : null;

/**
 * CREATE THE APPLICATION
 *
 * NOTE :
 */
$mainframe =& JFactory::getApplication('site');

/**
 * INITIALISE THE APPLICATION
 *
 * NOTE :
 */
// set the language

$option = "com_jevents";
define( 'JPATH_COMPONENT',					JPATH_BASE.DS.'components'.DS.$option);
$mainframe->initialise();

JPluginHelper::importPlugin('system');
jimport('joomla.filesystem.path');
include_once("components/com_jevents/jevents.defines.php");
$registry	=& JRegistry::getInstance("jevents");
$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
if ($params->get("icaltimezone","")!="" && is_callable("date_default_timezone_set")){
  //@$timezone= date_default_timezone_get();
 // @$timezone= 'Asia/Calcutta';
  //date_default_timezone_set($params->get("icaltimezone",""));
  date_default_timezone_set($timezone);
  $registry->setValue("jevents.timezone",$timezone);
}
$lang =& JFactory::getLanguage();
$lang->load(JEV_COM_COMPONENT, JPATH_ADMINISTRATOR);
$lang->load(JEV_COM_COMPONENT, JPATH_THEMES.DS.$mainframe->getTemplate());

@ini_set("zend.ze1_compatibility_mode","Off");


JPluginHelper::importPlugin("jevents");

// Make sure the view specific language file is loaded
JEV_CommonFunctions::loadJEventsViewLang();

// Add reference for constructor in registry - unfortunately there is no add by reference method
// we rely on php efficiency to not create a copy
$registry	=& JRegistry::getInstance("jevents");
$registry->setValue("jevents.controller",$controller);

// record what is running - used by the filters
//$controller->execute($task);
require('libraries/joomla/application/module/helper.php');

//$data = $datamodel->getWeekData( 2010, 9, 24 );
//$data = $datamodel->getRangeData('2010-09-25', '2010-10-24', 99999, 0);
//$data = $datamodel->getCalendarData('2010', '9', '28');

?>