<?php
/**
 * @version		1.0.0 townwizard_container $
 * @package		townwizard_container
 * @copyright	Copyright © 2012 - All rights reserved.
 * @license		GNU/GPL
 * @author		MLS
 * @author mail	nobody@nobody.com
 *
 *
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Your custom code here
jimport('joomla.filesystem.file');

$folder = JPATH_SITE.DS.'media'.DS.'com_townwizard';

if (JFolder::exists($folder))
{
    JFolder::delete($folder);
}
?>
