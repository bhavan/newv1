<?php
include("connection.php");
global $var;
include_once('inc/var.php');

include_once($var->inc_path.'base.php');
_init();

?>
<?php include('header.php');
    $rec=mysql_query("select * from jos_categories where section='com_jevlocations2' and published=1 order by `ordering`") or die(mysql_error());
?>      
	<ul data-role="listview" data-inset="true" data-theme="c" data-dividertheme="a">
	    <li data-role="list-divider"><?php echo $_GET['id']; ?></li>
		<li style="height:auto;">
			<div style="margin-top: -6px; text-align: justify;"><span style="width:100px; vertical-align:t">
				<?php   $text = db_fetch("select `introtext` from `jos_content` where `title` = 'Dining Page Introduction'");
					echo $text; ?>.
			</div>
		</li>
            <?php
	     while($row=mysql_fetch_array($rec)){ ?>
	    <li><a href="location_list.php?lid=<?php echo $row['id'];?>&ttl=<?php echo $row['title'] ?>"><?php echo $row['title']; ?></a></li>
	    <?php } ?>
        </ul>
<?php include('footer.php'); ?>
