<?php
include_once 'libs/gmailoath.php';
include_once 'libs/gmailconfig.php';
session_start();
$oauth =new GmailOath($consumer_key, $consumer_secret, $argarray, $debug, $callback);
$getcontact_access=new GmailGetContacts();
$request_token=$oauth->rfc3986_decode($_GET['oauth_token']);
$request_token_secret=$oauth->rfc3986_decode($_SESSION['oauth_token_secret']);
$oauth_verifier= $oauth->rfc3986_decode($_GET['oauth_verifier']);
$contact_access = $getcontact_access->get_access_token($oauth,$request_token, $request_token_secret,$oauth_verifier, false, true, true);
$access_token=$oauth->rfc3986_decode($contact_access['oauth_token']);
$access_token_secret=$oauth->rfc3986_decode($contact_access['oauth_token_secret']);
$contacts= $getcontact_access->GetContacts($oauth, $access_token, $access_token_secret, false, true,$emails_count);

print_r($contacts);

//Email Contacts 
foreach($contacts as $k => $a)
{
    $title = $a['title'];
    $email = $a['gd$email'];
    $phonenumber = $a['gd$phoneNumber'];
    $postaladdress = $a['gd$postalAddress'];
    //print_r($title);
    //print_r($email);
    //print_r($phonenumber);
    print_r($postaladdress);
    /*foreach($emailadd as $email)
    {
        echo $email["address"] ."<br />";
    }*/
}?>	