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

/**
 * The back-end backup model
 */
class AkeebaModelBackup extends JModel
{
	public function runBackup()
	{
		$ret_array = array();
		
		$ajaxTask = $this->getState('ajax');
		switch($ajaxTask)
		{
			case 'start':
				// Description is passed through a strict filter which removes HTML
				$description = $this->getState('description');
				// The comment is passed through the Safe HTML filter (note: use 2 to force no filtering)
				$comment = $this->getState('comment');
				$jpskey = $this->getState('jpskey');

				// Try resetting the engine
				AECoreKettenrad::reset();

				// Remove any stale memory files left over from the previous step
				$tag = $this->getState('tag');
				if(empty($tag)) $tag = AEPlatform::get_backup_origin();
				$memory_filename = AEUtilTempvars::get_storage_filename($tag);
				@unlink($memory_filename);

				$kettenrad =& AECoreKettenrad::load($tag);
				$options = array(
					'description'	=> $description,
					'comment'		=> $comment,
					'jpskey'		=> $jpskey
				);
				$kettenrad->setup($options);
				$kettenrad->tick();
				$ret_array  = $kettenrad->getStatusArray();
				$kettenrad->resetWarnings(); // So as not to have duplicate warnings reports
				AECoreKettenrad::save();
				break;

			case 'step':
				$tag = $this->getState('tag');
				$kettenrad =& AECoreKettenrad::load($tag);
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
		
		return $ret_array;
	}
}