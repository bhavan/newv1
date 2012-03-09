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
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="yes" name="apple-mobile-web-app-capable" />
<meta content="index,follow" name="robots" />
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<link href="pics/homescreen.gif" rel="apple-touch-icon" />
<meta content="minimum-scale=1.0, width=device-width, maximum-scale=0.6667, user-scalable=no" name="viewport" />
<link href="css/style.css" rel="stylesheet" media="screen" type="text/css" />
<script src="javascript/functions.js" type="text/javascript"></script>
<title><?=$site_name?>
</title>
<!--<link href="pics/startup.png" rel="apple-touch-startup-image" /> -->
<meta content="destin, vacactions in destin florida, destin, florida, real estate, sandestin resort, beaches, destin fl, maps of florida, hotels, hotels in florida, destin fishing, destin hotels, best florida beaches, florida beach house rentals, destin vacation rentals for destin, destin real estate, best beaches in florida, condo rentals in destin, vacaction rentals, fort walton beach, destin fishing, fl hotels, destin restaurants, florida beach hotels, hotels in destin, beaches in florida, destin, destin fl" name="keywords" />
<meta content="Destin Florida's FREE iPhone application and website guide to local events, live music, restaurants and attractions" name="description" />
</head>

<body>
<!--Google Adsense -->



<div id="topbar">
<div id="title">Photos</div>
<div id="leftnav">
<a href="/android/photos.php"><img alt="home" src="images/camera.png" /></a>
  <?php 
	if ($current_page!=0)
				  			{
							 $st1=($current_page*$num_rec)-$num_rec;	
						?> <a href="photos_view.php?start=<?=$st1?><?=$paginationstr?>&backstart=<?=$_REQUEST[backstart]?>">Back</a> <?php }?>
        </div>
        
        
<div id="rightnav">
	<?php
					  if (($current_page+1)<$num_pages)
				 		 {
					  $st1=($current_page*$num_rec)+$num_rec;
					  ?>
                    	<a href="photos_view.php?start=<?=$st1?><?=$paginationstr?>&backstart=<?=$_REQUEST[backstart]?>">Next</a><?php }?>
    
</div></div>

<div id="content">
          <!--<div align="center"><a href="photos.php?start=<?=$_REQUEST[backstart]?>" >BACK</a></div> -->
	<ul class="pageitem">
		<li class="textbox" style="text-align:center;">
    <?php while($row=mysql_fetch_array($rec)) {
    $file=explode('/',$row['filename']);
			$j++;
			
			if (count($file)>=2)
			{
			?>
     <a href="photos_view.php?start=<?=$photoindent+$j?>"><img src="/images/phocagallery/<?=$file[0]?>/thumbs/phoca_thumb_l_<?=$file[1]?>"  border="0" /></a>
      <?php }
			else {
			?>
      <a href="photos_view.php?start=<?=$photoindent+$j?>"><img src="/images/phocagallery/thumbs/phoca_thumb_l_<?=$row['filename']?>"  border="0"/></a>
      <?php
			}
			echo '<div>'.$row[title].'</div>';
			?>
    
      <?php
			}
			?>
		</li>
	</ul>
        
</div> 

<div id="footer">

	&copy; <?=date('Y');?> <?=$site_name?>, Inc. | <a href="mailto:<?=$email?>?subject=Feedback">Contact Us</a></div></div>
</body>

</html>
