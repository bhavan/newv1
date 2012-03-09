<?php
/**
 * copyright (C) 2008 GWE Systems Ltd - All rights reserved
 */

defined( 'JPATH_BASE' ) or die( 'Direct Access to this location is not allowed.' );

jimport('joomla.application.component.controller');

class AdminCpanelController extends JController {
	/**
	 * Controler for the Control Panel
	 * @param array		configuration
	 */
	function __construct($config = array())
	{
		parent::__construct($config);
		$this->registerTask( 'show',  'cpanel' );
		$this->registerDefaultTask("cpanel");

		// setupdatabase
		$this->_checkDatabase();

		$this->cleanUpOrphans();
		
		// setup filesystem for images
		$this->_checkFilesystem();
	}

	function cpanel( )
	{
		// get the view
		$this->view = & $this->getView("cpanel","html");

		// Set the layout
		$this->view->setLayout('cpanel');
		$this->view->assign('title'   , JText::_("Control Panel"));

		$this->view->cpanel();
	}

	function _checkDatabase(){
		$db	=& JFactory::getDBO();

		/**
	 * create tables if it doesn't exit
	 * 
	 */
		$sql = <<<SQL
CREATE TABLE IF NOT EXISTS #__jev_people(
	pers_id int(12) NOT NULL auto_increment,
	title VARCHAR(255) NOT NULL default "",
	alias VARCHAR(255) NOT NULL default "",
	street varchar(255) NOT NULL default "",
	postcode varchar(255) NOT NULL default "",
	city varchar(255) NOT NULL default "",
	state varchar(255) NOT NULL default "",
	country varchar(255) NOT NULL default "",
	image varchar(255) NOT NULL default "",
	imagetitle varchar(255) NOT NULL default "",
	description TEXT NOT NULL default "",
	geolon float NOT NULL default 0,
	geolat float NOT NULL default 0,
	geozoom int(2) NOT NULL default 10,
	pcode_id int(12) NOT NULL default 0,
	www varchar(255) NOT NULL default "",

	type_id int(11) NOT NULL default 0,
	linktouser int(11) NOT NULL default 0,
 
	catid0 int(11) NOT NULL default 0,
	catid1 int(11) NOT NULL default 0,
	catid2 int(11) NOT NULL default 0,
	catid3 int(11) NOT NULL default 0,
	catid4 int(11) NOT NULL default 0,
	
	global tinyint(1) unsigned NOT NULL default 0,

	ordering int(11) NOT NULL default '0',
	access int(11) unsigned NOT NULL default 0,
	published tinyint(1) unsigned NOT NULL default 0,
	created datetime  NOT NULL default '0000-00-00 00:00:00',
	created_by int(11) unsigned NOT NULL default '0',
	created_by_alias varchar(100) NOT NULL default '',
	modified_by int(11) unsigned NOT NULL default '0',
		
	checked_out int(11) unsigned NOT NULL default '0',
	checked_out_time DATETIME NOT NULL default '0000-00-00 00:00:00',

	params TEXT NOT NULL default "",
	
	PRIMARY KEY  (pers_id)
) TYPE=MyISAM;	
SQL;
		$db->setQuery($sql);
		if (!$db->query()){
			echo $db->getErrorMsg();
		}

		$sql = "SHOW COLUMNS FROM `#__jev_people`";
		$db->setQuery( $sql );
		$cols = $db->loadObjectList();
		$uptodate = false;
		foreach ($cols as $col) {
			if ($col->Field=="linktouser"){
				$uptodate = true;
				break;
			}
		}
		if (!$uptodate){

			$sql = "ALTER TABLE #__jev_people ADD column linktouser int(11) NOT NULL default 0";
			$db->setQuery($sql);
			if (!@$db->query()){
				echo $db->getErrorMsg()."<br/>";
			}

			$sql = "ALTER TABLE #__jev_people ADD column www varchar(255) NOT NULL default ''";
			$db->setQuery($sql);
			@$db->query();

			$sql = "ALTER TABLE #__jev_people ADD column image varchar(255) NOT NULL default ''";
			$db->setQuery($sql);
			@$db->query();

			$sql = "ALTER TABLE #__jev_people ADD column imagetitle varchar(255) NOT NULL default ''";
			$db->setQuery($sql);
			@$db->query();

		}

