<?php
define( '_JEXEC', 1 );
define('JPATH_BASE', dirname(__FILE__) );
define( 'DS', DIRECTORY_SEPARATOR );

require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php');
require_once ( JPATH_BASE .DS.'libraries'.DS.'joomla'.DS.'utilities'.DS.'utility.php');
require(JPATH_BASE .DS.'libraries'.DS.'joomla'.DS.'database'.DS.'table.php');
require(JPATH_BASE .DS.'libraries'.DS.'joomla'.DS.'database'.DS.'table'.DS.'category.php');
//require(JPATH_BASE .DS.'language'.DS.'en-GB'.DS.'en-GB.com_jevents.ini');

require_once ( JPATH_BASE .DS.'components'.DS.'com_jevents'.DS.'libraries'.DS.'helper.php');
require_once ( JPATH_BASE .DS.'components'.DS.'com_jevents'.DS.'libraries'.DS.'commonfunctions.php');
require_once ( JPATH_BASE .DS.'administrator'.DS.'components'.DS.'com_jevents'.DS.'libraries'.DS.'categoryClass.php');
require_once("configuration.php");

//#DD# 
//session_start();  // Start the session where the code will be stored.
include("securimage/securimage.php");
$img = new Securimage();
$validCode = $img->check($_POST['code']);
//#DD# 


$mainframe =& JFactory::getApplication('site');
$mainframe->initialise();
$session =& JFactory::getSession();

$jconfig = new JConfig();
define(DB_HOST, $jconfig->host);
define(DB_USER,$jconfig->user);
define(DB_PASSWORD,$jconfig->password);
define(DB_NAME,$jconfig->db);
$conn=mysql_connect(DB_HOST,DB_USER,DB_PASSWORD) or die(mysql_error());
$db=mysql_select_db(DB_NAME) or die(mysql_error());

// Query for default ics record.
$ics_query=mysql_query("select * from jos_jevents_icsfile where isdefault='1' and state='1'");
$ics_res=mysql_fetch_array($ics_query);
$ics=$ics_res['ics_id'];

$msg="";

