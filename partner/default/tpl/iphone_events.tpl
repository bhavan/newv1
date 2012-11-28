<div id="menu">
	<a id="calPrev" href="events.php?d=<?=date('d',mktime(0, 0, 0,$tomonth ,$today-1, $toyear));?>&m=<?=date('m',mktime(0, 0, 0,$tomonth ,$today-1, $toyear));?>&Y=<?=date('Y',mktime(0, 0, 0,$tomonth ,$today-1, $toyear));?>&lat=<?=$lat1?>&lon=<?=$lon1?>"></a>
	<h1><?=$todaestring?></h1>
	<form name='events' id='events' action='events.php' method='post'>
		<input type="text" value="" class="mobiscroll ui-input-text ui-body-null ui-corner-all ui-shadow-inset ui-body-d scroller" id="date1" name="eventdate" style="width:0px;height:0px;border:0px;background:#333333;    position: absolute;top: -100px;">
		<button data-theme="a" id="show" class="ui-btn-hidden" aria-disabled="false"></button>
	</form>
	<a id="calNext" href="events.php?d=<?=date('d',mktime(0, 0, 0,$tomonth ,$today+1, $toyear));?>&m=<?=date('m',mktime(0, 0, 0,$tomonth ,$today+1, $toyear));?>&Y=<?=date('Y',mktime(0, 0, 0,$tomonth ,$today+1, $toyear));?>&lat=<?=$lat1?>&lon=<?=$lon1?>"></a>
</div> <!-- menu -->
<div id="list" ontouchstart="touchStart(event,'list');" ontouchend="touchEnd(event);" ontouchmove="touchMove(event);" ontouchcancel="touchCancel(event);">
	<table>
		<thead>
			<tr>
				<th class="one">Time</th>
				<th class="two">Event</th>
				<th class="three">Distance</th>
			</tr>
		</thead>
		<tbody>
			<?php 
			$n = 0;
			while($row=mysql_fetch_array($rec)){
			//#DD#
			$ev=mysql_query("select *  from jos_jevents_vevent where ev_id=".$row['eventid']) or die(mysql_error());
			$evDetails=mysql_fetch_array($ev);
			$evrawdata = unserialize($evDetails['rawdata']);
			//#DD#	
			//$queryvevdetail="select *  from jos_jevents_vevdetail where evdet_id=".$row['eventid'];
			$queryvevdetail="select *  from jos_jevents_vevdetail where evdet_id=".$row['eventdetail_id'];
			$recvevdetail=mysql_query($queryvevdetail) or die(mysql_error());
			$rowvevdetail=mysql_fetch_array($recvevdetail);

			if ((int) ($rowvevdetail['location'])){
				$querylocdetail="select *  from jos_jev_locations where loc_id=".$rowvevdetail['location'];
				$reclocdetail=mysql_query($querylocdetail) or die(mysql_error());
				$rowlocdetail=mysql_fetch_array($reclocdetail);
				$lat2=$rowlocdetail[geolat];
				$lon2=$rowlocdetail[geolon];
			}

			#DD#
			$displayTime = '';
			if($evrawdata['allDayEvent']=='on'){
				$displayTime.='All Day Event';
			}else{
				$displayTime.= ltrim($row[timestart], "0");
				if($evrawdata['NOENDTIME']!=1){
					$displayTime.='-'.ltrim($row[timeend], "0");
				}
			}	
			#DD#
	  ?>
		<tr>
			<td class="one"><?=$displayTime?></td>
			<td class="two">
      			<strong><?=$rowvevdetail['summary']?></strong><br />
      			<span class="grayplan"><?=$rowlocdetail['title']?></span><br />
				<ul>
				<li><a class="call" href="tel:<?php echo str_replace(array(' ','(',')','-','.'), '',$rowlocdetail['phone'])?>">call</a</li>
				<?php
	 					 $ua = strtolower($_SERVER['HTTP_USER_AGENT']);
					 	if(stripos($ua,'android') == true) { ?>
 				<?php } else { ?>
				<li><a class="checkin" href="javascript:linkClicked('APP30A:FBCHECKIN:<?php echo $lat2; ?>:<?php echo $lon2; ?>')">check in</a></li>
				<?php } ?>
				<li><a class="info" href="events_details.php?eid=<?=$row['rp_id']?>&d=<?=$today?>&m=<?=$tomonth?>&Y=<?=$toyear?>&lat=<?=$lat1?>&lon=<?=$lon1?>">more info</a></li>
				</ul> 
      		</td>
			<td class="three"><?=round(distance($_SESSION['lat_device1'], $_SESSION['lon_device1'], $lat2, $lon2,$dunit),'1')?>&nbsp;<?=$dunit?></td>
      </tr>
      <?php
	  $rowlocdetail['title']="";
	  ++$n;
	  }
	  if(0 == $n)
	  {
     	echo '<style>#list table{display:none;}</style><div style="padding-top: 80px; border: 1px solid #878787;padding-bottom: 80px;text-align:center;font-size: 15px;">No Events Today</div>';
	  }  
	  ?>
		</tbody>
	</table>
</div>
<!-- Bottom Nav -->
	<div id="menu">
		<a id="calPrev" href="events.php?d=<?=date('d',mktime(0, 0, 0,$tomonth ,$today-1, $toyear));?>&m=<?=date('m',mktime(0, 0, 0,$tomonth ,$today-1, $toyear));?>&Y=<?=date('Y',mktime(0, 0, 0,$tomonth ,$today-1, $toyear));?>&lat=<?=$lat1?>&lon=<?=$lon1?>"></a>
		<h1><?=$todaestring?></h1>
		<form name='events1' id='events' action='events.php' method='post'>
		<input type="text" value="" class="mobiscroll ui-input-text ui-body-null ui-corner-all ui-shadow-inset ui-body-d scroller" id="date3" name="eventdate1">
		</form>
		<a id="calNext" href="events.php?d=<?=date('d',mktime(0, 0, 0,$tomonth ,$today+1, $toyear));?>&m=<?=date('m',mktime(0, 0, 0,$tomonth ,$today+1, $toyear));?>&Y=<?=date('Y',mktime(0, 0, 0,$tomonth ,$today+1, $toyear));?>&lat=<?=$lat1?>&lon=<?=$lon1?>"></a>
	</div> <!-- menu -->
<!-- <div id="footer">&copy; <?=date('Y');?> <?=$site_name?>, Inc. <!-- | <a href="mailto:<?=$email?>?subject=App Feedback">Contact Us</a> </div>  -->
<div style='display:none;'><?php echo $pageglobal['googgle_map_api_keys']; ?></div>