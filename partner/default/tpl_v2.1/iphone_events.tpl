<div id="featuredEvents">
	<div class="flexslider-container">
		<div class="flexslider">
		    <ul class="slides">
			<?php 
			
			$datacount = 0 ;
			$f=0;
			$imagecount = 0;
			
			while($row=mysql_fetch_array($rec)){

			$ev=mysql_query("select *  from jos_jevents_vevent where ev_id=".$row['eventid']) or die(mysql_error());
			$evDetails=mysql_fetch_array($ev);
			/*Edited By Akash*/
			/*To fetch category name of the event*/
			$event_category=mysql_query("select title  from jos_categories where id=".$evDetails['catid']) or die(mysql_error());
			$ev_cat=mysql_fetch_object($event_category);
			$categoryname[] = $ev_cat->title;
			
			/*To fetch Featured Event detail*/
			$featuredevedetail = mysql_query("select detail_id from jos_jevents_vevent where ev_id=".$row['eventid']) or die(mysql_error());
			$featured_evedetail=mysql_fetch_array($featuredevedetail);
			
			/*TO check Weather Event is featured or not(value = 0/1)*/
			$featuredeve = mysql_query("select value from jos_jev_customfields where evdet_id=".$featured_evedetail['detail_id']) or die(mysql_error());
			$featured_eve=mysql_fetch_object($featuredeve);
			$featureevent[] = $featured_eve->value;
			/*Edited By Akash End*/
			$queryvevdetail="select *  from jos_jevents_vevdetail where evdet_id=".$evDetails['detail_id'];
			
			$recvevdetail=mysql_query($queryvevdetail) or die(mysql_error());
			
			$rowvevdetail=mysql_fetch_array($recvevdetail);
			
			$bottomdata[] = $rowvevdetail;
			
			//Creating Image array from Event description
			##Image FEtched for slide show##
				$imageurl= strstr($rowvevdetail['description'],'http');
				$singleimagearray = explode('"',$imageurl);
			##end##
			
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
			<!--Edited By Akash-->
			<!--This code is for slider part-->
			<?php
			 if($featureevent[$f] == 1 && $imagecount<5){?> 
		    	<li>
					<img style="height:300px;" src="<?=$singleimagearray[0]?>" />
		    		<div class="flex-caption">
		    			<h1><?=$rowvevdetail['summary']?></h1>
		    			<h2><?=$rowlocdetail['title']?></h2>
		    			<h3><?=$displayTime?></h3>
		    		</div> <!-- caption -->
		    	</li>
				
			<?php ++$imagecount;/*5 featured event counter */
			 }?>
      <?php
	  ++$datacount;
	  ++$f;
	  }  
	  ?>	<!--Edited By Akash End-->			
		    </ul>
		</div>
	</div>
</div> <!-- featured events -->
<div class="section">

	<form name='events' id='events' action='events.php' method='post'>
		<input type="text" value="" class="mobiscroll ui-input-text ui-body-null ui-corner-all ui-shadow-inset ui-body-d scroller" id="date1" name="eventdate" style="width:0px;height:0px;border:0px;background:#333333;position: absolute;top: -100px;">
		<button data-theme="a" id="show" class="ui-btn-hidden button" aria-disabled="false" style="width:100%;">Check Events By Day</button>
	</form>
	
</div>

<div id="main" role="main">

<h1><?=$todaestring?></h1>

<ul id="eventList" class="mainList" ontouchstart="touchStart(event,'eventList');" ontouchend="touchEnd(event);" ontouchmove="touchMove(event);" ontouchcancel="touchCancel(event);">

<?php
for($i=0;$i<count($bottomdata);$i++){?>
	<li>
		<h1><?php echo $bottomdata[$i]['summary']; ?></h1>
		<h2><?php echo $bottomloc[$i]['title']?></h2>
		<h3>
		<? echo $bottomdata[$i]['displaytime'] ?> &bull;
		<?php echo $categoryname[$i]; ?> &bull;
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