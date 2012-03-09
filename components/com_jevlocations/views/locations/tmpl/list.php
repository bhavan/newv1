<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php JHTML::_('behavior.tooltip'); ?>
<div class='jevlocations'>

<form action="index.php?option=com_jevlocations&Itemid=<?php global $Itemid;echo $Itemid;?>" method="post" name="adminForm">
<table>
<tr>
	<td align="left" width="100%">
		<?php echo JText::_( 'Filter' ); ?>:
		<input type="text" name="search" id="jevsearch" value="<?php echo $this->lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
		<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
		<button onclick="document.getElementById('search').value='';this.form.getElementById('filter_catid').value='0';this.form.getElementById('filter_state').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
	</td>
	<td nowrap="nowrap">
		<?php
		if ($this->usecats){
			echo $this->lists['catid'];
		}
		echo $this->lists['state'];
		?>
	</td>
</tr>
</table>
<div id="editcell">
	<table class="adminlist">
	<thead>
		<tr>
			<th width="5">
				<?php echo JText::_( 'NUM' ); ?>
			</th>
			<th width="20">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ); ?>);" />
			</th>
			<th class="title">
				<?php echo JHTML::_('grid.sort',  'Title', 'loc.title', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
			<th width="5%" nowrap="nowrap">
				<?php echo JHTML::_('grid.sort',  'Published', 'loc.published', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
			<th width="5%" nowrap="nowrap">
				<?php echo JHTML::_('grid.sort',  'Global', 'art.global', $this->lists['order_Dir'], $this->lists['order'] ); ?>
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
			<th width="1%" nowrap="nowrap">
				<?php echo JHTML::_('grid.sort',  'ID', 'loc.loc_id', $this->lists['order_Dir'], $this->lists['order'] ); ?>
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

		$tmpl = "";
		if (JRequest::getString("tmpl","")=="component"){
			$tmpl = "&tmpl=component";
		}

		$link 	= JRoute::_( 'index.php?option=com_jevlocations&task=locations.edit&cid[]='. $row->loc_id . $tmpl );

		$checked 	= JHTML::_('grid.checkedout',   $row, $i ,"loc_id");
		$checked = str_replace('"images/' ,'"../administrator/images/',$checked);
		$published 	= JURI::root().( $row->published?'administrator/images/tick.png': 'administrator/images/publish_x.png');

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
				<?php echo $checked; ?>
			</td>
			<td>
				<?php
				if (  JTable::isCheckedOut($this->user->get ('id'), $row->checked_out ) ) {
					echo $this->escape($row->title);
				} else {
				?>
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'Edit Location' );?>::<?php echo $this->escape($row->title); ?>">
					<a href="<?php echo $link; ?>">
						<?php echo $this->escape($row->title); ?></a></span>
				<?php
				}
				?>
			</td>
			<td align="center">
				<img src="<?php echo $published;?>" alt="<?php echo JText::_( 'Published State' );?>" />
			</td>
			<td align="center">
				<?php echo $global;?>
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
			<td align="center">
				<?php echo $row->loc_id; ?>
			</td>
		</tr>
		<?php
		$k = 1 - $k;
	}
	?>
	</tbody>
	</table>
</div>

	<input type="hidden" name="option" value="com_jevlocations" />
	<input type="hidden" name="Itemid" value="<?php global $Itemid; echo $Itemid;?>" />
	<input type="hidden" name="task" value="locations.list" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php if (JRequest::getString("tmpl","")=="component"){ ?>
	<input type="hidden" name="tmpl" value="component" />	
	<?php } ?>
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
</div>