<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2006-2010 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id: dbef.php 89 2010-03-14 16:43:08Z nikosdion $
 * @since 1.3
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

// Load framework base classes
jimport('joomla.application.component.controller');

/**
 * MVC controller class for Database Table filters
 *
 */
class AkeebaControllerDbef extends JController
{
	/**
	 * Handles the "display" task, which displays a folder and file list
	 *
	 */
	public function display()
	{
		parent::display();
	}

	/**
	 * AJAX proxy.
	 */
	public function ajax()
	{
		// Parse the JSON data and reset the action query param to the resulting array
		$action_json = JRequest::getVar('action', '', 'default', 'none', 2);
		$action = json_decode($action_json);
		JRequest::setVar('action', $action);

		parent::display();
	}
}