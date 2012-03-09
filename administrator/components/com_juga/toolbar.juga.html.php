<?php
// ensure this file is being included by a parent file
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

// ensure user has access to this function
if (!($acl->acl_check('administration', 'edit', 'users', $my->usertype, 'components', 'all') || $acl->acl_check('administration', 'edit', 'users', $my->usertype, 'components', 'com_groups'))) {
    mosRedirect('index2.php', _NOT_AUTH);
}

	if (file_exists($mosConfig_absolute_path.'/administrator/components/com_juga/help/screen.juga_'.$mosConfig_lang.'.html')) {
			$helpfile = 'screen.juga_'.$mosConfig_lang;
	} else {
			$helpfile = 'screen.juga_english';
	}

class TOOLBAR_juga {
    function defaults( $pagetitle = _juga_config ) {
		JToolBarHelper::title( $pagetitle, 'juga_logo' );
		JToolBarHelper::spacer();
		JToolBarHelper::editList();
		JToolBarHelper::spacer();
		JToolBarHelper::deleteList();
		JToolBarHelper::spacer();
		JToolBarHelper::addNew();
		JToolBarHelper::spacer();
		// JToolBarHelper::help( $helpfile, true );
    }

    function items() {
		JToolBarHelper::title( _juga_config_siteitems, 'juga_logo');
		JToolBarHelper::spacer();
		JToolBarHelper::custom('sync_items', "reload_f2.png", "reload_f2.png", _juga_config_sync, false);
		JToolBarHelper::spacer();
		JToolBarHelper::custom('enroll_default', "groups_f2.png", "groups_f2.png", _juga_config_default, true);
		JToolBarHelper::spacer();
		JToolBarHelper::custom('withdraw_all', "file_f2.png", "file_f2.png", _juga_config_groups_withdraw, true);
		JToolBarHelper::spacer();
		JToolBarHelper::custom('enroll_flex', "folder_add_f2.png", "folder_add_f2.png", _juga_config_flexgroup, true);
		JToolBarHelper::spacer();
		JToolBarHelper::custom('withdraw_flex', "move_f2.png", "move_f2.png", _juga_config_flexgroup_withdraw, true);
		JToolBarHelper::spacer();
		JToolBarHelper::custom('ce_default', "go_f2.png", "go_f2.png", _juga_config_defaultce, true);
		JToolBarHelper::spacer();
		JToolBarHelper::custom('ce_remove', "cut_f2.png", "cut_f2.png", _juga_config_removece, true);
		JToolBarHelper::spacer();
		JToolBarHelper::publishList('publish', _juga_config_publishce );
		JToolBarHelper::spacer();
		JToolBarHelper::unpublishList();
		JToolBarHelper::spacer();
		JToolBarHelper::deleteList();
		JToolBarHelper::spacer();
		JToolBarHelper::custom('switch_inclusion', "switch_f2.png", "switch_f2.png", "In/Exclude", true);
		JToolBarHelper::spacer();
		JToolBarHelper::addNew();
		JToolBarHelper::spacer();
		// JToolBarHelper::help( $helpfile, true );
    }

    function u2g() {
		JToolBarHelper::title( _juga_config_assign_users, 'juga_logo');
		JToolBarHelper::spacer();
		JToolBarHelper::custom('enroll_default', "groups_f2.png", "groups_f2.png", _juga_config_default, true);
		JToolBarHelper::spacer();
		JToolBarHelper::custom('remove', "file_f2.png", "file_f2.png", _juga_config_groups_withdraw, true);
		JToolBarHelper::spacer();
		// JToolBarHelper::help( $helpfile, true );
    }

    function i2g( $pagetitle ) {
		JToolBarHelper::title( _juga_config_define_access.$pagetitle, 'juga_logo');
		JToolBarHelper::spacer();
		JToolBarHelper::custom('switch', "switch_f2.png", "switch_f2.png", _juga_config_switch, true);
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		JToolBarHelper::spacer();
		// JToolBarHelper::help( $helpfile, true );
    }

    function g2u( $pagetitle ) {
		JToolBarHelper::title( _juga_config_define_access.$pagetitle, 'juga_logo');
		JToolBarHelper::spacer();
		JToolBarHelper::custom('switch', "switch_f2.png", "switch_f2.png", _juga_config_switch, true);
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		JToolBarHelper::spacer();
		// JToolBarHelper::help( $helpfile, true );
    }
	
    function config() {
		JToolBarHelper::title( _juga_config, 'juga_logo');
		JToolBarHelper::editList();
		JToolBarHelper::spacer();
		JToolBarHelper::deleteList();
		JToolBarHelper::spacer();
		JToolBarHelper::addNew();
		JToolBarHelper::spacer();
		// JToolBarHelper::help( $helpfile, true );
    }
	
    function codes() {
		JToolBarHelper::title( _juga_config_codes, 'juga_logo');
		JToolBarHelper::publishList();
		JToolBarHelper::spacer();
		JToolBarHelper::unpublishList();
		JToolBarHelper::spacer();		
		JToolBarHelper::editList();
		JToolBarHelper::spacer();
		JToolBarHelper::deleteList();
		JToolBarHelper::spacer();
		JToolBarHelper::addNew();
		JToolBarHelper::spacer();
		// JToolBarHelper::help( $helpfile, true );
    }

	function edit( $pagetitle, $id ) {
		JToolBarHelper::title( $id ? $pagetitle." - "._juga_config_edit: $pagetitle." - "._juga_config_new, 'juga_logo');
		JToolBarHelper::save();
		JToolBarHelper::spacer();
		if ( $id ) {
			// for existing content items the button is renamed `close`
			JToolBarHelper::cancel( 'cancel', _juga_config_close );
		} else {
			JToolBarHelper::cancel();
		}
		JToolBarHelper::spacer();
		// JToolBarHelper::help( $helpfile, true );
	}

    function blank( $pagetitle = "JUGA - Joomla User Group Access" ) {
		JToolBarHelper::title( $pagetitle, 'juga_logo' );
		// JToolBarHelper::help( $helpfile, true );
    }
	
}