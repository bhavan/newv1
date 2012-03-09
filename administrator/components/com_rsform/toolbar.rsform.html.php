<?php
/**
* @version 1.2.0
* @package RSform!Pro 1.2.0
* @copyright (C) 2007-2009 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

 
class menuRsform {

	function _DEFAULT()
	{
		RSadapter::menuStartTable();  
		RSadapter::menuCustom( 'false', 'help.png', 'help_f2.png', _RSFORM_BACKEND_TOOLBAR_SUPPORT, false, "javascript:window.open('index.php?option=com_rsform&task=goto.support');" );
		RSadapter::menuSpacer();
		RSadapter::menuCustom( 'cancel', 'preview.png', 'preview_f2.png', _RSFORM_BACKEND_TOOLBAR_MAIN, false );    
        RSadapter::menuSpacer();
		RSadapter::menuEndTable();
	}


	function FORMSEDIT_MENU()
	{
		global $option;
		$formId = RSadapter::getParam($_REQUEST,'formId',0);
		RSadapter::menuStartTable();
		RSadapter::menuSave('forms.save');
		RSadapter::menuSpacer();
		RSadapter::menuApply('forms.apply');
		RSadapter::menuSpacer();
		RSadapter::menuCustom('false', 'copy.png', 'copy_f2.png', _RSFORM_BACKEND_TOOLBAR_PREVIEW, false, "javascript:window.open('" . _RSFORM_FRONTEND_SCRIPT_PATH . '?option=' . $option . '&formId=' . $formId."&Itemid=99999');return false;");
		RSadapter::menuSpacer();
		RSadapter::menuCustom( 'false', 'copy.png', 'copy_f2.png', _RSFORM_BACKEND_TOOLBAR_DUPLICATE, false, "javascript:componentsCopy(".$formId.");return false;" );
		RSadapter::menuSpacer();
		RSadapter::menuCancel('forms.cancel',_RSFORM_BACKEND_TOOLBAR_CLOSE);
		RSadapter::menuSpacer();
		RSadapter::menuCustom( 'false', 'help.png', 'help_f2.png', _RSFORM_BACKEND_TOOLBAR_SUPPORT, false, "javascript:window.open('index.php?option=com_rsform&task=goto.support');" );
		RSadapter::menuSpacer();
		RSadapter::menuCustom( '', 'preview.png', 'preview_f2.png', _RSFORM_BACKEND_TOOLBAR_MAIN, false );
		RSadapter::menuEndTable();
	}
	function COMPONENTS_COPY_MENU()
	{
		global $formId;
		RSadapter::menuStartTable();
		RSadapter::menuCustom( 'components.copy.process', 'copy.png', 'copy_f2.png', _RSFORM_BACKEND_TOOLBAR_DUPLICATE, false );
		RSadapter::menuSpacer();
		RSadapter::menuCancel('components.cancel',_RSFORM_BACKEND_TOOLBAR_CLOSE);
		RSadapter::menuSpacer();
		RSadapter::menuCustom( 'false', 'help.png', 'help_f2.png', _RSFORM_BACKEND_TOOLBAR_SUPPORT, false, "javascript:window.open('index.php?option=com_rsform&task=goto.support');" );
		RSadapter::menuSpacer();
		RSadapter::menuCustom( '', 'preview.png', 'preview_f2.png', _RSFORM_BACKEND_TOOLBAR_MAIN, false );
		RSadapter::menuEndTable();
	}


	function FORMSMANAGE_MENU()
	{
		RSadapter::menuStartTable();
		RSadapter::menuAddNewX('forms.edit');
		RSadapter::menuSpacer();
		RSadapter::menuCustom( 'forms.copy', 'copy.png', 'copy_f2.png', _RSFORM_BACKEND_TOOLBAR_DUPLICATE, false );
		RSadapter::menuSpacer();
		RSadapter::menuDeleteList( _RSFORM_BACKEND_PLUGINS_REMOVE_CONFIRM, 'forms.delete', _RSFORM_BACKEND_TOOLBAR_REMOVE );
		RSadapter::menuSpacer();
		RSadapter::menuPublishList('forms.publish');
		RSadapter::menuSpacer();
		RSadapter::menuUnpublishList('forms.unpublish');
		RSadapter::menuSpacer();
		RSadapter::menuCustom( 'false', 'help.png', 'help_f2.png', _RSFORM_BACKEND_TOOLBAR_SUPPORT, false, "javascript:window.open('index.php?option=com_rsform&task=goto.support');" );
		RSadapter::menuSpacer();
		RSadapter::menuCustom( '', 'preview.png', 'preview_f2.png', _RSFORM_BACKEND_TOOLBAR_MAIN, false );
		RSadapter::menuEndTable();
	}
	function SUBMISSIONSMANAGE_MENU()
	{
		RSadapter::menuStartTable();
		RSadapter::menuCustom('submissions.export','archive.png','archive_f2.png',_RSFORM_BACKEND_TOOLBAR_EXPORT, false);
		RSadapter::menuSpacer();
		RSadapter::menuBack('Back','index.php?option=com_rsform&task=forms.manage');
		RSadapter::menuSpacer();
		RSadapter::menuDeleteList('','submissions.delete',_RSFORM_BACKEND_TOOLBAR_REMOVE);
		//RSadapter::menuSpacer();
		//RSadapter::menuDeleteList('','submissions.delete.all',_RSFORM_BACKEND_TOOLBAR_REMOVE_ALL);
		RSadapter::menuSpacer();
		RSadapter::menuCustom( 'false', 'help.png', 'help_f2.png', _RSFORM_BACKEND_TOOLBAR_SUPPORT, false, "javascript:window.open('index.php?option=com_rsform&task=goto.support');" );
		RSadapter::menuSpacer();
		RSadapter::menuCustom( '', 'preview.png', 'preview_f2.png', _RSFORM_BACKEND_TOOLBAR_MAIN, false );
		RSadapter::menuEndTable();
	}

	function SETTINGS_MENU()
	{

		RSadapter::menuStartTable();
		RSadapter::menuSave('configuration.save');
		RSadapter::menuSpacer();
		RSadapter::menuCancel('cancel',_RSFORM_BACKEND_TOOLBAR_CLOSE);
		RSadapter::menuSpacer();
		RSadapter::menuCustom( 'false', 'help.png', 'help_f2.png', _RSFORM_BACKEND_TOOLBAR_SUPPORT, false, "javascript:window.open('index.php?option=com_rsform&task=goto.support');" );
		RSadapter::menuSpacer();
		RSadapter::menuCustom( '', 'preview.png', 'preview_f2.png', _RSFORM_BACKEND_TOOLBAR_MAIN, false );
		RSadapter::menuEndTable();
	}
}

?>