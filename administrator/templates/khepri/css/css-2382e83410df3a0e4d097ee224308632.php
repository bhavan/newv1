<?php 
ob_start ("ob_gzhandler");
header("Content-type: text/css; charset: UTF-8");
header("Cache-Control: must-revalidate");
$offset = 60 * 60 ;
$ExpStr = "Expires: " . 
gmdate("D, d M Y H:i:s",
time() + $offset) . " GMT";
header($ExpStr);
                ?>

/*** icon.css ***/

/** menu icons **/
.icon-16-archive 		{ background-image: url(../images/menu/icon-16-archive.png); }
.icon-16-article 		{ background-image: url(../images/menu/icon-16-article.png); }
.icon-16-category 	{ background-image: url(../images/menu/icon-16-category.png); }
.icon-16-checkin 		{ background-image: url(../images/menu/icon-16-checkin.png); }
.icon-16-component	{ background-image: url(../images/menu/icon-16-component.png); }
.icon-16-config 		{ background-image: url(../images/menu/icon-16-config.png); }
.icon-16-content 		{ background-image: url(../images/menu/icon-16-content.png); }
.icon-16-cpanel 		{ background-image: url(../images/menu/icon-16-cpanel.png); }
.icon-16-default 		{ background-image: url(../images/menu/icon-16-default.png); }
.icon-16-frontpage 	{ background-image: url(../images/menu/icon-16-frontpage.png); }
.icon-16-help			{ background-image: url(../images/menu/icon-16-help.png); }
.icon-16-info 			{ background-image: url(../images/menu/icon-16-info.png); }
.icon-16-install 		{ background-image: url(../images/menu/icon-16-install.png);}
.icon-16-language 	{ background-image: url(../images/menu/icon-16-language.png);}
.icon-16-logout 		{ background-image: url(../images/menu/icon-16-logout.png);}
.icon-16-massmail 	{ background-image: url(../images/menu/icon-16-massmail.png); }
.icon-16-media 		{ background-image: url(../images/menu/icon-16-media.png);}
.icon-16-menu 			{ background-image: url(../images/menu/icon-16-menu.png); }
.icon-16-menumgr 		{ background-image: url(../images/menu/icon-16-menumgr.png); }
.icon-16-messages 	{ background-image: url(../images/menu/icon-16-messages.png); }
.icon-16-module 		{ background-image: url(../images/menu/icon-16-module.png); }
.icon-16-plugin 		{ background-image: url(../images/menu/icon-16-plugin.png); }
.icon-16-section 		{ background-image: url(../images/menu/icon-16-section.png); }
.icon-16-static 		{ background-image: url(../images/menu/icon-16-static.png); }
.icon-16-stats 		{ background-image: url(../images/menu/icon-16-stats.png); }
.icon-16-themes 		{ background-image: url(../images/menu/icon-16-themes.png); }
.icon-16-trash 		{ background-image: url(../images/menu/icon-16-trash.png); }
.icon-16-user 			{ background-image: url(../images/menu/icon-16-user.png); }


/** toolbar icons **/
.icon-32-send 			{ background-image: url(../images/toolbar/icon-32-send.png); }
.icon-32-delete 		{ background-image: url(../images/toolbar/icon-32-delete.png); }
.icon-32-help 			{ background-image: url(../images/toolbar/icon-32-help.png); }
.icon-32-cancel 		{ background-image: url(../images/toolbar/icon-32-cancel.png); }
.icon-32-config 		{ background-image: url(../images/toolbar/icon-32-config.png); }
.icon-32-apply 		{ background-image: url(../images/toolbar/icon-32-apply.png); }
.icon-32-back			{ background-image: url(../images/toolbar/icon-32-back.png); }
.icon-32-forward		{ background-image: url(../images/toolbar/icon-32-forward.png); }
.icon-32-save 			{ background-image: url(../images/toolbar/icon-32-save.png); }
.icon-32-edit 			{ background-image: url(../images/toolbar/icon-32-edit.png); }
.icon-32-copy 			{ background-image: url(../images/toolbar/icon-32-copy.png); }
.icon-32-move 			{ background-image: url(../images/toolbar/icon-32-move.png); }
.icon-32-new 			{ background-image: url(../images/toolbar/icon-32-new.png); }
.icon-32-upload 		{ background-image: url(../images/toolbar/icon-32-upload.png); }
.icon-32-assign 		{ background-image: url(../images/toolbar/icon-32-publish.png); }
.icon-32-html 			{ background-image: url(../images/toolbar/icon-32-html.png); }
.icon-32-css 			{ background-image: url(../images/toolbar/icon-32-css.png); }
.icon-32-menus 			{ background-image: url(../images/toolbar/icon-32-menu.png); }
.icon-32-publish 		{ background-image: url(../images/toolbar/icon-32-publish.png); }
.icon-32-unpublish 	{ background-image: url(../images/toolbar/icon-32-unpublish.png);}
.icon-32-restore		{ background-image: url(../images/toolbar/icon-32-revert.png); }
.icon-32-trash 		{ background-image: url(../images/toolbar/icon-32-trash.png); }
.icon-32-archive 		{ background-image: url(../images/toolbar/icon-32-archive.png); }
.icon-32-unarchive 	{ background-image: url(../images/toolbar/icon-32-unarchive.png); }
.icon-32-preview 		{ background-image: url(../images/toolbar/icon-32-preview.png); }
.icon-32-default 		{ background-image: url(../images/toolbar/icon-32-default.png); }

