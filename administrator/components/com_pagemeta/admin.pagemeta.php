<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

$task	= JRequest::getCmd('task');

switch ($task) {
	case 'add':
		add( );
		break;
	case 'edit':
		edit( );
		break;
	case 'save':
		save();
		break;
	case 'remove':
		remove();
		break;
	case 'globalseting':
		globalseting();
		break;	
	default:		
		listmeta( );
		break;
}


function listmeta(){
	$db	=& JFactory::getDBO();
	$query = "SELECT * FROM #__pagemeta";
	$db->setQuery( $query );
	$row = $db->loadAssocList();	
		
?>
<form method="post" action="" name="adminForm">
<input type="hidden" name="task" value="" />
<input type="hidden" value="0" name="boxchecked">
<table class="adminlist" cellspacing="1">
  <thead>
  <tr>
    <th>#</th>
    <th><input type="checkbox" name="toggle" onclick="checkAll(8);" /></th>
    <th>URL</th>
    <th>Title</th>
    <th>Meta Description</th>
    <th>Keywords</th>  
    <th>Extra Meta</th>    
  </tr>
  </thead>
  <? for($i=0;$i<sizeof($row);$i++){?>
  <tr>
    <td><?=$row[$i]['id']?></td>
    <td align="center"><input type="checkbox" name="cid" onclick="isChecked(this.checked);" value="<?=$row[$i]['id']?>" /></td>    
    <td><?=$row[$i]['uri']?></td>
    <td><?=$row[$i]['title']?></td>
    <td><?=$row[$i]['metadesc']?></td>
    <td><?=$row[$i]['keywords']?></td>  
    <td><?=$row[$i]['extra_meta']?></td>    
  </tr>
  <? }?>
</table>
</form>
<?	
}

function add( ){
?>
<form method="post" action="" name="adminForm">
<input type="hidden" name="task" value="" />
<table class="admintable" cellspacing="1">
  <tr>
    <td width="20%" class="key"><label>URL:</label></td>
    <td width="80%"><input type="text" name="url" class="inputbox" size="50" value="" /></td>
  </tr>
  <tr>
    <td width="20%" class="key"><label>Title:</label></td>
    <td width="80%"><input type="text" name="title" class="inputbox" size="50" value="" /></td>
  </tr>
  <tr>
    <td width="20%" class="key"><label>Meta Description:</label></td>
    <td width="80%"><textarea name="metadesc" class="inputbox" rows="3" cols="50"></textarea></td>
  </tr>
  <tr>
    <td width="20%" class="key"><label>Keywords:</label></td>
    <td width="80%"><textarea name="keywords" class="inputbox" rows="3" cols="50"></textarea></td>
  </tr>  
  <tr>
    <td width="20%" class="key"><label>Extra Meta:</label></td>
    <td width="80%"><textarea name="extra_meta" class="inputbox" rows="3" cols="50"></textarea></td>
  </tr>
</table>
</form>
<?
}
function edit(){
	$db	=& JFactory::getDBO();
	$query = "SELECT * FROM #__pagemeta WHERE id=".$_POST['cid'];
	$db->setQuery( $query );
	$row = $db->loadAssoc();
?>
<form method="post" action="" name="adminForm">
<input type="hidden" name="task" value="" />	
<input type="hidden" name="mid"  value="<?=$_POST['cid']?>" />
<table class="admintable" cellspacing="1">
  <tr>
    <td width="20%" class="key"><label>URL:</label></td>
    <td width="80%"><input type="text" name="url" class="inputbox" size="50" value="<?=$row['uri']?>" /></td>
  </tr>
  <tr>
    <td width="20%" class="key"><label>Title:</label></td>
    <td width="80%"><input type="text" name="title" class="inputbox" size="50" value="<?=$row['title']?>" /></td>
  </tr>
   <tr>
    <td width="20%" class="key"><label>Meta Description:</label></td>
    <td width="80%"><textarea name="metadesc" class="inputbox" rows="3" cols="50"><?=$row['metadesc']?></textarea></td>
  </tr>
  <tr>
    <td width="20%" class="key"><label>Keywords:</label></td>
    <td width="80%"><textarea name="keywords" class="inputbox" rows="3" cols="50"><?=$row['keywords']?></textarea></td>
  </tr>  
  <tr>
    <td width="20%" class="key"><label>Extra Meta:</label></td>
    <td width="80%"><textarea name="extra_meta" class="inputbox" rows="3" cols="50"><?=$row['extra_meta']?></textarea></td>
  </tr> 
</table>
</form>
<?
}

