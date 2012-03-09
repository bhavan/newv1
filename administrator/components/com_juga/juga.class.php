<?php
// ensure this file is being included by a parent file  
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.'); 


/**
* jugaRightsCheck - grabs whether the user has the rights to perform this action
* @details = array(
*		[user]
*		[option]
*		[section]
*		[task]
*		[id]
*		[site]
* )
*/
// ************************************************************************
function jugaRightsCheck( $details ) {
	global $mosConfig_absolute_path, $mosConfig_live_site, $mainframe, $database;
	
	$my = $details["user"];
	$title 		= strval ( htmlspecialchars ( $details["title"] ) );
	$option 	= strval ( htmlspecialchars ( $details["option"] ) );
	$section	= strval ( htmlspecialchars ( $details["section"] ) );
	$view		= strval ( htmlspecialchars ( $details["view"] ) );
	$task 		= strval ( htmlspecialchars ( $details["task"] ) );
	$id			= intval ( $details["id"] );
	$site		= strval ( htmlspecialchars ( $details["site"] ) );

	// grab juga_superusergroup / superuser group id
	$database->setQuery("SELECT `value` FROM #__juga "
					 	." WHERE `title` = 'juga_superusergroup' ");
	$juga_superusergroup = $database->loadResult();

	// grab default_juga_admin / admin default access group
	$database->setQuery("SELECT `value` FROM #__juga "
					 	." WHERE `title` = 'default_juga_admin' ");
	$default_juga_admin = $database->loadResult();
	
	// grab default_juga / public access group
	$database->setQuery("SELECT `value` FROM #__juga "
					 	." WHERE `title` = 'default_juga' ");
	$default_juga = $database->loadResult();
	
	// site or administrator
	if ($site == "site") { $default_group = $default_juga; } 
	else { $default_group = $default_juga_admin; } 
	
	// SUPERUSER
	// if user is part of JUGA superuser group, allow access
	// but check if the Site Item is present in JUGA's DB
	$query = "SELECT * "
	. "FROM #__juga_u2g \n"
	. "WHERE #__juga_u2g.group_id = '".$juga_superusergroup."' " // superuser
	. "AND #__juga_u2g.user_id = '$my->id' "
	;
	$database->setQuery($query);
	$database->loadObject($superuser);
	if ($superuser || $my->gid == "25") { 
		// select the item's info
		$query = "SELECT * "
		. "FROM #__juga_items \n"
		. "WHERE #__juga_items.site_option = '$option' "
		. "AND #__juga_items.site_section = '$section' "
		. "AND #__juga_items.site_view = '$view' "
		. "AND #__juga_items.site_task = '$task' "
		. "AND #__juga_items.type_id = '$id' ";
		$database->setQuery($query);
		$database->loadObject($juga_item);

		// if the item (option & task combo) isn't in the db of juga_items
		// add it	
		if (!$juga_item) {
			if ($option == "com_content") { $juga_type = "cont"; } else { $juga_type = "com"; }
			if ($title) { $juga_title = $title; } else { $juga_title = "$option $section $view $task"; }
			// $juga_title = $mainframe->getPageTitle();
			// $juga_title = ereg_replace ( "$mosConfig_sitename - ", '', $juga_title );
			$newJugaItem = new jugaItem( $database );
			$newJugaItem->title			= $juga_title;
			$newJugaItem->site_option	= $option;
			$newJugaItem->site_section	= $section;
			$newJugaItem->site_view		= $view;
			$newJugaItem->site_task		= $task;
			$newJugaItem->type			= $juga_type;
			$newJugaItem->type_id		= $id;
			$newJugaItem->store();

			// add item to default_group 
			// which varies depending on whether we're the Admin side or Not
			// and only if default_group > 0
			if ($default_group > 0) {
				$query = "INSERT INTO #__juga_g2i "
					."\n SET #__juga_g2i.item_id = '".$newJugaItem->id."', "
					."\n #__juga_g2i.group_id = '".$default_group."'";
				$database->setQuery($query);
				$database->query();
			}				

			
			// check public_access for item
			$query = "SELECT * "
			. " FROM #__juga_g2i "
			. " LEFT JOIN #__juga_items ON #__juga_g2i.item_id = #__juga_items.id "
			. " WHERE #__juga_g2i.group_id = '$default_juga' "
			. " AND #__juga_items.site_option = '$option' "
			. " AND #__juga_items.site_section = '$section' "
			. " AND #__juga_items.site_view = '$view' "
			. " AND #__juga_items.site_task = '$task' "
			. " AND #__juga_items.type_id = '$id' "
			;
			$database->setQuery($query);
			$public_access = $database->loadObjectList();
			
			// if the item is public access/default_juga, then allow access
			if ($public_access) { $return["access"] = true; }

		} // end if no juga_item   
	
		$return["access"] = true; 
		return $return; 
	}

	// check public_access for item
	$query = "SELECT * "
	. " FROM #__juga_g2i "
	. " LEFT JOIN #__juga_items ON #__juga_g2i.item_id = #__juga_items.id "
	. " WHERE #__juga_g2i.group_id = '$default_juga' "
	. " AND #__juga_items.site_option = '$option' "
	. " AND #__juga_items.site_section = '$section' "
	. " AND #__juga_items.site_view = '$view' "
	. " AND #__juga_items.site_task = '$task' "
	. " AND #__juga_items.type_id = '$id' "
	;
	$database->setQuery($query);
    $public_access = $database->loadObjectList();
	
	// if the item is public access/default_juga, then allow access
	if ($public_access) { $return["access"] = true; }
	elseif (!$public_access) {
		// check exclusion status
		$query = "SELECT * "
		. " FROM #__juga_items "
		. " WHERE #__juga_items.option_exclude = '1' "
		. " AND #__juga_items.site_option = '$option' "
		. " AND #__juga_items.type = 'com' "
		;
		$database->setQuery($query);
		$ex = $database->loadObjectList();
		// if component is in excluded list, allow access
		if ($ex) { 
			// select the item's info
			$query = "SELECT * "
			. "FROM #__juga_items \n"
			. "WHERE #__juga_items.site_option = '$option' "
			. "AND #__juga_items.site_section = '$section' "
			. " AND #__juga_items.site_view = '$view' "
			. "AND #__juga_items.site_task = '$task' "
			. "AND #__juga_items.type_id = '$id' ";
			$database->setQuery($query);
			$database->loadObject($juga_item);
	
			// if the item (option & task combo) isn't in the db of juga_items
			// add it	
			if (!$juga_item) {
				if ($option == "com_content") { $juga_type = "cont"; } else { $juga_type = "com"; }
				if ($title) { $juga_title = $title; } else { $juga_title = "$option $section $view $task"; }
				// $juga_title = $mainframe->getPageTitle();
				// $juga_title = ereg_replace ( "$mosConfig_sitename - ", '', $juga_title );
				$newJugaItem = new jugaItem( $database );
				$newJugaItem->title			= $juga_title;
				$newJugaItem->site_option	= $option;
				$newJugaItem->site_section	= $section;
				$newJugaItem->site_view		= $view;
				$newJugaItem->site_task		= $task;
				$newJugaItem->type			= $juga_type;
				$newJugaItem->type_id		= $id;
				$newJugaItem->store();
		
				// add item to default_group 
				// which varies depending on whether we're the Admin side or Not
				// and only if default_group > 0
				if ($default_group > 0) {
					$query = "INSERT INTO #__juga_g2i "
						."\n SET #__juga_g2i.item_id = '".$newJugaItem->id."', "
						."\n #__juga_g2i.group_id = '".$default_group."'";
					$database->setQuery($query);
					$database->query();
				}				
			} // end if no juga_item   
		
			$excluded_component = true; 
			$return["access"] = true; 
			}
		elseif (!$ex) {
			// check user's access to content section/category
			$section_access = false; // not supported yet
			$cat_access = false; // not supported yet

			// if user has access to section or category (and item is content), allow access
			if ($section_access || $cat_access) { $return["access"] = true; }
			elseif (!($section_access || $cat_access)) {
				// else, check the user's group, and allow/disallow access accordingly	
				// check user's access to file	
				$query = "SELECT * "
				. "FROM #__juga_u2g \n"
				. "LEFT JOIN #__juga_g2i ON #__juga_g2i.group_id = #__juga_u2g.group_id \n"
				. "LEFT JOIN #__juga_items ON #__juga_g2i.item_id = #__juga_items.id \n"
				. "WHERE #__juga_items.site_option = '$option' "
				. "AND #__juga_items.site_section = '$section' "
				. " AND #__juga_items.site_view = '$view' "
				. "AND #__juga_items.site_task = '$task' "
				. "AND #__juga_items.type_id = '$id' "
				. "AND #__juga_u2g.user_id = '$my->id' "
				;
				$database->setQuery($query);
				$access = $database->loadObjectList();
				
				// ensure user has access to this item
				if ($access) { $return["access"] = true; }
				elseif (!$access) {
					// user has NO access if here
					$return["access"] = false; 
					
					// select the item's info
					$query = "SELECT * "
					. "FROM #__juga_items \n"
					. "WHERE #__juga_items.site_option = '$option' "
					. "AND #__juga_items.site_section = '$section' "
					. " AND #__juga_items.site_view = '$view' "
					. "AND #__juga_items.site_task = '$task' "
					. "AND #__juga_items.type_id = '$id' ";
					$database->setQuery($query);
					$database->loadObject($juga_item);
			
					// if the item (option & task combo) isn't in the db of juga_items
					// add it	
					if (!$juga_item) {
						if ($option == "com_content") { $juga_type = "cont"; } else { $juga_type = "com"; }
						if ($title) { $juga_title = $title; } else { $juga_title = "$option $section $view $task"; }
						// $juga_title = $mainframe->getPageTitle();
						// $juga_title = ereg_replace ( "$mosConfig_sitename - ", '', $juga_title );
						$newJugaItem = new jugaItem( $database );
						$newJugaItem->title			= $juga_title;
						$newJugaItem->site_option	= $option;
						$newJugaItem->site_section	= $section;
						$newJugaItem->site_view		= $view;
						$newJugaItem->site_task		= $task;
						$newJugaItem->type			= $juga_type;
						$newJugaItem->type_id		= $id;
						$newJugaItem->store();
						
						// add item to default_group 
						// which varies depending on whether we're the Admin side or Not
						// and only if default_group > 0
						if ($default_group > 0) {
							$query = "INSERT INTO #__juga_g2i "
								."\n SET #__juga_g2i.item_id = '".$newJugaItem->id."', "
								."\n #__juga_g2i.group_id = '".$default_group."'";
							$database->setQuery($query);
							$database->query();
						}				

						
						// check public_access for item
						$query = "SELECT * "
						. " FROM #__juga_g2i "
						. " LEFT JOIN #__juga_items ON #__juga_g2i.item_id = #__juga_items.id "
						. " WHERE #__juga_g2i.group_id = '$default_juga' "
						. " AND #__juga_items.site_option = '$option' "
						. " AND #__juga_items.site_section = '$section' "
						. " AND #__juga_items.site_view = '$view' "
						. " AND #__juga_items.site_task = '$task' "
						. " AND #__juga_items.type_id = '$id' "
						;
						$database->setQuery($query);
						$public_access = $database->loadObjectList();
						
						// if the item is public access/default_juga, then allow access
						if ($public_access) { $return["access"] = true; }						

					} // end if no juga_item   
					
					// return the error_url
			
					// if error_url_published, redirect there, else go to homepage
					if ( ($juga_item->error_url_published == '1') && ($juga_item->error_url) ) {
						// redirect to custom error URL
						// mosRedirect( $juga_item[0]->error_url );
						// exit();
						$return["error_url_published"] = $juga_item->error_url_published;
						$return["error_url"] = $juga_item->error_url;
						
					} else {
						// redirect to the home page w/ a notice saying ERROR: Unauthorized Access.
						// mosRedirect( $mosConfig_live_site, "ERROR: Unauthorized Access." );
						// exit();
						$return["error_url_published"] = $juga_item->error_url_published;
						$return["error_url"] = $juga_item->error_url;
					}
				} // end if no access
			} // end if no access to section/category
		} // end if not an excluded component
	} // end if no public access

	return $return;
} // end jugaRightsCheck
// ************************************************************************

