<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: typeController.php 1117 2008-07-06 17:20:59Z tstahl $
 * @package     JEvents
 * @copyright   Copyright (C) 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://joomlacode.org/gf/project/jevents
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class AdminTypesController extends JController {
	var $component = null;
	var $typeTable = null;
	var $typeClassname = null;

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
		$this->typeTable = "#__jev_peopletypes";
		$this->typeClassname = "JevPeopleType";

	}

	
	function overview( )
	{
		$user =& JFactory::getUser();
		if (strtolower($user->usertype)!="super administrator"){
			$this->setRedirect( "index.php?option=$this->component&task=cpanel.cpanel", "Not Authorised - must be super admin" );
			return;
		}

		// get the view
		$viewName = "types";
		$this->view = & $this->getView($viewName,"html");

		// Set the layout
		$this->view->setLayout('overview');
		$this->view->assign('title'   , JText::_("People Types List"));
		$jevuser = JEVHelper::getAuthorisedUser();
		$this->view->assign('jevuser',$jevuser);

		// Get/Create the model
		if ($model = & $this->getModel($viewName, "PeopleTypesModel")) {
			// Push the model into the view (as default)
			$this->view->setModel($model, true);
		}

		$this->view->overview();
	}

	function edit() {

		$user =& JFactory::getUser();
		if (strtolower($user->usertype)!="super administrator"){
			$this->setRedirect( "index.php?option=$this->component&task=cpanel.cpanel", "Not Authorised - must be super admin" );
			return;
		}
		
		// get the view
		$viewName = "types";
		$this->view = & $this->getView($viewName,"html");

		JRequest::setVar( 'hidemainmenu', 1 );

		$returntask	= JRequest::getVar( 'returntask', "types.overview");
		if ($returntask!="types.list" && $returntask!="types.overview" && $returntask!="types.select"){
			$returntask="types.overview";
		}
		$this->view->assign('returntask'   , $returntask);

		// Set the layout
		$this->view->setLayout('edit');
		$this->view->assign('title'   , JText::_("Person Edit"));
		$jevuser = JEVHelper::getAuthorisedUser();
		$this->view->assign('jevuser',$jevuser);

		// Get/Create the model
		if ($model = & $this->getModel("type", "PeopleTypesModel")) {
			// Push the model into the view (as default)
			$this->view->setModel($model, true);
		}

		$this->view->edit();
	}

	function cancel(){
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$user =& JFactory::getUser();
		if (strtolower($user->usertype)!="super administrator"){
			$this->setRedirect( "index.php?option=$this->component&task=cpanel.cpanel", "Not Authorised - must be super admin" );
			return;
		}

		$returntask	= JRequest::getVar( 'returntask', "types.overview");
		if ($returntask!="types.list" && $returntask!="types.overview" ){
			$returntask="types.overview";
		}
		$tmpl = "";

		if (method_exists($this,str_replace("types.","",$returntask))){
			$returntask = str_replace("types.","",$returntask);
			return $this->$returntask();
		}
		if (JRequest::getString("tmpl","")=="component"){
			$tmpl ="&tmpl=component";
		}
		$link = JRoute::_('index.php?option=com_jevpeople&task='.$returntask . $tmpl);
		$this->setRedirect($link);
	}

	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$user =& JFactory::getUser();
		if (strtolower($user->usertype)!="super administrator"){
			$this->setRedirect( "index.php?option=$this->component&task=cpanel.cpanel", "Not Authorised - must be super admin" );
			return;
		}
		
		$post	= JRequest::get('post');
		$cid	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$post['type_id'] = (int) $cid[0];

		$model = & $this->getModel("type", "PeopleTypesModel");

		if ($model->store($post)) {
			$msg = JText::_( 'Person Type Saved' );
		} else {
			$msg = JText::_( 'Error Saving Person Type')." - ". $model->getError() ;
		}

		$returntask	= JRequest::getVar( 'returntask', "types.overview");
		if ($returntask!="types.list" && $returntask!="types.overview" ){
			$returntask="types.overview";
		}
		if (method_exists($this,str_replace("types.","",$returntask))){
			$returntask = str_replace("types.","",$returntask);
			return $this->$returntask();
		}

		$tmpl = "";
		if (JRequest::getString("tmpl","")=="component"){
			$tmpl ="&tmpl=component";
		}

		$link = JRoute::_('index.php?option=com_jevpeople&task='.$returntask. $tmpl	);
		$this->setRedirect($link, $msg);
	}


	function delete()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$user =& JFactory::getUser();
		if (strtolower($user->usertype)!="super administrator"){
			$this->setRedirect( "index.php?option=$this->component&task=cpanel.cpanel", "Not Authorised - must be super admin" );
			return;
		}
		
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		if (count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item to delete' ) );
		}

		$model = & $this->getModel("type", "PeopleTypesModel");
		if(!$model->delete($cid)) {
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}

		$returntask	= JRequest::getVar( 'returntask', "types.overview");
		if ($returntask!="types.list" && $returntask!="types.overview" && $returntask!="types.select"){
			$returntask="types.overview";
		}
		if (method_exists($this,str_replace("types.","",$returntask))){
			$returntask = str_replace("types.","",$returntask);
			return $this->$returntask();
		}

		$tmpl = "";
		if (JRequest::getString("tmpl","")=="component"){
			$tmpl ="&tmpl=component";
		}

		$this->setRedirect( JRoute::_('index.php?option=com_jevpeople&task=types.list' . $tmpl));
	}
	
	

}
