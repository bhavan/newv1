<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2006-2011 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id: cpanel.php 513 2011-03-22 13:05:40Z nikosdion $
 * @since 1.3
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

// Load framework base classes
jimport('joomla.application.component.controller');

/**
 * The Control Panel controller class
 *
 */
class AkeebaControllerCpanel extends JController
{
	public function  __construct($config = array()) {
		parent::__construct($config);
		if(AKEEBA_JVERSION=='16')
		{
			// Access check, Joomla! 1.6 style.
			$user = JFactory::getUser();
			if (!$user->authorise('core.manage', 'com_akeeba')) {
				$this->setRedirect('index.php?option=com_akeeba');
				return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
				$this->redirect();
			}
		}
	}

	/**
	 * Displays the Control Panel (main page)
	 * Accessible at index.php?option=com_akeeba
	 *
	 */
	public function display()
	{
		$registry =& AEFactory::getConfiguration();

		// Invalidate stale backups
		AECoreKettenrad::reset( array('global'=>true,'log'=>false) );

		// Just in case the reset() loaded a stale configuration...
		AEPlatform::load_configuration();
		
		// Let's make sure the temporary and output directories are set correctly and writable...
		$wizmodel = JModel::getInstance('Confwiz','AkeebaModel');
		$wizmodel->autofixDirectories();
		
		// Check if we need to toggle the settings encryption feature
		$model = JModel::getInstance('Cpanel','AkeebaModel');
		$model->checkSettingsEncryption();
		// Update the magic component parameters
		$model->updateMagicParameters();

		// Display the panel
		parent::display();
	}

	public function switchprofile()
	{
		$newProfile = JRequest::getInt('profileid', -10);

		if(!is_numeric($newProfile) || ($newProfile <= 0))
		{
			$this->setRedirect(JURI::base().'index.php?option='.JRequest::getCmd('option'), JText::_('PANEL_PROFILE_SWITCH_ERROR'), 'error' );
			return;
		}

		$session =& JFactory::getSession();
		$session->set('profile', $newProfile, 'akeeba');
		$this->setRedirect(JURI::base().'index.php?option='.JRequest::getCmd('option'), JText::_('PANEL_PROFILE_SWITCH_OK'));
	}


}