/** header icons **/
.icon-48-generic 		{ background-image: url(../images/header/icon-48-generic.png); }
.icon-48-checkin 		{ background-image: url(../images/header/icon-48-checkin.png); }
.icon-48-cpanel 		{ background-image: url(../images/header/icon-48-cpanel.png); }
.icon-48-config 		{ background-image: url(../images/header/icon-48-config.png); }
.icon-48-module 		{ background-image: url(../images/header/icon-48-module.png); }
.icon-48-menu 			{ background-image: url(../images/header/icon-48-menu.png); }
.icon-48-menumgr 		{ background-image: url(../images/header/icon-48-menumgr.png); }
.icon-48-trash 		{ background-image: url(../images/header/icon-48-trash.png); }
.icon-48-user	 		{ background-image: url(../images/header/icon-48-user.png); }
.icon-48-inbox 		{ background-image: url(../images/header/icon-48-inbox.png); }
.icon-48-msgconfig 	{ background-image: url(../images/header/icon-48-message_config.png); }
.icon-48-langmanager { background-image: url(../images/header/icon-48-language.png); }
.icon-48-mediamanager{ background-image: url(../images/header/icon-48-media.png); }
.icon-48-plugin 	{ background-image: url(../images/header/icon-48-plugin.png); }
.icon-48-help_header { background-image: url(../images/header/icon-48-help_header.png); }
.icon-48-impressions { background-image: url(../images/header/icon-48-stats.png); }
.icon-48-browser 		{ background-image: url(../images/header/icon-48-stats.png); }
.icon-48-searchtext 	{ background-image: url(../images/header/icon-48-stats.png); }
.icon-48-thememanager{ background-image: url(../images/header/icon-48-themes.png); }
.icon-48-massemail 	{ background-image: url(../images/header/icon-48-massemail.png); }
.icon-48-frontpage 	{ background-image: url(../images/header/icon-48-frontpage.png); }
.icon-48-sections 	{ background-image: url(../images/header/icon-48-section.png); }
.icon-48-addedit 		{ background-image: url(../images/header/icon-48-article-add.png); }
.icon-48-article 		{ background-image: url(../images/header/icon-48-article.png); }
.icon-48-categories 	{ background-image: url(../images/header/icon-48-category.png); }
.icon-48-install 		{ background-image: url(../images/header/icon-48-extension.png); }
.icon-48-dbbackup		{ background-image: url(../images/header/icon-48-backup.png); }
.icon-48-dbrestore 	{ background-image: url(../images/header/icon-48-dbrestore.png); }
.icon-48-dbquery 		{ background-image: url(../images/header/icon-48-query.png); }
.icon-48-systeminfo 	{ background-image: url(../images/header/icon-48-info.png); }
.icon-48-massemail 	{ background-image: url(../images/header/icon-48-massmail.png); }


/*** general.css ***/

/**
* @version $Id: general.css 10387 2008-06-03 10:59:16Z pasamio $
* @copyright Copyright (C) 2005 - 2006 Open Source Matters. All rights reserved.
* @license GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

/**
 * Joomla! 1.5 Admin template main css file
 *
 * @author		Andy Miller <andy.miller@joomla.org>
 * @package		Joomla
 * @since		1.5
 * @version    1.0
 */

