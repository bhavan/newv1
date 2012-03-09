<?php
/*include("connection.php");
global $var;
include_once('inc/var.php');

include_once($var->inc_path.'base.php');
_init();
*/
include("connection.php");

$query="select * from jos_phocagallery where catid=2 order by id desc";
$rec=mysql_query($query) or die(mysql_error());
?>
<?php include('header.php');
?>

	<ul data-role="listview" data-inset="true" data-theme="c" data-dividertheme="a">
            <?php
		while($row=mysql_fetch_array($rec)){
	    
		  $arr=explode('/v/',$row['videocode']);
		  $arr1=explode('?',$arr[1]);
		  $arr2=explode('&',$arr1[0]);
		  $arr2[0]='http://www.youtube.com/watch?v='.$arr2[0];
	    ?>
		<li style="padding-left:8px;">
		    
		<a href="<?=$arr2[0]?>">
		
		
		
		
		<img src="/images/phocagallery/<?=$row['filename']?>" border="0" align="left" style="padding-right:10px;" /></a><font color="#999999"><strong><a href="<?=$arr2[0]?>"><img src="images/next-videos.gif" align="right" style="padding-top:20px;"  border="0"/></a>
		<a href="<?=$arr2[0]?>"><?=$row['title']?></a>
		</strong></font> </li>
	<?php } ?>
      
      
  
	    <?php //m_event_list_intro(); ?>
        </ul>
<?php include('footer.php'); ?>