<div class="infoBox">
  <h2>CALENDARIO DE EVENTOS</h2>
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
 	<script>function fbs_click() {u=location.href;t=document.title;window.open('http://www.facebook.com/sharer.php?u='+encodeURIComponent(u)+'&t='+encodeURIComponent(t),'sharer','toolbar=0,status=0,width=645,height=380');return false;}</script>
 	<a rel="nofollow" href="http://www.facebook.com/share.php?u=<;url>" onclick="return fbs_click()" target="_blank"><img src="common/images/facebook_share_icon.png"></a>
  <a href="http://twitter.com/share" class="twitter-share-button" data-count="horizontal">Tweet</a>
  <script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
  <a href="mailto:?body=Te recomiendo esta p&#225;gina:<?php echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; ?>" rel="nofollow">
    <img src="common/images/btn_email.gif" border="0" />
  </a><br /><br />
  <!--a class="btn" href="#" onclick="return false;">list my events&nbsp&raquo;</a-->
</div>