if($_POST['action']=='Save' && $validCode){

$title=$_POST['title'];
$allDayEvent=$_POST['allDayEvent'];
$custom_field4=$_POST['custom_field4'];
$publish_up=$_POST['publish_up'];
$publish_down=$_POST['publish_down'];

	if($_POST['allDayEvent']=='on') {
		$datem=$publish_up." ".'00:00:00';
		$datee=$publish_down." ".'23:59:59';
		$start_12h=strtotime($publish_up.'00:00:00');
		$end_12h=strtotime($publish_down.'23:59:59');
		$noend=0;

	} else if($_POST['noendtime']!='') {
		$start_12h=strtotime($_POST['publish_up'].$_POST['start_12h'].$_POST['start_ampm']);
		$end_12h=strtotime($_POST['publish_down'].$_POST['start_12h'].$_POST['start_ampm']);
		$datem=$publish_up." ".date("H:i:s",$start_12h);
		$datee=$publish_down." ".'23:59:59';
		$noend=1;
	
	} else {
		$start_12h=strtotime($_POST['publish_up'].$_POST['start_12h'].$_POST['start_ampm']);
		$end_12h=strtotime($_POST['publish_down'].$_POST['end_12h'].$_POST['end_ampm']);
		$datem=$publish_up." ".date("H:i:s",$start_12h);
		$datee=$publish_down." ".date("H:i:s",$end_12h);
		$noend=0;
	}
$day = date('l',strtotime($publish_up));
$weekday=strtoupper(substr($day,0,2));
$cat_id=$_POST['catid'];
$ics_id=$_POST['ics_id'];
$jevcontent=$_POST['jevcontent'];
$location=$_POST['location'];
$custom_anonusername=$_POST['custom_anonusername'];
$custom_anonemail=$_POST['custom_anonemail'];
$uid=$_SESSION['__default']['user']->id;

$userid=md5(uniqid(rand(),true));
$duplicate_value=md5(uniqid(rand(),true));
$data=array(dtstart =>$start_12h,
	    UID=>$userid,
	    dtend=>$end_12h,
	    description=>$jevcontent,
	    allDayEvent=>$allDayEvent,
	    publish_down=>$publish_down,
	    location=>$location,
	    publish_up=>$publish_up,
	    summary=>$title,
	    createdby=>$uid,
	    Customfield=>$custom_field4,
	    multiday=>1,
	    lockevent=>0,
	    FREQ=>None,
	    Category=>$cat_id,
	    Username=>$custom_anonusername,
	    UserEmail=>$custom_anonemail,
	    noendtime=>0);
$rawdata=serialize($data);

// Data insertion in event detail table  // 
$ins_query=mysql_query("insert into jos_jevents_vevdetail(dtstart,duration,dtend,description,geolon,geolat,location,priority,summary,sequence,state,multiday,hits,noendtime,modified) values('".$start_12h."','0','".$end_12h."','".$jevcontent."','0','0','".$location."','0','".$title."','0','1','1','0','".$noend."',now())");

// Query for last record id of detail table
$last_id_query=mysql_query("SELECT LAST_INSERT_ID() as last_id");
$result_lastid=mysql_fetch_array($last_id_query);
$last_id=$result_lastid['last_id'];


// Data insertion in event table
$ins_query1=mysql_query("insert into jos_jevents_vevent(icsid,catid,uid,created,created_by,rawdata,detail_id,state,access,lockevent,author_notified) values('".$ics_id."','".$cat_id."','".$userid."',now(),'".$uid."','".$rawdata."','".$last_id."','".$custom_field4."','0','0','0')");


// Query for last record id of event table
$last_id_query1=mysql_query("SELECT LAST_INSERT_ID() as last_id1");
$result_lastid1=mysql_fetch_array($last_id_query1);
$last_id1=$result_lastid1['last_id1'];

//#DD#
if($last_id1>0){
	$ins_query0=mysql_query("insert into jos_jev_anoncreator(ev_id ,name,email) values('".$last_id1."','".$custom_anonusername."','".$custom_anonemail."')"); 
}
//#DD#

// Data insertion in event rrule table 
$ins_query2=mysql_query("insert into jos_jevents_rrule(eventid,freq,until,count,rinterval,byday) values('".$last_id1."','none','0','1','1','".$weekday."')");

// Data insertion in event repetition table 
$ins_query3=mysql_query("insert into jos_jevents_repetition(eventid,eventdetail_id,duplicatecheck,startrepeat,endrepeat) values('".$last_id1."','".$last_id."','".$duplicate_value."','".$datem."','".$datee."')");


$db =& JFactory::getDBO();
$vevent->icsid = $ics_id;
$abc=JLoader::register('JEventsCategory',JEV_ADMINPATH."/libraries/categoryClass.php");
$cat = new JEventsCategory($db);
$cat->load($vevent->catid);

	if(!empty($last_id) && (!empty($last_id1))) {
		$msg='Thank you for submitting your event. Our team will review and promote your information as soon as possible! Please complete this form again to submit other events.';
		$adminuser = $cat->getAdminUser();
		$adminEmail	= $adminuser->email;
		//$adminEmail	= 'rinkal.gandhi@aaditsoftware.com';
		$sitename =  $jconfig->sitename;
		$subject= 'New Event Submission ';
		$message = '
		<table width="100%" cellpadding="0" cellspacing="0">
		<tr>
		<td colspan="2" align="left">Title : '.$title.'</td>
		
		</tr>
		<tr><td style="padding:15px">&nbsp;</td></tr>
		<tr>
		<td colspan="2">'.$jevcontent.'</td>
		</tr>
		<tr>
		<td align="left" colspan="2" style="padding-top:25px">Event Submitted from
		['.$sitename.'] by ['.$custom_anonusername.'('.$custom_anonemail.')]</td>
		</tr>
		</table>';
		
		$ack_message = '
		<table width="100%" cellpadding="0" cellspacing="0">
		<tr>
		<td colspan="2" align="left">Title : '.$title.'</td>
		
		</tr>
		<tr><td style="padding:15px">&nbsp;</td></tr>
		<tr>
		<td colspan="2">'.$msg.'</td>
		</tr>
		</table>';
		$headers = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type:text/html;charset=iso-8859-1' . "\r\n";
		$headers .= 'From: NO-REPLY <admin@domainname.com>' . "\r\n";
		$headers .= 'X-Mailer: PHP/' . phpversion() . "\r\n";
		// Email Notification to Administrator
		mail($adminEmail,$subject,$message,$headers);
		
		// Acknowledgement Email to the Event Creator. 
		mail($custom_anonemail,$subject,$ack_message,$headers);
	}
}

