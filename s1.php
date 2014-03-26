<?php
$uid = '9537243647';
$pwd = '61063602767';
$phone = '+919537243647';
$msg = 'MESSAGE TEXT';
$provider = 'way2sms';

$content = 'uid='.rawurlencode($uid).
'&pwd='.rawurlencode($pwd).
'&phone='.rawurlencode($phone).
'&msg='.rawurlencode($msg).
//'&ck=1'. // Use if you need a user freindly response message.
'&provider='.rawurlencode($provider);

$sms_response = file_get_contents('http://smsapi.cikly.in/index.php?' . $content);

echo $sms_response;
?>