<?php defined('_JEXEC') or die('Restricted access');  

JHTML::_('behavior.tooltip');
$editor = &JFactory::getEditor();
$compparams = JComponentHelper::getParams("com_jevpeople");

$imgfolder = "jevents/jevpeople";

$jevuser = JEVHelper::getAuthorisedUser();

// Attach script to document
$document =& JFactory::getDocument();

// debug
$session = JFactory::getSession();
if ($mainframe->isAdmin()){
	$targetURL = JURI::root().'administrator/index.php?tmpl=component&folder='.$imgfolder.'&'.$session->getName().'='.$session->getId().'&'.JUtility::getToken().'=1';
}
else {
	$targetURL = JURI::root().'index.php?tmpl=component&folder='.$imgfolder.'&'.$session->getName().'='.$session->getId().'&'.JUtility::getToken().'=1';
}

$uploaderInit = "
		var oldAction = '';
		var oldTarget = '';
		var oldTask = '';
		var oldOption = '';
		function uploadFileType(field){
			form = document.adminForm;
			oldAction = form.action;
			oldTarget = form.target;
			oldTask = form.task.value;
			oldOption = form.option.value;
			form.action = '".$targetURL."&field='+field;
			
			form.target = 'uploadtarget';
			form.task.value = 'people.upload';
			form.option.value = 'com_jevpeople';
			form.submit();
			form.action = oldAction ;
			form.target = oldTarget ;
			form.task.value = oldTask ;
			form.option.value = oldOption;
			
			var loading = document.getElementById(field+'_loading');
			loading.style.display='block';
			var loaded = document.getElementById(field+'_loaded');
			loaded.style.display='none';
		}
		function setImageFileName(){			
			iframe = frames.uploadtarget;
			if(!iframe.fname) return;
			//elemname = iframe.fname.replace('_file','');
			elemname = iframe.fname.substr(0,iframe.fname.length-5);
			elem = document.getElementById(elemname);
			if (elem) elem.value = iframe.filename;
			elem = document.getElementById(elemname+'title');
			if (elem) elem.value = iframe.oname;
			elem = document.getElementById(iframe.fname);
			if (elem) elem.value = '';
			img = document.getElementById(elemname+'_img');
			img.src = '". JEVP_MEDIA_BASEURL."/$imgfolder/thumbnails/thumb_'+iframe.filename;
			img.style.display='block';
			img.style.marginRight='10px';
			
			var loading = document.getElementById(elemname+'_loading');
			loading.style.display='none';
			var loaded = document.getElementById(elemname+'_loaded');
			loaded.style.display='block';
			
		}		
		function clearImageFile(elemname){
			img = document.getElementById(elemname+'_img');
			img.src = ''
			img.style.display='none';
			img.style.marginRight='0px';
			elem = document.getElementById(elemname);
			if (elem) elem.value = '';
			elem = document.getElementById(elemname+'title');
			if (elem) elem.value = '';
		}
";
$document->addScriptDeclaration($uploaderInit);

?>

<script language="javascript" type="text/javascript">
function submitbutton(pressbutton) {
	var form = document.adminForm;
	if (pressbutton == 'people.overview' || pressbutton == 'people.cancel') {
		submitform( pressbutton );
		return;
	}

	// do field validation
	var text = <?php echo $editor->getContent( 'description' ); ?>
	if (form.title.value == ""){
		alert( "<?php echo JText::_( 'Person must have a title', true ); ?>" );
	}
	else {
		<?php
		echo $editor->save( 'description' );
		?>
		submitform( pressbutton );
	}
}
</script>
<style type="text/css">
	table.paramlist td.paramlist_key {
		width: 92px;
		text-align: left;
		height: 30px;
	}
</style>

