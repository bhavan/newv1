<?php 
setlocale(LC_TIME,"spanish");
$todaestring=ucwords(strftime ('%a, %b %d',mktime(0, 0, 0, $tomonth, $today, $toyear)));
?>
<div id="menu">

<h1>Info del Evento</h1>

<a id="calPrev" href="events.php?d=<?=$today?>&m=<?=$tomonth?>&Y=<?=$toyear?>&lat=<?=$lat1?>&lon=<?=$lon1?>" ></a>

</div>



<div id="list" ontouchstart="touchStart(event,'list');" ontouchend="touchEnd(event);" ontouchmove="touchMove(event);" ontouchcancel="touchCancel(event);">

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

		<div style="padding-top: 5px;font-size:15px;"><strong><?=$rowvevdetail['summary']?></strong></div>

      <br />

      <div style="width:100%;text-align: left;"><div class="gray" style="width:10%;float:left;padding-right:50px;">Fecha:</div><div style="width:100%"><?=$todaestring?></div></div><br />

     <div style="width:100%;text-align: left;"><div class="gray" style="width:10%;float:left;padding-right:50px;">Hora:</div><div style="width:100%">

			<?php

				//#DD#

				if($evrawdata['allDayEvent']=='on'){

					echo 'All Day Event';

				}else{

						$displayTime.= ltrim($row[timestart], "0");

						if($evrawdata['NOENDTIME']!=1){

						$displayTime.='-'.ltrim($row[timeend], "0");

						}

						echo $displayTime;

				}

				//#DD#

			?>      	

     </div></div><br />

     <div style="width:100%;text-align: left;"><div class="gray" style="width:10%;float:left;padding-right:50px;">Ubicaci&#243;n:</div><div style="width:100%"><?=$rowlocdetail['title']?></div></div><br />

	<?php
	 			$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
				if(stripos($ua,'android') == true) { ?>
	<div style="width:100%;text-align: left;"><div class="gray" style="width:10%;float:left;padding-right:50px;">Direcci&#243;n:</div><div style="width:100%"><a href="map.php?lat=<?=$lat2?>&long=<?=$lon2?>"><?=$rowlocdetail['street']?></a></div></div><br />
 	<?php } else { ?>
      <div style="width:100%;text-align: left;"><div class="gray" style="width:10%;float:left;padding-right:50px;">Direcci&#243;n:</div><div style="width:100%"><a href="javascript:linkClicked('APP30A:SHOWMAP:<?=$lat2?>:<?=$lon2?>')" ><?=$rowlocdetail['street']?></a></div></div><br />
	 <?php } ?>
	 
     <div style="width:100%;text-align: left;"><div class="gray" style="width:10%;float:left;padding-right:50px;">Tel&#233;fono:</div><div style="width:100%"><a href="tel:<?php echo str_replace(array(' ','(',')','-','.'), '',$rowlocdetail['phone'])?>"><?=$rowlocdetail['phone']?></a></div></div><br />

     <div style="width:100%;text-align: left;"><div class="gray" style="width:10%;float:left;padding-right:50px;">Distancia:</div><div style="width:100%"> <?=round(distance($lat1, $lon1, $lat2, $lon2,$dunit),'1')?>&nbsp;<?=$dunit?></div></div><br />

	<?php if(trim($rowlocdetail['url']) != '') { ?>

     <div style="width:100%;text-align: left;"><div class="gray" style="width:10%;float:left;padding-right:50px;">Sitio Web:</div><div style="width:100%"><a href="http://<?php echo str_replace('http://','',$rowlocdetail['url']); ?>" target="_blank"><?php echo str_replace('http://','',$rowlocdetail['url']); ?></a></div></div><br />

	<?php } ?>

     <div style="width:100%;text-align: left;"><div class="gray" style="width:10%;float:left;padding-right:50px;">Descripci&#243;n:</div><div style="width:100%;text-align: justify;"><?=$rowvevdetail['description']?></div></div><br />

      

      

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

		$mailContent = str_replace('<br/>',"%0D%0A", $mailContent);

		$mailContent = str_replace('<br>',"%0D%0A", $mailContent);

		$mailContent = str_replace('<br />',"%0D%0A", $mailContent);

		$mailContent = str_replace('"','\"', $mailContent);

		$mailContent = strip_tags($mailContent);

		//#DD#



	  }

	  ?>



</div>


		
<!-- <div id="footer">&copy; <?=date('Y');?> <?=$site_name?>, Inc.<!-- | <a href="mailto:<?=$email?>?subject=App Feedback">Contacte con nosotros</a></div> -->
