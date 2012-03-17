<div id="topbar">
<div id="title">Lugares</div>
	<div id="leftnav">   
            <?php 
         if ($current_page!=0)
				  			{
							 $st1=($current_page*$num_rec)-$num_rec;	
						?>
    <a href="<?php echo $_SERVER['PHP_SELF']; ?>?start=<?=$st1?>&lat=<?=$lat1?>&lon=<?=$lon1?>&filter_loccat=<?=$loccat?>">Espalda</a>
    <?php }?>

        </div>
        
        
	<div id="rightnav">
		    <?php
					  if (($current_page+1)<$num_pages)
				 		 {
					  $st1=($current_page*$num_rec)+$num_rec;
					  ?>
    <a href="<?php echo $_SERVER['PHP_SELF']; ?>?start=<?=$st1?>&lat=<?=$lat1?>&lon=<?=$lon1?>&filter_loccat=<?=$loccat?>">Más</a>
    <?php }?>
    
</div></div>


<div id="content">

	<script type="text/javascript">
		function redirecturl(val)
		{
			url="<?php echo $_SERVER['PHP_SELF']; ?>?lat=<?php echo $_REQUEST['lat'];?>&lon=<?php echo $_REQUEST['lon'];?>&filter_loccat="+val;
			window.location=url;
		}
		
		function divopen(str) {

			if(document.getElementById(str).style.display=="none") {
				document.getElementById(str).style.display="block";
			} else {
				document.getElementById(str).style.display="none";
			}
		}

	</script>
	
	<style>
		body { margin-top: 1px; margin-left: 0px; margin-right: 0px; font-family: Verdana, Geneva, sans-serif; }
		.bluetext { color: #0088BB; font-size: 13px; font-weight:bold; }
		.bluetextsmall { color: #00AADD; font-size: 13px; /*font-style: italic;*/}
		.headertext { color: #000000; font-size: 17px; }
		.graytext { color: #777777; font-size: 14px; }
		.graytextSmall { color: #777777; font-size: 13px; }
		.linktext { color: #0000ff; font-size: 14px; text-decoration: underline; } 
	</style>


		<ul class="pageitem" style="width:85%;">
			<li class="select">

			<?php
		$recsub=mysql_query("select * from jos_categories where (parent_id=151 OR id=151) AND section='com_jevlocations2' and published=1 order by `ordering`") or die(mysql_error());
			?>

			<select name="d" onChange="redirecturl(this.value)" >
			<option value="0">Seleccione una categoría</option>
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
	 <span class="arrow"></span>
	 </li>
	
	</ul>
	
	<div style="border:0px solid;width:30px;height:25px;margin-top:-50px;margin-right:10px;float:right;">
	<div onclick="divopen('q1')" style="padding-top:5px;width:25px; height:25px;float:right;cursor:pointer"><img src="images/find.png" height="25px" width="25px"/></div>
</div>

	<ul class="pageitem" style='border:0px;'><li>
	<div id="q1" style="display:none;cursor:pointer;text-align:center;margin-top:15px"><form action="" method="post" name="location_form"><input type="text" name="searchvalue" value="" size="25"/><input type="submit" name="search_rcd" value="Buscar"/></form></div>
	</li></ul>
	


	<ul class="pageitem">
      <?php 
	  while($row=mysql_fetch_array($rec))
	  {
		  $lat2=$row[geolat];
			$lon2=$row[geolon];
	  ?>
      <li class="textbox"><div style="float:left;width:80%;padding-right:5px;">
      <strong><?=$row['title']?></strong><br /><span class="grayplain"><?php echo stripJunk(showBrief(strip_tags($row['description']),30)); ?></span>
        <br /> 
        <div class="gray"><a href="tel:<?=$row['phone']?>"><?=$row['phone']?></a>
        | <a href="dining_details.php?did=<?=$row['loc_id']?>&lat=<?=$lat1?>&lon=<?=$lon1?>">más información</a></div></div>
        <div style="float:right;width:15%;vertical-align:top;padding-top:0px;"><?=round(distance($lat1, $lon1, $lat2, $lon2, "m"),'1').' mi'?></div>
  
      </li>
      <?php
	  }
	  ?>
		
	</ul>

<div id="footer">&copy; <?=date('Y');?> <?=$site_name?> | <a href="mailto:<?=$email?>?subject=Feedback">Contacte con nosotros</a></div></div>