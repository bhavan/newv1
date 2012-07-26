<?php

/***
  base functions for the app
  developped by ---
  prasun <pras.svo@gmail.com>
***/

function _init() {
  global $var;
  include_once($var->inc_path.'util.php');
  include_once($var->inc_path.'session.php');
  include_once($var->inc_path.'db.php');
  include_once($var->inc_path.'validate.php');
  include_once($var->inc_path.'mbase.php');
  date_default_timezone_set(@date_default_timezone_get());
  db_init();
  session_init();
  $var->get = $_GET;
  $var->post = $_POST;
  if(isset($var->post['formname'])) {
    validate();
  }
  $var->request_uri = $_SERVER['REQUEST_URI'];
  if ($_SERVER['REQUEST_URI']=='/')
  $var->request_uri='/index.php';
  $requestarr=explode('?',$var->request_uri);
  if ($requestarr)
  $var->request_uri=$requestarr[0];
  
  if(isset($_SERVER['HTTP_REFERER'])) {
    $var->http_referer = $_SERVER['HTTP_REFERER'];
  } else {
    $var->http_referer = '/';
  }
  load_config();
  $var->abs_srv_path = str_replace('testsite'.DS.'inc', '', dirname(__FILE__));
  $var->abs_srv_path = str_replace('inc', '', dirname(__FILE__));
  //fprint(dirname(__FILE__));
}

function load_config() {
  global $var;

  $pageglobal = db_fetch("select * from `jos_pageglobal`");
  $pagemeta = db_fetch("select * from `jos_pagemeta` where `uri` = '".$var->request_uri."'");
  $pagejevent = db_fetch("select * from `jos_components` where `option`='com_jevlocations'");
 
  $gmapkeys=explode('googlemapskey=',$pagejevent['params']);
  $gmapkeys1=explode("\n",$gmapkeys[1]);

  $var->site_name = $pageglobal['site_name'];
  $var->beach = $pageglobal['beach'];
  $var->email = $pageglobal['email'];
  $var->googgle_map_api_keys = $gmapkeys1[0];
  $var->location_code = $pageglobal['location_code'];
  $var->photo_mini_slider_cat = $pageglobal['photo_mini_slider_cat'];
  $var->photo_upload_cat = $pageglobal['photo_upload_cat'];
  $var->facebook = $pageglobal['facebook'];
  $var->iphone = $pageglobal['iphone'];
  $var->android = $pageglobal['android'];
  $var->googgle_analytics=$pageglobal['googgle_map_api_keys'];

  $var->page_title = isset($pagemeta['title'])?$pagemeta['title']:'';
  $var->metadesc = isset($pagemeta['metadesc'])?$pagemeta['metadesc']:'';
  $var->keywords = isset($pagemeta['keywords'])?$pagemeta['keywords']:'';
  $var->extra_meta = isset($pagemeta['extra_meta'])?$pagemeta['extra_meta']:'';

}

function _gen_country() {
  global $var;
  $qr = mysql_query('select * from country');
  //var_dump($qr); exit;
  $country = array();
  while($row = mysql_fetch_assoc($qr)) {
    $country[$row['id']] = $row['name'];
  }
  $var->country = $country;
}

function send_email($param) {
  mysql_query("insert into `email` set 
    `to` = '{$param['to']}', 
    `from` = '{$param['from']}', 
    `subject` = '{$param['subject']}', 
    `body` = '{$param['body']}'"
  );
}

function headbuf($flag = 'start') {
  global $var;
  if($flag == 'start') {
    ob_start();
  } else {
    $var->header_content .= str_replace('<link href="/common/css/style.css" rel="stylesheet" type="text/css" />', '', ob_get_contents());
    ob_end_clean();
  }
}

function str_to_date($param) {
  global $var;
  $date_arr = explode('-', $param);
  if($date_arr[0] != '0000' && $date_arr[1] != '00' && $date_arr[2] != '00') {
    _gen_month();
    $date = $date_arr[2].'-';
    $date .= substr($var->month[intval($date_arr[1])], 0, 3).', ';
    $date .= $date_arr[0];
    return $date;
  } else {
    return 'not set';
  }
}

