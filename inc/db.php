<?php

function db_init() {
  global $var;
  $link = @mysql_pconnect($var->db['server'], $var->db['user'], $var->db['password']);
  $var->db_link = $link;
  if(!$link) {
    return false;
  } else {
    mysql_select_db($var->db['database']);
  }
}

function db_insert($sql) {
  global $var;
  if(mysql_query($sql)) {
    return mysql_insert_id();
  } else {
    return false;
  }
}

function db_update($sql) {
  global $var;
  return mysql_query($sql);
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

function db_delete($sql) {
  global $var;
  return mysql_query($sql);
}


function include_joomla_dbFunctions() {
  global $var;
  include_once('m_joomla_sql.php');
}


function _event_list_ajx() {
  global $var;
  $datamodel = new JEventsDataModel();
  require($var->tpl_path."event_list_ajax.tpl");
}

function _location_list_ajx() {
  global $var;
  $sql = parse_sql("jaigarfordo");
  // fprint($sql);
  $data = db_fetch($sql, true, true);
  exit(json_encode($data));
  // fprint($sql); fprint($data); _x();
  // require($var->tpl_path."location_list_ajax.tpl");
}

function _photo_albums_ajx() {
  global $var;
  $param = db_fetch(parse_sql("tomarchobiguli"), true, true);
  foreach($param as $k => $v) {
    $var->chobiguliniyeghoro = array("catid" => $v['id']);
    $v['photos'] = db_fetch(parse_sql("chobiguliniyeghoro"));
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
      $param[$k]['avatar'] = '/images/phocagallery/'.$userfolder.'thumbs/phoca_thumb_m_'.$filename;
  }
  /* fprint($param);
  foreach($param as $v) {
    echo $v['id'].' - '.$v['title'].'<br />';
  }
  _x(); */
  require($var->tpl_path."photo_albums_ajax.tpl");
}

function _list_videos_ajx() {
  global $var;
  $var->items_per_page = 6;
  $sql = "select * from `jos_phocagallery` where `catid` = ".id_from_phoca_cat('Videos')." and `published` = 1 order by id desc";
  //fprint($sql);
  $param = db_fetch($sql, true);
  //fprint($param); _x();
  require($var->tpl_path."videos_ajax.tpl");
}
