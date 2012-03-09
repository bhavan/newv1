<?php
/**
* @version 1.2.0
* @package RSform!Pro 1.2.0
* @copyright (C) 2007-2009 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


require_once( $mainframe->getPath( 'toolbar_html' ) );

switch ( $task ) {
		case "forms.manage":
		menuRsform::FORMSMANAGE_MENU();
		break;
		case "forms.edit":
		menuRsform::FORMSEDIT_MENU();
		break;
		case "components.copy.screen":
		menuRsform::COMPONENTS_COPY_MENU();
		break;
		case "submissions.manage":
		menuRsform::SUBMISSIONSMANAGE_MENU();
		break;


		case "configuration.edit":
			menuRsform::SETTINGS_MENU();
		break;
		default:
		menuRsform::_DEFAULT();
		break;

	}

?>