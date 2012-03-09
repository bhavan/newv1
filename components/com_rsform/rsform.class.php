<?php
/**
* @version 1.2.0
* @package RSform!Pro 1.2.0
* @copyright (C) 2007-2009 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );



///////////////////////////////////////////////////// SUBMISSIONS /////////////////////////////////////////////////////

include(JPATH_SITE.DS.'components'.DS.'com_rsform'.DS.'submissions.class.php');

///////////////////////////////////////////////////// CAPTCHA /////////////////////////////////////////////////////

include(JPATH_SITE.DS.'components'.DS.'com_rsform'.DS.'captcha.class.php');
  
///////////////////////////////////////////////////// RSINSTALLER /////////////////////////////////////////////////////  
  
class RSinstaller {
	var $archivename	= "";
	var $installDir		= "";
	var $installFile	= "";
	var $installType	= "";
	var $unpackDir		= "";
	var $xmldoc			= null;
	var $msg			= null;
	
	var $elementName	= null;
	var $elementDir		= null;
	
	
	function upload($filename = null) 
	{
		$this->archivename = $filename;

		if ($this->extract()) {
			if(file_exists($this->installDir . '/install.xml')) 
			{
				$this->setInstallFile($this->installDir . '/install.xml');
				return true;
			}
			else return false;
		} else {
			return false;
		}
	}
	
	
	function extract() 
	{
		$RSadapter = $GLOBALS['RSadapter'];

		$base_Dir 		= $RSadapter->processPath( $RSadapter->config['absolute_path'] . '/media' );

		$archivename 	= $base_Dir . $this->archivename;
		$tmpdir 		= uniqid( 'rsinstall_' );

		$extractdir 	= $RSadapter->processPath( $base_Dir . $tmpdir );
		$archivename 	= $RSadapter->processPath( $archivename, false );

		$this->unpackDir = $extractdir ;

		if (eregi( '.zip$', $archivename )) {
			// Extract functions
			require_once( $RSadapter->config['absolute_path'] . '/administrator/includes/pcl/pclzip.lib.php' );
			require_once( $RSadapter->config['absolute_path'] . '/administrator/includes/pcl/pclerror.lib.php' );
			
			$zipfile = new PclZip( $archivename );
			if((substr(PHP_OS, 0, 3) == 'WIN')) {
				define('OS_WINDOWS',1);
			} else {
				define('OS_WINDOWS',0);
			}

			$ret = $zipfile->extract( PCLZIP_OPT_PATH, $extractdir );
			if($ret == 0) {
				$this->msg = 'Unrecoverable error "'.$zipfile->errorName(true).'"' ;
				return false;
			}
		} else {
			require_once( $RSadapter->config['absolute_path'] . '/includes/Archive/Tar.php' );
			$archive = new Archive_Tar( $archivename );
			$archive->setErrorHandling( PEAR_ERROR_PRINT );

			if (!$archive->extractModify( $extractdir, '' )) {
				$this->msg = 'Extract Error' ;
				return false;
			}
		}

		$this->installDir = $extractdir;

		// Try to find the correct install dir. in case that the package have subdirs
		// Save the install dir for later cleanup
		$filesindir = $RSadapter->readDirectory( $this->installDir, '' );

		if (count( $filesindir ) == 1) {
			if (is_dir( $extractdir . $filesindir[0] )) {
				$this->installDir = $RSadapter->processPath( $extractdir . $filesindir[0] ) ;
			}
		}
		return true;
	}
	
	function setInstallFile( $filename = null ) 
	{
		if(!is_null($filename)) {
			if((substr(PHP_OS, 0, 3) == 'WIN')) {
				$this->installFile = str_replace('/','\\',$filename);
			} else {
				$this->installFile = str_replace('\\','/',$filename);
			}
		}
		return $this->installFile;
	}
	
	function readInstall()
	{
		$this->xmldoc = new DOMIT_Lite_Document();
		$this->xmldoc->resolveErrors( true );
		if (!$this->xmldoc->loadXML( $this->installFile, false, true )) {
			return false;
		}
		$root = &$this->xmldoc->documentElement;

		// Check that it's an installation file
		if ($root->getTagName() != 'RSinstall') {
			$this->msg = 'File :"' . $this->installFile . '" is not a valid RSform!Pro installation file' ;
			return false;
		}

		$this->installType = $root->getAttribute( 'type' ) ;
		return true;	
	}
	
	function cleanup( $userfile_name, $resultdir) {
		$RSadapter = $GLOBALS['RSadapter'];
		
		if (file_exists( $resultdir )) {
			$this->deldir( $resultdir );
			unlink( $RSadapter->processPath( $RSadapter->config['absolute_path'] . '/media/' . $userfile_name, false ) );
		}
	}

		
	function deldir( $dir ) {
		$RSadapter = $GLOBALS['RSadapter'];
		$current_dir = opendir( $dir );
		$old_umask = umask(0);
		while ($entryname = readdir( $current_dir )) {
			if ($entryname != '.' and $entryname != '..') {
				if (is_dir( $dir . $entryname )) {
					$this->deldir( $RSadapter->processPath( $dir . $entryname ) );
				} else {
	                @chmod($dir . $entryname, 0777);
					unlink( $dir . $entryname );
				}
			}
		}
		umask($old_umask);
		closedir( $current_dir );
		return rmdir( $dir );
	}
	
	
	
	
	
}
?>