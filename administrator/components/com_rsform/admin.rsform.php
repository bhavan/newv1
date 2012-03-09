<?php
/**
* @version 1.2.0
* @package RSform!Pro 1.2.0
* @copyright (C) 2007-2009 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
global $mainframe;

//Trigger Event - rsfp_bk_onBeforeInit
$mainframe->triggerEvent('rsfp_bk_onBeforeInit');


ini_set('max_execution_time','300');
require_once(dirname(__FILE__).'/../../../components/com_rsform/controller/adapter.php');

//create the RSadapter
$GLOBALS['RSadapter'] = new RSadapter();
$RSadapter = $GLOBALS['RSadapter'];

//$RSadapter = $GLOBALS['RSadapter'];


//require classes
require_once(_RSFORM_BACKEND_ABS_PATH.'/admin.rsform.html.php');
require_once(_RSFORM_FRONTEND_ABS_PATH.'/rsform.class.php');

//require controller
require_once(_RSFORM_FRONTEND_ABS_PATH.'/controller/functions.php');

//require backend language file
require_once(_RSFORM_FRONTEND_ABS_PATH.'/languages/'._RSFORM_BACKEND_LANGUAGE.'.php');

//get task
$task           = $RSadapter->getParam($_REQUEST, 'task');
// get form id
$formId 		= $RSadapter->getParam($_REQUEST, 'formId');


//Trigger Event - rsfp_bk_onInit
$mainframe->triggerEvent('rsfp_bk_onInit');

 /*
$cid 	= mosGetParam($_REQUEST, 'cid', array());


$layout= mosGetParam($_GET, 'layout', null);

$limit 			= intval( mosGetParam( $_REQUEST, 'limit', 15 ) );
$limitstart 	= intval( mosGetParam( $_REQUEST, 'limitstart', 0 ) );
*/
header('Content-type: text/html; charset=utf-8');
switch($task)
{
	case 'debug':
	
	break;
	
	case 'richtext.show':
		richtextShow();
	break;
	
//FORMS
    case 'forms.manage':
        formsManage();
    break;

    case 'forms.edit':
        formsEdit($formId);
    break;

    case 'forms.cancel':
        formsCancel($option);
    break;

    case 'forms.save':
        formsSave($option, 0);
    break;

    case 'forms.apply':
        formsSave($option, 1);
    break;

    case 'forms.delete':
        formsDelete($option);
    break;

    case 'forms.copy':
    	formsCopy($option);
    break;

	case "forms.publish":
		formsPublish( $option, 1);
	break;

	case "forms.unpublish":
		formsPublish( $option, 0 );
	break;

	case "forms.preview":
		formsPreview( $option );
	break;

	case "forms.menuadd.screen":
		formsMenuaddScreen( $option );
	break;

	case "forms.menuadd.process":
		formsMenuaddProcess( $option );
	break;

    case 'forms.changeAutoGenerateLayout':
        formsChangeAutoGenerateLayout($option, $formId);
        exit();
    break;

//COMPONENTS

	case 'components.validate.name':
		componentsValidateName($option);
		exit();
	break;

	case 'components.display':
		componentsDisplay($option);
		exit();
	break;

	case 'components.movedown':
		componentsMoveDown($option);
	break;

	case 'components.moveup':
		componentsMoveUp($option);
	break;

	case 'components.copy.screen':
		componentsCopyScreen($option);
	break;

	case 'components.copy.process':
		componentsCopyProcess($option);
	break;

	case 'components.cancel':
		componentsCancel($option);
	break;

	case 'components.changestatus':
		componentsChangeStatus($option);
		exit();
	break;

	case 'components.remove':
		componentsRemove($option);
		exit();
	break;

//LAYOUTS
	case 'layouts.generate':
		layoutsGenerate($option, $formId);
		exit();
	break;

	case 'layouts.saveLayoutName':
		layoutsSaveName($formId);
		exit();
	break;
//SUBMISSIONS
	case 'submissions.manage':
		submissionsManage($option, $formId);
	break;
	case 'submissions.edit':
		submissionsEdit($option, $formId);
	break;
	case 'submissions.delete':
		submissionsDelete($option);
	break;
	case 'submissions.delete.all':
		submissionsDelete($option,-1);
	break;
	case 'submissions.export':
		submissionsExport($option);
	break;
	case 'submissions.export.process':
		submissionsExportProcess($option);
	break;
	case 'submissions.export.task':
		submissionsExportTask($option);
	break;
	case 'submissions.export.file':
		submissionsExportFile($option);
	break;
	case 'submissions.resend':
		submissionsResend();
		exit();
	break;

//CONFIGURATION
	case 'configuration.save':
		configurationSave($option);
	break;

	case 'configuration.edit':
		configurationEdit($option);
	break;

//BACKUP/RESTORE
	case 'backup.restore':
		backupRestore($option);
	break;

	case 'backup.download':
		backupDownload($option);
	break;
	

//UPDATE
	case 'updates.manage':
		updatesManage($option);
	break;

	case 'update.upload.process':
		updateUploadProcess($option);
	break;

	
//CONTROL PANEL
    case 'saveRegistration':
        saveRegistration($option);
    break;

    case 'plugin':
		$mainframe->triggerEvent('rsfp_bk_onSwitchTasks');
	break;
	
	case 'goto.plugins':
		global $mainframe;
		$mainframe->redirect('http://www.rsjoomla.com/joomla-plugins/rsform-pro.html');
	break;
	
	case 'goto.support':
		global $mainframe;
		$mainframe->redirect('http://www.rsjoomla.com/customer-support/documentations/21-rsform-pro-user-guide.html');
	break;
	
	default:
		rsform_HTML::controlPanel();
	break;
	
}

