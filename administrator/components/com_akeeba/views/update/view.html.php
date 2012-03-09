<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2006-2010 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id: view.html.php 104 2010-03-31 19:54:01Z nikosdion $
 * @since 2.2
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

// Load framework base classes
jimport('joomla.application.component.view');

/**
 * MVC View for Live Update
 *
 */
class AkeebaViewUpdate extends JView
{
	function display()
	{
		$task = JRequest::getCmd('task');
		$force = ($task == 'force');

		// Set the toolbar title; add a help button
		JToolBarHelper::title(JText::_('AKEEBA').':: <span>'.JText::_('LIVEUPDATE')).'</span>';
		JToolBarHelper::back('Back', 'index.php?option='.JRequest::getCmd('option'));

		// Load the model
		$model =& $this->getModel();
		$updates =& $model->getUpdates($force);
		$this->assignRef('updates', $updates);

		// Add references to CSS and JS files
		AkeebaHelperIncludes::includeMedia(false);

		// Add live help
		AkeebaHelperIncludes::addHelp();

		parent::display();
	}
}