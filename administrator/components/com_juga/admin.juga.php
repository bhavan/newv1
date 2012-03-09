<?php
// ensure this file is being included by a parent file
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

// ensure user has access to this function
if (!($acl->acl_check('administration', 'edit', 'users', $my->usertype, 'components', 'all') || $acl->acl_check('administration', 'edit', 'users', $my->usertype, 'components', 'com_groups'))) {
    mosRedirect('index2.php', _NOT_AUTH);
}

JHTML::stylesheet('juga.css', $mosConfig_live_site."/administrator/components/com_juga/includes/css/");

if (file_exists($mosConfig_absolute_path.'/administrator/components/com_juga/languages/'.$mosConfig_lang.'.php')) {
      include($mosConfig_absolute_path.'/administrator/components/com_juga/languages/'.$mosConfig_lang.'.php');
} else {
      include($mosConfig_absolute_path.'/administrator/components/com_juga/languages/english.php');
}

// JUGA Application Helpers
require_once( JApplicationHelper::getPath('admin_html') );
require_once( JApplicationHelper::getPath('class') );

$cid = josGetArrayInts( 'cid' );

if (!isset($section)) {
	$section = JRequest::getVar( "section", JRequest::getVar( "section", "groups", "GET" ), "POST");
}

if (!isset($task)) {
	$task = JRequest::getVar( "task", JRequest::getVar( "task", "", "GET" ), "POST");
}

if (!isset($id)) {
	$id = JRequest::getVar( "id", JRequest::getVar( "id", "", "GET" ), "POST");
}
	
// no matter what, display juga header
HTML_juga::juga_Header();

// no matter what, run basic checks on system
basicChecks( );
	
switch($section) {
  case "tools":
  	switch ($task) {
	  case "patch":
	    toolPatch($option, $section);
	    break;
	  default:
	    HTML_juga::listTools( $option, $section );
	    break;
	}
    break;
	
  case 'config':
    switch ($task) {
      case 'new': case 'add':
        editConfig($option, $section, 0);
        break;
      case 'save':
        saveConfig( $option, $section, $id );
        break;
      case 'edit':
        editConfig($option, $section, $cid[0]);
        break;
      case 'editA':
        editConfig($option, $section, $id);
        break;
      case 'remove':
        removeConfig( $cid, $option, $section );
        break;
      default:
        listConfig($option, $section);
        break;
    }
    break;

  case 'groups':
    switch ($task) {
      case 'new': case 'add':
        editGroup($option, $section, 0);
        break;
      case 'save':
		saveGroup( $option, $section );
        break;
      case 'edit':
		editGroup($option, $section, $cid[0]);
        break;
      case 'editA':
		editGroup($option, $section, $id);
        break;
      case 'remove':
		removeGroups( $cid, $option, $section );
        break;
      default:
        listGroups($option, $section);
        break;
    }
    break; // end case groups

  case 'codes':
    switch ($task) {
      case 'new': case 'add':
        editCode($option, $section, 0);
        break;
      case 'save':
		saveCode( $option, $section );
        break;
      case 'edit':
		editCode($option, $section, $cid[0]);
        break;
      case 'editA':
		editCode($option, $section, $id);
        break;
	  case "publish":
	    publishCodes( $cid, 1, $option, $section );
	    break;
	  case "unpublish":
	    publishCodes( $cid, 0, $option, $section );
	    break;
      case 'remove':
		removeCodes( $cid, $option, $section );
        break;
      default:
        listCodes($option, $section);
        break;
    }
    break; // end case codes

  case 'items':
      switch ($task) {
        case 'new': case 'add':
          editItem($option, $section, 0);
          break;
        case 'edit':
		  editItem($option, $section, $cid[0]);
          break;
        case 'editA':
		  editItem($option, $section, $id);
          break;
        case 'save':
		  saveItem( $option, $section );
          break;
        case "publish":
          publishItemURL( $cid, 1, $option, $section );
		  break;
        case "unpublish":
          publishItemURL( $cid, 0, $option, $section );
		  break;
        case "sync_items":
          synchronizeItems ($option, $section);
          break;
        case "enroll":
          enrollItems ($option, $section, $cid);
          break;
        case "withdraw":
          withdrawItems ($option, $section, $cid);
          break;
        case "enroll_default":
          defaultItems( $cid, $option, $section );
          break;
        case "ce_default":
          defaultCEItems( $cid, $option, $section );
          break;
        case "ce_remove":
          removeCEItems( $cid, $option, $section );
          break;
        case "withdraw_all":
          withdrawAllItems( $cid, $option, $section );
          break;
        case "enroll_flex":
          flexItems( $cid, $option, $section );
          break;
        case "withdraw_flex":
          unflexItems( $cid, $option, $section );
          break;
      	case 'remove':
		  removeItems( $cid, $option, $section );
          break;
      	case 'switch_inclusion':
		  switchIncludeItems( $cid, $option, $section );
          break;
      	case 'exclude':
		  // removeItems( $cid, $option, $section );
          break;
      	case 'include':
		  // removeItems( $cid, $option, $section );
          break;
		default:
          listItems($option, $section);
          break;
      } // end task
    break;

  case 'u2g':
      switch ($task) {
        case "enroll":
          enrollUsers ($option, $section, $cid);
          break;
        case "withdraw":
          withdrawUsers ($option, $section, $cid);
          break;
        case "enroll_default":
          defaultUsers( $cid, $option, $section );
          break;
      	case 'remove':
		  removeUsers( $cid, $option, $section );
          break;
		default:
          listUsers($option, $section);
          break;
      } // end task
    break;

  case 'i2g':
      switch ($task) {
        case "cancel":
          // mosRedirect("index2.php?option=com_juga&section=items");
		  $mainframe->redirect( "index2.php?option=com_juga&section=items" );
          break;
        case "switch":
          switchItem2Groups ($option, $section, $cid);
          break;
		default:
          defineItem2Groups($option, $section);
          break;
      } // end task
    break;

  case 'g2u':
      switch ($task) {
        case "cancel":
          // mosRedirect("index2.php?option=com_juga&section=u2g");
		  $mainframe->redirect( "index2.php?option=com_juga&section=u2g" );
          break;
        case "switch":
          switchUser2Groups ($option, $section, $cid);
          break;
		default:
          defineUser2Groups($option, $section);
          break;
      } // end task
    break;
	
  default:
    listGroups($option, $section);
    break;

} // end switch section
// ************************************************************************

