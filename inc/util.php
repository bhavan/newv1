<?php

/***
  some utility functions for the app
  developped by ---
  prasun <pras.svo@gmail.com>
***/

function fprint($param = NULL) {
  static $count;
  if(!isset($count))
    $count = 1;
  else
    $count++;
  echo '<br />';
  echo '<div style="background-color:#fff;"><b><i><u>********* formated output '.$count.' *********</u></i></b>';
  echo '<pre>';
  var_dump($param);
  echo '</pre>';
  echo '<b><i>********* formated output *********</i></b></div>';
  echo '<br />';
}

function _x($param = null) {
  global $var;
  if($param != null) {
    echo '<pre>';
    if(is_array($param) || is_bool($param)) {
      var_dump($param);
    } else {
      echo $param;
    }
    echo '</pre>';
  }
  //include_once($var.'close.tpl');
  exit;
}

function redirect($param) {
  header('Location: '.$param);
  exit();
}

function _404redirect($param) {
  header('HTTP/1.1 404 Not Found');
  header('Location: '.$param);
  exit();
}

function rand_str($length = 8, $chars = 'abcdefghijklmnopqrstuvwxyz1234567890') {
  // Length of character list
  $chars_length = (strlen($chars) - 1);
  // Start our string
  $string = $chars{rand(0, $chars_length)};
  // Generate random string
  for ($i = 1; $i < $length; $i = strlen($string)) {
    // Grab a random character from our list
    $r = $chars{rand(0, $chars_length)};
    // Make sure the same two characters don't appear next to each other
    if ($r != $string{$i - 1}) $string .=  $r;
  }
  // Return the string
  return $string;
}

function _change_file_name($old, $new) {
  $file = explode('/', $old);
  $old = array_pop($file);
  unset($file);
  $ext_arr = explode('.', $old);
  $ext = array_pop($ext_arr);
  unset($ext_arr);
  return $new.'.'.$ext;
}

function _xtract_filename($name) {
  $ext_arr = explode('.', $name);
  $ext = array_pop($ext_arr);
  unset($ext_arr);
  return str_replace('.'.$ext, '', $name);
}

function _gen_month() {
  return(array(
    1   => 'January',
    2   => 'February',
    3   => 'March',
    4   => 'April',
    5   => 'May',
    6   => 'June',
    7   => 'July',
    8   => 'August',
    9   => 'September',
    10  => 'October',
    11  => 'November',
    12  => 'December',
  ));
}

function _gen_month_short() {
  return(array(
    1   => 'jan',
    2   => 'feb',
    3   => 'mar',
    4   => 'apr',
    5   => 'may',
    6   => 'jun',
    7   => 'jul',
    8   => 'aug',
    9   => 'sep',
    10  => 'oct',
    11  => 'nov',
    12  => 'dec',
  ));
}

function mysqldate_to_date($param) {
  $date_arr = explode('-', $param);
  if($date_arr[0] != '0000' && $date_arr[1] != '00' && $date_arr[2] != '00') {
    $month = _gen_month();
    $date = $date_arr[2].'-';
    $date .= substr($month[intval($date_arr[1])], 0, 3).', ';
    $date .= $date_arr[0];
    return $date;
  } else {
    return 'not set';
  }
}

function mysqldate_to_birthday($param) {
  $date_arr = explode('-', $param);
  if($date_arr[0] != '0000' && $date_arr[1] != '00' && $date_arr[2] != '00') {
    $month = _gen_month();
    $date = $month[intval($date_arr[1])].' - ';
    $date .= $date_arr[2];
    return $date;
  } else {
    return 'not set';
  }
}


function mysqltimestamp_to_date($param) {
  $arr = explode(' ', $param);
  return mysqldate_to_date($arr[0]);
}

function mysqltimestamp_to_time($param) {
  $arr = explode(' ', $param);
  return array(
    'date'  => mysqldate_to_date($arr[0]),
    'time'  => $arr[1]
  );
}

function str_to_seourl($param) {
  return urlencode(str_replace(' ', '-', stripslashes($param)));
}