function globalseting(){
	$db	=& JFactory::getDBO();
	$query = "SELECT * FROM #__pageglobal";
	$db->setQuery( $query );
	$row = $db->loadAssoc();
?>
<form method="post" action="" name="adminForm">
<input type="hidden" name="task" value="" />	
<input type="hidden" name="global"  value="1" />
<table class="admintable" cellspacing="1">
  <tr>
    <td width="20%" class="key"><label>SITE NAME:</label></td>
    <td width="80%"><input type="text" name="site_name" class="inputbox" size="50" value="<?=$row['site_name']?>" /></td>
  </tr>
  <tr>
    <td width="20%" class="key"><label>EMAIL:</label></td>
    <td width="80%"><input type="text" name="email" class="inputbox" size="50" value="<?=$row['email']?>" /></td>
  </tr>
  <tr>
    <td width="20%" class="key"><label>GOOGLE ANALITICS CODE:</label></td>
    <td width="80%"><input type="text" name="googgle_map_api_keys" class="inputbox" size="50" maxlength="25" value="<?=$row['googgle_map_api_keys']?>" />
    <img src="../partner/<?php echo $_SESSION['partner_folder_name'];?>/images/edit_f2.png" height="18" title="Enter Only UA code in the box, Example: UA-29293639-3" />
    </td>
  </tr>
  <tr>
    <td width="20%" class="key"><label>LOCATION CODE:</label></td>
    <td width="80%"><input type="text" name="location_code" class="inputbox" size="50" value="<?=$row['location_code']?>" /></td>
  </tr>
  <tr>
    <td width="20%" class="key"><label>CITY NAME:</label></td>
    <td width="80%"><input type="text" name="beach" class="inputbox" size="50" value="<?=$row['beach']?>" /></td>
  </tr>
  <tr>
    <td width="20%" class="key"><label>PHOTO MINI SLIDER CATEGORY:</label></td>
    <td width="80%"><input type="text" name="photo_mini_slider_cat" class="inputbox" size="50" value="<?=$row['photo_mini_slider_cat']?>" /></td>
  </tr>
  <tr>
    <td width="20%" class="key"><label>PHOTO UPLOAD CATEGORY:</label></td>
    <td width="80%"><input type="text" name="photo_upload_cat" class="inputbox" size="50" value="<?=$row['photo_upload_cat']?>" /></td>
  </tr>
  <tr>
    <td width="20%" class="key"><label>FACEBOOK LINK:</label></td>
    <td width="80%"><input type="text" name="facebook" class="inputbox" size="50" value="<?=$row['facebook']?>" /></td>
  </tr>
  <tr>
    <td width="20%" class="key"><label>IPHONE DOWNLOAD LINK:</label></td>
    <td width="80%"><input type="text" name="iphone" class="inputbox" size="50" value="<?=$row['iphone']?>" /></td>
  </tr>
  <tr>
    <td width="20%" class="key"><label>ANDROID DOWNLOAD LINK:</label></td>
    <td width="80%"><input type="text" name="android" class="inputbox" size="50" value="<?=$row['android']?>" /></td>
  </tr>
  <tr>
    <td width="20%" class="key"><label>DISTANCE UNIT:</label></td>
    <td width="80%">
      <?php
      if ($row['distance_unit'] == 'Kms') { ?>
      <input name="dunit" type="radio" value="Kms" checked />Kms&nbsp;<input name="dunit" type="radio" value="Miles" />Miles
      <?php } ?> 
      <?php
      if ($row['distance_unit'] == 'Miles') { ?>
      <input name="dunit" type="radio" value="Kms"/>Kms&nbsp;<input name="dunit" type="radio" value="Miles" checked />Miles
      <?php } ?>
      <?php if ($row['distance_unit'] == '') { ?>
      <input name="dunit" type="radio" value="Kms"/>Kms&nbsp;<input name="dunit" type="radio" value="Miles" checked />Miles
      <?php } ?>
    <td>
  </tr>
    <tr>
    <td width="20%" class="key"><label>WEATHER UNIT:</label></td>
    <td width="80%">
      <?php
      if ($row['weather_unit'] == 'm') { ?>
      <input name="wunit" type="radio" value="m" checked />C&nbsp;<input name="wunit" type="radio" value="s" />F
      <?php } ?> 
      <?php
      if ($row['weather_unit'] == 's') { ?>
      <input name="wunit" type="radio" value="m"/>C&nbsp;<input name="wunit" type="radio" value="s" checked />F
      <?php } ?>
      <?php if ($row['weather_unit'] == '') { ?>
      <input name="wunit" type="radio" value="m"/>C&nbsp;<input name="wunit" type="radio" value="s" checked />F
      <?php } ?>
   <td>
  </tr>
  <tr>
  	<td width="20%" class="key"><label>TIME ZONE:</label></td>
	<td width="80%">
<?php 

	$timezoneTable = array(
	"-12:00:00" => "(GMT -12:00) Eniwetok, Kwajalein",
	"-11:00:00" => "(GMT -11:00) Midway Island, Samoa",
	"-10:00:00" => "(GMT -10:00) Hawaii",
	"-9:00:00" => "(GMT -9:00) Alaska",
	"-8:00:00" => "(GMT -8:00) Pacific Time (US &amp; Canada)",
	"-7:00:00" => "(GMT -7:00) Mountain Time (US &amp; Canada)",
	"-6:00:00" => "(GMT -6:00) Central Time (US &amp; Canada), Mexico City",
	"-5:00:00" => "(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima",
	"-4:00:00" => "(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz",
	"-3:30:00" => "(GMT -3:30) Newfoundland",
	"-3:00:00" => "(GMT -3:00) Brazil, Buenos Aires, Georgetown",
	"-2:00:00" => "(GMT -2:00) Mid-Atlantic",
	"-1:00:00" => "(GMT -1:00 hour) Azores, Cape Verde Islands",
	"00:00:00" => "(GMT) Western Europe Time, London, Lisbon, Casablanca",
	"1:00:00" => "(GMT +1:00 hour) Brussels, Copenhagen, Madrid, Paris",
	"2:00:00" => "(GMT +2:00) Kaliningrad, South Africa",
	"3:00:00" => "(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg",
	"3:00:00" => "(GMT +3:30) Tehran",
	"4:00:00" => "(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi",
	"4:30:00" => "(GMT +4:30) Kabul",
	"5:00:00" => "(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent",
	"5:30:00" => "(GMT +5:30) Bombay, Calcutta, Madras, New Delhi",
	"6:00:00" => "(GMT +6:00) Almaty, Dhaka, Colombo",
	"7:00:00" => "(GMT +7:00) Bangkok, Hanoi, Jakarta",
	"8:00:00" => "(GMT +8:00) Beijing, Perth, Singapore, Hong Kong",
	"9:00:00" => "(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk",
	"9:30:00" => "(GMT +9:30) Adelaide, Darwin",
	"10:00:00" => "(GMT +10:00) Eastern Australia, Guam, Vladivostok",
	"11:00:00" => "(GMT +11:00) Magadan, Solomon Islands, New Caledonia",
	"12:00:00" => "(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka"
	);
?>
		<select size="1" class="inputbox" id="offset" name="timezone">
			<?php
			foreach($timezoneTable as $key => $value){?>
					 <option <?php echo ($row['time_zone'] == $key)?"selected='selected'":''?> value="<?=$key?>"><?=$value?></option>
			<?php }?>
		</select>
	</td>
  </tr>
  
</table>
</form>
<?
}


