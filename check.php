<?php
$pageURL = $_SERVER["HTTP_HOST"];
echo $pageURL;

$withoutWWW = str_replace ('www.','',$pageURL);
echo $withoutWWW;
?>

<?php phpinfo(); ?>

