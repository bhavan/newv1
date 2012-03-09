<?php 
defined('_JEXEC') or die('Restricted access');

function DefaultLoadedFromTemplate($view,$template, $event, $mask){

	$db = JFactory::getDBO();
	// find published template
	$db->setQuery("SELECT * FROM #__jev_defaults WHERE state=1 AND name= ".$db->Quote($template));
	$template = $db->loadObject();

	if (is_null($template) || $template->value=="")  return false;
	// now replace the fields
	$search = array();
	$replace = array();

	$jevparams = JComponentHelper::getParams(JEV_COM_COMPONENT);

	// Built in fields
	$search[]="{{TITLE}}";$replace[]=$event->title();
	$search[]="{{DESCRIPTION}}";$replace[]=$event->content();

	$document = JFactory::getDocument();
	$document->addStyleDeclaration("div.jevdialogs {position:relative;margin-top:35px;text-align:left;}\n div.jevdialogs img{float:none!important;margin:0px}");

	if ($jevparams->get("showicalicon",0) &&  !$jevparams->get("disableicalexport",0) ){
		JHTML::script( 'view_detail.js', 'components/'.JEV_COM_COMPONENT."/assets/js/" );
		$cssloaded = true;
		ob_start();
		?>
		<a href="javascript:void(0)" onclick='clickIcalButton()' title="<?php echo JText::_('JEV_SAVEICAL');?>">
			<img src="<?php echo JURI::root().'administrator/components/'.JEV_COM_COMPONENT.'/assets/images/jevents_event_sml.png'?>" align="middle" name="image"  alt="<?php echo JText::_('JEV_SAVEICAL');?>" style="height:24px;"/>
		</a>
        <div class="jevdialogs">
        <?php
        $view->eventIcalDialog($event, $mask);
        ?>
        </div>
		
		<?php
		$search[]="{{ICALBUTTON}}";$replace[]=ob_get_clean();
	}
	else {
		$search[]="{{ICALBUTTON}}";$replace[]="";
	}

	if( $event->canUserEdit() && !( $mask & MASK_POPUP )) {
		JHTML::script( 'view_detail.js', 'components/'.JEV_COM_COMPONENT."/assets/js/" );

		ob_start();
    	?>
        <a href="javascript:void(0)" onclick='clickEditButton()' title="<?php echo JText::_('JEV_E_EDIT');?>">
        	<?php echo JHTML::_('image.site', 'edit.png', '/images/M_images/', NULL, NULL, JText::_('JEV_E_EDIT'));?>
        </a>
        <div class="jevdialogs">
        <?php
        $view->eventManagementDialog($event, $mask);
        ?>
        </div>
        
        <?php
        $search[]="{{EDITBUTTON}}";;$replace[]=ob_get_clean();
	}
	else {
		$search[]="{{EDITBUTTON}}";$replace[]="";
	}
	$search[]="{{REPEATSUMMARY}}";$replace[]=$event->repeatSummary();
	$search[]="{{PREVIOUSNEXT}}";$replace[]=$event->previousnextLinks();
	$search[]="{{CREATOR}}";$replace[]=JText::_('JEV_BY') . '&nbsp;' . $event->contactlink();
	$search[]="{{HITS}}";$replace[]=JText::_('JEV_EVENT_HITS') . ' : ' . $event->hits();

	if ($event->hasLocation()){
		$search[]="{{LOCATION_LABEL}}";$replace[]=JText::_('JEV_EVENT_ADRESSE')."&nbsp;";
		$search[]="{{LOCATION}}";$replace[]=$event->location();
	}
	else {
		$search[]="{{LOCATION_LABEL}}";$replace[]="";
		$search[]="{{LOCATION}}";$replace[]="";
	}

	if ($event->hasContactInfo()){
		$search[]="{{CONTACT_LABEL}}";$replace[]=JText::_('JEV_EVENT_CONTACT')."&nbsp;";
		$search[]="{{CONTACT}}";$replace[]=$event->contact_info();
	}
	else {
		$search[]="{{CONTACT_LABEL}}";$replace[]="";
		$search[]="{{CONTACT}}";$replace[]="";
	}

	$search[]="{{EXTRAINFO}}";$replace[]=$event->extra_info();

	// Now do the plugins
	// get list of enabled plugins

	$jevplugins = JPluginHelper::getPlugin("jevents");
	foreach ($jevplugins as $jevplugin){
		$classname = "plgJevents".ucfirst($jevplugin->name);
		if (is_callable(array($classname,"substitutefield"))){
			$fieldNameArray = call_user_func(array($classname,"fieldNameArray"));
			foreach ($fieldNameArray["values"] as $fieldname) {
				$search[]="{{".$fieldname."}}";
				$replace[]=call_user_func(array($classname,"substitutefield"),$event,$fieldname);
			}
		}
	}

	// non greedy replacement - because of the ?
	$template->value = preg_replace_callback('|{{.*?:|', 'cleanLabels', $template->value);

	$template->value =  str_replace($search,$replace,$template->value);
	echo $template->value;
	return true;
}

function cleanLabels($matches){
	if (count($matches)==1){
		return "{{";
	}
	return "";
}