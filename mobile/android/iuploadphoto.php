<?php

include("connection.php");
define(ABS_SRV_PATH,'../');
define(DS,'/');
//////////////////////////////////////////////////////////////////////////////////////////////// Additional function ///////////

function file_upload($param) {
  //var_dump($_FILES);
  //var_dump($param);
  //fprint($param);
  if(isset($_FILES[$param['name']]['name'])) {
    //$name = _change_file_name($_FILES[$param['name']]['name'], $param['savename']);
    $name = $param['savename'];
    //fprint($_FILES[$param['name']]['tmp_name']);
    //fprint($param['path'].$name);
    if(@move_uploaded_file($_FILES[$param['name']]['tmp_name'], $param['path'].$name)) {
      //exit("Yuhoooo!");
      return $name;
    }
  }
  return false;
}

function image_convert($param) {
  ini_set('max_execution_time', 40);
  ini_set('memory_limit', "128M");
  if(!is_file($param['path'].$param['name'])) {
    return false;
  }
  if(false == ($prop = @getimagesize($param['path'].$param['name']))) {
    return false;
  }
  switch($prop[2]) {
    case 1: //GIF
      $image = imagecreatefromgif($param['path'].$param['name']);
    break;
    case 2: //JPG
      $image = imagecreatefromjpeg($param['path'].$param['name']);
    break;
    case 3: //PNG
      $image = imagecreatefrompng($param['path'].$param['name']);
    break;
    case 15: //WBMP
      $image = imagecreatefromwbmp($param['path'].$param['name']);
    break;
  }
	
  $name = str_replace('o', '', $param['name']);
  $src_w = $prop[0];
  $src_h = $prop[1];
  $percent = $src_h/$src_w;
  //fprint($prop); _x('image_convert');
  foreach($param['size'] as $key => $dst_w) {
    if($dst_w <= $src_w) {
      $dst_h = intval($dst_w * $percent);
    } else {
      $dst_w = $src_w;
      $dst_h = $src_h;
    }
    $canvas = imagecreatetruecolor($dst_w, $dst_h);
    imagecopyresampled($canvas, $image, 0, 0, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
    //echo $param['path'].$name;
    //@unlink($param['path'].$name);
    $name_arr = explode(DS, $param['path'].$name);
    $new_name = array_pop($name_arr);
    $new_name = $key.$new_name;
    unset($name_arr);
    //imagejpeg($canvas, preg_replace('/(.*)\.(\w+)$/', '$1'.$key.'.jpg', $param['path'].$name));
    imagejpeg($canvas, $param['path'].$new_name);
    imagedestroy($canvas);
  }
  return true;
}

function _clean($param) {
  return mysql_real_escape_string(trim($param));
}

//////////////////////////////////////////////////////////////////////////////////////////////// Additional function ///////////

if(($image = file_upload(array(
  'name'      => 'userphoto',
  'path'      => ABS_SRV_PATH.'images'.DS.'phocagallery'.DS.'thumbs'.DS,
  'savename'  => $_FILES['userphoto']['name']
))) != false) {
  //fprint($image);
  //echo ABS_SRV_PATH.'images'.DS.'phocagallery'.DS.'thumbs'.DS;
  image_convert(array(
    'path'  => ABS_SRV_PATH.'images'.DS.'phocagallery'.DS.'thumbs'.DS,
    'name'  => $image,
    'size'  => array(
      'phoca_thumb_l_' => '600px',
      'phoca_thumb_m_' => '180px',
      'phoca_thumb_s_' => '60px',
    ),
  ));
  $metadesc = array(
    'username' => $_POST['username'],
    'location' => $_POST['useremail']
  );
	$image1=rand().$image;
 $sql = "insert into `jos_phocagallery` set 
      `catid` = 8, 
      `title` = '"._clean($_POST['caption'])."', 
      `alias` = '"._clean(str_replace(' ', '-', strtolower(trim($_POST['caption']))))."', 
      `filename` = '".$image1."', 
      `approved` = 0, 
      `description` = '"._clean($_POST['description'])."', 
      `metadesc` = '".serialize($metadesc)."'";
  //fprint($sql);
  mysql_query($sql);
  @copy(ABS_SRV_PATH.'images'.DS.'phocagallery'.DS.'thumbs'.DS.$image, ABS_SRV_PATH.'images'.DS.'phocagallery'.DS.$image1);
  @unlink(ABS_SRV_PATH.'images'.DS.'phocagallery'.DS.'thumbs'.DS.$image);
  echo '1';
} else {
  echo '0';
}

//echo "<br />".ABS_SRV_PATH;

?>