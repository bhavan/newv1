<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php JHTML::_('behavior.tooltip'); ?>

<div class='jevlocations'>
<form action="index.php" method="post" name="adminForm">
<table>
<tr>
	<td align="left">
		<?php echo JText::_( 'Filter' ); ?>:
		<input type="text" name="search" id="search" value="<?php echo $this->lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
		<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>

	</td>
	<td nowrap="nowrap">
		<?php
		if ($this->usecats){
			echo $this->lists['catid'];
		}
		?>
	</td>
	<td nowrap="nowrap">
		<?php
		echo $this->lists['loctype'];
		?>
	</td>
	<td nowrap="nowrap">
		<?php
		echo $this->lists['loccat'];
		?>
	</td>
	<td nowrap="nowrap">
		<button onclick="document.getElementById('search').value='';this.form.getElementById('filter_catid').value='0';this.form.getElementById('filter_loctype').value='0';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
	</td>
</tr>
</table>
	<input type="hidden" name="option" value="com_jevlocations" />
	<input type="hidden" name="task" value="locations.select" />
	<input type="hidden" name="returntask" value="locations.select" />
	<input type="hidden" name="tmpl" value="component" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
<div id="editcell">
	<table class="adminlist">
	<thead>
		<tr>
			<th width="5">
				<?php echo JText::_( 'NUM' ); ?>
			</th>
			<th class="title">
				<?php echo JHTML::_('grid.sort',  'Title (click to select)', 'loc.title', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
			<th width="5%">
				<?php echo JHTML::_('grid.sort',  'Country', ($this->usecats?'c3title':'loc.country'), $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
			<th width="5%">
				<?php echo JHTML::_('grid.sort',  'State', ($this->usecats?'c2title':'loc.state'), $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
			<th width="5%">
				<?php echo JHTML::_('grid.sort',  'City', ($this->usecats?'c1title':'loc.city'), $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
			<th width="5%">
				<?php echo JHTML::_('grid.sort',  'Postcode', 'loc.postcode', $this->lists['order_Dir'], $this->lists['order'] ); ?>
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
	$k = 0;
	for ($i=0, $n=count( $this->items ); $i < $n; $i++)
	{
		$row = &$this->items[$i];

		$link 	= JRoute::_( 'index.php?option=com_jevlocations&task=locations.setlocation&cid[]='. $row->loc_id );

		$checked 	= JHTML::_('grid.checkedout',   $row, $i ,"loc_id");
		$checked = str_replace('"images/' ,'"../administrator/images/',$checked);
		$published 	= $row->published?'/administrator/images/tick.png': '/administrator/images/publish_x.png';

		// global list
		$global	= $this->_globalHTML($row,$i);

		$ordering = ($this->lists['order'] == 'loc.title');

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

			$row->country_link 	= JRoute::_( 'index.php?option=com_categories&section=com_jevlocationss&task=edit&type=other&cid[]='. $country );
			if ($province){
				$row->province_link 	= JRoute::_( 'index.php?option=com_categories&section=com_jevlocationss&task=edit&type=other&cid[]='. $province);
			}
			if ($city){
				$row->city_link 	= JRoute::_( 'index.php?option=com_categories&section=com_jevlocationss&task=edit&type=other&cid[]='. $city);
			}
		}
		?>
		<tr class="<?php echo "row$k"; ?>">
			<td>
				<?php echo $this->pagination->getRowOffset( $i ); ?>
			</td>
			<td>
				<?php
				if (  JTable::isCheckedOut($this->user->get ('id'), $row->checked_out ) ) {
					echo $this->escape($row->title);
				} else {
				?>
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'Select Location' );?>::<?php echo $this->escape($row->title); ?>">
					<a href="javascript:void()" onclick="return selectThisLocation(<?php echo $row->loc_id;?>,this);"><?php echo $this->escape($row->title); ?></a></span>
				<?php
				}
				?>
			</td>
			<?php
			if ($this->usecats){
			?>
			<td>
				<?php echo $this->escape($country); ?>
			</td>
			<td>
				<?php echo $this->escape($province); ?>
			</td>
			<td>
				<?php echo $this->escape($city); ?>
			</td>
			<?php
			}
			else {				
			?>
			<td>
				<?php echo $this->escape($row->country); ?>
			</td>
			<td>
				<?php echo $this->escape($row->state); ?>
			</td>
			<td>
				<?php echo $this->escape($row->city); ?>
			</td>
			<?php
			}
			?>
			<td>
				<?php echo $this->escape($row->postcode); ?>
			</td>
		</tr>
		<?php
		$k = 1 - $k;
	}
	?>
	</tbody>
	</table>
</div>

</form>

</div>