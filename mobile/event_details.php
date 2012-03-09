<?php

require('jevents.php');
global $var;
include_once('./inc/var.php');
include_once($var->inc_path.'base.php');
_init();
//date_default_timezone_set("Asia/Calcutta");

if(isset($var->get['event_id'])) {
  $data = db_fetch("select jjv.*, DATE_FORMAT(jjr.startrepeat,'%h:%i %p') as timestart, DATE_FORMAT(jjr.endrepeat,'%h:%i %p') as timeend from `jos_jevents_vevdetail` jjv, `jos_jevents_repetition` jjr where jjv.evdet_id = jjr.eventdetail_id and jjv.evdet_id = ".$var->get['event_id']." and jjr.rp_id = ".$var->get['rp_id']);
	$data['location'] = db_fetch("select title, street, postcode, city, state, phone, geozoom, geolon, geolat, url from `jos_jev_locations` where `loc_id` = ".$data['location']);
  $data['q'] = str_replace(' ', '+', ($data['location']['title'].' '.$data['location']['street'].' '.$data['location']['city'].' '.$data['location']['state'].' '.$data['location']['postcode']));
} else {
  redirect($var->http_referer);
}

//fprint($data); _x();

?>

<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $var->site_name.' | '.(isset($var->get['title'])?'Event | '.$var->get['title']:'Event Details'); ?></title>
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
    <h2>Event Details</h2>
    <table valign="top" style="width:350px;float:left;margin-right:15px;" cellpadding="0" cellspacing="0">
      <tbody>
      	<tr>
        	<td colspan="2" valign="top" style="font-size:18px;color:#666666;font-weight:bold;line-height:20px;"><?php echo $var->get['title'] ?><br /><br /></td>
        </tr>
        <tr height="40">
          <td valign="top"><font color="#666666"><strong>Date:</strong></font>&nbsp;</td>
          <td valign="top"><?php echo date("l, F d", strtotime($var->get['date'])); ?></td>
        </tr>
        <tr height="40">
        	<td valign="top"><font color="#666666"><strong>Time:</strong></font>&nbsp;</td>
          <td valign="top">
            <?php
			
              if(strstr($data['timestart'], '12:00 AM') && strstr($data['timeend'], '11:59 PM'))
                echo 'All Day Event';
              else if ($data['noendtime']==1)
			  echo $data['timestart'];
			  else
                echo $data['timestart'].' - '.$data['timeend'];
            ?>
          </td>
        </tr>
        <tr height="40">
        	<td valign="top"><font color="#666666"><strong>Location:</strong></font>&nbsp;</td>
          <td valign="top"><?php if($data['location']) echo $data['location']['title']; ?></td>
        </tr>
        <tr height="40">
        	<td valign="top"><font color="#666666"><strong>Address:</strong></font>&nbsp;</td>
          <td valign="top"><?php if($data['location']) echo $data['location']['street'].', <br />'.$data['location']['city'].', '.$data['location']['state'].' - '.$data['location']['postcode']; ?></td>
        </tr>
        <?php if(trim($data['location']['phone']) != '') { ?>
        <tr height="40">
        	<td valign="top"><font color="#666666"><strong>Phone:</strong></font>&nbsp;</td>
          <td valign="top"><?php if($data['location']) echo $data['location']['phone']; ?></td>
        </tr>
        <?php } ?>
        <?php if(trim($data['location']['url']) != '') { ?>
        <tr height="40">
          <td valign="top"><font color="#666666"><strong>Website:</strong></font>&nbsp;</td>
          <td valign="top"><a href="<?php if ($data['location']['url']!=''){ ?>http://<?php echo str_replace('http://','',$data['location']['url']); ?><?php }?>" target="_blank"><?php echo str_replace('http://','',$data['location']['url']); ?></a></td>
        </tr>
        <?php } ?>
        <?php if(trim($data['description']) != '') { ?>
        <tr>
        	<td colspan="2" valign="top"><font color="#666666"><strong>Event Description:</strong></font><br /><br /></td>
        </tr>
        <tr>
        	<td colspan="2" valign="top"><?php echo $data['description']; ?><br /><br /></td>
        </tr>
        <?php } ?>
        <tr>
        	<td colspan="2" valign="top">&nbsp;</td>
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
 
    <iframe class="map_container" width="300" height="220" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="showmap.php?lat=<?=$data['location']['geolat']?>&long=<?=$data['location']['geolon']?>&zoom=<?=$data['location']['geozoom']?>" style="margin-left:10px;"></iframe>
    <div class="adThreeHunderd" style="margin-left:0px;margin-right:15px;">
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