<?php
/**
* @version 1.2.0
* @package RSform!Pro 1.2.0
* @copyright (C) 2007-2009 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

//TOOLBAR
DEFINE('_RSFORM_BACKEND_TOOLBAR_MAIN','RSForm! Pro');
DEFINE('_RSFORM_BACKEND_TOOLBAR_SUPPORT','Support');
DEFINE('_RSFORM_BACKEND_TOOLBAR_EDIT','Edit');
DEFINE('_RSFORM_BACKEND_TOOLBAR_REMOVE','Delete');
DEFINE('_RSFORM_BACKEND_TOOLBAR_REMOVE_ALL','Delete All');
DEFINE('_RSFORM_BACKEND_TOOLBAR_DUPLICATE','Copy');
DEFINE('_RSFORM_BACKEND_TOOLBAR_CLOSE','Close');
DEFINE('_RSFORM_BACKEND_TOOLBAR_EXPORT','Export');
DEFINE('_RSFORM_BACKEND_TOOLBAR_EXPORT_ALL','Export All');
DEFINE('_RSFORM_BACKEND_TOOLBAR_NEWFIELD','New Field');
DEFINE('_RSFORM_BACKEND_TOOLBAR_UPDATE','Update');
DEFINE('_RSFORM_BACKEND_TOOLBAR_PREVIEW','Preview');

DEFINE('_RSFORM_BACKEND_NO','No');
DEFINE('_RSFORM_BACKEND_YES','Yes');

//INSTALLER
DEFINE('_RSFORM_INSTALLER_TABLES_OK','<font color="green"><b>Success</b></font>, RSform!Pro Tables Created<br/>');
DEFINE('_RSFORM_INSTALLER_XML_OK','');
DEFINE('_RSFORM_INSTALLER_PLUGIN_OK','<font color="green"><b>Success</b></font>, %s Plugin Added<br/>');
DEFINE('_RSFORM_INSTALLER_MODULE_OK','<font color="green"><b>Success</b></font>, %s Module Added<br/>');
DEFINE('_RSFORM_INSTALLER_PERMISSIONS_OK','<font color="green"><b>Success</b></font>, Permissions changed to directory %s to %s<br/>');
DEFINE('_RSFORM_INSTALLER_DB_OK','<font color="green"><b>Success</b></font>, Database Updated<br/>');

DEFINE('_RSFORM_INSTALLER_TABLES_ERROR','<font color="red"><b>Error</b></font>, RSform!Pro Tables Were not created properly:<br/>%s<br/>');
DEFINE('_RSFORM_INSTALLER_PLUGIN_ERROR','<font color="red"><b>Error</b></font>, Could not add plugin. Error:<br/>%s<br/>');
DEFINE('_RSFORM_INSTALLER_XML_ERROR','<font color="red"><b>Error</b></font>, Could not add xml file. Error:<br/>%s<br/>');
DEFINE('_RSFORM_INSTALLER_MODULE_ERROR','<font color="red"><b>Error</b></font>, Could not add module. Error:<br/>%s<br/>');
DEFINE('_RSFORM_INSTALLER_PERMISSIONS_ERROR','<font color="red"><b>Error</b></font>, Permissions could not be changed for %s. Please set them manually to %s<br/>');
DEFINE('_RSFORM_INSTALLER_DB_ERROR','<font color="red"><b>Error</b></font>, Database could not be Updated:<br/>%s<br/>');

DEFINE('_RSFORM_INSTALLER_WELCOME','<b>RSform!Pro 1.2.0 Component for Joomla!</b><br/>
&copy; 2007-2009 by <a href="http://www.rsjoomla.com" target="_blank">http://www.rsjoomla.com</a><br/>
All rights reserved.
<br/><br/>
This Joomla! Component has been released under a GPL Commercial License.<br/>
<br/><br/>

<b>Visit <a href="http://www.rsjoomla.com/" target="_blank">http://www.rsjoomla.com/</a> - for common issues, tech support, user manual, additional downloads and up to date knowledge base articles related to RSform!Pro.</b><br/><br/>

Thank you for using RSform!Pro.
<br/><br/>
The rsjoomla.com team.
<br/><br/>');

//BACKEND COMPONENT_TYPE_FIELDS

DEFINE('_RSFORM_BACKEND_COMP_FIELD_NAME','Name');
DEFINE('_RSFORM_BACKEND_COMP_FIELD_CAPTION','Caption');
DEFINE('_RSFORM_BACKEND_COMP_FIELD_REQUIRED','Required');
DEFINE('_RSFORM_BACKEND_COMP_FIELD_SIZE','Size');
DEFINE('_RSFORM_BACKEND_COMP_FIELD_FILESIZE','File Size(KB)');
DEFINE('_RSFORM_BACKEND_COMP_FIELD_MAXSIZE','Max Size');
DEFINE('_RSFORM_BACKEND_COMP_FIELD_VALIDATIONRULE','Validation Rule');
DEFINE('_RSFORM_BACKEND_COMP_FIELD_VALIDATIONMESSAGE','Validation Message');
DEFINE('_RSFORM_BACKEND_COMP_FIELD_VALIDATIONEXTRA','Validation Extra');
DEFINE('_RSFORM_BACKEND_COMP_FIELD_ADDITIONALATTRIBUTES','Additional Attributes');
DEFINE('_RSFORM_BACKEND_COMP_FIELD_DEFAULTVALUE','Default Value');
DEFINE('_RSFORM_BACKEND_COMP_FIELD_DESCRIPTION','Description');
DEFINE('_RSFORM_BACKEND_COMP_FIELD_COMPONENTTYPE','Component Type');
DEFINE('_RSFORM_BACKEND_COMP_FIELD_COLS','Cols');
DEFINE('_RSFORM_BACKEND_COMP_FIELD_ROWS','Rows');
DEFINE('_RSFORM_BACKEND_COMP_FIELD_MULTIPLE','Multiple');
DEFINE('_RSFORM_BACKEND_COMP_FIELD_ITEMS','Items');
DEFINE('_RSFORM_BACKEND_COMP_FIELD_FLOW','Flow');
DEFINE('_RSFORM_BACKEND_COMP_FIELD_DATEFORMAT','Date Format');
DEFINE('_RSFORM_BACKEND_COMP_FIELD_CALENDARLAYOUT','Calendar Layout');
DEFINE('_RSFORM_BACKEND_COMP_FIELD_LABEL','Label');
DEFINE('_RSFORM_BACKEND_COMP_FIELD_RESET','Reset');
DEFINE('_RSFORM_BACKEND_COMP_FIELD_RESETLABEL','Reset Label');
DEFINE('_RSFORM_BACKEND_COMP_FIELD_LENGTH','Length');
DEFINE('_RSFORM_BACKEND_COMP_FIELD_BACKGROUNDCOLOR','Background Color');
DEFINE('_RSFORM_BACKEND_COMP_FIELD_TEXTCOLOR','Text Color');
DEFINE('_RSFORM_BACKEND_COMP_FIELD_VIEW_TYPE','View Type');
DEFINE('_RSFORM_BACKEND_COMP_FVALUE_DROPDOWN','Dropdown');
DEFINE('_RSFORM_BACKEND_COMP_FVALUE_CHECKBOX','Checkbox');

/** added **/
DEFINE('_RSFORM_BACKEND_COMP_FIELD_IMAGETYPE','Image Generation');
DEFINE('_RSFORM_BACKEND_COMP_FVALUE_FREETYPE','Use FreeType');
DEFINE('_RSFORM_BACKEND_COMP_FVALUE_NOFREETYPE','No FreeType');
DEFINE('_RSFORM_BACKEND_COMP_FVALUE_INVISIBLE','Invisible');