/**
* JUGA DHTML Admnistrator Menus
*/
// ************************************************************************
class jugaMenu {
	/**
	* Show the menu
	* @param string The current user type
	*/
	// ************************************************************************
	function show( $user ) {
		global $acl, $database;
		global $mosConfig_live_site, $mosConfig_enable_stats, $mosConfig_caching;

		// **********************************************************************
		// check user's rights to be on selected page with option, section, task
		$details["user"] = $user; $details["section"] = ""; $details["task"] = ""; 
		
			$details["option"] = "com_admin"; $details["title"] = "Admin Home"; $access = jugaRightsCheck( $details );
		$canConfig 			= $access["access"];
			$details["option"] = "com_config"; $details["title"] = "Global Configuration"; $access = jugaRightsCheck( $details );
		$canConfig 			= $access["access"];
			$details["option"] = "com_templates"; $details["title"] = "Template Manager"; $access = jugaRightsCheck( $details );
		$manageTemplates 	= $access["access"];
			$details["option"] = "com_trash"; $details["title"] = "Trash Manager"; $access = jugaRightsCheck( $details );
		$manageTrash 		= $access["access"];
			$details["option"] = "com_menumanager"; $details["title"] = "Menu Manager"; $access = jugaRightsCheck( $details );
		$manageMenuMan 		= $access["access"];
			$details["option"] = "com_languages"; $details["title"] = "Language Manager"; $access = jugaRightsCheck( $details );
		$manageLanguages 	= $access["access"];
			$details["option"] = "com_massmail"; $details["title"] = "Mass Mail"; $access = jugaRightsCheck( $details );
		$canMassMail 		= $access["access"];
			$details["option"] = "com_users"; $details["title"] = "User Manager"; $access = jugaRightsCheck( $details );
		$canManageUsers 	= $access["access"];
			$details["option"] = "com_installer"; $details["title"] = "Installers"; $access = jugaRightsCheck( $details );
		$installModules 	= $access["access"];
		$installMambots 	= $access["access"];
		$installComponents 	= $access["access"];
			$details["option"] = "com_modules"; $details["title"] = "Modules"; $access = jugaRightsCheck( $details );
		$editAllModules 	= $access["access"];
			$details["option"] = "com_mambots"; $details["title"] = "Mambots"; $access = jugaRightsCheck( $details );
		$editAllMambots 	= $access["access"];
		$editAllComponents 	= $acl->acl_check( 'administration', 'edit', 'users', $user->usertype, 'components', 'all' );
		unset($access); unset($details["option"]); unset($details["title"]);

		// **********************************************************************
	

		$query = "SELECT a.id, a.title, a.name"
		. "\n FROM #__sections AS a"
		. "\n WHERE a.scope = 'content'"
		. "\n GROUP BY a.id"
		. "\n ORDER BY a.ordering"
		;
		$database->setQuery( $query );
		$sections = $database->loadObjectList();

		$menuTypes = mosAdminMenus::menutypes();
		?>
		<div id="myMenuID"></div>
		<script language="JavaScript" type="text/javascript">
		var myMenu =
		[
		<?php
	// Home Sub-Menu
?>			[null,'Home','index2.php',null,'Control Panel'],
			_cmSplit,
			<?php
	// Site Sub-Menu
?>			[null,'Site',null,null,'Site Management',
<?php
			if ($canConfig) {
?>				['<img src="../includes/js/ThemeOffice/config.png" />','Global Configuration','index2.php?option=com_config&hidemainmenu=1',null,'Configuration'],
<?php
			}
			if ($manageLanguages) {
?>				['<img src="../includes/js/ThemeOffice/language.png" />','Language Manager',null,null,'Manage languages',
					['<img src="../includes/js/ThemeOffice/language.png" />','Site Languages','index2.php?option=com_languages',null,'Manage Languages'],
				],
<?php
			}
?>				['<img src="../includes/js/ThemeOffice/media.png" />','Media Manager','index2.php?option=com_media',null,'Manage Media Files'],
					['<img src="../includes/js/ThemeOffice/preview.png" />', 'Preview', null, null, 'Preview',
					['<img src="../includes/js/ThemeOffice/preview.png" />','In New Window','<?php echo $mosConfig_live_site; ?>/index.php','_blank','<?php echo $mosConfig_live_site; ?>'],
					['<img src="../includes/js/ThemeOffice/preview.png" />','Inline','index2.php?option=com_admin&task=preview',null,'<?php echo $mosConfig_live_site; ?>'],
					['<img src="../includes/js/ThemeOffice/preview.png" />','Inline with Positions','index2.php?option=com_admin&task=preview2',null,'<?php echo $mosConfig_live_site; ?>'],
				],
				['<img src="../includes/js/ThemeOffice/globe1.png" />', 'Statistics', null, null, 'Site Statistics',
<?php
			if ($mosConfig_enable_stats == 1) {
?>					['<img src="../includes/js/ThemeOffice/globe4.png" />', 'Browser, OS, Domain', 'index2.php?option=com_statistics', null, 'Browser, OS, Domain'],
<?php
			}
?>					['<img src="../includes/js/ThemeOffice/search_text.png" />', 'Search Text', 'index2.php?option=com_statistics&task=searches', null, 'Search Text']
				],
<?php
			if ($manageTemplates) {
?>				['<img src="../includes/js/ThemeOffice/template.png" />','Template Manager',null,null,'Change site template',
					['<img src="../includes/js/ThemeOffice/template.png" />','Site Templates','index2.php?option=com_templates',null,'Change site template'],
					_cmSplit,
					['<img src="../includes/js/ThemeOffice/template.png" />','Administrator Templates','index2.php?option=com_templates&client=admin',null,'Change admin template'],
					_cmSplit,
					['<img src="../includes/js/ThemeOffice/template.png" />','Module Positions','index2.php?option=com_templates&task=positions',null,'Template positions']
				],
<?php
			}
			if ($manageTrash) {
?>				['<img src="../includes/js/ThemeOffice/trash.png" />','Trash Manager','index2.php?option=com_trash',null,'Manage Trash'],
<?php
			}
			if ($canManageUsers || $canMassMail) {
?>				['<img src="../includes/js/ThemeOffice/users.png" />','User Manager','index2.php?option=com_users&task=view',null,'Manage users'],
<?php
				}
?>			],
<?php
	// Menu Sub-Menu
?>			_cmSplit,
			[null,'Menu',null,null,'Menu Management',
<?php
			if ($manageMenuMan) {
?>				['<img src="../includes/js/ThemeOffice/menus.png" />','Menu Manager','index2.php?option=com_menumanager',null,'Menu Manager'],
				_cmSplit,
<?php
			}
			foreach ( $menuTypes as $menuType ) {
?>				['<img src="../includes/js/ThemeOffice/menus.png" />','<?php echo $menuType;?>','index2.php?option=com_menus&menutype=<?php echo $menuType;?>',null,''],
<?php
			}
?>			],
			_cmSplit,
<?php
	// Content Sub-Menu
?>			[null,'Content',null,null,'Content Management',
<?php
			if (count($sections) > 0) {
?>				['<img src="../includes/js/ThemeOffice/edit.png" />','Content by Section',null,null,'Content Managers',
<?php
				foreach ($sections as $section) {
					$txt = addslashes( $section->title ? $section->title : $section->name );
?>					['<img src="../includes/js/ThemeOffice/document.png" />','<?php echo $txt;?>', null, null,'<?php echo $txt;?>',
						['<img src="../includes/js/ThemeOffice/edit.png" />', '<?php echo $txt;?> Items', 'index2.php?option=com_content&sectionid=<?php echo $section->id;?>',null,null],
						['<img src="../includes/js/ThemeOffice/backup.png" />', '<?php echo $txt;?> Archives','index2.php?option=com_content&task=showarchive&sectionid=<?php echo $section->id;?>',null,null],
						['<img src="../includes/js/ThemeOffice/add_section.png" />', '<?php echo $txt;?> Categories', 'index2.php?option=com_categories&section=<?php echo $section->id;?>',null, null],
					],
<?php
				} // foreach
?>				],
				_cmSplit,
<?php
			}
?>
				['<img src="../includes/js/ThemeOffice/edit.png" />','All Content Items','index2.php?option=com_content&sectionid=0',null,'Manage Content Items'],
				['<img src="../includes/js/ThemeOffice/edit.png" />','Static Content Manager','index2.php?option=com_typedcontent',null,'Manage Typed Content Items'],
				_cmSplit,
				['<img src="../includes/js/ThemeOffice/add_section.png" />','Section Manager','index2.php?option=com_sections&scope=content',null,'Manage Content Sections'],
				['<img src="../includes/js/ThemeOffice/add_section.png" />','Category Manager','index2.php?option=com_categories&section=content',null,'Manage Content Categories'],
				_cmSplit,
				['<img src="../includes/js/ThemeOffice/home.png" />','Front Page Manager','index2.php?option=com_frontpage',null,'Manage Front Page Items'],
				['<img src="../includes/js/ThemeOffice/edit.png" />','Archive Manager','index2.php?option=com_content&task=showarchive&sectionid=0',null,'Manage Archive Items'],
				['<img src="../includes/js/ThemeOffice/globe3.png" />', 'Page Impressions', 'index2.php?option=com_statistics&task=pageimp', null, 'Page Impressions'],
			],
<?php
	// Components Sub-Menu
	if ($editAllComponents) {
?>			_cmSplit,
			[null,'Components',null,null,'Component Management',
<?php
		$query = "SELECT *"
		. "\n FROM #__components"
		. "\n WHERE name != 'frontpage'"
		. "\n AND name != 'media manager'"
		. "\n ORDER BY ordering, name"
		;
		$database->setQuery( $query );
		$comps = $database->loadObjectList();	// component list
		$subs = array();	// sub menus
		// first pass to collect sub-menu items
		foreach ($comps as $row) {
			if ($row->parent) {
				if (!array_key_exists( $row->parent, $subs )) {
					$subs[$row->parent] = array();
				}
				$subs[$row->parent][] = $row;
			}
		}
		$topLevelLimit = 19; //You can get 19 top levels on a 800x600 Resolution
		$topLevelCount = 0;
		foreach ($comps as $row) {
			// insert jugacheck here for specific component
			if ($row->option) { $details["option"] = $row->option; $details["title"] = $row->name; $access = jugaRightsCheck( $details ); }
			if ($access["access"]) {
				if ($row->parent == 0 && (trim( $row->admin_menu_link ) || array_key_exists( $row->id, $subs ))) {
					$topLevelCount++;
					if ($topLevelCount > $topLevelLimit) {
						continue;
					}
					$name = addslashes( $row->name );
					$alt = addslashes( $row->admin_menu_alt );
					$link = $row->admin_menu_link ? "'index2.php?$row->admin_menu_link'" : "null";
					echo "\t\t\t\t['<img src=\"../includes/$row->admin_menu_img\" />','$name',$link,null,'$alt'";
					if (array_key_exists( $row->id, $subs )) {
						foreach ($subs[$row->id] as $sub) {
							echo ",\n";
							$name = addslashes( $sub->name );
							$alt = addslashes( $sub->admin_menu_alt );
							$link = $sub->admin_menu_link ? "'index2.php?$sub->admin_menu_link'" : "null";
							echo "\t\t\t\t\t['<img src=\"../includes/$sub->admin_menu_img\" />','$name',$link,null,'$alt']";
						}
					}
					echo "\n\t\t\t\t],\n";
				}
			}
		}
		if ($topLevelLimit < $topLevelCount) {
			echo "\t\t\t\t['<img src=\"../includes/js/ThemeOffice/sections.png\" />','More Components...','index2.php?option=com_admin&task=listcomponents',null,'More Components'],\n";
		}
?>
			],
<?php
	// Modules Sub-Menu
		if ($installModules | $editAllModules) {
?>			_cmSplit,
			[null,'Modules',null,null,'Module Management',
<?php
			if ($editAllModules) {
?>				['<img src="../includes/js/ThemeOffice/module.png" />', 'Site Modules', "index2.php?option=com_modules", null, 'Manage Site modules'],
				['<img src="../includes/js/ThemeOffice/module.png" />', 'Administrator Modules', "index2.php?option=com_modules&client=admin", null, 'Manage Administrator modules'],
<?php
			}
?>			],
<?php
		} // if ($installModules | $editAllModules)
	} // if $installComponents
	// Mambots Sub-Menu
	if ($installMambots | $editAllMambots) {
?>			_cmSplit,
			[null,'Mambots',null,null,'Mambot Management',
<?php
		if ($editAllMambots) {
?>				['<img src="../includes/js/ThemeOffice/module.png" />', 'Site Mambots', "index2.php?option=com_mambots", null, 'Manage Site Mambots'],
<?php
		}
?>			],
<?php
	}
?>
<?php
	// Installer Sub-Menu
	if ($installModules) {
?>			_cmSplit,
			[null,'Installers',null,null,'Installer List',
<?php
		if ($manageTemplates) {
?>				['<img src="../includes/js/ThemeOffice/install.png" />','Templates - Site','index2.php?option=com_installer&element=template&client=',null,'Install Site Templates'],
				['<img src="../includes/js/ThemeOffice/install.png" />','Templates - Admin','index2.php?option=com_installer&element=template&client=admin',null,'Install Administrator Templates'],
<?php
		}
		if ($manageLanguages) {
?>				['<img src="../includes/js/ThemeOffice/install.png" />','Languages','index2.php?option=com_installer&element=language',null,'Install Languages'],
				_cmSplit,
<?php
		}
?>				['<img src="../includes/js/ThemeOffice/install.png" />', 'Components','index2.php?option=com_installer&element=component',null,'Install/Uninstall Components'],
				['<img src="../includes/js/ThemeOffice/install.png" />', 'Modules', 'index2.php?option=com_installer&element=module', null, 'Install/Uninstall Modules'],
				['<img src="../includes/js/ThemeOffice/install.png" />', 'Mambots', 'index2.php?option=com_installer&element=mambot', null, 'Install/Uninstall Mambots'],
			],
<?php
	} // if ($installModules)
	// Messages Sub-Menu
	if ($canConfig) {
?>			_cmSplit,
			[null,'Messages',null,null,'Messaging Management',
				['<img src="../includes/js/ThemeOffice/messaging_inbox.png" />','Inbox','index2.php?option=com_messages',null,'Private Messages'],
				['<img src="../includes/js/ThemeOffice/messaging_config.png" />','Configuration','index2.php?option=com_messages&task=config&hidemainmenu=1',null,'Configuration']
			],
<?php
	// System Sub-Menu
	/*
?>			_cmSplit,
			[null,'System',null,null,'System Management',
				['<img src="../includes/js/ThemeOffice/joomla_16x16.png" />', 'Version Check', 'index2.php?option=com_admin&task=versioncheck', null,'Version Check'],
				['<img src="../includes/js/ThemeOffice/sysinfo.png" />', 'System Info', 'index2.php?option=com_admin&task=sysinfo', null,'System Information'],
<?php
	*/
?>			_cmSplit,
			[null,'System',null,null,'System Management',
				['<img src="../includes/js/ThemeOffice/joomla_16x16.png" />', 'Version Check', 'http://www.joomla.org/latest10', '_blank','Version Check'],
				['<img src="../includes/js/ThemeOffice/sysinfo.png" />', 'System Info', 'index2.php?option=com_admin&task=sysinfo', null,'System Information'],
<?php
		if ($canConfig) {
?>
				['<img src="../includes/js/ThemeOffice/checkin.png" />', 'Global Checkin', 'index2.php?option=com_checkin', null,'Check-in all checked-out items'],
<?php
			if ($mosConfig_caching) {
?>				['<img src="../includes/js/ThemeOffice/config.png" />','Clean Content Cache','index2.php?option=com_admin&task=clean_cache',null,'Clean the content items cache'],
				['<img src="../includes/js/ThemeOffice/config.png" />','Clean All Caches','index2.php?option=com_admin&task=clean_all_cache',null,'Clean all caches'],
<?php
			}
		}
?>			],
<?php
			}
?>			_cmSplit,
<?php
	// Help Sub-Menu
?>			[null,'Help','index2.php?option=com_admin&task=help',null,null]
		];
		cmDraw ('myMenuID', myMenu, 'hbr', cmThemeOffice, 'ThemeOffice');
		</script>
<?php
	} // end show
	// ************************************************************************


