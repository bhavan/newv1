<?php
/**
 * copyright (C) 2008 GWE Systems Ltd - All rights reserved
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the component
 *
 * @static
 */
class AdminCPanelViewCPanel extends JView  
{
	/**
	 * Control Panel display function
	 *
	 * @param template $tpl
	 */
	function cpanel($tpl = null)
	{
		jimport('joomla.html.pane');

		global $option;
		JHTML::stylesheet( 'jevlocations.css', 'administrator/components/'.$option.'/assets/css/' );	 	

		$document =& JFactory::getDocument();
		$document->setTitle(JText::_('JEvents Location Manager') . ' :: ' .JText::_('Control Panel'));
		
		// Set toolbar items for the page
		JToolBarHelper::title( JText::_('JEvents Location Manager') .' :: '. JText::_( 'Control Panel' ), 'jevents' );
		JToolBarHelper::preferences('com_jevlocations', '580', '750');

		$this->_hideSubmenu();
		
		global $mainframe;
		if ($mainframe->isAdmin()){
			//JToolBarHelper::preferences(JEVEX_COM_COMPONENT, '580', '750');
		}
		//JToolBarHelper::help( 'screen.cpanel', true);

		JSubMenuHelper::addEntry(JText::_('Control Panel'), 'index.php?option='.$option, true);
		
		$params = JComponentHelper::getParams($option);
		
		parent::display($tpl);
	}	


	 /**
	 * This method creates a standard cpanel button
	 *
	 * @param string $link
	 * @param string $image
	 * @param string $text
	 * @param string $path
	 * @param string $target
	 * @param string $onclick
	 * @access protected
	 */
	 function _quickiconButton( $link, $image, $text, $path=null, $target='', $onclick='' ) {
	 	if( $target != '' ) {
	 		$target = 'target="' .$target. '"';
	 	}
	 	if( $onclick != '' ) {
	 		$onclick = 'onclick="' .$onclick. '"';
	 	}
	 	if( $path === null || $path === '' ) {
	 		global $option;
	 		$path = 'components/'.$option.'/assets/images/';
	 	}
		?>
		<div style="float:left;">
			<div class="icon">
				<a href="<?php echo $link; ?>" <?php echo $target;?>  <?php echo $onclick;?>>
					<?php echo JHTML::_('image.administrator', $image, $path, NULL, NULL, $text ); ?>
					<span><?php echo $text; ?></span>
				</a>
			</div>
		</div>
		<?php
	 }

	/**
	 * Routine to hide submenu suing CSS since there are no paramaters for doing so without hiding the main menu
	 *
	 */
	function _hideSubmenu(){
		global $option;
		JHTML::stylesheet( 'hidesubmenu.css', 'administrator/components/'.$option.'/assets/css/' );	 	
	}
	 
}