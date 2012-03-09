<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
require_once( JApplicationHelper::getPath( 'toolbar_html' ) );
switch($task)
{
	case 'edit':
		TOOLBAR_pagemeta::_EDIT();
		break;
	case 'add':
		TOOLBAR_pagemeta::_NEW();
		break;
	case 'globalseting':
		TOOLBAR_pagemeta::_GLOBAL();
		break;
	default:
		TOOLBAR_pagemeta::_DEFAULT();
		break;
}
?>