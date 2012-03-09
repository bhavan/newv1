<?php
/**
* @copyright	Copyright (C) 2008 GWE Systems Ltd. All rights reserved.
 * @license		By negoriation with author via http://www.gwesystems.com
*/
ini_set("display_errors",0);

list($usec, $sec) = explode(" ", microtime());
define('_SC_START', ((float)$usec + (float)$sec));

// Set flag that this is a parent file
define( '_JEXEC', 1 );
define( 'DS', DIRECTORY_SEPARATOR );
$x = realpath(dirname(__FILE__)."/../../") ;
// SVN version
if (!file_exists($x.DS.'includes'.DS.'defines.php')){
	$x = realpath(dirname(__FILE__)."/../../../") ;

}
define( 'JPATH_BASE', $x );

ini_set("display_errors",0);

require_once JPATH_BASE.DS.'includes'.DS.'defines.php';
require_once JPATH_BASE.DS.'includes'.DS.'framework.php';

global $mainframe;
$mainframe =& JFactory::getApplication('site');
$mainframe->initialise();

$user =& JFactory::getUser();

$params = JComponentHelper::getParams("com_shines");
$user = JFactory::getUser($params->get("phocauser",1));

$option = "com_phocagallery";
define( 'JPATH_COMPONENT',					JPATH_BASE.DS.'components'.DS.$option);
define( 'JPATH_COMPONENT_SITE',				JPATH_SITE.DS.'components'.DS.$option);
define( 'JPATH_COMPONENT_ADMINISTRATOR',	JPATH_ADMINISTRATOR.DS.'components'.DS.$option);

if (! class_exists('PhocaGalleryLoader')) {
	require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_phocagallery'.DS.'libraries'.DS.'loader.php');
}

// Require the base controller
require_once( JPATH_COMPONENT.DS.'controller.php' );
phocagalleryimport('phocagallery.path.path');
phocagalleryimport('phocagallery.pagination.paginationcategories');
phocagalleryimport('phocagallery.pagination.paginationcategory');
phocagalleryimport('phocagallery.library.library');
phocagalleryimport('phocagallery.text.text');
phocagalleryimport('phocagallery.access.access');
phocagalleryimport('phocagallery.file.file');
phocagalleryimport('phocagallery.image.image');
phocagalleryimport('phocagallery.image.imagefront');
phocagalleryimport('phocagallery.render.renderinfo');
phocagalleryimport('phocagallery.render.renderfront');

$controller = "user";
$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
if (file_exists($path)) {
	require_once $path;
} else {
	$controller = '';
}

$paramsC =& JComponentHelper::getParams('com_phocagallery') ;
// UCP is disabled (security reasons) - so enable it here!
$paramsC->set( 'enable_user_cp',  1);

// Create the controller
$classname    = 'PhocaGalleryController'.ucfirst($controller);
$controller   = new $classname( );

// Set the default data
JRequest::setVar("catid",intval($params->get("phocaimagecat",1)));

JRequest::setVar( 'phocagalleryuploadtitle', JRequest::getString('caption',"user")." - by ".JRequest::getString('username',"user"), 'post', 'string', 0 );
JRequest::setVar( 'phocagalleryuploaddescription','' , 'post', 'string', 0 );

$fileArray 		= JRequest::getVar( 'userphoto', '', 'files', 'array' );
if (count($fileArray)==1 && isset($fileArray[0]) && $fileArray[0]==""){
	$fileArray 		= JRequest::getVar( 'Filedata', '', 'files', 'array' );
}
if (count($fileArray)==1 && isset($fileArray[0]) && $fileArray[0]==""){
	echo 0;
}
else {
	// must ensure the image name is unique - append the username to the start of the filename
	if (isset($fileArray["name"]))	{
		$db = JFactory::getDBO();
		$db->setQuery("SELECT  max(id)+1 from #__phocagallery");
		$increment = intval($db->loadResult() );
		$secs = time()%10000;
		$fileArray["name"] = $increment."_".$secs."_".$fileArray["name"];
	}

	$token = JUtility::getToken(true);
	JRequest::setVar($token,1,'post');
	JRequest::setVar($token,1,'get');
	
	if (JRequest::checkToken('get' )) {
		$controller->_singleFileUpload($errUploadMsg, $fileArray, $redirectUrl);	
	}
	else {
		$errUploadMsg = "Invalid token";
	}
	

	$filename = JPATH_SITE."/components/com_shines/phoca.txt";
	if (!is_writable($filename)){
		file_put_contents($filename,"");
	}

	clearstatcache();
	if (filesize($filename)>20000){
		file_put_contents($filename,"");
	}

	if ($handle = fopen($filename,"a")){
		$log = $errUploadMsg."\n".var_export($fileArray,true)."\n--------------------------------------------------\n\n";
		fwrite($handle,$log);
		fclose($handle);
	}
	//file_put_contents(JPATH_SITE."/components/com_shines/phoca.txt",$log);

	if ($errUploadMsg!="PHOCAGALLERY_IMAGE_SAVED") echo 0;
	else echo 1;
}
