<?php 
setlocale(LC_TIME,"spanish");
$todaestring=ucwords(strftime ('%a, %b %d',mktime(0, 0, 0, $tomonth, $today, $toyear)));
?>
<div id="menu">
	<a id="calPrev" href="events.php?d=<?=date('d',mktime(0, 0, 0,$tomonth ,$today-1, $toyear));?>&m=<?=date('m',mktime(0, 0, 0,$tomonth ,$today-1, $toyear));?>&Y=<?=date('Y',mktime(0, 0, 0,$tomonth ,$today-1, $toyear));?>&lat=<?=$lat1?>&lon=<?=$lon1?>"></a>
	<h1><?=$todaestring?></h1>
	<form name='events' id='events' action='events.php' method='post'>
		<input type="text" value="" class="mobiscroll ui-input-text ui-body-null ui-corner-all ui-shadow-inset ui-body-d scroller" id="date1" name="eventdate" style="width:0px;height:0px;border:0px;background:#333333;    position: absolute;top: -100px;">
		<button data-theme="a" style='background: url("images/calIcon.png") no-repeat center;width:31px;height: 27px;margin-top:0px;border:0px solid blue;margin:0px;padding:0px;' id="show" class="ui-btn-hidden" aria-disabled="false"></button>
	</form>
	<a id="calNext" href="events.php?d=<?=date('d',mktime(0, 0, 0,$tomonth ,$today+1, $toyear));?>&m=<?=date('m',mktime(0, 0, 0,$tomonth ,$today+1, $toyear));?>&Y=<?=date('Y',mktime(0, 0, 0,$tomonth ,$today+1, $toyear));?>&lat=<?=$lat1?>&lon=<?=$lon1?>"></a>
</div> <!-- menu -->


<div id="list">
	<table>
		<thead>
				<tr>
					<th class="one">Tiempo</th>
					<th class="two">Evento</th>
					<th class="three">Distancia</th>
				</tr>
		</thead>
		<tbody>	
      <?php 
	  $n = 0;
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
			$lat2=$rowlocdetail[geolat];
			$lon2=$rowlocdetail[geolon];
			
			}

			#DD#
			$displayTime = '';
			if($evrawdata['allDayEvent']=='on')
			{
				$displayTime.='Todo el día';
			}
			else
			{
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
      	<a href="tel:<?php echo str_replace(array(' ','(',')','-','.'), '',$rowlocdetail['phone'])?>">llamar</a> |
      	<a href="javascript:linkClicked('APP30A:FBCHECKIN:<?php echo $lat2; ?>:<?php echo $lon2; ?>')">facturar</a> | 
      	<a href="events_details.php?eid=<?=$row['rp_id']?>&d=<?=$today?>&m=<?=$tomonth?>&Y=<?=$toyear?>&lat=<?=$lat1?>&lon=<?=$lon1?>">m&#225;s info</a>  
      </td>
      <td class="three"><?=round(distance($_SESSION['lat_device1'], $_SESSION['lon_device1'], $lat2, $lon2,$dunit),'1')?>&nbsp;<?=$dunit?></td>
  
      </li>
      <?php
	  $rowlocdetail['title']="";
	  ++$n;
	  }
	  
	  if(0 == $n)
	  {
	  	

     	echo '<style>#list table{display:none;}</style><div style="padding-top: 40px; border: 1px solid #878787;padding-bottom: 40px;text-align:center;font-size: 15px;">Hoy No Hay Eventos</div>';
     
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
			<input type="text" value="" class="mobiscroll ui-input-text ui-body-null ui-corner-all ui-shadow-inset ui-body-d scroller" id="date3" name="eventdate1" style='background: url("images/calIcon.png") no-repeat center;width:31px;height: 27px;margin-top:0px;border:0px solid blue;margin:0px;padding:0px;cursor: default; font-size:0px;' >
		</form>
		<a id="calNext" href="events.php?d=<?=date('d',mktime(0, 0, 0,$tomonth ,$today+1, $toyear));?>&m=<?=date('m',mktime(0, 0, 0,$tomonth ,$today+1, $toyear));?>&Y=<?=date('Y',mktime(0, 0, 0,$tomonth ,$today+1, $toyear));?>&lat=<?=$lat1?>&lon=<?=$lon1?>"></a>
	</div> <!-- menu -->

<!-- <div id="footer">&copy; <?=date('Y');?> <?=$site_name?>, Inc. | <a href="mailto:<?=$email?>?subject=App Feedback">Contacte con nosotros</a></div>-->
<div style='display:none;'><?php echo $pageglobal['googgle_map_api_keys']; ?></div>