#DD#
if(!$validCode){
	$postValues = $_POST;
	if($_POST['action']=='Save')
	{
		$msg="Invalid varification code.";
	}	
	
}else{
	$postValue = array();
	$postValue['title'] = ''; 
	$postValue['catid'] = '';
	$postValue['allDayEvent'] = '';
	$postValue['publish_up'] = '';
	$postValue['start_12h'] = '';
	$postValue['publish_up'] = '';
	$postValue['end_12h'] = '';
	$postValue['noendtime'] = '';
	$postValue['jevcontent'] = '';
	$postValue['custom_anonusername'] = '';
	$postValue['custom_anonemail'] = '';
}
#DD#

global $var;
include_once('./inc/var.php');
include_once($var->inc_path.'base.php');
_init();

?>
<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $var->site_name.' | '.$var->page_title; ?></title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="keywords" content="<?php echo $var->keywords; ?>" />
<meta name="description" content="<?php echo $var->metadesc; ?>" />
<script>
  document.createElement('header');
  document.createElement('nav');
  document.createElement('section');
  document.createElement('article');
  document.createElement('aside');
  document.createElement('footer');
</script>
<link rel="stylesheet" type="text/css" href="common/templatecolor/<?php echo $_SESSION['style_folder_name'];?>/css/all.css" media="screen" />
<link href="/templates/rt_quantive_j15/favicon.ico" rel="shortcut icon" type="image/x-icon" />
<link rel="stylesheet" href="/administrator/templates/khepri/css/icon.css" type="text/css" />
<link rel="stylesheet" href="/administrator/components/com_jevents/assets/css/eventsadmin.css" type="text/css" />
<link rel="stylesheet" href="/media/system/css/modal.css" type="text/css" />
<link rel="stylesheet" href="/components/com_jevents/assets/css/dashboard.css" type="text/css" />
<link rel="stylesheet" href="/plugins/system/rokbox/themes/light/rokbox-style.css" type="text/css" />
<link rel="stylesheet" href="/components/com_gantry/css/joomla.css" type="text/css" />
<link rel="stylesheet" href="/templates/rt_quantive_j15/css/joomla.css" type="text/css" />
<link rel="stylesheet" href="/templates/rt_quantive_j15/css/style8.css" type="text/css" />
<link rel="stylesheet" href="/templates/rt_quantive_j15/css/light-body.css" type="text/css" />
<link rel="stylesheet" href="/templates/rt_quantive_j15/css/demo-styles.css" type="text/css" />
<link rel="stylesheet" href="/templates/rt_quantive_j15/css/template.css" type="text/css" />
<link rel="stylesheet" href="/templates/rt_quantive_j15/css/template-firefox.css" type="text/css" />
<link rel="stylesheet" href="/templates/rt_quantive_j15/css/typography.css" type="text/css" />
<link rel="stylesheet" href="/templates/rt_quantive_j15/css/fusionmenu.css" type="text/css" />
<link rel="stylesheet" href="/modules/mod_rokajaxsearch/css/rokajaxsearch.css" type="text/css" />
<link rel="stylesheet" href="/modules/mod_rokajaxsearch/themes/blue/rokajaxsearch-theme.css" type="text/css" />


