<?php defined('_JEXEC') or die('Restricted access'); ?>
<div id="jevents">
   <form action="index.php" method="post" name="adminForm" >
	<div id="cpanel">
		<?php				
		$link = "index.php?option=$option&task=locations.overview";
		$this->_quickiconButton( $link, "locations.png", JText::_('JEvents Locations') );

		$compparams = JComponentHelper::getParams("com_jevlocations");
		$usecats = $compparams->get("usecats",0);
		//if ($usecats){
			$link = "index.php?option=$option&task=categories.list";
			$this->_quickiconButton( $link, "categories.png", JText::_('JEV_COUNTRY_STATE_CITY'),'/administrator/images/' );
		//}
		
		$link = "index.php?option=$option&task=cats.list";
		$this->_quickiconButton( $link, "icon-48-category.png", JText::_('JEV LOCATION CATEGORIES')  ,"/administrator/templates/khepri/images/header/");
		
		?>
	</div>
  <input type="hidden" name="task" value="cpanel" />
  <input type="hidden" name="act" value="" />
  <input type="hidden" name="option" value="<?php echo JEVEX_COM_COMPONENT; ?>" />
</form>
</div>