DEFINE('_RSFORM_BACKEND_SETTINGS_IIS','IIS Compatibility Mode');
DEFINE('_RSFORM_BACKEND_SETTINGS_IIS_DESC','Use this if you have problems submitting your form on a Windows server.');

DEFINE('_RSFORM_BACKEND_SETTINGS_EDITOR','WYSIWYG Editor Compatibility Mode');
DEFINE('_RSFORM_BACKEND_SETTINGS_EDITOR_DESC','Use this if you have problems with your editor settings, such as the automatically stripping of your website URL.');
/** added **/

DEFINE('_RSFORM_BACKEND_COMP_FIELD_TYPE','Type');
DEFINE('_RSFORM_BACKEND_COMP_FIELD_ACCEPTEDFILES','Accepted Files');
DEFINE('_RSFORM_BACKEND_COMP_FIELD_DESTINATION','Destination');
DEFINE('_RSFORM_BACKEND_COMP_FIELD_TEXT','Text');
DEFINE('_RSFORM_BACKEND_COMP_FIELD_IMAGERESET','Image Reset');
DEFINE('_RSFORM_BACKEND_COMP_FIELD_IMAGEBUTTON','Image Button');
DEFINE('_RSFORM_BACKEND_COMP_FIELD_SHOWREFRESH','Show Refresh');
DEFINE('_RSFORM_BACKEND_COMP_FIELD_REFRESHTEXT','Refresh Text');
DEFINE('_RSFORM_BACKEND_COMP_FIELD_READONLY','Readonly');
DEFINE('_RSFORM_BACKEND_COMP_FIELD_POPUPLABEL','Popup Label');
DEFINE('_RSFORM_BACKEND_COMP_FIELD_CHARACTERS','Characters');
DEFINE('_RSFORM_BACKEND_COMP_FIELD_ATTACHUSEREMAIL','Attach User Email');
DEFINE('_RSFORM_BACKEND_COMP_FIELD_ATTACHADMINEMAIL','Attach Admin Email');
DEFINE('_RSFORM_BACKEND_COMP_FIELD_WYSIWYG','Enable WYSIWYG Editor');
DEFINE('_RSFORM_BACKEND_COMP_FIELD_SHOW','Show in front-end?');
DEFINE('_RSFORM_BACKEND_COMP_FIELD_PBKEY','Public Key');
DEFINE('_RSFORM_BACKEND_COMP_FIELD_PRKEY','Private Key');
DEFINE('_RSFORM_BACKEND_COMP_FIELD_THEME','Theme');


