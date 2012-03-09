<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2006-2010 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id: view.html.php 115 2010-04-16 20:04:12Z nikosdion $
 * @since 1.3
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

// Load framework base classes
jimport('joomla.application.component.view');

/**
 * Akeeba Backup Control Panel view class
 *
 */
class AkeebaViewCpanel extends JView
{
	function display()
	{
		$registry =& AEFactory::getConfiguration();
		// Set the toolbar title; add a help button
		JToolBarHelper::title(JText::_('AKEEBA'));
		JToolBarHelper::preferences('com_akeeba', '550');

		// Add submenus (those nifty text links below the toolbar!)
		// -- Configuration
		$link = JURI::base().'?option='.JRequest::getCmd('option').'&view=config';
		JSubMenuHelper::addEntry(JText::_('CONFIGURATION'), $link);

		// -- Backup Now
		$link = JURI::base().'?option='.JRequest::getCmd('option').'&view=backup';
		JSubMenuHelper::addEntry(JText::_('BACKUP'), $link);
		// -- Administer Backup Files
		$link = JURI::base().'?option='.JRequest::getCmd('option').'&view=buadmin';
		JSubMenuHelper::addEntry(JText::_('BUADMIN'), $link);
		// -- View log
		$link = JURI::base().'?option='.JRequest::getCmd('option').'&view=log';
		JSubMenuHelper::addEntry(JText::_('VIEWLOG'), $link);

		// Load the helper classes
		$this->loadHelper('utils');
		$this->loadHelper('status');
		$statusHelper = AkeebaHelperStatus::getInstance();

		// Add Live Update button if it is supported on this server
		$this->assign('supports_update', false);
		if(JFile::exists(JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'update.php'))
		{
			$this->assign('supports_update', true);
			require_once( JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'update.php' );
			akimport('models.update', true);
			$updatemodel =& AkeebaModelUpdate::getInstance('update','AkeebaModel');
			if($updatemodel->isLiveUpdateSupported())
			{
				$updates =& $updatemodel->getUpdates();
				if($updates->update_available)
				{
					$this->assign('update', true);
				}
				else
				{
					$this->assign('update', false);
				}
			}
		} else {
			$this->assign('supports_update', false);
		}

		// Update the cached live site's URL for the front-end backup feature (altbackup.php)
		$component =& JComponentHelper::getComponent( 'com_akeeba' );
		$params = new JParameter($component->params);
		$params->set( 'siteurl', str_replace('/administrator','',JURI::base()) );

		$db =& JFactory::getDBO();
		$data = $params->toString();
		$sql = 'UPDATE `#__components` SET `params` = '.$db->Quote($data).' WHERE '.
			"`option` = 'com_akeeba' AND `parent` = 0 AND `menuid` = 0";
		$db->setQuery($sql);
		$db->query();

		// Load the model
		akimport('models.statistics', true);
		$model =& $this->getModel();
		$statmodel = new AkeebaModelStatistics();

		$this->assign('icondefs', $model->getIconDefinitions()); // Icon definitions
		$this->assign('profileid', $model->getProfileID()); // Active profile ID
		$this->assign('profilelist', $model->getProfilesList()); // List of available profiles
		$this->assign('statuscell', $statusHelper->getStatusCell() ); // Backup status
		$this->assign('newscell', $statusHelper->getNewsCell() ); // News
		$this->assign('detailscell', $statusHelper->getQuirksCell() ); // Details (warnings)
		$this->assign('statscell', $statmodel->getLatestBackupDetails() );

		// Add references to CSS and JS files
		AkeebaHelperIncludes::includeMedia(false);

		// Add live help
		AkeebaHelperIncludes::addHelp();

		parent::display();
	}
}