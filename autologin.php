<?php
include_once('Smarty.class.php');
$main_smarty = new Smarty;
include_once('config.php');
include(mnminclude.'html1.php');
include(mnminclude.'link.php');
include_once(mnminclude.'user.php');
include_once(mnminclude.'interest_member.php');
include_once(mnminclude.'location.php');
include_once(mnminclude.'group.php');
include('global_variable.php');
include_once(mnminclude.'smartyvariables.php');

$authenticated="false";
$details="false";
$forced=false;
if(isset($_REQUEST['forced']) && $_REQUEST['forced']=="true"){
    $forced=true;
}
if(isset($_REQUEST['loginId']) && is_numeric($_REQUEST['loginId']) && isset($_REQUEST['loginKey'])){
    $details="true";
    $loginid=$_REQUEST['loginId'];
    $loginKey=$_REQUEST['loginKey'];
    if(isset($_REQUEST['return'])){
        $return=urldecode($_REQUEST['return']);
        foreach($_GET as $key=>$value){
                $alread_param=array("loginId", "forced","loginKey", "return");
                if(!in_array($key, $alread_param)){
                   $return.="&".$key."=".urldecode($value);
                }
        }
    }else{
        $return=getmyurl('index');
    }
    $host = str_ireplace('www.', '', parse_url($return, PHP_URL_HOST));
    $sql="SELECT ".table_mail_login.".`user_id`, `user_login`,`user_names`, `user_pass` FROM ".table_mail_login." RIGHT JOIN ".table_users." ON ".table_mail_login.".user_id=".table_users.".user_id WHERE `id`=".$loginid." AND `key`='".$loginKey."' AND UNIX_TIMESTAMP(`dateDeleted`)>UNIX_TIMESTAMP()";
    $user_info=$db->get_row($sql);
    if($user_info && is_numeric($user_info->user_id)){
        global $current_user;
        if($current_user->authenticated && ($current_user->user_id!=$user_info->user_id && !$forced)){
            $main_smarty->assign('switchAccount', 'true');
            $main_smarty->assign('originalUserName', $current_user->user_name);
            $main_smarty->assign('originalUserPhoto', get_avatar("large", "", "", "", $current_user->user_id));
            $main_smarty->assign('newUserName', $user_info->user_names);
            $main_smarty->assign('newUserPhoto', get_avatar("large", "", "", "", $user_info->user_id));
            $main_smarty->assign('returnBackUrl', $_SERVER['REQUEST_URI']."&forced=true" );
        }
        elseif($current_user->authenticated && $current_user->user_id==$user_info->user_id){
            header("Location: ".$return);
        }
        elseif($current_user->Authenticate($user_info->user_login, $user_info->user_pass,false,  $user_info->user_pass)){
            $authenticated="true";
            header("Location: ".$return);
        }else{
            $main_smarty->assign('switchAccount', 'false');
        }
    }else if($current_user->authenticated){
        header("Location: ".$return);
    }else{
        header("Location: ".$return);
    }
}else{
    header("Location: ".my_base_url."/404error.php");
}
$main_smarty->assign('loginUrl', getmyFullurl('login', urlencode($return)));
$main_smarty->assign('authenticated', $authenticated);
$main_smarty->assign('posttitle',"Login");
$main_smarty->assign('og_posttitle',"Login");
$main_smarty->assign('postUrl', $return);
$main_smarty->assign('returnUrl', $return);
$main_smarty->assign('tpl_center', $the_template . '/auto_login_center');
$main_smarty->display($the_template . '/pligg.tpl');