function file_upload($param) {
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

function avatar_url($param) {
  /*
  $example_param = array(
    'ctype'   => 'user',
    'mapid'   => 1,
    'size'    => s,
  );
  */
  //var_dump($param);
  $file_path = "media/photo/".$param['ctype']."/".intval($param['mapid']/10000)."/".$param['mapid']."/a".$param['size'].".jpg";
  //echo $file_path;
  if(file_exists($file_path)) {
    return '/'.$file_path;
  } else {
    return "/media/photo/".$param['ctype']."/default".$param['size'].".jpg";
  }
}

function str_to_time($param) {
  $arr = explode(' ', $param);
  return str_to_date($arr[0]).' '.$arr[1];
}

function acctype($id) {
  if($id == 0) {
    return "Normal";
  } elseif($id == 100) {
    return "Admin";
  }
}

function _gen_page() {
  global $var;
  $items_per_page = 10;
  if(isset($var->items_per_page) && is_numeric($var->items_per_page)) {
    $items_per_page = $var->items_per_page;
  }
  $return = '';
  if(isset($var->row_count[$var->request_uri])) {
    $count = $var->row_count[$var->request_uri];
    $page_count = intval($count/$items_per_page);
    $rem = intval($count%$items_per_page);
    if($rem > 0) { $page_count = $page_count + 1; }
    if(isset($var->get['page'])) {
    $first_page = (intval($var->get['page']/$items_per_page)*$items_per_page)+1;
     $current_page = $var->get['page'];
      if(strpos($var->request_uri, '&page'))
        $uri = str_replace('&page='.$current_page, '', $var->request_uri);
      else
        $uri = str_replace('page='.$current_page, '', $var->request_uri);
    } else {
      $first_page = 1;
      $current_page = 1;
      $uri = $var->request_uri;
    }
    //echo $first_page;
    //echo $current_page;
    if(strpos($var->request_uri, '?')) {
      $uri .= '&';
    } else {
      $uri .= '?';
    }
    if($first_page > $items_per_page) {
      $return .= "<a href='".$uri."page=".($first_page - $items_per_page)."' class='active'>".($first_page - $items_per_page)."</a>"."<span class='inactive'>...</span>";
    }
    for($i = $first_page; $i <= $page_count; $i++) {
      if($i == ($first_page+$items_per_page)) {
        break;
      }
      if($i == $current_page) {
        $return = $return."<span class='inactive'>".$i."</span>";
      } else {
        $return = $return."<a href='".$uri."page=".$i."' class='active'>".$i."</a>";
      }
    }
  } else {
    $return = false;
  }
  return $return;
}

function _gender($param) {
  if($param == M) {
    return "his";
  } else {
    return "her";
  }
}

function Gender($param) {
  if($param == M) {
    return "His";
  } else {
    return "Her";
  }
}

function name_to_url($param) {
  return urlencode(str_replace(" ", "_", stripslashes($param)));
}

function url_to_name($param) {
  return str_replace("_", " ", urldecode($param));
}

function location_from_id($id) {
  $result = db_fetch("select title from `jos_jev_locations` where `loc_id` = ".$id);
  return $result['title'];
}

function loc_cat_from_id($id) {
  $result = db_fetch("select title from `jos_categories` where `id` = ".$id);
  return $result;
}

function plural_filter($param) {
  if(strstr($param, 'Hotels'))
    return str_replace('Hotels', 'Hotel', $param);
  else if(strstr($param, 'Restaurants'))
    return str_replace('Restaurants', 'Restaurant', $param);
  else
    return $param;
}

function id_from_phoca_cat($cat) {
  $result = db_fetch("select `id` from `jos_phocagallery_categories` where `title` = '".$cat."'");
  return $result;
}

/***************** [START]  module functions  [START] ******************/

/***************** [START]  module functions  [START] ******************/
/* ORIGINAL V1 FUNCTION */
/*
function m_header() {
	global $var;
	require($var->tpl_path."header.tpl");
}
*/

function m_header() {
	global $var;
	if($_SESSION['tpl_menu_folder_name'] == "default" || $_SESSION['tpl_menu_folder_name'] == "defaultspanish" ) {
		require($var->tpl_path."header.tpl");
	} else {
		require("partner/".$_SESSION['tpl_menu_folder_name']."/tpl/header.tpl");
	}
}

function m_footer() {
  global $var;
  require($var->tpl_path."footer.tpl");
}

function m_aside() {
  global $var;

  $todaydate = date("Y-m-j",strtotime("+1 day"));
  $today = date("Y-m-d G:i:s");
  $sql = "select jc.* from `jos_content` jc, `jos_categories` jcs where jcs.title = 'Today' and jcs.id = jc.catid and jc.state=1 and (jc.publish_down>'".$today."' or jc.publish_down='0000-00-00 00:00:00') and (jc.publish_up <= '".$todaydate."' or jc.publish_up='0000-00-00 00:00:00') order by jc.ordering";
  $param = db_fetch($sql, true, true);
  //fprint($sql);
  //fprint($param);
  $data = '';
  $c=1;
  if($param) { foreach($param as $v) {
    if(!strstr($v['introtext'], '<p>')) {
      $v['introtext'] = '<p>'.$v['introtext'].'</p>';
    }
    $data .= str_replace("images/", "/images/", $v['introtext']);
	$data .= '<hr />';
    $c++;
  } }
  require($var->tpl_path."aside.tpl");
}

function m_featured_event() {
  global $var;
  //$sql = "select *, DATE_FORMAT(FROM_UNIXTIME(dtstart),'%D %b, %Y') as `start`, DATE_FORMAT(FROM_UNIXTIME(dtend),'%D %b, %Y') as `end` from `jos_jevents_vevdetail` where DATE_FORMAT(FROM_UNIXTIME(dtend),'%Y-%m-%d') >= CURDATE() and `priority` = 1 order by `dtstart` limit 1";
  $sql = "select jjv.*,jjr.rp_id, jjr.startrepeat, DATE_FORMAT(jjr.startrepeat,'%D %b, %Y') _dateF, DATE_FORMAT(jjr.startrepeat,'%h:%i %p') as timestart, DATE_FORMAT(jjr.endrepeat,'%h:%i %p') as timeend, noendtime from `jos_jevents_vevdetail` jjv, `jos_jevents_repetition` jjr, `jos_jev_customfields` jjc where jjv.state = 1 and jjv.evdet_id = jjr.eventdetail_id and jjv.evdet_id = jjc.evdet_id and jjc.value = 1 and jjr.endrepeat >= CURRENT_TIMESTAMP  order by jjr.endrepeat limit 1";
  $data = db_fetch($sql);
  if(!$data) {
    $sql = "select jjv.*,jjr.rp_id, jjr.startrepeat, DATE_FORMAT(jjr.startrepeat,'%D %b, %Y') _dateF, DATE_FORMAT(jjr.startrepeat,'%h:%i %p') as timestart, DATE_FORMAT(jjr.endrepeat,'%h:%i %p') as timeend from `jos_jevents_vevdetail` jjv, `jos_jevents_repetition` jjr where jjv.state=1 and jjv.evdet_id = jjr.eventdetail_id and jjr.endrepeat >= CURRENT_TIMESTAMP order by jjr.endrepeat limit 1";
    $data = db_fetch($sql);
  }
  //fprint($data); _x();
  $data['location'] = db_fetch("select title, street, city, state, postcode from `jos_jev_locations` where `loc_id` = ".$data['location']);
  $temp = explode(' ', $data['startrepeat']);
  $data['_date'] = $temp[0];
  //fprint($sql); fprint($data); _x();
  require($var->tpl_path."featured_event.tpl");
}

function m_event_list_intro() {
  global $var;
  // $header = "Event Calendar";
  $intro = db_fetch("select introtext from `jos_content` where `title` = 'Events Page Introduction'");
  require($var->tpl_path."event_list_intro.tpl");
}

function m_event_list() {
  global $var;
  $datamodel = new JEventsDataModel();
  require($var->tpl_path."event_list.tpl");
}

function m_photos_mini($cat = 'Events', $limit = 15) {
  global $var;
  $album = db_fetch("select `id`, `userfolder` from `jos_phocagallery_categories` where title = '".$var->photo_mini_slider_cat."'");
  $cat = $album['id'];
  $cat_folder = $album['userfolder'];
  $param = db_fetch("select *, DATE_FORMAT(`date`, '%D %M, %Y') date_taken from `jos_phocagallery` where `catid` = ".$cat." and `published` = 1 and `approved` = 1 order by date desc limit $limit", true, true);
  //fprint($param); _x();
  require($var->tpl_path."photos_mini.tpl");
}

function m_visiting_intro() {
  global $var;
  $param = db_fetch("select * from `jos_content` where `id` = 77");
  $introtext = str_replace("images/", "images/", $param['introtext']);
  require($var->tpl_path."visiting.tpl");
}

function m_photo_albums() {
  global $var;
  $param = db_fetch("select * from `jos_phocagallery_categories` where id != 2 and `published` = 1 and `approved` = 1 order by ordering asc", true, true);
  foreach($param as $k => $v) {
    $v['photos'] = db_fetch("select id, filename from `jos_phocagallery` where `published` = 1 and `approved` = 1 and `catid` = ".$v['id']);
    $id = rand(0, (count($v['photos']) - 1));
    //fprint($v['photos'][$id]['filename']);
    /* $v['userfolder'] = trim($v['userfolder']);
    if($v['userfolder'] != '') {
      $v['userfolder'] = $v['userfolder'].'/';
    } */
    $tmp_arr = explode('/', $v['photos'][$id]['filename']);
    $userfolder = '';
    $filename = $v['photos'][$id]['filename'];
    if(count($tmp_arr) > 1) {
      $userfolder = $tmp_arr[0].'/';
      $filename = $tmp_arr[1];
    }
    unset($tmp_arr);
    if(trim($userfolder) == '' && trim($filename) == '')
      $param[$k]['avatar'] = '';
    else
      $param[$k]['avatar'] = '/partner/'.$_SESSION["partner_folder_name"].'/images/phocagallery/'.$userfolder.'thumbs/phoca_thumb_m_'.$filename;
  }
  /* fprint($param);
  foreach($param as $v) {
    echo $v['id'].' - '.$v['title'].'<br />';
  }
  _x(); */
  require($var->tpl_path."photo_albums.tpl");
}


function m_photos() {
  global $var;
  $param['album'] = db_fetch("select * from `jos_phocagallery_categories` where `id` = ".$var->get['album_id']);
  $param['photos'] = db_fetch("select * from `jos_phocagallery` where `catid` = ".$var->get['album_id']." and `published` = 1 and `approved` =1");
  //fprint($param); _x();
  require($var->tpl_path."photos.tpl");
}

function m_events_this_week() {
  global $var;
  $datamodel = new JEventsDataModel();
  require($var->tpl_path."events_this_week.tpl");
}

function m_dining_intro() {
  global $var;
  $text = db_fetch("select `introtext` from `jos_content` where `title` = 'Dining Page Introduction'");
  echo $text;
}
// Created for Places intro text V2
function m_places_intro() {
  global $var;
  $text = db_fetch("select `introtext` from `jos_content` where `title` = 'Places Page Introduction'");
  echo $text;
}
function m_location_list($cat, $featured = true, $searchedText='') {
  global $var;
  $title = '';
  if(is_array($cat)) {
    $cat_arr = $cat;
    $cat = "";
    $first = true;
    foreach($cat_arr as $v) {
      if($first) {
        $title .= $v;
        $cat .= "'$v'";
        $first = false;
      } else {
        $title .= " &amp; $v";
        $cat .= ",'$v'";
      }
    }
  } else {
    $title = $cat;
    $cat = "'$cat'";
  }
  
	//#DD#
	$additionalWhere = '';
	if($searchedText!=''){
		$additionalWhere = " AND (jjl.description LIKE '%$searchedText%' OR jjl.title LIKE '%$searchedText%')";
	}

  if($featured) 
    $sql = "select jjl.loc_id, jjl.title, jjl.street, jjl.phone, jjl.loccat from `jos_jev_locations` jjl, jos_jev_customfields3 jjc where jjl.loc_id = jjc.target_id and jjc.value = 1 and jjl.published=1 $additionalWhere order by jjl.title ";
	//jjl.loccat = jc.id and and jc.title in($cat) 
  else
 $sql = "select jjl.loc_id, jjl.title, jjl.street, jjl.phone, jjl.loccat from `jos_jev_locations` jjl, `jos_categories` jc where jjl.loccat = jc.id and jc.title in($cat) and jjl.published=1 $additionalWhere order by jjl.title";
	
  //fprint($sql);
  $data = db_fetch($sql, true, true);
  //fprint($sql); fprint($data); _x();
  require($var->tpl_path."location_list.tpl");
}

function m_location_count($cat, $featured = true, $searchedText='') {
  global $var;
  $title = '';
  if(is_array($cat)) {
    $cat_arr = $cat;
    $cat = "";
    $first = true;
    foreach($cat_arr as $v) {
      if($first) {
        $title .= $v;
        $cat .= "'$v'";
        $first = false;
      } else {
        $title .= " &amp; $v";
        $cat .= ",'$v'";
      }
    }
  } else {
    $title = $cat;
    $cat = "'$cat'";
  }
  
	//#DD#
	$additionalWhere = '';
	if($searchedText!=''){
		$additionalWhere = " AND (jjl.description LIKE '%$searchedText%' OR jjl.title LIKE '%$searchedText%')";
	}

  if($featured) 
    $sql = "select jjl.loc_id, jjl.title, jjl.street, jjl.phone, jjl.loccat from `jos_jev_locations` jjl, jos_jev_customfields3 jjc where jjl.loc_id = jjc.target_id and jjc.value = 1 and jjl.published=1 $additionalWhere order by jjl.title ";
	//jjl.loccat = jc.id and and jc.title in($cat) 
  else
   $sql = "select jjl.loc_id, jjl.title, jjl.street, jjl.phone, jjl.loccat from `jos_jev_locations` jjl, `jos_categories` jc where jjl.loccat = jc.id and jc.title in($cat) and jjl.published=1 $additionalWhere order by jjl.title";
  //fprint($sql);
  $data =mysql_query($sql);
  //fprint($sql); fprint($data); _x();

if($data)
	return mysql_num_rows($data);
else
	return 0;
}

function m_upload_photo() {
  global $var;
 		 
		
  if(isset($var->post['formname']) && $var->post['formname'] == 'upload.event.photo') {
    //fprint($_FILES); _x();
	
		$filename = $_FILES['image']['name'];
		
		$ext = substr($filename,(strpos($filename,'.') + 1));
			
		if(empty($filename) || $filename == ""){
			
			$msg="Choose image for upload!";
			
		}
		
		else if($ext == "gif" || $ext == "GIF" || $ext == "JPEG" || $ext == "jpeg" || $ext == "jpg" || $ext == "JPG" || $ext == "PNG" || $ext == "png")
		{
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
		          `catid` = ".id_from_phoca_cat($var->photo_upload_cat).", 
		          `title` = '"._clean($var->post['caption'])."', 
		          `alias` = '"._clean(str_replace(' ', '-', strtolower(trim($var->post['caption']))))."', 
		          `filename` = '".$image."', 
		          `approved` = 0, 
		          `description` = '"._clean($var->post['description'])."', 
		          `metadesc` = '".serialize($metadesc)."'";
		      //fprint($sql);
		      db_insert($sql);
		      @copy($var->joomla_root.'partner/'.$_SESSION["partner_folder_name"].'/images'.DS.'phocagallery'.DS.'thumbs'.DS.$image, $var->joomla_root.'partner/'.$_SESSION["partner_folder_name"].'/images'.DS.'phocagallery'.DS.$image);
		      @unlink($var->joomla_root.'partner/'.$_SESSION["partner_folder_name"].'/images'.DS.'phocagallery'.DS.'thumbs'.DS.$image);
		      $var->photo_uploaded = true;
			 
		    }
		
		    //#DD#
		    if($var->photo_uploaded==true)
		    {
					$rec = mysql_query("SELECT * FROM `jos_users` WHERE `id`=62");
					$pageglobal=mysql_fetch_array($rec);
					$adminEmail = $pageglobal['email'];
		            //$adminEmail	= $adminuser->email;
					$sitename =  $pageglobal['site_name'];
					$subject= 'New Photo Uploaded ';
					$message = '
					<table cellpadding="3" cellspacing="3">
					<tr><td colspan="2" align="left"><h2>Photo Details</h2></td></tr>
					<tr><td align="left"><b>Photo</b> </td><td>: '. "http://{$_SERVER['HTTP_HOST']}/partner/{$_SESSION["partner_folder_name"]}/images/phocagallery/$image".'</td></tr>
					<tr><td align="left"><b>Photo Caption</b> </td><td>: '.$var->post['caption'].'</td></tr>
					<tr><td align="left"><b>Photo Description</b> </td><td>: '.$var->post['description'].'</td></tr>
					<tr><td align="left"><b>Name</b> </td><td>: '.$var->post['username'].'</td></tr>
					<tr><td align="left"><b>Hometown</b> </td><td>: '.$var->post['location'].'</td></tr>
					</table>';
					$headers = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type:text/html;charset=iso-8859-1' . "\r\n";
					
					$_SERVER['SERVER_NAME'] = str_replace('www.', '', $_SERVER['SERVER_NAME']);
					
					$headers .= 'From: NO-REPLY <admin@'.$_SERVER['SERVER_NAME'].'>' . "\r\n";
					$headers .= 'X-Mailer: PHP/' . phpversion() . "\r\n";
					// Email Notification to Administrator
					mail($adminEmail,$subject,$message,$headers);
		    }
		} 
		else
		{
			$msg="File Type must be GIF or JPG or PNG images only!";
			$filename="";
			//return false;
		}
    
    //#DD#


    //fprint($var->post); _x();
  }
  require($var->tpl_path."upload_photo.tpl");
}