function richtextShow()
{
	$RSadapter = $GLOBALS['RSadapter'];
	$formId = intval($RSadapter->getParam($_GET,'formId'));
	$openerId = RScleanVar($RSadapter->getParam($_GET, 'openerId'));
	
	$additionalHTML = '
	<script type="text/javascript">
		window.opener.document.getElementById(\''.$openerId.'\').innerHTML = document.getElementById(\''.$openerId.'\').value;
	</script>
	
	';
	
	$db = JFactory::getDBO();
		
	if (isset($_POST[$openerId]))
		$_POST[$openerId] = RScleanVar(trim($_POST[$openerId]));
	
	if(isset($_POST['act']))
	{
		$db->setQuery("UPDATE #__rsform_forms SET `".$openerId."` = '".$_POST[$openerId]."' WHERE `FormId` = '".$formId."'");
		$db->query();
		
		if ($_POST['act'] == 'saveclose')
			$additionalHTML .= '
				<script type="text/javascript">
					window.close();
				</script>
				';
	}
	
	//get value
	$db->setQuery("SELECT ".$openerId." FROM #__rsform_forms WHERE FormId = '".$formId."'");
	$r = $db->loadResult();
	
	rsform_HTML::richtextShow($formId, $openerId, $r, $additionalHTML);
}
//////////////////////////////////////// FORMS ////////////////////////////////////////
/**
* @desc Forms Manager Screen
*/
function formsManage()
{
    $RSadapter = $GLOBALS['RSadapter'];

	$db = JFactory::getDBO();
	$db->setQuery("SELECT * FROM #__rsform_forms ORDER BY `FormId` DESC");
	$forms = $db->loadAssocList();

    $rows = array();
    foreach ($forms as $r)
	{
		$db->setQuery("SELECT COUNT(`SubmissionId`) cnt FROM #__rsform_submissions WHERE date_format(DateSubmitted,'%Y-%m-%d') = '".date('Y-m-d')."' AND FormId='".$r['FormId']."'");
		$r['_todaySubmissions'] = $db->loadResult();

		$db->setQuery("SELECT COUNT(`SubmissionId`) cnt FROM #__rsform_submissions WHERE date_format(DateSubmitted,'%Y-%m') = '".date('Y-m')."' AND FormId='".$r['FormId']."'");
        $r['_monthSubmissions'] = $db->loadResult();
		
		$db->setQuery("SELECT COUNT(`SubmissionId`) cnt FROM #__rsform_submissions WHERE FormId='".$r['FormId']."'");
		$r['_allSubmissions'] = $db->loadResult();

        $rows[] = $r;
    }
    rsform_HTML::formsManage($rows);
}

/**
 * Forms Publish/Unpublish Process
 *
 * @param str $option
 * @param int $publishform
 */
function formsPublish( $option, $publishform=1 )
{
	$RSadapter = $GLOBALS['RSadapter'];

	$publishform = intval($publishform);
  	$cids = $RSadapter->getParam($_POST,'cid');
  	$total = count($cids);
  	$formIds = implode(',', $cids);

	$db = JFactory::getDBO();
	
	if ($total > 0)
	{
		$db->setQuery("UPDATE #__rsform_forms SET Published = '".$publishform."' WHERE FormId IN (".$formIds.")");
		$db->query();
	}

    switch ($publishform)
	{
		case 1:
			$msg = $total ._RSFORM_BACKEND_SUC_PUBL_FORM.' ';
		break;
		
		case 0:
		default:
			$msg = $total ._RSFORM_BACKEND_SUC_UNPUBL_FORM.' ';
		break;
	}

	$RSadapter->redirect( _RSFORM_BACKEND_SCRIPT_PATH.'?option='.$option.'&task=forms.manage', $msg );

}

/**
 * Forms Menu Add Screen
 *
 * @param str $option
 */
function formsMenuaddScreen($option)
{
	$RSadapter = $GLOBALS['RSadapter'];

	$formId = intval($RSadapter->getParam($_REQUEST,'formId'));

	$db = JFactory::getDBO();
	
	//get form title
	$db->setQuery("SELECT FormTitle FROM #__rsform_forms WHERE FormId = '".$formId."'");
	$formTitle = $db->loadResult();

	$menus = $RSadapter->getMenus();

	rsform_HTML::formsMenuaddScreen($option, $menus, $formId, $formTitle);
}

/**
 * Forms Menu Add Process
 *
 * @param str $option
 */
function formsMenuaddProcess($option)
{
	$RSadapter = $GLOBALS['RSadapter'];

	$formId = intval($RSadapter->getParam($_REQUEST,'formId'));
	$menu = $RSadapter->getParam($_REQUEST,'menu');
	$menuTitle = $RSadapter->getParam($_REQUEST,'menutitle');

	$db = JFactory::getDBO();
	
	//get form title
	$db->setQuery("SELECT FormTitle FROM #__rsform_forms WHERE FormId = '".$formId."'");
	$formTitle = $db->loadResult();
	
	//insert
	$RSadapter->addMenu($formId, $menuTitle, $menu);

	$RSadapter->redirect( _RSFORM_BACKEND_SCRIPT_PATH.'?option='.$option.'&task=forms.manage', _RSFORM_BACKEND_FORMS_MENUADD_ADDED );
}
/**
 * Forms Preview Process
 *
 * @param str $option
 */
function formsPreview($option)
{
	$RSadapter = $GLOBALS['RSadapter'];

	$formId = intval($RSadapter->getParam($_REQUEST,'formId'));

	?>
	<script type="text/javascript">
		window.open('<?php echo _RSFORM_FRONTEND_SCRIPT_PATH.'/index.php?option='.$option.'&formId='.$formId;?>');
		document.location='<?php echo _RSFORM_BACKEND_SCRIPT_PATH.'?option='.$option.'&task=forms.edit&formId='.$formId;?>';
	</script>
	<?php
}

/**
 * Forms Copy Process
 */
function formsCopy($option)
{
	$RSadapter = $GLOBALS['RSadapter'];
	$formIds = $RSadapter->getParam($_POST,'cid');

	$total = count($formIds);
	if ($total > 0)
		foreach($formIds as $formId)
			RScopyForm($formId);

	$msg = $total._RSFORM_BACKEND_FORMS_COPY.' ';
	$RSadapter->redirect( _RSFORM_BACKEND_SCRIPT_PATH.'?option='. $option .'&task=forms.manage', $msg );
}
/**
 * Forms Delete Process
 *
 * @param str $option
 */
function formsDelete($option)
{
	$RSadapter = $GLOBALS['RSadapter'];

	$formIds = $RSadapter->getParam($_POST,'cid');
	$total = count($formIds);
	
	$db = JFactory::getDBO();
	
	if ($total > 0)
		foreach($formIds as $formId)
		{
			$formId = intval($formId);
			
			//Delete Submissions
			$db->setQuery("DELETE FROM #__rsform_submissions WHERE FormId = '".$formId."'");
			$db->query();
			$db->setQuery("DELETE FROM #__rsform_submission_values WHERE FormId = '".$formId."'");
			$db->query();

			//Delete Components
			$componentIds = array();
			$db->setQuery("SELECT ComponentId FROM #__rsform_components WHERE FormId = '".$formId."'");
			$rows = $db->loadAssocList();
			foreach ($rows as $row)
				$componentIds[] = $row['ComponentId'];
			
			if (!empty($componentIds))
			{
				$components = implode(',',$componentIds);
				$db->setQuery("DELETE FROM #__rsform_properties WHERE ComponentId IN (".$components.")");
				$db->query();
				$db->setQuery("DELETE FROM #__rsform_components WHERE ComponentId IN (".$components.")");
				$db->query();
			}

			//Delete Forms
			$db->setQuery("DELETE FROM #__rsform_forms WHERE FormId = '".$formId."'");
			$db->query();
		}
	
	$msg = $total ._RSFORM_BACKEND_FORMS_DEL.' ';
	$RSadapter->redirect( _RSFORM_BACKEND_SCRIPT_PATH.'?option='. $option .'&task=forms.manage', $msg );
}

/**
 * Forms Edit Screen
 *
 * @param int $formId
 */
function formsEdit($formId)
{
	$RSadapter = $GLOBALS['RSadapter'];
    global $option;

	$formId = intval($formId);
	
	$db = JFactory::getDBO();
	
    if(isset($_POST['ordering']))
    {
        $formId = intval($_POST['formId']);
        $order = $_POST['ordering'];
        asort($order);
        $i=1;
        foreach($order as $key => $val)
        {
            $val = $i++;
			$key = intval($key);
			$db->setQuery("UPDATE #__rsform_components SET `Order`='".$val."' WHERE ComponentId='".$key."'");
			$db->query();
        }
    }
	
    if (isset($_GET['formId']))
        $formId = intval($_GET['formId']);
    
	if (!isset($_GET['formId']) && !isset($_POST['formId']))
    {
		$db->setQuery("INSERT INTO #__rsform_forms SET `FormName`='"._RSFORM_BACKEND_FORMS_EDIT_NO_FORM_NAME."', `FormTitle`='"._RSFORM_BACKEND_FORMS_EDIT_NO_FORM_TITLE."', `FormLayout`='', `FormLayoutName`='inline', `FormLayoutAutogenerate`='1', `Required`='(*)', `ErrorMessage`='<p class=\"formRed\">Please complete all required fields!</p>'");
		$db->query();
		$formId = $db->insertid();

        $layout = @include(_RSFORM_BACKEND_ABS_PATH.'/layouts/inline.php');
		$db->setQuery("UPDATE #__rsform_forms SET `FormLayout` = '".$layout."' WHERE FormId = '".$formId."'");
		$db->query();
    }
	
    if(isset($_POST['COMPONENTTYPE']))
    {
        if($_POST['componentIdToEdit']!=-1)
        {
			$_POST['componentIdToEdit'] = intval($_POST['componentIdToEdit']);
            foreach($_POST['param'] as $key => $val)
            {
				$val = RScleanVar($val);
				$key = RScleanVar($key);
				$db->setQuery("UPDATE #__rsform_properties set PropertyValue='".$val."' WHERE ComponentId='".$_POST['componentIdToEdit']."' AND PropertyName='".$key."'");
				$db->query();
            }
        }
        else
        {
			$db->setQuery("SELECT MAX(`Order`)+1 AS MO FROM #__rsform_components WHERE FormId='".$formId."'");
			$nextOrder = $db->loadResult();
            
			$db->setQuery("INSERT INTO #__rsform_components SET FormId='".$_POST['formId']."', ComponentTypeId='".$_POST['COMPONENTTYPE']."', `Order`='".$nextOrder."'");
			$db->query();

			$componentId = $db->insertid();
            $values = $_POST['param'];
			
            foreach($values as $key => $value)
            {
				$value = RScleanVar($value);
				$key = RScleanVar($key);
				$db->setQuery("INSERT INTO #__rsform_properties SET ComponentId='".$componentId."', PropertyName='".$key."', PropertyValue='".$value."'");
				$db->query();
            }
        }
        $formId = intval($_POST['formId']);
    }
	
	$db->setQuery("SELECT * FROM #__rsform_forms WHERE FormId='".$formId."'");
	$row = $db->loadAssoc();
    rsform_HTML::formsEdit($formId, $row);
}

/**
 * Forms Save Process
 *
 * @param str $option
 * @param int $apply
 */
function formsSave($option,$apply=0)
{
    $RSadapter = $GLOBALS['RSadapter'];

	$db = JFactory::getDBO();
	
    foreach($_POST as $key=>$value)
    	$row[$key] = RScleanVar($RSadapter->getParam($_POST,$key));
    
//    	`FormLayoutAutogenerate`= '{$row['FormLayoutAutogenerate']}',
	$db->setQuery("UPDATE #__rsform_forms SET
    	`FormName` 				= '".$row['FormName']."',
    	`FormLayout` 			= '".$row['FormLayout']."',
    	`FormTitle`				= '".$row['FormTitle']."',
    	`ReturnUrl`				= '".$row['ReturnUrl']."',
    	`UserEmailTo`			= '".$row['UserEmailTo']."',
    	`UserEmailCC`			= '".$row['UserEmailCC']."',
    	`UserEmailBCC`			= '".$row['UserEmailBCC']."',
    	`UserEmailFrom`			= '".$row['UserEmailFrom']."',
    	`UserEmailReplyTo`		= '".$row['UserEmailReplyTo']."',
    	`UserEmailFromName`		= '".$row['UserEmailFromName']."',
    	`UserEmailSubject`		= '".$row['UserEmailSubject']."',
    	`UserEmailMode`			= '".$row['UserEmailMode']."',
		`UserEmailAttach`		= '".$row['UserEmailAttach']."',
		`UserEmailAttachFile`	= '".$row['UserEmailAttachFile']."',
    	".($row['UserEmailMode'] ? '':"`UserEmailText` = '".$row['UserEmailText']."',")."
    	".($row['AdminEmailMode'] ? '':"`AdminEmailText` = '".$row['AdminEmailText']."',")."
    	`AdminEmailTo`			= '".$row['AdminEmailTo']."',
    	`AdminEmailCC`			= '".$row['AdminEmailCC']."',
    	`AdminEmailBCC`			= '".$row['AdminEmailBCC']."',
    	`AdminEmailFrom`		= '".$row['AdminEmailFrom']."',
    	`AdminEmailReplyTo`		= '".$row['AdminEmailReplyTo']."',
    	`AdminEmailFromName`	= '".$row['AdminEmailFromName']."',
    	`AdminEmailSubject`		= '".$row['AdminEmailSubject']."',
    	`AdminEmailMode`		= '".$row['AdminEmailMode']."',
    	`ScriptProcess`			= '".$row['ScriptProcess']."',
    	`ScriptProcess2`		= '".$row['ScriptProcess2']."',
    	`ScriptDisplay`			= '".$row['ScriptDisplay']."',
    	`MetaTitle`				= '".$row['MetaTitle']."',
    	`MetaDesc`				= '".$row['MetaDesc']."',
    	`MetaKeywords`			= '".$row['MetaKeywords']."',
    	`Required`			= '".$row['Required']."'
    WHERE
    	`FormId` 				= '".$row['formId']."'");
	$db->query();

    if(!$apply)
		$RSadapter->redirect(_RSFORM_BACKEND_SCRIPT_PATH."?option=$option&task=forms.manage", _RSFORM_BACKEND_FORMS_SAVE." ");
    else
		$RSadapter->redirect(_RSFORM_BACKEND_SCRIPT_PATH."?option=$option&task=forms.edit&formId=".$row['formId'], _RSFORM_BACKEND_FORMS_SAVE." ");
}

/**
 * Closes the form
 *
 * @param str $option
 */
function formsCancel($option)
{
	$RSadapter = $GLOBALS['RSadapter'];

    $RSadapter->redirect(_RSFORM_BACKEND_SCRIPT_PATH."?option=$option&task=forms.manage" );
}
/**
 * Change the AutoGenerate layout
 *
 * @param unknown_type $option
 * @param unknown_type $formId
 * @param unknown_type $formLayoutName
 */
function formsChangeAutoGenerateLayout($option, $formId)
{
	$RSadapter = $GLOBALS['RSadapter'];

	$formLayoutName = RScleanVar($RSadapter->getParam($_GET, 'formLayoutName'));
	$formId = intval($formId);
	
	$db = JFactory::getDBO();
	$db->setQuery("UPDATE #__rsform_forms SET `FormLayoutAutogenerate` = ABS(FormLayoutAutogenerate-1), `FormLayoutName`='".$formLayoutName."' WHERE `FormId` = '".$formId."'");
	$db->query();
}

//////////////////////////////////////// COMPONENTS ////////////////////////////////////////
/**
 * Validates a component name
 *
 * @param str $option
 */
function componentsValidateName($option)
{
	$RSadapter = $GLOBALS['RSadapter'];

	$componentName 		= RScleanVar($RSadapter->getParam($_GET, 'componentName'));
	$currentComponentId = intval($RSadapter->getParam($_GET, 'currentComponentId'));
	$componentId		= intval($RSadapter->getParam($_GET, 'componentId'));
	$componentType		= intval($RSadapter->getParam($_GET, 'componentType'));
	$destination		= $RSadapter->getParam($_GET, 'destination');
	$formId				= intval($RSadapter->getParam($_GET, 'formId'));

	$componentName = trim($componentName);
	if(eregi('[^a-zA-Z0-9_ ]', $componentName ) || empty($componentName))
	{
		echo _RSFORM_BACKEND_COMPONENTS_VALIDATE_ERROR_UNIQUE_NAME;
		return;
	}
	
	//on file upload component, check destination
	if($componentType==9)
	{
		if (empty($destination))
		{
			echo _RSFORM_BACKEND_COMPONENTS_VALIDATE_ERROR_DESTINATION;
			return;
		}
		if(!is_dir($destination))
		{
			echo _RSFORM_BACKEND_COMPONENTS_VALIDATE_ERROR_DESTINATION_NOT_DIR;
			return;
		}
		if(!is_writable($destination))
		{
			echo _RSFORM_BACKEND_COMPONENTS_VALIDATE_ERROR_DESTINATION_NOT_WRITABLE;
			return;
		}
	}
	
	$db = JFactory::getDBO();
	
	if ($currentComponentId == 0)
		$db->setQuery("SELECT #__rsform_forms.FormId, #__rsform_properties.PropertyName, #__rsform_properties.PropertyValue FROM #__rsform_components LEFT JOIN #__rsform_properties ON #__rsform_components.ComponentId = #__rsform_properties.ComponentId LEFT JOIN #__rsform_forms ON #__rsform_components.FormId = #__rsform_forms.FormId WHERE #__rsform_forms.FormId='".$formId."' AND #__rsform_properties.PropertyName='NAME' AND #__rsform_properties.PropertyValue='".$componentName."'");
	else
		$db->setQuery("SELECT #__rsform_forms.FormId, #__rsform_properties.PropertyName, #__rsform_properties.PropertyValue FROM #__rsform_components LEFT JOIN #__rsform_properties ON #__rsform_components.ComponentId = #__rsform_properties.ComponentId LEFT JOIN #__rsform_forms ON #__rsform_components.FormId = #__rsform_forms.FormId WHERE #__rsform_forms.FormId='".$formId."' AND #__rsform_properties.PropertyName='NAME' AND #__rsform_properties.PropertyValue='".$componentName."' AND #__rsform_components.ComponentId!='".$currentComponentId."'");
	
	$db->query();
	$exists = $db->getNumRows();
	
	if ($exists)
		echo _RSFORM_BACKEND_COMPONENTS_VALIDATE_ERROR_UNIQUE_NAME;
	else
		echo 'Ok';

	exit();
}

/**
 * Displays a component in the backend.
 *
 * @param unknown_type $option
 */
function componentsDisplay($option)
{
	$RSadapter = $GLOBALS['RSadapter'];
	$componentId = intval($RSadapter->getParam($_GET, 'componentId'));
	$componentType = intval($RSadapter->getParam($_GET, 'componentType'));
	$RSadapter->addHeadTag(_RSFORM_FRONTEND_REL_PATH.'/controller/functions.js','js','other');
	$db = JFactory::getDBO();
	$db->setQuery("SELECT * FROM #__rsform_component_type_fields WHERE ComponentTypeId='".$componentType."' ORDER BY Ordering");
	$fields = $db->loadAssocList();
	
	$data = array();
	$out = '';
	if ($componentId > 0)
		$data=RSgetComponentProperties($componentId);
	
	$out.='<table class="componentForm" border="0" cellspacing="0" cellpadding="0">';
	$counter = 0;
	foreach ($fields as $r)
	{
		$db->query();
		if ($counter==2 && $db->getNumRows() > 3)
			$out.= '<tr><td><input type="button" onclick="processComponent('.$componentType.')" value="'._RSFORM_BACKEND_COMP_SAVE.'" style="float:right; margin-right:20px;"></td></tr>';
		$out.='<tr id="id'.$r['FieldName'].'">';
		
		switch($r['FieldType'])
		{
			case 'textbox':
			{
				$out.='<td>'.(defined('_RSFORM_BACKEND_COMP_FIELD_'.$r['FieldName']) ? constant('_RSFORM_BACKEND_COMP_FIELD_'.$r['FieldName']):JText::_('_RSFORM_BACKEND_COMP_FIELD_'.$r['FieldName'])).'<br/>';
				if ($componentId > 0)
				{	
					$val = (defined('_RSFORM_BACKEND_COMP_FVALUE_'.@$data[$r['FieldName']]) ? constant('_RSFORM_BACKEND_COMP_FVALUE_'.@$data[$r['FieldName']]) : @$data[$r['FieldName']]);
					$out .= '<input type="text" id="'.$r['FieldName'].'" name="param['.$r['FieldName'].']" value="'.RSshowVar(@$data[$r['FieldName']]).'" class="wide"></td>';
				}
				else
				{
					$val = (defined('_RSFORM_BACKEND_COMP_FVALUE_'.RSisCode($r['FieldValues'])) ? constant('_RSFORM_BACKEND_COMP_FVALUE_'.RSisCode($r['FieldValues'])) : RSisCode($r['FieldValues']));
					$out .= '<input type="text" id="'.$r['FieldName'].'" name="param['.$r['FieldName'].']" value="'.$val.'" class="wide"></td>';
				}
			}
			break;

			case 'textarea':
			{
				$out .= '<td>'.(defined('_RSFORM_BACKEND_COMP_FIELD_'.$r['FieldName']) ? constant('_RSFORM_BACKEND_COMP_FIELD_'.$r['FieldName']):JText::_('_RSFORM_BACKEND_COMP_FIELD_'.$r['FieldName'])).'<br/>';				
				if ($componentId > 0)
				{
					$constant = str_replace('::','',$data[$r['FieldName']]);
					$val = (defined('_RSFORM_BACKEND_COMP_FVALUE_'.$constant) ? constant($constant) : $data[$r['FieldName']]);
					$out .= '<textarea id="'.$r['FieldName'].'" name="param['.$r['FieldName'].']" rows="5" cols="20" class="wide">'.RSshowVar($val).'</textarea></td>';
				}
				else
				{
					$val = (defined('_RSFORM_BACKEND_COMP_FVALUE_'.RSisCode($r['FieldValues'])) ? constant('_RSFORM_BACKEND_COMP_FVALUE_'.RSisCode($r['FieldValues'])) : RSisCode($r['FieldValues']));
					$out .= '<textarea id="'.$r['FieldName'].'" name="param['.$r['FieldName'].']" rows="5" cols="20" class="wide">'.$val.'</textarea></td>';
				}
			}
			break;
			
			case 'select':
			{
				$out .= '<td>'.(defined('_RSFORM_BACKEND_COMP_FIELD_'.$r['FieldName']) ? constant('_RSFORM_BACKEND_COMP_FIELD_'.$r['FieldName']):JText::_('_RSFORM_BACKEND_COMP_FIELD_'.$r['FieldName'])).'<br/>';
				$out .= '<select name="param['.$r['FieldName'].']" id="'.$r['FieldName'].'" onchange="changeValidation(this);">';
				$r['FieldValues'] = str_replace("\r",'',$r['FieldValues']);
				$aux = RSisCode($r['FieldValues']);
				$items = explode("\n",$aux);				
				foreach($items as $item)
				{
					$buf = explode('|',$item);
					
					$option_value = $buf[0];
					$option_shown = count($buf) == 1 ? $buf[0] : $buf[1];
					$label = (defined('_RSFORM_BACKEND_COMP_FVALUE_'.$option_shown) ? constant('_RSFORM_BACKEND_COMP_FVALUE_'.$option_shown) : $option_shown);

					$out .= '<option '.($componentId > 0 && $data[$r['FieldName']] == $option_value ? 'selected="selected"' : '').' value="'.$option_value.'">'.RSshowVar($label).'</option>';
				}
				$out .= '</select></td>';	
			}
			break;
			
			case 'hidden':
			{
				$val = (defined('_RSFORM_BACKEND_COMP_FVALUE_'.$r['FieldValues']) ? constant('_RSFORM_BACKEND_COMP_FVALUE_'.$r['FieldValues']) : $r['FieldValues']);
				$out .= '<td><input type="hidden" id="'.$r['FieldName'].'" name="'.$r['FieldName'].'" value="'.RSshowVar($val).'"></td>';
			}
			break;
			
			case 'hiddenparam':
				$val = $r['FieldValues'];
				$out .= '<td><input type="hidden" id="'.$r['FieldName'].'" name="param['.$r['FieldName'].']" value="'.RSshowVar($val).'"></td>';
			break;
		}
		
		if ($componentId > 0)
			$out .= '<input type="hidden" name="updateComponent">';
			
		$out .= '</tr>';
		$counter++;
	}
	$out .= '<tr><td><input type="button" onclick="processComponent('.$componentType.')" value="'._RSFORM_BACKEND_COMP_SAVE.'" style="float:right; margin-right:20px;"></td></tr>';
	$out .= '<tr><td>&nbsp;</td></tr>';
	$out .= '</table>';
	

	echo $out;
}

/**
 * Moves the component up
 *
 * @param str $option
 */
function componentsMoveUp($option)
{
	$RSadapter = $GLOBALS['RSadapter'];
	
	$componentId = intval($RSadapter->getParam($_GET, 'componentId'));
	$formId = intval($RSadapter->getParam($_GET, 'formId'));

	$db = JFactory::getDBO();
	
	$db->setQuery("SELECT `Order` FROM #__rsform_components WHERE FormId='".$formId."' AND ComponentId='".$componentId."'");
	$order = $db->loadResult();

	if ($order > 1)
	{
		$order -= 1;
		$db->setQuery("SELECT ComponentId FROM #__rsform_components WHERE FormId='".$formId."' AND `Order`='".$order."'");
		$id = $db->loadResult();
		$db->setQuery("UPDATE #__rsform_components SET `Order`=`Order`-1 WHERE ComponentId='".$componentId."' AND FormId='".$formId."'");
		$db->query();
		$db->setQuery("UPDATE #__rsform_components SET `Order`=`Order`+1 WHERE ComponentId='".$id."' AND FormId='".$formId."'");
		$db->query();
	}
}

/**
 * Moves the component down
 *
 * @param str $option
 */
function componentsMoveDown($option)
{
	$RSadapter = $GLOBALS['RSadapter'];
	
	$db = JFactory::getDBO();
	
	$componentId = intval($RSadapter->getParam($_GET, 'componentId'));
	$formId = intval($RSadapter->getParam($_GET, 'formId'));

	$db->setQuery("SELECT COUNT(ComponentId) AS number FROM #__rsform_components WHERE FormId='".$formId."'");
	$max = $db->loadResult();
	
	$db->setQuery("SELECT `Order` FROM #__rsform_components WHERE FormId='".$formId."' AND ComponentId='".$componentId."'");
	$order = $db->loadResult();
	
	if ($order < $max)
	{
		$order += 1;
		$db->setQuery("SELECT ComponentId FROM #__rsform_components WHERE FormId='".$formId."' AND `Order`='".$order."'");
		$id = $db->loadResult();
		$db->setQuery("UPDATE #__rsform_components SET `Order`=`Order`+1 WHERE ComponentId='".$componentId."' AND FormId='".$formId."'");
		$db->query();
		$db->setQuery("UPDATE #__rsform_components SET `Order`=`Order`-1 WHERE ComponentId='".$id."' AND FormId='".$formId."'");
		$db->query();
	}
}

/**
 * Components Cancel
 *
 * @param str $option
 */
function componentsCancel($option)
{
	$RSadapter = $GLOBALS['RSadapter'];

	$formId = $RSadapter->getParam($_POST, 'formId');

	$RSadapter->redirect(_RSFORM_BACKEND_SCRIPT_PATH.'?option='.$option.'&task=forms.edit&formId='.$formId);
}

/**
 * Components Copy Process
 *
 * @param str $option
 */
function componentsCopyProcess($option)
{
	$RSadapter = $GLOBALS['RSadapter'];
	$formId = intval($RSadapter->getParam($_POST, 'formId'));
	$toFormId = intval($RSadapter->getParam($_POST, 'toFormId', 0));
	$componentsToCopy = $RSadapter->getParam($_POST, 'componentId', array());

	if ($toFormId > 0 && !empty($componentsToCopy))
		foreach($componentsToCopy as $componentToCopyId)
			RScopyComponent($componentToCopyId,$toFormId);

	$RSadapter->redirect(_RSFORM_BACKEND_SCRIPT_PATH.'?option='.$option.'&task=forms.edit&formId='.$toFormId,_RSFORM_BACKEND_COMPONENTS_COPY_OK);
}

/**
 * Components Copy Screen
 *
 * @param str $option
 */
function componentsCopyScreen($option)
{
	$RSadapter = $GLOBALS['RSadapter'];
	$formId = intval($RSadapter->getParam($_REQUEST, 'formId'));
	$components = $RSadapter->getParam($_REQUEST,'checks',array());
	//load all forms
	$db = JFactory::getDBO();
	$db->setQuery("SELECT FormId, FormTitle FROM #__rsform_forms");
	$result = $db->loadAssocList();

	$forms = array();
	foreach ($result as $r)
		$forms[$r['FormId']] = $r['FormTitle'];
	
	rsform_HTML::componentsCopyScreen($option, $forms, $components, $formId);
}

/**
 * Publish / Unpublish a component
 *
 * @param str $option
 */
function componentsChangeStatus($option)
{
	$RSadapter = $GLOBALS['RSadapter'];
	$componentId = intval($RSadapter->getParam($_GET, 'componentId'));

	$db = JFactory::getDBO();
	
	//get current status
	$db->setQuery("SELECT `Published` FROM #__rsform_components WHERE ComponentId='".$componentId."'");
	$currentStatus = $db->loadResult();
	$newStatus = ($currentStatus) ? 0 : 1;
	$db->setQuery("UPDATE #__rsform_components SET published = '".$newStatus."' WHERE ComponentId='".$componentId."'");
	$db->query();
}

/**
 * Remove Component
 *
 * @param str $option
 */
function componentsRemove($option)
{
	$RSadapter = $GLOBALS['RSadapter'];
	$componentId = intval($RSadapter->getParam($_GET, 'componentId'));
	$formId = intval($RSadapter->getParam($_GET, 'formId'));

	$db = JFactory::getDBO();
	
	$db->setQuery("DELETE FROM #__rsform_components WHERE ComponentId='".$componentId."'");
	$db->query();
	$db->setQuery("DELETE FROM #__rsform_properties WHERE ComponentId='".$componentId."'");
	$db->query();
	
	$db->setQuery("SELECT ComponentId FROM #__rsform_components WHERE FormId='".$formId."' ORDER BY `Order`");
	$components = $db->loadAssocList();
	$i = 1;
	foreach ($components as $r)
	{
		$db->setQuery("UPDATE #__rsform_components SET `Order`='".$i."' WHERE ComponentId='".$r['ComponentId']."'");
		$db->query();
		$i++;
	}
}

//////////////////////////////////////// LAYOUTS ////////////////////////////////////////

function layoutsGenerate($option, $formId)
{
	$RSadapter = $GLOBALS['RSadapter'];
	$layout = $RSadapter->getParam($_GET,'layout');

	$bad = array('\\','/');
	$layout = str_replace($bad,'',$layout);
	echo require_once(_RSFORM_BACKEND_ABS_PATH.'/layouts/'.$layout.'.php');
}

function layoutsSaveName($formId)
{
	$RSadapter = $GLOBALS['RSadapter'];
	$formId = intval($formId);
	
	$db = JFactory::getDBO();
	
	$formLayoutName = RScleanVar($RSadapter->getParam($_GET,'formLayoutName'));
	
	$db->setQuery("UPDATE #__rsform_forms SET FormLayoutName='".$formLayoutName."' WHERE FormId='".$formId."'");
	$db->query();
}

//////////////////////////////////////// SUBMISSIONS ////////////////////////////////////////
/**
 * Submissions Manager Screen
 *
 * @param str $option
 * @param int $formId
 */
function submissionsManage($option, $formId)
{
	$RSadapter = $GLOBALS['RSadapter'];

	$formId = intval($formId);
	
	$db = JFactory::getDBO();
	
	if ($formId == 0)
	{
		//get the first form
		$db->setQuery("SELECT FormId FROM #__rsform_forms WHERE published=1 ORDER BY FormId LIMIT 1");
		$formId = $db->loadResult();
		
		if ($formId > 0)
			$RSadapter->redirect(_RSFORM_BACKEND_SCRIPT_PATH.'?option='.$option.'&task=submissions.manage&formId='.$formId);
	}

	$data = new SManager($formId);
	$data->limit = $RSadapter->config['list_limit'];

	//load forms
	$forms = array();
	$db->setQuery("SELECT FormId, FormName FROM #__rsform_forms ORDER BY FormId");
	$result = $db->loadAssocList();
	foreach ($result as $r)
		$forms[$r['FormId']] = $r['FormName'];

	rsform_HTML::submissionsManage($option, $data, $forms);
}
/**
 * Edits one submission
 *
 * @param str $option
 * @param int $formId
 */
function submissionsEdit($option, $formId)
{
	$RSadapter = $GLOBALS['RSadapter'];

	$data = new SManager($formId);
	
	$order = 0;
	if (isset($_GET['order']) && $_GET['order'] == 'asc')
		$order = 1;
	
	$id = 0;
	if (isset($_GET['id']) && $_GET['id'] > 0)
		$id = $_GET['id'];

	$sort_id = 0;
	if (isset($_GET['sort_id']) && $_GET['sort_id'] > 0)
		$sort_id = $_GET['sort_id'];
	
	$filter = '';
	if (isset($_GET['filter']) && strlen($_GET['filter']) > 0)
		$filter = $_GET['filter'];
	$data->filter = $filter;
	
	$page = 1;
	if (isset($_GET['page']) && $_GET['page'] > 1)
		$page = $_GET['page'];
	$data->current = $page;
	
	$data->limit = $RSadapter->config['list_limit'];
	if (isset($_GET['limit']))
		$data->limit = $_GET['limit'];

	if(!isset($_GET['action']))
		$_GET['action'] = '';
	
	header('Content-type: text/html; charset=utf-8');
	switch($_GET['action']){
		case 'edit':
			$data->setValue($_GET['SubmissionId'], $_GET['SubmissionValueId'], $_POST['value'], $_GET['fieldName']);
			exit();
		break;
		case 'remove':
			$data->setOrder($sort_id, $order);
			$data->deleteRow($id);
			rsform_HTML::submissionsTable($option, $data);
			exit();
		break;
		case 'sort':
			$data->setOrder($sort_id, $order);
			rsform_HTML::submissionsTable($option, $data);
			exit();
		break;
		case 'filter':
			$data->setOrder($sort_id, $order);
			rsform_HTML::submissionsTable($option, $data);
			exit();
		break;
		case 'page':
			$data->setOrder($sort_id, $order);
			rsform_HTML::submissionsTable($option, $data);
			exit();
		break;
		case 'pager':
			$data->setOrder($sort_id, $order);
			$data->pager($page, $filter);
			exit();
		break;
		case 'exportall':
			$data->setOrder($sort_id, $order);
			$data->exportAll($page, $filter);
			exit();
		break;
	}

}

function submissionsResend()
{
	$SubmissionId = JRequest::getInt('SubmissionId');
	
	$userEmail = array('to'=>'', 'cc'=>'', 'bcc'=>'', 'from'=>'', 'replyto'=>'', 'fromName'=>'', 'text'=>'', 'subject'=>'', 'files' => array());
	$adminEmail = array('to'=>'', 'cc'=>'', 'bcc'=>'', 'from'=>'', 'replyto'=>'', 'fromName'=>'', 'text'=>'', 'subject'=>'', 'files' => array());
	
	$db = JFactory::getDBO();
	$db->setQuery("SELECT FormId FROM #__rsform_submissions WHERE SubmissionId='".$SubmissionId."'");
	$formId = $db->loadResult();
	
	$db->setQuery("SELECT * FROM #__rsform_forms WHERE FormId='".$formId."'");
	$r = $db->loadAssoc();

	$userEmail['to']=RSprocessField($r['UserEmailTo'],$SubmissionId);
	$userEmail['cc']=RSprocessField($r['UserEmailCC'],$SubmissionId);
	$userEmail['bcc']=RSprocessField($r['UserEmailBCC'],$SubmissionId);
	$userEmail['subject']=RSprocessField($r['UserEmailSubject'],$SubmissionId);
	$userEmail['from']=RSprocessField($r['UserEmailFrom'],$SubmissionId);
	$userEmail['replyto']=RSprocessField($r['UserEmailReplyTo'],$SubmissionId);
	$userEmail['fromName']=RSprocessField($r['UserEmailFromName'],$SubmissionId);
	$userEmail['text']=RSprocessField($r['UserEmailText'],$SubmissionId);
	$userEmail['mode']=$r['UserEmailMode'];

	$adminEmail['to']=RSprocessField($r['AdminEmailTo'],$SubmissionId);
	$adminEmail['cc']=RSprocessField($r['AdminEmailCC'],$SubmissionId);
	$adminEmail['bcc']=RSprocessField($r['AdminEmailBCC'],$SubmissionId);
	$adminEmail['subject']=RSprocessField($r['AdminEmailSubject'],$SubmissionId);
	$adminEmail['from']=RSprocessField($r['AdminEmailFrom'],$SubmissionId);
	$adminEmail['replyto']=RSprocessField($r['AdminEmailReplyTo'],$SubmissionId);
	$adminEmail['fromName']=RSprocessField($r['AdminEmailFromName'],$SubmissionId);
	$adminEmail['text']=RSprocessField($r['AdminEmailText'],$SubmissionId);
	$adminEmail['mode']=$r['AdminEmailMode'];
	
	// mail users
	$recipients = explode(',',$userEmail['to']);
	// cc
	if (strpos($userEmail['cc'], ',') !== false)
		$userEmail['cc'] = explode(',', $userEmail['cc']);
	// bcc
	if (strpos($userEmail['bcc'], ',') !== false)
		$userEmail['bcc'] = explode(',', $userEmail['bcc']);
	
	if ($r['UserEmailAttach'] && file_exists(RSprocessField($r['UserEmailAttachFile'],$SubmissionId)))
		$userEmail['files'][] = RSprocessField($r['UserEmailAttachFile'],$SubmissionId);
	
	// need to attach files
	$db->setQuery("SELECT ComponentId FROM #__rsform_components WHERE FormId='".$formId."' AND `ComponentTypeId`='9' AND `Published`='1'");
	$components = $db->loadResultArray();
	foreach ($components as $component)
	{
		$db->setQuery("SELECT PropertyId FROM #__rsform_properties WHERE ComponentId='".$component."' AND PropertyName='ATTACHUSEREMAIL' AND PropertyValue='YES'");
		if ($db->loadResult())
		{
			$db->setQuery("SELECT PropertyValue FROM #__rsform_properties WHERE ComponentId='".$component."' AND PropertyName='NAME'");
			$name = $db->loadResult();
			$db->setQuery("SELECT FieldValue FROM #__rsform_submission_values WHERE FieldName='".$db->getEscaped($name)."' AND SubmissionId='".$SubmissionId."' AND FormId='".$formId."'");
			$userEmail['files'][] = $db->loadResult();
		}
		
		$db->setQuery("SELECT PropertyId FROM #__rsform_properties WHERE ComponentId='".$component."' AND PropertyName='ATTACHADMINEMAIL' AND PropertyValue='YES'");
		if ($db->loadResult())
		{
			$db->setQuery("SELECT PropertyValue FROM #__rsform_properties WHERE ComponentId='".$component."' AND PropertyName='NAME'");
			$name = $db->loadResult();
			$db->setQuery("SELECT FieldValue FROM #__rsform_submission_values WHERE FieldName='".$db->getEscaped($name)."' AND SubmissionId='".$SubmissionId."' AND FormId='".$formId."'");
			$adminEmail['files'][] = $db->loadResult();
		}
	}
	
	if(!empty($recipients))
		foreach($recipients as $recipient)
			if(!empty($recipient))
				JUtility::sendMail($userEmail['from'], $userEmail['fromName'], $recipient, $userEmail['subject'], $userEmail['text'], $userEmail['mode'], !empty($userEmail['cc']) ? $userEmail['cc'] : null, !empty($userEmail['bcc']) ? $userEmail['bcc'] : null, $userEmail['files'], !empty($userEmail['replyto']) ? $userEmail['replyto'] : '');
				
	//mail admins
	$recipients = explode(',',$adminEmail['to']);
	// cc
	if (strpos($adminEmail['cc'], ',') !== false)
		$adminEmail['cc'] = explode(',', $adminEmail['cc']);
	// bcc
	if (strpos($adminEmail['bcc'], ',') !== false)
		$adminEmail['bcc'] = explode(',', $adminEmail['bcc']);
	if(!empty($recipients))
		foreach($recipients as $recipient)
			if(!empty($recipient))
				JUtility::sendMail($adminEmail['from'], $adminEmail['fromName'], $recipient, $adminEmail['subject'], $adminEmail['text'], $adminEmail['mode'], !empty($adminEmail['cc']) ? $adminEmail['cc'] : null, !empty($adminEmail['bcc']) ? $adminEmail['bcc'] : null, $adminEmail['files'], !empty($adminEmail['replyto']) ? $adminEmail['replyto'] : '');
}

function deleteSubmissionFiles($submissionId, $formId)
{
	$RSadapter = $GLOBALS['RSadapter'];
	
	$formId = intval($formId);
	
	$db = JFactory::getDBO();
	jimport('joomla.filesystem.file');
	
	//check if submissions have file uploads
		
	//check if form has upload fields, and return their names
	$db->setQuery("SELECT ComponentId FROM #__rsform_components WHERE ComponentTypeId = 9 AND FormId = '".$formId."'");
	$components = $db->loadAssocList();
	foreach ($components as $row)
	{
		$db->setQuery("SELECT sv.FieldValue FROM #__rsform_submission_values sv, #__rsform_properties p WHERE p.ComponentId = '".$row['ComponentId']."' AND p.PropertyName = 'NAME' AND p.PropertyValue = sv.FieldName AND sv.SubmissionId = '".$submissionId."' LIMIT 1");
		$file = $db->loadResult();
		
		if(!empty($file))
			JFile::delete($file);
	}
}

/**
 * Deletes submissions
 *
 * @param str $option
 * @param int $all
 */
function submissionsDelete($option, $all=1)
{
	$RSadapter = $GLOBALS['RSadapter'];

	$formId 		= intval($RSadapter->getParam($_REQUEST, 'formId'));
	$submissionIds 	= $RSadapter->getParam($_POST, 'checks');

	$db = JFactory::getDBO();
	
	//delete submissionIds
	if($all!=-1)
	{
		if(!empty($submissionIds))
		{
			foreach($submissionIds as $submissionId)
				deleteSubmissionFiles($submissionId, $formId);
			
			$db->setQuery("DELETE FROM #__rsform_submissions WHERE `SubmissionId` IN (".implode(',',$submissionIds).")");
			$db->query();
			
			$db->setQuery("DELETE FROM #__rsform_submission_values WHERE `SubmissionId` IN (".implode(',',$submissionIds).")");
			$db->query();
		}
	}
	else
	{
		$submissionIds = array();
		
		$db->setQuery("SELECT SubmissionId FROM #__rsform_submissions WHERE `FormId` = '".$formId."'");
		$submissions = $db->loadAssocList();
		
		foreach ($submissions as $row)
		{
			deleteSubmissionFiles($row['SubmissionId'], $formId);
			$submissionIds[] = $row['SubmissionId'];
		}
			
		if (!empty($submissionIds))
		{
			$db->setQuery("DELETE FROM #__rsform_submission_values WHERE `SubmissionId` IN (".implode(',',$submissionIds).")");
			$db->query();
		}
		
		$db->setQuery("DELETE FROM #__rsform_submissions WHERE `FormId` = '".$formId."'");
		$db->query();
	}
	$RSadapter->redirect(_RSFORM_BACKEND_SCRIPT_PATH.'?option='.$option.'&task=submissions.manage&formId='.$formId);
}

/**
 * Export Submissions Screen
 *
 * @param str $option
 */
function submissionsExport($option)
{
	$RSadapter = $GLOBALS['RSadapter'];

	$db = JFactory::getDBO();
	
	$formId 		= intval($RSadapter->getParam($_REQUEST, 'formId'));
	$submissionIds 	= $RSadapter->getParam($_POST, 'checks');

	//load form Name
	$db->setQuery("SELECT FormName FROM #__rsform_forms WHERE FormId = '".$formId."'");
	$formName = $db->loadResult();

	//load components
	$formComponents = array();
	$db->setQuery("SELECT `ComponentId`, `Order` FROM #__rsform_components WHERE `FormId` = '".$formId."' AND `Published` = 1 ORDER BY `Order`");
	$result = $db->loadAssocList();
	foreach ($result as $componentRow)
	{
		$componentProperties=RSgetComponentProperties($componentRow['ComponentId']);
		$formComponents[$componentRow['ComponentId']] = array('ComponentName'=>$componentProperties['NAME'],'Order'=>$componentRow['Order']);
	}
	
	rsform_HTML::submissionsExport($option, $formId, $submissionIds, $formName, $formComponents);
}

/**
 * Submissions Export Process
 *
 * @param str $option
 */
function submissionsExportProcess($option)
{
	global $RSadapter;

	$config = new JConfig();
	$post = $_SESSION['rsfp_post'];
	$start = $RSadapter->getParam($_GET,'start', 0);
	$limit = $RSadapter->getParam($_GET,'limit', 500);
	
	$_POST = unserialize(base64_decode($post));
	$formId = $RSadapter->getParam($_POST,'formId');
	$data = new SManager($formId,$export = 1);
	
	$data->filter = isset($_POST['filter']) ? $_POST['filter'] : '';
	
	//$data->submissionIds 		= $RSadapter->getParam($_POST,'ExportRows', 0);
	
	$data->exportHeaders		= $RSadapter->getParam($_POST,'ExportHeaders',0);
	$data->exportDelimiter		= (isset($_POST['ExportDelimiter']) ? stripslashes($_POST['ExportDelimiter']): '');
	$data->exportDelimiter		= str_replace(array('\t','\n','\r'),array("\t","\n","\r"),$data->exportDelimiter);
	$data->exportFieldEnclosure	= (isset($_POST['ExportFieldEnclosure']) ? stripslashes($_POST['ExportFieldEnclosure']) : '');
	$data->exportSubmission		= $RSadapter->getParam($_POST,'ExportSubmission');
	$data->exportOrder			= $RSadapter->getParam($_POST,'ExportOrder');
	$data->exportComponent		= $RSadapter->getParam($_POST,'ExportComponent');
	
	$data->limitstart = $start;
	$data->limit = $limit;
	$data->exportFile = $config->tmp_path.DS.$RSadapter->getParam($_POST,'ExportFile');
	$output = $data->createExportFile();
}

function submissionsExportFile($option)
{
	global $RSadapter;
	$config = new JConfig();
	$file = $RSadapter->getParam($_GET,'ExportFile');
	$file = $config->tmp_path.DS.$file;
	$fsize = filesize($file);
	$mod_date = date('r', filemtime( $file ) );


	/*header("Pragma: public");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Expires: 0");

	header("Content-Transfer-Encoding: binary");
	header('Content-Disposition:attachment;'
		. ' filename="'.date('Y-m-d').'_rsform.csv";'
		. ' modification-date="' . $mod_date . '";'
		. ' size=' . $fsize .';'
		); //RFC2183
	header("Content-Type: application/csv; charset=utf-8"  );			// MIME type
	header("Content-Length: "  . $fsize);
	*/
	
	header("Cache-Control: public, must-revalidate");
	header('Cache-Control: pre-check=0, post-check=0, max-age=0');
	header("Pragma: no-cache");
	header("Expires: 0"); 
	header("Content-Description: File Transfer");
	header("Expires: Sat, 01 Jan 2000 01:00:00 GMT");
	header("Content-Type: application/octet-stream");
	header("Content-Length: ".(string) ($fsize));
	header('Content-Disposition: attachment; filename="'.date('Y-m-d').'_rsform.csv"');
	header("Content-Transfer-Encoding: binary\n");
	ob_end_flush();
	RSreadfile_chunked($file);
	exit();
}

function submissionsExportTask($option)
{
	$post = base64_encode(serialize($_POST));
	$_SESSION['rsfp_post'] = $post;
	$limit = 500;
	
	//get total
	$total = 0;
	if (isset($_POST['ExportRows']))
	{
		if(is_array($_POST['ExportRows'])) $total = count($_POST['ExportRows']);
		else 
		{
			$db = JFactory::getDBO();
			$db->setQuery("SELECT count(*) FROM #__rsform_submissions WHERE FormId = '".$_POST['formId']."'");
			$total = $db->loadResult();
		}
	}
	$file = $_POST['ExportFile'];
	
	rsform_HTML::submissionsExportTask($option, $post, $limit, $total, $file);
}

//////////////////////////////////////// CONFIGURATION ////////////////////////////////////////
/**
 * Saves registration form
 *
 * @param str $option
 */
function saveRegistration($option)
{
	$RSadapter = $GLOBALS['RSadapter'];
	$rsformConfigPost = $RSadapter->getParam($_POST,'rsformConfig');
	if(!isset($rsformConfigPost['global.register.code']))$rsformConfigPost['global.register.code']='';
	if($rsformConfigPost['global.register.code']=='') $RSadapter->redirect(_RSFORM_BACKEND_SCRIPT_PATH.'?option='.$option,_RSFORM_BACKEND_SAVEREG_CODE);
	
	$db = JFactory::getDBO();
	$db->setQuery("UPDATE #__rsform_config SET SettingValue = '".RScleanVar(trim($rsformConfigPost['global.register.code']))."' WHERE SettingName = 'global.register.code'");
	$db->query();

	$RSadapter->redirect(_RSFORM_BACKEND_SCRIPT_PATH.'?option='.$option.'&task=updates.manage',_RSFORM_BACKEND_SAVEREG_SAVED);
}

/**
 * Configuration Edit Screen
 *
 * @param str $option
 */
function configurationEdit($option)
{
	$RSadapter = $GLOBALS['RSadapter'];
	rsform_HTML::configurationEdit($option);
}

/**
 * Configuration Save process
 *
 * @param str $option
 */
function configurationSave($option)
{
	$RSadapter = $GLOBALS['RSadapter'];

	$rsformConfig = $RSadapter->getParam($_POST,'rsformConfig',array());
	$languageFile = $RSadapter->getParam($_POST,'languageFile',array());

	$db = JFactory::getDBO();
	
	foreach($rsformConfig as $setting_name=>$setting_value)
	{
		$db->setQuery("UPDATE #__rsform_config SET SettingValue = '".RScleanVar($setting_value)."' WHERE SettingName = '".RScleanVar($setting_name)."'");
		$db->query();
	}

	if(!empty($languageFile))
		foreach($languageFile as $file=>$content)
		{
			$filename = _RSFORM_FRONTEND_ABS_PATH.'/languages/'.$file;
			if ( $fp = fopen ($filename, 'wb') ) {
				fputs( $fp, stripslashes( $content ) );
				fclose( $fp );
			}
		}
	
	$RSadapter->redirect(_RSFORM_BACKEND_SCRIPT_PATH.'?option='.$option.'&task=configuration.edit',_RSFORM_BACKEND_CONFIGURATION_SAVED);
}



//////////////////////////////////////// BACKUP / RESTORE ////////////////////////////////////////
/**
 * Backup / Restore Screen
 *
 * @param str $option
 */
function backupRestore($option)
{
	$RSadapter = $GLOBALS['RSadapter'];
	
	$db = JFactory::getDBO();
	
	$db->setQuery("SELECT FormId, FormTitle, FormName FROM #__rsform_forms ORDER BY FormId DESC");
	$result = $db->loadAssocList();
	$rows = array();
	
    foreach ($result as $r)
	{
		$db->setQuery("SELECT COUNT(`SubmissionId`) cnt FROM #__rsform_submissions WHERE FormId='".$r['FormId']."'");
        $r['_allSubmissions'] = $db->loadResult();
    	$rows[] = $r;
    }
	
	rsform_HTML::backupRestore( $rows, _RSFORM_BACKEND_BACKUPRESTORE_TITLE_HEAD, $option, 'component', '', dirname(__FILE__), "");
}

/**
 * Backup Generate Process
 *
 * @param str $option
 */
function backupDownload($option)
{
    $RSadapter = $GLOBALS['RSadapter'];

	if(empty($_POST['cid']))
  		$RSadapter->redirect(_RSFORM_BACKEND_SCRIPT_PATH.'?option='.$option.'&task=backup.restore',_RSFORM_BACKEND_BACKUPRESTORE_FORMS_SELECT);
  		
    $tmpdir = uniqid('rsformbkp');
    $pathtotmpdir = $RSadapter->config['absolute_path'].'/media/'.$tmpdir.'/';
    mkdir($pathtotmpdir);
    chmod($pathtotmpdir,0777);

    require_once( $RSadapter->config['absolute_path'] . '/administrator/includes/pcl/pclzip.lib.php' );
    require_once( $RSadapter->config['absolute_path'] . '/administrator/includes/pcl/pclerror.lib.php' );

    $name = 'rsform_backup_' . date('Y-m-d_His') . '.zip';

    $files4XML = array();
    RSbackupCreateXMLfile($option, $_POST['cid'], $_POST['submissions'], $files4XML, $pathtotmpdir . '/install.xml' );

    chdir($pathtotmpdir);
    $zipfile = new PclZip( $pathtotmpdir . $name );

    $zipfile->add($pathtotmpdir.'/install.xml',
                        PCLZIP_OPT_REMOVE_PATH, $pathtotmpdir);
    /*$zipfile->add(implode(',',$files),
                        PCLZIP_OPT_ADD_PATH, 'rsads',
                        PCLZIP_OPT_REMOVE_PATH, $mosConfig_absolute_path);*/
    @$zipfile->create();

    $RSadapter->redirect( $RSadapter->config['live_site'] .'/media/'. $tmpdir .'/'. $name );
}


//////////////////////////////////////// UPDATES ////////////////////////////////////////


function updateUploadProcess( $option ) {
	$RSadapter = $GLOBALS['RSadapter'];

    // Check that the zlib is available
    if(!extension_loaded('zlib')) {
        echo "The installer can't continue before zlib is installed";
        exit() ;
    }

    $userfile = $RSadapter->getParam( $_FILES, 'userfile' );
    $filetype = $RSadapter->getParam( $_POST, 'filetype');
    $overwrite = $RSadapter->getParam( $_POST, 'overwrite');
    
    if (!$userfile) {
        echo "No file selected";
        exit();
    }

    $userfile_name = $userfile['name'];

    $msg = @constant('_RSFORM_BACKEND_UPDATECHECK_STATUS_'.strtoupper($filetype));

    $resultdir = RSuploadFile( $userfile['tmp_name'], $userfile['name'], $msg );

    $has_errors = 0;
    //check if file is a valid plugin
    if ($resultdir !== false) {
        $baseDir = $RSadapter->config['absolute_path'] . '/media/' ;

        require_once( _RSFORM_JOOMLA_XML_PATH );
        $installer = new RSinstaller();
        $installer->archivename = $userfile['name'];
        if($installer->upload($userfile['name']))
        {
        	if($installer->readInstall())
        	{
        	 	$RSinstall = $installer->xmldoc->documentElement;
        	 	$version_nodes = $RSinstall->getElementsByPath('version', 1);
				$version_node = $version_nodes->childNodes;
				$version = $version_node[0]->getText();
				
				if($installer->installType!=$filetype)
					$msg = constant('_RSFORM_BACKEND_UPDATECHECK_'.strtoupper($filetype));
				else
				{
					$db = JFactory::getDBO();
					if ($filetype == 'rsformbackup' && $overwrite == 1)
					{
						$db->setQuery("TRUNCATE TABLE #__rsform_forms");
						$db->query();
						
						$db->setQuery("TRUNCATE TABLE #__rsform_components");
						$db->query();
						
						$db->setQuery("TRUNCATE TABLE #__rsform_properties");
						$db->query();
						
						$db->setQuery("TRUNCATE TABLE #__rsform_submissions");
						$db->query();
						
						$db->setQuery("TRUNCATE TABLE #__rsform_submission_values");
						$db->query();
					}
					
					$tasks_node = &$RSinstall->getElementsByPath('tasks', 1);
					if (!is_null($tasks_node)) {
						$tasks = $tasks_node->childNodes;
						$has_errors = false;
						foreach($tasks as $task){
							if(RSprocessTask($option, $task, $installer->installDir, $version)===FALSE)$has_errors = true;
						}
						//if($has_errors) die();
					}

					//clean up
					@unlink($baseDir.$userfile['name']);
					$installer->cleanup($userfile['name'], $installer->installDir);
					$msg = _RSFORM_BACKEND_UPDATECHECK_OK;
				}
        	}
			else
                $msg = _RSFORM_BACKEND_UPDATECHECK_NOINSTALL;
        }
		else
		{
            $msg = _RSFORM_BACKEND_UPDATECHECK_BADFILE;
            @unlink($baseDir.$userfile['name']);
        }
    }

    
    if(!$has_errors)
    switch($filetype)
	{
        case 'rsformbackup':
            $RSadapter->redirect(_RSFORM_BACKEND_SCRIPT_PATH.'?option='.$option.'&task=backup.restore',$msg);
        break;
        case 'rsformupdate':
            $RSadapter->redirect(_RSFORM_BACKEND_SCRIPT_PATH.'?option='.$option.'&task=updates.manage',$msg);
        break;
        case 'rsformplugin':
            $RSadapter->redirect(_RSFORM_BACKEND_SCRIPT_PATH.'?option='.$option.'&task=configuration.edit',$msg);
        break;

    }
}

function updatesManage($option){
	rsform_HTML::updatesManage($option);
}
?>