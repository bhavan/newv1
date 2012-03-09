<?php


function call_joomla_mobile_function($param) {

  include_joomla_dbFunctions();

  $function_list = array(
    "location" => "location_list",
    "photo_albums" => "photo_albums",
    "videos" => "list_videos",
    "events" => "event_list",
  );

  process_f_name($function_list);

}

?>