		$sql = <<<SQL
CREATE TABLE IF NOT EXISTS #__jev_peopletypes(
	type_id int(12) NOT NULL auto_increment,
	title VARCHAR(255) NOT NULL default "",
	multiple tinyint(1) unsigned NOT NULL default 0,	
	maxperevent int(5) unsigned NOT NULL default 1,	
	multicat tinyint(1) NOT NULL default 0,	
	showaddress tinyint(1) NOT NULL default 0,	

	categories VARCHAR(255) NOT NULL default "",
	calendars VARCHAR(255) NOT NULL default "",

	PRIMARY KEY  (type_id)
) TYPE=MyISAM;	
SQL;
		$db->setQuery($sql);
		if (!$db->query()){
			echo $db->getErrorMsg();
		}

				$sql = "SHOW COLUMNS FROM `#__jev_peopletypes`";
		$db->setQuery( $sql );
		$cols = $db->loadObjectList();
		$uptodate = false;
		foreach ($cols as $col) {
			if ($col->Field=="categories"){
				$uptodate = true;
				break;
			}
		}
		if (!$uptodate){
			$sql = "ALTER TABLE #__jev_peopletypes ADD column categories VARCHAR(255) NOT NULL default ''";
			$db->setQuery($sql);
			if (!@$db->query()){
				echo $db->getErrorMsg()."<br/>";
			}

			$sql = "ALTER TABLE #__jev_peopletypes ADD column calendars VARCHAR(255) NOT NULL default ''";
			$db->setQuery($sql);
			@$db->query();

			$sql = "ALTER TABLE #__jev_peopletypes ADD column maxperevent int(5) unsigned NOT NULL default 1";
			$db->setQuery($sql);
			@$db->query();
		}

		$sql = <<<SQL
CREATE TABLE IF NOT EXISTS #__jev_peopleeventsmap(
	pers_id int(12) NOT NULL  default 0,
	evdet_id int(12) NOT NULL  default 0,
	ordering int(4) NOT NULL  default 0,

	PRIMARY KEY (pers_id,evdet_id),
	INDEX  (evdet_id),
	INDEX  (pers_id)
) TYPE=MyISAM;	
SQL;
		$db->setQuery($sql);
		if (!$db->query()){
			echo $db->getErrorMsg();
		}

		$sql = "SHOW COLUMNS FROM `#__jev_peopleeventsmap`";
		$db->setQuery( $sql );
		$cols = $db->loadObjectList();
		$uptodate = false;
		foreach ($cols as $col) {
			if ($col->Field=="ordering"){
				$uptodate = true;
				break;
			}
		}
		if (!$uptodate){
			$sql = "ALTER TABLE #__jev_peopleeventsmap ADD column ordering int(4) NOT NULL  default 0";
			$db->setQuery($sql);
			if (!@$db->query()){
				echo $db->getErrorMsg()."<br/>";
			}
		}
	}

	function _checkFilesystem(){
		// folder relative to media folder
		// Get the media component configuration settings
		$params =& JComponentHelper::getParams('com_media');
		// Set the path definitions
		define('JEVP_MEDIA_BASE',    JPATH_ROOT.DS.$params->get('image_path', 'images'.DS.'stories'));
		define('JEVP_MEDIA_BASEURL', JURI::root(true).'/'.$params->get('image_path', 'images/stories'));

		$folder = "jevents/jevpeople";
		// ensure folder exists
		if (!JFolder::exists(JEVP_MEDIA_BASE.DS.$folder)) {
			JFolder::create(JEVP_MEDIA_BASE.DS.$folder,0777);
		}
		$folder = "jevents/jevpeople/thumbnails";
		// ensure folder exists
		if (!JFolder::exists(JEVP_MEDIA_BASE.DS.$folder)) {
			JFolder::create(JEVP_MEDIA_BASE.DS.$folder,0777);
		}
	}

	function cleanUpOrphans() {
		$db = JFactory::getDBO();
		$sql = "SELECT DISTINCT cat.section FROM #__categories as cat WHERE cat.section LIKE ('com_jevpeople_type%')";
		$db->setQuery($sql);
		$cats = $db->loadObjectList();
		
		$sql = "SELECT * FROM #__jev_peopletypes";
		$db->setQuery($sql);
		$types = $db->loadObjectList('type_id');

		foreach ($cats as $cat) {
			$type = str_replace('com_jevpeople_type','',$cat->section);
			if (!array_key_exists($type,$types)){
				$db->setQuery("DELETE FROM  #__categories WHERE section=".$db->Quote($cat->section));	
				$db->query();
			}
		}
	}
}
