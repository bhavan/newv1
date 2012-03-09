<?php
// ensure this file is being included by a parent file
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

// ensure user has access to this function
if (!($acl->acl_check('administration', 'edit', 'users', $my->usertype, 'components', 'all') || $acl->acl_check('administration', 'edit', 'users', $my->usertype, 'components', 'com_groups'))) {
    mosRedirect('index2.php', _NOT_AUTH);
}
?>
<?php

class HTML_juga {

/**
* juga_Header
* 
*/
// ************************************************************************
	function juga_Header() {
		echo "<div class='submenu-box'>";
		echo "<ul id='submenu'>";
		echo "<li><a href='index2.php?option=com_juga&section=config'>"._juga_config. "</a></li>";
		echo "<li><a href='index2.php?option=com_juga&section=groups'>"._juga_config_groups. "</a></li>";
		echo "<li><a href='index2.php?option=com_juga&section=items'>"._juga_config_siteitems."</a></li>";
		echo "<li><a href='index2.php?option=com_juga&section=u2g'>"._juga_config_assign_users."</a></li>";
		echo "<li><a href='index2.php?option=com_juga&section=codes'>"._juga_config_codes."</a></li>";
		echo "<li><a href='index2.php?option=com_juga&section=tools'>"._juga_config_tools."</a></li>";
		echo "</ul>";
		echo "<br />";
		echo "</div>";
	}
// ************************************************************************

/**
* standardMessage
* 
*/
// ************************************************************************
	function standardMessage ( $option, $section, &$row, &$lists, &$search, &$pageNav ) {
		global $my, $database, $mainframe;
		?>	
		
        <div class='componentheading'>
			<?php echo $lists["head"]; ?>
			<?php if ($lists["subhead"]) { ?> <h6><?php echo $lists["subhead"]; ?></h6> <?php } ?>
		</div>
		
        <?php if ($lists["alert"]) { ?> <div class="notepi"><?php echo $lists["alert"]; ?></div> <?php } ?>
		<?php if ($lists["notice"]) { ?> <div class="note"><?php echo $lists["notice"]; ?></div> <?php } ?>
        
		<?php if ($lists["message"]) { ?> 
            <table class="userlist">
            <tr>
                <td>
                <?php echo $lists["message"]; ?>
                </td>
            </tr>
            </table>	
		<?php } ?>

		<?php if ($lists["link"]) { ?> 
            <p>
            <?php echo $lists["link"]; ?>
            </p>
		<?php } ?>       
        
        <?php		
	} // end standardMessage
// ************************************************************************	

/**
* Compiles a list of records
* @param database A database connector object
*/
// ************************************************************************
	function listConfig( $option, $section, &$rows, &$lists, &$search, &$pageNav ) {
		global $my, $database;

		JHTML::_('behavior.tooltip');
		?>
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
		<tr>
			<th class='juga'>
			</th>
			<td class='right'>
			<?php echo _juga_config_filter; ?>
			</td>
			<td class='right'>
			<input type="text" name="search" value="<?php echo htmlspecialchars( $search );?>" class="text_area" onChange="document.adminForm.submit();" />
			</td>
		</tr>
		</table>

		<table class="adminlist">
		<thead>
            <tr>
                <th width="5">
                #
                </th>
                <th width="20">
                <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" />
                </th>
                <th class="title">
                <?php echo _juga_config_title; ?>
                </th>
                <th class="title">
                <?php echo _juga_config_desc; ?>
                </th>
                <th class="title">
                <?php echo _juga_config_value; ?>
                </th>
            </tr>
		</thead>
        <tbody>
			<?php
            $k = 0;
            for ($i=0, $n=count( $rows ); $i < $n; $i++) {
                $row = &$rows[$i];
                $link 	= "index2.php?option=$option&section=$section&task=editA&id=". $row->id; //&hidemainmenu=1
                $checked 	= mosCommonHTML::CheckedOutProcessing( $row, $i );
                ?>
                <tr class="<?php echo "row$k"; ?>">
                    <td>
                    <?php echo $pageNav->rowNumber( $i ); ?>
                    </td>
                    <td>
                    <?php echo $checked; ?>
                    </td>
                    <td>
                    <?php
                    if ( $row->checked_out && ( $row->checked_out != $my->id ) ) {
                        echo stripslashes( $row->title );
                    } else {
                        ?>
                        <a href="<?php echo $link; ?>" title="<?php echo _juga_config_edit; ?>">
                        <?php echo stripslashes( $row->title ); ?>
                        </a>
                        <?php
                    }
                    ?>
                    </td>
                    <td>
                        <?php echo stripslashes( $row->description ); ?>
                    </td>
                    <td>
                        <?php echo stripslashes( $row->value ); ?>
                    </td>
                </tr>
                <?php
                $k = 1 - $k;
            }
            ?>
        </tbody>
        <tfoot>
        	<tr>
                <td colspan='5'>
                    <div class="pagination">
                    <?php echo $pageNav->getListFooter(); ?>
                    </div>            
                </td>
            </tr>
        </tfoot>
		</table>
        
        
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="section" value="<?php echo $section;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0">
		</form>
		<?php
	} // end listConfig()
// ************************************************************************

/**
* Writes the edit form for new and existing record
*
* A new record is defined when <var>$row</var> is passed with the <var>id</var>
* property set to 0.
* @param mosWeblink The weblink object
* @param array An array of select lists
* @param object Parameters
* @param string The option
*/
// ************************************************************************
function editConfig( &$row, &$lists, &$params, $option, $section ) {
		mosMakeHtmlSafe( $row, ENT_QUOTES, 'description' );

		JHTML::_('behavior.tooltip');
		?>
		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}

