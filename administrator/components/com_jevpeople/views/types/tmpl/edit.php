<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php JHTML::_('behavior.tooltip'); 

	$editor = &JFactory::getEditor();
?>

<script language="javascript" type="text/javascript">
	function submitbutton(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'types.overview' || pressbutton == 'types.cancel') {
			submitform( pressbutton );
			return;
		}

		// do field validation
		if (form.title.value == ""){
			alert( "<?php echo JText::_( 'Person must have a title', true ); ?>" );
		} 
		else {
			submitform( pressbutton );
			return;			
		}
	}
</script>
<style type="text/css">
	table.paramlist td.paramlist_key {
		width: 92px;
		text-align: left;
		height: 30px;
	}
</style>

<form action="index.php" method="post" name="adminForm" id="adminForm">
<div class="col">

		<table class="admintable">
		<tr>
			<td width="100" align="right" class="key">
				<label for="title">
					<?php echo JText::_( 'Title' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="title" id="title" size="60" maxlength="250" value="<?php echo $this->type->title;?>" />
			</td>
		</tr>
		<tr>
			<td width="100" align="right" class="key">
				<label for="title">
					<?php echo JText::_( 'Multiple per event' ); ?>:
				</label>
			</td>
			<td>
				<?php echo $this->multiple;?>
			</td>
		</tr>
		<!--
		<tr>
			<td width="100" align="right" class="key">
				<label for="title">
					<?php echo JText::_( 'Max Numbers per event' ); ?>:
				</label>
			</td>
			<td>
				<?php echo $this->maxnumber;?>
			</td>
		</tr>
		//-->
		<tr>
			<td width="100" align="right" class="key">
				<label for="title">
					<?php echo JText::_( 'Cateogries per person' ); ?>:
				</label>
			</td>
			<td>
				<?php echo $this->multicat;?>
			</td>
		</tr>
		<tr>
			<td width="100" align="right" class="key">
				<label for="title">
					<?php echo JText::_( 'Show Address' ); ?>:
				</label>
			</td>
			<td>
				<?php echo $this->showaddress;?>
			</td>
		</tr>
	</table>

	<script type="text/javascript">
			function allselections(id) {
				var e = document.getElementById(id);
					e.disabled = true;
				var i = 0;
				var n = e.options.length;
				for (i = 0; i < n; i++) {
					e.options[i].disabled = true;
					e.options[i].selected = true;
				}
			}
			function enableselections(id) {
				var e = document.getElementById(id);
					e.disabled = false;
				var i = 0;
				var n = e.options.length;
				for (i = 0; i < n; i++) {
					e.options[i].disabled = false;
				}
			}
		</script>
	<table class="admintable">
		<tr>
			<td width="50%">
				<fieldset class="adminform">
					<legend><?php echo JText::_( 'JEV APPLICABLE CATEGORIES' ); ?></legend>
					<table class="admintable" cellspacing="1">
					  <tr>
					    <td valign="top" class="key"><?php echo JText::_( 'JEV Categories' ); ?>: </td>
					    <td><?php if ($this->catvalues == 'all' || $this->catvalues == '') { ?>
					      <label for="categories-all">
					        <input id="categories-all" type="radio" name="categories" value="all" onclick="allselections('categories');" checked="checked" />
					        <?php echo JText::_( 'JEV All' ); ?></label>
					      <label for="categories-select">
					        <input id="categories-select" type="radio" name="categories" value="select" onclick="enableselections('categories');" />
					        <?php echo JText::_( 'JEV Select From List' ); ?></label>
					      <?php }
					      else { ?>
					      <label for="categories-all">
					        <input id="categories-all" type="radio" name="categories" value="all" onclick="allselections('categories');" />
					        <?php echo JText::_( 'JEV All' ); ?></label>
					      <label for="categories-select">
					        <input id="categories-select" type="radio" name="categories" value="select" onclick="enableselections('categories');" checked="checked" />
					        <?php echo JText::_( 'JEV Select From List' ); ?></label>
					      <?php } ?></td>
					  </tr>
					  <tr>
					    <td class="paramlist_key" width="40%"><span class="editlinktip">
					      <label for="categories" id="categories-lbl"><?php echo JText::_('JEV Categories selection');?></label>
					      </span></td>
					    <td><?php echo $this->lists['categories'];?></td>
					  </tr>
					</table>
					<?php if ($this->catvalues == 'all'  || $this->catvalues == '') { ?>
					<script type="text/javascript">allselections('categories');</script>
					<?php } ?>
				</fieldset>
			</td>
			<td width="50%" style="display:none;">

				<fieldset class="adminform">
					<legend><?php echo JText::_( 'JEV APPLICABLE CALENDARS' ); ?></legend>
					<table class="admintable" cellspacing="1">
					  <tr>
					    <td valign="top" class="key"><?php echo JText::_( 'JEV Calendars' ); ?>: </td>
					    <td><?php if ($this->calvalues == 'all' || $this->calvalues == '') { ?>
					      <label for="calendars-all">
					        <input id="calendars-all" type="radio" name="calendars" value="all" onclick="allselections('calendars');" checked="checked" />
					        <?php echo JText::_( 'JEV All' ); ?></label>
					      <label for="calendars-select">
					        <input id="calendars-select" type="radio" name="calendars" value="select" onclick="enableselections('calendars');" />
					        <?php echo JText::_( 'JEV Select From List' ); ?></label>
					      <?php }
					      else { ?>
					      <label for="calendars-all">
					        <input id="calendars-all" type="radio" name="calendars" value="all" onclick="allselections('calendars');" />
					        <?php echo JText::_( 'JEV All' ); ?></label>
					      <label for="calendars-select">
					        <input id="calendars-select" type="radio" name="calendars" value="select" onclick="enableselections('calendars');" checked="checked" />
					        <?php echo JText::_( 'JEV Select From List' ); ?></label>
					      <?php } ?></td>
					  </tr>
					  <tr>
					    <td class="paramlist_key" width="40%"><span class="editlinktip">
					      <label for="calendars" id="calendars-lbl"><?php echo JText::_('JEV Calendars selection');?></label>
					      </span></td>
					    <td><?php echo $this->lists['calendars'];?></td>
					  </tr>
					</table>
					<?php if ($this->calvalues == 'all'|| $this->calvalues == '') { ?>
					<script type="text/javascript">allselections('calendars');</script>
					<?php }  ?>
				</fieldset>
			</td>
		</tr>
	</table>
	</div>


	<input type="hidden" name="option" value="com_jevpeople" />
	<input type="hidden" name="cid[]" value="<?php echo $this->type->type_id; ?>" />
	<input type="hidden" name="returntask" value="<?php echo $this->returntask;?>" />
	<input type="hidden" name="task" value="types.edit" />
	<?php if (JRequest::getString("tmpl","")=="component"){ ?>
	<input type="hidden" name="tmpl" value="component" />	
	<?php } ?>
	<?php echo JHTML::_( 'form.token' ); ?>
</form>