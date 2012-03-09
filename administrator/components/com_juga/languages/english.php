<?php
/**
* Author: Achim Raji - www.filmanleitungen.de
* JUGA - A Joomla User Group Access Component
* @package JUGA
* @license Released under the terms of the GNU General Public License (see LICENSE.php in the Joomla! root directory)
**/

// ************************************************************************
/**
* ADMIN SIDE
*/
// ************************************************************************
// Sync
DEFINE("_juga_syncron_comps","Synchronizing components...");
DEFINE("_juga_syncron_newcomps"," New Component Entries - OK ");
DEFINE("_juga_syncron_content","Synchronizing content...");
DEFINE("_juga_syncron_newcontent"," New Content Entries - OK ");

DEFINE("_juga_syncron_users"," Synchronizing Users...");
DEFINE("_juga_syncron_newusers"," New User Entries - OK ");

//admin_juga_html.php
DEFINE("_juga_config","Config");
DEFINE("_juga_config_assign_users","Assign Users");
DEFINE("_juga_config_title","Title");
DEFINE("_juga_config_desc","Description");
DEFINE("_juga_config_value","Value");
DEFINE("_juga_config_edit","Edit");
DEFINE("_juga_config_new","New");
DEFINE("_juga_config_usergroups","User Groups");
DEFINE("_juga_config_usergroup","User Group:");
DEFINE("_juga_config_user","User");
DEFINE("_juga_config_users","Users");
DEFINE("_juga_config_username","Username");
DEFINE("_juga_config_filter","Filter:");
DEFINE("_juga_config_members","Members");
DEFINE("_juga_config_items","Items");
DEFINE("_juga_config_details","Details");
DEFINE("_juga_config_siteitems","Site Items");
DEFINE("_juga_config_option","Option");
DEFINE("_juga_config_task","Task");
DEFINE("_juga_config_typeid","Type ID");
DEFINE("_juga_config_type","Type");
DEFINE("_juga_config_errorurl","CE URL");
DEFINE("_juga_config_errorurl1","Custom Error URL");
DEFINE("_juga_config_currentgroups","Current Group(s)");
DEFINE("_juga_config_groups","Groups");
DEFINE("_juga_config_groups_enroll","Enroll");
DEFINE("_juga_config_groups_withdraw","Withdraw");
DEFINE("_juga_config_publish","Published");
DEFINE("_juga_config_unpublish","Unpublished");
// new in v0.2
DEFINE("_juga_config_define_access","Define Access");
DEFINE("_juga_config_access","Access");
DEFINE("_juga_config_current_flex_group","Flex");
DEFINE("_juga_config_usergroup_id","Group ID");
DEFINE("_juga_config_joomlagroup","Joomla Group");
DEFINE("_juga_config_item","Item");
DEFINE("_juga_config_switch","Switch");
DEFINE("_juga_config_codes","Access Codes");
DEFINE("_juga_config_codes_times","Times Allowed");
DEFINE("_juga_config_codes_hits","Hits");
DEFINE("_juga_config_codes_group","Group (ID#)");
DEFINE("_juga_config_codes_status","Status");
DEFINE("_juga_config_include","Include");
DEFINE("_juga_config_exclude","Exclude Entire Option");
DEFINE("_juga_config_errorurl_published","CE URL Published");
DEFINE("_juga_config_tools","Tools");
DEFINE("_juga_config_patch","Check JUGA installation");

//toolbar_juga_html.php
DEFINE("_juga_config_publishce","Publish CE");
DEFINE("_juga_config_defaultce","Default CE");
DEFINE("_juga_config_removece","Remove CE");
DEFINE("_juga_config_sync","Sync");
DEFINE("_juga_config_default","Default");
DEFINE("_juga_config_close","Close");
// new in v0.2
DEFINE("_juga_config_flexgroup","Flex +");
DEFINE("_juga_config_flexgroup_withdraw","Flex -");

// juga.class.php
DEFINE("_juga_title_exist","Title already exists.");
DEFINE("_juga_title_blank","Must have a title.");

// new in v0.9
DEFINE("_JUGA_INTERNALERROR","JUGA Internal Error");
DEFINE("_JUGA_RESTRICTEDRESOURCE","JUGA - Restricted Resource");
DEFINE("_JUGA_SECTION","Section");
DEFINE("_JUGA_SELECTGROUP","Select Group");
DEFINE("_JUGA_SELECTDATE","Select Date");
DEFINE("_JUGA_PUBLISHED","Published");
DEFINE("_JUGA_PUBLISHUP","Publish Up");
DEFINE("_JUGA_PUBLISHDOWN","Publish Down");
DEFINE("_JUGA_SUBMIT", "Submit");
DEFINE("_JUGA_EMAIL", "Email");
DEFINE("_JUGA_RUNPATCH_MESSAGE", "It appears you have not run the JUGA Patch to update your JUGA database tables.  Until you do, your JUGA may have errors.  Please browse to JUGA-><a href='".$mosConfig_live_site."/administrator/index2.php?option=com_juga&section=tools'>Tools</a> and check your JUGA Installation.  Thanks!");
DEFINE("_JUGA_ID", "ID");
DEFINE("_JUGA_VIEW", "View");
DEFINE("_JUGA_NORECORDS", "No Records");

// TIPS v0.9
DEFINE("_TIP_juga_config_codes_times", "The maximum number of times this access code may be used.  Use -1 for infinity.");
DEFINE("_TIP_juga_config_exclude", "If set to YES, JUGA will not impose any restrictions on this option (com_whatever) and all users will have unrestricted access to it.");
DEFINE("_TIP_juga_config_errorurl_published", "If set to YES, JUGA will redirect a user to the "._juga_config_errorurl1." above (rather than the Default Error URL as defined in JUGA - Config) when a they try to access restricted items.");
DEFINE("_TIP_juga_config_errorurl1", "If "._juga_config_errorurl_published." set to YES, JUGA will redirect a user to this URL (rather than the Default Error URL as defined in JUGA - Config) when a they try to access restricted items.");
DEFINE("_TIP_juga_config_typeid", "Many components, such as com_content (among others), use the id variable to identify items.  To restrict a specific item that uses the id variable, enter the id value here. Leave blank or set to 0 to ignore.");
DEFINE("_TIP_juga_config_task", "Nearly all components use the task variable to identify actions to perform.  To restrict a specific task, enter the task value here. Leave blank to ignore.");
DEFINE("_TIP_JUGA_SECTION", "Many components, such as com_juga (among others made by Dioscouri), use the section variable to identify particular command subsets.  To restrict a specific command subset that uses the section variable, enter the section value here. Leave blank to ignore.");
DEFINE("_TIP_juga_config_option", "All components identify themselves by their option value (whether it is com_frontpage, com_remository, com_fireboard, or com_whatever).  Enter that value here to set JUGA restriction rules.");
DEFINE("_TIP_juga_config_title", "Identify this Site Item - common values are the page title (such as Welcome to Joomla!) or the component name (such as Community Builder).");


// ************************************************************************
/**
* FRONT END 
*/
// ************************************************************************
// juga.php
DEFINE("_juga_invalid_code","Invalid Access Code.");
DEFINE("_juga_code_success","Access Codes successfully processed.");

// juga.html.php
DEFINE("_juga_code","Access Code");
