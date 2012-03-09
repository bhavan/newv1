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
	private $firsttype = false;

	function overview($tpl = null)
	{

		JHTML::stylesheet( 'jevpeople.css', 'administrator/components/'.JEVEX_COM_COMPONENT.'/assets/css/' );

		$document =& JFactory::getDocument();
		$document->setTitle(JText::_('Categories'));

		// Set toolbar items for the page
		$this->section  = JRequest::getString("section","com_jevpeople");
		JToolBarHelper::title( JText::_(  'Categories : People' ), 'jevents' );

		JToolBarHelper::publishList('categories.publish');
		JToolBarHelper::unpublishList('categories.unpublish');
		JToolBarHelper::addNew('categories.edit');
		JToolBarHelper::editList('categories.edit');
		JToolBarHelper::deleteList('','categories.delete');
		JToolBarHelper::spacer();
		JToolBarHelper::custom( 'cpanel.cpanel', 'default.png', 'default.png', 'CONTROL PANEL', false );
		JToolBarHelper::help( 'screen.categories', true);

		JSubMenuHelper::addEntry(JText::_('Control Panel'), 'index.php?option='.JEVEX_COM_COMPONENT, true);

		$params = JComponentHelper::getParams(JEVEX_COM_COMPONENT);
		//$section = $params->getValue("section",0);

		JHTML::_('behavior.tooltip');
		
		global  $mainframe;

		$firsttype = $this->getFirstType();
		
		$limit		= intval( $mainframe->getUserStateFromRequest( "cat_listlimit", 'limit', 10 ));
		$limitstart = intval( $mainframe->getUserStateFromRequest( "cat_{com_jevpeople}limitstart", 'limitstart', 0 ));
		$catfilter	= intval( $mainframe->getUserStateFromRequest( "cat_catid", 'catid', 0 ));
		$typefilter	= intval( $mainframe->getUserStateFromRequest( "type_type_id", 'type_id', $firsttype));

		$section_name  = "com_jevpeople_type".$typefilter;

		// get the total number of records
		if ($catfilter>0){
			$query = "SELECT count(*) FROM $this->categoryTable as c "
			. " LEFT JOIN $this->categoryTable as pc on c.parent_id = pc.id"
			. " LEFT JOIN $this->categoryTable as gpc on pc.parent_id = gpc.id"
			. " WHERE c.section='$section_name' "
			. " AND (c.id=$catfilter OR pc.id=$catfilter OR gpc.id=$catfilter)";
		}
		else {
			$query = "SELECT count(*) FROM $this->categoryTable WHERE section='$section_name' ";
		}
		$db =  JFactory::getDBO();

		$db->setQuery( $query);
		$total = $db->loadResult();
		echo $db->getErrorMsg();

		if( $limit > $total ) {
			$limitstart = 0;
		}

		$db	=& JFactory::getDBO();

		// get the total number of records
		if ($catfilter>0){
			$sql = "SELECT c.* , g.name AS _groupname, pc.title as parenttitle FROM $this->categoryTable as c"
			. " LEFT JOIN $this->categoryTable as pc on c.parent_id = pc.id"
			. " LEFT JOIN $this->categoryTable as gpc on pc.parent_id = gpc.id"
			. " LEFT JOIN #__groups AS g ON g.id = c.access"
			. " WHERE c.section='$section_name'"
			. " AND (c.id=$catfilter OR pc.id=$catfilter OR gpc.id=$catfilter)"
			. " ORDER BY parenttitle, c.title ";
		}
		else {
			$sql = "SELECT c.* , g.name AS _groupname, pc.title as parenttitle FROM $this->categoryTable as c"
			. " LEFT JOIN $this->categoryTable as pc on c.parent_id = pc.id"
			. " LEFT JOIN #__groups AS g ON g.id = c.access"
			. " WHERE c.section='$section_name' "
			. " ORDER BY parenttitle, c.title ";

		}
		if ($limit>0){
			$sql .= "\n LIMIT $limitstart, $limit";
		}

		$db->setQuery($sql);
		$rows = $db->loadObjectList();
		
		$cats = array();
		if ($rows){
			foreach ($rows as $row) {
				$cat = new $this->categoryClassname($db,$this->categoryTable);
				$cat->bind(get_object_vars($row), $row->section);
				// extra fields
				$cat->_groupname = $row->_groupname;
				$cat->parenttitle = $row->parenttitle;
				$cats[$cat->id]=$cat;
			}
		}

		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $limitstart, $limit  );
		
		$this->assign('cats',$cats);
		$this->assign('catfilter',$catfilter);
		$this->assign('pageNav',$pageNav);
		
	}


	function edit($tpl = null)
	{
		JRequest::setVar( 'hidemainmenu', 1 );

		JHTML::stylesheet( 'jevpeople.css', 'administrator/components/'.JEVEX_COM_COMPONENT.'/assets/css/' );

		$document =& JFactory::getDocument();
		$document->setTitle(JText::_('Categories'));

		// Set toolbar items for the page
		$this->section  = JRequest::getString("section","com_jevpeople");
		JToolBarHelper::title( JText::_( 'Categories : People' ), 'jevents' );

		JToolBarHelper::save('categories.save');
		JToolBarHelper::cancel('categories.list');
		JToolBarHelper::help( 'screen.categories.edit', true);

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
		$typefilter	= $this->getFirstType();
		
		// get list of top level categories for dropdown filter
		$query = 'SELECT cc.id AS value, cc.title AS text FROM #__categories AS cc WHERE section="com_jevpeople_type'.$typefilter.'" AND parent_id=0 order by title';
		$db =& JFactory::getDBO();
		$db->setQuery($query);
		$options = $db->loadObjectList();

		$any = new stdClass();
		$any->text=JText::_("Any");
		$any->value=-1;

		array_unshift($options,$any);

		return JText::_("Top Level Category") . " : ". JHTML::_('select.genericlist', $options, 'catid', 'class="inputbox" size="1" id="catid" onchange="form.submit();"', 'value', 'text', $this->catfilter);
	}

	function typeFilter($asinput = false, $javascript = ""){
		$typefilter	= $this->getFirstType();

		$query = 'SELECT tp.type_id AS value, tp.title AS text FROM #__jev_peopletypes AS tp order by title';
		$db =& JFactory::getDBO();
		$db->setQuery($query);
		$options = $db->loadObjectList();

		if (!$asinput) 	return JText::_("People Type") . " : ". JHTML::_('select.genericlist', $options, 'type_id', 'class="inputbox" size="1" onchange="document.getElementById(\'catid\').value=-1;form.submit();"', 'value', 'text', $typefilter);
		else 	return JHTML::_('select.genericlist', $options, 'type_id', 'class="inputbox" size="1" '.$javascript, 'value', 'text', $typefilter);
	}

	function getFirstType(){
		if (!$this->firsttype){
			$query = 'SELECT * FROM #__jev_peopletypes AS tp order by title limit 1';
			$db =& JFactory::getDBO();
			$db->setQuery($query);
			$firsttype = $db->loadObject();
			global $mainframe;
			$this->firsttype	= intval( $mainframe->getUserStateFromRequest( "type_type_id", 'type_id', $firsttype->type_id ));
		}
		return $this->firsttype;
	}

}