//BACKEND COMPONENT_FIELD_VALUES
DEFINE('_RSFORM_BACKEND_COMP_FVALUE_NO','No');
DEFINE('_RSFORM_BACKEND_COMP_FVALUE_YES','Yes');
DEFINE('_RSFORM_BACKEND_COMP_FVALUE_INVALIDINPUT','Invalid Input');
DEFINE('_RSFORM_BACKEND_COMP_FVALUE_HORIZONTAL','Horizontal');
DEFINE('_RSFORM_BACKEND_COMP_FVALUE_VERTICAL','Vertical');
DEFINE('_RSFORM_BACKEND_COMP_FVALUE_DDMMYYYY','dd.mm.yyyy');
DEFINE('_RSFORM_BACKEND_COMP_FVALUE_FLAT','Flat');
DEFINE('_RSFORM_BACKEND_COMP_FVALUE_POPUP','Popup');
DEFINE('_RSFORM_BACKEND_COMP_FVALUE_ALPHA','Alpha');
DEFINE('_RSFORM_BACKEND_COMP_FVALUE_ALPHANUMERIC','Alphanumeric');
DEFINE('_RSFORM_BACKEND_COMP_FVALUE_NUMERIC','Numeric');
DEFINE('_RSFORM_BACKEND_COMP_FVALUE_REFRESH','Refresh');

DEFINE('_RSFORM_BACKEND_COMP_SAVE','Save');


//BACKEND FORMS MANAGE                                             
DEFINE('_RSFORM_BACKEND_FORMS_MANAGE_TITLE','Form Title');
DEFINE('_RSFORM_BACKEND_FORMS_MANAGE_NAME','Form Name');
DEFINE('_RSFORM_BACKEND_FORMS_MANAGE_PUBLISHED','Published');
DEFINE('_RSFORM_BACKEND_FORMS_MANAGE_SUBMISSIONS','Submissions');     
DEFINE('_RSFORM_BACKEND_FORMS_MANAGE_LINK','Add To Menu');
DEFINE('_RSFORM_BACKEND_FORMS_MANAGE_PREVIEW','Preview');
DEFINE('_RSFORM_BACKEND_FORMS_MANAGE_ID','Form ID');  
                                       
DEFINE('_RSFORM_BACKEND_FORMS_MANAGE_TODAY','Today: ');
DEFINE('_RSFORM_BACKEND_FORMS_MANAGE_MONTH','This Month: ');
DEFINE('_RSFORM_BACKEND_FORMS_MANAGE_ALL','All: ');
DEFINE('_RSFORM_BACKEND_FORMS_MANAGE_SEARCH','Search:');
DEFINE('_RSFORM_BACKEND_FORMS_MANAGE_SEARCH_LIMIT','Page limit:');

//FORMS ADD MENU
DEFINE('_RSFORM_BACKEND_FORMS_MENUADD_ADDED','Form added to menu');
DEFINE('_RSFORM_BACKEND_FORMS_MENUADD_MENUTITLE','Menu Title: ');


//BACKEND FORMS DELETE

DEFINE('_RSFORM_BACKEND_FORMS_DEL',' Form(s) Deleted.');

//BACKEND FORMS COPY
DEFINE('_RSFORM_BACKEND_FORMS_COPY',' Form(s) Copied.');

//BACKEND FORMS PUBLISH/UNPUBLISH
DEFINE('_RSFORM_BACKEND_SUC_PUBL_FORM',' Form(s) successfully Published.');
DEFINE('_RSFORM_BACKEND_SUC_UNPUBL_FORM',' Form(s) successfully Unpublished.');

//BACKEND FORMS EDIT
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_NO_FORM_NAME','no name');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_NO_FORM_TITLE','no title');

