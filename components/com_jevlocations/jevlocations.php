<?php
/**
 * copyright (C) 2008 GWE Systems Ltd - All rights reserved
 */

defined( 'JPATH_BASE' ) or die( 'Direct Access to this location is not allowed.' );

//error_reporting(E_ALL);

jimport('joomla.filesystem.path');

if (!defined("JEVEX_COM_COMPONENT")){
	define("JEVEX_COM_COMPONENT","com_jevlocations");
	define("JEVEX_COMPONENT",str_replace("com_","",JEVEX_COM_COMPONENT));
	define("JEV_COM_COMPONENT","com_jevents");
	define("JEV_COMPONENT",str_replace("com_","",JEV_COM_COMPONENT));
}
JLoader::register('JEVHelper',JPATH_SITE."/components/com_jevents/libraries/helper.php");
JLoader::register('JevLocationsHelper',JPATH_ADMINISTRATOR."/components/com_jevlocations/libraries/helper.php");

// load admin language too
$lang 		=& JFactory::getLanguage();		
$lang->load("com_jevlocations", JPATH_ADMINISTRATOR);

$cmd = JRequest::getCmd('task', 'locations.locations');
$view = JRequest::getCmd('view', 'locations');
$layout = JRequest::getCmd('layout', '');

if (strpos($cmd, '.') != false) {
	// We have a defined controller/task pair -- lets split them out
	list($controllerName, $task) = explode('.', $cmd);
	
	// Define the controller name and path
	$controllerName	= strtolower($controllerName);
	$controllerPath	= JPATH_COMPONENT.DS.'controllers'.DS.$controllerName.'.php';
	$controllerName = "Front".$controllerName;
	
	// If the controller file path exists, include it ... else lets die with a 500 error
	if (file_exists($controllerPath)) {
		require_once($controllerPath);
	} else {
		JError::raiseError(500, 'Invalid Controller '.$controllerName);
	}
} else {
	// Base controller, just set the task 
	$controllerName = $view;
	$task = $layout;
}

// Set the name for the controller and instantiate it
$controllerClass = ucfirst($controllerName).'Controller';
if (class_exists($controllerClass)) {
	$controller = new $controllerClass();
} else {
	JError::raiseError(500, 'Invalid Controller Class - '.$controllerClass.(file_exists($controllerPath)?" Exists":" doesnt Exist" ));
}

$config	=& JFactory::getConfig();

// Perform the Request task
$controller->execute($task);

// Redirect if set by the controller
$controller->redirect();
