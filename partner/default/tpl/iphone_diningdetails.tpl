<div id="list" ontouchstart="touchStart(event,'list');" ontouchend="touchEnd(event);" ontouchmove="touchMove(event);" ontouchcancel="touchCancel(event);">
	
      <?php 
	  while($row=mysql_fetch_array($rec))
	  {
		  
			
			$lat2=$row['geolat'];
			$lon2=$row['geolon'];
			
			
	  ?>
      
      <div style="width:100%"><strong style="font-size:15px;"><?php echo utf8_encode($row['title']);?></strong>
      <br /><br />
      <div style="width:100%">
	  <?php
	  $ua = strtolower($_SERVER['HTTP_USER_AGENT']);
if(stripos($ua,'android') == true) { 
?>
  <div style="width:10%;float:left;padding-right:50px;">Address:</div><div style="width:100%"><a href="map.php?lat=<?=$lat2?>&long=<?=$lon2?>"><?=$row['street']?></a></div></div><br />
  
  <?php } 
  else {
  ?> <div style="width:10%;float:left;padding-right:50px;">Address:</div><div style="width:100%"><a href="javascript:linkClicked('APP30A:SHOWMAP:<?=$lat2?>:<?=$lon2?>')"><?=$row['street']?></a></div></div><br />
 
  <?php } ?>
	  
        
     <div style="width:100%">
       <div style="width:10%;float:left;padding-right:50px;">Phone:</div>
       <div style="width:90%"><a href="tel:<?php echo str_replace(array(' ','(',')','-','.'), '',$row[phone])?>"><?=$row[phone]?></a></div></div><br />
       <div style="width:100%"><div style="width:10%;float:left;padding-right:50px;">Distance:</div><div style="width:100%"> <?=round(distance($lat1, $lon1, $lat2, $lon2,$dunit),'1')?>&nbsp;<?=$dunit?></div></div><br />
	<?php if ($row['url']!=''){ ?>
       <div style="width:100%"><div style="width:10%;float:left;padding-right:50px;">Website:</div><div style="width:90%"><a href="http://<?php echo str_replace('http://','',$row['url']); ?>" target="_blank"><?php echo str_replace('http://','',$row['url']); ?></a></div></div><br />
	<?php } ?>
	<?php if ($row['description']!=''){ ?>
       <div style="width:100%"><div style="width:10%;float:left;padding-right:50px;">Description:</div><div style="width:100%"><?php echo stripJunk(utf8_encode($row['description'])); ?></div></div><br />
	<?php } ?>
      </div>
     
      <?php
	  }
	  ?>
<?php
$host = $_SERVER[HTTP_HOST];
?>
<div style="clear:both; overflow:hidden;">		
<div style='float:left;padding:3px 3px 3px 8px;'>
		<a expr:share_url='data:post.url' href='http://www.facebook.com/sharer.php?u=http://<?php echo $host; ?>/location_details.php?id=<?php echo $did; ?>' name='fb_share' type='box_count'><img src="/components/com_shines/images/facebook_share_icon.png"/></a>
		<!-- <script src='http://static.ak.fbcdn.net/connect.php/js/FB.Share' type='text/javascript'></script> -->		
</div>

<div style='float:left;padding:3px 3px 3px 8px;'>
<a href="https://plus.google.com/share?url=http://<?php echo $host; ?>/location_details.php?id=<?php echo $did; ?>" onclick="javascript:window.open(this.href,'','menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;">
	<img src="/components/com_shines/images/google-share-button.jpg" alt="Share on Google+"/>
</a>
</div>	
	</div>	
	
</div>

<!-- <div id="footer">&copy; <?=date('Y');?> <?=$site_name?>, Inc.  | <a href="mailto:<?=$email?>?subject=App Feedback">Contact Us</a>  </div> </div>-->
<div style='display:none;'><?php echo $pageglobal['googgle_map_api_keys']; ?></div>