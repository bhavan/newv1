<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2006-2010 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id: backup.php 91 2010-03-16 20:34:37Z nikosdion $
 * @since 1.3
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

// Load framework base classes
jimport('joomla.application.component.controller');

/**
 * The Backup controller class
 *
 */
class AkeebaControllerBackup extends JController
{
	/**
	 * Default task; shows the initial page where the user selects a profile
	 * and enters description and comment
	 *
	 */
	public function display()
	{
		$format = JRequest::getCmd('format','html');

		$newProfile = JRequest::getInt('profileid', -10);
		if(is_numeric($newProfile) && ($newProfile > 0))
		{
			$session =& JFactory::getSession();
			$session->set('profile', $newProfile, 'akeeba');
		}

		// For raw view with default task use the default_raw.php template file
		if($format == 'raw')
		{
			JRequest::setVar('tpl', 'raw');
		}
		else
		{
			// Deactivate the menus
			JRequest::setVar('hidemainmenu', 1);
		}

		parent::display();
	}

}