<!--
* Nice Social Bookmark Module for Joomla 1.5
* @ Version 1.0
* @ Copyright 2009 by Nikola Biskup
* @ All rights reserved
* @ http://www.salamander-studios.com
-->

<?php
// no direct access
//defined( '_VALID_MOS' ) or die( 'Restricted access' );
$url = "http://".$_SERVER['HTTP_HOST'] . getenv('REQUEST_URI'); 
$isize = $params->get('isize');
$iset = $params->get('iset');
$iposition = $params->get('iposition');
$document = JFactory::getDocument();
$document->addStyleSheet('modules/mod_nice_social_bookmark/css/nsb.css');

echo '<div class="nsb_container" align="'.$iposition.'">';
$tt = $params->get('s1', 'yes');
if ($tt == "yes")echo '<a id="l1" target="_blank" rel="nofollow" href="http://www.facebook.com/sharer.php?u='.$url.'&amp;title="><img title="Facebook" border="0" src="modules/mod_nice_social_bookmark/icons/facebook_'.$iset.'_'.$isize.'.png" alt="Facebook" /></a>&nbsp;';
$tt = $params->get('s2', 'yes');
if ($tt == "yes")echo '<a id="l2" target="_blank" rel="nofollow" href="http://www.myspace.com/Modules/PostTo/Pages/?l=3&amp;u='.$url.'&amp;title="><img title="MySpace" border="0" src="modules/mod_nice_social_bookmark/icons/myspace_'.$iset.'_'.$isize.'.png" alt="MySpace" /></a>&nbsp;';
$tt = $params->get('s3', 'yes');
if ($tt == "yes")echo '<a id="l3" target="_blank" rel="nofollow" href="http://twitter.com/home?status='.$url.'&amp;title="><img title="Twitter" border="0" src="modules/mod_nice_social_bookmark/icons/twitter_'.$iset.'_'.$isize.'.png" alt="Twitter" /></a>&nbsp;';
$tt = $params->get('s4', 'yes');
if ($tt == "yes")echo '<a id="l4" target="_blank" rel="nofollow" href="http://digg.com/submit?phase=2&amp;url='.$url.'&amp;title="><img title="Digg" border="0" src="modules/mod_nice_social_bookmark/icons/digg_'.$iset.'_'.$isize.'.png" alt="Digg" /></a>&nbsp;';
$tt = $params->get('s5', 'yes');
if ($tt == "yes")echo '<a id="l5" target="_blank" rel="nofollow" href="http://del.icio.us/post?url='.$url.'&amp;title="><img title="Delicious" border="0" src="modules/mod_nice_social_bookmark/icons/delicious_'.$iset.'_'.$isize.'.png" alt="Delicious" /></a>&nbsp;';
$tt = $params->get('s6', 'yes');
if ($tt == "yes")echo '<a id="l6" target="_blank" rel="nofollow" href="http://www.stumbleupon.com/submit?url='.$url.'&amp;title="><img title="Stumbleupon" border="0" src="modules/mod_nice_social_bookmark/icons/stumbleupon_'.$iset.'_'.$isize.'.png" alt="Stumbleupon" /></a>&nbsp;';
$tt = $params->get('s7', 'yes');
if ($tt == "yes")echo '<a id="l7" target="_blank" rel="nofollow" href="http://www.google.com/bookmarks/mark?op=edit&amp;bkmk='.$url.'&amp;title="><img title="Google Bookmarks" border="0" src="modules/mod_nice_social_bookmark/icons/google_'.$iset.'_'.$isize.'.png" alt="Google Bookmarks" /></a>&nbsp;';
$tt = $params->get('s8', 'yes');
if ($tt == "yes")echo '<a id="l8" target="_blank" rel="nofollow" href="'.$url.'/index.php?format=feed&amp;type=rss&amp;title="><img title="RSS Feed" border="0" src="modules/mod_nice_social_bookmark/icons/rss_'.$iset.'_'.$isize.'.png" alt="RSS Feed" /></a>&nbsp;';
echo '</div><div style="clear:both;"></div>';
?>