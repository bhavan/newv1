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
$jconfig = new JConfig();
				 
$link = @mysql_pconnect($jconfig->host,  $jconfig->user, $jconfig->password);
mysql_select_db($jconfig->db);


$rec01 = mysql_query("select * from `jos_pageglobal`");
$pageglobal=mysql_fetch_array($rec01);
 
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
$RES=mysql_query("select id from jos_categories where parent_id=169 AND section='com_jevlocations2' and published=1 order by `ordering`");
while($idsrow=mysql_fetch_assoc($RES)){
	$allCatIds[] = $idsrow['id'];
}
$allCatIds[] = 169;
#@#


$path= $_SERVER['PHP_SELF'] . "?option=com_jevlocations&task=locations.listlocations&tmpl=component&needdistance=1&sortdistance=1&lat=".$lat1."&lon=".$lon1."&bIPhone=". $_REQUEST[bIPhone]."&iphoneapp=1&search=". $_REQUEST[search]."&limit=0&jlpriority_fv=0&filter_loccat=".$filter_loccat."&filter_order=".$filter_order."&filter_order_Dir=".$filter_order_Dir;
		
if ($_REQUEST['search']!=='')
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
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
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

</head>
<body>

<div id="content" style="min-height:0px;margin-top:0px;width:310px;">
    <ul class="pageitem" style="width:260px; margin:5px;">
		<li class="select">
			<?php
				$recsubsql="select * from jos_categories where (parent_id=169 OR id=169) AND section='com_jevlocations2' and published=1 ORDER BY title ASC";
				$recsub=mysql_query($recsubsql) or die(mysql_error());
			?>

			<select name="d" onChange="redirecturl(this.value)" style="width:100%; height:40px;border: 0pt none;font-weight:bold;font-size:17px;border: 1px solid #878787;">
				<option value="0">Select a Category</option>
				<option value="0">All</option>
				<option value="alp" <?php if ($_REQUEST['filter_loccat']=='alp') {?> selected <?php }?>>Alphabetic</option>
				<?php	  				
					while($rowsub=mysql_fetch_array($recsub))
					{
						$querycount = "SELECT * FROM jos_jev_locations WHERE published=1 and loccat=".$rowsub['id'];
						if($filter_order != "")
							$querycount .= " ORDER BY title ASC ";
						else
							$querycount .= " ORDER BY ordering ASC";	

						$reccount=mysql_query($querycount) or die(mysql_error());
						if (mysql_num_rows($reccount))
						{
							if(($_REQUEST['filter_loccat']!='alp') || ($_REQUEST['filter_loccat']!='0'))
							{
				?>
			  					<option value="<?=$rowsub['id'];?>" <?php if ($_REQUEST['filter_loccat']==$rowsub['id']) {?> selected <?php }?>><?=$rowsub['title'];?></option>
			 	<?php
			 	  			}
				   		}
					}
				?>
			</select>
			<span class="arrow"></span>
		</li>
	</ul>

	<div onclick="divopen('q1')" style="padding-top:5px;width:25px; height:25px;float:right;cursor:pointer;margin-top:-45px;margin-right:0px;"><img src="../../images/find.png" height="25px" width="25px"/></div>
	
	<ul class="pageitem" style='border:0px;margin:5px;'>
		<li>
			<div id="q1" style="display:none;cursor:pointer;margin:5px">
				<form action="" method="post" name="location_form" style="margin:0px;">
					<input type="text" name="searchvalue" value="" size="25"/>
					<input type="submit" name="search_rcd" value="Search"/>
				</form>
			</div>
		</li>
	</ul>
</div>