/**
* perform basic checks
* @param database A database connector object
*/
// ************************************************************************
function basicChecks( ) {
	global $database, $mainframe, $mosConfig_list_limit, $mosConfig_live_site;

	// if the field site_section or site_view doesn't exist in juga_items, echo message to run patch
		$query = "SHOW COLUMNS FROM #__juga_items "
		. "\n LIKE 'site_section' "
		;
		$database->setQuery( $query );
		$site_section = $database->loadObjectList();
		if ($database->getErrorNum()) {
			echo $database->stderr();
			return false;
		}
		
		if (!$site_section) {
			echo "<p style='color:#FFCC00;'>No column of the name `site_section`.</p>";
			$query = "ALTER TABLE #__juga_items ADD `site_section` VARCHAR(255) NOT NULL;"
			;
			$database->setQuery( $query );
			if (!$database->query()) {
				echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
				exit();
			}
			echo "<p style='color:#00CC00;' >Column `site_section` created in #__juga_items. </p>";		
		}		
		
		$query = "SHOW COLUMNS FROM #__juga_items "
		. "\n LIKE 'site_view' "
		;
		$database->setQuery( $query );
		$site_view = $database->loadObjectList();
		if ($database->getErrorNum()) {
			echo $database->stderr();
			return false;
		}
		
		if (!$site_view) {
			echo "<p style='color:#FFCC00;'>No column of the name `site_view`.</p>";
			$query = "ALTER TABLE #__juga_items ADD `site_view` VARCHAR(255) NOT NULL;"
			;
			$database->setQuery( $query );
			if (!$database->query()) {
				echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
				exit();
			}
			echo "<p style='color:#00CC00;' >Column `site_view` created in #__juga_items. </p>";		
		} 		
	
		//		if (!site_section || !$site_view) {
		//			$lists["alert"] = _JUGA_RUNPATCH_MESSAGE;
		//			HTML_juga::standardMessage ( $option, $section, &$row, &$lists, &$search, &$pageNav );
		//		}		
	
	// basic checks
	unset($data);
	$query = "	
		SELECT * FROM #__juga_items 
		WHERE `site_option` = '' 
		AND `site_section` = '' 
		AND `site_view` = '' 
		AND `site_task` = '' 
		AND `type` = 'com'
		"
		;
	$database->setQuery( $query );
	$database->loadObject($data);
		if (!$data) {
			$query = "	
				INSERT INTO #__juga_items
				SET `title` = 'Joomla! 1.5 Homepage', `site_option` = '', `type` = 'com', `option_exclude` = '1' 
				"
				;
			$database->setQuery( $query );
			$database->query();
			if ($database->getErrorNum()) {
				echo $database->stderr();
				return false;
			}
		} elseif ($data->option_exclude != '1') {
			// set exclude = '1' for frontpage to prevent endless loops
			$query = "	
				UPDATE #__juga_items
				SET `option_exclude` = '1' 
				WHERE `id` = '".$data->id."' 
				"
				;			
			$database->setQuery( $query );
			$database->query();
		}
			
	// basic checks
	unset($data);
	$query = "	
		SELECT * FROM #__juga_items 
		WHERE `site_option` = 'com_frontpage' 
		AND `site_section` = '' 
		AND `site_view` = '' 
		AND `site_task` = '' 
		AND `type` = 'com'
		"
		;
	$database->setQuery( $query );
	$database->loadObject($data);
		if (!$data) {
			$query = "	
				INSERT INTO #__juga_items
				SET `title` = 'Frontpage', `site_option` = 'com_frontpage', `type` = 'com', `option_exclude` = '1' 
				"
				;
			$database->setQuery( $query );
			$database->query();
			if ($database->getErrorNum()) {
				echo $database->stderr();
				return false;
			}
		} elseif ($data->option_exclude != '1') {
			// set exclude = '1' for frontpage to prevent endless loops
			$query = "	
				UPDATE #__juga_items
				SET `option_exclude` = '1' 
				WHERE `id` = '".$data->id."' 
				"
				;			
			$database->setQuery( $query );
			$database->query();
		}

	// basic checks
	unset($data);
	$query = "	
		SELECT * FROM #__juga_items 
		WHERE `site_option` = 'com_login' 
		AND `site_section` = '' 
		AND `site_view` = '' 
		AND `site_task` = '' 
		AND `type` = 'com'
		"
		;
	$database->setQuery( $query );
	$database->loadObject($data);
		if (!$data) {
			$query = "	
				INSERT INTO #__juga_items
				SET `title` = 'Login', `site_option` = 'com_login', `type` = 'com', `option_exclude` = '1' 
				"
				;
			$database->setQuery( $query );
			$database->query();
			if ($database->getErrorNum()) {
				echo $database->stderr();
				return false;
			}
		} elseif ($data->option_exclude != '1') {
			// set exclude = '1' for login to prevent endless loops
			$query = "	
				UPDATE #__juga_items
				SET `option_exclude` = '1' 
				WHERE `id` = '".$data->id."' 
				"
				;			
			$database->setQuery( $query );
			$database->query();
		}
		
	// basic checks
	unset($data);
	$query = "	
		SELECT * FROM #__juga_items 
		WHERE `site_option` = 'com_content' 
		AND `site_section` = '' 
		AND `site_view` = '' 
		AND `site_task` = '' 
		AND `type` = 'com'
		"
		;
	$database->setQuery( $query );
	$database->loadObject($data);
		if (!$data) {
			$query = "	
				INSERT INTO #__juga_items
				SET `title` = 'Content Component', `site_option` = 'com_content', `type` = 'com' 
				"
				;
			$database->setQuery( $query );
			$database->query();
			if ($database->getErrorNum()) {
				echo $database->stderr();
				return false;
			}
		}

	// basic checks
	unset($data);
	$query = "	
		SELECT * FROM #__juga_groups "
		;
	$database->setQuery( $query );
	$database->loadObject($data);
		if (!isset($data->id)) {
			$newJugaGroup = new jugaGroup( $database );
			$newJugaGroup->title			= "Public Access";
			$newJugaGroup->description		= "The default JUGA group for Public Access.";
			$newJugaGroup->store();
		}	


	// basic checks
	unset($data);
	$query = " SELECT * FROM #__juga WHERE `title` = 'juga_superusergroup' "
		;
	$database->setQuery( $query );
	$database->loadObject($data);
		if (!isset($data->id)) {
			$newJugaConfig = new jugaConfig( $database );
			$newJugaConfig->title			= "juga_superusergroup";
			$newJugaConfig->description		= "The JUGA Superuser Group";
			$newJugaConfig->value			= "-1";
			$newJugaConfig->store();
		}	
		
	// basic checks
	unset($data);
	$query = "	
		SELECT * FROM #__juga WHERE `title` = 'flex_juga' "
		;
	$database->setQuery( $query );
	$database->loadObject($data);
		if (!isset($data->id)) {
			$newJugaConfig = new jugaConfig( $database );
			$newJugaConfig->title			= "flex_juga";
			$newJugaConfig->description		= "Use this JUGA group for quickly assigning many users and items access";
			$newJugaConfig->value			= "1";
			$newJugaConfig->store();
		}	

	// basic checks
	unset($data);
	$query = "	
		SELECT * FROM #__juga WHERE `title` = 'default_ce' "
		;
	$database->setQuery( $query );
	$database->loadObject($data);
		if (!isset($data->id)) {
			$newJugaConfig = new jugaConfig( $database );
			$newJugaConfig->title			= "default_ce";
			$newJugaConfig->description		= "The default custom error url.";
			$newJugaConfig->value			= $mosConfig_live_site;
			$newJugaConfig->store();
		}	

	// basic checks
	unset($data);
	$query = "	
		SELECT * FROM #__juga WHERE `title` = 'default_juga' "
		;
	$database->setQuery( $query );
	$database->loadObject($data);
		if (!isset($data->id)) {
			$newJugaConfig = new jugaConfig( $database );
			$newJugaConfig->title			= "default_juga";
			$newJugaConfig->description		= "The default JUGA group.  Usually is best as a Public Group or something along those lines...";
			$newJugaConfig->value			= "1";
			$newJugaConfig->store();
		}	

	// basic checks
	unset($data);
	$query = "	
		SELECT * FROM #__juga WHERE `title` = 'default_juga_admin' "
		;
	$database->setQuery( $query );
	$database->loadObject($data);
		if (!isset($data->id)) {
			$newJugaConfig = new jugaConfig( $database );
			$newJugaConfig->title			= "default_juga_admin";
			$newJugaConfig->description		= "The default JUGA admin group, used for new Admin-side Items.  Is often the Administrator group.";
			$newJugaConfig->value			= "-1";
			$newJugaConfig->store();
		}	
			
} // end basicChecks
// ************************************************************************


/**
* Compiles a list of records
* @param database A database connector object
*/
// ************************************************************************
function listConfig($option, $section) {
	global $database, $mainframe, $mosConfig_list_limit;

	$where = array();
	$lists = array();
	
	// $catid 		= intval( $mainframe->getUserStateFromRequest( "catid{$option}", 'catid', 0 ) );
	$limit 		= intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );
	$limitstart = intval( $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 ) );
	$search 	= $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
	if (get_magic_quotes_gpc()) {
		$search	= stripslashes( $search );
	}

	if ($search) {
		$where[] = "LOWER(title) LIKE '%" . $database->getEscaped( trim( strtolower( $search ) ) ) . "%'";
	}

	$query = "SELECT COUNT(*) FROM #__juga "
	. (count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "")
	;

	$database->setQuery( $query );
	$total = $database->loadResult();

	require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );

	$query = "SELECT * FROM #__juga "
	. (count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "")
	. " ORDER BY `title` ASC "
	;
	$database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );

	$rows = $database->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );

    HTML_juga::listConfig($option, $section, $rows, $lists, $search, $pageNav);
    return true;
}
// ************************************************************************

