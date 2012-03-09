<?php
/**
 * AllVideos Reloaded Module Entry Point
 *
 * @version		$Id: mod_avreloaded.php 947 2008-06-09 17:29:02Z Fritz Elfert $
 * @copyright	Copyright (C) 2008 Fritz Elfert. All rights reserved.
 * @license		GNU/GPLv2
 */

// no direct access
defined( '_JEXEC' ) or die('Restricted access');

// Include the syndicate functions only once
require_once(dirname(__FILE__).DS.'helper.php');

$video = modAvReloadedHelper::getVideo($params, $module);
require(JModuleHelper::getLayoutPath('mod_avreloaded'));
