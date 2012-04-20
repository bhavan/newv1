<div id="topbar">
<div id="title">Fotos</div>
<div id="leftnav">
<!--<a href="/android/photos.php"><img alt="home" src="images/camera.png" /></a>-->
<a href="/components/com_shines/galleries.php">Casa</a>

    <?php 
         if ($current_page!=0)
				  			{
							 $st1=($current_page*$num_rec)-$num_rec;	
						?>
    <a href="photos.php?start=<?=$st1?><?=$paginationstr?>">Espalda</a>
    <?php }else{ ?>
<a href="photos.php" style="margin-left:6px;">Espalda</a>
   <?php } ?>

        </div>
        
        
<div id="rightnav">

    <?php
					  if (($current_page+1)<$num_pages)
				 		 {
					  $st1=($current_page*$num_rec)+$num_rec;
					  ?>
    <a href="photos.php?start=<?=$st1?>">Próximo</a>
    <?php }?>
    
</div></div>

<div id="content" style="text-align:center;">
  <ul class="pageitem">
    <li class="textbox">
      <?php while($row=mysql_fetch_array($rec)) {
			$file=explode('/',$row['filename']);
			$j++;
			if (count($file)>=2)
			{
			?>
      <a href="photos_view.php?start=<?=$photoindent+$j?>&backstart=<?=(int)$_REQUEST[start]?>&id=<?=$CatId ?>"><img src="/images/phocagallery/<?=$file[0]?>/thumbs/phoca_thumb_s_<?=$file[1]?>" width="55" border="0" /></a>
      <?php }
			else {
			?>
      <a href="photos_view.php?start=<?=$photoindent+$j?>&backstart=<?=(int)$_REQUEST[start]?>&id=<?=$CatId ?>"><img src="/images/phocagallery/thumbs/phoca_thumb_s_<?=$row['filename']?>" width="55" border="0"/></a>
      <?php
			}}
			?>
    </li>
  </ul>
</div>
<!-- <div id="topbar"><div id="rightnav"><a href="upload.php">Upload</a></div></div> --> 
</div>
<div style='display:none;'><?php echo $pageglobal['googgle_map_api_keys']; ?></div>