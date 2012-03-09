<?php


################################################
#                                              #
#  SRSSS - Simple RSS Script                   #
#  Version 1.5 - 2007-02-11 Florian Beer       #
#                                              #
#  Dieses Script steht unter der MDWDW Lizenz. #
#  Die 'Mach doch was du willst' Lizenz.       #
#  Viel Spass damit!                           #
#                                              #
################################################

// RSS Setup
$title            = 'My RSS Feed';
$link            = 'http://www.destinshines.com';
$description     = 'This is the feed from my website';
$encoding        = 'iso-8859-1';
$lang            = 'en-us';

// DB Setup
//$host             = "localhost";
//$user             = "root";
//$pass             = "";
//$db                = "eam_joomla";


$query             = "SELECT * FROM jos_content WHERE sectionid=8 AND state=1 AND (publish_up='0000-00-00 00:00:00' OR publish_up<='CURRENT_DATE()') AND (publish_down='0000-00-00 00:00:00' OR publish_down>='CURRENT_DATE()') ORDER BY id DESC";

//$connection = mysql_connect($host, $user, $pass) or die ("Unable to connect!");
//mysql_select_db($db, $connection) or die ("Unable to select database!");
//$result = mysql_query($query);

//use joomla db class
require_once("../configuration.php");
$jconfig = new JConfig();
//db establish
$db_error = "I am sorry! We are maintaining the website, please try again later.";
$db_config = mysql_connect( $jconfig->host, $jconfig->user, $jconfig->password ) or die( $db_error );
mysql_select_db( $jconfig->db, $db_config ) or die( $db_error );

$result = mysql_query($query);

function convTimestamp($date)
{
    $year   = substr($date,0,4); 
    $month  = substr($date,4,2); 
    $day    = substr($date,6,2); 
    $hour   = substr($date,8,2); 
    $minute = substr($date,10,2); 
    $second = substr($date,12,2); 
    $stamp =  mktime($hour, $minute, $second, $month, $day, $year);
    return $stamp; 
}
$title = $jconfig->sitename;
//header('Content-type: application/rss+xml');
header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="'.$encoding.'"?>';
echo "\n";
echo '<rss version="2.0">';
echo "\n";
echo '<channel>';
echo "\n";
echo "\n";
echo '<title>'.$title.'</title>';
echo "\n";
echo '<link>'.$link.'</link>';
echo "\n";
echo '<description>'.$description.'</description>';
echo "\n";
echo '<lastBuildDate>'.date("D, d M Y H:i:s").' +0000</lastBuildDate>';
echo "\n";
echo '<language>'.$lang.'</language>';
echo "\n";
echo "\n";

$link = 'http://'.$_SERVER['HTTP_HOST'];

// Loop through all items
// substitute with your DB values
while($row = mysql_fetch_object($result))
{
	
	$introtext = str_replace('src="images/', 'src="'.$link.'/images/',$row->introtext);
	$stamp = convTimestamp($row->created);
	echo '<item>';
	echo "\n";
	echo '<title>'.$row->title.'</title>';
	echo "\n";
	echo '<link><![CDATA['.$link.']]></link>'; //http://www.destinshines.com/feeds/article.php?id='.$row->id
	echo "\n";
	echo '<guid><![CDATA['.$link.']]></guid>';
	echo "\n";
	echo '<pubDate>'.date("D, d M Y H:i:s", $stamp).' +0000</pubDate>';
	echo "\n";
	echo '<description><![CDATA['.$introtext.']]></description>';
	echo "\n";
	echo '</item>';
	echo "\n";
	
}

echo "\n";
echo '</channel>';
echo "\n";
echo '</rss>';
?> 