<?php
/**
 * Events Calendar Search plugin for Joomla 1.5.x
 *
 * @version     $Id: eventsearch.php 969 2008-02-16 11:24:45Z geraint $
 * @package     Events
 * @subpackage  Mambot Events Calendar
 * @copyright   Copyright (C) 2006-2007 JEvents Project Group
 * @copyright   Copyright (C) 2000 - 2003 Eric Lamette, Dave McDonnell
 * @licence     http://www.gnu.org/copyleft/gpl.html
 * @link        http://joomlacode.org/gf/project/jevents
 */

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

// setup for all required function and classes
$file = JPATH_SITE . '/components/com_jevents/mod.defines.php';
if (file_exists($file) ) {
	include_once($file);
	include_once(JEV_LIBS."/modfunctions.php");

} else {
	die ("JEvents Locations\n<br />This plugin needs the JEvents component");
}

JPlugin::loadLanguage( 'plg_search_jevlocsearch' );

// Import library dependencies
jimport('joomla.event.plugin');

$mainframe->registerEvent( 'onSearchAreas', 'plgSearchJevlocSearchAreas' );
/**
 * @return array An array of search areas
 */
function &plgSearchJevlocSearchAreas() {
	static $areas = array(
	'eventlocations' => 'JEvent Locations'
	);
	return $areas;
}


class plgSearchJevlocsearch extends JPlugin {

	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param 	object $subject The object to observe
	 * @param 	array  $config  An array that holds the plugin configuration
	 * @since 1.5
	 */

	function plgSearchJevlocsearch( &$subject )
	{
		parent::__construct( $subject );

		// load plugin parameters
		$this->_plugin = & JPluginHelper::getPlugin( 'search', 'jevlocsearch' );
		$this->_params = new JParameter( $this->_plugin->params );
	}

	/**
	* Search method
	*
	* The sql must return the following fields that are used in a common display
	* routine: href, title, section, created, text, browsernav
	* @param string Target search string
	* @param string matching option, exact|any|all
	* @param string ordering option, newest|oldest|popular|alpha|category
	*/
	function onSearch( $text, $phrase='', $ordering='' , $areas=null) {

		$db	=& JFactory::getDBO();
		$user =& JFactory::getUser();


		$limit = $this->_params->def( 'search_limit', 50 );
		$limit 		= "\n LIMIT $limit";

		$search_private = $this->_params->def( 'search_private', 0 );

		$text = trim( $text );
		if ($text == '') {
			return array();
		}

		if (is_array( $areas )) {
			if (!array_intersect( $areas, array_keys( plgSearchJevlocSearchAreas() ) )) {
				return array();
			}
		}

		$search_attributes  = array('loc.title', 'loc.description', 'loc.street', 'loc.city', 'loc.state', 'loc.country');

		$wheres_ical = array();
		switch ($phrase) {
			case 'exact':
				$text		= $db->Quote( '%'.$db->getEscaped( $text, true ).'%', false );
				$wheres2 = array();
				foreach ($search_attributes as $search_item) {
					$wheres2[] = "LOWER($search_item) LIKE ".$text;
				}
				$where_ical = '(' . implode( ') OR (', $wheres2 ) . ')';
				break;
			case 'all':
			case 'any':
			default:
				$words = explode( ' ', $text );

				$wheres = array();
				foreach ($words as $word) {
					$wheres2 = array();
					foreach ($search_attributes as $search_item) {
						$word		= $db->Quote( '%'.$db->getEscaped( $word, true ).'%', false );
						$wheres2[] = "LOWER($search_item) LIKE ".$word;
					}
					$wheres[] = implode( ' OR ', $wheres2 );
				}
				$where_ical = '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheres ) . ')';

				break;
		}

		$order = '';
		switch ($ordering) {
			case 'oldest':
				$order = 'loc.created ASC ';
				break;

			case 'popular':
			case 'category':
			case 'alpha':
				$order = 'loc.title ASC ';
				break;

			case 'newest':
			default:
				$order = 'loc.created DESC ';
				break;
		}

		$eventstitle=JText::_("Event Locations");
		$display2 = array();
		foreach ($search_attributes as $search_attribute) {
			$display2[] = "$search_attribute";
		}
		$display = 'CONCAT('. implode(", ' ', ", $display2) . ')';
		$query = "SELECT loc.title,"
		. "\n loc.created,"
		. "\n $display as text,"
		. "\n CONCAT('$eventstitle','/',loc.title) AS section,"
		. "\n CONCAT('index.php?option=com_jevlocations&task=locations.detail&loc_id=',loc.loc_id) AS href,"
		. "\n '2' AS browsernav "
		. "\n FROM #__jev_locations as loc"
		. "\n WHERE ($where_ical)"
		. "\n AND loc.access<=$user->gid"
		. "\n AND loc.published = '1'"
		. ((!$search_private)?" \n AND loc.global=1":"")
		. "\n ORDER BY " . $order
		.	$limit
		;

		$db->setQuery( $query );
		$list_ical = $db->loadObjectList();

		return $list_ical;
	}
}