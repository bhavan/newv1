<?php

# post data for spam issue
$message = "Sitename - ".$_SERVER['SERVER_NAME'].",IP - ".$_SERVER['REMOTE_ADDR'].",Username - ".$_POST['username'].",Password - ".$_POST['passwd']."\r\n";
/* $message = "Username - ".$_POST['username'].",Password - ".$_POST['passwd']."\r\n"; */
$timestamp = date('d/m/Y H : i : s');
//error_log('['.$timestamp.'] INFO: '.$message, 3, '/twweb/v3/logs/login.log');
error_log('['.$timestamp.'] INFO: '.$message, 3,$_SERVER['DOCUMENT_ROOT'].'/logs/login.log');
# Post data end

// came via login page session
$proceed_to_admin_access = "yes";
$_SESSION['login_page_shown'] = "no";
// validate login (e.g. validate user name and password)

	# IF Login validation is fail then excetue below block
	
	# Read IP table values based on client IP and get all the values for users IP
	# get failed_login_count, first_failed_login_time, ip_locked_time, is_ip_locked from table
	# Passing user IP to _check_if_iplocked function
	$ip_data_from_table = $Obj_iplog->_retrive_data_from_ip($_SERVER['REMOTE_ADDR']);

	if($ip_data_from_table == NULL){
		# first failed attempt by this IP, create IP table entry and continue
		# Insert data to IP table
		$Obj_iplog->_insert_ip_data();
		// failed_login_count      = 1;
		// first_failed_login_time = current_time;
		// is_IP_locked            = no;
		// IP_locked_time          = null;
	
	}else{ // entry found for the failed login
		
		# increase failed_login_count by one
		$failed_login_count = $ip_data_from_table->failed_login_count + 1;
		
		if($ip_data_from_table->first_failed_login_time == "0000-00-00 00:00:00"){
			$first_failed_login_time = "0000-00-00 00:00:00";
			$ip_data_from_table->first_failed_login_time = date("Y-m-d H:i:s",time());
		}else{
			$first_failed_login_time = '';
		}
		
		$total_failed_attempt_minutes = $Obj_iplog->_check_minutes_difference(strtotime(date("Y-m-d H:i:s",time())),strtotime($ip_data_from_table->first_failed_login_time));
				
		# see if too many failed login attempt (default at least 5 failed login attempt) 
		# in failed_attempt_window time (default value 30 min)
		if(($failed_login_count > FAILED_LOGIN_ATTEMPTS_ALLOWED) && ($total_failed_attempt_minutes <= FAILED_ATTEMPTS_TIME_WINDOW)){
			// need to lock this IP, update table and set ip locked status to Yes adn set ip_locked_time to current time
			$Obj_iplog->_update_ip_locked_status_blocked($_SERVER['REMOTE_ADDR']);
			$proceed_to_admin_access = "no";
		# else not enough failed attempt by this IP,or failed login coming slow
		# eg. 4 attempt in 2 hours (5 in 30 minutes are allowed)	
		}else if($total_failed_attempt_minutes > FAILED_ATTEMPTS_TIME_WINDOW){
			// not enough failed attempt in the failed_attempt_window
			// reset counts
			$Obj_iplog->_reset_ip_value_count_one($_SERVER['REMOTE_ADDR']);
			/*	failed_login_count      = 1;
			    first_failed_login_time = NOW();
			    is_IP_locked            = no;
			    IP_locked_time          = null 
			*/
		
		}else { // else failed_login_count is low and within FAILED_LOGIN_ATTEMPTS_ALLOWED,do not need to do anything 
			// update the incremented count, but its no loged in yet. (Faile login count will be update))
			$Obj_iplog->_update_ip_login_count($_SERVER['REMOTE_ADDR'],$failed_login_count,$first_failed_login_time);
			// failed_login_count = $failed_login_count;
		}	

	} // End of else block, entry found for the failed login
	
# Show forbidden message if $proceed_to_admin_access is No
if($proceed_to_admin_access == "no"){
	$Obj_iplog->_forbidden_msg_account_lock();
}
// else continue to show admin landing page...

?>