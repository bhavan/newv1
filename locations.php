<?php

global $var;
include_once('./inc/var.php');
include_once($var->inc_path.'base.php');
_init();

//#DD#
$searchdata = '';
if($_POST['search_rcd']=="Search Places" || $_POST['search_rcd']=="Lugares de la búsqueda") {
	$searchdata = trim($_POST['searchvalue']);
}


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
<link rel="stylesheet" type="text/css" href="common/templatecolor/<?php echo $_SESSION['style_folder_name'];?>/css/all.css" media="screen" />
<!--[if IE 7]><link rel="stylesheet" type="text/css" href="common/templatecolor/<?php echo $_SESSION['style_folder_name'];?>/css/ie7.css" media="screen" /><![endif]-->
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
    <h2><?php echo $var->site_name; ?> <?php echo $var->page_title; ?></h2>
    <?php
		/* Code added for locations.tpl for V2 */
		require($var->tpl_path."locations.tpl");
	?>
    <div class="adThreeHunderd" style="float:left;">
      <?php m_show_banner('Website Front Page Feature'); ?>
    </div> <!-- adThreeHunderd -->
    
    <!--#DD#--> 
    <div style="text-align:center;">
    <form action="" method="post" name="location_form">
    <input type="text" name="searchvalue" value="<?php echo $searchdata; ?>" size="35" style="margin-top:20px;" />
    <?php
	/* Code added for location_button.tpl */
	require($var->tpl_path."location_button.tpl");
	?>
    </form>
    </div>
    <!--#DD#--> 


    <?php
	$rec=mysql_query("select * from jos_categories where 	(parent_id=151 OR id=151) AND section='com_jevlocations2' and published=1 order by `ordering`") or die(mysql_error());
	while($row=mysql_fetch_array($rec))
	{
		if (m_location_count($row['title'],false, $searchdata))
		{
	?>
    <div style="float:left;width:720px;display:block;">
    <h2><?=$row['title'];?></h2>
      
        <?php m_location_list($row['title'],false, $searchdata); ?>
     
     <br clear="all"> 
     </div>
    <?php
	}}
	?>
	</section> <!-- rightColumn -->
</div> <!-- wrapper -->
<footer>
	<?php m_footer(); ?> <!-- footer -->
</footer>

</body>
</html>