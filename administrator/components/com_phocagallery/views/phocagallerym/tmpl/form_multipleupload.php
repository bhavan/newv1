<!--#DD#-->
<script language="javascript">

function add_new_file(field)
{
    // Get the number of files previously uploaded.
    var count = parseInt(document.getElementById('file_count').value);
    
    // Get the name of the file that has just been uploaded.
    var file_name = document.getElementById("new_file["+count+"]").value;
    
    // Hide the file upload control containing the information about the picture that was just uploaded.
    document.getElementById('new_file_row').style.display = "none";
    document.getElementById('new_file_row').id = "new_file_row["+count+"]";
    
    // Get a reference to the table containing the uploaded pictures.        
    var table = document.getElementById('files_table');
    
    // Insert a new row with the file name and a delete button.
    var row = table.insertRow(table.rows.length);
    row.id = "inserted_file["+count+"]";
    var cell0 = row.insertCell(0);
    cell0.innerHTML = '<input type="text" disabled="disabled" name="inserted_file['+count+']" value="'+file_name+'" /><input type="button" name="delete['+count+']" value="Delete" onclick="delete_inserted(this)"';
    
    // Increment count of the number of files uploaded.
    ++count;
    
    // Insert a new file upload control in the table.
    var row = table.insertRow(table.rows.length);
    row.id = "new_file_row";
    var cell0 = row.insertCell(0);
    cell0.innerHTML = '<input type="file" name="new_file['+count+']" id="new_file['+count+']" readonly="readonly" onchange="add_new_file(this)" />';    
    
    // Update the value of the file hidden input tag holding the count of files uploaded.
    document.getElementById('file_count').value = count;
}




function delete_inserted(field)
{
    // Get the field name.
    var name = field.name;
    
    // Extract the file id from the field name.
    var id = name.substr(name.indexOf('[') + 1, name.indexOf(']') - name.indexOf('[') - 1);
    
    // Hide the row displaying the uploaded file name.
    document.getElementById("inserted_file["+id+"]").style.display = "none";
        
    // Get a reference to the uploaded file control.
    var control = document.getElementById("new_file["+id+"]");
    
    // Remove the new file control.
    control.parentNode.removeChild(control);
} 


function  getXMLHttp()
{
  var xmlHttp

  try
  {
    //Firefox, Opera 8.0+, Safari
    xmlHttp = new XMLHttpRequest();
  }
  catch(e)
  {
    //Internet Explorer
    try
    {
      xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
    }
    catch(e)
    {
      try
      {
        xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
      }
      catch(e)
      {
        alert("Your browser does not support AJAX!")
        return false;
      }
    }
  }
  return xmlHttp;
}

function  HandleResponse(response, id)
{
  document.getElementById(id).innerHTML = document.getElementById(id).innerHTML + response;
}

function  MakeRequest($callfile,id)
{
  var xmlHttp = getXMLHttp();
 
  xmlHttp.onreadystatechange = function()
  {
    if(xmlHttp.readyState == 4)
    {
      HandleResponse(xmlHttp.responseText, id);
    }
  }

  xmlHttp.open("GET", $callfile, true);
  xmlHttp.send(null);
}

</script>
<!--#DD#-->
<?php 
defined('_JEXEC') or die('Restricted access');
$currentFolder = '';
if (isset($this->state->folder) && $this->state->folder != '') {
 $currentFolder = $this->state->folder;
}

?><div id="phocagallery-upload">
	<div style="font-size:1px;height:1px;margin:0px;padding:0px;">&nbsp;</div>

	
	<form action="<?php echo JURI::base(); ?>index.php?option=com_phocagallery&controller=phocagalleryu&amp;task=multipleUploadProcess&amp;<?php echo $this->session->getName().'='.$this->session->getId(); ?>&amp;<?php echo JUtility::getToken();?>=1&amp;viewback=phocagallerym&amp;folder=<?php echo $currentFolder?>" id="uploadForm" method="post" enctype="multipart/form-data">
	<?php 
	/*$action = JURI::base().'index.php?option=com_phocagallery&controller=phocagalleryu&amp;task=javaupload&amp;'.$this->session->getName().'='.$this->session->getId().'&amp;'. JUtility::getToken().'=1&amp;viewback=phocagallerym&amp;folder='. $currentFolder;
	
	<form action="<?php echo $action;?>" id="uploadForm" method="post" enctype="multipart/form-data">
*/?>
<!-- File Upload Form -->
<?php
if ($this->require_ftp) {
	echo PhocaGalleryFileUpload::renderFTPaccess();
}  ?>
<fieldset>
	<legend><?php 
	echo JText::_( 'Upload File' ).' [ '. JText::_( 'Max Size' ).':&nbsp;'.$this->tmpl['uploadmaxsizeread'].','
	.' '.JText::_('Max Resolution').':&nbsp;'. $this->tmpl['uploadmaxreswidth'].' x '.$this->tmpl['uploadmaxresheight'].' px ]';
?></legend>		
	
	
	<fieldset class="actions">
		<div id="multipleFilesUpload">
		<!--<input type="file" id="sfile-upload" value="" name="Filedata[]" />-->
		</div>
		
		 <div style="margin-bottom:10px; color:#AA0000; font-weight:bold;">Upload ZIP file [Maximum Size: 15MB]</div>
		<input type="hidden" name="file_count" id="file_count" value="0" />
		<table id="files_table" border="0" cellpadding="0" cellspacing="0">
				<tr id="new_file_row">
						<td>
								<input type="file" name="new_file[0]" id="new_file[0]" readonly="readonly" onchange="add_new_file(this)" />
						</td>
				</tr>
		</table>

		
		<!--<a id='a' onclick='javascript:MakeRequest("<?php echo JURI::base(); ?>index.php?option=com_phocagallery&controller=phocagalleryu&amp;task=multipleUpload", "multipleFilesUpload");' href="#a">add more</a>-->
		<br>		
		
		
		<br><input type="submit" id="sfile-upload-submit" value="<?php echo JText::_('Start Upload'); ?>"/>
	</fieldset>
	
	
</fieldset>
	<input type="hidden" name="return-url" value="<?php echo base64_encode('index.php?option=com_phocagallery&view=phocagallerym&layout=form&tab='.$this->tmpl['currenttab']['upload']); ?>" />
	<input type="hidden" name="tab" value="<?php echo $this->tmpl['currenttab']['upload']; ?>" />
</form>

<?php 
echo PhocaGalleryFileUpload::renderCreateFolder($this->session->getName(), $this->session->getId(), $currentFolder, 'phocagallerym' );
?>
</div>