<?php
/**
* @version 1.2.0
* @package RSform!Pro 1.2.0
* @copyright (C) 2007-2009 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


class rsform_HTML
{

	function richtextShow($formId, $openerId, $value, $additionalHTML)
	{
        $RSadapter = $GLOBALS['RSadapter'];
        
        echo '<form action="" method="post" name="richtext">';
        echo '<input type="button" onclick="document.getElementById(\'act\').value = \'save\';document.richtext.submit();" value="Save"/>&nbsp;&nbsp;&nbsp;';
        echo '<input type="button" onclick="document.getElementById(\'act\').value = \'saveclose\';document.richtext.submit();" value="Save &amp; Close"/>&nbsp;&nbsp;&nbsp;';
        echo '<input type="button" onclick="window.close();" value="Close"/>&nbsp;&nbsp;&nbsp;';
        echo $RSadapter->WYSIWYG($openerId,  $value , $openerId, '600', '250', '75', '50');
        echo '<input type="hidden" id="act" name="act" value="save"/>';
        echo '</form>';
        echo $additionalHTML;
	}
	
//////////////////////////////////////// FORMS ///////////////////////////////////

    /**
    * @desc Forms Manager Screen
    */
    function formsManage($rows)
    {
        global $option;
        $RSadapter = $GLOBALS['RSadapter'];
        $RSadapter->addHeadTag(_RSFORM_FRONTEND_REL_PATH.'/controller/functions.js','js');
        ?>
        <form action="<?php echo _RSFORM_BACKEND_SCRIPT_PATH;?>" method="post" name="adminForm" id="adminForm">
        <table border="0" width="100%" class="adminform">
        <tr>
            <th width="5"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" /></th>
            <th class="title"><?php echo _RSFORM_BACKEND_FORMS_MANAGE_TITLE; ?></th>
            <th class="title"><?php echo _RSFORM_BACKEND_FORMS_MANAGE_NAME; ?></th>
            <th class="title"><?php echo _RSFORM_BACKEND_FORMS_MANAGE_PUBLISHED; ?></th>
            <th class="title"><?php echo _RSFORM_BACKEND_FORMS_MANAGE_SUBMISSIONS; ?></th>
            <th class="title" width="80"><?php echo _RSFORM_BACKEND_FORMS_MANAGE_LINK; ?></th>
            <th class="title"><?php echo _RSFORM_BACKEND_FORMS_MANAGE_PREVIEW; ?></th>
            <th class="title"><?php echo _RSFORM_BACKEND_FORMS_MANAGE_ID; ?></th>
        </tr>
        <?php
        $i = 0;
        foreach($rows as $row)
        {
            $task = $row['Published'] ? 'forms.unpublish' : 'forms.publish';
            $img = $row['Published'] ? 'publish_g.png' : 'publish_x.png';

            echo '<tr>';
                echo '<td><input type="checkbox" id="cb'.$i.'" name="cid[]" value="'.$row['FormId'].'" onclick="isChecked(this.checked);" /></td>';
                echo '<td><a href="'._RSFORM_BACKEND_SCRIPT_PATH.'?option='.$option.'&task=forms.edit&formId='.$row['FormId'].'">'.(!empty($row['FormTitle']) ? $row['FormTitle'] : '<em>no title</em>').'</a></td>';
                echo '<td>'.$row['FormName'].'</td>';
                echo '<td><a href="javascript: void(0);" onclick="return listItemTask('."'cb{$i}','{$task}'".')"><img src="images/'.$img.'" width="12" height="12" border="0" alt="" /></a></td>';
                echo '<td><a href="'._RSFORM_BACKEND_SCRIPT_PATH.'?option=com_rsform&task=submissions.manage&formId='.$row['FormId'].'">'.
                        _RSFORM_BACKEND_FORMS_MANAGE_TODAY.$row['_todaySubmissions'].'<br/>'.
                        _RSFORM_BACKEND_FORMS_MANAGE_MONTH.$row['_monthSubmissions'].'<br/>'.
                        _RSFORM_BACKEND_FORMS_MANAGE_ALL.$row['_allSubmissions'].'<br/>'.
                        '</a></td>';
                echo '<td><div align="center"><a href="'._RSFORM_BACKEND_SCRIPT_PATH.'?option=com_rsform&task=forms.menuadd.screen&formId='.$row['FormId'].'"><img src="components/com_rsform/images/mainmenu.png" border="0"/></a></div></td>';
                echo '<td><a href="'._RSFORM_FRONTEND_SCRIPT_PATH.'?option=com_rsform&amp;formId='.$row['FormId'].'&Itemid=99999" target="_blank">'._RSFORM_BACKEND_FORMS_MANAGE_PREVIEW.'</a></td>';
                echo "<td>{$row['FormId']}</td>";
            echo '</tr>';
            $i++;
        }

        ?>
        </table>
        <input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="option" value="<?php echo $option; ?>" />
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="hidemainmenu" value="0">
        </form>
        <?php
    }

	/**
	 * Forms Editor Screen
	 *
	 * @param int $formId
	 * @param array $row
	 */
    function formsEdit($formId, $row)
    {
        global $option,$mainframe;
        $RSadapter = $GLOBALS['RSadapter'];
        $RSadapter->addHeadTag(_RSFORM_BACKEND_REL_PATH.'/style.css','css','other');
        $RSadapter->addHeadTag(_RSFORM_FRONTEND_REL_PATH.'/controller/functions.js','js','other');
        $RSadapter->addHeadTag(_RSFORM_FRONTEND_REL_PATH.'/controller/ajax.js','js','other');

        $RSadapter->initTabs(1);
        ?>
        <div id="state"></div>
        <?php
        	$RSadapter->startPane('content-pane');
            $RSadapter->startTab(_RSFORM_BACKEND_FORMS_EDIT_TITLE_FORM_COMPONENTS,"form-components");
        ?>

		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'forms.cancel') {
				submitform( pressbutton );
				return;
			}

			// do field validation
			if (document.getElementById('FormName').value == ""){
				alert( '<?php echo _RSFORM_BACKEND_COMPONENTS_VALIDATE_ERROR_UNIQUE_NAME;?>' );
			} else {
				if(pressbutton!='false')submitform(pressbutton);
			}
			
		}
		
		function transferItems(){
			var form = document.adminForm;
			
			
			
				form.FormName.value 				= document.getElementById('FormName').value;
				form.FormLayout.value 				= document.getElementById('formLayout').value;
		    	form.FormTitle.value 				= document.getElementById('FormTitle').value;
		    	form.ReturnUrl.value = document.getElementById('ReturnUrl').value;
		    	form.UserEmailTo.value = document.getElementById('UserEmailTo').value;
		    	form.UserEmailCC.value = document.getElementById('UserEmailCC').value;
		    	form.UserEmailBCC.value = document.getElementById('UserEmailBCC').value;
		    	form.UserEmailFrom.value = document.getElementById('UserEmailFrom').value;
		    	form.UserEmailReplyTo.value = document.getElementById('UserEmailReplyTo').value;
		    	form.UserEmailFromName.value = document.getElementById('UserEmailFromName').value;
		    	form.UserEmailSubject.value = document.getElementById('UserEmailSubject').value;
		    	form.UserEmailMode.value = (document.getElementById('UserEmailMode0').checked ? 0:1);
				form.UserEmailAttach.value = (document.getElementById('UserEmailAttach0').checked ? 0:1);
				form.UserEmailAttachFile.value = document.getElementById('UserEmailAttachFile').value;
		    	if(form.AdminEmailMode.value==0) form.UserEmailText.value = document.getElementById('UserEmailText').value;
		    	if(form.AdminEmailMode.value==0) form.AdminEmailText.value = document.getElementById('AdminEmailText').value;
		    	form.AdminEmailTo.value = document.getElementById('AdminEmailTo').value;
		    	form.AdminEmailCC.value = document.getElementById('AdminEmailCC').value;
		    	form.AdminEmailBCC.value = document.getElementById('AdminEmailBCC').value;
		    	form.AdminEmailFrom.value = document.getElementById('AdminEmailFrom').value;
		    	form.AdminEmailReplyTo.value = document.getElementById('AdminEmailReplyTo').value;
		    	form.AdminEmailFromName.value = document.getElementById('AdminEmailFromName').value;
		    	form.AdminEmailSubject.value = document.getElementById('AdminEmailSubject').value;
		    	form.AdminEmailMode.value = (document.getElementById('AdminEmailMode0').checked ? 0:1);
		    	form.ScriptProcess.value = document.getElementById('ScriptProcess').value;
		    	form.ScriptProcess2.value = document.getElementById('ScriptProcess2').value;
		    	form.ScriptDisplay.value = document.getElementById('ScriptDisplay').value;
				form.MetaTitle.value = document.getElementById('MetaTitle0').checked ? 0 : 1;
		    	form.MetaDesc.value = document.getElementById('MetaDesc').value;
		    	form.MetaKeywords.value = document.getElementById('MetaKeywords').value;
		    	form.Required.value = document.getElementById('Required').value;
		    	document.adminForm.submit();
		}
		</script>
        <table border="0" width="100%" class="adminform">
            <tr>
                <td class="components" valign="top">
                    <form method="post" action="<?php echo _RSFORM_BACKEND_SCRIPT_PATH;?>?option=<?php echo $option;?>&amp;task=forms.edit&amp;formId=<?php echo $formId; ?>" id="formComponentEdit">
                    <?php $mainframe->triggerEvent('rsfp_onBeforeShowComponents');?>
                    <a href="#" onclick="displayTemplate('1');return false;" class="component" id="textfield">
                        <?php echo _RSFORM_BACKEND_FORMS_EDIT_TEXTBOX;?>
                    </a><div title="componentEdit" id="componentEdit1" class="componentEdit"></div>

                    <a href="#" onclick="displayTemplate('2');return false;" class="component" id="textarea">
                        <?php echo _RSFORM_BACKEND_FORMS_EDIT_TEXTAREA;?>
                    </a><div title="componentEdit" id="componentEdit2" class="componentEdit"></div>

                    <a href="#" onclick="displayTemplate('3');return false;" class="component" id="select">
                        <?php echo _RSFORM_BACKEND_FORMS_EDIT_DROPDOWN;?>
                    </a><div title="componentEdit" id="componentEdit3" class="componentEdit"></div>

                    <a href="#" onclick="displayTemplate('4');return false;" class="component" id="check">
                        <?php echo _RSFORM_BACKEND_FORMS_EDIT_CHECKBOX;?>
                    </a><div title="componentEdit" id="componentEdit4" class="componentEdit"></div>

                    <a href="#" onclick="displayTemplate('5');return false;" class="component" id="radio">
                        <?php echo _RSFORM_BACKEND_FORMS_EDIT_RADIO;?>
                    </a><div title="componentEdit" id="componentEdit5" class="componentEdit"></div>

                    <a href="#" onclick="displayTemplate('13');return false;" class="component" id="button">
                        <?php echo _RSFORM_BACKEND_FORMS_EDIT_SUBMITBUTTON;?>
                    </a><div title="componentEdit" id="componentEdit13" class="componentEdit"></div>

                    <a href="#" onclick="displayTemplate('14');return false;" class="component" id="password">
                        <?php echo _RSFORM_BACKEND_FORMS_EDIT_PASSWORD;?>
                    </a><div title="componentEdit" id="componentEdit14" class="componentEdit"></div>

                    <a href="#" onclick="displayTemplate('9');return false;" class="component" id="upload">
                        <?php echo _RSFORM_BACKEND_FORMS_EDIT_FILE;?>
                    </a><div title="componentEdit" id="componentEdit9" class="componentEdit"></div>

                    <a href="#" onclick="displayTemplate('10');return false;" class="component" id="freetext">
                        <?php echo _RSFORM_BACKEND_FORMS_EDIT_FREETEXT;?>
                    </a><div title="componentEdit" id="componentEdit10" class="componentEdit"></div>

                    <a href="#" onclick="displayTemplate('6');return false;" class="component" id="calendar">
                        <?php echo _RSFORM_BACKEND_FORMS_EDIT_CALENDAR;?>
                    </a><div title="componentEdit" id="componentEdit6" class="componentEdit"></div>

                    <a href="#" onclick="displayTemplate('7');return false;" class="component" id="button">
                        <?php echo _RSFORM_BACKEND_FORMS_EDIT_BUTTON;?>
                    </a><div title="componentEdit" id="componentEdit7" class="componentEdit"></div>

                    <a href="#" onclick="displayTemplate('12');return false;" class="component" id="image">
                        <?php echo _RSFORM_BACKEND_FORMS_EDIT_IMAGEBUTTON;?>
                    </a><div title="componentEdit" id="componentEdit12" class="componentEdit"></div>

                    <a href="#" onclick="displayTemplate('8');return false;" class="component" id="captcha">
                        <?php echo _RSFORM_BACKEND_FORMS_EDIT_CAPTCHA;?>
                    </a><div title="componentEdit" id="componentEdit8" class="componentEdit"></div>

                    <a href="#" onclick="displayTemplate('11');return false;" class="component" id="hidden">
                        <?php echo _RSFORM_BACKEND_FORMS_EDIT_HIDDEN;?>
                    </a><div title="componentEdit" id="componentEdit11" class="componentEdit"></div>

                    <a href="#" onclick="displayTemplate('15');return false;" class="component" id="ticket">
                        <?php echo _RSFORM_BACKEND_FORMS_EDIT_TICKET;?>
                    </a><div title="componentEdit" id="componentEdit15" class="componentEdit"></div>

                    <?php
                    	$mainframe->triggerEvent('rsfp_bk_onAfterShowComponents');
                    ?>
                    
                    <input type="hidden" name="formId" id="formId" value="<?php echo $formId; ?>">
                    <input type="hidden" name="componentIdToEdit" id="componentIdToEdit" value="-1">
                    <input type="hidden" name="componentEditForm" id="componentEditForm" value="-1">
                    </form>
                </td>
                <td valign="top" class="componentPreview">
						<div class="rsform_error" id="rsform_layout_msg" <?php if ($row['FormLayoutAutogenerate']) { ?>style="display: none"<?php } ?>><?php echo _RSFORM_BACKEND_FORMS_EDIT_LAYOUT_AUTOGENERATE_MSG; ?></div>
                    <form method="post" action="<?php echo _RSFORM_BACKEND_SCRIPT_PATH;?>?option=<?php echo $option;?>&amp;task=forms.edit&amp;formId=<?php echo $formId; ?>" id="formComponentOrder">
                        <input type="hidden" name="formId" id="formId" value="<?php echo $formId; ?>">
                        <table border="0" id="componentPreview" class="adminform">
                            <tr>
                            	<th width="1"><input type="hidden" value="-2" name="previewComponentId"/></th>
                                <th class="title"><input type="checkbox" name="checks[]" onclick="checkAll();"/></th>
                                <th class="title"><?php echo _RSFORM_BACKEND_FORMS_EDIT_COMPREVIEW_NAME;?></th>
                                <th class="title"><?php echo _RSFORM_BACKEND_FORMS_EDIT_COMPREVIEW_CAPTION;?></th>
                                <th class="title"><?php echo _RSFORM_BACKEND_FORMS_EDIT_COMPREVIEW_PREVIEW;?></th>
                                <th class="title"><?php echo _RSFORM_BACKEND_FORMS_EDIT_COMPREVIEW_EDIT;?></th>
                                <th class="title"><?php echo _RSFORM_BACKEND_FORMS_EDIT_COMPREVIEW_REMOVE;?></th>
                                <th class="title" colspan="2"><?php echo _RSFORM_BACKEND_FORMS_EDIT_COMPREVIEW_ORDERING;?></th>
                                <th class="title"><a href="javascript: document.getElementById('formComponentOrder').submit();"><img src="images/filesave.png" border="0" width="16" height="16" alt="<?php echo _RSFORM_BACKEND_FORMS_EDIT_COMPREVIEW_SAVE_ORDER;?>" /></a></th>
                                <th class="title"><?php echo _RSFORM_BACKEND_FORMS_EDIT_COMPREVIEW_PUBLISHED;?></th>
                            </tr>
                            <?php RSinitForm($formId); ?>
                        </table>
                    </form>
                </td>
                <td>&nbsp;</td>
            </tr>
        </table>
        <?php
        $RSadapter->endTab();
        $RSadapter->startTab(_RSFORM_BACKEND_FORMS_EDIT_TITLE_FORM_LAYOUT,"form-layout");
        ?>
        <table border="1" width="100%" class="adminform">
			<tr>
				<td><strong><?php echo _RSFORM_BACKEND_FORMS_EDIT_LAYOUT_CLASSIC; ?></strong></td>
			</tr>
            <tr>
                <td valign="top">
                    <div style="border:1px solid #cccccc;width:185px;margin:5px;padding:2px;float:left;">
                        <label for="formLayoutInline">
                            <input type="radio" id="formLayoutInline" name="formLayoutOption" value="inline" onclick="saveLayoutName('<?php echo $formId; ?>','inline');" <?php if (RSreturnCheckedLayoutName($formId,'inline')) echo 'checked="checked"'; ?> /><?php echo _RSFORM_BACKEND_FORMS_EDIT_LAYOUT_INLINE;?><br/>
                            <img src="<?php echo _RSFORM_BACKEND_REL_PATH."/images/layouts/inline.gif";?>" width="175"/>
                        </label>
                    </div>
                    <div style="border:1px solid #cccccc;width:185px;margin:5px;padding:2px;float:left;">
                        <label for="formLayout2lines">
                            <input type="radio" id="formLayout2lines" name="formLayoutOption" value="2lines" onclick="saveLayoutName('<?php echo $formId; ?>','2lines')" <?php if (RSreturnCheckedLayoutName($formId,'2lines')) echo 'checked="checked"'; ?>/><?php echo _RSFORM_BACKEND_FORMS_EDIT_LAYOUT_2LINES;?><br/>
                            <img src="<?php echo _RSFORM_BACKEND_REL_PATH."/images/layouts/2lines.gif";?>" width="175"/>
                        </label>
                    </div>
                    <div style="border:1px solid #cccccc;width:185px;margin:5px;padding:2px;float:left;">
                        <label for="formLayout2colsinline">
                            <input type="radio" id="formLayout2colsinline" name="formLayoutOption" value="2colsinline" onclick="saveLayoutName('<?php echo $formId; ?>','2colsinline')" <?php if (RSreturnCheckedLayoutName($formId,'2colsinline')) echo 'checked="checked"'; ?> /><?php echo _RSFORM_BACKEND_FORMS_EDIT_LAYOUT_2COLSINLINE;?><br/>
                            <img src="<?php echo _RSFORM_BACKEND_REL_PATH."/images/layouts/2colsinline.gif";?>" width="175"/>
                        </label>
                    </div>
                    <div style="border:1px solid #cccccc;width:185px;margin:5px;padding:2px;float:left;">
                        <label for="formLayout2cols2lines">
                            <input type="radio" id="formLayout2cols2lines" name="formLayoutOption" value="2cols2lines" onclick="saveLayoutName('<?php echo $formId; ?>','2cols2lines')" <?php if (RSreturnCheckedLayoutName($formId,'2cols2lines')) echo 'checked="checked"'; ?>/><?php echo _RSFORM_BACKEND_FORMS_EDIT_LAYOUT_2COLS2LINES;?><br/>
                            <img src="<?php echo _RSFORM_BACKEND_REL_PATH."/images/layouts/2cols2lines.gif";?>" width="175"/>
                        </label>
                    </div>
				</td>
			</tr>
			<tr>
				<td><strong><?php echo _RSFORM_BACKEND_FORMS_EDIT_LAYOUT_XHTML; ?></strong></td>
			</tr>
			<tr>
                <td valign="top">
				<div style="border:1px solid #cccccc;width:185px;margin:5px;padding:2px;float:left;">
					<label for="formLayoutInlineXhtml">
						<input type="radio" id="formLayoutInlineXhtml" name="formLayoutOption" value="inline-xhtml" onclick="saveLayoutName('<?php echo $formId; ?>','inline-xhtml');" <?php if (RSreturnCheckedLayoutName($formId,'inline-xhtml')) echo 'checked="checked"'; ?> /><?php echo _RSFORM_BACKEND_FORMS_EDIT_LAYOUT_INLINE_XHTML;?><br/>
						<img src="<?php echo _RSFORM_BACKEND_REL_PATH."/images/layouts/inline-xhtml.gif";?>" width="175"/>
					</label>
				</div>
				<div style="border:1px solid #cccccc;width:185px;margin:5px;padding:2px;float:left;">
                        <label for="formLayout2linesXhtml">
                            <input type="radio" id="formLayout2linesXhtml" name="formLayoutOption" value="2lines-xhtml" onclick="saveLayoutName('<?php echo $formId; ?>','2lines-xhtml')" <?php if (RSreturnCheckedLayoutName($formId,'2lines-xhtml')) echo 'checked="checked"'; ?>/><?php echo _RSFORM_BACKEND_FORMS_EDIT_LAYOUT_2LINES_XHTML;?><br/>
                            <img src="<?php echo _RSFORM_BACKEND_REL_PATH."/images/layouts/2lines-xhtml.gif";?>" width="175"/>
                        </label>
                    </div>
				</td>
			</tr>
			<tr>
				<td>
					<input type="hidden" name="formLayoutStyle" id="formLayoutStyle" value="<?php echo RSgetFormLayoutName($formId); ?>"/>
					<button type="button" onclick="generateLayout('<?php echo $formId; ?>');"><?php echo _RSFORM_BACKEND_FORMS_EDIT_LAYOUT_GENERATE_LAYOUT; ?></button>
					<label for="FormLayoutAutogenerate"><?php echo _RSFORM_BACKEND_FORMS_EDIT_LAYOUT_AUTOGENERATE;?> <input type="checkbox" name="FormLayoutAutogenerate" id="FormLayoutAutogenerate" value="1" <?php echo $row['FormLayoutAutogenerate'] ? 'checked':'';?> onclick="changeFormAutoGenerateLayout('<?php echo $formId; ?>');" /></label>
				</td>
			</tr>
		</table>
       <table width="100%" style="clear:both;">
			<tr>
				<td width="1%" valign="top">
			       <table width="100%" style="clear:both;">
						<tr>
							<td width="1%">
				    			<textarea name="FormLayout" id="formLayout" style="width: 800px; height: 450px;" <?php echo $row['FormLayoutAutogenerate'] ? 'readonly':'';?>><?php echo RSgetFormLayout($formId); ?></textarea>
				    		</td>
				    		<td valign="top">
				    		</td>
				    	</tr>
				    </table>
                </td>
                <td valign="top">
	                <div style="width:200px;">
	                    			<h3><?php echo _RSFORM_BACKEND_FORMS_EDIT_COMP_QUICKADD;?></h3>
	                    			<?php echo _RSFORM_BACKEND_FORMS_EDIT_COMP_QUICKADD_DESC;?><br/><br/>
				                    <?php
				                    	$components = RSlistComponents($formId);
				                    	if(!empty($components)){
				                    		foreach($components as $component){
				                    			$str =
				                    			'<pre>{'.$component->PropertyValue.':'._RSFORM_BACKEND_FORMS_EDIT_COMP_CAPTION.'}'."<br/>".
				                    			'{'.$component->PropertyValue.':'._RSFORM_BACKEND_FORMS_EDIT_COMP_BODY.'}'."<br/>".
				                    			'{'.$component->PropertyValue.':'._RSFORM_BACKEND_FORMS_EDIT_COMP_VALIDATION.'}'."<br/>".
				                    			'{'.$component->PropertyValue.':'._RSFORM_BACKEND_FORMS_EDIT_COMP_DESCRIPTION.'}'."<br/></pre>";
				                    			?>
				                    			<strong><?php echo $component->PropertyValue;?></strong><br/>
				                    			<?php echo $str;?><br/>
					                    		<?php
				                    		}
				                    	}
				                    ?>
				   </div>
                </td>
            </tr>
        </table>
        <?php
       $RSadapter->endTab();
       $RSadapter->startTab(_RSFORM_BACKEND_FORMS_EDIT_TITLE_FORM_EDIT,"form-edit");
        ?>
            <table cellpadding="4" cellspacing="0" border="0" class="adminform">
            <tr>
                  <th colspan="2"><?php echo _RSFORM_BACKEND_FORMS_EDIT_FORM_EDIT_HEAD; ?></th>
              </tr>
              <tr>
                  <td valign="top" align="left" width="1%">
                      <table>
                          <tr>
                            <td><?php echo _RSFORM_BACKEND_FORMS_EDIT_FORM_EDIT_TITLE; ?><br/>
                            <input name="FormTitle" value="<?php echo $row['FormTitle']; ?>" size="105" id="FormTitle"></td>
                          </tr>
                          <tr>
                            <td></td>
                             </tr>
                          <tr>
                            <td><?php echo _RSFORM_BACKEND_FORMS_EDIT_FORM_EDIT_NAME; ?><br/>
                            <input name="FormName" value="<?php echo $row['FormName']; ?>" size="105" id="FormName"></td>
                          </tr>
                          <tr>
                            <td><?php echo _RSFORM_BACKEND_FORMS_EDIT_FORM_EDIT_RETURN; ?><br/>
                            <input name="ReturnUrl" value="<?php echo $row['ReturnUrl']; ?>" size="105" id="ReturnUrl"><br/>
                            <?php echo _RSFORM_BACKEND_FORMS_EDIT_FORM_EDIT_RETURN_DESC; ?>
                            </td>
                          </tr>
                          <tr>
                            <td>
								<button onclick="window.open('index3.php?option=com_rsform&task=richtext.show&openerId=Thankyou&formId=<?php echo $formId;?>','Richtext','width=600,height=400');return false;" type="button"><?php echo _RSFORM_BACKEND_FORMS_EDIT_FORM_EDIT_THANKYOU; ?></button><br/>
                             <div style="border:1px solid #CCCCCC;margin: 5px;" id="Thankyou"><?php echo $row['Thankyou']; ?></div>
                            </td>
                          </tr>
                          <tr>
                              <td><?php echo _RSFORM_BACKEND_FORMS_EDIT_FORM_EDIT_THANKYOU_DESC; ?></td>
                          </tr>
						  <tr>
								<td><?php echo _RSFORM_BACKEND_FORMS_EDIT_FORM_EDIT_REQUIRED; ?><br />
								<input name="Required" value="<?php echo htmlentities($row['Required']); ?>" size="105" id="Required"></td>
						  </tr>
						  <tr>
								<td><?php echo _RSFORM_BACKEND_FORMS_EDIT_FORM_EDIT_ERROR_MESSAGE; ?><br />
								<button onclick="window.open('index3.php?option=com_rsform&task=richtext.show&openerId=ErrorMessage&formId=<?php echo $formId;?>','Richtext','width=600,height=400');return false;" type="button"><?php echo _RSFORM_BACKEND_FORMS_EDIT_FORM_EDIT_ERROR_MESSAGE_BUTTON; ?></button><br/>
								<div style="border:1px solid #CCCCCC;margin: 5px;" id="ErrorMessage"><?php echo $row['ErrorMessage']; ?></div>
						  </tr>
						  <tr>
                              <td><?php echo _RSFORM_BACKEND_FORMS_EDIT_FORM_EDIT_ERROR_MESSAGE_DESC; ?></td>
                          </tr>
                      </table>
                  </td>
		            <td valign="top">
		            	 <div style="width:200px;">
		            			<h3><?php echo _RSFORM_BACKEND_FORMS_EDIT_COMP_QUICKADD;?></h3>
		            			<?php echo _RSFORM_BACKEND_FORMS_EDIT_COMP_QUICKADD_DESC;?><br/><br/>
			                    <?php
			                    	$components = RSlistComponents($formId);
			                    	if(!empty($components)){
			                    		foreach($components as $component){
			                    			$str =
			                    			'{'.$component->PropertyValue.':'._RSFORM_BACKEND_FORMS_EDIT_COMP_CAPTION.'}'."<br/>".
			                    			'{'.$component->PropertyValue.':'._RSFORM_BACKEND_FORMS_EDIT_COMP_VALUE.'}'."<br/>";
			                    			?>
			                    			<strong><?php echo $component->PropertyValue;?></strong><br/>
			                    			<?php echo $str;?><br/>
				                    		<?php
			                    		}
			                    	}
			                    ?>
		                </div>
		            </td>
              </tr>

            </table>

        <?php
        $RSadapter->endTab();
        $RSadapter->startTab(_RSFORM_BACKEND_FORMS_EDIT_TITLE_USER_EMAILS,"email-user");
        ?>
        <table cellpadding="4" cellspacing="0" border="0" class="adminform">
        <tr>
              <th colspan="2"><?php echo _RSFORM_BACKEND_FORMS_EDIT_USER_EMAILS_HEAD; ?></th>
          </tr>
          <tr>
              <td valign="top" align="left" width="1%">
                  <table>
                      <tr>
                        <td width="80">
                        	<?php echo _RSFORM_BACKEND_FORMS_EDIT_USER_EMAILS_FROM; ?>
                        </td>
                        <td>
                        	<input name="UserEmailFrom"  id="UserEmailFrom" value="<?php echo $row['UserEmailFrom']; ?>" size="35">&nbsp;&nbsp;
                        	<?php echo _RSFORM_BACKEND_FORMS_EDIT_USER_EMAILS_FROMNAME; ?>
                        	<input name="UserEmailFromName" id="UserEmailFromName" value="<?php echo $row['UserEmailFromName']; ?>" size="20">
                        </td>
                      </tr>
					  <tr>
                        <td>
                        	Reply to:
                        </td>
						<td>
                        	<input name="UserEmailReplyTo" id="UserEmailReplyTo" value="<?php echo $row['UserEmailReplyTo']; ?>" style="width:500px;">
                        </td>
					  </tr>
                      <tr>
                        <td>
                        	<?php echo _RSFORM_BACKEND_FORMS_EDIT_USER_EMAILS_RECIPIENTS; ?>
                        </td>
                        <td>
                        	<input name="UserEmailTo" id="UserEmailTo" value="<?php echo $row['UserEmailTo']; ?>" style="width:500px;">
                        </td>
					  </tr>
					  <tr>
                        <td>
                        	CC:
                        </td>
						<td>
                        	<input name="UserEmailCC" id="UserEmailCC" value="<?php echo $row['UserEmailCC']; ?>" style="width:500px;">
                        </td>
					  </tr>
					  <tr>
                        <td>
                        	BCC:
                        </td>
						<td>
                        	<input name="UserEmailBCC" id="UserEmailBCC" value="<?php echo $row['UserEmailBCC']; ?>" style="width:500px;">
                        </td>
                      </tr>
                      <tr>
                        <td>
                        	<?php echo _RSFORM_BACKEND_FORMS_EDIT_USER_EMAILS_SUBJECT; ?>
                        </td>
                        <td>
                        	<input name="UserEmailSubject" id="UserEmailSubject" value="<?php echo $row['UserEmailSubject']; ?>" style="width:500px;">
                        </td>
                      </tr>
                      <tr>
                        <td>
                        	<?php echo _RSFORM_BACKEND_FORMS_EDIT_USER_EMAILS_MODE; ?>
                        </td>
                        <td>
                        	<?php echo $RSadapter->YesNoRadio('UserEmailMode',' onclick="javascript:submitbutton(\'forms.apply\');"', $row['UserEmailMode'], _RSFORM_BACKEND_FORMS_EDIT_EMAILS_MODE_HTML, _RSFORM_BACKEND_FORMS_EDIT_EMAILS_MODE_TEXT);?>
                        </td>
                      </tr>
					  <tr>
						<td valign="top"><?php echo _RSFORM_BACKEND_FORMS_EDIT_ADMIN_EMAILS_ATTACH; ?></td>
						<td><?php echo $RSadapter->YesNoRadio('UserEmailAttach','', $row['UserEmailAttach'], _RSFORM_BACKEND_YES, _RSFORM_BACKEND_NO);?></td>
					  </tr>
					  <tr>
						<td><?php echo _RSFORM_BACKEND_FORMS_EDIT_ADMIN_EMAILS_ATTACH_FILE; ?></td>
						<td><input name="UserEmailAttachFile" id="UserEmailAttachFile" value="<?php echo !empty($row['UserEmailAttachFile']) ? $row['UserEmailAttachFile'] : $RSadapter->config['absolute_path'].'/components/com_rsform/uploads/'; ?>" style="width:500px;">
						<br />
						<?php if ($row['UserEmailAttachFile'] != $RSadapter->config['absolute_path'].'/components/com_rsform/uploads/' && !file_exists($row['UserEmailAttachFile']) && $row['UserEmailAttach']) { ?>
						<?php echo _RSFORM_BACKEND_FORMS_EDIT_ADMIN_EMAILS_ATTACH_FILE_WARNING; ?>
						<?php } ?>
						</td>
					  </tr>
                      <tr>
                        <td valign="top">
                        	<?php echo _RSFORM_BACKEND_FORMS_EDIT_USER_EMAILS_TEXT; ?>
                        </td>
                        <td>
                        <br/>
                        <?php
                        if($row['UserEmailMode']){
                        	
                        	?>
                        	<button type="button" onclick="window.open('index3.php?option=com_rsform&task=richtext.show&openerId=UserEmailText&formId=<?php echo $formId;?>','Richtext','width=600,height=400');return false;"><?php echo _RSFORM_BACKEND_FORMS_EDIT_FORM_EDIT_USER_EMAILS_TEXT; ?></button><br/>
                            	
                             <div style="border:1px solid #CCCCCC;margin: 5px;" id="UserEmailText"><?php echo $row['UserEmailText']; ?></div>
                        	<?php
                        }
                        else
                        {?>
                        	<textarea rows="7" cols="75" name="UserEmailText" id="UserEmailText" style="width:500px;"><?php echo $row['UserEmailText'];?></textarea>
                        <?php
                        }
                        ?>
                        </td>
                      </tr>
                      <tr>
                          <td colspan="2"><?php echo _RSFORM_BACKEND_FORMS_EDIT_USER_EMAILS_DESC; ?></td>
                      </tr>
                </table>
            </td>
            <td>
            	 <div style="width:200px;">
            			<h3><?php echo _RSFORM_BACKEND_FORMS_EDIT_COMP_QUICKADD;?></h3>
            			<?php echo _RSFORM_BACKEND_FORMS_EDIT_COMP_QUICKADD_DESC;?><br/><br/>
	                    <?php
	                    	$components = RSlistComponents($formId);
	                    	if(!empty($components)){
	                    		foreach($components as $component){
	                    			$str =
	                    			'{'.$component->PropertyValue.':'._RSFORM_BACKEND_FORMS_EDIT_COMP_CAPTION.'}'.":".
	                    			'{'.$component->PropertyValue.':'._RSFORM_BACKEND_FORMS_EDIT_COMP_VALUE.'}'."<br/>";
	                    			?>
	                    			<strong><?php echo $component->PropertyValue;?></strong><br/>
	                    			<?php echo $str;?><br/>
		                    		<?php
	                    		}
	                    	}
	                    ?>
                </div>
            </td>
        </tr>
        </table>

        <?php
        $RSadapter->endTab();
        $RSadapter->startTab(_RSFORM_BACKEND_FORMS_EDIT_TITLE_ADMIN_EMAILS,"email-admin");
        ?>
        <table cellpadding="4" cellspacing="0" border="0" class="adminform">
       		<tr>
            	<th colspan="2"><?php echo _RSFORM_BACKEND_FORMS_EDIT_ADMIN_EMAILS_HEAD; ?></th>
          	</tr>
          	<tr>
              <td valign="top" align="left" width="1%">



                  <table>
                      <tr>
                        <td width="80">
                        	<?php echo _RSFORM_BACKEND_FORMS_EDIT_ADMIN_EMAILS_FROM; ?>
                        </td>
                        <td>
                        	<input name="AdminEmailFrom" id="AdminEmailFrom" value="<?php echo $row['AdminEmailFrom']; ?>" size="35">&nbsp;&nbsp;
                        	<?php echo _RSFORM_BACKEND_FORMS_EDIT_ADMIN_EMAILS_FROMNAME; ?>
                        	<input name="AdminEmailFromName" id="AdminEmailFromName" value="<?php echo $row['AdminEmailFromName']; ?>" size="20">
                        </td>
                      </tr>
					  <tr>
                        <td>
                        	Reply to:
                        </td>
						<td>
                        	<input name="AdminEmailReplyTo" id="AdminEmailReplyTo" value="<?php echo $row['AdminEmailReplyTo']; ?>" style="width:500px;">
                        </td>
					  </tr>
                      <tr>
                        <td>
                        	<?php echo _RSFORM_BACKEND_FORMS_EDIT_ADMIN_EMAILS_RECIPIENTS; ?>
                        </td>
                        <td>
                        	<input name="AdminEmailTo" id="AdminEmailTo" value="<?php echo $row['AdminEmailTo']; ?>" style="width:500px;">
                        </td>
                      </tr>
					  <tr>
                        <td>
                        	CC:
                        </td>
						<td>
                        	<input name="AdminEmailCC" id="AdminEmailCC" value="<?php echo $row['AdminEmailCC']; ?>" style="width:500px;">
                        </td>
					  </tr>
					  <tr>
                        <td>
                        	BCC:
                        </td>
						<td>
                        	<input name="AdminEmailBCC" id="AdminEmailBCC" value="<?php echo $row['AdminEmailBCC']; ?>" style="width:500px;">
                        </td>
                      </tr>
                      <tr>
                        <td>
                        	<?php echo _RSFORM_BACKEND_FORMS_EDIT_ADMIN_EMAILS_SUBJECT; ?>
                        </td>
                        <td>
                        	<input name="AdminEmailSubject" id="AdminEmailSubject" value="<?php echo $row['AdminEmailSubject']; ?>" style="width:500px;">
                        </td>
                      </tr>
                      <tr>
                        <td>
                        	<?php echo _RSFORM_BACKEND_FORMS_EDIT_ADMIN_EMAILS_MODE; ?>
                        </td>
                        <td>
                        	<?php echo $RSadapter->YesNoRadio('AdminEmailMode',' onclick="javascript:submitbutton(\'forms.apply\');"', $row['AdminEmailMode'], _RSFORM_BACKEND_FORMS_EDIT_EMAILS_MODE_HTML, _RSFORM_BACKEND_FORMS_EDIT_EMAILS_MODE_TEXT);?>
                        </td>
                      </tr>
                      <tr>
                        <td valign="top">
                        	<?php echo _RSFORM_BACKEND_FORMS_EDIT_ADMIN_EMAILS_TEXT; ?>
                        </td>
                        <td>
                        <br/>
                        <?php
                        if($row['AdminEmailMode']){
                        	?>
                        	<button type="button" onclick="window.open('index3.php?option=com_rsform&task=richtext.show&openerId=AdminEmailText&formId=<?php echo $formId;?>','Richtext','width=600,height=400');return false;"><?php echo _RSFORM_BACKEND_FORMS_EDIT_FORM_EDIT_ADMIN_EMAILS_TEXT; ?></button><br/>
                            	
                             <div style="border:1px solid #CCCCCC;margin: 5px;" id="AdminEmailText"><?php echo $row['AdminEmailText']; ?></div>
                        	<?php
                        }
                        else
                        {?>
                        	<textarea rows="7" cols="75" name="AdminEmailText" id="AdminEmailText" style="width:500px;"><?php echo $row['AdminEmailText'];?></textarea>
                        <?php
                        }
                        ?>
                        </td>
                      </tr>
                      <tr>
                          <td colspan="2"><?php echo _RSFORM_BACKEND_FORMS_EDIT_ADMIN_EMAILS_DESC; ?></td>
                      </tr>
                </table>

            </td>
            <td>
	            <div style="width:200px;">
	            			<h3><?php echo _RSFORM_BACKEND_FORMS_EDIT_COMP_QUICKADD;?></h3>
	            			<?php echo _RSFORM_BACKEND_FORMS_EDIT_COMP_QUICKADD_DESC;?><br/><br/>
		                    <?php
		                    	$components = RSlistComponents($formId);
		                    	if(!empty($components)){
		                    		foreach($components as $component){
		                    			$str =
		                    			'{'.$component->PropertyValue.':'._RSFORM_BACKEND_FORMS_EDIT_COMP_CAPTION.'}'.":".
		                    			'{'.$component->PropertyValue.':'._RSFORM_BACKEND_FORMS_EDIT_COMP_VALUE.'}'."<br/>";
		                    			?>
		                    			<strong><?php echo $component->PropertyValue;?></strong><br/>
		                    			<?php echo $str;?><br/>
			                    		<?php
		                    		}
		                    	}
		                    ?>
	                </div>
            </td>
        </tr>


        </table>

        <?php
        $RSadapter->endTab();
        $RSadapter->startTab(_RSFORM_BACKEND_FORMS_EDIT_TITLE_SCRIPTS,"script-notify");
        ?>
        <table cellpadding="4" cellspacing="0" border="0" class="adminform">
        <tr>
              <th colspan="2"><?php echo _RSFORM_BACKEND_FORMS_EDIT_SCRIPTS_HEAD; ?></th>
          </tr>
          <tr>
              <td valign="top" align="left" width="100%">
                  <table>
                      <tr>
                        <td><?php echo _RSFORM_BACKEND_FORMS_EDIT_SCRIPTS_DISPLAY; ?><br/>
                        <textarea rows="20" cols="75" name="ScriptDisplay" id="ScriptDisplay" style="width:100%;"><?php echo htmlentities($row['ScriptDisplay']);?></textarea><br/>
                        <?php echo _RSFORM_BACKEND_FORMS_EDIT_SCRIPTS_DISPLAY_DESC; ?></td>
                      </tr>
                      <tr>
                        <td><?php echo _RSFORM_BACKEND_FORMS_EDIT_SCRIPTS_PROCESS; ?><br/>
                        <textarea rows="20" cols="75" name="ScriptProcess" id="ScriptProcess" style="width:100%;"><?php echo htmlentities($row['ScriptProcess']);?></textarea><br/>
                        <?php echo _RSFORM_BACKEND_FORMS_EDIT_SCRIPTS_PROCESS_DESC; ?></td>
                      </tr>
					  <tr>
                        <td><?php echo _RSFORM_BACKEND_FORMS_EDIT_SCRIPTS_PROCESS2; ?><br/>
                        <textarea rows="20" cols="75" name="ScriptProcess2" id="ScriptProcess2" style="width:100%;"><?php echo htmlentities($row['ScriptProcess2']);?></textarea><br/>
                        <?php echo _RSFORM_BACKEND_FORMS_EDIT_SCRIPTS_PROCESS2_DESC; ?></td>
                      </tr>
                </table>

            </td>
        </tr>
        </table>
		
        <?php
        $RSadapter->endTab();
		$RSadapter->startTab(_RSFORM_BACKEND_FORMS_EDIT_META, 'meta-tags');
        ?>
        <table cellpadding="4" cellspacing="0" border="0" class="adminform">
		<tr>
              <th colspan="2"><?php echo _RSFORM_BACKEND_FORMS_EDIT_META; ?></th>
        </tr>
          <tr>
              <td valign="top" align="left" width="100%">
                  <table>
                      <tr>
                        <td><?php echo _RSFORM_BACKEND_FORMS_META_DESCRIPTION; ?><br/>
                        <textarea rows="10" cols="35" name="MetaDesc" id="MetaDesc" style="width:100%;"><?php echo htmlentities($row['MetaDesc']); ?></textarea><br/>
                        </td>
                      </tr>
                      <tr>
                        <td><?php echo _RSFORM_BACKEND_FORMS_META_KEYWORDS; ?><br/>
                        <textarea rows="10" cols="35" name="MetaKeywords" id="MetaKeywords" style="width:100%;"><?php echo htmlentities($row['MetaKeywords']); ?></textarea>
						</td>
                      </tr>
					  <tr>
                        <td><?php echo _RSFORM_BACKEND_FORMS_META_TITLE; ?><br/>
                        <?php echo $RSadapter->YesNoRadio('MetaTitle','', $row['MetaTitle'], _RSFORM_BACKEND_YES, _RSFORM_BACKEND_NO);?></td>
                      </tr>
                </table>
            </td>
        </tr>
        </table>
		
        <?php
        $RSadapter->endTab();
    	$mainframe->triggerEvent('rsfp_bk_onAfterShowFormEditTabs');
        $RSadapter->endPane();
        ?>


        <form action="<?php echo _RSFORM_BACKEND_SCRIPT_PATH;?>" method="post" name="adminForm" id="adminForm" onsubmit="transferItems();">
			<input type="hidden" name="FormName" value=""/>
			<input type="hidden" name="FormLayout" value=""/>
			<input type="hidden" name="FormLayoutName" value=""/>
			<input type="hidden" name="FormLayoutAutogenerate" value=""/>
			<input type="hidden" name="FormTitle" value=""/>
			<input type="hidden" name="Published" value=""/>
			<input type="hidden" name="ReturnUrl" value=""/>
			<input type="hidden" name="Thankyou" value=""/>
			<input type="hidden" name="UserEmailTo" value=""/>
			<input type="hidden" name="UserEmailCC" value=""/>
			<input type="hidden" name="UserEmailBCC" value=""/>
			<input type="hidden" name="UserEmailFrom" value=""/>
			<input type="hidden" name="UserEmailReplyTo" value=""/>
			<input type="hidden" name="UserEmailFromName" value=""/>
			<input type="hidden" name="UserEmailSubject" value=""/>
			<input type="hidden" name="UserEmailMode" value=""/>
			<input type="hidden" name="UserEmailText" value=""/>
			<input type="hidden" name="UserEmailAttach" value=""/>
			<input type="hidden" name="UserEmailAttachFile" value=""/>
			<input type="hidden" name="AdminEmailTo" value=""/>
			<input type="hidden" name="AdminEmailCC" value=""/>
			<input type="hidden" name="AdminEmailBCC" value=""/>
			<input type="hidden" name="AdminEmailFrom" value=""/>
			<input type="hidden" name="AdminEmailReplyTo" value=""/>
			<input type="hidden" name="AdminEmailFromName" value=""/>
			<input type="hidden" name="AdminEmailSubject" value=""/>
			<input type="hidden" name="AdminEmailMode" value=""/>
			<input type="hidden" name="AdminEmailText" value=""/>
			<input type="hidden" name="ScriptProcess" value=""/>
			<input type="hidden" name="ScriptProcess2" value=""/>
			<input type="hidden" name="ScriptDisplay" value=""/>
			<input type="hidden" name="MetaTitle" value=""/>
			<input type="hidden" name="MetaDesc" value=""/>
			<input type="hidden" name="MetaKeywords" value=""/>
			<input type="hidden" name="Required" value=""/>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="task" value="forms.menuadd.process" />
			<input type="hidden" name="formId" value="<?php echo $formId;?>" />
        	<input type="hidden" name="boxchecked" value="0" />
        	<input type="hidden" name="hidemainmenu" value="0"/>
       
		</form>


	<?php
	//keep session alive while editing
	JHTML::_('behavior.keepalive');
    }

    function formsMenuaddScreen($option, $menus, $formId, $formTitle)
    {
    	$RSadapter = $GLOBALS['RSadapter'];
    	$RSadapter->addHeadTag(_RSFORM_FRONTEND_REL_PATH.'/controller/functions.js','js');
    	?>
    	<form action="<?php echo _RSFORM_BACKEND_SCRIPT_PATH;?>" method="post" name="adminForm" id="adminForm">
		<table class="adminlist">
			<tr>
				<td colspan="2">
		<?php
    	if(!empty($menus))
    	{
    		printf(_RSFORM_BACKEND_FORMS_MENUADD_ADD, $formTitle);
    		?>
    		<select name="menu">
    		<?php
    		foreach($menus as $menu)
    		{
    			echo '<option value="'.$menu.'">'.$menu.'</option>';
    		}
    		?>
    		</select><br/><br/>
			<?php echo _RSFORM_BACKEND_FORMS_MENUADD_MENUTITLE;?>
			<input type="text" size="55" name="menutitle" value="<?php echo $formTitle;?>"/>
    		&nbsp;&nbsp;
			<input type="button" onclick="document.adminForm.submit();" value="<?php echo _RSFORM_BACKEND_FORMS_MENUADD_BTN;?>"/>
    		<?php
    	}
    	?>
				</td>
	  		</tr>
		</table>
        <input type="hidden" name="option" value="<?php echo $option; ?>" />
        <input type="hidden" name="formId" value="<?php echo $formId; ?>" />
        <input type="hidden" name="task" value="forms.menuadd.process" />
        <input type="hidden" name="hidemainmenu" value="0">
		</form>
	<?php

    }

