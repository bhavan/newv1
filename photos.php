<?php

global $var;
include_once('./inc/var.php');
include_once($var->inc_path.'base.php');
_init();

?>

<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $var->site_name.' | Photos'; ?></title>
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
<link rel="stylesheet" type="text/css" href="common/templatecolor/<?php echo $_SESSION['style_folder_name'];?>/css/all.css" media="screen" />
<!--[if IE 7]><link rel="stylesheet" type="text/css" href="common/templatecolor/<?php echo $_SESSION['style_folder_name'];?>/css/ie7.css" media="screen" /><![endif]-->
<link rel="stylesheet" type="text/css" href="common/css/pirobox/style.css" media="screen" />
<link rel="stylesheet" type="text/css" href="common/css/jquery-ui.css" media="screen" />
<script type="text/javascript" src="common/js/jquery.min.js"></script>
<script type="text/javascript" src="common/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="common/js/pirobox.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	$().piroBox({
    my_speed: 400, //animation speed
    bg_alpha: 0.1, //background opacity
    slideShow : true, // true == slideshow on, false == slideshow off
    slideSpeed : 4, //slideshow duration in seconds(3 to 6 Recommended)
    close_all : '.piro_close,.piro_overlay'// add class .piro_overlay(with comma)if you want overlay click close piroBox
	});
});
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
	<section>
    <?php m_photos(); ?>
	</section> <!-- rightColumn -->
</div> <!-- wrapper -->
<footer>
	<?php m_footer(); ?> <!-- footer -->
</footer>

</body>
</html>