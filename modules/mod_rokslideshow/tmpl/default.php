<?php
/**
 * RokSlideshow Module
 *
 * @package		Joomla
 * @subpackage	RokSlideshow Module
 * @copyright Copyright (C) 2009 RocketTheme. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see RT-LICENSE.php
 * @author RocketTheme, LLC
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access'); 


if (count($images) > 0) :
?>
	<div id="slidewrap">
		<div id="slideshow"></div>
		<div id="loadingDiv"></div>
	</div>
<?php endif;?>