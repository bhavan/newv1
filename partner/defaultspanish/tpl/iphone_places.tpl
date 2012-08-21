<form id="placeForm">

	<?php 
		$recsub=mysql_query("select * from jos_categories where (parent_id=151 OR id=151) AND section='com_jevlocations2' and published=1 order by `ordering`") or die(mysql_error());
	?>

	<select name="d" onChange="redirecturl(this.value)" >
	<option value="0">Seleccione una categor&#237;a</option>
	<option value="0">Todo</option>
	 	<?php	  
		while($rowsub=mysql_fetch_array($recsub))
		{
		$querycount = "SELECT * FROM jos_jev_locations WHERE published=1 and loccat=".$rowsub['id'];
			$reccount=mysql_query($querycount) or die(mysql_error());	
			if (mysql_num_rows($reccount))
			{
		?>
	<option value="<?=$rowsub['id'];?>" <?php if ($_REQUEST['filter_loccat']==$rowsub['id']) {?> selected <?php }?>><?=$rowsub['title'];?></option>
		<?php
		}
		}
		?>
			</select>
	
</form>
	
<div id="search"><div onclick="divopen('q1')"><img width="37px" height="31px" src="./images/searchIcon.png"></div></div>
<div id="q1" style="display:none;"><form action="" method="post" name="location_form"><input type="text" name="searchvalue" value="" size="15"/><input type="submit" name="search_rcd" value="Buscar"/></form></div>
	
<div id="list">
	<table>
		<thead>
			<tr>
				<th class="two"></th>
				<th class="three">Distance</th>
			</tr>
		</thead>
		<tbody>

    <?php 
	while($row=mysql_fetch_array($rec))
	{
	$lat2=$row[geolat];
	$lon2=$row[geolon];
	?>
		<tr>
		 	<td class="two">
				<strong><?php echo $row['title'];?></strong><br />
				<span ><?php echo stripJunk(showBrief(strip_tags($row['description']),30)); ?></span><br /> 
		       	<ul>
        			<li><a class="grayplain" href="tel:<?php echo str_replace(array(' ','(',')','-','.'), '',$row['phone'])?>">llamar</a> |</li>
					<li><a class="grayplain" href="javascript:linkClicked('APP30A:FBCHECKIN:<?php echo $row[geolat]; ?>:<?php echo $row[geolon]; ?>')">facturar</a> |</li>
					<li><a class="grayplain" href="diningdetails.php?did=<?=$row['loc_id']?>&lat=<?=$lat1?>&lon=<?=$lon1?>">m&#225;s info</a></li>
					<li><a class="grayplain" href="javascript:linkClicked('APP30A:SHOWMAP:<?php echo $row[geolon]; ?>:<?php echo $row[geolat]; ?>')"></a></li> 
		        </ul>
		    </td>
		    <td class="three"><?=round(distance($lat1, $lon1, $lat2, $lon2,$dunit),'1')?>&nbsp;<?=$dunit?></td>  
		</tr>
    <?php
	}
	?>
		</tbody>
	</table>
</div>
<!-- <div id="footer">&copy; <?=date('Y');?> <?=$site_name?> | <a href="mailto:<?=$email?>?subject=App Feedback">Contact Us</a> </div> </div>--> 
<div style='display:none;'><?php echo $pageglobal['googgle_map_api_keys']; ?></div>