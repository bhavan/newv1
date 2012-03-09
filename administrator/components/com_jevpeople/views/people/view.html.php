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
class AdminPeopleViewPeople extends JView
{
	private $firsttype = 0;

	function overview($tpl = null)
	{
		JLoader::register('JEventsHTML',JPATH_SITE."/components/com_jevents/libraries/jeventshtml.php");

		global $mainframe, $option;
		JHTML::stylesheet( 'jevpeople.css', 'administrator/components/'.$option.'/assets/css/' );

		// Set toolbar items for the page
		JToolBarHelper::title(   JText::_( 'People Manager' ), 'generic.png' );
		JToolBarHelper::publishList("people.publish");
		JToolBarHelper::unpublishList("people.unpublish");
		JToolBarHelper::deleteList("Are you sure you want to delete these people?","people.delete");
		JToolBarHelper::editListX("people.edit");
		JToolBarHelper::addNewX("people.edit");
		if (JRequest::getString("tmpl","")=="component"){
			JToolBarHelper::custom("people.select","back","back","JEV BACK",false);
		}
		if ($mainframe->isAdmin() && JRequest::getString("tmpl","")!="component"){
			JToolBarHelper::cancel('cpanel.show', 'Control Panel' );
		}

		$this->showToolBar();

		$document =& JFactory::getDocument();
		$document->setTitle(JText::_('JEvents Extras') . ' :: ' .JText::_('People'));

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

		// Make sure the type filter is set to the first (if its blank)
		JRequest::setVar("type_id",$this->getFirstType());

		$model	=& $this->getModel();
		$items		= & $this->get( 'Data');
		$total		= & $this->get( 'Total');
		$pagination = & $this->get( 'Pagination' );

		// build list of categories
		$javascript 	= 'onchange="document.adminForm.submit();"';
		$compparams = JComponentHelper::getParams("com_jevpeople");

		$lists['typefilter'] = $this->typeFilter(false);

		$firsttype = $this->getFirstType();
		$typefilter	= intval( $mainframe->getUserStateFromRequest( "type_type_id", 'type_id', $firsttype));
		$section_name  = "com_jevpeople_type".$typefilter;

		$lists['catid'] = JEventsHTML::buildCategorySelect(intval( $filter_catid ),$javascript,"",true,false,0,'filter_catid',$section_name);
		$lists['catid'] = str_replace(JText::_('JEV_EVENT_ALLCAT'),JText::_("All Categories"),$lists['catid'] );

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

	function select($tpl = null)
	{

		JHTML::script('people.js', 'administrator/components/'.JEVEX_COM_COMPONENT.'/assets/js/');

		JLoader::register('JEventsHTML',JPATH_SITE."/components/com_jevents/libraries/jeventshtml.php");

		global $mainframe, $option;
		JHTML::stylesheet( 'jevpeople.css', 'administrator/components/'.$option.'/assets/css/' );

		// Set toolbar items for the page
		JToolBarHelper::title(   JText::_( 'Select Person' ), 'generic.png' );

		// Only offer management buttons if use is authorised
		if (JevPeopleHelper::canCreateOwn()){
			//JToolBarHelper::addNew("people.edit","Create Person");
			$this->toolbarButton("people.edit","new","new","Create Person",false);
			$this->toolbarButton("people.overview","config","config","Manage People",false);
		}

		$this->showToolBar();

		$document =& JFactory::getDocument();
		$document->setTitle(JText::_('JEvents Extras') . ' :: ' .JText::_('People'));

		$db		=& JFactory::getDBO();
		$uri	=& JFactory::getURI();

		$filter_perstype	= $mainframe->getUserStateFromRequest( $option.'pers_filter_perstype',	'filter_perstype',		0,				'int' );
		$filter_catid		= $mainframe->getUserStateFromRequest( $option.'pers_filter_catid',	'filter_catid',		0,				'int' );
		$filter_order		= $mainframe->getUserStateFromRequest( $option.'pers_filter_order',		'filter_order',		'pers.ordering',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'pers_filter_order_Dir',	'filter_order_Dir',	'',				'word' );
		$search				= $mainframe->getUserStateFromRequest( $option.'pers_search',			'search',			'',				'string' );
		$search				= JString::strtolower( $search );

		// Get data from the model
		$model	=& $this->getModel();
		$model->setState("select",true);
		$model->setState("perstype",$filter_perstype);

		$items		= & $this->get( 'Data');
		$total		= & $this->get( 'Total');
		$pagination = & $this->get( 'Pagination' );

		// build list of categories
		$compparams = JComponentHelper::getParams("com_jevpeople");

		$lists['typefilter'] = $this->typeFilter(false);

		$firsttype = $this->getFirstType();
		$typefilter	= intval( $mainframe->getUserStateFromRequest( "type_type_id", 'type_id', $firsttype));
		$section_name  = "com_jevpeople_type".$typefilter;

		$javascript 	= 'onchange="document.adminForm.submit();"';
		$lists['catid'] = JEventsHTML::buildCategorySelect(intval( $filter_catid ),$javascript,"",true,false,0,'filter_catid',$section_name);
		$lists['catid'] = str_replace(JText::_('JEV_EVENT_ALLCAT'),JText::_("All Categories"),$lists['catid'] );

		$options = array();
		$options[] = JHTML::_('select.option', 0 ,JText::_('Any Person'));
		$options[] = JHTML::_('select.option', 1 ,JText::_('My People'));
		$options[] = JHTML::_('select.option', 2 ,JText::_('Common People'));
		$lists["perstype"] = JHTML::_('select.genericlist', $options, 'filter_perstype', 'class="inputbox" size="1" onchange="form.submit();"', 'value', 'text', $filter_perstype);

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
		JLoader::register('jevpeopleCategory',JPATH_COMPONENT_ADMINISTRATOR."/libraries/categoryClass.php");

		global $mainframe, $option;
		JHTML::stylesheet( 'jevpeople.css', 'administrator/components/'.$option.'/assets/css/' );

		// Set toolbar items for the page
		$edit		= JRequest::getVar('edit',true);
		$text = !$edit ? JText::_( 'New' ) : JText::_( 'Edit' );
		JToolBarHelper::title(   JText::_( 'Person' ).': <small><small>[ ' . $text.' ]</small></small>' );
		$this->toolbarButton("people.save","save","save","Save",false);
		if (!$edit)  {
			$this->toolbarButton("people.cancel","cancel","cancel","Cancel",false);
		} else {
			// for existing items the button is renamed `close`
			$this->toolbarButton("people.cancel","cancel","cancel","Close",false);
		}

		$this->showToolBar();

		$compparams = JComponentHelper::getParams("com_jevpeople");
		$document =& JFactory::getDocument();
		$document->setTitle(JText::_('JEvents Extras') . ' :: ' .JText::_('Edit Person'));

		global $mainframe, $option;

		$db		=& JFactory::getDBO();
		$uri 	=& JFactory::getURI();
		$user 	=& JFactory::getUser();
		$model	=& $this->getModel();

		$lists = array();

		//get the person
		$person	=& $this->get('data');
		$isNew		= ($person->pers_id < 1);

		// get the type
		$typemodel = & $this->getModel("type", "PeopleTypesModel") ;
		$typeid = $person->type_id>0?$person->type_id:$this->getFirstType();
		$typemodel->setId($typeid);
		$perstype = $typemodel->getData();

		if ($perstype->showaddress>0) {

			$googlekey = $compparams->get("googlemapskey","");
			$googleurl = $compparams->get("googlemaps",'http://maps.google.com');
			if ($googlekey!=""){
				JHTML::script( '/maps?file=api&amp;v=2.x&amp;key='.$googlekey ,$googleurl , true);
			}

			if ($isNew || ($person->geolon==0 && $person->geolat==0)){
				$long = $compparams->get("long",30);
				$lat = $compparams->get("lat",30);
				$zoom = 10;
			}
			else {
				$long =$person->geolon;
				$lat = $person->geolat;
				$zoom = $person->geozoom;
			}
			$script=<<<SCRIPT
var globallong = $long;
var globallat = $lat;
var globalzoom = $zoom;
SCRIPT;
			$document->addScriptDeclaration($script);
}


JHTML::script('people.js', 'administrator/components/'.$option.'/assets/js/' );

// fail if checked out not by 'me'
if ($model->isCheckedOut( $user->get('id') )) {
	$msg = JText::sprintf( 'DESCBEINGEDITTED', JText::_( 'The Person' ), $person->title );
	$mainframe->redirect( 'index.php?option='. $option, $msg );
}

// Edit or Create?
if (!$isNew)
{
	$model->checkout( $user->get('id') );
}
else
{
	// initialise new record
	$person->published = 1;
	$person->approved 	= 1;
	$person->order 	= 0;
	$person->catid 	= JRequest::getVar( 'catid', 0, 'post', 'int' );
	if (JevPeopleHelper::canCreateGlobal() && $compparams->get("commondefault",0)){
		$person->global 	= 1;
	}

}

$lists['catid0'] = JEventsHTML::buildCategorySelect($person->catid0 ,"","",true,true,0,'catid0','com_jevpeople_type'.$typeid);
$lists['catid0'] = str_replace(JText::_('JEV_EVENT_CHOOSE_CATEG'),JText::_("Choose Category"),$lists['catid0'] );

$lists['catid1'] = JEventsHTML::buildCategorySelect($person->catid1 ,"","",true,true,0,'catid1','com_jevpeople_type'.$typeid);
$lists['catid1'] = str_replace(JText::_('JEV_EVENT_CHOOSE_CATEG'),JText::_("Choose Category"),$lists['catid1'] );

$lists['catid2'] = JEventsHTML::buildCategorySelect($person->catid2 ,"","",true,true,0,'catid2','com_jevpeople_type'.$typeid);
$lists['catid2'] = str_replace(JText::_('JEV_EVENT_CHOOSE_CATEG'),JText::_("Choose Category"),$lists['catid2'] );

$lists['catid3'] = JEventsHTML::buildCategorySelect($person->catid3 ,"","",true,true,0,'catid3','com_jevpeople_type'.$typeid);
$lists['catid3'] = str_replace(JText::_('JEV_EVENT_CHOOSE_CATEG'),JText::_("Choose Category"),$lists['catid3'] );

$lists['catid4'] = JEventsHTML::buildCategorySelect($person->catid4 ,"","",true,true,0,'catid4','com_jevpeople_type'.$typeid);
$lists['catid4'] = str_replace(JText::_('JEV_EVENT_CHOOSE_CATEG'),JText::_("Choose Category"),$lists['catid4'] );

// build the html select list
$lists['published'] 		= JHTML::_('select.booleanlist',  'published', 'class="inputbox"', $person->published );

// build the html select list
$lists['global'] 		= JHTML::_('select.booleanlist',  'global', 'class="inputbox"', $person->global );

// Person Type
if ($typemodel = JModel::getInstance("types", "PeopleTypesModel")) {
	$typedata	= $typemodel->getData();
}

$options = array();
$options[] = JHTML::_('select.option', 0 ,JText::_('Select Type'),'type_id', 'title');
$options = array_merge($options,$typedata);
$lists["type"] = JHTML::_('select.genericlist', $options, 'type_id', 'class="inputbox" size="1" onchange="alert(\'You must save the person and re-edit to show the correct category options\');" ', 'type_id', 'title', $person->type_id);

//clean person data
JFilterOutput::objectHTMLSafe( $person, ENT_QUOTES, 'description' );

$file 	= JPATH_COMPONENT.DS.'models'.DS.'person.xml';
$params = new JParameter( $person->params, $file );

$this->assignRef('lists',		$lists);
$this->assignRef('person',		$person);
$this->assignRef('params',		$params);
$this->assignRef('perstype',	$perstype);

parent::display($tpl);

	}

	function detail($tpl = null)
	{

		JLoader::register('JEventsHTML',JPATH_SITE."/components/com_jevents/libraries/jeventshtml.php");
		JLoader::register('jevpeopleCategory',JPATH_COMPONENT_ADMINISTRATOR."/libraries/categoryClass.php");

		global $mainframe, $option;
		JHTML::stylesheet( 'jevpeople.css', 'administrator/components/'.$option.'/assets/css/' );

		// Set toolbar items for the page

		$compparams = JComponentHelper::getParams("com_jevpeople");

		//get the person
		$db		=& JFactory::getDBO();
		$uri 	=& JFactory::getURI();
		$user 	=& JFactory::getUser();
		$model	=& $this->getModel();

		$person	=& $this->get('data');

		// get the type
		$typemodel = & $this->getModel("type", "PeopleTypesModel") ;
		$typeid = $person->type_id>0?$person->type_id:$this->getFirstType();
		$typemodel->setId($typeid);
		$perstype = $typemodel->getData();

		$document =& JFactory::getDocument();
		$document->setTitle(JText::_('Event Person'). " :: ". $person->title);

		$subtitle = str_replace(" ","+",$person->title);

		$lists = array();
		if ($perstype->showaddress>0) {

			$googlekey = $compparams->get("googlemapskey","");
			$googleurl = $compparams->get("googlemaps",'http://maps.google.com');
			if ($googlekey!=""){
				JHTML::script( '/maps?file=api&amp;v=2.x&amp;key='.$googlekey ,$googleurl , true);
			}

			$long =$person->geolon;
			$lat = $person->geolat;
			$zoom = $person->geozoom;

			$script=<<<SCRIPT
var globallong = $long;
var globallat = $lat;
var globalzoom = $zoom;
var globaltitle = "$subtitle";
var googleurl = "$googleurl";
SCRIPT;
			$document->addScriptDeclaration($script);
		}
		JHTML::script('persondetail.js', 'components/'.$option.'/assets/js/' );

		$dispatcher	=& JDispatcher::getInstance();
		JPluginHelper::importPlugin('content');
		$params = new JParameter(null);
		$tmprow = new stdClass();
		$tmprow->text = $person->description;
		$dispatcher->trigger( 'onPrepareContent', array( &$tmprow, &$params, 0 ));
		$person->description = $tmprow->text;

		$this->assignRef('person',		$person);
		$this->assignRef('perstype',	$perstype);

		parent::display($tpl);

	}

	function upload($tpl = null)
	{
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

	function showToolBar(){
		global $mainframe;
		if (JRequest::getVar("tmpl","")=="component" || !$mainframe->isAdmin()){
			?>
			<div id="toolbar-box" >
					<div class="t">
					<div class="t">
						<div class="t"></div>
					</div>
				</div>
				<div class="m">
				<?php
				$bar = & JToolBar::getInstance('toolbar');
				$barhtml = $bar->render();
				$barhtml = preg_replace('/onclick="(.*)" /','onclick="$1;return false;" ',$barhtml);
				echo $barhtml;
				global $mainframe;
				$title = $mainframe->get('JComponentTitle');
				echo $title;
				?>
				<div class="clr"></div>
				</div>
				<div class="b">
					<div class="b">
						<div class="b"></div>	
					</div>
				</div>
			</div>
		<?php		
		}
		// Kepri doesn't load icons etc. when using tmpl=component - but we want them!
		if (JRequest::getVar("tmpl","")=="component" && $mainframe->isAdmin()){
			JHTML::stylesheet( 'template.css', 'administrator/templates/'.$mainframe->getTemplate().'/css/' );

		}
	}


	function _globalHTML(&$row, $i){
		$img 	= $row->global ? 'tick.png':  'publish_x.png';
		$alt 	= $row->global ? JText::_( 'Global' ) : JText::_( 'User' );

		global $mainframe;
		if ($mainframe->isAdmin()){
			$img = '<img src="images/'. $img .'" border="0" alt="'. $alt .'" /></a>';
		}
		else {
			$img = '<img src="../administrator/images/'. $img .'" border="0" alt="'. $alt .'" /></a>';
		}

		if (JevPeopleHelper::canCreateGlobal()){
			$action = $row->global ? JText::_( 'Make Private' ) : JText::_( 'Make Global' );
			$task = $row->global ? "people.privatise":"people.globalise";

			$href = '
		<a href="javascript:void(0);" onclick="return listItemTask(\'cb'. $i .'\',\''. $task .'\')" title="'. $action .'">
		'.$img		;

			return $href;
		}
		return $img;
	}

	function typeFilter($asinput = false){
		$typefilter	= $this->getFirstType();

		$query = 'SELECT tp.type_id AS value, tp.title AS text FROM #__jev_peopletypes AS tp order by title';
		$db =& JFactory::getDBO();
		$db->setQuery($query);
		$options = $db->loadObjectList();

		if (!$asinput) 	return JText::_("People Type") . " : ". JHTML::_('select.genericlist', $options, 'type_id', 'class="inputbox" size="1" onchange="document.getElementById(\'filter_catid\').value=0;form.submit();"', 'value', 'text', $typefilter);
		else 	return JHTML::_('select.genericlist', $options, 'type_id', 'class="inputbox" size="1" ', 'value', 'text', $typefilter);
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