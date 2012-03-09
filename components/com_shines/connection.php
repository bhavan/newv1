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
?>