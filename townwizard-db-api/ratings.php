<?php

require_once('../jevents.php');
include_once('user-api.php');

echo tw_get_rating($_GET['contentId'], $_GET['contentType']);

?>