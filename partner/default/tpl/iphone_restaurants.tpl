<div id="content" style="min-height:0px;margin-top:0px;width:310px;">
    <ul class="pageitem" style="width:260px; margin:5px;">
		<li class="select">
			<?php
				$recsubsql="select * from jos_categories where (parent_id=152 OR id=152) AND section='com_jevlocations2' and published=1 ORDER BY title ASC";
				$recsub=mysql_query($recsubsql) or die(mysql_error());
			?>

			<select name="d" onChange="redirecturl(this.value)" style="width:100%; height:45px;border: 0pt none;font-weight:bold;font-size:17px;">
				<option value="0">Select a Category</option>
				<option value="0">All</option>
				<option value="alp" <?php if ($_REQUEST['filter_loccat']=='alp') {?> selected <?php }?>>Alphabetic</option>
				<?php	  				
					while($rowsub=mysql_fetch_array($recsub))
					{
						$querycount = "SELECT * FROM jos_jev_locations WHERE published=1 and loccat=".$rowsub['id'];
						if($filter_order != "")
							$querycount .= " ORDER BY title ASC ";
						else
							$querycount .= " ORDER BY ordering ASC";	

						$reccount=mysql_query($querycount) or die(mysql_error());
						if (mysql_num_rows($reccount))
						{
							if(($_REQUEST['filter_loccat']!='alp') || ($_REQUEST['filter_loccat']!='0'))
							{
				?>
			  					<option value="<?=$rowsub['id'];?>" <?php if ($_REQUEST['filter_loccat']==$rowsub['id']) {?> selected <?php }?>><?=$rowsub['title'];?></option>
			 	<?php
			 	  			}
				   		}
					}
				?>
			</select>
			<span class="arrow"></span>
		</li>
	</ul>

	<div onclick="divopen('q1')" style="padding-top:5px;width:25px; height:25px;float:right;cursor:pointer;margin-top:-45px;margin-right:0px;"><img src="../../images/find.png" height="25px" width="25px"/></div>
	
	<ul class="pageitem" style='border:0px;margin:5px;'>
		<li>
			<div id="q1" style="display:none;cursor:pointer;margin:5px">
				<form action="" method="post" name="location_form" style="margin:0px;">
					<input type="text" name="searchvalue" value="" size="25"/>
					<input type="submit" name="search_rcd" value="Search"/>
				</form>
			</div>
		</li>
	</ul>
</div>

<?php
	$featuredListingSQL = " SELECT jjl.loc_id, jjc.target_id, jjl.title, jjl.street, jjl.phone, jjl.loccat, jjc.name, jjc.value, jc.id, jc.parent_id
							FROM `jos_jev_locations` jjl, `jos_categories` jc, `jos_jev_customfields3` jjc
							WHERE jjl.published =1
							AND (jjl.loccat = jc.id AND (jc.parent_id =152 OR jc.id =152) AND jc.section = 'com_jevlocations2' AND jc.published =1 )
							AND (jjl.loc_id = jjc.target_id AND jjc.value = 1 )
							ORDER BY jjl.title ";

	$featuredListing_rec = mysql_query($featuredListingSQL) or die(mysql_error());
	if (mysql_num_rows($featuredListing_rec))
	{	
?>
		<ul class="pageitem" style="width:308px;">
		<li>
		<b>Featured</b>
		<table width="308" cellpadding="0" cellspacing="0" border="0" bgcolor="#F0F0F0">
		<!--<tr><td colspan="2" align="left"></td></tr>-->
<?php
		$rec_featured = mysql_query($query_featured) or die(mysql_error());
		while($row_featured=mysql_fetch_assoc($rec_featured))
		{
			$distance_featured = distance($lat1, $lon1, $row_featured['geolat'],  $row_featured['geolon'], $dunit);
?>
		<tr>
			<td style="width:260px;border-bottom:solid 1px #777777;padding:3px;">
			<table cellpadding="1" cellspacing="0" border=0>
			<tr><td style="height:3px"></td></tr>
			<tr><td class="headertext"><?php echo $row_featured['title']; ?></td></tr>
			<tr><td class="graytext"><?php
			$words = explode(' ',strip_tags($row_featured['description']));
			$desc = htmlspecialchars(implode(" ",array_slice($words,0,30)));
			if(!empty($desc)){ echo $desc .' ...' ;}?></td></tr>
			<tr><td class="graytext">
			<?php if ($_REQUEST['bIPhone']=='0'){?>
				<a class="linktext" href="tel:<?php echo str_replace(array(' ','(',')','-','.'), '', $row_featured[phone]); ?>">call</a> |     
			<?php } else { ?>
				<a class="linktext" href="tel:<?php echo str_replace(array(' ','(',')','-','.'), '', $row_featured[phone]); ?>">call</a> |<?php } ?>

				<a class="linktext" href="javascript:linkClicked('APP30A:FBCHECKIN:<?php echo $row_featured[geolat]; ?>:<?php echo $row_featured[geolon]; ?>')">check in</a> | 
				<a class="linktext" href="diningdetails.php?did=<?=$row_featured[loc_id]?>&<?=round($distance_featured,1)?>&lat=<?=$lat1?>&lon=<?=$lon1?>">more info</a> 
				<a class="linktext" href="javascript:linkClicked('APP30A:SHOWMAP:<?php echo $row_featured[geolon]; ?>:<?php echo $row_featured[geolat]; ?>')"></a> 
				</td>
			</tr>
			
			</table>
			</td>
			<td class="graytext" width="40px" style="border-bottom:solid 1px #777777;padding:3px;text-align:right;" valign="middle" align="center"><?php echo round($distance_featured,1); ?> miles</td>			
		</tr>			
<?php
		}
?>
		</table>
		</li>
		</ul>
<?php
	}