DEFINE('_RSFORM_BACKEND_FORMS_EDIT_TITLE_FORM_COMPONENTS','Components');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_TITLE_FORM_LAYOUT','Form Layout');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_TITLE_FORM_EDIT','Edit Form');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_TITLE_USER_EMAILS','User Emails');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_TITLE_ADMIN_EMAILS','Admin Emails');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_TITLE_SCRIPTS','Scripts');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_META','Meta Tags');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_TITLE_MAPPINGS','Mappings');

DEFINE('_RSFORM_BACKEND_FORMS_EDIT_TEXTBOX','Textbox');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_PASSWORD','Password');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_TEXTAREA','Textarea');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_DROPDOWN','Drop-down');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_CHECKBOX','Checkbox Group');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_RADIO','Radio Group');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_CALENDAR','Calendar');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_BUTTON','Button');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_SUBMITBUTTON','Submit Button');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_IMAGEBUTTON','Image Button');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_CAPTCHA','Captcha Antispam');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_FILE','File Upload');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_FREETEXT','Free Text');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_HIDDEN','Hidden Field');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_TICKET','Support Ticket');

DEFINE('_RSFORM_BACKEND_FORMS_EDIT_LAYOUT_CLASSIC','Classic Layouts');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_LAYOUT_XHTML','XHTML Layouts');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_LAYOUT_INLINE_XHTML','Inline (XHTML)');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_LAYOUT_INLINE','Inline');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_LAYOUT_2LINES','2 Lines');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_LAYOUT_2LINES_XHTML','2 Lines (XHTML)');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_LAYOUT_2COLSINLINE','2 Columns inline');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_LAYOUT_2COLS2LINES','2 Columns 2 lines');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_LAYOUT_AUTOGENERATE','Auto Generate Layout ?');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_LAYOUT_GENERATE_LAYOUT','Generate Layout');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_LAYOUT_AUTOGENERATE_MSG','Layout autogeneration has been disabled. If you add more components to your form, please make sure you add their placeholders in the HTML of the Form Layout tab or else they will not show up on your form at all.');

DEFINE('_RSFORM_BACKEND_FORMS_EDIT_FORM_EDIT_HEAD','Edit Form Properties');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_FORM_EDIT_TITLE','Form Title');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_FORM_EDIT_NAME','Form Name');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_FORM_EDIT_LANGUAGE','Form Language ISO(for multilanguage support).If you want to enable multiple language forms you must have at least two forms with the same Form Name, but with different Form Language ISO.');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_FORM_EDIT_RETURN','Return URL');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_FORM_EDIT_RETURN_DESC','You can set up a Return URL where the user will be redirected after submitting the data. If thank you is enabled (not empty), the user will first see the thank you message and then click continue to be redirected.');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_FORM_EDIT_THANKYOU','Edit the Thank You Message');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_FORM_EDIT_THANKYOU_DESC','The Thank You message appears after the user fills the form. It can be customized with your form fields ids.');

DEFINE('_RSFORM_BACKEND_FORMS_EDIT_FORM_EDIT_REQUIRED', 'Required fields will be marked with');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_FORM_EDIT_ERROR_MESSAGE', 'Error Message');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_FORM_EDIT_ERROR_MESSAGE_BUTTON', 'Edit the Error Message');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_FORM_EDIT_ERROR_MESSAGE_DESC', 'This is the error message the user will receive on an unsuccessful attempt to submit the form. Remember to add the {error} placeholder to the form layout!');

DEFINE('_RSFORM_BACKEND_FORMS_EDIT_USER_EMAILS_HEAD','User Emails');


DEFINE('_RSFORM_BACKEND_FORMS_EDIT_USER_EMAILS_RECIPIENTS','<strong>To:</strong>');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_USER_EMAILS_FROM','<strong>From:</strong>');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_USER_EMAILS_FROMNAME','<strong>From Name:</strong>');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_USER_EMAILS_SUBJECT','<strong>Subject: </strong>');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_USER_EMAILS_MODE','<strong>Mode(Text/HTML):</strong>');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_USER_EMAILS_TEXT','<strong>Message:</strong>');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_FORM_EDIT_USER_EMAILS_TEXT','Edit the e-mail text');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_USER_EMAILS_DESC','Note that you must fill in all the e-mail fields correctly in order to receive the e-mail. You can customize the e-mail text to match what the user filled in by copying the Quick Add text in the Email text field.');

DEFINE('_RSFORM_BACKEND_EDITFORMS_EMAIL_USER_SUBJECT_DEFAULT','Thank you for the submission');
DEFINE('_RSFORM_BACKEND_EDITFORMS_EMAIL_USER_TEXT_DEFAULT','You can add the form fields to this message if you enclose them in {}. Example:<br/>Dear {fullname:value}, thank you for your visit.');


