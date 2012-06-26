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

// move global var here for v2
global $var;
include_once('./inc/var.php');
include_once($var->inc_path.'base.php');
_init();
// end v2 code


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

if($_POST['action']=='Save' || $_POST['action']=='Ahorrar' && $validCode){
	if($_POST['catid'] != 0){
	
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
		$ins_query=mysql_query("insert into jos_jevents_vevdetail(dtstart,duration,dtend,description,geolon,geolat,location,priority,summary,sequence,state,multiday,hits,noendtime,modified) values('".$start_12h."','0','".$end_12h."','".$jevcontent."','0','0','".$location."','0','".$title."','0','".$custom_field4."','1','0','".$noend."',now())");
		
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
				require_once($var->tpl_path."events_submit_mail.tpl");
				$adminuser = $cat->getAdminUser();
				$adminEmail	= $adminuser->email;
				//$adminEmail	= 'rinkal.gandhi@aaditsoftware.com';
				$sitename =  $jconfig->sitename;
				
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
}

#DD#
if(!$validCode){
	$postValues = $_POST;
	if($_POST['action']=='Save' || $_POST['action']=='Ahorrar')
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



?>
<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $var->site_name.' | '.$var->page_title; ?></title>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
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
<?php include("ga.php"); ?>
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
	/* Code added for Events_submit.tpl */
	require($var->tpl_path."events_submit.tpl");
	?>
</section> <!-- rightColumn -->
</div> <!-- wrapper -->
</div>
<footer>
	<?php m_footer(); ?> <!-- footer -->
</footer>
</body>
</html>