<?php
//error_reporting(1);
//header("Location: index.php?sk=home");

include_once('Smarty.class.php');
$main_smarty = new Smarty;

include_once('config.php');
include(mnminclude.'html1.php');
include(mnminclude.'location.php');
include_once(mnminclude.'interest_member.php');
include_once(mnminclude.'interest.php');
include(mnminclude.'group.php');
include(mnminclude.'groups.php');
include('global_variable.php');
$mobOpt=true;
include(mnminclude.'smartyvariables.php');

//echo $home_url;


define('pagename', 'landing');
$main_smarty->assign('pagename', pagename);
$main_smarty->assign('posttitle', 'Home');
$main_smarty->assign('postImage', 'http://shaukk.com/shaukk_small_logo.png');
$main_smarty->assign('postUrl', 'http://shaukk.com/landing.php');
$main_smarty->assign('og_posttitle', 'Shaukk -  Relive your Interests near you.');
$main_smarty->assign('canonical_url', my_base_url.my_pligg_base);
$main_smarty->assign('og_content', 'A highly innovative solution to help you relive your interests near you. Get to know all that is HAPPENING in your locality!');
$main_smarty->assign('tpl_header', $the_template .'/header_landing');
$main_smarty->assign('tpl_center', $the_template . '/landing_center');
//$main_smarty->assign('tpl_footer', $the_template . '/landing_footer');
$main_smarty->display($the_template . '/pligg.tpl');
?>
