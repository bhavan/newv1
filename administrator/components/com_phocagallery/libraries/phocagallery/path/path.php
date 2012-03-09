<?php
/*
 * @package Joomla 1.5
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @component Phoca Gallery
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
//require(JPATH_ROOT."configuration.php");
class PhocaGalleryPath extends JObject
{
	function __construct() {}
	
	function phocaUrlPath($url){
		$hostname = str_replace('www.', '', $url);
		$needle = '.';
		$rtnphocaURL = substr($hostname,0,strpos($hostname, $needle));
		return $rtnphocaURL;	
	}
	
	function &getInstance() {
		static $instance;
		if (!$instance) {
			$instance = new PhocaGalleryPath();
						
			//$phocaURL = $instance->phocaUrlPath($_SERVER["SERVER_NAME"]);
			$phocaURL = $_SESSION['partner_folder_name'];
			/*
			$instance->image_abs 			= JPATH_ROOT . DS . 'images' . DS . 'phocagallery' . DS ;
			$instance->image_rel			= 'images/phocagallery/';
			$instance->avatar_abs 			= JPATH_ROOT . DS . 'images' . DS . 'phocagallery' . DS . 'avatars' . DS ;
			$instance->avatar_rel			= 'images/phocagallery/avatars/';
			*/
			$instance->image_abs 			= JPATH_ROOT . DS . 'partner' . DS . $phocaURL . DS . 'images' . DS . 'phocagallery' . DS ;
			$instance->image_rel			= 'partner/'.$phocaURL.'/images/phocagallery/';
			$instance->avatar_abs 			= JPATH_ROOT . DS . 'images' . DS . 'phocagallery' . DS . 'avatars' . DS ;
			$instance->avatar_rel			= 'partner/'.$phocaURL.'/images/phocagallery/avatars/';
			$instance->image_rel_full		= JURI::base(true) . '/' . $instance->image_rel;
			$instance->image_rel_admin 		= 'administrator/components/com_phocagallery/assets/images/';
			$instance->image_rel_admin_full = JURI::base(true) . '/' . $instance->image_rel_admin;
			$instance->image_rel_front 		= 'components/com_phocagallery/assets/images/';
			$instance->image_rel_front_full = JURI::base(true) . '/' . $instance->image_rel_front;
			$instance->image_abs_front		= JPATH_ROOT . DS . 'components' . DS . 'com_phocagallery' . DS . 'assets' . DS . 'images'.DS ;
		}
		return $instance;
	}

	function getPath() {
		$instance 	= &PhocaGalleryPath::getInstance();
		return $instance;
	}


}
?>