function m_show_banner($cat) {
  global $var;
  $partnerBannerImg = $_SESSION["partner_folder_name"];
  $sql = "select b.* from `jos_banner` b, `jos_categories` c where c.title = '".$cat."' and c.id = b.catid and b.showBanner = 1";
  $data = db_fetch($sql, true, true);
  $d = array();
  if(count($data) > 1) {
    $d = $data[rand(0, (count($data) - 1))];
  } else {
    $d = $data[0];
  }
  //fprint($d); _x();
  if ($d['custombannercode'] != "")
    echo $d['custombannercode'];
  else{
	  $url=$d['clickurl'];
	if (strpos('aaa'.$url,'mailto:') || strpos('aaa'.$url,'tel:'))
	 echo '<a href="/adsclick.php?option=com_banners&task=click&bid='.$d['bid'].'"><img src="/partner/'.$partnerBannerImg.'/images/banners/'.$d['imageurl'].'" alt="'.$d['name'].'" title="'.$d['name'].'" /></a>';
	 else
    echo '<a href="/adsclick.php?option=com_banners&task=click&bid='.$d['bid'].'" target="_blank"><img src="/partner/'.$partnerBannerImg.'/images/banners/'.$d['imageurl'].'" alt="'.$d['name'].'" title="'.$d['name'].'"/></a>';
}
// for Impressions: track the number of times the banner is displayed to web site visitors.
  $sql = 'UPDATE jos_banner SET impmade = impmade + 1 WHERE bid =' .$d['bid'];
  db_update($sql); 
}

