<?php
/**
 *
 * @version     $Id: view.html.php 1603 2009-10-12 08:59:30Z geraint $
 * @copyright   Copyright (C)  2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

jimport( 'joomla.application.component.view');

class AdminCPanelViewCPanel extends JView 
{
	/**
	 * Control Panel display function
	 *
	 * @param template $tpl
	 */
	function display($tpl = null)
	{
		jimport('joomla.html.pane');
		
		JHTML::stylesheet( 'shines.css', 'administrator/components/com_shines/assets/css/' );

		$document =& JFactory::getDocument();
		$document->setTitle( JText::_('Shines') .' :: '. JText::_('Control Panel'));
		
		// Set toolbar items for the page
		JToolBarHelper::title( JText::_('Shines') .' :: '. JText::_( 'Control Panel' ), 'shines' );
		
		//JToolBarHelper::preferences("com_shines", '580', '750');
		
		JHTML::_('behavior.modal');
		
		parent::display($tpl);
	}	

	
}