//////////////////////////////////////// COMPONENTS ///////////////////////////////////
    /**
     * Components Copy Screen
     *
     * @param str $option
     * @param array $forms
     * @param array $components
     */
    function componentsCopyScreen($option, $forms, $components, $fromFormId)
    {
    	$RSadapter = $GLOBALS['RSadapter'];
    	$RSadapter->addHeadTag(_RSFORM_FRONTEND_REL_PATH.'/controller/functions.js','js');
	?>
	<form action="<?php echo _RSFORM_BACKEND_SCRIPT_PATH;?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<tr>
			<td colspan="2">
				<?php
					$hiddens = '';
					foreach($components as $componentId)
						if(intval($componentId)!=0)
							$hiddens .= '<input type="hidden" name="componentId[]" value="'.$componentId.'"/>';
					
					echo _RSFORM_BACKEND_COMPONENTS_COPY.'<br/>';
					?>
					<select name="toFormId">
					<?php
						foreach($forms as $formId=>$formTitle)
							echo '<option value="'.$formId.'">'.$formTitle.'</option>'."\r\n";
					?>
					</select>
					&nbsp;&nbsp;
					<input type="button" onclick="document.adminForm.submit();" value="<?php echo _RSFORM_BACKEND_COMPONENTS_COPY_BTN;?>"/>
					<?php
					echo $hiddens;
				?>
			</td>
	  </tr>
	</table>

	<input type="hidden" name="option" value="<?php echo $option;?>" />
	<input type="hidden" name="task" value="components.copy.process" />
	<input type="hidden" name="formId" value="<?php echo $fromFormId;?>" />
	</form>
	<?php
    }