DEFINE('_RSFORM_BACKEND_FORMS_EDIT_ADMIN_EMAILS_HEAD','Admin Emails');


DEFINE('_RSFORM_BACKEND_FORMS_EDIT_ADMIN_EMAILS_RECIPIENTS','<strong>To:</strong>');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_ADMIN_EMAILS_FROM','<strong>From:</strong>');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_ADMIN_EMAILS_FROMNAME','<strong>From Name:</strong>');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_ADMIN_EMAILS_SUBJECT','<strong>Subject: </strong>');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_ADMIN_EMAILS_MODE','<strong>Mode(Text/HTML):</strong>');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_ADMIN_EMAILS_ATTACH','<strong>Attach file:</strong>');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_ADMIN_EMAILS_ATTACH_FILE','<strong>Location of file to attach:</strong>');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_ADMIN_EMAILS_ATTACH_FILE_WARNING','<strong style="color: red">WARNING! Please verify if the file exists on your server.</strong>');

DEFINE('_RSFORM_BACKEND_FORMS_EDIT_ADMIN_EMAILS_TEXT','<strong>Message:</strong>');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_FORM_EDIT_ADMIN_EMAILS_TEXT','Edit the e-mail text');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_ADMIN_EMAILS_DESC','Note that you must fill in all the e-mail fields correctly in order to receive the e-mail. You can customize the e-mail text to match what the user filled in by copying the Quick Add text in the Email text field.');

DEFINE('_RSFORM_BACKEND_EDITFORMS_EMAIL_ADMIN_SUBJECT_DEFAULT','Thank you for the submission');
DEFINE('_RSFORM_BACKEND_EDITFORMS_EMAIL_ADMIN_TEXT_DEFAULT','You can add the form fields to this message if you enclose them in {}. Example:<br/>Dear {fullname:value}, thank you for your visit.');

DEFINE('_RSFORM_BACKEND_FORMS_EDIT_EMAILS_MODE_TEXT','Text');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_EMAILS_MODE_HTML','HTML');


DEFINE('_RSFORM_BACKEND_FORMS_EDIT_SCRIPTS_HEAD','Scripts');

DEFINE('_RSFORM_BACKEND_FORMS_EDIT_SCRIPTS_DISPLAY','Script called on form display');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_SCRIPTS_DISPLAY_DESC','Add your script without &lt;?php ?&gt;');

DEFINE('_RSFORM_BACKEND_FORMS_EDIT_SCRIPTS_PROCESS','Script called on form process');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_SCRIPTS_PROCESS_DESC','Add your script without &lt;?php ?&gt;');

DEFINE('_RSFORM_BACKEND_FORMS_EDIT_SCRIPTS_PROCESS2','Script called after form has been processed');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_SCRIPTS_PROCESS2_DESC','Add your script without &lt;?php ?&gt;');


DEFINE('_RSFORM_BACKEND_FORMS_META_DESCRIPTION', 'Meta Description');
DEFINE('_RSFORM_BACKEND_FORMS_META_KEYWORDS', 'Meta Keywords');
DEFINE('_RSFORM_BACKEND_FORMS_META_TITLE', 'Use form title as page title ?');


//FORMS MENUADD

DEFINE('_RSFORM_BACKEND_FORMS_MENUADD_ADD','Add <b>%s</b> to: ');
DEFINE('_RSFORM_BACKEND_FORMS_MENUADD_BTN','Save');

    //COMPONENT PREVIEW
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_COMPREVIEW_NAME','Name');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_COMPREVIEW_CAPTION','Caption');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_COMPREVIEW_PREVIEW','Component Preview');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_COMPREVIEW_EDIT','Edit');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_COMPREVIEW_REMOVE','Remove');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_COMPREVIEW_ORDERING','Ordering');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_COMPREVIEW_PUBLISHED','Published');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_COMPREVIEW_SAVE_ORDER','Save Order');
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_COMPREVIEW_NOT_AVAILABLE','Form component not available');

	//COMPONENT LAYOUT
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_COMP_QUICKADD','Quick Add');	
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_COMP_QUICKADD_DESC','Select &gt; drag &gt; drop');	
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_COMP_QUICKADD_BTN','Add');	
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_COMP_CAPTION','caption');	
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_COMP_BODY','body');	
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_COMP_VALIDATION','validation');	
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_COMP_DESCRIPTION','description');	
DEFINE('_RSFORM_BACKEND_FORMS_EDIT_COMP_VALUE','value');	

//FORMS SAVE

DEFINE('_RSFORM_BACKEND_FORMS_SAVE','Form Saved');
DEFINE('_RSFORM_BACKEND_FORMS_SAVE_ERROR_NAME','The form must have a name');

