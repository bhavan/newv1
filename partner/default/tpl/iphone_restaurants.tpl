<form id="placeForm">
	<?php
	$recsubsql="select * from jos_categories where (parent_id=152 OR id=152) AND section='com_jevlocations2' and published=1 ORDER BY title ASC";
	$recsub=mysql_query($recsubsql) or die(mysql_error());
	?>

	<select name="d" onChange="redirecturl(this.value)" >
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

</form>

<div id="search"><div onclick="divopen('q1')"><img width="37px" height="31px" src="./images/searchIcon.png"></div></div>
<div id="q1" style="display:none;"><form action="" method="post" name="location_form"><input type="text" name="searchvalue" value="" size="25"/><input type="submit" name="search_rcd" value="Search"/></form></div>
	
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

			<strong>Featured</strong>
				
		<?php
		$rec_featured = mysql_query($query_featured) or die(mysql_error());
		while($row_featured=mysql_fetch_assoc($rec_featured))
		{
			$distance_featured = distance($lat1, $lon1, $row_featured['geolat'],  $row_featured['geolon'], $dunit);
		?>
			<tr>
				<td class="two" style="background:#EFEFEF;">			
				<strong><?php echo utf8_encode($row_featured['title']); ?></strong><br />
				<span class="grayplain">
		<?php
				$words = explode(' ',$row_featured['description']);
				$desc = implode(" ",array_slice($words,0,30));
				if(!empty($desc)){
					echo (count($words)>30)?stripJunk($desc) .' ...' :stripJunk($desc);		
		 		}
		?>
			 	</span><br /> 
			 	<ul>
		<?php if ($_REQUEST['bIPhone']=='0'){?>
					<li><a class="call" href="tel:<?php echo str_replace(array(' ','(',')','-','.'), '', $row_featured[phone]); ?>">call</a></li>    
					<?php } else { ?>
					<li><a class="call" href="tel:<?php echo str_replace(array(' ','(',')','-','.'), '', $row_featured[phone]); ?>">call</a></li><?php } ?>
					<li><a class="checkin" href="javascript:linkClicked('APP30A:FBCHECKIN:<?php echo $row_featured[geolat]; ?>:<?php echo $row_featured[geolon]; ?>')">check in</a></li> 
					<li><a class="info" href="diningdetails.php?did=<?=$row_featured[loc_id]?>&<?=round($distance_featured,1)?>&lat=<?=$lat1?>&lon=<?=$lon1?>">more info</a></li> 
					<li><a href="javascript:linkClicked('APP30A:SHOWMAP:<?php echo $row_featured[geolon]; ?>:<?php echo $row_featured[geolat]; ?>')"></a></li> 
				</ul>
				</td>
				<td class="three" style="background:#EFEFEF;"><?php echo round($distance_featured,1); ?>&nbsp;<?=$dunit?></td>			
			</tr>
		<?php
			}
		?>
	<?php
		}
	?>

<?php if($_POST['search_rcd']!="Search") { ?>

	<?php

	$query = "SELECT *,(((acos(sin(($lat1 * pi() / 180)) * sin((geolat * pi() / 180)) + cos(($lat1 * pi() / 180)) * cos((geolat * pi() / 180)) * cos((($lon1 - geolon) * pi() / 180)))) * 180 / pi()) * 60 * 1.1515) as dist FROM jos_jev_locations $customfields3_table WHERE loccat IN (".implode(',',$allCatIds).") AND published=1 ".$subquery;

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
				<td class="two">
				<strong><?=utf8_encode($row['title'])?></strong><br />
				<span class="grayplain"><?php echo stripJunk(showBrief(strip_tags($row['description']),30)); ?></span><br /> 
				<ul>
					<?php if ($_REQUEST['bIPhone']=='0'){?>
					<li><a class="call" href="tel:<?php echo str_replace(array(' ','(',')','-','.'), '', $row[phone]); ?>">call</a></li>   
					<?php } else { ?>
					<li><a class="call" href="tel:<?php echo str_replace(array(' ','(',')','-','.'), '', $row[phone]); ?>">call</a></li><?php } ?>
					<li><a class="checkin" href="javascript:linkClicked('APP30A:FBCHECKIN:<?php echo $row[geolat]; ?>:<?php echo $row[geolon]; ?>')">check in</a></li> 
					<li><a class="info" href="diningdetails.php?did=<?=$row['loc_id']?>&lat=<?=$lat1?>&lon=<?=$lon1?>">more info</a></li> 
					<li><a href="javascript:linkClicked('APP30A:SHOWMAP:<?php echo $row[geolon]; ?>:<?php echo $row[geolat]; ?>')"></a></li>
				</ul>
				</td>
				<td class="three"><?php echo round($distance,1); ?>&nbsp;<?=$dunit?></td>
			</tr>
<?php
}
?>

