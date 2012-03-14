<h1>
	<a href="index.php" title="HOME"> <img src="./partner/<?php echo $_SESSION['partner_folder_name']?>/images/logo/logo.png" height="150" width="245" /> </a>
</h1>
<div id="banner">
<?php m_show_banner('Website Top 468x60'); ?>
</div> <!-- banner -->
<nav class="spanish_nav">
  <ul>
    <li><a href="/">HELLO</a></li>
    <li><a href="events.php">EVENTOS</a></li>
    <li><a href="restaurants.php">RESTAURANTES</a></li>
    <li><a href="locations.php">LUGARES</a></li>
    <li><a href="photo_albums.php">FOTOS</a></li>
    <!--li><a href="visiting.php">Visiting</a></li-->
    <li><a href="videos.php">VIDEOS</a></li>
    <!--<li><a href="contact_us.php">Contact</a></li>-->
  </ul>
</nav>
<?php require ("./inc/config.php"); 
$handle = fopen($query, "r");
$xml = '';
while (!feof($handle)) {
  $xml.= fread($handle, 8192);
}
fclose($handle);
$data = XML_unserialize($xml);
?>
<div id="weather">
  <?php
echo str_replace('N/A','--',$data[weather][cc][tmp]) . "&#176; ";
echo " <a href='http://www.weather.com/weather/today/$var->location_code' target='_blank'><IMG SRC='common/images/weather/" . $data[weather][cc][icon] . ".png' height='27' border='0'></a>";
?>
</div> <!-- weather -->
<a id="facebook" href="<?php echo $var->facebook?>" target="_blank">Become a fan</a>
<!--<a id="download" href="<?php echo $var->iphone?>" target="_blank">Download our FREE iPhone App!</a> -->

<div id="download" class="spanish_icon">
		<p>Descarga nuestra aplicación móvil GRATIS!</p>
		<a href="<?php echo $var->iphone?>" target="_blank" id="iPhone" title="Descarga nuestra aplicación móvil GRATIS!">iPhone</a>
		<a href="<?php echo $var->android?>" target="_blank" id="android" title="Download our FREE Android app">Android</a>
</div>
