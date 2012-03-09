<?php
global $var;
include_once('./inc/var.php');
include_once($var->inc_path.'base.php');
_init();

if (!isset($_REQUEST['bid']))
header("Location: index.php");

$id=$_REQUEST['bid'];
$query = 'SELECT clickurl, bid FROM jos_banner' .
			' WHERE bid = ' . (int) $id;
			
$rec=mysql_query($query);
$row=mysql_fetch_array($rec);
$url=$row['clickurl'];
if (strpos('aaa'.$url,'mailto:') || strpos('aaa'.$url,'tel:'))
$url=$url;
else
{
if (!preg_match( '#http[s]?://|index[2]?\.php#', $url ))
		{
			$url = "http://$url";
		}
}
$query_update = 'UPDATE jos_banner' .
' SET clicks = ( clicks + 1 )' .
' WHERE bid = ' . (int)$id;
$rec_int=mysql_query($query_update);
header("Location: $url");

?>