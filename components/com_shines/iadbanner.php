<?php

/***
  base functions for the app ads 
***/

function m_show_banner($cat) {  
	
  global $var;
  $partnerBannerImg = $_SESSION["partner_folder_name"];
  $sql = "select b.* from `jos_banner` b, `jos_categories` c where c.title = '".$cat."' and c.id = b.catid and b.showBanner = 1";
  $data = db_fetch12($sql, true, true);
  $d = array();
  if(count($data) > 1) {
    $d = $data[rand(0, (count($data) - 1))];
  } else {
    $d = $data[0];
  }

if ($d['imageurl'] == ""){
$cat = "iphone-news-screen";
m_show_banner($cat);}
else{
	

     
//fprint($d); _x();  
  if ($d['custombannercode'] != "")
    echo $d['custombannercode'];
  else{
	  $url=$d['clickurl'];

// strpos() is just to check weather, is there email id or telephone no in URL or not ?

    if (strpos($url,'mailto:') || strpos($url,'tel:'))
	 echo '<a href="/adsclick.php?option=com_banners&task=click&bid='.$d['bid'].'" target="_blank"><img src="/partner/'.$partnerBannerImg.'/images/banners/'.$d['imageurl'].'" alt="'.$d['name'].'" title="'.$d['name'].'" width="320px" height="50px"  /></a>';
    else
         echo '<a href="/adsclick.php?option=com_banners&task=click&bid='.$d['bid'].'" target="_blank"><img src="/partner/'.$partnerBannerImg.'/images/banners/'.$d['imageurl'].'" alt="'.$d['name'].'" title="'.$d['name'].'" width="320px" height="50px" /></a>';
}
// for Impressions: track the number of times the banner is displayed to web site visitors.
  $sql = 'UPDATE jos_banner SET impmade = impmade + 1 WHERE bid =' .$d['bid'];
  db_update($sql); 
}}

function db_fetch12($sql, $list = false, $all = false) {
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
function db_update($sql) {
  global $var;
  return mysql_query($sql);
}


/***************** [END]  module functions  [END] **********************/
