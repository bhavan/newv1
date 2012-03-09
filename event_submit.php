<?php
/**
* @version		$Id: index.php 11407 2009-01-09 17:23:42Z willebil $
* @package		Joomla
* @copyright	Copyright (C) 2005 - 2009 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Set flag that this is a parent file
define( '_JEXEC', 1 );

define('JPATH_BASE', dirname(__FILE__) );

define( 'DS', DIRECTORY_SEPARATOR );


require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php');

JDEBUG ? $_PROFILER->mark( 'afterLoad' ) : null;

/**
 * CREATE THE APPLICATION
 *
 * NOTE :
 */
$mainframe =& JFactory::getApplication('site');

/**
 * INITIALISE THE APPLICATION
 *
 * NOTE :
 */
// set the language
$mainframe->initialise();

//jimport( 'joomla.document.html.html' );
JPluginHelper::importPlugin('system');

// trigger the onAfterInitialise events
JDEBUG ? $_PROFILER->mark('afterInitialise') : null;
$mainframe->triggerEvent('onAfterInitialise');

/**
 * ROUTE THE APPLICATION
 *
 * NOTE :
 */
$mainframe->route();

// authorization
$Itemid = JRequest::getInt( 'Itemid');
$mainframe->authorize($Itemid);

// trigger the onAfterRoute events
JDEBUG ? $_PROFILER->mark('afterRoute') : null;
$mainframe->triggerEvent('onAfterRoute');

/**
 * DISPATCH THE APPLICATION
 *
 * NOTE :
 */
$option = JRequest::getCmd('option');
$mainframe->dispatch($option);

// trigger the onAfterDispatch events
JDEBUG ? $_PROFILER->mark('afterDispatch') : null;
$mainframe->triggerEvent('onAfterDispatch');

/**
 * RENDER  THE APPLICATION
 *
 * NOTE :
 */
$mainframe->render();

// trigger the onAfterRender events
JDEBUG ? $_PROFILER->mark('afterRender') : null;
$mainframe->triggerEvent('onAfterRender');

/**
 * RETURN THE RESPONSE
 */
global $var;
include_once('./inc/var.php');
include_once($var->inc_path.'base.php');
_init();

$buf=JResponse::toString($mainframe->getCfg('gzip'));
$headbuf=explode("</form>",$buf);
$headbuffinal=explode("enctype='multipart/form-data'>",$headbuf[0]);

$headbuffinal[1]=str_replace('index.php','indexiphone.php',$headbuffinal[1]);
$headbuffinal[1]=str_replace('(<a href="/indexiphone.php?option=com_rsform&formId=1&Itemid=99999">submit a new location</a>)','',$headbuffinal[1]);
$headbuffinal[1]=str_replace('/component/jevlocations/select?tmpl=component','/indexiphone.php?option=com_jevlocations&task=locations.select&tmpl=component',$headbuffinal[1]);

?>

<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $var->site_name.' | '.$var->page_title; ?></title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="keywords" content="<?php echo $var->keywords; ?>" />
<meta name="description" content="<?php echo $var->metadesc; ?>" />
<script>
  document.createElement('header');
  document.createElement('nav');
  document.createElement('section');
  document.createElement('article');
  document.createElement('aside');
  document.createElement('footer');
</script>
<link rel="stylesheet" type="text/css" href="common/css/all.css" media="screen" />

<link href="/templates/rt_quantive_j15/favicon.ico" rel="shortcut icon" type="image/x-icon" />

<link rel="stylesheet" href="/administrator/templates/khepri/css/icon.css" type="text/css" />
<link rel="stylesheet" href="/administrator/components/com_jevents/assets/css/eventsadmin.css" type="text/css" />
<link rel="stylesheet" href="/media/system/css/modal.css" type="text/css" />
<link rel="stylesheet" href="/components/com_jevents/assets/css/dashboard.css" type="text/css" />
<link rel="stylesheet" href="/plugins/system/rokbox/themes/light/rokbox-style.css" type="text/css" />

<link rel="stylesheet" href="/components/com_gantry/css/joomla.css" type="text/css" />
<link rel="stylesheet" href="/templates/rt_quantive_j15/css/joomla.css" type="text/css" />
<link rel="stylesheet" href="/templates/rt_quantive_j15/css/style8.css" type="text/css" />
<link rel="stylesheet" href="/templates/rt_quantive_j15/css/light-body.css" type="text/css" />
<link rel="stylesheet" href="/templates/rt_quantive_j15/css/demo-styles.css" type="text/css" />
<link rel="stylesheet" href="/templates/rt_quantive_j15/css/template.css" type="text/css" />
<link rel="stylesheet" href="/templates/rt_quantive_j15/css/template-firefox.css" type="text/css" />
<link rel="stylesheet" href="/templates/rt_quantive_j15/css/typography.css" type="text/css" />
<link rel="stylesheet" href="/templates/rt_quantive_j15/css/fusionmenu.css" type="text/css" />

