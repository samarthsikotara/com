<?php
include_once('Smarty.class.php');
$main_smarty = new Smarty;

include_once('config.php');
include(mnminclude.'html1.php');
include(mnminclude.'link.php');
include(mnminclude.'smartyvariables.php');
include_once(mnminclude.'interest_member.php');
include_once(mnminclude.'interest.php');
include_once(mnminclude.'location.php');
include_once(mnminclude.'group.php');
include_once(mnminclude.'groups.php');
include_once(mnminclude.'user.php');
include_once(mnminclude.'news.php');
include_once(mnminclude.'test_brand.php');
include_once('global_variable.php');
global $current_user,$db;

//set to true if this page has mobile template
$mobOpt=false;
//og-title
$main_smarty->assign('og_posttitle', '\'Tu Bawraa hai toh chal\' Contest');
//Site Title
$main_smarty->assign('posttitle', '\'Tu Bawraa hai toh chal\' Contest - by Shaukk - Bawraas with Mood Indigo\'13\'');
//met description
$main_smarty->assign('description', 'Calling all you passionate souls out there - \'Tu Bawraa hai toh chal\' Contest: In search of the next Bawraa - Someone who passionately pursues unconventional interests. 

Apply on http://shaukk.com/bawraas/ 

By Shaukk-Bawraas in association with Mood Indigo\'13.');
//og-description
$main_smarty->assign('og_content', ' Calling all you passionate souls out there - \'Tu Bawraa hai toh chal\' Contest: In search of the next Bawraa - Someone who passionately pursues unconventional interests. 

Apply on http://shaukk.com/bawraas/ 

By Shaukk-Bawraas in association with Mood Indigo\'13. ');
//og-image
$main_smarty->assign('postImage', 'http://shaukk.com/images/logos.png');

//echo "hi";
// sidebar
define('pagename', 'bawraas');
$main_smarty->assign('pagename', pagename);
$main_smarty->assign('bawraas_step', '1');
$main_smarty->assign('bawraas_apply_url', getmyFullurl('bawraas_apply'));
$main_smarty->assign('tpl_content', "bawraas_step0.tpl");
$main_smarty = do_sidebar($main_smarty);

$canIhaveAccess = 0;
$canIhaveAccess = $canIhaveAccess + checklevel('god');
$canIhaveAccess = $canIhaveAccess + checklevel('admin');
/*
// If not logged in, redirect to the index page
if (!$current_user->authenticated)  header('Location: '.$my_base_url.$my_pligg_base."/login.php");
if (isset($_GET['login']))
	$login=$_GET['login'];
elseif(isset($_GET['id'])){
    if(is_numeric($_GET['id'])){$id=$_GET['id'];}
    else{header('Location: '.$my_base_url.$my_pligg_base);
	    die;
    }
}
elseif ($current_user->user_id > 0 && $current_user->authenticated)
	$login=$current_user->user_login;
else{
	header('Location: '.$my_base_url.$my_pligg_base);
	die;
}
*/
//include_once('create_result.php');
$isGuest = 'true';
if ($current_user->user_id > 0 && $current_user->authenticated){
    $login=$current_user->user_login;
    $isGuest = 'false';
}else{
    $isGuest = 'true';
}
$redirect_url='http://shaukk.com/login.php?referrer=facebook&fb_return='.urlencode(my_base_url.my_pligg_base.'/bawraas_profile.php');
//echo $redirect_url;
    $facebook1 = new Facebook($fb_config);
    $fb_user_id = $facebook1->getUser();
    //if(!$fb_user_id){

        $thisparams = array(
            'scope' => $fb_perms,
            'redirect_uri' => $redirect_url
        );
        //print_r($thisparams);
        $fbnewlogin=$facebook1->getLoginUrl($thisparams);
        //echo $fbnewlogin;
        $main_smarty->assign('fbloginurl', $fbnewlogin);
    //}

$breadcrumbs[0]['text']='Home »';
$breadcrumbs[0]['url']=getmyFullurl('index');
$breadcrumbs[1]['text']='Bawraas »';
$breadcrumbs[1]['url']=getmyFullurl('bawraas');

$url_login = my_base_url.my_pligg_base.'/login.php';
$return_login = my_base_url.my_pligg_base.'/bawraas_profile.php';
$main_smarty->assign('return_login', $return_login);
$main_smarty->assign('breadcrumbs', $breadcrumbs);
$main_smarty->assign('URL_login', $url_login);
$shaukk_register_redirect = my_base_url.my_pligg_base.'/register.php?return=bawraas_profile.php';
$main_smarty->assign('shaukk_register', $shaukk_register_redirect);

$main_smarty->assign('isGuest', $isGuest);
$main_smarty->assign('tpl_header', $the_template . '/header');
$main_smarty->assign('tpl_center', $the_template . '/bawaras_main');

$main_smarty->display($the_template . '/pligg.tpl');


?>