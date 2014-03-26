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
$main_smarty->assign('og_posttitle', 'Bawaras');
//Site Title
$main_smarty->assign('posttitle', 'Bawaras');
//met description
$main_smarty->assign('description', '');
//og-description
$main_smarty->assign('og_content', '');
//og-image
$main_smarty->assign('postImage', 'http://shaukk.com/avatars/user_uploaded/1_original.jpg');

//when form is submitted

$main_smarty->assign('tpl_header', $the_template . '/header');
$main_smarty->assign('tpl_center', $the_template . '/bawaras');

$main_smarty->display($the_template . '/pligg.tpl');


?>