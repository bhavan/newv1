<h2>Esta semana en la <?php echo $var->beach; ?></h2>
<table style="margin-top:-14px;">
  <tbody>
    <?php
    $start_date = date("Y-m-d");
    $check_date = $start_date;
    $end_date = date("Y-m-d", strtotime ("+6 day", strtotime($check_date)));
    $i = 0;
    while ($check_date != $end_date) {
      $date = explode('-', $check_date);
      $data = $datamodel->getDayData($date[0], $date[1], $date[2]);
      $print_date = false;
      for($i=0;$i<24;$i++) {
        if(count($data['hours'][$i]['events']) > 0) {
          $print_date = true;
          break;
        }
      }
    ?>
    <tr>
        <td colspan="2">
          <?php if($print_date) { ?>
          <br /><strong><font style="font-size: 14px; color: rgb(102, 102, 102);"><?php setlocale(LC_TIME,"spanish");echo ucwords(strftime ('%A, %B %d',strtotime($check_date))); /*echo date("l, F d", strtotime($check_date));*/ ?></font></strong><br />
          <?php } ?>
        </td>
      </tr>
      
    <?php
     //fprint($data[hours][timeless]); _x();
      
        if(count($data['hours'][timeless]['events']) > 0) { for ($j=0;$j<count($data[hours][timeless]['events']);$j++) {
          $event = (array) $data['hours'][timeless]['events'][$j];
          if($event['_hup']>12) {
            $time_start = sprintf("%02d", (intval($event['_hup'])-12)).':'.sprintf("%02d", $event['_minup']).'pm';
          } else {
            $time_start = sprintf("%02d", $event['_hup']).':'.sprintf("%02d", $event['_minup']).'am';
          }
          if($event['_hdn']>12) {
            $time_end = sprintf("%02d", (intval($event['_hdn'])-12)).':'.sprintf("%02d", $event['_mindn']).'pm';
          } else {
            $time_end = sprintf("%02d", $event['_hdn']).':'.sprintf("%02d", $event['_mindn']).'am';
          }
          $timeless = false;
          //if($event['_hup'] == '0' && $event['_hdn'] == '23') {
            if($event['_noendtime'] == '1') {
            $timeless = true;
          }
    ?>
          <tr>
            
            <td width="120">
             Todo el día
            </td>
            
            <td><a href="event_details.php?event_id=<?php echo $event['_eventdetail_id']; ?>&title=<?php echo $event['_title']; ?>&date=<?php echo $check_date; ?>&rp_id=<?php echo $event['_rp_id']; ?>"><?php echo $event['_title']; ?></a>
            <?php if(!strstr($var->request_uri, 'leach.php')) { ?>&nbsp;&nbsp;@&nbsp;&nbsp;<font color="#999999"><?php echo $event['_location']; ?></font><?php } ?>
            <br /></td>
          </tr>
    <?php
        }
      }
     // fprint($data[hours][timeless]); _x();
      for($i=0;$i<24;$i++) {
        if(count($data['hours'][$i]['events']) > 0) { for ($j=0;$j<count($data['hours'][$i]['events']);$j++) {
          $event = (array) $data['hours'][$i]['events'][$j];
         if($event['_hup']==12) {
           $time_start = sprintf("%02d", (intval($event['_hup'])-0)).':'.sprintf("%02d", $event['_minup']).'pm';
           }
          
          else if($event['_hup']>12) {
            $time_start = sprintf("%02d", (intval($event['_hup'])-12)).':'.sprintf("%02d", $event['_minup']).'pm';
          } else {
            $time_start = sprintf("%02d", $event['_hup']).':'.sprintf("%02d", $event['_minup']).'am';
          }

         if($event['_hdn']==12) {
           $time_end = sprintf("%02d", (intval($event['_hdn'])-0)).':'.sprintf("%02d", $event['_mindn']).'pm';
           }
          else if($event['_hdn']>12) {
            $time_end = sprintf("%02d", (intval($event['_hdn'])-12)).':'.sprintf("%02d", $event['_mindn']).'pm';
          } else {
            $time_end = sprintf("%02d", $event['_hdn']).':'.sprintf("%02d", $event['_mindn']).'am';
          }
          $timeless = false;
          //if($event['_hup'] == '0' && $event['_hdn'] == '23') {
            if($event['_noendtime'] == '1') {
            $timeless = true;
          }
    ?>
          <tr>
            <?php if(!$timeless) { ?>
            <td width="120">
              <?php if($event['_hup'] > $event['_hdn']) echo $time_start; else echo $time_start.' - '.$time_end; ?>
            </td>
            <?php } else { ?>
            <td width="120"><?php if($event['_hup'] > $event['_hdn']) echo $time_start; else echo $time_start ?></td>
            <?php } ?>
            <td><a href="event_details.php?event_id=<?php echo $event['_eventdetail_id']; ?>&title=<?php echo $event['_title']; ?>&date=<?php echo $check_date; ?>&rp_id=<?php echo $event['_rp_id']; ?>"><?php echo $event['_title']; ?></a>
            <?php if(!strstr($var->request_uri, 'leach.php')) { ?>&nbsp;&nbsp;@&nbsp;&nbsp;<font color="#999999"><?php echo $event['_location']; ?></font><?php } ?>
            <br /></td>
          </tr>
    <?php
        }}
      }
       
      $check_date = date("Y-m-d", strtotime("+1 day", strtotime($check_date)));
      if($i > 31) { die ('Error!'); }
      $i++;
    }
    ?>
  </tbody>
</table>
<br />
<a class="button" href="events.php" style="float:left; margin-right:10px;">más eventos</a>
<!--<a class="button" style="float:left;" href="/event_submit.php?option=com_jevents&view=icalevent&task=icalevent.edit&Itemid=111&tmpl=component">Send Us Your Events</a>-->

<a class="button" style="float:left;" href="events_submit.php">Envíenos sus eventos</a>
<!--a class="btn" href="#" onclick="return false;">list my events&nbsp&raquo;</a-->
