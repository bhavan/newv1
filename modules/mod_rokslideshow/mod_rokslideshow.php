<?php
/**
 * RokSlideshow Module
 *
 * @package		Joomla
 * @subpackage	RokSlideshow Module
 * @copyright Copyright (C) 2009 RocketTheme. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see RT-LICENSE.php
 * @author RocketTheme, LLC
 *
 */


// no direct access
defined('_JEXEC') or die('Restricted access');
// Include the syndicate functions only once
require_once (dirname(__FILE__).DS.'helper.php');

$imagePath 	= modRokSlideshowHelper::cleanDir($params->get( 'imagePath', 'images/stories/fruit' ));
$sortCriteria = $params->get( 'sortCriteria', 0);
$sortOrder = $params->get( 'sortOrder', 'asc');
$sortOrderManual = $params->get( 'sortOrderManual', '');

if (trim($sortOrderManual) != "")
	$images = explode(",", $sortOrderManual);
else
	$images = modRokSlideshowHelper::imageList($imagePath, $sortCriteria, $sortOrder);

if (count($images) > 0) modRokSlideshowHelper::loadScripts($params, $imagePath, $images);
require(JModuleHelper::getLayoutPath('mod_rokslideshow'));