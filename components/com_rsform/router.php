<?php
/**
* @version 1.2.0
* @package RSform!Pro 1.2.0
* @copyright (C) 2007-2009 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

function RSFormBuildRoute(&$query)
{
	$segments = array();
	
	if (!empty($query['formId']))
	{
		$segments[] = 'form';
		
		$formId = (int) $query['formId'];
		
		$db = JFactory::getDBO();
		$db->setQuery("SELECT `FormTitle` FROM #__rsform_forms WHERE `FormId`='".$formId."'");
		$formName = JFilterOutput::stringURLSafe($db->loadResult());
		
		$segments[] = $formId.(!empty($formName) ? ':'.$formName : '');
		
		unset($query['formId']);
	}
	
	return $segments;
}

function RSFormParseRoute($segments)
{
	$query = array();
	
	if (!empty($segments[0]) && $segments[0] == 'form')
	{
		$exp = explode(':', @$segments[1]);
		$query['formId'] = (int) @$exp[0];
	}
	
	return $query;
}
?>