<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<div class="col">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'Details' ); ?></legend>

		<table class="admintable">
		<tr>
			<td width="100" align="right" class="key">
				<label for="type_id">
					<?php echo JText::_( 'Person Type' ); ?>:
				</label>
			</td>
			<td>
				<?php echo $this->lists["type"];?>
			</td>
		</tr>
		<tr>
			<td width="100" align="right" class="key">
				<label for="title">
					<?php echo JText::_( 'Name' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="title" id="title" size="60" maxlength="250" value="<?php echo $this->person->title;?>" />
			</td>
		</tr>
		<?php
		if ($mainframe->isAdmin()){
		?>
		<tr>
			<td width="100" align="right" class="key">
				<label for="alias">
					<?php echo JText::_( 'Alias' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="alias" id="alias" size="60" maxlength="250" value="<?php echo $this->person->alias;?>" />
			</td>
		</tr>
		<?php
		}
		?>
		<?php
		if ($compparams->get("linktouser",0)){
			$client = $mainframe->isAdmin()?"administrator":"site";
			$script = "var urlroot = '".JURI::root()."';\n";
			$script .= "var jsontoken = '".JUtility::getToken()."';\n";

			$document = JFactory::getDocument();
			$document->addScriptDeclaration($script);
			
			if ( $this->person->linktouser>0){
				$linkeduser = JFactory::getUser($this->person->linktouser);
				$linktouser = $linkeduser->name." (".$linkeduser->username.")";
			}
			else {
				$linktouser = "";
			}
		?>
		<tr>
			<td width="100" align="right" class="key">
				<label for="alias">
					<?php echo JText::_( 'Link to Joomla User' ); ?>:
				</label>
			</td>
			<td>
				<input type="text"	name="jev_name" onchange="findUser(event,this,'<?php echo JURI::root()."components/com_jevpeople/libraries/finduser.php";?>', '<?php echo $client;?>')" onkeyup="findUser(event,this,'<?php echo JURI::root()."components/com_jevpeople/libraries/finduser.php";?>', '<?php echo $client;?>')" size="30"  autocomplete="off"  />
				<input type="hidden" id="linktouser"  name="linktouser" value="<?php echo $this->person->linktouser?>" />
				<span id="linktousertext"><?php echo $linktouser?></span>
				<div id="ltumatches" style="width:150px;height:50px;overflow:margin-top:10px;overflow-y:auto;border:solid 1px #ccc;margin:0px;padding:0px;display:none;">
			</td>
		</tr>
		<?php
		}
		?>

		<tr>
			<td width="100" align="right" class="key">
				<label for="www">
					<?php echo JHTML::tooltip(JText::_("Full URL"),"","",'Website'); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="www" id="www" size="60" maxlength="250" value="<?php echo $this->person->www;?>" />
			</td>
		</tr>
		<?php
		$result = '<iframe src="about:blank" style="display:none" name="uploadtarget" id="uploadtarget"></iframe>';

		$filename = isset($this->person->image)?$this->person->image:"";
		$filetitle = isset($this->person->imagetitle)?$this->person->imagetitle:"";
		$fieldname = "image";
		if ($filename){
			$src = JEVP_MEDIA_BASEURL."/$imgfolder/thumbnails/thumb_$filename";
			$visibility="visibility:visible;";
			$visibility="margin-right:10px;";
		}
		else {
			$src = "about:blank";
			$visibility="margin-right:0px;";
			$visibility="display:none;";
		}
		$result .= '<img id="'.$fieldname.'_img" src="'.$src.'" style="float:left;'.$visibility.'"/>';
		$result .= '<input type="hidden" name="'.$fieldname.'" id="'.$fieldname.'" value="'.$filename.'" size="50"/>';
		$result .= '<input type="hidden" name="'.$fieldname.'title" id="'.$fieldname.'title" value="'.$filetitle.'" size="50"/>';
		$result .= '<br/>';
		$result .= '<input type="file" name="'.$fieldname.'_file" id="'.$fieldname.'_file" size="50"/>';
		$result .= ' <input type="button" onclick="uploadFileType(\''.$fieldname.'\')" value="'.JText::_("upload").'"/> ';
		$result .= '<input type="button" onclick="clearImageFile(\''.$fieldname.'\')" value="'.JText::_("Delete").'"/>';
		$result .= '<div id="'.$fieldname.'_loading" class="loading" style="display:none">'.JText::_("Image uploading. One Moment ...") .'</div>';
		$result .= '<div id="'.$fieldname.'_loaded" class="loaded" style="display:none">'.JText::_("Upload Complete ...").'</div>';
		$result .= '<br style="clear:both"/>';

		$label = JText::_("Image 1");
		if (JevPeopleHelper::canUploadImages()){
			?>
		<tr>
			<td width="100" align="right" class="key">
				<label for="image1">
					<?php echo $label; ?>:
				</label>
			</td>
			<td>
				<?php echo $result;?>
			</td>
		</tr>
		<?php	}	?>
		<?php if ($this->perstype->multicat>0) {?>
		<tr>
			<td width="100" align="right" class="key">
				<label for="catid0">
					<?php echo JText::_( 'JEV_EVENT_CHOOSE_CATEG' ); ?>:
				</label>
			</td>
			<td>
				<?php echo $this->lists["catid0"];?>
			</td>
		</tr>
		<?php if ($this->perstype->multicat>1) {?>
		<tr>
			<td width="100" align="right" class="key">
				<label for="catid1">
					<?php echo JText::_( 'JEV_EVENT_CHOOSE_CATEG' ); ?>:
				</label>
			</td>
			<td>
				<?php echo $this->lists["catid1"];?>
			</td>
		</tr>
		<tr>
			<td width="100" align="right" class="key">
				<label for="catid2">
					<?php echo JText::_( 'JEV_EVENT_CHOOSE_CATEG' ); ?>:
				</label>
			</td>
			<td>
				<?php echo $this->lists["catid2"];?>
			</td>
		</tr>
		<tr>
			<td width="100" align="right" class="key">
				<label for="catid3">
					<?php echo JText::_( 'JEV_EVENT_CHOOSE_CATEG' ); ?>:
				</label>
			</td>
			<td>
				<?php echo $this->lists["catid3"];?>
			</td>
		</tr>
		<tr>
			<td width="100" align="right" class="key">
				<label for="catid4">
					<?php echo JText::_( 'JEV_EVENT_CHOOSE_CATEG' ); ?>:
				</label>
			</td>
			<td>
				<?php echo $this->lists["catid4"];?>
			</td>
		</tr>
		<?php }?>
		<?php }?>
		<?php if ($this->perstype->showaddress>0) {?>
		<tr>
			<td width="100" align="right" class="key">
				<label for="street">
					<?php echo JText::_( 'Street' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="street" id="street" size="60" maxlength="250" value="<?php echo $this->person->street;?>" />
			</td>
		</tr>
		<tr>
			<td valign="top" align="right" class="key">
				<label for="catid">
					<?php echo JText::_( 'City' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="city" id="city" size="20" maxlength="50" value="<?php echo $this->person->city;?>" />
			</td>
		</tr>
		<tr>
			<td valign="top" align="right" class="key">
				<label for="catid">
					<?php echo JText::_( 'State' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="state" id="state" size="20" maxlength="50" value="<?php echo $this->person->state;?>" />
			</td>
		</tr>
		<tr>
			<td valign="top" align="right" class="key">
				<label for="catid">
					<?php echo JText::_( 'Country' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="country" id="country" size="20" maxlength="50" value="<?php echo $this->person->country;?>" />
			</td>
		</tr>
		<tr>
			<td width="100" align="right" class="key">
				<label for="street">
					<?php echo JText::_( 'Postcode/Zip' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="postcode" id="postcode" size="20" maxlength="50" value="<?php echo $this->person->postcode;?>" />
			</td>
		</tr>
		<?php }?>
		<?php if ($this->jevuser && $this->jevuser->cancreateglobal){ ?>
		<tr>
			<td valign="top" align="right" class="key">
				<?php echo JText::_( 'Global' ); ?>:
			</td>
			<td>
				<?php echo $this->lists['global']; ?>
			</td>
		</tr>
		<tr>
			<td valign="top" align="right" class="key">
				<?php echo JText::_( 'Published' ); ?>:
			</td>
			<td>
				<?php echo $this->lists['published']; ?>
			</td>
		</tr>
		<!--
		<tr>
			<td valign="top" align="right" class="key">
				<label for="ordering">
					<?php echo JText::_( 'Ordering' ); ?>:
				</label>
			</td>
			<td>
				<?php echo $this->lists['ordering']; ?>
			</td>
		</tr>
		//-->
		<?php } 

		// Now include any custom fields
		JLoader::register('JevCfParameter',JPATH_SITE."/plugins/jevents/customfields/jevcfparameter.php");
		// New parameterised fields
		$hasparams = false;
		$template = $compparams->get("template","");
		$customfields = array();
		if ($template!=""){
			$xmlfile = JPATH_SITE."/plugins/jevents/customfields/templates/".$template;
			if (file_exists($xmlfile)){
				if ($this->person->pers_id){
					$db = JFactory::getDBO();
					$db->setQuery("SELECT * FROM #__jev_customfields2 WHERE target_id=".intval($this->person->pers_id). " AND targettype='com_jevpeople'");

					$jcfparams = new JevCfParameter($db->loadObjectList(),$xmlfile,  $this->person);
				}
				else {
					$jcfparams = new JevCfParameter(array(),$xmlfile,  $this->person);
				}
				JHTML::_('behavior.tooltip');
				if($jcfparams->getNumParams()>0){

					$jcfparams->render('custom_','_default',$customfields);

					if (count($customfields)>0){
						foreach ($customfields as $customfield ) {
							?>
		<tr>
			<td valign="top" align="right" class="key">
				<?php echo $customfield['label']; ?>:
			</td>
			<td>
				<?php echo $customfield['input']; ?>
			</td>
		</tr>
							<?php
						}
					}
				}
			}
		}
	?>
	</table>
	</fieldset>
	
	<?php if ($this->params->getNumParams()>0) { ?>
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'Parameters' ); ?></legend>

		<table class="admintable">
		<tr>
			<td>
				<?php echo $this->params->render();?>
			</td>
		</tr>
		</table>
	</fieldset>
	<?php } ?>

	<?php if ($this->perstype->showaddress>0) {?>

	<fieldset class="adminform">
		<legend><?php echo JText::_( 'Google Map' ); ?></legend>
		<table class="admintable">
		<tr	>
			<td colspan="2">
	    	    <input type="button" name='findaddress' onclick="findAddress();" value="<?php echo JText::_("Find Address");?>" />
			</td>
		</tr>		
		<tr>
			<td colspan="2">
				<div id="gmap" style="width: 550px; height: 350px"></div>
				<div style="clear:both;"></div>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="key" style="text-align:left;">
				<label for="geolat">
					<?php echo JText::_( 'Geo Lat' ); ?>:
				</label>
				<input class="text_area" type="text" name="geolat" id="geolat" size="32" maxlength="250" value="<?php echo $this->person->geolat;?>" />
			</td>
		</tr>
		<tr>
			<td colspan="2" class="key" style="text-align:left;">
				<label for="geolong">
					<?php echo JText::_( 'Geo Long' ); ?>:
				</label>
				<input class="text_area" type="text" name="geolon" id="geolon" size="32" maxlength="250" value="<?php echo $this->person->geolon;?>" />
			</td>
		</tr>
		<tr>
			<td colspan="2" class="key" style="text-align:left;">
				<label for="v">
					<?php echo JText::_( 'Geo Zoom' ); ?>:
				</label>
				<input class="text_area" type="text" name="geozoom" id="geozoom" size="32" maxlength="250" value="<?php echo $this->person->geozoom;?>" />
			</td>
		</tr>
		</table>
	</fieldset>
	<?php } ?>

	<fieldset class="adminform">
	<legend><?php echo JText::_( 'Description' ); ?></legend>

		<table class="admintable">
		<tr>
			<td>
				<?php
				// parameters : areaname, content, width, height, cols, rows
				echo $editor->display( 'description',  $this->person->description , '100%', '350', '75', '20' ) ;
				?>
			</td>
		</tr>
		</table>
	</fieldset>

	</div>

	<input type="hidden" name="option" value="com_jevpeople" />
	<input type="hidden" name="Itemid" value="<?php global $Itemid;echo $Itemid; ?>" />
	<input type="hidden" name="cid[]" value="<?php echo $this->person->pers_id; ?>" />
	<input type="hidden" name="returntask" value="<?php echo $this->returntask;?>" />
	<input type="hidden" name="task" value="people.edit" />
	<?php if (JRequest::getString("tmpl","")=="component"){ ?>
	<input type="hidden" name="tmpl" value="component" />	
	<?php } ?>
	<?php echo JHTML::_( 'form.token' ); ?>
</form>