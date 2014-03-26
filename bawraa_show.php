<?php

include_once('Smarty.class.php');
$main_smarty = new Smarty;
include_once('config.php');
include_once(mnminclude.'html1.php');
include_once(mnminclude.'link.php');
include_once(mnminclude.'interest.php');
include_once(mnminclude.'interest_member.php');
include_once(mnminclude.'scribble.php');
include_once(mnminclude.'tags.php');
include_once(mnminclude.'search.php');
include_once(mnminclude.'location.php');
include_once(mnminclude.'searchscribble.php');
include_once(mnminclude.'group.php');
include_once(mnminclude.'news.php');

/*
if(!$current_user->authenticated && (!isset($_GET['sk']) || $_GET['sk']!='home')){
   header('Location: landing.php');
}
*/

include_once('global_variable.php');
$mobOpt=true;
include_once(mnminclude.'smartyvariables.php');
// module system hook

$main_smarty->assign('posttitle', 'Home');
$main_smarty->assign('og_posttitle', 'Home');
$main_smarty->assign('postImage', 'http://shaukk.com/shaukk_small_logo.png');
$main_smarty->assign('postUrl', 'http://shaukk.com/?sk=home');

$vars = '';
check_actions('index_top', $vars);
if($_GET['user_id']){
   $id = $_GET['user_id'] ;
   $sql="select user_url FROM `shusers` WHERE user_id= ".$id."";
   $result=$db->get_var($sql);
}else if(isset($_REQUEST['title'])){
    $requestTitle=$_REQUEST['title'];
    if(isset($requestTitle)){
         $id = $db->get_var($sql="SELECT user_id FROM " . table_users . " WHERE `user_url` = '".$db->escape(sanitize($requestTitle,4))."';");
         if(!is_null($id)){
            $result = $db->escape(sanitize($requestTitle,4));
         }
    }
}else{
    header('Location: '.$my_base_url.$my_pligg_base);
    die;
}





$url=getmyFullUrl('bawraas_profiles',$result);
$main_smarty->assign('id', $id);
$main_smarty->assign('url', $url);
$main_smarty->assign('tpl_header', $the_template . '/header');
$main_smarty->assign('tpl_center', $the_template . '/bawraa_show');
        $main_smarty->display($the_template . '/pligg.tpl');
?>