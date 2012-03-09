<?php
/**
 * copyright (C) 2008 JEV Systems Ltd - All rights reserved
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

include_once(JPATH_COMPONENT_ADMINISTRATOR."/views/".basename(dirname(__FILE__))."/".basename(__FILE__));

/**
 * HTML View class for the component
 *
 * @static
 */
class FrontPeopleViewPeople extends AdminPeopleViewPeople
{
	function __construct($config = array()){
		include_once(JPATH_ADMINISTRATOR.DS."includes".DS."toolbar.php");
		parent::__construct($config);

		// TODO find the active admin template
		JHTML::stylesheet("system.css",JURI::root()."administrator/templates/system/css/");
		JHTML::stylesheet("template.css",JURI::root()."administrator/templates/khepri/css/");
		JHTML::stylesheet("pagination.css",JURI::root()."administrator/components/com_jevpeople/assets/pagination/css/");
	}

	function people($tpl = null)
	{
		//JHTML::stylesheet("general.css",JURI::root()."administrator/templates/khepri/css/");
		JHTML::stylesheet("pagination.css",JURI::root()."administrator/components/com_jevpeople/assets/pagination/css/");
		// make sure sorting JS is loaded
		$user		=& JFactory::getUser();
		if ( !$user->get('id') ) {
			JHTML::script("joomla.javascript.js",JURI::base().'includes/js/');
		}

		JLoader::register('JEventsHTML',JPATH_SITE."/components/com_jevents/libraries/jeventshtml.php");

		global $mainframe, $option;
		JHTML::stylesheet( 'jevpeople.css', 'components/'.$option.'/assets/css/' );

		$db		=& JFactory::getDBO();
		$uri	=& JFactory::getURI();

		$filter_order		= $mainframe->getUserStateFromRequest( $option.'pers_filter_order',		'filter_order',		'pers.ordering',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'pers_filter_order_Dir',	'filter_order_Dir',	'',				'word' );
		$filter_catid		= $mainframe->getUserStateFromRequest( $option.'pers_filter_catid',	'filter_catid',		0,				'int' );
		$search				= $mainframe->getUserStateFromRequest( $option.'pers_search',			'search',			'',				'string' );
		$search				= JString::strtolower( $search );

		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;

		$compparams = JComponentHelper::getParams("com_jevpeople");

		$model	=& $this->getModel();
		$model->setState("limitstart",JRequest::getInt("limitstart",0));
		$items		=  $model->getPublicData();
		$pagination =  $model->getPublicPagination();

		$lists['typefilter'] = $this->typeFilter(false);


		// if we filtered the menu then we should filter the cats!
		$types= $compparams->get("type","");
		if ($types!=""){
			if (!is_array($types)){
				$types = array($types);
			}
			$sections = array();
			foreach ($types as $type) {
				$sections[]="'com_jevpeople_type$type'";
			}
			$javascript 	= 'onchange="document.adminForm.submit();"';
			$lists['catid'] = $this->buildCategorySelect(intval( $filter_catid ),$javascript,"",true,false,0,'filter_catid',$sections);
			$lists['catid'] = str_replace(JText::_('JEV_EVENT_ALLCAT'),JText::_("All Categories"),$lists['catid'] );
			
		}
		else {
			$firsttype = $this->getFirstType();
			$typefilter	= intval( $mainframe->getUserStateFromRequest( "type_type_id", 'type_id', $firsttype));
			$section_name  = "com_jevpeople_type".$typefilter;

			$javascript 	= 'onchange="document.adminForm.submit();"';
			$lists['catid'] = JEventsHTML::buildCategorySelect(intval( $filter_catid ),$javascript,"",true,false,0,'filter_catid',$section_name);
			$lists['catid'] = str_replace(JText::_('JEV_EVENT_ALLCAT'),JText::_("All Categories"),$lists['catid'] );
		}


		// search filter
		$lists['search']= $search;

		// check if person has any events	- a very crude test
		jimport("joomla.utilities.date");
		$startdate = new JDate("-".$compparams->get("checkeventbefore",30)." days");
		$enddate = new JDate("+".$compparams->get("checkeventafter",30)." days");

		foreach ($items as &$item) {
			$item->hasEvents = $model->hasEvents($item->pers_id, $startdate->toMySQL(), $enddate->toMySQL());
			unset($item);
		}

		$this->assignRef('items',		$items);
		$this->assignRef('lists',		$lists);
		$this->assignRef('pagination',	$pagination);

		parent::display($tpl);
	}

