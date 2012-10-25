<?php

require('jevents.php');
global $var;
include_once('./inc/var.php');
include_once($var->inc_path.'base.php');
_init();

// for facebook description issue.
$intro = db_fetch("select introtext from `jos_content` where `title` = 'Events Page Introduction'");
?>

<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $var->site_name.' | '.$var->page_title; ?></title>
<link rel="image_src" href="http://<?php echo $_SERVER['HTTP_HOST']?>/partner/<?php echo $_SESSION['partner_folder_name']?>/images/logo/logo.png" />  
<meta property="og:image" content="http://<?php echo $_SERVER['HTTP_HOST']?>/partner/<?php echo $_SESSION['partner_folder_name']?>/images/logo/logo.png"/>
<meta property="og:title" content="<?php echo $var->site_name.' | '.$var->page_title; ?>"/>
<meta property="og:description" content="<?php echo strip_tags($intro); ?>"/>

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
<link rel="stylesheet" type="text/css" href="common/css/jquery-ui.css" media="screen" />
<script type="text/javascript" src="common/js/jquery.min.js"></script>
<script type="text/javascript" src="common/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="common/js/default.js"></script>

<link rel="stylesheet" type="text/css" media="all" href="common/DatePick/jsDatePick_ltr.min.css" />
<!-- <script type="text/javascript" src="common/DatePick/jsDatePick.min.1.3.js"></script> -->
<script type="text/javascript" src="common/DatePick/jsDatePick.jquery.full.1.3.js"></script>
<script type="text/javascript">
	window.onload = function(){
		new JsDatePick({
			useMode:2,
			target:"inputField",
			dateFormat:"%d-%M-%Y"
			/*selectedDate:{				This is an example of what the full configuration offers.
				day:5,						For full documentation about these settings please see the full version of the code.
				month:9,
				year:2006
			},
			yearsRange:[1978,2020],
			limitToToday:false,
			cellColorScheme:"beige",
			dateFormat:"%m-%d-%Y",
			imgPath:"img/",
			weekStartDay:1*/
		});

		if(document.getElementById('inputField').value == ''){
			document.getElementById('inputField').value='Search Events by Date';
		}
	};

	function setBlank(htmlObj)
	{
		if(htmlObj.value=='Search Events by Date')
		{
			htmlObj.value='';
		}
	}

	function setEventDate(htmlObj)
	{
		htmlObj.value= date();
	}

	function subForm()
	{
		document.getElementById('frmEventDateSubmit').submit();
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
	<section>
	
    <?php m_event_list_intro(); ?>
	<?php m_event_list(); ?>
	
	
	</section> <!-- rightColumn -->
</div> <!-- wrapper -->
<footer>
	<?php m_footer(); ?> <!-- footer -->
</footer>

</body>
</html> 


