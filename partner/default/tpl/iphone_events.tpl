<div id="topbar" style="height:40px; margin-top:5px; width:100%;">
<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr>
		<td width="50" height="50" valign="top" align="left">
		<div id="leftnav" style="left:15px;"> 
			<a href="events.php?d=<?=date('d',mktime(0, 0, 0,$tomonth ,$today-1, $toyear));?>&m=<?=date('m',mktime(0, 0, 0,$tomonth ,$today-1, $toyear));?>&Y=<?=date('Y',mktime(0, 0, 0,$tomonth ,$today-1, $toyear));?>&lat=<?=$lat1?>&lon=<?=$lon1?>"></a> 
		</div>
		</td>
		<td width="200" height="50" valign="top" align="center">
			<table cellpadding="0" cellspacing="0" border="0" width="200">
				<tr>
					<td width="150" height="50" valign="top" align="center">
						<div style="width:120px;text-align:right;font-size:16pt;color:#333333; font-weight:bold;"><?=$todaestring?></div>
					</td>
					<td width="50" height="50" valign="top" align="left">
						<div style="width:60px;text-align:left;">
							<form name='events' id='events' action='events.php' method='post'>
								<input type="text" value="" class="mobiscroll ui-input-text ui-body-null ui-corner-all ui-shadow-inset ui-body-d scroller" id="date1" name="eventdate" style="width:0px;height:0px;border:0px;">
								<button data-theme="a" style='background: url("images/calendar.jpg") no-repeat center;width: 25px;height: 25px;margin-top:0px;border:0px solid blue;margin:0px;padding:0px;' id="show" class="ui-btn-hidden" aria-disabled="false"></button>
							</form>
						</div>
					</td>
				</tr>
			</table>
		</td>
		<td width="50" height="50" valign="top" align="right">
		<div id="rightnav" style="right: 15px;">
			<a href="events.php?d=<?=date('d',mktime(0, 0, 0,$tomonth ,$today+1, $toyear));?>&m=<?=date('m',mktime(0, 0, 0,$tomonth ,$today+1, $toyear));?>&Y=<?=date('Y',mktime(0, 0, 0,$tomonth ,$today+1, $toyear));?>&lat=<?=$lat1?>&lon=<?=$lon1?>"></a>
		</div>	
		</td>
	</tr>
</table>
</div>


<div id="content">
  <ul class="pageitem">
	<li class="textbox">

   	 <div style="float:right;width:18%" class="dist">Distance</div>
    </li>	
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
				$displayTime.='All Day Event';
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
	  
      <li class="textbox">
      <div style="float:left;padding-right:7px;width:18%;font-size:11px;" class="small"><?=$displayTime?></div>
      <div style="float:left;width:54%;text-align:left;">
      	<strong><?=$rowvevdetail['summary']?></strong><br />
      	<span class="grayplan"><?=$rowlocdetail['title']?></span><br />
		<a href="tel:<?php echo str_replace(array(' ','(',')','-','.'), '',$rowlocdetail['phone'])?>">call</a> |
      	<a href="javascript:linkClicked('APP30A:FBCHECKIN:<?php echo $lat2; ?>:<?php echo $lon2; ?>')">check in</a> | 
      	<a href="events_details.php?eid=<?=$row['rp_id']?>&d=<?=$today?>&m=<?=$tomonth?>&Y=<?=$toyear?>&lat=<?=$lat1?>&lon=<?=$lon1?>">more info</a>  
      </div>
      <div style="float:right;width:25%; text-align:right"><?=round(distance($lat1, $lon1, $lat2, $lon2,$dunit),'1')?>&nbsp;<?=$dunit?></div>
  
      </li>
      <?php
	  $rowlocdetail['title']="";
	  ++$n;
	  }
	  
	  if(0 == $n)
	  {
	  	

     	echo '<style>.dist{display:none;}</style><div style="padding-top: 10px; border: 1px solid #878787;padding-bottom: 10px;text-align:center;font-size: 15px;">No Events Today</div>';
     
	  }  
	  ?>
		
	</ul>
</div>

<!-- Bottom Nav -->
<div id="tributton2">
	<div class="links" style="width:100%;">
	     <div id="leftnav" style="left:15px;">   
                 <a href="events.php?d=<?=date('d',mktime(0, 0, 0,$tomonth ,$today-1, $toyear));?>&m=<?=date('m',mktime(0, 0, 0,$tomonth ,$today-1, $toyear));?>&Y=<?=date('Y',mktime(0, 0, 0,$tomonth ,$today-1, $toyear));?>&lat=<?=$lat1?>&lon=<?=$lon1?>">&nbsp;</a>
             </div>
                 <a id="pressed" href="#"><?=$todaestring?></a>
             <div id="rightnav" style="right:15px;">
                 <a href="events.php?d=<?=date('d',mktime(0, 0, 0,$tomonth ,$today+1, $toyear));?>&m=<?=date('m',mktime(0, 0, 0,$tomonth ,$today+1, $toyear));?>&Y=<?=date('Y',mktime(0, 0, 0,$tomonth ,$today+1, $toyear));?>&lat=<?=$lat1?>&lon=<?=$lon1?>">&nbsp;</a>   
             </div>
       </div>
</div>

<div id="footer">&copy; <?=date('Y');?> <?=$site_name?>, Inc. <!-- | <a href="mailto:<?=$email?>?subject=App Feedback">Contact Us</a>--> </div>
<div style='display:none;'><?php echo $pageglobal['googgle_map_api_keys']; ?></div>