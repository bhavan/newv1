<?php
//create the database tables

//use joomla db class
require_once("configuration.php");
$jconfig = new JConfig();
//db establish
$db_error = "I am sorry! We are maintaining the website, please try again later.";
$db_config = mysql_connect( $jconfig->host, $jconfig->user, $jconfig->password ) or die( $db_error );
mysql_select_db( $jconfig->db, $db_config ) or die( $db_error );

$sqldrop = 'DROP TABLE IF EXISTS `jos_jevents_colors`';
mysql_query($sqldrop);

$sqlcreate = 'CREATE TABLE IF NOT EXISTS `jos_jevents_colors` (
  `id` int(11) NOT NULL auto_increment,
  `colour` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
)';
mysql_query($sqlcreate);

$sqlinsert = "INSERT INTO `jos_jevents_colors` (`id`, `colour`) VALUES
(1, 'red'),
(2, 'blue'),
(3, 'yellow'),
(4, 'green'),
(5, 'pink'),
(6, 'purple'),
(7, 'orange')";

mysql_query($sqlinsert);


$sqldropmarker = 'DROP TABLE IF EXISTS `jos_jevents_markers`';

mysql_query($sqldropmarker);

$sqlmarker = 'CREATE TABLE IF NOT EXISTS `jos_jevents_markers` (
  `id` int(11) NOT NULL auto_increment,
  `catid` int(11) NOT NULL,
  `colour` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `catid` (`catid`)
)';

mysql_query($sqlmarker);

?>