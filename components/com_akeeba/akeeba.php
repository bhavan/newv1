<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2006-2010 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 2, or later
 * @version $Id: akeeba.php 92 2010-03-18 10:33:11Z nikosdion $
 * @since 1.3
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

define('AKEEBAENGINE', 1); // Required for accessing Akeeba Engine's factory class
define('AKEEBAPLATFORM', 'joomla15'); // So that platform-specific stuff can get done!

require_once(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_akeeba'.DS.'version.php');

// Apply profile selection, if any
$profile = JRequest::getInt('profile',1);
if(!is_numeric($profile)) $profile = 1;
$session =& JFactory::getSession();
$session->set('profile', $profile, 'akeeba');
JRequest::setVar('profile', $profile);

// Get the view and controller from the request, or set to default if they weren't set
JRequest::setVar('view', JRequest::getCmd('view','backup'));
JRequest::setVar('c', JRequest::getCmd('view')); // Black magic: Get controller based on the selected view

// Black Magic II: merge the default translation with the current translation
$jlang =& JFactory::getLanguage();
$jlang->load('com_akeeba', JPATH_SITE, 'en-GB', true);
$jlang->load('com_akeeba', JPATH_SITE, $jlang->getDefault(), true);
$jlang->load('com_akeeba', JPATH_SITE, null, true);
$jlang->load('com_akeeba', JPATH_ADMINISTRATOR, 'en-GB', true);
$jlang->load('com_akeeba', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
$jlang->load('com_akeeba', JPATH_ADMINISTRATOR, null, true);

// Preload the JPFactory
jimport('joomla.filesystem.file');
require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'akeeba'.DS.'factory.php';

// Load the utils helper library
AEPlatform::load_version_defines();

// Load the appropriate controller
$c = JRequest::getCmd('c','cpanel');
$path = JPATH_COMPONENT.DS.'controllers'.DS.$c.'.php';
jimport('joomla.filesystem.file');
if(JFile::exists($path))
{
	// The requested controller exists and there you load it...
	require_once($path);
}
else
{
	// Hmm... an invalid controller was passed
	JError::raiseError('500',JText::_('Unknown controller'));
}

// Instanciate and execute the controller
jimport('joomla.utilities.string');
$c = 'AkeebaController'.ucfirst($c);
$controller = new $c();
$controller->execute(JRequest::getCmd('task','display'));

// Redirect
$controller->redirect();