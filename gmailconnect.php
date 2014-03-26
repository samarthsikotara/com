<?php
include_once('Smarty.class.php');
$main_smarty = new Smarty;

include('config.php');
include(mnminclude.'html1.php');
include(mnminclude.'user.php');
include_once(mnminclude.'user_fetch.php');
include('global_variable.php');
$mobOpt=true;
include(mnminclude.'smartyvariables.php');


include_once(mnminclude.'gmailconfig.php');
include_once(mnminclude.'gmailoath.php');
session_start();
$oauth =new GmailOath($consumer_key, $consumer_secret, $argarray, $debug, $callback);
$getcontact=new GmailGetContacts();
$access_token=$getcontact->get_request_token($oauth, false, true, true);
$_SESSION['oauth_token']=$access_token['oauth_token'];
$_SESSION['oauth_token_secret']=$access_token['oauth_token_secret'];
?>

<a href="https://www.google.com/accounts/OAuthAuthorizeToken?oauth_token=<?php echo $oauth->rfc3986_decode($access_token['oauth_token']) ?>">
    <img src='images/Googleconnect.png'/>
</a>