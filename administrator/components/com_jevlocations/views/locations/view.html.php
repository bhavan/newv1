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
class AdminLocationsViewLocations extends JView
{

	function overview($tpl = null)
	{
		JLoader::register('JEventsHTML',JPATH_SITE."/components/com_jevents/libraries/jeventshtml.php");

		global $mainframe, $option;
		JHTML::stylesheet( 'jevlocations.css', 'administrator/components/'.$option.'/assets/css/' );

		// Set toolbar items for the page
		JToolBarHelper::title(   JText::_( 'Locations Manager' ), 'generic.png' );
		JToolBarHelper::publishList("locations.publish");
		JToolBarHelper::unpublishList("locations.unpublish");
		JToolBarHelper::deleteList("Are you sure you want to delete these locations?","locations.delete");
		JToolBarHelper::editListX("locations.edit");
		JToolBarHelper::addNewX("locations.edit");
		if (JRequest::getString("tmpl","")=="component"){
			JToolBarHelper::custom("locations.select","back","back","JEV BACK",false);
		}
		//JToolBarHelper::help( 'screen.locations' );
		if ($mainframe->isAdmin() && JRequest::getString("tmpl","")!="component"){
			JToolBarHelper::cancel('cpanel.show', 'Control Panel' );
		}

		$this->showToolBar();

		$document =& JFactory::getDocument();
		$document->setTitle(JText::_('JEvents JEvents Location Manager') . ' :: ' .JText::_('Locations'));

		$db		=& JFactory::getDBO();
		$uri	=& JFactory::getURI();

		$filter_state		= $mainframe->getUserStateFromRequest( $option.'loc_filter_state',		'filter_state',		'',				'word' );
		$filter_catid		= $mainframe->getUserStateFromRequest( $option.'loc_filter_catid',	'filter_catid',		0,				'int' );
		$filter_loccat		= $mainframe->getUserStateFromRequest( $option.'loc_filter_loccat',	'filter_loccat',		0,				'int' );
		$filter_priority	= $mainframe->getUserStateFromRequest( $option.'loc_filter_priority',	'filter_priority',		0,				'int' );

		$filter_order		= $mainframe->getUserStateFromRequest( $option.'loc_filter_order',		'filter_order',		'loc.ordering',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'loc_filter_order_Dir',	'filter_order_Dir',	'',				'word' );
		$search				= $mainframe->getUserStateFromRequest( $option.'loc_search',			'search',			'',				'string' );
		$search				= JString::strtolower( $search );

		// Get data from the model
		$params =& JComponentHelper::getParams('com_jevents');
		$authorisedonly = $params->get("authorisedonly",0);
		$juser =& JFactory::getUser();
		if ($authorisedonly){
			JRequest::setVar("showglobal",$this->jevuser && $this->jevuser->cancreateglobal);
		}
		else {
			$compparams =& JComponentHelper::getParams('com_jevlocations');
			$loc_global = $compparams->get("loc_global",24);
			if ($juser->gid>=intval($loc_global)){
				JRequest::setVar("showglobal",1);
			}
			else {
				JRequest::setVar("showglobal",0);
			}
		}
		if ($juser->usertype=="Super Administrator" ||  $juser->usertype=="Administrator" ){
			JRequest::setVar("showall",1);
		}
		$model	=& $this->getModel();
		$items		= & $this->get( 'Data');
		$total		= & $this->get( 'Total');
		$pagination = & $this->get( 'Pagination' );

		// New custom fields
		JLoader::register('JevCfParameter', JPATH_SITE . "/plugins/jevents/customfields/jevcfparameter.php");
		$compparams = JComponentHelper::getParams("com_jevlocations");
		$template = $compparams->get("template", "");
		if ($template != "" && $compparams->get("custinlist"))
		{
			$xmlfile = JPATH_SITE . "/plugins/jevents/customfields/templates/" . $template;
			if (file_exists($xmlfile))
			{
				$db = JFactory::getDBO();
				foreach ($items as &$item)
				{
					$db->setQuery("SELECT * FROM #__jev_customfields3 WHERE target_id=" . intval($item->loc_id) . " AND targettype='com_jevlocations'");
					$customdata = $db->loadObjectList();

					$jcfparams = new JevCfParameter($customdata, $xmlfile, $item);
					$customfields = $jcfparams->renderToBasicArray();
					$item->customfields = $customfields;
					unset($item);
				}
			}
		}


		// build list of categories
		$javascript 	= 'onchange="document.adminForm.submit();"';
		$compparams = JComponentHelper::getParams("com_jevlocations");
		$usecats = $compparams->get("usecats",0);
		if ($usecats){
			$lists['catid'] = JEventsHTML::buildCategorySelect(intval( $filter_catid ),$javascript,"",false,false,0,'filter_catid','com_jevlocations');
			$lists['catid'] = str_replace(JText::_('JEV_EVENT_ALLCAT'),JText::_("All Cities"),$lists['catid'] );
		}
		// normal category filter
		$lists['loccat'] = JEventsHTML::buildCategorySelect(intval( $filter_loccat),$javascript,"",false,false,0,'filter_loccat','com_jevlocations2');
		// state filter
		$lists['state']	= JHTML::_('grid.state',  $filter_state );

		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;

		$priorities = "";
		// only those who can publish globally can set priority field
		if (JEVHelper::isEventPublisher(true)){
			$list = array();
			for ($i=0;$i<10;$i++)	{
				$list[] = JHTML::_('select.option', $i, $i, 'val', 'text' );
			}
			$priorities = JHTML::_('select.genericlist', $list, 'filter_loccat', "", 'val', 'text', intval( $filter_priority ) );
			$this->assign('setPriority',true);
			$lists["priority"]=$priorities;
		}
		else {
			$this->assign('setPriority',false);
			$lists["priority"]=$priorities;
		}

		// search filter
		$lists['search']= $search;

		$this->assignRef('user',		JFactory::getUser());
		$this->assignRef('usecats',		$usecats);
		$this->assignRef('lists',		$lists);
		$this->assignRef('items',		$items);
		$this->assignRef('pagination',	$pagination);

		parent::display($tpl);

	}