			// do field validation
			if (form.title.value == ""){
				alert( "Item must have a title." );
			} else if (form.value.value == ""){
				alert( "Item must select an value." );
			} else {
				submitform( pressbutton );
			}
		}
		</script>
		<form action="index2.php" method="post" name="adminForm" id="adminForm">
		<table class="adminheading">
		<tr>
			<th class='juga'>
			</th>
			<td class='right'>
			</td>
			<td class='right'>
			</td>
		</tr>
		</table>

		<table width="100%">
		<tr>
			<td width="60%" valign="top">
				<table class="adminlist">
				<thead>
                    <tr>
                        <th colspan="2">
                        <?php echo _juga_config_details ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th width="20%" align="right">
                        <?php echo _juga_config_title ?>:
                        </th>
                        <td width="80%">
                        <input class="text_area" type="text" name="title" size="50" maxlength="250" value="<?php echo stripslashes( $row->title ); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th valign="top" align="right">
                        <?php echo _juga_config_desc ?>:
                        </td>
                        <td>
                        <textarea class="text_area" cols="50" rows="5" name="description" style="width:500px" width="500"><?php echo stripslashes( $row->description ); ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th width="20%" align="right">
                        <?php echo _juga_config_value ?>:
                        </td>
                        <td width="80%">
                        <input class="text_area" type="text" name="value" size="50" maxlength="250" value="<?php echo stripslashes( $row->value ); ?>" />
                        </td>
                    </tr>
                </tbody>
				</table>
			</td>
		</tr>
		</table>

		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="section" value="<?php echo $section;?>" />
		<input type="hidden" name="task" value="" />
		</form>
		<?php
	} // end edit Config
// ************************************************************************

/**
* Compiles a list of records
* @param database A database connector object
*/
// ************************************************************************
	function listGroups( $option, $section, &$rows, &$lists, &$search, &$pageNav ) {
		global $my, $database;

		JHTML::_('behavior.tooltip');
		?>
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
		<tr>
			<th class='juga'>
			</th>
			<td>
			<?php echo _juga_config_filter; ?>
			</td>
			<td>
			<input type="text" name="search" value="<?php echo htmlspecialchars( $search );?>" class="text_area" onChange="document.adminForm.submit();" />
			</td>
		</tr>
		</table>

		<table class="adminlist">
		<thead>
            <tr>
                <th width="5">
                #
                </th>
                <th width="20">
                <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" />
                </th>
                <th class="title">
                <?php echo _juga_config_title ?>
                </th>
                <th class="title">
                <?php echo _juga_config_desc ?>
                </th>
                <th class="title">
                <?php echo _juga_config_usergroup_id ?>
                </th>            
                <th class="title">
                <?php echo _juga_config_members ?>
                </th>
                <th class="title">
                <?php echo _juga_config_items ?>
                </th>
            </tr>
        </thead>
        <tbody>
			<?php
            $k = 0;
            for ($i=0, $n=count( $rows ); $i < $n; $i++) {
                $row = &$rows[$i];
                $link 	= "index2.php?option=$option&section=$section&task=editA&id=". $row->id; //&hidemainmenu=1
                $checked 	= mosCommonHTML::CheckedOutProcessing( $row, $i );
                ?>
                <tr class="<?php echo "row$k"; ?>">
                    <td>
                    <?php echo $pageNav->rowNumber( $i ); ?>
                    </td>
                    <td>
                    <?php echo $checked; ?>
                    </td>
                    <td>
                    <?php
                    if ( $row->checked_out && ( $row->checked_out != $my->id ) ) {
                        echo stripslashes( $row->title );
                    } else {
                        ?>
                        <a href="<?php echo $link; ?>" title="<?php echo _juga_config_edit ?>">
                        <?php echo stripslashes( $row->title ); ?>
                        </a>
                        <?php
                    }
                    ?>
                    </td>
                    <td>
                        <?php echo stripslashes( $row->description ); ?>
                    </td>
                    <td>
                        <?php echo $row->id; ?>
                    </td>
                    <td>
                    <?php
                     $database->setQuery("SELECT COUNT(*) FROM #__juga_u2g "
                                         ." WHERE `group_id` = '$row->id'");
                     $mems = $database->loadResult();
                     ?>
                     <a href='index2.php?option=com_juga&section=u2g&group_id=<?php echo $row->id;?>'>
                     <?php
                     echo $mems;
                     ?>
                     </a>
                    </td>
                    <td>
                    <?php
                     $database->setQuery("SELECT COUNT(*) FROM #__juga_g2i "
                                         ." WHERE `group_id` = '$row->id'");
                     $items = $database->loadResult();
                     ?>
                     <a href='index2.php?option=com_juga&section=items&group_id=<?php echo $row->id;?>'>
                     <?php
                     echo $items;
                     ?>
                     </a>
                    </td>
                </tr>
                <?php
                $k = 1 - $k;
            }
            ?>
        </tbody>
        <tfoot>
        	<tr>
                <td colspan='7'>
                    <div class="pagination">
                    <?php echo $pageNav->getListFooter(); ?>
                    </div>            
                </td>
            </tr>
        </tfoot>

		</table>

		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="section" value="<?php echo $section;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0">
		</form>
		<?php
	} // end listGroups()
// ************************************************************************

/**
* Writes the edit form for new and existing record
*
* A new record is defined when <var>$row</var> is passed with the <var>id</var>
* property set to 0.
* @param mosWeblink The weblink object
* @param array An array of select lists
* @param object Parameters
* @param string The option
*/
// ************************************************************************
function editGroup( &$row, &$lists, &$params, $option, $section ) {
		mosMakeHtmlSafe( $row, ENT_QUOTES, 'description' );

		JHTML::_('behavior.tooltip');
		?>
		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}

			// do field validation
			if (form.title.value == ""){
				alert( "Item must have a title" );
			} else {
				submitform( pressbutton );
			}
		}
		</script>
		<form action="index2.php" method="post" name="adminForm" id="adminForm">
		<table class="adminheading">
		<tr>
			<th class='juga'>
			</th>
		</tr>
		</table>

		<table width="100%">
		<tr>
			<td width="60%" valign="top">
				<table class="adminlist">
				<thead>
                    <tr>
                        <th colspan="2">
                        <?php echo _juga_config_details ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th width="20%" align="right">
                        <?php echo _juga_config_title ?>:
                        </td>
                        <td width="80%">
                        <input class="text_area" type="text" name="title" size="50" maxlength="250" value="<?php echo stripslashes( $row->title );?>" />
                        </td>
                    </tr>
                    <tr>
                        <th valign="top" align="right">
                        <?php echo _juga_config_desc ?>:
                        </td>
                        <td>
                        <textarea class="text_area" cols="50" rows="5" name="description" style="width:500px" width="500"><?php echo stripslashes( $row->description ); ?></textarea>
                        </td>
                    </tr>
				</tbody>
				</table>
			</td>
		</tr>
		</table>

		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="section" value="<?php echo $section;?>" />
		<input type="hidden" name="task" value="" />
		</form>
		<?php
	} // end edit Groups
