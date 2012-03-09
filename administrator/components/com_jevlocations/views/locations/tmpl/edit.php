<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php JHTML::_('behavior.tooltip'); 

$editor = &JFactory::getEditor();
$compparams = JComponentHelper::getParams("com_jevlocations");

// Attach script to document
$document =& JFactory::getDocument();

$imgfolder = "jevents/jevlocations";

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
			form.task.value = 'locations.upload';
			form.option.value = 'com_jevlocations';
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
	if (pressbutton == 'locations.overview' || pressbutton == 'locations.cancel') {
		submitform( pressbutton );
		return;
	}

	// do field validation
	var text = <?php echo $editor->getContent( 'description' ); ?>
	if (form.title.value == ""){
		alert( "<?php echo JText::_( 'Location must have a title', true ); ?>" );
	}
	<?php if ($this->usecats) {?>
	else if (form.catid.value == "0"){
		alert( "<?php echo JText::_( 'You must select a city', true ); ?>" );
	}
	<?php } ?>
	else {
		<?php
		echo $editor->save( 'description' );
		?>
		<?php 	if (isset($this->recaptcha)){ ?>
		var requestObject = new Object();
		requestObject.challengeField =  document.adminForm.recaptcha_challenge_field.value;
		requestObject.responseField =  document.adminForm.recaptcha_response_field.value;
		requestObject.error = false;

		url = urlroot + "plugins/jevents/anonuserlib/json.recaptcha.php";

		var jSonRequest = new Json.Remote(url, {
			method:'get',
			onComplete: function(json){
				if (json.error){
					try {
						Recaptcha.reload();
						eval(json.error);
					}
					catch (e){
						alert('could not process error handler');
					}
				}
				else {
					if(json.result == "success"){
						document.adminForm.task.value = pressbutton;
						document.adminForm.submit();
					}
				}
			},
			onFailure: function(){
				// in case the anon user plugin is not enabled (bizarre situation but just in case)
				document.adminForm.task.value = pressbutton;
				document.adminForm.submit();
			}
		}).send(requestObject);
		<?php }
		else { ?>
		submitform( pressbutton );
		<?php } ?>
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

<form action="index.php" method="post" name="adminForm" id="adminForm" enctype='multipart/form-data'>
<div class="col">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'Details' ); ?></legend>

		<table class="admintable">
		<tr>
			<td width="100" align="right" class="key">
				<label for="title">
					<?php echo JText::_( 'Name' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="title" id="title" size="60" maxlength="250" value="<?php echo $this->location->title;?>" />
			</td>
		</tr>
		<?php if(isset($this->users)){?>
		<tr>
			<td width="100" align="right" class="key">
				<label for="alias">
					<?php echo JText::_( 'JEV LOCATION CREATOR' ); ?>:
				</label>
			</td>
			<td>
				<?php echo $this->users;?>
			</td>
		</tr>
		<?php
		}
		if ($mainframe->isAdmin()){
		?>
		<tr>
			<td width="100" align="right" class="key">
				<label for="alias">
					<?php echo JText::_( 'Alias' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="alias" id="alias" size="60" maxlength="250" value="<?php echo $this->location->alias;?>" />
			</td>
		</tr>
		<?php
		}
		?>
		<tr>
			<td valign="top" align="right" class="key">
				<label for="loccat">
					<?php echo JText::_( 'JEV LOCATION CATEGORY' ); ?>:
				</label>
			</td>
			<td>
				<?php echo $this->lists['loccat']; ?>
			</td>
		</tr>
		<tr>
			<td width="100" align="right" class="key">
				<label for="street">
					<?php echo JText::_( 'Street' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="street" id="street" size="60" maxlength="250" value="<?php echo $this->location->street;?>" />
			</td>
		</tr>
		<?php if ($this->usecats) {?>		
		<tr>
			<td valign="top" align="right" class="key">
				<label for="catid">
					<?php echo JText::_( 'City' ); ?>:
				</label>
			</td>
			<td>
				<?php echo $this->lists['catid']; ?>
			</td>
		</tr>
		<?php
		}
		else {
			?>
		<tr>
			<td valign="top" align="right" class="key">
				<label for="catid">
					<?php echo JText::_( 'City' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="city" id="city" size="20" maxlength="50" value="<?php echo $this->location->city;?>" />
			</td>
		</tr>
		<tr>
			<td valign="top" align="right" class="key">
				<label for="catid">
					<?php echo JText::_( 'State' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="state" id="state" size="20" maxlength="50" value="<?php echo $this->location->state;?>" />
			</td>
		</tr>
		<tr>
			<td valign="top" align="right" class="key">
				<label for="catid">
					<?php echo JText::_( 'Country' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="country" id="country" size="20" maxlength="50" value="<?php echo $this->location->country;?>" />
			</td>
		</tr>
			<?php
		}
		?>
		<tr>
			<td width="100" align="right" class="key">
				<label for="street">
					<?php echo JText::_( 'Postcode' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="postcode" id="postcode" size="20" maxlength="50" value="<?php echo $this->location->postcode;?>" />
			</td>
		</tr>
		<tr>
			<td width="100" align="right" class="key">
				<label for="street">
					<?php echo JText::_( 'PHONE' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="phone" id="phone" size="60" maxlength="250" value="<?php echo $this->location->phone;?>" />
			</td>
		</tr>
		<tr>
			<td width="100" align="right" class="key">
				<label for="street">
					<?php echo JText::_( 'URL' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="url" id="url" size="60" maxlength="250" value="<?php echo $this->location->url;?>" />
			</td>
		</tr>

		<?php
		$result = '<iframe src="about:blank" style="display:none" name="uploadtarget" id="uploadtarget"></iframe>';

		$filename = isset($this->location->image)?$this->location->image:"";
		$filetitle = isset($this->location->imagetitle)?$this->location->imagetitle:"";
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
		if (JevLocationsHelper::canUploadImages()){
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
		<?php 	if (isset($this->recaptcha)){ ?>
		<tr>
			<td width="100" align="right" class="key">
				<?php echo $this->name['label']; ?>
			</td>
			<td>
				<?php echo $this->name['input']; ?>
			</td>
		</tr>
		<tr>
			<td width="100" align="right" class="key">
				<?php echo $this->email['label']; ?>
			</td>
			<td>
				<?php echo $this->email['input']; ?>
			</td>
		</tr>
		<tr>
			<td width="100" align="right" class="key">
				<?php echo $this->recaptcha['label']; ?>
			</td>
			<td>
				<?php echo $this->recaptcha['input']; ?>
			</td>
		</tr>
        <?php } ?>
		
		<?php 	if (JevLocationsHelper::canCreateGlobal()){ ?>
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
        <?php 
        $params =& JComponentHelper::getParams( JEVEX_COM_COMPONENT );
        $showpriority = $params->getValue("showpriority",0);
        if ($this->setPriority && $showpriority){ ?>
		<tr>
        	<td  valign="top" align="right" class="key"><?php echo JText::_('JEV_LOCATION_PRIORITY'); ?>:</td>
            <td >
            	<?php echo $this->priority; ?>
            </td>
		</tr>
        <?php } else { ?>
		<tr style="display:none;">
            <td colspan="2">
            	<input type="hidden" name="priority" value="<?php echo $this->location->priority;?>" />
            </td>            
		</tr>
        <?php } ?>
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
				if ($this->location->loc_id){
					$db = JFactory::getDBO();
					$db->setQuery("SELECT * FROM #__jev_customfields3 WHERE target_id=".intval($this->location->loc_id). " AND targettype='com_jevlocations'");

					$jcfparams = new JevCfParameter($db->loadObjectList(),$xmlfile,  $this->location);
				}
				else {
					$jcfparams = new JevCfParameter(array(),$xmlfile,  $this->location);
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

	<fieldset class="adminform">
		<legend><?php echo JText::_( 'Google Map' ); ?></legend>
		<?php if ($this->usecats) {?>
		<div><?php echo JText::_( 'GOOGLE MAP LOOKUP EXPLANATIION' ); ?></div>
		<?php } ?>
		<table class="admintable">
		<?php if ($this->usecats) {?>
		
		<tr>
			<td>
    	    	<label for="googleaddress"><?php echo JText::_("Address");?></label>
			</td>
			<td>
    	    	<label for="googlecountry"><?php echo JText::_("Country");?></label>
			</td>
		</tr>
		<tr>
			<td>
    	    	<input type="text" size="60" name="googleaddress" id="googleaddress" value="" />
			</td>
			<td>
    	    	<input type="text" size="20" name="googlecountry" id="googlecountry" value="" />
	    	    <input type="button" name='findaddress' onclick="findAddress();" value="<?php echo JText::_("Find Address");?>" />
			</td>
		</tr>
		<?php } 
		else {
		?>
		<tr	>
			<td colspan="2">
	    	    <input type="button" name='findaddress' onclick="findAddress();" value="<?php echo JText::_("Find Address");?>" />
			</td>
		</tr>

		<?php } ?>
		
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
				<input class="text_area" type="text" name="geolat" id="geolat" size="32" maxlength="250" value="<?php echo $this->location->geolat;?>" />
			</td>
		</tr>
		<tr>
			<td colspan="2" class="key" style="text-align:left;">
				<label for="geolong">
					<?php echo JText::_( 'Geo Long' ); ?>:
				</label>
				<input class="text_area" type="text" name="geolon" id="geolon" size="32" maxlength="250" value="<?php echo $this->location->geolon;?>" />
			</td>
		</tr>
		<tr>
			<td colspan="2" class="key" style="text-align:left;">
				<label for="v">
					<?php echo JText::_( 'Geo Zoom' ); ?>:
				</label>
				<input class="text_area" type="text" name="geozoom" id="geozoom" size="32" maxlength="250" value="<?php echo $this->location->geozoom;?>" />
			</td>
		</tr>
		</table>
	</fieldset>

		<fieldset class="adminform">
		<legend><?php echo JText::_( 'Description' ); ?></legend>

		<table class="admintable">
		<tr>
			<td>
				<?php
				$jevcfg = JComponentHelper::getParams("com_jevents");

				if ($jevcfg->get('com_show_editor_buttons')) {
					$t_buttons = explode(',', $jevcfg->get('com_editor_button_exceptions'));
				} else {
					// hide all
					$t_buttons = false;
				}

				// parameters : areaname, content, width, height, cols, rows
				echo $editor->display( 'description',  $this->location->description , '100%', '350', '75', '20' , $t_buttons) ;
				?>
			</td>
		</tr>
		</table>
	</fieldset>

	</div>

	<input type="hidden" name="option" value="com_jevlocations" />
	<input type="hidden" name="cid[]" value="<?php echo $this->location->loc_id; ?>" />
	<input type="hidden" name="returntask" value="<?php echo $this->returntask;?>" />
	<input type="hidden" name="Itemid" value="<?php echo JRequest::getInt("Itemid",0);?>" />
	<input type="hidden" name="task" value="locations.edit" />
	<?php if (JRequest::getString("tmpl","")=="component"){ ?>
	<input type="hidden" name="tmpl" value="component" />	
	<?php } ?>
	<?php if (JRequest::getInt("pop",0)==1){ ?>
	<input type="hidden" name="pop" value="1" />	
	<?php } ?>
	<?php echo JHTML::_( 'form.token' ); ?>
</form>