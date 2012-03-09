<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2006-2010 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id: view.raw.php 51 2010-01-30 10:49:58Z nikosdion $
 * @since 1.3
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

// Load framework base classes
jimport('joomla.application.component.view');

/**
 * MVC View for Log (raw output for iframe)
 *
 */
class AkeebaViewLog extends JView
{
	public function display()
	{
		$this->loadHelper('log');
		parent::display('raw');
	}
}