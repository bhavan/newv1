<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

JLoader::register('jevFilterProcessing',JPATH_SITE."/components/com_jevents/libraries/filters.php");
global $mainframe;
if($mainframe->isAdmin()) {
	return;
}

jimport( 'joomla.plugin.plugin' );


class plgJEventsJevtimelimit extends JPlugin
{

	function plgJEventsJevtimelimit(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}

	function onListIcalEvents( & $extrafields, & $extratables, & $extrawhere, & $extrajoin, & $needsgroupdby=false)
	{
		global $mainframe;
		if($mainframe->isAdmin()) {
			return;
		}

		$pluginsDir = JPATH_ROOT.DS.'plugins'.DS.'jevents';
		$filters = jevFilterProcessing::getInstance(array("timelimit"),$pluginsDir.DS."filters".DS);

		$filters->setWhereJoin($extrawhere,$extrajoin);

		return true;
	}

	function onListEventsById( & $extrafields, & $extratables, & $extrawhere, & $extrajoin)
	{
		global $mainframe;
		if($mainframe->isAdmin()) {
			return;
		}
		$pluginsDir = JPATH_ROOT.DS.'plugins'.DS.'jevents';
		$filters = jevFilterProcessing::getInstance(array("timelimit"),$pluginsDir.DS."filters".DS);

		$filters->setWhereJoin($extrawhere,$extrajoin);

		return true;
	}

}
