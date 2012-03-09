<?php
global $var;
include_once('inc/var.php');
include_once($var->inc_path.'base.php');
_init();

include('header.php');

?>
	<ul data-role="listview" data-inset="true" data-theme="c" data-dividertheme="a">
		<li data-role="list-divider">Event Calendar</li>
		<li style="height:60px;">
			<span>
				<img src="http://www.tapdestin.com/jquerymobile/dp-img.png" />
			</span>
			<span style="padding-left:10px;width:100px;">
				This is test article data.
			</span>
		</li>
		
		<li style="background:#FFFFFF;height:.5px;">&nbsp;</li>
		<li><a href="events.php?id=EVENT" data-transition="slide" >EVENT</a></li>
		<li><a href="locations.php?id=PLACES" data-transition="slide" >PLACES</a></li>
		<li><a href="photos.php?id=PHOTOS" data-transition="slide" >PHOTOS</a></li>
		<li><a href="videos.php?id=VIDEOS" data-transition="slide" >VIDEOS</a></li>
	</ul>

	<!--<br /><br />
	<ul data-role="listview" data-dividertheme="e">
		<li data-role="list-divider">Seamless List (margin-less)</li>
		<li><a href="#foo" data-transition="slide">Internal Link 1</a></li>
		<li><a href="#bar" data-transition="slide">Internal Link 2</a></li>
		<li><a href="#" data-transition="slide">Example Item 3</a></li>
	</ul>-->
<?php include('footer.php'); ?>