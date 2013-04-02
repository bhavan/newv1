<div id="main" role="main">
<div id="zigzag" style="vertical-align:bottom;"> </div>
<div id="content">

	<ul class="mainList" id="placesList">

    <?php 

	  foreach($param as $v) 
		{
		if(isset($v['avatar']) && trim($v['avatar']) != '') 

	  	{

	  	?>

      <li class="textbox"  style="padding-bottom:0px;">

     <table><tr><td>

 <a href="photos.php?id=<?php echo $v['id']?>">   

 <img class="photo_container" src="<?php echo $v['avatar']; ?>" alt="<?php echo $v['title']; ?>" title="<?php echo $v['title']; ?>" />

</a>

</td><td valign="middle;">

&nbsp;&nbsp;

     <font color="#999999">

     <strong>

     <a href="photos.php?id=<?php echo $v['id']?>"><?php echo $v['title']?></a>

     </strong></font> 

     </td></tr></table>



     </li>

		

		<?php

			}

    }

    ?>

		

	</ul>

	

	

</div>

</div>

<div id="footer">



	<!--&copy; <?php echo date('Y');?> <?php echo $site_name?>  | <a href="mailto:<?php echo $email?>?subject=App Feedback">Contact Us</a>--></div>

<div style='display:none;'><?php echo $pageglobal['googgle_map_api_keys']; ?></div>

