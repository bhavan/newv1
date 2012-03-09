<?php

function session_init() {
  global $var, $session;
  session_save_path($var->tmp_path.'session/');
  ini_set('session.gc.maxlifetime',3600*24*30);
  @session_start();
  $session = $_SESSION;
  if(sget('is_online')) {
    $var->is_online = true;
    $var->user = sget('user');
    sunset('user');
  } else {
    $var->is_online = false;
  }
  register_shutdown_function('session_cleanup');
}

function session_login() {
  global $var;
  sset('is_online',true);
  sset('userid', $var->user['id']);
  sset('email',$var->user['email']);
  sset('user',$var->user);
  sset('currentlyat',$var->user['currently_at']);
}

function session_logout() {
  sset('is_online',false);
  sunset('userid',null);
  sunset('email',null);
  sunset('user');
  session_destroy();
}

function sisset($key) {
  global $session;
  if(isset($session[$key])) {
    return true;
  } else {
    return false;
  }
}

function sget($key) {
  global $session;
  if(sisset($key)) {
    return $session[$key];
  } else {
    return false;
  }
}

function sset($key,$value) {
  global $session;
  $session[$key] = $value;
}

function sunset($key) {
  global $var, $session;
  unset($session[$key]);
  // fprint($key);
  // fprint($session);
}

function session_cleanup() {
  global $var, $session;
  if($var->is_online) {
    $session['user'] = $var->user;
  }
  $_SESSION = $session;
  unset($session);
  @session_write_close();
}
