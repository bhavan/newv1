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

	global $mainframe;
	$client = "site";
	if (isset($requestObject->client) && in_array($requestObject->client,array("site","administrator"))){
		$client = $requestObject->client;
	}
	$mainframe =& JFactory::getApplication($client);
	$mainframe->initialise();

	$token = JUtility::getToken();
	if (!isset($requestObject->token)  || $requestObject->token!=$token){
		throwerror("There was an error - bad token.  Please refresh the page and try again.");
	}
		
	$user = JFactory::getUser();
	if ($user->id==0){
		throwerror("There was an error");
	}

	if ($requestObject->error){
		return "Error";
	}
	if (isset($requestObject->title) && trim($requestObject->title)!==""){
		$returnData->result = "title is ".$requestObject->title;
	}
	else {
		throwerror ( "There was an error - no valid argument");
	}

	$db = JFactory::getDBO();

	$title = JFilterInput::clean($requestObject->title,"string");
	//$text  = $db->Quote( '%'.$db->getEscaped( $text, true ).'%', false );

	// Remove any dodgy characters from fields
	// Only allow a to z , 0 to 9, ', " space (\\040), hyphen (\\-), underscore (\\_)
	/*
	$regex     = '/[^a-zA-Z0-9_\'\"\'\\40\\-\\_]/';
	$title    = preg_replace($regex, "", $title);
	$title = substr($title."    ",0,4);
	*/

	if (trim($title)=="" && trim($title)==""){
		throwerror ( "There was an error - no valid argument");
	}

	if (strlen($title)<2 && $title!="*"){
		$returnData->result = 0;
		return $returnData;
	}
	
	$params = JComponentHelper::getParams("com_jevpeople");
	$linktouser = $params->get("linktouser",0);
	if (!$linktouser){
		$returnData->result = 0;
		return $returnData;
	}
	
	if (isset($requestObject->task) && $requestObject->task=="checkTitle"){
		// find user that has not already been used (subject to linktouser setting
		if ($title!="*"){
			// unique
			if ($linktouser==1){
				$rule = " AND jp.linktouser is null ";
			}
			// unique per type
			else if ($linktouser==2){
				$rule = " AND not (jp.linktouser is not null AND pt.type_id=".intval($requestObject->type).")";
			}
			else if ($linktouser==3){
				$rule = "";
			}

			$sql = "SELECT DISTINCT ju.username, ju.name, ju.id  FROM #__users as ju"
			." LEFT JOIN #__jev_people as jp ON jp.linktouser = ju.id"
			." LEFT JOIN #__jev_peopletypes as pt ON pt.type_id = jp.type_id"
			. " WHERE (ju.name LIKE ".$db->Quote($title."%")." OR ju.username LIKE ".$db->Quote($title."%")."  OR ju.email LIKE ".$db->Quote($title."%").") "
			. $rule
			." order by ju.name asc" ;
		}
		else {
			// unique
			if ($linktouser==1){
				$rule = " WHERE jp.linktouser is null ";
			}
			// unique per type
			else if ($linktouser==2){
				$rule = " WHERE not (jp.linktouser is not null AND pt.type_id=".intval($requestObject->type).")";
			}
			else if ($linktouser==3){
				$rule = "";
			}
			$sql = "SELECT DISTINCT ju.username, ju.name, ju.id  FROM #__users as ju" 
			." LEFT JOIN #__jev_people as jp ON jp.linktouser = ju.id"
			. $rule;
		}
	}
	$db->setQuery($sql);
	$matches = $db->loadObjectList();
	if (count($matches)==0){
		$returnData->result = 0;
	}
	else {
		$returnData->result = count($matches);
		foreach ($matches as $match) {
			if (trim(strtolower($match->name))==trim(strtolower($title)) || trim(strtolower($match->username))==trim(strtolower($title)))	$returnData->exactmatch=true;
			$returnData->titles[] = array("name"=>$match->name,"username"=>$match->username,"id"=>$match->id);
		}
	}

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