<?php
	$featuredListingSQL = " SELECT jjl.loc_id, jjc.target_id, jjl.title, jjl.street, jjl.phone, jjl.loccat, jjc.name, jjc.value, jc.id, jc.parent_id
							FROM `jos_jev_locations` jjl, `jos_categories` jc, `jos_jev_customfields3` jjc
							WHERE jjl.published =1
							AND (jjl.loccat = jc.id AND (jc.parent_id =169 OR jc.id =169) AND jc.section = 'com_jevlocations2' AND jc.published =1 )
							AND (jjl.loc_id = jjc.target_id AND jjc.value = 1 )
							ORDER BY jjl.title ";

	$featuredListing_rec = mysql_query($featuredListingSQL) or die(mysql_error());
	if (mysql_num_rows($featuredListing_rec))
	{	
?>
		<ul class="pageitem" style="width:308px;">
		<li>
		<b>Featured</b>
		<table width="308" cellpadding="0" cellspacing="0" border="0" bgcolor="#F0F0F0">
		<!--<tr><td colspan="2" align="left"></td></tr>-->
<?php
		$rec_featured = mysql_query($query_featured) or die(mysql_error());
		while($row_featured=mysql_fetch_assoc($rec_featured))
		{
			$distance_featured = distance($lat1, $lon1, $row_featured['geolat'],  $row_featured['geolon'], "m");
?>
		<tr>
			<td style="width:260px;border-bottom:solid 1px #777777;padding:3px;">
			<table cellpadding="1" cellspacing="0" border=0>
			<tr><td style="height:3px"></td></tr>
			<tr><td class="headertext"><?php echo $row_featured['title']; ?></td></tr>
			<tr><td class="graytext"><?php
			$words = str_word_count(strip_tags($row_featured['description']),1);
			$desc = htmlspecialchars(implode(" ",array_slice($words,0,30)));
			if(!empty($desc)){ echo $desc .' ...' ;}?></td></tr>
			<tr><td class="graytext">
			<?php if ($_REQUEST['bIPhone']=='0'){?>
				<a class="linktext" href="tel:<?php echo str_replace(array(' ','(',')','-','.'), '', $row_featured[phone]); ?>">call</a> |     
			<?php } else { ?>
				<a class="linktext" href="tel:<?php echo str_replace(array(' ','(',')','-','.'), '', $row_featured[phone]); ?>">call</a> |<?php } ?>

				<a class="linktext" href="javascript:linkClicked('APP30A:FBCHECKIN:<?php echo $row_featured[geolat]; ?>:<?php echo $row_featured[geolon]; ?>')">check in</a> | 
				<a class="linktext" href="diningdetailsv2.php?did=<?=$row_featured[loc_id]?>&<?=round($distance_featured,1)?>&lat=<?=$lat1?>&lon=<?=$lon1?>">more info</a> 
				<a class="linktext" href="javascript:linkClicked('APP30A:SHOWMAP:<?php echo $row_featured[geolon]; ?>:<?php echo $row_featured[geolat]; ?>')"></a> 
				</td>
			</tr>
			<tr><td style="height:5px"></td></tr>
			</table>
			</td>
			<td class="graytext" width="40px" style="border-bottom:solid 1px #777777;padding:3px;" valign="middle" align="center"><?php echo round($distance_featured,1); ?> miles</td>			
		</tr>			
<?php
		}
?>
		</table>
		</li>
		</ul>
<?php
	}
?>


