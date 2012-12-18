<table width='720' id='large' class='tablesorter enableSearch'>
  <thead>
    <th align='left' style='background-color:#FFF;padding-top:5px;border-bottom:#000 1px dashed;' width="240"><strong><?php echo $title; ?></strong></th>
    <th align='left' style='background-color:#FFF;padding-top:5px;border-bottom:#000 1px dashed;' width="240"><strong>Street</strong></th>
    <th align='left' style='background-color:#FFF;padding-top:5px;border-bottom:#000 1px dashed;width:110px;' width="240"><strong>Phone</strong></th>
  </thead>
  <tbody>
  <?php if($data) { foreach($data as $v) { ?>
    <tr>
    <td>
      <a href='location_details.php?id=<?php echo $v['loc_id']; ?>&loccat=<?php echo $v['loccat']; ?>'><?php echo $v['title'] ?></a><br/>
      
      <!-- Rating form -->
      <?php if(!empty($_SESSION['tw_user'])) { ?>
      <div id="rating_msg_<?php echo $v['loc_id']; ?>"><a href="#" onclick="showRatingForm(<?php echo $v['loc_id']; ?>);">Rate it</a></div>
      <div id="rating_form_div_<?php echo $v['loc_id']; ?>" style="display:none">
        <form id="rating_form_<?php echo $v['loc_id']; ?>">
          <input type="hidden" name="contentId" value="<?php echo $v['loc_id']; ?>"/>
          <input type="hidden" name="contentType" value="LOCATION"/>
          <input type="text" name="value" size="3"/>
          <input type="button" value="Rate" onclick="tw_rate(<?php echo $v['loc_id']; ?>);"/>
          <input type="button" value="Cancel" onclick="showRating(<?php echo $v['loc_id']; ?>);"/>
        </form>
      </div>
      <div id="rating_error_<?php echo $v['loc_id']; ?>" style="display:none"></div>
      <?php } else { ?>
      <div id="rating_msg_<?php echo $v['loc_id']; ?>"></div>
      <?php } ?>
      <!-- Rating form -->

    </td>
    <td><font color="#999999"><?php echo $v['street'] ?></font></td>
    <td><?php echo $v['phone'] ?></td>
    </tr>
  <?php } ?>
  <?php } else { ?>
    <tr>
      <td colspan="3" style="text-align:center;color:#CCCCCC">
      --- --- --- --- --- --- --- ---
      </td>
    </tr>
  <?php } ?>
  </tbody>
</table>
<?php if($featured) { ?>
<a href="list_locations.php?cat=<?php echo $data[0]['loccat']; ?>" style="float:right;font-weight:bold;">More&nbsp;&raquo;</a>
<?php } ?>


<script>
  <?php if(!empty($_SESSION['tw_user'])) { ?>

  function showRatingForm(contentId) {
    $('#rating_msg_' + contentId).hide();
    $('#rating_error_' + contentId).html('').hide();
    $('#rating_form_div_' + contentId).show();
  }

  function showRating(contentId) {
    $('#rating_msg_' + contentId).show();
    $('#rating_error_' + contentId).html('').hide();
    $('#rating_form_div_' + contentId).hide();
  }

  function showRatingError(contentId, error) {
    $('#rating_msg_' + contentId).hide();
    $('#rating_error_' + contentId).html(error).show();
    $('#rating_form_div_' + contentId).hide();
  }

  function tw_rate(contentId) {
    $.ajax({
        url: "townwizard-db-api/rate.php",
        type: "post",
        data: $('#rating_form_' + contentId).serialize(),        
        success: function(response) {            
            if(response.indexOf('failure') == -1) {
              var r = eval('[' + response + ']')[0];
              showRating(contentId);
              var html = 'Your rating: <a href="#" onclick="showRatingForm(' + r.contentId + ');">' + r.value + '</a>';
              $('#rating_msg_' + contentId).html(html);
              showRating(contentId);
            } else {
              showRatingError(contentId, response);
            }
        }
    });
  }
  <?php } ?>

  function tw_get_ratings() {
    var contentIds = '';

    $('div[id^="rating_msg_"]').each(function(i, d){
      var div = $(d);
      var divId = div.attr('id');
      var lastDash = divId.lastIndexOf('_');
      var contentId = divId.substring(lastDash+1, divId.length);
      contentIds += (contentId + ',');
    });

    contentIds = contentIds.substring(0, contentIds.length-1);    
    
    $.ajax({
      url: "townwizard-db-api/ratings.php?contentIds="+contentIds+"&contentType=LOCATION",
      type: "get",                
      success: function(response) {
        if(response) {          
          var ratings = eval(response);
          for (i = 0; i < ratings.length; i++) { 
            var r = ratings[i];
            <?php if(!empty($_SESSION['tw_user'])) { ?>
            var html = 'Your rating: <a href="#" onclick="showRatingForm(' + r.contentId + ');">' + r.value + '</a>';
            <?php } else { ?>
            var html = 'Average rating: ' + r.value + ' (' + r.count + ')';
            <?php } ?>
            $('#rating_msg_' + r.contentId).html(html);
          }
        }
      }
    });
  }


</script>