/**
* Compiles information to add or edit
* @param integer The unique id of the record to edit (0 if new)
*/
// ************************************************************************
function editConfig( $option, $section, $id ) {
	global $database, $mainframe, $mosConfig_list_limit;

	$lists = array();

	$row = new jugaConfig( $database );
	// load the row from the db table
	$row->load( (int)$id );

	// fail if checked out not by 'me'
	if ($row->isCheckedOut( $my->id )) {
		mosRedirect( 'index2.php?option='. $option, 'The module $row->title is currently being edited by another administrator.' );
	}

	if ($id) {
		$row->checkout( $my->id );
	} else {
		// initialise new record
		// $row->published = 1;
		// $row->approved 	= 1;
		// $row->order 	= 0;
		// $row->catid 	= intval( mosGetParam( $_POST, 'catid', 0 ) );
		// NOTHING TO INIT
	}

	HTML_juga::editConfig( $row, $lists, $params, $option, $section);
}
// ************************************************************************

/**
* Saves the record on an edit form submit
* @param database A database connector object
*/
function saveConfig( $option, $section ) {
	global $database, $my;

	$row = new jugaConfig( $database );
	if (!$row->bind( $_POST )) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}

	if (!$row->check()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	if (!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	$row->checkin();

	mosRedirect( "index2.php?option=$option&section=$section" );
}
// ************************************************************************

/**
* Deletes one or more records
* @param array An array of unique category id numbers
* @param string The current url option
*/
function removeConfig( $cid, $option, $section ) {
	global $database;

	if (!is_array( $cid ) || count( $cid ) < 1) {
		echo "<script> alert('Select an item to delete'); window.history.go(-1);</script>\n";
		exit;
	}
	if (count( $cid )) {
		mosArrayToInts( $cid );
		$cids = 'id=' . implode( ' OR id=', $cid );
		$query = "DELETE FROM #__juga "
		. "\n WHERE ( $cids )"
		;
		$database->setQuery( $query );
		if (!$database->query()) {
			echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		}
	}

	mosRedirect( "index2.php?option=$option&section=$section" );
}
// ************************************************************************

/**
* Compiles a list of records
* @param database A database connector object
*/
// ************************************************************************
function listGroups($option, $section) {
	global $database, $mainframe, $mosConfig_list_limit;

	$where = array();
	$lists = array();
	
	// $catid 		= intval( $mainframe->getUserStateFromRequest( "catid{$option}", 'catid', 0 ) );
	$limit 		= intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );
	$limitstart = intval( $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 ) );
	$search 	= $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
	if (get_magic_quotes_gpc()) {
		$search	= stripslashes( $search );
	}

	if ($search) {
		$where[] = "LOWER(title) LIKE '%" . $database->getEscaped( trim( strtolower( $search ) ) ) . "%'";
	}

	// get the total number of records
	$query = "SELECT COUNT(*) "
	." FROM #__juga_groups "
	. (count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "");

	$database->setQuery( $query );
	$total = $database->loadResult();
	
	require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );

	$query = "SELECT * FROM #__juga_groups "
	. (count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "")
	. " ORDER BY `title` ASC "
	;
	$database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );

	$rows = $database->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );

    HTML_juga::listGroups($option, $section, $rows, $lists, $search, $pageNav);
    return true;
}
// ************************************************************************


/**
* Compiles information to add or edit
* @param integer The unique id of the record to edit (0 if new)
*/
// ************************************************************************
function editGroup( $option, $section, $id ) {
	global $database, $mainframe, $mosConfig_list_limit;

	$lists = array();

	$row = new jugaGroup( $database );
	// load the row from the db table
	$row->load( (int)$id );

	// fail if checked out not by 'me'
	if ($row->isCheckedOut( $my->id )) {
		mosRedirect( 'index2.php?option='. $option, 'The module $row->title is currently being edited by another administrator.' );
	}

	if ($id) {
		$row->checkout( $my->id );
	} else {
		// initialise new record
		// $row->published = 1;
		// $row->approved 	= 1;
		// $row->order 	= 0;
		// $row->catid 	= intval( mosGetParam( $_POST, 'catid', 0 ) );
		// NOTHING TO INIT
	}

	HTML_juga::editGroup( $row, $lists, $params, $option, $section);
}
// ************************************************************************

/**
* Saves the record on an edit form submit
* @param database A database connector object
*/
function saveGroup( $option, $section ) {
	global $database, $my;

	$row = new jugaGroup( $database );
	if (!$row->bind( $_POST )) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}

	if (!$row->check()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	if (!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	$row->checkin();

	mosRedirect( "index2.php?option=$option&section=$section" );
}
// ************************************************************************

/**
* Deletes one or more records
* @param array An array of unique category id numbers
* @param string The current url option
*/
function removeGroups( $cid, $option, $section ) {
	global $database;

	if (!is_array( $cid ) || count( $cid ) < 1) {
		echo "<script> alert('Select an item to delete'); window.history.go(-1);</script>\n";
		exit;
	}
	if (count( $cid )) {
		mosArrayToInts( $cid );
		$cids = 'id=' . implode( ' OR id=', $cid );
		$query = "DELETE FROM #__juga_groups "
		. "\n WHERE ( $cids )"
		;
		$database->setQuery( $query );
		$database->query();

		// g2i
		$cids = 'group_id=' . implode( ' OR group_id=', $cid );
		$query = "DELETE FROM #__juga_g2i "
		. "\n WHERE ( $cids )"
		;
		$database->setQuery( $query );
		$database->query();

		// u2g
		$cids = 'group_id=' . implode( ' OR group_id=', $cid );
		$query = "DELETE FROM #__juga_u2g "
		. "\n WHERE ( $cids )"
		;
		$database->setQuery( $query );
		$database->query();

		/* if (!$database->query()) {
			echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		}
		*/
	}

	mosRedirect( "index2.php?option=$option&section=$section" );
}
// ************************************************************************

/**
* Compiles a list of records
* @param database A database connector object
*/
// ************************************************************************
function listItems($option, $section) {
	global $database, $mainframe, $mosConfig_list_limit;

	// echo "<pre>"; print_r($_REQUEST); echo "</pre>";
	// $catid 		= intval( $mainframe->getUserStateFromRequest( "catid{$option}", 'catid', 0 ) );
	$group_id	= intval( $mainframe->getUserStateFromRequest( "group_id{$option}", 'group_id') );
	$type 		= $mainframe->getUserStateFromRequest( "type{$option}", 'type');
	$limit 		= intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );
	$limitstart = intval( $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 ) );
	$search 	= $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
	if (get_magic_quotes_gpc()) {
		$search	= stripslashes( $search );
	}

	$where = array();

	if ($group_id) {
		$where[] = "`group_id` = '$group_id'";

		if ($search) {
			$where[] = "LOWER(title) LIKE '%" . $database->getEscaped( trim( strtolower( $search ) ) ) . "%'"
					 . " OR LOWER(type_id) LIKE '%" . $database->getEscaped( trim( strtolower( $search ) ) ) . "%'"
					 . " OR LOWER(site_option) LIKE '%" . $database->getEscaped( trim( strtolower( $search ) ) ) . "%'"
					 . " OR LOWER(site_task) LIKE '%" . $database->getEscaped( trim( strtolower( $search ) ) ) . "%'";
		}

		// get the total number of records
		$query = "SELECT COUNT(*) "
		." FROM #__juga_g2i "
		. (count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "")
		;
		$database->setQuery( $query );
		$total = $database->loadResult();

		require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
		$pageNav = new mosPageNav( $total, $limitstart, $limit  );

		$query = "SELECT #__juga_items.* "
		." FROM #__juga_items "
		." LEFT JOIN #__juga_g2i ON #__juga_g2i.item_id = #__juga_items.id "
		. (count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "")
	    ." ORDER BY `title` "
		;

	}
	else {

		if ($type != '') {
			$where[] = "`type` LIKE '$type'";
		}
		if ($search) {
			$where[] = "LOWER(title) LIKE '%" . $database->getEscaped( trim( strtolower( $search ) ) ) . "%'"
					 . " OR LOWER(type_id) LIKE '%" . $database->getEscaped( trim( strtolower( $search ) ) ) . "%'"
					 . " OR LOWER(site_option) LIKE '%" . $database->getEscaped( trim( strtolower( $search ) ) ) . "%'"
					 . " OR LOWER(site_task) LIKE '%" . $database->getEscaped( trim( strtolower( $search ) ) ) . "%'";
		}

		// get the total number of records
		$query = "SELECT COUNT(*)"
		. "\n FROM #__juga_items "
		. (count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "")
		;
		$database->setQuery( $query );
		$total = $database->loadResult();

		require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
		$pageNav = new mosPageNav( $total, $limitstart, $limit  );

		$query = "SELECT * FROM #__juga_items "
		. (count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "")
	    ." ORDER BY `title` "
		;
	} // end else

	$database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );

	$rows = $database->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	// build list of types
	$types = array
			(
			  0 => mosHTML::makeOption( '', '- Select Type -' ),
			  1 => mosHTML::makeOption( 'cont', 'Content' ),
			  2 => mosHTML::makeOption( 'com', 'Component' ),
			  3 => mosHTML::makeOption( 'mod', 'Module' )
			);
	$javascript 	= 'onchange="document.adminForm.submit();"';
	$lists['type'] = mosHTML::selectList( $types, "type", "size='1' $javascript", "value", "text", $type );

	$database->setQuery("SELECT * FROM #__juga_groups "
						." ORDER BY `title` ASC");
	$db_groups = $database->loadObjectList();

	unset($types);
	$types[] = mosHTML::makeOption( '', '- Select Group -' );
	if ($db_groups) {
	  foreach ($db_groups as $dbg) {
	    $types[] = mosHTML::makeOption( $dbg->id, $dbg->title );
	  }
	}

	$javascript 	= 'onchange="document.adminForm.submit();"';
	$lists['group_id'] = mosHTML::selectList( $types, "group_id", "size='1' $javascript", "value", "text", $group_id );

	unset($types);
	 $database->setQuery("SELECT #__juga_groups.* FROM #__juga_groups "
	 					 ." LEFT JOIN #__juga ON #__juga.value = #__juga_groups.id "
                         ." WHERE #__juga.title = 'flex_juga' "
						 );
     $flex_juga = $database->loadObjectList();
	 $lists['flex_juga'] = &$flex_juga[0];
	
    HTML_juga::listItems($option, $section, $rows, $lists, $search, $pageNav);
    return true;
}
// ************************************************************************

