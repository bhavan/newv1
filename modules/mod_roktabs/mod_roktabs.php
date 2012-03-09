<?php
/**
 * RokTabs Module
 *
 * @package		Joomla
 * @subpackage	RokTabs Module
 * @copyright Copyright (C) 2009 RocketTheme. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see RT-LICENSE.php
 * @author RocketTheme, LLC
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Include the syndicate functions only once
require_once (dirname(__FILE__).DS.'helper.php');


$conf =& JFactory::getConfig();
if ($conf->getValue('config.caching') && $params->get("module_cache", 0)) { 
	$cache =& JFactory::getCache('mod_roktabs');
	$list = $cache->call(array('modRokTabsHelper', 'getList'), $params);
}
else {
	$list = modRokTabsHelper::getList($params);
}

require(JModuleHelper::getLayoutPath('mod_roktabs'));