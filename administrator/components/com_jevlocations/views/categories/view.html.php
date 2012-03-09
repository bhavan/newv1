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
class AdminCategoriesViewCategories extends JView  
{
	function overview($tpl = null)
	{

		JHTML::stylesheet( 'jevlocations.css', 'administrator/components/'.JEVEX_COM_COMPONENT.'/assets/css/' );

		$document =& JFactory::getDocument();
		$document->setTitle(JText::_('Categories'));
		
		// Set toolbar items for the page
		$this->section  = JRequest::getString("section","com_jevlocations");
		JToolBarHelper::title( JText::_(  'JEV_COUNTRY_STATE_CITY' ), 'jevents' );
	
		JToolBarHelper::publishList('categories.publish');
		JToolBarHelper::unpublishList('categories.unpublish');
		JToolBarHelper::addNew('categories.edit');
		JToolBarHelper::editList('categories.edit');
		JToolBarHelper::deleteList('','categories.delete');
		JToolBarHelper::spacer();
		JToolBarHelper::custom( 'cpanel.cpanel', 'default.png', 'default.png', JText::_('JEV_ADMIN_CPANEL'), false );
		//JToolBarHelper::help( 'screen.categories', true);

		JSubMenuHelper::addEntry(JText::_('Control Panel'), 'index.php?option='.JEVEX_COM_COMPONENT, true);
		
		$params = JComponentHelper::getParams(JEVEX_COM_COMPONENT);
		//$section = $params->getValue("section",0);
		
		global $mainframe, $option;
		$search				= $mainframe->getUserStateFromRequest( $option.'loccats_search',			'search',			'',				'string' );
		$search				= JString::strtolower( $search );
		// search filter
		$lists['search']= $search;
		$this->assignRef('lists',		$lists);
		
		JHTML::_('behavior.tooltip');
	}	


	function edit($tpl = null)
	{
		JRequest::setVar( 'hidemainmenu', 1 );
		
		JHTML::stylesheet( 'jevlocations.css', 'administrator/components/'.JEVEX_COM_COMPONENT.'/assets/css/' );

		$document =& JFactory::getDocument();
		$document->setTitle(JText::_('Categories'));
		
		// Set toolbar items for the page
		$this->section  = JRequest::getString("section","com_jevlocations");
		JToolBarHelper::title( JText::_( 'JEV_COUNTRY_STATE_CITY' ), 'jevents' );
	
		JToolBarHelper::save('categories.save');
		JToolBarHelper::cancel('categories.list');
		//JToolBarHelper::help( 'screen.categories.edit', true);

		JSubMenuHelper::addEntry(JText::_('Control Panel'), 'index.php?option='.JEVEX_COM_COMPONENT, true);
		
		$params = JComponentHelper::getParams(JEVEX_COM_COMPONENT);
		//$section = $params->getValue("section",0);
				
		JHTML::_('behavior.tooltip');
	}	

	/**
	 * Control Panel display function
	 *
	 * @param template $tpl
	 */
	function display($tpl = null)
	{
		$layout = $this->getLayout();
		if (method_exists($this,$layout)){
			$this->$layout($tpl);
		} 			

		parent::display($tpl);
	}
	
	function displaytemplate($tpl = null)
	{
		return parent::display($tpl);
	}
	
	function catFilter(){
		// get list of top level categories for dropdown filter
		$query = 'SELECT cc.id AS value, cc.title AS text FROM #__categories AS cc WHERE section="com_jevlocations" AND parent_id=0 order by title';
		$db =& JFactory::getDBO();
		$db->setQuery($query);
		$options = $db->loadObjectList();

		$any = new stdClass();
		$any->text=JText::_("Any");
		$any->value=-1;
		
		array_unshift($options,$any);

		return JText::_("Country") . " : ". JHTML::_('select.genericlist', $options, 'catid', 'class="inputbox" size="1" onchange="form.submit();"', 'value', 'text', $this->catfilter);
	}
	
	function catFilter2(){
		
		global $mainframe;

		$catfilter	= intval( $mainframe->getUserStateFromRequest( "cat_catid", 'catid', 0 ));
		if ($catfilter>0) $catfilter = " AND pc.id=$catfilter";
		else $catfilter="";
		
		// get list of top level categories for dropdown filter
		$query = 'SELECT cc.id AS value, cc.title AS text FROM #__categories AS cc LEFT JOIN #__categories AS pc on cc.parent_id = pc.id WHERE cc.section="com_jevlocations" '.$catfilter.' AND pc.parent_id=0 order by cc.title';
		$db =& JFactory::getDBO();
		$db->setQuery($query);
		$options = $db->loadObjectList();

		$any = new stdClass();
		$any->text=JText::_("Any");
		$any->value=-1;
		
		array_unshift($options,$any);

		return JText::_("State") . " : ". JHTML::_('select.genericlist', $options, 'catid2', 'class="inputbox" size="1" onchange="form.submit();"', 'value', 'text', $this->catfilter2);
	}
	
}