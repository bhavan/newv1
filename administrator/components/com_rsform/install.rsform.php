<?php
/**
* @version 1.2.0
* @package RSform!Pro 1.2.0
* @copyright (C) 2007-2009 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// This is going to be redone, but for the time being, it's working as it is

//Init RS Adapter
require_once(JPATH_SITE.DS.'components'.DS.'com_rsform'.DS.'controller'.DS.'adapter.php');
require_once(JPATH_SITE.DS.'components'.DS.'com_rsform'.DS.'controller'.DS.'functions.php');

$RSadapter = new RSadapter();
$GLOBALS['RSadapter'] = $RSadapter;

// Require backend language file
require_once(JPATH_SITE.DS.'components'.DS.'com_rsform'.DS.'languages'.DS._RSFORM_FRONTEND_LANGUAGE.'.php');

$message = '';
$message.= _RSFORM_INSTALLER_TABLES_OK;

//Try setting directories permissions
$uploads_folder = JPATH_SITE.DS.'components'.DS.'com_rsform'.DS.'uploads'.DS;
$message .= sprintf(@chmod($uploads_folder, 0777) ? _RSFORM_INSTALLER_PERMISSIONS_OK : _RSFORM_INSTALLER_PERMISSIONS_ERROR, $uploads_folder, '0777');

//Add sample forms
$message .= _RSFORM_INSTALLER_DB_OK;

?>
<div align="left" width="100%">
	<img src="../components/com_rsform/images/rsform-pro.jpg" alt="RSform!Pro Logo"/>
</div>
<br/>
<table class="adminform">
	<tr>
		<td align="left">
		<?php echo _RSFORM_INSTALLER_WELCOME;?>
		</td>
	</tr>
</table><br/>
<table class="adminform">
	<tr>
		<td align="left">
		<?php echo $message;?>
		</td>
	</tr>
</table><br/><br/>

<?php
// Initialize DB
$db = JFactory::getDBO();

// Disable error reporting
$db->setQuery("REPLACE INTO `#__rsform_config` (`ConfigId`, `SettingName`, `SettingValue`) VALUES(2, 'global.debug.mode', '0')");
$db->query();

$db->setQuery("UPDATE `#__rsform_component_type_fields` SET `FieldType` = 'textarea' WHERE `ComponentTypeFieldId` = 10 LIMIT 1");
$db->query();

if (str_replace($RSadapter->config['dbprefix'], '', $RSadapter->tbl_rsform_config) == 'RSFORM_CONFIG')
{
	$wrong_tables = array($RSadapter->tbl_rsform_components, $RSadapter->tbl_rsform_component_types, $RSadapter->tbl_rsform_component_type_fields, $RSadapter->tbl_rsform_config, $RSadapter->tbl_rsform_forms,	$RSadapter->tbl_rsform_mappings, $RSadapter->tbl_rsform_properties,	$RSadapter->tbl_rsform_submissions,	$RSadapter->tbl_rsform_submission_values);
	$good_tables = array($RSadapter->config['dbprefix'].'rsform_components', $RSadapter->config['dbprefix'].'rsform_component_types', $RSadapter->config['dbprefix'].'rsform_component_type_fields', $RSadapter->config['dbprefix'].'rsform_config', $RSadapter->config['dbprefix'].'rsform_forms', $RSadapter->config['dbprefix'].'rsform_mappings', $RSadapter->config['dbprefix'].'rsform_properties', $RSadapter->config['dbprefix'].'rsform_submissions', $RSadapter->config['dbprefix'].'rsform_submission_values');
foreach ($wrong_tables as $i => $wrong_table)
{
	$db->setQuery("RENAME TABLE `".$wrong_tables[$i]."` TO `".$good_tables[$i]."`");
	$db->query();
}
// Replace uppercase tables if there are any scripts
foreach ($wrong_tables as $i => $wrong_table)
{
	$db->setQuery("UPDATE `".$good_tables[4]."` SET `ScriptProcess`=REPLACE(`ScriptProcess`,'".$wrong_tables[$i]."','".$good_tables[$i]."'), `ScriptDisplay`=REPLACE(`ScriptDisplay`,'".$wrong_tables[$i]."','".$good_tables[$i]."')");
	$db->query();
	$db->setQuery("UPDATE `".$good_tables[6]."` SET `PropertyValue`=REPLACE(`PropertyValue`,'".$wrong_tables[$i]."','".$good_tables[$i]."')");
	$db->query();
}
	$RSadapter->tbl_rsform_components = $good_tables[0];
	$RSadapter->tbl_rsform_component_types = $good_tables[1];
	$RSadapter->tbl_rsform_component_type_fields = $good_tables[2];
	$RSadapter->tbl_rsform_config = $good_tables[3];
	$RSadapter->tbl_rsform_forms = $good_tables[4];
	$RSadapter->tbl_rsform_mappings = $good_tables[5];
	$RSadapter->tbl_rsform_properties = $good_tables[6];
	$RSadapter->tbl_rsform_submissions = $good_tables[7];
	$RSadapter->tbl_rsform_submission_values = $good_tables[8];
}

$db->setQuery("DESCRIBE #__rsform_forms");
$form_properties = $db->loadAssocList();
$exists_email_attach = 0;
$exists_email_attach_file = 0;
$exists_process2 = 0;
$exists_user_cc = 0;
$exists_user_bcc = 0;
$exists_user_reply = 0;
$exists_admin_cc = 0;
$exists_admin_bcc = 0;
$exists_admin_reply = 0;
foreach ($form_properties as $row)
{
	if($row['Field'] == 'UserEmailAttach') $exists_email_attach = 1;
	if($row['Field'] == 'UserEmailAttachFile') $exists_email_attach_file = 1;
	if($row['Field'] == 'ScriptProcess2') $exists_process2 = 1;
	if($row['Field'] == 'UserEmailCC') $exists_user_cc = 1;
	if($row['Field'] == 'UserEmailBCC') $exists_user_bcc = 1;
	if($row['Field'] == 'UserEmailReplyTo') $exists_user_reply = 1;
	if($row['Field'] == 'AdminEmailCC') $exists_admin_cc = 1;
	if($row['Field'] == 'AdminEmailBCC') $exists_admin_bcc = 1;
	if($row['Field'] == 'AdminEmailReplyTo') $exists_admin_reply = 1;
}
if(!$exists_email_attach) { $db->setQuery("ALTER TABLE #__rsform_forms ADD `UserEmailAttach` TINYINT NOT NULL AFTER `UserEmailMode`"); $db->query(); }
if(!$exists_email_attach_file) { $db->setQuery("ALTER TABLE #__rsform_forms ADD `UserEmailAttachFile` VARCHAR (255) NOT NULL AFTER `UserEmailAttach`"); $db->query(); }
if(!$exists_process2) { $db->setQuery("ALTER TABLE #__rsform_forms ADD `ScriptProcess2` TEXT NOT NULL AFTER `ScriptProcess`"); $db->query(); }
if(!$exists_user_cc) { $db->setQuery("ALTER TABLE #__rsform_forms ADD `UserEmailCC` VARCHAR (255) NOT NULL AFTER `UserEmailTo`"); $db->query(); }
if(!$exists_user_bcc) { $db->setQuery("ALTER TABLE #__rsform_forms ADD `UserEmailBCC` VARCHAR (255) NOT NULL AFTER `UserEmailCC`"); $db->query(); }
if(!$exists_user_reply) { $db->setQuery("ALTER TABLE #__rsform_forms ADD `UserEmailReplyTo` VARCHAR (255) NOT NULL AFTER `UserEmailBCC`"); $db->query(); }
if(!$exists_admin_cc) { $db->setQuery("ALTER TABLE #__rsform_forms ADD `AdminEmailCC` VARCHAR (255) NOT NULL AFTER `AdminEmailTo`"); $db->query(); }
if(!$exists_admin_bcc) { $db->setQuery("ALTER TABLE #__rsform_forms ADD `AdminEmailBCC` VARCHAR (255) NOT NULL AFTER `AdminEmailCC`"); $db->query(); }
if(!$exists_admin_reply) { $db->setQuery("ALTER TABLE #__rsform_forms ADD `AdminEmailReplyTo` VARCHAR (255) NOT NULL AFTER `AdminEmailBCC`"); $db->query(); }

$db->setQuery("SELECT * FROM #__rsform_component_type_fields WHERE `ComponentTypeId` = 2 AND `FieldName`='WYSIWYG'");
$db->query();
if ($db->getNumRows() == 0)
{
	$db->setQuery("INSERT INTO #__rsform_component_type_fields SET `ComponentTypeId`=2, `FieldName`='WYSIWYG', `FieldType`='select', `FieldValues`='NO\r\nYES', `Ordering` = 11");
	$db->query();
	$db->setQuery("SELECT ComponentId FROM #__rsform_components WHERE `ComponentTypeId`=2");
	$components = $db->loadAssocList();
	foreach ($components as $row)
	{
		$db->setQuery("INSERT INTO #__rsform_properties SET `ComponentId`='".$row['ComponentId']."', `PropertyName`='WYSIWYG', `PropertyValue`='NO'");
		$db->query();
	}
}

$db->setQuery("SELECT * FROM #__rsform_component_type_fields WHERE `ComponentTypeId` = 8 AND `FieldName`='SIZE'");
$db->query();
if ($db->getNumRows() == 0)
{
	$db->setQuery("INSERT INTO #__rsform_component_type_fields SET `ComponentTypeId`=8, `FieldName`='SIZE', `FieldType`='textbox', `FieldValues`='15', `Ordering` = 12");
	$db->query();
	$components = $db->setQuery("SELECT ComponentId FROM #__rsform_components WHERE `ComponentTypeId`=8");
	$db->loadAssocList();
	foreach ($components as $row)
	{
		$db->setQuery("INSERT INTO #__rsform_properties SET `ComponentId`='".$row['ComponentId']."', `PropertyName`='SIZE', `PropertyValue`='15'");
		$db->query();
	}
}

$db->setQuery("DESCRIBE #__rsform_submission_values");
$sqlinfo = $db->loadAssocList();
$form_id = 0;
foreach ($sqlinfo as $row)
	if($row['Field'] == 'FormId') $form_id = 1;

if(!$form_id)
{
	$db->setQuery("ALTER TABLE #__rsform_submission_values ADD `FormId` INT NOT NULL AFTER `SubmissionValueId`");
	$db->query();
	$db->setQuery("UPDATE #__rsform_submission_values sv, #__rsform_submissions s SET sv.FormId=s.FormId WHERE sv.SubmissionId = s.SubmissionId");
	$db->query();
}

$index_ctid = 0;
$index_fid = 0;
$db->setQuery("DESCRIBE #__rsform_components");
$sqlinfo = $db->loadAssocList();
foreach ($sqlinfo as $row)
{
	if ($row['Field'] == 'ComponentTypeId' && $row['Key'] == 'MUL') $index_ctid = 1;
	if ($row['Field'] == 'FormId' && $row['Key'] == 'MUL') $index_fid = 1;
}
if (!$index_ctid)
{
	$db->setQuery("ALTER TABLE #__rsform_components ADD INDEX (`ComponentTypeId`)");
	$db->query();
}
if (!$index_fid)
{
	$db->setQuery("ALTER TABLE #__rsform_components ADD INDEX (`FormId`)");
	$db->query();
}
$index_ctid = 0;
$db->setQuery("DESCRIBE #__rsform_component_type_fields");
$sqlinfo = $db->loadAssocList();
foreach ($sqlinfo as $row)
	if ($row['Field'] == 'ComponentTypeId' && $row['Key'] == 'MUL')	$index_ctid = 1;
if (!$index_ctid)
{
	$db->setQuery("ALTER TABLE #__rsform_component_type_fields ADD INDEX (`ComponentTypeId`)");
	$db->query();
}

$index_cid = 0;
$db->setQuery("DESCRIBE #__rsform_properties");
$sqlinfo = $db->loadAssocList();
foreach ($sqlinfo as $row)
	if ($row['Field'] == 'ComponentId' && $row['Key'] == 'MUL') $index_cid = 1;
if (!$index_cid)
{
	$db->setQuery("ALTER TABLE #__rsform_properties ADD INDEX (`ComponentId`)");
	$db->query();
}
$index_fid = 0;
$db->setQuery("DESCRIBE #__rsform_submissions");
$sqlinfo = $db->loadAssocList();
foreach ($sqlinfo as $row)
	if ($row['Field'] == 'FormId' && $row['Key'] == 'MUL') $index_fid = 1;
if (!$index_fid)
{
	$db->setQuery("ALTER TABLE #__rsform_submissions ADD INDEX (`FormId`)");
	$db->query();
}

$index_fid = 0;
$index_sid = 0;
$db->setQuery("DESCRIBE #__rsform_submission_values");
$sqlinfo = $db->loadAssocList();
foreach ($sqlinfo as $row)
{
	if ($row['Field'] == 'FormId' && $row['Key'] == 'MUL') $index_fid = 1;
	if ($row['Field'] == 'SubmissionId' && $row['Key'] == 'MUL') $index_sid = 1;
}
if (!$index_fid)
{
	$db->setQuery("ALTER TABLE #__rsform_submission_values ADD INDEX (`FormId`)"); 
	$db->query();
}
if (!$index_sid)
{
	$db->setQuery("ALTER TABLE #__rsform_submission_values ADD INDEX (`SubmissionId`)");
	$db->query();
}
$index_cid = 0;
$db->setQuery("DESCRIBE #__rsform_mappings"); $sqlinfo = $db->loadAssocList();
foreach ($sqlinfo as $row)
	if ($row['Field'] == 'FormId' && $row['Key'] == 'MUL') $index_cid = 1;
if (!$index_cid)
{
	$db->setQuery("ALTER TABLE #__rsform_mappings ADD INDEX (`ComponentId`)");
	$db->query();
}

$db->setQuery("ALTER TABLE #__rsform_component_type_fields CHANGE `FieldType` `FieldType` ENUM( 'hidden', 'hiddenparam', 'textbox', 'textarea', 'select' ) NOT NULL DEFAULT 'hidden'");
$db->query();

$db->setQuery("SELECT * FROM #__rsform_config WHERE `SettingName`='global.iis'");
$db->query();
if ($db->getNumRows() == 0)
{
	$db->setQuery("INSERT INTO #__rsform_config SET `SettingName`='global.iis', `SettingValue`='1'");
	$db->query();
}
$db->setQuery("SELECT * FROM #__rsform_config WHERE `SettingName`='global.editor'");
$db->query();
if ($db->getNumRows() == 0)
{
	$db->setQuery("INSERT INTO #__rsform_config SET `SettingName`='global.editor', `SettingValue`='1'");
	$db->query();
}

$db->setQuery("SELECT * FROM #__rsform_component_type_fields WHERE `ComponentTypeId` = 8 AND `FieldName`='IMAGETYPE'");
$db->query();
if ($db->getNumRows() == 0)
{
	$db->setQuery("INSERT INTO #__rsform_component_type_fields SET `ComponentTypeId`=8, `FieldName`='IMAGETYPE', `FieldType`='select', `FieldValues`='FREETYPE\r\nNOFREETYPE\r\nINVISIBLE', `Ordering` = 3");
	$db->query();
	$db->setQuery("SELECT ComponentId FROM #__rsform_components WHERE `ComponentTypeId`=8");
	$components = $db->loadAssocList();
	foreach ($components as $row)
	{
		$db->setQuery("INSERT INTO #__rsform_properties SET `ComponentId`='".$row['ComponentId']."', `PropertyName`='IMAGETYPE', `PropertyValue`='FREETYPE'");
		$db->query();
	}
}
$db->setQuery("SELECT `id` FROM `#__components` WHERE `option`='com_rsform' AND `parent`='0'");
$id = $db->loadResult();
$db->setQuery("SELECT `id` FROM `#__components` WHERE `parent`='".$id."' AND `name`='Plugins'");
$db->query();
if ($db->getNumRows() == 0)
{
	$db->setQuery("INSERT INTO `#__components` SET `name`='Plugins', `parent`='".$id."', `admin_menu_link`='option=com_rsform&amp;task=goto.plugins', `admin_menu_alt`='Plugins', `option`='com_rsform', `ordering`='5', `admin_menu_img`='js/ThemeOffice/component.png', `iscore`='0', `params`='', `enabled`='1'");
	$db->query();
}
$db->setQuery("UPDATE `#__components` SET `admin_menu_img`='../administrator/components/com_rsform/images/rsformpro.gif' WHERE `id`='".$id."' LIMIT 1");
$db->query();

$db->setQuery("SELECT * FROM #__rsform_component_type_fields WHERE `ComponentTypeId` = 1 AND `FieldName`='VALIDATIONEXTRA'");
$db->query();
if ($db->getNumRows() == 0)
{
	$db->setQuery("INSERT INTO #__rsform_component_type_fields SET `ComponentTypeId`=1, `FieldName`='VALIDATIONEXTRA', `FieldType`='textbox', `FieldValues`='', `Ordering` = 6");
	$db->query();
	$db->setQuery("SELECT ComponentId FROM #__rsform_components WHERE `ComponentTypeId`=1");
	$components = $db->loadAssocList();
	foreach ($components as $row)
	{
		$db->setQuery("INSERT INTO #__rsform_properties SET `ComponentId`='".$row['ComponentId']."', `PropertyName`='VALIDATIONEXTRA', `PropertyValue`=''");
		$db->query();
	}
}
$db->setQuery("SELECT * FROM #__rsform_component_type_fields WHERE `ComponentTypeId` = 2 AND `FieldName`='VALIDATIONEXTRA'");
$db->query();
if ($db->getNumRows() == 0)
{
	$db->setQuery("INSERT INTO #__rsform_component_type_fields SET `ComponentTypeId`=2, `FieldName`='VALIDATIONEXTRA', `FieldType`='textbox', `FieldValues`='', `Ordering` = 6");
	$db->query();
	$db->setQuery("SELECT ComponentId FROM #__rsform_components WHERE `ComponentTypeId`=2");
	$components = $db->loadAssocList();
	foreach ($components as $row)
	{
		$db->setQuery("INSERT INTO #__rsform_properties SET `ComponentId`='".$row['ComponentId']."', `PropertyName`='VALIDATIONEXTRA', `PropertyValue`=''");
		$db->query();
	}
}
$db->setQuery("SELECT * FROM #__rsform_component_type_fields WHERE `ComponentTypeId` = 14 AND `FieldName`='VALIDATIONRULE'");
$db->query();
if ($db->getNumRows() == 0)
{
	$db->setQuery("INSERT INTO #__rsform_component_type_fields SET `ComponentTypeId`=14, `FieldName`='VALIDATIONRULE', `FieldType`='select', `FieldValues`='//<code>\r\nreturn RSgetValidationRules();\r\n//</code>', `Ordering` = 9");
	$db->query();
	$db->setQuery("SELECT ComponentId FROM #__rsform_components WHERE `ComponentTypeId`=14");
	$components = $db->loadAssocList();
	foreach ($components as $row)
	{
		$db->setQuery("INSERT INTO #__rsform_properties SET `ComponentId`='".$row['ComponentId']."', `PropertyName`='VALIDATIONRULE', `PropertyValue`=''");
		$db->query();
	}
}

$db->setQuery("SHOW COLUMNS FROM #__rsform_forms WHERE `Field`='MetaTitle'");
$db->query();
if ($db->getNumRows() == 0)
{
	$db->setQuery("ALTER TABLE `#__rsform_forms` ADD `MetaTitle` TINYINT( 1 ) NOT NULL");
	$db->query();
	$db->setQuery("ALTER TABLE `#__rsform_forms` ADD `MetaDesc` TEXT NOT NULL");
	$db->query();
	$db->setQuery("ALTER TABLE `#__rsform_forms` ADD `MetaKeywords` TEXT NOT NULL");
	$db->query();
	$db->setQuery("ALTER TABLE `#__rsform_forms` ADD `Required` VARCHAR( 255 ) NOT NULL DEFAULT '(*)'");
	$db->query();
	$db->setQuery("ALTER TABLE `#__rsform_forms` ADD `ErrorMessage` TEXT NOT NULL");
	$db->query();
}

$db->setQuery("SELECT FormId FROM #__rsform_forms WHERE FormId='1' AND FormName='RSformPro example' AND ErrorMessage=''");
if ($db->loadResult())
{
	$db->setQuery("UPDATE #__rsform_forms SET MetaTitle=0, MetaDesc='This is the meta description of your form. You can use it for SEO purposes.', MetaKeywords='rsform, contact, form, joomla', Required='(*)', ErrorMessage='<p class=\"formRed\">Please complete all required fields!</p>' WHERE FormId='1' LIMIT 1");
	$db->query();
}
?>

<div align="left" width="100%"><b>RSForm! Pro <?php echo _RSFORM_VERSION;?> Rev <?php echo _RSFORM_REVISION; ?> Installed</b></div>