// ************************************************************************

/**
* Compiles a list of records
* @param database A database connector object
*/
// ************************************************************************
	function listItems( $option, $section, &$rows, &$lists, &$search, &$pageNav ) {
		global $my, $database;

		JHTML::_('behavior.tooltip');
		?>
		<script language="javascript" type="text/javascript">
		//needed by saveorder function
		function checkall_submit( n, task ) {
			for ( var j = 0; j <= n; j++ ) {
				box = eval( "document.adminForm.cb" + j );
				if ( box ) {
					if ( box.checked == false ) {
						box.checked = true;
					}
				} else {
					alert("You cannot modify these items, as an item in the list is `Checked Out`");
					return;
				}
			}
			submitform(task);
		}
		</script>                  
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
		<tr>
			<th class='juga'>
              [ <?php echo _juga_config_current_flex_group; ?>: <?php echo $lists['flex_juga']->title; ?> ]
			</th>
			<td>
			<?php echo _juga_config_filter; ?>
			</td>
			<td>
			<input type="text" name="search" value="<?php echo htmlspecialchars( $search );?>" class="text_area" onChange="document.adminForm.submit();" />
			</td>
			<td width="right">
			<?php echo $lists['type'];?>
			</td>
			<td width="right">
			<?php echo $lists['group_id'];?>
			</td>
		</tr>
		</table>

		<table class="adminlist">
        <thead>
            <tr>
                <th width="5">
                #
                </th>
                <th width="20">
                <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" />
                </th>
                <th class="title">
                <?php echo _juga_config_title ?>
                </th>
                <th class="title">
                <?php echo _juga_config_option ?>
                </th>
                <th class="title">
                <?php echo _JUGA_SECTION; ?>
                </th>
                <th class="title">
                <?php echo _JUGA_VIEW; ?>
                </th>
                <th class="title">
                <?php echo _juga_config_task ?>
                </th>
                <th class="title">
                <?php echo _juga_config_include ?>
                </th>
                <th class="title">
                <?php echo _juga_config_typeid ?>
                </th>
                <th class="title">
                <?php echo _juga_config_errorurl ?>
                </th>
                <th class='title'>
                <?php echo _juga_config_currentgroups ?>
                </th>
                <th class="title">
                <?php echo _juga_config_groups ?>
                <a href="javascript: checkall_submit( <?php echo count( $rows )-1; ?>, 'enroll' )"><img src="images/folder_add_f2.png" border="0" width="18" height="18" alt="<?php echo _juga_config_groups_enroll ?>" title="<?php echo _juga_config_groups_enroll ?>" name="<?php echo _juga_config_groups_enroll ?>" /></a>
                <a href="javascript: checkall_submit( <?php echo count( $rows )-1; ?>, 'withdraw' )"><img src="images/file_f2.png" border="0" width="18" height="18" alt="<?php echo _juga_config_groups_withdraw ?>" title="<?php echo _juga_config_groups_withdraw ?>" name="<?php echo _juga_config_groups_withdraw ?>" /></a>
                </th>
                <th class="title">
                <?php echo _juga_config_access; ?>
                </th>
            </tr>
        </thead>
        <tbody>
			<?php
            $database->setQuery("SELECT * FROM #__juga_groups "
                                ." ORDER BY `title` ASC");
            $db_groups = $database->loadObjectList();
    
            $k = 0;
            for ($i=0, $n=count( $rows ); $i < $n; $i++) {
                $row = &$rows[$i];
                $link 	= "index2.php?option=$option&section=$section&task=editA&id=". $row->id; //&hidemainmenu=1
                $checked 	= mosCommonHTML::CheckedOutProcessing( $row, $i );
    
                $taskx 	= $row->option_exclude ? 'switch_inclusion' : 'switch_inclusion';
                $imgx 	= $row->option_exclude ? 'publish_x.png' : 'tick.png';
                $altx 	= $row->option_exclude ? 'Excluded' : 'Included';
    
                $task 	= $row->error_url_published ? 'unpublish' : 'publish';
                $img 	= $row->error_url_published ? 'publish_g.png' : 'publish_x.png';
                $alt 	= $row->error_url_published ? 'Published' : 'Unpublished';
                ?>
                <tr class="<?php echo "row$k"; ?>">
                    <td>
                    <?php echo $pageNav->rowNumber( $i ); ?>
                    </td>
                    <td>
                    <?php echo $checked; ?>
                    </td>
                    <td>
                    <?php
                    if ( $row->checked_out && ( $row->checked_out != $my->id ) ) {
                        echo stripslashes( $row->title );
                    } else {
                        ?>
                        <a href="<?php echo $link; ?>" title="<?php echo _juga_config_edit ?>">
                        <?php echo stripslashes( $row->title ); ?>
                        </a>
                        <?php
                    }
                    ?>
                    </td>
                    <td>
                        <?php echo $row->site_option; ?>
                    </td>
                    <td>
                        <?php echo $row->site_section; ?>
                    </td>
                    <td>
                        <?php echo $row->site_view; ?>
                    </td>
                    <td>
                        <?php echo $row->site_task; ?>
                    </td>
                    <td>
                        <?php if ($row->type == "com") { ?>
                        <a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $taskx;?>')">
                        <img src="images/<?php echo $imgx;?>" width="16" height="16" border="0" alt="<?php echo $altx; ?>" />
                        </a>
                        <?php } else { echo "n/a"; } ?>
                    </td>
                    <td>
                        <?php
                        /*
                        switch ($row->type) {
                          case "com": echo "Component"; break;
                          case "cont": echo "Content"; break;
                          case "mod": echo "Module"; break;
                          default: echo "Other"; break;
                        }
                        */
                        echo $row->type_id;
                        ?>
                    </td>
                    <td valign="middle">
                        <a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')">
                        <img src="images/<?php echo $img;?>" width="16" height="16" border="0" alt="<?php echo $alt; ?>" />
                        </a>
                        <?php echo $row->error_url; ?>
                    </td>
                    <td>
                        <?php
                            $database->setQuery("SELECT * FROM #__juga_groups, #__juga_g2i "
                                                ." WHERE #__juga_g2i.group_id = #__juga_groups.id "
                                                ." AND #__juga_g2i.item_id = '$row->id' "
                                                ." ORDER BY `title` ASC");
                            $item_groups = $database->loadObjectList();
    
                            if ($item_groups) {
                                foreach ($item_groups as $g) {
                                  echo "<a href='index2.php?option=com_juga&section=groups&search=$g->title'>$g->title</a><br />";
                                } //endforeach
                            } //endif
                        ?>
                    </td>
                    <td>
                        <?php
                        if ($db_groups) {
                          echo "<select name='group[$row->id]' size='1'>";
                          echo "<option value=''>&nbsp;&nbsp;&nbsp;</option>";
                          foreach ($db_groups as $dbg) {
                            echo "<option value='$dbg->id'>$dbg->title&nbsp;&nbsp;&nbsp;</option>";
                          } // end foreach
                          echo "</select>";
                        } //end if
                        ?>
                    </td>
                    <td>
                        <a href='index2.php?option=com_juga&section=i2g&item_id=<?php echo $row->id; ?>'><img src="images/next_f2.png" border="0" width="18" height="18" alt="<?php echo _juga_config_define_access ?>" title="<?php echo _juga_config_define_access ?>" name="<?php echo _juga_config_define_access ?>" /></a>
                    </td>
                </tr>
                <?php
                $k = 1 - $k;
            }
            ?>
        </tbody>
        <tfoot>
        	<tr>
                <td colspan='13'>
                    <div class="pagination">
                    <?php echo $pageNav->getListFooter(); ?>
                    </div>            
                </td>
            </tr>
        </tfoot>
		</table>
        
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="section" value="<?php echo $section;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0">
		</form>
		<?php
	} // end listItems()
