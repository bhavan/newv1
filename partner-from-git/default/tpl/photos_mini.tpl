<div id="photos">
  <div id="photo">
    <?php
   
    $cat_folder = trim($cat_folder);
    if($cat_folder != '') {
      $cat_folder = $cat_folder.'/';
    }  
    
    $first = true; foreach($param as $v) {
     $tmp_arr = explode('/', $v['filename']);
        $userfolder = '';
        $filename = $v['filename'];
        if(count($tmp_arr) > 1) {
          $userfolder = $tmp_arr[0].'/';
          $filename = $tmp_arr[1];
        }
        unset($tmp_arr);
       // $v['avatar'] = '/images/phocagallery/'.$userfolder.'thumbs/phoca_thumb_m_'.$filename;
      //  $v['image'] = '/images/phocagallery/'.$userfolder.'thumbs/phoca_thumb_l_'.$filename;
        
        $v['avatar'] = 'partner/'.$_SESSION["partner_folder_name"].'/images/phocagallery/'.$userfolder.'thumbs/phoca_thumb_m_'.$filename;
        $v['image'] = 'partner/'.$_SESSION["partner_folder_name"].'/images/phocagallery/'.$userfolder.'thumbs/phoca_thumb_l_'.$filename;
    ?>
    <div id="photo_<?php echo $v['id']; ?>" class="photo_box" style="<?php if($first) echo ''; else echo 'display:none;' ?>">
      <div class="img_container">
        <img src="<?php echo $v['image']; ?>" alt="photo" />
      </div>
      <div class="img_text">
        <strong><?php echo $v['title']; ?></strong>
        
      </div>
    </div>
    <?php $first = false; } ?>
  </div> <!-- photo -->
  <h3><?php echo $var->site_name; ?> Photos</h3>
  <h4>Smile <?php echo $var->site_name; ?>!</h4>
  <em></em>
  <p>
    Use the free <a href="<?php echo $var->iphone?>"><?php echo $var->site_name; ?> iPhone app</a> to share live photos of faces, places and events in <?php echo $var->beach; ?>! Every day, we feature the best pics right here.
  </p>
  <a href="#" id="prevPhoto">PREV</a>
  <a href="#" id="randomPhoto">RANDOM</a>
  <a href="#" id="nextPhoto">NEXT</a>
  <div class="adTwoThirtyFour">
    <?php m_show_banner('Website Photo Box 1'); ?>
  </div> <!-- adTwoThirtyFour -->
  <div class="adTwoThirtyFourSecond">
    <?php m_show_banner('Website Photo Box 2'); ?>
  </div> <!-- adTwoThirtyFour -->
</div>