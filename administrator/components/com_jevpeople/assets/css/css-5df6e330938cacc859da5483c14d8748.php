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

/*** jevpeople.css ***/

/** header icons **/
div.header.icon-48-jevents {
	background-image: url(../images/logo.png);
	padding-left:265px!important;
	line-height:48px;
}

/* Front end admin styling */
table.toolbar td.button {
	background-image:none;
	background-color:transparent;
	border:0px;
}

input#search {
	float:none;
	height:16px;
	width:150px;
	margin-bottom:3px;
}

div.jevpersondetail {
	margin:1em;
}
