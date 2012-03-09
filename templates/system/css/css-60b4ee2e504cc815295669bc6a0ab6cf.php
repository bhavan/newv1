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

/*** system.css ***/

/* OpenID icon style */
input.system-openid, input.com-system-openid {
   background: url(http://openid.net/images/login-bg.gif) no-repeat;
   background-color: #fff;
   background-position: 0 50%;
   color: #000;
   padding-left: 18px;
}


/* Unpublished */
.system-unpublished {
background: #e8edf1;
border-top: 4px solid #c4d3df;
border-bottom: 4px solid #c4d3df;
}

/* System Messages */
#system-message    { margin-bottom: 10px; padding: 0;}
#system-message dt { font-weight: bold; }
#system-message dd { margin: 0; font-weight: bold; text-indent: 30px; }
#system-message dd ul { color: #0055BB; margin-bottom: 10px; list-style: none; padding: 10px; border-top: 3px solid #84A7DB; border-bottom: 3px solid #84A7DB;}

/* System Standard Messages */
#system-message dt.message { display: none; }
#system-message dd.message {  }

/* System Error Messages */
#system-message dt.error { display: none; }
#system-message dd.error ul { color: #c00; background-color: #E6C0C0; border-top: 3px solid #DE7A7B; border-bottom: 3px solid #DE7A7B;}

/* System Notice Messages */
#system-message dt.notice { display: none; }
#system-message dd.notice ul { color: #c00; background: #EFE7B8; border-top: 3px solid #F0DC7E; border-bottom: 3px solid #F0DC7E;}

/* Debug */
#system-debug     { color: #ccc; background-color: #fff; padding: 10px; margin: 10px; }
#system-debug div { font-size: 11px;}


/*** general.css ***/

/* Form validation */
.invalid { border-color: #ff0000; }
label.invalid { color: #ff0000; }

/* Buttons */
#editor-xtd-buttons {
	padding: 5px;
}

.button2-left,
.button2-right,
.button2-left div,
.button2-right div {
	float: left;
}

.button2-left a,
.button2-right a,
.button2-left span,
.button2-right span {
	display: block;
	height: 22px;
	float: left;
	line-height: 22px;
	font-size: 11px;
	color: #666;
	cursor: pointer;
}

.button2-left span,
.button2-right span {
	cursor: default;
	color: #999;
}

.button2-left .page a,
.button2-right .page a,
.button2-left .page span,
.button2-right .page span {
	padding: 0 6px;
}

.page span {
	color: #000;
	font-weight: bold;
}

.button2-left a:hover,
.button2-right a:hover {
	text-decoration: none;
	color: #0B55C4;
}

.button2-left a,
.button2-left span {
	padding: 0 24px 0 6px;
}

.button2-right a,
.button2-right span {
	padding: 0 6px 0 24px;
}

.button2-left {
	background: url(../images/j_button2_left.png) no-repeat;
	float: left;
	margin-left: 5px;
}

.button2-right {
	background: url(../images/j_button2_right.png) 100% 0 no-repeat;
	float: left;
	margin-left: 5px;
}

.button2-left .image {
	background: url(../images/j_button2_image.png) 100% 0 no-repeat;
}

.button2-left .readmore {
	background: url(../images/j_button2_readmore.png) 100% 0 no-repeat;
}

.button2-left .pagebreak {
	background: url(../images/j_button2_pagebreak.png) 100% 0 no-repeat;
}

.button2-left .blank {
	background: url(../images/j_button2_blank.png) 100% 0 no-repeat;
}

/* Tooltips */
div.tooltip {
	float: left;
	background: #ffc;
	border: 1px solid #D4D5AA;
	padding: 5px;
	max-width: 200px;
	z-index:13000;
}

div.tooltip h4 {
	padding: 0;
	margin: 0;
	font-size: 95%;
	font-weight: bold;
	margin-top: -15px;
	padding-top: 15px;
	padding-bottom: 5px;
	background: url(../images/selector-arrow.png) no-repeat;
}

div.tooltip p {
	font-size: 90%;
	margin: 0;
}

/* Caption fixes */
.img_caption.left {
	float: left;
	margin-right: 1em;
}

.img_caption.right {
	float: right;
	margin-left: 1em;
}

.img_caption.left p {
	clear: left;
	text-align: center;
}

.img_caption.right p {
	clear: right;
	text-align: center;
}

.img_caption  {
text-align: center!important;
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
