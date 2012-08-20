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
			<div style="padding-right: 4px; float: left; width: 63px;">
				<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
				<g:plusone size="medium" width: 65px;></g:plusone>
			</div>
			
 			<script>function fbs_click() {u=location.href;t=document.title;window.open('http://www.facebook.com/sharer.php?u='+encodeURIComponent(u)+'&t='+encodeURIComponent(t),'sharer','toolbar=0,status=0,width=645,height=380');return false;}</script>
 			<a rel="nofollow" href="http://www.facebook.com/share.php?u=<;url>" onclick="return fbs_click()" target="_blank"><img src="common/images/facebook_share_icon.png"></a>
			
            <a href="http://twitter.com/share" class="twitter-share-button" data-count="horizontal">Tweet</a>
            <script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
            <a href="mailto:?body=Check%20this%20out:%20<?php echo 'http://'.urlencode($_SERVER['HTTP_HOST']).urlencode($_SERVER['REQUEST_URI']); ?>" rel="nofollow">
              <img src="common/images/btn_email.gif" border="0" />
            </a>
          </td>
        </tr>
      </tbody>
    </table>
<div id="map_canvas" style="width:300px; height:220px;margin: auto;" class="map_container"></div>
    <div class="adThreeHunderd" style="margin-left:0px;margin-right:20px;">
      <?php m_show_banner('Website Front Page Feature'); ?>
    </div> <!-- adThreeHunderd -->
    <div style="float:left;"><br /><br /><?php m_events_this_week(); ?></div>
    <?php //fprint($data); ?>