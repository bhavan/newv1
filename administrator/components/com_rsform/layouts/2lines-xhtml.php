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
	
	// Start layout generation
	$out ='';
	$out.='<fieldset class="formFieldset">'."\n";
	$out.='<legend>{global:formtitle}</legend>'."\n";
	$out.='{error}'."\n";
	$out.='<ol class="formContainer">'."\n";
	foreach ($info as $r)
	{
		//build validation message
		$componentProperties=RSgetComponentProperties($r['ComponentId']);
		if(in_array($r['ComponentTypeId'],$RSadapter->config['component_ids']))
		{
			$out.="\t".'<li>'."\n";
			
			$out.= "\t\t".'<div class="formCaption2">{'.$r['PropertyValue'].':caption}'.((isset($componentProperties['REQUIRED']) && $componentProperties['REQUIRED']=='YES') ? '<strong class="formRequired">'.(isset($required) ? $required : '(*)').'</strong>' : '' ).'</div>'."\n";
			$out.= "\t\t".'<div class="formBody">{'.$r['PropertyValue'].':body}<span class="formClr">{'.$r['PropertyValue'].':validation}</span></div>'."\n";
			$out.= "\t\t".'<div class="formDescription">{'.$r['PropertyValue'].':description}</div>'."\n";
			
			$out.="\t".'</li>'."\n";
		}
	}
	$out.='</ol>'."\n";
	$out.='</fieldset>'."\n";
	// End layout generation
	
	// Clean it
	$cleanout = $db->getEscaped($out);
	// Update the layout
	$db->setQuery("UPDATE #__rsform_forms SET FormLayout='".$cleanout."' WHERE FormId=".$formId);
	$db->query();

	return $out;
?>