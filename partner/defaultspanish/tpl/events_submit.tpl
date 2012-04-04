<?php
// Print Message after event form submission starts.
if($msg!='') {?>
<table cellpadding="0" cellspacing="0" width="100%" style="border: 2px solid rgb(255, 0, 0); height:40px;margin-bottom">
	<tr>
		<td style="padding:8px">
			<font color="#FF0000"><b><?php echo $msg; ?></b></font>
		</td>
	</tr>
</table>
<?php } ?>

<!--Jevent Form Starts-->
<div style="padding:10px"></div>
<h2>Env&#237;enos sus eventos</h2>
<div id="jevents" >
<form action="" method="post" name="adminForm" enctype='multipart/form-data' onSubmit="return form_validation()">
<div style='width:500px;'>
<div class="adminform" align="left" style="width:600px">

	<table width="94%" cellpadding="5" cellspacing="2" border="1"  class="adminform" id="jevadminform">
	<tr>
		<td align="left">Nombre del Evento:</td>
		<td align="right"><input class="inputbox" type="text" name="title" size="60" maxlength="255" value="<?=$postValues['title']?>" /></td>
		<td colspan="2"><input type="hidden" name="priority" value="0" /></td>
	</tr>
	<tr>
		<td valign="top" align="left">categor&#237;as</td>
			<?php $cat_query=mysql_query("select * from jos_categories where section='com_jevents' and published='1'");?> 
		<td style="width:200px" >
			<select name="catid" id="catid">
				<option value="0" >Seleccione una categor&#237;a</option>
				<?php while($row=mysql_fetch_array($cat_query)) { 
				if($postValues['catid']==$row['id']){
					$selectedVal = 'selected';
				}else{
					$selectedVal = '';
				}
				?>
				
				<option value="<?php echo $row['id']?>" <?=$selectedVal?> ><?php echo $row['name']?></option>
				<?php } ?>
			</select>
		</td>
		<td colspan="2"></td>
	</tr>
	<tr>
		<td colspan='4'><input type="hidden" name="ics_id" value="<?php echo $ics?>" /></td>		
	</tr>
	<tr>
		<td valign="top" align="left" colspan="4">
		  	<div style="clear:both;width:700px">
				<fieldset class="jev_sed">
					<legend>Inicio, Fin, Duraci&#243;n</legend>
					<span>
						<span >Todo el d&#237;a o el tiempo no especificado</span>
						<span><input type="checkbox" id='allDayEvent' name='allDayEvent' <?php if($postValues['allDayEvent']=='on') {echo 'checked'; }?>  onclick="alldayeventtog()" />
						</span>
					</span>
					<span style="margin:20px" class='checkbox12h'>
						<span><input type="hidden" id='view12Hour' name='view12Hour' checked='checked' onClick="toggleView12Hour();" value="1"/></span>
					</span>
					<div>
						<fieldset>
							<legend>Fecha de Inicio</legend>
							<div style="float:left">
								<?php 
									if(empty($postValues['publish_up'])){ 
										$publish_up_value = date("Y-m-d");
									}else{ 
										$publish_up_value = $postValues['publish_up']; 
									} 
								?>
								<input type="text" name="publish_up" id="publish_up" value="<?php echo $publish_up_value;?>" maxlength="10" onChange="var elem = $('publish_up');checkDates(elem);" size="10"  />         
							</div>
							<div style="float:left;margin-left:11px!important;">Hora de Inicio&nbsp;
								<span id="start_12h_area" style="display:inline">
								
								<?php 
									if(empty($postValues['start_12h'])){ 
										$start_12h_value = '08:00';
									}else{ 
										$start_12h_value = $postValues['start_12h']; 
									} 

									if($postValues['start_ampm']=='pm'){ 
										$start_ampm_check = 'checked="checked"';
									} 

									$end_ampm_check = array();
									if($postValues['start_ampm']=='pm'){ 
										$end_ampm_check['pm'] = 'checked="checked"';
										$end_ampm_check['am'] = '';
									}else{
										$end_ampm_check['pm'] = '';
										$end_ampm_check['am'] = 'checked="checked"';
									}
								?>

								<input class="inputbox" type="text" name="start_12h" id="start_12h" size="8" maxlength="8"  value="<?=$start_12h_value?>" onChange="check12hTime(this);" />
								<input type="radio" name="start_ampm" id="startAM" value="am" <?=$end_ampm_check['am']?> checked="checked" onClick="toggleAMPM('startAM');"  />am  <input type="radio" name="start_ampm" id="startPM" value="pm" <?=$end_ampm_check['pm']?> onClick="toggleAMPM('startPM');"  />pm		</span>
							</div>
						</fieldset>
					</div>
					<div>
						<fieldset><legend>Fecha de Finalizaci&#243;n</legend>
						<div style="float:left">
						<?php 
							if(empty($postValues['publish_down'])){ 
								$publish_down_value = date("Y-m-d");
							}else{ 
								$publish_down_value = $postValues['publish_down']; 
							} 
						?>
					<input type="text" name="publish_down" id="publish_down" value="<?php echo $publish_down_value;?>" maxlength="10" onChange="var elem = $('publish_up');checkDates(elem);" size="10"  />         
						</div>
						<div style="float:left;margin-left:11px!important">Hora de Finalizaci&#243;n&nbsp;
							<span id="end_12h_area" style="display:inline">
							<?php 
								if(empty($postValues['end_12h'])){ 
									$end_12h_value = '05:00';
								}else{ 
									$end_12h_value = $postValues['end_12h']; 
								} 

								$end_ampm_check = array();
								if($postValues['end_ampm']=='am'){ 
									$end_ampm_check['am'] = 'checked="checked"';
									$end_ampm_check['pm'] = '';
								}else{
									$end_ampm_check['am'] = '';
									$end_ampm_check['pm'] = 'checked="checked"';
								}

							?>
							<input class="inputbox" type="text" name="end_12h" id="end_12h" size="8" maxlength="8"  value="<?php echo $end_12h_value;?>" onChange="check12hTime(this);" />
							<input type="radio" name="end_ampm" id="endAM" value="am" <?=$end_ampm_check['am']?>  onclick="toggleAMPM('endAM');"  />am 
							<input type="radio" name="end_ampm" id="endPM" value="pm" <?=$end_ampm_check['pm']?> onClick="toggleAMPM('endPM');" />pm	
							</span>
							<span style="margin-left:10px">
								<span><input type="checkbox" id='noendtime' name='noendtime'  onclick="noendtimetog();" <?php if($postValues['noendtime']==1) {echo 'checked'; }?> value="1" />
								<span >No hay tiempo de finalizaci&#243;n espec&#237;fica</span>
								</span>
							</span>
						</div>
						</fieldset>
					</div>
				</fieldset>
			</div>
			<div style="clear:both;"></div>

		</td>
	</tr>
	<tr>
		<td style="vertical-align:top" align="left">Descripci&#243;n</td>
		<td colspan="3">
			<div id='jeveditor' style="width:457px"><textarea id="jevcontent" name="jevcontent" cols="70" rows="10" style="width:100%;height:230px;" class="mceEditor"><?=$postValues['jevcontent']?></textarea>
			</div>       	
		</td>
	</tr>
	<tr id="jeveditlocation">
		<td width="130" align="left" style="vertical-align:top;">Ubicaci&#243;n</td>
		<td colspan="3">
			<input type="hidden" name="location" id="locn" value=""/>
			<input type="text" name="evlocation_notused" disabled="disabled" id="evlocation" value=" -- " style="float:left"/>
			<div class="button2-left">
				<div class="blank"><a href="javascript:selectLocation('' ,'/indexiphone.php?option=com_jevlocations&amp;task=locations.select&amp;tmpl=component','750','500')" title="Select Location"  >Seleccionar</a>
				</div>
			</div>
			<div class="button2-left">
				<div class="blank"><a href="javascript:removeLocation();" title="Remove Location"  >eliminar</a>
				</div>
			</div>
			<div style="font-size:10px; vertical-align:top;float:left;">Si su ubicaci&#243;n no est&#225; en la lista, por favor incluya la direcci&#243;n completa en la descripci&#243;n.</div>
	         </td>
	</tr>
	<tr class="jevplugin_anonusername">
		<td valign="top"  width="130" align="left">Su Nombre</td>
		<td colspan="3"><input size="50" type="text" name="custom_anonusername" id="custom_anonusername" value="<?=$postValues['custom_anonusername']?>" /></td>
	</tr>
	<tr class="jevplugin_anonemail">
		<td valign="top"  width="130" align="left">Su direcci&#243;n de correo Electr&#243;nico</td>
		<td colspan="3"><input size="50" type="text" name="custom_anonemail" id="custom_anonemail" value="<?=$postValues['custom_anonemail']?>" /></td>
	</tr>
	
	<!--#DD#-->
	<tr class="jevplugin_anonemail">	
	<td>&nbsp;</td>
	<td>
		<div style="font-size: 11px; vertical-align: top; float: left;">Escribe los caracteres que ves en la imagen siguiente.</div><br>
			<img id="siimage" align="left" style="width:150px;" style="padding-right: 5px; border: 0" src="securimage/securimage_show.php?sid=<?php echo md5(time()) ?>" />
			<a tabindex="-1" style="border-style: none" href="#" title="Refresh Image" onClick="document.getElementById('siimage').src = 'securimage/securimage_show.php?sid=' + Math.random(); return false"><img src="securimage/images/refresh.gif" alt="Reload Image" border="0" onClick="this.blur()" align="bottom" /></a>
		</td>
	</tr>
	<tr class="jevplugin_anonemail">	
	<td>C&#243;digo de Verificaci&#243;n</td>
	<td>
		<input type="text" value="" id="code" name="code" size="25">
		<br><br>
		</td>
	</tr>
	<!--#DD#-->
	
	</table>
</div>
<input type="hidden" name="custom_field4" value="0" />

<table align="left" style="" width="30%" cellpadding="0" cellspacing="0">
<tbody><tr>
		<td id="toolbar-save" valign="top"style="padding-right:3px">
			<a style="cursor:pointer;height:21px;"><input src="images/save-btn.gif" type="submit" name="action" value="Ahorrar" class="button"/></a>
		</td>
		<td id="toolbar-save" valign="top"style="padding-right:3px">
			<a style="cursor:pointer;height:21px;">
			<input type="button" name="can" id="can" value="Cancelar" class="button" onClick="gotoindex(this.id)"/></a>
		</td>

	</tr></tbody>
</table>
</form>

</div>
<!--Jevent Form Ends-->