<?php
/*
define(DB_HOST,'localhost');
define(DB_USER,'destinsh_read');
define(DB_PASSWORD,'readonly');
define(DB_NAME,'destinsh_destinjoomla');
$conn=mysql_connect(DB_HOST,DB_USER,DB_PASSWORD) or die(mysql_error());
$db=mysql_select_db(DB_NAME) or die(mysql_error());
*/
require_once("../../configuration.php");
$jconfig = new JConfig();
define(DB_HOST,$jconfig->host);
define(DB_USER,$jconfig->user);
define(DB_PASSWORD,$jconfig->password);
define(DB_NAME,$jconfig->db);
$conn=mysql_connect(DB_HOST,DB_USER,DB_PASSWORD) or die(mysql_error());
$db=mysql_select_db(DB_NAME) or die(mysql_error());

$rec = mysql_query("select * from `jos_pageglobal`");
$pageglobal=mysql_fetch_array($rec);

$gmapkeys=explode('googlemapskey=',$pagejevent['params']);
$gmapkeys1=explode("\n",$gmapkeys[1]);
$site_name = $pageglobal['site_name'];
$beach = $pageglobal['beach'];
$email = $pageglobal['email'];
$googgle_map_api_keys = $gmapkeys1[0];
$location_code = $pageglobal['location_code'];

?>