<div class="infoBox">
  <h2>Event Calendar</h2>
  <div style="width:350px;display:block;float:left;margin-bottom:20px;">
    <?php echo $intro; ?>
  </div>
  <div class="adThreeHunderd" style="float:left;">
    <?php m_show_banner('Website Front Page Feature'); ?>
  </div> <!-- adThreeHunderd -->
	<div style="padding-right: 4px; float: left; width: 63px;">
		<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
		<g:plusone size="medium" width: 65px;></g:plusone>
	</div>
	<!--
  		<a name="fb_share" type="button" href="http://www.facebook.com/sharer.php" ></a>
  		<script src="http://static.ak.fbcdn.net/connect.php/js/FB.Share" type="text/javascript"></script>
	-->	

			<!-- FB share button Code Begin -->
			<?php $shareUrl = rawurlencode($_SERVER[SERVER_NAME].$_SERVER['REQUEST_URI']); ?>
			<a expr:share_url='data:post.url' target="_blank" href='http://www.facebook.com/share.php?u=<?php echo $shareUrl ?>' name='fb_share' type='box_count'><img src="common/images/facebook_share_icon.png"/></a>
			<!-- FB share button Code Begin -->
				
  &nbsp;&nbsp;&nbsp;&nbsp;<a href="http://twitter.com/share" class="twitter-share-button" data-count="horizontal">Tweet</a>
  <script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
  <a href="mailto:?body=Check%20this%20out:%20<?php echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; ?>" rel="nofollow">
    <img src="common/images/btn_email.gif" border="0" />
  </a><br /><br />
  <!--a class="btn" href="#" onclick="return false;">list my events&nbsp&raquo;</a-->
</div>