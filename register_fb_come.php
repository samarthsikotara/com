<?php
include_once('Smarty.class.php');
$main_smarty = new Smarty;

include_once('config.php');
include(mnminclude.'html1.php');
include(mnminclude.'fb_api_config.php');
include(mnminclude.'fbimporter.php');
include_once(mnminclude.'location.php');
include_once(mnminclude.'interest_member.php');
include_once(mnminclude.'group.php');
include_once(mnminclude.'groups.php');
include_once('global_variable.php');
include_once(mnminclude.'smartyvariables.php');


define('pagename', "gettingStarted");
$main_smarty->assign('pagename', pagename);
$main_smarty->assign('posttitle', "Connect with facebook");

$register=true;
if(isset($_REQUEST['register']) && $_REQUEST['register']=="false"){$register=false;}
$fb= new fbimporter();
if(isset($_REQUEST['fb_id']) && is_numeric($_REQUEST['fb_id'])){
        $fb->fb_id=$_REQUEST['fb_id'];
        $fb->accessToken=$_REQUEST['access_token'];
}
$fb->importData();
//print_r($fbimp->friendlist);
//print_r($fbimp->interests);

if(isset($_REQUEST['redirect_uri'])) $uri=$_REQUEST['redirect_uri'];
else $uri=getmyurl('index');
$main_smarty->assign('url', $uri);
if($fb->status && $register){
    header("Location: register_client.php?client=facebook&data=true&fb_id=".$fb->fb_id);
}else if($fb->status && !$register){
    $sql="SELECT COUNT(*) from ".table_users." WHERE `user_fbid`=".$fb->fb_id." LIMIT 1";
    $count=$db->get_var($sql);
   if($count>0){
      $type="Error";
      $errorMssg="Your facebook account is already connected with another Shaukk account";
   }
   else if(check_email($user_profile['email']) && $current_user->user_email==$user_profile['email']){
        if($current_user->fbid==0){
            $user =new User();
            $user->id=$current_user->user_id;
            $user->read();
            if($user->fbid==0){
                    $user->fbid=$fb->fb_id;
                    $user->store();
                    $current_user->fbid=$fb->fb_id;
                    header("Location: ".$uri);
            }else{
                    $type="Error";
                    $errorMssg="Your account is already connected with a facebook account.";
            }
        }
        else{
            $type="Error";
            $errorMssg="Your account is already connected with a facebook account.";
        }
    }else{
        $main_smarty->assign('merge', 'true');
        $main_smarty->assign('fb_profilePic', "https://graph.facebook.com/".$fb->fb_id."/picture?type=large");
        $main_smarty->assign('fb_name', $fb->name);
        $main_smarty->assign('merge', 'true');
        $main_smarty->assign('positive', 'true');
        $link=my_base_url.my_pligg_base."/mergeaccount.php?redirect_uri=".$uri."&fb_email=".$fb->email."&fb_id=".$fb->fb_id."&confirmCode=".md5($fb->fb_id."-".$fb->email);
        $type="Merge Accounts";
        $errorMssg="The Email on the Facebook doesn't match with the email provided to shaukk. Do you want to connect this facebook account with Shaukk";
        $main_smarty->assign('mergeUrl', $link);
    }
}
else{
    header("Location: 404error.php?error=FB_Register_Permissions");
}

$main_smarty->assign('errorMssg', $errorMssg);
$main_smarty->assign('type', $type);
$main_smarty->assign('tpl_center', $the_template . '/fb_come');
$main_smarty->display($the_template . '/pligg.tpl');