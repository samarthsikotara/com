<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Swaroop
 * Date: 2/23/13
 * Time: 2:51 PM
 * To change this template use File | Settings | File Templates.
 */


include_once('Smarty.class.php');
$main_smarty = new Smarty;

include_once('config.php');
include(mnminclude.'html1.php');
include(mnminclude.'link.php');
include(mnminclude.'tags.php');
include(mnminclude.'user.php');
include(mnminclude.'user_fetch.php');
include(mnminclude.'smartyvariables.php');
include(mnminclude."facebookapi.php");
include_once(mnminclude.'location.php');
include_once(mnminclude.'interest_member.php');
include_once(mnminclude.'group.php');
include_once(mnminclude.'groups.php');
include('global_variable.php');
include(mnminclude.'gmailoath.php');
include(mnminclude.'gmailconfig.php');
include(mnminclude.'gmailcontactclass.php');
include(mnminclude.'gmailapi.php');

define('pagename', "gettingStarted");
$main_smarty->assign('pagename', pagename);

if(!$current_user->authenticated) force_authentication();
$main_smarty->assign('sidebuttons', 'false');
$start=false;
if(isset($_REQUEST['start']) && $_REQUEST['start']==1){
    $start=true;
    $main_smarty->assign('start', 'true');
    $main_smarty->assign('posttitle', 'Getting Started');
}else{
    $main_smarty->assign('posttitle', 'Find Your Friends');
}
//print_r($current_user);
//echo $current_user->fbid;
$reqClient="fb";
if(isset($_REQUEST['client'])){$reqClient="gmail";}
if(isset($_REQUEST['referrer']) && $_REQUEST['referrer']=="gmail"){

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
        //print_r($contacts);
        $run = new gmailcontact();

}
//die($current_user->fb_id);
if(is_numeric($current_user->fb_id) && $current_user->fb_id >0 && $reqClient!="gmail"){
    $client="fb";
    $main_smarty->assign('client', $client);
    $fb_friends_on_shaukk = fbfriendsOnshaukk();
    $fb_friends_not_on_shaukk = fbfriendsNotOnshaukk();
    $number_of_friend_on_shaukk = count($fb_friends_on_shaukk);
    $number_of_friend_not_on_shaukk = count($fb_friends_not_on_shaukk);
    $groupsSug=groupforuser(0,12);
    //print_r($groupsSug);
    if(count($groupsSug)>0){
        $i=0;
        foreach($groupsSug as $result){
            $groupsSug[$i]['image']=getGroupImage($result['group_id'], '100');
            $groupsSug[$i]['image_small']=getGroupImage($result['group_id'], '50');
            $groupsSug[$i]['image_large']=getGroupImage($result['group_id'], '250');
            $groupsSug[$i]['url']=getmyurl('group_page',$result['group_id'] );
            $i++;
        }
    }
    $main_smarty->assign('allgroup',$groupsSug);
    //print_r($groupsSug);
    //print_r($fb_friends_on_shaukk);

    if($number_of_friend_on_shaukk==0){
        include_once(mnminclude.'fb_api_config.php');
        include_once(mnminclude.'fbimporter.php');
        $fb= new fbimporter();
        $fb->fb_id=$current_user->fb_id;
        $fb->accessToken=$current_user->fb_accessToken;
        $fb->importData();
    }
    $fb_friends_on_shaukk = fbfriendsOnshaukk();
    $fb_friends_not_on_shaukk = fbfriendsNotOnshaukk();
    $number_of_friend_on_shaukk = count($fb_friends_on_shaukk);
    $number_of_friend_not_on_shaukk = count($fb_friends_not_on_shaukk);
    //die;
    $first_step=1;
    $second_step=2;
    $third_step=3;
    if($number_of_friend_on_shaukk<1){$first_step=0;$second_step=1;$third_step=2;}
    if($number_of_friend_on_shaukk<1 && $number_of_friend_not_on_shaukk<1){$first_step=0;$second_step=1;$third_step=0;}
    if($number_of_friend_on_shaukk>1 && $number_of_friend_not_on_shaukk<1){$third_step=0;$second_step=2;}
    if($number_of_friend_on_shaukk<1 && $number_of_friend_not_on_shaukk<1 && !$start){header("Location: ".my_base_url);}
    $main_smarty->assign('first_step', $first_step);
    $main_smarty->assign('second_step',$second_step);
    $main_smarty->assign('third_step',$third_step);
    $main_smarty->assign('fb_friends_on_shaukk',$fb_friends_on_shaukk);
    $main_smarty->assign('number_of_friend_on_shaukk',$number_of_friend_on_shaukk);
    $main_smarty->assign('fb_friends_not_on_shaukk',$fb_friends_not_on_shaukk);
    $main_smarty->assign('number_of_friend_not_on_shaukk',$number_of_friend_not_on_shaukk);
}
else{
    $gmail_fr_shaukk=gmailfriendsOnshaukk();
    $gmail_fr_not_shaukk=gmailfriendsNotOnshaukk();
    //print_r($gmail_fr_shaukk);
    //print_r($gmail_fr_not_shaukk);
    $groupsSug=groupforuser(0,12);
    //print_r($groupsSug);
    if(count($groupsSug)>0){
        $i=0;
        foreach($groupsSug as $result){
            $groupsSug[$i]['image']=getGroupImage($result['group_id'], '100');
            $groupsSug[$i]['image_small']=getGroupImage($result['group_id'], '50');
            $groupsSug[$i]['image_large']=getGroupImage($result['group_id'], '250');
            $groupsSug[$i]['url']=getmyurl('group_page',$result['group_id'] );
            $i++;
        }
    }
    $main_smarty->assign('allgroup',$groupsSug);
    if(count($gmail_fr_shaukk)>1 || count($gmail_fr_not_shaukk)>1){
        foreach($gmail_fr_not_shaukk as $key=>$value){
            $search=array('@', '.');
            $replace=array("", "");
            if(trim($value['contact_name'])==""){$gmail_fr_not_shaukk[$key]['contact_name']=$value['contact_emailid'];}
            $gmail_fr_not_shaukk[$key]['frnd_id']=str_replace($search,$replace, $value['contact_emailid']);
        }
        $first_step=1;
        $second_step=2;
        $third_step=3;
        if(count($gmail_fr_shaukk)<1){$first_step=0;$second_step=1;$third_step=2;}
        if(count($gmail_fr_not_shaukk)<1){$third_step=0;}

        if(count($gmail_fr_shaukk)<1 && count($gmail_fr_not_shaukk)<1 && !$start && $current_user->fb_id==0){header("Location: ".my_base_url);}
        $main_smarty->assign('first_step', $first_step);
        $main_smarty->assign('second_step',$second_step);
        $main_smarty->assign('third_step',$third_step);
        $client="gmail";
        $main_smarty->assign('client', $client);
        //print_r($gmail_fr_shaukk);
        //print_r($gmail_fr_not_shaukk);
        $main_smarty->assign('fb_friends_on_shaukk', $gmail_fr_shaukk);
        $main_smarty->assign('number_of_friend_on_shaukk',count($gmail_fr_shaukk));
        $main_smarty->assign('fb_friends_not_on_shaukk',$gmail_fr_not_shaukk);
        $main_smarty->assign('number_of_friend_not_on_shaukk',count($gmail_fr_not_shaukk));
    }else{
        $facebook = new Facebook($fb_config);
        $login_redirect_uri="http://shaukk.com/register_fb_come.php?redirect_uri=gettingstarted.php&register=false";
        $params = array(
            'scope' => $fb_perms,
            'redirect_uri' => $login_redirect_uri
        );

        $loginUrl = $facebook->getLoginUrl($params);
        session_start();
        $oauth =new GmailOath($consumer_key, $consumer_secret, $argarray, $debug, $callback);
        $getcontact=new GmailGetContacts();
        $access_token=$getcontact->get_request_token($oauth, false, true, true);
        $_SESSION['oauth_token']=$access_token['oauth_token'];
        $_SESSION['oauth_token_secret']=$access_token['oauth_token_secret'];

        $gmail_url="https://www.google.com/accounts/OAuthAuthorizeToken?oauth_token=".$oauth->rfc3986_decode($access_token['oauth_token']);
        $main_smarty->assign('client', 'false');
        $main_smarty->assign('fb_url', $loginUrl);
        $main_smarty->assign('gmail_url', $gmail_url);
    }
}



$main_smarty->assign('tpl_center', $the_template . '/startup_center');
$main_smarty->display($the_template . '/pligg.tpl');
?>