<div id="topbar">
<div id="title">Galleries</div>
</div>
<div id="content">
	

	<ul class="pageitem">
		
    <?php 
	  foreach($param as $v) 
	  {
	  	if(isset($v['avatar']) && trim($v['avatar']) != '') 
	  	{
	  	?>
      <li class="textbox"  style="padding-bottom:0px;">
     <table><tr><td>
 <a href="photos.php?id=<?=$v['id']?>">   
 <img class="photo_container" src="<?php echo $v['avatar']; ?>" alt="<?php echo $v['title']; ?>" title="<?php echo $v['title']; ?>" />
</a>
</td><td valign="middle;">
&nbsp;&nbsp;
     <font color="#999999">
     <strong>
     <a href="photos.php?id=<?=$v['id']?>"><?=$v['title']?></a>
     </strong></font> 
     </td></tr></table>

     </li>
		
		<?php
			}
    }
    ?>
		
	</ul>
	
	
	
	

	
	
	
</div>

<div id="footer">

	&copy; <?=date('Y');?> <?=$site_name?> | <a href="mailto:<?=$email?>?subject=Feedback">Contact Us</a></div></div>
<div style='display:none;'><?php echo $pageglobal['googgle_map_api_keys']; ?></div>