//////////////////////////////////////// SUBMISSIONS ///////////////////////////////////

	/**
	 * Manage Submissions Screen
	 *
	 * @param int $option
	 * @param obj $data
	 * @param array $forms
	 */
	function submissionsManage($option, $data, $forms)
	{
		$RSadapter = $GLOBALS['RSadapter'];
    	$RSadapter->addHeadTag(_RSFORM_FRONTEND_REL_PATH.'/controller/functions.js','js');
		$RSadapter->addHeadTag( _RSFORM_BACKEND_REL_PATH."/style.css", 'css' );
		$RSadapter->addHeadTag( _RSFORM_FRONTEND_REL_PATH.'/controller/ajax.js','js' );
		?>
		<form action="<?php echo _RSFORM_BACKEND_SCRIPT_PATH;?>" method="POST" name="adminForm" id="adminForm" style="overflow: auto">
		<input type="hidden" id="sort_id" name="sort_id" value="<?php echo $data->order[0]; ?>"/>
		<input type="hidden" id="sort_order" name="sort_order" value="<?php echo $data->order[1]; ?>"/>
		<input type="hidden" id="page" value="1"/>
		<input type="hidden" id="formId" name="formId" value="<?php echo $data->formId;?>"/>
		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="option" value="<?php echo $option;?>"/>
		<input type="hidden" name="boxchecked" value="0" />

		<table width="95%" align="center" cellpadding="0" cellspacing="0">
		   <tr>
		      <td align="right">
				<?php
					//build forms select
					$formsSelectHtml = _RSFORM_BACKEND_SUBMISSIONS_MANAGE_FORM_SELECT;
					$formsSelectHtml .= '<select onchange="document.location=\''._RSFORM_BACKEND_SCRIPT_PATH.'?option='.$option.'&task=submissions.manage&formId=\'+this.value;">';
					if(!empty($forms)){
						foreach($forms as $formRowId=>$formRowName){
							$formsSelectHtml .= '<option value="'.$formRowId.'"'.($formRowId==$data->formId ? ' selected':'').'>'.$formRowName.'</option>';
						}
					}
					$formsSelectHtml .= '</select>';
					echo $formsSelectHtml;

					echo _RSFORM_BACKEND_SUBMISSIONS_MANAGE_VIEW;
				?>
				
				<select id="limit" onchange="changeRows();">
				<?php
				$limits = array(5,10,15,20,30,50,100);
				foreach ($limits as $limit) {
				?>
				<option value="<?php echo $limit; ?>" <?php echo ($data->limit==$limit) ? 'selected="selected"' : '' ;?>><?php echo $limit; ?></option>
				<?php } ?>
				</select>
				
				<?php echo _RSFORM_BACKEND_SUBMISSIONS_MANAGE_FILTER;?>
		         <input type="text" name="filter" id="filter" />
		         <input type="submit" name="filterBtn" value="<?php echo _RSFORM_BACKEND_SUBMISSIONS_MANAGE_FILTER_BTN;?>" onclick="createFilter(); return false;" />
		         <input type="button" name="action" id="action" value="<?php echo _RSFORM_BACKEND_SUBMISSIONS_MANAGE_CLR_FILTER_BTN;?>" onclick="clearFilter();"/>
		      </td>
		   </tr>
		   <tr>
		   	<td style="margin:0; padding:0;">
		      	<div id="content">
					<?php rsform_HTML::submissionsTable($option, $data);?>
		         </div>
		       </td>
		      </tr>
		  		<tr>
		      	<td id="pager">
					<?php $data->pager(); ?>
		         </td>
		      </tr>
		</table>
		</form>
		<?php

	}

	/**
	 * Displays the submissions table
	 *
	 * @param str $option
	 * @param obj $data
	 */
	function submissionsTable($option, $data)
	{
		?>
        <table border="0" cellpadding="0" cellspacing="0" width="100%" class="adminform">
           <tr id="titles">
              <th class="title">
                 <input name="toggle" type="checkbox" id="checkall" value="1" onclick="checkAll()"/>
              </th>
                 <?php $data->createHeaders(); ?>
              <th class="title">
              	<?php echo _RSFORM_BACKEND_SUBMISSIONS_MANAGE_TABLE_ACTIONS;?>
              </th>
          </tr>
             <?php $data->createRows(); ?>
        </table>
		<?php
	}

	/**
	 * Export Submissions Config Screen
	 *
	 * @param str $option
	 * @param int $formId
	 * @param array $submissionIds
	 * @param str $formName
	 * @param array $formComponents
	 */
	function submissionsExport($option,$formId,$submissionIds,$formName, $formComponents)
	{
		$RSadapter = $GLOBALS['RSadapter'];
    	$RSadapter->addHeadTag(_RSFORM_FRONTEND_REL_PATH.'/controller/functions.js','js');
		//build formComponents table
		$formComponentsHtml = '<table border="0" class="adminform" style="width:500px;">';
		//global columns
		$formComponentsHtml .= '
			<tr>
				<th class="title">'._RSFORM_BACKEND_SUBMISSIONS_EXPORT_HEAD_EXPORT.'</th>
				<th class="title">'._RSFORM_BACKEND_SUBMISSIONS_EXPORT_HEAD_SUBMISSION_DATA.'</th>
				<th class="title">'._RSFORM_BACKEND_SUBMISSIONS_EXPORT_HEAD_COLUMN_ORDER.'</th>
			</tr>';
		//DateSubmitted
		$formComponentsHtml .=
				'<tr class="row0">
					<td><input type="checkbox" name="ExportSubmission[DateSubmitted]" value="1" checked/></td>
					<td>'._RSFORM_BACKEND_SUBMISSIONS_EXPORT_DATE_SUBMITTED.'</td>
					<td><input type="text" name="ExportOrder[DateSubmitted]" value="1" size="3"/></td>
				</tr>'."\r\n";
		//Userip
		$formComponentsHtml .=
				'<tr class="row1">
					<td><input type="checkbox" name="ExportSubmission[UserIp]" value="1"/></td>
					<td>'._RSFORM_BACKEND_SUBMISSIONS_EXPORT_USER_IP.'</td>
					<td><input type="text" name="ExportOrder[UserIp]" value="2" size="3"/></td>
				</tr>'."\r\n";
		//Username
		$formComponentsHtml .=
				'<tr class="row0">
					<td><input type="checkbox" name="ExportSubmission[Username]" value="1"/></td>
					<td>'._RSFORM_BACKEND_SUBMISSIONS_EXPORT_USERNAME.'</td>
					<td><input type="text" name="ExportOrder[Username]" value="3" size="3"/></td>
				</tr>'."\r\n";
		$formComponentsHtml .= '
			<tr>
				<th class="title">'._RSFORM_BACKEND_SUBMISSIONS_EXPORT_HEAD_EXPORT.'</th>
				<th class="title">'._RSFORM_BACKEND_SUBMISSIONS_EXPORT_HEAD_COMPONENTS.'</th>
				<th class="title">'._RSFORM_BACKEND_SUBMISSIONS_EXPORT_HEAD_COLUMN_ORDER.'</th></tr>';

		if(!empty($formComponents))
		{
			$i=0;
			foreach($formComponents as $formComponentId=>$formComponentRow)
			{
				$i = $i ? 0:1;
				$formComponentsHtml .=
				'<tr class="row'.$i.'">
					<td><input type="checkbox" name="ExportComponent['.$formComponentRow['ComponentName'].']" value="'.$formComponentId.'" checked/></td>
					<td>'.$formComponentRow['ComponentName'].'</td>
					<td><input type="text" name="ExportOrder['.$formComponentRow['ComponentName'].']" value="'.($formComponentRow['Order']+3).'" size="3"/></td>
				</tr>'."\r\n";
			}
		}
		
		global $mainframe;
		$mainframe->triggerEvent('rsfp_bk_onAfterShowExportComponents', array(&$formComponentsHtml, @$formComponentRow['Order']+4));
		
		$formComponentsHtml .= '</table>';

		?>
			<form action="<?php echo _RSFORM_BACKEND_SCRIPT_PATH;?>" method="POST" id="adminForm" name="adminForm">
				<table class="adminform" border="0">
				<tr>
					<th class="title"><?php printf(_RSFORM_BACKEND_SUBMISSIONS_EXPORT_FOR,$formName);?></th>
				</tr>
				<tr>
					<td>
						<label for="ExportRowsAll"><?php echo _RSFORM_BACKEND_SUBMISSIONS_EXPORT_ALL_ROWS;?></label>
						<input type="radio" name="ExportRows" id="ExportRowsAll" value="0" <?php echo count($submissionIds) ? '':'checked';?>/>
						<label for="ExportRowsSelected"><?php echo _RSFORM_BACKEND_SUBMISSIONS_EXPORT_SELECTED_ROWS;?> (<?php echo count($submissionIds);?>) </label>
						<input type="radio" name="ExportRows" id="ExportRowsSelected" value="<?php echo implode(',',$submissionIds);?>" <?php echo count($submissionIds) ? 'checked':'';?>/>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo _RSFORM_BACKEND_SUBMISSIONS_EXPORT_DELIMITER;?> <input type="text" name="ExportDelimiter" value="," size="5"/>
						<?php echo _RSFORM_BACKEND_SUBMISSIONS_EXPORT_FIELD_ENCLOSURE;?> <input type="text" name="ExportFieldEnclosure" value="&quot;" size="5"/>
						<?php echo _RSFORM_BACKEND_SUBMISSIONS_EXPORT_HEADERS;?> <input type="checkbox" name="ExportHeaders" value="1"/>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo $formComponentsHtml;?>
					</td>
				</tr>
				<tr>
					<td><input type="submit" name="Export" value="<?php echo _RSFORM_BACKEND_SUBMISSIONS_EXPORT_BTN;?>"/></td>
				</tr>
				</table>
				<input type="hidden" name="task" value="submissions.export.task"/>
				<input type="hidden" name="option" value="<?php echo $option;?>"/>
				<input type="hidden" name="formId" value="<?php echo $formId;?>"/>
				<input type="hidden" name="ExportFile" value="<?php echo uniqid("");?>"/>
				<input type="hidden" name="ExportRowsOrder" value="<?php echo $_POST['sort_id'];?>"/>
				<input type="hidden" name="ExportRowsOrderDirection" value="<?php echo $_POST['sort_order'];?>"/>
				<input type="hidden" name="filter" value="<?php echo $_POST['filter'];?>"/>
			</form>
		<?php
	}
	
	function submissionsExportTask($option, $post, $limit, $total, $file)
	{
		$RSadapter = $GLOBALS['RSadapter'];
    	$RSadapter->addHeadTag(_RSFORM_FRONTEND_REL_PATH.'/controller/functions.js','js');
		$RSadapter->addHeadTag( _RSFORM_BACKEND_REL_PATH."/style.css", 'css' );
		
		$config = new JConfig();
		?>
		
		<?php if (is_writable($config->tmp_path)) { ?>
			<div class="progressWrapper"><div class="progressBar" id="progressBar">0%</div></div>
			<p><?php echo sprintf(_RSFORM_BACKEND_SUBMISSIONS_EXPORT_MSG, '<strong id="exportmsg">5</strong>'); ?></p>
		<?php } else { ?>
			<p><?php echo sprintf(_RSFORM_BACKEND_SUBMISSIONS_EXPORT_MSG_ERROR, $config->tmp_path); ?></p>
		<?php } ?>
		
		
		<input type="hidden" value="<?php echo $file;?>" id="ExportFile" />
		
		<?php if (is_writable($config->tmp_path)) { ?>
		<script type="text/javascript">
		t = setInterval(function() {
			var count = parseInt($('exportmsg').innerHTML);
			if (count <= 0)
				return clearTimeout(t);
			
			$('exportmsg').innerHTML = count - 1;
		}, 1000);
		
		setTimeout(function() {
			exportProcess(0,<?php echo $limit;?>,<?php echo $total;?>);
		}, 5000);
		</script>
		<?php } ?>
		
		<?php
	}

