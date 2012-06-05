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

?>

<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $var->site_name.' | '.$var->page_title; ?></title>
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
    <?php m_featured_event(); ?> <!-- featuredEvent -->
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