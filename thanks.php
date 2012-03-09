<?php
if ($_REQUEST['1639d3e6a2e97a0f11946da637ec38a7']=='') {
header("location:index.php");
}
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
<meta name="description" content="<?php echo $var->extra_meta; ?>" />

<script>
  document.createElement('header');
  document.createElement('nav');
  document.createElement('section');
  document.createElement('article');
  document.createElement('aside');
  document.createElement('footer');
</script>
<link rel="stylesheet" type="text/css" href="common/templatecolor/<?php echo $_SESSION['style_folder_name'];?>/css/all.css" media="screen" />
<!--[if IE 7]><link rel="stylesheet" type="text/css" href="common/templatecolor/<?php echo $_SESSION['style_folder_name'];?>/css/ie7.css" media="screen" /><![endif]-->
<link rel="stylesheet" type="text/css" href="common/css/jquery-ui.css" media="screen" />
<script type="text/javascript" src="common/js/jquery.min.js"></script>
<script type="text/javascript" src="common/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="common/js/default.js"></script>

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
	<div><jdoc:include type='message'/><font color="#FF0000">
 <?php
if ($_REQUEST['1639d3e6a2e97a0f11946da637ec38a7']!='')
 {
 echo '<span style="text-align:left;">Your event has been saved and is under review.</span><br/><br/>';
?>

	<a class="button" href="event_submit.php" style="float:left;" >Submit Another Event</a><br /><br /><br /><br /><br /></font>
<?php } 
?>
	</div>

	<?php //m_featured_event(); ?> <!-- featuredEvent -->
    	<?php m_photos_mini(); ?>  <!-- photos -->
    	<br /><br />
    	<?php //m_events_this_week(); ?>
	</section> <!-- rightColumn -->
</div> <!-- wrapper -->
<footer>
	<?php m_footer(); ?> <!-- footer -->
</footer>

</body>
</html> 