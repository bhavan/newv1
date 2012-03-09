<?php
/**
 * copyright (C) 2008 GWE Systems Ltd - All rights reserved
 */

defined( 'JPATH_BASE' ) or die( 'Direct Access to this location is not allowed.' );

jimport('joomla.application.component.controller');

class AdminLocationsController extends JController {

	function __construct($config = array())
	{
		parent::__construct($config);
		$this->registerTask( 'list',  'overview' );
		$this->registerDefaultTask("overview");

	}

	function overview( )
	{
		$this->_authoriseAccess();
			
		// get the view
		$viewName = "locations";
		$this->view = & $this->getView($viewName,"html");

		// Set the layout
		$this->view->setLayout('list');
		$this->view->assign('title'   , JText::_("Locations List"));
		$jevuser = JEVHelper::getAuthorisedUser();
		$this->view->assign('jevuser',$jevuser);

		// Get/Create the model
		if ($model = & $this->getModel($viewName, "LocationsModel")) {
			// Push the model into the view (as default)
			$this->view->setModel($model, true);
		}

		$this->view->overview();
	}

	function select( )
	{
		//$this->_authoriseAccess();

		// get the view
		$viewName = "locations";
		$this->view = & $this->getView($viewName,"html");

		// Set the layout
		$this->view->setLayout('select');
		$this->view->assign('title'   , JText::_("Locations List"));

		$this->fixCreationPermissions();
		$jevuser = JEVHelper::getAuthorisedUser();
		$this->view->assign('jevuser',$jevuser);

		JRequest::setVar('filter_state','P','post');
		// Get/Create the model
		if ($model = & $this->getModel($viewName, "LocationsModel")) {
			// Push the model into the view (as default)
			$this->view->setModel($model, true);
		}

		$this->view->select();
	}

	function edit() {

		$cid	= JRequest::getVar( 'cid', array(0), 'request', 'array' );
		$this->_authoriseAccess($cid);
		
		// get the view
		$viewName = "locations";
		$this->view = & $this->getView($viewName,"html");

		JRequest::setVar( 'hidemainmenu', 1 );

		$returntask	= JRequest::getVar( 'returntask', "locations.overview");
		if ($returntask!="locations.list" && $returntask!="locations.overview" && $returntask!="locations.select"){
			$returntask="locations.overview";
		}
		$this->view->assign('returntask'   , $returntask);

		// Set the layout
		$this->view->setLayout('edit');
		$this->view->assign('title'   , JText::_("Location Edit"));
		$jevuser = JEVHelper::getAuthorisedUser();
		$this->view->assign('jevuser',$jevuser);

		// Get/Create the model
		if ($model = & $this->getModel("location", "LocationsModel")) {
			// Push the model into the view (as default)
			$this->view->setModel($model, true);
		}

		// Get the media component configuration settings
		$params =& JComponentHelper::getParams('com_media');
		// Set the path definitions
		define('JEVP_MEDIA_BASE',    JPATH_ROOT.DS.$params->get('image_path', 'images'.DS.'stories'));
		define('JEVP_MEDIA_BASEURL', JURI::root(true).'/'.$params->get('image_path', 'images/stories'));		

		$this->view->edit();
	}

	function cancel(){
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$cid	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$this->_authoriseAccess((int) $cid[0]);

		$model = & $this->getModel("location", "LocationsModel");

		$model->getData();

		// Check the table in so it can be edited.... we are done with it anyway
		$model->checkin();

		if (JRequest::getString("tmpl","")=="component" && JRequest::getInt("pop",0)==1) {
			ob_end_clean();
				?>
				<script type="text/javascript">				
				window.parent.SqueezeBox.close();
				</script>
				<?php
				exit();
		}
		
		$user = JFactory::getUser();
		if ($user->id ==0) {
			$returntask	= JRequest::getVar( 'returntask', "locations.locations");
			if ($returntask!="locations.select"){
				JRequest::setVar("returntask","locations.locations");
			}

		}
		
		$returntask	= JRequest::getVar( 'returntask', "locations.overview");
		if ($returntask!="locations.list" && $returntask!="locations.overview" && $returntask!="locations.select"  && $returntask!="locations.locations") {
			$returntask="locations.overview";
		}
		$tmpl = "";
		
		if ($user->id !=0  && method_exists($this,str_replace("locations.","",$returntask))){
			$returntask = str_replace("locations.","",$returntask);
			return $this->$returntask();
		}
		if ($returntask=="locations.select" || JRequest::getString("tmpl","")=="component"){
			$tmpl ="&tmpl=component";
		}
		$link = JRoute::_('index.php?option=com_jevlocations&task='.$returntask . $tmpl);
		$this->setRedirect($link);
	}

	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$post	= JRequest::get('post',JREQUEST_ALLOWHTML);
		$cid	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$post['loc_id'] = (int) $cid[0];
		$this->_authoriseAccess($post['loc_id']);

