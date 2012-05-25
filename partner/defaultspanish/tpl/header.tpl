<h1>
	<a href="index.php" title="HOME"> <img src="./partner/<?php echo $_SESSION['partner_folder_name']?>/images/logo/logo.png" height="150" width="245" /> </a>
</h1>
<div id="banner">
<?php m_show_banner('Website Top 468x60'); ?>
</div> <!-- banner -->
<nav class="spanish_nav">
  <ul>
    <li><a href="/">INICIO</a></li>
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
// Applying condition for $handle variable to check weather it is null or not
if($handle != null || !empty($handle)){
	while (!feof($handle)) {
  		$xml.= fread($handle, 8192);
	}
}
fclose($handle);
$data = XML_unserialize($xml);
?>
<div id="weather">
  <?php
echo str_replace('N/A','--',$data[weather][cc][tmp]) . "&#176; ";
echo " <a href='http://www.weather.com/weather/today/$var->location_code' target='_blank'><IMG SRC='common/images/weather/" . $data[weather][cc][icon] . ".png' height='27' border='0'></a>";
?>
</div>   
<a id="facebook" href="<?php echo $var->facebook?>" target="_blank" class="facebook_sp">Become a fan</a>
<!--<a id="download" href="<?php echo $var->iphone?>" target="_blank">Download our FREE iPhone App!</a> -->

<div id="download" class="spanish_icon">
		<?php if($var->iphone != "" || $var->android != ""):?>
				<p>Descarga nuestra aplicación móvil GRATIS!</p>
		<?php endif;?>		
		<?php if($var->iphone && $var->iphone != ""):?>
				<a href="<?php echo $var->iphone?>" target="_blank" id="iPhone" title="Download our FREE iPhone app">iPhone</a>
		<?php endif;?>
		<?php if($var->android && $var->android != ""):?>
				<a href="<?php echo $var->android?>" target="_blank" id="android" title="Download our FREE Android app">Android</a>
		<?php endif;?>
</div>

<!--  Town wizard Google Analytic code -->
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-31932515-1']);
  _gaq.push(['_setDomainName', 'auto']);
  _gaq.push(['_trackPageview']);

  <?php if($var->googgle_analytics != null && !empty($var->googgle_analytics)): ?>
  _gaq.push(['t2._setAccount', '<?php echo $var->googgle_analytics;?>']);  
  _gaq.push(['t2._trackPageview']);
  <?php endif;?>
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>