/**
* Compiles a list of records
* @param database A database connector object
*/
// ************************************************************************

function synchronizeItems ( $option, $section) {
	global $database;
     $database->setQuery("SELECT * FROM #__juga_items"
                         ." ORDER BY id");
     $jugaitems = $database->loadObjectList();

	// *********************************************************************************
	// BEGIN COMPONENT SYNC
	echo "<br>"._juga_syncron_comps." ";

     $database->setQuery("SELECT DISTINCT ( `option` ), `name`, `id` FROM #__components"
     					." WHERE `option` != ' ' AND `parent` = '0'"
                         ." ORDER BY `id` ASC");
    $db_components = $database->loadObjectList();

	$cnt_new = 0;
     foreach ($db_components as $db_component)
     {
     	$found = false;
     	if ( count($db_components > 0))
     	{
	     	foreach ($jugaitems as $jugaitem)
   		  	{
				if ( $jugaitem->site_option == $db_component->option )
				{
					$found = true;
				}
     		}
    	 }
    	 if ($found == false)
    	 {
			$newJugaItem = new jugaItem( $database );
			$newJugaItem->title			= $db_component->name;
			$newJugaItem->site_option	= $db_component->option;
			$newJugaItem->type			= "com";
			$newJugaItem->store();
			$cnt_new += 1;
    	 }//if
     } //foreach
    // END COMPONENT SYNC
	echo $cnt_new .  _juga_syncron_newcomps . '<img src="images/tick.png" />';
	// *********************************************************************************

	// *********************************************************************************
	// BEGIN CONTENT SYNC
	echo "<br/>"._juga_syncron_content." ";

     $database->setQuery("SELECT * FROM #__content"
                         ." ORDER BY `id` ASC");
     $db_contents = $database->loadObjectList();

	$cnt_new = 0;
     foreach ($db_contents as $db_content)
     {
     	$found = false;
     	if ( count($db_contents > 0))
     	{
	     	foreach ($jugaitems as $jugaitem)
   		  	{
				if ( $jugaitem->type_id == $db_content->id )
				{
					$found = true;
				}
     		}
    	 }
    	 if ($found == false)
    	 {
			$newJugaItem = new jugaItem( $database );
			$newJugaItem->title			= $db_content->title;
			$newJugaItem->site_option	= "com_content";
			$newJugaItem->site_view		= "article";
			$newJugaItem->type			= "cont";
			$newJugaItem->type_id		= $db_content->id;
			$newJugaItem->store();
			$cnt_new += 1;
    	 }//if
     } //foreach
    // END CONTENT SYNC
	echo $cnt_new . _juga_syncron_newcontent . '<img src="images/tick.png" />';
	// *********************************************************************************

	listItems ( $option , $section );
} // sync
// ************************************************************************

/**
* Compiles information to add or edit
* @param integer The unique id of the record to edit (0 if new)
*/
// ************************************************************************
function editItem( $option, $section, $id ) {
	global $database, $mainframe, $mosConfig_list_limit;

	$lists = array();

	$row = new jugaItem( $database );
	// load the row from the db table
	$row->load( (int)$id );

	// fail if checked out not by 'me'
	if ($row->isCheckedOut( $my->id )) {
		mosRedirect( 'index2.php?option='. $option, 'The module $row->title is currently being edited by another administrator.' );
	}

	if ($id) {
		$row->checkout( $my->id );
	}

	// build list of types

	$lists['error_url_published'] 		= mosHTML::yesnoRadioList( 'error_url_published', 'class="inputbox"', $row->error_url_published );

	$types = array
			(
			  0 => mosHTML::makeOption( '', '- Select Type -' ),
			  1 => mosHTML::makeOption( 'cont', 'Content' ),
			  2 => mosHTML::makeOption( 'com', 'Component' ),
			  3 => mosHTML::makeOption( 'mod', 'Module' )
			);

	$lists['type'] = mosHTML::selectList( $types, "type", "size='1'", "value", "text", $row->type );
	$lists['option_exclude'] 		= mosHTML::yesnoRadioList( 'option_exclude', 'class="inputbox"', $row->option_exclude );

	HTML_juga::editItem( $row, $lists, $params, $option, $section);
}
// ************************************************************************