// ************************************************************************

	/**
	* Writes the edit form for new and existing record
	*
	* A new record is defined when <var>$row</var> is passed with the <var>id</var>
	* property set to 0.
	* @param mosWeblink The weblink object
	* @param array An array of select lists
	* @param object Parameters
	* @param string The option
	*/
// ************************************************************************
function editItem( &$row, &$lists, &$params, $option, $section ) {
	global $mosConfig_live_site;
	
		mosMakeHtmlSafe( $row, ENT_QUOTES, 'description' );

		JHTML::_('behavior.tooltip');
		?>
		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}

			// do field validation
			if (form.title.value == ""){
				alert( "Item must have a title." );
			} else if (form.option.value == ""){
				alert( "Item must select an option." );
			} else {
				submitform( pressbutton );
			}
		}
		</script>
		<form action="index2.php" method="post" name="adminForm" id="adminForm">
		<table class="adminheading">
		<tr>
			<th class='juga'>
			</th>
		</tr>
		</table>

		<table width="100%">
		<tr>
			<td width="60%" valign="top">
				<table class="adminlist">
                <thead>
                    <tr>
                        <th colspan="3">
                        <?php echo _juga_config_details ?>
                        </th>
                    </tr>
				</thead>
                <tbody>
                    <tr>
                        <td colspan='3'>
                        <center><img src='<?php echo $mosConfig_live_site."/administrator/components/com_juga/includes/images/site_items_01.png"; ?>' /></center>
                        </td>
                    </tr>
                    <tr>
                        <th width="20%" align="right">
                        <?php echo _juga_config_title ?>:
                        </td>
                        <td>
                        <?php $tip = "_TIP_juga_config_title"; if (defined($tip)) { echo JHTML::tooltip( constant($tip) ); } ?> 
                        </td>
                        <td>
                        <input class="text_area" type="text" name="title" size="50" maxlength="250" value="<?php echo stripslashes( $row->title );?>" />
                        </td>
                    </tr>
                    <tr>
                        <th width="20%" align="right">
                        <?php echo _juga_config_option ?>:
                        </td>
                        <td>
                        <?php $tip = "_TIP_juga_config_option"; if (defined($tip)) { echo JHTML::tooltip( constant($tip) ); } ?> 
                        </td>
                        <td>
                        <input class="text_area" type="text" name="site_option" size="50" maxlength="250" value="<?php echo $row->site_option;?>" />
                        </td>
                    </tr>
                    <tr>
                        <th width="20%" align="right">
                        <?php echo _JUGA_SECTION; ?>:
                        </td>
                        <td>
                        <?php $tip = "_TIP_JUGA_SECTION"; if (defined($tip)) { echo JHTML::tooltip( constant($tip) ); } ?> 
                        </td>
                        <td>
                        <input class="text_area" type="text" name="site_section" size="50" maxlength="250" value="<?php echo $row->site_section; ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th width="20%" align="right">
                        <?php echo _JUGA_VIEW; ?>:
                        </td>
                        <td>
                        <?php $tip = "_TIP_JUGA_VIEW"; if (defined($tip)) { echo JHTML::tooltip( constant($tip) ); } ?> 
                        </td>
                        <td>
                        <input class="text_area" type="text" name="site_view" size="50" maxlength="250" value="<?php echo $row->site_view; ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th width="20%" align="right">
                        <?php echo _juga_config_task ?>:
                        </td>
                        <td>
                        <?php $tip = "_TIP_juga_config_task"; if (defined($tip)) { echo JHTML::tooltip( constant($tip) ); } ?> 
                        </td>
                        <td>
                        <input class="text_area" type="text" name="site_task" size="50" maxlength="250" value="<?php echo $row->site_task;?>" />
                        </td>
                    </tr>
                    <tr>
                        <th width="20%" align="right">
                        <?php echo _juga_config_type ?>:
                        </td>
                        <td>
                        <?php $tip = "_TIP_juga_config_type"; if (defined($tip)) { echo JHTML::tooltip( constant($tip) ); } ?> 
                        </td>
                        <td>
                        <?php echo $lists['type']; ?>
                        </td>
                    </tr>
                    <tr>
                        <th width="20%" align="right">
                        <?php echo _juga_config_typeid ?>:
                        </td>
                        <td>
                        <?php $tip = "_TIP_juga_config_typeid"; if (defined($tip)) { echo JHTML::tooltip( constant($tip) ); } ?> 
                        </td>
                        <td>
                        <input class="text_area" type="text" name="type_id" size="50" maxlength="250" value="<?php echo $row->type_id;?>" />
                        </td>
                    </tr>
                    <tr>
                        <th width="20%" align="right">
                        <?php echo _juga_config_errorurl1 ?>:
                        </td>
                        <td>
                        <?php $tip = "_TIP_juga_config_errorurl1"; if (defined($tip)) { echo JHTML::tooltip( constant($tip) ); } ?> 
                        </td>
                        <td>
                        <input class="text_area" type="text" name="error_url" size="50" maxlength="250" value="<?php echo $row->error_url;?>" />
                        </td>
                    </tr>
                    <tr>
                        <th width="20%" align="right">
                        <?php echo _juga_config_errorurl_published ?>:
                        </td>
                        <td>
                        <?php $tip = "_TIP_juga_config_errorurl_published"; if (defined($tip)) { echo JHTML::tooltip( constant($tip) ); } ?> 
                        </td>
                        <td>
                        <?php echo $lists['error_url_published']; ?>
                        </td>
                    </tr>
                    <tr>
                        <th width="20%" align="right">
                        <?php echo _juga_config_exclude ?>?
                        </td>
                        <td>
                        <?php $tip = "_TIP_juga_config_exclude"; if (defined($tip)) { echo JHTML::tooltip( constant($tip) ); } ?> 
                        </td>
                        <td>
                        <?php echo $lists['option_exclude']; ?>
                        </td>
                    </tr>
				</tbody>
				</table>
			</td>
		</tr>
		</table>

		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="section" value="<?php echo $section;?>" />
		<input type="hidden" name="task" value="" />
		</form>
		<?php
	} // end edit Item
