
<?php
/**
 * @version		1.0.0 townwizard_container $
 * @package		townwizard_container
 * @copyright	Copyright © 2012 - All rights reserved.
 * @license		GNU/GPL
 * @author		MLS
 * @author mail	nobody@nobody.com
 *
 *
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Require the base controller
require_once (JPATH_COMPONENT.DS.'controller.php');
require_once (JPATH_COMPONENT.DS.'model.php');
require_once (JPATH_COMPONENT.DS.'table.php');
require_once ( JPATH_COMPONENT.DS.'helpers'.DS.'helper.php' );

// Require specific controller if requested
if($controller = JRequest::getWord('controller')) {
	$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
	if (file_exists($path)) {
		require_once $path;
	} else {
		$controller = '';
	}
}
else
{
    $controller = 'partner';
    $path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
    require_once $path;
}

// Create the controller
$classname	= 'TownwizardController' . $controller;
$controller	= new $classname();

// Perform the Request task
$controller->execute(JRequest::getVar('task', 'index'));
$controller->redirect();
?>