/**
* Saves the record on an edit form submit
* @param database A database connector object
*/
function saveItem( $option, $section ) {
	global $database, $my;

	$row = new jugaItem( $database );
	if (!$row->bind( $_POST )) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}

	if (!$row->check()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	if (!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	$row->checkin();

	unset($type);

	mosRedirect( "index2.php?option=$option&section=$section" );
}
// ************************************************************************

/**
* Publishes or Unpublishes one or more records
* @param array An array of unique category id numbers
* @param integer 0 if unpublishing, 1 if publishing
* @param string The current url option
*/
function publishItemURL( $cid=null, $publish=1,  $option, $section ) {
	global $database, $my;

	if (!is_array( $cid ) || count( $cid ) < 1) {
		$action = $publish ? 'publish' : 'unpublish';
		echo "<script> alert('Select an item to $action'); window.history.go(-1);</script>\n";
		exit;
	}

	mosArrayToInts( $cid );
	$cids = 'id=' . implode( ' OR id=', $cid );

	$query = "UPDATE #__juga_items "
	. "\n SET error_url_published = " . (int) $publish
	. "\n WHERE ( $cids )"
	. "\n AND ( checked_out = 0 OR ( checked_out = " . (int) $my->id . " ) )"
	;
	$database->setQuery( $query );
	if (!$database->query()) {
		echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		exit();
	}

	if (count( $cid ) == 1) {
		$row = new jugaItem( $database );
		$row->checkin( $cid[0] );
	}
	mosRedirect( "index2.php?option=$option&section=$section" );
}
// ************************************************************************

/**
* Deletes one or more records
* @param array An array of unique category id numbers
* @param string The current url option
*/
// ************************************************************************
function removeItems( $cid, $option, $section ) {
	global $database;

	if (!is_array( $cid ) || count( $cid ) < 1) {
		echo "<script> alert('Select an item to delete'); window.history.go(-1);</script>\n";
		exit;
	}
	if (count( $cid )) {
		mosArrayToInts( $cid );
		$cids = 'id=' . implode( ' OR id=', $cid );
		$query = "DELETE FROM #__juga_items "
		. "\n WHERE ( $cids )"
		;
		$database->setQuery( $query );
		if (!$database->query()) {
			echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		}

		$cids = 'item_id=' . implode( ' OR item_id=', $cid );
		$query = "DELETE FROM #__juga_g2i "
		. "\n WHERE ( $cids )"
		;
		$database->setQuery( $query );
		if (!$database->query()) {
			echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		}
	}

	mosRedirect( "index2.php?option=$option&section=$section" );
}
// ************************************************************************

/**
* Deletes one or more records
* @param array An array of unique category id numbers
* @param string The current url option
*/
// ************************************************************************
function withdrawAllItems( $cid, $option, $section ) {
	global $database;

	if (!is_array( $cid ) || count( $cid ) < 1) {
		echo "<script> alert('Select an item to delete'); window.history.go(-1);</script>\n";
		exit;
	}
	if (count( $cid )) {
		mosArrayToInts( $cid );
		$cids = 'item_id=' . implode( ' OR item_id=', $cid );
		$query = "DELETE FROM #__juga_g2i "
		. "\n WHERE ( $cids )"
		;
		$database->setQuery( $query );
		if (!$database->query()) {
			echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		}
	}

	mosRedirect( "index2.php?option=$option&section=$section" );
}
// ************************************************************************

/**
* Deletes one or more records
* @param array An array of unique category id numbers
* @param string The current url option
*/
// ************************************************************************
function enrollItems( $option, $section, $cid ) {
	global $database;

	if (!is_array( $cid ) || count( $cid ) < 1) {
		echo "<script> alert('Select an item'); window.history.go(-1);</script>\n";
		exit;
	}

	$group = JRequest::getVar( "group" );
	
	if (isset($group)) {
	  while (list($key, $val) = each($group)) {
		// if not already a member of the group
		// add member to group
		$database->setQuery("SELECT item_id FROM #__juga_g2i "
							." WHERE `group_id` = '$val'"
							." AND `item_id` = '$key' ");
		$already = $database->loadResult();

	    // echo "$key => $val\n";
	    if (($already != $key) && ($val)) {
		  $query = "INSERT INTO #__juga_g2i "
			."\n SET `item_id` = '$key',"
			."\n `group_id` = '$val'";
			$database->setQuery( $query );
			if (!$database->query()) {
				echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
			} // end if !db query
		} // end if $already
	  }	// end while
	} // end if group

	mosRedirect( "index2.php?option=$option&section=$section" );
}
// ************************************************************************

/**
* Deletes one or more records
* @param array An array of unique category id numbers
* @param string The current url option
*/
function withdrawItems( $option, $section, $cid ) {
	global $database;

	if (!is_array( $cid ) || count( $cid ) < 1) {
		echo "<script> alert('Select an item'); window.history.go(-1);</script>\n";
		exit;
	}

	$group = JRequest::getVar( "group" );
	
	if (isset($group)) {
	  while (list($key, $val) = each($group)) {

		  $query = "DELETE FROM #__juga_g2i "
			."\n WHERE `item_id` = '$key' "
			."\n AND `group_id` = '$val'";

			$database->setQuery( $query );
			if (!$database->query()) {
				echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
			} // end if !db query

	  }	// end while
	} // end if group

	mosRedirect( "index2.php?option=$option&section=$section" );
}
// ************************************************************************

/**
* Defaults one or more records
* @param array An array of unique category id numbers
* @param string The current url option
*/
function defaultItems( $cid, $option, $section ) {
	global $database;

	if (!is_array( $cid ) || count( $cid ) < 1) {
		echo "<script> alert('Select an item to default'); window.history.go(-1);</script>\n";
		exit;
	}

	 $database->setQuery("SELECT value FROM #__juga "
                         ." WHERE `title` = 'default_juga' ");
     $default_juga = $database->loadResult();

	if (count( $cid )) {
		foreach ($cid as $id) {
			// if not already a member of the group
			// add member to group
			$database->setQuery("SELECT item_id FROM #__juga_g2i "
								." WHERE `group_id` = '$default_juga'"
								." AND `item_id` = '$id' ");
			$already = $database->loadResult();

			if (($already != $id) && ($default_juga)) {
			  $query = "INSERT INTO #__juga_g2i "
				."\n SET `item_id` = '$id',"
				."\n `group_id` = '$default_juga'";
				$database->setQuery( $query );
				if (!$database->query()) {
					echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
				}
			}
		}
	}

	mosRedirect( "index2.php?option=$option&section=$section" );
}
// ************************************************************************

/**
* Defaults one or more records
* @param array An array of unique category id numbers
* @param string The current url option
*/
function defaultCEItems( $cid, $option, $section ) {
	global $database;

	if (!is_array( $cid ) || count( $cid ) < 1) {
		echo "<script> alert('Select an item to default'); window.history.go(-1);</script>\n";
		exit;
	}

	 $database->setQuery("SELECT value FROM #__juga "
                         ." WHERE `title` = 'default_ce' ");
     $default_ce = $database->loadResult();

	if (count( $cid )) {
		foreach ($cid as $id) {
		  $query = "UPDATE #__juga_items "
			."\n SET `error_url` = '$default_ce' "
			."\n WHERE `id` = '$id'";
			$database->setQuery( $query );
			if (!$database->query()) {
				echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
			}
		}
	}

	mosRedirect( "index2.php?option=$option&section=$section" );
}
// ************************************************************************

/**
* Defaults one or more records
* @param array An array of unique category id numbers
* @param string The current url option
*/
function removeCEItems( $cid, $option, $section ) {
	global $database;

	if (!is_array( $cid ) || count( $cid ) < 1) {
		echo "<script> alert('Select an item to default'); window.history.go(-1);</script>\n";
		exit;
	}

	if (count( $cid )) {
		foreach ($cid as $id) {
		  $query = "UPDATE #__juga_items "
			."\n SET `error_url` = '' "
			."\n WHERE `id` = '$id'";
			$database->setQuery( $query );
			if (!$database->query()) {
				echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
			}
		}
	}

	mosRedirect( "index2.php?option=$option&section=$section" );
}
// ************************************************************************

/**
* Flex one or more records
* @param array An array of unique category id numbers
* @param string The current url option
*/
function flexItems( $cid, $option, $section ) {
	global $database;

	if (!is_array( $cid ) || count( $cid ) < 1) {
		echo "<script> alert('Select an item to default'); window.history.go(-1);</script>\n";
		exit;
	}

	 $database->setQuery("SELECT value FROM #__juga "
                         ." WHERE `title` = 'flex_juga' ");
     $flex_juga = $database->loadResult();

	if (count( $cid )) {
		foreach ($cid as $id) {
			// if not already a member of the group
			// add member to group
			$database->setQuery("SELECT user_id FROM #__juga_g2i "
								." WHERE `group_id` = '$flex_juga'"
								." AND `item_id` = '$id' ");
			$already = $database->loadResult();

			if (($already != $id) && ($flex_juga)) {
			  $query = "INSERT INTO #__juga_g2i "
				."\n SET `item_id` = '$id',"
				."\n `group_id` = '$flex_juga'";
				$database->setQuery( $query );
				if (!$database->query()) {
					echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
				}
			}
		}
	}

	mosRedirect( "index2.php?option=$option&section=$section" );
}
// ************************************************************************

/**
* Unflex one or more records
* @param array An array of unique category id numbers
* @param string The current url option
*/
// ************************************************************************
function unflexItems( $cid, $option, $section ) {
	global $database;

	if (!is_array( $cid ) || count( $cid ) < 1) {
		echo "<script> alert('Select an item to UnFlex'); window.history.go(-1);</script>\n";
		exit;
	}
	
	if (count( $cid )) {
			 $database->setQuery("SELECT value FROM #__juga "
								 ." WHERE `title` = 'flex_juga' ");
			 $flex_juga = $database->loadResult();	
			 
		mosArrayToInts( $cid );
		$cids = 'item_id=' . implode( ' OR item_id=', $cid );
		$query = "DELETE FROM #__juga_g2i "
		. "\n WHERE ( $cids ) "
		. "\n AND group_id = '$flex_juga' "
		;
		$database->setQuery( $query );
		if (!$database->query()) {
			echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		}
	}

	mosRedirect( "index2.php?option=$option&section=$section" );
}
// ************************************************************************

/**
* Flex one or more records
* @param array An array of unique category id numbers
* @param string The current url option
*/
// ************************************************************************
function switchIncludeItems( $cid, $option, $section ) {
	global $database;

	if (!is_array( $cid ) || count( $cid ) < 1) {
		echo "<script> alert('Select an item to In/Exclude'); window.history.go(-1);</script>\n";
		exit;
	}

	 // $database->setQuery("SELECT value FROM #__juga "
     //                    ." WHERE `title` = 'flex_juga' ");
     // $flex_juga = $database->loadResult();

	if (count( $cid )) {
		foreach ($cid as $id) {
			// grab current option_exclude setting
			$database->setQuery("SELECT option_exclude FROM #__juga_items "
								." WHERE `id` = '$id' ");
			$option_exclude = $database->loadResult();
			if ($option_exclude == "1") { $new_exclude = 0; } else { $new_exclude = 1; }

			  $query = "UPDATE #__juga_items "
				."\n SET `option_exclude` = '$new_exclude' "
				." WHERE `id` = '$id' "
				;
				$database->setQuery( $query );
				if (!$database->query()) {
					echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
				}
		}
	}

	mosRedirect( "index2.php?option=$option&section=$section" );
}
// ************************************************************************

/**
* Compiles a list of records
* @param database A database connector object
*/
// ************************************************************************
function listUsers($option, $section) {
	global $database, $mainframe, $mosConfig_list_limit;

	// echo "<pre>"; print_r($_REQUEST); echo "</pre>";
	// $catid 		= intval( $mainframe->getUserStateFromRequest( "catid{$option}", 'catid', 0 ) );
	$group_id	= intval( $mainframe->getUserStateFromRequest( "group_id{$option}", 'group_id') );
	$limit 		= intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );
	$limitstart = intval( $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 ) );
	$search 	= $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
	if (get_magic_quotes_gpc()) {
		$search	= stripslashes( $search );
	}

	$where = array();
	
	$group_query = " ";	
	if ($group_id) {
		$group_query = "AND `group_id` = '$group_id'";
	}
	
	if ($search) {
		$where[] = "LOWER(name) LIKE '%" . $database->getEscaped( trim( strtolower( $search ) ) ) . "%'";
		$where[] = "LOWER(username) LIKE '%" . $database->getEscaped( trim( strtolower( $search ) ) ) . "%'";
		$where[] = "LOWER(email) LIKE '%" . $database->getEscaped( trim( strtolower( $search ) ) ) . "%'";
	}

	// get the total number of records
	$query = "SELECT DISTINCT(`user_id`), #__users.* FROM #__users "
	." LEFT JOIN #__juga_u2g ON #__juga_u2g.user_id = #__users.id "
	." WHERE 1 "
	. $group_query 
	. (count( $where ) ? "\n HAVING " . implode( ' OR ', $where ) : "")
	." ORDER BY `name` "
	;
	$database->setQuery( $query );
	$allrecords = $database->loadObjectList();
	$total = count($allrecords);

	require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );

	$database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );

	$rows = $database->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	$database->setQuery("SELECT * FROM #__juga_groups "
						." ORDER BY `title` ASC");
	$db_groups = $database->loadObjectList();

	$types[] = mosHTML::makeOption( '', '- Select Group -' );
	if ($db_groups) {
	  foreach ($db_groups as $dbg) {
	    $types[] = mosHTML::makeOption( $dbg->id, $dbg->title );
	  }
	}

	$javascript 	= 'onchange="document.adminForm.submit();"';
	$lists['group_id'] = mosHTML::selectList( $types, "group_id", "size='1' $javascript", "value", "text", $group_id );

	// find com_comprofiler
	
    $database->setQuery("SELECT DISTINCT ( `option` ), `name`, `id` FROM #__components"
     					." WHERE `option` = 'com_comprofiler' AND `parent` = '0'"
                        ." ORDER BY `id` ASC");
    $database->loadObject($com_comprofiler);
	$lists["com_comprofiler"] = $com_comprofiler;

    HTML_juga::listUsers($option, $section, $rows, $lists, $search, $pageNav);
    return true;
}
// ************************************************************************

