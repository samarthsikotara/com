<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Swaroop
 * Date: 6/6/13
 * Time: 10:25 AM
 * To change this template use File | Settings | File Templates.
 */
include_once('Smarty.class.php');
$main_smarty = new Smarty;

include('config.php');
include(mnminclude.'html1.php');
include_once('global_variable.php');
//include(mnminclude."facebook.php");
$fb_config = array(
    'appId' => '221440314656956',
    'secret' => '03d87da0c93c8e0b6ebfd9cfe4835b14',
    'cookie'=> true
  );
$fb_perms='publish_stream,email,user_birthday,user_location,user_interests,friends_location';
$facebook= new Facebook($fb_config);
echo "aaaaa";

echo $user=$facebook->getUser();
echo $user=$facebook->getSignedRequest();
try {
  $facebook->api('/me/feed','POST',
                   array(
                     'message' => 'Hello World!',
                     'link' => 'www.example.com'
                        )
                );
} catch(FacebookApiException $e) {
    print_r($e);
    $e_type = $e->getType();
  //error_log('Got an ' . $e_type . ' while posting');
}

$params = array(
  'scope' => $fb_perms,
  'redirect_uri' => 'http://shaukk.com/facebookcheck.php'
);

$loginUrl = $facebook->getLoginUrl($params);
?>
<a href="<?php echo $loginUrl; ?>">Login</a>

 
