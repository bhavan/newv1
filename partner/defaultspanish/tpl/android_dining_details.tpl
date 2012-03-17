<div id="topbar">
<div id="title">Lugares Información</div>
<!--<div id="leftnav"><a href="dining.php?lat=<?=$lat1?>&lon=<?=$lon1?>" >Back</a></div>-->
<div id="leftnav"><a href="javascript:history.go(-1)" onMouseOver="self.status=document.referrer;return true">Espalda</a></div>
        
        
<div id="rightnav"></div>
</div>

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
        <div style="width:10%;float:left;padding-right:50px;">Dirección:</div><div style="width:100%"><a href="map.php?lat=<?=$lat2?>&long=<?=$lon2?>"><?=$row['street']?></a></div></div><br />
     <div style="width:100%">
       <div style="width:10%;float:left;padding-right:50px;">Teléfono:</div>
       <div style="width:90%"><a href="tel:<?=$row[phone]?>"><?=$row[phone]?></a></div></div><br />
       <div style="width:100%"><div style="width:10%;float:left;padding-right:50px;">Distancia:</div><div style="width:100%"> <?=round(distance($lat1, $lon1, $lat2, $lon2, "m"),'1').' miles'?></div></div><br />
	<?php if ($row['url']!=''){ ?>
       <div style="width:100%"><div style="width:21%;float:left;padding-right:18px;">Sitio Web:</div><div style="width:90%"><a href="http://<?php echo str_replace('http://','',$row['url']); ?>" target="_blank"><?php echo str_replace('http://','',$row['url']); ?></a></div></div><br />
	<?php } ?>
       <div style="width:100%"><div style="width:10%;float:left;padding-right:50px;">Descripción:</div><div style="width:100%"><?=$row['description']?></div></div><br />
      </div>
      </li>
      <?php
	  }
	  ?>
		
	</ul>
</div>

<div id="footer">

	&copy; <?=date('Y');?> <?=$site_name?> | <a href="mailto:<?=$email?>?subject=Feedback">Contacte con nosotros</a></div></div>
<div style='display:none;'><?php echo $pageglobal['googgle_map_api_keys']; ?></div>