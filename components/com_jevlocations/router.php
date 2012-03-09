<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: router.php 1631 2009-11-26 05:38:33Z geraint $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined('_JEXEC') or die( 'No Direct Access' );

JLoader::register('JEVConfig',JPATH_ADMINISTRATOR."/components/com_jevents/libraries/config.php");
JLoader::register('JEVHelper',JPATH_SITE."/components/com_jevents/libraries/helper.php");

function JevLocationsBuildRoute(&$query)
{
	$cfg = & JEVConfig::getInstance();
	$segments = array();
	// We don't need the view - its only used to manipulate parameters
	if (isset($query['view'])){
		unset($query['view']);
	}

	$task = false;
	if (!isset($query['task'])){
		if (isset($query["Itemid"])){
			$menu = & JSite::getMenu();
			$menuitem = $menu->getItem($query["Itemid"]);
			if (!is_null($menuitem) && isset($menuitem->query["task"])){
				$task = $menuitem->query["task"];
			}
		}
		if (!$task){
			$task = 'locations.locations';
		}
	}
	else {
		$task=$query['task'];
		unset($query['task']);
	}

	$task = str_replace("locations.","",$task);
	switch ($task) {
		case "locations":
		case "overview":
		case "list":
			$segments[]=$task;
			break;
		case "detail":
			$segments[]=$task;
			if(isset($query['loc_id'])) {
				$segments[] = $query['loc_id'];
				unset($query['loc_id']);
			}
			else {
				$segments[] = 0;
			}
			if(isset($query['se'])) {
				$segments[] = $query['se'];
				unset($query['se']);
			}
			else {
				$segments[] = 0;
			}
			if(isset($query['title'])) {
				$segments[] = $query['title'];
				unset($query['title']);
			}
			else {
				$segments[] = '-';
			}
			break;

		default:
			$segments[]=$task;
			break;
	}


	return $segments;
}

function JevLocationsParseRoute($segments)
{
	$vars = array();

	//Get the active menu item
	$menu =& JSite::getMenu();
	$item =& $menu->getActive();

	// Count route segments
	$count = count($segments);

	if ($count>0){
		// task
		$task = $segments[0];
		// backwards compatability
		if (strpos($task,"locations.")===false) $task = "locations.".$task;
		$vars["task"]=$task;

		switch 	($task){
			case "locations.locations":
			case "locations.overview":
			case "locations.list":
				break;
			case "locations.detail":
				if($count>1) {
					$vars['loc_id'] = $segments[1];
				}
				if($count>2) {
					$vars['se'] = $segments[2];
				}
				break;
			default:
				break;
		}


	}
	return $vars;

}
