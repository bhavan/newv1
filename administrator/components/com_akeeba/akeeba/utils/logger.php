<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 * @copyright Copyright (c)2009 Nicholas K. Dionysopoulos
 * @license GNU GPL version 3 or, at your option, any later version
 * @package akeebaengine
 * @version $Id: logger.php 51 2010-01-30 10:49:58Z nikosdion $
 */

// Protection against direct access
defined('AKEEBAENGINE') or die('Restricted access');

/**
 * Writes messages to the backup log file
 */
class AEUtilLogger
{
	/** @var string Full path to log file. You can change it at will. */
	public static $logName = null;

	/**
	 * Clears the logfile
	 */
	static function ResetLog() {
		if(empty(AEUtilLogger::$logName))
		{
			AEUtilLogger::$logName = AEUtilLogger::logName();
		}

		// Create (touch) an empty file, or truncate existing file
		$fp = @fopen(AEUtilLogger::$logName,'w');
		if($fp !== false)
		{
			@ftruncate($fp);
			@fclose($fp);
		}
	}

	/**
	 * Writes a line to the log, if the log level is high enough
	 *
	 * @param int|bool $level The log level (_AE_LOG_XX constants). Use FALSE to pause logging, TRUE to resume logging
	 * @param string $message The message to write to the log
	 */
	static function WriteLog( $level, $message )
	{
		static $configuredLoglevel;
		static $site_root_untranslated;
		static $site_root;
		static $fp = null;

		if( is_null($level) && !is_null($fp) )
		{
			@fclose($fp);
		}

		if(empty(AEUtilLogger::$logName))
		{
			AEUtilLogger::$logName = AEUtilLogger::logName();
		}

		if(empty($site_root) || empty($site_root_untranslated))
		{
			$site_root_untranslated = AEPlatform::get_site_root();
			$site_root = AEUtilFilesystem::TranslateWinPath( $site_root_untranslated );
		}

		if(empty($configuredLoglevel) or ($level === true))
		{
			// Load the registry and fetch log level
			$registry =& AEFactory::getConfiguration();
			$configuredLoglevel = $registry->get('akeeba.basic.log_level');
			return;
		}

		if($level === false)
		{
			// Pause logging
			$configuredLogLevel = false;
			return;
		}

		// Catch paused logging
		if($configuredLoglevel === false) return;

		if( ($configuredLoglevel >= $level) && ($configuredLoglevel != 0))
		{
			$message = str_replace( $site_root_untranslated, "<root>", $message );
			$message = str_replace( $site_root, "<root>", $message );
			$message = str_replace( "\n", ' \n ', $message );
			switch( $level )
			{
				case _AE_LOG_ERROR:
					$string = "ERROR   |";
					break;
				case _AE_LOG_WARNING:
					$string = "WARNING |";
					break;
				case _AE_LOG_INFO:
					$string = "INFO    |";
					break;
				default:
					$string = "DEBUG   |";
					break;
			}
			$string .= @strftime( "%y%m%d %H:%M:%S" ) . "|$message\r\n";

			if(is_null($fp))
			{
				$fp = @fopen( AEUtilLogger::$logName, "at" );
			}

			if (!($fp === FALSE))
			{
				@fwrite( $fp, $string );
			}
		}
	}

	/**
	 * Calculates the absolute path to the log file
	 * @param	string	$fileName	The log's file name, defaults to akeeba.log
	 * @return	string	The absolute path to the log file
	 */
	public static function logName( $fileName = 'akeeba.log' )
	{
		// Get output directory
		$registry =& AEFactory::getConfiguration();
		$outdir = $registry->get('akeeba.basic.output_directory');

		// Get log's file name
		return AEUtilFilesystem::TranslateWinPath($outdir.DIRECTORY_SEPARATOR.$fileName);
	}

	public static function closeLog()
	{
		self::WriteLog(null,null);
	}
}

// Make sure we close the log file every time we finish with a page load
register_shutdown_function( array('AEUtilLogger','closeLog') );