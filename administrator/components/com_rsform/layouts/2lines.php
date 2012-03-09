<?php
/**
* @version 1.2.0
* @package RSform!Pro 1.2.0
* @copyright (C) 2007-2009 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

	$db = JFactory::getDBO();
	
	// Select components
	$db->setQuery("SELECT #__rsform_properties.PropertyValue, #__rsform_components.ComponentId, #__rsform_components.ComponentTypeId FROM #__rsform_components LEFT JOIN #__rsform_properties ON #__rsform_properties.ComponentId=#__rsform_components.ComponentId WHERE #__rsform_components.FormId=".$formId." AND #__rsform_properties.PropertyName='NAME' AND #__rsform_components.Published=1 ORDER BY #__rsform_components.Order");
	$info = $db->loadAssocList();
	
	$db->setQuery("SELECT `Required` FROM #__rsform_forms WHERE `FormId`='".(int) $formId."'");
	$required = $db->loadResult();
	
	$out ='<div class="componentheading">{global:formtitle}</div>'."\n";
	$out.='{error}'."\n";
	$out.="<div>\n";
	foreach ($info as $r)
	{
		$componentProperties=RSgetComponentProperties($r['ComponentId']);
		if(in_array($r['ComponentTypeId'],$RSadapter->config['component_ids']))
		{
			//$out.= "\t<tr>\n";
			$out.= "\t{".$r['PropertyValue'].":caption}".((array_key_exists('REQUIRED',$componentProperties) && $componentProperties['REQUIRED']=='YES') ? ' '.(isset($required) ? $required : '(*)') : "" )."<br/>\n";
			$out.= "\t{".$r['PropertyValue'].":body}<br/>\n";
			$out.= "\t{".$r['PropertyValue'].":validation}\n";
			$out.= "\t{".$r['PropertyValue'].":description}<br/>\n";
			//$out.= "\t</tr>\n";
		}
	}
	$out.= "</div>\n";
	
	// Clean it
	$cleanout = $db->getEscaped($out);
	// Update the layout
	$db->setQuery("UPDATE #__rsform_forms SET FormLayout='".$cleanout."' WHERE FormId=".$formId);
	$db->query();

	return $out;
?>