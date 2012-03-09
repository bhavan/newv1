<?php

require('jevents.php');
global $var;
include_once('./inc/var.php');
include_once($var->inc_path.'base.php');
_init();


?>

<!DOCTYPE HTML>
<html>
<!-- Envolve -->

<head>
<title><?php echo $var->site_name.' | '.$var->page_title; ?></title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="keywords" content="<?php echo $var->keywords; ?>" />
<meta name="description" content="<?php echo $var->metadesc; ?>" />
<meta name="description" content="<?php echo $var->extra_meta; ?>" />
<style type="text/css">
div.mapinfo
{
	text-align:left;
	width:200px;
	height:100px;
}
</style>
<script>
  document.createElement('header');
  document.createElement('nav');
  document.createElement('section');
  document.createElement('article');
  document.createElement('aside');
  document.createElement('footer');
</script>
<link rel="stylesheet" type="text/css" href="common/css/all.css" media="screen" />
<link rel="stylesheet" type="text/css" href="common/css/jquery-ui.css" media="screen" />
<script type="text/javascript" src="common/js/jquery.min.js"></script>
<script type="text/javascript" src="common/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="common/js/default.js"></script>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script> 
<script type="text/javascript" src="scripts/downloadxml.js"></script>
<script type="text/javascript">
    //<![CDATA[
      // this variable will collect the html which will eventually be placed in the side_bar 
      var side_bar_html = ""; 

      var gmarkers = [];
      var gicons = [];
      var map = null;

var infowindow = new google.maps.InfoWindow(
  { 
    size: new google.maps.Size(300,200)
  });


gicons["red"] = new google.maps.MarkerImage("http://www.google.com/intl/en_us/mapfiles/ms/micons/red.png",
      // This marker is 20 pixels wide by 34 pixels tall.
      new google.maps.Size(32, 34),
      // The origin for this image is 0,0.
      new google.maps.Point(0,0),
      // The anchor for this image is at 9,34.
      new google.maps.Point(9, 34));
  // Marker sizes are expressed as a Size of X,Y
  // where the origin of the image (0,0) is located
  // in the top left of the image.
 
  // Origins, anchor positions and coordinates of the marker
  // increase in the X direction to the right and in
  // the Y direction down.

  var iconImage = new google.maps.MarkerImage('http://www.google.com/intl/en_us/mapfiles/ms/micons/red.png',
      // This marker is 20 pixels wide by 34 pixels tall.
      new google.maps.Size(32, 34),
      // The origin for this image is 0,0.
      new google.maps.Point(0,0),
      // The anchor for this image is at 9,34.
      new google.maps.Point(9, 34));
  var iconShadow = new google.maps.MarkerImage('http://www.google.com/mapfiles/shadow50.png',
      // The shadow image is larger in the horizontal dimension
      // while the position and offset are the same as for the main image.
      new google.maps.Size(37, 34),
      new google.maps.Point(0,0),
      new google.maps.Point(9, 34));
      // Shapes define the clickable region of the icon.
      // The type defines an HTML &lt;area&gt; element 'poly' which
      // traces out a polygon as a series of X,Y points. The final
      // coordinate closes the poly by connecting to the first
      // coordinate.
  var iconShape = {
      coord: [9,0,6,1,4,2,2,4,0,8,0,12,1,14,2,16,5,19,7,23,8,26,9,30,9,34,11,34,11,30,12,26,13,24,14,21,16,18,18,16,20,12,20,8,18,4,16,2,15,1,13,0],
      type: 'poly'
  };

function getMarkerImage(iconColor) {
   if ((typeof(iconColor)=="undefined") || (iconColor==null)) { 
      iconColor = "red"; 
   }
   if (!gicons[iconColor]) {
      gicons[iconColor] = new google.maps.MarkerImage("http://www.google.com/intl/en_us/mapfiles/ms/micons/"+ iconColor +".png",
      // This marker is 20 pixels wide by 34 pixels tall.
      new google.maps.Size(32, 34),
      // The origin for this image is 0,0.
      new google.maps.Point(0,0),
      // The anchor for this image is at 6,20.
      new google.maps.Point(9, 34));
   } 
   return gicons[iconColor];

}

