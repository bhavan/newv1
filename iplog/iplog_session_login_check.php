<?php
/*
@@@@@@ At this point, user is shown the login form and user submits the form @@@@@@
[Usually at this point IP should not be locked as it was checked by the controller (section above).
But hacker can perform direct form post without going through login page.
That's why we saved SESSION[login_page_shown] to yes above.]
*/
# including class file
require_once("iplog_class.php");

# Create class object
$Obj_iplog = new iplog();

if(($_SESSION['login_page_shown'] != "yes")){
	# direct form post, without going through login page: return 403
	$Obj_iplog->_forbidden_msg_account_lock();
}
?>