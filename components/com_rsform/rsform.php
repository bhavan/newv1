<?php
/**
* @version 1.2.0
* @package RSform!Pro 1.2.0
* @copyright (C) 2007-2009 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


@session_start();
require_once(dirname(__FILE__).'/controller/adapter.php');

//create the RSadapter
$RSadapter = new RSadapter();
$GLOBALS['RSadapter'] = $RSadapter;
//require classes
require_once(_RSFORM_BACKEND_ABS_PATH.'/admin.rsform.html.php');
require_once(_RSFORM_FRONTEND_ABS_PATH.'/rsform.class.php');

//require controller
require_once(_RSFORM_FRONTEND_ABS_PATH.'/controller/functions.php');
require_once(_RSFORM_FRONTEND_ABS_PATH.'/controller/validation.php');

//require backend language file
require_once(_RSFORM_FRONTEND_ABS_PATH.'/languages/'._RSFORM_FRONTEND_LANGUAGE.'.php');

//$formId = $RSadapter->getMenuParam('formId',0);
$Itemid = JRequest::getVar('Itemid');
$menu =& JSite::getMenu();
$params =& $menu->getParams($Itemid);
$formId = $params->get('formId');

if(!$formId) $formId = intval( $RSadapter->getParam( $_REQUEST,'formId',0));

$task 			= $RSadapter->getParam( $_REQUEST, 'task' );


switch($task){

	case 'captcha':
		RScaptcha();
	break;

	case 'showJs':
		showJs();
	break;
	
    case 'plugin':
		$mainframe->triggerEvent('rsfp_f_onSwitchTasks');
	break;
	
	default:
		formsShow($formId);
	break;
}

function showJs()
{
	echo _RSFORM_FRONTEND_CALENDARJS;
	exit();
}

function RScaptcha()
{
	global $RSadapter;	
	$componentId	= intval( $RSadapter->getParam( $_GET,'componentId'));

	$captcha = new rsfp_captcha($componentId);

	$_SESSION['CAPTCHA'.$componentId] = $captcha->getCaptcha();
	exit;
}

function formsShow($formId)
{
	global $mainframe;
	$RSadapter = $GLOBALS['RSadapter'];
	
	$db = JFactory::getDBO();
	$db->setQuery("SELECT FormTitle, MetaTitle, MetaDesc, MetaKeywords FROM #__rsform_forms WHERE FormId='".(int) $formId."'");
	$form = $db->loadObject();
	
	$doc = JFactory::getDocument();
	$doc->setMetaData('description', $form->MetaDesc);
	$doc->setMetaData('keywords', $form->MetaKeywords);
	if ($form->MetaTitle)
		$doc->setTitle($form->FormTitle);
	
	if(isset($_SESSION['form'][$formId]['thankYouMessage']) && !empty($_SESSION['form'][$formId]['thankYouMessage']))
	{
		echo RSshowThankyouMessage($formId);
	}
	else
	{
		if(!empty($_POST['form']['formId']) && $_POST['form']['formId'] == $formId)
		{			
			$invalid = RSprocessForm($formId);		
			if($invalid)
			{
				//the invalid variable is returned
				echo RSshowForm($formId, $_POST['form'], $invalid);
			}
		}
		else
		{
			if(isset($_SESSION['form'][$formId]['thankYouMessage']) && empty($_SESSION['form'][$formId]['thankYouMessage']))
			{
				unset($_SESSION['form'][$formId]['thankYouMessage']);
				
				//is there a return url?
				$db->setQuery("SELECT ReturnUrl FROM #__rsform_forms WHERE `formId` = '".$formId."'");
				$returnUrl = $db->loadResult();
				if(!empty($returnUrl)) 
				{
					$returnUrl = stripslashes($returnUrl);
					if(!isset($_SESSION['form'][$formId]['submissionId']))$_SESSION['form'][$formId]['submissionId'] = '';
					$returnUrl = RSprocessField($returnUrl, $_SESSION['form'][$formId]['submissionId']);
					//unset($_SESSION['form'][$formId]['submissionId']);
					
					$RSadapter->redirect($returnUrl);
				}
								
				echo _RSFORM_FRONTEND_THANKYOU;
			}
			$args = array();
			$mainframe->triggerEvent('rsfp_f_onBeforeShowForm');
			echo RSshowForm($formId);
		}
	}
}

?>