//////////////////////////////////////// CONFIGURATION ////////////////////////////////////////
/**
 * Configuration Edit screen
 *
 * @param str $option
 */
function configurationEdit($option)
{
	global $mainframe;
	$RSadapter = $GLOBALS['RSadapter'];
    $RSadapter->addHeadTag(_RSFORM_FRONTEND_REL_PATH.'/controller/functions.js','js');
    $RSadapter->addHeadTag(_RSFORM_BACKEND_REL_PATH.'/style.css','css');
	$RSadapter->initTabs(1);
	?>
	
	<form action="<?php echo _RSFORM_BACKEND_SCRIPT_PATH;?>" method="post" name="adminForm" id="adminForm">	
	<table width="100%" border="0">
		<tr>
			<td width="100%" valign="top">
				<?php

				$RSadapter->startPane("content-pane");
				$RSadapter->startTab(_RSFORM_BACKEND_SETTINGS_TABS_CONFIG,"config");
				?>
				<table cellpadding="4" cellspacing="1" border="0" class="adminform">
		    	<tr>
		      		<th colspan="2"><?php echo _RSFORM_BACKEND_SETTINGS_GLOBAL_HEAD; ?></th>
		      	</tr>
		      	<tr>
		      		<td valign="top" align="left" width="100%">
		      			<table>
		  					<tr>
		    					<td><strong><?php echo _RSFORM_BACKEND_SETTINGS_REGISTER; ?></strong><br/>
		    					<input name="rsformConfig[global.register.code]" value="<?php echo $RSadapter->config['global.register.code']; ?>" size="100" maxlength="50" /><br />
		    					<?php echo _RSFORM_BACKEND_SETTINGS_REGISTER_DESC;?>
		    					</td>
		  					</tr>
		  				</table>
		  			</td>
		  		</tr>
		      	<tr>
		      		<td valign="top" align="left" width="100%">
		      			<table>
		  					<tr>
		    					<td><strong><?php echo _RSFORM_BACKEND_SETTINGS_DEBUG; ?></strong><br/>
		    					<?php echo $RSadapter->YesNoRadio('rsformConfig[global.debug.mode]','', $RSadapter->config['global.debug.mode'], _RSFORM_BACKEND_YES, _RSFORM_BACKEND_NO);?><br/>
		    					<?php echo _RSFORM_BACKEND_SETTINGS_DEBUG_DESC;?>
		    					</td>
		    					<td>
		    					&nbsp;
		    					</td>
		  					</tr>
							
							<tr>
		    					<td><strong><?php echo _RSFORM_BACKEND_SETTINGS_IIS; ?></strong><br/>
		    					<?php echo $RSadapter->YesNoRadio('rsformConfig[global.iis]','', $RSadapter->config['global.iis'], _RSFORM_BACKEND_YES, _RSFORM_BACKEND_NO);?><br/>
		    					<?php echo _RSFORM_BACKEND_SETTINGS_IIS_DESC;?>
		    					</td>
		    					<td>
		    					&nbsp;
		    					</td>
		  					</tr>
							
							<tr>
		    					<td><strong><?php echo _RSFORM_BACKEND_SETTINGS_EDITOR; ?></strong><br/>
		    					<?php echo $RSadapter->YesNoRadio('rsformConfig[global.editor]','', $RSadapter->config['global.editor'], _RSFORM_BACKEND_YES, _RSFORM_BACKEND_NO);?><br/>
		    					<?php echo _RSFORM_BACKEND_SETTINGS_EDITOR_DESC;?>
		    					</td>
		    					<td>
		    					&nbsp;
		    					</td>
		  					</tr>
		  				</table>
		  			</td>
		  		</tr>
				</table>
				<?php
				$RSadapter->endTab();
				//triggerEvent - rsfp_bk_onAfterShowConfigurationTabs
				$mainframe->triggerEvent('rsfp_bk_onAfterShowConfigurationTabs');
				
				$RSadapter->endPane();
				?>
			</td>
		</tr>
	</table>
	<input type="hidden" name="option" value="<?php echo $option;?>" />
	<input type="hidden" name="task" value="settings.manage" />
	</form>
	<?php
}


