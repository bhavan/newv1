<?php

require ("inc/config.php"); 

$handle = fopen($query, "r");
$xml = '';
while (!feof($handle)) {
  $xml.= fread($handle, 8192);
}
fclose($handle);
$data = XML_unserialize($xml);

echo "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n";

$image_url ="http://www.30amobile.com/30amobile/weather/icons/" . $data[weather][dayf][day][0][part][0][icon] . ".png" ;
$min_temp = str_replace('N/A','0',$data[weather][dayf][day][0][low]);
$max_temp = str_replace('N/A','0',$data[weather][dayf][day][0][hi]);

$contentBody .= '<weather min_temp="'.$min_temp.'" max_temp="'.$max_temp.'" image_url="'.$image_url.'"></weather>';

echo $contentBody;

?>