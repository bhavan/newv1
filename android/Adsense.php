<!-- eg: Adsense.html?cat='iPhone-Events'-->
<?php
include("connection.php");

if ($_REQUEST['cat']!="")
	$cat=$_REQUEST['cat'];
else
	$cat='main';

function m_show_banner($cat) {
   $sql = "select b.* from `jos_banner` b, `jos_categories` c where c.title = '".$cat."' and c.id = b.catid and b.showBanner = 1 ORDER BY RAND() LIMIT 0,1";
  $rec = mysql_query($sql) or die(mysql_error());
 $d=mysql_fetch_array($rec);
   if ($d['custombannercode'] != "")
    echo $d['custombannercode'];
  else
    echo '<a href="/adsclick.php?option=com_banners&task=click&bid='.$d['bid'].'" target="_blank"><img src="/partner/'.$_SESSION["partner_folder_name"].'/images/banners/'.$d['imageurl'].'" alt="'.$d['name'].'" title="'.$d['name'].'" /></a>';

	if($d['bid'])
	{
		// for Impressions: track the number of times the banner is displayed to web site visitors.
		$sql = 'UPDATE jos_banner SET impmade = impmade + 1 WHERE bid =' .$d['bid'];
		$result = mysql_query($sql);
	}
}
?>
<html>

<head>
	<!-- PUT THIS TAG IN THE head SECTION -->
	<title>Google Ad</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
	
	<style>
		body {
			background-color: #CCC;
			margin: 0px;
		}
	</style>
	<!-- END OF TAG FOR head SECTION -->
</head>

<body>

	<div style="height:0px; width:320px;">
    <?php m_show_banner($cat); ?>
    </div>


</body>

</html>