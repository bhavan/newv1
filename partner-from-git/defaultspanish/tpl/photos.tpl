<h2><?php echo $var->site_name; ?> Fotos</h2><br />
<table cellspacing="5">
  <tbody>
    <?php
      $three = 0;
      $first = true;
      echo '<tr>';
      $param['album']['userfolder'] = trim($param['album']['userfolder']);
      if($param['album']['userfolder'] != '') {
        $param['album']['userfolder'] = $param['album']['userfolder'].'/';
      }
      foreach($param['photos'] as &$v) {
        $three++;
        if(($three%3) == 1 && !$first) {
          echo '<tr>';
        }
        $tmp_arr = explode('/', $v['filename']);
        $userfolder = '';
        $filename = $v['filename'];
        if(count($tmp_arr) > 1) {
          $userfolder = $tmp_arr[0].'/';
          $filename = $tmp_arr[1];
        }
        unset($tmp_arr);
        $v['avatar'] = 'partner/'.$_SESSION["partner_folder_name"].'/images/phocagallery/'.$userfolder.'thumbs/phoca_thumb_m_'.$filename;
        $v['image'] = 'partner/'.$_SESSION["partner_folder_name"].'/images/phocagallery/'.$userfolder.'thumbs/phoca_thumb_l_'.$filename;
    ?>
      <td align="center" style="padding-right:30px;">
        <a href="<?php echo $v['image']; ?>" class="pirobox_gall" title="<?php echo $v['title']; ?>" style="text-decoration:none;">
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
      if($three%3 == 1)
        echo '<td>&nbsp;</td><td>&nbsp;</td></tr>';
      else if($three%3 == 2)
        echo '<td>&nbsp;</td></tr>';
    ?>
  </tbody>
</table>
<a href="upload_photo.php" class="button" style="float:left;margin-top:20px;">Cargue su propia foto</a>
