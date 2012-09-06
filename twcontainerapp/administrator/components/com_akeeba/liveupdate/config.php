<?php
/**
 * @package LiveUpdate
 * @copyright Copyright ©2011 Nicholas K. Dionysopoulos / AkeebaBackup.com
 * @license GNU LGPLv3 or later <http://www.gnu.org/copyleft/lesser.html>
 */

defined('_JEXEC') or die();

/**
 * Configuration class for your extension's updates. Override to your liking.
 */
class LiveUpdateConfig extends LiveUpdateAbstractConfig
{
	var $_extensionName			= 'com_akeeba';
	var $_versionStrategy		= 'different';
	var $_storageAdapter		= 'component';
	var $_storageConfig			= array(
			'extensionName'	=> 'com_akeeba',
			'key'			=> 'liveupdate'
		);
	
	function __construct()
	{
		$useSVNSource = AEPlatform::get_platform_configuration_option('usesvnsource', 0);

		// Determine the appropriate update URL based on whether we're on Core or Professional edition
		AEPlatform::load_version_defines();
		$fname = 'http://www.akeebabackup.com/updates/ab';
		$fname .= (AKEEBA_PRO == 1) ? 'pro' : 'core';
		if($useSVNSource) $fname .= 'svn';
		$fname .= '.ini';
		$this->_updateURL = $fname;
		
		$this->_extensionTitle = 'Akeeba Backup '.(AKEEBA_PRO == 1 ? 'Professional' : 'Core');
		$this->_requiresAuthorization = (AKEEBA_PRO == 1);
		
		parent::__construct();
		
		$this->_downloadID = AEPlatform::get_platform_configuration_option('update_dlid', '');
	}
}