//COMPONENTS COPY SCREEN
DEFINE('_RSFORM_BACKEND_COMPONENTS_COPY','Where do you want to copy the components?');
DEFINE('_RSFORM_BACKEND_COMPONENTS_COPY_OK','Components were copied');
DEFINE('_RSFORM_BACKEND_COMPONENTS_COPY_BTN','Copy Components');


//COMPONENTS VALIDATE NAME
DEFINE('_RSFORM_BACKEND_COMPONENTS_VALIDATE_ERROR_UNIQUE_NAME','Please specify an unique name, using only alphanumeric characters for this component');
DEFINE('_RSFORM_BACKEND_COMPONENTS_VALIDATE_ERROR_DESTINATION','Please enter a non-empty destination');
DEFINE('_RSFORM_BACKEND_COMPONENTS_VALIDATE_ERROR_DESTINATION_NOT_DIR','The destination you chose is not a directory');
DEFINE('_RSFORM_BACKEND_COMPONENTS_VALIDATE_ERROR_DESTINATION_NOT_WRITABLE','The destination you chose is not writable');

//SUBMISSIONS MANAGE
DEFINE('_RSFORM_BACKEND_SUBMISSIONS_MANAGE_FORM_SELECT','View submissions for: ');
DEFINE('_RSFORM_BACKEND_SUBMISSIONS_MANAGE_VIEW','View ');
DEFINE('_RSFORM_BACKEND_SUBMISSIONS_MANAGE_FILTER','Filter ');
DEFINE('_RSFORM_BACKEND_SUBMISSIONS_MANAGE_FILTER_BTN','Filter');
DEFINE('_RSFORM_BACKEND_SUBMISSIONS_MANAGE_CLR_FILTER_BTN','Clear Filter');

DEFINE('_RSFORM_BACKEND_SUBMISSIONS_MANAGE_TABLE_ACTIONS','Actions');
DEFINE('_RSFORM_BACKEND_SUBMISSIONS_MANAGE_TABLE_ACTION_EDIT','Edit');
DEFINE('_RSFORM_BACKEND_SUBMISSIONS_MANAGE_TABLE_ACTION_REMOVE','Remove');
DEFINE('_RSFORM_BACKEND_SUBMISSIONS_MANAGE_TABLE_ACTION_RESEND','Resend Emails');

//SUBMISSIONS EXPORT
DEFINE('_RSFORM_BACKEND_SUBMISSIONS_EXPORT_HEAD_EXPORT','Export');
DEFINE('_RSFORM_BACKEND_SUBMISSIONS_EXPORT_HEAD_SUBMISSION_DATA','Submission Related Info');
DEFINE('_RSFORM_BACKEND_SUBMISSIONS_EXPORT_HEAD_COLUMN_ORDER','Column Order');
DEFINE('_RSFORM_BACKEND_SUBMISSIONS_EXPORT_HEAD_COMPONENTS','Components');

DEFINE('_RSFORM_BACKEND_SUBMISSIONS_EXPORT_DATE_SUBMITTED','Date Submitted');
DEFINE('_RSFORM_BACKEND_SUBMISSIONS_EXPORT_USER_IP','User Ip');
DEFINE('_RSFORM_BACKEND_SUBMISSIONS_EXPORT_USERNAME','Username');

DEFINE('_RSFORM_BACKEND_SUBMISSIONS_EXPORT_FOR','Export submissions for &quot;%s&quot;');
DEFINE('_RSFORM_BACKEND_SUBMISSIONS_EXPORT_ALL_ROWS','Export all rows');
DEFINE('_RSFORM_BACKEND_SUBMISSIONS_EXPORT_SELECTED_ROWS','Export selected rows');

DEFINE('_RSFORM_BACKEND_SUBMISSIONS_EXPORT_DELIMITER','Delimiter:');
DEFINE('_RSFORM_BACKEND_SUBMISSIONS_EXPORT_FIELD_ENCLOSURE','Field Enclosure:');
DEFINE('_RSFORM_BACKEND_SUBMISSIONS_EXPORT_HEADERS','Include Column Headers:');

DEFINE('_RSFORM_BACKEND_SUBMISSIONS_EXPORT_BTN','Export');
DEFINE('_RSFORM_BACKEND_SUBMISSIONS_EXPORT_MSG','Please wait, your export will start in %s seconds ...');
DEFINE('_RSFORM_BACKEND_SUBMISSIONS_EXPORT_MSG_ERROR','Could not create export file. Please make sure that <strong>%s</strong> is writable.');

