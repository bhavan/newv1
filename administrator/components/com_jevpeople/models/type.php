<?php
/**
 * copyright (C) 2008 GWE Systems Ltd - All rights reserved
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.model');

/**
 * People Component Person Model
 *
 */
class PeopleTypesModelType extends JModel
{
	/**
	 * Person id
	 *
	 * @var int
	 */
	var $_type_id = null;

	/**
	 * Person data
	 *
	 * @var array
	 */
	var $_data = null;

	/**
	 * Constructor
	 *
	 * @since 1.5
	 */
	function __construct()
	{
		parent::__construct();

		$array = JRequest::getVar('cid', array(0), '', 'array');
		$edit	= JRequest::getVar('edit',true);
		if($edit)
		$this->setId((int)$array[0]);
	}

	/**
	 * Method to set the person identifier
	 *
	 * @access	public
	 * @param	int Person identifier
	 */
	function setId($id)
	{
		// Set person id and wipe data
		$this->_type_id		= $id;
		$this->_data	= null;
	}

	/**
	 * Method to get a person
	 *
	 * @since 1.5
	 */
	function &getData()
	{
		// Load the person data
		if ($this->_loadData())
		{
			// Initialize some variables
			$user = &JFactory::getUser();

		}
		else  $this->_initData();

		return $this->_data;
	}


	/**
	 * Method to store the person
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function store($data)
	{

		// fix the calendars and categories fields
		if ($data['calendars']=='select') $data['calendars']=array();
		if ($data['categories']=='select') $data['categories']=array();

		$row =& $this->getTable();

		// Bind the form fields to the person table
		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Store the person table to the database
		if (!$row->store()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}

	/**
	 * Method to remove a person
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function delete($cid = array())
	{
		$result = false;

		if (count( $cid ))
		{
			JArrayHelper::toInteger($cid);
			$cids = implode( ',', $cid );
			$query = 'DELETE FROM #__jev_peopletypes WHERE type_id IN ( '.$cids.' )';
			$this->_db->setQuery( $query );
			if(!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}

		return true;
	}

	/**
	 * Method to load content person data
	 *
	 * @access	private
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function _loadData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = 'SELECT t.* FROM #__jev_peopletypes AS t' .
			' WHERE t.type_id = '.(int) $this->_type_id;
			$this->_db->setQuery($query);
			$this->_data = $this->_db->loadObject();
			return (boolean) $this->_data;
		}
		return true;
	}

	/**
	 * Method to initialise the person data
	 *
	 * @access	private
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function _initData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$this->_data =& $this->getTable();
			return (boolean) $this->_data;
		}
		return true;
	}
}