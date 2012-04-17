<div id="content" style="width:310px;">
		<ul class="pageitem" style="width:85%; margin-bottom:5px;">
			<li class="select">

			<?php 
				$recsub=mysql_query("select * from jos_categories where (parent_id=151 OR id=151) AND section='com_jevlocations2' and published=1 order by `ordering`") or die(mysql_error());
			?>

			<select name="d" onChange="redirecturl(this.value)" style="width:100%; height:45px;border: 0pt none;font-weight:bold;font-size:17px;" >
			<option value="0">Select a Category</option>
			<option value="0">All</option>
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
	 <span class="arrow"></span>
	 </li>
	
	</ul>
	
	<div style="border:0px solid;width:30px;height:25px;margin-top:-50px;margin-right:10px;float:right;">
	<div onclick="divopen('q1')" style="padding-top:5px;width:25px; height:25px;float:right;cursor:pointer"><img src="../../images/find.png" height="25px" width="25px"/></div>
</div>

	<ul class="pageitem" style='border:0px; margin-bottom:5px;'><li>
	<div id="q1" style="display:none;cursor:pointer;text-align:center;margin-top:5px"><form action="" method="post" name="location_form"><input type="text" name="searchvalue" value="" size="25"/><input type="submit" name="search_rcd" value="Search"/></form></div>
	</li></ul>

	<ul class="pageitem">
      <?php 
	  while($row=mysql_fetch_array($rec))
	  {
		  $lat2=$row[geolat];
			$lon2=$row[geolon];
	  ?>
      <li class="textbox">
      <div style="float:left;width:80%;padding-right:5px;">
		<strong><?=$row['title']?></strong><br />
		<span class="grayplain"><?php echo stripJunk(showBrief(strip_tags($row['description']),30)); ?></span><br /> 
        <div class="gray">
        	<a href="tel:<?php echo str_replace(array(' ','(',')','-','.'), '',$row['phone'])?>"><?=$row['phone']?></a> | 
			<a class="linktext" href="javascript:linkClicked('APP30A:FBCHECKIN:<?php echo $row[geolat]; ?>:<?php echo $row[geolon]; ?>')">check in</a> | 
			<a class="linktext" href="diningdetails.php?did=<?=$row['loc_id']?>&lat=<?=$lat1?>&lon=<?=$lon1?>">more info</a> 
			<a class="linktext" href="javascript:linkClicked('APP30A:SHOWMAP:<?php echo $row[geolon]; ?>:<?php echo $row[geolat]; ?>')"></a> 
	        <!--<a href="dining_details.php?did=<?=$row['loc_id']?>&lat=<?=$lat1?>&lon=<?=$lon1?>">more info</a> -->
        </div>
      </div>
      <div style="float:right;width:15%;vertical-align:top;padding-top:0px;"><?=round(distance($lat1, $lon1, $lat2, $lon2, "m"),'1').' mi'?></div>
  
      </li>
      <?php
	  }
	  ?>
		
	</ul>

 <div id="footer">&copy; <?=date('Y');?> <?=$site_name?> | <a href="mailto:<?=$email?>?subject=App Feedback">Contact Us</a></div> </div> 
<div style='display:none;'><?php echo $pageglobal['googgle_map_api_keys']; ?></div>