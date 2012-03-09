<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2006-2010 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 2, or later
 * @version $Id: default_raw.php 92 2010-03-18 10:33:11Z nikosdion $
 * @since 1.3
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');
$task = JRequest::getCmd('task','');

switch($task)
{
	case 'getdirectory':
		// Return the output directory in JSON format
		$registry =& AEFactory::getConfiguration();
		$outdir = $registry->get('akeeba.basic.output_directory');
		// # Fix 2.4: Drop the output buffer
		if(function_exists('ob_clean')) @ob_clean();
		echo json_encode($outdir);
		break;

	default:
		// Return the CUBE array in JSON format
		$kettenrad =& AECoreKettenrad::load();
		$array = $kettenrad->getStatusArray();
		// # Fix 2.4: Drop the output buffer
		if(function_exists('ob_clean')) @ob_clean();
		echo json_encode($array);
		break;
}

# Fix 2.4: Die the script in order to avoid misbehaving modules from ruining the output
die();