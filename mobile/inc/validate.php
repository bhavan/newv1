<?php

function validate() {
  global $var;
  $var->validate = true;
  $error = array();
  $post = $var->post;
  unset($var->post);
  foreach($post as $key => $value) {
    $field = explode('-', $key);
    if(isset($field[1]) && $field[1] == 'v') {
      if(!isset($value) || trim($value) == '') {
        $error[$field[0]] = ucfirst(str_replace('_', ' ', $field[0]))." cannot be left blank!";
        if($var->validate) {
          $var->validate = false;
        }
      }
      unset($post[$key]);
      $post[$field[0]] = $value;
    }
  }
  $var->post = $post;
  if(count($error) > 0) {
    $var->error = $error;
  }
}


function process_f_name($param) {
  global $var;
  call_user_func("_".$param[$var->zoomla_fname]."_".template_type());
}

