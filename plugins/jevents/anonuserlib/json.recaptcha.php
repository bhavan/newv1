<?php
/**
* @copyright	Copyright (C) 2009 GWE Systems Ltd. All rights reserved.
 * @license		By negoriation with author via http://www.gwesystems.com
*/

// Set flag that this is a parent file
define( '_JEXEC', 1 );
define( 'DS', DIRECTORY_SEPARATOR );
$x = realpath(dirname(__FILE__).DS ."..".DS."..".DS."..".DS ) ;

if (!file_exists($x.DS.'includes'.DS.'defines.php')){
	$x = realpath(dirname(__FILE__).DS ."..".DS."..".DS."..".DS."..".DS  ) ;
}
define( 'JPATH_BASE', $x );
require_once JPATH_BASE.DS.'includes'.DS.'defines.php';
require_once JPATH_BASE.DS.'includes'.DS.'framework.php';

// We want to echo the errors so that the client has a chance to capture them in the payload
JError::setErrorHandling( E_ERROR,	 'die' );
JError::setErrorHandling( E_WARNING, 'echo' );
JError::setErrorHandling( E_NOTICE,	 'echo' );

// create the mainframe object
$_REQUEST['tmpl'] = 'component';

// Create JSON data structure
$data = new stdClass();
$data->error = 0;
$data->result = "ERROR";

// Get JSON data
if (!array_key_exists("json",$_REQUEST)){
	throwerror("There was an error - no request data");
}
else {
	$requestData = $_REQUEST["json"];

	if (isset($requestData)){
		try {
			if (ini_get("magic_quotes_gpc")){
				$requestData= stripslashes($requestData);
			}

			$requestObject = json_decode($requestData, 0);
		}
		catch (Exception $e) {
			throwerror("There was an exception");
		}

		if ($requestObject->error){
			throwerror("There was an input error");
		}
		else {
			global $mainframe;
			$mainframe =& JFactory::getApplication('site');
			$mainframe->initialise();

			ProcessRequest($requestObject, $data);
		}
	}
	else {
		throwerror("Invalid Input");
	}
}

header("Content-Type: application/x-javascript");
// Must suppress any error messages
@ob_end_clean();
echo json_encode($data);

function ProcessRequest(&$requestObject, &$returnData){
	if ($requestObject->error){
		return "Error";
	}

	$plugin = JPluginHelper::getPlugin('jevents', 'jevanonuser');
	$params = new JParameter( $plugin->params );

	$lang 		=& JFactory::getLanguage();
	$lang->load("plg_jevents_jevanonuser", JPATH_ADMINISTRATOR);
	
	if ($params->get("recaptchaprivate",false)){
		require_once('recaptcha.php');
		if (isset($requestObject->responseField) && trim($requestObject->responseField)!==""){
			$response = recaptcha_check_answer($params->get("recaptchaprivate",false),JRequest::getString("REMOTE_ADDR","","server"), $requestObject->challengeField,$requestObject->responseField);
			if (!$response->is_valid){
				throwerror ( JText::_("JEV RECAPTCHA ERROR",true));
			}
		}
		else {
			throwerror ( "There was an error - no valid argument");
		}

		global $mainframe;
		$mainframe->setUserState("jevrecaptcha","ok");

		$returnData->result = "success";
	}
}
function throwerror ($msg){
	$data = new stdClass();
	//"document.getElementById('products').innerHTML='There was an error - no valid argument'");
	$data->error = "alert('".$msg."')";
	$data->result = "ERROR";

	global $mainframe;
	$mainframe->setUserState("jevrecaptcha","error");
	
	header("Content-Type: application/x-javascript");
	// Must suppress any error messages
	ob_end_clean();
	echo json_encode($data);
	exit();
}