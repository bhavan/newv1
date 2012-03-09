<?php

function db_init() {
  global $var;
  $link = @mysql_pconnect(DB_HOST, DB_USER, DB_PASSWORD);
  $var->db_link = $link;
  
  if(!$link) {
    return false;
  } else {
    mysql_select_db(DB_NAME);
  }
}

function _init() {
  global $var;
  
  db_init();
 	
  $var->get = $_GET;
  $var->post = $_POST;
	$var->joomla_root = '../';	
	define( 'DS', '/' );
}


function _clean($param) {
  return mysql_real_escape_string(trim($param));
}

function db_fetch($sql, $list = false, $all = false) {
  global $var;
  $items_per_page = 10;
  if(isset($var->items_per_page) && is_numeric($var->items_per_page)) {
    $items_per_page = $var->items_per_page;
  }
  $result = array();
  $tmp = $var->tmp;
  if(isset($tmp[$sql])) {
    unset($result);
    return $tmp[$sql];
  } else {
    //echo(str_replace(substr($sql, 0, strpos($sql, "from")), "select count(*) ", $sql));
    if($list && $all == false && strpos(strtolower($sql), "limit") === false) {
      //echo str_replace(substr($sql, 0, strpos($sql, "from")), "select count(*) ", $sql);
      $tmp_qr = @mysql_query(str_replace(substr($sql, 0, strpos($sql, "from")), "select count(*) ", $sql));
      $count = @mysql_fetch_row($tmp_qr);
      if($count === false) {
        $var->row_count = array( $var->request_uri => 0);
      } else {
        $var->row_count = array( $var->request_uri => $count[0]);
      }
      if(isset($var->get['page'])) {
        $sql = $sql." limit ".(($var->get['page']-1)*$items_per_page).", ".$items_per_page;
      } else {
        $sql = $sql." limit 0, ".$items_per_page;
      }
    }
    $qr = mysql_query($sql);
    if($qr !== false) {
      if(mysql_num_rows($qr) > 1) {
        while($row = mysql_fetch_assoc($qr)) {
          $result[] = $row;
        }
      } else {
        if(mysql_num_rows($qr) == 1) {
          if($list) {
            $result[] = mysql_fetch_assoc($qr);
          } elseif(mysql_num_fields($qr) ==  1) {
            $r = mysql_fetch_row($qr);
            $result = $r[0];
            unset($r);
          } else {
            $result = mysql_fetch_assoc($qr);
          }
        } else {
          $result = mysql_fetch_assoc($qr);
        }
      }
      $tmp[$sql] = $result;
    } else {
      $result = false;
    }
    $var->tmp = $tmp;
    unset($tmp);
    return $result;
  }
}

function id_from_phoca_cat($cat) {
  $result = db_fetch("select `id` from `jos_phocagallery_categories` where `title` = '".$cat."'");
  return $result;
}


function db_insert($sql) {
  global $var;
  if(mysql_query($sql)) {
    return mysql_insert_id();
  } else {
    return false;
  }
}


function file_upload($param) {

  if(isset($_FILES[$param['name']]['name'])) {
    //$name = _change_file_name($_FILES[$param['name']]['name'], $param['savename']);
    $name = $param['savename'];
    //fprint($_FILES[$param['name']]['tmp_name']);
    //fprint($param['path'].$name);
    
    if(move_uploaded_file($_FILES[$param['name']]['tmp_name'], $param['path'].$name)) {
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




function m_upload_photo($pageglobal) {
  global $var;
  if(isset($var->post['formname']) && $var->post['formname'] == 'upload.event.photo') {
    //fprint($_FILES); _x();
    if(($image = file_upload(array(
      'name'      => 'image',
      'path'      => $var->joomla_root.'partner/'.$_SESSION["partner_folder_name"].'/images'.DS.'phocagallery'.DS.'thumbs'.DS,
      'savename'  => $_FILES['image']['name']
    ))) != false) {
      //fprint($image);
      //echo $var->abs_srv_path.'images'.DS.'phocagallery'.DS.'thumbs'.DS;
      image_convert(array(
        'path'  => $var->joomla_root.'partner/'.$_SESSION["partner_folder_name"].'/images'.DS.'phocagallery'.DS.'thumbs'.DS,
        'name'  => $image,
        'size'  => array(
          'phoca_thumb_l_' => '600px',
          'phoca_thumb_m_' => '180px',
          'phoca_thumb_s_' => '60px',
        ),
      ));
      $metadesc = array(
        'username' => $var->post['username'],
        'location' => $var->post['location']
      );
      $sql = "insert into `jos_phocagallery` set 
          `catid` = ".id_from_phoca_cat($pageglobal['photo_upload_cat']).", 
          `title` = '"._clean($var->post['caption'])."', 
          `alias` = '"._clean(str_replace(' ', '-', strtolower(trim($var->post['caption']))))."', 
          `filename` = '".$image."', 
          `approved` = 0, 
          `description` = '"._clean($var->post['description'])."', 
          `metadesc` = '".serialize($metadesc)."'";
      
     
      db_insert($sql);
      @copy($var->joomla_root.'partner/'.$_SESSION["partner_folder_name"].'/images'.DS.'phocagallery'.DS.'thumbs'.DS.$image, $var->joomla_root.'partner/'.$_SESSION["partner_folder_name"].'/images'.DS.'phocagallery'.DS.$image);
      @unlink($var->joomla_root.'partner/'.$_SESSION["partner_folder_name"].'/images'.DS.'phocagallery'.DS.'thumbs'.DS.$image);
      $var->photo_uploaded = true;
    }
   
  }

}

?>