?>


<?php if($_POST['search_rcd']!="Search") { ?>
	<ul class="pageitem" style="width:308px;">
	<li>
	<table width="308" cellpadding="0" cellspacing="0" border="0">
	<tr><td colspan="2"></td></tr>
	<?php
	
	//if (($filter_loccat==0) || ($_REQUEST['filter_loccat']=='alp'))
	// $query = "SELECT *,(((acos(sin(($lat1 * pi() / 180)) * sin((geolat * pi() / 180)) + cos(($lat1 * pi() / 180)) * cos((geolat * pi() / 180)) * cos((($lon1 - geolon) * pi() / 180)))) * 180 / pi()) * 60 * 1.1515) as dist FROM jos_jev_locations $customfields3_table WHERE loccat IN (".implode(',',$allCatIds).") AND published=1 ".$subquery;
	//else

	 $query = "SELECT *,(((acos(sin(($lat1 * pi() / 180)) * sin((geolat * pi() / 180)) + cos(($lat1 * pi() / 180)) * cos((geolat * pi() / 180)) * cos((($lon1 - geolon) * pi() / 180)))) * 180 / pi()) * 60 * 1.1515) as dist FROM jos_jev_locations $customfields3_table WHERE loccat IN (".implode(',',$allCatIds).") AND published=1 ".$subquery;

	//and loccat=".$filter_loccat
	if($filter_loccat == 'Featured')
		$query .= " AND (jos_jev_locations.loc_id = jos_jev_customfields3.target_id AND jos_jev_customfields3.value = 1 ) ";
	elseif($filter_loccat!=0 && $_REQUEST['filter_loccat']!='alp')
		$query .= " AND loccat = $filter_loccat ";

	if(($filter_order != "") || ($_REQUEST['filter_loccat']=='alp'))
		$query .= " ORDER BY title ASC LIMIT " .$start_at.','.$entries_per_page;
	else 
		$query .= " ORDER BY dist ASC LIMIT " .$start_at.','.$entries_per_page;

		
	$rec=mysql_query($query) or die(mysql_error());
	while($row=mysql_fetch_assoc($rec))
	{
		$distance = distance($lat1, $lon1, $row[geolat],  $row[geolon], $dunit);
					
?>		
	<tr>
		<td style="width:260px;border-bottom:solid 1px #777777;padding:3px;">
		<table cellpadding="1" cellspacing="0" border=0>
		<tr><td style="height:3px"></td></tr>
		<tr><td class="headertext"><?php echo $row[title]; ?></td></tr>
		<tr><td class="graytext"><?php
		$words = explode(' ',strip_tags($row[description]));
		$desc = htmlspecialchars(implode(" ",array_slice($words,0,30)));
		if(!empty($desc)){
				echo (count($words)>30)?$desc .' ...' :$desc;	
		}?>
		</td></tr>
		<tr><td class="graytext">
		<?php if ($_REQUEST['bIPhone']=='0'){?>
			<a class="linktext" href="tel:<?php echo str_replace(array(' ','(',')','-','.'), '', $row[phone]); ?>">call</a> |     
		<?php } else { ?>
			<a class="linktext" href="tel:<?php echo str_replace(array(' ','(',')','-','.'), '', $row[phone]); ?>">call</a> |<?php } ?>

			<a class="linktext" href="javascript:linkClicked('APP30A:FBCHECKIN:<?php echo $row[geolat]; ?>:<?php echo $row[geolon]; ?>')">check in</a> | 

			<a class="linktext" href="diningdetails.php?did=<?=$row['loc_id']?>&lat=<?=$lat1?>&lon=<?=$lon1?>">more info</a> 
			<a class="linktext" href="javascript:linkClicked('APP30A:SHOWMAP:<?php echo $row[geolon]; ?>:<?php echo $row[geolat]; ?>')"></a> 
			</td>
		</tr>
		
		</table>
		</td>
		<td class="graytext" width="40px" style="border-bottom:solid 1px #777777;padding:3px;text-align:right;" valign="middle" align="center"><?php echo round($distance,1); ?>&nbsp;<?=$dunit?></td>			
	</tr>			
	<?php } ?>
</table>
<?php 
if($total_rows>50) {
echo get_paginate_links($total_rows,$entries_per_page,$current_page,$link_to);}?>	
		
</li>
</ul>
<?php } ?>
<?php
	if($_POST['search_rcd']=="Search") {
	$searchdata=$_POST['searchvalue'];
?>
<ul class="pageitem" style="width:308px;">
<li>
	<table width="308" cellpadding="0" cellspacing="0" border="0">
	<?php
	
if (($filter_loccat==0) || ($_REQUEST['filter_loccat']=='alp') && ($_POST['search_rcd']=="Search")) {
	$search_query1="select * from `jos_jev_locations` where loccat IN (".implode(',',$allCatIds).") AND published=1 and title like '%$searchdata%' or description like '%$searchdata%' ORDER BY title ASC LIMIT " .$start_at.','.$entries_per_page;
} else if($filter_loccat == 'Featured' && $_POST['search_rcd']=="Search" ) {
	$search_query1="select * from `jos_jev_locations` $customfields3_table where loccat IN (".implode(',',$allCatIds).") AND published=1 and title like '%$searchdata%' or description like '%$searchdata%'  AND (jos_jev_locations.loc_id = jos_jev_customfields3.target_id AND jos_jev_customfields3.value = 1 ) ORDER BY title ASC LIMIT " .$start_at.','.$entries_per_page;
} else if($_POST['search_rcd']=="Search"){ 
	$search_query1="select * from `jos_jev_locations` where loccat IN (".implode(',',$allCatIds).") AND published=1 and loccat=$filter_loccat and title like '%$searchdata%' or description like '%$searchdata%' ORDER BY title ASC LIMIT " .$start_at.','.$entries_per_page;
}
		
		$search_query=mysql_query($search_query1) or die(mysql_error());
	

		while($data = mysql_fetch_array($search_query)) {
		      $title=$data[title];
		      $lat2=$data[geolat];
		      $lon2=$data[geolon];

		if (JRequest::getFloat("needdistance",0)){
		$lat=JRequest::getFloat("lat",999);
		$lon=JRequest::getFloat("lon",999);
		$km=JRequest::getInt("km",0)?1.609344:1;
	
		//$dist = (((acos(sin(($lat*pi()/180)) * sin(($lat2 * pi()/180)) + cos(($lat * pi() / 180)) * cos(($lat2 * pi() / 180)) * cos((($lon - $lon2) * pi() / 180)))))*180/pi())*60*1.1515*$km;
		
		$dist = distance($lat, $lon, lat2, $lon2, $dunit);
		}
?>
<tr>
	<td style="width:260px;border-bottom:solid 1px #777777;padding:3px;">
	<table cellpadding="1" cellspacing="0" border=0>
	<tr><td style="height:3px"></td></tr>
	<tr><td class="headertext"><?php echo $title ?></td></tr>	
	<tr><td class="graytext"><?php 
	$words = explode(' ',strip_tags($data[description]));
	$desc = htmlspecialchars(implode(" ",array_slice($words,0,30)));
	if(!empty($desc)){ echo $desc .' ...' ;}?></td></tr>
	<tr><td class="graytext">
	<?php if ($_REQUEST['bIPhone']=='0'){?>
	<a class="linktext" href="tel:<?php echo str_replace(array(' ','(',')','-','.'), '', $data[phone]); ?>">call</a> |     
	<?php } else { ?>
	<a class="linktext" href="tel:<?php echo str_replace(array(' ','(',')','-','.'), '', $data[phone]); ?>">call</a> |
<?php 
	//echo $this->escape($data['phone'])." | ";
	} ?>

	<a class="linktext" href="javascript:linkClicked('APP30A:FBCHECKIN:<?php echo $data[geolat]; ?>:<?php echo $data[geolon]; ?>')">check in</a> | 					
	<a class="linktext" href="diningdetails.php?did=<?=$data['loc_id']?>&lat=<?=$lat1?>&lon=<?=$lon1?>">more info</a> 
	<a class="linktext" href="javascript:linkClicked('APP30A:SHOWMAP:<?php echo $data['geolon']; ?>:<?php echo $data['geolat']; ?>')"></a>  
	</td>
	</tr>
	
</table>
</td>
<td class="graytext" width="40px" style="border-bottom:solid 1px #777777;padding:3px;text-align:right;" valign="middle" align="center"><?php echo round($dist,1); ?>&nbsp;<?=$dunit?></td>			
</tr>				
	
<?php } ?>
</table>
<?php if($total_rows >'50') {
echo get_paginate_links($total_rows,$entries_per_page,$current_page,$link_to);}?>
</li>
</ul>
<?php } 

include("connection.php");
?>	
<div id="footer">&copy; <?=date('Y');?> <?=$site_name?> <!-- | <a href="mailto:<?=$email?>?subject=App Feedback">Contact Us</a>--></div>

<div style='display:none;'><?php echo $pageglobal['googgle_map_api_keys']; ?></div>