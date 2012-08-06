<?php

define( '_JEXEC', 1 );
define( 'DS', DIRECTORY_SEPARATOR );
$x = realpath(dirname(__FILE__)."/../../") ;
// SVN version
if (!file_exists($x.DS.'includes'.DS.'defines.php')){
	$x = realpath(dirname(__FILE__)."/../../../") ;

}
define( 'JPATH_BASE', $x );

ini_set("display_errors",0);

require_once JPATH_BASE.DS.'includes'.DS.'defines.php';
require_once JPATH_BASE.DS.'includes'.DS.'framework.php';
include("../../pagination.php");
require_once("../../configuration.php");
include("connection.php");
include("iadbanner.php");
$jconfig = new JConfig();
				 
$link = @mysql_pconnect($jconfig->host,  $jconfig->user, $jconfig->password);
mysql_select_db($jconfig->db);


$rec01 = mysql_query("select * from `jos_pageglobal`");
$pageglobal=mysql_fetch_array($rec01);
 
function stripJunk($string) { 
$cleanedString = preg_replace("/[^A-Za-z0-9\s\.\-\/+\!;\n\t\r\(\)\'\"._\?>,~\*<}{\[\]\=\&\@\#\$\%\^` ]:/","", $string); 
$cleanedString = preg_replace("/\s+/"," ",$cleanedString); 
return $cleanedString; 
}
 
function distance($lat1, $lon1, $lat2, $lon2, $unit) { 

  $theta = $lon1 - $lon2; 
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)); 
  $dist = acos($dist); 
  $dist = rad2deg($dist); 
  $miles = $dist * 60 * 1.1515;
  $unit = strtoupper($unit);

  if ($unit == "KMS") {
    return ($miles * 1.609344); 
  } else if ($unit == "N") {
      return ($miles * 0.8684);
    } else {
        return $miles;
      }
}
if ($_REQUEST['lat']!="")
$lat1=$_REQUEST['lat'];
else
$lat1=0;
if ($_REQUEST['lon']!="")
$lon1=$_REQUEST['lon'];
else
$lon1=0;

if ($_REQUEST['filter_loccat']!='0')
$filter_loccat=$_REQUEST['filter_loccat'];

if($filter_loccat == 'Featured')
	$customfields3_table = ", `jos_jev_customfields3` ";
else
	$customfields3_table = "";

if (isset($_REQUEST['start']))
	$ii=$_REQUEST['start'];
else
	$ii=0;

if(isset($_REQUEST['filter_order']) && $_REQUEST['filter_order']!="")
	$filter_order = $_REQUEST['filter_order'];
else	
	$filter_order = "";
	
if(isset($_REQUEST['filter_order_Dir']) && $_REQUEST['filter_order_Dir']!="")
	$filter_order_Dir = $_REQUEST['filter_order_Dir'];
else	
	$filter_order_Dir = "ASC";


#@#
$RES=mysql_query("select id from jos_categories where parent_id=152 AND section='com_jevlocations2' and published=1 order by `ordering`");
while($idsrow=mysql_fetch_assoc($RES)){
	$allCatIds[] = $idsrow['id'];
}
$allCatIds[] = 152;
#@#


$path= $_SERVER['PHP_SELF'] . "?option=com_jevlocations&task=locations.listlocations&tmpl=component&needdistance=1&sortdistance=1&lat=".$lat1."&lon=".$lon1."&bIPhone=". $_REQUEST[bIPhone]."&iphoneapp=1&search=". $_REQUEST[search]."&limit=0&jlpriority_fv=0&filter_loccat=".$filter_loccat."&filter_order=".$filter_order."&filter_order_Dir=".$filter_order_Dir;
		
if ($_REQUEST['search']!='' || $_REQUEST['Buscar']!='')
	$subquery="  and title like '%".$_REQUEST['search']."%' or description like '%".$_REQUEST['search']."%'";

//if ($filter_loccat==0 || $_REQUEST['filter_loccat']=='alp')
//	$query1 = "SELECT *,(((acos(sin(($lat1 * pi() / 180)) * sin((geolat * pi() / 180)) + cos(($lat1 * pi() / 180)) * cos((geolat * pi() / 180)) * cos((($lon1 - geolon) * pi() / 180)))) * 180 / pi()) * 60 * 1.1515) as dist FROM jos_jev_locations $customfields3_table WHERE loccat IN (".implode(',',$allCatIds).") AND published=1 ".$subquery;
//else

	$query1 = "SELECT *,(((acos(sin(($lat1 * pi() / 180)) * sin((geolat * pi() / 180)) + cos(($lat1 * pi() / 180)) * cos((geolat * pi() / 180)) * cos((($lon1 - geolon) * pi() / 180)))) * 180 / pi()) * 60 * 1.1515) as dist FROM jos_jev_locations $customfields3_table WHERE loccat IN (".implode(',',$allCatIds).") AND published=1 ".$subquery;


//and loccat=".$filter_loccat
if($filter_loccat == 'Featured')
	$query .= " AND (jos_jev_locations.loc_id = jos_jev_customfields3.target_id AND jos_jev_customfields3.value = 1 ) ";
elseif($filter_loccat!=0 && $_REQUEST['filter_loccat']!='alp')
	$query .= " AND loccat = $filter_loccat ";



