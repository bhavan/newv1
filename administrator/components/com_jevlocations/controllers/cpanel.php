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
CREATE TABLE IF NOT EXISTS #__jev_locations(
	loc_id int(12) NOT NULL auto_increment,
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
	geolon float(12,8) NOT NULL default 0,
	geolat float(12,8) NOT NULL default 0,
	geozoom int(2) NOT NULL default 10,
	pcode_id int(12) NOT NULL default 0,
	
	phone varchar(255) NOT NULL default '',
	url varchar(255) NOT NULL default '',
	
	loccat int(11) NOT NULL default 0,

	anonname varchar(255) NOT NULL default '',
	anonemail varchar(255) NOT NULL default '',

	catid int(11) NOT NULL default 0,
	global tinyint(1) unsigned NOT NULL default 0,
	priority tinyint(2) unsigned NOT NULL default 0,
 	 	
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
	
	PRIMARY KEY  (loc_id)
) ENGINE=MyISAM;
SQL;
		$db->setQuery($sql);
		if (!$db->query()){
			echo $db->getErrorMsg();
		}

		$sql = "SHOW COLUMNS FROM `#__jev_locations`";
		$db->setQuery( $sql );
		$cols = $db->loadObjectList();
		$uptodate = false;
		foreach ($cols as $col) {
			if ($col->Field=="imagetitle"){
				$uptodate = true;
				break;
			}
		}
		if (!$uptodate){
			
			$sql = "ALTER TABLE #__jev_locations ADD column imagetitle varchar(255) NOT NULL default ''";
			$db->setQuery($sql);
			if (!@$db->query()){
				echo $db->getErrorMsg()."<br/>";
			}
			
			$sql = "ALTER TABLE #__jev_locations ADD column image varchar(255) NOT NULL default ''";
			$db->setQuery($sql);
			@$db->query();
			
			$sql = "ALTER TABLE #__jev_locations ADD column anonname varchar(255) NOT NULL default ''";
			$db->setQuery($sql);
			
			$sql = "ALTER TABLE #__jev_locations ADD column anonemail varchar(255) NOT NULL default ''";
			$db->setQuery($sql);

			$sql = "ALTER TABLE #__jev_locations MODIFY column geolon float(12,8) NOT NULL default 0";
			$db->setQuery($sql);

			$sql = "ALTER TABLE #__jev_locations MODIFY column geolat float(12,8) NOT NULL default 0";
			$db->setQuery($sql);			
			
			$sql = "ALTER TABLE #__jev_locations ADD column loccat int(11) NOT NULL default 0";
			$db->setQuery($sql);
			if (!@$db->query()){
				//echo $db->getErrorMsg()."<br/>";
			}

			$sql = "ALTER TABLE #__jev_locations ADD column priority tinyint(2) unsigned NOT NULL default 0";
			$db->setQuery($sql);
			if (!@$db->query()){
				//echo $db->getErrorMsg()."<br/>";
			}
			
			$sql = "ALTER TABLE #__jev_locations ADD column url varchar(255) NOT NULL default ''";
			$db->setQuery($sql);
			if (!@$db->query()){
				//echo $db->getErrorMsg()."<br/>";
			}
			$sql = "ALTER TABLE #__jev_locations ADD column phone varchar(255) NOT NULL default ''";
			$db->setQuery($sql);
			if (!@$db->query()){
				//echo $db->getErrorMsg()."<br/>";
			}
			$sql = "ALTER TABLE #__jev_locations ADD column city varchar(255) NOT NULL default ''";
			$db->setQuery($sql);
			if (!@$db->query()){
				//echo $db->getErrorMsg()."<br/>";
			}
			$sql = "ALTER TABLE #__jev_locations ADD column state varchar(255) NOT NULL default ''";
			$db->setQuery($sql);
			if (!@$db->query()){
				//echo $db->getErrorMsg()."<br/>";
			}
			$sql = "ALTER TABLE #__jev_locations ADD column country varchar(255) NOT NULL default ''";
			$db->setQuery($sql);
			if (!@$db->query()){
				//echo $db->getErrorMsg()."<br/>";
			}

			
		}
	}
	
	function _checkFilesystem(){
		// folder relative to media folder
		$folder = "jevents/jevlocations";
		// ensure folder exists
		if (!JFolder::exists(JPATH_ROOT.DS. 'images'.DS.'stories'.DS.$folder)) {
			JFolder::create(JPATH_ROOT.DS. 'images'.DS.'stories'.DS.$folder,0777);
		}
	}
	
}
