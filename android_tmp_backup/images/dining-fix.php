<?php
include("connection.php");
include("class.paggination.php");
function distance($lat1, $lon1, $lat2, $lon2, $unit) { 

  $theta = $lon1 - $lon2; 
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)); 
  $dist = acos($dist); 
  $dist = rad2deg($dist); 
  $miles = $dist * 60 * 1.1515;
  $unit = strtoupper($unit);

  if ($unit == "K") {
    return ($miles * 1.609344); 
  } else if ($unit == "N") {
      return ($miles * 0.8684);
    } else {
        return $miles;
      }
}

function showBrief($str, $length) {
  $str = strip_tags($str);
  $str = explode(" ", $str);
  return implode(" " , array_slice($str, 0, $length));
}

if ($_REQUEST['lat']!="")
$lat1=$_REQUEST['lat'];
else
$lat1=30.393534;
if ($_REQUEST['lon']!="")
$lon1=$_REQUEST['lon'];
else
$lon1=-86.495783;

if ($_REQUEST['filter_loccat']!="")
	$loccat = $_REQUEST['filter_loccat'];
else
	$loccat = 0;

/*
global $var;
include_once('./inc/var.php');
include_once($var->inc_path.'base.php');
_init();
*/

$query= 'SELECT *,((ACOS(SIN('.$lat1.' * PI() / 180) * SIN(`geolat` * PI() / 180) + COS('.$lat1.' * PI() / 180) * COS(`geolat` * PI() / 180) * COS(('.$lon1.' - `geolon`) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance FROM jos_jev_locations WHERE published=1 AND global=1 ';

if(!empty($loccat))
{
	$query .= " AND loccat = $loccat ";
}
$query .= ' ORDER BY distance ASC ';

//$rec=mysql_query($query) or die(mysql_error());

 $mydb=new pagination(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
	$mydb->connection();

 $num_rec=25;

		 $mydb->set_qry($query);
		 $mydb->set_record_per_sheet($num_rec);
		 $num_pages=$mydb->num_pages();
		 if (isset($_REQUEST['start']))
	 	 $recno=$_REQUEST['start'];
		 else
	 	 $recno=0;
		 
		 $rec=$mydb->execute_query($recno);
		 $current_page=$mydb->current_page();
		 $start_page=$mydb->start_page();
		 $end_page=$mydb->end_page();
		 $photoindent=$recno-1;

function stripJunk($string) { 
$cleanedString = preg_replace("/[^A-Za-z0-9\s\.\-\/+\!;\n\t\r\(\)\'\"._\?>,~\*<}{\[\]\=\&\@\#\$\%\^` ]/","", $string); 
$cleanedString = preg_replace("/\s+/"," ",$cleanedString); 
return $cleanedString; 
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="yes" name="apple-mobile-web-app-capable" />
<meta content="index,follow" name="robots" />
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<link href="pics/homescreen.gif" rel="apple-touch-icon" />
<meta content="minimum-scale=1.0, width=device-width, maximum-scale=0.6667, user-scalable=no" name="viewport" />
<link href="css/style.css" rel="stylesheet" media="screen" type="text/css" />
<script src="javascript/functions.js" type="text/javascript"></script>
<title><?=$site_name?></title>
<!--<link href="pics/startup.png" rel="apple-touch-startup-image" /> -->
<meta name="description" content="<?php echo $var->metadesc; ?>" />
<meta name="description" content="<?php echo $var->extra_meta; ?>" />
</head>

<body>
<!--Google Adsense -->



<div id="topbar">
<div id="title">Places</div>
	<div id="leftnav">   
            <?php 
         if ($current_page!=0)
				  			{
							 $st1=($current_page*$num_rec)-$num_rec;	
						?>
    <a href="<?php echo $_SERVER['PHP_SELF']; ?>?start=<?=$st1?>&lat=<?=$lat1?>&lon=<?=$lon1?>&filter_loccat=<?=$loccat?>">Back</a>
    <?php }?>

        </div>
        
        
	<div id="rightnav">
		    <?php
					  if (($current_page+1)<$num_pages)
				 		 {
					  $st1=($current_page*$num_rec)+$num_rec;
					  ?>
    <a href="<?php echo $_SERVER['PHP_SELF']; ?>?start=<?=$st1?>&lat=<?=$lat1?>&lon=<?=$lon1?>&filter_loccat=<?=$loccat?>">More</a>
    <?php }?>
    
</div></div>


<div id="content">

	<script type="text/javascript">
		function redirecturl(val)
		{
			url="<?php echo $_SERVER['PHP_SELF']; ?>?lat=<?php echo $_REQUEST['lat'];?>&lon=<?php echo $_REQUEST['lon'];?>&filter_loccat="+val;
			window.location=url;
		}
	</script>
	
	<style>
		body { margin-top: 1px; margin-left: 0px; margin-right: 0px; font-family: Verdana, Geneva, sans-serif; }
		.bluetext { color: #0088BB; font-size: 13px; font-weight:bold; }
		.bluetextsmall { color: #00AADD; font-size: 13px; /*font-style: italic;*/}
		.headertext { color: #000000; font-size: 17px; }
		.graytext { color: #777777; font-size: 14px; }
		.graytextSmall { color: #777777; font-size: 13px; }
		.linktext { color: #0000ff; font-size: 14px; text-decoration: underline; } 
	</style>


		<ul class="pageitem">
			<li class="select">

			<?php
		$recsub=mysql_query("select * from jos_categories where section='com_jevlocations2' and published=1 order by `ordering`") or die(mysql_error());
			?>

			<select name="d" onChange="redirecturl(this.value)">
			<option value="0">Select a Category</option>
			<option value="0">All</option>
	  <?php
	  while($rowsub=mysql_fetch_array($recsub))
		{
		$querycount = "SELECT * FROM jos_jev_locations WHERE published=1 and loccat=".$rowsub['id'];
			$reccount=mysql_query($querycount) or die(mysql_error());	
		if (mysql_num_rows($reccount))
		{
	  ?>
	  <option value="<?=$rowsub['id'];?>" <?php if ($_REQUEST['filter_loccat']==$rowsub['id']) {?> selected <?php }?>><?=$rowsub['title'];?></option>

	 <?php
		}
		}
	 ?>
	 </select>
	<span class="arrow"></span> </li></ul>

	<ul class="pageitem">
      <?php 
	  while($row=mysql_fetch_array($rec))
	  {
		  $lat2=$row[geolat];
			$lon2=$row[geolon];
	  ?>
      <li class="textbox"><div style="float:left;width:80%;padding-right:5px;">
      <strong><?=$row['title']?></strong><br /><span class="grayplain"><?php echo stripJunk(showBrief(strip_tags($row['description']),30)); ?></span>
        <br /> 
        <div class="gray"><a href="tel:<?=$row['phone']?>"><?=$row['phone']?></a>
        | <a href="dining_details.php?did=<?=$row['loc_id']?>&lat=<?=$lat1?>&lon=<?=$lon1?>">more info</a></div></div>
        <div style="float:right;width:15%;vertical-align:top;padding-top:0px;"><?=round(distance($lat1, $lon1, $lat2, $lon2, "m"),'1').' mi'?></div>
  
      </li>
      <?php
	  }
	  ?>
		
	</ul>

<div id="footer">&copy; <?=date('Y');?> <?=$site_name?>, Inc. | <a href="mailto:<?=$email?>?subject=Feedback">Contact Us</a></div></div>
</body>

</html>
