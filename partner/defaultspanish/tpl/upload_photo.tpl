<h2>SUBE TUS PROPIAS FOTOS</h2>

<form id="uploadForm" name="uploadForm" action="" method="post" enctype="multipart/form-data" onSubmit="return form_validation()">
  <div class="no-margin-top no-margin-bottom">
	<input type="hidden" name="MAX_FILE_SIZE" value="2000000">
    <?php 
	if($msg!='')
	{
		echo '<div style="color:#FF0000;font-size: 14px;">'.$msg.'</div>';
	}
	if(isset($var->photo_uploaded) && $var->photo_uploaded) echo "<h4>Gracias! Su foto ha sido cargado con éxito, en espera de aprobación de administrador.</h4><br />"; ?>
    <h3>Elegir una imagen</h3><br />
    <label class="no-margin-top" accesskey="f" for="userfile">Nombre de archivo (límite de tamaño de 2 MB) :</label>
    <input class="no-margin-top" id="image" name="image" value="Vali fail" type="file" />
    <br /><br />
    <label accesskey="t" for="caption">Pie de foto (opcional):</label>
    <input class="no-margin-top" name="caption" id="caption" style="width: 90%;" value="<?php echo $_POST['caption'];?>" />
    <br /><br />
    <label accesskey="c" for="description">Descripción de la foto (opcional):</label>
    <textarea class="no-margin-top" name="description" id="description" style="width: 90%;" cols="43" rows="6"></textarea>
    <br /><br />
    <label accesskey="n" for="username">Su nombre (opcional):</label>
    <input class="no-margin-top" name="username" id="username" style="width: 90%;" value="<?php echo $_POST['username'];?>" />
    <br /><br />
    <label accesskey="h" for="location">Su origen (opcional):</label>
    <input class="no-margin-top" name="location" id="location" style="width: 90%;"  value="<?php echo $_POST['location'];?>" />
    <br /><br />
    <input type="hidden" name="backurl" value="<?php echo $var->http_referer; ?>" />
    <input type="hidden" name="formname" value="upload.event.photo" />
    <input class="no-margin-top" type="submit" name="submit" value="Subir" style="width:160px;" />
  </div><!-- /no-margin-top no-margin-bottom -->
  <br /><br />
  <h3><a href="<?php echo $var->http_referer; ?>" style=":left;">&laquo;&nbsp;de nuevo</a></h3>
</form>