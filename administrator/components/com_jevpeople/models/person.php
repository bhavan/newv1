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
class PeopleModelPerson extends JModel
{
	/**
	 * Person id
	 *
	 * @var int
	 */
	var $_pers_id = null;

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
		$this->_pers_id		= $id;
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
	 * Tests if person is checked out
	 *
	 * @access	public
	 * @param	int	A user id
	 * @return	boolean	True if checked out
	 * @since	1.5
	 */
	function isCheckedOut( $uid=0 )
	{
		if ($this->_loadData())
		{
			if ($uid) {
				return ($this->_data->checked_out && $this->_data->checked_out != $uid);
			} else {
				return $this->_data->checked_out;
			}
		}
	}

	/**
	 * Method to checkin/unlock the person
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function checkin()
	{
		if ($this->_pers_id)
		{
			$person = & $this->getTable();
			if(! $person->checkin($this->_pers_id)) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
		return false;
	}

	/**
	 * Method to checkout/lock the person
	 *
	 * @access	public
	 * @param	int	$uid	User ID of the user checking the article out
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function checkout($uid = null)
	{
		if ($this->_pers_id)
		{
			// Make sure we have a user id to checkout the article with
			if (is_null($uid)) {
				$user	=& JFactory::getUser();
				$uid	= $user->get('id');
			}
			// Lets get to it and checkout the thing...
			$person = & $this->getTable();
			if(!$person->checkout($uid, $this->_pers_id)) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}

			return true;
		}
		return false;
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
		$row =& $this->getTable();

		// Bind the form fields to the person table
		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Create the timestamp for the date

		// if new item, order last in appropriate group
		if (!$row->pers_id) {
			$where = 'type_id = ' . (int) $row->type_id ;
			$row->ordering = $row->getNextOrder( $where );
		}

		// Make sure the person table is valid
		if (!$row->check()) {
			$this->setError($row->getError());
			return false;
		}

		// Store the person table to the database
		if (!$row->store()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Now do any custom fields
		JLoader::register('JevCfParameter',JPATH_SITE."/plugins/jevents/customfields/jevcfparameter.php");
		$compparams = JComponentHelper::getParams("com_jevpeople");
		$template = $compparams->get("template","");
		$customfields = array();
		if ($template!=""){
			$xmlfile = JPATH_SITE."/plugins/jevents/customfields/templates/".$template;
			if (file_exists($xmlfile)){
				$db = JFactory::getDBO();
				$db->setQuery("SELECT * FROM #__jev_customfields2 WHERE target_id=".intval($row->pers_id). " AND targettype='com_jevpeople'");

				$customdata = $db->loadObjectList();

				$params = new JevCfParameter($customdata, $xmlfile,  null);
				$params = $params->renderToBasicArray();
				
				// clean out the defunct data!!
				$sql = "DELETE FROM #__jev_customfields2 WHERE target_id=".intval($row->pers_id). " AND targettype='com_jevpeople'";
				$db->setQuery($sql);
				$success =  $db->query();
				
				foreach ($params as $param) {
					if (!array_key_exists("custom_".$param["name"],$data)) continue;
					if (!is_array($data["custom_".$param["name"]])){
						$customfield = JFilterInput::clean($data["custom_".$param["name"]]);
					}
					else {
						$customfield = implode(",",$data["custom_".$param["name"]]);
					}

					$sql = "INSERT INTO  #__jev_customfields2 (value, target_id, targettype, name ) VALUES(".$db->Quote($customfield).", ".intval($row->pers_id).",'com_jevpeople', ".$db->Quote($param["name"]).")";
					$db->setQuery($sql);
					$success =  $db->query();					
				}
				
			}
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
			$query = 'DELETE FROM #__jev_people'
			. ' WHERE pers_id IN ( '.$cids.' )';
			$this->_db->setQuery( $query );
			if(!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}

		return true;
	}

	/**
	 * Method to (un)globalise a person
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function globalise($cid = array(), $global = 1)
	{

		if (count( $cid ))
		{
			JArrayHelper::toInteger($cid);
			$cids = implode( ',', $cid );

			$query = 'UPDATE #__jev_people'
			. ' SET global = '.(int) $global
			. ' WHERE pers_id IN ( '.$cids.' )'
			;
			$this->_db->setQuery( $query );
			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}

		return true;
	}
	
	/**
	 * Method to (un)publish a person
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function publish($cid = array(), $publish = 1)
	{
		$user 	=& JFactory::getUser();

		if (count( $cid ))
		{
			JArrayHelper::toInteger($cid);
			$cids = implode( ',', $cid );

			$query = 'UPDATE #__jev_people'
			. ' SET published = '.(int) $publish
			. ' WHERE pers_id IN ( '.$cids.' )'
			. ' AND ( checked_out = 0 OR ( checked_out = '.(int) $user->get('id').' ) )'
			;
			$this->_db->setQuery( $query );
			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}

		return true;
	}

	/**
	 * Method to move a person
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function move($direction)
	{
		$row =& $this->getTable();
		if (!$row->load($this->_pers_id)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		if (!$row->move( $direction, ' type_id = '.(int) $row->type_id.' AND published >= 0 ' )) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}

	/**
	 * Method to move a person
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function saveorder($cid = array(), $order)
	{
		$row =& $this->getTable();
		$groupings = array();

		// update ordering values
		for( $i=0; $i < count($cid); $i++ )
		{
			$row->load( (int) $cid[$i] );
			// track categories
			$groupings[] = $row->type_id;

			if ($row->ordering != $order[$i])
			{
				$row->ordering = $order[$i];
				if (!$row->store()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			}
		}

		// execute updateOrder for each parent group
		$groupings = array_unique( $groupings );
		foreach ($groupings as $group){
			$row->reorder('type_id = '.(int) $group);
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
			$query = 'SELECT w.* FROM #__jev_people AS w' .
			' LEFT JOIN #__jev_peopletypes AS pt ON pt.type_id = w.type_id' .
			' WHERE w.pers_id = '.(int) $this->_pers_id;
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