/**
* Compiles a list of records
* @param database A database connector object
*/
// ************************************************************************

function synchronizeUsers ( $option, $section) {
	global $database;
     $database->setQuery("SELECT * FROM #__juga_u2g"
                         ." ORDER BY user_id");
     $jugausers = $database->loadObjectList();

     $database->setQuery("SELECT value FROM #__juga "
                         ." WHERE `title` = 'default_juga' ");
     $default_juga = $database->loadResult();

	// *********************************************************************************
	// BEGIN COMPONENT SYNC
	echo "<br/>"._juga_syncron_users." ";

     $database->setQuery("SELECT * FROM #__users"
                         ." ORDER BY `id` ASC");
    $db_users = $database->loadObjectList();

	$cnt_new = 0;
     foreach ($db_users as $db_user)
     {
     	$found = false;
     	if ( count($db_users > 0))
     	{
	     	foreach ($jugausers as $jugauser)
   		  	{
				if ( $jugauser->user_id == $db_user->id )
				{
					$found = true;
				}
     		}
    	 }
    	 if ($found == false)
    	 {
			$query = "INSERT INTO #__juga_u2g "
			."\n SET `user_id` = '$db_user->id',"
			."\n `group_id` = '$default_juga' "; // should grab default
   		 	$database->setQuery($query);
   		    $database->query();
			$cnt_new += 1;
    	 }//if
     } //foreach
    // END COMPONENT SYNC
	echo $cnt_new . _juga_syncron_newusers . '<img src="images/tick.png" />';
	// *********************************************************************************

	listUsers ( $option , $section );
} // sync
// ************************************************************************

/**
* Deletes one or more records
* @param array An array of unique category id numbers
* @param string The current url option
*/
function removeUsers( $cid, $option, $section ) {
	global $database;

	if (!is_array( $cid ) || count( $cid ) < 1) {
		echo "<script> alert('Select an item to delete'); window.history.go(-1);</script>\n";
		exit;
	}
	if (count( $cid )) {
		mosArrayToInts( $cid );
		$cids = 'user_id=' . implode( ' OR user_id=', $cid );
		$query = "DELETE FROM #__juga_u2g "
		. "\n WHERE ( $cids )"
		;
		$database->setQuery( $query );
		if (!$database->query()) {
			echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		}
	}

	mosRedirect( "index2.php?option=$option&section=$section" );
}
// ************************************************************************

/**
* Add one or more records to a Group
* @param array An array of unique category id numbers
* @param string The current url option
*/
function enrollUsers( $option, $section, $cid ) {
	global $database;

	if (!is_array( $cid ) || count( $cid ) < 1) {
		echo "<script> alert('Select an item'); window.history.go(-1);</script>\n";
		exit;
	}
	
	$group = JRequest::getVar( "group" );
	
	if (isset($group)) {
	  while (list($key, $val) = each($group)) {
		// if not already a member of the group
		// add member to group
		$database->setQuery("SELECT user_id FROM #__juga_u2g "
							." WHERE `group_id` = '$val'"
							." AND `user_id` = '$key' ");
		$already = $database->loadResult();

	    // echo "$key => $val\n";
	    if (($already != $key) && ($val)) {
		  $query = "INSERT INTO #__juga_u2g "
			."\n SET `user_id` = '$key',"
			."\n `group_id` = '$val'";
			$database->setQuery( $query );
			if (!$database->query()) {
				echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
			} // end if !db query
		} // end if $already
	  }	// end while
	} // end if group

	mosRedirect( "index2.php?option=$option&section=$section" );
}
// ************************************************************************

