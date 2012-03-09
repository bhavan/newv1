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
class FrontLocationsViewLocations extends AdminLocationsViewLocations
{
	function __construct($config = array()){
		parent::__construct($config);

	}

	function locations($tpl = null)
	{
		JHTML::stylesheet("pagination.css",JURI::root()."administrator/components/com_jevlocations/assets/pagination/css/");
		// make sure sorting JS is loaded
		$user		=& JFactory::getUser();
		if ( !$user->get('id') ) {
			JHTML::script("joomla.javascript.js",JURI::base().'includes/js/');
		}

		JLoader::register('JEventsHTML',JPATH_SITE."/components/com_jevents/libraries/jeventshtml.php");

		global $mainframe, $option;
		JHTML::stylesheet( 'jevlocations.css', 'components/'.$option.'/assets/css/' );

		$db		=& JFactory::getDBO();
		$uri	=& JFactory::getURI();

		$filter_order		= $mainframe->getUserStateFromRequest( $option.'loc_filter_order',		'filter_order',		'loc.ordering',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'loc_filter_order_Dir',	'filter_order_Dir',	'',				'word' );
		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;

		$filter_catid		= $mainframe->getUserStateFromRequest( $option.'loc_filter_loccat',	'filter_loccat',		0,				'int' );
		$javascript 	= 'onchange="document.adminForm.submit();"';
		$lists['loccat'] = JEventsHTML::buildCategorySelect(intval( $filter_catid ),$javascript,"",false,false,0,'filter_loccat','com_jevlocations2');
		$lists['loccat'] = str_replace(JText::_('JEV_EVENT_ALLCAT'),JText::_("All Categories"),$lists['loccat'] );

		$search				= $mainframe->getUserStateFromRequest( $option.'loc_search',			'search',			'',				'string' );
		$search				= JString::strtolower( $search );
		// search filter
		$lists['search']= $search;
		
		$compparams = JComponentHelper::getParams("com_jevlocations");
		$usecats = $compparams->get("usecats",0);
		$this->assignRef('usecats',		$usecats);

		$model	=& $this->getModel();
		$model->setState("limitstart",JRequest::getInt("limitstart",0));
		$items		=  $model->getPublicData();
		$pagination =  $model->getPublicPagination();

		// check if location has any events	- a very crude test
		jimport("joomla.utilities.date");
		$startdate = new JDate("-".$compparams->get("checkeventbefore",30)." days");
		$enddate = new JDate("+".$compparams->get("checkeventafter",30)." days");

		foreach ($items as &$item) {
			if ($compparams->get('checkevents',1)){
				$item->hasEvents = $model->hasEvents($item->loc_id, $startdate->toMySQL(), $enddate->toMySQL());
			}
			else $item->hasEvents = 1;
			unset($item);
		}

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

		$this->assignRef('items',		$items);
		$this->assignRef('lists',		$lists);
		$this->assignRef('pagination',	$pagination);

		parent::display($tpl);
	}

	function overview($tpl = null)
	{
		JLoader::register('JToolbarHelper' , JPATH_ADMINISTRATOR.DS.'includes'.DS.'toolbar.php');

		// TODO find the active admin template
		//JHTML::stylesheet("system.css",JURI::root()."administrator/templates/system/css/");
		//JHTML::stylesheet("template.css",JURI::root()."administrator/templates/khepri/css/");
		JHTML::stylesheet("admin.css",JURI::root()."components/com_jevlocations/assets/adminsim/css/");
		JHTML::stylesheet("pagination.css",JURI::root()."administrator/components/com_jevlocations/assets/pagination/css/");

		$model	=& $this->getModel();
		$model->setState("limitstart",JRequest::getInt("limitstart",0));

		parent::overview($tpl);

	}

	function select($tpl = null)
	{
		// Make sure form stuff is loaded
		$user		=& JFactory::getUser();
		if ( !$user->get('id') ) {
			JHTML::script("joomla.javascript.js",JURI::base().'includes/js/');
		}

		JLoader::register('JToolbarHelper' , JPATH_ADMINISTRATOR.DS.'includes'.DS.'toolbar.php');
		// TODO find the active admin template
		//JHTML::stylesheet("system.css",JURI::root()."administrator/templates/system/css/");
		//JHTML::stylesheet("template.css",JURI::root()."administrator/templates/khepri/css/");
		JHTML::stylesheet("admin.css",JURI::root()."components/com_jevlocations/assets/adminsim/css/");
		JHTML::stylesheet("pagination.css",JURI::root()."administrator/components/com_jevlocations/assets/pagination/css/");

		$model	=& $this->getModel();
		$model->setState("limitstart",JRequest::getInt("limitstart",0));

		parent::select($tpl);

	}

	function edit($tpl = null)
	{
		JLoader::register('JToolbarHelper' , JPATH_ADMINISTRATOR.DS.'includes'.DS.'toolbar.php');
		// TODO find the active admin template
		//JHTML::stylesheet("system.css",JURI::root()."administrator/templates/system/css/");
		//JHTML::stylesheet("template.css",JURI::root()."administrator/templates/khepri/css/");
		JHTML::stylesheet("admin.css",JURI::root()."components/com_jevlocations/assets/adminsim/css/");
		JHTML::stylesheet("pagination.css",JURI::root()."administrator/components/com_jevlocations/assets/pagination/css/");


		$plugin = JPluginHelper::getPlugin('jevents', 'jevanonuser' );
		if ($plugin){
			$pluginparams = new JParameter($plugin->params);
			$user = JFactory::getUser();
			if ($pluginparams->get("recaptchapublic",false) && $user->id==0){

				JPlugin::loadLanguage( 'plg_jevents_jevanonuser',JPATH_ADMINISTRATOR );

				$label = JText::_("JEV ANON NAME");
				$input	= '<input size="50" type="text" name="anonname" id="anonname" value="" />';
				$customfield = array("label"=>$label,"input"=>$input);
				$this->assign("name",$customfield);

				$label = JText::_("JEV ANON EMAIL");
				$input	= '<input size="50" type="text" name="anonemail" id="anonemail" value="" />';
				$customfield = array("label"=>$label,"input"=>$input);
				$this->assign("email",$customfield);

				JHTML::script("recaptcha.js","plugins/jevents/anonuserlib/",true);

				$label = JText::_("JEV ANON RECAPTCHA");
				require_once(JPATH_SITE.'/plugins/jevents/anonuserlib/recaptcha.php');
				$input	= recaptcha_get_html($pluginparams->get("recaptchapublic",false));
				$customfield = array("label"=>$label,"input"=>$input);
				$this->assign("recaptcha",$customfield);
				
				$lang = JFactory::getLanguage();

				list ($tag1,$tag2) = explode("-",$lang->getTag());
				// See http://recaptcha.net/apidocs/captcha/client.html for list of supported languages
				$langs = array("en","nl","fr","de","pt","ru","es","tr");

				if (in_array($tag1,$langs)){
					$this->recaptchalang = $tag1;
				}

				$root = JURI::root();
				$token = JUtility::getToken();
				$checkscript = <<<SCRIPT
	urlroot = '$root';
var RecaptchaOptions = {
   theme : 'clean',
   lang : '$this->recaptchalang'
};
SCRIPT;
				$document=& JFactory::getDocument();
				$document->addScriptDeclaration($checkscript);

				global $mainframe;
				$mainframe->setUserState("jevrecaptcha","error");
}
		}
		parent::edit($tpl);

	}

}