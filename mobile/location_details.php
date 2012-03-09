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
<link rel="stylesheet" type="text/css" href="common/css/all.css" media="screen" />
<!--[if IE 7]><link rel="stylesheet" type="text/css" href="common/css/ie7.css" media="screen" /><![endif]-->
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
    <h2>Place Details</h2>
    <table valign="top" width="100%" cellpadding="0" cellspacing="0" style="width:350px;float:left;">
      <tbody>
      	<tr>
        	<td colspan="2" valign="top" style="font-size:18px;color:#666666;font-weight:bold;line-height:20px;"><?php echo $data['title'] ?><br /><br /></td>
        </tr>
        <tr height="40">
        	<td valign="top"><font color="#666666"><strong>Address:</strong></font>&nbsp;</td>
          <td valign="top"><?php echo $data['street'].",<br />".$data['city'].",<br />".$data['state']." - ".$data['postcode']; ?></td>
        </tr>
      	<tr>
        	<td colspan="2" valign="top">&nbsp;</td>
        </tr>
        <tr height="40">
        	<td valign="top"><font color="#666666"><strong>Phone:</strong></font>&nbsp;</td>
          <td valign="top"><?php echo $data['phone']; ?></td>
        </tr><?php if ($data['url']!=''){ ?>
        <tr height="40">
          <td valign="top"><font color="#666666"><strong>Website:</strong></font>&nbsp;</td>
          <td valign="top"><a href="<?php if ($data['url']!=''){ ?>http://<?php echo str_replace('http://','',$data['url']); ?><?php }?>" target="_blank"><?php echo str_replace('http://','',$data['url']); ?></a></td>
        </tr><?php }?>
      	
        <tr>
        	<td colspan="2" valign="top"><font color="#666666"><strong>About <?php echo $data['title'] ?>:</strong></font></td>
        </tr>
        <tr>
        	<td colspan="2" valign="top"><?php echo $data['description']; ?><br /><br /></td>
        </tr>
        <tr>
          <td colspan="2">
            <a name="fb_share" type="button" href="http://www.facebook.com/sharer.php" style="float:left;">Share</a>
            <script src="http://static.ak.fbcdn.net/connect.php/js/FB.Share" type="text/javascript"></script>
            &nbsp;&nbsp;<a href="http://twitter.com/share" class="twitter-share-button" data-count="horizontal">Tweet</a>
            <script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
            <a style="outline: medium none;margin-left:-15px;margin-top:-5px;" href="mailto:?body=Check%20this%20out:%20<?php echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; ?>" rel="nofollow">
              <img src="common/images/btn_email.gif" border="0" />
            </a>
          </td>
        </tr>
      </tbody>
    </table>
    <iframe class="map_container" width="300" height="220" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="showmap.php?lat=<?=$data['geolat']?>&long=<?=$data['geolon']?>&zoom=<?=$data['geozoom']?>" style="margin-left:20px;"></iframe>
    <div class="adThreeHunderd" style="margin-left:0px;margin-right:20px;">
      <?php m_show_banner('Website Front Page Feature'); ?>
    </div> <!-- adThreeHunderd -->
    <div style="float:left;"><br /><br /><?php m_events_this_week(); ?></div>
    <?php //fprint($data); ?>
	</section> <!-- rightColumn -->
</div> <!-- wrapper -->
<footer>
	<?php m_footer(); ?> <!-- footer -->
</footer>

</body>
</html>