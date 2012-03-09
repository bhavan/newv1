<?php 
defined('_JEXEC') or die('Restricted access');

$cfg	 = & JEVConfig::getInstance();

global $mainframe;
$cfg = & JEVConfig::getInstance();
$option = JEV_COM_COMPONENT;
$Itemid = JEVHelper::getItemid();

$compname = JEV_COM_COMPONENT;
$viewname = $this->getViewName();
$viewpath = JURI::root() . "components/$compname/views/".$viewname."/assets";
$viewimages = $viewpath . "/images";

$view =  $this->getViewName();

$data = $this->datamodel->getWeekData($this->year, $this->month, $this->day);

// previous and following month names and links
$followingWeek = $this->datamodel->getFollowingWeek($this->year, $this->month, $this->day);
$precedingWeek = $this->datamodel->getPrecedingWeek($this->year, $this->month, $this->day);

?>

<style>

.element.style  {
color:inherit;
font-weight:normal;
}

</style>

<table class="maintable" align="center" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td  class="previousmonth" align="center" height="22" nowrap="nowrap" valign="middle" width="33%">&nbsp;
<!-- BEGIN previous_month_link_row -->
      	<?php echo "<a href='".$precedingWeek."' title='".JText::_("< Previous Week")."' >"?>
      	<?php echo JText::_("< Previous Week")."</a>";?>
      	

<!-- END previous_month_link_row -->
			</td>
			<td  class="currentmonth" style="background-color: rgb(208, 230, 246);" align="center" height="22" nowrap="nowrap" valign="middle">
				<?php echo  $data['startdate'] . ' - ' . $data['enddate'] ;?>
			</td>
			<td  class="nextmonth" align="center" height="22" nowrap="nowrap" valign="middle"  width="33%">
      	<?php echo "<a href='".$followingWeek."' title='next month' >"?>
      	<?php echo JText::_("Next Week >")."</a>";?>
      	<?php echo "</a>";?>

			</td>
		</tr>
<?php
for( $d = 0; $d < 7; $d++ ){

	$num_events	= count($data['days'][$d]['rows']);
	if ($num_events==0) continue;

	$day_link = JEventsHTML::getDateFormat( $data['days'][$d]['week_year'], $data['days'][$d]['week_month'], $data['days'][$d]['week_day'], 7 )."\n";

	echo '<tr class="tableh2"><td class="tableh2" colspan="3">' . $day_link . '</td></tr>' ;
	echo "<tr>";
	echo '<td class="ev_td_right" colspan="3">' ;

	if ($num_events>0) {
		echo "<ul class='ev_ul'>\n";

		for( $r = 0; $r < $num_events; $r++ ){
			$row = $data['days'][$d]['rows'][$r];

			$listyle = 'style="border-color:'.$row->bgcolor().';"';
			echo "<li class='ev_td_li' $listyle>\n";
			$this->viewEventRowNew ( $row);
			/* echo "&nbsp;::&nbsp;";
			$this->viewEventCatRowNew($row); */
			if (isset($row->_loc_title)){
			echo " @ <a href=index.php?option=com_jevlocations&task=locations.detail&loc_id=" . $row->_loc_id . ">" . $row->_loc_title . "</a>";
			}
			else {
			echo " ". $row->location();
			}
			echo "</li>\n";
		}
		echo "</ul>\n";
	}
	echo '<br></td></tr>' . "\n";
} // end for days

?>

		<tr>
			<td  class="previousmonth" align="center" height="22" nowrap="nowrap" valign="middle" width="33%">&nbsp;
<!-- BEGIN previous_month_link_row -->
      	<?php echo "<a href='".$precedingWeek."' title='".JText::_("< Previous Week")."' >"?>
      	<?php echo JText::_("< Previous Week")."</a>";?>
      	

<!-- END previous_month_link_row -->
			</td>
            			<td  class="currentmonth" style="background-color: rgb(208, 230, 246);" align="center" height="22" nowrap="nowrap" valign="middle">
				<?php echo  $data['startdate'] . ' - ' . $data['enddate'] ;?>
			</td>
			<td  class="nextmonth" align="center" height="22" nowrap="nowrap" valign="middle"  width="33%">
      	<?php echo "<a href='".$followingWeek."' title='next month' >"?>
      	<?php echo JText::_("Next Week >")."</a>";?>
      	<?php echo "</a>";?>

			</td>
		
        </tr>
</table>
<br />