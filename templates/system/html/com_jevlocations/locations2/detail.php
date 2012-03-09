<?php defined('_JEXEC') or die('Restricted access'); 
?><div style="margin:3px;">

<style>
/* pull details fields to the top */
.adminform { vertical-align:top;}
</style>

<!--- Facebook javascript --->
<div id="fb-root"></div>
<script>
  window.fbAsyncInit = function() {
    FB.init({appId: 'your app id', status: true, cookie: true,
             xfbml: true});
  };
  (function() {
    var e = document.createElement('script'); e.async = true;
    e.src = document.location.protocol +
      '//connect.facebook.net/en_US/all.js';
    document.getElementById('fb-root').appendChild(e);
  }());
</script>

<meta property="og:title" content="<?php echo $this->location->title ?>"/>

<table width="670" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td colspan="2" class="module-title"><?php echo "<h3 class=\"title\">".$this->location->title. "</h3>" ?> <fb:like show_faces="false"></fb:like></td>
  </tr>
  <tr>
  <td class="adminform" valign="top"><fieldset class="adminform">
	<?php 
	$compparams = JComponentHelper::getParams("com_jevlocations");
	$usecats = $compparams->get("usecats",0);
	if ($usecats){
		echo $this->location->street."<br/>";
		echo $this->location->category."<br/><br/>";
	}
	else {
		if (strlen($this->location->phone)>0) echo "<b>Phone: </b>" . $this->location->phone."<br><br>";
			if (strlen($this->location->url)>0) {
			$pattern = '[a-zA-Z0-9&?_.,=%\-\/]';
			if (strpos($this->location->url,"http://")===false) $this->location->url = "http://".trim($this->location->url);
			$this->location->url = preg_replace('#(http://)('.$pattern.'*)#i', '<a href="\\1\\2" target="_blank">\\1\\2</a>', $this->location->url);
			echo "<b>Website: </b>" . $this->location->url."<br><br>";
			}

		if (strlen($this->location->street)>0) echo "<b>Address: </b><br>" . $this->location->street."<br>";
		if (strlen($this->location->city)>0) echo $this->location->city.", ";
		if (strlen($this->location->state)>0) echo $this->location->state." ";
		if (strlen($this->location->postcode)>0) echo $this->location->postcode."<br/>";
	}

?>
</fieldset>
</td>
 <td align="right" valign="top">
    <fieldset class="adminform">
	<br><div id="gmap" style="width: 300px; height: 200px"></div>
	</fieldset>
</td>
  </tr>
  <tr>
    <td colspan="2"><fieldset class="adminform"><b><?php  echo JText::_( 'Description' ) . ": </b><br>" . $this->location->description; ?></fieldset><br /><br /></td>
  </tr>
</table>


<?php
if (JRequest::getInt("se",0)){
?>
<fieldset class="adminform">
	<?php echo "<b>" . JText::_( 'JEV UPCOMING EVENTS' ) . ": <b><br><br>"; ?>
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