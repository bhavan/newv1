<h2>Upload your own photo</h2>
 
<form id="uploadForm" name="uploadForm" action="" method="post" enctype="multipart/form-data" onSubmit="return form_validation()">
  <div class="no-margin-top no-margin-bottom">
	<input type="hidden" name="MAX_FILE_SIZE" value="2000000">
    <?php if(isset($var->photo_uploaded) && $var->photo_uploaded) echo "<h4>Thank you! Your photo has successfully been uploaded, waiting for admin approval.</h4><br />"; ?>
    <h3>Choose an Image</h3><br />
    <label class="no-margin-top" accesskey="f" for="userfile">Filename (2MB Size Limit) :</label>
    <input class="no-margin-top" id="image" name="image" value="Vali fail" type="file" />
    <br /><br />
    <label accesskey="t" for="caption">Photo Caption (optional):</label>
    <input class="no-margin-top" name="caption" id="caption" style="width: 90%;" />
    <br /><br />
    <label accesskey="c" for="description">Photo Description (optional):</label>
    <textarea class="no-margin-top" name="description" id="description" style="width: 90%;" cols="43" rows="6"></textarea>
    <br /><br />
    <label accesskey="n" for="username">Your Name (optional):</label>
    <input class="no-margin-top" name="username" id="username" style="width: 90%;" />
    <br /><br />
    <label accesskey="h" for="location">Your Hometown (optional):</label>
    <input class="no-margin-top" name="location" id="location" style="width: 90%;" />
    <br /><br />
    <input type="hidden" name="backurl" value="<?php echo $var->http_referer; ?>" />
    <input type="hidden" name="formname" value="upload.event.photo" />
    <input class="no-margin-top" type="submit" name="submit" value="Upload" style="width:160px;" />
  </div><!-- /no-margin-top no-margin-bottom -->
  <br /><br />
  <h3><a href="<?php echo $var->http_referer; ?>" style=":left;">&laquo;&nbsp;Back</a></h3>
 
</form>