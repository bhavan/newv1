<?php

/***
  base functions for the app ads 
***/

function m_show_banner($cat) {  
	$ad = array();
	global $var;
	$partnerBannerImg = $_SESSION["partner_folder_name"];
	$sql = "select b.* from `jos_banner` b, `jos_categories` c where c.title = '".$cat."' and c.id = b.catid and b.showBanner = 1";
	$data = db_fetch12($sql, true, true);
	$d = array();

	//Coundition for the Count the Data array to check the Banner add
	$showad = true;
	if($data != null){
		if(count($data) > 1) {
			$d = $data[rand(0, (count($data) - 1))];
		} elseif(count($data) == 1){
			$d = $data[0];
		} else {
			$showad = false;
		}
	} else {
		$showad = false;	
	}
	
	if($showad == true) {
		
		//fprint($d); _x();  
		if (trim($d['custombannercode']) != ""){
			$ad['type'] 		= 'google';
			$ad['publisher_id'] = getGooglePubId($d['custombannercode']);
		}else{
			 // $url=$d['clickurl'];
			$ad['type']			= 'internal';
			$ad['url']	= 'http://'.$_SERVER["HTTP_HOST"].'/adsclick.php?option=com_banners&task=click&bid='.$d['bid'];
			$ad['banner']		= 'http://'.$_SERVER["HTTP_HOST"].'/partner/'.$partnerBannerImg.'/images/banners/'.$d["imageurl"];
		}
		
		// for Impressions: track the number of times the banner is displayed to web site visitors.
		$sql = 'UPDATE jos_banner SET impmade = impmade + 1 WHERE bid =' .$d['bid'];
		db_update($sql); 
	} 
	return $ad;
}


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

function getGooglePubId($customBannerCode){
	$bannerArray = explode('pub-',$customBannerCode);
	$pubId = '';
	for($i=0;$i<count($bannerArray);$i++){
		if(strstr($bannerArray[$i],'";',true) != ''){
			if($pubId == ''){
				$pubId = strstr($bannerArray[$i],'";',true);
			}	
		}						
	}		
	return 'pub-'.$pubId;
}


/***************** [END]  module functions  [END] **********************/
