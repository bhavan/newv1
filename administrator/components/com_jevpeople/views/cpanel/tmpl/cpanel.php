<?php defined('_JEXEC') or die('Restricted access'); ?>
<div id="jevents">
   <form action="index.php" method="post" name="adminForm" >
	<div id="cpanel">
		<?php				
		$link = "index.php?option=$option&task=people.overview";
		$this->_quickiconButton( $link, "people.png", JText::_('JEv People') );

		$compparams = JComponentHelper::getParams("com_jevpeople");
		$link = "index.php?option=$option&task=types.list";
		$this->_quickiconButton( $link, "categories.png", JText::_('People Types'),'/administrator/images/' );

		$compparams = JComponentHelper::getParams("com_jevpeople");
		$link = "index.php?option=$option&task=categories.list";
		$this->_quickiconButton( $link, "categories.png", JText::_('Categories'),'/administrator/images/' );
		?>
	</div>
  <input type="hidden" name="task" value="cpanel" />
  <input type="hidden" name="act" value="" />
  <input type="hidden" name="option" value="<?php echo JEVEX_COM_COMPONENT; ?>" />
</form>
</div>
