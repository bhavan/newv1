<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: mod.defines.php 1400 2009-03-30 08:45:17Z geraint $
 * @package     JEvents
 * @copyright   Copyright (C) 2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

class plgJEventsJevanonuser extends JPlugin
{
	private $recaptchalang = "en";

	function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);

		JPlugin::loadLanguage( 'plg_jevents_jevanonuser',JPATH_ADMINISTRATOR );
		
		$lang = JFactory::getLanguage();
		
		list ($tag1,$tag2) = explode("-",$lang->getTag());
		// See http://recaptcha.net/apidocs/captcha/client.html for list of supported languages
		$langs = array("en","nl","fr","de","pt","ru","es","tr");

		if (in_array($tag1,$langs)){
			$this->recaptchalang = $tag1;
		}
	}

	// This enable anon users to create (but not edit events)
	function isEventCreator(&$isEventCreator){

		$user = JFactory::getUser();
		// if logged in then do not change isEventCreator
		if ($user->id>0) return true;

		$document	=& JFactory::getDocument();
		$document->addScript( JURI::root(true).'/includes/js/joomla.javascript.js');

		$isEventCreator = true;
		return true;
	}

	// This enable anon users to create (but not edit events)
	function isEventPublisher($type, &$isEventPublisher){

		$user = JFactory::getUser();
		// if logged in then do not change isEventPublisher
		if ($user->id>0 || $type=="strict") return true;

		$isEventPublisher = $this->params->get("canpublishown",0);
		
	}


	/**
	 * Custom part of form for re-captcha and name/email
	 *
	 * @param unknown_type $row
	 * @param unknown_type $customfields
	 * @return unknown
	 */
	function onEditCustom( &$row, &$customfields )
	{
		
		$user = JFactory::getUser();
		
		if ($user->id>0 && $row->ev_id()==0) return true;

		if ($row->ev_id()>0 && $row->created_by()>0) return true;
		
		$this->createtable();
		
		// Only setup when editing an event (they should not be able to edit a repeat !!!)
		if (JRequest::getString("jevtask","")!="icalevent.edit") return;

		$anonname=false;
		$anonemail=false;
		if ($row->ev_id()>0){
			$db = JFactory::getDBO();
			$db->setQuery("SELECT * FROM #__jev_anoncreator where ev_id=".intval($row->ev_id()));
			$anonrow = $db->loadObject();	
			if ($anonrow){
				$anonname=$anonrow->name;
				$anonemail=$anonrow->email;
			}
		}
				
		$label = JText::_("JEV ANON NAME");
		$input	= '<input size="50" type="text" name="custom_anonusername" id="custom_anonusername" value="'.$anonname.'" />';
		$customfield = array("label"=>$label,"input"=>$input);
		$customfields["anonusername"]=$customfield;

		$label = JText::_("JEV ANON EMAIL");
		$input	= '<input size="50" type="text" name="custom_anonemail" id="custom_anonemail" value="'.$anonemail.'" />';
		$customfield = array("label"=>$label,"input"=>$input);
		$customfields["anonemail"]=$customfield;

		if ($user->id==0 && $this->params->get("recaptchapublic",false)){

			JHTML::script("recaptcha.js","plugins/jevents/anonuserlib/",true);

			$label = JText::_("JEV ANON RECAPTCHA");
			if (!defined("RECAPTCHA_API_SERVER"))	require_once('anonuserlib/recaptcha.php');
			$input	= recaptcha_get_html($this->params->get("recaptchapublic",false));
			$customfield = array("label"=>$label,"input"=>$input);
			$customfields["recaptcha"]=$customfield;

			$root = JURI::root();
			$token = JUtility::getToken();
			$missingnameemail = JText::_("JEV MISSING NAME OR EMAIL",true);
			$checkscript = <<<SCRIPT
	urlroot = '$root';
var RecaptchaOptions = {
   theme : 'clean',
   lang : '$this->recaptchalang'
};
var missingnameoremail = '$missingnameemail';
SCRIPT;
			$document=& JFactory::getDocument();
			$document->addScriptDeclaration($checkscript);

			global $mainframe;
			$mainframe->setUserState("jevrecaptcha","error");

		}


		return true;
	}

	function onBeforeSaveEvent(&$array, &$rrule){
		// make sure self publishing respects plugin settings and also set access level to public
		$array["access"]=0;
		
		if (!$this->params->get("canpublishown",0)){
			$params =& JComponentHelper::getParams(JEV_COM_COMPONENT);
			$params->set("jevpublishown",0);
		}
		
	}
	
	/**
	 * Store custom fields at event level
	 *
	 */
	function onStoreCustomEvent($event){

		$user = JFactory::getUser();
		if ($user->id>0 && $event->ev_id==0) return true;

		// do I need to reset the created_by field to 0;
		$name = JRequest::getString("custom_anonusername","");
		$email = JRequest::getString("custom_anonemail","");
		
		if ($event->ev_id>0 && ($name || $email)){
			$db = JFactory::getDBO();
			$db->setQuery("SELECT * FROM #__jev_anoncreator where ev_id=".intval($event->ev_id));
			$anonrow = $db->loadObject();
			
			if ($anonrow) {
				$db->setQuery("UPDATE #__jevents_vevent SET created_by=0 WHERE ev_id=".intval($event->ev_id));
				$db->query();
								
				$event->created_by = 0;
				
				$creator = new JTable("#__jev_anoncreator",'id',$db);
				$creator->id = $anonrow->id;
				$creator->name = $name;
				$creator->email = $email;
				$creator->store();
				return true;				
			}
		}

		if ($event->ev_id>0 && $event->created_by>0) return true;
		
		$this->createtable();

		$eventid = $event->ev_id;

		if ($this->params->get("recaptchaprivate",false)){

			global $mainframe;
			$jevrecaptcha = $mainframe->getUserState("jevrecaptcha");

			$name = JRequest::getString("custom_anonusername","");
			$email = JRequest::getString("custom_anonemail","");

			if ($jevrecaptcha == "ok" && $name!="" && $email!="")  {
				$mainframe->setUserState("jevrecaptcha","error");
				
				// Store the name and email address with the event
				$db = JFactory::getDBO();
				$creator = new JTable("#__jev_anoncreator",'id',$db);
				$creator->id = 0;
				$creator->email = $email;
				$creator->name = $name;
				$creator->ev_id = $eventid;
				$creator->store();
				return true;
			}
			// Belt and braces
			$label = JText::_("JEV ANON RECAPTCHA");
			if (!defined("RECAPTCHA_API_SERVER"))	require_once('anonuserlib/recaptcha.php');
			$response = recaptcha_check_answer($this->params->get("recaptchaprivate",false),JRequest::getString("REMOTE_ADDR","","server"), JRequest::getString("recaptcha_challenge_field"),JRequest::getString("recaptcha_response_field"));
			if (!$response->is_valid){

				// The event has already been saved - I need to delete it!
				$db = JFactory::getDBO();

				$query = "SELECT detail_id FROM #__jevents_vevent WHERE ev_id IN ($eventid)";
				$db->setQuery( $query);
				$detailidstring = $db->loadResult();

				$query = "DELETE FROM #__jevents_rrule WHERE eventid IN ($eventid)";
				$db->setQuery( $query);
				$db->query();

				$query = "DELETE FROM #__jevents_repetition WHERE eventid IN ($eventid)";
				$db->setQuery( $query);
				$db->query();

				$query = "DELETE FROM #__jevents_exception WHERE eventid IN ($eventid)";
				$db->setQuery( $query);
				$db->query();

				$query = "DELETE FROM #__jevents_vevdetail WHERE evdet_id IN ($detailidstring)";
				$db->setQuery( $query);
				$db->query();

				// I also need to clean out associated custom data
				$dispatcher	=& JDispatcher::getInstance();
				// just incase we don't have jevents plugins registered yet
				JPluginHelper::importPlugin("jevents");
				$res = $dispatcher->trigger( 'onDeleteEventDetails' , array($detailidstring));

				$query = "DELETE FROM #__jevents_vevent WHERE ev_id IN ($eventid)";
				$db->setQuery( $query);
				$db->query();

				// I also need to delete custom data
				$dispatcher	=& JDispatcher::getInstance();
				// just incase we don't have jevents plugins registered yet
				JPluginHelper::importPlugin("jevents");
				$res = $dispatcher->trigger( 'onDeleteCustomEvent' , array(&$eventid));

				echo "<script> alert('".JText::_("JEV RECAPTCHA ERROR",true)."'); window.history.go(-1); </script>\n";
				exit();

			}
		}


		return $success;
	}
	
	private function createtable(){
		$db = & JFactory::getDBO();
		$charset = ($db->hasUTF()) ? 'DEFAULT CHARACTER SET `utf8`' : '';
		$sql = <<<SQL
CREATE TABLE IF NOT EXISTS #__jev_anoncreator(
	id int(11) NOT NULL auto_increment,
	ev_id int(11) NOT NULL default 0,
	name varchar(255) NOT NULL default '',
	email varchar(255) NOT NULL default '',
	PRIMARY KEY  (id),
	INDEX (ev_id)
) TYPE=MyISAM $charset;	
SQL;
		$db->setQuery($sql);
		if (!$db->query()){
			echo $db->getErrorMsg();
		}

	}
	
}

