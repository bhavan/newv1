<?php
include("connection.php");
global $var;
include_once('inc/var.php');

include_once($var->inc_path.'base.php');
_init();

?>
  
<?php include('header.php');?>
   	<ul data-role="listview" data-inset="true" data-theme="c" data-dividertheme="a">

	    <div class="ui-btn-inner ui-corner-top ui-corner-bottom ui-controlgroup-last" style="background-color:#f1f1f1; text-decoration:none"><?php m_location_list($_REQUEST['ttl'],false) ?></div>

        </ul>
<?php include('footer.php'); ?>