// ************************************************************************

/**
* Compiles a list of records
* @param database A database connector object
*/
// ************************************************************************
	function listUsers( $option, $section, &$rows, &$lists, &$search, &$pageNav ) {
		global $my, $database;

		JHTML::_('behavior.tooltip');
		?>
		<script language="javascript" type="text/javascript">
		//needed by saveorder function
		function checkall_submit( n, task ) {
			for ( var j = 0; j <= n; j++ ) {
				box = eval( "document.adminForm.cb" + j );
				if ( box ) {
					if ( box.checked == false ) {
						box.checked = true;
					}
				} else {
					alert("You cannot modify these items, as an item in the list is `Checked Out`");
					return;
				}
			}
			submitform(task);
		}
		</script>
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
		<tr>
			<th class='juga'>
			</th>
			<td>
			<?php echo _juga_config_filter ?>
			</td>
			<td>
			<input type="text" name="search" value="<?php echo htmlspecialchars( $search );?>" class="text_area" onChange="document.adminForm.submit();" />
			</td>
			<td width="right">
			<?php echo $lists['group_id'];?>
			</td>
		</tr>
		</table>

		<table class="adminlist">
        <thead>
            <tr>
                <th width="5">
                #
                </th>
                <th width="20">
                <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" />
                </th>
                <th class="title">
                <?php echo _juga_config_user ?>
                </th>
                <th class="title">
                <?php echo _juga_config_username ?>
                </th>
                <th class="title">
                <?php echo _JUGA_EMAIL ?>
                </th>
                <th class="title">
                <?php echo _juga_config_currentgroups ?>
                </th>
                <th class="title">
                <?php echo _juga_config_groups ?>
                <a href="javascript: checkall_submit( <?php echo count( $rows )-1; ?>, 'enroll' )"><img src="images/folder_add_f2.png" border="0" width="18" height="18" alt="<?php echo _juga_config_groups_enroll ?>" title="<?php echo _juga_config_groups_enroll ?>" name="<?php echo _juga_config_groups_enroll ?>" /></a>
                <a href="javascript: checkall_submit( <?php echo count( $rows )-1; ?>, 'withdraw' )"><img src="images/file_f2.png" border="0" width="18" height="18" alt="<?php echo _juga_config_groups_withdraw ?>" title="<?php echo _juga_config_groups_withdraw ?>" name="<?php echo _juga_config_groups_withdraw ?>" /></a>
                </th>
                <th class="title">
                <?php echo _juga_config_access; ?>
                </th>
            </tr>
        </thead>
        <tbody>
			<?php
            $database->setQuery("SELECT * FROM #__juga_groups "
                                ." ORDER BY `title` ASC");
            $db_groups = $database->loadObjectList();
    
            $k = 0;
            for ($i=0, $n=count( $rows ); $i < $n; $i++) {
                $row = &$rows[$i];
                // $link 	= "index2.php?option=$option&section=$section&task=editA&id=". $row->id; //&hidemainmenu=1
                if ($lists["com_comprofiler"]) { 
                    $link 	= "index2.php?option=com_comprofiler&task=showusers&uid=$row->id&search=$row->username";
                } else {
                    $link	= "index2.php?option=com_users&task=view&search=".$row->username;
                }
				$row->checked_out = null;
                $checked 	= mosCommonHTML::CheckedOutProcessing( $row, $i );
                ?>
                <tr class="<?php echo "row$k"; ?>">
                    <td>
                    <?php echo $pageNav->rowNumber( $i ); ?>
                    </td>
                    <td>
                    <?php echo $checked; ?>
                    </td>
                    <td>
                    <?php
                    if ( $row->checked_out && ( $row->checked_out != $my->id ) ) {
                        echo $row->name;
                    } else {
                        ?>
                        <a href="<?php echo $link; ?>" title="<?php echo _juga_config_edit ?>">
                        <?php echo $row->name; ?>
                        </a>
                        <?php
                    }
                    ?>
                    </td>
                    <td>
                        <?php if ($lists["com_comprofiler"]) { ?>
                        [
                        <a href='index2.php?option=com_comprofiler&task=showusers&search=<?php echo $row->username; ?>'>
                        <?php echo "CB"; ?>
                        </a>
                        ]&nbsp;
                        <?php } ?>
                        <a href='index2.php?option=com_users&task=view&search=<?php echo $row->username; ?>'>
                        <?php echo $row->username; ?>
                        </a>
                    </td>
                    <td>
                    <?php echo $row->email; ?>
                    </td>
                    <td>
                        <?php
                            $database->setQuery("SELECT * FROM #__juga_groups, #__juga_u2g WHERE #__juga_u2g.group_id = #__juga_groups.id AND #__juga_u2g.user_id = '$row->id' "
                                                ." ORDER BY `title` ASC");
                            $user_groups = $database->loadObjectList();
    
                            if ($user_groups) {
                                foreach ($user_groups as $g) {
                                  echo "<a href='index2.php?option=com_juga&section=groups&search=$g->title'>$g->title</a><br />";
                                } //endforeach
                            } //endif
                        ?>
                    </td>
                    <td>
                        <?php
                        if ($db_groups) {
                          echo "<select name='group[$row->id]' size='1'>";
                          echo "<option value=''>&nbsp;&nbsp;&nbsp;</option>";
                          foreach ($db_groups as $dbg) {
                            echo "<option value='$dbg->id'>$dbg->title&nbsp;&nbsp;&nbsp;</option>";
                          } // end foreach
                          echo "</select>";
                        } //end if
                        ?>
                    </td>
                    <td>
                        <a href='index2.php?option=com_juga&section=g2u&user_id=<?php echo $row->id; ?>'><img src="images/next_f2.png" border="0" width="18" height="18" alt="<?php echo _juga_config_define_access ?>" title="<?php echo _juga_config_define_access ?>" name="<?php echo _juga_config_define_access ?>" /></a>
                    </td>
                </tr>
                <?php
                $k = 1 - $k;
            }
            ?>
        </tbody>
        <tfoot>
        	<tr>
                <td colspan='8'>
                    <div class="pagination">
                    <?php echo $pageNav->getListFooter(); ?>
                    </div>            
                </td>
            </tr>
        </tfoot>
		</table>

		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="section" value="<?php echo $section;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0">
		</form>
		<?php
	} // end listUsers()
