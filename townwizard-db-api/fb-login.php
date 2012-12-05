<?php

require_once('../jevents.php');
include_once('user-api.php');

$location = $_GET['l'];
$user_id = $_GET['uid'];

if(empty($location) or empty($user_id)) {
  echo '<script>window.location.href="'.TOWNWIZARD_DB_FB_LOGIN_URL.'?l="+window.opener.location.href;</script>';
} else {

  tw_login_with_id($user_id);

  echo '<script>';    
  echo 'window.opener.location.href="'.$location.'";';
  echo 'window.close();';
  echo '</script>';
}

?>