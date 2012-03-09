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

/*** calendar-jos.css ***/

/* The main calendar widget.  DIV containing a table. */

div.calendar {
  position: relative;
  z-index: 100;
  width: 226px;
}

.calendar, .calendar table {
  border: 1px solid #cccccc;
  font-size: 11px;
  color: #000;
  cursor: default;
  background: #efefef;
  font-family: arial,verdana,sans-serif;
}

/* Header part -- contains navigation buttons and day names. */

.calendar .button { /* "<<", "<", ">", ">>" buttons have this class */
  text-align: center;    /* They are the navigation buttons */
  padding: 2px;          /* Make the buttons seem like they're pressing */
}

.calendar thead .title { /* This holds the current "month, year" */
  font-weight: bold;      /* Pressing it will take you to the current date */
  text-align: center;
  background: #333333;
  color: #ffffff;
  padding: 2px;
}

.calendar thead .headrow { /* Row <TR> containing navigation buttons */
  background: #dedede;
  color: #000;
}

.calendar thead .name { /* Cells <TD> containing the day names */
  border-bottom: 1px solid #cccccc;
  padding: 2px;
  text-align: center;
  color: #000;
}

.calendar thead .weekend { /* How a weekend day name shows in header */
  color: #dedede;
}

.calendar thead .hilite { /* How do the buttons in header appear when hover */
  background: #bbbbbb;
  color: #000000;
  border: 1px solid #cccccc;
  padding: 1px;
}

.calendar thead .active { /* Active (pressed) buttons in header */
  background: #c77;
  padding: 2px 0px 0px 2px;
}

.calendar thead .daynames { /* Row <TR> containing the day names */
  background: #dddddd;
}

/* The body part -- contains all the days in month. */

.calendar tbody .day { /* Cells <TD> containing month days dates */
  width: 2em;
  text-align: right;
  padding: 2px 4px 2px 2px;
}

.calendar table .wn {
  padding: 2px 3px 2px 2px;
  border-right: 1px solid #cccccc;
  background: #dddddd;
}

.calendar tbody .rowhilite td {
  background: #666666;
  color: #ffffff;
}

.calendar tbody .rowhilite td.wn {
  background: #666666;
  color: #ffffff;
}

.calendar tbody td.hilite { /* Hovered cells <TD> */
  background: #999999;
  padding: 1px 3px 1px 1px;
  border: 1px solid #666666;
}

.calendar tbody td.active { /* Active (pressed) cells <TD> */
  background: #000000;
  color: #ffffff;
  padding: 2px 2px 0px 2px;
}

.calendar tbody td.selected { /* Cell showing today date */
  font-weight: bold;
  border: 1px solid #000;
  padding: 1px 3px 1px 1px;
  background: #000000;
  color: #ffffff;
}

.calendar tbody td.weekend { /* Cells showing weekend days */
  color: #cccccc;
}

.calendar tbody td.today { font-weight: bold; }

.calendar tbody .disabled { color: #999; }

.calendar tbody .emptycell { /* Empty cells (the best is to hide them) */
  visibility: hidden;
}

.calendar tbody .emptyrow { /* Empty row (some months need less than 6 rows) */
  display: none;
}

/* The footer part -- status bar and "Close" button */

.calendar tfoot .footrow { /* The <TR> in footer (only one right now) */
  text-align: center;
  background: #cccccc;
  color: #000;
}

.calendar tfoot .ttip { /* Tooltip (status bar) cell <TD> */
  border-top: 1px solid #cccccc;
  background: #efefef;
  color: #000000;
}

.calendar tfoot .hilite { /* Hover style for buttons in footer */
  background: #666666;
  border: 1px solid #f40;
  padding: 1px;
}

.calendar tfoot .active { /* Active (pressed) style for buttons in footer */
  background: #999999;
  padding: 2px 0px 0px 2px;
}

/* Combo boxes (menus that display months/years for direct selection) */

.combo {
  position: absolute;
  display: none;
  top: 0px;
  left: 0px;
  width: 4em;
  cursor: default;
  border: 1px solid #655;
  background: #ffffff;
  color: #000;
  font-size: smaller;
}

.combo .label {
  width: 100%;
  text-align: center;
}

.combo .hilite {
  background: #fc8;
}

.combo .active {
  border-top: 1px solid #cccccc;
  border-bottom: 1px solid #cccccc;
  background: #efefef;
  font-weight: bold;
}


/*** modal.css ***/

.body-overlayed embed, .body-overlayed object, .body-overlayed select
{
	visibility:				hidden;
}

#sbox-window embed, #sbox-window object, #sbox-window select
{
	visibility:				visible;
}

#sbox-overlay
{
	position:				absolute;
	background-color:		#000;
}

#sbox-window
{
	position:				absolute;
	background-color:		#000;
	text-align:				left;
	overflow:				visible;
	padding:				10px;
	-moz-border-radius:		3px;
}

* html #sbox-window
{
	top: 50% !important;
	left: 50% !important;
}

#sbox-btn-close
{
	position:				absolute;
	width:					30px;
	height:					30px;
	right:					-15px;
	top:					-15px;
	background:				url(../images/closebox.png) no-repeat top left;
	border:					none;
}

.sbox-loading #sbox-content
{
	background-image:		url(../images/spinner.gif);
	background-repeat:		no-repeat;
	background-position:	center;
}

#sbox-content
{
	clear:					both;
	overflow:				auto;
	background-color:		#fff;
	height:					100%;
	width:					100%;
}

.sbox-content-image#sbox-content
{
	overflow:				visible;
}

#sbox-image
{
	display:				block;
}

.sbox-content-image img
{
	display:				block;
}

.sbox-content-iframe#sbox-content
{
	overflow:				visible;
}