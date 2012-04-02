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
$lat1=0;

//$lat1=30.393534;
if ($_REQUEST['lon']!="")
$lon1=$_REQUEST['lon'];
else
$lon1=0;

//$lon1=-86.495783;

if (isset($_REQUEST['filter_loccat']) && $_REQUEST['filter_loccat']!="")
	$loccat = $_REQUEST['filter_loccat'];
else
	$loccat = 0;

if(!empty($loccat) && $loccat == 'Featured')
{
	$customfields3_table = ", `jos_jev_customfields3` ";
}
else
{
	$customfields3_table = "";
}

/*
global $var;
include_once('./inc/var.php');
include_once($var->inc_path.'base.php');
_init();
*/

if($_POST['search_rcd']=="Search") {
	$searchdata= !empty($_POST['searchvalue']) ? $_POST['searchvalue'] : '';
}

#@#
$RES=mysql_query("select id from jos_categories where parent_id=151");
while($idsrow=mysql_fetch_assoc($RES)){
	$allCatIds[] = $idsrow['id'];
}
$allCatIds[] = 151;
#@#

if(!empty($searchdata)){
	//$query= 'SELECT *,((ACOS(SIN('.$lat1.' * PI() / 180) * SIN(`geolat` * PI() / 180) + COS('.$lat1.' * PI() / 180) * COS(`geolat` * PI() / 180) * COS(('.$lon1.' - `geolon`) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance FROM jos_jev_locations WHERE published=1 AND global=1 AND title like "%'.$searchdata.'%"';
	$query= 'SELECT *,((ACOS(SIN('.$lat1.' * PI() / 180) * SIN(`geolat` * PI() / 180) + COS('.$lat1.' * PI() / 180) * COS(`geolat` * PI() / 180) * COS(('.$lon1.' - `geolon`) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance FROM jos_jev_locations ' . $customfields3_table . ' WHERE loccat IN ('.implode(',',$allCatIds).') AND published=1 AND global=1 AND title like "%'.$searchdata.'%"';
}else{
	$query= 'SELECT *,((ACOS(SIN('.$lat1.' * PI() / 180) * SIN(`geolat` * PI() / 180) + COS('.$lat1.' * PI() / 180) * COS(`geolat` * PI() / 180) * COS(('.$lon1.' - `geolon`) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance FROM jos_jev_locations ' . $customfields3_table . ' WHERE loccat IN ('.implode(',',$allCatIds).') AND published=1 AND global=1 ';
}


