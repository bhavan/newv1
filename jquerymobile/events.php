<?php
include("connection.php");
define( '_JEXEC', 1 );
//include('/home/tapdesti/public_html/index.php');


global $var;
include_once('inc/var.php');

include_once($var->inc_path.'base.php');
_init();

?>
<?php include('header.php');
	   /* $intro = mysql_query("select introtext from `jos_content` where `title` = 'Events intro'");
	    $result=mysql_fetch_array($intro);
	    echo $result;*/
?>
	<ul data-role="listview" data-inset="true" data-theme="c" data-dividertheme="a">
            <?php
	    m_event_list_intro(); ?>
        </ul>
<?php include('footer.php'); ?>