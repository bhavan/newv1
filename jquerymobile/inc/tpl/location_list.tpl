<table width="65%" >
  <thead>
    <th align='left' style='border-bottom:#000 1px dashed; font-size:15px'><strong><?php echo $title; ?></strong></th>
    <!--<th align='left' style='background-color:#FFF;border-bottom:#000 1px dashed;'><strong>Street</strong></th>-->
    <th align='right' style='border-bottom:#000 1px dashed;font-size:15px;width:100px'><strong>Phone</strong></th>
  </thead>
  <tbody>
  <?php
  if($data) {
	foreach($data as $v) {
		$vtitle = $v['title'];
		
		?>
	<tr>
		<td  style="font-size:13px"><div class="record"><a style="color:#000000" href="location_detail.php?id=<?php echo $v['loc_id']; ?>&loccat=<?php echo $v['loccat']; ?>"><?php echo $vtitle; ?> </a></div></td>
		
		<!--<td><?php //echo $v['street'] ?></td>-->
		<td align='right' style="font-size:13px;font-weight:bold"><?php echo $v['phone'] ?></td>
	</tr>
	<?php } ?>
  <?php } else { ?>
	<tr>
		<td colspan="2" style="text-align:center;color:#000000">
		--- --- --- --- --- --- --- --- --- --- --- --- --- ---</td>
	</tr>
  <?php } ?>
  </tbody>
</table>
<?php if($featured) { ?>
<a href="list_locations.php?cat=<?php echo $data[0]['loccat']; ?>" style="float:right;font-weight:bold;">More&nbsp;&raquo;</a>
<?php } ?>
