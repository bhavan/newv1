<?php 
defined('_JEXEC') or die('Restricted access');

$cfg	 = & JEVConfig::getInstance();

$view =  $this->getViewName();

echo "<div id='cal_title'>".JText::_('JEV_ICAL_EXPORT')."</div>\n";

?>

<form name="ical" method="post">
<?php
$categories = JEV_CommonFunctions::getCategoryData();

$accessiblecats = explode(",",$this->datamodel->accessibleCategoryList());

echo "<p>".JText::_('JEV_EVENT_CHOOSE_CATEG')."<br>\n";
foreach ($categories AS $c) {
	// Make sure the user is authorised to view this category and the menu item doesn't block it!
	if (!in_array($c->id, $accessiblecats)) continue;
	$cb ="<input name=\"categories[]\" value=\"".$c->id."\" type=\"checkbox\"";
	if (!isset($_POST['categories'])){
		$cb=$cb." CHECKED";
	}
	else if (isset($_POST['categories']) && in_array($c->id,JRequest::getVar('categories','','POST'))) {
		$cb=$cb." CHECKED";
	}
	$cb= $cb."><span style=\"background:".$c->color."\">&nbsp;&nbsp;&nbsp;&nbsp;</span> ".$c->title."<br>\n";
	echo $cb;
}

echo "</p><p>".JText::_('JEV_REP_YEAR')."<br>\n";
//consturc years array, easy to add own kind of selection
$year = array(date('Y'),date('Y')+1);

foreach ($year AS $y) {
	$yt="<input name=\"years[]\" type=\"checkbox\" value=\"".$y."\"";
	if (!isset($_POST['years'])){
		$yt=$yt." CHECKED";
	}
	else if (isset($_POST['years']) && in_array($y,JRequest::getVar('years','','POST'))) {
		$yt=$yt." CHECKED";
	}
	$yt= $yt.">".$y."<br>\n";
	echo $yt;
}

echo "<input type=\"submit\" name=\"submit\" value=\"".JText::_('JEV SELECT')."\">";

?>
</form>

<?php 
if (isset($_POST['submit'])) {

	$categories = JRequest::getVar('categories','','POST');
	
	$cats = array(0);
	foreach ($categories AS $cid) {
		$cid = intval($cid);
		// Make sure the user is authorised to view this category and the menu item doesn't block it!
		if (!in_array($cid, $accessiblecats)) continue;
		$cats[]=$cid;
	}

	//$years  = str_replace(",","|",JEVHelper::forceIntegerArray(JRequest::getVar('years','','POST'),true));
	//$cats = implode("|",$cats);
	$years  = JEVHelper::forceIntegerArray(JRequest::getVar('years','','POST'),true);
	$cats = implode(",",$cats);
	
	
	$link = JURI::root()."index.php?option=com_jevents&task=icals.export&format=ical";
	if (count($cats)>0) {
		$link .="&catids=".$cats;
	}
	if ($years != 0) {
		$link .="&years=".$years;
	}
	
	$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
	$icalkey = $params->get("icalkey","secret phrase");
	$publiclink = $link. "&k=".md5($icalkey . $cats . $years);
	
	$user = JFactory::getUser();
	if ($user->id!=0){
		$privatelink = $link. "&pk=".md5($icalkey . $cats . $years . $user->password . $user->username . $user->id)."&i=".$user->id;
	}
	
	echo "<p><a href='$publiclink'>".JText::_('JEV_REP_ICAL_PUBLIC')."</a></p>";
	if ($user->id!=0){
		echo "<p><a href='$privatelink'>".JText::_('JEV_REP_ICAL_PRIVATE')."</a></p>";
	}
}
