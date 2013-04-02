<div id="featuredEvents">
	<div class="flexslider-container">
		<div class="flexslider">
		    <ul class="slides">
			<?php
			
			$f=0;
			$imagecount = 0;
			$tempeventid;
			while($fearow=mysql_fetch_array($fea_rec)){
			
			$feaev=mysql_query("select *  from jos_jevents_vevent where ev_id=".$fearow['eventid']) or die(mysql_error());
			$feaevDetails=mysql_fetch_array($feaev);
			
			$featuredevedetail = mysql_query("select detail_id from jos_jevents_vevent where ev_id=".$fearow['eventid']) or die(mysql_error());
			$featured_evedetail=mysql_fetch_array($featuredevedetail);
						
			/*TO check Weather Event is featured or not(value = 0/1)*/
			$featuredeve = mysql_query("select value from jos_jev_customfields where evdet_id=".$featured_evedetail['detail_id']) or die(mysql_error());
			$featured_eve=mysql_fetch_object($featuredeve);
			$featureevent[] = $featured_eve->value;
			/*Edited By Akash End*/
			
			$feaevdetail="select *  from jos_jevents_vevdetail where evdet_id=".$feaevDetails['detail_id'];
			$recfeaevdetail=mysql_query($feaevdetail) or die(mysql_error());
			$featureevdetail=mysql_fetch_array($recfeaevdetail);		

			//Creating Image array from Event description
			##Image FEtched for slide show##
				$imageurl= strstr($featureevdetail['description'],'http');
				$singleimagearray = explode('"',$imageurl);
				if($singleimagearray[0] == ""){
					$singleimagearray[0] = "/components/com_shines_v2.1/images/nofe_image.png"; }
			##end##

			if ((int) ($featureevdetail['location'])){
				$fealocdetail="select *  from jos_jev_locations where loc_id=".$featureevdetail['location'];
				$recfealocdetail=mysql_query($fealocdetail) or die(mysql_error());
				$rowfealocdetail=mysql_fetch_array($recfealocdetail);
				$lat2=$rowfealocdetail[geolat];
				$lon2=$rowfealocdetail[geolon];
			}
			$displayTime = '';

			if($fearow[timestart]=='12:00 AM' && $fearow[timeend]=='11:59PM')
            {    $displayTime.='All Day Event';}
			else{
				$displayTime.= ltrim($fearow[timestart], "0");
				
				if($featureevdetail['noendtime']==0){
					$displayTime.='-'.ltrim($fearow[timeend], "0");
				}
			}			
			?>
			<!--Edited By Akash-->
			<!--This code is for slider part-->
			<?php
			if(in_array($fearow['eventid'], $tempeventid)){
			}else{
			if($featureevent[$f] == 1 && $imagecount<5){?> 
		    	<li>
					<img style="height:200px;" src="<?php echo $singleimagearray[0]?>" />
		    		<div class="flex-caption">
		    			<h1><?php echo $featureevdetail['summary']?></h1>
		    			<h2><?php echo $rowfealocdetail['title']?></h2>
		    			<h3><?php echo $displayTime?></h3>
		    		</div> <!-- caption -->
		    	</li>
			<?php
			++$imagecount;/*5 featured event counter */
			$tempeventid[] = $fearow['eventid'];
			}}
			?>
			<?php
			++$datacount;
			++$f;
			}
			?>
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

<h1><?php echo $todaestring?></h1>

<ul id="eventList" class="mainList" ontouchstart="touchStart(event,'eventList');" ontouchend="touchEnd(event);" ontouchmove="touchMove(event);" ontouchcancel="touchCancel(event);">

			<?php 
			$n = 0;
			while($row=mysql_fetch_array($rec)){
			//#DD#
			$ev=mysql_query("select *  from jos_jevents_vevent where ev_id=".$row['eventid']) or die(mysql_error());
			$evDetails=mysql_fetch_array($ev);
			$evrawdata = unserialize($evDetails['rawdata']);
			
			/*Edited By Akash*/
			/*To fetch category name of the event*/
			$event_category=mysql_query("select title  from jos_categories where id=".$evDetails['catid']) or die(mysql_error());
			$ev_cat=mysql_fetch_object($event_category);
			$categoryname[] = $ev_cat->title;
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
			if($row[timestart]=='12:00 AM' && $row[timeend]=='11:59PM')
            {    $displayTime.='All Day Event';}
			else{
				$displayTime.= ltrim($row[timestart], "0");
				
				if($rowvevdetail['noendtime']==0){
					$displayTime.='-'.ltrim($row[timeend], "0");
				}
			}

			#DD#

	  ?>

	  
	<li>
		<h1><?php echo $rowvevdetail['summary']?></h1>
      	<h2><?php echo $rowlocdetail['title']?></h2>
		<h3>
			<?php echo $displayTime?> &bull;
			<?php echo $categoryname[$n]; ?>
			<ul class="btnList"><li><a class="button small" href="tel:<?php echo str_replace(array(' ','(',')','-','.'), '',$rowlocdetail['phone'])?>">call</a</li>
				
			<?php
	 			$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
				if(stripos($ua,'android') == true) { ?>
 			<?php } else { ?>
			<li><a class="button small" href="javascript:linkClicked('APP30A:FBCHECKIN:<?php echo $lat2; ?>:<?php echo $lon2; ?>')">check in</a></li>
				<?php } ?>
			<li><a class="button small" href="events_details.php?eid=<?php echo $row['rp_id']?>&d=<?php echo $today?>&m=<?php echo $tomonth?>&Y=<?php echo $toyear?>&lat=<?php echo $lat1?>&lon=<?php echo $lon1?>">more info</a></li></ul>
		</h3> 
				<!--<?=round(distance($_SESSION['lat_device1'], $_SESSION['lon_device1'], $lat2, $lon2,$dunit),'1')?>&nbsp;<?=$dunit?></td> Away -->
    </li>

      <?php
	  $rowlocdetail['title']="";
	  ++$n;
	  }  
	  ?>
</ul>
</div>

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