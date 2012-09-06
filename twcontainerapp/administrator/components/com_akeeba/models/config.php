<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2006-2011 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id$
 * @since 3.2.5
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

// Load framework base classes
jimport('joomla.application.component.model');

class AkeebaModelConfig extends JModel
{
	public function testFTP()
	{
		$config = array(
			'host' => $this->getState('host'),
			'port' => $this->getState('port'),
			'user' => $this->getState('user'),
			'pass' => $this->getState('pass'),
			'initdir' => $this->getState('initdir'),
			'usessl' => $this->getState('usessl'),
			'passive' => $this->getState('passive'),
		);

		// Perform the FTP connection test
		$test = new AEArchiverDirectftp();
		$test->initialize('', $config);
		$errors = $test->getError();
		if(empty($errors) || $test->connect_ok)
		{
			$result = true;
		}
		else
		{
			$result = $errors;
		}
		return $result;
	}
}