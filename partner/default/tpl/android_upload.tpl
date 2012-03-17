<div id="topbar">
<div id="leftnav"><a href="<?php echo $var->http_referer; ?>" style=":left;">&laquo;&nbsp;Back</a></div>

<div id="title">Upload Photos</div>

</div>
<div id="content">
	
<?php if(isset($var->photo_uploaded) && $var->photo_uploaded) echo "<h4>Thank you! Your photo has successfully been uploaded, waiting for admin approval.</h4><br />"; ?>

<ul class="pageitem">

		<li class="textbox"  style="padding-bottom:20px;">

		<form action="" enctype="multipart/form-data" method="post">
		<input type="hidden" name="MAX_FILE_SIZE" value="2000000">
		<input type="hidden" name="formname" value="upload.event.photo" />
		<input type="hidden" name="referralPage" value="<?=$var->http_referer?>" />
		
				<input type="file" value="Vali fail" name="image" id="image" ><br/><br/>
				Photo Caption (optional):<br/>
				<input style="width: 90%;" id="caption" name="caption" ><br/><br/>
				
				Photo Description (optional):<br/>
				<textarea rows="6" cols="43" style="width: 90%;" id="description" name="description" class="no-margin-top"></textarea><br/><br/>

				Your Name (optional):<br/>
				<input style="width: 90%;" id="username" name="username" class="no-margin-top"><br/><br/>

				Your Hometown (optional):<br/>
				<input style="width: 90%;" id="location" name="location" class="no-margin-top"><br/><br/>

				<input type="submit" name='uploadPhoto' value="Upload">
		</form>

	 </li>
</ul>

	
</div>

<div id="footer">

	&copy; <?=date('Y');?> <?=$site_name?>, Inc. | <a href="mailto:<?=$email?>?subject=Feedback">Contact Us</a></div></div>
</body>
<div style='display:none;'><?php echo $pageglobal['googgle_map_api_keys']; ?></div>