function category2color(category) {
   var color = "red";
   switch(category) {
     case "40": color = "blue";
                break;
     case "39":    color = "green";
                break;
     case "41":    color = "yellow";
                break;
     default:   color = "red";
                break;
   }
   return color;
}

	<?php	
		//use joomla db class
		require_once("configuration.php");
		$jconfig = new JConfig();
		//db establish
		$db_error = "I am sorry! We are maintaining the website, please try again later.";
		$db_config = mysql_connect( $jconfig->host, $jconfig->user, $jconfig->password ) or die( $db_error );
		mysql_select_db( $jconfig->db, $db_config ) or die( $db_error );
		
		$query = "SELECT * FROM jos_jevents_markers";
		
		//get the sections
		$markerscolor = mysql_query($query);
		
		
		while($markicon = mysql_fetch_object($markerscolor))
		{	
			?>
			 	gicons["<?php echo $markicon->catid; ?>"] = getMarkerImage("<?php echo $markicon->colour; ?>");
			<?php
		}
	?>
     
      

      // A function to create the marker and set up the event window
function createMarker(latlng,name,html,category) {
    var contentString = html;
    var marker = new google.maps.Marker({
        position: latlng,
        icon: gicons[category],
        shadow: iconShadow,
        map: map,
        title: name,
        zIndex: Math.round(latlng.lat()*-100000)<<5
        });
        // === Store the category and name info as a marker properties ===
        marker.mycategory = category;                                 
        marker.myname = name;
        gmarkers.push(marker);

    google.maps.event.addListener(marker, 'click', function() {
        infowindow.setContent(contentString); 
        infowindow.open(map,marker);
        });
}

      // == shows all markers of a particular category, and ensures the checkbox is checked ==
      function show(category) {
	  	
        for (var i=0; i<gmarkers.length; i++) {
          if (gmarkers[i].mycategory == category) {
            gmarkers[i].setVisible(true);
          }
        }
        // == check the checkbox ==
        document.getElementById(category+"box").checked = true;
      }

      // == hides all markers of a particular category, and ensures the checkbox is cleared ==
      function hide(category) {
	  	
        for (var i=0; i<gmarkers.length; i++) {
          if (gmarkers[i].mycategory == category) {
            gmarkers[i].setVisible(false);
          }
        }
        // == clear the checkbox ==
        document.getElementById(category+"box").checked = false;
        // == close the info window, in case its open on a marker that we just hid
        infowindow.close();
      }

      // == a checkbox has been clicked ==
      function boxclick(box,category) {	  	
        if (box.checked) {
          show(category);
        } else {
          hide(category);
        }
        // == rebuild the side bar
        //makeSidebar();
      }

      function myclick(i) {
        google.maps.event.trigger(gmarkers[i],"click");
      }


      // == rebuilds the sidebar to match the markers currently displayed ==
     /* function makeSidebar() {
        var html = "";
        for (var i=0; i<gmarkers.length; i++) {
          if (gmarkers[i].getVisible()) {
            html += '<a href="javascript:myclick(' + i + ')">' + gmarkers[i].myname + '<\/a><br>';
          }
        }
        document.getElementById("side_bar").innerHTML = html;
      }*/

  function initialize() {
    var myOptions = {
      zoom: 8,
      center: new google.maps.LatLng(30.48173714,-86.41385651),
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    map = new google.maps.Map(document.getElementById("map"), myOptions);


    google.maps.event.addListener(map, 'click', function() {
        infowindow.close();
        });



      // Read the data
      downloadUrl("markers.php", function(doc) {
  var xml = xmlParse(doc);
  var markers = xml.documentElement.getElementsByTagName("marker");
          
        for (var i = 0; i < markers.length; i++) {
          // obtain the attribues of each marker
          var lat = parseFloat(markers[i].getAttribute("lat"));
          var lng = parseFloat(markers[i].getAttribute("lng"));
          var point = new google.maps.LatLng(lat,lng);
          var address = markers[i].getAttribute("address");
		  var locationlink = markers[i].getAttribute("locationlink");
		  var eventlink = markers[i].getAttribute("eventlink");
		  var image = markers[i].getAttribute("image");
          var name = markers[i].getAttribute("name");
          var html = "<div class='mapinfo'><b><a href="+locationlink+">"+name+"<\/a><\/b><br><a href="+eventlink+">"+address+"<\/a><br><img src="+image+"><\/div>";
          var category = markers[i].getAttribute("category");
          // create the marker
          var marker = createMarker(point,name,html,category);
        }

        // == show or hide the categories initially ==
		<?php
		//use joomla db class
		require_once("configuration.php");
		$jconfig = new JConfig();
		//db establish
		$db_error = "I am sorry! We are maintaining the website, please try again later.";
		$db_config = mysql_connect( $jconfig->host, $jconfig->user, $jconfig->password ) or die( $db_error );
		mysql_select_db( $jconfig->db, $db_config ) or die( $db_error );
		
				
		$query = "SELECT c.*, s.id as cat_id FROM jos_categories as c LEFT JOIN jos_jevents_categories as s ON s.id=c.id WHERE c.section='com_jevents' AND s.id<>'' AND c.published=1 ORDER BY s.id DESC";
		
		//get the sections
		$values = mysql_query($query);
		
		$checkbox = '';
		while($value = mysql_fetch_object($values))
		{	
			?>
			show("<?php echo $value->cat_id; ?>");
			<?php
		}
		?>
       		
        // == create the initial sidebar ==
        //makeSidebar();
      });
    }

    // This Javascript is based on code provided by the
    // Community Church Javascript Team
    // http://www.bisphamchurch.org.uk/   
    // http://econym.org.uk/gmap/
    // from the v2 tutorial page at:
    // http://econym.org.uk/gmap/example_categories.htm
    //]]>
    </script>
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
    <table width="100%">
   <tr>
   <td>
        <div id="map" style="width: 700px; height: 500px; margin:0px auto; text-align:center"></div>
	</td>
	</tr>
    <tr>
    <td align="center">	
    <form action="#">
      <?php
		
		
		$sql = "SELECT c.*, s.id as cat_id,m.colour as color FROM jos_categories as c LEFT JOIN jos_jevents_categories as s ON s.id=c.id LEFT JOIN jos_jevents_markers as m ON m.catid=c.id WHERE c.section='com_jevents' AND s.id<>'' AND c.published=1 ORDER BY s.id DESC";
		
		//get the sections
		$values = mysql_query($sql);
		
		$checkbox = '';
		while($value = mysql_fetch_object($values))
		{	
			$checkbox .= $value->title.': <img src="http://www.google.com/intl/en_us/mapfiles/ms/micons/'.$value->color.'.png"><input type="checkbox" class="event" name="events" value="on" id="'.$value->cat_id.'box" onclick="boxclick(this,'.$value->id.')" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';//echo $value->title."<br>";	
		}
		echo $checkbox;
?>
    </form>  
	</td>
	</tr>
	</table>



    <noscript><b>JavaScript must be enabled in order for you to use Google Maps.</b> 
      However, it seems JavaScript is either disabled or not supported by your browser. 
      To view Google Maps, enable JavaScript by changing your browser options, and then 
      try again.
    </noscript>
	</section> <!-- rightColumn -->
</div> <!-- wrapper -->
<footer>
	<?php m_footer(); ?> <!-- footer -->
</footer>

</body>
</html> 


