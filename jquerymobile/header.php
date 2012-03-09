<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
	<title>jQuery Mobile</title>   
	<link rel="stylesheet" href="http://www.tapdestin.com/jquerymobile/css/style.css" />  
	<script type="text/javascript" src="http://code.jquery.com/jquery-1.4.3.min.js"></script>
	<script type="text/javascript" src="http://code.jquery.com/mobile/1.0a1/jquery.mobile-1.0a1.min.js"></script>
</head>
<body>
<div data-role="page" data-theme="b">
	<div data-role="header" data-theme="b">
		<h1 style="text-align:center">
			<?php
				if(isset($_GET['id']))
					echo "&nbsp;";
				else
					echo "Your Logo";/*echo '<img src="../images/logo/logo.png"/>';*/
			?>
		</h1>
	</div>
	<div data-role="content"> 