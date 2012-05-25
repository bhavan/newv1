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
</table>
</form>
<?
}


function save(){
	
	global $mainframe;
	
	$db	=& JFactory::getDBO();
	
	if(isset($_POST['mid'])){
		
		$query = "UPDATE #__pagemeta SET uri ='".$_POST['url']."', title ='".$_POST['title']."', metadesc ='".$_POST['metadesc']."', keywords ='".$_POST['keywords']."', extra_meta ='".$_POST['extra_meta']."' WHERE id='".$_POST['mid']."'";
		
		$db->setQuery( $query );
		
		if (! $db->query()) {
			$mainframe->redirect( 'index.php?option=com_pagemeta' , 'Unable to update Page Meta');
		}else{
			$mainframe->redirect( 'index.php?option=com_pagemeta' , 'Page Meta updated sucessfully');
		}	
	}
	elseif(isset($_POST['global'])){
		$query = "UPDATE #__pageglobal SET site_name ='".$_POST['site_name']."', email ='".$_POST['email']."', googgle_map_api_keys ='".addslashes($_POST['googgle_map_api_keys'])."', location_code ='".$_POST['location_code']."', beach ='".$_POST['beach']."', photo_mini_slider_cat ='".$_POST['photo_mini_slider_cat']."', photo_upload_cat ='".$_POST['photo_upload_cat']."', facebook ='".$_POST['facebook']."', iphone ='".$_POST['iphone']."', android ='".$_POST['android']."' WHERE id='1'";
		
		$db->setQuery( $query );
		
		if (! $db->query()) {
			$mainframe->redirect( 'index.php?option=com_pagemeta' , 'Unable to update Global Settings');
		}else{
			$mainframe->redirect( 'index.php?option=com_pagemeta' , 'Global Settings updated sucessfully');
		}	
	}
	else{	
		$query = "INSERT INTO #__pagemeta (`id`, `uri`, `title`, `metadesc`, `keywords`, `extra_meta`) VALUES (NULL, '".$_POST['url']."', '".$_POST['title']."', '".$_POST['metadesc']."', '".$_POST['keywords']."', '".$_POST['extra_meta']."')";
		
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