// ************************************************************************

/**
* Compiles a list of records
* @param database A database connector object
*/
// ************************************************************************
	function defineItem2Groups( $option, $section, &$rows, &$lists, &$search, &$pageNav ) {
		global $my, $database;

		JHTML::_('behavior.tooltip');
		?>
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
		<tr>
			<th class='juga'>
			</th>
		</tr>
		</table>

		<table class="adminlist">
        <thead>
            <tr>
                <th width="5">
                #
                </th>
                <th width="20">
                <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" />
                </th>
                <th class="title">
                <?php echo _juga_config_title ?>
                </th>
                <th class="title">
                <?php echo _juga_config_desc ?>
                </th>
                <th class="title">
                <?php echo _juga_config_usergroup_id ?>
                </th>            
                <th class="title">
                <?php echo _juga_config_access ?>
                </th>
            </tr>
        </thead>
        <tbody>
			<?php
            $k = 0;
            for ($i=0, $n=count( $rows ); $i < $n; $i++) {
                $row = &$rows[$i];
                $link 	= "index2.php?option=$option&section=$section&task=editA&id=". $row->id; //&hidemainmenu=1
                $checked 	= mosCommonHTML::CheckedOutProcessing( $row, $i );
                $database->setQuery("SELECT item_id FROM #__juga_g2i "
                                    ." WHERE `group_id` = '$row->id'"
                                    ." AND `item_id` = '".$lists['item']->id."' ");
                $already = $database->loadResult();			
    
                $img 	= $already ? 'tick.png' : 'publish_x.png';
                $alt 	= $already ? 'Access' : 'No Access';
    
                ?>
                <tr class="<?php echo "row$k"; ?>">
                    <td>
                    <?php echo $pageNav->rowNumber( $i ); ?>
                    </td>
                    <td>
                    <?php echo $checked; ?>
                    </td>
                    <td>
                    <?php
                    if ( $row->checked_out && ( $row->checked_out != $my->id ) ) {
                        echo stripslashes( $row->title );
                    } else {
                        ?>
                        <?php echo stripslashes( $row->title ); ?>
                        <?php
                    }
                    ?>
                    </td>
                    <td>
                        <?php echo stripslashes( $row->description ); ?>
                    </td>
                    <td>
                        <?php echo $row->id; ?>
                    </td>
                    <td>
                    <img src="images/<?php echo $img;?>" width="12" height="12" border="0" alt="<?php echo $alt; ?>" />
                    </td>
                </tr>
                <?php
                $k = 1 - $k;
            }
            ?>
        </tbody>
        <tfoot>
        	<tr>
                <td colspan='6'>
                    <div class="pagination">
                    <?php echo $pageNav->getListFooter(); ?>
                    </div>            
                </td>
            </tr>
        </tfoot>
		</table>

		<input type="hidden" name="item_id" value="<?php echo $lists['item']->id;?>" />
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="section" value="<?php echo $section;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0">
		</form>
		<?php
	} // end defineItem2Groups()
// ************************************************************************

/**
* Compiles a list of records
* @param database A database connector object
*/
// ************************************************************************
	function defineUser2Groups( $option, $section, &$rows, &$lists, &$search, &$pageNav ) {
		global $my, $database;

		JHTML::_('behavior.tooltip');
		?>
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
		<tr>
			<th class='juga'>
			</th>
		</tr>
		</table>

		<table class="adminlist">
        <thead>
            <tr>
                <th width="5">
                #
                </th>
                <th width="20">
                <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" />
                </th>
                <th class="title">
                <?php echo _juga_config_title ?>
                </th>
                <th class="title">
                <?php echo _juga_config_desc ?>
                </th>
                <th class="title">
                <?php echo _juga_config_usergroup_id ?>
                </th>            
                <th class="title">
                <?php echo _juga_config_access ?>
                </th>
            </tr>
        </thead>
        <tbody>
			<?php
            $k = 0;
            for ($i=0, $n=count( $rows ); $i < $n; $i++) {
                $row = &$rows[$i];
                $link 	= "index2.php?option=$option&section=$section&task=editA&id=". $row->id; //&hidemainmenu=1
                $checked 	= mosCommonHTML::CheckedOutProcessing( $row, $i );
                $database->setQuery("SELECT user_id FROM #__juga_u2g "
                                    ." WHERE `group_id` = '$row->id'"
                                    ." AND `user_id` = '".$lists['user']->id."' ");
                $already = $database->loadResult();			
    
                $img 	= $already ? 'tick.png' : 'publish_x.png';
                $alt 	= $already ? 'Access' : 'No Access';
    
                ?>
                <tr class="<?php echo "row$k"; ?>">
                    <td>
                    <?php echo $pageNav->rowNumber( $i ); ?>
                    </td>
                    <td>
                    <?php echo $checked; ?>
                    </td>
                    <td>
                    <?php
                    if ( $row->checked_out && ( $row->checked_out != $my->id ) ) {
                        echo stripslashes( $row->title );
                    } else {
                        ?>
                        <?php echo stripslashes( $row->title ); ?>
                        <?php
                    }
                    ?>
                    </td>
                    <td>
                        <?php echo stripslashes( $row->description ); ?>
                    </td>
                    <td>
                        <?php echo $row->id; ?>
                    </td>
                    <td>
                    <img src="images/<?php echo $img;?>" width="12" height="12" border="0" alt="<?php echo $alt; ?>" />
                    </td>
                </tr>
                <?php
                $k = 1 - $k;
            }
            ?>
        </tbody>
        <tfoot>
        	<tr>
                <td colspan='6'>
                    <div class="pagination">
                    <?php echo $pageNav->getListFooter(); ?>
                    </div>            
                </td>
            </tr>
        </tfoot>
		</table>

		<input type="hidden" name="user_id" value="<?php echo $lists['user']->id;?>" />
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="section" value="<?php echo $section;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0">
		</form>
		<?php
	} // end defineUser2Groups()
