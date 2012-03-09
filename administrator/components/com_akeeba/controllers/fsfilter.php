<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2006-2010 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id: fsfilter.php 89 2010-03-14 16:43:08Z nikosdion $
 * @since 3.0
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

// Load framework base classes
jimport('joomla.application.component.controller');

/**
 * The Filesystem Filters controller class
 *
 */
class AkeebaControllerFsfilter extends JController
{
	/**
	 * Displays the filter browser page
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