	/**
	* Show an disbaled version of the menu, used in edit pages
	* @param string The current user type
	*/
	// ************************************************************************
	function showDisabled( $user ) {
		global $acl;

		// **********************************************************************
		// check user's rights to be on selected page with option, section, task
		$details["user"] = $user; $details["section"] = ""; $details["task"] = ""; 
		
			$details["option"] = "com_admin"; $details["title"] = "Admin Home"; $access = jugaRightsCheck( $details );
		$canConfig 			= $access["access"];
			$details["option"] = "com_config"; $details["title"] = "Global Configuration"; $access = jugaRightsCheck( $details );
		$canConfig 			= $access["access"];
			$details["option"] = "com_templates"; $details["title"] = "Template Manager"; $access = jugaRightsCheck( $details );
		$manageTemplates 	= $access["access"];
			$details["option"] = "com_trash"; $details["title"] = "Trash Manager"; $access = jugaRightsCheck( $details );
		$manageTrash 		= $access["access"];
			$details["option"] = "com_menumanager"; $details["title"] = "Menu Manager"; $access = jugaRightsCheck( $details );
		$manageMenuMan 		= $access["access"];
			$details["option"] = "com_languages"; $details["title"] = "Language Manager"; $access = jugaRightsCheck( $details );
		$manageLanguages 	= $access["access"];
			$details["option"] = "com_massmail"; $details["title"] = "Mass Mail"; $access = jugaRightsCheck( $details );
		$canMassMail 		= $access["access"];
			$details["option"] = "com_users"; $details["title"] = "User Manager"; $access = jugaRightsCheck( $details );
		$canManageUsers 	= $access["access"];
			$details["option"] = "com_installer"; $details["title"] = "Installers"; $access = jugaRightsCheck( $details );
		$installModules 	= $access["access"];
		$installMambots 	= $access["access"];
		$installComponents 	= $access["access"];
			$details["option"] = "com_modules"; $details["title"] = "Modules"; $access = jugaRightsCheck( $details );
		$editAllModules 	= $access["access"];
			$details["option"] = "com_mambots"; $details["title"] = "Mambots"; $access = jugaRightsCheck( $details );
		$editAllMambots 	= $access["access"];
		$editAllComponents 	= $acl->acl_check( 'administration', 'edit', 'users', $user->usertype, 'components', 'all' );
		unset($access);

		// **********************************************************************

		
		$text = 'Menu inactive for this Page';
		?>
		<div id="myMenuID" class="inactive"></div>
		<script language="JavaScript" type="text/javascript">
		var myMenu =
		[
		<?php
	/* Home Sub-Menu */
		?>
			[null,'<?php echo 'Home'; ?>',null,null,'<?php echo $text; ?>'],
			_cmSplit,
		<?php
	/* Site Sub-Menu */
		?>
			[null,'<?php echo 'Site'; ?>',null,null,'<?php echo $text; ?>'
			],
		<?php
	/* Menu Sub-Menu */
		?>
			_cmSplit,
			[null,'<?php echo 'Menu'; ?>',null,null,'<?php echo $text; ?>'
			],
			_cmSplit,
		<?php
	/* Content Sub-Menu */
		?>
			[null,'<?php echo 'Content'; ?>',null,null,'<?php echo $text; ?>'
			],
		<?php
	/* Components Sub-Menu */
			if ( $installComponents) {
				?>
				_cmSplit,
				[null,'<?php echo 'Components'; ?>',null,null,'<?php echo $text; ?>'
				],
				<?php
			} // if $installComponents
			?>
		<?php
	/* Modules Sub-Menu */
			if ( $installModules | $editAllModules) {
				?>
				_cmSplit,
				[null,'<?php echo 'Modules'; ?>',null,null,'<?php echo $text; ?>'
				],
				<?php
			} // if ( $installModules | $editAllModules)
			?>
		<?php
	/* Mambots Sub-Menu */
			if ( $installMambots | $editAllMambots) {
				?>
				_cmSplit,
				[null,'<?php echo 'Mambots'; ?>',null,null,'<?php echo $text; ?>'
				],
				<?php
			} // if ( $installMambots | $editAllMambots)
			?>


			<?php
	/* Installer Sub-Menu */
			if ( $installModules) {
				?>
				_cmSplit,
				[null,'<?php echo 'Installers'; ?>',null,null,'<?php echo $text; ?>'
					<?php
					?>
				],
				<?php
			} // if ( $installModules)
			?>
			<?php
	/* Messages Sub-Menu */
			if ( $canConfig) {
				?>
				_cmSplit,
				[null,'<?php echo 'Messages'; ?>',null,null,'<?php echo $text; ?>'
				],
				<?php
			}
			?>

			<?php
	/* System Sub-Menu */
			if ( $canConfig) {
				?>
				_cmSplit,
				[null,'<?php echo 'System'; ?>',null,null,'<?php echo $text; ?>'
				],
				<?php
			}
			?>
			_cmSplit,
			<?php
	/* Help Sub-Menu */
			?>
			[null,'<?php echo 'Help'; ?>',null,null,'<?php echo $text; ?>']
		];
		cmDraw ('myMenuID', myMenu, 'hbr', cmThemeOffice, 'ThemeOffice');
		</script>
		<?php
	}// end showDisabled  
	// ************************************************************************
	
} // end class jugaMenu
// ************************************************************************