function save(){
	
	global $mainframe;
	
	$db	=& JFactory::getDBO();
	
	if(isset($_POST['mid'])){
		
		$query = "UPDATE #__pagemeta SET uri ='".$_POST['url']."', title ='".$_POST['title']."', metadesc ='".addslashes($_POST['metadesc'])."', keywords ='".addslashes($_POST['keywords'])."', extra_meta ='".addslashes($_POST['extra_meta'])."' WHERE id='".$_POST['mid']."'";
		
		$db->setQuery( $query );
		
		if (! $db->query()) {
			$mainframe->redirect( 'index.php?option=com_pagemeta' , 'Unable to update Page Meta');
		}else{
			$mainframe->redirect( 'index.php?option=com_pagemeta' , 'Page Meta updated sucessfully');
		}	
	}
	elseif(isset($_POST['global'])){
	
		$query = "UPDATE #__pageglobal SET site_name ='".$_POST['site_name']."', email ='".$_POST['email']."', googgle_map_api_keys ='".addslashes($_POST['googgle_map_api_keys'])."', location_code ='".$_POST['location_code']."', beach ='".$_POST['beach']."', photo_mini_slider_cat ='".$_POST['photo_mini_slider_cat']."', photo_upload_cat ='".$_POST['photo_upload_cat']."', facebook ='".$_POST['facebook']."', iphone ='".$_POST['iphone']."', android ='".$_POST['android']."', distance_unit ='".$_POST['dunit']."', weather_unit ='".$_POST['wunit']."',time_zone ='".$_POST['timezone']."' WHERE id='1'";
		
		$db->setQuery( $query );
		
		if (! $db->query()) {
			$mainframe->redirect( 'index.php?option=com_pagemeta' , 'Unable to update Global Settings');
		}else{
			$mainframe->redirect( 'index.php?option=com_pagemeta' , 'Global Settings updated sucessfully');
		}	
	}
	else{	
		$query = "INSERT INTO #__pagemeta (`id`, `uri`, `title`, `metadesc`, `keywords`, `extra_meta`) VALUES (NULL, '".$_POST['url']."', '".$_POST['title']."', '".addslashes($_POST['metadesc'])."', '".addslashes($_POST['keywords'])."', '".addslashes($_POST['extra_meta'])."')";
		
		$db->setQuery( $query );
		if (! $db->query()) {
			$mainframe->redirect( 'index.php?option=com_pagemeta' , 'Unable to add Page Meta');
		}else{
			$mainframe->redirect( 'index.php?option=com_pagemeta' , 'Page Meta added sucessfully');
		}	
	}
}

function remove(){
	global $mainframe;
	$db	=& JFactory::getDBO();
	$query = "DELETE FROM #__pagemeta WHERE id=".$_POST['cid'];
	$db->setQuery( $query );
	if (! $db->query()) {
		$mainframe->redirect( 'index.php?option=com_pagemeta' , 'Unable to remove Page Meta');
	}else{
		$mainframe->redirect( 'index.php?option=com_pagemeta' , 'Page Meta remove sucessfully');
	}	
}
?>

