<?php
/**
* @version		$Id: mod_fuofb.php 10855 2009-08-19 16:32:34Z bbrock $
* @package		Joomla
* @copyright	Copyright (C) 2005 - 2009 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once( dirname(__FILE__).DS.'helper.php' );

$facebook_url		= $params->get('facebook_url', 'http://facebook.com/');
$target				= $params->get('target',1);
$language			= $params->get('language', 'en');
$image_style		= $params->get('image_choice', 1);
$image_align		= $params->get('image_align', 'center');
$popup_text			= $params->get('title_text', '');
$set_Itemid			= intval($params->get('set_itemid', 0));
$moduleclass_sfx	= $params->get('moduleclass_sfx', '');

$image = modFUOFBHelper::getFUOFBImage( $popup_text, $image_style, $language );
$url = $facebook_url;
	
require(JModuleHelper::getLayoutPath('mod_fuofb'));
