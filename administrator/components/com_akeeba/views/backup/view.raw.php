<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2006-2010 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id: view.raw.php 122 2010-04-23 15:56:12Z nikosdion $
 * @since 1.3
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

// Load framework base classes
jimport('joomla.application.component.view');

/**
 * The RAW mode view class for the backup page. It serves a double purpose. On one hand,
 * it processes AJAX requests. On the other hand (when task=step), it displays the iframe
 * contents.
 *
 */
class AkeebaViewBackup extends JView
{
	function display()
	{
		$ajax = JRequest::getCmd('ajax', 'start');

		$ret_array = array();

		switch($ajax)
		{
			case 'start':
				// Description is passed through a strict filter which removes HTML
				$description = JRequest::getString('description','','default', null);
				// The comment is passed through the Safe HTML filter (note: use 2 to force no filtering)
				$comment = JRequest::getString('comment','','default', 4);

				AECoreKettenrad::reset();
				$kettenrad =& AECoreKettenrad::load();
				$options = array(
					'description'	=> $description,
					'comment'		=> $comment
				);
				$kettenrad->setup($options);
				$kettenrad->tick();
				$ret_array  = $kettenrad->getStatusArray();
				$kettenrad->resetWarnings(); // So as not to have duplicate warnings reports
				AECoreKettenrad::save();
				break;

			case 'step':
				$kettenrad =& AECoreKettenrad::load();
				$kettenrad->tick();
				$ret_array  = $kettenrad->getStatusArray();
				$kettenrad->resetWarnings(); // So as not to have duplicate warnings reports
				AECoreKettenrad::save();

				if($ret_array['HasRun'] == 1)
				{
					// Clean up
					AEFactory::nuke();
					AEUtilTempvars::reset();
				}
				break;

			default:
				break;
		}

		$json = json_encode($ret_array);
		$this->assign('json', $json);

		parent::display(JRequest::getCmd('tpl',null));
	}
}
?>