<?php
# Error display
ini_set("display_errors",1);

# Global variable initialization
define("FAILED_LOGIN_ATTEMPTS_ALLOWED",5); // Maximum failed login attempt allowed
define("FAILED_ATTEMPTS_TIME_WINDOW",30);// In minutes
define("LOCKOUT_TIME_WINDOW",1440); // in minutes, Default value 1440 = 24 hours

# DB varialbe
define("HOST",'localhost');
define("USER",'root');
define("PASSWORD",'bitnami');
define("DATABASE",'iplog');


class iplog {
	
	# Creating constrcut Class for DB connetion with iplog
	function __construct() {
		// Connetion with iplog DB
 		$link	= mysql_pconnect(HOST,USER,PASSWORD) or die(mysql_error());
		$dblink	= mysql_select_db(DATABASE) or die(mysql_error());
	}
	
	# Function to check if IP is locked or not, if lock then return the raw for IP
	function _check_if_iplocked($user_ip){
		# Query to fetch IP address data where it is_ip_locked equal to Yes
		$sql			= "SELECT * FROM iplog WHERE ip_address LIKE '$user_ip' AND is_ip_locked = 'Yes'";
		$resultIpLog	= mysql_query($sql);
		
		if(mysql_num_rows($resultIpLog) > 0){
			# If IP log data found with locked status yes then return all details for this IP
			return $ipData	= mysql_fetch_object($resultIpLog); 
		}else{
			 # If no datafound then return Zero
			return NULL;
		}
	}

	# Function to retrive data from IP address
	function _retrive_data_from_ip($user_ip){
		# Query to fetch IP address data where it is_ip_locked equal to Yes
		$sql	= "SELECT * FROM iplog WHERE ip_address LIKE '$user_ip'";
		$result	= mysql_query($sql);
		
		if(mysql_num_rows($result) > 0){
			return $ipData = mysql_fetch_object($result); 
		}else{
			 # If no datafound then return Null
			return NULL;
		}
	}

	# Function to Insert entry in IP table, creating row for this IP first time (keep name Create)
	function _insert_ip_data(){
		$ip_address = $_SERVER['REMOTE_ADDR'];
		# Query to Insert IP address data
		$sql	= "INSERT INTO `iplog` (`ip_address`, `failed_login_count`, `first_failed_login_time`, `ip_locked_time`, `is_ip_locked`) VALUES ('$ip_address','1',NOW(),'','No');";
		$result	= mysql_query($sql);
	}
	
	# Function to reset and update IP table value for IP address when,IP is locked and if request from this IP is coming after lockout_time_window
	function _reset_ip_value($user_ip){
		$sql 	= "UPDATE `iplog` SET `failed_login_count` = '0', `first_failed_login_time` = '', `ip_locked_time` = '', `is_ip_locked` = 'No' WHERE `ip_address` = '$user_ip'";
		$result	= mysql_query($sql);
	}

        # Function to reset and update IP table value for IP address when,IP is locked and if request from this IP is coming after lockout_time_window
	function _reset_ip_value_count_one($user_ip){
		$sql 	= "UPDATE `iplog` SET `failed_login_count` = '1', `first_failed_login_time` = NOW(), `ip_locked_time` = '', `is_ip_locked` = 'No' WHERE `ip_address` = '$user_ip'";
		$result	= mysql_query($sql);
	}

	# Function to Update IP Status to locked and capture locked time
	function _update_ip_locked_status_blocked($user_ip){
		# Query to Update IP Status to locked and capture locked time
		$sql 	= "UPDATE `iplog` SET `ip_locked_time` = NOW(),`is_ip_locked` = 'Yes' WHERE `ip_address` = '$user_ip'";
		$result	= mysql_query($sql);
	}
	
	# Function to Update IP failed login count
	function _update_ip_login_count($user_ip,$failed_login_count,$first_failed_login_time){
		# Query to Update failed login count for user IP
		$sql 	= "UPDATE `iplog` SET `failed_login_count` = $failed_login_count";
		if($first_failed_login_time == "0000-00-00 00:00:00"){
			$sql .= " ,`first_failed_login_time` = NOW()";
		}
		$sql .= " WHERE `ip_address` LIKE '$user_ip'";
		$result	= mysql_query($sql);
	}
	
	# Function to to find Difference in Minutes from 2 given dates
	function _check_minutes_difference($to_time,$from_time){
		# Return minutes from substracting IP locked time from current time
		return round(abs($to_time - $from_time) / 60);	
	}
	
	# Function 403 forbidden message account Lock
	function _forbidden_msg_account_lock(){
		# Code for 403 forbidden page Begin
		header("HTTP/1.0 403 Forbidden");?>
		<div><h1>Forbidden</h1>
                    <h4>Your account is locked for invalid login attempts, please create a support ticket or send an email to <a href="mailto:support@townwizard.com">support@townwizard.com</a> to unlock it.</h4><hr>
		</div>
		<?php exit;
		# Code for 403 forbidden page End
	}
	

} // Class ends here