if(($filter_order != "") || ($_REQUEST['filter_loccat']=='alp'))
	$query1 .= " ORDER BY title ASC ";
else
	$query1 .= " ORDER BY dist ASC";

	$rec1=mysql_query($query1) or die(mysql_error());
	$total_data=mysql_num_rows($rec1);
	$total_rows=$total_data;
	$page_limit=50;
	$entries_per_page=$page_limit;
	$current_page=(empty($_REQUEST['page']))? 1:$_REQUEST['page'];
	$start_at=($current_page * $entries_per_page)-$entries_per_page;
	$link_to=$path;
	
$query_featured = "SELECT *,(((acos(sin(($lat1 * pi() / 180)) * sin((geolat * pi() / 180)) + cos(($lat1 * pi() / 180)) * cos((geolat * pi() / 180)) * cos((($lon1 - geolon) * pi() / 180)))) * 180 / pi()) * 60 * 1.1515) as dist FROM jos_jev_locations, jos_jev_customfields3 WHERE loccat IN (".implode(',',$allCatIds).") AND published=1 ";
$query_featured .= " AND (jos_jev_locations.loc_id = jos_jev_customfields3.target_id AND jos_jev_customfields3.value = 1 ) ";
$query_featured .= " ORDER BY dist ASC";



//ob_end_clean();
header( 'Content-Type:text/html;charset=utf-8');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta name="viewport" content="width=280, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
<meta content="yes" name="apple-mobile-web-app-capable" />
<meta content="index,follow" name="robots" />
<meta content="text/html; charset=iso-8859-1" http-equiv="Content-Type" />
<meta name="description" content="<?php echo $var->metadesc; ?>" />
<meta name="description" content="<?php echo $var->extra_meta; ?>" />
<title><?=$site_name?></title>

<link href="pics/homescreen.gif" rel="apple-touch-icon" />
<link href="css/style.css" rel="stylesheet" media="screen" type="text/css" />
<link href="pics/startup.png" rel="apple-touch-startup-image" />

<style>
	/*.pagination-btn{height:23px; float:left; padding-left:6px; background:url('images/btn-left.png') no-repeat; text-decoration:none;}
	.pagination-btn span{height:23px; display:block;  background:url('images/btn-right.png') no-repeat right top; font:Arial, Helvetica, sans-serif; font-size:13px; color:#333; text-decoration:none; width:65px; padding-top:3px;text-align:center;}

	.pagination-btn:hover{background:url('images/btn-left-hover.png') no-repeat;text-decoration:none;}
	.pagination-btn:hover span{background:url('images/btn-right-hover.png') no-repeat right top; text-decoration:none;}*/

	.pagination-btn{height:23px; float:left; padding-left:6px; text-decoration:none;}
	.pagination-btn span{height:23px; display:block;  font:Arial, Helvetica, sans-serif; font-size:13px; color:#333; text-decoration:none; width:65px; padding-top:3px;text-align:center;}

	.pagination-btn:hover{text-decoration:none;}
	.pagination-btn:hover span{text-decoration:none;}
	
	body { margin-top: 1px; margin-left: 0px; margin-right: 0px; font-family: Verdana, Geneva, sans-serif; }
	.bluetext { color: #0088BB; font-size: 13px; font-weight:bold; }
	.bluetextsmall { color: #00AADD; font-size: 13px; /*font-style: italic;*/}
	.headertext { color: #000000; font-size: 12px; font-weight: bold;}
	.graytext { color: #000000; font-size: 13px; }
	.graytextSmall { color: #777777; font-size: 13px; }
	.linktext { color: #2200c1; font-size: 14px; text-decoration: underline; }
	.pageitem { background-color: #FFFFFF;display: block;font-size: 12pt;height: auto;list-style: none outside none;margin: 3px 5px 17px;overflow: hidden;padding: 0;position: relative;width: auto}
</style>

<script src="javascript/functions.js" type="text/javascript"></script>
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
	url="<?php echo $_SERVER['PHP_SELF']; ?>?option=com_jevlocations&task=locations.listlocations&tmpl=component&needdistance=1&sortdistance=1&lat=<?=$_REQUEST['lat']?>&lon=<?=$_REQUEST['lon']?>&bIPhone=<?=$_REQUEST['bIPhone']?>&iphoneapp=1&search=<?=$_REQUEST['search']?>&limit=0&jlpriority_fv=0&filter_loccat="+val + "&filter_order=<?=$filter_order?>&filter_order_Dir=<?=$filter_order_Dir?>";
	window.location=url;
}

function divopen(str) {
	if(document.getElementById(str).style.display=="none") {
		document.getElementById(str).style.display="block";
	} else {
		document.getElementById(str).style.display="none";
	}
}
</script>
<?php include("../../ga.php"); ?>
</head>
<body>
 <div class="iphoneads" style="vertical-align:top">
    <?php m_show_banner('iphone-restaurants-screen'); ?>
  </div>
<?php
	/* Code added for iphone_restaurants.tpl */
	require("../../partner/".$_SESSION['tpl_folder_name']."/tpl/iphone_restaurants.tpl");
	?>
</body>
</html>
<?php
exit();