		// ensure no dodgy setting of global values !
		$jevuser = JEVHelper::getAuthorisedUser();

		if (!JevLocationsHelper::canCreateGlobal($post)){
			$post["global"]=0;
		}
		else if (!isset($post["global"])){
			// anon users only - set global true and published false
			$user = JFactory::getUser();
			if ($user->id ==0) {
				$post["global"]=1;
				$params =& JComponentHelper::getParams("com_jevlocations");
				$post["published"]=$params->get("anonpublished",0);
				$returntask	= JRequest::getVar( 'returntask', "locations.locations");
				if ($returntask!="locations.select"){
					JRequest::setVar("returntask","locations.locations");
				}
			}
		}

		$model = & $this->getModel("location", "LocationsModel");

		if ($model->store($post)) {
			$msg = JText::_( 'Location Saved' , true);
			$user = JFactory::getUser();
			$params =& JComponentHelper::getParams("com_jevlocations");
			if ($user->id ==0 && !$params->get("anonpublished",0)) {
				$msg = JText::_( 'JEV_LOCSAVED_REVIEWED' );
			}
			if ($user->id ==0) {
				$location = $model->lastrow;
				$this->notifyAdmin($location);
			}

		} else {
			$msg = JText::_( 'Error Saving Location', true)." - ". $model->getError() ;
		}

		// Check the table in so it can be edited.... we are done with it anyway
		$model->checkin();

		if (JRequest::getString("tmpl","")=="component" && JRequest::getInt("pop",0)==1) {
			ob_end_clean();
				?>
				<script type="text/javascript">				
				window.parent.SqueezeBox.close();
				window.parent.alert("<?php echo $msg;?>");
				</script>
				<?php
				exit();
		}
		
		$returntask	= JRequest::getVar( 'returntask', "locations.overview");
		if ($returntask!="locations.list" && $returntask!="locations.locations" && $returntask!="locations.overview" && $returntask!="locations.select"){
			$returntask="locations.overview";
		}
		if ($user->id !=0  && method_exists($this,str_replace("locations.","",$returntask))){
			$returntask = str_replace("locations.","",$returntask);
			return $this->$returntask();
		}

		$tmpl = "";
		if ($returntask=="locations.select" || JRequest::getString("tmpl","")=="component"){
			$tmpl ="&tmpl=component";
		}

