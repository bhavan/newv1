<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php JHTML::_('behavior.tooltip'); 
	$compparams = JComponentHelper::getParams("com_jevpeople");
	global $Itemid;
?>

<form action="<?php echo JRoute::_("index.php?option=com_jevpeople&task=people.people&Itemid=$Itemid");?>" method="post" name="adminForm">
<table>
<tr>
	<td align="left" width="100%">
		<?php echo JText::_( 'Filter' ); ?>:
		<input type="text" name="search" id="search" value="<?php echo $this->lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
		<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
		<button onclick="document.getElementById('search').value='';this.form.getElementById('filter_catid').value='0';this.form.getElementById('filter_state').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
	</td>
	<td nowrap="nowrap">
		<?php
		if ($compparams->get("type","")=="") echo $this->lists['typefilter'];
		if ($compparams->get("jevpcat","")=="") echo $this->lists['catid'];
		?>
	</td>
</tr>
</table>
<div id="editcell">
	<table class="adminlist">
	<thead>
		<tr>
			<th class="title">
				<?php echo JHTML::_('grid.sort',  'PERSON', 'pers.title', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
			<th>
				<?php echo JText::_('JEV PEOPLE Events'); ?>
			</th>
			<th>
				<?php 
				echo JHTML::_('grid.sort',  'JEV CATEGORY 1', 'catname0', $this->lists['order_Dir'], $this->lists['order'] );
				?>
			</th>
			<th>
				<?php 
				echo JHTML::_('grid.sort',  'JEV CATEGORY 2', 'catname1', $this->lists['order_Dir'], $this->lists['order'] ); 
				?>
			</th>
			<th>
				<?php 
				echo JHTML::_('grid.sort',  'JEV CATEGORY 3', 'catname2', $this->lists['order_Dir'], $this->lists['order'] ); 
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
	$params = JComponentHelper::getParams("com_jevpeople");
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

		$link 	= JRoute::_( 'index.php?option=com_jevpeople&task=people.detail&pers_id='. $row->pers_id . $tmpl ."&se=1"."&title=".JFilterOutput::stringURLSafe($row->title));

		$eventslink = JRoute::_("index.php?option=com_jevents&task=$task&peoplelkup_fv=".$row->pers_id."&Itemid=".$targetid);

		// global list
		$global	= $this->_globalHTML($row,$i);

		$ordering = ($this->lists['order'] == 'pers.ordering');
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
						<img src="<?php echo JURI::base();?>components/com_jevpeople/assets/images/jevents_event_sml.png" alt="Calendar" style="height:24px;margin:0px;"/>
				</span>
				<?php } ?>
			</td>
			<td>
				<?php echo $this->escape($row->catname0); ?>
			</td>
			<td>
				<?php echo $this->escape($row->catname1); ?>
			</td>
			<td>
				<?php echo $this->escape($row->catname2); ?>
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

	<input type="hidden" name="option" value="com_jevpeople" />
	<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
	<input type="hidden" name="task" value="people.people" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php if (JRequest::getString("tmpl","")=="component"){ ?>
	<input type="hidden" name="tmpl" value="component" />	
	<?php } ?>
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