<style type="text/css">
<!--
#rt-main-surround ul.menu li.active > a, #rt-main-surround ul.menu li.active > .separator, #rt-main-surround ul.menu li.active > .item, #rt-main-surround .square4 ul.menu li:hover > a, #rt-main-surround .square4 ul.menu li:hover > .item, #rt-main-surround .square4 ul.menu li:hover > .separator, .roktabs-links ul li.active span {color:#3636eb;}
a, #rt-main-surround ul.menu a:hover, #rt-main-surround ul.menu .separator:hover, #rt-main-surround ul.menu .item:hover {color:#3636eb;}
-->
</style>
<style type="text/css">
/* overriding edit event header logo */
div#toolbar-box div.header.icon-48-jevents {
  background-image: none!important;
  padding-left:1px!important;
  color:#666;
  line-height:48px;
  background-color: #FFF;
}
</style>
<script type="text/javascript" src="/includes/js/joomla.javascript.js"></script>
<script type="text/javascript" src="/media/system/js/mootools.js"></script>
<script type="text/javascript" src="/administrator/components/com_jevents/assets/js/editical.js?v=1.5.4"></script>
<script type="text/javascript" src="/administrator/components/com_jevpeople/assets/js/people.js"></script>
<script type="text/javascript" src="/common/js/modal.js"></script>
<script type="text/javascript" src="/media/system/js/tabs.js"></script>
<script type="text/javascript" src="/plugins/editors/jce/tiny_mce/tiny_mce.js?version=156"></script>

<script type="text/javascript" src="/plugins/editors/jce/libraries/js/editor.js?version=156"></script>
<script type="text/javascript" src="/components/com_jevents/assets/js/calendar11.js"></script>
<script type="text/javascript" src="/administrator/components/com_jevlocations/assets/js/locations.js"></script>
<script type="text/javascript" src="/plugins/content/avreloaded/silverlight.js"></script>
<script type="text/javascript" src="/plugins/content/avreloaded/wmvplayer.js"></script>
<script type="text/javascript" src="/plugins/content/avreloaded/swfobject.js"></script>
<script type="text/javascript" src="/plugins/content/avreloaded/avreloaded.js"></script>
<script type="text/javascript" src="/plugins/system/rokbox/rokbox.js"></script>
<script type="text/javascript" src="/plugins/system/rokbox/themes/light/rokbox-config.js"></script>

<script type="text/javascript" src="/components/com_gantry/js/gantry-buildspans.js"></script>
<script type="text/javascript" src="/modules/mod_roknavmenu/themes/fusion/js/fusion.js"></script>
<script type="text/javascript" src="/modules/mod_rokajaxsearch/js/rokajaxsearch.js"></script>
<script type="text/javascript"></script>
<script type="text/javascript" src="/plugins/system/pc_includes/ajax_1.3.js"></script>
<link href="/indexiphone.php?option=com_jevents&amp;task=modlatest.rss&amp;format=feed&amp;type=rss&amp;Itemid=111&amp;modid=0"  rel="alternate"  type="application/rss+xml" title="JEvents - RSS 2.0 Feed" />
<link href="/indexiphone.php?option=com_jevents&amp;task=modlatest.rss&amp;format=feed&amp;type=atom&amp;Itemid=111&amp;modid=0"  rel="alternate"  type="application/rss+xml" title="JEvents - Atom Feed" />
<script type="text/javascript" src="common/js/event_submit.js"></script>

<script type="text/javascript" language="javascript">
function gotoindex(str){
//alert(str);
var id=document.getElementById(str).value;
	if(id=="Cancel") {
		document.location='/index.php'
		return false;
	}
}
function alldayeventtog() {
var check = document.adminForm.allDayEvent.checked;
var noendchecked = document.adminForm.noendtime.checked;
var spm = document.getElementById("startPM");
var sam = document.getElementById("startAM");
var epm = document.getElementById("endPM");
var eam = document.getElementById("endAM");
if(check) {
	document.adminForm.noendtime.checked = false;
	document.adminForm.start_12h.disabled=true;
	document.adminForm.end_12h.disabled=true;
	spm.disabled=true;
	sam.disabled=true;
	epm.disabled=true;
	eam.disabled=true;

	if(!noendchecked) {
	epm.disabled=true;
	eam.disabled=true;
	document.adminForm.start_12h.disabled=true;
	document.adminForm.end_12h.disabled=true;
	spm.disabled=true;
	sam.disabled=true;
	} 

} else {
	document.adminForm.start_12h.disabled=false;
	document.adminForm.end_12h.disabled=false;
	spm.disabled=false;
	sam.disabled=false;
	epm.disabled=false;
	eam.disabled=false;
}
}
function noendtimetog(){
var noendchecked = document.adminForm.noendtime.checked;
var epm = document.getElementById("endPM");
var eam = document.getElementById("endAM");
	if (noendchecked && document.adminForm.allDayEvent.checked) {
		document.adminForm.allDayEvent.checked = false;
		alldayeventtog();
	}

if(noendchecked) {
	epm.disabled=true;
	eam.disabled=true;
	document.adminForm.end_12h.disabled=true;
} else {
	epm.disabled=false;
	eam.disabled=false;
	document.adminForm.end_12h.disabled=false;
}

}

