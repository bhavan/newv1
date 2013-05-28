<?php
/**
* @copyright	Copyright (C) 2008 GWE Systems Ltd. All rights reserved.
 * @license		By negoriation with author via http://www.gwesystems.com
*/
//ini_set("display_errors",0);

require_once($_SERVER['DOCUMENT_ROOT']."/configuration.php");
$jconfig = new JConfig();

/* define(DB_HOST, $jconfig->host);
define(DB_USER,$jconfig->user);
define(DB_PASSWORD,$jconfig->password);
define(DB_NAME,$jconfig->db);
$conn=mysql_connect(DB_HOST,DB_USER,DB_PASSWORD) or die(mysql_error());
$db=mysql_select_db(DB_NAME) or die(mysql_error());*/ 

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
$x = realpath(dirname(__FILE__)."/../../") ;
// SVN version
if (!file_exists($x.DS.'includes'.DS.'defines.php')){
	$x = realpath(dirname(__FILE__)."/../../../") ;

}
define( 'JPATH_BASE', $x );

@ini_set("display_errors",0);

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
$todaydate = date("Y-m-j",strtotime("+1 day"));
$today = date("Y-m-d G:i:s");
$root = $uri->toString( array('scheme', 'host', 'port') );
$sql = "select jc.* from `jos_content` jc, `jos_categories` jcs where jcs.title = 'Today' and jcs.id = jc.catid and jc.state=1 and (jc.publish_down>'".$today."' or jc.publish_down='0000-00-00 00:00:00') and (jc.publish_up <= '".$todaydate."' or jc.publish_up='0000-00-00 00:00:00') order by jc.ordering";
  $param = db_fetch($sql, true, true);
  //fprint($sql);
  //fprint($param);
  $data = '';
	$c=1;
  if($param) { foreach($param as $v) {
  
  preg_match_all ("/(<img.*?>)/i" , $v['introtext'] , $matches);
	foreach($matches[1] as $m) {
		$v['introtext']=str_replace($m, "<li style='text-align:center;'><div>$m</div>",$v['introtext']);
	}

      $v['introtext'] = ''.$v['introtext'].'</li>';

	$t1='<div class="contentheading">'.$v['title'].'</div>' ;
    $data .=$t1.str_replace("images/", "images/", $v['introtext']);
		$c++;
  } }
header('Content-type: text/html;charset=utf-8', true);
include("iadbanner.php"); 
include("connection.php");
?>
<html>
<head>
<link href="/components/com_shines_v2.1/css/style.css" rel="stylesheet" media="screen" type="text/css" />

<title>
<?php echo $site_name.' | ';
echo ($_SESSION['tpl_folder_name'] == 'defaultspanish')?'Noticias':'News';?>
</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
 <?php include($_SERVER['DOCUMENT_ROOT']."/ga.php"); ?>
</head>
<body>
 
  <div id="main" role="main">
  <?php
$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
if(stripos($ua,'android') == true) { ?>
	<div id="zigzag" style="vertical-align:bottom;">
		<?php m_show_banner('android-news-screen'); ?>
	</div>
  <?php } 
  else {
  ?>
  <div id="zigzag" style="vertical-align:bottom;">
    <?php m_show_banner('iphone-news-screen'); ?>
  </div>
  <?php } ?>
	<ul id="placesList" class="mainList offer">
		<?php
		echo $data ;
		?>
	</ul>
  </div>
</body>
</html>
