    <div id="featuredEvents">
	    <div class="flexslider-container">
			<div class="flexslider">
		    <ul class="1slides">
			<?php 
			
			$datacount = 0 ;
			//$imagecounter = 0;
			
			while($row=mysql_fetch_array($rec)){

			$ev=mysql_query("select *  from jos_jevents_vevent where ev_id=".$row['eventid']) or die(mysql_error());
			$evDetails=mysql_fetch_array($ev);
	
			$event_category=mysql_query("select title  from jos_categories where id=".$evDetails['catid']) or die(mysql_error());
			$ev_cat=mysql_fetch_array($event_category);
			
			$queryvevdetail="select *  from jos_jevents_vevdetail where evdet_id=".$evDetails['detail_id'];
			
			$recvevdetail=mysql_query($queryvevdetail) or die(mysql_error());
			
			$rowvevdetail=mysql_fetch_array($recvevdetail);
			
			$bottomdata[] = $rowvevdetail;
			
			//Creating Image array from Event description
			$imgArray = explode('src="',$rowvevdetail['description']);		
			$evImageArray = array();
			$singleImage = array();
			$FirstImage = array();
			for($i=0;$i<count($imgArray);$i++){
				if(strstr($imgArray[$i],'" />',true) != ''){
					if($singleImage == '')
						$singleImage 	= strstr($imgArray[$i],'" />',true); // As of PHP 5.3.0
					else
						$evImageArray[] = strstr($imgArray[$i],'" />',true); // As of PHP 5.3.0	
						
				}	
			}
			if ((int) ($rowvevdetail['location'])){
				$querylocdetail="select *  from jos_jev_locations where loc_id=".$rowvevdetail['location'];
				$reclocdetail=mysql_query($querylocdetail) or die(mysql_error());
				$rowlocdetail=mysql_fetch_array($reclocdetail);
				$lat2=$rowlocdetail[geolat];
				$lon2=$rowlocdetail[geolon];
			}
			$bottomloc[]=$rowlocdetail;
			$displayTime = '';
			/* Coded By Rinkal */
			
			if($row[timestart]=='12:00 AM' && $row[timeend]=='11:59PM')
            {    $displayTime.='All Day Event';}
			else{
				$displayTime.= ltrim($row[timestart], "0");
				
				if($rowvevdetail['noendtime']==0){
					$displayTime.='-'.ltrim($row[timeend], "0");
				}
			}	
			$bottomdata[$datacount]['displaytime'] = $displayTime ;
			$Event_rpid[] = $row['rp_id'] ;
			?>		
		    	<li>
		    		<img src="<?=$singleImage[$i]?>" />
					<!--<img src="<?php $singleImage[0];?>" />-->
		    		<div class="flex-caption">
		    			<h1><?=$rowvevdetail['summary']?></h1>
		    			<h2><?=$rowlocdetail['title']?></h2>
		    			<h3><?=$displayTime?></h3>
		    		</div> <!-- caption -->
		    	</li>

      <?php
	  //++$imagecounter;
	  ++$datacount;
	  }  
	  ?>				
		    </ul>
		  </div>
	 	</div>
	</div> <!-- featured events -->
<div class="section">
		<div class="fLeft">
			<h2>View Calendar for:</h2>
			<a href="" class="button">Check Event by Day</a>
		</div>
		<div class="fRight">
			<h2>Types of Events:</h2>
			<a href="" class="button">All Events</a>
		</div>
	</div>
	<form name='events' id='events' action='events.php' method='post'>
		<input type="text" value="" class="mobiscroll ui-input-text ui-body-null ui-corner-all ui-shadow-inset ui-body-d scroller" id="date1" name="eventdate" style="width:0px;height:0px;border:0px;background:#333333;    position: absolute;top: -100px;">
		<button data-theme="a" id="show" class="ui-btn-hidden" aria-disabled="false"></button>
	</form>
<div id="main" role="main">

	<h1><?=$todaestring?></h1>

<ul id="eventList" class="mainList" ontouchstart="touchStart(event,'eventList');" ontouchend="touchEnd(event);" ontouchmove="touchMove(event);" ontouchcancel="touchCancel(event);">

<?php
		// echo "<pre>";
		//print_r($bottomdata);
//	echo count($bottmdata);
	
for($i=0;$i<count($bottomdata);$i++){?>
		<li>
      			<h1><?php echo $bottomdata[$i]['summary']; ?></h1>
      			<h2><?php echo $bottomloc[$i]['title']?></h2>
				<h3>
				<? echo $bottomdata[$i]['displaytime'] ?> &bull;
				<?=$ev_cat['title']?> &bull;
				<a class="call" href="tel:<?php echo str_replace(array(' ','(',')','-','.'), '',$rowlocdetail['phone'])?>">Call</a> &bull;
				<?php
				$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
			 	if(stripos($ua,'android') == true) { ?>
 				<?php } else { ?>
				<a class="checkin" href="javascript:linkClicked('APP30A:FBCHECKIN:<?php echo $lat2; ?>:<?php echo $lon2; ?>')">Check in</a> &bull;
				<?php } ?>
				<a class="info" href="events_details.php?eid=<?=$Event_rpid[$i]?>&d=<?=$today?>&m=<?=$tomonth?>&Y=<?=$toyear?>&lat=<?=$lat1?>&lon=<?=$lon1?>">More info</a>
				</h3> 
      		
			<!--<?=round(distance($_SESSION['lat_device1'], $_SESSION['lon_device1'], $lat2, $lon2,$dunit),'1')?>&nbsp;<?=$dunit?> Away-->
      </li>
<?php } ?>
</ul>	

	</div> <!-- menu -->
<!-- <div id="footer">&copy; <?=date('Y');?> <?=$site_name?>, Inc. <!-- | <a href="mailto:<?=$email?>?subject=App Feedback">Contact Us</a> </div>  -->
<div style='display:none;'><?php echo $pageglobal['googgle_map_api_keys']; ?></div>


  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
  <script>window.jQuery || document.write('<script src="js/libs/jquery-1.7.1.min.js"><\/script>')</script>

<!-- scripts for sliders -->
	<script type="text/javascript" src="/components/com_shines_v2.1/javascript/sliders.js"></script>
	<script type="text/javascript">
		$(window).load(function() {
			$('.flexslider').flexslider({
			  animation: "slide",
			  directionNav: false,
			  controlsContainer: ".flexslider-container"
		  });
		});
	</script>
	<script src="js/helper.js"></script>