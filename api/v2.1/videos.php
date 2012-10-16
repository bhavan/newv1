<?php
include("connection.php");
include("iadbanner.php");

$query="select title as name, filename, videocode from jos_phocagallery where catid=2 order by id desc";
$rec=mysql_query($query) or die(mysql_error());
$num_records = mysql_num_rows($rec);

$k = 0; 
while($row	= mysql_fetch_array($rec))
{
	$arr=explode('/v/',$row['videocode']);
	$arr1=explode('?',$arr[1]);
	$arr2=explode('&',$arr1[0]);
	$arr2[0]='http://www.youtube.com/watch?v='.$arr2[0];

	$data[$k]['name']	= $row['name'];
	$data[$k]['thumb']	= "http://".$_SERVER['SERVER_NAME']."/partner/".$_SESSION['partner_folder_name']."/images/phocagallery/".$row['filename'];
	$data[$k]['url']	= $arr2[0];
	++$k;
}
	  
$response = array(
   	'data' => $data,
   	'meta' => array(
		'total' => $num_records,
		'limit' => $num_records,
		'offset' => 0
	)
);

//echo "<pre>";
//print_r($response);
header('Content-type: application/json');
echo json_encode($response);

?>