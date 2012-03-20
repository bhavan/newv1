 <h2>Detalles del evento</h2>

<?php if(!$notExists) { ?>

    <table valign="top" style="width:350px;float:left;margin-right:15px;" cellpadding="0" cellspacing="0">
      <tbody>
      	<tr>
        	<td colspan="2" valign="top" style="font-size:18px;color:#666666;font-weight:bold;line-height:20px;"><?php echo stripslashes(urldecode($var->get['title'])) ?><br /><br /></td>
        </tr>
        <tr height="40">
          <td valign="top"><font color="#666666"><strong>Fecha:</strong></font>&nbsp;</td>
          <td valign="top"><?php setlocale(LC_TIME,"spanish");echo ucwords(strftime ('%A, %B %d',strtotime($var->get['date'])));  ?></td>
        </tr>
        <tr height="40">
        	<td valign="top"><font color="#666666"><strong>Tiempo:</strong></font>&nbsp;</td>
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
        	<td valign="top"><font color="#666666"><strong>Ubicaci&#243;n:</strong></font>&nbsp;</td>
          <td valign="top"><?php if($data['location']) echo $data['location']['title']; ?></td>
        </tr>
        <tr height="40">
        	<td valign="top"><font color="#666666"><strong>Direcci&#243;n:</strong></font>&nbsp;</td>
          <td valign="top"><?php if($data['location']) echo $data['location']['street'].', <br />'.$data['location']['city'].', '.$data['location']['state'].' - '.$data['location']['postcode']; ?></td>
        </tr>
        <?php if(trim($data['location']['phone']) != '') { ?>
        <tr height="40">
        	<td valign="top"><font color="#666666"><strong>Tel&#233;fono:</strong></font>&nbsp;</td>
          <td valign="top"><?php if($data['location']) echo $data['location']['phone']; ?></td>
        </tr>
        <?php } ?>
        <?php if(trim($data['location']['url']) != '') { ?>
        <tr height="40">
          <td valign="top"><font color="#666666"><strong>Sitio Web:</strong></font>&nbsp;</td>
          <td valign="top"><a href="<?php if ($data['location']['url']!=''){ ?>http://<?php echo str_replace('http://','',$data['location']['url']); ?><?php }?>" target="_blank"><?php echo str_replace('http://','',$data['location']['url']); ?></a></td>
        </tr>
        <?php } ?>
        <?php if(trim($data['description']) != '') { ?>
        <tr>
        	<td colspan="2" valign="top"><font color="#666666"><strong>Descripci&#243;n del evento:</strong></font><br /><br /></td>
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
			<div style="padding-left: 2px; float: left; width: 63px;">
				<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
				<g:plusone size="medium" width: 65px;></g:plusone>
			</div>
            <a name="fb_share" type="button" href="http://www.facebook.com/sharer.php" style="float:left;">Compartir</a>
            <script src="http://static.ak.fbcdn.net/connect.php/js/FB.Share" type="text/javascript"></script>
            &nbsp;&nbsp;<a href="http://twitter.com/share" class="twitter-share-button" data-count="horizontal">Tweet</a>
            <script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
            <a style="outline: medium none;margin-left:-15px;margin-top:-5px;" href="mailto:?body=Check%20this%20out:%20<?php echo 'http://'.urlencode($_SERVER['HTTP_HOST']).urlencode($_SERVER['REQUEST_URI']); ?>" rel="nofollow">
              <img src="common/images/btn_email.gif" border="0" />
            </a>
          </td>
        </tr>
      </tbody>
    </table>
 
    <iframe class="map_container" width="300" height="220" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="showmap.php?lat=<?=$data['location']['geolat']?>&long=<?=$data['location']['geolon']?>&zoom=<?=$data['location']['geozoom']?>" style="margin-left:10px;"></iframe>

	 <?php }else{ ?>
		<table valign="top" style="width:350px;float:left;margin-right:15px;" cellpadding="0" cellspacing="0">
			<tbody>
			<tr>
				<td colspan="2" valign="top" style="font-size:18px;color:#666666;font-weight:bold;line-height:20px;">Este evento ya no existe<br /><br /></td>
			</tr>
		 </tbody>
	 </table>      

		<?php } ?>


    <div class="adThreeHunderd" style="margin-left:0px;margin-right:15px;">
      <?php m_show_banner('Website Front Page Feature'); ?>
    </div> <!-- adThreeHunderd -->
    <div style="float:left;"><br /><br /><?php m_events_this_week(); ?></div>
    <?php //fprint($data); ?>