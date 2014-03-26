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

include('config.php');
include(mnminclude.'html1.php');
include(mnminclude.'link.php');
include(mnminclude.'tags.php');
include(mnminclude.'user.php');
include(mnminclude.'user_fetch.php');
include(mnminclude.'smartyvariables.php');
include(mnminclude."facebookapi.php");
include('global_variable.php');

define('pagename', "fbfriend");
$main_smarty->assign('pagename', pagename);
$main_smarty->assign('client', 'fb');


$fb_friends_on_shaukk = fbfriendsOnshaukk();
$fb_friends_not_on_shaukk = fbfriendsNotOnshaukk();
$number_of_friend_on_shaukk = count($fb_friends_on_shaukk);
$number_of_friend_not_on_shaukk = count($fb_friends_not_on_shaukk);
$main_smarty->assign('fb_friends_on_shaukk',$fb_friends_on_shaukk);
$main_smarty->assign('number_of_friend_on_shaukk',$number_of_friend_on_shaukk);
$main_smarty->assign('fb_friends_not_on_shaukk',$fb_friends_not_on_shaukk);
$main_smarty->assign('number_of_friend_not_on_shaukk',$number_of_friend_not_on_shaukk);
$main_smarty->assign('tpl_center', $the_template . '/fbfriend_center');
$main_smarty->display($the_template . '/pligg.tpl');
?>