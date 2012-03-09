<?php
/**
* @copyright	Copyright (C) 2008 GWE Systems Ltd. All rights reserved.
 * @license		By negoriation with author via http://www.gwesystems.com
*/
ini_set("display_errors",0);

list($usec, $sec) = explode(" ", microtime());
define('_SC_START', ((float)$usec + (float)$sec));

// Set flag that this is a parent file
define( '_JEXEC', 1 );
define( 'DS', DIRECTORY_SEPARATOR );
$x = realpath(dirname(__FILE__)."/../../") ;
// SVN version
if (!file_exists($x.DS.'includes'.DS.'defines.php')){
	$x = realpath(dirname(__FILE__)."/../../../") ;

}
define( 'JPATH_BASE', $x );

ini_set("display_errors",0);

require_once JPATH_BASE.DS.'includes'.DS.'defines.php';
require_once JPATH_BASE.DS.'includes'.DS.'framework.php';

global $mainframe;
$mainframe =& JFactory::getApplication('site');
$mainframe->initialise();

$params = JComponentHelper::getParams("com_shines");
$article = intval($params->get("aboutarticle",1));

$db = JFactory::getDBO();
$db->setQuery("SELECT * FROM #__content where id=$article");
$content = $db->loadObject();


$uri = JURI::getInstance();
$root = $uri->toString( array('scheme', 'host', 'port') );

header('Content-type: text/html;charset=utf-8', true);
?>
<html>
<head>
	<base href="<?php echo $root;?>" />
	
	<title>30A today</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
	<style>
		html, body, td {
			font-family:verdana, Helvetica, sans-serif;
			font-size:12px;
			color:#333333;
		}
	</style>
</head>
<body>
  <table align="center" width="300">
    <tr>
      <td>
<?php
echo $content->introtext.$content->fulltext ;
?>
	</td>
    </tr>
  </table>
</body>
</html>
<?php
/*
$redirect = "/indexiphone.php?option=com_content&view=article&tmpl=component&id=".);

$redirect .= "&iphoneapp=1";

header( 'HTTP/1.1 303 Temporary Redirect' );
header( 'Location: ' . $redirect );
*/