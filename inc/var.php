<?php
session_start();

global $var;

class global_var {
  public $arr = array();

  public function __construct() {
	  require_once("configuration.php");
	 $jconfig = new JConfig();


    $this->arr['db'] = array(
							 
							 
      'server'    => $jconfig->host,
      'user'      => $jconfig->user,
      'password'  => $jconfig->password,
      'database'  => $jconfig->db,
    );
    $this->arr['inc_path'] = './inc/';
   // $this->arr['tpl_path'] = './inc/tpl/';
   
    // Code by Yogi to set path for TPL folder from master DB 
    $this->arr['tpl_path'] = './partner/'.$_SESSION['tpl_folder_name'].'/tpl/';

    $this->arr['tmp_path'] = './tmp/';
    $this->arr['root'] = '/';
    $this->arr['joomla_root'] = './';
    $this->arr['ad_size_300x250'] = 'ad_size_300x250';
	}

  public function __get($name) {
    return($this->arr[$name]);
  }

  public function __set($name, $value) {
    $this->arr[$name] = $value;
  }

  public function __isset($name) {
    return(isset($this->arr[$name]));
  }

  public function __unset($name) {
    if(isset($this->arr[$name])) {
      unset($this->arr[$name]);
      return true;
    } else {
      return false;
    }
  }
}

$var = new global_var();
define( 'DS', DIRECTORY_SEPARATOR );
