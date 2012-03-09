<?php
/**
 * copyright (C) 2008 GWE Systems Ltd - All rights reserved
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.model');

/**
 * People Component People Model
 *
 */
class PeopleModelPeople extends JModel
{
	/**
	 * Category ata array
	 *
	 * @var array
	 */
	var $_data = null;

	/**
	 * Category total
	 *
	 * @var integer
	 */
	var $_total = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	var $_pagination = null;

	/**
	 * Constructor
	 *
	 * @since 1.5
	 */
	function __construct()
	{
		parent::__construct();

		global $mainframe, $option;

		// Get the pagination request variables
		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart	= $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );

		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Method to get person item data
	 *
	 * @access public
	 * @return array
	 */
	function getData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
			$db =& JFactory::getDBO();
			echo $db->getErrorMsg();
		}

		return $this->_data;
	}

	
	/**
	 * Get list of items for public list in frontend 
	 *
	 * @return unknown
	 */
	function getPublicData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_publicdata))
		{
			$query = $this->_buildPublicQuery();
			$this->_publicdata = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
			$db =& JFactory::getDBO();
			echo $db->getErrorMsg();
		}

		return $this->_publicdata;
	}
	
	/**
	 * Method to get the total number of person items
	 *
	 * @access public
	 * @return integer
	 */
	function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_total))
		{
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}

	/**
	 * Method to get the total number of location items
	 *
	 * @access public
	 * @return integer
	 */
	function getPublicTotal()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_total))
		{
			$query = $this->_buildPublicQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}
	
	/**
	 * Method to get a pagination object for the people
	 *
	 * @access public
	 * @return integer
	 */
	function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			global  $mainframe;
			if ($mainframe->isAdmin()){
				jimport('joomla.html.pagination');
				$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
			}
			else {
				include_once(JPATH_COMPONENT_ADMINISTRATOR."/libraries/JevPagination.php");
				$this->_pagination = new JevPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit'),true);
			}
		}

		return $this->_pagination;
	}

	/**
	 * Method to get a pagination object for the locations
	 *
	 * @access public
	 * @return integer
	 */
	function getPublicPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			global  $mainframe;
			if ($mainframe->isAdmin()){
				jimport('joomla.html.pagination');
				$this->_pagination = new JPagination( $this->getPublicTotal(), $this->getState('limitstart'), $this->getState('limit') );
			}
			else {
				include_once(JPATH_COMPONENT_ADMINISTRATOR."/libraries/JevPagination.php");
				$this->_pagination = new JevPagination( $this->getPublicTotal(), $this->getState('limitstart'), $this->getState('limit'),true);
			}
		}

		return $this->_pagination;
	}
	
	function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildContentWhere();
		$orderby	= $this->_buildContentOrderBy();

		$query = ' SELECT pers.*, pt.title as typename, cat0.title as catname0,cat1.title as catname1,cat2.title as catname2,cat3.title as catname3,cat4.title as catname4'
		. ' FROM #__jev_people AS pers '
		. ' LEFT JOIN #__jev_peopletypes AS pt ON pt.type_id = pers.type_id'
		. ' LEFT JOIN #__categories AS cat0 ON cat0.id = pers.catid0'
		. ' LEFT JOIN #__categories AS cat1 ON cat1.id = pers.catid1'
		. ' LEFT JOIN #__categories AS cat2 ON cat2.id = pers.catid2'
		. ' LEFT JOIN #__categories AS cat3 ON cat3.id = pers.catid3'
		. ' LEFT JOIN #__categories AS cat4 ON cat4.id = pers.catid4'
		. $where
		. $orderby
		;
		return $query;
	}

	function _buildPublicQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildPublicContentWhere();
		$orderby	= $this->_buildContentOrderBy();

		$query = ' SELECT pers.*, pt.title as typename, cat0.title as catname0,cat1.title as catname1,cat2.title as catname2,cat3.title as catname3,cat4.title as catname4'
		. ' FROM #__jev_people AS pers '
		. ' LEFT JOIN #__jev_peopletypes AS pt ON pt.type_id = pers.type_id'
		. ' LEFT JOIN #__categories AS cat0 ON cat0.id = pers.catid0'
		. ' LEFT JOIN #__categories AS cat1 ON cat1.id = pers.catid1'
		. ' LEFT JOIN #__categories AS cat2 ON cat2.id = pers.catid2'
		. ' LEFT JOIN #__categories AS cat3 ON cat3.id = pers.catid3'
		. ' LEFT JOIN #__categories AS cat4 ON cat4.id = pers.catid4'
		. $where
		. ' GROUP BY pers.pers_id'
		. $orderby
		;
		return $query;
	}

	
	function _buildContentOrderBy()
	{
		global $mainframe, $option;

		$filter_order		= $mainframe->getUserStateFromRequest( $option.'pers_filter_order',		'filter_order',		'pers.ordering',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'pers_filter_order_Dir',	'filter_order_Dir',	'',				'word' );

		if ($filter_order == 'pers.ordering'){
			$orderby 	= ' ORDER BY pers.type_id,  pers.ordering '.$filter_order_Dir;
		} else {
			$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir.' ,  pers.type_id,  pers.ordering ';
		}

		return $orderby;
	}

	function _buildContentWhere()
	{
		global $mainframe, $option;
		$db					=& JFactory::getDBO();
		$filter_type		= $mainframe->getUserStateFromRequest( $option.'type_id',				'type_id',			'',				'int' );
		$filter_state		= $mainframe->getUserStateFromRequest( $option.'pers_filter_state',		'filter_state',		'',				'word' );
		$filter_catid		= $mainframe->getUserStateFromRequest( $option.'pers_filter_catid',		'filter_catid',		0,				'int' );
		$filter_order		= $mainframe->getUserStateFromRequest( $option.'pers_filter_order',		'filter_order',		'pers.ordering',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'pers_filter_order_Dir',	'filter_order_Dir',	'',				'word' );
		$search				= $mainframe->getUserStateFromRequest( $option.'pers_search',			'search',			'',				'string' );
		$search				= JString::strtolower( $search );

		$where = array();

		$compparams = JComponentHelper::getParams("com_jevpeople");
		if ($filter_catid > 0) {
			$where[] = '( pers.catid0 = '.(int) $filter_catid
			. ' OR pers.catid1 = '.(int) $filter_catid
			. ' OR pers.catid2 = '.(int) $filter_catid
			. ' OR pers.catid3 = '.(int) $filter_catid
			. ' OR pers.catid4 = '.(int) $filter_catid .")";
		}
		if ($filter_type > 0) {
			$where[] = ' pers.type_id = '.(int) $filter_type;
		}
		if ($search) {
			$where[] = ' (LOWER(pers.title) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false )
			.' OR LOWER(pers.city) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false )
			.' OR LOWER(pers.state) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false )
			.' OR LOWER(pers.country) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false )
			. ')';
		}

		if ( $filter_state ) {
			if ( $filter_state == 'P' ) {
				$where[] = 'pers.published = 1';
			} else if ($filter_state == 'U' ) {
				$where[] = 'pers.published = 0';
			}
		}

		$canShowGlobal = JRequest::getVar("showglobal",true);
		$canShowAll = JRequest::getVar("showall",false);
		$user =& JFactory::getUser();
		if (!$canShowAll){
			$where[] = ' (pers.global = 1 OR pers.created_by='.$user->id.')';
		}
		if (!$canShowGlobal){
			$where[] = ' pers.created_by='.$user->id;
		}
		else if ($this->getState("select")){
			$loctype = $this->getState("loctype");
			switch ($loctype){
				case 0:
					$where[] = ' (pers.global = 1 OR pers.created_by='.$user->id.')';
					break;
				case 1;
				$where[] = ' pers.created_by='.$user->id;
				break;
				case 2;
				$where[] = ' pers.global = 1';
				break;
			}
		}

		$where 		= ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );

		return $where;
	}
	
	function _buildPublicContentWhere()
	{
		global $mainframe, $option;
		$db					=& JFactory::getDBO();
		$filter_type		= $mainframe->getUserStateFromRequest( $option.'type_id',				'type_id',			'',				'int' );
		$filter_state		= $mainframe->getUserStateFromRequest( $option.'pers_filter_state',		'filter_state',		'',				'word' );
		$filter_catid		= $mainframe->getUserStateFromRequest( $option.'pers_filter_catid',		'filter_catid',		0,				'int' );
		$filter_order		= $mainframe->getUserStateFromRequest( $option.'pers_filter_order',		'filter_order',		'pers.ordering',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'pers_filter_order_Dir',	'filter_order_Dir',	'',				'word' );
		$search				= $mainframe->getUserStateFromRequest( $option.'pers_search',			'search',			'',				'string' );
		$search				= JString::strtolower( $search );

		$where = array();

		$compparams = JComponentHelper::getParams("com_jevpeople");

		$cats = $compparams->get("jevpcat","");
		if ($cats=="" && $filter_catid > 0) {
			$where[] = '( pers.catid0 = '.(int) $filter_catid
			. ' OR pers.catid1 = '.(int) $filter_catid
			. ' OR pers.catid2 = '.(int) $filter_catid
			. ' OR pers.catid3 = '.(int) $filter_catid
			. ' OR pers.catid4 = '.(int) $filter_catid .")";
		}
		else if ($cats!="" ) {
			if (!is_array($cats)){
				$cats = array($cats);
			}
			// make sure we don't have an empty array
			$cats[] = -1;
			$cats0 = implode(",",$cats);
			$cats = array_diff($cats,array(0));
			$cats = implode(",",$cats);
			// Note we must search for non set first cats only otherwise we'll get them all
			$where[] = '( pers.catid0 IN('.$cats0.')'
			. ' OR pers.catid1 IN('.$cats.')'
			. ' OR pers.catid2 IN('.$cats.')'
			. ' OR pers.catid3 IN('.$cats.')'
			. ' OR pers.catid4 IN('.$cats.'))';
		}

		$types= $compparams->get("type","");
		if ($types==0 && $filter_type > 0) {
			$where[] = ' pers.type_id = '.(int) $filter_type;
		}
		else if ($types!="") {
			if (!is_array($types)){
				$types = array($types);
			}
			// make sure we don't have an empty array
			$types[] = -1;
			$types = implode(",",$types);
			$where[] = ' pers.type_id IN ( '.$types.')';
		}
		
		if ($search) {
			$where[] = ' (LOWER(pers.title) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false )
			.' OR LOWER(pers.city) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false )
			.' OR LOWER(pers.state) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false )
			.' OR LOWER(pers.country) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false )
			. ')';
		}

		if ( $filter_state ) {
			if ( $filter_state == 'P' ) {
				$where[] = 'pers.published = 1';
			} else if ($filter_state == 'U' ) {
				$where[] = 'pers.published = 0';
			}
		}


		$canShowGlobal = JRequest::getVar("showglobal",true);
		$canShowAll = JRequest::getVar("showall",false);
		$user =& JFactory::getUser();
		if (!$canShowAll){
			$where[] = ' (pers.global = 1 OR pers.created_by='.$user->id.')';
		}
		if (!$canShowGlobal){
			$where[] = ' pers.created_by='.$user->id;
		}
		else if ($this->getState("select")){
			$loctype = $this->getState("loctype");
			switch ($loctype){
				case 0:
					$where[] = ' (pers.global = 1 OR pers.created_by='.$user->id.')';
					break;
				case 1;
				$where[] = ' pers.created_by='.$user->id;
				break;
				case 2;
				$where[] = ' pers.global = 1';
				break;
			}
		}

		$where 		= ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );

		return $where;
	}
	
	// VERY CRUDE TEST
	function hasEvents($pers_id, $startdate, $enddate) {
		$db	=& JFactory::getDBO();
		$query = "SELECT count(ev.ev_id) "
		. "\n FROM #__jevents_repetition as rpt"
		. "\n LEFT JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
		. "\n LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
		. "\n LEFT JOIN #__jev_peopleeventsmap AS map ON map.evdet_id=det.evdet_id"
		. "\n WHERE ev.state=1"
		. "\n AND rpt.endrepeat >= '".$startdate."' AND rpt.startrepeat <= '".$enddate."'"
		. "\n AND map.pers_id=$pers_id LIMIT 1";
		$db->setQuery($query);
		return $db->loadResult();
	}
	
	
}
