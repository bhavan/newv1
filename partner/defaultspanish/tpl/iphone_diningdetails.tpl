<div id="content">
	<ul class="pageitem">
      <?php 
	  while($row=mysql_fetch_array($rec))
	  {
		  
			
			$lat2=$row['geolat'];
			$lon2=$row['geolon'];
			
			
	  ?>
      <li class="textbox">
      <div style="width:100%"><strong><?=$row['title']?></strong>
      <br /><br />
      <div style="width:100%">
        <div style="width:10%;float:left;padding-right:50px;">Direcci&#243;n:</div><div style="width:100%"><a href="javascript:linkClicked('APP30A:SHOWMAP:<?=$lat2?>:<?=$lon2?>')"><?=$row['street']?></a></div></div><br />
     <div style="width:100%">
       <div style="width:10%;float:left;padding-right:50px;">Tel&#233;fono:</div>
       <div style="width:90%"><a href="tel:<?=$row[phone]?>"><?=$row[phone]?></a></div></div><br />
       <div style="width:100%"><div style="width:10%;float:left;padding-right:50px;">Distancia:</div><div style="width:100%"> <?=round(distance($lat1, $lon1, $lat2, $lon2, "m"),'2').' miles'?></div></div><br />
	<?php if ($row['url']!=''){ ?>
       <div style="width:100%"><div style="width:21%;float:left;padding-right:18px;">Sitio Web:</div><div style="width:90%"><a href="http://<?php echo str_replace('http://','',$row['url']); ?>" target="_blank"><?php echo str_replace('http://','',$row['url']); ?></a></div></div><br />
	<?php } ?>
	<?php if ($row['description']!=''){ ?>
       <div style="width:100%"><div style="width:10%;float:left;padding-right:50px;">Descripci&#243;n:</div><div style="width:100%"><?php echo stripJunk($row['description']); ?></div></div><br />
	<?php } ?>
      </div>
      </li>
      <?php
	  }
	  ?>
		
	</ul>
</div>

<!-- <div id="footer">&copy; <?=date('Y');?> <?=$site_name?>, Inc. | <a href="mailto:<?=$email?>?subject=Feedback">Contact Us</a>  &nbsp;&nbsp;&nbsp; <a href="<?=$pageglobal['facebook']?>"><img src="images/icon_facebook_16x16.gif" alt="facebook_icon" width="16" height="16" /></a> &nbsp;&nbsp;&nbsp; </div> --> </div>
<div style='display:none;'><?php echo $pageglobal['googgle_map_api_keys']; ?></div>