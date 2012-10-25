<?php

require('jevents.php');
global $var;
include_once('./inc/var.php');
include_once($var->inc_path.'base.php');
_init();

//Fetching key from database
  $pageglobal = db_fetch("select * from `jos_pageglobal`");
  $pagemeta = db_fetch("select * from `jos_pagemeta` where `uri` = '".$var->request_uri."'");
  $pagejevent = db_fetch("select * from `jos_components` where `option`='com_jevlocations'");
 
  	$gmapkeys=explode('googlemapskey=',$pagejevent['params']);
  	$gmapkeys1=explode("\n",$gmapkeys[1]);  
  
  $data = db_fetch("select * from `jos_jev_locations` where `loc_id` = ".$var->get['id']);
  $data['q'] = str_replace(' ', '+', ($data['title'].' '.$data['street'].' '.$data['city'].' '.$data['state'].' '.$data['postcode']));
  	
	/*query to get lon and lat from database*/
	$queryx =  mysql_query("SELECT geolat FROM jos_jev_locations where loc_id = $data[loc_id]");	
	$queryy = mysql_query("SELECT geolon FROM jos_jev_locations where loc_id = $data[loc_id]");

	/*fetching x and y coordinate*/
	$x = mysql_result($queryx,0);/*Latitude*/	
  	$y = mysql_result($queryy,0);/*Longitude*/
  	
?>

<!DOCTYPE HTML>
<html>
<head>
<!-- <title><?php echo $var->site_name.' | '.$var->page_title; ?></title> -->

<title><?php echo $var->site_name.' | '.$var->page_title; ?> | <?php echo $data['title']; ?></title>
<link rel="image_src" href="http://<?php echo $_SERVER['HTTP_HOST']?>/partner/<?php echo $_SESSION['partner_folder_name']?>/images/logo/logo.png" />  
<meta property="og:image" content="http://<?php echo $_SERVER['HTTP_HOST']?>/partner/<?php echo $_SESSION['partner_folder_name']?>/images/logo/logo.png"/>
<meta property="og:title" content="<?php echo $var->site_name.' | '.$var->page_title; ?> | <?php echo $data['title']; ?>"/>
<meta property="og:description" content="Check out <?php echo $data['title'];?>.Check out more local favorites at <?php echo $_SERVER['SERVER_NAME'];?>."/>

<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<meta name="keywords" content="<?php echo $var->keywords; ?>" />
<meta name="description" content="<?php echo $var->metadesc; ?>" />
<meta name="description" content="<?php echo $var->extra_meta; ?>" />
<script>
  document.createElement('header');
  document.createElement('nav');
  document.createElement('section');
  document.createElement('article');
  document.createElement('aside');
  document.createElement('footer');
</script>

<!--MAP DATA 
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <style type="text/css">
      html { height: 100% }
      body { height:100%; margin: 0; padding: 0 }
      #map_canvas { height: 50%; }
    </style>-->


    <!--
    The URL contained in the script tag is the location of a JavaScript file that loads all of the symbols and definitions we need for using the Google
&nbsp;Maps API The sensor parameter of the URL must be included, and indicates whether this application uses a sensor (such as a GPS locator) to 
determine the user's locationKey   -->

    <script type="text/javascript"
      src="http://maps.googleapis.com/maps/api/js?<?php if ($gmapkeys1[0]!= ""){echo "key=".$gmapkeys1[0]."&";} ?>sensor=false">
    </script>
    
    <script type="text/javascript">
      function initialize() {
        var myOptions = {
          center:new google.maps.LatLng(<?php echo $x; ?>,<?php echo $y; ?>),
          zoom: 15,
		  <!--ROADMAP/SATELLITE/HYBRID/TERRAIN-->
          mapTypeId: google.maps.MapTypeId.ROADMAP 
        };
        var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
    	var marker = new google.maps.Marker({position:new google.maps.LatLng(<?php echo $x; ?>,<?php echo $y; ?>),map:map})
      }
    </script>

<!--MAP DATA -->

<link rel="stylesheet" type="text/css" href="common/templatecolor/<?php echo $_SESSION['style_folder_name'];?>/css/all.css" media="screen" />
<!--[if IE 7]><link rel="stylesheet" type="text/css" href="common/templatecolor/<?php echo $_SESSION['style_folder_name'];?>/css/ie7.css" media="screen" /><![endif]-->
<?php include("ga.php"); ?>
</head>

<body onLoad="initialize()">

<header>
	<?php m_header(); ?> <!-- header -->
</header>
<div id="wrapper">
	<aside>
    <?php m_aside(); ?>
	</aside> <!-- left Column -->
	<section>
		    
   <?php
	/* Code added for location_details.tpl */
	require($var->tpl_path."location_details.tpl");
	?>
	</section> <!-- rightColumn -->
</div> <!-- wrapper -->
<footer>
	<?php m_footer(); ?> <!-- footer -->
</footer>

</body>
</html>