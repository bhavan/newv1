<?php
include("connection.php");
global $var;
include_once('inc/var.php');

include_once($var->inc_path.'base.php');
_init();

?>
  
<?php include('header.php');?>
   	<ul data-role="listview" data-inset="true" data-theme="c" data-dividertheme="a" style="width:100%">
	    <div class="ui-btn-inner ui-corner-top ui-corner-bottom ui-controlgroup-last" style="background-color:#f1f1f1; text-decoration:none"><?php events_list($_REQUEST['rd'],$_REQUEST['ed'],$_REQUEST['end']) ?></div>
        </ul>
<?php include('footer.php'); ?>