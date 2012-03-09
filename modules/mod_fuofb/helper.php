<?php
/**
* @version		$Id: helper.php 10381 2008-06-01 03:35:53Z pasamio $
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

class modFUOFBHelper {
    function getFUOFBImage($image_text,$image_style,$language) {
    	$mod_URL = 'modules/mod_fuofb/assets/'.$language.'/';
    	$mod_path = 'modules'.DS.'mod_fuofb'.DS.'assets'.DS.$language.DS;
    	$img_URL = $mod_URL.'find-us-on-facebook-'.$image_style.'.png';
    	$img_path = $mod_path.'find-us-on-facebook-'.$image_style.'.png';
		$size = getimagesize(JPATH_BASE.DS.$img_path); //get dimensions of image
    	$attr = array('title'=>$image_text,'width'=>$size[0],'height'=>$size[1]);
    	$img =  JHTML::image(JURI::base().$img_URL,'Facebook Image',$attr);
	    
		return $img;
	}
}