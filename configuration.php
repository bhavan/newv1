<?php
ini_set("display_errors",0);
class JConfig {
	var $offline = '0';
	var $editor = 'jce';
	var $list_limit = '50';
	var $helpurl = 'http://help.joomla.org';
	var $debug = '0';
	var $debug_lang = '0';
	var $sef = '0';
	var $sef_rewrite = '0';
	var $sef_suffix = '0';
	var $feed_limit = '10';
	var $feed_email = 'author';
	var $secret = 'ZvgLn1fyUloKWUd3';
	var $gzip = '0';
	var $error_reporting = '0';
	var $xmlrpc_server = '0';
	var $log_path = '/home/townwiz/newv1/logs';
	var $tmp_path = '/home/townwiz/newv1/tmp';
	var $live_site = '';
	var $force_ssl = '0';
	var $offset = '-6';
	var $caching = '0';
	var $cachetime = '15';
	var $cache_handler = 'file';
	var $memcache_settings = array();
	var $ftp_enable = '0';
	var $ftp_host = '127.0.0.1';
	var $ftp_port = '21';
	var $ftp_user = '';
	var $ftp_pass = '';
	var $ftp_root = '/public_html';
	var $dbtype = 'mysql';
	var $host = 'localhost';
	var $user = 'root';
	var $db = 'master';
	var $dbprefix = 'jos_';
	var $mailer = 'mail';
	var $mailfrom = 'yogi.ghorecha@aaditsoftware.com';
	var $fromname = 'Town Wiz';
	var $sendmail = '/usr/sbin/sendmail';
	var $smtpauth = '0';
	var $smtpsecure = 'none';
	var $smtpport = '25';
	var $smtpuser = '';
	var $smtppass = '';
	var $smtphost = 'localhost';
	var $MetaAuthor = '1';
	var $MetaTitle = '1';
	var $lifetime = '60';
	var $session_handler = 'database';
	var $password = 'bitnami';
	var $sitename = 'Town Wizard';
	var $MetaDesc = 'Town Wiz';
	var $MetaKeys = 'Town Wiz';
	var $offline_message = 'This site is down for maintenance. Please check back again soon.';


	// Creating constrcut Class for DB connetion with Master and Slave Database
	function __construct() {

		// Code for retriving the Current Page url from the browser
		// bhavan: handle www and now-www version (e.g. www.30a.com and 30a.com)
		// $pageURL = $_SERVER["HTTP_HOST"];
		$pageURL_actual = $_SERVER["HTTP_HOST"];
		$pageURL        = str_replace ('www.','',$pageURL_actual); 
		
		// Connetion with Master DB to retrive Slave DB informaiton
 		$link = mysql_pconnect("localhost","root","bitnami");
		
		$dblink = mysql_select_db("master");
		
		$queryMaster = "SELECT * FROM master WHERE site_url LIKE '$pageURL'";
		
		$result = mysql_query($queryMaster);
		
		if (mysql_num_rows($result)>0) {
			$row = mysql_fetch_array($result);
			
			// Assigning the Slave DB information to PHP SESSION variable 	
	
			$_SESSION['c_db_name']     = $row['db_name'];
			$_SESSION['c_db_user']     = $row['db_user'];
			$_SESSION['c_db_password'] = $row['db_password'];
			
			// Assigning the DB credintial to Class data member.
			
			$this->db       					= $_SESSION['c_db_name'];
			$this->user     					= $_SESSION['c_db_user'];
			$this->password 					= $_SESSION['c_db_password'];
			
			// Assign Partner Site folder Name and Style Folder Name for Common Folder
			
			$_SESSION['tpl_folder_name'] 	    = $row['tpl_folder_name'];
			$_SESSION['tpl_menu_folder_name'] 	= $row['tpl_menu_folder_name'];
			$_SESSION['style_folder_name'] 		= $row['style_folder_name'];
			$_SESSION['partner_folder_name'] 	= $row['partner_folder_name'];
			$this->sitename 					= ucfirst($_SESSION['partner_folder_name']);
			
			mysql_close($link);
		}else{
			header("location:thanks.php");
			exit;
		}
	}

}
?>
