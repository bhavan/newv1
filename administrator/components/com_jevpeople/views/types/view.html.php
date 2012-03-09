<?php
/**
 * copyright (C) 2008 GWE Systems Ltd - All rights reserved
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the component
 *
 * @static
 */
class AdminTypesViewTypes extends JView
{

	function overview($tpl = null)
	{
		JLoader::register('JEventsHTML',JPATH_SITE."/components/com_jevents/libraries/jeventshtml.php");

		global $mainframe, $option;
		JHTML::stylesheet( 'jevpeople.css', 'administrator/components/'.$option.'/assets/css/' );

		// Set toolbar items for the page
		JToolBarHelper::title(   JText::_( 'Types Manager' ), 'generic.png' );
		JToolBarHelper::deleteList("Are you sure you want to delete these types?","types.delete");
		JToolBarHelper::editListX("types.edit");
		JToolBarHelper::addNewX("types.edit");
		if ($mainframe->isAdmin()){
			JToolBarHelper::cancel('cpanel.show', 'Control Panel' );
		}

		$this->showToolBar();

		$document =& JFactory::getDocument();
		$document->setTitle(JText::_('JEvents Extras') . ' :: ' .JText::_('Types'));

		$db		=& JFactory::getDBO();
		$uri	=& JFactory::getURI();

		$filter_state		= $mainframe->getUserStateFromRequest( $option.'pers_filter_state',		'filter_state',		'',				'word' );
		$filter_catid		= $mainframe->getUserStateFromRequest( $option.'pers_filter_catid',	'filter_catid',		0,				'int' );
		$filter_order		= $mainframe->getUserStateFromRequest( $option.'pers_filter_order',		'filter_order',		'pers.ordering',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'pers_filter_order_Dir',	'filter_order_Dir',	'',				'word' );
		$search				= $mainframe->getUserStateFromRequest( $option.'pers_search',			'search',			'',				'string' );
		$search				= JString::strtolower( $search );

		// Get data from the model
		$params =& JComponentHelper::getParams('com_jevents');
		$authorisedonly = $params->get("authorisedonly",0);
		$juser =& JFactory::getUser();
		if ($authorisedonly){
			JRequest::setVar("showglobal",$this->jevuser && $this->jevuser->cancreateglobal);
		}
		else {
			$loc_global = $params->get("loc_global",24);
			if ($juser->gid>=intval($loc_global)){
				JRequest::setVar("showglobal",1);
			}
			else {
				JRequest::setVar("showglobal",0);
			}
		}
		if ($juser->usertype=="Super Administrator"){
			JRequest::setVar("showall",1);
		}
		$model	=& $this->getModel();
		$items		= & $this->get( 'Data');
		$total		= & $this->get( 'Total');
		$pagination = & $this->get( 'Pagination' );

		// build list of categories
		$javascript 	= 'onchange="document.adminForm.submit();"';
		$compparams = JComponentHelper::getParams("com_jevtypes");

		// state filter
		$lists['state']	= JHTML::_('grid.state',  $filter_state );

		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;

		// search filter
		$lists['search']= $search;

		$this->assignRef('user',		JFactory::getUser());
		$this->assignRef('lists',		$lists);
		$this->assignRef('items',		$items);
		$this->assignRef('pagination',	$pagination);

		parent::display($tpl);

	}

