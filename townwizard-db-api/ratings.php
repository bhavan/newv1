<?php

require_once('../jevents.php');
include_once('user-api.php');

if(!empty($_SESSION['tw_user'])) {
    echo tw_get_ratings($_GET['contentIds'], $_GET['contentType'], true);
} else {
    echo tw_get_avg_ratings($_GET['contentIds'], $_GET['contentType'], true);
}

?>