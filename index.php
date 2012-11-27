<?php

if (count($_GET) || count($_POST))
{

include("indexiphone.php");
exit;
}

require('jevents.php');

if (isset($_SESSION['__default']['application.queue'][0]['message']))
{
	$_SESSION['displayeventupload']="Thank you for submitting your event. Our team will review and promote your information as soon as possible! Please complete this form again to submit other events.";
	header("Location: event_submit.php?option=com_jevents&view=icalevent&task=icalevent.edit&Itemid=111&tmpl=component");
	exit;
}
global $var;
include_once('./inc/var.php');
include_once($var->inc_path.'base.php');
_init();

// directory Settings
$web_root = 'http';
if(isset($_SERVER['HTTPS']))
{
 $web_root .= ($_SERVER['HTTPS'] == 'on' ? 's' : '');
}
$web_root .= '://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
preg_match('/https?:\/\/.*\//i', $web_root, $matches);
$web_root = $matches[0];
//#DD#

// for facebook description issue.
$sql = "select jjv.*,jjr.rp_id, jjr.startrepeat, DATE_FORMAT(jjr.startrepeat,'%D %b, %Y') _dateF, DATE_FORMAT(jjr.startrepeat,'%h:%i %p') as timestart, DATE_FORMAT(jjr.endrepeat,'%h:%i %p') as timeend from `jos_jevents_vevdetail` jjv, `jos_jevents_repetition` jjr where jjv.state=1 and jjv.evdet_id = jjr.eventdetail_id and jjr.endrepeat >= TIMESTAMP(CURRENT_TIMESTAMP,'$var->timezone') order by jjr.endrepeat limit 1";
$data = db_fetch($sql);
$temp = explode(' ', $data['startrepeat']);
$data['_date'] = $temp[0];

?>

<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $var->site_name.' | '.$var->page_title; ?></title>
<link rel="image_src" href="http://<?php echo $_SERVER['HTTP_HOST']?>/partner/<?php echo $_SESSION['partner_folder_name']?>/images/logo/logo.png" />  
<meta property="og:image" content="http://<?php echo $_SERVER['HTTP_HOST']?>/partner/<?php echo $_SESSION['partner_folder_name']?>/images/logo/logo.png"/>
<meta property="og:title" content="<?php echo $var->site_name.' | '.$var->page_title.' | '.$data['summary']; ?>"/>
<meta property="og:description" content="Check out <?php echo $data['summary'];?> on <?php echo $data['_date']; ?>. Check out more local events at <?php echo $_SERVER['SERVER_NAME']?>."/>

<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<meta name="keywords" content="<?php echo $var->keywords; ?>" />
<meta name="description" content="<?php echo $var->metadesc; ?>" />
<meta name="description" content="<?php echo $var->extra_meta; ?>" />

<script>
  document.createElement('header');
  document.createElement('nav');
  document.createElement('section');
  document.createElement('article');
  document.createElement('aside');
  document.createElement('footer');
</script>
<link rel="stylesheet" type="text/css" href="<?php echo $web_root;?>common/templatecolor/<?php echo $_SESSION['style_folder_name'];?>/css/all.css" media="screen" />
<!--[if IE 7]><link rel="stylesheet" type="text/css" href="common/templatecolor/<?php echo $_SESSION['style_folder_name'];?>/css/ie7.css" media="screen" /><![endif]-->
<link rel="stylesheet" type="text/css" href="<?php echo $web_root;?>common/css/jquery-ui.css" media="screen" />
<script type="text/javascript" src="<?php echo $web_root;?>common/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo $web_root;?>common/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo $web_root;?>common/js/default.js"></script>
<!-- use favicon icon for v2 -->
<link rel="shortcut icon" href="partner/<?php echo $_SESSION['partner_folder_name'];?>/images/favicon.ico" />

<!--  CODE for SAFARI BROWSER DETECTION BEGIN -->
	<script>
	// First Time Visit Processing
	// copyright 10th January 2006, Stephen Chapman
	// permission to use this Javascript on your web page is granted
	// provided that all of the below code in this script (including this
	// comment) is used without any alteration
	function rC(nam) {var tC = document.cookie.split('; '); for (var i = tC.length - 1; i >= 0; i--) {var x = tC[i].split('='); if (nam == x[0]) return unescape(x[1]);} return '~';} function wC(nam,val) {document.cookie = nam + '=' + escape(val);} function lC(nam,pg) {var val = rC(nam); if (val.indexOf('~'+pg+'~') != -1) return false; val += pg + '~'; wC(nam,val); return true;} function firstTime(cN) {return lC('pWrD4jBo',cN);} function thisPage() {var page = location.href.substring(location.href.lastIndexOf('\/')+1); pos = page.indexOf('.');if (pos > -1) {page = page.substr(0,pos);} return page;}

	// example code to call it - you may modify this as required
	function start() {
	   if (firstTime(thisPage())) {
	      // this code only runs for first visit
	     if((navigator.userAgent.match(/iphone/i)) || (navigator.userAgent.match(/ipad/i)) || (navigator.userAgent.match(/ipod/i))) {
	  var r=confirm("Install iphone app on your device: Click ok to Install");
	   if (r==true){window.location = "<?php echo $var->iphone?>";}
	     }
	     else if (navigator.userAgent.match(/android/i)) {
	     var r=confirm("Install android app on your device: Click ok to Install");
	     if (r==true){location.href="<?php echo $var->android?>";}
	 }else {}
	   }
	   // other code to run every time once page is loaded goes here
	}
	onload = start;

	</script>
<!--  CODE for SAFARI BROWSER DETECTION END -->

<!--  Town wizard Google Analytic code -->
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
	<section>
	
    <?php 
	
	m_featured_event(); 
	
	?> <!-- featuredEvent -->
    <?php m_photos_mini(); ?>  <!-- photos -->
    <br /><br />
    <?php m_events_this_week(); ?>
	</section> <!-- rightColumn -->
</div> <!-- wrapper -->
<footer>
	<?php m_footer(); ?> <!-- footer -->
</footer>

</body>
</html>