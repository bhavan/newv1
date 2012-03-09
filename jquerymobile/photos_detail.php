<?php
include("connection.php");
include("class.paggination.php");

$select_query = "select * from jos_phocagallery where catid<>2 and published=1 and approved=1 order by id desc";
 $rec_no=mysql_query( $select_query);
 
 $mydb=new pagination(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
	$mydb->connection();
 $num_records=mysql_num_rows($rec_no);
 $num_rec=1;
		 $mydb->set_qry($select_query);
		 $mydb->set_record_per_sheet($num_rec);
		 $num_pages=$mydb->num_pages();
		 if (isset($_REQUEST['start']))
	 	 $recno=$_REQUEST['start'];
		 else
	 	 $recno=0;
		 
		 $rec=$mydb->execute_query($recno);
		 $current_page=$mydb->current_page();
		 $start_page=$mydb->start_page();
		 $end_page=$mydb->end_page();
		 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<head>
<link href="css/style.css" rel="stylesheet" media="screen" type="text/css" />
<title><?=$site_name?>
</title>
<!--<link href="pics/startup.png" rel="apple-touch-startup-image" /> -->
<meta content="" name="keywords" />
<meta content="" name="description" />
</head>

<body>
   
<?php include('header.php');?>

<ul data-role="listview" data-inset="true" data-theme="c" data-dividertheme="a" style="width:30%">
<!--<li style="height:18px" data-role="list-divider"><?php //echo $_GET['id']; ?></li>-->
<div class="ui-btn-inner ui-corner-top ui-corner-bottom ui-controlgroup-last" style="height:auto;background-color:#f1f1f1">
<!--<div style="margin-left:75px; margin-top: -6px; text-align: center;">-->
	<?php while($row=mysql_fetch_array($rec)) {
		$file=explode('/',$row['filename']);
		$j++;
	?>
	<span style="width:100px; vertical-align:top; margin-left:-10px">		
		<?php if (count($file)>=2) { ?>
			<a href="photos_detail.php?start=<?php echo $photoindent+$j?>"><img src="/images/phocagallery/<?php echo $file[0]?>/thumbs/phoca_thumb_l_<?php echo $file[1]?>"  border="0" width="270"/></a>
		<?php } else { ?>
			<a href="photos_detail.php?start=<?php echo $photoindent+$j?>"><img src="/images/phocagallery/thumbs/phoca_thumb_l_<?php echo $row['filename']?>"  border="0" width="270"/></a>
		<?php } ?>
	</span>
	<div><?php //echo '<br/>'.$row[title];?></div>
	<?php } ?>
</div>

<!--Pagination Starts-->
	<div>
	<span style="margin-left:10px">
	<?php 
		if ($current_page!=0)	{
			$st1=($current_page*$num_rec)-$num_rec;	?>
			<a href="photos_detail.php?start=<?php echo $st1?><?php echo $paginationstr?>&backstart=<?php echo $_REQUEST[backstart]?>">Back</a>
		<?php }?>
	</span>
		
		
	<span style="margin-left:10px">
	<?php
		if (($current_page+1)<$num_pages){
							
		$st1=($current_page*$num_rec)+$num_rec;
		?>
		<a href="photos_detail.php?start=<?php echo $st1?><?php echo $paginationstr?>&backstart=<?php echo $_REQUEST[backstart]?>">Next</a><?php }?>
	
	</span>
	</div>
.<!--Pagination Ends-->
</ul>
<?php include('footer.php');?>
</body>

</html>