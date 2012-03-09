<?php

global $var;
include_once('./inc/var.php');
include_once($var->inc_path.'base.php');
_init();

?>

<!DOCTYPE HTML>
<html>
<head>
<title>Tally Life | Dining</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<script>
  document.createElement('header');
  document.createElement('nav');
  document.createElement('section');
  document.createElement('article');
  document.createElement('aside');
  document.createElement('footer');
</script>
<link rel="stylesheet" type="text/css" href="common/css/all.css" media="screen" />
<!--[if IE 7]><link rel="stylesheet" type="text/css" href="common/css/ie7.css" media="screen" /><![endif]-->
</head>

<body>

<header>
<title><?php echo $var->site_name.' | '.$var->page_title; ?></title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="keywords" content="<?php echo $var->keywords; ?>" />
<meta name="description" content="<?php echo $var->metadesc; ?>" />
<meta name="description" content="<?php echo $var->extra_meta; ?>" />
	<?php m_header(); ?> <!-- header -->
</header>
<div id="wrapper">
	<aside>
    <?php m_aside(); ?>
	</aside> <!-- left Column -->
	<section>
    <h2>Tallylife <?php echo loc_cat_from_id($var->get['cat']); ?></h2>
    <?php m_location_list(loc_cat_from_id($var->get['cat']), false); ?>
	</section> <!-- rightColumn -->
</div> <!-- wrapper -->
<footer>
	<?php m_footer(); ?> <!-- footer -->
</footer>

</body>
</html>