	/**
	 * Build HTML selection list of categories
	 *
	 * @param int $catid				Selected catid
	 * @param string $args				Additional HTML attributes for the <select> tag
	 * @param string $catidList			Restriction list of categories
	 * @param boolean $with_unpublished	Set true to build list with unpublished categories
	 * @param boolean $require_sel		First entry: true = Choose one category, false = All categories
	 * @param int $catidtop				Top level category ancestor
	 */
	function buildCategorySelect( $catid, $args, $catidList=null, $with_unpublished=false, $require_sel=false, $catidtop=0, $fieldname="catid", $sections=array(), $excludeid=false){
		
		$user =& JFactory::getUser();
		$db	=& JFactory::getDBO();
		
		$catsql = 'SELECT c.id, c.published, c.title as ctitle,p.title as ptitle, gp.title as gptitle, ggp.title as ggptitle, c.ordering , c.section' .
				// for Joomfish onlu
				' , p.id as pid, gp.id as gpid, ggp.id as ggpid '.
				' FROM #__categories AS c' .
				' LEFT JOIN #__categories AS p ON p.id=c.parent_id' .
				' LEFT JOIN #__categories AS gp ON gp.id=p.parent_id ' .
				' LEFT JOIN #__categories AS ggp ON ggp.id=gp.parent_id ' .
				//' LEFT JOIN #__categories AS gggp ON gggp.id=ggp.parent_id ' .
				' WHERE c.access<='.$db->Quote($user->aid) .
				' AND c.section IN ('.implode(",",$sections).")";
		if ($with_unpublished) {
			$catsql .= ' AND c.published >= 0';
		} else {
			$catsql .= ' AND c.published = 1';
		}
		if ($excludeid) $catsql .= ' AND c.id NOT IN ('.$excludeid.')';
		if (is_string($catidList) && strlen(trim($catidList)) ) {
			$catsql .=' AND c.id IN (' . trim($catidList) . ')';
		}
		$catsql .=" ORDER BY c.section, ordering";
		
		$db->setQuery($catsql);
		//echo $db->_sql;
		$rows = $db->loadObjectList('id');
		
		foreach ($rows as $key=>$option) {
			$title = $option->ctitle;
			if (!is_null($option->ptitle)){
				// this doesn't; work in Joomfish
				//$title = $option->ptitle."=>".$title;
				if (array_key_exists($option->pid,$rows)){
					$title = $rows[$option->pid]->ctitle."=>".$title;
				}
				else {
					$title = $option->ptitle."=>".$title;
				}
			}
			if (!is_null($option->gptitle)){
				// this doesn't; work in Joomfish
				//$title = $option->gptitle."=>".$title;
				if (array_key_exists($option->gpid,$rows)){
					$title = $rows[$option->gpid]->ctitle."=>".$title;
				}
				else {
					$title = $option->gptitle."=>".$title;
				}
			}
			if (!is_null($option->ggptitle)){
				// this doesn't; work in Joomfish
				//$title = $option->ggptitle."=>".$title;
				if (array_key_exists($option->ggpid,$rows)){
					$title = $rows[$option->ggpid]->ctitle."=>".$title;
				}
				else {
					$title = $option->ggptitle."=>".$title;
				}
			}
			/*
			if (!is_null($option->gggptitle)){
				$title = $option->gggptitle."=>".$title;
			}
			*/
			$rows[$key]->name = $title;
		}
		JArrayHelper::sortObjects($rows,"section");
		
		$t_first_entry = ($require_sel) ? JText::_('JEV_EVENT_CHOOSE_CATEG') : JText::_('JEV_EVENT_ALLCAT');
		//$categories[] = JHTML::_('select.option', '0', JText::_('JEV_EVENT_CHOOSE_CATEG'), 'id', 'name' );
		$categories[] = JHTML::_('select.option', '0', $t_first_entry, 'id', 'name' );
		
		
		if ($with_unpublished) {
			for ($i=0;$i<count($rows);$i++) {
				if ($rows[$i]->published == 0) $rows[$i]->name = $rows[$i]->name . '('. JText::_('JEV_NOT_PUBLISHED') . ')';
			}
		}

		$categories = array_merge( $categories, $rows );
		$clist = JHTML::_('select.genericlist', $categories, $fieldname, $args, 'id', 'name', $catid );

		return $clist;
	}
	
}