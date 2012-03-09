<?php defined('_JEXEC') or die('Restricted access'); 
?><div style="margin:3px;"><?php
echo "<h3>".$this->location->title. "</h3>"?>

<fieldset class="adminform">
	<legend><?php echo JText::_( 'Description' ); ?></legend>
	<?php 
	$compparams = JComponentHelper::getParams("com_jevlocations");
	$usecats = $compparams->get("usecats",0);
	if ($usecats){
		echo $this->location->street."<br/>";
		echo $this->location->category."<br/><br/>";
	}
	else {
		if (strlen($this->location->street)>0) echo $this->location->street."<br/>";
		if (strlen($this->location->city)>0) echo $this->location->city."<br/>";
		if (strlen($this->location->state)>0) echo $this->location->state."<br/>";
		if (strlen($this->location->postcode)>0) echo $this->location->postcode."<br/>";
		if (strlen($this->location->country)>0) echo $this->location->country."<br/>";
	}

	if (strlen($this->location->phone)>0) echo $this->location->phone."<br/>";
	if (strlen($this->location->url)>0) {
		$pattern = '[a-zA-Z0-9&?_.,=%\-\/]';
		if (strpos($this->location->url,"http://")===false) $this->location->url = "http://".trim($this->location->url);
		$this->location->url = preg_replace('#(http://)('.$pattern.'*)#i', '<a href="\\1\\2" target="_blank">\\1\\2</a>', $this->location->url);
		echo $this->location->url."<br/>";
	}
	if ($this->location->image!=""){
		// Get the media component configuration settings
		$params =& JComponentHelper::getParams('com_media');
		// Set the path definitions
		$mediapath =  JURI::root(true).'/'.$params->get('image_path', 'images/stories');

		// folder relative to media folder
		$locparams = JComponentHelper::getParams("com_jevlocations");
		$folder = "jevents/jevlocations";
		$thimg = '<img src="'.$mediapath.'/'.$folder.'/thumbnails/thumb_'.$this->location->image.'" />' ;
		$img = '<img src="'.$mediapath.'/'.$folder.'/'.$this->location->image.'" />' ;
		echo $thimg."<br/>";
	}

	echo $this->location->description;

	// New custom fields
	JLoader::register('JevCfParameter',JPATH_SITE."/plugins/jevents/customfields/jevcfparameter.php");
	$compparams = JComponentHelper::getParams("com_jevlocations");
	$template = $compparams->get("template","");
	if ($template!=""){
		$html = "";
		$xmlfile = JPATH_SITE."/plugins/jevents/customfields/templates/".$template;
		if (file_exists($xmlfile)){
			$db = JFactory::getDBO();
			$db->setQuery("SELECT * FROM #__jev_customfields3 WHERE target_id=".intval($this->location->loc_id). " AND targettype='com_jevlocations'");
			$customdata = $db->loadObjectList();

			$jcfparams = new JevCfParameter($customdata,$xmlfile,  $this->location);
			$customfields = $jcfparams->renderToBasicArray();
		}
		$templatetop = $compparams->get("templatetop","<table border='0'>");
		$templaterow = $compparams->get("templatebody","<tr><td class='label'>{LABEL}</td><td>{VALUE}</td>");
		$templatebottom = $compparams->get("templatebottom","</table>");

		$row->customfields = $customfields;
		$html = $templatetop;
		$user = JFactory::getUser();
		foreach ($customfields as $customfield) {
			if ($user->aid < intval($customfield["access"])) continue;
			if (!is_null($customfield["hiddenvalue"]) && trim($customfield["value"])==$customfield["hiddenvalue"]) continue;
			$outrow = str_replace("{LABEL}",$customfield["label"],$templaterow);
			$outrow = str_replace("{VALUE}",nl2br($customfield["value"]),$outrow );
			$html .= $outrow ;
		}
		$html .= $templatebottom;

		echo $html;
	}

	?>
</fieldset>

<fieldset class="adminform">
	<legend><?php echo JText::_( 'Google Map' ); ?></legend>
	<?php echo JText::_( 'Click Map' ); ?><br/><br/>
	<div id="gmap" style="width: 450px; height: 300px"></div>
</fieldset>

<?php
if (JRequest::getInt("se",0)){
?>
<fieldset class="adminform">
	<legend><?php echo JText::_( 'JEV UPCOMING EVENTS' ); ?></legend>
	<?php
	require_once (JPATH_SITE."/modules/mod_jevents_latest/helper.php");

	$jevhelper = new modJeventsLatestHelper();
	$theme = JEV_CommonFunctions::getJEventsViewName();

	JPluginHelper::importPlugin("jevents");
	$viewclass = $jevhelper->getViewClass($theme, 'mod_jevents_latest',$theme.DS."latest", $compparams);

	// record what is running - used by the filters
	$registry	=& JRegistry::getInstance("jevents");
	$registry->setValue("jevents.activeprocess","mod_jevents_latest");
	$registry->setValue("jevents.moduleid", "cb");

	$menuitem = intval($compparams->get("targetmenu",0));
	if ($menuitem>0){
		$compparams->set("target_itemid",$menuitem);
	}
	// ensure we use these settings
	$compparams->set("modlatest_useLocalParam",1);
	// disable link to main component
	$compparams->set("modlatest_LinkToCal",0);

	$registry->setValue("jevents.moduleparams", $compparams);

	$loclkup_fv = JRequest::setVar("loclkup_fv",$this->location->loc_id);
	$modview = new $viewclass($compparams, 0);
	echo $modview->displayLatestEvents();
	JRequest::setVar("loclkup_fv",$loclkup_fv);

	echo "<br style='clear:both'/>";

	$task = $compparams->get("view",",month.calendar");
	$link = JRoute::_("index.php?option=com_jevents&task=$task&loclkup_fv=".$this->location->loc_id."&Itemid=".$menuitem);

	echo "<strong>".JText::sprintf("JEV ALL EVENTS",$link)."</strong>";
	?>
</fieldset>
<?php
}
?>
</div>