	function edit($tpl = null)
	{

		JLoader::register('JEventsHTML',JPATH_SITE."/components/com_jevents/libraries/jeventshtml.php");
		JLoader::register('jevtypesCategory',JPATH_COMPONENT_ADMINISTRATOR."/libraries/categoryClass.php");

		global $mainframe, $option;
		JHTML::stylesheet( 'jevpeople.css', 'administrator/components/'.$option.'/assets/css/' );

		// Set toolbar items for the page
		$edit		= JRequest::getVar('edit',true);
		$text = !$edit ? JText::_( 'New' ) : JText::_( 'Edit' );
		JToolBarHelper::title(   JText::_( 'Type' ).': <small><small>[ ' . $text.' ]</small></small>' );
		$this->toolbarButton("types.save","save","save","Save",false);
		if (!$edit)  {
			$this->toolbarButton("types.cancel","cancel","cancel","Cancel",false);
		} else {
			// for existing items the button is renamed `close`
			$this->toolbarButton("types.cancel","cancel","cancel","Close",false);
		}

		$this->showToolBar();

		$compparams = JComponentHelper::getParams("com_jevtypes");
		$googlekey = $compparams->get("googlemapskey","");
		$googleurl = $compparams->get("googlemaps",'http://maps.google.com');
		if ($googlekey!=""){
			JHTML::script( '/maps?file=api&amp;v=2.x&amp;key='.$googlekey ,$googleurl , true);
		}

		$document =& JFactory::getDocument();
		$document->setTitle(JText::_('JEvents Extras') . ' :: ' .JText::_('Edit Type'));

		global $mainframe, $option;

		$db		=& JFactory::getDBO();
		$uri 	=& JFactory::getURI();
		$user 	=& JFactory::getUser();
		$model	=& $this->getModel();

		//get the type
		$type	=& $this->get('data');
		$isNew		= ($type->type_id < 1);

		//clean type data
		JFilterOutput::objectHTMLSafe( $type, ENT_QUOTES, 'description' );

		$db = JFactory::getDBO();
		
		$options = array();
		$options[] = JHTML::_('select.option', 0 ,JText::_('No Categorisation'));
		$options[] = JHTML::_('select.option', 1 ,JText::_('Single Category'));
		$options[] = JHTML::_('select.option', 2 ,JText::_('Multiple Categories'));
		$multicat = JHTML::_('select.genericlist', $options, 'multicat', 'class="inputbox" size="1" ', 'value', 'text', $type->multicat);
		
		$options = array();
		$options[] = JHTML::_('select.option', 0 ,JText::_('One per Event'));
		$options[] = JHTML::_('select.option', 1 ,JText::_('Multiple per Event'));
		$multiple = JHTML::_('select.genericlist', $options, 'multiple', 'class="inputbox" size="1" ', 'value', 'text', $type->multiple);

		$options = array();
		$options[] = JHTML::_('select.option', 0 ,JText::_('No'));
		$options[] = JHTML::_('select.option', 1 ,JText::_('Yes'));
		$showaddress = JHTML::_('select.genericlist', $options, 'showaddress', 'class="inputbox" size="1" ', 'value', 'text', $type->showaddress);

		$maxnumber = "<input type='text' name='maxperevent' value='".intval($type->maxperevent)."' />";

		// Must load admin language files
		$lang =& JFactory::getLanguage();
		$lang->load("com_jevents", JPATH_ADMINISTRATOR);

		JLoader::register('JEventsCategory',JPATH_ADMINISTRATOR."/components/com_jevents/libraries/categoryClass.php");
		$categories = JEventsCategory::categoriesTree();
		$catvalues = $type->categories;
		$lists['categories'] = JHTML::_('select.genericlist', $categories, 'categories[]', 'multiple="multiple" size="15"', 'value', 'text',explode("|",$catvalues));

		// get calendars
		$sql = "SELECT label as text, ics_id as value FROM #__jevents_icsfile where icaltype=2";
		$db->setQuery( $sql );
		$calendars = $db->loadObjectList();
		$calvalues  = $type->calendars;
		$lists['calendars'] = JHTML::_('select.genericlist', $calendars, 'calendars[]', 'multiple="multiple" size="15"', 'value', 'text',explode("|",$calvalues));

		$this->assignRef('lists',$lists);
		$this->assignRef('catvalues',$catvalues);
		$this->assignRef('calvalues',$calvalues);

		$this->assignRef('type',		$type);
		$this->assignRef('multiple',	$multiple);
		$this->assignRef('maxnumber',	$maxnumber);
		$this->assignRef('multicat',	$multicat);
		$this->assignRef('showaddress',	$showaddress);

		parent::display($tpl);

	}

	function toolbarButton($task = '', $icon = '', $iconOver = '', $alt = '', $listSelect = true){
		include_once(JPATH_ADMINISTRATOR."/components/com_jevents/libraries/jevbuttons.php");
		$bar = & JToolBar::getInstance('toolbar');

		// Add a standard button
		$bar->appendButton( 'Jev', $icon, $alt, $task, $listSelect );

	}

	function toolbarConfirmButton($task = '',  $msg='',  $icon = '', $iconOver = '', $alt = '', $listSelect = true){
		include_once(JPATH_ADMINISTRATOR."/components/com_jevents/libraries/jevbuttons.php");
		$bar = & JToolBar::getInstance('toolbar');

		// Add a standard button
		$bar->appendButton( 'Jevconfirm', $msg, $icon, $alt, $task, $listSelect );

	}

	// abstract method
	function showToolBar(){

	}

	function _globalHTML(&$row,$i){
		$img 	= $row->global ? 'tick.png':  'publish_x.png';
		$alt 	= $row->global ? JText::_( 'Global' ) : JText::_( 'User' );

		global $mainframe;
		if ($mainframe->isAdmin()){
			$img = '<img src="images/'. $img .'" border="0" alt="'. $alt .'" /></a>';
		}
		else {
			$img = '<img src="../administrator/images/'. $img .'" border="0" alt="'. $alt .'" /></a>';
		}

		return $img;
	}

}