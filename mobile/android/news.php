<?php
function db_fetch($sql, $list = false, $all = false) {
  global $var;
  $result = array();
  $tmp = $var->tmp;
  if(isset($tmp[$sql])) {
    unset($result);
    return $tmp[$sql];
  } else {
    //echo(str_replace(substr($sql, 0, strpos($sql, "from")), "select count(*) ", $sql));
    if($list && $all == false && strpos(strtolower($sql), "limit") === false) {
      //echo str_replace(substr($sql, 0, strpos($sql, "from")), "select count(*) ", $sql);
      $tmp_qr = @mysql_query(str_replace(substr($sql, 0, strpos($sql, "from")), "select count(*) ", $sql));
      $count = @mysql_fetch_row($tmp_qr);
      if($count === false) {
        $var->row_count = array( $var->request_uri => 0);
      } else {
        $var->row_count = array( $var->request_uri => $count[0]);
      }
      if(isset($var->get['page'])) {
        $sql = $sql." limit ".(($var->get['page']-1)*10).", 10";
      } else {
        $sql = $sql." limit 0, 10";
      }
    }
    $qr = mysql_query($sql);
    if($qr !== false) {
      if(mysql_num_rows($qr) > 1) {
        while($row = mysql_fetch_assoc($qr)) {
          $result[] = $row;
        }
      } else {
        if(mysql_num_rows($qr) == 1) {
          if($list) {
            $result[] = mysql_fetch_assoc($qr);
          } elseif(mysql_num_fields($qr) ==  1) {
            $r = mysql_fetch_row($qr);
            $result = $r[0];
            unset($r);
          } else {
            $result = mysql_fetch_assoc($qr);
          }
        } else {
          $result = mysql_fetch_assoc($qr);
        }
      }
      $tmp[$sql] = $result;
    } else {
      $result = false;
    }
    $var->tmp = $tmp;
    unset($tmp);
    return $result;
  }
}
list($usec, $sec) = explode(" ", microtime());
define('_SC_START', ((float)$usec + (float)$sec));

// Set flag that this is a parent file
define( '_JEXEC', 1 );
define( 'DS', DIRECTORY_SEPARATOR );
$x = realpath(dirname(__FILE__)."/../") ;
// SVN version
if (!file_exists($x.DS.'includes'.DS.'defines.php')){
	$x = realpath(dirname(__FILE__)."/../../") ;

}
define( 'JPATH_BASE', $x );

ini_set("display_errors",0);

require_once JPATH_BASE.DS.'includes'.DS.'defines.php';
require_once JPATH_BASE.DS.'includes'.DS.'framework.php';

global $mainframe;
$mainframe =& JFactory::getApplication('site');
$mainframe->initialise();

$params = JComponentHelper::getParams("com_shines");
$article = intval($params->get("todayarticle",1));

$db = JFactory::getDBO();
$db->setQuery("SELECT * FROM #__content where id=$article");
$content = $db->loadObject();


$uri = JURI::getInstance();
$today = date("Y-m-d G:i:s");
$root = $uri->toString( array('scheme', 'host', 'port') );
 $sql = "select jc.* from `jos_content` jc, `jos_categories` jcs where jcs.title = 'Today' and jcs.id = jc.catid and jc.state=1 and (jc.publish_down>'".$today."' or jc.publish_down='0000-00-00 00:00:00') and (jc.publish_up <= '".$today."' or jc.publish_up='0000-00-00 00:00:00') order by jc.ordering";
  $param = db_fetch($sql, true, true);
  //fprint($sql);
  //fprint($param);
  $data = '';
	$c=1;
  if($param) { foreach($param as $v) {
  
      $v['introtext'] = ''.$v['introtext'].'<hr>';

    $data .= str_replace("images/", "/images/", $v['introtext']);
	
		$c++;
  } }
  
  $data = str_replace('href="', 'href="../', $data);
  $data = str_replace('../http', 'http', $data);
header('Content-type: text/html;charset=utf-8', true);
include("connection.php");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="yes" name="apple-mobile-web-app-capable" />
<meta content="index,follow" name="robots" />
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<link href="pics/homescreen.gif" rel="apple-touch-icon" />
<meta content="minimum-scale=1.0, width=device-width, maximum-scale=0.6667, user-scalable=no" name="viewport" />
<link href="css/style.css" rel="stylesheet" media="screen" type="text/css" />
<script src="javascript/functions.js" type="text/javascript"></script>
<title><?=$site_name?>
</title>
<!--<link href="pics/startup.png" rel="apple-touch-startup-image" /> -->
<meta content="destin, vacactions in destin florida, destin, florida, real estate, sandestin resort, beaches, destin fl, maps of florida, hotels, hotels in florida, destin fishing, destin hotels, best florida beaches, florida beach house rentals, destin vacation rentals for destin, destin real estate, best beaches in florida, condo rentals in destin, vacaction rentals, fort walton beach, destin fishing, fl hotels, destin restaurants, florida beach hotels, hotels in destin, beaches in florida, destin, destin fl" name="keywords" />
<meta content="Destin Florida's FREE iPhone application and website guide to local events, live music, restaurants and attractions" name="description" />
</head>

<body>

<!--Google Adsense -->

<div id="content" align="center">
	<ul class="pageitem">
		<li class="textbox">
      <?php echo $data ;?>
		</li>
	</ul>
</div>

<div id="footer">

	&copy; <?=date('Y');?> <?=$site_name?>, Inc. | <a href="mailto:<?=$email?>?subject=Feedback">Contact Us</a></div></div>
</body>

</html>
