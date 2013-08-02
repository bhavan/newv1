 <div id="main" role="main">
	<ul id="placesList" class="mainList" ontouchstart="touchStart(event,'list');" ontouchend="touchEnd(event);" ontouchmove="touchMove(event);" ontouchcancel="touchCancel(event);">
<li>
	<?php 
	while($row=mysql_fetch_array($rec))
	
	{
	$lat2=$row['geolat'];
	$lon2=$row['geolon'];
	?>
	
		<h1><?php echo $row['title'];?></h1>
		<p>
			<strong>Direcci&#243;n:</strong>&nbsp;&nbsp<a href="javascript:linkClicked('APP30A:SHOWMAP:<?=$lat2?>:<?=$lon2?>')"><?=$row['street']?></a>
		</p>
	
		
		<p>
		<strong>Tel&#233;fono:</strong>&nbsp;&nbsp;<a href="tel:<?php echo str_replace(array(' ','(',')','-','.'), '',$row[phone])?>"><?=$row[phone]?></a>
		</p>
		
		<p><strong>Distancia:</strong>&nbsp;&nbsp;<?=round(distance($lat1, $lon1, $lat2, $lon2,$dunit),'1')?><?=$dunit?></p>
		
		<?php if ($row['url']!=''){ ?>
		<p>
			<strong>Sitio Web:</strong>&nbsp;&nbsp;
			<a href="http://<?php echo str_replace('http://','',$row['url']); ?>" target="_blank">
				<?php echo str_replace('http://','',$row['url']); ?>
			</a>
		</p>
		<?php } ?>
		
		<?php if ($row['description']!=''){ ?>
		<p>
			<strong>Descripci&#243;n:</strong>
			 <?php echo stripJunk(utf8_encode($row['description'])); ?>
		</p>

		<?php } ?>
		<ul class="btnList">
			<li><a href="tel:<?php echo str_replace(array(' ','(',')','-','.'), '',$row[phone])?>" class="button small">llamar</a></li>
			
			<?php
			$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
			if(stripos($ua,'iphone') == true) { 
			?>
			<li><a href="javascript:linkClicked('APP30A:FBCHECKIN:<?php echo $row[geolat]; ?>:<?php echo $row[geolon]; ?>')" class="button small">facturar</a></li>
			<?php } ?>
		</ul>
		<?php  } ?>
	</li> <!-- end place -->
</ul> <!-- end place list -->
</div> <!-- main -->
<div style='display:none;'>
<?php echo $pageglobal['googgle_map_api_keys']; ?>
</div>
