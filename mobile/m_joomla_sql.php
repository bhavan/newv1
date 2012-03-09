<?php


function parse_sql($key) {
  global $var;
  if(isset($var->$key))
    $param = $var->$key;

  $sqls = array(
    "tomarchobiguli" => "select * from `jos_phocagallery_categories` where id != 2 and `published` = 1 and `approved` = 1 order by ordering asc",
    "chobiguliniyeghoro" => "select id, filename from `jos_phocagallery` where `published` = 1 and `approved` = 1 and `catid` = ".$param['catid'],
    "jaigarfordo" => "select jjl.loc_id, jjl.title, jjl.street, jjl.phone, jjl.loccat from `jos_jev_locations` jjl, jos_jev_customfields3 jjc where jjl.loc_id = jjc.target_id and jjl.published=1 order by jjl.title ",
    "jaigarfordoCat" => "select jjl.loc_id, jjl.title, jjl.street, jjl.phone, jjl.loccat from `jos_jev_locations` jjl, `jos_categories` jc where jjl.loccat = jc.id and jc.title in(".$param['cat'].") and jjl.published=1 order by jjl.title"
  );


  return $sqls[$key];
}



?>