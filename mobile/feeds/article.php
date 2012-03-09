<?php

$id = (int)$_GET['id'];

$query             = "SELECT * FROM jos_content WHERE id=".$id;

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

$row = mysql_fetch_object($result);
$introtext = str_replace('src="images/', 'src="'.$jconfig->live_site.'/images/',$row->introtext);
?>
<div class="content" style="margin:0px auto; width:80%;">
<h3><?php echo $row->title; ?></h3>
<p>
<?php echo $introtext; ?>
</p>
</div>