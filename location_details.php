<?php

require('jevents.php');
global $var;
include_once('./inc/var.php');
include_once($var->inc_path.'base.php');
_init();


$data = db_fetch("select * from `jos_jev_locations` where `loc_id` = ".$var->get['id']);
$data['q'] = str_replace(' ', '+', ($data['title'].' '.$data['street'].' '.$data['city'].' '.$data['state'].' '.$data['postcode']));
//fprint($data); _x();

?>

<!DOCTYPE HTML>
<html>
<head>
<!-- <title><?php echo $var->site_name.' | '.$var->page_title; ?></title> -->
<title><?php echo $var->site_name.' | '.$var->page_title; ?> | <?php echo $data['title']; ?></title>
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
	/* Code added for location_details.tpl */
	require($var->tpl_path."location_details.tpl");
	?>
	</section> <!-- rightColumn -->
</div> <!-- wrapper -->
<footer>
	<?php m_footer(); ?> <!-- footer -->
</footer>

</body>
</html>