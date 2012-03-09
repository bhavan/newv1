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
class TablePerson extends JTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	var $pers_id = null;

	/**
	 * @var string
	 */
	var $title = null;

	/**
	 * @var string
	 */
	var $alias = null;

	/**
	 * @var int
	 */
	var $type_id = 0;

	/**
	 * @var int
	 */
	var $catid0 = null;

	/**
	 * @var int
	 */
	var $catid1 = null;

	/**
	 * @var int
	 */
	var $catid2 = null;

	/**
	 * @var int
	 */
	var $catid3 = null;

	/**
	 * @var int
	 */
	var $catid4 = null;

	/**
	 * @var string
	 */
	var $description = null;

	/**
	 * @var string
	 */
	var $street = null;

	/**
	 * @var string
	 */
	var $city = null;

	/**
	 * @var string
	 */
	var $state= null;

	/**
	 * @var string
	 */
	var $country= null;

	/**
	 * @var string
	 */
	var $postcode = null;

	/**
	 * @var string
	 */
	var $www = null;

	/**
	 * @var string
	 */
	var $image = null;

	/**
	 * @var string
	 */
	var $imagetitle = null;

	/**
	 * @var int
	 */
	var $geolon = null;
	
	/**
	 * @var int
	 */
	var $geolat = null;
	
	/**
	 * @var int
	 */
	var $geozoom = null;

	/**
	 * @var int
	 */
	var $ordering = null;

	/**
	 * @var int
	 */
	var $access = null;

	/**
	 * @var int
	 */
	var $global = null;

	/**
	 * @var int
	 * published on by default
	 */
	var $published = 1;

	/**
	 * @var datetime
	 */
	var $created = null;

	/**
	 * @var int 
	 */
	var $created_by = null;

	/**
	 * @var string
	 */
	var $created_by_alias = null;

	/**
	 * @var int 
	 */
	var $modified_by = null;

	/**
	 * @var boolean
	 */
	var $checked_out = 0;

	/**
	 * @var time
	 */
	var $checked_out_time = 0;

	/**
	 * @var int
	 */
	var $linktouser = 0;

	/**
	 * @var string
	 */
	var $params = null;

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 * @since 1.0
	 */
	function __construct() {
		$db =& JFactory::getDBO();
		parent::__construct('#__jev_people', 'pers_id', $db);
		
		$params = JComponentHelper::getParams("com_jevpeople");
		$this->geozoom = $params->get("zoom",10);
		$this->geolon = $params->get("long",-3.6);
		$this->geolat = $params->get("lat",-5.3);
	}

	/**
	* Overloaded bind function
	*
	* @acces public
	* @param array $hash named array
	* @return null|string	null is operation was satisfactory, otherwise returns an error
	* @see JTable:bind
	* @since 1.5
	*/
	function bind($array, $ignore = '')
	{
		if (key_exists( 'params', $array ) && is_array( $array['params'] ))
		{
			$registry = new JRegistry();
			$registry->loadArray($array['params']);
			$array['params'] = $registry->toString();
		}

		return parent::bind($array, $ignore);
	}

	/**
	 * Overloaded check method to ensure data integrity
	 *
	 * @access public
	 * @return boolean True on success
	 * @since 1.0
	 */
	function check()
	{

		/** check for valid name */
		if (trim($this->title) == '') {
			$this->setError(JText::_('Your Person must contain a title.'));
			return false;
		}

		/** check for existing name */
		$query = 'SELECT pers_id FROM #__jev_people WHERE title = '.$this->_db->Quote($this->title).' AND type_id = '.$this->_db->Quote($this->type_id);
		$this->_db->setQuery($query);

		$xid = intval($this->_db->loadResult());
		if ($xid && $xid != intval($this->pers_id)) {
			$this->setError(JText::sprintf('WARNNAMETRYAGAIN', JText::_('Person')));
			return false;
		}

		if(empty($this->alias)) {
			$this->alias = $this->title;
		}
		$this->alias = JFilterOutput::stringURLSafe($this->alias);
		if(trim(str_replace('-','',$this->alias)) == '') {
			$datenow =& JFactory::getDate();
			$this->alias = $datenow->toFormat("%Y-%m-%d-%H-%M-%S");
		}

		return true;
	}

	function store( $updateNulls=false ) {
		
		$user =& JFactory::getUser();
		if (is_null($this->created_by)){
			$this->created_by = $user->id;
		}
		else {
			$this->modified_by = $user->id;
		}
		if (is_null($this->created)){
			$datenow =& JFactory::getDate();
			$this->created	= $datenow->toMySQL();
		}
		return parent::store($updateNulls);
	}

}
