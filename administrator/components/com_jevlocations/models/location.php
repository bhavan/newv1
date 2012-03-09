<?php
/**
 * copyright (C) 2008 GWE Systems Ltd - All rights reserved
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.model');

/**
 * Locations Component Location Model
 *
 */
class LocationsModelLocation extends JModel
{
	/**
	 * Location id
	 *
	 * @var int
	 */
	var $_loc_id = null;

	/**
	 * Location data
	 *
	 * @var array
	 */
	var $_data = null;

	public $lastrow = null;

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
	 * Method to set the location identifier
	 *
	 * @access	public
	 * @param	int Location identifier
	 */
	function setId($id)
	{
		// Set location id and wipe data
		$this->_loc_id		= $id;
		$this->_data	= null;
	}

	/**
	 * Method to get a location
	 *
	 * @since 1.5
	 */
	function &getData()
	{
		// Load the location data
		if ($this->_loadData())
		{
			// Initialize some variables
			$user = &JFactory::getUser();

			// Check to see if the category is published
			$compparams = JComponentHelper::getParams("com_jevlocations");
			$usecats = $compparams->get("usecats",0);
			if ($usecats){
				if (is_null($this->_data->cat_pub)){
					JError::raiseWarning(100, JText::_("City Not Found"));
				}
				else {
					if (!$this->_data->cat_pub) {
						JError::raiseError( 404, JText::_("Resource Not Found") );
						return;
					}

					// Check whether category access level allows access
					if ($this->_data->cat_access > $user->get('aid', 0)) {
						JError::raiseError( 403, JText::_('ALERTNOTAUTH') );
						return;
					}
				}
			}
		}
		else  $this->_initData();

		return $this->_data;
	}

	/**
	 * Tests if location is checked out
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
	 * Method to checkin/unlock the location
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function checkin()
	{
		if ($this->_loc_id)
		{
			$location = & $this->getTable();
			if(! $location->checkin($this->_loc_id)) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
		return false;
	}

	/**
	 * Method to checkout/lock the location
	 *
	 * @access	public
	 * @param	int	$uid	User ID of the user checking the article out
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function checkout($uid = null)
	{
		if ($this->_loc_id)
		{
			// Make sure we have a user id to checkout the article with
			if (is_null($uid)) {
				$user	=& JFactory::getUser();
				$uid	= $user->get('id');
			}
			// Lets get to it and checkout the thing...
			$location = & $this->getTable();
			if(!$location->checkout($uid, $this->_loc_id)) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}

			return true;
		}
		return false;
	}

	/**
	 * Method to store the location
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function store($data)
	{
		$row =& $this->getTable();

		if (isset($data["loc_id"]) && $data["loc_id"]>0){
			$row->load(intval($data["loc_id"]));
		}
		// Bind the form fields to the location table
		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Create the timestamp for the date

		// if new item, order last in appropriate group
		if (!$row->loc_id) {
			$where = 'catid = ' . (int) $row->catid ." and loccat = ".(int) $row->loccat;
			$row->ordering = $row->getNextOrder( $where );
		}

		// Make sure the location table is valid
		if (!$row->check()) {
			$this->setError($row->getError());
			return false;
		}

		// Store the location table to the database
		if (!$row->store()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		$this->lastrow = $row;
		
		// Now do any custom fields
		JLoader::register('JevCfParameter',JPATH_SITE."/plugins/jevents/customfields/jevcfparameter.php");
		$compparams = JComponentHelper::getParams("com_jevlocations");
		$template = $compparams->get("template","");
		$customfields = array();
		if ($template!=""){
			$xmlfile = JPATH_SITE."/plugins/jevents/customfields/templates/".$template;
			if (file_exists($xmlfile)){
				$db = JFactory::getDBO();
				$db->setQuery("SELECT * FROM #__jev_customfields3 WHERE target_id=".intval($row->loc_id). " AND targettype='com_jevlocations'");

				$customdata = $db->loadObjectList();

				$params = new JevCfParameter($customdata, $xmlfile,  null);
				$params = $params->renderToBasicArray();
				
				// clean out the defunct data!!
				$sql = "DELETE FROM #__jev_customfields3 WHERE target_id=".intval($row->loc_id). " AND targettype='com_jevlocations'";
				$db->setQuery($sql);
				$success =  $db->query();
				
				foreach ($params as $param) {
					if (!array_key_exists("custom_".$param["name"],$data)) continue;
					
					if (!is_array($data["custom_".$param["name"]])){
						if ($param["allowhtml"]){
							static $safeHtmlFilter;
							if (!isset($safeHtmlFilter)){
								$safeHtmlFilter = & JFilterInput::getInstance(null, null, 1, 1);
							}
							$customfield  = $safeHtmlFilter->clean($data["custom_".$param["name"]]);
						}
						else{
							$customfield = JFilterInput::clean($data["custom_".$param["name"]]);
						}
					}
					else {
						$customfield = implode(",",$data["custom_".$param["name"]]);
					}

					$sql = "INSERT INTO  #__jev_customfields3 (value, target_id, targettype, name ) VALUES(".$db->Quote($customfield).", ".intval($row->loc_id).",'com_jevlocations', ".$db->Quote($param["name"]).")";
					$db->setQuery($sql);
					$success =  $db->query();					
				}
				
			}
		}
		
		return true;
	}

	/**
	 * Method to remove a location
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
			$query = 'DELETE FROM #__jev_locations'
			. ' WHERE loc_id IN ( '.$cids.' )';
			$this->_db->setQuery( $query );
			if(!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}

		// Now ensure no jevents location orphans
		$query = 'UPDATE #__jevents_vevdetail SET location="" WHERE location IN ( '.$cids.' )';
		$this->_db->setQuery( $query );
		if(!$this->_db->query()) {
			$this->setError($this->_db->getErrorMsg());
		}


		return true;
	}

	/**
	 * Method to (un)publish a location
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

			$query = 'UPDATE #__jev_locations'
			. ' SET published = '.(int) $publish
			. ' WHERE loc_id IN ( '.$cids.' )'
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
	 * Method to (un)globalise a location
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

			$query = 'UPDATE #__jev_locations'
			. ' SET global = '.(int) $global
			. ' WHERE loc_id IN ( '.$cids.' )'
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
	 * Method to move a location
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function move($direction)
	{
		$row =& $this->getTable();
		if (!$row->load($this->_loc_id)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		if (!$row->move( $direction, ' catid = '.(int) $row->catid.' and loccat = '.(int) $row->loccat.' AND published >= 0 ' )) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}

	/**
	 * Method to move a location
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
			$groupings[] = $row->loccat;

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
			$row->reorder('catid = '.(int) $group);
		}

		return true;
	}

	/**
	 * Method to load content location data
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
			$query = 'SELECT w.*, cc.title AS category,'.
			' cc.published AS cat_pub, cc.access AS cat_access'.
			' FROM #__jev_locations AS w' .
			' LEFT JOIN #__categories AS cc ON cc.id = w.catid' .
			' WHERE w.loc_id = '.(int) $this->_loc_id;
			$this->_db->setQuery($query);
			$this->_data = $this->_db->loadObject();
			return (boolean) $this->_data;
		}
		return true;
	}

	/**
	 * Method to initialise the location data
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