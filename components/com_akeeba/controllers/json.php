<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2006-2010 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 2, or later
 * @version $Id: json.php 92 2010-03-18 10:33:11Z nikosdion $
 * @since 1.3
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

// Load framework base classes
jimport('joomla.application.component.controller');

// If JSON functions don't exist, load our compatibility layer
if( (!function_exists('json_encode')) || (!function_exists('json_decode')) )
{
	require_once JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_akeeba'.DS.'helpers'.DS.'jsonlib.php';
}


class AkeebaControllerJson extends JController
{
	/**
	 * Starts a backup
	 * @return
	 */
	public function display()
	{
		// Check permissions
		$this->_checkPermissions();
		// Set the profile
		$this->_setProfile();
		// Force the output to be of the raw format type
		JRequest::setVar('format', 'raw');
		$document =& JFactory::getDocument();
		$document->setType('raw');
		// Get the description, if present; otherwise use the default description
		jimport('joomla.utilities.date');
		$user =& JFactory::getUser();
		$userTZ = $user->getParam('timezone',0);
		$dateNow = new JDate();
		$dateNow->setOffset($userTZ);
		$default_description = JText::_('BACKUP_DEFAULT_DESCRIPTION').' '.$dateNow->toFormat(JText::_('DATE_FORMAT_LC2'));
		$description = JRequest::getString('description', $default_description);
		// Start the backup (CUBE Operation)
		AECoreKettenrad::reset();
		$kettenrad =& AECoreKettenrad::load();
		$options = array(
			'description'	=> $description,
			'comment'		=> ''
		);
		$kettenrad->setup($options);
		AECoreKettenrad::save();

		// Return the JSON output
		parent::display(false);
	}

	/**
	 * task=start is an alias for task=display or lack of task definition altogether
	 */
	public function start()
	{
		$this->display();
	}

	/**
	 * Step through the backup
	 * @return
	 */
	public function step()
	{
		// Check permissions
		$this->_checkPermissions();
		// Set the profile
		$this->_setProfile();

		$kettenrad =& AECoreKettenrad::load();
		$array = $kettenrad->getStatusArray();

		if( ($array['Error'] == '') && ($array['HasRun'] != 1) )
		{
			$kettenrad->tick();
			AECoreKettenrad::save();
		}

		// Return the JSON output
		parent::display(false);
	}

	/**
	 * Return the absolute path to the output directory
	 * @return
	 */
	public function getdirectory()
	{
		// Check permissions
		$this->_checkPermissions();

		parent::display(false);
	}

	/**
	 * Check that the user has sufficient permissions, or die in error
	 *
	 */
	private function _checkPermissions()
	{
		$component =& JComponentHelper::getComponent( 'com_akeeba' );
		$params = new JParameter($component->params);

		// Is frontend backup enabled?
		$febEnabled = $params->get('frontend_enable',0) != 0;
		if(!$febEnabled)
		{
			die('403 '.JText::_('ERROR_NOT_ENABLED'));
		}

		// Is the key good? Check using 'keyhash'.
		$keyhash = JRequest::getVar('keyhash');
		$hashparts = explode(':', $keyhash, 2);
		$validKey=$params->get('frontend_secret_word','');

		$hash_to_check = $hashparts[0];
		$salt= $hashparts[1];
		$valid_hash = md5($validKey.$salt);

		$validKeyTrim = trim($validKey);
		if( ($hash_to_check != $valid_hash) || (empty($validKeyTrim)) )
		{
			die('403 '.JText::_('ERROR_INVALID_KEY'));
		}
	}

	private function _setProfile()
	{
		// Set profile
		$profile = JRequest::getInt('profile',1);
		if(!is_numeric($profile)) $profile = 1;
		$session =& JFactory::getSession();
		$session->set('profile', $profile, 'akeeba');
	}

}

