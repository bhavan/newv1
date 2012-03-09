<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2006-2010 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id: update.php 101 2010-03-28 14:04:53Z nikosdion $
 * @since 2.2
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

// Load framework base classes
jimport('joomla.application.component.controller');

/**
 * The Live Update controller class
 *
 */
class AkeebaControllerUpdate extends JController
{
	function display()
	{
		parent::display();
	}

	function update()
	{
		// Make sure there are updates available
		$model =& $this->getModel('Update', 'AkeebaModel');
		$updates =& $model->getUpdates(false);
		if(!$updates->update_available)
		{
			$url = JURI::base().'index.php?option=com_akeeba';
			$msg = JText::_('UPDATE_ERROR_NOUPDATES');
			$this->setRedirect($url, $msg, 'error');
			$this->redirect();
			return;
		}

		// Download the package
		$package = $updates->package_url.$updates->package_url_suffix;

		jimport('joomla.installer.helper');
		$config =& JFactory::getConfig();
		$target = $config->getValue('config.tmp_path').DS.'akeeba_update.zip';
		$result = JInstallerHelper::downloadPackage($package, $target);

		if($result === false)
		{
			$url = JURI::base().'index.php?option=com_akeeba';
			$msg = JText::_('UPDATE_ERROR_CANTDOWNLOAD');
			$this->setRedirect($url, $msg, 'error');
			$this->redirect();
			return;
		}

		// Extract the package
		$package = $config->getValue('config.tmp_path').DS.$result;
		$result = JInstallerHelper::unpack($package);

		if($result === false)
		{
			$url = JURI::base().'index.php?option=com_akeeba';
			$msg = JText::_('UPDATE_ERROR_CANTEXTRACT');
			$this->setRedirect($url, $msg, 'error');
			$this->redirect();
			return;
		}

		// Package extracted; run the installer
		$tempdir = $result['dir'];
		@ob_end_clean();
?>
<html>
<head>
</head>
<body>
<form action="<?php echo JURI::base() ?>index.php" method="post" name="frm" id="frm">
	<input type="hidden" name="option" value="com_installer" />
	<input type="hidden" name="task" value="doInstall" />
	<input type="hidden" name="installtype" value="folder" />
	<input type="hidden" name="install_directory" value="<?php echo htmlspecialchars($tempdir) ?>" />
	<input type="hidden" name="<?php echo JUtility::getToken() ?>" value="1" />
</form>
<script type="text/javascript">
document.frm.submit();
</script>
</body>
<html>
<?php
		die();
	}
}