		// anon users only
		$user = JFactory::getUser();
		global $Itemid;
		if ($user->id ==0) {
			$link = JRoute::_('index.php?option=com_jevlocations&task='.$returntask. $tmpl."&Itemid=".$Itemid	);
		}
		else {
			$link = JRoute::_('index.php?option=com_jevlocations&task='.$returntask. $tmpl."&Itemid=".$Itemid	);
		}
		$this->setRedirect($link, $msg);

	}


	function delete()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		$this->_authoriseAccess($cid);

		if (count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item to delete' ) );
		}

		$model = & $this->getModel("location", "LocationsModel");
		if(!$model->delete($cid)) {
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}

		$returntask	= JRequest::getVar( 'returntask', "locations.overview");
		if ($returntask!="locations.list" && $returntask!="locations.overview" && $returntask!="locations.select"){
			$returntask="locations.overview";
		}
		if (method_exists($this,str_replace("locations.","",$returntask))){
			$returntask = str_replace("locations.","",$returntask);
			return $this->$returntask();
		}

		$tmpl = "";
		if (JRequest::getString("tmpl","")=="component"){
			$tmpl ="&tmpl=component";
		}

		$this->setRedirect( JRoute::_('index.php?option=com_jevlocations&task=locations.list' . $tmpl));
	}


	function publish()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		$this->_authoriseAccess($cid);

		if (count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item to publish' ) );
		}

		$model = & $this->getModel("location", "LocationsModel");
		if(!$model->publish($cid, 1)) {
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}

		$returntask	= JRequest::getVar( 'returntask', "locations.overview");
		if ($returntask!="locations.list" && $returntask!="locations.overview" && $returntask!="locations.select"){
			$returntask="locations.overview";
		}
		if (method_exists($this,str_replace("locations.","",$returntask))){
			$returntask = str_replace("locations.","",$returntask);
			return $this->$returntask();
		}

		$tmpl = "";
		if ( JRequest::getString("tmpl","")=="component"){
			$tmpl ="&tmpl=component";
		}

		$this->setRedirect(JRoute::_( 'index.php?option=com_jevlocations&task=locations.list' . $tmpl));
	}


	function unpublish()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		$this->_authoriseAccess($cid);

		if (count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item to unpublish' ) );
		}

		$model = & $this->getModel("location", "LocationsModel");
		if(!$model->publish($cid, 0)) {
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}

		$returntask	= JRequest::getVar( 'returntask', "locations.overview");
		if ($returntask!="locations.list" && $returntask!="locations.overview" && $returntask!="locations.select"){
			$returntask="locations.overview";
		}
		if (method_exists($this,str_replace("locations.","",$returntask))){
			$returntask = str_replace("locations.","",$returntask);
			return $this->$returntask();
		}

		$tmpl = "";
		if (JRequest::getString("tmpl","")=="component"){
			$tmpl ="&tmpl=component";
		}

		$this->list();
		//$this->setRedirect( JRoute::_('index.php?option=com_jevlocations&task=locations.list'.$tmpl) );
	}


	function globalise()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		if (JevLocationsHelper::canCreateGlobal()){

			$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
			JArrayHelper::toInteger($cid);

			$this->_authoriseAccess($cid);

			if (count( $cid ) < 1) {
				JError::raiseError(500, JText::_( 'Select an item to publish' ) );
			}

			$model = & $this->getModel("location", "LocationsModel");
			if(!$model->globalise($cid, 1)) {
				echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
			}

			$returntask	= JRequest::getVar( 'returntask', "locations.overview");
			if ($returntask!="locations.list" && $returntask!="locations.overview" && $returntask!="locations.select"){
				$returntask="locations.overview";
			}
			if (method_exists($this,str_replace("locations.","",$returntask))){
				$returntask = str_replace("locations.","",$returntask);
				return $this->$returntask();
			}

			$tmpl = "";
			if ( JRequest::getString("tmpl","")=="component"){
				$tmpl ="&tmpl=component";
			}
		}
		$this->setRedirect(JRoute::_( 'index.php?option=com_jevlocations&task=locations.list' . $tmpl));
	}


	function privatise()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		if (JevLocationsHelper::canCreateGlobal()){

			$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
			JArrayHelper::toInteger($cid);

			$this->_authoriseAccess($cid);

			if (count( $cid ) < 1) {
				JError::raiseError(500, JText::_( 'Select an item to unpublish' ) );
			}

			$model = & $this->getModel("location", "LocationsModel");
			if(!$model->globalise($cid, 0)) {
				echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
			}

			$returntask	= JRequest::getVar( 'returntask', "locations.overview");
			if ($returntask!="locations.list" && $returntask!="locations.overview" && $returntask!="locations.select"){
				$returntask="locations.overview";
			}
			if (method_exists($this,str_replace("locations.","",$returntask))){
				$returntask = str_replace("locations.","",$returntask);
				return $this->$returntask();
			}

			$tmpl = "";
			if (JRequest::getString("tmpl","")=="component"){
				$tmpl ="&tmpl=component";
			}

		}
		//$this->list();
		$this->setRedirect( JRoute::_('index.php?option=com_jevlocations&task=locations.list'.$tmpl) );
	}

	function upload(){

		// Check for request forgeries
		JRequest::checkToken( 'request' ) or jexit( 'Invalid Token' );

		$this->view = & $this->getView("locations","html");

		if (!JevLocationsHelper::canUploadImages()){
				$this->view->setLayout('noauth');
				$this->view->assign('msg'   , JText::_("Not authorised"));
				$this->view->display();
				return;			
		}

		$this->view->setLayout('upload');

		$folder		= JRequest::getVar( 'folder', '', '', 'path' );
		$field		= JRequest::getVar( 'field', '', '' );

		// Get the media component configuration settings
		$params =& JComponentHelper::getParams('com_media');
		// Set the path definitions
		define('JEVP_MEDIA_BASE',    JPATH_ROOT.DS.$params->get('image_path', 'images'.DS.'stories'));
		define('JEVP_MEDIA_BASEURL', JURI::root(true).'/'.$params->get('image_path', '/mages/stories'));

		$jevLocationsHelper = new JevLocationsHelper();
		foreach ($_FILES as $fname=>$file) {
			if ($fname != $field."_file") continue;
			if (strpos($fname,"image")===0){
				$filename = $jevLocationsHelper->processImageUpload($fname);
				$this->view->assign("filetype","image");
				$oname = $_FILES[$fname]['name'];
				$this->view->assign("oname",$oname);
			}
		}
		$this->view->assign("fname",$field."_file");
		$this->view->assign("filename",$filename);
		$this->view->display();

	}
	

	function orderup()
	{
		$this->_authoriseAccess();

		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$model = & $this->getModel("locations", "LocationsModel");
		$model->move(-1);

		$tmpl = "";
		if (JRequest::getString("tmpl","")=="component"){
			$tmpl ="&tmpl=component";
		}

		$this->setRedirect( JRoute::_('index.php?option=com_jevlocations&task=locations.list' .$tmpl));
	}

	function orderdown()
	{
		$this->_authoriseAccess();

		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$model = & $this->getModel("locations", "LocationsModel");
		$model->move(1);
		$tmpl = "";
		if (JRequest::getString("tmpl","")=="component"){
			$tmpl ="&tmpl=component";
		}

		$this->setRedirect( JRoute::_('index.php?option=com_jevlocations&task=locations.list'.$tmpl) );
	}

	function saveorder()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$cid 	= JRequest::getVar( 'cid', array(), 'post', 'array' );
		$order 	= JRequest::getVar( 'order', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
		JArrayHelper::toInteger($order);

		$this->_authoriseAccess($cid);

		$model = & $this->getModel("location", "LocationsModel");
		$model->saveorder($cid, $order);

		$tmpl = "";
		if (JRequest::getString("tmpl","")=="component"){
			$tmpl ="&tmpl=component";
		}

		$msg = 'New ordering saved';
		$this->setRedirect( JRoute::_('index.php?option=com_jevlocations&task=locations.list' .  $tmpl, false),$msg);
	}

	
	/**
	 * This mechanism currently only checks to see if user is authorised to do anything to locations
	 *
	 * @param unknown_type $locid
	 */
	function _authoriseAccess($locid=0)
	{
		$jevuser = JEVHelper::getAuthorisedUser();
		if (is_null($jevuser)){
			$params =& JComponentHelper::getParams('com_jevents');
			$authorisedonly = $params->get("authorisedonly",0);
			if (!$authorisedonly){
				$params =& JComponentHelper::getParams("com_jevlocations");
				$loc_own = $params->get("loc_own",25);
				$juser =& JFactory::getUser();
				if ($juser->gid>=intval($loc_own)){
					return true;
				}
			}

			// check if anonymous users can add or save locations
			if (($locid==0 && $this->_task=='save') || (is_array($locid) && isset($locid[0]) && $locid[0]==0 && $this->_task=='edit' ) || $this->_task=='cancel'){
				$params =& JComponentHelper::getParams("com_jevlocations");
				$anoncreate = $params->get("anoncreate",25);
				$user = JFactory::getUser();
				if ($anoncreate && $user->id==0){

					// Now make sure captcha is valid
					if ($this->_task=='save'){
						$plugin = JPluginHelper::getPlugin('jevents', 'jevanonuser' );
						$pluginparams = new JParameter($plugin->params);
						if ($pluginparams->get("recaptchapublic",false)){
							global $mainframe;
							$jevrecaptcha = $mainframe->getUserState("jevrecaptcha");

							if ($jevrecaptcha == "ok") {
								$mainframe->setUserState("jevrecaptcha","error");
								return true;
							}
							// Belt and braces
							require_once(JPATH_SITE.'/plugins/jevents/anonuserlib/recaptcha.php');
							$response = recaptcha_check_answer($pluginparams->get("recaptchaprivate",false),JRequest::getString("REMOTE_ADDR","","server"), JRequest::getString("recaptcha_challenge_field"),JRequest::getString("recaptcha_response_field"));
							if ($response->is_valid){

								// check for valid email and name ?

								$mainframe->setUserState("jevrecaptcha","ok");
								return true;
							}
							else {
								JPlugin::loadLanguage( 'plg_jevents_jevanonuser',JPATH_ADMINISTRATOR );
								echo "<script> alert('".JText::_("JEV RECAPTCHA ERROR",true)."'); window.history.go(-1); </script>\n";
								exit();
							}
						}
					}
					else return true;
				}
			}
		}
		else if ($jevuser->cancreateown || $jevuser->cancreateglobal) {
			// if jevents is not in authorised only mode then switch off this user's permissions
			$params =& JComponentHelper::getParams('com_jevents');
			$authorisedonly = $params->get("authorisedonly",0);
			if (!$authorisedonly){
				$params =& JComponentHelper::getParams("com_jevlocations");
				$loc_own = $params->get("loc_own",25);
				$juser =& JFactory::getUser();
				if ($juser->gid<intval($loc_own)){
					$jevuser->cancreateown=false;
					$jevuser->cancreateglobal=false;
					return false;
				}
			}
			return true;
		}

		$this->setRedirect( 'index.php', JText::_("Not authorised") );
		$this->redirect();

	}

	function fixCreationPermissions() {
		$jevuser =& JEVHelper::getAuthorisedUser();
		if 	($jevuser && ($jevuser->cancreateown || $jevuser->cancreateglobal)){
			// if jevents is not in authorised only mode then switch off this user's permissions
			$params =& JComponentHelper::getParams('com_jevents');
			$authorisedonly = $params->get("authorisedonly",0);
			if (!$authorisedonly){
				$params =& JComponentHelper::getParams("com_jevlocations");
				$loc_own = $params->get("loc_own",25);
				$juser =& JFactory::getUser();
				if ($juser->gid<intval($loc_own)){
					$jevuser->cancreateown=false;
					$jevuser->cancreateglobal=false;
					return false;
				}
				$loc_global = $params->get("loc_global",24);
				if ($juser->gid<intval($loc_global)){
					$jevuser->cancreateglobal=false;
				}
			}
			return true;

		}
	}

	private function notifyAdmin($location){
		$params =& JComponentHelper::getParams("com_jevlocations");
		$anoncreate = $params->get("anoncreate",25);
		$user = JFactory::getUser();
		if ($anoncreate && $user->id==0){
			$locadmin = $params->get("locadmin",62);

			$subject = $params->get("notifysubject","");
			$message = $params->get("notifymessage","");
			if ($subject =="" || $message =="") return;
			$message = str_replace("{TITLE}",$location->title,$message);
			$message = str_replace("{NAME}",$location->anonname,$message);
			$message = str_replace("{EMAIL}",$location->anonemail,$message);
			$adminuser = JFactory::getUser($locadmin);
			JUtility::sendMail($adminuser->email,$adminuser->name,$adminuser->email,$subject,$message);
		}
	}
}
