<?php
# including class file
require_once("iplog_class.php");

# Create class object
$Obj_iplog = new iplog();

# Set session varialble login page shown to No
$_SESSION['login_page_shown'] = "no";
$proceed_to_show_login_page = "yes"; 

# Passing user IP to _check_if_iplocked function
$ip_record_from_db = $Obj_iplog->_check_if_iplocked($_SERVER['REMOTE_ADDR']);
	
# If IP ENTRY FOUND and "is_ip_locked" is equal to Yes
if(isset($ip_record_from_db) && $ip_record_from_db != NULL){
	//$ip_record_from_db->ip_locked_time = "2014-04-02 16:02:06";
	$total_minutes = $Obj_iplog->_check_minutes_difference(strtotime(date("Y-m-d H:i:s",time())),strtotime($ip_record_from_db->ip_locked_time));
	
	# IP is locked, see if request from this IP is coming after LOCKOUT_TIME_WINDOW (24hr (1440 Minutes)) default value)
	# If yes then unlock IP address
	if(($total_minutes) > (LOCKOUT_TIME_WINDOW)){
		# waited enough time, update and reset all value within IP table for this IP
		$Obj_iplog->_reset_ip_value($_SERVER['REMOTE_ADDR']);
	}else{
		$Obj_iplog->_forbidden_msg_account_lock();
	}
}
# else IP is not locked or IP table entry not found, continue to show login page....

if($proceed_to_show_login_page == "yes"){
	$_SESSION['login_page_shown'] = "yes";
	// Show login page
}else{
	// return 403 forbidden, message: your account is locked, please call customer support to unlock it.
	$Obj_iplog->_forbidden_msg_account_lock();
}
# **** at this point, user is shown the login form and user submits the form *******

?>