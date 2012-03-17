<!-- <h3 class="tally_aside_h3">Tally Life Today</h3>
<a href=""><img src="common/images/adHolder1.png" alt="sample ad" /></a>
<p>
  Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt 
  ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco 
  laboris nisi ut.
</p>
<a href=""><img src="common/images/adHolder1.png" alt="sample ad" /></a>
<p>
  Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt 
  ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco 
  laboris nisi ut.
</p>
<div id="leftFooter">&nbsp;</div> -->


<div id="topLeftContent">
  <h3><?php echo $var->beach; ?> Hoy</h3>
</div> <!-- topLeftContent -->
<div id="leftColContent">
  <?php echo $data; ?>
  <br />
  <span style="text-align:center">
	<!--<a href="<?php echo $var->iphone?>" target="_blank">
    <img src="common/images/gray-app-store-logo-200.gif" alt="iPhone App Store" title="iPhone App Store" style="margin-bottom:30px;" />
  	</a>-->

  <a href="<?php echo $var->iphone?>" target="_blank">
    <img src="common/images/appleStore.png" alt="iPhone App Store" title="iPhone App Store" style="margin-bottom:15px;" />
  </a>

  <a href="<?php echo $var->android?>" target="_blank">
   <img src="common/images/androidMarket.png" alt="Android Market" title="Android Market" style="margin-bottom:15px;" />
 </a>

  </span>
</div> <!-- leftColContent -->
<div id="leftFooter">&nbsp;</div>