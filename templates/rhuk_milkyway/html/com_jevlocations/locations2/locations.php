<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php JHTML::_('behavior.tooltip'); 
	$compparams = JComponentHelper::getParams("com_jevlocations");
	$usecats = $compparams->get("usecats",0);
	global $Itemid;
?>

<form action="<?php echo JRoute::_("index.php?option=com_jevlocations&task=locations.locations&Itemid=$Itemid");?>" method="post" name="adminForm">
<div id="editcell">
	<table class="adminlist">
	<thead>
		<tr>
			<th class="title">
				<?php echo JHTML::_('grid.sort',  'Restaurants', 'loc.title', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
			<th>
				<?php echo JText::_('JEV Location Events'); ?>
			</th>
			<th>
				<?php 
				/* if (!$usecats) echo JHTML::_('grid.sort',  'Telephone', 'loc.city', $this->lists['order_Dir'], $this->lists['order'] ); 
				else echo JHTML::_('grid.sort',  'City', 'cc3.title', $this->lists['order_Dir'], $this->lists['order'] ); */
				echo "Telephone";
				?>
			</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="9">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>
	<tbody>
	<?php
	$params = JComponentHelper::getParams("com_jevlocations");
	$targetid = intval($params->get("targetmenu",0));
	if ($targetid>0){
		$menu = & JSite::getMenu();
		$targetmenu = $menu->getItem($targetid);
		if ($targetmenu->component!="com_jevents"){
			$targetid = JEVHelper::getItemid();
		}
		else {
			$targetid = $targetmenu->id;
		}
	}
	else {
		$targetid = JEVHelper::getItemid();
	}
	$task = $params->get("view","month.calendar");


	$k = 0;
	for ($i=0, $n=count( $this->items ); $i < $n; $i++)
	{
		$row = &$this->items[$i];

		$tmpl = "";
		if (JRequest::getString("tmpl","")=="component"){
			$tmpl = "&tmpl=component";
		}

		$link 	= JRoute::_( 'index.php?option=com_jevlocations&task=locations.detail&loc_id='. $row->loc_id . $tmpl ."&se=1"."&title=".JFilterOutput::stringURLSafe($row->title));

		$eventslink = JRoute::_("index.php?option=com_jevents&task=$task&loclkup_fv=".$row->loc_id."&Itemid=".$targetid);

		// global list
		$global	= $this->_globalHTML($row,$i);

		$ordering = ($this->lists['order'] == 'loc.ordering');
		if ($this->usecats){
			if(isset($row->c3title)){
				$country = $row->c3title;
				$province = $row->c2title;
				$city = $row->c1title;
			}
			else if(isset($row->c2title)){
				$country = $row->c2title;
				$province = $row->c1title;
				$city = false;
			}
			else {
				$country = $row->c1title;
				$province = false;
				$city = false;
			}
		}
		else {
			$country = $row->country;
			$province = $row->state;
			$city = $row->city;
		}
		?>
		<tr class="<?php echo "row$k"; ?>">
			<td>
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'JEV view Location' );?>::<?php echo $this->escape($row->title); ?>">
					<a href="<?php echo $link; ?>">
						<?php echo $this->escape($row->title); ?></a>
				</span>
			</td>
			<td align="center">
				<?php if ($row->hasEvents) {?>
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'JEV View Events At' );?>::<?php echo $this->escape($row->title); ?>">
					<a href="<?php echo $eventslink; ?>">
						<img src="<?php echo JURI::base();?>components/com_jevlocations/assets/images/jevents_event_sml.png" alt="Calendar" style="height:24px;margin:0px;"/>
				</span>
				<?php } ?>
			</td>
			<td align="center">
				<?php echo $this->escape($row->phone); ?>
			</td>
		</tr>
		<?php
		$k = 1 - $k;
	}
	?>
	</tbody>
	</table>
</div>

<?php if ($compparams->get("showmap",0)) echo $this->loadTemplate("map");?>

	<input type="hidden" name="option" value="com_jevlocations" />
	<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
	<input type="hidden" name="task" value="locations.locations" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php if (JRequest::getString("tmpl","")=="component"){ ?>
	<input type="hidden" name="tmpl" value="component" />	
	<?php } ?>
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