//////////////////////////////////////// BACKUP / RESTORE ////////////////////////////////////////
function backupRestore( $rows, $title, $option, $element, $client = "", $p_startdir = "", $backLink="" )
{
	$RSadapter = $GLOBALS['RSadapter'];
    $RSadapter->addHeadTag(_RSFORM_FRONTEND_REL_PATH.'/controller/functions.js','js');
	?>
	<form enctype="multipart/form-data" action="<?php echo _RSFORM_BACKEND_SCRIPT_PATH;?>" method="post" name="adminForm">
	<table class="adminheading" width="100%">
	
	<?php
		if(is_writable($RSadapter->config['absolute_path'].'/media/')){
	?>
	
	<tr>
		<td>
			
			<table border="0" width="100%" class="adminform">
	        <tr>
	            <th width="5"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $rows ); ?>);" /></th>
	            <th class="title" width="20%"><?php echo _RSFORM_BACKEND_FORMS_MANAGE_TITLE; ?></th>
	            <th class="title" width="20%"><?php echo _RSFORM_BACKEND_FORMS_MANAGE_NAME; ?></th>
	            <th class="title" width="20%"><?php echo _RSFORM_BACKEND_FORMS_MANAGE_SUBMISSIONS; ?></th>
	            <th>&nbsp;</th>
	        </tr>
	        <?php
	        $i = 0;
			if (!empty($rows) && count($rows) > 0)
	        foreach($rows as $row)
	        {
	            echo '<tr>';
	                echo "<td>".'<input type="checkbox" id="cb'.$i.'" name="cid[]" value="'.$row['FormId'].'" onclick="isChecked(this.checked);" />'."</td>";
	                echo "<td>".(!empty($row['FormTitle']) ? $row['FormTitle'] : '<em>no title</em>')."</td>";
	                echo "<td>{$row['FormName']}</td>";
	                echo "<td>{$row['_allSubmissions']}</td>";
	                echo "<td>&nbsp;</td>";
	            echo '</tr>';
	            $i++;
	        }
	        
			?>
			
			<tr>
				<td colspan="5"><hr/></td>
			</tr>
			<tr>
				<td colspan="2"><input type="radio" name="submissions" id="forms" value="0" checked/><label for="forms"> <?php echo _RSFORM_BACKEND_BACKUPRESTORE_FORMS;?></label></td>
				<td colspan="3"><input type="radio" name="submissions" id="submissions" value="1"/><label for="submissions"> <?php echo _RSFORM_BACKEND_BACKUPRESTORE_FORMS_SUBMISSIONS;?></label></td>
			</tr>
			<tr>
				<td colspan="5">
					<?php echo '<input type="button" onclick="document.adminForm.task.value = \'backup.download\';document.adminForm.submit();" value="'._RSFORM_BACKEND_BACKUPRESTORE_BACKUP.'"/>';?>
				</td>
			</tr>
	        </table>
			
			
			
		</td>
	</tr>
	
	<?php }else{ ?>
	<tr>
		<th class="dbbackup">
		

			<?php echo _RSFORM_BACKEND_BACKUPRESTORE_NOTWRITABLE;?>
		</th>
	</tr>
		<?php }	?>
	</table>
	<?php
	if(!extension_loaded('zlib')) {
		echo "The installer can't continue before zlib is installed";
		return ;
	}else{
	?>
	<table class="adminheading">
	<tr>
		<th class="install">
		<h3><?php echo $title;?></h3>
		</th>
		<td align="right" nowrap="nowrap">
		<?php echo $backLink;?>
		</td>
	</tr>
	</table>

	<table class="adminform">
	<tr>
		<th>
		<?php echo _RSFORM_BACKEND_BACKUPRESTORE_TITLE_HEAD;?>
		</th>
	</tr>
	<tr>
		<td align="left">
		<?php echo _RSFORM_BACKEND_BACKUPRESTORE_PACKAGE_FILE;?>
		<input class="text_area" name="userfile" type="file" size="70"/>
		<input class="button" type="button" value="<?php echo _RSFORM_BACKEND_BACKUPRESTORE_RESTORE_BTN;?>" onclick="javascript:submitbutton('update.upload.process')"/>
		</td>
	</tr>
	<tr>
		<td><input type="checkbox" name="overwrite" value="1"/> <?php echo _RSFORM_BACKEND_BACKUPRESTORE_OVERWRITE;?></td>
	</tr>
	<tr>
		<td align="left">
		<?php echo _RSFORM_BACKEND_BACKUPRESTORE_INSTRUCTIONS;?>
		</td>
	</tr>
	</table>
	<?php
	}
	
	?>
	
	<input type="hidden" name="filetype" value="rsformbackup"/>
	<input type="hidden" name="task" value="update.upload.process"/>
	<input type="hidden" name="option" value="<?php echo $option;?>"/>
	<input type="hidden" name="boxchecked" value="0"/>
	</form>
	<?php
}



