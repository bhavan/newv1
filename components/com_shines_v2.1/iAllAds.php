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

// use the default layout for the iphone app
setcookie("jevents_view","default",null,"/");
JRequest::setVar("iphoneapp",1);

$script = $_SERVER['REQUEST_URI'];
$urlparts = parse_url($_SERVER['REQUEST_URI']);

$parts = pathinfo($urlparts["path"]);

$filename = $parts["filename"];

$action = "iAllAds";


$Str_Cat = $_REQUEST['screen'];
/**
	 * This gives us the screen names and phone chooses one at random for home page
	 */

$db = JFactory::getDBO();
$db->setQuery("SELECT b.*, cat.title as catname FROM #__banner as b LEFT JOIN #__categories as cat ON cat.id=b.catid 
AND LOWER(cat.title)='".$Str_Cat."' where b.showBanner=1 AND (b.imptotal=0 OR b.impmade<b.imptotal) AND LOWER(cat.title) IN ('screen','restaurants','Main','Map','event','weather','photos','Videos','Places') ORDER BY RAND()");
$ads = $db->loadObjectList();


/* setting category to main and executing the qury once again if loadObjectList is Empty */

if (empty($ads))
{
$Str_Cat	=	'main';

$db->setQuery("SELECT b.*, cat.title as catname FROM #__banner as b LEFT JOIN #__categories as cat ON cat.id=b.catid 
AND LOWER(cat.title)='".$Str_Cat."' where b.showBanner=1 AND (b.imptotal=0 OR b.impmade<b.imptotal) AND LOWER(cat.title) IN ('screen','restaurants','Main','Map','event','weather','photos','Videos','Places') ORDER BY RAND()");
$ads = $db->loadObjectList();
}



header('Content-type: text/xml', true);
echo '<?xml version="1.0" encoding="UTF-8"?>';


echo '<AllAds>';
$bits = parse_url( JURI::root()."../../");
$root = $bits["scheme"]."://".$bits["host"]."/".realpath($bits["path"]);

// Use Category in place of screen name
if ($ads) foreach ($ads as $ad)
{
	if($ad->bid)
	{
		// for Impressions: track the number of times the banner is displayed to web site visitors.
		$sql = 'UPDATE jos_banner SET impmade = impmade + 1 WHERE bid =' .$ad->bid;
		$db->execute($sql);
	}
		
	$szClickUrl = $ad->clickurl;
	$findme   = ':';
	$pos = strpos($szClickUrl, $findme);

	$szType = mb_substr ($szClickUrl, 0, $pos);
	
	$szBannerCode = $ad->custombannercode."";
	

	
	if($szBannerCode != "")
	{
	$szType = "adsense";
		
	$url = $szBannerCode;
	}
	else
	{	
	if ($szType == "tel" || $szType == "mailto")
           {
           $url =$szClickUrl;
           }
       else
          {
          $szType = "URL";
        
         $url = $root."indexiphone.php?option=com_banners&task=click&bid=".$ad->bid;
         }
	
	}
	
/* Declaring Partner folder name for Banner using str_replace function :  Yogi */
//	$without_www = str_replace( 'www.', '', $_SERVER["SERVER_NAME"] );
//	$partnername = str_replace( '.com', '', $without_www );
/* Code End */

/* Declaring Partner folder name from master tables:  Yogi */
	// Code for retriving the Current Page url from the browser
//	$pageURL = $_SERVER["HTTP_HOST"]; 
		
	// Connetion with Master DB to retrive Slave DB informaiton
// 	$link = mysql_pconnect("localhost","root","bitnami");
//	$dblink = mysql_select_db("master");
//	$queryMaster = "SELECT partner_folder_name FROM master WHERE site_url LIKE '$pageURL'";
		
//	$result = mysql_query($queryMaster);
//	$row = mysql_fetch_array($result);
	
//	$partnername = $row['partner_folder_name'];
/* Code End */

/* Retriving <Partner Folder Name> from <Configuration.php> */	

// Including configuration file
require_once("../../configuration.php");

// Declaring Object of JConfig Class
$jconfig = new JConfig();

// Assigning Partner Folder Name to $partnerName variable
$partnerName = $_SESSION['partner_folder_name'];


	echo '<Ad id="'.$ad->bid.'" screen="'.$ad->catname.'" image="'.($root.'partner/'.$partnerName.'/images/banners/'.$ad->imageurl).'" type="'.$szType.'"

	details="'.htmlspecialchars($url).'" />';
	}

echo '</AllAds>';