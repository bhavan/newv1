<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: categoryClass.php 1117 2008-07-06 17:20:59Z tstahl $
 * @package     JEvents
 * @copyright   Copyright (C) 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://joomlacode.org/gf/project/jevents
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'category.php');

class JevLocationsCategory extends JTableCategory {

	// catid is a temporary field to ensure no duplicate mappings are created.
	// this can be removed from database and application after full migration
	var $catid 			= null;

	// security check
	function bind( $array , $section="com_jevlocations") {
		$array['id'] = isset($array['id']) ? intval($array['id']) : 0;
		parent::bind($array);
		// Fill in the gaps
		$this->name=$this->title;
		$this->section=$section;
		$this->image_position="left";
		
		
		
		return true;		
	}

	function store(){
		return parent::store();
	}
	

}