/**
 * Display the main component control panel
 */
function controlPanel()
{
	global $option;

	$RSadapter = $GLOBALS['RSadapter'];
    $RSadapter->addHeadTag(_RSFORM_FRONTEND_REL_PATH.'/controller/functions.js','js');

    //$RSadapter=new RSadapter();
    $RSadapter->addHeadTag(_RSFORM_BACKEND_REL_PATH.'/style.css','css');

    $modifyRegister = $RSadapter->getParam($_POST,'modify_register');
    if(!empty($modifyRegister)){
    	$RSadapter->config['global.register.code'] = '';
    }
    ?>
    <form action="<?php echo _RSFORM_BACKEND_SCRIPT_PATH;?>" method="post" name="adminForm">
    <table class="thisform">
   		<tr class="thisform">
      		<td width="50%" valign="top" class="thisform">
      			<table width="100%" class="thisform2" border="1">
      				<tr class="thisform2">
	      				<td align="center" height="100px" width="33%" class="thisform2">
	      				<div align="center">
				            <a href="<?php echo _RSFORM_BACKEND_SCRIPT_PATH;?>?option=com_rsform&task=forms.manage" style="text-decoration:none;" title="<?php echo _RSFORM_BACKEND_CPANEL_FORMS_MANAGE;?>">
				            <img src="components/com_rsform/images/forms.png" width="48px" height="48px" align="middle" border="0"/>
				            <br />
				            <?php echo _RSFORM_BACKEND_CPANEL_FORMS_MANAGE;?>
				            </a>
				        </div>
						</td>
	      				<td align="center" height="100px" width="34%" class="thisform2">
				            <a href="<?php echo _RSFORM_BACKEND_SCRIPT_PATH;?>?option=com_rsform&task=submissions.manage" style="text-decoration:none;" title="<?php echo _RSFORM_BACKEND_CPANEL_SUBMISSIONS_MANAGE;?>">
				            <img src="components/com_rsform/images/viewdata.png" width="48px" height="48px" align="middle" border="0"/>
				            <br />
				            <?php echo _RSFORM_BACKEND_CPANEL_SUBMISSIONS_MANAGE;?>
				            </a>
						</td>
	      				<td align="center" height="100px" class="thisform2">
				            <a href="<?php echo _RSFORM_BACKEND_SCRIPT_PATH;?>?option=com_rsform&task=configuration.edit" style="text-decoration:none;" title="<?php echo _RSFORM_BACKEND_CPANEL_CONFIGURATION_EDIT;?>">
				            <img src="components/com_rsform/images/config.png" width="48px" height="48px" align="middle" border="0"/>
				            <br />
				            <?php echo _RSFORM_BACKEND_CPANEL_CONFIGURATION_EDIT;?>
				            </a>
						</td>
					</tr>
      				<tr class="thisform2">
	      				<td align="center" height="100px" width="33%" class="thisform2">
				            <a href="<?php echo _RSFORM_BACKEND_SCRIPT_PATH;?>?option=com_rsform&task=backup.restore" style="text-decoration:none;" title="<?php echo _RSFORM_BACKEND_CPANEL_BACKUP_RESTORE;?>">
				            <img src="components/com_rsform/images/backup.png" width="48px" height="48px" align="middle" border="0"/>
				            <br />
				            <?php echo _RSFORM_BACKEND_CPANEL_BACKUP_RESTORE;?>
				            </a>
						</td>
	      				<td align="center" height="100px" class="thisform2">
				            <a href="<?php echo _RSFORM_BACKEND_SCRIPT_PATH;?>?option=com_rsform&task=updates.manage" style="text-decoration:none;" title="<?php echo _RSFORM_BACKEND_CPANEL_UPDATES_MANAGE;?>">
				            <img src="components/com_rsform/images/restore.png" width="48px" height="48px" align="middle" border="0"/>
				            <br />
				            <?php echo _RSFORM_BACKEND_CPANEL_UPDATES_MANAGE;?>
				            </a>
						</td>
	      				<td align="center" height="100px" width="33%" class="thisform2">
				            <a href="<?php echo _RSFORM_BACKEND_SCRIPT_PATH;?>?option=com_rsform&task=goto.support" style="text-decoration:none;" title="<?php echo _RSFORM_BACKEND_CPANEL_SUPPORT;?>" target="_blank">
				            <img src="components/com_rsform/images/support.png" width="48px" height="48px" align="middle" border="0"/>
				            <br />
				            <?php echo _RSFORM_BACKEND_CPANEL_SUPPORT;?>
				            </a>
						</td>
					</tr>
					<tr class="thisform2">
						<td align="center" height="100px" width="33%" class="thisform2">
				            <a href="<?php echo _RSFORM_BACKEND_SCRIPT_PATH;?>?option=com_rsform&task=goto.plugins" style="text-decoration:none;" title="<?php echo _RSFORM_BACKEND_CPANEL_SUPPORT;?>" target="_blank">
				            <img src="components/com_rsform/images/samples.png" width="48px" height="48px" align="middle" border="0"/>
				            <br />
				            <?php echo _RSFORM_BACKEND_SETTINGS_TABS_PLUGINS;?>
				            </a>
						</td>
						<td align="center" height="100px" width="33%" class="thisform2"></td>
						<td align="center" height="100px" width="33%" class="thisform2"></td>
					</tr>
				</table>

			</td>
		    <td width="50%" valign="top" align="center">

			    <table border="1" width="100%" class="thisform">
					<tr class="thisform">
			            <th class="cpanel" colspan="2"><?php echo _RSFORM_PRODUCT . ' ' . _RSFORM_VERSION. ' rev ' . _RSFORM_REVISION;?></th></td>
			         </tr>
			         <tr class="thisform"><td bgcolor="#FFFFFF" colspan="2"><br />
			      <div style="width=100%" align="center">
			      <img src="../components/com_rsform/images/rsform-pro.jpg" align="middle" alt="RSForm! Pro logo"/>
			      <br /><br /></div>
			      </td></tr>
			         <tr class="thisform">
			            <td width="120" bgcolor="#FFFFFF"><?php echo _RSFORM_VERSION_TITLE;?></td>
			            <td bgcolor="#FFFFFF"><?php echo _RSFORM_VERSION;?></td>
			         </tr>
			         <tr class="thisform">
			            <td bgcolor="#FFFFFF">Copyright:</td>
			            <td bgcolor="#FFFFFF"><?php echo _RSFORM_COPYRIGHT;?></td>
			         </tr>
			         <tr class="thisform">
			            <td bgcolor="#FFFFFF">License:</td>
			            <td bgcolor="#FFFFFF"><?php echo _RSFORM_LICENSE;?></td>
			         </tr>
			         <tr class="thisform">
			            <td valign="top" bgcolor="#FFFFFF"><?php echo _RSFORM_AUTHOR_TITLE;?></td>
			            <td bgcolor="#FFFFFF">
			            <?php echo _RSFORM_AUTHOR;?>
						</td>
			         </tr>
			         <tr class="<?php echo ($RSadapter->config['global.register.code']==''||$RSadapter->config['global.register.code']=='') ? 'thisformError':'thisformOk';?>">
						<td valign="top">
							<?php echo _RSFORM_CODE_TITLE;?>
						</td>
						<td>
							<?php echo ($RSadapter->config['global.register.code']=='') ? '<input type="text" name="rsformConfig[global.register.code]" value=""/>':$RSadapter->config['global.register.code'];?>
						</td>
			         </tr>
			         <tr class="<?php echo ($RSadapter->config['global.register.code']==''||$RSadapter->config['global.register.code']=='') ? 'thisformError':'thisformOk';?>">
						<td valign="top">&nbsp;</td>
						<td>
							<?php
							if($RSadapter->config['global.register.code']!=''){
							?>
							<input type="submit" name="modify_register" value="<?php echo _RSFORM_REGISTER_MODIFY;?>" /><br/>

							<?php
							}else{
							?>
							<input type="button" name="register" value="<?php echo _RSFORM_REGISTER;?>" onclick="javascript:submitbutton('saveRegistration');"/>
							<?php
							}
							?>
						</td>
			         </tr>
			      </table>
				  <p align="center"><a href="http://www.rsjoomla.com/joomla-components/joomla-security.html" target="_blank"><img src="../components/com_rsform/images/rsfirewall-approved.gif" align="middle" alt="RSFirewall! Approved"/></a></p>
		      </td>
		   </tr>
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="" />
	</form>
<?php
}



