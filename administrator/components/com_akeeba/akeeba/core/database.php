<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 * @copyright Copyright (c)2009 Nicholas K. Dionysopoulos
 * @license GNU GPL version 3 or, at your option, any later version
 * @package akeebaengine
 * @version $Id: database.php 51 2010-01-30 10:49:58Z nikosdion $
 */

// Protection against direct access
defined('AKEEBAENGINE') or die('Restricted access');

/**
 * A utility class to return a database connection object
 */
class AECoreDatabase extends AEAbstractObject
{
	/**
	 * Returns a database connection object. It caches the created objects for future use.
	 * @param array $options Options to use when instanciating the database connection
	 * @return AEAbstractDriver
	 */
	public static function &getDatabase($options)
	{
		static $instances;

		if (!isset( $instances )) {
			$instances = array();
		}

		$signature = serialize( $options );

		if (empty($instances[$signature]))
		{
			$driver		= array_key_exists('driver', $options) 		? $options['driver']	: '';
			$select		= array_key_exists('select', $options)		? $options['select']	: true;
			$database	= array_key_exists('database', $options)	? $options['database']	: null;

			$driver = preg_replace('/[^A-Z0-9_\.-]/i', '', $driver);
			if(empty($driver))
			{
				// No driver specified; try to guess
				$driver = AEPlatform::get_default_database_driver();
			}
			else
			{
				// Make sure a full driver name was given
				if(substr($driver,0,2) != 'AE') $driver = 'AEDriver'.ucfirst($driver);
			}

			$instance	= new $driver($options);

			$instances[$signature] = & $instance;
		}

		return $instances[$signature];
	}
}