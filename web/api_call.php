<?php

$ch=curl_init("https://ec2-3-142-142-169.us-east-2.compute.amazonaws.com:8080/api/");
$data="";
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_POST,1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	'content-type: application/x-www-form-urlencoded',
	'content-length: '.strlen($data))
			);

$result=curl_exec($ch);
curl_close($ch);
$jsonResult=json_decode($result,true);
echo "Response: ";
echo $jsonResult;
?>