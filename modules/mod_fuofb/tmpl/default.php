<?php
// no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<div style="text-align: <?php echo $image_align ?>;">
<?php

if($popup_text != ''){
	$title = ' title="'.htmlspecialchars($popup_text).'"';
	$title_js = " title=\"".htmlspecialchars($popup_text)."\"";
}else{
	$title = '';
	$title_js = '';
}

switch ($target){
// cases are slightly different
	case 1:
		// open in a new window
		$output = '<a href="'. $url .'" target="_blank">'. $image .'</a>';
		break;
	case 2:
		// open in a popup window
		$output = "<a href=\"#\" onclick=\"javascript: window.open('". $url ."', '', 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=780,height=550'); return false\"".$title_js.">". $image ."</a>\n";
		break;
	default:
		// formerly case 2
		// open in parent window
		$output = '<a href="'. $url .'"'.$title.'>'. $image .'</a>';
		break;
}

echo $output;
?>
</div>