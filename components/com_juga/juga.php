<?php
// ensure this file is being included by a parent file
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

if (file_exists($mosConfig_absolute_path.'/administrator/components/com_juga/languages/'.$mosConfig_lang.'.php')) {
      include($mosConfig_absolute_path.'/administrator/components/com_juga/languages/'.$mosConfig_lang.'.php');
} else {
      include($mosConfig_absolute_path.'/administrator/components/com_juga/languages/english.php');
}


require_once ( $mainframe->getPath( 'front_html' ) );
require_once ( $mainframe->getPath( 'class' ) );

switch( $task ) {
	case "processcode":
		jugaProcessCode( $option );
		break;	
	default:
		jugaMain( $option );
		break;
} // end switch
// ************************************************************************

/**
* Display Main Juga Page
*/
// ************************************************************************
function jugaMain( $option ) {
	global $my;
	if ( !$my->id ) {
		mosNotAuth();
		return;
	}

	HTML_juga::jugaMain( $option );
} // end jugaMain
// ************************************************************************

/**
* Process Juga Access Code
*/
// ************************************************************************
function jugaProcessCode( $option ) {
	global $my, $database;

	$jugacode = strval( htmlspecialchars( mosGetParam( $_POST, 'jugacode' ) ) );

	// do some security checks
	if (!$my->id || $jugacode == '') {
		mosNotAuth();
		return;
	}

	// simple spoof check security
	josSpoofCheck();

	$now 		= _CURRENT_SERVER_TIME;
	$nullDate 	= $database->getNullDate();
	$where 		= array();

	$where[] = "a.published = 1";
	$where[] = "( a.times_allowed > a.hits OR a.times_allowed = '-1' ) ";
	$where[] = "( a.publish_up = " . $database->Quote( $nullDate ) . " OR a.publish_up <= " . $database->Quote( $now ) . " )";
	$where[] = "( a.publish_down = " . $database->Quote( $nullDate ) . " OR a.publish_down >= " . $database->Quote( $now ) . " )";
	$where[] = "LOWER(a.title) = '" . $database->getEscaped( trim( strtolower( $jugacode ) ) ) . "'";

	$query = "SELECT id"
	. "\n FROM #__juga_codes AS a";
	$query .= ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : '' )
	;
	$database->setQuery( $query );
	$jugacode_id = intval($database->loadResult());

	if($jugacode_id > 0) {
		$row = new jugaCode( $database );
		// load the row from the db table
		$row->load( (int)$jugacode_id );
		
		// if not already a member of the group
		// add member to group
		// then update number of hits for juga code
		$database->setQuery("SELECT user_id FROM #__juga_u2g "
							." WHERE `group_id` = '$row->group_id'"
							." AND `user_id` = '$my->id' ");
		$already = $database->loadResult();

	    if (($already != $my->id) && ($row->group_id)) {
		  $query = "INSERT INTO #__juga_u2g "
			."\n SET `user_id` = '$my->id',"
			."\n `group_id` = '$row->group_id'";
			$database->setQuery( $query );
			if (!$database->query()) {
				echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
			} // end if !db query

			$query = "UPDATE #__juga_codes"
			. "\n SET `hits` = hits + 1 "
			. "\n WHERE `id` = '$jugacode_id' "
			;
			$database->setQuery( $query );
			$database->query();
			
			echo _juga_code_success;
		} // end if $already

	} else {
		// juga code not found
		echo "<script> alert('"._juga_invalid_code."'); window.history.go(-1); </script>\n";
	}
}
// ************************************************************************

?>