//BACKEND SAVEREG
DEFINE('_RSFORM_BACKEND_SAVEREG_CODE','Please enter your code to receive updates');
DEFINE('_RSFORM_BACKEND_SAVEREG_SAVED','Registration Saved.');


//CONFIGURATION
DEFINE('_RSFORM_BACKEND_CONFIGURATION_SAVED','Configuration Saved');

//SETTINGS

DEFINE('_RSFORM_BACKEND_SETTINGS_TABS_CONFIG','Settings');
DEFINE('_RSFORM_BACKEND_SETTINGS_TABS_PLUGINS','Plugins');

DEFINE('_RSFORM_BACKEND_SETTINGS_GLOBAL_HEAD','Global Settings');
DEFINE('_RSFORM_BACKEND_SETTINGS_IMAGES_HEAD','Image Settings');
DEFINE('_RSFORM_BACKEND_SETTINGS_PATHS_HEAD','Path Settings');

DEFINE('_RSFORM_BACKEND_SETTINGS','Settings');
DEFINE('_RSFORM_BACKEND_SETTINGS_REGISTER','Code');
DEFINE('_RSFORM_BACKEND_SETTINGS_REGISTER_DESC','Please enter your code to receive updates');

DEFINE('_RSFORM_BACKEND_SETTINGS_DEBUG','Debug Mode');
DEFINE('_RSFORM_BACKEND_SETTINGS_DEBUG_DESC','Enable the debug mode.');


DEFINE('_RSFORM_BACKEND_SETTINGS_LANGUAGE','Backend Default Language');
DEFINE('_RSFORM_BACKEND_SETTINGS_LANGUAGE_DESC','');

DEFINE('_RSFORM_BACKEND_SETTINGS_SAVED','Settings Saved');

DEFINE('_RSFORM_BACKEND_SETTINGS_UPLOADDIR_WRITABLE','<font color="green"><b>Writable</b></font>');
DEFINE('_RSFORM_BACKEND_SETTINGS_UPLOADDIR_NOTWRITABLE','<font color="red"><b>Not Writable</b></font>');

DEFINE('_RSFORM_BACKEND_PLUGINS_HEAD','Plugins');

