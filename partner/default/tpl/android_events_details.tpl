<div id="topbar">
<div id="title">Event Info</div>
<div id="leftnav">   
<a href="events.php?d=<?=$today?>&m=<?=$tomonth?>&Y=<?=$toyear?>&lat=<?=$lat1?>&lon=<?=$lon1?>" >Back</a></div>
        
        
<div id="rightnav"></div>
</div>

<div id="content">
	<ul class="pageitem">	
      <?php 
	  while($row=mysql_fetch_array($rec))
	  {
	  	//#DD#
		  $ev=mysql_query("select *  from jos_jevents_vevent where ev_id=".$row['eventid']) or die(mysql_error());
		  $evDetails=mysql_fetch_array($ev);
		  $evrawdata = unserialize($evDetails['rawdata']);
		  //#DD#	
		  
			//$queryvevdetail="select *  from jos_jevents_vevdetail where evdet_id=".$row['eventid'];
			$queryvevdetail="select *  from jos_jevents_vevdetail where evdet_id=".$row['eventdetail_id'];
			$recvevdetail=mysql_query($queryvevdetail) or die(mysql_error());
			$rowvevdetail=mysql_fetch_array($recvevdetail);
			if ((int) ($rowvevdetail['location']))
			{
			$querylocdetail="select *  from jos_jev_locations where loc_id=".$rowvevdetail['location'];
			$reclocdetail=mysql_query($querylocdetail) or die(mysql_error());
			$rowlocdetail=mysql_fetch_array($reclocdetail);
			$lat2=$rowlocdetail['geolat'];
			$lon2=$rowlocdetail['geolon'];
			
			}
	  ?>
      <li class="textbox">
      <div style="width:100%"><strong><?=$rowvevdetail['summary']?></strong>
      <br /><br />
      <div style="width:100%"><div class="gray" style="width:10%;float:left;padding-right:50px;">Date:</div><div style="width:100%"><?=$todaestring?></div></div><br />
     <div style="width:100%"><div class="gray" style="width:10%;float:left;padding-right:50px;">Time:</div><div style="width:100%">
			<?php
				//#DD#
				if($evrawdata['allDayEvent']=='on'){
					echo 'All Day Event';
				}else{
					echo ltrim($row[timestart], "0");
				}
				//#DD#
			?>      	
     </div></div><br />
     <div style="width:100%"><div class="gray" style="width:10%;float:left;padding-right:50px;">Location:</div><div style="width:100%"><?=$rowlocdetail['title']?></div></div><br />
     <div style="width:100%"><div class="gray" style="width:10%;float:left;padding-right:50px;">Address:</div><div style="width:100%"><a href="map.php?lat=<?=$lat2?>&long=<?=$lon2?>"><?=$rowlocdetail['street']?></a></div></div><br />
     <div style="width:100%"><div class="gray" style="width:10%;float:left;padding-right:50px;">Phone:</div><div style="width:100%"><a href="tel:<?=$rowlocdetail['phone']?>"><?=$rowlocdetail['phone']?></a></div></div><br />
     <div style="width:100%"><div class="gray" style="width:10%;float:left;padding-right:50px;">Distance:</div><div style="width:100%"> <?=round(distance($lat1, $lon1, $lat2, $lon2, "m"),'1').' miles'?></div></div><br />
	<?php if(trim($rowlocdetail['url']) != '') { ?>
     <div style="width:100%"><div class="gray" style="width:10%;float:left;padding-right:50px;">Website:</div><div style="width:100%"><a href="http://<?php echo str_replace('http://','',$rowlocdetail['url']); ?>" target="_blank"><?php echo str_replace('http://','',$rowlocdetail['url']); ?></a></div></div><br />
	<?php } ?>
     <div style="width:100%"><div class="gray" style="width:10%;float:left;padding-right:50px;">Description:</div><div style="width:100%"><?=$rowvevdetail['description']?></div></div><br />
      </div>
      </li>
      <?php
	  }
	  ?>
		
	</ul>
</div>

<div id="footer">

	&copy; <?=date('Y');?> <?=$site_name?> | <a href="mailto:<?=$email?>?subject=Feedback">Contact Us</a></div></div>
<div style='display:none;'><?php echo $pageglobal['googgle_map_api_keys']; ?></div>