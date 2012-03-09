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

/*** poll_bars.css ***/

/* polls Bar Colors/Formats - Follow the format to add your own.
	Then update the maxcolors setting for polls in
	components/com_poll/poll.php -> $polls_maxcolors
*/

.polls_color_1{ background-color: #8D1B1B; border: 2px ridge #B22222; }

.polls_color_2{ background-color: #6740E1; border: 2px ridge #4169E1; }

.polls_color_3{ background-color: #8D8D8D; border: 2px ridge #D2D2D2; }

.polls_color_4{ background-color: #CC8500; border: 2px ridge #FFA500; }

.polls_color_5{ background-color: #5B781E; border: 2px ridge #6B8E23; }

