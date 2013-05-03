<div id="main" role="main">
	<div id="searchBar">
		<form id="placeCatForm">
			<?php $recsubsql="select * from jos_categories where (parent_id=151 OR id=151) AND section='com_jevlocations2' and published=1 ORDER BY title ASC";
			$recsub = mysql_query($recsubsql) or die(mysql_error());?>
			<select name="d" onChange="redirecturl(this.value)" >
				<option value="0">Select a Category</option>
				<option value="0">All</option>
				<option value="alp" <?php if ($_REQUEST['filter_loccat']=='alp') {?> selected <?php }?>>Alphabetic</option>
				<?php
				while($rowsub = mysql_fetch_array($recsub)){
					$querycount = "SELECT * FROM jos_jev_locations WHERE published=1 and loccat=".$rowsub['id'];
					if($filter_order != "")
						$querycount .= " ORDER BY title ASC ";
					else
						$querycount .= " ORDER BY ordering ASC";
						
					$reccount = mysql_query($querycount) or die(mysql_error());
					if(mysql_num_rows($reccount)){
						if(($_REQUEST['filter_loccat']!='alp') || ($_REQUEST['filter_loccat']!='0')){?>
							<option value="<?php echo $rowsub['id'];?>" <?php if ($_REQUEST['filter_loccat']==$rowsub['id']) {?> selected <?php }?>><?php echo $rowsub['title'];?></option>
				<?php }}
				}?>
			</select>
		</form>
		
		<div onclick="divopen('q1')">
			<a id="searchIcon" href="#">s</a>
			<form action="" method="post" name="location_form" id="searchForm">
				<fieldset>
					<input type="search" name="searchvalue" value="" size="15"/>
					<input type="submit" name="search_rcd" value="Search"/>
				</fieldset>
			</form>
		</div>
	</div>

	<ul id="placesList" class="mainList">
		
		<?php
		if($_POST['search_rcd']!="Search") { 
			$query = "SELECT *,(((acos(sin(($lat1 * pi() / 180)) * sin((geolat * pi() / 180)) + cos(($lat1 * pi() / 180)) * cos((geolat * pi() / 180)) * cos((($lon1 - geolon) * pi() / 180)))) * 180 / pi()) * 60 * 1.1515) as dist FROM jos_jev_locations $customfields3_table WHERE loccat IN (".implode(',',$allCatIds).") AND published=1 ".$subquery;
			
			if($filter_loccat == 'Featured')
				$query .= " AND (jos_jev_locations.loc_id = jos_jev_customfields3.target_id AND jos_jev_customfields3.value = 1 ) ";
			elseif($filter_loccat != 0 && $_REQUEST['filter_loccat'] != 'alp')
				$query .= " AND loccat = $filter_loccat ";
			
			if(($filter_order != "") || ($_REQUEST['filter_loccat']=='alp'))
				$query .= " ORDER BY title ASC LIMIT " .$start_at.','.$entries_per_page;
			else 
				$query .= " ORDER BY dist ASC LIMIT " .$start_at.','.$entries_per_page;
			
			$rec = mysql_query($query) or die(mysql_error());
			$n = 0;
			
			while($row=mysql_fetch_assoc($rec)){
				$distance = distance($lat1, $lon1, $row[geolat],  $row[geolon], $dunit);?>
				<li>
					<h1><?php echo utf8_encode($row['title'])?></h1>
					<p><?php echo stripJunk(showBrief(strip_tags(utf8_encode($row['description'])),30)); ?></p>
					<p class="distance"><?php echo round($distance,1); ?>&nbsp;<?php echo $dunit?> Away</p>
					<ul class="btnList">
						<?php if ($_REQUEST['bIPhone']=='0'){?>
									<li><a class="button small" href="tel:<?php echo str_replace(array(' ','(',')','-','.'), '', $row[phone]); ?>">call</a></li>
						<?php } else { ?>
									<li><a class="button small" href="tel:<?php echo str_replace(array(' ','(',')','-','.'), '', $row[phone]); ?>">call</a></li>
						<?php } ?>

						<li><a class="button small" href="javascript:linkClicked('APP30A:FBCHECKIN:<?php echo $row[geolat]; ?>:<?php echo $row[geolon]; ?>')">check in</a></li>
						<li><a class="button small" href="diningdetails.php?did=<?php echo $row['loc_id']?>&lat=<?php echo $lat1?>&lon=<?php echo $lon1?>">more info</a></li>
						<li><a href="javascript:linkClicked('APP30A:SHOWMAP:<?php echo $row[geolon]; ?>:<?php echo $row[geolat]; ?>')"></a></li>
					</ul>
				</li>
			<?php ++$n;
			}
		}
		
		if($_POST['search_rcd']=="Search"){
			$searchdata = addslashes($_POST['searchvalue']);
			
			if(($filter_loccat==0) || ($_REQUEST['filter_loccat']=='alp') && ($_POST['search_rcd']=="Search")){
				$search_query1="select * from `jos_jev_locations` where loccat IN (".implode(',',$allCatIds).") AND published=1 and title like '%$searchdata%' or description like '%$searchdata%' ORDER BY title ASC LIMIT " .$start_at.','.$entries_per_page;
			}else if($filter_loccat == 'Featured' && $_POST['search_rcd']=="Search" ){
				$search_query1="select * from `jos_jev_locations` $customfields3_table where loccat IN (".implode(',',$allCatIds).") AND published=1 and title like '%$searchdata%' or description like '%$searchdata%'  AND (jos_jev_locations.loc_id = jos_jev_customfields3.target_id AND jos_jev_customfields3.value = 1 ) ORDER BY title ASC LIMIT " .$start_at.','.$entries_per_page;
			}else if($_POST['search_rcd'] == "Search"){
				$search_query1="select * from `jos_jev_locations` where loccat IN (".implode(',',$allCatIds).") AND published=1 and loccat=$filter_loccat and title like '%$searchdata%' or description like '%$searchdata%' ORDER BY title ASC LIMIT " .$start_at.','.$entries_per_page;
			}
			
			$search_query = mysql_query($search_query1) or die(mysql_error());
			
			while($data = mysql_fetch_array($search_query)) {
				$title=$data[title];
				$lat2=$data[geolat];
				$lon2=$data[geolon];
				
				$dist = distance($lat1, $lon1, $lat2, $lon2, $dunit);?>
				<li>
					<h1><?php echo utf8_encode($data['title'])?></h1>
					<p><?php echo stripJunk(showBrief(strip_tags(utf8_encode($data['description'])),30)); ?></p>
					<p class="distance"><?php echo round($dist,1); ?>&nbsp;<?php echo $dunit?> Away</p>
					<ul class="btnList">
						<?php if ($_REQUEST['bIPhone'] == '0'){?>
						   			<li><a class="button small" href="tel:<?php echo str_replace(array(' ','(',')','-','.'), '', $data[phone]); ?>">call</a></li>
						<?php } else { ?>
									<li><a class="button small" href="tel:<?php echo str_replace(array(' ','(',')','-','.'), '', $data[phone]); ?>">call</a></li>
						<?php } ?>
						
						<li><a class="button small" href="javascript:linkClicked('APP30A:FBCHECKIN:<?php echo $data[geolat]; ?>:<?php echo $data[geolon]; ?>')">check in</a></li>
						<li><a class="button small" href="diningdetails.php?did=<?php echo $data['loc_id']?>&lat=<?php echo $lat1?>&lon=<?php echo $lon1?>">more info</a></li>
						<li><a  href="javascript:linkClicked('APP30A:SHOWMAP:<?php echo $data['geolon']; ?>:<?php echo $data['geolat']; ?>')"></a></li>
					</ul><?php
			} 
		}
		include("connection.php");?>
		</li>
	</ul>
	<?php 
	if($n =='50'){
		echo get_paginate_links($total_rows,$entries_per_page,$current_page,$link_to);
	}?>
	<div style='display:none;'>
		<?php echo $pageglobal['googgle_map_api_keys']; ?>
	</div>
</div>