function form_validation() {
	if (document.adminForm.title.value=="")
	{
		alert ("Title can not be blank !");
		document.adminForm.title.focus();
		return false;
	}
	if (document.adminForm.catid.value=="0")
	{
		alert ("Please Select the Category!");
		document.adminForm.catid.focus();
		return false;
	}
	if (document.adminForm.custom_anonusername.value=="")
	{
		alert ("User Name can not be blank !");
		document.adminForm.custom_anonusername.focus();
		return false;
	}
	if (document.adminForm.custom_anonemail.value=="")
	{
		alert ("Email Address can not be blank !");
		document.adminForm.custom_anonemail.focus();
		return false;
	}
	var stuchkemail=/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,5})+$/.test(document.adminForm.custom_anonemail.value);
	if (!stuchkemail){
		alert("Invalid E-mail Address! Please re-enter.");
		document.adminForm.custom_anonemail.focus();
		return (false);
	}
}
</script>
</head>
<body>
<header>
	<?php m_header(); ?> <!-- header -->
</header>

<div id="wrapper">
<aside>
    <?php m_aside(); ?>
</aside> <!-- left Column -->
<section><!-- Right Column -->

<?php
// Print Message after event form submission starts.
if($msg!='') {?>
<table cellpadding="0" cellspacing="0" width="100%" style="border: 2px solid rgb(255, 0, 0); height:40px;margin-bottom">
	<tr>
		<td style="padding:8px">
			<font color="#FF0000"><b><?php echo $msg;?></b></font>
		</td>
	</tr>
</table>
<?php } ?>