function m_article($title) {
  global $var;
  if($param = db_fetch("select `introtext` from `jos_content` where `title` like '".$title."'")) {
    $param = str_replace("images/", "images/", $param);
    echo $param;
  } else {
    echo '';
  }
  //require($var->tpl_path."visiting.tpl");
}

function m_list_videos() {
  global $var;
  $var->items_per_page = 6 ;
  $sql = "select * from `jos_phocagallery` where `catid` = ".id_from_phoca_cat('Videos')." and `published` = 1 order by id desc";
  //fprint($sql);
  $param = db_fetch($sql, true);
  //fprint($param); _x();
  require($var->tpl_path."videos.tpl");
}

function m_event_submit() {
  global $var;
  $param = db_fetch("select `id`, `title` from `jos_categories` where `section` = 'com_jevents'", true, true);
  require($var->tpl_path."event_submit.tpl");
}


function j_locations() {
  global $var;
  $var->zoomla_fname = "location";
  return call_joomla_mobile_function("_z1239");
}

function j_photo_albums() {
  global $var;
  $var->zoomla_fname = "photo_albums";
  call_joomla_mobile_function("_z1240");
}

function j_videos() {
  global $var;
  $var->zoomla_fname = "videos";
  call_joomla_mobile_function("_z1241");
}

function j_events() {
  global $var;
  $var->zoomla_fname = "events";
  call_joomla_mobile_function("_z1242");
}


/***************** [END]  module functions  [END] **********************/