function format_mysqldate($param) {
  $param = explode(' ', trim($param));
  $date_arr = explode('-', $param[0]);
  $return = array();
  if($date_arr[0] != '0000' && $date_arr[1] != '00' && $date_arr[2] != '00') {
    $month = _gen_month_short();
    $return['day'] = $date_arr[2];
    $return['month'] = $month[intval($date_arr[1])];
    $return['year'] = substr(strval($date_arr[0]), 2);
    return $return;
  } else {
    return false;
  }
}

function format_mysqltimestamp($param) {
  $arr = explode(' ', $param);
  if($date = mysqldate_to_date($arr[0])) {
    $date['time'] = $arr[1];
    return $date;
  } else {
    return false;
  }
}

function dateandtime2timestamp($date, $hrs, $mins, $secs = '00') {
  $timestamp = '';
  if($hrs < 10)
    $timestamp .= '0'.$hrs.':';
  else
    $timestamp .= $hrs.':';

  if($mins < 10)
    $timestamp .= '0'.$mins.':'.$secs;
  else
    $timestamp .= $mins.':'.$secs;

  $timestamp = $date.' '.$timestamp;
  return $timestamp;
}

function timestamp2dateandtime($timestamp) {
  $dateandtime = array();
  $tmp_arr = explode(' ', $timestamp);
  $dateandtime['date'] = $tmp_arr[0];
  $tmp_arr = explode(':', $tmp_arr[1]);
  $dateandtime['time'] = array(
    'hrs'   => $tmp_arr[0],
    'mins'  => $tmp_arr[1],
    'secs'  => $tmp_arr[2]
  );
  unset($tmp_arr);
  return $dateandtime;
}

function _clean($param) {
  return mysql_real_escape_string(trim($param));
}

function solve_char_problem($str) {
  $trans_tbl = get_html_translation_table(HTML_ENTITIES);
  foreach($trans_tbl as $k => $v) {
    $str = str_replace($k, $v, $str);
  }
  return trim($str);
}

function unhtmlentities( $string ){
  $trans_tbl = get_html_translation_table ( HTML_ENTITIES );
  $trans_tbl = array_flip( $trans_tbl );
  $ret = strtr( $string, $trans_tbl );
  return preg_replace( '/&#(\d+);/me' , "chr('\\1')" , $ret );
}

function imagecreatefrombmp($p_sFile) {
  //    Load the image into a string
  $file    =    fopen($p_sFile,"rb");
  $read    =    fread($file,10);
  while(!feof($file)&&($read<>""))
    $read    .=    fread($file,1024);
  $temp    =    unpack("H*",$read);
  $hex    =    $temp[1];
  $header    =    substr($hex,0,108);
  //    Process the header
  //    Structure: http://www.fastgraph.com/help/bmp_header_format.html
  if (substr($header,0,4)=="424d") {
    //    Cut it in parts of 2 bytes
    $header_parts    =    str_split($header,2);
    //    Get the width        4 bytes
    $width            =    hexdec($header_parts[19].$header_parts[18]);
    //    Get the height        4 bytes
    $height            =    hexdec($header_parts[23].$header_parts[22]);
    //    Unset the header params
    unset($header_parts);
  }
  //    Define starting X and Y
  $x                =    0;
  $y                =    1;
  //    Create newimage
  $image            =    imagecreatetruecolor($width,$height);
  //    Grab the body from the image
  $body            =    substr($hex,108);
  //    Calculate if padding at the end-line is needed
  //    Divided by two to keep overview.
  //    1 byte = 2 HEX-chars
  $body_size        =    (strlen($body)/2);
  $header_size    =    ($width*$height);
  //    Use end-line padding? Only when needed
  $usePadding        =    ($body_size>($header_size*3)+4);
  //    Using a for-loop with index-calculation instaid of str_split to avoid large memory consumption
  //    Calculate the next DWORD-position in the body
  for ($i=0;$i<$body_size;$i+=3) {
    //    Calculate line-ending and padding
    if ($x>=$width) {
      //    If padding needed, ignore image-padding
      //    Shift i to the ending of the current 32-bit-block
      if ($usePadding)
        $i    +=    $width%4;
      //    Reset horizontal position
      $x    =    0;
      //    Raise the height-position (bottom-up)
      $y++;
      //    Reached the image-height? Break the for-loop
      if ($y>$height)
        break;
    }
    //    Calculation of the RGB-pixel (defined as BGR in image-data)
    //    Define $i_pos as absolute position in the body
    $i_pos    =    $i*2;
    $r        =    hexdec($body[$i_pos+4].$body[$i_pos+5]);
    $g        =    hexdec($body[$i_pos+2].$body[$i_pos+3]);
    $b        =    hexdec($body[$i_pos].$body[$i_pos+1]);
    //    Calculate and draw the pixel
    $color    =    imagecolorallocate($image,$r,$g,$b);
    imagesetpixel($image,$x,$height-$y,$color);
    //    Raise the horizontal position
    $x++;
  }
  //    Unset the body / free the memory
  unset($body);
  //    Return image-object
  return $image;
}

