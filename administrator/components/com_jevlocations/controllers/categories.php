<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: categoryController.php 1117 2008-07-06 17:20:59Z tstahl $
 * @package     JEvents
 * @copyright   Copyright (C) 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://joomlacode.org/gf/project/jevents
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

JLoader::register('JevLocationsCategory',JPATH_COMPONENT_ADMINISTRATOR."/libraries/categoryClass.php");

class AdminCategoriesController extends JController {
	var $component = null;
	var $categoryTable = null;
	var $categoryClassname = null;

	/**
	 * Controler for the Control Panel
	 * @param array		configuration
	 */
	function __construct($config = array())
	{

		parent::__construct($config);
		$this->registerTask( 'list',  'overview' );
		$this->registerDefaultTask("overview");

		$this->component = 	JEVEX_COM_COMPONENT;
		$this->categoryTable = "#__categories";
		$this->categoryClassname = "JevLocationsCategory";

	}

	/**
	 * Category Management code
	 *
	 * Author: Geraint Edwards
	 */
	/**
	 * Manage categories - show lists
	 *
	 */
	function overview( )
	{
		$compparams = JComponentHelper::getParams("com_jevlocations");
		$usecats = $compparams->get("usecats",0);
		if (!$usecats){
			$this->setRedirect( "index.php?option=$this->component&task=cpanel.cpanel", "Not relevant when using Free Input of Addresses" );
			return;
		}

		global  $mainframe;
		$section_name  = JRequest::getString("section","com_jevlocations");
		$db	=& JFactory::getDBO();
		$user =& JFactory::getUser();

		if (strtolower($user->usertype)!="super administrator" && strtolower($user->usertype)!="administrator"){
			$this->setRedirect( "index.php?option=$this->component&task=cpanel.cpanel", "Not Authorised - must be super admin" );
			return;
		}

		$limit		= intval( $mainframe->getUserStateFromRequest( "cat_listlimit", 'limit', 10 ));
		$limitstart = intval( $mainframe->getUserStateFromRequest( "cat_{$this->component}limitstart", 'limitstart', 0 ));
		$catfilter	= intval( $mainframe->getUserStateFromRequest( "cat_catid", 'catid', 0 ));
		$catfilter2	= intval( $mainframe->getUserStateFromRequest( "cat_catid2", 'catid2', 0 ));

		global $mainframe,$option;
		$search				= $mainframe->getUserStateFromRequest( $option.'loccats_search',			'search',			'',				'string' );
		$search				= JString::strtolower( $search );

		$where = "";
		if (trim($search)!="") {
			$where = ' AND (LOWER(c.title) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false ). ')';
		}