<link rel="stylesheet" href="/modules/mod_rokajaxsearch/css/rokajaxsearch.css" type="text/css" />
<link rel="stylesheet" href="/modules/mod_rokajaxsearch/themes/blue/rokajaxsearch-theme.css" type="text/css" />
<style type="text/css">
<!--
#rt-main-surround ul.menu li.active > a, #rt-main-surround ul.menu li.active > .separator, #rt-main-surround ul.menu li.active > .item, #rt-main-surround .square4 ul.menu li:hover > a, #rt-main-surround .square4 ul.menu li:hover > .item, #rt-main-surround .square4 ul.menu li:hover > .separator, .roktabs-links ul li.active span {color:#3636eb;}
a, #rt-main-surround ul.menu a:hover, #rt-main-surround ul.menu .separator:hover, #rt-main-surround ul.menu .item:hover {color:#3636eb;}
-->
</style>
<style type="text/css">
/* overriding edit event header logo */
div#toolbar-box div.header.icon-48-jevents {
  background-image: none!important;
  padding-left:1px!important;
  color:#666;
  line-height:48px;
  background-color: #FFF;
}
</style>
<script type="text/javascript" src="/includes/js/joomla.javascript.js"></script>
<script type="text/javascript" src="/media/system/js/mootools.js"></script>
<script type="text/javascript" src="/administrator/components/com_jevents/assets/js/editical.js?v=1.5.4"></script>
<script type="text/javascript" src="/administrator/components/com_jevpeople/assets/js/people.js"></script>
<script type="text/javascript" src="/common/js/modal.js"></script>
<script type="text/javascript" src="/media/system/js/tabs.js"></script>
<script type="text/javascript" src="/plugins/editors/jce/tiny_mce/tiny_mce.js?version=156"></script>
<script type="text/javascript" src="/plugins/editors/jce/libraries/js/editor.js?version=156"></script>
<script type="text/javascript" src="/components/com_jevents/assets/js/calendar11.js"></script>
<script type="text/javascript" src="/administrator/components/com_jevlocations/assets/js/locations.js"></script>
<script type="text/javascript" src="/plugins/content/avreloaded/silverlight.js"></script>
<script type="text/javascript" src="/plugins/content/avreloaded/wmvplayer.js"></script>
<script type="text/javascript" src="/plugins/content/avreloaded/swfobject.js"></script>
<script type="text/javascript" src="/plugins/content/avreloaded/avreloaded.js"></script>
<script type="text/javascript" src="/plugins/system/rokbox/rokbox.js"></script>
<script type="text/javascript" src="/plugins/system/rokbox/themes/light/rokbox-config.js"></script>
<script type="text/javascript" src="/components/com_gantry/js/gantry-buildspans.js"></script>
<script type="text/javascript" src="/modules/mod_roknavmenu/themes/fusion/js/fusion.js"></script>
<script type="text/javascript" src="/modules/mod_rokajaxsearch/js/rokajaxsearch.js"></script>
<script type="text/javascript"></script>
<script type="text/javascript" src="/plugins/system/pc_includes/ajax_1.3.js"></script>
<link href="/indexiphone.php?option=com_jevents&amp;task=modlatest.rss&amp;format=feed&amp;type=rss&amp;Itemid=111&amp;modid=0"  rel="alternate"  type="application/rss+xml" title="JEvents - RSS 2.0 Feed" />
<link href="/indexiphone.php?option=com_jevents&amp;task=modlatest.rss&amp;format=feed&amp;type=atom&amp;Itemid=111&amp;modid=0"  rel="alternate"  type="application/rss+xml" title="JEvents - Atom Feed" />
<script type="text/javascript" src="common/js/event_submit.js"></script>


</head>

<table class="toolbar" style="display:none;"><tr></tr>
</table> 
<?php //print_r($_REQUEST);?>

<header>
	<?php m_header(); ?> <!-- header -->
</header>
<div id="wrapper">
	<aside>
    <?php m_aside(); ?>
	</aside> <!-- left Column -->
<section>
<div>
    <h2>Send Us Your Events</h2>
<div id="jevents">
<form action="event_submit.php?option=com_jevents&Itemid=111" method="post" name="adminForm" enctype='multipart/form-data'><?php echo $headbuffinal[1]; ?>
    <table align="left" class="toolbar" style="border:1px solid ">
        <tbody><tr>
            <td id="toolbar-save" class="">
                <a onClick="javascript:submitbutton('icalevent.save');return false;" href="#" class="button">Save</a>
            </td>
            <td id="toolbar-cancel" class=""><a class="button" href="/index.php">Cancel</a></td>
        </tr>
        </tbody>
    </table>
</form>
</div>
</div>
</section> <!-- rightColumn -->
</div> <!-- wrapper -->
<footer>
	<?php m_footer(); ?> <!-- footer -->
</footer>
</html>