/**
* Remove one or more records from a Group
* @param array An array of unique category id numbers
* @param string The current url option
*/
function withdrawUsers( $option, $section, $cid ) {
	global $database;

	if (!is_array( $cid ) || count( $cid ) < 1) {
		echo "<script> alert('Select an item'); window.history.go(-1);</script>\n";
		exit;
	}

	$group = JRequest::getVar( "group" );
	
	if (isset($group)) {
	  while (list($key, $val) = each($group)) {

		  $query = "DELETE FROM #__juga_u2g "
			."\n WHERE `user_id` = '$key' "
			."\n AND `group_id` = '$val'";

			$database->setQuery( $query );
			if (!$database->query()) {
				echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
			} // end if !db query

	  }	// end while
	} // end if group

	mosRedirect( "index2.php?option=$option&section=$section" );
}
// ************************************************************************

/**
* Defaults one or more records
* @param array An array of unique category id numbers
* @param string The current url option
*/
function defaultUsers( $cid, $option, $section ) {
	global $database;

	if (!is_array( $cid ) || count( $cid ) < 1) {
		echo "<script> alert('Select an item to default'); window.history.go(-1);</script>\n";
		exit;
	}

	 $database->setQuery("SELECT value FROM #__juga "
                         ." WHERE `title` = 'default_juga' ");
     $default_juga = $database->loadResult();

	if (count( $cid )) {
		foreach ($cid as $id) {
			// if not already a member of the group
			// add member to group
			$database->setQuery("SELECT user_id FROM #__juga_u2g "
								." WHERE `group_id` = '$default_juga'"
								." AND `user_id` = '$id' ");
			$already = $database->loadResult();

			if (($already != $id) && ($default_juga)) {
			  $query = "INSERT INTO #__juga_u2g "
				."\n SET `user_id` = '$id',"
				."\n `group_id` = '$default_juga'";
				$database->setQuery( $query );
				if (!$database->query()) {
					echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
				}
			}
		}
	}

	mosRedirect( "index2.php?option=$option&section=$section" );
}
// ************************************************************************

/**
* Compiles a list of records
* @param database A database connector object
*/
// ************************************************************************
function defineItem2Groups($option, $section) {
	global $database, $mainframe, $mosConfig_list_limit;

	$item_id 	= mosGetParam($_GET, "item_id");
	$limit 		= intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );
	$limitstart = intval( $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 ) );
	$search 	= $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );

	// require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
	// $pageNav = new mosPageNav( $total, $limitstart, $limit  );

	$query = "SELECT * FROM #__juga_groups "
	. (count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "")
	;
	// $database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
	$database->setQuery( $query );

	$rows = $database->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	$total = count($rows);

	require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );

	// list info
	$item = new jugaItem( $database );
	// load the row from the db table
	$item->load( (int)$item_id );
	$lists['item'] = $item;
	
    HTML_juga::defineItem2Groups($option, $section, $rows, $lists, $search, $pageNav);
    return true;
}
// ************************************************************************

/**
* Defaults one or more records
* @param array An array of unique category id numbers
* @param string The current url option
*/
// ************************************************************************
function switchItem2Groups( $option, $section, $cid ) {
	global $database;

	if (!is_array( $cid ) || count( $cid ) < 1) {
		echo "<script> alert('Select a group to define'); window.history.go(-1);</script>\n";
		exit;
	}

	$item_id = JRequest::getVar( "item_id" );
	
	if (count( $cid )) {
		foreach ($cid as $id) {
			// if not already a member of the group
			// add item to group
			$database->setQuery("SELECT group_id FROM #__juga_g2i "
								." WHERE `group_id` = '$id'"
								." AND `item_id` = '$item_id' ");
			$already = $database->loadResult();

			if (($already != $id) && ($item_id)) {
			  $query = "INSERT INTO #__juga_g2i "
				."\n SET `item_id` = '$item_id',"
				."\n `group_id` = '$id'";
				$database->setQuery( $query );
				if (!$database->query()) {
					echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
				}
			}
			elseif (($already == $id) && ($item_id)) {
			  $query = "DELETE FROM #__juga_g2i "
				."\n WHERE `item_id` = '$item_id' "
				."\n AND `group_id` = '$id'";
				$database->setQuery( $query );
				if (!$database->query()) {
					echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
				}
			}
		}
	}

	mosRedirect( "index2.php?option=$option&section=i2g&item_id=$item_id" );
}
// ************************************************************************


/**
* Compiles a list of records
* @param database A database connector object
*/
// ************************************************************************
function defineUser2Groups($option, $section) {
	global $database, $mainframe, $mosConfig_list_limit;

	$user_id	= JRequest::getVar( "user_id" );
	
	$limit 		= intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );
	$limitstart = intval( $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 ) );
	$search 	= $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
	if (get_magic_quotes_gpc()) {
		$search	= stripslashes( $search );
	}

	// require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
	// $pageNav = new mosPageNav( $total, $limitstart, $limit  );

	$query = "SELECT * FROM #__juga_groups "
	. (count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "")
	;
	// $database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
	$database->setQuery( $query );

	$rows = $database->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	$total = count($rows);

	require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );

	// list info
		$query = "SELECT #__users.* "
		." FROM #__users "
		." WHERE `id` = '$user_id' "
		;
		$database->setQuery( $query );
		$user = $database->loadObjectList();
		$lists['user'] = &$user[0];
	
    HTML_juga::defineUser2Groups($option, $section, $rows, $lists, $search, $pageNav);
    return true;
}
// ************************************************************************

/**
* Defaults one or more records
* @param array An array of unique category id numbers
* @param string The current url option
*/
// ************************************************************************
function switchUser2Groups( $option, $section, $cid ) {
	global $database;

	if (!is_array( $cid ) || count( $cid ) < 1) {
		echo "<script> alert('Select a group to define'); window.history.go(-1);</script>\n";
		exit;
	}

	$user_id	= JRequest::getVar( "user_id" );

	if (count( $cid )) {
		foreach ($cid as $id) {
			// if not already a member of the group
			// add item to group
			$database->setQuery("SELECT group_id FROM #__juga_u2g "
								." WHERE `group_id` = '$id'"
								." AND `user_id` = '$user_id' ");
			$already = $database->loadResult();

			if (($already != $id) && ($user_id)) {
			  $query = "INSERT INTO #__juga_u2g "
				."\n SET `user_id` = '$user_id',"
				."\n `group_id` = '$id'";
				$database->setQuery( $query );
				if (!$database->query()) {
					echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
				}
			}
			elseif (($already == $id) && ($user_id)) {
			  $query = "DELETE FROM #__juga_u2g "
				."\n WHERE `user_id` = '$user_id' "
				."\n AND `group_id` = '$id'";
				$database->setQuery( $query );
				if (!$database->query()) {
					echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
				}
			}
		}
	}

	mosRedirect( "index2.php?option=$option&section=g2u&user_id=$user_id" );
}
// ************************************************************************

/**
* Compiles a list of records
* @param database A database connector object
*/
// ************************************************************************
function listCodes($option, $section) {
	global $database, $mainframe, $mosConfig_list_limit;
	
	$where	= array();
	// $catid 		= intval( $mainframe->getUserStateFromRequest( "catid{$option}", 'catid', 0 ) );
	$limit 		= intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );
	$limitstart = intval( $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 ) );
	$search 	= $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
	if (get_magic_quotes_gpc()) {
		$search	= stripslashes( $search );
	}

	if ($search) {
		$where[] = "LOWER(title) LIKE '%" . $database->getEscaped( trim( strtolower( $search ) ) ) . "%'";
	}

	$query = "SELECT * FROM #__juga_codes "
	. (count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "")
	. " ORDER BY `title` ASC "
	;

	$database->setQuery( $query );
	$allrows = $database->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}	
	$total = count($allrows);

	require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );

	$database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
	$rows = $database->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}
	
    HTML_juga::listCodes($option, $section, $rows, $lists, $search, $pageNav);
    return true;
}
// ************************************************************************

