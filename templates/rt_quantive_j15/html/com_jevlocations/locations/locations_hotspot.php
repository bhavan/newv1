<?php defined('_JEXEC') or die('Restricted access'); 

if (false && !JRequest::getInt("iphoneapp",0)){
	// revert to main layout if not called from iphone
	$maintemplate = JPath::find(JPATH_SITE."/components/com_jevlocations/views/locations/tmpl/","locations.php");
	if ($maintemplate){
		include($maintemplate);
		return;
	}
}

$filter_loccat = JRequest::getInt("filter_loccat",0);
$db = JFactory::getDBO();
$db->setQuery("SELECT * FROM #__categories WHERE section='com_jevlocations2' AND published=1 AND id=".$filter_loccat." ORDER BY title");
$cat = $db->loadObject();
if (!$cat) die();
ob_end_clean();

header('Content-type: text/xml', true);
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<Category id="'.$filter_loccat.'" name="'.htmlspecialchars($cat->title,ENT_QUOTES).'">';

$rank = 0;
if (count($this->items)) foreach ($this->items as $item) {
	$rank++;
   echo '<HotSpot id="'.$item->loc_id.'" name="'.htmlspecialchars($item->title,ENT_QUOTES).'" rank="'.$rank.'" bRestaurant="1" details="'.$item->loc_id.'" />';
}
?>
</Category>
<?php
die();