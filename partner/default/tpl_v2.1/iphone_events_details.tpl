<div id="main" role="main" ontouchstart="touchStart(event,'list');" ontouchend="touchEnd(event);" ontouchmove="touchMove(event);" ontouchcancel="touchCancel(event);">
	<ul id="placesList" class="mainList"><li>
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
		<H1><?=$rowvevdetail['summary']?></h1>

		<p><strong>Date:</strong><?=$todaestring?></p>
		<p><strong>Time:</strong>
		<?php
		//#DD#
		/* Coded By Rinkal */
		$displayTime = '';
			if($row[timestart]=='12:00 AM' && $row[timeend]=='11:59PM')
            {    echo 'All Day Event';}
			else{
				$displayTime.= ltrim($row[timestart], "0");
				if($rowvevdetail['noendtime']==0){
					$displayTime.='-'.ltrim($row[timeend], "0");
				}
				echo $displayTime;
			}
	   /* End By Rinkal */
		
		
		/* if($evrawdata['allDayEvent']=='on'){
				echo 'All Day Event';
			}else{
					$displayTime.= ltrim($row[timestart], "0");
					if($evrawdata['NOENDTIME']!=1){
					$displayTime.='-'.ltrim($row[timeend], "0");
					}
		echo $displayTime;
		} */
		//#DD#
		?>

		</p>
		<p><strong>Location:</strong><?=$rowlocdetail['title']?></p>
		<?php
		$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
		if(stripos($ua,'android') == true) { ?>
		<p><strong>Address:</strong><a href="map.php?lat=<?=$lat2?>&long=<?=$lon2?>"><?=$rowlocdetail['street']?></a></p>
			<?php } else { ?>
		<p><strong>Address:</strong><a href="javascript:linkClicked('APP30A:SHOWMAP:<?=$lat2?>:<?=$lon2?>')" ><?=$rowlocdetail['street']?></a></p>
			<?php } ?>
		<p><strong>Phone:</strong><a href="tel:<?php echo str_replace(array(' ','(',')','-','.'), '',$rowlocdetail['phone'])?>"><?=$rowlocdetail['phone']?></a></p>
		<p><strong>Distance:</strong><?=round(distance($_SESSION['lat_device1'], $_SESSION['lon_device1'], $lat2, $lon2,$dunit),'1')?>&nbsp;<?=$dunit?></p>
		<?php if(trim($rowlocdetail['url']) != '') { ?>
		<p><strong>Website:</strong><a href="http://<?php echo str_replace('http://','',$rowlocdetail['url']); ?>" target="_blank"><?php echo str_replace('http://','',$rowlocdetail['url']); ?></a></p>
			<?php } ?>
	<p><strong>Description:</strong><?=$rowvevdetail['description']?></p>
		<?php
		//#DD#
		$mailContent.= "
		{$rowvevdetail['summary']} %0D%0A%0D%0A
		Date: {$todaestring} %0D%0A%0D%0A
		Time: " . ltrim($row[timestart], "0"). " %0D%0A%0D%0A
		Location: {$rowlocdetail['title']} %0D%0A%0D%0A
		Address: {$rowlocdetail['street']} %0D%0A%0D%0A
		Phone: {$rowlocdetail['phone']} %0D%0A%0D%0A
		";
		if(trim($rowlocdetail['url']) != '') { 
		$mailContent.="Website: ". str_replace('http://','',$rowlocdetail['url']) ."%0D%0A%0D%0A";
		} 
		$mailContent.="Description: {$rowvevdetail['description']} %0D%0A%0D%0A";
		$mailContent = str_replace('
		<br/>
			',"%0D%0A", $mailContent);
			$mailContent = str_replace('
				<br>
					',"%0D%0A", $mailContent);
					$mailContent = str_replace('
				<br />
				',"%0D%0A", $mailContent);
				$mailContent = str_replace('"','\"', $mailContent);
				$mailContent = strip_tags($mailContent);
				//#DD#
				}
				?>
				<!-- Added by yogi for Facebook Share feature Begin -->
				<?php 
				//$eddate_array = explode(" ",$rowvevdetail['modified']);
				// $ev_detail_date = $eddate_array[0];
				$ev_detail_date = date('Y-m-d', mktime(0, 0, 0, $tomonth, $today, $toyear));
				$ev_detail_title = $rowvevdetail['summary']; 
				$ev_detail_id = $rowvevdetail['evdet_id'];
				$host = $_SERVER[HTTP_HOST];
				$eurl = utf8_encode("http://$host/event_details.php?event_id=$ev_detail_id%26title=$ev_detail_title%26date=$ev_detail_date%26rp_id=$eid");
				$egurl = str_replace('%20','%2B',$eurl);
				?>
				
				</li></ul>
				<!-- Added by yogi for Facebook Share feature End -->
				<!-- #DD# -->

		<div style='float:left;padding:3px 3px 3px 8px;'>
		<a expr:share_url='data:post.url' href='http://www.facebook.com/sharer.php?u=<?php echo $eurl ?>' name='fb_share' type='box_count'>
				<div class="facebook">
				</div>
			</a>
			<!--
		<script src='http://static.ak.fbcdn.net/connect.php/js/FB.Share' type='text/javascript'>
	</script>
	-->
		</div>
<div style='float:left;padding:3px 3px 3px 8px;'>
<a href="https://plus.google.com/share?url=<?php echo $egurl ?>" onclick="javascript:window.open(this.href,'','menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;">
<div class="google">
</div>
</a>
</div>
</div>


<!-- #DD# -->
<!--
<div id="footer">
&copy; <?=date('Y');?> <?=$site_name?>, Inc.
<!-- |
<a href="mailto:<?=$email?>?subject=App Feedback">
Contact Us
</a>
</div>
-->