	function select($tpl = null)
	{

		JHTML::script('locations.js', 'administrator/components/'.JEVEX_COM_COMPONENT.'/assets/js/');

		JLoader::register('JEventsHTML',JPATH_SITE."/components/com_jevents/libraries/jeventshtml.php");

		global $mainframe, $option;
		JHTML::stylesheet( 'jevlocations.css', 'administrator/components/'.$option.'/assets/css/' );

		// Set toolbar items for the page
		JToolBarHelper::title(   JText::_( 'Select Location' ), 'generic.png' );

		// Only offer management buttons if use is authorised
		if (JevLocationsHelper::canCreateOwn() || JevLocationsHelper::canCreateGlobal()){
			//JToolBarHelper::addNew("locations.edit","Create Location");
			$this->toolbarButton("locations.edit","new","new","Create Location",false);
		}
		if (JevLocationsHelper::canCreateOwn()){
			$this->toolbarButton("locations.overview","config","config","Manage Locations",false);
		}

		$this->showToolBar();

		$document =& JFactory::getDocument();
		$document->setTitle(JText::_('JEvents JEvents Location Manager') . ' :: ' .JText::_('Locations'));

		$db		=& JFactory::getDBO();
		$uri	=& JFactory::getURI();

		$filter_loctype		= $mainframe->getUserStateFromRequest( $option.'loc_filter_loctype',	'filter_loctype',		0,				'int' );
		$filter_catid		= $mainframe->getUserStateFromRequest( $option.'loc_filter_catid',	'filter_catid',		0,				'int' );
		$filter_loccat		= $mainframe->getUserStateFromRequest( $option.'loc_filter_loccat',	'filter_loccat',		0,				'int' );
		$filter_priority	= $mainframe->getUserStateFromRequest( $option.'loc_filter_priority',	'filter_priority',		0,				'int' );

		$filter_order		= $mainframe->getUserStateFromRequest( $option.'loc_filter_order',		'filter_order',		'loc.ordering',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'loc_filter_order_Dir',	'filter_order_Dir',	'',				'word' );
		$search				= $mainframe->getUserStateFromRequest( $option.'loc_search',			'search',			'',				'string' );
		$search				= JString::strtolower( $search );

		// Should we be allowed to select from all locations?
		$compparams = JComponentHelper::getParams("com_jevlocations");
		JRequest::setVar("showall",$compparams->get("selectfromall",0));
		
		// Get data from the model
		$model	=& $this->getModel();
		$model->setState("select",true);
		$model->setState("loctype",$filter_loctype);

		$items		= & $this->get( 'Data');
		$total		= & $this->get( 'Total');
		$pagination = & $this->get( 'Pagination' );

		// build list of categories
		$usecats = $compparams->get("usecats",0);
		$javascript 	= 'onchange="document.adminForm.submit();"';
		if ($usecats) {
			$lists['catid'] = JEventsHTML::buildCategorySelect(intval( $filter_catid ),$javascript,"",false,false,0,'filter_catid','com_jevlocations');
			$lists['catid'] = str_replace(JText::_('JEV_EVENT_ALLCAT'),JText::_("All Cities"),$lists['catid'] );
		}
		// normal category filter
		$lists['loccat'] = JEventsHTML::buildCategorySelect(intval( $filter_loccat ),$javascript,"",false,false,0,'filter_loccat','com_jevlocations2');

		$options = array();
		$options[] = JHTML::_('select.option', 0 ,JText::_('Any Location'));
		$options[] = JHTML::_('select.option', 1 ,JText::_('My Locations'));
		$options[] = JHTML::_('select.option', 2 ,JText::_('Common Locations'));
		$lists["loctype"] = JHTML::_('select.genericlist', $options, 'filter_loctype', 'class="inputbox" size="1" onchange="form.submit();"', 'value', 'text', $filter_loctype);

		// only those who can publish globally can set priority field
		if (JEVHelper::isEventPublisher(true)){
			$list = array();
			for ($i=0;$i<10;$i++)	{
				$list[] = JHTML::_('select.option', $i, $i, 'val', 'text' );
			}
			$priorities = JHTML::_('select.genericlist', $list, 'filter_loccat', "", 'val', 'text', intval( $filter_priority ) );
			$this->assign('setPriority',true);
			$lists["priority"]=$priorities;
		}
		else {
			$this->assign('setPriority',false);
			$lists["priority"]="";
		}

		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;

		// search filter
		$lists['search']= $search;

		$this->assignRef('user',		JFactory::getUser());
		$this->assignRef('usecats',		$usecats);
		$this->assignRef('lists',		$lists);
		$this->assignRef('items',		$items);
		$this->assignRef('pagination',	$pagination);

		parent::display($tpl);

	}

