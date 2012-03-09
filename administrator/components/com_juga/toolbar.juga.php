<?php 
// ensure this file is being included by a parent file  
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

if (!isset($section)) {
	$section = JRequest::getVar( "section", JRequest::getVar( "section", "groups", "GET" ), "POST");
}

if (!isset($task)) {
	$task = JRequest::getVar( "task", JRequest::getVar( "task", "", "GET" ), "POST");
}

if (!isset($id)) {
	$id = JRequest::getVar( "id", JRequest::getVar( "id", "", "GET" ), "POST");
}

// Include toolbar's HTML class 
require_once($mainframe->getPath('toolbar_html'));


switch ($section) { 
 	case 'config':
	switch ($task) {
		case 'new': 
		case 'add':
		case 'edit':
		case 'editA': 
		  TOOLBAR_juga::edit( _juga_config, $id );
		  break; 
		default:  
		  TOOLBAR_juga::defaults( _juga_config );
		  break; 
	} // end task
	break; // end config
	
 	case 'groups':
	switch ($task) {
		case 'new': 
		case 'add':
		case 'edit':
		case 'editA': 
		  TOOLBAR_juga::edit( _juga_config_groups, $id );
		  break; 
		default:  
		  TOOLBAR_juga::defaults( _juga_config_groups );
		  break; 
	} // end task
	break; // end groups

	case "items":
	switch ($task) {
		case 'new': 
		case 'add':
		case 'edit':
		case 'editA': 
		  TOOLBAR_juga::edit( _juga_config_siteitems, $id );
		  break; 
		default:  
		  TOOLBAR_juga::items();
		  break; 
	} // end task	
	break; // end items

 	case 'codes':
	switch ($task) {
		case 'new': 
		case 'add':
		case 'edit':
		case 'editA': 
		  TOOLBAR_juga::edit( _juga_config_codes, $id );
		  break; 
		default:  
		  TOOLBAR_juga::codes();
		  break; 
	} // end task
	break; // end codes
	
	case "u2g":
	switch ($task) {
		case 'new': 
		case 'add':
		case 'edit':
		case 'editA': 
		  TOOLBAR_juga::edit( _juga_config_assign_users, $id );
		  break; 
		default:  
		  TOOLBAR_juga::u2g( $id );
		  break; 
	} // end task	
	break; // end u2g

	case "i2g":
	switch ($task) {
		case 'new': 
		case 'add':
		case 'edit':
		case 'editA': 
		default:
			if (!isset($item_id)) {
				$item_id = JRequest::getVar( "item_id", JRequest::getVar( "item_id", "", "GET" ), "POST");
			}
			if (isset($item_id)) {
				global $database;
				// list info
				$item = new jugaItem( $database );
				// load the row from the db table
				$item->load( (int)$item_id );
				if ($item) { $pagetitle = " - ".$item->title." (".$item->id.")"; }
			}
		    TOOLBAR_juga::i2g( $pagetitle );
		  break; 
	} // end task	
	break; // end i2g

	case "g2u":
	switch ($task) {
		case 'new': 
		case 'add':
		case 'edit':
		case 'editA': 
		default:  
			if (!isset($user_id)) {
				$user_id = JRequest::getVar( "user_id", JRequest::getVar( "user_id", "", "GET" ), "POST");
			}
			if (isset($user_id)) {
				global $database;
				// list info
					$query = "SELECT #__users.* "
					." FROM #__users "
					." WHERE `id` = '".$user_id."' "
					;
					$database->setQuery( $query );
					$database->loadObject( $user );

				if ($user) { $pagetitle = " - ".$user->username.", ".$user->email." (".$user->id.")"; }
			}
		    TOOLBAR_juga::g2u( $pagetitle );
		  break; 
	} // end task	
	break; // end g2u

	case "tools":
	switch ($task) {
		case 'new': 
		case 'add':
		case 'edit':
		case 'editA': 
		default:  
		  TOOLBAR_juga::blank( _juga_config_tools );
		  break; 
	} // end task	
	break; // end g2u
		
	default:  
	  TOOLBAR_juga::blank( );
	  break;
} // end section