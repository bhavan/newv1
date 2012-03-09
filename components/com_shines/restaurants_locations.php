<?php
define( '_JEXEC', 1 );
define( 'DS', DIRECTORY_SEPARATOR );
$x = realpath(dirname(__FILE__)."/../../") ;
// SVN version
if (!file_exists($x.DS.'includes'.DS.'defines.php')){
	$x = realpath(dirname(__FILE__)."/../../../") ;

}
define( 'JPATH_BASE', $x );

ini_set("display_errors",1);
ini_set("error_reporting",1);


require_once JPATH_BASE.DS.'includes'.DS.'defines.php';
require_once JPATH_BASE.DS.'includes'.DS.'framework.php';
include("../../pagination.php");


require_once("../../configuration.php");
$jconfig = new JConfig();

//include("connection.php");
include("class.paggination.php");
$link = @mysql_pconnect($jconfig->host,  $jconfig->user, $jconfig->password);
mysql_select_db($jconfig->db);

$rec = mysql_query("select * from `jos_pageglobal`");
$pageglobal=mysql_fetch_array($rec);
 
 
$gmapkeys=explode('googlemapskey=',$pagejevent['params']);
$gmapkeys1=explode("\n",$gmapkeys[1]);
 
$site_name = $pageglobal['site_name'];
$beach = $pageglobal['beach'];
$email = $pageglobal['email'];
$googgle_map_api_keys = $gmapkeys1[0];
$location_code = $pageglobal['location_code'];

if($_POST['search_rcd']=="Search") {
	$searchdata= trim($_POST['searchvalue']);
}

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


if ($_REQUEST['filter_loccat']!=0)
	$loccat=$_REQUEST['filter_loccat'];
else
	$loccat = 0;

if (isset($_REQUEST['start']))
	$ii=$_REQUEST['start'];
else
	$ii=0;

if(isset($_REQUEST['filter_order']) && $_REQUEST['filter_order']!="")
	$filter_order = $_REQUEST['filter_order'];
else	
	$filter_order = 0;
	
if(isset($_REQUEST['filter_order_Dir']) && $_REQUEST['filter_order_Dir']!="")
	$filter_order_Dir = $_REQUEST['filter_order_Dir'];
else	
	$filter_order_Dir = "ASC";

$path="restaurants_locations.php?option=com_jevlocations&task=locations.listlocations&tmpl=component&needdistance=1&sortdistance=1&lat=".$lat1."&lon=".$lon1."&bIPhone=". $_REQUEST['bIPhone']."&iphoneapp=1&search=". $_REQUEST['search']."&limit=0&jlpriority_fv=0&filter_loccat=".$loccat."&filter_order=".$filter_order."&filter_order_Dir=".$filter_order_Dir;


if ($searchdata!='')
	$subquery = " and jos_jev_locations.title like '%".$searchdata."%' or jos_jev_locations.description like '%".$searchdata."%' ";
else
	$subquery = "";


	
/*
global $var;
include_once('./inc/var.php');
include_once($var->inc_path.'base.php');
_init();
*/

//$query= 'SELECT *,((ACOS(SIN('.$lat1.' * PI() / 180) * SIN(`geolat` * PI() / 180) + COS('.$lat1.' * PI() / 180) * COS(`geolat` * PI() / 180) * COS(('.$lon1.' - `geolon`) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance FROM jos_jev_locations WHERE published=1 AND global=1 ';

$query= 'SELECT jos_jev_locations.*, jos_categories.id, jos_categories.parent_id, ((ACOS(SIN('.$lat1.' * PI() / 180) * SIN(`geolat` * PI() / 180) + COS('.$lat1.' * PI() / 180) * COS(`geolat` * PI() / 180) * COS(('.$lon1.' - `geolon`) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance
		FROM jos_jev_locations
		LEFT JOIN jos_categories ON (jos_jev_locations.loccat = jos_categories.id and jos_categories.section="com_jevlocations2")  
		WHERE jos_jev_locations.published=1 AND jos_jev_locations.global=1 ';


if(!empty($loccat))
{
	$query .= " AND jos_jev_locations.loccat = $loccat ";
}
else
{
	$query .= " AND ( jos_categories.id = 152 || jos_categories.parent_id = 152 ) ";
}

if(!empty($subquery))
	$query .= $subquery;
	
if($filter_order != "")
	$query .= " ORDER BY jos_jev_locations.title ASC ";
else
	$query .= ' ORDER BY distance ASC ';

//$rec=mysql_query($query) or die(mysql_error());

 $mydb=new pagination($jconfig->host,$jconfig->user,$jconfig->password,$jconfig->db);
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
$cleanedString = preg_replace("/[^A-Za-z0-9\s\.\-\/+\!;:\n\t\r\(\)\'\"._\?>,~\*<}{\[\]\=\&\@\#\$\%\^` ]/","", $string); 
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
<link href="images/homescreen.gif" rel="apple-touch-icon" />
<meta content="minimum-scale=1.0, width=device-width, maximum-scale=0.6667, user-scalable=no" name="viewport" />
<!--<link href="css/style.css" rel="stylesheet" media="screen" type="text/css" />-->
<script src="javascript/functions.js" type="text/javascript"></script>
<title><?=$site_name?></title>
<!--<link href="pics/startup.png" rel="apple-touch-startup-image" /> -->
<meta name="description" content="<?php echo $var->metadesc; ?>" />
<meta name="description" content="<?php echo $var->extra_meta; ?>" />
</head>

<body>
<!--Google Adsense -->


