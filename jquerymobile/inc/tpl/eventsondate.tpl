<table width="100%" >
  <thead>
    <th align='left' style='border-bottom:#000 1px dashed; font-size:15px'><strong>Time</strong></th>
    <!--<th align='left' style='background-color:#FFF;border-bottom:#000 1px dashed;'><strong>Street</strong></th>-->
    <th align='right' style='border-bottom:#000 1px dashed;font-size:15px;width:100px'><strong>Summary</strong></th>
  </thead>
  <tbody>
  <?php
  if($data) {
	foreach($data as $v) {
		$veventid = $v['eventid'];
		$veventdetailid = $v['eventdetail_id'];
		$starttime1= date('Y-m-d H:i:s',$v['dtstart']);
		$starttime=substr($starttime1,11,8);

		$endtime1= date('Y-m-d H:i:s',$v['dtend']);
		$endtime=substr($endtime1,11,8);
		
		if($starttime=='00:00:00' && $endtime=='23:59:59') {
			$start_time='All Day Event';
			$end_time='';
		} else if($starttime!='00:00:00') {
			$start_time=date('g:i a',$v['dtstart']);
		}

		$location=$v['location'];
		
		if($endtime!='23:59:59') {
			$end_time= "&nbsp;-&nbsp;".date('g:i a',$v['dtend']);
		} 
		$loc_query="select * from jos_jev_locations where loc_id=".$location;
			$query=mysql_query($loc_query);
			$result_location=mysql_fetch_array($query);
			$location_title=$result_location['title'];
		?>

<tr>
	<td style="font-size:13px">
<div class="record"><a style="color:#000000" href="event_detail.php?loc=<?php echo $location?>"><?php echo $start_time.$end_time; ?></a></div></td>

<td align='right' style="font-size:13px"><?php echo $v['summary'].'&nbsp;@ &nbsp;'.$location_title?></td>
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
