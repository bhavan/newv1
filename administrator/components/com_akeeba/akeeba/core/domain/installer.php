<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 * @copyright Copyright (c)2009 Nicholas K. Dionysopoulos
 * @license GNU GPL version 3 or, at your option, any later version
 * @package akeebaengine
 * @version $Id: installer.php 51 2010-01-30 10:49:58Z nikosdion $
 */

// Protection against direct access
defined('AKEEBAENGINE') or die('Restricted access');

/**
 * Installer deployment
 */
class AECoreDomainInstaller extends AEAbstractPart
{

	/** @var int Installer image file offset last read */
	private $offset;

	/**
	 * Public constructor
	 * @return AECoreDomainInstaller
	 */
	public function __construct()
	{
		parent::__construct();
		AEUtilLogger::WriteLog(_AE_LOG_DEBUG, __CLASS__." :: New instance");
	}

	/**
	 * Implements the _prepare abstract method
	 *
	 */
	function _prepare()
	{
		// Nothing to do
		$this->setState('prepared');
	}

	/**
	 * Implements the _run() abstract method
	 */
	function _run()
	{
		if( $this->getState() == 'postrun' )
		{
			AEUtilLogger::WriteLog(_AE_LOG_DEBUG, __CLASS__." :: Already finished");
			$this->setStep('');
			$this->setSubstep('');
		} else {
			$this->setState('running');
		}

		// Try to step the archiver
		$archive =& AEFactory::getArchiverEngine();
		$ret = $archive->transformJPA($this->offset);
		// Error propagation
		$this->propagateFromObject($archive);

		if( ($ret !== false) && ($archive->getError() == '') )
		{
			$this->offset = $ret['offset'];
			$this->setStep($ret['filename']);
		}

		// Check for completion
		if($ret['done'])
		{
			AEUtilLogger::WriteLog(_AE_LOG_DEBUG, __CLASS__.":: archive is initialized");
			$this->setState('finished');
		}
	}

	/**
	 * Implements the _finalize() abstract method
	 *
	 */
	function _finalize()
	{
		$this->setState('finished');
	}

}