<h2>Tallylife On Film</h2>
<table cellspacing="20" style="float:left;margin-bottom:30px;">
  <tbody>
    <?php
      $two = 0;
      $first = true;
      echo '<tr>';
      foreach($param as $v) {
        $two++;
        if(($two%2) == 1 && !$first) {
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
        if(($two%2) == 0 && $two != 0) {
          echo '</tr>';
        }
        $first = false;
      }
      if($two%2 == 1)
        echo '<td>&nbsp;</td></tr>';
    ?>
  </tbody>
</table>
<!--div class="adThreeHunderd" style="margin-top:-20px;">
  <?php m_show_banner('Event'); ?>
</div--> <!-- adThreeHunderd -->