// ************************************************************************

/**
* Compiles a list of records
* @param database A database connector object
*/
// ************************************************************************
	function listCodes( $option, $section, &$rows, &$lists, &$search, &$pageNav ) {
		global $my, $database;

		JHTML::_('behavior.tooltip');
		?>
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
		<tr>
			<th class='juga'>
			</th>
			<td>
			<?php echo _juga_config_filter; ?>
			</td>
			<td>
			<input type="text" name="search" value="<?php echo htmlspecialchars( $search );?>" class="text_area" onChange="document.adminForm.submit();" />
			</td>
		</tr>
		</table>

		<table class="adminlist">
        <thead>
            <tr>
                <th width="5">
                #
                </th>
                <th width="20">
                <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" />
                </th>
                <th class="title">
                <?php echo _juga_config_title; ?>
                </th>
                <th class="title">
                <?php echo _juga_config_desc; ?>
                </th>
                <th class="title">
                <?php echo _juga_config_codes_group; ?>
                </th>
                <th class="title">
                <?php echo _juga_config_publish; ?>
                </th>
                <th class="title">
                <?php echo _juga_config_codes_status; ?>
                </th>
                <th class="title">
                <?php echo _juga_config_codes_times; ?>
                </th>
                <th class="title">
                <?php echo _juga_config_codes_hits; ?>
                </th>
                <th class="title">
                <?php echo _JUGA_ID; ?>
                </th>
            </tr>
        </thead>
        <tbody>
			<?php
            $k = 0;
            $nullDate = $database->getNullDate();
            for ($i=0, $n=count( $rows ); $i < $n; $i++) {
                $row = &$rows[$i];
                $link 	= "index2.php?option=$option&section=$section&task=editA&id=". $row->id; //&hidemainmenu=1
                $checked 	= mosCommonHTML::CheckedOutProcessing( $row, $i );					
                ?>
                <tr class="<?php echo "row$k"; ?>">
                    <td>
                    <?php echo $pageNav->rowNumber( $i ); ?>
                    </td>
                    <td>
                    <?php echo $checked; ?>
                    </td>
                    <td>
                    <?php
                    if ( $row->checked_out && ( $row->checked_out != $my->id ) ) {
                        echo stripslashes( $row->title );
                    } else {
                        ?>
                        <a href="<?php echo $link; ?>" title="<?php echo _juga_config_edit; ?>">
                        <?php echo stripslashes( $row->title ); ?>
                        </a>
                        <?php
                    }
                    ?>
                    </td>
                    <td>
                        <?php echo stripslashes( $row->description ); ?>
                    </td>
                    <td>
                    <?php
                            $database->setQuery("SELECT * FROM #__juga_groups "
                                                ." WHERE `id` = '$row->group_id' "
                                                );
                            $group = $database->loadObjectList();
    
                            if ($group) {
                                foreach ($group as $g) {
                                  echo "<a href='index2.php?option=com_juga&section=groups&search=$g->title'>$g->title</a> ($row->group_id)<br />";
                                } //endforeach
                            } //endif
                     ?>      
                    </td>
                    <td>
                        <?php
                        $task 	= $row->published ? 'unpublish' : 'publish';
                        $img 	= $row->published ? 'tick.png' : 'publish_x.png';
                        $alt 	= $row->published ? 'Published' : 'Unpublished';
                        ?>				
                        <a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')">
                        <img src="images/<?php echo $img;?>" width="12" height="12" border="0" alt="<?php echo $alt; ?>" />
                        </a>
                    </td>
                    <td>
                        <?php
                        $now = _CURRENT_SERVER_TIME;
                        if ( $now <= $row->publish_up && $row->published == 1 ) {
                        // Pending
                            $img = 'publish_y.png';
                            $alt = 'Scheduled';
                        } else if ( ( $now <= $row->publish_down || $row->publish_down == $nullDate ) && $row->published == 1 ) {
                        // Published
                            $img = 'publish_g.png';
                            $alt = 'Enabled';
                        } else if ( $now > $row->publish_down && $row->published == 1 ) {
                        // Expired
                            $img = 'publish_r.png';
                            $alt = 'Expired';
                        } elseif ( $row->published == 0 ) {
                        // Unpublished
                            $img = 'publish_x.png';
                            $alt = 'Disabled';
                        } 
                        
                        if ( $row->times_allowed != 0 && $row->hits >= $row->times_allowed && $row->published == 1 ) {
                        // Hits over Times
                            $img = 'publish_r.png';
                            $alt = 'Limit Reached';
                        }
            
                        // correct times to include server offset info
                        $row->publish_up 	= mosFormatDate( $row->publish_up, _CURRENT_SERVER_TIME_FORMAT );
                        if (trim( $row->publish_down ) == $nullDate || trim( $row->publish_down ) == '' || trim( $row->publish_down ) == '-' ) {
                            $row->publish_down = 'Never';
                        }
                        $row->publish_down 	= mosFormatDate( $row->publish_down, _CURRENT_SERVER_TIME_FORMAT );
            
                        $times = '';
                        if ($row->publish_up == $nullDate) {
                            $times .= "<tr><td>Start: Always</td></tr>";
                        } else {
                            $times .= "<tr><td>Start: $row->publish_up</td></tr>";
                        }
                        if ($row->publish_down == $nullDate || $row->publish_down == 'Never') {
                            $times .= "<tr><td>Finish: No Expiry</td></tr>";
                        } else {
                            $times .= "<tr><td>Finish: $row->publish_down</td></tr>";
                        }
                        $times .= "<tr><td>Status: $alt</td></tr>";
                        ?>
                        <img src="images/<?php echo $img;?>" width="12" height="12" border="0" alt="<?php echo $alt; ?>" onMouseOver="return overlib('<table><?php echo $times; ?></table>', CAPTION, 'Availability Information', BELOW, RIGHT);" onMouseOut="return nd();" />
                        <?php // echo "status icon"; ?>
                    </td>
                    <td>
                        <?php echo $row->times_allowed; ?>
                    </td>
                    <td>
                        <?php echo $row->hits; ?>
                    </td>
                    <td>
                        <?php echo $row->id; ?>
                    </td>
                </tr>
                <?php
                $k = 1 - $k;
            } 
			
			if (!$rows) { ?>
				<tr>
					<td colspan='10'>
						<?php echo _JUGA_NORECORDS; ?>
					</td>
				</tr>			
			<?php } ?>
        </tbody>
        <tfoot>
        	<tr>
                <td colspan='10'>
                    <div class="pagination">
                    <?php echo $pageNav->getListFooter(); ?>
                    </div>            
                </td>
            </tr>
        </tfoot>
		</table>

		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="section" value="<?php echo $section;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0">
		</form>
		<?php
	} // end listCode()