if(!empty($loccat))
{
	if($loccat == 'Featured')
		$query .= " AND (jos_jev_locations.loc_id = jos_jev_customfields3.target_id AND jos_jev_customfields3.value = 1 ) ";
	else
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

$query_featured = "SELECT *,((ACOS(SIN('.$lat1.' * PI() / 180) * SIN(`geolat` * PI() / 180) + COS('.$lat1.' * PI() / 180) * COS(`geolat` * PI() / 180) * COS(('.$lon1.' - `geolon`) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance FROM jos_jev_locations, jos_jev_customfields3 WHERE loccat IN (".implode(',',$allCatIds).") AND published=1 AND global=1 ";
$query_featured .= " AND (jos_jev_locations.loc_id = jos_jev_customfields3.target_id AND jos_jev_customfields3.value = 1 ) ";
$query_featured .= " ORDER BY distance ASC ";

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


<?php /*?>
<div id="topbar">
<div id="title">Places</div>
	<div id="leftnav">   
            <?php 
         if ($current_page!=0)
				  			{
							 $st1=($current_page*$num_rec)-$num_rec;	
						?>
    <a href="<?php echo $_SERVER['PHP_SELF']; ?>?start=<?=$st1?>&lat=<?=$lat1?>&lon=<?=$lon1?>&filter_loccat=<?=$loccat?>">&nbsp;</a>
    <?php }?>

        </div>
        
        
	<div id="rightnav">
		    <?php
					  if (($current_page+1)<$num_pages)
				 		 {
					  $st1=($current_page*$num_rec)+$num_rec;
					  ?>
    <a href="<?php echo $_SERVER['PHP_SELF']; ?>?start=<?=$st1?>&lat=<?=$lat1?>&lon=<?=$lon1?>&filter_loccat=<?=$loccat?>">&nbsp;</a>
    <?php }?>
    
</div></div>
<?php */?>

<div id="content" style="width:310px;">

	<script type="text/javascript">
		function redirecturl(val)
		{
			url="<?php echo $_SERVER['PHP_SELF']; ?>?lat=<?php echo $_REQUEST['lat'];?>&lon=<?php echo $_REQUEST['lon'];?>&filter_loccat="+val;
			window.location=url;
		}
		
		function linkClicked(link) { document.location = link; }
		
		function divopen(str) {

			if(document.getElementById(str).style.display=="none") {
				document.getElementById(str).style.display="block";
			} else {
				document.getElementById(str).style.display="none";
			}
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
		.pageitem {margin: 3px 3px 17px;font-size: 13px;}
	</style>


		<ul class="pageitem" style="width:85%; margin-bottom:5px;">
			<li class="select">

			<?php 
				$recsub=mysql_query("select * from jos_categories where (parent_id=151 OR id=151) AND section='com_jevlocations2' and published=1 order by `ordering`") or die(mysql_error());
			?>

			<select name="d" onChange="redirecturl(this.value)" style="width:100%; height:40px;border: 0pt none;font-weight:bold;font-size:17px;border: 1px solid #878787;" >
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
	 <span class="arrow"></span>
	 </li>
	
	</ul>
	
	<div style="border:0px solid;width:30px;height:25px;margin-top:-50px;margin-right:10px;float:right;">
	<div onclick="divopen('q1')" style="padding-top:5px;width:25px; height:25px;float:right;cursor:pointer"><img src="../../images/find.png" height="25px" width="25px"/></div>
</div>

	<ul class="pageitem" style='border:0px; margin-bottom:5px;'><li>
	<div id="q1" style="display:none;cursor:pointer;text-align:center;margin-top:5px"><form action="" method="post" name="location_form"><input type="text" name="searchvalue" value="" size="25"/><input type="submit" name="search_rcd" value="Search"/></form></div>
	</li></ul>

<?php	
	$featuredListingSQL = " SELECT jjl.loc_id, jjc.target_id, jjl.title, jjl.street, jjl.phone, jjl.loccat, jjc.name, jjc.value, jc.id, jc.parent_id
							FROM `jos_jev_locations` jjl, `jos_categories` jc, `jos_jev_customfields3` jjc
							WHERE jjl.published =1
							AND (jjl.loccat = jc.id AND (jc.parent_id =151 OR jc.id =151) AND jc.section = 'com_jevlocations2' AND jc.published =1 )
							AND (jjl.loc_id = jjc.target_id AND jjc.value = 1 )
							ORDER BY jjl.title ";

	$featuredListing_rec = mysql_query($featuredListingSQL) or die(mysql_error());
	if (mysql_num_rows($featuredListing_rec))
	{
?>
	<span style="padding-left:5px;"><b>Featured</b></span>
	<div style="background-color:#F0F0F0;margin-left:5px;">
	<ul class="pageitem" style="margin: 3px 3px 17px 0px;">
<?php 
		$rec_featured = mysql_query($query_featured) or die(mysql_error());
		while($row_featured=mysql_fetch_assoc($rec_featured))
		{
			$lat2_featured = $row_featured['geolat'];
			$lon2_featured = $row_featured['geolon'];
			$distance_featured = distance($lat1, $lon1, $row_featured['geolat'],  $row_featured['geolon'], "m");
?>
		  <li class="textbox" style="background-color:#F0F0F0;">
		  <div style="float:left;width:80%;padding-right:5px;background-color:#F0F0F0;">
			<strong><?=$row_featured['title']?></strong><br />
			<span class="grayplain"><?php echo stripJunk(showBrief(strip_tags($row_featured['description']),30)); ?></span><br /> 
			<div class="gray">
				<a href="tel:<?=$row['phone']?>">call</a> | 
				<a class="linktext" href="javascript:linkClicked('APP30A:FBCHECKIN:<?php echo $row_featured[geolat]; ?>:<?php echo $row_featured[geolon]; ?>')">check in</a> | 
				<a class="linktext" href="placedetails.php?did=<?=$row_featured[loc_id]?>&<?=round($distance_featured,1)?>&lat=<?=$lat1?>&lon=<?=$lon1?>">more info</a> 
				<a class="linktext" href="javascript:linkClicked('APP30A:SHOWMAP:<?php echo $row_featured[geolon]; ?>:<?php echo $row_featured[geolat]; ?>')"></a> 
				<!--<a href="dining_details.php?did=<?=$row['loc_id']?>&lat=<?=$lat1?>&lon=<?=$lon1?>">more info</a> -->
			</div>
		  </div>
		  <div style="float:right;width:15%;vertical-align:top;padding-top:0px;background-color:#F0F0F0;"><?=round(distance($lat1, $lon1, $lat2_featured, $lon2_featured, "m"),'1').' mi'?></div>

		  </li>
<?php
		}
?>
	</ul>
	</div>
<?php
	}
?>

	<ul class="pageitem">
      <?php 
	  while($row=mysql_fetch_array($rec))
	  {
		  $lat2=$row[geolat];
			$lon2=$row[geolon];
	  ?>
      <li class="textbox">
      <div style="float:left;width:80%;padding-right:5px;">
		<strong><?=$row['title']?></strong><br />
		<span class="grayplain"><?php echo stripJunk(showBrief(strip_tags($row['description']),30)); ?></span><br /> 
        <div class="gray">
        	<a href="tel:<?=$row['phone']?>"><?=$row['phone']?></a> | 
			<a class="linktext" href="javascript:linkClicked('APP30A:FBCHECKIN:<?php echo $row[geolat]; ?>:<?php echo $row[geolon]; ?>')">check in</a> | 
			<a class="linktext" href="diningdetailsv2.php?did=<?=$row['loc_id']?>&lat=<?=$lat1?>&lon=<?=$lon1?>">more info</a> 
			<a class="linktext" href="javascript:linkClicked('APP30A:SHOWMAP:<?php echo $row[geolon]; ?>:<?php echo $row[geolat]; ?>')"></a> 
	        <!--<a href="dining_details.php?did=<?=$row['loc_id']?>&lat=<?=$lat1?>&lon=<?=$lon1?>">more info</a> -->
        </div>
      </div>
      <div style="float:right;width:15%;vertical-align:top;padding-top:0px;"><?=round(distance($lat1, $lon1, $lat2, $lon2, "m"),'1').' mi'?></div>
  
      </li>
      <?php
	  }
	  ?>
		
	</ul>

<div id="footer">&copy; <?=date('Y');?> <?=$site_name?> | <a href="mailto:<?=$email?>?subject=Feedback">Contact Us</a></div></div>
<div style='display:none;'><?php echo $pageglobal['googgle_map_api_keys']; ?></div>
</body>

</html>
