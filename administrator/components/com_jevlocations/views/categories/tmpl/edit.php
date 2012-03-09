<?php defined('_JEXEC') or die('Restricted access'); 

$editor =& JFactory::getEditor();

?>		
<div id="jevents">
<form action="index.php" method="post" name="adminForm" >
<table width="90%" border="0" cellpadding="2" cellspacing="2" class="adminform">
<tr>
<td>
		<input type="hidden" name="cid[]" value="<?php echo $this->cat->id;?>">
		<input type="hidden" name="section" value="<?php echo $this->section;?>" />

		<input type="hidden" name="published" id="published" value="<?php echo $this->cat->published;?>">
		<input type="hidden" name="id" id="id" value="<?php echo $this->cat->id;?>">
		<script type="text/javascript" language="Javascript">

		function submitbutton(pressbutton) {
			if (pressbutton == 'cancel' || pressbutton == 'categories.list') {
				submitform( pressbutton );
				return;
			}
			var form = document.adminForm;
			<?php echo $editor->getContent( 'description' ); ?>
			// do field validation
			if (form.title.value == "") {
				alert ( "<?php echo html_entity_decode( JText::_('JEV_E_WARNTITLE') ); ?>" );
			}
			else {
				submitform(pressbutton);
			}
		}

		</script>
        <div class="adminform" align="left">
       	<div style="margin-bottom:20px;">
	        <table cellpadding="5" cellspacing="0" border="0" >
    			<tr>
                	<td align="left"><?php echo JText::_('JEV_CATEGORY_TITLE'); ?>:</td>
                    <td align="left" colspan="3">
                    	<input class="inputbox" type="text" name="title" size="50" maxlength="100" value="<?php echo htmlspecialchars( $this->cat->title, ENT_QUOTES, 'UTF-8'); ?>" />
                    </td>
      			</tr>
                <tr>
                	<td valign="top" align="left"><?php echo JText::_('JEV_CATEGORY_PARENT'); ?></td>
                    <td  >
                    <?php echo $this->plist;?>
                    </td>
                    <?php if (isset($this->glist)) {?>
                    <td align="right"><?php echo JText::_('JEV_ACCESS'); ?></td>
                    <td align="right"><?php echo $this->glist; ?></td>
                    <?php } 
                    else echo "<td/><td/>\n";?>
                 </tr>
                 <tr>
                 	<td valign="top" align="left">
                    <?php echo JText::_('JEV_DESCRIPTION'); ?>
                    </td>
                    <td style="width:600px;" colspan="3">
                    <?php
                    // parameters : areaname, content, hidden field, width, height, rows, cols
                    echo $editor->display( 'description',  htmlspecialchars( $this->cat->description, ENT_QUOTES, 'UTF-8'), "100%", 250, '70', '10', array("readmore","pagebreak")) ;
                    ?>
                    </td>
                 </tr>
            </table>
		</div>
		</div>




</td>
</tr>  
</table>
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="task" value="categories.edit" />
<input type="hidden" name="act" value="" />
<input type="hidden" name="option" value="<?php echo JEVEX_COM_COMPONENT;?>" />
</form>
</div>