/**
* Add to enable compatibility with J!1.0.10!
* CC
*/
// ************************************************************************
if (!function_exists('josGetArrayInts')) {
    /*
    * Function to handle an array of integers
    * Added 1.0.11
    */
    function josGetArrayInts( $name, $type=NULL ) {
        if ( $type == NULL ) {
            $type = $_POST;
        }
    
        $array = mosGetParam( $type, $name, array(0) );
    
        mosArrayToInts( $array );
    
        if (!is_array( $array )) {
            $array = array(0);
        }
    
        return $array;
    }
}
// ************************************************************************
// ************************************************************************
class jugaGroup extends mosDBTable {
	/** @var int Primary key */
	var $id					= null;
	/** @var string */
	var $title				= null;
	/** @var string */
	var $description		= null;
	/** @var boolean */
	var $checked_out		= null;
	/** @var time */
	var $checked_out_time	= null;

	function jugaGroup( &$db ) {
		$this->mosDBTable( '#__juga_groups', 'id', $db );
	}
	/** overloaded check function */
	function check() {
		// filter malicious code
		$ignoreList = array( 'params' );
		$this->filter( $ignoreList );

		// specific filters
		$iFilter = new JFilterInput();

		/** check for valid name */
		if (trim( $this->title ) == '') {
			$this->_error = _juga_title_blank;
			return false;
		}

		/** check for existing name */
		$query = "SELECT id"
		. "\n FROM #__juga_groups "
		. "\n WHERE title = " . $this->_db->Quote( $this->title )
		;
		$this->_db->setQuery( $query );

		$xid = intval( $this->_db->loadResult() );
		if ($xid && $xid != intval( $this->id )) {
			$this->_error = _juga_title_exist;
			return false;
		}
		return true;
	}	
} // end class jugaGroup
// ************************************************************************
// ************************************************************************
class jugaItem extends mosDBTable {
	/** @var int Primary key */
	var $id					= null;
	/** @var string */
	var $title				= null;
	/** @var string */
	var $site_option		= null;
	/** @var string */
	var $site_section		= null;
	/** @var string */
	var $site_view			= null;
	/** @var string */
	var $site_task			= null;
	/** @var string */
	var $type				= null;
	/** @var int */
	var $type_id			= null;
	/** @var int */
	var $error_url_published	= null;
	/** @var string */
	var $error_url				= null;
	/** @var boolean */
	var $checked_out		= null;
	/** @var time */
	var $checked_out_time	= null;
	var $option_exclude		= null;

