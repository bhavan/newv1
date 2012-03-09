<h2><?php echo $var->site_name; ?> Photos</h2>
<table cellspacing="20" style="float:left;margin-bottom:30px;">
  <tbody>
    <?php
      $three = 0;
      $first = true;
      echo '<tr>';
      foreach($param as $v) {
        if(isset($v['avatar']) && trim($v['avatar']) != '') {
          $three++;
          if(($three%3) == 1 && !$first) {
            echo '<tr>';
          }
          //fprint($v);
    ?>
      <td align="center">
        <a href="photos.php?album_id=<?php echo $v['id']; ?>" style="text-decoration:none;">
          <img class="photo_container" src="<?php echo $v['avatar']; ?>" alt="<?php echo $v['title']; ?>" title="<?php echo $v['title']; ?>" /><br />
          <span style="font-size:14px;"><?php echo $v['title']; ?></span>
        </a>
      </td>
    <?php
          if(($three%3) == 0 && $three != 0) {
            echo '</tr>';
          }
          $first = false;
        }
      }
      if($three%3 == 1)
        echo '<td>&nbsp;</td><td>&nbsp;</td></tr>';
      else if($three%3 == 2)
        echo '<td>&nbsp;</td></tr>';
    ?>
  </tbody>
</table>
<br clear="all" />
<a href="upload_photo.php" class="button" style="float:left;margin-top:20px;margin-bottom:20px;">Upload your own photo</a>

<!--div class="adThreeHunderd" style="margin-top:-20px;">
  <?php m_show_banner('Website Front Page Feature'); ?>
</div--> <!-- adThreeHunderd -->