DEFINE('_RSFORM_BACKEND_PLUGINS_UPLOAD_TITLE','Install Plugins');
DEFINE('_RSFORM_BACKEND_PLUGINS_UPLOAD_FILE','Package File:');
DEFINE('_RSFORM_BACKEND_PLUGINS_BUTTON','Upload');
DEFINE('_RSFORM_BACKEND_PLUGINS_UPLOAD_INSTRUCTIONS','<h2>Plugins Instructions</h2>
<ul>
	<li>RSform!Pro Plugins expand the component functionalities. You can find a list of plugins here: <a href="http://www.rsjoomla.com/index.php/RSformPro/RSformPro-Professional-Joomla-Form-Management-Component.html" target="_blank">http://www.rsjoomla.com/index.php/RSformPro/RSformPro-Professional-Joomla-Form-Management-Component.html</a>.</li>
	<li>Click on Browse and choose a plugin file then click on the "Upload" button.</li>
</ul>
');

DEFINE('_RSFORM_BACKEND_PLUGINS_TITLE','Plugins');
DEFINE('_RSFORM_BACKEND_PLUGINS_REMOVE_OK','Plugin removed');
DEFINE('_RSFORM_BACKEND_PLUGINS_REMOVE_ERROR','Plugin could not be removed. Please check permissions.');
DEFINE('_RSFORM_BACKEND_PLUGINS_REMOVE_CONFIRM','Are you sure?');

//BACKEND BACKUP/RESTORE
DEFINE('_RSFORM_BACKEND_BACKUPRESTORE_TITLE_HEAD','Restore');
DEFINE('_RSFORM_BACKEND_BACKUPRESTORE_BACKUP','Generate a backup file');
DEFINE('_RSFORM_BACKEND_BACKUPRESTORE_PACKAGE_FILE','Package File:');
DEFINE('_RSFORM_BACKEND_BACKUPRESTORE_RESTORE_BTN','Restore');
DEFINE('_RSFORM_BACKEND_BACKUPRESTORE_INSTRUCTIONS','<h2>Backup / Restore Instructions</h2>
<ul>
	<li>Whenever you want, download a backup file by clicking on "Click here to generate a backup file". The backup will save all the uploaded files and database entries.</li>
	<li>Click on Browse and choose a backup file then click on the "Restore" button.</li>
</ul>
');
DEFINE('_RSFORM_BACKEND_BACKUPRESTORE_NOTWRITABLE','<font color="red">/media Not Writable</font><br/>Please set writable permissions to the /media directory in order to be able to download a backup file.');
DEFINE('_RSFORM_BACKEND_BACKUPRESTORE_INCLUDE_SUBMISSIONS','Include Submissions?');
DEFINE('_RSFORM_BACKEND_BACKUPRESTORE_OVERWRITE','Overwrite existing forms? <b><font color="red">Caution</font>, you will not be able to recover old forms after restoring if you check this box.</b>');
DEFINE('_RSFORM_BACKEND_BACKUPRESTORE_FORMS','Backup Only Forms');
DEFINE('_RSFORM_BACKEND_BACKUPRESTORE_FORMS_SUBMISSIONS','Backup Forms And Submissions ?');
DEFINE('_RSFORM_BACKEND_BACKUPRESTORE_FORMS_SELECT','Please select at least one form to backup');



//UPDATES

DEFINE('_RSFORM_BACKEND_UPDATESMANAGE_INSTRUCTIONS','<h2>Updates Instructions</h2>
<ul>
	<li>Whenever we release a new update, you are able to download it directly from this screen. Save the file on your computer, then click on Browse, and then install the update.</li>
</ul>');
DEFINE("_RSFORM_BACKEND_UPDATESMANAGE_BUTTON","Update");
DEFINE('_RSFORM_BACKEND_UPDATECHECK_OK','Success');
DEFINE('_RSFORM_BACKEND_UPDATECHECK_NOINSTALL','Please select a valid file.');
DEFINE('_RSFORM_BACKEND_UPDATECHECK_BADFILE','The file you selected is not valid.');
DEFINE('_RSFORM_BACKEND_UPDATECHECK_STATUS_RSFORMBACKUP','Backup loaded.');
DEFINE('_RSFORM_BACKEND_UPDATECHECK_STATUS_RSFORMUPDATE','The component was updated.');
DEFINE('_RSFORM_BACKEND_UPDATECHECK_STATUS_RSFORMPLUGIN','Plugin loaded.');

DEFINE('_RSFORM_BACKEND_UPDATESMANAGE_UPDATE_TITLE','Upload Package File');
DEFINE('_RSFORM_BACKEND_UPDATESMANAGE_UPDATE_FILE','Package File:');
DEFINE('_RSFORM_BACKEND_UPDATESMANAGE_WRITABLE','(<font color="green"><b>WRITABLE</b></font>)');
DEFINE('_RSFORM_BACKEND_UPDATESMANAGE_NOTWRITABLE','(<font color="red"><b>NOT WRITABLE</b></font>)');
DEFINE('_RSFORM_BACKEND_UPDATESMANAGE_PERMISSIONS','Please set writable permissions to the folders above and their enclosed items. You must use your ftp client and set permissions to 777 in order to make the folders writable.');



//CONTROL PANEL
DEFINE('_RSFORM_BACKEND_CPANEL_FORMS_MANAGE','Forms Manager');    
DEFINE('_RSFORM_BACKEND_CPANEL_SUBMISSIONS_MANAGE','Manage Submissions');
DEFINE('_RSFORM_BACKEND_CPANEL_CONFIGURATION_EDIT','Configuration');
DEFINE('_RSFORM_BACKEND_CPANEL_BACKUP_RESTORE','Backup / Restore');
DEFINE('_RSFORM_BACKEND_CPANEL_UPDATES_MANAGE','Check for Updates');
DEFINE('_RSFORM_BACKEND_CPANEL_SUPPORT','Support');  

DEFINE('_RSFORM_VERSION_TITLE','Installed version:');
DEFINE('_RSFORM_AUTHOR_TITLE','Author:');
DEFINE('_RSFORM_CODE_TITLE','Please enter your code to receive updates');
DEFINE('_RSFORM_REGISTER','Update Code');
DEFINE('_RSFORM_REGISTER_MODIFY','Modify Code');


DEFINE('_RSFORM_FRONTEND_THANKYOU','The form has been submitted successfully. Thank you!');
DEFINE('_RSFORM_FRONTEND_THANKYOU_BUTTON','<br/><input type="button" name="continue" value="Continue" onclick="%s"/>');


DEFINE('_RSFORM_MOD_RSFORM_LIST_DATETIME','d.m.Y');
DEFINE('_RSFORM_FRONTEND_CALENDARJS', '
//CALENDAR SETUP
var MONTHS_SHORT 	= Array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
var MONTHS_LONG 	= Array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
var WEEKDAYS_1CHAR 	= Array("S", "M", "T", "W", "T", "F", "S");
var WEEKDAYS_SHORT 	= Array("Su", "Mo", "Tu", "We", "Th", "Fr", "Sa");
var WEEKDAYS_MEDIUM = Array("Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat");
var WEEKDAYS_LONG 	= Array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
var START_WEEKDAY 	= 0;
');

?>