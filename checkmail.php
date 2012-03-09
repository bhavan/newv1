<?php
$to = "yogi.ghorecha@gmail.com";
$subject = "My subject";
$txt = "Hello world!";
$headers = "From: yogi.ghorecha@aaditsoftware.com" . "\r\n" .
"CC: bhavan@salzinger.com";

$sentMailResult = mail($to,$subject,$txt,$headers);

if($sentMailResult){
	echo "E-Mail Sent Successfully!!";
}else {
	echo "Can not send your E-Mail..";
	echo "result:";
	echo $sentMailResult;
}

?>