	function edit($tpl = null)
	{

		JLoader::register('JEventsHTML',JPATH_SITE."/components/com_jevents/libraries/jeventshtml.php");
		JLoader::register('jevlocationsCategory',JPATH_COMPONENT_ADMINISTRATOR."/libraries/categoryClass.php");

		global $mainframe, $option;
		JHTML::stylesheet( 'jevlocations.css', 'administrator/components/'.$option.'/assets/css/' );

		// Set toolbar items for the page
		$edit		= JRequest::getVar('edit',true);
		$text = !$edit ? JText::_( 'New' ) : JText::_( 'Edit' );
		JToolBarHelper::title(   JText::_( 'Location' ).': <small><small>[ ' . $text.' ]</small></small>' );
		$this->toolbarButton("locations.save","save","save","Save",false);
		if (!$edit)  {
			$this->toolbarButton("locations.cancel","cancel","cancel","Cancel",false);
		} else {
			// for existing items the button is renamed `close`
			$this->toolbarButton("locations.cancel","cancel","cancel","Close",false);
		}
		//JToolBarHelper::help( 'screen.locations.edit' );

		if ($mainframe->isAdmin()){
			JToolBarHelper::cancel('cpanel.show', 'Control Panel' );
		}

		$this->showToolBar();
		
		$compparams = JComponentHelper::getParams("com_jevlocations");
		$googlekey = JevLocationsHelper::getApiKey();//$compparams->get("googlemapskey","");
		$googleurl = JevLocationsHelper::getApiUrl(); //$compparams->get("googlemaps",'http://maps.google.com');
		if ($googlekey!=""){
			JHTML::script( '/maps?file=api&amp;v=2.x&amp;key='.$googlekey ,$googleurl , true);
		}

		$document =& JFactory::getDocument();
		$document->setTitle(JText::_('JEvents JEvents Location Manager') . ' :: ' .JText::_('Edit Location'));

		global $mainframe, $option;

		$db		=& JFactory::getDBO();
		$uri 	=& JFactory::getURI();
		$user 	=& JFactory::getUser();
		$model	=& $this->getModel();

		$lists = array();

		//get the location
		$location	=& $this->get('data');
		$isNew		= ($location->loc_id < 1);

		if ($isNew || ($location->geolon==0 && $location->geolat==0)){
			$long = $compparams->get("long",30);
			$lat = $compparams->get("lat",30);
			$zoom = 10;
		}
		else {
			$long =$location->geolon;
			$lat = $location->geolat;
			$zoom = $location->geozoom;
		}
		$script=<<<SCRIPT
var globallong = $long;
var globallat = $lat;
var globalzoom = $zoom;
SCRIPT;
		$document->addScriptDeclaration($script);

		JHTML::script('locations.js', 'administrator/components/'.$option.'/assets/js/' );

		// fail if checked out not by 'me'
		if ($model->isCheckedOut( $user->get('id') )) {
			$msg = JText::sprintf( 'DESCBEINGEDITTED', JText::_( 'The Location' ), $location->title );
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
			$location->published = 1;
			$location->approved 	= 1;
			$location->order 	= 0;
			$location->catid 	= JRequest::getVar( 'catid', 0, 'post', 'int' );
			$location->loccat 	= JRequest::getVar( 'loccat', 0, 'post', 'int' );
			if (JevLocationsHelper::canCreateGlobal() && $compparams->get("commondefault",0)){
				$location->global 	= 1;
			}
		}

		// build the html select list for ordering
		$query = 'SELECT ordering AS value, title AS text'
		. ' FROM #__jev_locations'
		. ' WHERE catid = ' . (int) $location->catid
		. ' ORDER BY title';

		$lists['ordering'] 			= JHTML::_('list.specificordering',  $location, $location->loc_id, $query );

		$usecats = $compparams->get("usecats",0);
		if ($usecats){
			$lists['catid'] = JEventsHTML::buildCategorySelect($location->catid ,"","",false,true,0,'catid','com_jevlocations');
			$lists['catid'] = str_replace(JText::_('JEV_EVENT_CHOOSE_CATEG'),JText::_("Choose City"),$lists['catid'] );
		}

		// normal category filter
		$lists['loccat'] = JEventsHTML::buildCategorySelect($location->loccat,"","",false,true,0,'loccat','com_jevlocations2');

		// build the html select list
		$lists['published'] 		= JHTML::_('select.booleanlist',  'published', 'class="inputbox"', $location->published );

		// build the html select list
		$lists['global'] 		= JHTML::_('select.booleanlist',  'global', 'class="inputbox"', $location->global );

		// only those who can publish globally can set priority field
		if (JEVHelper::isEventPublisher(true)){
			$list = array();
			for ($i=0;$i<10;$i++)	{
				$list[] = JHTML::_('select.option', $i, $i, 'val', 'text' );
			}
			$priorities = JHTML::_('select.genericlist', $list, 'priority', "", 'val', 'text', $location->priority );
			$this->assign('setPriority',true);
			$this->assign('priority',$priorities);
		}
		else {
			$this->assign('setPriority',false);
		}

		$this->setCreatorLookup($location);

		//clean location data
		JFilterOutput::objectHTMLSafe( $location, ENT_QUOTES, 'description' );

		$file 	= JPATH_COMPONENT.DS.'models'.DS.'location.xml';
		$params = new JParameter( $location->params, $file );

		$this->assignRef('usecats',		$usecats);
		$this->assignRef('lists',		$lists);
		$this->assignRef('location',		$location);
		$this->assignRef('params',		$params);

		parent::display($tpl);

	}