//////////////////////////////////////// UPDATES ////////////////////////////////////////

	function updatesManage($option)
	{
		$RSadapter = $GLOBALS['RSadapter'];
		$RSadapter->addHeadTag( _RSFORM_BACKEND_REL_PATH."/style.css",'css' );
 		$RSadapter->addHeadTag( _RSFORM_FRONTEND_REL_PATH.'/controller/functions.js','js' );

    ?>

		<form enctype="multipart/form-data" action="<?php echo _RSFORM_BACKEND_SCRIPT_PATH;?>" method="post" name="adminForm">
		<table class="adminform">
            <tr>
                <th>
                    Checking for updates
                </th>
            </tr>
		    <tr>
			    <td>
				    <iframe src="http://www.rsjoomla.com/index2.php?option=com_rslicense&task=checkUpdate&sess=<?php echo rsform_HTML::genKeyCode();?>&amp;revision=<?php echo _RSFORM_REVISION;?>&amp;version=1.5" style="border:0px solid;width:100%;height:18px;" scrolling="no" frameborder="no"></iframe>
			    </td>
		    </tr>
		</table>
        <br />
        <table class="adminform">
            <tr>
                <th>
                <?php echo _RSFORM_BACKEND_UPDATESMANAGE_UPDATE_TITLE;?>
                </th>
            </tr>
            	<?php
            	//check permissions
            	if(is_writable($RSadapter->config['absolute_path'].'/administrator/components/com_rsform/')&&is_writable($RSadapter->config['absolute_path'].'/components/com_rsform/')){
            	?>
            <tr>
                <td align="left">
				<strong>Updates are now installed through Extensions &gt; Install/Uninstall.</strong>
                </td>
            </tr>
            	<?php
            	}else{
            	?>
            <tr>
                <td align="left">
                <?php 
                echo $RSadapter->config['absolute_path'].'/administrator/components/com_rsform/ '.(is_writable($RSadapter->config['absolute_path'].'/administrator/components/com_rsform/') ? _RSFORM_BACKEND_UPDATESMANAGE_WRITABLE:_RSFORM_BACKEND_UPDATESMANAGE_NOTWRITABLE).'<br/>';
                echo $RSadapter->config['absolute_path'].'/components/com_rsform/ '.(is_writable($RSadapter->config['absolute_path'].'/components/com_rsform/') ? _RSFORM_BACKEND_UPDATESMANAGE_WRITABLE:_RSFORM_BACKEND_UPDATESMANAGE_NOTWRITABLE).'<br/>';
                echo '<br/><br/>';
                echo _RSFORM_BACKEND_UPDATESMANAGE_PERMISSIONS;
                
                ?>
                </td>
           	</tr>
            	<?php	
            	}
            	?>
				
			<tr>
				<td><iframe src="http://www.rsjoomla.com/latest.html?tmpl=component" style="border:0px solid;width:100%;height:380px;" scrolling="no" frameborder="no"></iframe></td>
			</tr>
        </table>

		<input type="hidden" name="filetype" value="rsformupdate"/>
		<input type="hidden" name="task" value="update.upload.process"/>
		<input type="hidden" name="option" value="<?php echo $option;?>"/>
		</form>
		<?php
	}





	function genKeyCode(){
		$RSadapter = $GLOBALS['RSadapter'];
		return md5($RSadapter->config['global.register.code']._RSFORM_KEY);
	}


}
?>