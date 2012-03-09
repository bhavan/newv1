<?php defined('_JEXEC') or die('Restricted access'); ?>
<div class='jevpersondetail'>
	<h3><?php echo $this->person->title;?></h3>
	
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'Description' ); ?></legend>
		<?php 
		$compparams = JComponentHelper::getParams("com_jevpeople");
		if (strlen($this->person->image)>0) {

			// Get the media component configuration settings
			$params =& JComponentHelper::getParams('com_media');
			// Set the path definitions
			$mediapath =  JURI::root(true).'/'.$params->get('image_path', 'images/stories');

			echo '<img src="'.$mediapath.'/jevents/jevpeople/'.$this->person->image.'" alt="'.htmlspecialchars($this->person->imagetitle).'"/>';
		}
		if ($this->perstype->showaddress>0) {
			if (strlen($this->person->street)>0) echo $this->person->street."<br/>";
			if (strlen($this->person->city)>0) echo $this->person->city."<br/>";
			if (strlen($this->person->state)>0) echo $this->person->state."<br/>";
			if (strlen($this->person->postcode)>0) echo $this->person->postcode."<br/>";
			if (strlen($this->person->country)>0) echo $this->person->country."<br/>";
		}
		echo $this->person->description;
		echo "<br/>";
		if (strlen($this->person->www)>0) echo "www: <a href='".$this->person->www."' target='_blank' >".$this->person->title."</a>";


		// New custom fields
		JLoader::register('JevCfParameter',JPATH_SITE."/plugins/jevents/customfields/jevcfparameter.php");
		$compparams = JComponentHelper::getParams("com_jevpeople");
		$template = $compparams->get("template","");
		if ($template!=""){
			$html = "";
			$xmlfile = JPATH_SITE."/plugins/jevents/customfields/templates/".$template;
			if (file_exists($xmlfile)){
				$db = JFactory::getDBO();
				$db->setQuery("SELECT * FROM #__jev_customfields2 WHERE target_id=".intval($this->person->pers_id). " AND targettype='com_jevpeople'");
				$customdata = $db->loadObjectList();

				$jcfparams = new JevCfParameter($customdata,$xmlfile,  $this->person);
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
	<?php if ($this->perstype->showaddress>0) {?>
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'Google Map' ); ?></legend>
		<?php echo JText::_( 'Click Map' ); ?><br/><br/>
		<div id="gmap" style="width: 450px; height: 300px"></div>
	</fieldset>
	<?php } 


	?>
</div>