<?php

/**
 * copyright (C) 2008 GWE Systems Ltd - All rights reserved
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

/**
 * Locations Component Locations Model
 *
 */
class LocationsModelLocations extends JModel
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
		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest($option . '.limitstart', 'limitstart', 0, 'int');

		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);

	}

	/**
	 * Method to get location item data
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
			$db = & JFactory::getDBO();
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
			$db = & JFactory::getDBO();
			echo $db->getErrorMsg();
		}

		return $this->_publicdata;

	}

	/**
	 * Method to get the total number of location items
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
	 * Method to get a pagination object for the locations
	 *
	 * @access public
	 * @return integer
	 */
	function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			global $mainframe;
			if ($mainframe->isAdmin())
			{
				jimport('joomla.html.pagination');
				$this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
			}
			else
			{
				include_once(JPATH_COMPONENT_ADMINISTRATOR . "/libraries/JevPagination.php");
				$this->_pagination = new JevPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'), true);
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
			global $mainframe;
			if ($mainframe->isAdmin())
			{
				jimport('joomla.html.pagination');
				$this->_pagination = new JPagination($this->getPublicTotal(), $this->getState('limitstart'), $this->getState('limit'));
			}
			else
			{
				include_once(JPATH_COMPONENT_ADMINISTRATOR . "/libraries/JevPagination.php");
				$this->_pagination = new JevPagination($this->getPublicTotal(), $this->getState('limitstart'), $this->getState('limit'), true);
			}
		}

		return $this->_pagination;

	}

	function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where = $this->_buildContentWhere();
		$orderby = $this->_buildContentOrderBy();

		$query = ' SELECT loc.*, cc1.title AS c1title, cc2.title AS c2title, cc3.title AS c3title, cat.title as category, u.name AS editor  '
				. ' FROM #__jev_locations AS loc '
				. ' LEFT JOIN #__categories AS cat ON cat.id = loc.loccat '
				. ' LEFT JOIN #__categories AS cat2 ON cat2.id = cat.parent_id '
				. ' LEFT JOIN #__categories AS cc1 ON cc1.id = loc.catid '
				. ' LEFT JOIN #__categories AS cc2 ON cc1.parent_id = cc2.id '
				. ' LEFT JOIN #__categories AS cc3 ON cc2.parent_id = cc3.id '
				. ' LEFT JOIN #__users AS u ON u.id = loc.checked_out'
				. $where
				. $orderby
		;
		return $query;

	}

	function _buildPublicQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where = $this->_buildPublicContentWhere();
		$orderby = $this->_buildContentOrderBy();

		$query = ' SELECT loc.*, cc1.title AS c1title, cc2.title AS c2title, cc3.title AS c3title, cat.title as category, u.name AS editor  ';

