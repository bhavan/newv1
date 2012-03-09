<?php
include("connection.php");
global $var;
include_once('inc/var.php');

include_once($var->inc_path.'base.php');
_init();

$data = db_fetch("select * from `jos_jev_locations` where `loc_id` = ".$var->get['id']);
$data['q'] = str_replace(' ', '+', ($data['title'].' '.$data['street'].' '.$data['city'].' '.$data['state'].' '.$data['postcode']));
?>
  
<?php include('header.php');?>
   	<ul data-role="listview" data-inset="true" data-theme="c" data-dividertheme="a">

	    <li><iframe class="map_container" width="300" height="220" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="../showmap.php?lat=<?php echo $data['geolat']?>&long=<?php echo $data['geolon']?>&zoom=<?php echo $data['geozoom']?>" style="margin-left:20px;"></iframe></li>

        </ul>
<?php include('footer.php'); ?>