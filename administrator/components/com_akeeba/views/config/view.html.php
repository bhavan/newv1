<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2006-2010 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id: view.html.php 104 2010-03-31 19:54:01Z nikosdion $
 * @since 1.3
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

// Load framework base classes
jimport('joomla.application.component.view');

/**
 * Akeeba Backup Configuration view class
 *
 */
class AkeebaViewConfig extends JView
{
	function display()
	{
		// Toolbar buttons
		JToolBarHelper::title(JText::_('AKEEBA').':: <small><small>'.JText::_('CONFIGURATION').'</small></small>');
		JToolBarHelper::apply();
		JToolBarHelper::save();
		JToolBarHelper::cancel();

		// Add references to scripts and CSS
		AkeebaHelperIncludes::includeMedia(false);
		$media_folder = JURI::base().'../media/com_akeeba/';

		// Get a JSON representation of GUI data
		$json = addcslashes(AEUtilInihelper::getJsonGuiDefinition(), "'\\");
		$this->assignRef( 'json', $json );

		// Get profile ID
		$profileid = AEPlatform::get_active_profile();
		$this->assign('profileid', $profileid);

		// Get profile name
		akimport('models.profiles',true);
		$model = new AkeebaModelProfiles();
		$model->setId($profileid);
		$profile_data = $model->getProfile();
		$this->assign('profilename', $profile_data->description);

		// Get the root URI for media files
		$this->assign( 'mediadir', addcslashes($media_folder.'theme/', "'\\") );

		// Add live help
		AkeebaHelperIncludes::addHelp();

		parent::display();
	}
}