// for joomfish
		$query .= ' , cat.id as catid1, cat2.id as catid2, cc1.id as cc1id, cc2.id as cc2id, cc3.id as cc3id ';

		// If we have geocoding of visitors' location then incorporate distance too
		if (JRequest::getFloat("needdistance", 0))
		{
			$lat = JRequest::getFloat("lat", 999);
			$lon = JRequest::getFloat("lon", 999);
			$km = JRequest::getInt("km", 0) ? 1.609344 : 1;

			//	$query .= ",(((acos(sin(($lat*pi()/180)) * sin((loc.geolat*pi()/180))+cos(($lat*pi()/180)) * cos((loc.geolat*pi()/180)) * cos((($lon- loc.geolon)*pi()/180))))*180/pi())*60*1.1515*$km) as distance";
			$query .= ",(((acos(sin(RADIANS($lat)) * sin(RADIANS(loc.geolat))+cos(RADIANS($lat)) * cos(RADIANS(loc.geolat)) * cos((RADIANS($lon- loc.geolon)))))*180/pi())*60*1.1515*$km) as distance";
		}

		$compparams = JComponentHelper::getParams("com_jevlocations");
		$join = "";
		$groupby = "";
		if ($compparams->get("onlywithevents", 0))
		{
			$groupby = "GROUP BY loc.loc_id";

			if (!isset($this->datamodel))
			{
				include_once(JPATH_SITE . "/components/" . JEV_COM_COMPONENT . "/jevents.defines.php");
				$this->datamodel = new JEventsDataModel();

				JPluginHelper::importPlugin('jevents');
			}
			// process the new plugins
			// get extra data and conditionality from plugins
			$extrawhere = array();
			$extrajoin = array();
			$extrafields = "";  // must have comma prefix
			$extratables = "";  // must have comma prefix
			$needsgroup = false;

			$filters = jevFilterProcessing::getInstance(array("published", "justmine", "category", "search"));
			$filters->setWhereJoin($extrawhere, $extrajoin);
			$needsgroup = $filters->needsGroupBy();

			$dispatcher = & JDispatcher::getInstance();
			$dispatcher->trigger('onListIcalEvents', array(& $extrafields, & $extratables, & $extrawhere, & $extrajoin, & $needsgroup));

			$extrajoin = ( count($extrajoin) ? " \n LEFT JOIN " . implode(" \n LEFT JOIN ", $extrajoin) : '' );
			$extrawhere = ( count($extrawhere) ? ' AND ' . implode(' AND ', $extrawhere) : '' );

			$where .= $extrawhere;
			$join = $join . $extrajoin;

			if (strpos($join, "#__jev_locations AS loc") === false)
			{
				//$join .= "\n LEFT JOIN #__jev_locations AS loc on loc.loc_id= det.location";
			}
			$query .= "\n FROM #__jevents_repetition as rpt"
					. "\n LEFT JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
					. "\n LEFT JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid "
					. "\n LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
					. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = rpt.eventid";
			$query .= $join;
			$query .= ' LEFT JOIN #__categories AS cat ON cat.id = loc.loccat '
					. ' LEFT JOIN #__categories AS cat2 ON cat2.id = cat.parent_id '
					. ' LEFT JOIN #__categories AS cc1 ON cc1.id = loc.catid '
					. ' LEFT JOIN #__categories AS cc2 ON cc1.parent_id = cc2.id '
					. ' LEFT JOIN #__categories AS cc3 ON cc2.parent_id = cc3.id '
					. ' LEFT JOIN #__users AS u ON u.id = loc.checked_out'
					. $where
					. $groupby
					. $orderby
			;
		}
		else
		{
			$query .= ' FROM #__jev_locations AS loc ';
			$query .= ' LEFT JOIN #__categories AS cat ON cat.id = loc.loccat '
					. ' LEFT JOIN #__categories AS cat2 ON cat2.id = cat.parent_id '
					. ' LEFT JOIN #__categories AS cc1 ON cc1.id = loc.catid '
					. ' LEFT JOIN #__categories AS cc2 ON cc1.parent_id = cc2.id '
					. ' LEFT JOIN #__categories AS cc3 ON cc2.parent_id = cc3.id '
					. ' LEFT JOIN #__users AS u ON u.id = loc.checked_out'
					. $join
					. $where
					. $groupby
					. $orderby
			;
		}
		return $query;

	}

	function _buildContentOrderBy()
	{
		global $mainframe, $option;

		$filter_order = $mainframe->getUserStateFromRequest($option . 'loc_filter_order', 'filter_order', '', 'cmd');
		$filter_order_Dir = $mainframe->getUserStateFromRequest($option . 'loc_filter_order_Dir', 'filter_order_Dir', '', 'word');

		$compparams = JComponentHelper::getParams("com_jevlocations");
		$usecats = $compparams->get("usecats", 0);
		if (!$usecats)
		{
			if ($filter_order == '')
			{
				if ($compparams->get("deforder", 0) == 0)
				{
					$orderby = ' ORDER BY loc.country, loc.state, loc.city, loc.title ' ;
				}
				else if ($compparams->get("deforder", 0) == 1)
				{
					$orderby = ' ORDER BY loc.title ' ;
				}
				else
				{
					$orderby = ' ORDER BY loc.ordering ' ;
				}
			}
			else
			{
				if ($compparams->get("deforder", 0) == 0)
				{
					$orderby = ' ORDER BY ' . $filter_order . ' ' . $filter_order_Dir . ' ,  loc.country, loc.state, loc.city,  loc.title ASC';
				}
				else if ($compparams->get("deforder", 0) == 1)
				{
					$orderby = ' ORDER BY ' . $filter_order . ' ' . $filter_order_Dir . ' , loc.title ASC';
				}
				else
				{
					$orderby = ' ORDER BY ' . $filter_order . ' ' . $filter_order_Dir . ' , loc.ordering ' ;
				}
			}
		}
		else
		{
			if ($filter_order == '')
			{
				if ($compparams->get("deforder", 0) == 0)
				{
					$orderby = ' ORDER BY c3title,  c2title,  c1title, loc.title ' ;
				}
				else if ($compparams->get("deforder", 0) == 1)
				{
					$orderby = ' ORDER BY loc.title ' ;
				}
				else
				{
					$orderby = ' ORDER BY loc.ordering ' ;
				}
			}
			else
			{
				if ($compparams->get("deforder", 0) == 0)
				{
					$orderby = ' ORDER BY ' . $filter_order . ' ' . $filter_order_Dir . ' ,  c3title,  c2title,  c1title,  loc.title ASC';
				}
				else if ($compparams->get("deforder", 0) == 1)
				{
					$orderby = ' ORDER BY ' . $filter_order . ' ' . $filter_order_Dir . ' , loc.title ASC';
				}
				else
				{
					$orderby = ' ORDER BY ' . $filter_order . ' ' . $filter_order_Dir . ' , loc.ordering ASC';
				}
			}
		}

		// If we have geocoding of visitors' location then use this as the sort algorithm
		if (JRequest::getFloat("sortdistance", 0))
		{
			$orderby = ' ORDER BY distance asc ';
		}

		return $orderby;

	}

	function _buildContentWhere()
	{
		global $mainframe, $option;
		$db = & JFactory::getDBO();
		$filter_state = $mainframe->getUserStateFromRequest($option . 'loc_filter_state', 'filter_state', '', 'word');
		$filter_catid = $mainframe->getUserStateFromRequest($option . 'loc_filter_catid', 'filter_catid', 0, 'int');
		$filter_loccat = $mainframe->getUserStateFromRequest($option . 'loc_filter_loccat', 'filter_loccat', 0, 'int');
		$search = $mainframe->getUserStateFromRequest($option . 'loc_search', 'search', '', 'string');
		$search = JString::strtolower($search);

		$where = array();

		$compparams = JComponentHelper::getParams("com_jevlocations");
		$usecats = $compparams->get("usecats", 0);
		if ($usecats)
		{
			if ($filter_catid > 0)
			{
				$where[] = ' cc1.id = ' . (int) $filter_catid . ' or cc2.id = ' . (int) $filter_catid . ' or cc3.id = ' . (int) $filter_catid;
			}
			if (trim($search) != "")
			{
				$where[] = 'LOWER(loc.title) LIKE ' . $db->Quote('%' . $db->getEscaped($search, true) . '%', false);
			}
		}
		else if (trim($search) != "")
		{
			$where[] = ' (LOWER(loc.title) LIKE ' . $db->Quote('%' . $db->getEscaped($search, true) . '%', false)
					. ' OR LOWER(loc.city) LIKE ' . $db->Quote('%' . $db->getEscaped($search, true) . '%', false)
					. ' OR LOWER(loc.state) LIKE ' . $db->Quote('%' . $db->getEscaped($search, true) . '%', false)
					. ' OR LOWER(loc.country) LIKE ' . $db->Quote('%' . $db->getEscaped($search, true) . '%', false)
					. ')';
		}

		if ($filter_loccat > 0)
		{
			$where[] = ' ( cat.id = ' . (int) $filter_loccat . ' OR cat2.id = ' . (int) $filter_loccat . ')';
		}

		if ($filter_state)
		{
			if ($filter_state == 'P')
			{
				$where[] = 'loc.published = 1';
			}
			else if ($filter_state == 'U')
			{
				$where[] = 'loc.published = 0';
			}
		}

		$canShowGlobal = JRequest::getVar("showglobal", true);
		$canShowAll = JRequest::getVar("showall", false);
		$user = & JFactory::getUser();
		if (!$canShowAll)
		{
			$where[] = ' (loc.global = 1 OR loc.created_by=' . $user->id . ')';
		}
		if (!$canShowGlobal)
		{
			$where[] = ' loc.created_by=' . $user->id;
		}
		else if ($this->getState("select"))
		{
			$loctype = $this->getState("loctype");
			switch ($loctype) {
				case 0:
					if (!intval($compparams->get("selectfromall", 0)))
						$where[] = ' (loc.global = 1 OR loc.created_by=' . $user->id . ')';
					break;
				case 1;
					$where[] = ' loc.created_by=' . $user->id;
					break;
				case 2;
					$where[] = ' loc.global = 1';
					break;
			}
		}

		$cityfilter = $compparams->get("cityfilter", "");
		if ($cityfilter != "")
		{
			$cityfilters = explode(",", $cityfilter);
			foreach ($cityfilters as &$cfilter)
			{
				$cfilter = $db->Quote($db->getEscaped(trim($cfilter), true), false);
				unset($cfilter);
			}
			$cityfilter = implode(",", $cityfilters);
			$where[] = "loc.city IN (" . $cityfilter . ")";
		}

		$statefilter = $compparams->get("statefilter", "");
		if ($statefilter != "")
		{
			$statefilters = explode(",", $statefilter);
			foreach ($statefilters as &$sfilter)
			{
				$sfilter = $db->Quote($db->getEscaped(trim($sfilter), true), false);
				unset($sfilter);
			}
			$statefilter = implode(",", $statefilters);
			$where[] = "loc.state IN (" . $statefilter . ")";
		}

		// filtering on priority/featured level
		if (JRequest::getInt("jlpriority_fv", 0) > 0)
		{
			$where[] = "loc.priority>=" . JRequest::getInt("jlpriority_fv", 0);
		}


		$where = ( count($where) ? ' WHERE ' . implode(' AND ', $where) : '' );

		return $where;

	}

	function _buildPublicContentWhere()
	{
		global $mainframe, $option;
		$db = & JFactory::getDBO();
		$filter_state = $mainframe->getUserStateFromRequest($option . 'loc_filter_state', 'filter_state', '', 'word');
		$filter_catid = $mainframe->getUserStateFromRequest($option . 'loc_filter_catid', 'filter_catid', 0, 'int');
		$filter_loccat = $mainframe->getUserStateFromRequest($option . 'loc_filter_loccat', 'filter_loccat', 0, 'int');
		$search = $mainframe->getUserStateFromRequest($option . 'loc_search', 'search', '', 'string');
		$search = JString::strtolower($search);

		$where = array();

		$compparams = JComponentHelper::getParams("com_jevlocations");
		$usecats = $compparams->get("usecats", 0);
		if ($usecats)
		{
			if ($filter_catid > 0)
			{
				$where[] = ' cc1.id = ' . (int) $filter_catid . ' or cc2.id = ' . (int) $filter_catid . ' or cc3.id = ' . (int) $filter_catid;
			}
			if (trim($search) != "")
			{
				//$where[] = '(LOWER(loc.title) LIKE ' . $db->Quote('%' . $db->getEscaped($search, true) . '%', false) . ' OR LOWER(loc.description) LIKE ' . $db->Quote('%' . $db->getEscaped($search, true) . '%', false).')';
				$where[] = '(LOWER(loc.title) LIKE ' . $db->Quote('%' . $db->getEscaped($search, true) . '%', false);
			}
		}
		else if (trim($search) != "")
		{
			$where[] = ' (LOWER(loc.title) LIKE ' . $db->Quote('%' . $db->getEscaped($search, true) . '%', false)
					. ' OR LOWER(loc.city) LIKE ' . $db->Quote('%' . $db->getEscaped($search, true) . '%', false)
					. ' OR LOWER(loc.state) LIKE ' . $db->Quote('%' . $db->getEscaped($search, true) . '%', false)
					. ' OR LOWER(loc.country) LIKE ' . $db->Quote('%' . $db->getEscaped($search, true) . '%', false)
					//. ' OR LOWER(loc.description) LIKE ' . $db->Quote('%' . $db->getEscaped($search, true) . '%', false)
					. ')';
		}

		if ($filter_loccat > 0)
		{
			$where[] = ' ( cat.id = ' . (int) $filter_loccat . ' OR cat2.id = ' . (int) $filter_loccat . ')';
		}

		$where[] = 'loc.published = 1';
		$user = & JFactory::getUser();
		if ($compparams->get("onlyglobal", 1))
			$where[] = ' (loc.global = 1 OR loc.created_by=' . $user->id . ')';

		$cityfilter = $compparams->get("cityfilter", "");
		if ($cityfilter != "")
		{
			$cityfilters = explode(",", $cityfilter);
			foreach ($cityfilters as &$cfilter)
			{
				$cfilter = $db->Quote($db->getEscaped(trim($cfilter), true), false);
				unset($cfilter);
			}
			$cityfilter = implode(",", $cityfilters);
			$where[] = "loc.city IN (" . $cityfilter . ")";
		}

		$statefilter = $compparams->get("statefilter", "");
		if ($statefilter != "")
		{
			$statefilters = explode(",", $statefilter);
			foreach ($statefilters as &$sfilter)
			{
				$sfilter = $db->Quote($db->getEscaped(trim($sfilter), true), false);
				unset($sfilter);
			}
			$statefilter = implode(",", $statefilters);
			$where[] = "loc.state IN (" . $statefilter . ")";
		}

		$catfilters = $compparams->get("catfilter", "");
		if (is_array($catfilters))
		{
			JArrayHelper::toInteger($catfilters);
			$catfilters = implode(",", $catfilters);
			$where[] = "loc.loccat IN (" . $catfilters . ")";
		}
		else if ($catfilters != "")
		{
			$catfilters = intval($catfilters);
			$where[] = "loc.loccat IN (" . $catfilters . ")";
		}


		// filtering on priority/featured level
		if (JRequest::getInt("jlpriority_fv", 0) > 0)
		{
			$where[] = "loc.priority>=" . JRequest::getInt("jlpriority_fv", 0);
		}

		if ($compparams->get("onlywithevents", 0))
		{

			jimport("joomla.utilities.date");
			$startdate = new JDate("-" . $compparams->get("checkeventbefore", 30) . " days");
			$enddate = new JDate("+" . $compparams->get("checkeventafter", 30) . " days");

			$startdate = $startdate->toMySQL();
			$enddate = $enddate->toMySQL();

			$where[] = "ev.state=1";
			$where[] = " rpt.endrepeat >= '" . $startdate . "' AND rpt.startrepeat <= '" . $enddate . "'";
		}

		/*
		  $withevents = $compparams->get("withevents",0);
		  if ($withevents){
		  $where[] = 1;
		  }
		 */
		$where = ( count($where) ? ' WHERE ' . implode(' AND ', $where) : '' );

		return $where;

	}

	// VERY CRUDE TEST
	function hasEvents($loc_id, $startdate, $enddate)
	{
		$db = & JFactory::getDBO();
		global $option;
		$params = JComponentHelper::getParams($option);
		if ($params->get("ignorefiltermodule", 1))
		{
			$query = "SELECT count(ev.ev_id) "
					. "\n FROM #__jevents_repetition as rpt"
					. "\n LEFT JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
					. "\n LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
					. "\n WHERE ev.state=1"
					. "\n AND rpt.endrepeat >= '" . $startdate . "' AND rpt.startrepeat <= '" . $enddate . "'"
					. "\n AND det.location=$loc_id LIMIT 1";
			$db->setQuery($query);
			return $db->loadResult();
		}
		else
		{
			if (!isset($this->datamodel))
			{
				include_once(JPATH_SITE . "/components/" . JEV_COM_COMPONENT . "/jevents.defines.php");
				$this->datamodel = new JEventsDataModel();

				JPluginHelper::importPlugin('jevents');
			}

			$user = JFactory::getUser();

			$db = & JFactory::getDBO();

			// process the new plugins
			// get extra data and conditionality from plugins
			$extrawhere = array();
			$extrajoin = array();
			$extrafields = "";  // must have comma prefix
			$extratables = "";  // must have comma prefix
			$needsgroup = false;

			$filters = jevFilterProcessing::getInstance(array("published", "justmine", "category", "search"));
			$filters->setWhereJoin($extrawhere, $extrajoin);
			$needsgroup = $filters->needsGroupBy();

			$dispatcher = & JDispatcher::getInstance();
			$dispatcher->trigger('onListIcalEvents', array(& $extrafields, & $extratables, & $extrawhere, & $extrajoin, & $needsgroup));

			$extrajoin = ( count($extrajoin) ? " \n LEFT JOIN " . implode(" \n LEFT JOIN ", $extrajoin) : '' );
			$extrawhere = ( count($extrawhere) ? ' AND ' . implode(' AND ', $extrawhere) : '' );

			$query = "SELECT count(ev.ev_id)";
			$query .= "\n FROM #__jevents_repetition as rpt"
					. "\n LEFT JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
					. "\n LEFT JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid "
					. "\n LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
					. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = rpt.eventid"
					. $extrajoin
					. "\n WHERE ev.catid IN(" . $this->datamodel->accessibleCategoryList() . ")"
					. "\n AND rpt.endrepeat >= '$startdate' AND rpt.startrepeat <= '$enddate'"

					// Must suppress multiday events that have already started
					. "\n AND NOT (rpt.startrepeat < '$startdate' AND det.multiday=0) "
					. $extrawhere
					. "\n AND ev.access <= " . $user->aid
					. "  AND icsf.state=1 AND icsf.access <= " . $user->aid
					. "\n AND det.location=$loc_id LIMIT 1"
			;
			$db->setQuery($query);
			return $db->loadResult();
		}

	}

}
