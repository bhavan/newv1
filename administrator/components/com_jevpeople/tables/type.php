<?php
/**
 * copyright (C) 2008 GWE Systems Ltd - All rights reserved
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
* Person Table class
*
*/
class TableType extends JTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	var $type_id = null;

	/**
	 * @var string
	 */
	var $title = null;

	/**
	 * @var int
	 */
	var $multiple = 0;

	/**
	 * @var int
	 */
	var $maxperevent = 1;
	/**
	 * @var int
	 */
	var $multicat = 0;

	/**
	 * @var int
	 */
	var $showaddress = 0;

	/**
	 * @var string
	 */
	var $categories = null;

	/**
	 * @var string
	 */
	var $calendars = null;

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 * @since 1.0
	 */
	function __construct() {
		$db =& JFactory::getDBO();
		parent::__construct('#__jev_peopletypes', 'type_id', $db);
	}		


	function bind($array, $ignore = '') {
		$success = parent::bind($array, $ignore);

		if (key_exists('categories', $array)) {
			if($array['categories']=='all' || $array['categories']=='none') $this->categories = $array['categories'];
			else if (is_array($array['categories'])){
				JArrayHelper::toInteger($array['categories']);
				$this->categories = implode("|",$array['categories']);
			}
		}
		if (key_exists('calendars', $array)) {
			if($array['calendars']=='all' || $array['calendars']=='none') $this->calendars = $array['calendars'];
			else if (is_array($array['calendars'])){
				JArrayHelper::toInteger($array['calendars']);
				$this->calendars = implode("|",$array['calendars']);
			}
		}

		return $success;
	}

}
