<?php

require('jevents.php');
global $var;
include_once('./inc/var.php');
include_once($var->inc_path.'base.php');
_init();

?>

<?php

  if(isset($var->get['tab']) && $var->get['tab'] == 'places') {
    j_locations();
  } elseif(isset($var->get['tab']) && $var->get['tab'] == 'photos') {
    j_photo_albums();
  } elseif(isset($var->get['tab']) && $var->get['tab'] == 'videos') {
    j_videos();
  } else {
    j_events();
  }
  exit();

?>