// ************************************************************************

/**
* Writes the edit form for new and existing record
*
* @param string The option
*/
// ************************************************************************
function editCode( &$row, &$lists, &$params, $option, $section ) {
		mosMakeHtmlSafe( $row, ENT_QUOTES, 'description' );

		JHTML::_('behavior.tooltip');
		?>
		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}

			// do field validation
			if (form.title.value == ""){
				alert( "Item must have a title." );
			} else {
				submitform( pressbutton );
			}
		}
		</script>
		<form action="index2.php" method="post" name="adminForm" id="adminForm">
		<table class="adminheading">
		<tr>
			<th class='juga'>
			</th>
		</tr>
		</table>

		<table width="100%">
		<tr>
			<td width="60%" valign="top">
				<table class="adminlist">
                <thead>
                    <tr>
                        <th colspan="3">
                        <?php echo _juga_config_details ?>
                        </th>
                    </tr>
                </thead>
                </tbody>
                    <tr>
                        <th width="20%" align="right">
                        <?php echo _juga_config_title ?>:
                        </td>
                        <td></td>
                        <td>
                        <input class="text_area" type="text" name="title" size="50" maxlength="250" value="<?php echo stripslashes( $row->title );?>" />
                        </td>
    
                    </tr>
                    <tr>
                        <th width="20%" align="right">
                        <?php echo _juga_config_desc ?>:
                        </td>
                        <td></td>
                        <td>
                        <textarea class="text_area" cols="50" rows="5" name="description" style="width:500px" width="500"><?php echo stripslashes( $row->description ); ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th width="20%" align="right">
                        <?php echo _juga_config_usergroup ?>
                        </td>
                        <td></td>
                        <td>
                        <?php echo $lists[groups]; ?>
                        </td>
                    </tr>
                    <tr>
                        <th width="20%" align="right">
                        <?php echo _JUGA_PUBLISHED ?>:
                        </td>
                        <td></td>
                        <td>
                        <?php echo $lists[published];?>
                        </td>
                    </tr>
                    <tr>
                        <th width="20%" align="right">
                        <?php echo _JUGA_PUBLISHUP ?>:
                        </td>
                        <td></td>
                        <td>
                        <?php mosCommonHTML::loadCalendar(); ?>
                        <input class="text_area" type="text" name="publish_up" id="publish_up" size="25" maxlength="19" value="<?php echo $row->publish_up; ?>" readonly/>
                        <input type="reset" class="button" value="<?php echo _JUGA_SELECTDATE; ?>" onclick="return showCalendar('publish_up', 'y-mm-dd');" />
                        </td>
                    </tr>
                    <tr>
                        <th width="20%" align="right">
                        <?php echo _JUGA_PUBLISHDOWN; ?>:
                        </td>
                        <td></td>
                        <td>
                        <input class="text_area" type="text" name="publish_down" id="publish_down" size="25" maxlength="19" value="<?php echo $row->publish_down; ?>" readonly/>
                        <input type="reset" class="button" value="<?php echo _JUGA_SELECTDATE; ?>" onclick="return showCalendar('publish_down', 'y-mm-dd');" />
                        </td>
                    </tr>
                    <tr>
                        <th width="20%" align="right">
                        <?php echo _juga_config_codes_times ?>:
                        </td>
                        <td>
                        <?php $tip = "_TIP_juga_config_codes_times"; if (defined($tip)) { echo JHTML::tooltip( constant($tip) ); } ?> 
                        </td>
                        <td>
                        <input class="text_area" type="text" name="times_allowed" size="50" maxlength="250" value="<?php echo $row->times_allowed;?>" />
                        </td>
                    </tr>
                </tbody>
				</table>
			</td>
		</tr>
		</table>

		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="section" value="<?php echo $section;?>" />
		<input type="hidden" name="task" value="" />
		</form>
		<?php  
	} // end editCode
// ************************************************************************

/**
* list of Tools
* @param string The option
*/
// ************************************************************************
	function listTools ( $option, $section ) {
	?>
    
		<table class="adminheading">
		<tr>
			<th class='juga'>
			</th>
		</tr>
		</table>
        
        
		<table class="adminlist">
		<thead>
            <tr>
                <th width="5">
                #
                </th>
                <th class="title">
                <?php echo _juga_config_title; ?>
                </th>
                <th></th>
            </tr>
		</thead>
		<tbody>
            <tr>
                <td>1</td>
                <td><?php echo _juga_config_patch; ?></td>
                <td><input type="button" class="button" value="<?php echo _JUGA_SUBMIT; ?>" onclick="window.location='index2.php?option=com_juga&section=tools&task=patch'"/></td>
            </tr>
		</tbody>
        </table>

	<?php
	}
// ************************************************************************

} // end class