	function detail($tpl = null)
	{

		JLoader::register('JEventsHTML',JPATH_SITE."/components/com_jevents/libraries/jeventshtml.php");
		JLoader::register('jevlocationsCategory',JPATH_COMPONENT_ADMINISTRATOR."/libraries/categoryClass.php");

		global $mainframe, $option;

		// Set toolbar items for the page

		$compparams = JComponentHelper::getParams("com_jevlocations");
		$googlekey = $compparams->get("googlemapskey","");
		$googleurl = $compparams->get("googlemaps",'http://maps.google.com');
		if ($googlekey!=""){
			JHTML::script( '/maps?file=api&amp;v=2.x&amp;key='.$googlekey ,$googleurl , true);
		}

		//get the location
		$db		=& JFactory::getDBO();
		$uri 	=& JFactory::getURI();
		$user 	=& JFactory::getUser();
		$model	=& $this->getModel();

		$location	=& $this->get('data');

		// This check is not relevant any longer - since we can view detail in the frontend
		/*
		// Check authorised to view
		if (!$location->global){
		if ($location->created_by!=$user->id){
		global $mainframe;
		$mainframe->redirect( 'index.php', JText::_("Not authorised") );
		}
		}
		*/
		if (!$location->published){
			global $mainframe;
			$mainframe->redirect( 'index.php', JText::_("Not authorised") );
		}

		$document =& JFactory::getDocument();
		$document->setTitle(JText::_('Event Location'). " :: ". $location->title);

		$subtitle = addslashes(str_replace(" ","+",urlencode($location->title)));

		$lists = array();

		$long =$location->geolon;
		$lat = $location->geolat;
		$zoom = $location->geozoom;

		$script=<<<SCRIPT
var globallong = $long;
var globallat = $lat;
var globalzoom = $zoom;
var globaltitle = "$subtitle";
var googleurl = "$googleurl";
SCRIPT;
		$document->addScriptDeclaration($script);

		if ($compparams->get("redirecttodirections")){
			global $mainframe;
			$plugin = JPluginHelper::getPlugin("jevents","jevlocations");
			if ($plugin){
				$pluginparams = new JParameter($plugin->params);
				$maptype = $pluginparams->get("maptype","G_NORMAL_MAP")=="G_NORMAL_MAP"?"m":"h";
			}
			else $maptype="h";
			$mainframe->redirect($googleurl."/maps?f=q&geocode=&time=&date=&ttype=&ie=UTF8&t=$maptype&om=1&q=".$subtitle."@".$lat.",".$long."&ll=".$lat.",".$long."&z=".$zoom."&iwloc=addr");
		}

		JHTML::script('locationdetail.js', 'components/'.$option.'/assets/js/' );

		$this->assignRef('location',		$location);

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


	function _globalHTML(&$row, $i){
		$img 	= $row->global ? 'tick.png':  'publish_x.png';
		$alt 	= $row->global ? JText::_( 'Global' ) : JText::_( 'User' );

		global $mainframe;
		if ($mainframe->isAdmin()){
			$img = '<img src="images/'. $img .'" border="0" alt="'. $alt .'" /></a>';
		}
		else {
			$img = '<img src="'.JURI::root().'administrator/images/'. $img .'" border="0" alt="'. $alt .'" /></a>';
		}

		if (JevLocationsHelper::canCreateGlobal()){
			$action = $row->global ? JText::_( 'Make Private' ) : JText::_( 'Make Global' );
			$task = $row->global ? "locations.privatise":"locations.globalise";

			$href = '
		<a href="javascript:void(0);" onclick="return listItemTask(\'cb'. $i .'\',\''. $task .'\')" title="'. $action .'">
		'.$img		;

			return $href;
		}
		return $img;
	}


	function showToolBar(){
		global $mainframe;
		if (JRequest::getVar("tmpl","")=="component" || !$mainframe->isAdmin()){
			?>
			<div class='jevlocations'>
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
				//$barhtml = str_replace('href="#"','href="javascript void();"',$barhtml);
				//$barhtml = str_replace('submitbutton','return submitbutton',$barhtml);
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
			</div>
		<?php		
		}
		// Kepri doesn't load icons etc. when using tmpl=component - but we want them!
		if (JRequest::getVar("tmpl","")=="component" && $mainframe->isAdmin()){
			JHTML::stylesheet( 'template.css', 'administrator/templates/'.$mainframe->getTemplate().'/css/' );

		}
	}

	protected function setCreatorLookup($row){
		// If user has backend access then allow them to specify the creator
		$user = JFactory::getUser();

		// Get an ACL object
		$acl =& JFactory::getACL();
		$grp = $acl->getAroGroup($user->get('id'));
		// if no valid group (e.g. anon user) then skip this.
		if (!$grp) return;

		$access = $acl->is_group_child_of($grp->name, 'Public Backend');

		if ( $access){
			$params =& JComponentHelper::getParams( JEVEX_COM_COMPONENT );
			$jevparams =& JComponentHelper::getParams( JEV_COM_COMPONENT );
			$authusers = $jevparams->getValue("authorisedonly",0);
			if ($authusers){
				$sql = "SELECT u.* FROM #__users as u LEFT JOIN #__jev_users as ju on ju.user_id=u.id where ju.cancreateown=1 OR ju.cancreateglobal=1 ORDER BY u.name ASC";
				$db = JFactory::getDBO();
				$db->setQuery( $sql );
				$users = $db->loadObjectList();
			}
			else {
				$minaccess = $params->getValue("loc_own",20);
				$sql = "SELECT * FROM #__users where gid>=".$minaccess." ORDER BY name ASC";
				$db = JFactory::getDBO();
				$db->setQuery( $sql );
				$users = $db->loadObjectList();
			}

			$userOptions[] = JHTML::_('select.option', '-1','Select User' );
			foreach( $users as $user )
			{
				$userOptions[] = JHTML::_('select.option', $user->id, $user->name );
			}
			$creator = $row->created_by>0?$row->created_by:$user->id;
			$userlist = JHTML::_('select.genericlist', $userOptions, 'created_by', 'class="inputbox" size="1" ', 'value', 'text', $creator);

			$this->assignRef("users",$userlist);
		}

	}

}