<!--Jevent Form Starts-->
<div style="padding:10px"></div>
<h2>Send Us Your Events</h2>
<div id="jevents" >
<form action="" method="post" name="adminForm" enctype='multipart/form-data' onSubmit="return form_validation()">
<div style='width:500px;'>
<div class="adminform" align="left" style="width:600px">

	<table width="94%" cellpadding="5" cellspacing="2" border="1"  class="adminform" id="jevadminform">
	<tr>
		<td align="left">Event Name:</td>
		<td align="right"><input class="inputbox" type="text" name="title" size="60" maxlength="255" value="<?=$postValues['title']?>" /></td>
		<td colspan="2"><input type="hidden" name="priority" value="0" /></td>
	</tr>
	<tr>
		<td valign="top" align="left">Categories</td>
			<?php $cat_query=mysql_query("select * from jos_categories where section='com_jevents' and published='1'");?> 
		<td style="width:200px" >
			<select name="catid" id="catid">
				<option value="0" >Choose Category</option>
				<?php while($row=mysql_fetch_array($cat_query)) { 
				if($postValues['catid']==$row['id']){
					$selectedVal = 'selected';
				}else{
					$selectedVal = '';
				}
				?>
				
				<option value="<?php echo $row['id']?>" <?=$selectedVal?> ><?php echo $row['name']?></option>
				<?php } ?>
			</select>
		</td>
		<td colspan="2"></td>
	</tr>
	<tr>
		<td colspan='4'><input type="hidden" name="ics_id" value="<?php echo $ics?>" /></td>		
	</tr>
	<tr>
		<td valign="top" align="left" colspan="4">
		  	<div style="clear:both;width:551px">
				<fieldset class="jev_sed">
					<legend>Start, End, Duration</legend>
					<span>
						<span >All day Event or Unspecified time</span>
						<span><input type="checkbox" id='allDayEvent' name='allDayEvent' <?php if($postValues['allDayEvent']=='on') {echo 'checked'; }?>  onclick="alldayeventtog()" />
						</span>
					</span>
					<span style="margin:20px" class='checkbox12h'>
						<span><input type="hidden" id='view12Hour' name='view12Hour' checked='checked' onClick="toggleView12Hour();" value="1"/></span>
					</span>
					<div>
						<fieldset>
							<legend>Start date</legend>
							<div style="float:left">
								<?php 
									if(empty($postValues['publish_up'])){ 
										$publish_up_value = date("Y-m-d");
									}else{ 
										$publish_up_value = $postValues['publish_up']; 
									} 
								?>
								<input type="text" name="publish_up" id="publish_up" value="<?php echo $publish_up_value;?>" maxlength="10" onChange="var elem = $('publish_up');checkDates(elem);" size="10"  />         
							</div>
							<div style="float:left;margin-left:11px!important;">Start Time&nbsp;
								<span id="start_12h_area" style="display:inline">
								
								<?php 
									if(empty($postValues['start_12h'])){ 
										$start_12h_value = '08:00';
									}else{ 
										$start_12h_value = $postValues['start_12h']; 
									} 

									if($postValues['start_ampm']=='pm'){ 
										$start_ampm_check = 'checked="checked"';
									} 

									$end_ampm_check = array();
									if($postValues['start_ampm']=='pm'){ 
										$end_ampm_check['pm'] = 'checked="checked"';
										$end_ampm_check['am'] = '';
									}else{
										$end_ampm_check['pm'] = '';
										$end_ampm_check['am'] = 'checked="checked"';
									}
								?>

								<input class="inputbox" type="text" name="start_12h" id="start_12h" size="8" maxlength="8"  value="<?=$start_12h_value?>" onChange="check12hTime(this);" />
								<input type="radio" name="start_ampm" id="startAM" value="am" <?=$end_ampm_check['am']?> checked="checked" onClick="toggleAMPM('startAM');"  />am  <input type="radio" name="start_ampm" id="startPM" value="pm" <?=$end_ampm_check['pm']?> onClick="toggleAMPM('startPM');"  />pm		</span>
							</div>
						</fieldset>
					</div>
					<div>
						<fieldset><legend>End date</legend>
						<div style="float:left">
						<?php 
							if(empty($postValues['publish_down'])){ 
								$publish_down_value = date("Y-m-d");
							}else{ 
								$publish_down_value = $postValues['publish_down']; 
							} 
						?>
					<input type="text" name="publish_down" id="publish_down" value="<?php echo $publish_down_value;?>" maxlength="10" onChange="var elem = $('publish_up');checkDates(elem);" size="10"  />         
						</div>
						<div style="float:left;margin-left:11px!important">End Time&nbsp;
							<span id="end_12h_area" style="display:inline">
							<?php 
								if(empty($postValues['end_12h'])){ 
									$end_12h_value = '05:00';
								}else{ 
									$end_12h_value = $postValues['end_12h']; 
								} 

								$end_ampm_check = array();
								if($postValues['end_ampm']=='am'){ 
									$end_ampm_check['am'] = 'checked="checked"';
									$end_ampm_check['pm'] = '';
								}else{
									$end_ampm_check['am'] = '';
									$end_ampm_check['pm'] = 'checked="checked"';
								}

							?>
							<input class="inputbox" type="text" name="end_12h" id="end_12h" size="8" maxlength="8"  value="<?php echo $end_12h_value;?>" onChange="check12hTime(this);" />
							<input type="radio" name="end_ampm" id="endAM" value="am" <?=$end_ampm_check['am']?>  onclick="toggleAMPM('endAM');"  />am 
							<input type="radio" name="end_ampm" id="endPM" value="pm" <?=$end_ampm_check['pm']?> onClick="toggleAMPM('endPM');" />pm	
							</span>
							<span style="margin-left:10px">
								<span><input type="checkbox" id='noendtime' name='noendtime'  onclick="noendtimetog();" <?php if($postValues['noendtime']==1) {echo 'checked'; }?> value="1" />
								<span >No specific end time</span>
								</span>
							</span>
						</div>
						</fieldset>
					</div>
				</fieldset>
			</div>
			<div style="clear:both;"></div>

		</td>
	</tr>
	<tr>
		<td style="vertical-align:top" align="left">Description</td>
		<td colspan="3">
			<div id='jeveditor' style="width:457px"><textarea id="jevcontent" name="jevcontent" cols="70" rows="10" style="width:100%;height:230px;" class="mceEditor"><?=$postValues['jevcontent']?></textarea>
			</div>       	
		</td>
	</tr>
	<tr id="jeveditlocation">
		<td width="130" align="left" style="vertical-align:top;">Location</td>
		<td colspan="3">
			<input type="hidden" name="location" id="locn" value=""/>
			<input type="text" name="evlocation_notused" disabled="disabled" id="evlocation" value=" -- " style="float:left"/>
			<div class="button2-left">
				<div class="blank"><a href="javascript:selectLocation('' ,'/indexiphone.php?option=com_jevlocations&amp;task=locations.select&amp;tmpl=component','750','500')" title="Select Location"  >select</a>
				</div>
			</div>
			<div class="button2-left">
				<div class="blank"><a href="javascript:removeLocation();" title="Remove Location"  >remove</a>
				</div>
			</div>
			<div style="font-size:10px; vertical-align:top;float:left;">If your location is not listed, please include the full address in the description.</div>
	         </td>
	</tr>
	<tr class="jevplugin_anonusername">
		<td valign="top"  width="130" align="left">Your name</td>
		<td colspan="3"><input size="50" type="text" name="custom_anonusername" id="custom_anonusername" value="<?=$postValues['custom_anonusername']?>" /></td>
	</tr>
	<tr class="jevplugin_anonemail">
		<td valign="top"  width="130" align="left">Your email address</td>
		<td colspan="3"><input size="50" type="text" name="custom_anonemail" id="custom_anonemail" value="<?=$postValues['custom_anonemail']?>" /></td>
	</tr>
	
	<!--#DD#-->
	<tr class="jevplugin_anonemail">	
	<td>&nbsp;</td>
	<td>
		<div style="font-size: 11px; vertical-align: top; float: left;">Type the characters you see in the picture below.</div><br>
			<img id="siimage" align="left" style="width:150px;" style="padding-right: 5px; border: 0" src="securimage/securimage_show.php?sid=<?php echo md5(time()) ?>" />
			<a tabindex="-1" style="border-style: none" href="#" title="Refresh Image" onClick="document.getElementById('siimage').src = 'securimage/securimage_show.php?sid=' + Math.random(); return false"><img src="securimage/images/refresh.gif" alt="Reload Image" border="0" onClick="this.blur()" align="bottom" /></a>
		</td>
	</tr>
	<tr class="jevplugin_anonemail">	
	<td>Verification Code</td>
	<td>
		<input type="text" value="" id="code" name="code" size="25">
		<br><br>
		</td>
	</tr>
	<!--#DD#-->
	
	</table>
</div>
<input type="hidden" name="custom_field4" value="0" />

<table align="left" style="" width="30%" cellpadding="0" cellspacing="0">
<tbody><tr>
		<td id="toolbar-save" valign="top"style="padding-right:3px">
			<a style="cursor:pointer;height:21px;"><input src="images/save-btn.gif" type="submit" name="action" value="Save" class="button"/></a>
		</td>
		<td id="toolbar-save" valign="top"style="padding-right:3px">
			<a style="cursor:pointer;height:21px;">
			<input type="button" name="can" id="can" value="Cancel" class="button" onClick="gotoindex(this.id)"/></a>
		</td>

	</tr></tbody>
</table>
</form>

</div>
<!--Jevent Form Ends-->
</section> <!-- rightColumn -->
</div> <!-- wrapper -->
</div>
<footer>
	<?php m_footer(); ?> <!-- footer -->
</footer>
</body>
</html>