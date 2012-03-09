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
class AdminCatsViewCats extends JView  
{
	function overview($tpl = null)
	{

		JHTML::stylesheet( 'jevlocations.css', 'administrator/components/'.JEVEX_COM_COMPONENT.'/assets/css/' );

		$document =& JFactory::getDocument();
		$document->setTitle(JText::_('Cats'));
		
		// Set toolbar items for the page
		$this->section  = JRequest::getString("section","com_jevlocations2");
		JToolBarHelper::title( JText::_(  'CATS_LOCATIONS' ), 'jevents' );
	
		JToolBarHelper::publishList('cats.publish');
		JToolBarHelper::unpublishList('cats.unpublish');
		JToolBarHelper::addNew('cats.edit');
		JToolBarHelper::editList('cats.edit');
		JToolBarHelper::deleteList('','cats.delete');
		JToolBarHelper::spacer();
		JToolBarHelper::custom( 'cpanel.cpanel', 'default.png', 'default.png', "CONTROL PANEL", false );
		//JToolBarHelper::help( 'screen.cats', true);

		JSubMenuHelper::addEntry(JText::_('Control Panel'), 'index.php?option='.JEVEX_COM_COMPONENT, true);
		
		$params = JComponentHelper::getParams(JEVEX_COM_COMPONENT);
		//$section = $params->getValue("section",0);
		
		global $mainframe, $option;
		$search				= $mainframe->getUserStateFromRequest( $option.'loccat_search',			'search',			'',				'string' );
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
		$document->setTitle(JText::_('Cats'));
		
		// Set toolbar items for the page
		$this->section  = JRequest::getString("section","com_jevlocations2");
		JToolBarHelper::title( JText::_( 'CATS_LOCATIONS' ), 'jevents' );
	
		JToolBarHelper::save('cats.save');
		JToolBarHelper::cancel('cats.list');
		//JToolBarHelper::help( 'screen.cats.edit', true);

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
		// get list of top level cats for dropdown filter
		$query = 'SELECT cc.id AS value, cc.title AS text FROM #__categories AS cc WHERE section="com_jevlocations2" AND parent_id=0 order by title';
		$db =& JFactory::getDBO();
		$db->setQuery($query);
		$options = $db->loadObjectList();

		$any = new stdClass();
		$any->text=JText::_("Any");
		$any->value=-1;
		
		array_unshift($options,$any);

		return JText::_("JEV PARENT CATEGORY") . " : ". JHTML::_('select.genericlist', $options, 'catid', 'class="inputbox" size="1" onchange="form.submit();"', 'value', 'text', $this->catfilter);
	}
}