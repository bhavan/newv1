<table width='720' id='large' class='tablesorter enableSearch'>
  <thead>
    <th align='left' style='background-color:#FFF;padding-top:5px;border-bottom:#000 1px dashed;' width="240"><strong><?php echo $title; ?></strong></th>
    <th align='left' style='background-color:#FFF;padding-top:5px;border-bottom:#000 1px dashed;' width="240"><strong>Street</strong></th>
    <th align='left' style='background-color:#FFF;padding-top:5px;border-bottom:#000 1px dashed;width:110px;' width="240"><strong>Phone</strong></th>
  </thead>
  <tbody>
  <?php if($data) { foreach($data as $v) { ?>
    <tr>
    <td><a href='location_details.php?id=<?php echo $v['loc_id']; ?>&loccat=<?php echo $v['loccat']; ?>'><?php echo $v['title'] ?></a></td>
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