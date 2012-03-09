<?php
/**
* @copyright	Copyright (C) 2008 GWE Systems Ltd. All rights reserved.
 * @license		By negoriation with author via http://www.gwesystems.com
*/
ini_set("display_errors",0);

require 'jsonwrapper.php';

list($usec, $sec) = explode(" ", microtime());
define('_SC_START', ((float)$usec + (float)$sec));

// Set flag that this is a parent file
define( '_JEXEC', 1 );
define( 'DS', DIRECTORY_SEPARATOR );
$x = realpath(dirname(__FILE__).DS ."..".DS. "..".DS. "..".DS) ;
if (!file_exists($x.DS."plugins")){
	$x = realpath(dirname(__FILE__).DS ."..".DS. "..".DS. "..".DS. "..".DS ) ;
}
define( 'JPATH_BASE', $x );

// create the mainframe object
$_REQUEST['tmpl'] = 'component';

// Create JSON data structure
$data = new stdClass();
$data->error = 0;
$data->result = "ERROR";
$data->user = "";

// Enforce referrer
if (!array_key_exists("HTTP_REFERER",$_SERVER) ){
	throwerror("There was an error");
}

$live_site = $_SERVER['HTTP_HOST'];
$ref_parts = parse_url($_SERVER["HTTP_REFERER"]);

if (!isset($ref_parts["host"]) || $ref_parts["host"] != $live_site ){
	throwerror("There was an error - missing host in referrer");
}


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
			if (!$requestObject){
				$requestObject = json_decode(utf8_encode($requestData), 0);
			}
		}
		catch (Exception $e) {
			throwerror("There was an exception");
		}

		if (!$requestObject){
			file_put_contents(dirname(__FILE__)."/cache/error.txt", var_export($requestData,true));
			throwerror("There was an error - no request object ");
		}
		else if ($requestObject->error){
			throwerror("There was an error - Request object error ".$requestObject->error);
		}
		else {

			try {
				$data = ProcessRequest($requestObject, $data);
			}
			catch (Exception $e){
				throwerror("There was an exception ".$e->getMessage());
			}
		}
	}
	else {
		throwerror("Invalid Input");
	}
}

header("Content-Type: application/x-javascript; charset=utf-8");

list ($usec,$sec) = explode(" ", microtime());
$time_end = (float)$usec + (float)$sec;
$data->timing = round($time_end - _SC_START,4);

// Must suppress any error messages
@ob_end_clean();
echo json_encode($data);

function ProcessRequest(&$requestObject, $returnData){

	define("REQUESTOBJECT",serialize($requestObject));
	define("RETURNDATA",serialize($returnData));

	require_once JPATH_BASE.DS.'includes'.DS.'defines.php';
	require_once JPATH_BASE.DS.'includes'.DS.'framework.php';

	$requestObject = unserialize(REQUESTOBJECT);
	$returnData = unserialize(RETURNDATA);

	$returnData->titles	= array();
	$returnData->exactmatch=false;

	ini_set("display_errors",0);

	global $mainframe, $option;
	$option = "com_jevents";
	$client = "site";
	if (isset($requestObject->client) && in_array($requestObject->client,array("site","administrator"))){
		$client = $requestObject->client;
	}
	$mainframe = JFactory::getApplication($client);
	$mainframe->initialise();

	if (!isset($requestObject->rpid) || $requestObject->rpid==0){
		throwerror("There was an error");
	}

	$user = JFactory::getUser();
	if ($user->id==0 || $user->id!=$requestObject->userid){
		throwerror("There was an error");
	}

	include_once(JPATH_SITE."/components/com_jevents/jevents.defines.php");

	$datamodel =new JEventsDataModel();
	$event = $datamodel->queryModel->listEventsById( $requestObject->rpid, 0,"icaldb");

	if (!$event || $event->rp_id()!= $requestObject->rpid)  throwerror("There was an error");

	if (!JEVHelper::canEditEvent($event)){
		throwerror("There was an error");
	}

	$value = JFilterInput::clean($requestObject->value, "string");

	// does the custom field exist already?
	$db = JFactory::getDBO();
	$sql = "SELECT * FROM #__jev_customfields where name=".$db->Quote($requestObject->field)." and evdet_id=".$event->_detail_id;
	$db->setQuery($sql);
	$field = $db->loadObject();
	
	if ($requestObject->append && $field){
		$value = $value."\n".$field->value;
	}
	
	$returnData->newvalue = nl2br($value);
	
	$value  = $db->Quote( $db->getEscaped( $value, true ), false );		
	

	if ($field){
		$sql = "UPDATE #__jev_customfields SET value=".$value. " WHERE name=".$db->Quote($requestObject->field)." and evdet_id=".$event->_detail_id;
		$db->setQuery($sql);
		$success = $db->query();
	}
	else {
		$sql = "INSERT INTO  #__jev_customfields (value, evdet_id, name ) VALUES(".$value.", ".intval($event->_detail_id).", ".$db->Quote($requestObject->field).")";
		$db->setQuery($sql);
		$success = $db->query();
	}

	if (!$success) $returnData->error = 1;
	
	$returnData->result = $success;

	return $returnData;
}



function throwerror ($msg){
	$data = new stdClass();
	//"document.getElementById('products').innerHTML='There was an error - no valid argument'");
	$data->error = "alert('".$msg."')";
	$data->result = "ERROR";
	$data->user = "";

	header("Content-Type: application/x-javascript");
	require 'jsonwrapper.php';
	// Must suppress any error messages
	@ob_end_clean();
	echo json_encode($data);
	exit();
}