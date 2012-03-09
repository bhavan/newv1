<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 * @copyright Copyright (c)2009 Nicholas K. Dionysopoulos
 * @license GNU GPL version 3 or, at your option, any later version
 * @package akeebaengine
 * @version $Id: filesystem.php 51 2010-01-30 10:49:58Z nikosdion $
 */

// Protection against direct access
defined('AKEEBAENGINE') or die('Restricted access');

/**
 * Utility functions related to filesystem objects, e.g. path translation
 */
class AEUtilFilesystem
{
	/**
	 * Makes a Windows path more UNIX-like, by turning backslashes to forward slashes.
	 * It takes into account UNC paths, e.g. \\myserver\some\folder becomes
	 * \\myserver/some/folder
	 *
	 * @param	string	$p_path	The path to transform
	 * @return	string
	 */
	static public function TranslateWinPath( $p_path )
	{
		static $is_windows;

		if(empty($is_windows))
		{
			$is_windows =  (DS == '\\');
		}

		$is_unc = false;

		if ($is_windows)
		{
			// Is this a UNC path?
			$is_unc = (substr($p_path, 0, 2) == '//');
			// Change potential windows directory separator
			if ((strpos($p_path, '\\') > 0) || (substr($p_path, 0, 1) == '\\')){
				$p_path = strtr($p_path, '\\', '/');
			}
		}

		// Remove multiple slashes
		$p_path = str_replace('///','/',$p_path);
		$p_path = str_replace('//','/',$p_path);

		// Fix UNC paths
		if($is_unc)
		{
			$p_path = '/'.$p_path;
		}

		return $p_path;
	}

	/**
	 * Removes trailing slash or backslash from a pathname
	 *
	 * @param	string	$path	The path to treat
	 * @return	string	The path without the trailing slash/backslash
	 */
	static public function TrimTrailingSlash($path)
	{
		$newpath = $path;
		if( substr($path, strlen($path)-1, 1) == '\\' )
		{
			$newpath = substr($path, 0, strlen($path)-1);
		}
		if( substr($path, strlen($path)-1, 1) == '/' )
		{
			$newpath = substr($path, 0, strlen($path)-1);
		}
		return $newpath;
	}

	/**
	 * Returns the relative and absolute path to the archive, if defined
	 * @param	string	$relative The relative path
	 * @param	string	$absolute The absolute path
	 */
	public static function get_archive_name( &$relative, &$absolute )
	{
		static $relative_path = null;
		static $absolute_path = null;

		if( is_null($relative_path) || is_null($absolute_path) )
		{
			$registry =& AEFactory::getConfiguration();

			// Import volatile scripting keys to the registry
			AEUtilScripting::importScriptingToRegistry();

			// Determine the extension
			$force_extension = AEUtilScripting::getScriptingParameter('core.forceextension', null);
			if( is_null($force_extension)  )
			{
				$archiver =& AEFactory::getArchiverEngine();
				$extension = $archiver->getExtension();
			}
			else
			{
				$extension = $force_extension;
			}

			// Get the template name
			$templateName = $registry->get('akeeba.basic.archive_name');
			AEUtilLogger::WriteLog(_AE_LOG_DEBUG, "Archive template name: $templateName");

			// Parse [DATE] tag
			$dateExpanded = AEPlatform::get_local_timestamp("%Y%m%d");
			$templateName = str_replace("[DATE]", $dateExpanded, $templateName);

			// Parse [TIME] tag
			$timeExpanded = AEPlatform::get_local_timestamp("%H%M%S");
			$templateName = str_replace("[TIME]", $timeExpanded, $templateName);

			// Parse [HOST] tag
			$host = AEPlatform::get_host();
			$host = empty($host) ? 'unknown_host' : $host;
			$templateName = str_replace("[HOST]", $_SERVER['SERVER_NAME'], $templateName);

			// Parse [RANDOM] tag
			$templateName = str_replace("[RANDOM]", md5(microtime()) , $templateName);

			AEUtilLogger::WriteLog(_AE_LOG_DEBUG, "Expanded template name: $templateName");

			$ds = DIRECTORY_SEPARATOR;
			$relative_path = $templateName.$extension;
			$absolute_path = AEUtilFilesystem::TranslateWinPath( $registry->get('akeeba.basic.output_directory').$ds.$relative_path );
		}

		$relative = $relative_path;
		$absolute = $absolute_path;
	}

	/**
	 * Checks if a folder (directory) exists.
	 * @param string $folder_to_check The path to check if it exists
	 * @return bool True if the folder is there, false if it's not or the path exists but is not a folder
	 */
	static function folderExists($folder_to_check)
	{
		// Try to find the real path to the folder
		$folder_clean = @realpath($folder_to_check);
		if( ($folder_clean !== false) && (!empty($folder_clean)) )
		{
			$folder = $folder_clean;
		}
		else
		{
			$fodler = $folder_to_check;
		}

		// Clear filesystem cache to avoid getting stale information
		if(function_exists("clearstatcache")) clearstatcache();
		// Check that the path is there
		if( !file_exists($folder) ) return false;
		// Check that it is a folder, indeed
		if( !is_dir($folder) ) return false;

		return true;
	}

	static function translateStockDirs( $folder, $translate_win_dirs = false, $trim_trailing_slash = false )
	{
		static $stock_dirs;
		if(empty($stock_dirs))
		{
			$stock_dirs = AEPlatform::get_stock_directories();
		}

		$temp = $folder;
		foreach($stock_dirs as $find => $replace)
		{
			$temp = str_replace($find, $replace, $temp);
		}
		if($translate_win_dirs)
		{
			$temp = self::TranslateWinPath( $temp );
		}
		if( $trim_trailing_slash )
		{
			$temp = self::TrimTrailingSlash( $temp );
		}
		return $temp;
	}

}