/**
* Compiles information to add or edit
* @param integer The unique id of the record to edit (0 if new)
*/
// ************************************************************************
function editCode( $option, $section, $id ) {
	global $database, $mainframe, $mosCode_list_limit;

	$lists = array();

	$row = new jugaCode( $database );
	// load the row from the db table
	$row->load( (int)$id );

	// fail if checked out not by 'me'
	if ($row->isCheckedOut( $my->id )) {
		mosRedirect( 'index2.php?option='. $option, 'The module $row->title is currently being edited by another administrator.' );
	}

	if ($id) {
		$row->checkout( $my->id );
	} else {
		// initialise new record
		// $row->published = 1;
		// $row->approved 	= 1;
		// $row->order 	= 0;
		// $row->catid 	= intval( mosGetParam( $_POST, 'catid', 0 ) );
		// NOTHING TO INIT
	}
	
	$lists['published'] = mosHTML::yesnoRadioList( 'published', 'class="inputbox"', $row->published );
	
	$query = "SELECT * FROM #__juga_groups "
			." ORDER BY `title` "
			;
	$database->setQuery( $query );
	$data = $database->loadObjectList();
	unset($types);
	$types[] = mosHTML::makeOption( " ", "- "._JUGA_SELECTGROUP." -" );
	if ($data) { foreach ($data as $d) {
	    $types[] = mosHTML::makeOption( $d->id, $d->title );
	} }	
	$lists["groups"] = mosHTML::selectList( $types, "group_id", "size='1' ", "value", "text", $row->group_id );


	HTML_juga::editCode( $row, $lists, $params, $option, $section);
}
// ************************************************************************

/**
* Saves the record on an edit form submit
* @param database A database connector object
*/
function saveCode( $option, $section ) {
	global $database, $my;

	$row = new jugaCode( $database );
	if (!$row->bind( $_POST )) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}

	if (!$row->check()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	if (!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	$row->checkin();

	mosRedirect( "index2.php?option=$option&section=$section" );
}
// ************************************************************************

/**
* Deletes one or more records
* @param array An array of unique category id numbers
* @param string The current url option
*/
function removeCodes( $cid, $option, $section ) {
	global $database;

	if (!is_array( $cid ) || count( $cid ) < 1) {
		echo "<script> alert('Select an item to delete'); window.history.go(-1);</script>\n";
		exit;
	}
	if (count( $cid )) {
		mosArrayToInts( $cid );
		$cids = 'id=' . implode( ' OR id=', $cid );
		$query = "DELETE FROM #__juga_codes "
		. "\n WHERE ( $cids )"

		;
		$database->setQuery( $query );
		if (!$database->query()) {
			echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		}
	}

	mosRedirect( "index2.php?option=$option&section=$section" );
}
// ************************************************************************

/**
* Publishes or Unpublishes one or more records
* @param array An array of unique category id numbers
* @param integer 0 if unpublishing, 1 if publishing
* @param string The current url option
*/
function publishCodes( $cid=null, $publish=1,  $option, $section ) {
	global $database, $my;

	if (!is_array( $cid ) || count( $cid ) < 1) {
		$action = $publish ? 'publish' : 'unpublish';
		echo "<script> alert('Select an item to $action'); window.history.go(-1);</script>\n";
		exit;
	}

	mosArrayToInts( $cid );
	$cids = 'id=' . implode( ' OR id=', $cid );

	$query = "UPDATE #__juga_codes "
	. "\n SET published = " . (int) $publish
	. "\n WHERE ( $cids )"
	. "\n AND ( checked_out = 0 OR ( checked_out = " . (int) $my->id . " ) )"
	;
	$database->setQuery( $query );
	if (!$database->query()) {
		echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		exit();
	}

	if (count( $cid ) == 1) {
		$row = new jugaCode( $database );
		$row->checkin( $cid[0] );
	}
	mosRedirect( "index2.php?option=$option&section=$section" );
}
// ************************************************************************


/**
* runs the JUGA patch
* @param database A database connector object
*/
// ************************************************************************
function toolPatch ( $option, $section ) {
	global $database;
    // if the table #__juga_codes doesn't exist, create it
		$query = "SHOW COLUMNS FROM #__juga_codes ";
		$database->setQuery( $query );
		$rows = $database->loadObjectList();
	
		if (!$rows) {
			echo "<p style='color:#FFCC00;'>No table `#__juga_codes`.</p>";
			$query = "
					CREATE TABLE `#__juga_codes` (
					  `id` int(255) NOT NULL auto_increment,
					  `title` varchar(255) NOT NULL,
					  `description` text NOT NULL,
					  `group_id` int(11) NOT NULL,
					  `published` tinyint(1) NOT NULL,
					  `publish_up` datetime NOT NULL default '0000-00-00 00:00:00',
					  `publish_down` datetime NOT NULL default '0000-00-00 00:00:00',
					  `times_allowed` int(11) unsigned NOT NULL default '0',
					  `hits` int(11) unsigned NOT NULL default '0',
					  `checked_out` int(11) unsigned NOT NULL,
					  `checked_out_time` datetime NOT NULL,
					  PRIMARY KEY  (`id`)
					) TYPE=MyISAM ;		
			";
			$database->setQuery( $query );
			if (!$database->query()) {
				echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
				exit();
			}
			echo "<p style='color:#00CC00;' >Table `#__juga_codes` created successfully.</p>";		
		} else {
			echo "<p style='color:#00CC00;' >Table `#__juga_codes` already exists. </p>";
		}

	// if the field option_exclude doesn't exist in juga_items, insert it
		$query = "SHOW COLUMNS FROM #__juga_items "
		. "\n LIKE 'option_exclude' "
		;
		$database->setQuery( $query );
		$rows = $database->loadObjectList();
		if ($database->getErrorNum()) {
			echo $database->stderr();
			return false;
		}
	
		if (!$rows) {
			echo "<p style='color:#FFCC00;'>No column of the name `option_exclude`.</p>";
			$query = "ALTER TABLE #__juga_items ADD `option_exclude` TINYINT(1) NOT NULL;"
			;
			$database->setQuery( $query );
			if (!$database->query()) {
				echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
				exit();
			}
			echo "<p style='color:#00CC00;' >Column `option_exclude` created in #__juga_items. </p>";		
		} else {
			echo "<p style='color:#00CC00;' >Column `option_exclude` already exists in #__juga_items. </p>";
		}

	// if the field site_section doesn't exist in juga_items, insert it
		$query = "SHOW COLUMNS FROM #__juga_items "
		. "\n LIKE 'site_section' "
		;
		$database->setQuery( $query );
		$rows = $database->loadObjectList();
		if ($database->getErrorNum()) {
			echo $database->stderr();
			return false;
		}
	
		if (!$rows) {
			echo "<p style='color:#FFCC00;'>No column of the name `site_section`.</p>";
			$query = "ALTER TABLE #__juga_items ADD `site_section` VARCHAR(255) NOT NULL;"
			;
			$database->setQuery( $query );
			if (!$database->query()) {
				echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
				exit();
			}
			echo "<p style='color:#00CC00;' >Column `site_section` created in #__juga_items. </p>";		
		} else {
			echo "<p style='color:#00CC00;' >Column `site_section` already exists in #__juga_items. </p>";
		}		

	// if the field site_view doesn't exist in juga_items, insert it
		$query = "SHOW COLUMNS FROM #__juga_items "
		. "\n LIKE 'site_view' "
		;
		$database->setQuery( $query );
		$rows = $database->loadObjectList();
		if ($database->getErrorNum()) {
			echo $database->stderr();
			return false;
		}
	
		if (!$rows) {
			echo "<p style='color:#FFCC00;'>No column of the name `site_view`.</p>";
			$query = "ALTER TABLE #__juga_items ADD `site_view` VARCHAR(255) NOT NULL;"
			;
			$database->setQuery( $query );
			if (!$database->query()) {
				echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
				exit();
			}
			echo "<p style='color:#00CC00;' >Column `site_view` created in #__juga_items. </p>";		
		} else {
			echo "<p style='color:#00CC00;' >Column `site_view` already exists in #__juga_items. </p>";
		}		
	
    return true;	
} // end listTools
// ************************************************************************