<?php if($_POST['search_rcd']!="Search") { ?>
	<ul class="pageitem" style="width:308px;">
	<li>
	<table width="308" cellpadding="0" cellspacing="0" border="0">
	<tr><td colspan="2"></td></tr>
	<?php
	
	//if (($filter_loccat==0) || ($_REQUEST['filter_loccat']=='alp'))
	// $query = "SELECT *,(((acos(sin(($lat1 * pi() / 180)) * sin((geolat * pi() / 180)) + cos(($lat1 * pi() / 180)) * cos((geolat * pi() / 180)) * cos((($lon1 - geolon) * pi() / 180)))) * 180 / pi()) * 60 * 1.1515) as dist FROM jos_jev_locations $customfields3_table WHERE loccat IN (".implode(',',$allCatIds).") AND published=1 ".$subquery;
	//else

	 $query = "SELECT *,(((acos(sin(($lat1 * pi() / 180)) * sin((geolat * pi() / 180)) + cos(($lat1 * pi() / 180)) * cos((geolat * pi() / 180)) * cos((($lon1 - geolon) * pi() / 180)))) * 180 / pi()) * 60 * 1.1515) as dist FROM jos_jev_locations $customfields3_table WHERE loccat IN (".implode(',',$allCatIds).") AND published=1 ".$subquery;

	//and loccat=".$filter_loccat
	if($filter_loccat == 'Featured')
		$query .= " AND (jos_jev_locations.loc_id = jos_jev_customfields3.target_id AND jos_jev_customfields3.value = 1 ) ";
	elseif($filter_loccat!=0 && $_REQUEST['filter_loccat']!='alp')
		$query .= " AND loccat = $filter_loccat ";

	if(($filter_order != "") || ($_REQUEST['filter_loccat']=='alp'))
		$query .= " ORDER BY title ASC LIMIT " .$start_at.','.$entries_per_page;
	else 
		$query .= " ORDER BY dist ASC LIMIT " .$start_at.','.$entries_per_page;

		
	$rec=mysql_query($query) or die(mysql_error());
	while($row=mysql_fetch_assoc($rec))
	{
		$distance = distance($lat1, $lon1, $row[geolat],  $row[geolon], "m");
					
?>		
	<tr>
		<td style="width:260px;border-bottom:solid 1px #777777;padding:3px;">
		<table cellpadding="1" cellspacing="0" border=0>
		<tr><td style="height:3px"></td></tr>
		<tr><td class="headertext"><?php echo $row[title]; ?></td></tr>
		<tr><td class="graytext"><?php
		$words = str_word_count(strip_tags($row[description]),1);
		$desc = htmlspecialchars(implode(" ",array_slice($words,0,30)));
		//$desc = str_word_count(strip_tags($row[description]),1);
		//echo htmlspecialchars(implode(" ",array_slice($desc,0,30)));
		if(!empty($desc)){ echo $desc .' ...' ;}?></td></tr>
		<tr><td class="graytext">
		<?php if ($_REQUEST['bIPhone']=='0'){?>
			<a class="linktext" href="tel:<?php echo str_replace(array(' ','(',')','-','.'), '', $row[phone]); ?>">call</a> |     
		<?php } else { ?>
			<a class="linktext" href="tel:<?php echo str_replace(array(' ','(',')','-','.'), '', $row[phone]); ?>">call</a> |<?php } ?>

			<a class="linktext" href="javascript:linkClicked('APP30A:FBCHECKIN:<?php echo $row[geolat]; ?>:<?php echo $row[geolon]; ?>')">check in</a> | 

			<a class="linktext" href="diningdetailsv2.php?did=<?=$row['loc_id']?>&lat=<?=$lat1?>&lon=<?=$lon1?>">more info</a> 
			<a class="linktext" href="javascript:linkClicked('APP30A:SHOWMAP:<?php echo $row[geolon]; ?>:<?php echo $row[geolat]; ?>')"></a> 
			</td>
		</tr>
		<tr><td style="height:5px"></td></tr>
		</table>
		</td>
		<td class="graytext" width="40px" style="border-bottom:solid 1px #777777;padding:3px;" valign="middle" align="center"><?php echo round($distance,1); ?> miles</td>			
	</tr>			
	<?php } ?>
</table>
<?php 
if($total_rows>50) {
echo get_paginate_links($total_rows,$entries_per_page,$current_page,$link_to);}?>	
		
</li>
</ul>
<?php } ?>
<?php
	if($_POST['search_rcd']=="Search") {
	$searchdata=$_POST['searchvalue'];
?>
<ul class="pageitem" style="width:308px;">
<li>
	<table width="308" cellpadding="0" cellspacing="0" border="0">
	<?php
if (($filter_loccat==0) || ($_REQUEST['filter_loccat']=='alp') && ($_POST['search_rcd']=="Search")) {
	$search_query1="select * from `jos_jev_locations` where loccat IN ('.implode(',',$allCatIds).') AND published=1 and title like '%$searchdata%' or description like '%$searchdata%' ORDER BY title ASC LIMIT " .$start_at.','.$entries_per_page;
} else if($filter_loccat == 'Featured' && $_POST['search_rcd']=="Search" ) {
	$search_query1="select * from `jos_jev_locations` $customfields3_table where loccat IN ('.implode(',',$allCatIds).') AND published=1 and title like '%$searchdata%' or description like '%$searchdata%'  AND (jos_jev_locations.loc_id = jos_jev_customfields3.target_id AND jos_jev_customfields3.value = 1 ) ORDER BY title ASC LIMIT " .$start_at.','.$entries_per_page;
} else if($_POST['search_rcd']=="Search"){ 
	$search_query1="select * from `jos_jev_locations` where loccat IN ('.implode(',',$allCatIds).') AND published=1 and loccat='.$filter_loccat.' and title like '%$searchdata%' or description like '%$searchdata%' ORDER BY title ASC LIMIT " .$start_at.','.$entries_per_page;
}
		
		$search_query=mysql_query($search_query1) or die(mysql_error());
	

		while($data = mysql_fetch_array($search_query)) {
		      $title=$data[title];
		      $lat2=$data[geolat];
		      $lon2=$data[geolon];

		if (JRequest::getFloat("needdistance",0)){
		$lat=JRequest::getFloat("lat",999);
		$lon=JRequest::getFloat("lon",999);
		$km=JRequest::getInt("km",0)?1.609344:1;
	
		$dist = (((acos(sin(($lat*pi()/180)) * sin(($lat2 * pi()/180)) + cos(($lat * pi() / 180)) * cos(($lat2 * pi() / 180)) * cos((($lon - $lon2) * pi() / 180)))))*180/pi())*60*1.1515*$km;
		}
?>
<tr>
	<td style="width:260px;border-bottom:solid 1px #777777;padding:3px;">
	<table cellpadding="1" cellspacing="0" border=0>
	<tr><td style="height:3px"></td></tr>
	<tr><td class="headertext"><?php echo $title ?></td></tr>	
	<tr><td class="graytext"><?php 
	$words = str_word_count(strip_tags($data[description]),1);
	$desc = htmlspecialchars(implode(" ",array_slice($words,0,30)));
	if(!empty($desc)){ echo $desc .' ...' ;}?></td></tr>
	<tr><td class="graytext">
	<?php if ($_REQUEST['bIPhone']=='0'){?>
	<a class="linktext" href="tel:<?php echo str_replace(array(' ','(',')','-','.'), '', $data[phone]); ?>">call</a> |     
	<?php } else { ?>
	<a class="linktext" href="tel:<?php echo str_replace(array(' ','(',')','-','.'), '', $data[phone]); ?>">call</a> |
<?php 
	//echo $this->escape($data['phone'])." | ";
	} ?>

	<a class="linktext" href="javascript:linkClicked('APP30A:FBCHECKIN:<?php echo $data[geolat]; ?>:<?php echo $data[geolon]; ?>')">check in</a> | 					
	<a class="linktext" href="diningdetailsv2.php?did=<?=$row['loc_id']?>&lat=<?=$lat1?>&lon=<?=$lon1?>">more info</a> 
	<a class="linktext" href="javascript:linkClicked('APP30A:SHOWMAP:<?php echo $data['geolon']; ?>:<?php echo $data['geolat']; ?>')"></a>  
	</td>
	</tr>
	<tr><td style="height:5px"></td></tr>
</table>
</td>
<td class="graytext" width="40px" style="border-bottom:solid 1px #777777;padding:3px;" valign="middle" align="center"><?php echo round($dist,1); ?> miles</td>			
</tr>				
	
<?php } ?>
</table>
<?php if($total_rows >'50') {
echo get_paginate_links($total_rows,$entries_per_page,$current_page,$link_to);}?>
</li>
</ul>
<?php } 

include("connection.php");
?>	

<div id="footer">&copy; <?=date('Y');?> <?=$site_name?> | <a href="mailto:<?=$email?>?subject=Feedback">Contact Us</a></div>
<div style='display:none;'><?php echo $pageglobal['googgle_map_api_keys']; ?></div>
</body>
</html>
<?php
exit();