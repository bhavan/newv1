<?php
include("connection.php");
function distance($lat1, $lon1, $lat2, $lon2, $unit) { 

$theta = $lon1 - $lon2; 
$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)); 
$dist = acos($dist); 
$dist = rad2deg($dist); 
$miles = $dist * 60 * 1.1515;
$unit = strtoupper($unit);

	if($unit == "KMS") {
		return ($miles * 1.609344); 
	}else if($unit == "N"){
		return ($miles * 0.8684);
	}else{
		return $miles;
		}
}

$eid=$_REQUEST['eid'];

if ($_REQUEST['lat']!="")
	$lat1=$_REQUEST['lat'];
else
	$lat1=0;

if ($_REQUEST['lon']!="")
	$lon1=$_REQUEST['lon'];
else
	$lon1=0;

if ($_REQUEST['d']=="")
	$today=date('d');
else
	$today=$_REQUEST['d'];

if ($_REQUEST['m']=="")
	$tomonth=date('m');
else
	$tomonth=$_REQUEST['m'];

if ($_REQUEST['Y']=="")
	$toyear=date('Y');
else
	$toyear=$_REQUEST['Y'];

$todaestring=date('D, M j', mktime(0, 0, 0, $tomonth, $today, $toyear));

$query="select *,DATE_FORMAT(`startrepeat`,'%h:%i %p') as timestart, DATE_FORMAT(`endrepeat`,'%h:%i%p') as timeend from jos_jevents_repetition where rp_id=$eid";
$rec=mysql_query($query) or die(mysql_error());
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta property="og:image" content="http://<?php echo $_SERVER['HTTP_HOST']?>/partner/<?php echo $_SESSION['partner_folder_name']?>/images/logo/logo.png"/>
		<meta content="yes" name="apple-mobile-web-app-capable" />
		<meta content="index,follow" name="robots" />
		<meta content="text/html; charset=iso-8859-1" http-equiv="Content-Type" />
		<link href="pics/homescreen.gif" rel="apple-touch-icon" />
		<meta content="minimum-scale=1.0, width=device-width, maximum-scale=0.6667, user-scalable=no" name="viewport" />
		<!--<link href="css/style.css" rel="stylesheet" media="screen" type="text/css" />-->
		<link href="/components/com_shines/css/style_new_24oct2011.css" rel="stylesheet" media="screen" type="text/css" />
		<script src="/components/com_shines/javascript/functions.js" type="text/javascript"></script>
		
		<script language="javascript">
			function linkClicked(link) { document.location = link; } 
		</script>
		
		<title><?=$site_name?></title>
		
		<!--<link href="pics/startup.png" rel="apple-touch-startup-image" /> -->
		<meta content="destin, vacactions in destin florida, destin, florida, real estate, sandestin resort, beaches, destin fl, maps of florida, hotels, hotels in florida, destin fishing, destin hotels, best florida beaches, florida beach house rentals, destin vacation rentals for destin, destin real estate, best beaches in florida, condo rentals in destin, vacaction rentals, fort walton beach, destin fishing, fl hotels, destin restaurants, florida beach hotels, hotels in destin, beaches in florida, destin, destin fl" name="keywords" />
		<meta content="Destin Florida's FREE iPhone application and website guide to local events, live music, restaurants and attractions" name="description" />
		
		<?php include($_SERVER['DOCUMENT_ROOT']."/ga.php"); ?>
	</head>
	
	<body>
	<!--Google Adsense -->
	<?php
		/* Code added for iphone_places.tpl */
		require($_SERVER['DOCUMENT_ROOT']."/partner/".$_SESSION['tpl_folder_name']."/tpl/iphone_events_details.tpl");
		?>
	</body>
</html>