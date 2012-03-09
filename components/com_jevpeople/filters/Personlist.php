<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: Justmine.php 1400 2009-03-30 08:45:17Z geraint $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// ensure this file is being included by a parent file
defined('_JEXEC') or die( 'Direct Access to this person is not allowed.' );



/**
 * Filters events to restrict events to those at particular people
 */

class jevPersonlistFilter extends jevFilter
{

	function jevPersonlistFilter($tablename, $filterfield, $isstring=true,$yesLabel="Yes", $noLabel="No"){
		$this->filterNullValue="";
		parent::jevFilter($tablename, "personlist", $isstring);

	}

	function _createFilter($prefix=""){
		$registry =& JFactory::getConfig();
		$loclist = $registry->get("jevpeople.people",-1);
		return "det.person IN ($loclist)";
	}


}
