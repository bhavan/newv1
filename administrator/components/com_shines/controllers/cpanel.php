<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: cpanel.php 1603 2009-10-12 08:59:30Z geraint $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined( 'JPATH_BASE' ) or die( 'Direct Access to this location is not allowed.' );

jimport('joomla.application.component.controller');

class AdminCpanelController extends JController {
	/**
	 * Controler for the Control Panel
	 * @param array		configuration
	 */
	function __construct($config = array())
	{
		parent::__construct($config);
		$this->registerTask( 'show',  'cpanel' );
		$this->registerDefaultTask("cpanel");

		//$this->setupTemplates(); //#DD#//
	}

	function cpanel( )
	{
		// get the view
		$this->view = & $this->getView("cpanel","html");

		// Set the layout
		$this->view->setLayout('cpanel');
		$this->view->assign('title'   , JText::_("Control Panel"));

		$this->view->display();
	}


	function setupTemplates()
	{
	
		// Installs into template folders
		$templateDirs = JFolder::folders(JPATH_SITE.DS.'templates');
		foreach ($templateDirs as $dir) {
			//JFile::copy(dirname(__FILE__).DS."jwizard.php",JPATH_SITE.DS.'templates'.DS.$dir.DS."jwizard.php");
			
			JFolder::copy(JPATH_COMPONENT_ADMINISTRATOR."/template",JPATH_SITE.DS.'templates'.DS.$dir.DS."html", '', true);
		}

	}
}
