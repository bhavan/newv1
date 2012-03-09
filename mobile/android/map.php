<?php
if (isset($_REQUEST['lat']))
$lat=$_REQUEST['lat'];
else
$lat=30.393534;
if (isset($_REQUEST['long']))
$long=$_REQUEST['long'];
else
$long=-86.495783;
?>
<html> 
<head> 
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" /> 
<meta http-equiv="content-type" content="text/html; charset=UTF-8"/> 
<title><?=$site_name?></title> 
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script> 
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">
  function initialize() {
    var myOptions = {
      zoom: 16,
      center: new google.maps.LatLng(<?=$lat?>, <?=$long?>),
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    var map = new google.maps.Map(document.getElementById("map_canvas"),
                                  myOptions);

    var image = 'images/beachflag.png';
    var myLatLng = new google.maps.LatLng(<?=$lat?>, <?=$long?>);
    var beachMarker = new google.maps.Marker({
        position: myLatLng,
        map: map,
        icon: image
    });
  }
</script>
</head> 
<body style="margin:0px; padding:0px;" onLoad="initialize()"> 
  <div id="map_canvas" style="width:100%; height:100%"></div> 
</body> 
</html> 