	function jugaItem( &$db ) {
		$this->mosDBTable( '#__juga_items', 'id', $db );
	}
	/** overloaded check function */
	function check() {
		// filter malicious code
		$ignoreList = array( 'params' );
		$this->filter( $ignoreList );

		// specific filters
		$iFilter = new JFilterInput();
		return true;
	}	
} // end class jugaItem
// ************************************************************************
// ************************************************************************
class jugaConfig extends mosDBTable {
	/** @var int Primary key */
	var $id					= null;
	/** @var string */
	var $title				= null;
	/** @var string */
	var $description		= null;
	/** @var string */
	var $value				= null;
	/** @var boolean */
	var $checked_out		= null;
	/** @var time */
	var $checked_out_time	= null;

	function jugaConfig( &$db ) {
		$this->mosDBTable( '#__juga', 'id', $db );
	}
	/** overloaded check function */
	function check() {
		// filter malicious code
		$ignoreList = array( 'params' );
		$this->filter( $ignoreList );

		// specific filters
		$iFilter = new JFilterInput();

		/** check for valid name */
		if (trim( $this->title ) == '') {
			$this->_error = _juga_title_blank;
			return false;
		}

		/** check for existing name */
		$query = "SELECT id"
		. "\n FROM #__juga "
		. "\n WHERE title = " . $this->_db->Quote( $this->title )
		;
		$this->_db->setQuery( $query );

		$xid = intval( $this->_db->loadResult() );
		if ($xid && $xid != intval( $this->id )) {
			$this->_error = _juga_title_exist;
			return false;
		}
		return true;
	}	
} // end class jugaConfig
// ************************************************************************
// ************************************************************************
class jugaCode extends mosDBTable {
	/** @var int Primary key */
	var $id 			= null; 
	/** @var string Unique key */
	var $title 			= null; 
	/** @var string */
	var $description	= null;
	/** @var int */
	var $group_id		= null; 
	/** @var int */
	var $published 		= null; 
	/** @var datetime */
	var $publish_up 	= null; 
	/** @var datetime */
	var $publish_down 	= null; 
	/** @var int */
  	var $times_allowed	= null; 
	/** @var int */
  	var $hits 			= null;
	/** @var boolean */
	var $checked_out		= null;
	/** @var time */
	var $checked_out_time	= null;
	
	function jugaCode( &$db ) {
		$this->mosDBTable( '#__juga_codes', 'id', $db );
	}
	/** overloaded check function */
	function check() {
		// filter malicious code
		$ignoreList = array( 'params' );
		$this->filter( $ignoreList );

		// specific filters
		$iFilter = new JFilterInput();

		/** check for valid name */
		if (trim( $this->title ) == '') {
			$this->_error = _juga_title_blank;
			return false;
		}

		/** check for existing name */
		$query = "SELECT id"
		. "\n FROM #__juga_codes "
		. "\n WHERE title = " . $this->_db->Quote( $this->title )
		;
		$this->_db->setQuery( $query );

		$xid = intval( $this->_db->loadResult() );
		if ($xid && $xid != intval( $this->id )) {
			$this->_error = _juga_title_exist;
			return false;
		}
		return true;
	}	
} // end class jugaCode
// ************************************************************************
// ************************************************************************
