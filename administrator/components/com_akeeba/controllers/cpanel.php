<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2006-2010 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id: cpanel.php 71 2010-02-22 22:17:01Z nikosdion $
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
	/**
	 * Displays the Control Panel (main page)
	 * Accessible at index.php?option=com_akeeba
	 *
	 */
	public function display()
	{
		$registry =& AEFactory::getConfiguration();

		// Invalidate stale backups
		AECoreKettenrad::reset(true);

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