/* -- General styles ------------------------------ */

body {
	margin: 10px; padding: 0;
	background: #fff;
	padding-bottom: 1px;

	font-size: 11px;
}

body, td, th { font-family: Arial, Helvetica, sans-serif; }

html, body { height: 95%; }

#minwidth { min-width: 960px; }

.clr { clear: both; overflow:hidden; height: 0; }

a, img { padding: 0; margin: 0; }

img { border: 0 none; }

form { margin: 0; padding: 0; }

h1 {
	margin: 0; padding-bottom: 8px;
	color: #0B55C4; font-size: 20px; font-weight: bold;
}

h3 {
	font-size: 13px;
}

a:link    { color: #0B55C4; text-decoration: none; }
a:visited { color: #0B55C4; text-decoration: none; }
a:hover   { text-decoration: underline; }

fieldset {
	margin-bottom: 10px;
	border: 1px #ccc solid;
	padding: 5px;
	text-align: left;
}

fieldset p {  margin: 10px 0px;  }

legend    {
	color: #0B55C4;
	font-size: 12px;
	font-weight: bold;
}

input, select { font-size: 10px;  border: 1px solid silver; }
textarea      { font-size: 11px;  border: 1px solid silver; }
button        { font-size: 10px;  }

input.disabled { background-color: #F0F0F0; }

input.button  { cursor: pointer;   }

input:focus,
select:focus,
textarea:focus { background-color: #ffd }

/* -- overall styles ------------------------------ */

#border-top.h_green          { background: url(../images/h_green/j_header_middle.png) repeat-x; }
#border-top.h_green div      { background: url(../images/h_green/j_header_right.png) 100% 0 no-repeat; }
#border-top.h_green div div  { background: url(../images/h_green/j_header_left.png) no-repeat; height: 54px; }

#border-top.h_teal          { background: url(../images/h_teal/j_header_middle.png) repeat-x; }
#border-top.h_teal div      { background: url(../images/h_teal/j_header_right.png) 100% 0 no-repeat; }
#border-top.h_teal div div  { background: url(../images/h_teal/j_header_left.png) no-repeat; height: 54px; }

#border-top.h_cherry          { background: url(../images/h_cherry/j_header_middle.png) repeat-x; }
#border-top.h_cherry div      { background: url(../images/h_cherry/j_header_right.png) 100% 0 no-repeat; }
#border-top.h_cherry div div  { background: url(../images/h_cherry/j_header_left.png) no-repeat; height: 54px; }

#border-top .title {
	font-size: 22px; font-weight: bold; color: #fff; line-height: 44px;
	padding-left: 180px;
}

#border-top .version {
	display: block; float: right;
	color: #fff;
	padding: 25px 5px 0 0;
}

#border-bottom 			{ background: url(../images/j_bottom.png) repeat-x; }
#border-bottom div  		{ background: url(../images/j_corner_br.png) 100% 0 no-repeat; }
#border-bottom div div 	{ background: url(../images/j_corner_bl.png) no-repeat; height: 11px; }

#footer .copyright { margin: 10px; text-align: center; }

#header-box  { border: 1px solid #ccc; background: #f0f0f0; }

#content-box {
	border-left: 1px solid #ccc;
	border-right: 1px solid #ccc;
}

#content-box .padding  { padding: 10px 10px 0 10px; }

#toolbar-box 			{ background: #fbfbfb; margin-bottom: 10px; }

#submenu-box { background: #f6f6f6; margin-bottom: 10px; }
#submenu-box .padding { padding: 0px;}


/* -- status layout */
#module-status      { float: right; }
#module-status span { display: block; float: left; line-height: 16px; padding: 4px 10px 0 22px; margin-bottom: 5px; }

#module-status { background: url(../images/mini_icon.png) 3px 5px no-repeat; }
.legacy-mode{ color: #c00;}
#module-status .preview 			  { background: url(../images/menu/icon-16-media.png) 3px 3px no-repeat; }
#module-status .unread-messages,
#module-status .no-unread-messages { background: url(../images/menu/icon-16-messages.png) 3px 3px no-repeat; }
#module-status .unread-messages a  { font-weight: bold; }
#module-status .loggedin-users     { background: url(../images/menu/icon-16-user.png) 3px 3px no-repeat; }
#module-status .logout             { background: url(../images/menu/icon-16-logout.png) 3px 3px no-repeat; }

/* -- various styles -- */
span.note {
	display: block;
	background: #ffd;
	padding: 5px;
	color: #666;
}

/** overlib **/

.ol-foreground {
	background-color: #ffe;
}

.ol-background {
	background-color: #6db03c;
}

.ol-textfont {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10px;
	color: #666;
}

.ol-captionfont {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 11px;
	color: #fff;
	font-weight: bold;
}
.ol-captionfont a {
	color: #0b5fc6;
	text-decoration: none;
}

.ol-closefont {}

/** toolbar **/

div.header {
	font-size: 22px; font-weight: bold; color: #0B55C4; line-height: 48px;
	padding-left: 55px;
	background-repeat: no-repeat;
	margin-left: 10px;
}

div.header span { color: #666; }

div.configuration {
	font-size: 14px; font-weight: bold; color: #0B55C4; line-height: 16px;
	padding-left: 30px;
	margin-left: 10px;
	background-image: url(../images/menu/icon-16-config.png);
	background-repeat: no-repeat;
}

div.toolbar { float: right; text-align: right; padding: 0; }

table.toolbar    			 { border-collapse: collapse; padding: 0; margin: 0;	 }
table.toolbar td 			 { padding: 1px 1px 1px 4px; text-align: center; color: #666; height: 48px; }
table.toolbar td.spacer  { width: 10px; }
table.toolbar td.divider { border-right: 1px solid #eee; width: 5px; }

table.toolbar span { float: none; width: 32px; height: 32px; margin: 0 auto; display: block; }

table.toolbar a {
   display: block; float: left;
	white-space: nowrap;
	border: 1px solid #fbfbfb;
	padding: 1px 5px;
	cursor: pointer;
}

table.toolbar a:hover {
	border-left: 1px solid #eee;
	border-top: 1px solid #eee;
	border-right: 1px solid #ccc;
	border-bottom: 1px solid #ccc;
	text-decoration: none;
	color: #0B55C4;
}

/** for massmail component **/
td#mm_pane			{ width: 90%; }
input#mm_subject    { width: 200px; }
textarea#mm_message { width: 100%; }

/* pane-sliders  */
.pane-sliders .title {
	margin: 0;
	padding: 2px;
	color: #666;
	cursor: pointer;
}

.pane-sliders .panel   { border: 1px solid #ccc; margin-bottom: 3px;}

.pane-sliders .panel h3 { background: #f6f6f6; color: #666}

.pane-sliders .content { background: #f6f6f6; }

.pane-sliders .adminlist     { border: 0 none; }
.pane-sliders .adminlist td  { border: 0 none; }

.jpane-toggler  span     { background: transparent url(../images/j_arrow.png) 5px 50% no-repeat; padding-left: 20px;}
.jpane-toggler-down span { background: transparent url(../images/j_arrow_down.png) 5px 50% no-repeat; padding-left: 20px;}

.jpane-toggler-down {  border-bottom: 1px solid #ccc; }

/* tabs */

dl.tabs {
	float: left;
	margin: 10px 0 -1px 0;
	z-index: 50;
}

dl.tabs dt {
	float: left;
	padding: 4px 10px;
	border-left: 1px solid #ccc;
	border-right: 1px solid #ccc;
	border-top: 1px solid #ccc;
	margin-left: 3px;
	background: #f0f0f0;
	color: #666;
}

dl.tabs dt.open {
	background: #F9F9F9;
	border-bottom: 1px solid #F9F9F9;
	z-index: 100;
	color: #000;
}

div.current {
	clear: both;
	border: 1px solid #ccc;
	padding: 10px 10px;
}

div.current dd {
	padding: 0;
	margin: 0;
}
/** cpanel settings **/

#cpanel div.icon {
	text-align: center;
	margin-right: 5px;
	float: left;
	margin-bottom: 5px;
}

#cpanel div.icon a {
	display: block;
	float: left;
	border: 1px solid #f0f0f0;
	height: 97px;
	width: 108px;
	color: #666;
	vertical-align: middle;
	text-decoration: none;
}

#cpanel div.icon a:hover {
	border-left: 1px solid #eee;
	border-top: 1px solid #eee;
	border-right: 1px solid #ccc;
	border-bottom: 1px solid #ccc;
	background: #f9f9f9;
	color: #0B55C4;
}

#cpanel img  { padding: 10px 0; margin: 0 auto; }
#cpanel span { display: block; text-align: center; }

/* standard form style table */
div.col { float: left; }
div.width-45 { width: 45%; }
div.width-55 { width: 55%; }
div.width-50 { width: 50%; }
div.width-70 { width: 70%; }
div.width-30 { width: 30%; }
div.width-60 { width: 60%; }
div.width-40 { width: 40%; }

table.admintable td 					 { padding: 3px; }
table.admintable td.key,
table.admintable td.paramlist_key {
	background-color: #f6f6f6;
	text-align: right;
	width: 140px;
	color: #666;
	font-weight: bold;
	border-bottom: 1px solid #e9e9e9;
	border-right: 1px solid #e9e9e9;
}

table.paramlist td.paramlist_description {
	background-color: #f6f6f6;
	text-align: left;
	width: 170px;
	color: #333;
	font-weight: normal;
	border-bottom: 1px solid #e9e9e9;
	border-right: 1px solid #e9e9e9;
}

table.admintable td.key.vtop { vertical-align: top; }

table.adminform {
	background-color: #f9f9f9;
	border: solid 1px #d5d5d5;
	width: 100%;
	border-collapse: collapse;
	margin: 8px 0 10px 0;
	margin-bottom: 15px;
	width: 100%;
}
table.adminform.nospace { margin-bottom: 0; }
table.adminform tr.row0 { background-color: #f9f9f9; }
table.adminform tr.row1 { background-color: #eeeeee; }

table.adminform th {
	font-size: 11px;
	padding: 6px 2px 4px 4px;
	text-align: left;
	height: 25px;
	color: #000;
	background-repeat: repeat;
}
table.adminform td { padding: 3px; text-align: left; }

table.adminform td.filter{
	text-align: left;
}

table.adminform td.helpMenu{
	text-align: right;
}


fieldset.adminform { border: 1px solid #ccc; margin: 0 10px 10px 10px; }

/** Table styles **/

table.adminlist {
	width: 100%;
	border-spacing: 1px;
	background-color: #e7e7e7;
	color: #666;
}

table.adminlist td,
table.adminlist th { padding: 4px; }

table.adminlist thead th {
	text-align: center;
	background: #f0f0f0;
	color: #666;
	border-bottom: 1px solid #999;
	border-left: 1px solid #fff;
}

table.adminlist thead a:hover { text-decoration: none; }

table.adminlist thead th img { vertical-align: middle; }

table.adminlist tbody th { font-weight: bold; }

table.adminlist tbody tr			{ background-color: #fff;  text-align: left; }
table.adminlist tbody tr.row1 	{ background: #f9f9f9; border-top: 1px solid #fff; }

table.adminlist tbody tr.row0:hover td,
table.adminlist tbody tr.row1:hover td  { background-color: #ffd ; }

table.adminlist tbody tr td 	   { height: 25px; background: #fff; border: 1px solid #fff; }
table.adminlist tbody tr.row1 td { background: #f9f9f9; border-top: 1px solid #FFF; }

table.adminlist tfoot tr { text-align: center;  color: #333; }
table.adminlist tfoot td,
table.adminlist tfoot th { background-color: #f3f3f3; border-top: 1px solid #999; text-align: center; }

table.adminlist td.order 		{ text-align: center; white-space: nowrap; }
table.adminlist td.order span { float: left; display: block; width: 20px; text-align: center; }

table.adminlist .pagination { display:table; padding:0;  margin:0 auto;	 }

.pagination div.limit {
	float: left;
	height: 22px;
	line-height: 22px;
	margin: 0 10px;
}

/** stu nicholls solution for centering divs **/
.container {clear:both; text-decoration:none;}
* html .container {display:inline-block;}

/** table solution for global config **/
table.noshow   		 { width: 100%; border-collapse: collapse; padding: 0; margin: 0; }
table.noshow tr 		 { vertical-align: top; }
table.noshow td 		 { }
table.noshow fieldset { margin: 15px 7px 7px 7px; }

#editor-xtd-buttons { padding: 5px; }

/* -- buttons -> STILL NEED CLEANUP*/

.button1,
.button1 div{
	height: 1%;
	float: right;
}

.button2-left,
.button2-right,
.button2-left div,
.button2-right div {
	float: left;
}

.button1 { background: url(../images/j_button1_left.png) no-repeat; white-space: nowrap; padding-left: 10px; margin-left: 5px;}

.button1 .next { background: url(../images/j_button1_next.png) 100% 0 no-repeat; }

.button1 a {
	display: block;
	height: 26px;
	float: left;
	line-height: 26px;
	font-size: 12px;
	font-weight: bold;
	color: #333;
	cursor: pointer;
	padding: 0 30px 0 6px;
}

.button1 a:hover { text-decoration: none; color: #0B55C4; }

.button2-left a,
.button2-right a,
.button2-left span,
.button2-right span {
	display: block;
	height: 22px;
	float: left;
	line-height: 22px;
	font-size: 11px;
	color: #333;
	cursor: pointer;
}

.button2-left span,
.button2-right span { cursor: default; color: #999; }

.button2-left .page a,
.button2-right .page a,
.button2-left .page span,
.button2-right .page span,
.button2-left .blank a,
.button2-right .blank a,
.button2-left .blank span,
.button2-right .blank span { padding: 0 6px; }

.page span,
.blank span {
	color: #000;
	font-weight: bold;
}

.button2-left a:hover,
.button2-right a:hover { text-decoration: none; color: #0B55C4; }

.button2-left a,
.button2-left span { padding: 0 24px 0 6px; }

.button2-right a,
.button2-right span { padding: 0 6px 0 24px; }

.button2-left { background: url(../images/j_button2_left.png) no-repeat; float: left; margin-left: 5px; }

.button2-right { background: url(../images/j_button2_right.png) 100% 0 no-repeat; float: left; margin-left: 5px; }

.button2-right .prev { background: url(../images/j_button2_prev.png) no-repeat; }

.button2-right.off .prev { background: url(../images/j_button2_prev_off.png) no-repeat; }

.button2-right .start { background: url(../images/j_button2_first.png) no-repeat; }

.button2-right.off .start { background: url(../images/j_button2_first_off.png) no-repeat; }

.button2-left .page,
.button2-left .blank { background: url(../images/j_button2_right_cap.png) 100% 0 no-repeat; }

.button2-left .next { background: url(../images/j_button2_next.png) 100% 0 no-repeat; }

.button2-left.off .next { background: url(../images/j_button2_next_off.png) 100% 0 no-repeat; }

.button2-left .end { background: url(../images/j_button2_last.png) 100% 0 no-repeat; }

.button2-left.off .end { background: url(../images/j_button2_last_off.png) 100% 0 no-repeat; }

.button2-left .image 		{ background: url(../images/j_button2_image.png) 100% 0 no-repeat; }
.button2-left .readmore 	{ background: url(../images/j_button2_readmore.png) 100% 0 no-repeat; }
.button2-left .pagebreak 	{ background: url(../images/j_button2_pagebreak.png) 100% 0 no-repeat; }
.button2-left .blank	 	{ background: url(../images/j_button2_blank.png) 100% 0 no-repeat; }

/* Tooltips */
.tool-tip {
	float: left;
	background: #ffc;
	border: 1px solid #D4D5AA;
	padding: 5px;
	max-width: 200px;
	z-index: 50;
}

.tool-title {
	padding: 0;
	margin: 0;
	font-size: 100%;
	font-weight: bold;
	margin-top: -15px;
	padding-top: 15px;
	padding-bottom: 5px;
	background: url(../images/selector-arrow.png) no-repeat;
}

.tool-text {
	font-size: 100%;
	margin: 0;
}

/* Calendar */
a img.calendar {
	width: 16px;
	height: 16px;
	margin-left: 3px;
	background: url(../images/calendar.png) no-repeat;
	cursor: pointer;
	vertical-align: middle;
}

/* System Standard Messages */
#system-message dd.message ul { background: #C3D2E5 url(../images/notice-info.png) 4px center no-repeat;}

/* System Error Messages */
#system-message dd.error ul { color: #c00; background: #E6C0C0 url(../images/notice-alert.png) 4px top no-repeat; border-top: 3px solid #DE7A7B; border-bottom: 3px solid #DE7A7B;}

/* System Notice Messages */
#system-message dd.notice ul { color: #c00; background: #EFE7B8 url(../images/notice-note.png) 4px top no-repeat; border-top: 3px solid #F0DC7E; border-bottom: 3px solid #F0DC7E;}