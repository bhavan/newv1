<?php

$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
if(stripos($ua,'android') == true) { ?>

<div id="menu">
	<div id="title">Photos</div>
	
	<div id="leftnav">
		<!--<a href="/android/photos.php"><img alt="home" src="images/camera.png" /></a>
		<a href="/components/com_shines/photos.php">Home</a>-->
		
		  <?php 
			if ($current_page!=0)
						  			{
									 $st1=($current_page*$num_rec)-$num_rec;	
								?> <a href="photos_view.php?start=<?=$st1?><?=$paginationstr?>&backstart=<?=$_REQUEST[backstart]?>&id=<?=$CatId ?>"></a> <?php }?>
   	</div>
        
        
	<div id="rightnav">
	<?php
					  if (($current_page+1)<$num_pages)
				 		 {
					  $st1=($current_page*$num_rec)+$num_rec;
					  ?>
                    	<a href="photos_view.php?start=<?=$st1?><?=$paginationstr?>&backstart=<?=$_REQUEST[backstart]?>&id=<?=$CatId ?>"></a><?php }?>


	</div>

</div>
 <?php } 
  else {
  ?>
  
  <?php } ?>
  
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
     <a href="photos_view.php?start=<?=$photoindent+$j?>"><img src="/partner/<?=$_SESSION['partner_folder_name']?>/images/phocagallery/<?=$file[0]?>/thumbs/phoca_thumb_l_<?=$file[1]?>"  border="0" /></a>
      <?php }
			else {
			?>
      <a href="photos_view.php?start=<?=$photoindent+$j?>"><img src="/partner/<?=$_SESSION['partner_folder_name']?>/images/phocagallery/thumbs/phoca_thumb_l_<?=$row['filename']?>"  border="0"/></a>
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
<!-- <div id="topbar"><div id="rightnav"><a href="upload.php">Upload</a></div></div> --> 
</div>
<div style='display:none;'><?php echo $pageglobal['googgle_map_api_keys']; ?></div>