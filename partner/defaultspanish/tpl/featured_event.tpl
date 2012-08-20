<h2>Eventos Destacados</h2>
<div class="infoBox" style="margin-top:-12px;">
  <table cellspacing="10" style="width:350px;float:left;margin-left:-9px;margin-right:10px;"><tbody>
    <tr>
      <td colspan="2">
        <h3><?php echo $data['summary']; ?></h3>
      </td>
    </tr>
    <tr>
      <td><strong>Fecha:</strong></td>
      <td><?php $datearr=explode('-',$data['_date']);setlocale(LC_TIME,"spanish");echo ucwords(strftime ('%A, %B %d',mktime(0,0,0,$datearr[1],$datearr[2],$datearr[0]))); /*echo date("l, F j ",mktime(0,0,0,$datearr[1],$datearr[2],$datearr[0]));*/ ?></td>
    </tr>
    <?php if(!strstr($data['timestart'], '00:00')) { ?>
    <tr>
      <td><strong>Tiempo:</strong></td>
      <td>
      <?php 
        if($data['noendtime']==1) {
          echo $data['timestart'];
        } else {
          echo $data['timestart'].' - '.$data['timeend'];
        }
      ?>
      </td>
    </tr>
    <?php } ?>
    <tr>
      <td><strong>Donde:</strong></td>
      <td><?php echo $data['location']['title']; ?></td>
    </tr>
    <tr>
      <td colspan="2">
        <p><?php echo $data['description']; ?></p>
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <a class="button" href="event_details.php?event_id=<?php echo $data['evdet_id']; ?>&title=<?php echo $data['summary']; ?>&date=<?php echo $data['_date']; ?>&rp_id=<?php echo $data['rp_id']; ?>" style="float:left;" >Más Información</a><br /><br /><br /><br /><br />
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
        </a>
      </td>
    </tr>
  </tbody></table>
  <div class="adThreeHunderd" style="margin-top:12px;">
    <?php m_show_banner('Website Front Page Feature'); ?>
  </div> <!-- adThreeHunderd -->
</div>