<!--
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
-->

<script type="text/javascript">
	function linkClicked(link) { document.location = link; }

	ddsmoothmenu.init({
		mainmenuid: "smoothmenu1", //menu DIV id
		orientation: 'h', //Horizontal or vertical menu: Set to "h" or "v"
		classname: 'ddsmoothmenu', //class added to menu's outer DIV
		//customtheme: ["#1c5a80", "#18374a"],
		contentsource: "markup" //"markup" or ["container_id", "path_to_menu_file"]
	})

	ddsmoothmenu.init({
		mainmenuid: "smoothmenu2", //Menu DIV id
		orientation: 'v', //Horizontal or vertical menu: Set to "h" or "v"
		classname: 'ddsmoothmenu-v', //class added to menu's outer DIV
		//customtheme: ["#804000", "#482400"],
		contentsource: "markup" //"markup" or ["container_id", "path_to_menu_file"]
	})

	function redirecturl(val)
	{
		//url="<?php echo $_SERVER['PHP_SELF']; ?>?filter_loccat="+val;

		url="restaurants_locations.php?option=com_jevlocations&task=locations.listlocations&tmpl=component&needdistance=1&sortdistance=1&lat=<?=$_REQUEST['lat']?>&lon=<?=$_REQUEST['lon']?>&bIPhone=<?=$_REQUEST['bIPhone']?>&iphoneapp=1&search=<?=$_REQUEST['search']?>&limit=0&jlpriority_fv=0&filter_loccat="+val + "&filter_order=<?echo $filter_order?>&filter_order_Dir=<?=$filter_order_Dir?>";
		window.location=url;
	}

	function divopen(str)
	{
		if(document.getElementById(str).style.display=="none") {
			document.getElementById(str).style.display="block";
		} else {
			document.getElementById(str).style.display="none";
		}
	}

</script>

<div id="content">
	<style>
		body { margin-top: 1px; margin-left: 0px; margin-right: 0px; font-family: Verdana, Geneva, sans-serif; }
		.bluetext { color: #0088BB; font-size: 13px; font-weight:bold; }
		.bluetextsmall { color: #00AADD; font-size: 13px; /*font-style: italic;*/}
		.headertext { color: #000000; font-size: 17px; }
		.graytext { color: #777777; font-size: 14px; }
		.graytextSmall { color: #777777; font-size: 13px; }
		.linktext { color: #0000ff; font-size: 14px; text-decoration: underline; } 
		
	</style>




<!--********************************************************-->

		<table width="310" cellpadding="0" cellspacing="0" border="0" >
			<tr>
				<td style="width:260px;">
					<?php
						$recsub=mysql_query("select * from jos_categories where section='com_jevlocations2' and published=1 AND (id='152' || parent_id='152') order by `ordering`") or die(mysql_error());
					?>
					<select name="d" onChange="redirecturl(this.value)" style="margin: 5px; width: 90%; height: 40px; font-weight: bold; font-size: 17px; border: 1px solid #878787;">
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
	 				
	 				<div onclick="divopen('q1')" style="padding-top:5px;width:25px; height:25px;float:right;cursor:pointer;margin-top:-40px;"><img src="../../images/find.png" height="25px" width="25px"/></div>		
	 				
				</td>
			</tr>
				<td>
					<div id="q1" style="display:none;cursor:pointer;float:right;margin-top:15px">
						<form action="" method="post" name="location_form">
							<input type="text" name="searchvalue" value="" size="25"/>
							<input type="submit" name="search_rcd" value="Search"/>
						</form>
					</div>
				</td>
			<tr>
			</tr>
		</table>
		
		

<!--********************************************************-->


	<table width="310" cellpadding="0" cellspacing="0" border="0" style="padding-left:5px;">
	<?php 
		while($row=mysql_fetch_array($rec))
		{
			$lat2=$row['geolat'];
			$lon2=$row['geolon'];
	?>
		<tr >
			<td style="width:260px;border-top:solid 1px #009dd9">
				<table cellpadding="1" cellspacing="0">
					<tr><td style="height:3px"></td></tr>
					<tr><td class="headertext"><?=$row['title']?></td></tr>		            
					<tr><td class="graytext"><?php echo stripJunk(showBrief(strip_tags($row['description']),30)); ?></td></tr>
					<tr><td class="graytext">
		            	<?php if ($_REQUEST["bIPhone"]==1){?>
                        <a class="linktext" href="tel:<?php echo $row[phone];?>"><?php echo $row['phone']; ?></a> |     
                      	<?php } else { 
                      		echo $row['phone']." | ";
                      	 } ?>
						<!--<a href="dining_details.php?did=<?=$row['loc_id']?>&lat=<?=$lat1?>&lon=<?=$lon1?>">more info</a>-->
						<a class="linktext" href="javascript:linkClicked('APP30A:SHOWDETAILS:<?php echo $row['loc_id']; ?>:<?=round(distance($lat1, $lon1, $lat2, $lon2, "m"),'1')?>')">more info</a>
						</td>
					</tr>
					<tr><td style="height:5px"></td></tr>
				</table>
			</td>
			<td class="graytext" width="50px" style="border-top:solid 1px #009dd9" valign="middle" align="center"><?=round(distance($lat1, $lon1, $lat2, $lon2, "m"),'1').' miles'?></td>			
		</tr>			
	<?php
		}
	?>
	</table>

</div>
<div style='display:none;'><?php echo $pageglobal['googgle_map_api_keys']; ?></div>
</body>

</html>
<?php
exit();