<?php 
if($total_rows>50) { echo get_paginate_links($total_rows,$entries_per_page,$current_page,$link_to);}?>	
<?php } ?>
<?php
	if($_POST['search_rcd']=="Search") {$searchdata=$_POST['searchvalue'];
?>

	<?php
		if(($filter_loccat==0) || ($_REQUEST['filter_loccat']=='alp') && ($_POST['search_rcd']=="Search")) {$search_query1="select * from `jos_jev_locations` where loccat IN (".implode(',',$allCatIds).") AND published=1 and title like '%$searchdata%' or description like '%$searchdata%' ORDER BY title ASC LIMIT " .$start_at.','.$entries_per_page;}
		else if($filter_loccat == 'Featured' && $_POST['search_rcd']=="Search" ) {$search_query1="select * from `jos_jev_locations` $customfields3_table where loccat IN (".implode(',',$allCatIds).") AND published=1 and title like '%$searchdata%' or description like '%$searchdata%'  AND (jos_jev_locations.loc_id = jos_jev_customfields3.target_id AND jos_jev_customfields3.value = 1 ) ORDER BY title ASC LIMIT " .$start_at.','.$entries_per_page;}
		else if($_POST['search_rcd']=="Search"){ $search_query1="select * from `jos_jev_locations` where loccat IN (".implode(',',$allCatIds).") AND published=1 and loccat=$filter_loccat and title like '%$searchdata%' or description like '%$searchdata%' ORDER BY title ASC LIMIT " .$start_at.','.$entries_per_page;}
		$search_query=mysql_query($search_query1) or die(mysql_error());
	
		while($data = mysql_fetch_array($search_query)) {
			$title=$data[title];
			$lat2=$data[geolat];
			$lon2=$data[geolon];

		if(JRequest::getFloat("needdistance",0)){
			$lat=JRequest::getFloat("lat",999);
			$lon=JRequest::getFloat("lon",999);
			$km=JRequest::getInt("km",0)?1.609344:1;
		
		$dist = distance($lat, $lon, lat2, $lon2, $dunit);
		}
	?>
		<tr>
			<td class="two">
			<strong><?php echo utf8_encode($data[title]); ?></strong><br/>	
			<span class="grayplain"><?php 
			$words = explode(' ',$data[description]);
			$desc = implode(" ",array_slice($words,0,30));
			if(!empty($desc)){
				echo (count($words)>30)?stripJunk($desc) .' ...' :stripJunk($desc);		
			}
			?>
			</span><br/>
	 		<ul>
				<?php if ($_REQUEST['bIPhone']=='0'){?>
				<li><a class="call" href="tel:<?php echo str_replace(array(' ','(',')','-','.'), '', $data[phone]); ?>">call</a></li>    
				<?php } else { ?>
				<li><a class="call" href="tel:<?php echo str_replace(array(' ','(',')','-','.'), '', $data[phone]); ?>">call</a></li>
			<?php } ?>
				<li><a class="checkin" href="javascript:linkClicked('APP30A:FBCHECKIN:<?php echo $data[geolat]; ?>:<?php echo $data[geolon]; ?>')">check in</a></li>				
				<li><a class="info" href="diningdetails.php?did=<?=$data['loc_id']?>&lat=<?=$lat1?>&lon=<?=$lon1?>">more info</a> </li>
				<li><a  href="javascript:linkClicked('APP30A:SHOWMAP:<?php echo $data['geolon']; ?>:<?php echo $data['geolat']; ?>')"></a></li> 
			</ul> 
			</td>
			<td class="three"><?php echo round($dist,1); ?>&nbsp;<?=$dunit?></td>			
		</tr>
	
<?php } ?>

<?php if($total_rows >'50') {
echo get_paginate_links($total_rows,$entries_per_page,$current_page,$link_to);}?>

<?php } 

include("connection.php");
?>	
		 </tbody>
	</table>
</div>
<div id="footer">&copy; <?=date('Y');?> <?=$site_name?> <!-- | <a href="mailto:<?=$email?>?subject=App Feedback">Contact Us</a>--></div>

<div style='display:none;'><?php echo $pageglobal['googgle_map_api_keys']; ?></div>