		// get the total number of records
		if ($catfilter>0){
			$query = "SELECT count(*) FROM $this->categoryTable as c "
			. " LEFT JOIN $this->categoryTable as pc on c.parent_id = pc.id"
			. " LEFT JOIN $this->categoryTable as gpc on pc.parent_id = gpc.id"
			. " WHERE c.section='$section_name' "
			. " AND (c.id=$catfilter OR pc.id=$catfilter OR gpc.id=$catfilter)";
			if ($catfilter2>0){
				$query .= " AND (c.id=$catfilter2 OR pc.id=$catfilter2 OR gpc.id=$catfilter2)";
			}
			$query .= $where;
		}
		else {
			$query = "SELECT count(*) FROM $this->categoryTable as c WHERE c.section='$section_name' ";
			if ($catfilter2>0){
				$query .= " AND c.id=$catfilter2";
			}
			$query .= $where;
		}

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
			. (($catfilter2>0)?" AND (c.id=$catfilter2 OR pc.id=$catfilter2 OR gpc.id=$catfilter2)":"")
			. $where
			. " ORDER BY ordering, parenttitle, c.title ";
		}
		else {
			$sql = "SELECT c.* , g.name AS _groupname, pc.title as parenttitle FROM $this->categoryTable as c"
			. " LEFT JOIN $this->categoryTable as pc on c.parent_id = pc.id"
			. " LEFT JOIN #__groups AS g ON g.id = c.access"
			. " WHERE c.section='$section_name' "
			. (($catfilter2>0)?" AND (c.id=$catfilter2 OR pc.id=$catfilter2)":"")
			. $where
			. " ORDER BY ordering, parenttitle, c.title ";

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

		// get the view
		$this->view = & $this->getView("categories","html");

		// Set the layout
		$this->view->setLayout('overview');
		$this->view->assign('title'   , JText::_("Categories"));
		$this->view->assign('cats',$cats);
		$this->view->assign('catfilter',$catfilter);
		$this->view->assign('catfilter2',$catfilter2);
		$this->view->assign('pageNav',$pageNav);

		$this->view->display();

	}

	/**
	 * Category Editing code
	 *
	 * Author: Geraint Edwards
	 * 
	 */
	function edit(){
		$cid = JRequest::getVar(	'cid',	array(0) );
		JArrayHelper::toInteger($cid);

		$section_name  = JRequest::getString("section","com_jevlocations");

		$user =& JFactory::getUser();

		if (strtolower($user->usertype)!="super administrator" && strtolower($user->usertype)!="administrator"){
			$this->setRedirect( "index.php?option=".JEVEX_COM_COMPONENT."&task=categories", "Not Authorised - must be super admin" );
			return;
		}

		$db	=& JFactory::getDBO();

		if (count($cid)<=0){
			$this->setRedirect( "index.php?option=".JEVEX_COM_COMPONENT."&task=categories", "Invalid Category Selection" );
			return;
		}
		else {
			$cid=$cid[0];
		}
		$cat = new $this->categoryClassname($db,$this->categoryTable);
		$cat->load($cid);

		// get categories for parent info
		$sql = "SELECT c.* FROM $this->categoryTable as c "
		."\n WHERE section='$section_name' AND c.id<>$cid"
		."\n ORDER BY ordering"
		;
		$db->setQuery($sql);
		$rows = $db->loadObjectList();
		$cats = array();
		// empty row
		$emptycat = new $this->categoryClassname($db,$this->categoryTable);
		$emptycat->title=JText::_("JEV_CATEGORY_PARENT_NONE");
		$cats[0]=$emptycat;

		if ($rows){
			foreach ($rows as $row) {
				$tempcat = new $this->categoryClassname($db,$this->categoryTable);
				$tempcat->bind(get_object_vars($row), $row->section);
				$cats[]=$tempcat;

			}
		}
		JLoader::register('JEventsHTML',JPATH_SITE."/components/com_jevents/libraries/jeventshtml.php");
		$plist = JEventsHTML::buildCategorySelect(intval( $cat->parent_id ),'',"",false,false,0,'parent_id','com_jevlocations');
		//$plist = JHTML::_('select.genericlist', $cats, 'parent_id', 'class="inputbox" size="1"',"id","title",$cat->parent_id);

		// get list of groups
		$query = "SELECT id AS value, name AS text"
		. "\n FROM #__groups"
		. "\n ORDER BY id"
		;
		$db->setQuery( $query );
		$groups = $db->loadObjectList();

		// build the html select list
		$glist = JHTML::_('select.genericlist', $groups, 'access', 'class="inputbox" size="1"',
		'value', 'text', intval( $cat->access ) );

		// get the view
		$this->view = & $this->getView("categories","html");

		// Set the layout
		$this->view->setLayout('edit');
		$this->view->assign('title'   , JText::_("Categories"));
		$this->view->assign('cat',$cat);
		$this->view->assign('plist',$plist);
		$this->view->assign('glist',$glist);

		$this->view->display();
	}

	/**
	 * Category Saving code
	 *
	 * Author: Geraint Edwards
	 * 
	 */
	function save(){
		$db	=& JFactory::getDBO();
		$user =& JFactory::getUser();

		$cid = JRequest::getVar(	'cid',	array(0) );
		JArrayHelper::toInteger($cid);

		if (strtolower($user->usertype)!="super administrator" && strtolower($user->usertype)!="administrator"){
			$this->setRedirect( "index.php?option=".JEVEX_COM_COMPONENT."&task=cpanel.cpanel", "Not Authorised - must be super admin" );
			return;
		}

		$cat = new $this->categoryClassname($db,$this->categoryTable);

		$section = JRequest::getString("section","com_jevlocations");
		if (!$cat->bind( JRequest::get('request', JREQUEST_ALLOWHTML), $section)) {
			echo "<script> alert('".$cat->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}

		if (!$cat->check()) {
			echo "<script> alert('".$cat->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}

		if (!$cat->store()) {
			echo "<script> alert('".$cat->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		$cat->checkin();
		$cat->reorder( "section='$cat->section'" );

		$this->setRedirect( "index.php?option=".JEVEX_COM_COMPONENT."&task=categories.list&section=$section", JText::_('JEV_ADMIN_CATSUPDATED'));

	}

	/**
	 * Category Ordering code
	 *
	 * Author: Geraint Edwards
	 * Copyright: 2007 Geraint Edwards
	 * 
	 */
	function saveorder(){
		$user =& JFactory::getUser();
		if (strtolower($user->usertype)!="super administrator" && strtolower($user->usertype)!="administrator"){
			$this->setRedirect( "index.php?option=".JEVEX_COM_COMPONENT."&task=cpanel.cpanel", "Not Authorised - must be super admin" );
			return;
		}
		$cid = JRequest::getVar(	'cid',	array(0) );
		JArrayHelper::toInteger($cid);

		$db	=& JFactory::getDBO();
		$order	= JRequest::getVar(		'order', 		array(0) );
		if (count($order)!=count($cid)){
			$this->setRedirect( "index.php?option=".JEVEX_COM_COMPONENT."&task=cpanel.cpanel", "Category order problems" );
			return;
		}
		for ($k=0;$k<count($cid);$k++){
			$cat = new $this->categoryClassname($db,$this->categoryTable);
			$cat->load($cid[$k]);
			$cat->ordering = $order[$k];
			$cat->store();
		}
		$section = JRequest::getString("section","com_jevlocations");
		$this->setRedirect( "index.php?option=".JEVEX_COM_COMPONENT."&task=categories.list&section=$section", JText::_('JEV_ADMIN_CATSUPDATED'));
		return;
	}

	/*
	function saveorder($cid = array(), $order)
	{
	$row =& $this->getTable();
	$groupings = array();

	// update ordering values
	for( $i=0; $i < count($cid); $i++ )
	{
	$row->load( (int) $cid[$i] );
	// track categories
	$groupings[] = $row->catid;

	if ($row->ordering != $order[$i])
	{
	$row->ordering = $order[$i];
	if (!$row->store()) {
	$this->setError($this->_db->getErrorMsg());
	return false;
	}
	}
	}

	// execute updateOrder for each parent group
	$groupings = array_unique( $groupings );
	foreach ($groupings as $group){
	$row->reorder('catid = '.(int) $group);
	}

	return true;
	}

	*/

	/**
	 * Category Deletion code
	 *
	 * Author: Geraint Edwards
	 * 
	 */	
	function delete(){
		$user =& JFactory::getUser();
		if (strtolower($user->usertype)!="super administrator" && strtolower($user->usertype)!="administrator"){
			$this->setRedirect( "index.php?option=".JEVEX_COM_COMPONENT."&task=cpanel.cpanel", "Not Authorised - must be super admin" );
			return;
		}
		$cid = JRequest::getVar(	'cid',	array(0) );
		JArrayHelper::toInteger($cid);
		$catids = implode(",",$cid);

		// REMEMBER TO CLEAN OUT THE MAPPING TOO!!
		$db	=& JFactory::getDBO();

		if (strlen($catids)==""){
			$this->setRedirect( "index.php?option=".JEVEX_COM_COMPONENT."&task=cpanel.cpanel", "Bad categories" );
			return;
		}

		$query = "DELETE FROM $this->categoryTable WHERE id in ($catids)";
		$db->setQuery( $query );
		$db->query();

		$section = JRequest::getString("section","com_jevlocations");
		$this->setRedirect( "index.php?option=".JEVEX_COM_COMPONENT."&task=categories.list&section=$section", "Category(s) deleted" );
		return;
	}


	function publish(){
		$cid = JRequest::getVar(	'cid',	array(0) );
		JArrayHelper::toInteger($cid);
		$this->toggleCatPublish($cid,1);
	}

	function unpublish(){
		$cid = JRequest::getVar(	'cid',	array(0) );
		JArrayHelper::toInteger($cid);
		$this->toggleCatPublish($cid,0);
	}

	function toggleCatPublish($cid,$newstate){
		$user =& JFactory::getUser();
		if (strtolower($user->usertype)!="super administrator" && strtolower($user->usertype)!="administrator"){
			$this->setRedirect( "index.php?option=".JEVEX_COM_COMPONENT."&task=cpanel.cpanel", "Not Authorised - must be super admin" );
			return;
		}

		foreach ($cid as $kid) {
			if ($kid>0){
				$cat = JTable::getInstance("category");
				$cat->load($kid);
				$cat->published = $newstate;
				$cat->store();
			}
		}
		$section = JRequest::getString("section","com_jevlocations");
		$this->setRedirect( "index.php?option=".JEVEX_COM_COMPONENT."&task=categories.list&section=$section", JText::_('JEV_ADMIN_CATSUPDATED'));

	}

}