function _ampmto24hrs($hrs, $ampm) {
  $return = $hrs;
  if($hrs < 13) {
    if(strtoupper($ampm) == "AM") {
      if($hrs == 12)
        $return = 0;
    } else {
      if($hrs < 12)
        $return += 12;
    }
  }
  return $return;
}

function _24hrstoampm($hrs) {
  $return = array();
  if($hrs < 12) {
    $return['hrs'] = $hrs;
    $return['ampm'] = "AM";
  } else {
    if($hrs > 12)
      $return['hrs'] = $hrs - 12;
    else
      $return['hrs'] = $hrs;
    $return['ampm'] = "PM";
  }
  return $return;
}


function template_type() {
  return "ajx";
}

function dob_to_sunsign($date) {
  $d_arr = explode('-', $date);
  if(($d_arr[1] == 3 && $d_arr[2] >= 21) || ($d_arr[1] == 4  && $d_arr[2] <= 20)) {
    // Aries - March 21 - April 20
    $return = 'Aries';
  } elseif(($d_arr[1] == 4 && $d_arr[2] >= 21) || ($d_arr[1] == 5  && $d_arr[2] <= 21)) {
    // Taurus - April 21 - May 21
    $return = 'Taurus';
  } elseif(($d_arr[1] == 5 && $d_arr[2] >= 22) || ($d_arr[1] == 6  && $d_arr[2] <= 21)) {
    // Gemini - May 22 - June 21
    $return = 'Gemini';
  } elseif(($d_arr[1] == 6 && $d_arr[2] >= 22) || ($d_arr[1] == 7  && $d_arr[2] <= 22)) {
    // Cancer - June 22 - July 22
    $return = 'Cancer';
  } elseif(($d_arr[1] == 7 && $d_arr[2] >= 23) || ($d_arr[1] == 8  && $d_arr[2] <= 21)) {
    // Leo - July 23 - August 21
    $return = 'Leo';
  } elseif(($d_arr[1] == 8 && $d_arr[2] >= 22) || ($d_arr[1] == 9  && $d_arr[2] <= 23)) {
    // Virgo - August 22 - September 23
    $return = 'Virgo';
  } elseif(($d_arr[1] == 9 && $d_arr[2] >= 24) || ($d_arr[1] == 10  && $d_arr[2] <= 23)) {
    // Libra - September 24 - October 23
    $return = 'Libra';
  } elseif(($d_arr[1] == 10 && $d_arr[2] >= 24) || ($d_arr[1] == 11  && $d_arr[2] <= 22)) {
    // Scorpio - October 24 - November 22
    $return = 'Scorpio';
  } elseif(($d_arr[1] == 11 && $d_arr[2] >= 23) || ($d_arr[1] == 12  && $d_arr[2] <= 22)) {
    // Sagittarius - November 23 - December 22
    $return = 'Sagittarius';
  } elseif(($d_arr[1] == 12 && $d_arr[2] >= 23) || ($d_arr[1] == 1  && $d_arr[2] <= 20)) {
    // Capricorn - December 23 - January 20
    $return = 'Capricorn';
  } elseif(($d_arr[1] == 1 && $d_arr[2] >= 21) || ($d_arr[1] == 2  && $d_arr[2] <= 19)) {
    // Aquarius - January 21 - February 19
    $return = 'Aquarius';
  } elseif(($d_arr[1] == 2 && $d_arr[2] >= 20) || ($d_arr[1] == 3  && $d_arr[2] <= 20)) {
    // Pisces - February 20 - March 20
    $return = 'Pisces';
  }
  return $return;
}


